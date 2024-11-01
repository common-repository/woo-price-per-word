<?php
class DocCounter {
        
    private $file;
    private $filetype;
        
    public function setFile($filename)
    {
        $this->file = $filename;
        $this->filetype = pathinfo($this->file, PATHINFO_EXTENSION);
    }
        
    public function getFile()
    {
        return $this->file;
    }
        
    public function getInfo()
    {    
        $ft = $this->filetype;
        
        $obj = new stdClass();
        $obj->format = $ft;
        $obj->wordCount = null;
        /*$obj->lineCount = null;
        $obj->pageCount = null;*/
        $obj->charCount = null;
        
        switch($ft)
        {
            case "doc":
                $doc = $this->read_doc_file();
                $obj->wordCount = $this->str_word_count_utf8($doc);
                $obj->charCount = $this->characterCount($doc);
                /*$obj->lineCount = $this->lineCount($doc);
                $obj->pageCount = $this->pageCount($doc);*/
                break;
            case "docx":
                $docx2text = $this->docx2text();
                $obj->wordCount = $this->str_word_count_utf8($docx2text);
                $obj->charCount = $this->characterCount($docx2text);
                /*$obj->lineCount = $this->lineCount($this->docx2text());
                $obj->pageCount = $this->PageCount_DOCX();*/
                break;
            case "pdf":
                $pdf2text = $this->pdf2text();
                $obj->wordCount = $this->str_word_count_utf8($pdf2text);
                $obj->charCount = $this->characterCount($pdf2text);
                /*$obj->lineCount = $this->lineCount($this->pdf2text());
                $obj->pageCount = $this->PageCount_PDF();*/
                break;
            case "txt":
                $textContents = file_get_contents($this->file);
                $obj->wordCount = $this->str_word_count_utf8($textContents);
                $obj->charCount = $this->characterCount($textContents);
                /*$obj->lineCount = $this->lineCount($textContents);
                $obj->pageCount = $this->pageCount($textContents);*/
                break;
            default:
                $obj->wordCount = "unsupported file format";
                /*$obj->lineCount = "unsupported file format";
                $obj->pageCount = "unsupported file format";*/
        }
        
        return $obj;
    }
    
    /* Convert: Word.doc to Text String */
    function read_doc_file() {                
        $f = $this->file;
         if(file_exists($f))
        {
            if(($fh = fopen($f, 'r')) !== false ) 
            {
               $headers = fread($fh, 0xA00);

               /* 1 = (ord(n)*1) ; Document has from 0 to 255 characters */
               $n1 = ( ord($headers[0x21C]) - 1 );

               /* 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters */
               $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

               /* 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters */
               $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

               /* 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters */
               $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

               /* Total length of text in the document */
               $textLength = ($n1 + $n2 + $n3 + $n4);

               $extracted_plaintext = fread($fh, $textLength);
                $extracted_plaintext = mb_convert_encoding($extracted_plaintext,'UTF-8');
               /* simple print character stream without new lines */
               /* echo $extracted_plaintext;*/

               /* if you want to see your paragraphs in a new line, do this */
               return nl2br($extracted_plaintext);
               /* need more spacing after each paragraph use another nl2br */
            }
        }
    }
    /* Jonny 5's simple word splitter */
    function str_word_count_utf8($str) {
        return count(preg_split('~[^\p{L}\p{N}\']+~u',$str));
    }
    /* Convert: Word.docx to Text String */
    function docx2text()
    {
        return $this->readZippedXML($this->file, "word/document.xml");
    }

    function readZippedXML($archiveFile, $dataFile)
    {       
        /* Create new ZIP archive */
        $zip = new ZipArchive;
        
        $f = $archiveFile;        
        /* Open received archive file */
        if (true === $zip->open($f)) {
            /* If done, search for the data file in the archive */
            if (($index = $zip->locateName($dataFile)) !== false) {
                /* If found, read it to the string */
                $data = $zip->getFromIndex($index);
                /* Close archive file */
                $zip->close();
                /* Load XML from a string */
                /* Skip errors and warnings */
                $xml = new DOMDocument();
                $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                
                $xmldata = $xml->saveXML();
                /* Newline Replacement */
                $xmldata = str_replace("</w:p>", "\r\n", $xmldata);
                /* Return data without XML formatting tags */
                return strip_tags($xmldata);
            }
            $zip->close();
        }

        /* In case of failure return empty string */
        return "";
    }
    
    /* Convert: Word.doc to Text String */
    function read_doc()
    {        
        $f = $this->file;
        $fileHandle = fopen($f, "r");
        $line = @fread($fileHandle, filesize($this->file));   
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
          {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
              {
              } else {
                $outtext .= $thisline." ";
              }
          }
        $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;
    }
    
    /* Convert: Adobe.pdf to Text String */
    function pdf2text()
    {        
        $f = $this->file;
        
        if (file_exists($f)) {
            include('vendor/autoload.php');
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($f);
            $text = $pdf->getText();
            return $text;
        }
        
        return null;
    }
    
    function characterCount($text){
        return strlen(utf8_decode($text));
    }


    /*
    // Page Count: DOCX using XML Metadata
    function PageCount_DOCX()
    {
        $pageCount = 0;

        $zip = new ZipArchive();
                
        $f = $this->file;

        if($zip->open($f) === true) {
            if(($index = $zip->locateName('docProps/app.xml')) !== false)  {
                $data = $zip->getFromIndex($index);
                $zip->close();
                $xml = new SimpleXMLElement($data);
                $pageCount = $xml->Pages;
            }
        }

        return intval($pageCount);
    } */
    /*
    // Page Count: PDF using FPDF and FPDI 
    function PageCount_PDF()
    {        
        $f = $this->file;
        $pageCount = 0;
        if (file_exists($f)) {
            require_once('lib/fpdf/fpdf.php');
            require_once('lib/fpdi/fpdi.php');
            $pdf = new FPDI();
            $pageCount = $pdf->setSourceFile($f);        // returns page count
        }
        return $pageCount;
    } */
    
    /*
    // Page Count: General
    function pageCount($text)
    {
        require_once('lib/fpdf/fpdf.php');

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);
        $pdf->MultiCell(0,5,$text);
        //$pdf->Output();
        $filename="tmp.pdf";
        $pdf->Output($filename,'F');
        
        require_once('lib/fpdi/fpdi.php');
        $pdf = new FPDI();
        $pageCount = $pdf->setSourceFile($filename);
        
        unlink($filename);
        return $pageCount;
    } */
    
    /*
    // Line Count: General
    function lineCount($text)
    {
        $lines_arr = preg_split('/\n|\r/',$text);
        $num_newlines = count($lines_arr); 
        return $num_newlines;
    }
     */
}