<?php
/**
* Plugin Name:       WooCommerce Product Peal Countdown
* Plugin URI:        https://example.com/plugins/the-basics/
* Description:       
* Version:           1.0
* Requires at least: 6.0
* Requires PHP:      7.2
* Author:            Alessio Calanchini
* Author URI:
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Update URI:        https://example.com/my-plugin/
* Text Domain:       woocommerce-product-deal-countdown
* Domain Path:       /languages
*
*
* @since      1.0.0
* @package    woocommerce-product-deal-countdown
* @author     Alessio Calanchini <ac.calanchini@gmail.com>
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Woocommerce_Product_Deal_Countdown_Init' ) ) {
    class Woocommerce_Product_Deal_Countdown_Init {
        
        
        /**
        * Definiamo costruttore della classe
        */

        function __construct() {

            // Declare the methods
            $this->define_constants();
            $this->load_dependencies();
          

        }
        /**
        * Define constants
        */

        public function define_constants() {
            define( 'WCPDC_PATH', plugin_dir_path( __FILE__ ) );//path fino alla cartella plugin
            define( 'WCPDC_URL', plugin_dir_url( __FILE__ ) );//url fino alla cartella plugin
            define( 'WCPDC_VERSION', '1.0.0' );
            define( 'WCPDC_TEXTDOMAIN', 'woocommerce-product-deal-countdown' );
        }

        public function is_woocommerce_active() {
            if ( class_exists( 'WooCommerce' ) ) {
                return true;
            } else {
                return false;
            }
        }

        private function load_dependencies() {

            /**
            * The class responsible ...
            */
            require_once( WCPDC_PATH . 'admin/class_wc_product_deal_countdown.php' );
           

           

        }

        // Activate method
        public static function activate() {
        }

        //Deactivate method
        public static function deactivate() {
        }

        // Uninstall method
        public static function uninstall() {
        }

    }
}

if ( class_exists( 'Woocommerce_Product_Deal_Countdown_Init' ) ) {
    register_activation_hook( __FILE__, array( 'Woocommerce_Product_Deal_Countdown_Init', 'activate' ) );
    register_deactivation_hook( __FILE__, array( 'Woocommerce_Product_Deal_Countdown_Init', 'deactivate' ) );
    register_uninstall_hook( __FILE__, array( 'Woocommerce_Product_Deal_Countdown_Init', 'uninstall' ) );
    $wc_order_stats = new Woocommerce_Product_Deal_Countdown_Init();
}