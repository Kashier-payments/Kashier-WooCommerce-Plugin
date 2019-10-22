<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Provides static methods as helpers.
 */
class WC_Kashier_Helper
{
    /**
     * Localize Kashier messages based on code
     * @return array
     */
    public static function get_localized_messages()
    {
        return apply_filters(
            'wc_kashier_localized_messages',
            array(
                'payment_method_title' => __('Kashier', 'woocommerce-gateway-kashier'),
                'payment_method_description' => __('Online Payments by <a href="%1$s">Kashier</a> <a href="%2$s">Signup</a> to obtain your test MID and Credentials.', 'woocommerce-gateway-kashier'),
                'please_check_card_info' => __('Please check your card info.', 'woocommerce-gateway-kashier'),
                'order_not_found' => __('Requested order not found', 'woocommerce-gateway-kashier'),
                'payment_failed' => __('Payment processing failed. Please try again.', 'woocommerce-gateway-kashier'),
                'minimum_amount_error' => __('Sorry, the minimum allowed order total is %1$s to use this payment method.', 'woocommerce-gateway-kashier')
            )
        );
    }

    public static function get_localized_message($message_key)
    {
        $messages = self::get_localized_messages();
        return $messages[$message_key];
    }

    /**
     * Checks Kashier minimum order value authorized
     */
    public static function get_minimum_amount()
    {
        return 0;
    }

    /**
     * Checks if WC version is less than passed in version.
     *
     * @param string $version Version to check against.
     * @return bool
     */
    public static function is_wc_lt($version)
    {
        return version_compare(WC_VERSION, $version, '<');
    }

    public static function expiry_year_format($year, $from_format = 'y', $to_format = 'Y')
    {
        $dt = DateTime::createFromFormat($from_format, $year);
        return $dt->format($to_format);
    }
}
