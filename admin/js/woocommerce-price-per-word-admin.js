jQuery(function ($) {
    'use strict';
    $(function () {

        if (typeof (woocommerce_price_per_word_params.aewcppw_word_character) != "undefined" && woocommerce_price_per_word_params.aewcppw_word_character !== null && woocommerce_price_per_word_params.aewcppw_word_character == 'word') {
            var wppw_product_type = 'Word ';
        } else if (typeof (woocommerce_price_per_word_params.aewcppw_word_character) != "undefined" && woocommerce_price_per_word_params.aewcppw_word_character !== null && woocommerce_price_per_word_params.aewcppw_word_character == 'character') {
            var wppw_product_type = 'Character ';
        } else {
            var wppw_product_type = 'Word ';
        }

        $('#_price_per_word_character_enable').change(function () {
            if ($('input[name="_price_per_word_character"][value="word"]').is(":checked")) {
                var wppw_product_type = 'Word ';
            }
            else {
                var wppw_product_type = 'Character ';
            }
            if ($(this).is(':checked')) {
                $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".custom_tab_woocommerce_price_word_character_tab").removeClass("custom_tab_woocommerce_price_word_character_tab_hidden");
                if ($(".custom_tab_woocommerce_price_word_character_tab ").hasClass("active")) {
                    $("#custom_tab_data_woocommerce_price_word_character_tab").show();
                }
            } else {
                var wppw_product_type = '';
                $("label[for='_regular_price']").text('Regular Price ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".word_count_cap").hide();
                $(".custom_tab_woocommerce_price_word_character_tab").addClass("custom_tab_woocommerce_price_word_character_tab_hidden");
                $("#custom_tab_data_woocommerce_price_word_character_tab").hide();
            }
        });

        $('input[name="_price_per_word_character"]').click(function () {
            if ($(this).val() == 'word') {
                var wppw_product_type = 'Word ';
                $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $("label[for='_word_count_cap_status']").text('Enable ' + wppw_product_type.toLowerCase() + 'count cap?');
                $("label[for='_word_count_cap_word_limit']").text(wppw_product_type + 'limit');
                $("#price-breaks-list .min-title-head").text("Min Words");
                $("#price-breaks-list .max-title-head").text("Max Words");
                $("#custom_tab_woocommerce_price_word_character_tab a").html("<span>Price Per " + wppw_product_type + " Settings</span>");
            } else if ($(this).val() == 'character') {
                var wppw_product_type = 'Character ';
                $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $("label[for='_word_count_cap_status']").text('Enable ' + wppw_product_type.toLowerCase() + 'count cap?');
                $("label[for='_word_count_cap_word_limit']").text(wppw_product_type + 'limit');
                $("#price-breaks-list .min-title-head").text("Min Characters");
                $("#price-breaks-list .max-title-head").text("Max Characters");
                $("#custom_tab_woocommerce_price_word_character_tab a").html("<span>Price Per " + wppw_product_type + " Settings</span>");
            }
            else {
                var wppw_product_type = ' ';
                $("label[for='_regular_price']").text('Regular Price ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $("#price-breaks-list .min-title-head").text("Min Words");
                $("#price-breaks-list .max-title-head").text("Max Words");
            }
        });

        $('.variations_tab a, .woocommerce_variation').click(function () {
            if ($('#_price_per_word_character_enable').is(':checked')) {
                setTimeout(function () {
                    $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 5000);

            } else {
                setTimeout(function () {
                    $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 5000);
            }
        });

        $(document).on('change', '#variable_product_options', function () {
            if ($('#_price_per_word_character_enable').is(':checked')) {
                setTimeout(function () {
                    $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                    $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 10000);

            } else {
                setTimeout(function () {
                    $("label[for='_regular_price']").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                    $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 10000);
            }
        });


        $("#product-type, #variable_product_options .woocommerce_variations").change(function () {
            if ($('#_price_per_word_character_enable').is(':checked')) {
                setTimeout(function () {
                    $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                    $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 5000);

            } else {
                setTimeout(function () {
                    $("label[for='_regular_price']").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                    $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                }, 5000);
            }
        });

        if ($('#_price_per_word_character_enable').is(':checked')) {
            setTimeout(function () {
                if ($('input[name="_price_per_word_character"][value="word"]').is(":checked")) {
                    var wppw_product_type = 'Word ';
                }
                else {
                    var wppw_product_type = 'Character ';
                }
                $("label[for='_regular_price']").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Price Per ' + wppw_product_type + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
            }, 5000);

        } else {
            setTimeout(function () {
                $("label[for='_regular_price']").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
                $(".variable_pricing p.form-row-first label").text('Regular Price ' + woocommerce_price_per_word_params.woocommerce_currency_symbol_js);
            }, 3000);
        }
    });

    $("input[name='price-breaks-max[]']").live('keyup', function (e) {
        if ($.trim($(this).val()) != ">") {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        }
    });

    // Hide the remove link for pricebreaks first row.
    $("table#price-breaks-list a.remove:first").hide();

    // Add button functionality
    $("table#price-breaks-list a.add").click(function () {
        var $last_max_field = $("input[name='price-breaks-max[]']").last().val();
        if ($last_max_field == ">") {
            if ($('input[name="_price_per_word_character"][value="word"]').is(":checked")) {
                var wppw_product_type = 'Words';
            }
            else {
                var wppw_product_type = 'Characters';
            }
            alert("The Max " + wppw_product_type + " field for the last row must be greater than 0 to add an additional row.");
            return false;
        }

        var master = $(this).parents("table#price-breaks-list");

        // Get a new row based on the prototype row
        var prot = master.find(".prototype").clone();
        prot.attr("class", "")
        var $last_max_value = $("input[name='price-breaks-max[]']").last().parents("tr").find("input[name='price-breaks-max[]']").val();
        prot.find("input[name='price-breaks-min[]']").attr("value", parseInt($last_max_value) + 1);
        prot.find("input[name='price-breaks-max[]']").attr("value", ">");
        prot.find("a.remove").show();
        master.find("tbody").append(prot);
    });

    // Remove button functionality
    $("table#price-breaks-list a.remove").live("click", function () {
        if (!$(this).parents("tr").hasClass("prototype")) {
            console.log($(this).is(':last-child'));
            if ($(this).parents("tr").is(':last-child')) {
                $(this).parents("tr").prev().find("input[name='price-breaks-max[]']").attr("value", ">");
            }
            var $last_max_value = $(this).parents("tr").prev().find("input[name='price-breaks-max[]']").val();
            console.log($last_max_value);
            $(this).parents("tr").next().find("input[name='price-breaks-min[]']").attr("value", parseInt($last_max_value) + 1);
            $(this).parents("tr").remove();
        }
    });

    $("#_is_enable_price_breaks").click(function () {
        if ($(this).is(":checked")) {
            $("#custom_tab_data_woocommerce_price_word_character_tab #price-breaks-list").show();
            $("input[name='price-breaks-price[]']").val($('#_regular_price').val());
        }
        else {
            $("#custom_tab_data_woocommerce_price_word_character_tab #price-breaks-list").hide();
        }
    });

    $(document).on("blur", "input[name='price-breaks-price[]']", function (e) {
        var val = $(this).val();
        var regex_to_test = /^([\-\+]?[0-9]*(\.[0-9]+)?)$/g;
        var regex_to_match = /^([\-\+]?[0-9]*(\.[0-9]+)?)/g;
        if (regex_to_test.test(val)) {
            if (val == "") {
                $(this).val($('#_regular_price').val());
            }
        } else {
            val = regex_to_match.exec(val);
            if (val[0] != "") {
                $(this).val(val[0]);
            } else {
                $(this).val($('#_regular_price').val());
            }
        }
    });

    $(document).on("blur", "input[name='price-breaks-max[]']", function () {
        if ($('input[name="_price_per_word_character"][value="word"]').is(":checked")) {
            var wppw_product_type = 'Words';
        }
        else {
            var wppw_product_type = 'Characters';
        }
        var $min_element = $("input[name='price-breaks-min[]']");
        var $max_element = $("input[name='price-breaks-max[]']");
        var $max = parseInt($(this).val());
        var $max_without_parse = $(this).val();
        var $min = parseInt($(this).parents("tr").find($min_element).val());
        if ($max == ">" && $(this).parents("tr").next("tr").length == 0) {
            return false;
        }
        else if ($(this).parents("tr").next("tr").length == 0) {
            if ($max_without_parse != ">" && (isNaN($max) || $max <= $min) || $max == '') {
                alert("The Max " + wppw_product_type + " for the last row must be greater than the Min " + wppw_product_type + ", or use the > symbol.");
                $(this).val(">");
            }
            if ($(this).val() != '>') {
                $("table#price-breaks-list a.add").trigger("click");
            }
        }
        else if ($(this).parents("tr").next("tr").length > 0) {
            // check next tr having or not
            var $next_min = parseInt($(this).parents("tr").next("tr").find($min_element).val());
            if ($max <= $min || ($max_without_parse == '>') || isNaN($max)) {
                alert("The Max " + wppw_product_type + " field must be greater than the Min " + wppw_product_type + " field.");
                $(this).val(parseInt($(this).parents("tr").next().find($min_element).val()) - 1);
            }
            else if ($max >= $next_min || ($next_min - $max) > 1) {
                alert("All rows will be reset to match new value.");
                $(this).parents("tr").nextAll().not(":last-child").remove();
                $(this).parents("tr").next("tr").find($min_element).val($max + 1)
            }
        }
    });

    $('#ppw-bulk-tool-action-target-type').change(function () {
        if ($(this).val() == 'where') {
            $('.ppw-bulk-tool-action-target-where-type').show();
            $('#ppw-bulk-tool-action-target-where-type').attr("required", "required");
        }
        else {
            $('.ppw-bulk-tool-action-target-where-type').hide();
            $('.ppw-bulk-tool-target-where-category').hide();
            $('.ppw-bulk-tool-target-where-product-type').hide();
            $('.ppw-bulk-tool-action-target-where-price-value').hide();

            $('#ppw-bulk-tool-action-target-where-type').removeAttr("required", "required");
            $('#ppw-bulk-tool-target-where-category').removeAttr("required", "required");
            $('#ppw-bulk-tool-target-where-product-type').removeAttr("required", "required");
            $('#ppw-bulk-tool-action-target-where-price-value').removeAttr("required", "required");
        }
    });

    $('#ppw-bulk-tool-action-target-where-type').change(function () {
        $('.ppw-bulk-tool-target-where-category').hide();
        $('.ppw-bulk-tool-target-where-product-type').hide();
        $('.ppw-bulk-tool-action-target-where-price-value').hide();

        $('#ppw-bulk-tool-action-target-where-type').removeAttr("required", "required");
        $('#ppw-bulk-tool-target-where-category').removeAttr("required", "required");
        $('#ppw-bulk-tool-target-where-product-type').removeAttr("required", "required");
        $('#ppw-bulk-tool-action-target-where-price-value').removeAttr("required", "required");

        if ($(this).val() == 'category') {
            $(".ppw-bulk-tool-target-where-category").show();
            $('#ppw-bulk-tool-target-where-category').attr("required", "required");
        }
        else if ($(this).val() == 'product_type') {
            $(".ppw-bulk-tool-target-where-product-type").show();
            $('#ppw-bulk-tool-target-where-product-type').attr("required", "required");
        }
        else if ($(this).val() == 'price_greater' || $(this).val() == 'price_less') {
            $(".ppw-bulk-tool-action-target-where-price-value").show();
            $('#ppw-bulk-tool-action-target-where-price-value').attr("required", "required");
        }
    });

    // AJAX - Bulk enable/disable tool
    $('#ppw_tool_enable_price_per_words_characters').submit(function () {
        // show processing status
        $('#ppw-tool-bulk-submit').attr('disabled', 'disabled');
        $('#ppw-tool-bulk-submit').removeClass('button-primary');
        $('#ppw-tool-bulk-submit').html('<i class="ofwc-spinner"></i> Processing, please wait...');
        $('#ppw-tool-bulk-submit i.spinner').show();

        var actionType = $('#ppw-bulk-tool-action-type').val();
        var actionTargetType = $('#ppw-bulk-tool-action-target-type').val();
        var actionTargetWhereType = $('#ppw-bulk-tool-action-target-where-type').val();
        var actionTargetWhereCategory = $('#ppw-bulk-tool-target-where-category').val();
        var actionTargetWhereProductType = $('#ppw-bulk-tool-target-where-product-type').val();
        var actionTargetWherePriceValue = $('#ppw-bulk-tool-action-target-where-price-value').val();

        var auto_price_per_word_or_character_enable = "";


        if ($("#ppw-bulk-tool-action-type").val() === "enable_price_per_words" ||
            $("#ppw-bulk-tool-action-type").val() === "enable_price_per_characters" ||
            $("#ppw-bulk-tool-action-type").val() === "disable_price_per_words_chacacters") {
            auto_price_per_word_or_character_enable = "_price_per_word_character_enable";
        }

        var data = {
            'action': 'adminToolBulkEnableDisablePricePerWordsCharacters',
            'actionType': actionType,
            'actionTargetType': actionTargetType,
            'actionTargetWhereType': actionTargetWhereType,
            'actionTargetWhereCategory': actionTargetWhereCategory,
            'actionTargetWhereProductType': actionTargetWhereProductType,
            'actionTargetWherePriceValue': actionTargetWherePriceValue,
            'ppw_meta_key_value': auto_price_per_word_or_character_enable,
        };

        // post it
        $.post(ajaxurl, data, function (response) {
            if ('failed' !== response) {
                var redirectUrl = response;

                /** Debug **/
                    //console.log(redirectUrl);
                    //return false;

                top.location.replace(redirectUrl);
                return false;
            }
            else {
                alert('Error updating records.');
                return false;
            }
        });
        /*End Post*/
        return false;
    });

    if (typeof $("#product-type").val() != "undefied" && $("#product-type").val() != "simple") {
        $("#custom_tab_data_woocommerce_price_word_character_tab .price-breaks-section").hide();
    }

    $("#product-type").change(function () {
        if ($(this).val() == 'simple') {
            $("#custom_tab_data_woocommerce_price_word_character_tab .price-breaks-section").show();
        }
        else {
            $("#custom_tab_data_woocommerce_price_word_character_tab .price-breaks-section").hide();
        }
    });
});