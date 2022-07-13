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
        $this->settings = new Product_Deal_countdown_default_Data();
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
		load_plugin_textdomain( 'sales-countdown-timer' );
		$this->load_plugin_textdomain();
	
	}

    public function admin_menu() {
		add_menu_page( __( 'Product Deal Countdown', 'product-deal-countdown' ), __( 'Product Deal Countdown', 'product-deal-countdown' ), 'manage_options', 'product-deal-countdown', 
        array( $this, 'product_deal_countdown_settings'),
        'dashicons-clock', 2 );

		// add_submenu_page(
		// 	'product-deal-countdown',
		// 	__( 'Checkout Countdown', 'product-deal-countdown' ),
		// 	__( 'Checkout Countdown', 'product-deal-countdown' ),
		// 	'manage_options',
		// 	'product-deal-countdown-checkout',
		// 	array( $this, 'settings_checkout_countdown' )
		// );
	}

    public function product_deal_countdown_settings(){

		$id        = $this->settings->get_id();
		// $div_class = is_rtl() ? 'woo-sctr-wrap woo-sctr-wrap-rtl' : 'woo-sctr-wrap';
        $div_class = 'woo-pdc-wrap';
		?>
        <div class="<?php echo esc_attr( $div_class ); ?>">
            <h2 class=""><?php esc_html_e( 'Product Deal Countdown', 'product-deal-countdown' ) ?></h2>
            <form class="vi-ui form" method="post">
				<?php
				wp_nonce_field( 'woo_ctr_settings_page_save', 'woo_ctr_nonce_field' );
				if ( get_transient( '_sales_countdown_timer_demo_product_init' ) ) {
					$sale_products     = get_transient( 'wc_products_onsale' );
					$default_countdown = count( $id ) ? $id[0] : 'salescountdowntimer';
					$now               = current_time( 'timestamp', true );
					$product_url       = '';
					if ( false === $sale_products ) {
						$products_args = array(
							'post_type'      => 'product',
							'status'         => 'publish',
							'posts_per_page' => - 1,
							'meta_query'     => array(
								'relation' => 'AND',
								array(
									'key'     => '_sale_price',
									'value'   => '',
									'compare' => '!=',
								),
								array(
									'key'     => '_sale_price_dates_to',
									'value'   => $now,
									'compare' => '>',
								)
							),
						);
						$the_query     = new WP_Query( $products_args );
						if ( $the_query->have_posts() ) {
							while ( $the_query->have_posts() ) {
								$the_query->the_post();
								$product_id = get_the_ID();
								update_post_meta( $product_id, '_woo_ctr_select_countdown_timer', $default_countdown );
								if ( ! $product_url ) {
									$product_url = get_permalink( $product_id );
								}
							}
						}
						wp_reset_postdata();
					} elseif ( is_array( $sale_products ) && count( $sale_products ) ) {
						foreach ( $sale_products as $product_id ) {
							update_post_meta( $product_id, '_woo_ctr_select_countdown_timer', $default_countdown );
							if ( ! $product_url ) {
								$product_url = get_permalink( $product_id );
							}
						}
					}
					if ( $product_url ) {
						echo esc_html__( 'See your very first sales countdown timer ', 'sales-countdown-timer' ) . '<a href="' . $product_url . '" target="_blank">' . esc_html__( 'here.', 'sales-countdown-timer' ) . '</a>';
						delete_transient( '_sales_countdown_timer_demo_product_init' );
					}
				}
				if ( is_array( $id ) && count( $id ) ) {
					?>
					<?php
					for ( $i = 0; $i < sizeof( $id ); $i ++ ) {
						switch ( $this->settings->get_time_separator()[ $i ] ) {
							case 'dot':
								$time_separator = '.';
								break;
							case 'comma':
								$time_separator = ',';
								break;
							case 'colon':
								$time_separator = ':';
								break;
							default:
								$time_separator = '';
						}
						?>
                        <div class="woo-sctr-accordion-wrap woo-sctr-accordion-wrap-<?php echo esc_attr( $i ); ?> vi-ui segment"
                             data-accordion_id="<?php echo esc_attr( $i ); ?>">
                            <div class="woo-sctr-accordion">
                                <div class="vi-ui toggle checkbox">
                                    <input type="hidden" name="woo_ctr_active[]"
                                           class="woo-sctr-active"
                                           value="<?php echo esc_attr( $this->settings->get_active()[ $i ] ); ?>">
                                    <input type="checkbox"
                                           class="woo-sctr-active" <?php echo $this->settings->get_active()[ $i ] ? 'checked' : ''; ?>><label>
                                </div>
                                <span class="woo-sctr-accordion-name"><?php echo esc_html( $this->settings->get_names()[ $i ] ); ?></span>

                                <span class="woo-sctr-short-description">
                                    <span class="woo-sctr-short-description-from"><?php echo esc_html__( 'From: ', 'sales-countdown-timer' ) ?>
                                        <span class="woo-sctr-short-description-from-date"><?php echo esc_html( $this->settings->get_sale_from_date()[ $i ] ) ?></span>&nbsp;
                                        <span class="woo-sctr-short-description-from-time"><?php echo esc_html( $this->settings->get_sale_from_time()[ $i ] ); ?></span>
                                    </span>
                                    <span class="woo-sctr-short-description-to"><?php echo esc_html__( 'To: ', 'sales-countdown-timer' ) ?>
                                        <span class="woo-sctr-short-description-to-date"><?php echo esc_html( $this->settings->get_sale_to_date()[ $i ] ) ?></span>&nbsp;
                                        <span class="woo-sctr-short-description-to-time"><?php echo esc_html( $this->settings->get_sale_to_time()[ $i ] ); ?></span>
                                    </span>
                                </span>
                                <div class="woo-sctr-shortcode-text">
                                    <span><?php echo esc_html__( 'Shortcode: ', 'sales-countdown-timer' ) ?></span><span><?php echo '[sales_countdown_timer id="' . $id[ $i ] . '"]'; ?></span>
                                </div>
                                <span class="woo-sctr-button-edit">
                                    <span class="woo-sctr-short-description-copy-shortcode vi-ui button"><?php esc_html_e( 'Copy shortcode', 'sales-countdown-timer' ); ?></span>
                                    <span class="woo-sctr-button-edit-duplicate vi-ui positive button"><?php esc_html_e( 'Duplicate', 'sales-countdown-timer' ) ?></span>
                                    <span class="woo-sctr-button-edit-remove vi-ui negative button"><?php esc_html_e( 'Remove', 'sales-countdown-timer' ) ?></span>
                                </span>
                            </div>
                            <div class="woo-sctr-panel vi-ui styled fluid accordion" id="woo-sctr-panel-accordion">
                                <div class="title  <?php if ( $this->settings->get_active()[ $i ] ) {
									echo 'active';
								} ?>">
                                    <i class="dropdown icon"></i>
									<?php esc_html_e( 'General settings', 'sales-countdown-timer' ) ?>
                                </div>
                                <div class="content  <?php if ( $this->settings->get_active()[ $i ] ) {
									echo 'active';
								} ?>">

                                    <div class="field">
                                        <label><?php esc_html_e( 'Name', 'sales-countdown-timer' ) ?></label>
                                        <input type="hidden" name="woo_ctr_id[]" class="woo-sctr-id"
                                               value="<?php echo esc_attr( $id[ $i ] ); ?>">
                                        <input type="text" name="woo_ctr_name[]" class="woo-sctr-name"
                                               value="<?php echo esc_attr( $this->settings->get_names()[ $i ] ); ?>">
                                    </div>

                                    <h4 class="vi-ui dividing header">
                                        <label><?php esc_html_e( 'Schedule time for shortcode usage', 'sales-countdown-timer' ) ?></label>
                                    </h4>
                                    <div class="field"
                                         data-tooltip="<?php esc_html_e( 'These values are used for shortcode only. To schedule sale for product please go to admin product.', 'sales-countdown-timer' ) ?>">
                                        <div class="two fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'From', 'sales-countdown-timer' ) ?></label>
                                                <div class="two fields">
                                                    <div class="field">
                                                        <input type="date"
                                                               name="woo_ctr_sale_from_date[]"
                                                               class="woo-sctr-sale-from-date woo-sctr-sale-date <?php if ( $this->settings->get_time_type()[ $i ] == 'loop' ) {
															       echo 'woo-sctr-hide-date';
														       } ?>"
                                                               value="<?php echo esc_url( $this->settings->get_sale_from_date()[ $i ] ) ?>">
                                                    </div>
                                                    <div class="field">
                                                        <input type="time"
                                                               name="woo_ctr_sale_from_time[]"
                                                               class="woo-sctr-sale-from-time"
                                                               value="<?php echo $this->settings->get_sale_from_time()[ $i ] ? esc_attr( $this->settings->get_sale_from_time()[ $i ] ) : '00:00' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'To', 'sales-countdown-timer' ) ?></label>
                                                <div class="two fields">
                                                    <div class="field">
                                                        <input type="date" name="woo_ctr_sale_to_date[]"
                                                               class="woo-sctr-sale-to-date woo-sctr-sale-date <?php if ( $this->settings->get_time_type()[ $i ] == 'loop' ) {
															       echo 'woo-sctr-hide-date';
														       } ?>"
                                                               value="<?php echo esc_attr( $this->settings->get_sale_to_date()[ $i ] ) ?>">
                                                    </div>
                                                    <div class="field">
                                                        <input type="time" name="woo_ctr_sale_to_time[]"
                                                               class="woo-sctr-sale-to-time"
                                                               value="<?php echo $this->settings->get_sale_to_time()[ $i ] ? esc_attr( $this->settings->get_sale_to_time()[ $i ] ) : '00:00' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <div class="vi-ui labeled right action input">
                                                    <div class="vi-ui basic label"><?php esc_html_e( 'Countdown evergreen', 'sales-countdown-timer' ); ?></div>
                                                    <a class="vi-ui button yellow" href="https://1.envato.market/962d3" target="_blank">
														<?php esc_html_e( 'Unlock This Feature', 'sales-countdown-timer' ); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <div class="vi-ui labeled right action input">
                                                    <div class="vi-ui basic label"><?php esc_html_e( 'Restart countdown after', 'sales-countdown-timer' ); ?></div>
                                                    <a class="vi-ui button yellow" href="https://1.envato.market/962d3" target="_blank">
														<?php esc_html_e( 'Unlock This Feature', 'sales-countdown-timer' ); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="title">
                                    <i class="dropdown icon"></i>
									<?php esc_html_e( 'Design', 'sales-countdown-timer' ) ?>
                                </div>
                                <div class=" content">
									<?php
									$message = $this->settings->get_message()[ $i ];
									$text    = explode( '{countdown_timer}', $message );
									if ( count( $text ) < 2 ) {
										$text_before = $text_after = '';
									} else {
										$text_before = $text[0];
										$text_after  = $text[1];
									}
									?>
                                    <div class="field">
                                        <label><?php esc_html_e( 'Message', 'sales-countdown-timer' ) ?></label>

                                        <input type="text" name="woo_ctr_message[]"
                                               class="woo-sctr-message"
                                               value="<?php echo esc_attr( $this->settings->get_message()[ $i ] ); ?>">
                                    </div>
                                    <div class="field">
                                        <p>{countdown_timer}
                                            - <?php esc_html_e( 'The countdown timer that you set on tab design', 'sales-countdown-timer' ) ?></p>
                                        <p class="woo-sctr-warning-message-countdown-timer <?php if ( count( $text ) >= 2 ) {
											esc_attr_e( 'woo-sctr-hidden-class' );
										} ?>"><?php esc_html_e( 'The countdown timer will not show if message does not include {countdown_timer}', 'sales-countdown-timer' ) ?></p>
                                    </div>
                                    <div class="equal width fields">
                                        <div class="field">
                                            <label><?php esc_html_e( 'Time separator', 'sales-countdown-timer' ) ?></label>
                                            <select name="woo_ctr_time_separator[]"
                                                    class="woo-sctr-time-separator vi-ui dropdown">
                                                <option value="blank" <?php selected( $this->settings->get_time_separator() [ $i ], 'blank' ); ?>><?php esc_html_e( 'Blank', 'sales-countdown-timer' ) ?></option>
                                                <option value="colon" <?php selected( $this->settings->get_time_separator() [ $i ], 'colon' ); ?>><?php esc_html_e( 'Colon(:)', 'sales-countdown-timer' ) ?></option>
                                                <option value="comma" <?php selected( $this->settings->get_time_separator()[ $i ], 'comma' ); ?>><?php esc_html_e( 'Comma(,)', 'sales-countdown-timer' ) ?></option>
                                                <option value="dot" <?php selected( $this->settings->get_time_separator()[ $i ], 'dot' ); ?>><?php esc_html_e( 'Dot(.)', 'sales-countdown-timer' ) ?></option>
                                            </select>
                                        </div>
                                        <div class="field">
                                            <label><?php esc_html_e( 'Datetime format style', 'sales-countdown-timer' ) ?></label>
                                            <select name="woo_ctr_count_style[]"
                                                    class="woo-sctr-count-style vi-ui dropdown">
                                                <option value="1" <?php selected( $this->settings->get_count_style()[ $i ], 1 ); ?>><?php esc_html_e( '01 days 02 hrs 03 mins 04 secs', 'sales-countdown-timer' ) ?></option>
                                                <option value="2" <?php selected( $this->settings->get_count_style()[ $i ], 2 ); ?>><?php esc_html_e( '01 days 02 hours 03 minutes 04 seconds', 'sales-countdown-timer' ) ?></option>
                                                <option value="3" <?php selected( $this->settings->get_count_style()[ $i ], 3 ); ?>><?php esc_html_e( '01:02:03:04', 'sales-countdown-timer' ) ?></option>
                                                <option value="4" <?php selected( $this->settings->get_count_style()[ $i ], 4 ); ?>><?php esc_html_e( '01d:02h:03m:04s', 'sales-countdown-timer' ) ?></option>
                                            </select>
                                        </div>
                                    </div>
									<?php
									$datetime_unit_position = isset( $this->settings->get_datetime_unit_position() [ $i ] ) ? $this->settings->get_datetime_unit_position() [ $i ] : 'bottom';
									$animation_style        = isset( $this->settings->get_animation_style()[ $i ] ) ? $this->settings->get_animation_style()[ $i ] : 'default';
									?>
                                    <div class="equal width fields">
                                        <div class="field">
                                            <label><?php esc_html_e( 'Datetime unit position', 'sales-countdown-timer' ) ?></label>
                                            <select name="woo_ctr_datetime_unit_position[]"
                                                    class="woo-sctr-datetime-unit-position vi-ui dropdown">
                                                <option value="top" <?php selected( $datetime_unit_position, 'top' ); ?>><?php esc_html_e( 'Top', 'sales-countdown-timer' ) ?></option>
                                                <option value="bottom" <?php selected( $datetime_unit_position, 'bottom' ); ?>><?php esc_html_e( 'Bottom', 'sales-countdown-timer' ) ?></option>
                                            </select>
                                        </div>
                                        <div class="field">
                                            <label><?php esc_html_e( 'Animation style', 'sales-countdown-timer' ) ?></label>
                                            <select name="woo_ctr_animation_style[]"
                                                    class="woo-sctr-animation-style vi-ui dropdown">
                                                <option value="default" <?php selected( $animation_style, 'default' ); ?>><?php esc_html_e( 'Default', 'sales-countdown-timer' ) ?></option>
                                                <option value="slide" <?php selected( $animation_style, 'slide' ); ?>><?php esc_html_e( 'Slide', 'sales-countdown-timer' ) ?></option>
                                            </select>
                                        </div>
                                    </div>
									<?php
									switch ( $this->settings->get_count_style()[ $i ] ) {
										case '1':
											$date   = esc_html__( 'days', 'sales-countdown-timer' );
											$hour   = esc_html__( 'hrs', 'sales-countdown-timer' );
											$minute = esc_html__( 'mins', 'sales-countdown-timer' );
											$second = esc_html__( 'secs', 'sales-countdown-timer' );
											break;
										case '2':
											$date   = esc_html__( 'days', 'sales-countdown-timer' );
											$hour   = esc_html__( 'hours', 'sales-countdown-timer' );
											$minute = esc_html__( 'minutes', 'sales-countdown-timer' );
											$second = esc_html__( 'seconds', 'sales-countdown-timer' );
											break;
										case '3':
											$date   = esc_html__( '', 'sales-countdown-timer' );
											$hour   = esc_html__( '', 'sales-countdown-timer' );
											$minute = esc_html__( '', 'sales-countdown-timer' );
											$second = esc_html__( '', 'sales-countdown-timer' );
											break;
										default:
											$date   = esc_html__( 'd', 'sales-countdown-timer' );
											$hour   = esc_html__( 'h', 'sales-countdown-timer' );
											$minute = esc_html__( 'm', 'sales-countdown-timer' );
											$second = esc_html__( 's', 'sales-countdown-timer' );
									}

									?>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header">
                                            <label><?php esc_html_e( 'Display type', 'sales-countdown-timer' ) ?></label>
                                        </h4>
                                        <input type="hidden"
                                               name="woo_ctr_display_type[]"
                                               class="woo-sctr-display-type"
                                               value="<?php echo esc_attr( $this->settings->get_display_type()[ $i ] ); ?>">

                                        <div class="two fields">

                                            <div class="field">
                                                <div class="vi-ui segment">
                                                    <div class="fields">
                                                        <div class="three wide field">
                                                            <div class="vi-ui toggle checkbox">

                                                                <input type="radio"
                                                                       name="woo_ctr_display_type_<?php echo esc_attr( $i ); ?>"
                                                                       class="woo-sctr-display-type-checkbox"
                                                                       value="1" <?php checked( $this->settings->get_display_type()[ $i ], '1' ) ?>><label></label>
                                                            </div>
                                                        </div>
                                                        <div class="thirteen wide field">
                                                            <div class="woo-sctr-shortcode-wrap-wrap">
                                                                <div class="woo-sctr-shortcode-wrap">

                                                                    <div class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-1">
                                                                        <div class="woo-sctr-shortcode-countdown">
                                                                            <div class="woo-sctr-shortcode-countdown-1">
                                                                                <span class="woo-sctr-shortcode-countdown-text-before"><?php echo esc_html( $text_before ); ?></span>
                                                                                <div class="woo-sctr-shortcode-countdown-2">
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                                <span class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <div class="vi-ui segment">
                                                    <div class="fields">
                                                        <div class="three wide field">
                                                            <div class="vi-ui toggle checkbox">

                                                                <input type="radio"
                                                                       name="woo_ctr_display_type_<?php echo esc_attr( $i ); ?>"
                                                                       class="woo-sctr-display-type-checkbox"
                                                                       value="2" <?php checked( $this->settings->get_display_type()[ $i ], '2' ) ?>><label></label>
                                                            </div>
                                                        </div>
                                                        <div class="thirteen wide field">
                                                            <div class="woo-sctr-shortcode-wrap-wrap">
                                                                <div class="woo-sctr-shortcode-wrap">

                                                                    <div class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-2">
                                                                        <div class="woo-sctr-shortcode-countdown">
                                                                            <div class="woo-sctr-shortcode-countdown-1">
                                                                                <span class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                                                                <div class="woo-sctr-shortcode-countdown-2">
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                    <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                    <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                        <span class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                                            <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'sales-countdown-timer' ); ?></span>
                                                                                            <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                                <span class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field">

                                            <div class="vi-ui segment">
                                                <div class="fields">
                                                    <div class="three wide field">
                                                        <div class="vi-ui toggle checkbox">

                                                            <input type="radio"
                                                                   name="woo_ctr_display_type_<?php echo esc_attr( $i ); ?>"
                                                                   class="woo-sctr-display-type-checkbox"
                                                                   value="3" <?php checked( $this->settings->get_display_type()[ $i ], '3' ) ?>><label></label>
                                                        </div>
                                                    </div>
                                                    <div class="ten wide field">
                                                        <div class="woo-sctr-shortcode-wrap-wrap woo-sctr-shortcode-wrap-wrap-inline">

                                                            <span class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                                            <span class="woo-sctr-shortcode-countdown-1">
                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                    <span class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                        <span class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'sales-countdown-timer' ); ?></span>
                                                                        <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $date ); ?></span>
                                                                    </span>
                                                                </span>
                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                    <span class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                        <span class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'sales-countdown-timer' ); ?></span>
                                                                        <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $hour ); ?></span>
                                                                    </span>
                                                                </span>
                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                    <span class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                        <span class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'sales-countdown-timer' ); ?></span>
                                                                        <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $minute ); ?></span>
                                                                    </span>
                                                                </span>
                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                    <span class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                        <span class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'sales-countdown-timer' ); ?></span>
                                                                        <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $second ); ?></span>
                                                                    </span>
                                                                </span>
                                                            </span>
                                                            <span class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="three wide field">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field">

                                            <div class="vi-ui segment">
                                                <div class="fields">
                                                    <div class="three wide field">
                                                        <div class="vi-ui toggle checkbox">

                                                            <input type="radio"
                                                                   name="woo_ctr_display_type_<?php echo esc_attr( $i ); ?>"
                                                                   class="woo-sctr-display-type-checkbox"
                                                                   value="4" <?php checked( $this->settings->get_display_type()[ $i ], '4' ) ?>><label></label>
                                                        </div>
                                                    </div>
                                                    <div class="ten wide field">
                                                        <div class="woo-sctr-shortcode-wrap-wrap">
                                                            <div class="woo-sctr-shortcode-wrap">

                                                                <div class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-4">
                                                                    <div class="woo-sctr-shortcode-countdown">
                                                                        <div class="woo-sctr-shortcode-countdown-1">
                                                                            <span class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                                                            <div class="woo-sctr-shortcode-countdown-2">
                                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                    <span class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                                        <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                        <div class="woo-sctr-progress-circle">
                                                                                            <span class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '10', 'sales-countdown-timer' ); ?></span>
                                                                                            <div class="woo-sctr-left-half-clipper">
                                                                                                <div class="woo-sctr-first50-bar"></div>
                                                                                                <div class="woo-sctr-value-bar"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                                    </span>
                                                                                </span>
                                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                    <span class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                                        <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                        <div class="woo-sctr-progress-circle">
                                                                                            <span class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'sales-countdown-timer' ); ?></span>
                                                                                            <div class="woo-sctr-left-half-clipper">
                                                                                                <div class="woo-sctr-first50-bar"></div>
                                                                                                <div class="woo-sctr-value-bar"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                                    </span>
                                                                                </span>
                                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                    <span class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                                        <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                        <div class="woo-sctr-progress-circle">
                                                                                            <span class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '30', 'sales-countdown-timer' ); ?></span>
                                                                                            <div class="woo-sctr-left-half-clipper">
                                                                                                <div class="woo-sctr-first50-bar"></div>
                                                                                                <div class="woo-sctr-value-bar"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                                    </span>
                                                                                </span>
                                                                                <span class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                                <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                                    <span class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                                        <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top" <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                        <div class="woo-sctr-progress-circle woo-sctr-over50">
                                                                                            <span class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '40', 'sales-countdown-timer' ); ?></span>
                                                                                            <div class="woo-sctr-left-half-clipper">
                                                                                                <div class="woo-sctr-first50-bar"></div>
                                                                                                <div class="woo-sctr-value-bar"></div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <span class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom" <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                            <span class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="three wide field">
                                                    </div>
                                                </div>
                                                <div class="vi-ui toggle checkbox">
													<?php
													$smooth_animation = isset( $this->settings->get_circle_smooth_animation()[ $i ] ) ? $this->settings->get_circle_smooth_animation()[ $i ] : '';
													?>
                                                    <input type="hidden" name="woo_ctr_circle_smooth_animation[]"
                                                           class="woo-sctr-circle-smooth-animation"
                                                           value="<?php echo esc_attr( $smooth_animation ); ?>">
                                                    <input type="checkbox"
                                                           class="woo-sctr-circle-smooth-animation-check"
                                                           value="1" <?php checked( $smooth_animation, '1' ) ?>><label><?php esc_html_e( 'Use smooth animation for circle', 'sales-countdown-timer' ) ?></label>
                                                </div>
                                                <p><?php esc_html_e( '(*)Countdown timer items Border radius, Height and Width are not applied to this type.', 'sales-countdown-timer' ) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header">
                                            <label><?php esc_html_e( 'Countdown timer', 'sales-countdown-timer' ) ?></label>
                                        </h4>
                                        <div class="three fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-countdown-timer-color"
                                                       name="woo_ctr_countdown_timer_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Background', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-countdown-timer-bg-color"
                                                       name="woo_ctr_countdown_timer_bg_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_bg_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_bg_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Border color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-countdown-timer-border-color"
                                                       name="woo_ctr_countdown_timer_border_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_border_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_border_color()[ $i ] ) ?>">
                                            </div>

                                            <div class="field">
                                                <label><?php esc_html_e( 'Padding(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-countdown-timer-padding"
                                                       name="woo_ctr_countdown_timer_padding[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_padding()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Border radius', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-countdown-timer-border-radius"
                                                       name="woo_ctr_countdown_timer_border_radius[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_border_radius()[ $i ] ) ?>">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header">
                                            <label><?php esc_html_e( 'Countdown timer items', 'sales-countdown-timer' ) ?></label>
                                        </h4>
                                        <div class="three fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Border color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-countdown-timer-item-border-color"
                                                       name="woo_ctr_countdown_timer_item_border_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Border radius(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-countdown-timer-item-border-radius"
                                                       name="woo_ctr_countdown_timer_item_border_radius[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_radius()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Height(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-countdown-timer-item-height"
                                                       name="woo_ctr_countdown_timer_item_height[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_height()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Width(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-countdown-timer-item-width"
                                                       name="woo_ctr_countdown_timer_item_width[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_width()[ $i ] ) ?>">
                                            </div>


                                        </div>
                                    </div>

                                    <div class="field">
                                        <h4 class="vi-ui dividing header">
                                            <label><?php esc_html_e( 'Datetime value', 'sales-countdown-timer' ) ?></label>
                                        </h4>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-datetime-value-color"
                                                       name="woo_ctr_datetime_value_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_value_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_datetime_value_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Background', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-datetime-value-bg-color"
                                                       name="woo_ctr_datetime_value_bg_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_value_bg_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_datetime_value_bg_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Font size(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-datetime-value-font-size"
                                                       name="woo_ctr_datetime_value_font_size[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_value_font_size()[ $i ] ) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header">
                                            <label><?php esc_html_e( 'Datetime unit', 'sales-countdown-timer' ) ?></label>
                                        </h4>
                                        <div class=" equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-datetime-unit-color"
                                                       name="woo_ctr_datetime_unit_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_unit_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_datetime_unit_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Background', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-datetime-unit-bg-color"
                                                       name="woo_ctr_datetime_unit_bg_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_unit_bg_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_datetime_unit_bg_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Font size(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number"
                                                       class="woo-sctr-datetime-unit-font-size"
                                                       name="woo_ctr_datetime_unit_font_size[]"
                                                       min="0"
                                                       value="<?php echo esc_attr( $this->settings->get_datetime_unit_font_size()[ $i ] ) ?>">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="title">
                                    <i class="dropdown icon"></i>
									<?php esc_html_e( 'WooCommerce Product', 'sales-countdown-timer' ) ?>
                                </div>
                                <div class="content">
                                    <div class="field">

                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Make countdown timer sticky when scroll', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui toggle checkbox">
                                                    <input type="hidden" name="woo_ctr_stick_to_top[]"
                                                           class="woo-sctr-stick-to-top"
                                                           value="<?php echo isset( $this->settings->get_stick_to_top()[ $i ] ) ? esc_attr( $this->settings->get_stick_to_top()[ $i ] ) : ''; ?>">
                                                    <input type="checkbox"
                                                           class="woo-sctr-stick-to-top-check" <?php echo ( isset( $this->settings->get_stick_to_top()[ $i ] ) && $this->settings->get_stick_to_top()[ $i ] ) ? 'checked' : ''; ?>><label></label>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Position on single product page', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui input"
                                                     data-tooltip="<?php esc_attr_e( 'Position of countdown timer of main product on single product page(Can not set position for variations)', 'sales-countdown-timer' ) ?>">
                                                    <select name="woo_ctr_position[]"
                                                            class="woo-sctr-position vi-ui fluid dropdown">
                                                        <option value="before_price" <?php selected( $this->settings->get_position()[ $i ], 'before_price' ); ?>><?php esc_html_e( 'Before price', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_price" <?php selected( $this->settings->get_position()[ $i ], 'after_price' ); ?>><?php esc_html_e( 'After price', 'sales-countdown-timer' ) ?></option>
                                                        <option value="before_saleflash" <?php selected( $this->settings->get_position()[ $i ], 'before_saleflash' ); ?>><?php esc_html_e( 'Before sale flash', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_saleflash" <?php selected( $this->settings->get_position()[ $i ], 'after_saleflash' ); ?>><?php esc_html_e( 'After sale flash', 'sales-countdown-timer' ) ?></option>
                                                        <option value="before_cart" <?php selected( $this->settings->get_position()[ $i ], 'before_cart' ); ?>><?php esc_html_e( 'Before cart', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_cart" <?php selected( $this->settings->get_position()[ $i ], 'after_cart' ); ?>><?php esc_html_e( 'After cart', 'sales-countdown-timer' ) ?></option>
                                                        <option value="product_image" <?php selected( $this->settings->get_position()[ $i ], 'product_image' ); ?>><?php esc_html_e( 'Product image', 'sales-countdown-timer' ) ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Position on archive page', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui input"
                                                     data-tooltip="<?php esc_attr_e( 'Position of countdown timer on shop page, category page and related products', 'sales-countdown-timer' ) ?>">
                                                    <select name="woo_ctr_archive_page_position[]"
                                                            class="woo-sctr-archive-page-position vi-ui fluid dropdown">
                                                        <option value="before_price" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_price' ); ?>><?php esc_html_e( 'Before price', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_price" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_price' ); ?>><?php esc_html_e( 'After price', 'sales-countdown-timer' ) ?></option>
                                                        <option value="before_saleflash" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_saleflash' ); ?>><?php esc_html_e( 'Before sale flash', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_saleflash" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_saleflash' ); ?>><?php esc_html_e( 'After sale flash', 'sales-countdown-timer' ) ?></option>
                                                        <option value="before_cart" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_cart' ); ?>><?php esc_html_e( 'Before cart', 'sales-countdown-timer' ) ?></option>
                                                        <option value="after_cart" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_cart' ); ?>><?php esc_html_e( 'After cart', 'sales-countdown-timer' ) ?></option>
                                                        <option value="product_image" <?php selected( $this->settings->get_archive_page_position()[ $i ], 'product_image' ); ?>><?php esc_html_e( 'Product image', 'sales-countdown-timer' ) ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Show on shop page', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui toggle checkbox">
                                                    <input type="hidden" name="woo_ctr_shop_page[]"
                                                           class="woo-sctr-shop-page"
                                                           value="<?php echo esc_attr( $this->settings->get_shop_page()[ $i ] ); ?>">
                                                    <input type="checkbox"
                                                           class="woo-sctr-shop-page-check" <?php echo $this->settings->get_shop_page()[ $i ] ? 'checked' : ''; ?>><label></label>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Show on category page', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui toggle checkbox">
                                                    <input type="hidden" name="woo_ctr_category_page[]"
                                                           class="woo-sctr-category-page"
                                                           value="<?php echo esc_attr( $this->settings->get_category_page()[ $i ] ); ?>">
                                                    <input type="checkbox"
                                                           class="woo-sctr-category-page-check" <?php echo $this->settings->get_category_page()[ $i ] ? 'checked' : ''; ?>><label></label>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Reduce size of countdown timer on shop/category page and on mobile(single product) by', 'sales-countdown-timer' ) ?></label>
                                                <div class="inline field">
                                                    <input type="number" name="woo_ctr_size_on_archive_page[]" min="30"
                                                           max="100"
                                                           class="woo-sctr-related-products"
                                                           value="<?php echo isset( $this->settings->get_size_on_archive_page()[ $i ] ) ? esc_attr( $this->settings->get_size_on_archive_page()[ $i ] ) : '75'; ?>">%
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header"><?php esc_html_e( 'Upcoming sale', 'sales-countdown-timer' ) ?></h4>
                                        <div class="fields">
                                            <div class="three wide field">
                                                <label><?php esc_html_e( 'Enable', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui toggle checkbox">
                                                    <input type="hidden" name="woo_ctr_upcoming[]"
                                                           class="woo-sctr-upcoming"
                                                           value="<?php echo esc_attr( $this->settings->get_upcoming()[ $i ] ); ?>">
                                                    <input type="checkbox"
                                                           class="woo-sctr-upcoming-check" <?php echo $this->settings->get_upcoming()[ $i ] ? 'checked' : ''; ?>><label></label>
                                                </div>
                                            </div>
                                            <div class="thirteen wide field">
                                                <label><?php esc_html_e( 'Upcoming sale message', 'sales-countdown-timer' ) ?></label>

                                                <input type="text" name="woo_ctr_upcoming_message[]"
                                                       class="woo-sctr-upcoming-message"
                                                       value="<?php echo esc_attr( $this->settings->get_upcoming_message()[ $i ] ); ?>">
                                                <p>{countdown_timer}
                                                    - <?php esc_html_e( 'The countdown timer that you set on tab design', 'sales-countdown-timer' ) ?></p>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="field">
                                        <h4 class="vi-ui dividing header"><?php esc_html_e( 'Progress bar', 'sales-countdown-timer' ) ?></h4>
                                        <div class="field">
                                            <label><?php esc_html_e( 'Progress bar message', 'sales-countdown-timer' ) ?></label>
                                            <div class="vi-ui input">
                                                <input type="text" name="woo_ctr_progress_bar_message[]"
                                                       class="woo-sctr-progress-bar-message"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_message()[ $i ] ) ?>">
                                            </div>

                                        </div>
                                        <div class="field">
                                            <p>{quantity_left} - <?php esc_html_e( 'Number of products left' ) ?></p>
                                            <p>{quantity_sold} - <?php esc_html_e( 'Number of products sold' ) ?></p>
                                            <p>{percentage_left}
                                                - <?php esc_html_e( 'Percentage of products left' ) ?></p>
                                            <p>{percentage_sold}
                                                - <?php esc_html_e( 'Percentage of products sold' ) ?></p>
                                            <p>{goal}
                                                - <?php esc_html_e( 'The goal that you set on single product' ) ?></p>
                                        </div>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Progress bar type', 'sales-countdown-timer' ) ?></label>
                                                <div class="vi-ui input"
                                                     data-tooltip="<?php esc_attr_e( 'If select increase, the progress bar fill will increase each time the product is bought and vice versa', 'sales-countdown-timer' ) ?>">
                                                    <select name="woo_ctr_progress_bar_type[]"
                                                            class="woo-sctr-progress-bar-type vi-ui fluid dropdown">
                                                        <option value="increase" <?php selected( $this->settings->get_progress_bar_type()[ $i ], 'increase' ); ?>
                                                                data-tooltip="asdasd"><?php esc_html_e( 'Increase', 'sales-countdown-timer' ) ?></option>
                                                        <option value="decrease" <?php selected( $this->settings->get_progress_bar_type()[ $i ], 'decrease' ); ?>><?php esc_html_e( 'Decrease', 'sales-countdown-timer' ) ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Order status', 'sales-countdown-timer' ) ?></label>
                                                <input type="hidden" name="woo_ctr_progress_bar_order_status[]"
                                                       value="<?php $this->settings->get_progress_bar_order_status()[ $i ] ?>"
                                                       class="woo-sctr-progress-bar-order-status-hidden">
                                                <div class="vi-ui input"
                                                     data-tooltip="<?php esc_attr_e( 'When new order created, update the progress bar when order status are(leave blank to apply for all order status):', 'sales-countdown-timer' ) ?>">
                                                    <select multiple
                                                            class="woo-sctr-progress-bar-order-status vi-ui fluid dropdown">
                                                        <option value="wc-completed" <?php if ( in_array( 'wc-completed', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Completed', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-on-hold" <?php if ( in_array( 'wc-on-hold', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'On-hold', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-pending" <?php if ( in_array( 'wc-pending', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Pending', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-processing" <?php if ( in_array( 'wc-processing', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Processing', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-failed" <?php if ( in_array( 'wc-failed', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Failed', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-refunded" <?php if ( in_array( 'wc-refunded', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Refunded', 'sales-countdown-timer' ) ?></option>
                                                        <option value="wc-cancelled" <?php if ( in_array( 'wc-cancelled', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Cancelled', 'sales-countdown-timer' ) ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Position', 'sales-countdown-timer' ) ?></label>
                                                <select name="woo_ctr_progress_bar_position[]"
                                                        class="woo-sctr-progress-bar-position vi-ui dropdown">
                                                    <option value="above_countdown" <?php selected( $this->settings->get_progress_bar_position()[ $i ], 'above_countdown' ); ?>><?php esc_html_e( 'Above Countdown', 'sales-countdown-timer' ) ?></option>
                                                    <option value="below_countdown" <?php selected( $this->settings->get_progress_bar_position()[ $i ], 'below_countdown' ); ?>><?php esc_html_e( 'Below Countdown', 'sales-countdown-timer' ) ?></option>
                                                </select>
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Width(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number" min="0"
                                                       name="woo_ctr_progress_bar_width[]"
                                                       class="woo-sctr-progress-bar-width"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_width()[ $i ] ); ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Height(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number" min="0"
                                                       name="woo_ctr_progress_bar_height[]"
                                                       class="woo-sctr-progress-bar-height"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_height()[ $i ] ); ?>">
                                            </div>
                                        </div>
                                        <div class="three fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Background', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-progress-bar-color"
                                                       name="woo_ctr_progress_bar_bg_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_bg_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_progress_bar_bg_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Color', 'sales-countdown-timer' ) ?></label>
                                                <input type="text"
                                                       class="color-picker woo-sctr-progress-bar-color"
                                                       name="woo_ctr_progress_bar_color[]"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_color()[ $i ] ) ?>"
                                                       style="background:<?php echo esc_attr( $this->settings->get_progress_bar_color()[ $i ] ) ?>">
                                            </div>
                                            <div class="field">
                                                <label><?php esc_html_e( 'Border radius(px)', 'sales-countdown-timer' ) ?></label>
                                                <input type="number" min="0"
                                                       name="woo_ctr_progress_bar_border_radius[]"
                                                       class="woo-sctr-progress-bar-border-radius"
                                                       value="<?php echo esc_attr( $this->settings->get_progress_bar_border_radius()[ $i ] ); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

						<?php
					}
					?>
					<?php
				}
				?>
                <!--                <p><input type="submit" name="submit" class="vi-ui primary button"-->
                <!--                          value="-->
				<?php //esc_html_e( 'Save', 'sales-countdown-timer' ); ?><!--"></p>-->

                <p class="woo-sctr-button-save-container">
                    <span class="woo-sctr-save vi-ui primary button"><?php esc_html_e( 'Save', 'sales-countdown-timer' ); ?></span>
                </p>
            </form>
        </div>
        <div class="woo-sctr-save-sucessful-popup">
			<?php esc_html_e( 'Settings saved', 'sales-countdown-timer' ); ?>
        </div>
		<?php
		do_action( 'support_product-deal-countdown' );
	
    }



    /**
	 * Init Script in Admin
	 */
	public function admin_enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		if ( $page === 'sales-countdown-timer-checkout' ) {
			// global $wp_scripts;
			// $scripts = $wp_scripts->registered;
			// //			print_r($scripts);
			// foreach ( $scripts as $k => $script ) {
			// 	preg_match( '/^\/wp-/i', $script->src, $result );
			// 	if ( count( array_filter( $result ) ) ) {
			// 		preg_match( '/^(\/wp-content\/plugins|\/wp-content\/themes)/i', $script->src, $result1 );
			// 		if ( count( array_filter( $result1 ) ) ) {
			// 			wp_dequeue_script( $script->handle );
			// 		}
			// 	} else {
			// 		if ( $script->handle != 'query-monitor' ) {
			// 			wp_dequeue_script( $script->handle );
			// 		}
			// 	}
			// }
			// wp_enqueue_style( 'sales-countdown-timer-semantic-button', SALES_COUNTDOWN_TIMER_CSS . 'button.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-form', SALES_COUNTDOWN_TIMER_CSS . 'form.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-menu', SALES_COUNTDOWN_TIMER_CSS . 'menu.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-message', SALES_COUNTDOWN_TIMER_CSS . 'message.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-label', SALES_COUNTDOWN_TIMER_CSS . 'label.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-input', SALES_COUNTDOWN_TIMER_CSS . 'input.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-transition', SALES_COUNTDOWN_TIMER_CSS . 'transition.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-segment', SALES_COUNTDOWN_TIMER_CSS . 'segment.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-popup', SALES_COUNTDOWN_TIMER_CSS . 'popup.min.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-tab', SALES_COUNTDOWN_TIMER_CSS . 'tab.css' );
			// wp_enqueue_style( 'sales-countdown-timer-semantic-header', SALES_COUNTDOWN_TIMER_CSS . 'header.min.css' );

			// wp_enqueue_style( 'sales-countdown-timer-admin-css', SALES_COUNTDOWN_TIMER_CSS . 'admin-checkout.css' );

			// wp_enqueue_script( 'jquery-ui-sortable' );
			// wp_enqueue_script( 'sales-countdown-timer-semantic-address', SALES_COUNTDOWN_TIMER_JS . 'address.min.js', array( 'jquery' ) );
			// wp_enqueue_script( 'sales-countdown-timer-semantic-form', SALES_COUNTDOWN_TIMER_JS . 'form.js', array( 'jquery' ) );
			// wp_enqueue_script( 'sales-countdown-timer-semantic-tab', SALES_COUNTDOWN_TIMER_JS . 'tab.js', array( 'jquery' ) );
			// wp_enqueue_script( 'sales-countdown-timer-semantic-transition', SALES_COUNTDOWN_TIMER_JS . 'transition.min.js', array( 'jquery' ) );
			// wp_enqueue_script( 'sales-countdown-timer-admin-js', SALES_COUNTDOWN_TIMER_JS . 'admin-checkout.js', array( 'jquery' ), SALES_COUNTDOWN_TIMER_VERSION );
		} elseif ( $page == 'sales-countdown-timer' ) {
			global $wp_scripts;
			if ( isset( $wp_scripts->registered['jquery-ui-accordion'] ) ) {
				unset( $wp_scripts->registered['jquery-ui-accordion'] );
				wp_dequeue_script( 'jquery-ui-accordion' );
			}
			if ( isset( $wp_scripts->registered['accordion'] ) ) {
				unset( $wp_scripts->registered['accordion'] );
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
			wp_enqueue_style( 'sales-countdown-timer-semantic-button', SALES_COUNTDOWN_TIMER_CSS . 'button.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-checkbox', SALES_COUNTDOWN_TIMER_CSS . 'checkbox.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-dropdown', SALES_COUNTDOWN_TIMER_CSS . 'dropdown.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-form', SALES_COUNTDOWN_TIMER_CSS . 'form.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-icon', SALES_COUNTDOWN_TIMER_CSS . 'icon.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-menu', SALES_COUNTDOWN_TIMER_CSS . 'menu.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-segment', SALES_COUNTDOWN_TIMER_CSS . 'segment.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-label', SALES_COUNTDOWN_TIMER_CSS . 'label.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-transition', SALES_COUNTDOWN_TIMER_CSS . 'transition.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-accordion', SALES_COUNTDOWN_TIMER_CSS . 'accordion.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-input', SALES_COUNTDOWN_TIMER_CSS . 'input.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-header', SALES_COUNTDOWN_TIMER_CSS . 'header.min.css' );
			wp_enqueue_style( 'sales-countdown-timer-semantic-popup', SALES_COUNTDOWN_TIMER_CSS . 'popup.min.css' );

			wp_enqueue_style( 'sales-countdown-timer-admin', SALES_COUNTDOWN_TIMER_CSS . 'sales-countdown-timer-admin.css', array(), SALES_COUNTDOWN_TIMER_VERSION );

			wp_enqueue_script( 'sales-countdown-timer-semantic-checkbox', SALES_COUNTDOWN_TIMER_JS . 'checkbox.js', array( 'jquery' ) );
			wp_enqueue_script( 'sales-countdown-timer-semantic-dropdown', SALES_COUNTDOWN_TIMER_JS . 'dropdown.js', array( 'jquery' ) );
			wp_enqueue_script( 'sales-countdown-timer-semantic-form', SALES_COUNTDOWN_TIMER_JS . 'form.js', array( 'jquery' ) );
			wp_enqueue_script( 'sales-countdown-timer-semantic-tab', SALES_COUNTDOWN_TIMER_JS . 'tab.js', array( 'jquery' ) );
			wp_enqueue_script( 'sales-countdown-timer-semantic-transition', SALES_COUNTDOWN_TIMER_JS . 'transition.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'sales-countdown-timer-semantic-accordion', SALES_COUNTDOWN_TIMER_JS . 'accordion.min.js', array( 'jquery' ) );

			wp_enqueue_script( 'sales-countdown-timer-admin', SALES_COUNTDOWN_TIMER_JS . 'sales-countdown-timer-admin.js', array( 'jquery' ), SALES_COUNTDOWN_TIMER_VERSION );
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

				for ( $i = 0; $i < count( $id ); $i ++ ) {
					if ( $this->settings->get_datetime_value_bg_color()[ $i ] ) {
						$css .= '.woo-sctr-accordion-wrap[data-accordion_id="' . $i . '"] .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle:after{' . esc_attr__( 'background:' ) . $this->settings->get_datetime_value_bg_color()[ $i ] . ';}';
					}
					if ( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) {
						$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle .woo-sctr-value-bar{' . esc_attr__( 'border-color: ' ) . $this->settings->get_countdown_timer_item_border_color()[ $i ] . ';}';
						$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle .woo-sctr-first50-bar{' . esc_attr__( 'background-color: ' ) . $this->settings->get_countdown_timer_item_border_color()[ $i ] . ';}';
					}
					if ( $this->settings->get_datetime_value_font_size()[ $i ] ) {
						$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-style-4 .woo-sctr-shortcode-countdown-1 .woo-sctr-progress-circle{' . esc_attr__( 'font-size:' ) . $this->settings->get_datetime_value_font_size()[ $i ] . 'px;}';
					}

					$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1{';
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
					$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1 .woo-sctr-shortcode-countdown-value{';
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
					$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-wrap-wrap .woo-sctr-shortcode-countdown-1 .woo-sctr-shortcode-countdown-text{';
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
						$css .= '.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-wrap.woo-sctr-shortcode-countdown-style-1 .woo-sctr-shortcode-countdown-unit,.woo-sctr-accordion-wrap-' . $i . ' .woo-sctr-shortcode-countdown-wrap.woo-sctr-shortcode-countdown-style-2 .woo-sctr-shortcode-countdown-value{' . $css1 . '}';
					}

				}


				wp_add_inline_style( 'sales-countdown-timer-admin', $css );
			}
		}
	}

}



new Product_Deal_Countdown_Admin();
