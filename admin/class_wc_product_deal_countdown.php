<?php

/**
* The file that defines the core plugin class
*
* A class definition that includes attributes and functions used across both the
* public-facing side of the site and the admin area.
*
* @link       ac.com
* @since      1.0.0
*
* @package    woocommerce-product-deal-countdowns
* @subpackage woocommerce-product-deal-countdown/admin
*/

/**
* The core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since      1.0.0
* @package     woocommerce-product-deal-countdown
* @subpackage  woocommerce-product-deal-countdown/admin
* @author     Alessio Calanchini <ac.calanchini@gmail.com>
*/

if ( class_exists( 'woocommerce' ) ) {
    class Wc_Product_Deal_Countdown extends WC_Product_Simple {

        public function __construct() {
     
        }

    }
    new Wc_Product_Deal_Countdown();
}