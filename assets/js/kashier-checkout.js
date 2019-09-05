/* global wc_kashier_params */
jQuery(function ($) {
    'use strict';
    const wc_kashier_checkout_form = {
        $body: $('body'),
        selectorCCNumber: '#kashier_credit_card_number',
        selectorExpiryDate: '#kashier_expiry_date',
        selectorCCV: '#kashier_ccv',
        tokenizationResponseClassName: 'kashier-ktr',

        init: function () {
            let $form = $('form.woocommerce-checkout');
            if ($form.length) {
                this.$form = $form;
            }

            if (this.isAddPaymentMethodPage()) {
                this.$form = $('form#add_payment_method');
                this.$form.on('submit', this.onSubmit);
            }

            if (! this.$form) {
                return;
            }

            this.$form.on('checkout_place_order_kashier', this.onSubmit);


            this.$body.on('keyup', '#wc-kashier-cc-form input', function () {
                wc_kashier_checkout_form.onCCFormChange();
            });

            wc_kashier_checkout_form._initMasks();
            wc_kashier_checkout_form._initValidators();
        },
        _initMasks: function () {
            $(this.selectorCCNumber).mask('0000 0000 0000 0000');
            $(this.selectorExpiryDate).mask('00/00');
            $(this.selectorCCV).mask('000');
        },
        _initValidators: function () {
            this.$body.on("keyup", this.selectorCCNumber, function () {
                wc_kashier_checkout_form._updateCardBrand($.payform.parseCardType(this.value));
                $(this).removeClass('kashier-invalid');
                if (false === $.payform.validateCardNumber(this.value)) {
                    $(this).addClass('kashier-invalid');
                }
            });

            this.$body.on("keyup", this.selectorExpiryDate, function () {
                $(this).removeClass('kashier-invalid');
                const expiry = $.payform.parseCardExpiry(this.value);
                if (false === $.payform.validateCardExpiry(expiry.month, expiry.year)) {
                    $(this).addClass('kashier-invalid');
                }
            });

            this.$body.on("keyup", this.selectorCCV, function () {
                $(this).removeClass('kashier-invalid');
                if (false === $.payform.validateCardCVC(this.value)) {
                    $(this).addClass('kashier-invalid');
                }
            });
        },
        _updateCardBrand: function (brand) {
            $('input#kashier_card_brand').val(brand);

            const brandClass = {
                'visa': 'kashier-visa-brand',
                'mastercard': 'kashier-mastercard-brand',
                'amex': 'kashier-amex-brand',
                'discover': 'kashier-discover-brand',
                'diners': 'kashier-diners-brand',
                'jcb': 'kashier-jcb-brand',
                'meeza': 'kashier-meeza-brand',
                'unknown': 'kashier-credit-card-brand'
            };

            let imageElement = $('.kashier-card-brand'),
                imageClass = 'kashier-credit-card-brand';

            if (brand in brandClass) {
                imageClass = brandClass[brand];
            }

            // Remove existing card brand class.
            $.each(brandClass, function (index, el) {
                imageElement.removeClass(el);
            });

            imageElement.addClass(imageClass);
        },
        onSubmit() {
            if (wc_kashier_checkout_form.isKashierSavedCardChosen() || wc_kashier_checkout_form.isTokenized()) {
                return true;
            }
            wc_kashier_checkout_form.block();

            // noinspection JSUnresolvedVariable
            const kashier = new Kashier(wc_kashier_params.kashier_url);
            const $last_name = $('#billing_last_name'),
                $first_name = $('#billing_first_name');

            const last_name = $last_name.length ? $last_name.val() : wc_kashier_params.billing_last_name;
            let full_name = $first_name.length ? $first_name.val() : wc_kashier_params.billing_first_name;
            if (last_name) {
                full_name += ' ' + last_name;
            }

            const expiry = $.payform.parseCardExpiry($(wc_kashier_checkout_form.selectorExpiryDate).val());
            const tokenValidity = $('#wc-kashier-new-payment-method').prop('checked') === true || wc_kashier_checkout_form.isAddPaymentMethodPage() ? 'perm' : 'temp';

            // noinspection JSUnresolvedVariable
            kashier.tokenize(new KashierTokenization(
                wc_kashier_params.mid,
                full_name,
                $(wc_kashier_checkout_form.selectorCCNumber).val().replace(/\s+/g, ''),
                $(wc_kashier_checkout_form.selectorCCV).val(),
                expiry.month.toString().padStart(2, '0'),
                expiry.year.toString().slice(2),
                wc_kashier_params.shopper_reference,
                wc_kashier_params.tokenization_hash,
                tokenValidity
            )).then((response) => {
                wc_kashier_checkout_form.$form.append(
                    $('<input type="hidden" />')
                        .addClass(wc_kashier_checkout_form.tokenizationResponseClassName)
                        .attr('name', 'kashier_ktr')
                        .val(JSON.stringify(response.body))
                );
            }).catch((error) => {
                wc_kashier_checkout_form.$form.append(
                    $('<input type="hidden" />')
                        .addClass(wc_kashier_checkout_form.tokenizationResponseClassName)
                        .attr('name', 'kashier_ktr')
                        .val(JSON.stringify(error))
                );

                wc_kashier_checkout_form.$form.unblock();
            }).finally(function() {
                wc_kashier_checkout_form.$form.submit();
            });

            if ( wc_kashier_checkout_form.isAddPaymentMethodPage() ) {
                $( wc_kashier_checkout_form.$form ).off( 'submit', wc_kashier_checkout_form.$form.onSubmit );
            }


            return false;
        },
        isAddPaymentMethodPage: function() {
            return $( 'form#add_payment_method' ).length;
        },
        block: function () {
            wc_kashier_checkout_form.$form.block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },
        isTokenized: function () {
            return 0 < $('input.' + this.tokenizationResponseClassName).length;
        },
        isKashierSavedCardChosen: function () {
            return (
                $('#payment_method_kashier').is(':checked')
                && $('input[name="wc-kashier-payment-token"]').is(':checked')
                && 'new' !== $('input[name="wc-kashier-payment-token"]:checked').val()
            );
        },
        onCCFormChange: function () {
            $('.' + this.tokenizationResponseClassName).remove();
        }
    };

    wc_kashier_checkout_form.init();
});
