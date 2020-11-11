/* global wc_kashier_params */
jQuery(function ($) {
    'use strict';
    var wc_kashier_3ds = {
        $body: $('body'),
        $woocommerceNoticesWrapper: $('.woocommerce-notices-wrapper'),

        init: function () {
            wc_kashier_3ds._init3DsListener();
        },
        _init3DsListener: function () {
            if (window.addEventListener) {
                addEventListener("message", wc_kashier_3ds._3DsFrameMessageListener, false)
            } else {
                attachEvent("onmessage", wc_kashier_3ds._3DsFrameMessageListener)
            }

            if (wc_kashier_params.is_order_pay_page === '1') {
                $("#" + wc_kashier_params.kashier_form_id).submit();
                $("#" + wc_kashier_params.kashier_iframe_id).addClass('show').removeClass('hide');
            }
        },
        _3DsFrameMessageListener: function (e) {
            var iFrameMessage = e.data;

            // noinspection EqualityComparisonWithCoercionJS
            if (iFrameMessage.message == "merchantStoreRedirect" && iFrameMessage.params) {
                $("#kashier_3ds_iframe").addClass('hide').removeClass('show');

                wc_kashier_3ds.$body.addClass('kashier-processing').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                iFrameMessage.params.order_key = wc_kashier_params.current_order_key;

                $.ajax({
                    type: 'POST',
                    url: wc_kashier_params.callback_3ds_url,
                    data: iFrameMessage.params,
                    dataType: 'json',
                    success: function (result) {
                        if (-1 === result.redirect.indexOf('https://') || -1 === result.redirect.indexOf('http://')) {
                            window.location = result.redirect;
                        } else {
                            window.location = decodeURI(result.redirect);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        
                        //BEGIN Handling for Print for "Serial Numbers created" in Easy Serial numbers plugin
                        console.log(jqXHR.responseText)
                        if(jqXHR.responseText &&  jqXHR.responseText.split(">{").length > 1) 
                        {
                         let badResult =  jqXHR.responseText.split(">{");
                         let  parsedResult = JSON.parse("{"+badResult[1]);
                         console.log(parsedResult)
                     
                         if( parsedResult.redirect &&  (-1 === parsedResult.redirect.indexOf('https://') || -1 === parsedResult.redirect.indexOf('http://')))     {
                           console.log(parsedResult.redirect)
                         window.location = parsedResult.redirect;
                                } 
                        }
                         //END Handling for Print for "Serial Numbers created" in Easy Serial numbers plugin
                         
                         
                     },
                    always: function () {
                        wc_kashier_3ds.$body.removeClass('kashier-processing').unblock();
                    }
                });
            }
        },
        _showError: function (errorMessage) {
            $( '.woocommerce-NoticeGroup-kashier, .woocommerce-error, .woocommerce-message' ).remove();
            wc_kashier_3ds.$woocommerceNoticesWrapper.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-kashier"><div class="woocommerce-error"> ' + errorMessage + '</div></div>' );
        }
    };
    wc_kashier_3ds.init();
});
