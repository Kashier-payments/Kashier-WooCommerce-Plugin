<?php
/**
 * Plugin Name: WooCommerce Kashier Gateway 
 * Description: Acceptd online payments on your store.
 * Author: Kashier
 * Author URI: https://kashier.io/
 * Version: 1.1.0
 * Requires at least: 4.4
 * Tested up to: 5.2.1
 * WC requires at least: 2.6
 * WC tested up to: 3.6.4
 * Text Domain: woocommerce-gateway-kashier
 * Domain Path: /languages
 * Plugin URI: https://github.com/Kashier-payments/Kashier-WooCommerce-Plugin/
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

// phpcs:disable WordPress.Files.FileName

/**
 * WooCommerce fallback notice.
 */
function woocommerce_kashier_missing_wc_notice()
{
    echo '<div class="error"><p><strong>' . sprintf(esc_html__('Kashier requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-gateway-kashier'),
            '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>') . '</strong></p></div>';
}

add_action('plugins_loaded', 'woocommerce_gateway_kashier_init');

function woocommerce_gateway_kashier_init()
{
    add_filter( 'woocommerce_subscriptions_is_duplicate_site', '__return_false' );

    load_plugin_textdomain('woocommerce-gateway-kashier', false, plugin_basename(__DIR__) . '/languages');

    if (!class_exists('WooCommerce')) {

        add_action('admin_notices', 'woocommerce_kashier_missing_wc_notice');

        return;
    }

    if (!class_exists('WC_Kashier')) :
        /**
         * Required minimums and constants
         */
        define('WC_KASHIER_VERSION', '1.0.0');
        define('WC_KASHIER_MIN_PHP_VER', '5.6.0');
        define('WC_KASHIER_MIN_WC_VER', '2.6.0');
        define('WC_KASHIER_MAIN_FILE', __FILE__);
        define('WC_KASHIER_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__))));
        define('WC_KASHIER_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

        class WC_Kashier
        {
            /**
             * @var WC_Kashier $instance Singleton The reference the *Singleton* instance of this class
             */
            private static $instance;

            /**
             * Returns the *Singleton* instance of this class.
             *
             * @return WC_Kashier The *Singleton* instance.
             */
            public static function get_instance()
            {
                if (null === self::$instance) {
                    self::$instance = new self();
                }

                return self::$instance;
            }

            /**
             * Private clone method to prevent cloning of the instance of the
             * *Singleton* instance.
             *
             * @return void
             */
            private function __clone()
            {
            }

            /**
             * Private unserialize method to prevent unserializing of the *Singleton*
             * instance.
             *
             * @return void
             */
            private function __wakeup()
            {
            }

            /**
             * Protected constructor to prevent creating a new instance of the
             * *Singleton* via the `new` operator from outside of this class.
             */
            private function __construct()
            {
                add_action('admin_init', array($this, 'install'));
                $this->init();
            }

            /**
             * Init the plugin after plugins_loaded so environment variables are set.
             */
            public function init()
            {
                require_once __DIR__ . '/lib/vendor/autoload.php';
                require_once __DIR__ . '/includes/class-wc-kashier-exception.php';
                require_once __DIR__ . '/includes/class-wc-kashier-logger-factory.php';
                require_once __DIR__ . '/includes/class-wc-kashier-logger.php';
                require_once __DIR__ . '/includes/class-wc-kashier-helper.php';
                require_once __DIR__ . '/includes/class-wc-gateway-kashier.php';

                add_filter('woocommerce_payment_gateways', array($this, 'add_gateways'));
                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));

                if (version_compare(WC_VERSION, '3.4', '<')) {
                    add_filter( 'woocommerce_get_sections_checkout', array( $this, 'filter_gateway_order_admin' ) );
                }
            }

            /**
             * Updates the plugin version in db
             */
            public function update_plugin_version()
            {
                delete_option('wc_kashier_version');
                update_option('wc_kashier_version', WC_KASHIER_VERSION);
            }

            /**
             * Handles upgrade routines.
             */
            public function install()
            {
                if (!is_plugin_active(plugin_basename(__FILE__))) {
                    return;
                }

                if (!defined('IFRAME_REQUEST') && (WC_KASHIER_VERSION !== get_option('wc_kashier_version'))) {
                    do_action('woocommerce_kashier_updated');

                    if (!defined('WC_KASHIER_INSTALLING')) {
                        define('WC_KASHIER_INSTALLING', true);
                    }

                    $this->update_plugin_version();
                }
            }

            /**
             * Adds plugin action links.
             */
            public function plugin_action_links($links)
            {
                $plugin_links = array(
                    '<a href="admin.php?page=wc-settings&tab=checkout&section=kashier">' . esc_html__('Settings', 'woocommerce-gateway-kashier') . '</a>'
                );

                return array_merge($plugin_links, $links);
            }

            /**
             * Add the gateways to WooCommerce.
             */
            public function add_gateways($methods)
            {
                $methods[] = 'WC_Gateway_Kashier';

                return $methods;
            }

            /**
             * Modifies the order of the gateways displayed in admin.
             */
            public function filter_gateway_order_admin($sections)
            {
                unset($sections['kashier']);

                $sections['kashier'] = 'Kashier';

                return $sections;
            }
        }

        WC_Kashier::get_instance();
    endif;
}
