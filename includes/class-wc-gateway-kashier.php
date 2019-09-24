<?php
use ITeam\Kashier\Core\KashierConstants;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WC_Gateway_Kashier class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_Kashier extends WC_Payment_Gateway_CC
{
    /**
     * @var bool
     */
    public $saved_cards;
    /**
     * @var string
     */
    public $api_key;
    /**
     * @var bool
     */
    public $testmode;
    /**
     * @var string
     */
    public $merchant_id;
    /**
     * @var bool
     */
    public $logging_enabled;
    /**
     * @var \ITeam\Kashier\Rest\ApiContext
     */
    public $apiContext;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'kashier';
        $this->method_title = WC_Kashier_Helper::get_localized_message('payment_method_title');
        $this->method_description = sprintf(WC_Kashier_Helper::get_localized_message('payment_method_description'), 'https://kashier.io/', 'https://merchant.kashier.io/en/signup');
        $this->has_fields = true;
        $this->supports = [
            'products',
            'tokenization',
            'add_payment_method',
        ];

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Get setting values.
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        $this->testmode = 'yes' === $this->get_option('testmode');
        $this->logging_enabled = 'yes' === $this->get_option('logging');
        $this->merchant_id = $this->get_option('merchant_id');
        $this->saved_cards = 'yes' === $this->get_option('saved_cards');
        $this->api_key = $this->testmode ? $this->get_option('test_api_key') : $this->get_option('api_key');

        $this->apiContext = new \ITeam\Kashier\Rest\ApiContext(
            $this->merchant_id,
            new \ITeam\Kashier\Auth\KashierKey($this->api_key)
        );

        $this->apiContext->setConfig([
            'mode' => $this->testmode ? 'sandbox' : 'live',
            'log.LogEnabled' => $this->logging_enabled,
            'log.LogLevel' => $this->testmode ? 'debug' : 'info',
            'log.AdapterFactory' => WC_Kashier_Logger_Factory::class
        ]);

        // Hooks.
        add_action('wp_enqueue_scripts', [$this, 'payment_scripts']);
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
        add_action('set_logged_in_cookie', [$this, 'set_cookie_on_current_request']);
        add_action('woocommerce_receipt_' . $this->id, [$this, 'kashier_receipt_page']);
        add_action('woocommerce_api_wc_gateway_kashier', [$this, 'check_3ds_response']);
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = require __DIR__ . '/admin/kashier-settings.php';
    }

    /**
     *
     */
    public function check_3ds_response()
    {
        $localizedErrors = WC_Kashier_Helper::get_localized_messages();

        $response = [
            'result' => 'failure',
            'redirect' => home_url()
        ];

        $order_id = wc_get_order_id_by_order_key(urldecode(wc_clean($_POST['order_key'])));
        $order = wc_get_order($order_id);

        if (is_a($order, WC_Order::class)) {
            if (strtoupper($_POST['status']) === 'SUCCESS') {
                $this->_payment_success_handler($order, wc_clean($_POST['response']['transactionId']));

                $response['result'] = 'success';
                $response['redirect'] = $this->get_return_url($order);
            } else {
                wc_add_notice($localizedErrors['please_check_card_info'], 'error');
                $response['redirect'] = $order->get_checkout_payment_url();
            }
        } else {
            wc_add_notice($localizedErrors['order_not_found'], 'error');
        }

        wp_send_json($response);
    }

    /**
     * @param WC_Order $order
     * @param string $transaction_id
     */
    protected function _payment_success_handler($order, $transaction_id)
    {
        $order->payment_complete($transaction_id);
        $message = sprintf(__('Kashier charge complete (Transaction ID: %s)', 'woocommerce-gateway-kashier'), $transaction_id);
        $order->add_order_note($message);

        // Remove cart.
        WC()->cart->empty_cart();
    }

    /**
     * @param $order_id
     */
    public function kashier_receipt_page($order_id)
    {
        $order = new WC_Order($order_id);
        $order_meta = get_post_meta($order->get_id(), '');
        ?>
        <form id="<?php echo $this->id; ?>_3ds_form" target="kashier_3ds_iframe" method="POST"
              action="<?php echo $order_meta['_kashier_3ds_acsUrl'][0] ?>" accept-charset="UTF-8">
            <input id="paReq" type="hidden" name="PaReq" value="<?php echo $order_meta['_kashier_3ds_paReq'][0] ?>">
            <input id="term" type="hidden" name="TermUrl"
                   value="<?php echo $order_meta['_kashier_3ds_processACSRedirectURL'][0] ?>">
            <input id="md" type="hidden" name="MD" value="">
        </form>
        <iframe name="<?php echo $this->id ?>_3ds_iframe" id="<?php echo $this->id ?>_3ds_iframe" class="hide"></iframe>
        <!--        <a href="--><?php //echo $order->get_checkout_payment_url()
        ?><!--" class="button kashier-checkout-back-btn">--><?php //echo __('Change credit card info', 'woocommerce-gateway-kashier')
        ?><!--</a>-->
        <?php
    }

    /**
     * Checks if gateway should be available to use.
     */
    public function is_available()
    {
        if (is_add_payment_method_page() && !$this->saved_cards) {
            return false;
        }

        return parent::is_available();
    }

    /**
     * Get_icon function.
     *
     *
     *
     * @return string
     */
    public function get_icon()
    {
        $icons = $this->payment_icons();

        $icons_str = '';

        $icons_str .= isset($icons['visa']) ? $icons['visa'] : '';
        $icons_str .= isset($icons['mastercard']) ? $icons['mastercard'] : '';
        $icons_str .= isset($icons['meeza']) ? $icons['meeza'] : '';

        return apply_filters('woocommerce_gateway_icon', $icons_str, $this->id);
    }

    /**
     * All payment icons that work with Kashier. Some icons references
     * WC core icons.
     *
     * @return array
     */
    public function payment_icons()
    {
        return apply_filters(
            'wc_kashier_payment_icons',
            [
                'credit-card' => '<img src="' . WC_KASHIER_PLUGIN_URL . '/assets/images/credit-card.svg" class="kashier-credit-card-icon kashier-icon" alt="Credit Card" />',
                'visa' => '<img src="' . WC_KASHIER_PLUGIN_URL . '/assets/images/visa.svg" class="kashier-visa-icon kashier-icon" alt="Visa" />',
                'mastercard' => '<img src="' . WC_KASHIER_PLUGIN_URL . '/assets/images/mastercard.svg" class="kashier-mastercard-icon kashier-icon" alt="Mastercard" />',
                'meeza' => '<img src="' . WC_KASHIER_PLUGIN_URL . '/assets/images/meeza.svg" class="kashier-meeza-icon kashier-icon" alt="Meeza" />',
            ]
        );
    }

    /**
     * Payment form on checkout page
     */
    public function payment_fields()
    {
        $display_tokenization = $this->supports('tokenization') && is_checkout() && $this->saved_cards;
        $description = $this->get_description();
        $description = !empty($description) ? $description : '';

        if ($this->testmode) {
            $description .= ' ' . __('TEST MODE ENABLED', 'woocommerce-gateway-kashier');
        }

        $description = trim($description);

        echo apply_filters('wc_kashier_description', wpautop(wp_kses_post($description)), $this->id);

        if ($display_tokenization) {
            $this->tokenization_script();
            $this->saved_payment_methods();
        }

        $this->elements_form();

        if (apply_filters('wc_kashier_display_save_payment_method_checkbox', $display_tokenization) && !is_add_payment_method_page() && !isset($_GET['change_payment_method'])) { // wpcs: csrf ok.

            $this->save_payment_method_checkbox();
        }

        echo '<div id="secured-by-kashier-container"><img src="' . WC_KASHIER_PLUGIN_URL . '/assets/images/secured-by-kashier.png" alt="Secured by Kashier"/></div>';

        do_action('wc_kashier_cards_payment_fields', $this->id);
    }

    /**
     * Renders the Kashier elements form.
     */
    public function elements_form()
    {
        ?>
        <fieldset id="wc-<?php echo esc_attr($this->id); ?>-cc-form" class="wc-credit-card-form wc-payment-form"
                  style="background:transparent;">
            <?php do_action('woocommerce_credit_card_form_start', $this->id); ?>
            <div class="form-row form-row-wide">
                <label for="kashier-card-element"><?php esc_html_e('Card Number', 'woocommerce-gateway-kashier'); ?>
                    <span class="required">*</span></label>
                <div class="kashier-card-group">
                    <div class="wc-kashier-elements-field">
                        <input id="<?php echo $this->id . '_credit_card_number' ?>" autocomplete="cc-number"
                               autocorrect="off" spellcheck="false" inputmode="numeric"
                               aria-label="Credit or debit card number" placeholder="1234 1234 1234 1234"
                               aria-placeholder="1234 1234 1234 1234" aria-invalid="false">
                    </div>
                    <i class="kashier-credit-card-brand kashier-card-brand"></i>
                </div>
            </div>

            <div class="form-row form-row-first">
                <label for="kashier-exp-element"><?php esc_html_e('Expiry Date', 'woocommerce-gateway-kashier'); ?>
                    <span class="required">*</span></label>
                <div class="wc-kashier-elements-field">
                    <input id="<?php echo $this->id . '_expiry_date' ?>" autocomplete="cc-exp" autocorrect="off"
                           spellcheck="false" inputmode="numeric"
                           aria-label="Credit or debit card expiration date" placeholder="MM/YY"
                           aria-placeholder="MM/YY" aria-invalid="false" maxlength="5">
                </div>
            </div>

            <div class="form-row form-row-last">
                <label for="kashier-cvc-element"><?php esc_html_e('Card Code (CCV)', 'woocommerce-gateway-kashier'); ?>
                    <span class="required">*</span></label>
                <div class="wc-kashier-elements-field">
                    <input id="<?php echo $this->id . '_ccv' ?>" autocomplete="cc-csc" autocorrect="off"
                           spellcheck="false" inputmode="numeric"
                           aria-label="Credit or debit card CVC/CVV" placeholder="123" aria-placeholder="CVC"
                           aria-invalid="false" maxlength="3">
                </div>
            </div>
            <div class="clear"></div>
            <br/>
            <input id="<?php echo $this->id . '_card_brand' ?>" name="<?php echo $this->id . '_card_brand' ?>" type="hidden">
            <?php do_action('woocommerce_credit_card_form_end', $this->id); ?>
            <div class="clear"></div>
        </fieldset>
        <?php
    }

    /**
     * Payment_scripts function.
     *
     * Outputs scripts used for kashier payment
     */
    public function payment_scripts()
    {
        if (!is_product() && !is_cart() && !is_checkout() && !isset($_GET['pay_for_order']) && !is_add_payment_method_page() && !isset($_GET['change_payment_method'])) { // wpcs: csrf ok.
            return;
        }

        // If Kashier is not enabled bail.
        if ('no' === $this->enabled) {
            return;
        }

        // If keys are not set bail.
        if (!$this->are_keys_set()) {
            WC_Kashier_Logger::addLog('Keys are not set correctly.');
            return;
        }

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        wp_register_style('kashier_styles', plugins_url('assets/css/kashier-styles.css', WC_KASHIER_MAIN_FILE), [], WC_KASHIER_VERSION);
        wp_enqueue_style('kashier_styles');

        wp_register_script('lib_kashier_model_checkout', plugins_url('lib/assets/js/model/kashier-checkout' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery-payment'], WC_KASHIER_VERSION, true);
        wp_register_script('lib_kashier_model_tokenization', plugins_url('lib/assets/js/model/kashier-tokenization' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery-payment'], WC_KASHIER_VERSION, true);
        wp_register_script('lib_kashier', plugins_url('lib/assets/js/kashier' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery-payment'], WC_KASHIER_VERSION, true);

        wp_register_script('woocommerce_kashier_mask', plugins_url('assets/js/jquery.mask' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery'], WC_KASHIER_VERSION, true);
        wp_register_script('woocommerce_kashier_payform', plugins_url('assets/js/jquery.payform' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery'], WC_KASHIER_VERSION, true);
        wp_register_script('woocommerce_kashier_checkout', plugins_url('assets/js/kashier-checkout' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery-payment'], WC_KASHIER_VERSION, true);
        wp_register_script('woocommerce_kashier_3ds', plugins_url('assets/js/kashier-3ds' . $suffix . '.js', WC_KASHIER_MAIN_FILE), ['jquery-payment'], WC_KASHIER_VERSION, true);

        $kashier_params = [];

        $kashier_params['kashier_form_id'] = $this->id . '_3ds_form';
        $kashier_params['kashier_iframe_id'] = $this->id . '_3ds_iframe';
        $kashier_params['is_order_pay_page'] = is_wc_endpoint_url('order-pay');

        $kashier_params['kashier_url'] = $this->testmode ? KashierConstants::REST_SANDBOX_ENDPOINT : KashierConstants::REST_LIVE_ENDPOINT;
        $kashier_params['mid'] = $this->apiContext->getMerchantId();
        $kashier_params['shopper_reference'] = get_current_user_id();
        $kashier_params['currency'] = get_woocommerce_currency();

        $tokenizationRequest = new \ITeam\Kashier\Api\Data\TokenizationRequest();
        $tokenizationRequest->setShopperReference(get_current_user_id());
        $tokenizationRequestCipher = new \ITeam\Kashier\Security\TokenizationRequestCipher($this->apiContext, $tokenizationRequest);

        $kashier_params['tokenization_hash'] = $tokenizationRequestCipher->encrypt();

        if ($kashier_params['is_order_pay_page']) {
            $order_id = wc_get_order_id_by_order_key(urldecode($_GET['key']));
            $order = wc_get_order($order_id);
            if (is_a($order, WC_Order::class)) {
                $kashier_params['billing_first_name'] = WC_Kashier_Helper::is_wc_lt('3.0') ? $order->billing_first_name : $order->get_billing_first_name();
                $kashier_params['billing_last_name'] = WC_Kashier_Helper::is_wc_lt('3.0') ? $order->billing_last_name : $order->get_billing_last_name();

                $kashier_params['current_order_key'] = urldecode($_GET['key']);
                $kashier_params['return_url'] = esc_url_raw($this->get_return_url($order));
                $kashier_params['callback_3ds_url'] = add_query_arg('wc-api', 'WC_Gateway_Kashier', home_url('/'));
            }
        }

        // Merge localized messages to be use in JS.
        $kashier_params = array_merge($kashier_params, WC_Kashier_Helper::get_localized_messages());
        $kashier_params = apply_filters('wc_kashier_params', $kashier_params);

        wp_localize_script('woocommerce_kashier_checkout', 'wc_kashier_params', $kashier_params);
        wp_localize_script('woocommerce_kashier_3ds', 'wc_kashier_params', $kashier_params);

        $this->tokenization_script();

        wp_enqueue_script('lib_kashier_model_checkout');
        wp_enqueue_script('lib_kashier_model_tokenization');
        wp_enqueue_script('lib_kashier');

        wp_enqueue_script('woocommerce_kashier_mask');
        wp_enqueue_script('woocommerce_kashier_payform');
        wp_enqueue_script('woocommerce_kashier_checkout');
        wp_enqueue_script('woocommerce_kashier_3ds');
    }

    /**
     * Checks if keys are set.
     *
     *
     * @return bool
     */
    public function are_keys_set()
    {
        if (empty($this->api_key)) {
            return false;
        }

        return true;
    }

    /**
     * @return array|mixed|object
     */
    public function parseTokenizationResponse()
    {
        return json_decode(stripslashes(wc_clean($_POST['kashier_ktr'])), true);
    }

    /**
     * Process the payment
     *
     * @param int $order_id Reference.
     * @param bool $retry Should we retry on fail.
     * @param bool $force_save_source Force save the payment source.
     * @param bool $previous_error Any error message from previous request.
     *
     * @return array
     * @throws \ITeam\Kashier\Exception\KashierConfigurationException
     * @throws \ITeam\Kashier\Exception\KashierConnectionException
     */
    public function process_payment($order_id, $retry = true, $force_save_source = false, $previous_error = false)
    {
        $order = wc_get_order($order_id);
        $redirectUrl = $this->get_return_url($order);
        try {

            if (0 >= $order->get_total()) {
                return $this->complete_free_order($order);
            }

            // This will throw exception if not valid.
            $this->validate_minimum_order_amount($order);

            WC_Kashier_Logger::addLog("Info: Begin processing payment for order $order_id for the amount of {$order->get_total()}");

            $checkoutRequest = new \ITeam\Kashier\Api\Data\CheckoutRequest();
            $checkoutRequest
                ->setOrderId($order->get_id())
                ->setAmount($order->get_total())
                ->setCurrency($order->get_currency())
                ->setShopperReference(get_current_user_id())
                ->setDisplay('en');

            $requestCipher = new \ITeam\Kashier\Security\CheckoutWithTokenRequestCipher($this->apiContext, $checkoutRequest);

            $token = WC_Payment_Tokens::get($this->get_selected_card_token());

            $submittedToken = $this->parseTokenizationResponse();

            if ($this->is_using_saved_card() && $token !== null) {
                $checkoutRequest->setCardToken($token->get_token());
            } else if (is_array($submittedToken) && ! empty($submittedToken)) {
                if (isset($submittedToken['response']['ccvToken'])) {
                    $checkoutRequest->setCcvToken($submittedToken['response']['ccvToken']);
                }
                $checkoutRequest->setCardToken($submittedToken['response']['cardToken']);
            } else {
                throw new WC_Kashier_Exception('Payment processing failed', WC_Kashier_Helper::get_localized_message('payment_failed'));
            }

            $checkout = new \ITeam\Kashier\Api\Checkout();
            $checkout->setCheckoutRequest($checkoutRequest);

            $response = $checkout->create($this->apiContext, $requestCipher);

            WC_Kashier_Logger::addLog('Processing response: ' . print_r($response, true));

            $responseData = $response->getResponse();

            if (($response->is3DsRequired() || $response->isSuccess()) && $this->is_save_new_card()) {
                $tokenData = $submittedToken['response'];

                $this->save_token($tokenData['cardToken'],
                    strtolower(wc_clean($_POST['kashier_card_brand'])),
                    substr($tokenData['maskedCard'], -4),
                    $tokenData['expiry_month'],
                    $tokenData['expiry_year']
                );
            }

            if ($response->is3DsRequired()) {
                $order->add_order_note(__('3DSecure payment verification required', 'woocommerce-gateway-kashier'));

                foreach ($responseData['card']['3DSecure'] as $key => $value) {
                    WC_Kashier_Helper::is_wc_lt('3.0') ? update_post_meta($order_id, '_kashier_3ds_' . $key, $value) : $order->update_meta_data('_kashier_3ds_' . $key, $value);
                }

                $redirectUrl = $order->get_checkout_payment_url(true);
            } else if ($response->isSuccess()) {
                $this->_payment_success_handler($order, $responseData['transactionId']);
                do_action('wc_gateway_kashier_process_response', $response, $order);
            } else {
                $localized_message = WC_Kashier_Helper::get_localized_message('payment_failed');
                $order->add_order_note($localized_message . ' ' . __('Error: ') . $response->getErrorMessage());
                throw new WC_Kashier_Exception(print_r($response, true), $localized_message);
            }

            if (is_callable([$order, 'save'])) {
                $order->save();
            }

            return [
                'result' => 'success',
                'redirect' => $redirectUrl,
            ];

        } catch (Exception $e) {
            wc_add_notice($e->getLocalizedMessage(), 'error');
            WC_Kashier_Logger::addLog('Error: ' . $e->getMessage());

            do_action('wc_gateway_kashier_process_payment_error', $e, $order);

            $order->update_status('failed');

            return [
                'result' => 'failure',
                'redirect' => '',
            ];
        }
    }

    /**
     * Completes an order without a positive value.
     *
     *
     * @param WC_Order $order The order to complete.
     * @return array Redirection data for `process_payment`.
     */
    public function complete_free_order($order)
    {
        $order->payment_complete();

        // Remove cart.
        WC()->cart->empty_cart();

        // Return thank you page redirect.
        return [
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        ];
    }

    /**
     * Validates that the order meets the minimum order amount
     * set by Kashier.
     *
     * @param object $order
     * @throws WC_Kashier_Exception
     */
    public function validate_minimum_order_amount($order)
    {
        if ($order->get_total() * 100 < WC_Kashier_Helper::get_minimum_amount()) {
            throw new WC_Kashier_Exception('Did not meet minimum amount', sprintf(WC_Kashier_Helper::get_localized_message('minimum_amount_error'), wc_price(WC_Kashier_Helper::get_minimum_amount() / 100)));
        }
    }

    /**
     * @return array|string
     */
    protected function get_selected_card_token()
    {
        $tokenKey = 'wc-' . $this->id . '-payment-token';
        return wc_clean($_POST[$tokenKey]);
    }

    /**
     * @return bool
     */
    public function is_using_saved_card()
    {
        $tokenKey = 'wc-' . $this->id . '-payment-token';
        return (isset($_POST[$tokenKey]) && 'new' !== $_POST[$tokenKey]);
    }

    /**
     * @return bool
     */
    public function is_save_new_card()
    {
        $newKey = 'wc-' . $this->id . '-new-payment-method';
        return isset($_POST[$newKey]) && !empty($_POST[$newKey]);
    }

    /**
     * @param $token
     * @param $cardBrand
     * @param $last4
     * @param $expiry_month
     * @param $expiry_year
     */
    protected function save_token($token, $cardBrand, $last4, $expiry_month, $expiry_year)
    {
        $wc_token = new WC_Payment_Token_CC();
        $wc_token->set_token($token);
        $wc_token->set_gateway_id($this->id);
        $wc_token->set_card_type($cardBrand);
        $wc_token->set_last4($last4);
        $wc_token->set_expiry_month($expiry_month);
        $wc_token->set_expiry_year(WC_Kashier_Helper::expiry_year_format($expiry_year));
        $wc_token->set_user_id(get_current_user_id());
        $wc_token->save();
    }

    /**
     * Proceed with current request using new login session (to ensure consistent nonce).
     */
    public function set_cookie_on_current_request($cookie)
    {
        $_COOKIE[LOGGED_IN_COOKIE] = $cookie;
    }

    /**
     * @return array|void
     */
    public function add_payment_method()
    {
        try {
            $response = $this->parseTokenizationResponse();
            $response = $response['response'];
        } catch (\Exception $e) {
            wc_add_notice($e->getMessage(), 'error');
            WC_Kashier_Logger::addLog('Add payment method Error: ' . $e->getMessage());
            return;
        }

        $this->save_token($response['cardToken'],
            strtolower(wc_clean($_POST['kashier_card_brand'])),
            substr($response['maskedCard'], -4),
            $response['expiry_month'],
            $response['expiry_year']
        );

        return [
            'result' => 'success',
            'redirect' => wc_get_endpoint_url('payment-methods'),
        ];
    }
}
