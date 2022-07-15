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
*/

/**
* The core plugin class.
*
*
* @since      1.0.0
* @package     product-deal-countdown
* @subpackage  product-deal-countdown/admin
* @author     Alessio Calanchini <ac.calanchini@gmail.com>
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( PDC_PATH . 'includes/data.php' );

class Product_Deal_Countdown_Admin {

    protected $settings;

    public function __construct() {
        $this->settings = new Product_Deal_Countdown_Default_Data();
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
    }

    /**
    * load Language translate
    */

    public function load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'product-deal-countdown' );
        // Global + Frontend Locale
        load_textdomain( 'product-deal-countdown', PRODUCT_DEAL_COUNTDOWN_LANGUAGES . "product-deal-countdown-$locale.mo" );
        load_plugin_textdomain( 'product-deal-countdown', false, PRODUCT_DEAL_COUNTDOWN_LANGUAGES );
    }

    public function init() {
        load_plugin_textdomain( 'product-deal-countdown' );
        $this->load_plugin_textdomain();

    }

    public function admin_menu() {
        add_menu_page( __( 'Product Deal Countdown', 'product-deal-countdown' ), __( 'Product Deal Countdown', 'product-deal-countdown' ), 'manage_options', 'product-deal-countdown',
        array( $this, 'product_deal_countdown_settings' ),
        'dashicons-clock', 2 );
    }

    public function product_deal_countdown_settings() {

        require_once( PDC_PATH . 'admin/views/product-deal-countdown-settings.php' );

    }

    /**
    * Init Script in Admin
    */

    public function admin_enqueue_scripts() {
        $page = isset( $_REQUEST[ 'page' ] ) ? sanitize_text_field( $_REQUEST[ 'page' ] ) : '';
        if ( $page == 'product-deal-countdown' ) {
            global $wp_scripts;
            if ( isset( $wp_scripts->registered[ 'jquery-ui-accordion' ] ) ) {
                unset( $wp_scripts->registered[ 'jquery-ui-accordion' ] );
                wp_dequeue_script( 'jquery-ui-accordion' );
            }
            if ( isset( $wp_scripts->registered[ 'accordion' ] ) ) {
                unset( $wp_scripts->registered[ 'accordion' ] );
                wp_dequeue_script( 'accordion' );
            }
            $scripts = $wp_scripts->registered;
            foreach ( $scripts as $k => $script ) {
                preg_match( '/^\/wp-/i', $script->src, $result );
                if ( count( array_filter( $result ) ) ) {
                    preg_match( '/^(\/wp-content\/plugins|\/wp-content\/themes)/i', $script->src, $result1 );
                    if ( count( array_filter( $result1 ) ) ) {
                        wp_dequeue_script( $script->handle );
                    }
                } else {
                    if ( $script->handle != 'query-monitor' ) {
                        wp_dequeue_script( $script->handle );
                    }
                }
            }

            /*Stylesheet*/
            wp_enqueue_style( 'product-deal-countdown-semantic-checkbox', PDC_URL . 'css/checkbox.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-dropdown', PDC_URL . 'css/dropdown.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-form', PDC_URL . 'css/form.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-segment', PDC_URL . 'css/segment.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-transition', PDC_URL . 'css/transition.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-accordion', PDC_URL . 'css/accordion.min.css' );
            wp_enqueue_style( 'product-deal-countdown-semantic-input', PDC_URL . 'css/input.min.css' );
            wp_enqueue_style( 'product-deal-countdown-admin', PDC_URL . 'css/product-deal-countdown-admin.css', array(), '1.0' );
            
            /*Scripts*/
            wp_enqueue_script( 'product-deal-countdown-semantic-checkbox', PDC_URL . 'js/checkbox.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-semantic-dropdown', PDC_URL . 'js/dropdown.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-semantic-form', PDC_URL . 'js/form.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-semantic-tab', PDC_URL . 'js/tab.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-semantic-transition', PDC_URL . 'js/transition.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-semantic-accordion', PDC_URL . 'js/accordion.min.js', array( 'jquery' ) );
            wp_enqueue_script( 'product-deal-countdown-admin', PDC_URL . 'js/product-deal-countdown-admin.js', array( 'jquery' ), '1.0' );

            /*Color picker*/
            wp_enqueue_script(
                'iris', admin_url( 'js/iris.min.js' ), array(
                    'jquery-ui-draggable',
                    'jquery-ui-slider',
                    'jquery-touch-punch'
                ), false, 1
            );


            
            $id = $this->settings->get_id();
            if ( is_array( $id ) && count( $id ) ) {
                $css = '';

                for ( $i = 0; $i < count( $id );
                $i ++ ) {
                    if ( $this->settings->get_datetime_value_bg_color()[ $i ] ) {
                        $css .= '.pdc-accordion-wrap[data-accordion_id="' . $i . '"] .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle:after{' . esc_attr__( 'background:' ) . $this->settings->get_datetime_value_bg_color()[ $i ] . ';}';
                    }
                    if ( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) {
                        $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle .woo-sctr-value-bar{' . esc_attr__( 'border-color: ' ) . $this->settings->get_countdown_timer_item_border_color()[ $i ] . ';}';
                        $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle .woo-sctr-first50-bar{' . esc_attr__( 'background-color: ' ) . $this->settings->get_countdown_timer_item_border_color()[ $i ] . ';}';
                    }
                    if ( $this->settings->get_datetime_value_font_size()[ $i ] ) {
                        $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle{' . esc_attr__( 'font-size:' ) . $this->settings->get_datetime_value_font_size()[ $i ] . 'px;}';
                    }

                    $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1{';
                    if ( $this->settings->get_countdown_timer_color()[ $i ] ) {
                        $css .= esc_attr__( 'color:' ) . $this->settings->get_countdown_timer_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_countdown_timer_bg_color()[ $i ] ) {
                        $css .= esc_html__( 'background:' ) . $this->settings->get_countdown_timer_bg_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_countdown_timer_padding()[ $i ] ) {
                        $css .= esc_html__( 'padding:' ) . $this->settings->get_countdown_timer_padding()[ $i ] . 'px;';
                    }
                    if ( $this->settings->get_countdown_timer_border_radius()[ $i ] ) {
                        $css .= esc_html__( 'border-radius:' ) . $this->settings->get_countdown_timer_border_radius()[ $i ] . 'px;';
                    }
                    if ( $this->settings->get_countdown_timer_border_color()[ $i ] ) {
                        $css .= esc_html__( 'border: 1px solid ' ) . $this->settings->get_countdown_timer_border_color()[ $i ] . ';';
                    }
                    $css .= '}';
                    $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1 .woo-sctr-shortcode-countdown-value{';
                    if ( $this->settings->get_datetime_value_color()[ $i ] ) {
                        $css .= esc_attr__( 'color:' ) . $this->settings->get_datetime_value_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_datetime_value_bg_color()[ $i ] ) {
                        $css .= esc_attr__( 'background:' ) . $this->settings->get_datetime_value_bg_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_datetime_value_font_size()[ $i ] ) {
                        $css .= esc_attr__( 'font-size:' ) . $this->settings->get_datetime_value_font_size()[ $i ] . 'px;';
                    }
                    $css .= '}';
                    $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1 .woo-sctr-shortcode-countdown-text{';
                    if ( $this->settings->get_datetime_unit_color()[ $i ] ) {
                        $css .= esc_attr__( 'color:' ) . $this->settings->get_datetime_unit_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_datetime_unit_bg_color()[ $i ] ) {
                        $css .= esc_attr__( 'background:' ) . $this->settings->get_datetime_unit_bg_color()[ $i ] . ';';
                    }
                    if ( $this->settings->get_datetime_unit_font_size()[ $i ] ) {
                        $css .= esc_attr__( 'font-size:' ) . $this->settings->get_datetime_unit_font_size()[ $i ] . 'px;';
                    }
                    $css .= '}';

                    $css1 = '';
                    if ( $this->settings->get_countdown_timer_item_height()[ $i ] ) {
                        $css1 .= esc_html__( 'height:' ) . $this->settings->get_countdown_timer_item_height()[ $i ] . 'px;';
                    }
                    if ( $this->settings->get_countdown_timer_item_width()[ $i ] ) {
                        $css1 .= esc_html__( 'width:' ) . $this->settings->get_countdown_timer_item_width()[ $i ] . 'px;';
                    }
                    if ( $this->settings->get_countdown_timer_item_border_radius()[ $i ] ) {
                        $css1 .= esc_html__( 'border-radius:' ) . $this->settings->get_countdown_timer_item_border_radius()[ $i ] . 'px;';
                    }
                    if ( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) {
                        $css1 .= esc_html__( 'border:1px solid ' ) . $this->settings->get_countdown_timer_item_border_color()[ $i ] . ';';
                    }
                    if ( $css1 ) {
                        $css .= '.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-wrap.woo-sctr-shortcode-countdown-style-1 .woo-sctr-shortcode-countdown-unit,.pdc-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-wrap.woo-sctr-shortcode-countdown-style-2 .woo-sctr-shortcode-countdown-value{' . $css1 . '}';
                    }

                }

                wp_add_inline_style( 'product-deal-countdown-admin', $css );
            }
        }
    }

}

new Product_Deal_Countdown_Admin();