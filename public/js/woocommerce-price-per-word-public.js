jQuery(function ($) {
    'use strict';
    $(function () {
        $(".cart").addClass("wppw_cart");
        if (typeof (woocommerce_price_per_word_params) != 'undefined' && ( typeof (woocommerce_price_per_word_params.is_enable_price_per_word_public) == 'string' &&  woocommerce_price_per_word_params.is_enable_price_per_word_public == '' )) {
            
            return false;
        }
        if (typeof (woocommerce_price_per_word_params.aewcppw_word_character) != "undefined" && woocommerce_price_per_word_params.aewcppw_word_character !== null && woocommerce_price_per_word_params.aewcppw_word_character == 'word') {
            if (typeof (woocommerce_price_per_word_params.total_word) != "undefined" && woocommerce_price_per_word_params.total_word !== null && woocommerce_price_per_word_params.total_word > 0) {
                            if(woocommerce_price_per_word_params.aewcppw_hide_qty_from_buyer == 'yes'){
                                $(".woocommerce .quantity input[name='quantity']").attr('type','hidden');
                            }
                $(".woocommerce .quantity input[name='quantity']").val(woocommerce_price_per_word_params.total_word);
                $(".woocommerce .quantity input[name='quantity']").prop("readonly", true);
                $(".single_variation_wrap").show();
                $(".single_add_to_cart_button").parent('div').show();
            } else {
                if (typeof (woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty) != "undefined" && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty !== null && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty == 'no') {
                    $(".single_variation_wrap").hide();
                    $(".single_add_to_cart_button").parent('div').hide();
                }
            }
        } else {
            if (typeof (woocommerce_price_per_word_params.total_character) != "undefined" && woocommerce_price_per_word_params.total_character !== null && woocommerce_price_per_word_params.total_character > 0) {
                if(woocommerce_price_per_word_params.aewcppw_hide_qty_from_buyer == 'yes'){
                    $(".woocommerce .quantity input[name='quantity']").attr('type','hidden');
                }
                $(".woocommerce .quantity input[name='quantity']").val(woocommerce_price_per_word_params.total_character);
                $(".woocommerce .quantity input[name='quantity']").prop("readonly", true);
                $(".single_variation_wrap").show();
                $(".single_add_to_cart_button").parent('div').show();
            } else {
                if (typeof (woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty) != "undefined" && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty !== null && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty == 'no') {
                    $(".single_variation_wrap").hide();
                    $(".single_add_to_cart_button").parent('div').hide();
                }
            }
        }
        if (typeof (woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty) != "undefined" && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty !== null && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty == 'no') {
            $(".single_variation_wrap").hide();
            $(".single_add_to_cart_button").parent('div').hide();
        } else {
            $(".single_variation_wrap").show();
            $(".single_add_to_cart_button").parent('div').show();
        }
        $(".variations select").change(function (event) {
            if (woocommerce_price_per_word_params.is_enable_price_per_word_public == '1') {
                if (!$("#ppw_remove_file").length) {
                    setTimeout(function () {
                        if (typeof (woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty) != "undefined" && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty !== null && woocommerce_price_per_word_params.aewcppw_allow_users_to_enter_qty == 'no') {
                            $(".single_variation_wrap").hide();
                            $(".ppw_total_price").hide();
                        } else {
                            $(".single_variation_wrap").show();
                            $(".ppw_total_price").show();
                        }

                        if ($(event.currentTarget).val() != '') {
                            $(event.currentTarget).parents("form").find(".ppw_total_price").hide();
                            $(event.currentTarget).parents("form").find("#aewcppw_product_page_message").show();
                            $(event.currentTarget).parents("form").find(".ppw_file_upload_div").show();

                            var select_field_name = $(event.currentTarget).attr("name");
                            var product_variations = JSON.parse($(event.currentTarget).parents("form").attr("data-product_variations"));
                            var display_price = 0;
                            $.each(product_variations, function (index, value) {
                                $.each(value.attributes, function (index, attribute) {
                                    if (attribute.toString() === $(event.currentTarget).val()) {
                                        display_price = value.display_price;
                                    }
                                });
                            });
                            var cache = $(event.currentTarget).parents(".summary").find("div[itemprop='offers']").find(".woocommerce-Price-amount").children(".woocommerce-Price-currencySymbol:first");
                            // $(event.currentTarget).parents(".summary").find("div[itemprop='offers']").find(".woocommerce-Price-amount").text(display_price).prepend(cache);
                        } else {
                            $(event.currentTarget).parents("form").find("#aewcppw_product_page_message").hide();
                            $(event.currentTarget).parents("form").find(".ppw_file_upload_div").hide();
                        }
                    }, 2);
                } else {
                    var variations_select = $(".woocommerce div.product form.cart .variations select option:selected").attr("value");
                    if (typeof (woocommerce_price_per_word_params.aewcppw_word_character) != "undefined" && woocommerce_price_per_word_params.aewcppw_word_character !== null && woocommerce_price_per_word_params.aewcppw_word_character == 'word') {
                        if (typeof (woocommerce_price_per_word_params.total_word) != "undefined" && woocommerce_price_per_word_params.total_word !== null && woocommerce_price_per_word_params.total_word > 0) {
                            var quantity = woocommerce_price_per_word_params.total_word;
                        } else {
                            var quantity = $('input[name="quantity"]').val();
                        }
                    } else {
                        if (typeof (woocommerce_price_per_word_params.total_character) != "undefined" && woocommerce_price_per_word_params.total_character !== null && woocommerce_price_per_word_params.total_character > 0) {
                            var quantity = woocommerce_price_per_word_params.total_character;
                        } else {
                            var quantity = $('input[name="quantity"]').val();
                        }
                    }

                    var select_field_name = $(event.currentTarget).attr("name");
                    var product_variations = JSON.parse($(event.currentTarget).parents("form").attr("data-product_variations"));
                    var display_price = 0;
                    $.each(product_variations, function (index, value) {
                        $.each(value.attributes, function (index, attribute) {
                            if (attribute.toString() === $(event.currentTarget).val()) {
                                display_price = value.display_price;
                            }
                        });
                    });
                    var cache = $(event.currentTarget).parents(".summary").find("div[itemprop='offers']").find(".woocommerce-Price-amount").children(".woocommerce-Price-currencySymbol:first");
                    //$(event.currentTarget).parents(".summary").find("div[itemprop='offers']").find(".woocommerce-Price-amount").text(display_price).prepend(cache);

                    setTimeout(function () {
                        $('input[name="quantity"]').val(quantity);
                        if (woocommerce_price_per_word_params.is_product_type_variable) {
                            var product_price = $(".summary").find("div.single_variation_wrap").find(".woocommerce-Price-amount").html().replace(/[^0-9\.]+/g, '');
                        } else {
                            var product_price = $(".summary").find("div[itemprop='offers']").find(".woocommerce-Price-amount").html().replace(/[^0-9\.]+/g, '');
                        }

                        var total_amount = product_price * quantity;
                        var decimals_point = product_price.split(".")[1].length;
                        $(".ppw_total_amount").html(woocommerce_price_per_word_params.woocommerce_currency_symbol_js + parseFloat(total_amount).toFixed(decimals_point));
                        if (variations_select.length > 0) {
                            $(".ppw_total_price").show();
                        } else {
                            $(".ppw_total_price").hide();
                        }
                        $(".single_variation_wrap").show();
                        $(".ppw_total_price").show();
                        $(".woocommerce-variation-add-to-cart-enabled").show();
                        $(".ppw_file_upload_div").hide();
                    }, 2);
                }
            }
        });

        $("input[name=ppw_file_upload]").change(function (event) {
            if ($(this).val() == '') {
                return false;
            }
            $("#ppw_loader").show();
            if (!$('input[name="file_uploaded"]').length) {
                var input = $("<input>").attr("type", "hidden").attr("name", "submit_by_ajax").val("true");
                $(".wppw_cart").append(input);
            }
            setTimeout(function () {
                $(event.currentTarget).parents("form.wppw_cart").submit();
            }, 1500);
        });

        $(".wppw_cart").submit(function (event) {
            if ($('input[name="submit_by_ajax"]').length) {
                $("input[name='submit_by_ajax']").remove();
                event.preventDefault();
            } else {
                return true;
            }
            
            var ProductId= '';
            /* old WC version condition start*/
            if($("input[name='add-to-cart']").val() > 0 ){
               ProductId = $("input[name='add-to-cart']").val();
            }
            /* old WC version condition end */

            /* New WC version condition start*/
            if($("button[name='add-to-cart']").val() > 0){
                ProductId = $("button[name='add-to-cart']").val();
            }
            /* New WC version condition end */
                       
            var variation_id='';
            if($('input[name="variation_id"]').val() && $('input[name="variation_id"]').val() !== 0){
                variation_id = $('input[name="variation_id"]').val();
            }
            
            var formData = new FormData();
            formData.append("action", "ppw_uploads");
            var fileInputElement = $(this).find("#ppw_file_upload_id")[0];
            formData.append("file", fileInputElement.files[0]);
            formData.append("name", fileInputElement.files[0].name);
            formData.append("security", woocommerce_price_per_word_params.woocommerce_price_per_word_params_nonce);
            formData.append("product_id", ProductId);
            formData.append("variation_id", variation_id);
            $.ajax({
                url: woocommerce_price_per_word_params.ajax_url,
                type: 'POST',
                data: formData,
                async: false,
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.message == "File successfully uploaded") {
                        var input_two = $("<input>").attr("type", "hidden").attr("name", "file_uploaded").val(obj.url);
                        $(".wppw_cart").append(input_two);
                        $(".ppw_file_upload_div").hide();
                        $("#aewcppw_product_page_message").hide();
                        $(".single_variation_wrap").show();
                        if ($("#ppw_file_container").hasClass("woocommerce-error")) {
                            $('#ppw_file_container').removeClass('woocommerce-error');
                        }
                        $("#ppw_file_container").html(obj.message_content);
                        $("#ppw_file_container").show();
                        var price_string ='';
                        if (typeof (obj.aewcppw_word_character) != "undefined" && obj.aewcppw_word_character !== null && obj.aewcppw_word_character == 'word') {
                            if(woocommerce_price_per_word_params.aewcppw_hide_qty_from_buyer == 'yes'){
                                $(".woocommerce .quantity input[name='quantity']").attr('type','hidden');
                            }                            
                            $(".woocommerce .quantity input[name='quantity']").val(obj.total_word);
                             price_string = 'Price Per Word: ';
                        } else {
                            if(woocommerce_price_per_word_params.aewcppw_hide_qty_from_buyer == 'yes'){
                                $(".woocommerce .quantity input[name='quantity']").attr('type','hidden');
                            }
                            $(".woocommerce .quantity input[name='quantity']").val(obj.total_character);
                             price_string = 'Price Per Character: ';
                        }

                        $(".woocommerce .quantity input[name='quantity']").prop("readonly", true);
                        $(".ppw_total_amount").html(obj.product_price);
                        $(".ppw_total_price").show();
                        $(".single_variation_wrap").show();
                        $(".single_variation_wrap").find('.price').text(price_string + obj.pprice);
                        $(".single_add_to_cart_button").parent('div').show();

                        //show quantity and cart button for the simple product
                        $(".woocommerce .quantity").show();
                        $(".woocommerce .single_add_to_cart_button").show();

                    } else {
                        $("#ppw_file_container").addClass("woocommerce-error");
                        if (typeof (obj.message) != "undefined" && obj.message !== null) {
                            $("#ppw_file_container").html(obj.message);
                        } else {
                            $("#ppw_file_container").html(obj.message_content);
                        }
                        $("#ppw_file_container").show();
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
            $("#ppw_loader").hide();
            return false;
        });

        $("#ppw_remove_file").live("click", function (event) {
            event.preventDefault();
            var data = {
                action: 'ppw_remove',
                security: woocommerce_price_per_word_params.woocommerce_price_per_word_params_nonce,
                value: $("#ppw_remove_file").attr('data_file')
            };
            $.post(woocommerce_price_per_word_params.ajax_url, data, function (response) {
                var obj = $.parseJSON(response);
                if (obj.message == 'File successfully deleted') {
                    $(".ppw_total_price").hide();
                    $(".woocommerce .quantity input[name='quantity']").val(0);
                    $(".woocommerce .quantity input[name='quantity']").prop("readonly", true);
                    $("input[name='file_uploaded']").remove();
                    $("#ppw_file_container").html('');
                    $("#ppw_file_container").hide();
                    $(".ppw_file_upload_div").show();
                    $("#aewcppw_product_page_message").show();
                    $(".single_variation_wrap").hide();
                    location.reload();
                }
            });
            return false;
        });

        $("form.wppw_cart").each(function () {
            if ($(this).hasClass("variations_form")) {
                $(this).find("#aewcppw_product_page_message").hide();
                $(this).find(".ppw_file_upload_div").hide();
            } else if ($(this).find("#aewcppw_product_page_message").length) {

                if ($.trim($(this).find("#ppw_file_container").text()) != '') {
                    $(this).find("#aewcppw_product_page_message").hide();
                    $(this).find(".ppw_file_upload_div").hide();
                    if(woocommerce_price_per_word_params.aewcppw_hide_qty_from_buyer == 'yes'){
                        $(".woocommerce .quantity input[name='quantity']").attr('type','hidden');
                    }
                    $(this).find(".quantity").show();
                    $(this).find(".single_add_to_cart_button").show();
                } else {
                    $(this).find("#aewcppw_product_page_message").show();
                    $(this).find(".ppw_file_upload_div").show();
                    $(this).find(".quantity").hide();
                    $(this).find(".single_add_to_cart_button").hide();
                }
            } else if ($(this).find(".ppw_file_upload_div").length == 0) {
                $(this).find(".single_add_to_cart_button").parent("div").show();
            }
        })
    });
});