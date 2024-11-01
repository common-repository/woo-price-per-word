<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 * @date    2013-08-08
 * @license GPL-3.0
 * @url     <https://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2014 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 *
 */

namespace Smalot\PdfParser\Tests\Units\Element;

use mageekguy\atoum;

/**
 * Class ElementNull
 *
 * @package Smalot\PdfParser\Tests\Units\Element
 */
class ElementNull extends atoum\test
{
    public function testParse()
    {
        // Skipped.
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse('ABC', null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' [ null ]', null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' << null >>', null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' / null ', null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' 0 null ', null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(" 0 \n null ", null, $offset);
        $this->assert->boolean($element)->isEqualTo(false);
        $this->assert->integer($offset)->isEqualTo(0);

        // Valid.
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' null ', null, $offset);
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
        $this->assert->integer($offset)->isEqualTo(5);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' null ', null, $offset);
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
        $this->assert->integer($offset)->isEqualTo(5);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(' null', null, $offset);
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
        $this->assert->integer($offset)->isEqualTo(5);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse('null', null, $offset);
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
        $this->assert->integer($offset)->isEqualTo(4);
        $offset  = 0;
        $element = \Smalot\PdfParser\Element\ElementNull::parse(" \n null ", null, $offset);
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
        $this->assert->integer($offset)->isEqualTo(7);
    }

    public function testGetContent()
    {
        $element = new \Smalot\PdfParser\Element\ElementNull('null');
        $this->assert->boolean(is_null($element->getContent()))->isEqualTo(true);
    }

    public function testEquals()
    {
        $element = new \Smalot\PdfParser\Element\ElementNull('null');
        $this->assert->boolean($element->equals(null))->isEqualTo(true);
        $this->assert->boolean($element->equals(false))->isEqualTo(false);
        $this->assert->boolean($element->equals(0))->isEqualTo(false);
        $this->assert->boolean($element->equals(1))->isEqualTo(false);
    }

    public function testContains()
    {
        $element = new \Smalot\PdfParser\Element\ElementNull('null');
        $this->assert->boolean($element->contains(null))->isEqualTo(true);
        $this->assert->boolean($element->contains(false))->isEqualTo(false);
        $this->assert->boolean($element->contains(0))->isEqualTo(false);
    }

    public function test__toString()
    {
        $element = new \Smalot\PdfParser\Element\ElementNull('null');
        $this->assert->castToString($element)->isEqualTo('null');
    }
}
