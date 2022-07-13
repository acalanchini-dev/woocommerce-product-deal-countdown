 <?php 
        $id = $this->settings->get_id();
		// $div_class = is_rtl() ? 'woo-sctr-wrap woo-sctr-wrap-rtl' : 'woo-sctr-wrap';
        $div_class = 'woo-pdc-wrap'; ?>



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
						echo esc_html__( 'See your very first sales countdown timer ', 'product-deal-countdown' ) . '<a href="' . $product_url . '" target="_blank">' . esc_html__( 'here.', 'product-deal-countdown' ) . '</a>';
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
                     <input type="hidden" name="woo_ctr_active[]" class="woo-sctr-active"
                         value="<?php echo esc_attr( $this->settings->get_active()[ $i ] ); ?>">
                     <input type="checkbox" class="woo-sctr-active"
                         <?php echo $this->settings->get_active()[ $i ] ? 'checked' : ''; ?>><label>
                 </div>
                 <span
                     class="woo-sctr-accordion-name"><?php echo esc_html( $this->settings->get_names()[ $i ] ); ?></span>

                 <span class="woo-sctr-short-description">
                     <span
                         class="woo-sctr-short-description-from"><?php echo esc_html__( 'From: ', 'product-deal-countdown' ) ?>
                         <span
                             class="woo-sctr-short-description-from-date"><?php echo esc_html( $this->settings->get_sale_from_date()[ $i ] ) ?></span>&nbsp;
                         <span
                             class="woo-sctr-short-description-from-time"><?php echo esc_html( $this->settings->get_sale_from_time()[ $i ] ); ?></span>
                     </span>
                     <span
                         class="woo-sctr-short-description-to"><?php echo esc_html__( 'To: ', 'product-deal-countdown' ) ?>
                         <span
                             class="woo-sctr-short-description-to-date"><?php echo esc_html( $this->settings->get_sale_to_date()[ $i ] ) ?></span>&nbsp;
                         <span
                             class="woo-sctr-short-description-to-time"><?php echo esc_html( $this->settings->get_sale_to_time()[ $i ] ); ?></span>
                     </span>
                 </span>
                 <div class="woo-sctr-shortcode-text">
                     <span><?php echo esc_html__( 'Shortcode: ', 'product-deal-countdown' ) ?></span><span><?php echo '[sales_countdown_timer id="' . $id[ $i ] . '"]'; ?></span>
                 </div>
                 <span class="woo-sctr-button-edit">
                     <span
                         class="woo-sctr-short-description-copy-shortcode vi-ui button"><?php esc_html_e( 'Copy shortcode', 'product-deal-countdown' ); ?></span>
                     <span
                         class="woo-sctr-button-edit-duplicate vi-ui positive button"><?php esc_html_e( 'Duplicate', 'product-deal-countdown' ) ?></span>
                     <span
                         class="woo-sctr-button-edit-remove vi-ui negative button"><?php esc_html_e( 'Remove', 'product-deal-countdown' ) ?></span>
                 </span>
             </div>
             <div class="woo-sctr-panel vi-ui styled fluid accordion" id="woo-sctr-panel-accordion">
                 <div class="title  <?php if ( $this->settings->get_active()[ $i ] ) {
									echo 'active';
								} ?>">
                     <i class="dropdown icon"></i>
                     <?php esc_html_e( 'General settings', 'product-deal-countdown' ) ?>
                 </div>
                 <div class="content  <?php if ( $this->settings->get_active()[ $i ] ) {
									echo 'active';
								} ?>">

                     <div class="field">
                         <label><?php esc_html_e( 'Name', 'product-deal-countdown' ) ?></label>
                         <input type="hidden" name="woo_ctr_id[]" class="woo-sctr-id"
                             value="<?php echo esc_attr( $id[ $i ] ); ?>">
                         <input type="text" name="woo_ctr_name[]" class="woo-sctr-name"
                             value="<?php echo esc_attr( $this->settings->get_names()[ $i ] ); ?>">
                     </div>

                     <h4 class="vi-ui dividing header">
                         <label><?php esc_html_e( 'Schedule time for shortcode usage', 'product-deal-countdown' ) ?></label>
                     </h4>
                     <div class="field"
                         data-tooltip="<?php esc_html_e( 'These values are used for shortcode only. To schedule sale for product please go to admin product.', 'product-deal-countdown' ) ?>">
                         <div class="two fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'From', 'product-deal-countdown' ) ?></label>
                                 <div class="two fields">
                                     <div class="field">
                                         <input type="date" name="woo_ctr_sale_from_date[]" class="woo-sctr-sale-from-date woo-sctr-sale-date <?php if ( $this->settings->get_time_type()[ $i ] == 'loop' ) {
															       echo 'woo-sctr-hide-date';
														       } ?>" value="<?php echo esc_url( $this->settings->get_sale_from_date()[ $i ] ) ?>">
                                     </div>
                                     <div class="field">
                                         <input type="time" name="woo_ctr_sale_from_time[]"
                                             class="woo-sctr-sale-from-time"
                                             value="<?php echo $this->settings->get_sale_from_time()[ $i ] ? esc_attr( $this->settings->get_sale_from_time()[ $i ] ) : '00:00' ?>">
                                     </div>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'To', 'product-deal-countdown' ) ?></label>
                                 <div class="two fields">
                                     <div class="field">
                                         <input type="date" name="woo_ctr_sale_to_date[]" class="woo-sctr-sale-to-date woo-sctr-sale-date <?php if ( $this->settings->get_time_type()[ $i ] == 'loop' ) {
															       echo 'woo-sctr-hide-date';
														       } ?>" value="<?php echo esc_attr( $this->settings->get_sale_to_date()[ $i ] ) ?>">
                                     </div>
                                     <div class="field">
                                         <input type="time" name="woo_ctr_sale_to_time[]" class="woo-sctr-sale-to-time"
                                             value="<?php echo $this->settings->get_sale_to_time()[ $i ] ? esc_attr( $this->settings->get_sale_to_time()[ $i ] ) : '00:00' ?>">
                                     </div>
                                 </div>
                             </div>
                         </div>

                     </div>
                 </div>
                 <div class="title">
                     <i class="dropdown icon"></i>
                     <?php esc_html_e( 'Design', 'product-deal-countdown' ) ?>
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
                         <label><?php esc_html_e( 'Message', 'product-deal-countdown' ) ?></label>

                         <input type="text" name="woo_ctr_message[]" class="woo-sctr-message"
                             value="<?php echo esc_attr( $this->settings->get_message()[ $i ] ); ?>">
                     </div>
                     <div class="field">
                         <p>{countdown_timer}
                             -
                             <?php esc_html_e( 'The countdown timer that you set on tab design', 'product-deal-countdown' ) ?>
                         </p>
                         <p class="woo-sctr-warning-message-countdown-timer <?php if ( count( $text ) >= 2 ) {
											esc_attr_e( 'woo-sctr-hidden-class' );
										} ?>"><?php esc_html_e( 'The countdown timer will not show if message does not include {countdown_timer}', 'product-deal-countdown' ) ?>
                         </p>
                     </div>
                     <div class="equal width fields">
                         <div class="field">
                             <label><?php esc_html_e( 'Time separator', 'product-deal-countdown' ) ?></label>
                             <select name="woo_ctr_time_separator[]" class="woo-sctr-time-separator vi-ui dropdown">
                                 <option value="blank"
                                     <?php selected( $this->settings->get_time_separator() [ $i ], 'blank' ); ?>>
                                     <?php esc_html_e( 'Blank', 'product-deal-countdown' ) ?></option>
                                 <option value="colon"
                                     <?php selected( $this->settings->get_time_separator() [ $i ], 'colon' ); ?>>
                                     <?php esc_html_e( 'Colon(:)', 'product-deal-countdown' ) ?></option>
                                 <option value="comma"
                                     <?php selected( $this->settings->get_time_separator()[ $i ], 'comma' ); ?>>
                                     <?php esc_html_e( 'Comma(,)', 'product-deal-countdown' ) ?></option>
                                 <option value="dot"
                                     <?php selected( $this->settings->get_time_separator()[ $i ], 'dot' ); ?>>
                                     <?php esc_html_e( 'Dot(.)', 'product-deal-countdown' ) ?></option>
                             </select>
                         </div>
                         <div class="field">
                             <label><?php esc_html_e( 'Datetime format style', 'product-deal-countdown' ) ?></label>
                             <select name="woo_ctr_count_style[]" class="woo-sctr-count-style vi-ui dropdown">
                                 <option value="1" <?php selected( $this->settings->get_count_style()[ $i ], 1 ); ?>>
                                     <?php esc_html_e( '01 days 02 hrs 03 mins 04 secs', 'product-deal-countdown' ) ?>
                                 </option>
                                 <option value="2" <?php selected( $this->settings->get_count_style()[ $i ], 2 ); ?>>
                                     <?php esc_html_e( '01 days 02 hours 03 minutes 04 seconds', 'product-deal-countdown' ) ?>
                                 </option>
                                 <option value="3" <?php selected( $this->settings->get_count_style()[ $i ], 3 ); ?>>
                                     <?php esc_html_e( '01:02:03:04', 'product-deal-countdown' ) ?></option>
                                 <option value="4" <?php selected( $this->settings->get_count_style()[ $i ], 4 ); ?>>
                                     <?php esc_html_e( '01d:02h:03m:04s', 'product-deal-countdown' ) ?></option>
                             </select>
                         </div>
                     </div>
                     <?php
									$datetime_unit_position = isset( $this->settings->get_datetime_unit_position() [ $i ] ) ? $this->settings->get_datetime_unit_position() [ $i ] : 'bottom';
									$animation_style        = isset( $this->settings->get_animation_style()[ $i ] ) ? $this->settings->get_animation_style()[ $i ] : 'default';
									?>
                     <div class="equal width fields">
                         <div class="field">
                             <label><?php esc_html_e( 'Datetime unit position', 'product-deal-countdown' ) ?></label>
                             <select name="woo_ctr_datetime_unit_position[]"
                                 class="woo-sctr-datetime-unit-position vi-ui dropdown">
                                 <option value="top" <?php selected( $datetime_unit_position, 'top' ); ?>>
                                     <?php esc_html_e( 'Top', 'product-deal-countdown' ) ?></option>
                                 <option value="bottom" <?php selected( $datetime_unit_position, 'bottom' ); ?>>
                                     <?php esc_html_e( 'Bottom', 'product-deal-countdown' ) ?></option>
                             </select>
                         </div>
                         <div class="field">
                             <label><?php esc_html_e( 'Animation style', 'product-deal-countdown' ) ?></label>
                             <select name="woo_ctr_animation_style[]" class="woo-sctr-animation-style vi-ui dropdown">
                                 <option value="default" <?php selected( $animation_style, 'default' ); ?>>
                                     <?php esc_html_e( 'Default', 'product-deal-countdown' ) ?></option>
                                 <option value="slide" <?php selected( $animation_style, 'slide' ); ?>>
                                     <?php esc_html_e( 'Slide', 'product-deal-countdown' ) ?></option>
                             </select>
                         </div>
                     </div>
                     <?php
									switch ( $this->settings->get_count_style()[ $i ] ) {
										case '1':
											$date   = esc_html__( 'days', 'product-deal-countdown' );
											$hour   = esc_html__( 'hrs', 'product-deal-countdown' );
											$minute = esc_html__( 'mins', 'product-deal-countdown' );
											$second = esc_html__( 'secs', 'product-deal-countdown' );
											break;
										case '2':
											$date   = esc_html__( 'days', 'product-deal-countdown' );
											$hour   = esc_html__( 'hours', 'product-deal-countdown' );
											$minute = esc_html__( 'minutes', 'product-deal-countdown' );
											$second = esc_html__( 'seconds', 'product-deal-countdown' );
											break;
										case '3':
											$date   = esc_html__( '', 'product-deal-countdown' );
											$hour   = esc_html__( '', 'product-deal-countdown' );
											$minute = esc_html__( '', 'product-deal-countdown' );
											$second = esc_html__( '', 'product-deal-countdown' );
											break;
										default:
											$date   = esc_html__( 'd', 'product-deal-countdown' );
											$hour   = esc_html__( 'h', 'product-deal-countdown' );
											$minute = esc_html__( 'm', 'product-deal-countdown' );
											$second = esc_html__( 's', 'product-deal-countdown' );
									}

									?>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <label><?php esc_html_e( 'Display type', 'product-deal-countdown' ) ?></label>
                         </h4>
                         <input type="hidden" name="woo_ctr_display_type[]" class="woo-sctr-display-type"
                             value="<?php echo esc_attr( $this->settings->get_display_type()[ $i ] ); ?>">

                         <div class="two fields">

                             <div class="field">
                                 <div class="vi-ui segment">
                                     <div class="fields">
                                         <div class="three wide field">
                                             <div class="vi-ui toggle checkbox">

                                                 <input type="radio"
                                                     name="woo_ctr_display_type_<?php echo esc_attr( $i ); ?>"
                                                     class="woo-sctr-display-type-checkbox" value="1"
                                                     <?php checked( $this->settings->get_display_type()[ $i ], '1' ) ?>><label></label>
                                             </div>
                                         </div>
                                         <div class="thirteen wide field">
                                             <div class="woo-sctr-shortcode-wrap-wrap">
                                                 <div class="woo-sctr-shortcode-wrap">

                                                     <div
                                                         class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-1">
                                                         <div class="woo-sctr-shortcode-countdown">
                                                             <div class="woo-sctr-shortcode-countdown-1">
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-text-before"><?php echo esc_html( $text_before ); ?></span>
                                                                 <div class="woo-sctr-shortcode-countdown-2">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_html( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                 </div>
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>

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
                                                     class="woo-sctr-display-type-checkbox" value="2"
                                                     <?php checked( $this->settings->get_display_type()[ $i ], '2' ) ?>><label></label>
                                             </div>
                                         </div>
                                         <div class="thirteen wide field">
                                             <div class="woo-sctr-shortcode-wrap-wrap">
                                                 <div class="woo-sctr-shortcode-wrap">

                                                     <div
                                                         class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-2">
                                                         <div class="woo-sctr-shortcode-countdown">
                                                             <div class="woo-sctr-shortcode-countdown-1">
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                                                 <div class="woo-sctr-shortcode-countdown-2">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                                 <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'product-deal-countdown' ); ?></span>
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                                 <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                         </span>
                                                                     </span>
                                                                 </div>
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
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
                                                 class="woo-sctr-display-type-checkbox" value="3"
                                                 <?php checked( $this->settings->get_display_type()[ $i ], '3' ) ?>><label></label>
                                         </div>
                                     </div>
                                     <div class="ten wide field">
                                         <div class="woo-sctr-shortcode-wrap-wrap woo-sctr-shortcode-wrap-wrap-inline">

                                             <span
                                                 class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                             <span class="woo-sctr-shortcode-countdown-1">
                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                     <span
                                                         class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '01', 'product-deal-countdown' ); ?></span>
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $date ); ?></span>
                                                     </span>
                                                 </span>
                                                 <span
                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                     <span
                                                         class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'product-deal-countdown' ); ?></span>
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $hour ); ?></span>
                                                     </span>
                                                 </span>
                                                 <span
                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                     <span
                                                         class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '03', 'product-deal-countdown' ); ?></span>
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $minute ); ?></span>
                                                     </span>
                                                 </span>
                                                 <span
                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                     <span
                                                         class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '04', 'product-deal-countdown' ); ?></span>
                                                         <span
                                                             class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text"><?php echo esc_attr( $second ); ?></span>
                                                     </span>
                                                 </span>
                                             </span>
                                             <span
                                                 class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
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
                                                 class="woo-sctr-display-type-checkbox" value="4"
                                                 <?php checked( $this->settings->get_display_type()[ $i ], '4' ) ?>><label></label>
                                         </div>
                                     </div>
                                     <div class="ten wide field">
                                         <div class="woo-sctr-shortcode-wrap-wrap">
                                             <div class="woo-sctr-shortcode-wrap">

                                                 <div
                                                     class="woo-sctr-shortcode-countdown-wrap woo-sctr-shortcode-countdown-style-4">
                                                     <div class="woo-sctr-shortcode-countdown">
                                                         <div class="woo-sctr-shortcode-countdown-1">
                                                             <span
                                                                 class="woo-sctr-shortcode-countdown-text-before"><?php echo wp_kses_post( $text_before ); ?></span>
                                                             <div class="woo-sctr-shortcode-countdown-2">
                                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-date woo-sctr-shortcode-countdown-unit">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                             <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                         <div class="woo-sctr-progress-circle">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-date-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '10', 'product-deal-countdown' ); ?></span>
                                                                             <div class="woo-sctr-left-half-clipper">
                                                                                 <div class="woo-sctr-first50-bar">
                                                                                 </div>
                                                                                 <div class="woo-sctr-value-bar"></div>
                                                                             </div>
                                                                         </div>
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-date-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                             <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $date ); ?></span>
                                                                     </span>
                                                                 </span>
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-hour woo-sctr-shortcode-countdown-unit">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                             <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                         <div class="woo-sctr-progress-circle">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-hour-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '02', 'product-deal-countdown' ); ?></span>
                                                                             <div class="woo-sctr-left-half-clipper">
                                                                                 <div class="woo-sctr-first50-bar">
                                                                                 </div>
                                                                                 <div class="woo-sctr-value-bar"></div>
                                                                             </div>
                                                                         </div>
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-hour-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                             <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $hour ); ?></span>
                                                                     </span>
                                                                 </span>
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-minute woo-sctr-shortcode-countdown-unit">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                             <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                         <div class="woo-sctr-progress-circle">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-minute-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '30', 'product-deal-countdown' ); ?></span>
                                                                             <div class="woo-sctr-left-half-clipper">
                                                                                 <div class="woo-sctr-first50-bar">
                                                                                 </div>
                                                                                 <div class="woo-sctr-value-bar"></div>
                                                                             </div>
                                                                         </div>
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-minute-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                             <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $minute ); ?></span>
                                                                     </span>
                                                                 </span>
                                                                 <span
                                                                     class="woo-sctr-shortcode-countdown-time-separator"><?php echo esc_attr( $time_separator ); ?></span>
                                                                 <span class="woo-sctr-shortcode-countdown-unit-wrap">
                                                                     <span
                                                                         class="woo-sctr-shortcode-countdown-second woo-sctr-shortcode-countdown-unit">
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-top"
                                                                             <?php echo $datetime_unit_position == 'top' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                         <div
                                                                             class="woo-sctr-progress-circle woo-sctr-over50">
                                                                             <span
                                                                                 class="woo-sctr-shortcode-countdown-second-value woo-sctr-shortcode-countdown-value"><?php esc_html_e( '40', 'product-deal-countdown' ); ?></span>
                                                                             <div class="woo-sctr-left-half-clipper">
                                                                                 <div class="woo-sctr-first50-bar">
                                                                                 </div>
                                                                                 <div class="woo-sctr-value-bar"></div>
                                                                             </div>
                                                                         </div>
                                                                         <span
                                                                             class="woo-sctr-shortcode-countdown-second-text woo-sctr-shortcode-countdown-text woo-sctr-datetime-unit-position-bottom"
                                                                             <?php echo $datetime_unit_position == 'bottom' ? '' : 'style="display:none;"'; ?>><?php echo esc_html( $second ); ?></span>
                                                                     </span>
                                                                 </span>
                                                             </div>
                                                             <span
                                                                 class="woo-sctr-shortcode-countdown-text-after"><?php echo wp_kses_post( $text_after ); ?></span>
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
                                     <input type="checkbox" class="woo-sctr-circle-smooth-animation-check" value="1"
                                         <?php checked( $smooth_animation, '1' ) ?>><label><?php esc_html_e( 'Use smooth animation for circle', 'product-deal-countdown' ) ?></label>
                                 </div>
                                 <p><?php esc_html_e( '(*)Countdown timer items Border radius, Height and Width are not applied to this type.', 'product-deal-countdown' ) ?>
                                 </p>
                             </div>
                         </div>
                     </div>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <label><?php esc_html_e( 'Countdown timer', 'product-deal-countdown' ) ?></label>
                         </h4>
                         <div class="three fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-countdown-timer-color"
                                     name="woo_ctr_countdown_timer_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Background', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-countdown-timer-bg-color"
                                     name="woo_ctr_countdown_timer_bg_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_bg_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_bg_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Border color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-countdown-timer-border-color"
                                     name="woo_ctr_countdown_timer_border_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_border_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_border_color()[ $i ] ) ?>">
                             </div>

                             <div class="field">
                                 <label><?php esc_html_e( 'Padding(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-countdown-timer-padding"
                                     name="woo_ctr_countdown_timer_padding[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_padding()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Border radius', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-countdown-timer-border-radius"
                                     name="woo_ctr_countdown_timer_border_radius[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_border_radius()[ $i ] ) ?>">
                             </div>

                         </div>
                     </div>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <label><?php esc_html_e( 'Countdown timer items', 'product-deal-countdown' ) ?></label>
                         </h4>
                         <div class="three fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Border color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-countdown-timer-item-border-color"
                                     name="woo_ctr_countdown_timer_item_border_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Border radius(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-countdown-timer-item-border-radius"
                                     name="woo_ctr_countdown_timer_item_border_radius[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_border_radius()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Height(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-countdown-timer-item-height"
                                     name="woo_ctr_countdown_timer_item_height[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_height()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Width(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-countdown-timer-item-width"
                                     name="woo_ctr_countdown_timer_item_width[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_countdown_timer_item_width()[ $i ] ) ?>">
                             </div>


                         </div>
                     </div>

                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <label><?php esc_html_e( 'Datetime value', 'product-deal-countdown' ) ?></label>
                         </h4>
                         <div class="equal width fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-datetime-value-color"
                                     name="woo_ctr_datetime_value_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_value_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_datetime_value_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Background', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-datetime-value-bg-color"
                                     name="woo_ctr_datetime_value_bg_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_value_bg_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_datetime_value_bg_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Font size(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-datetime-value-font-size"
                                     name="woo_ctr_datetime_value_font_size[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_value_font_size()[ $i ] ) ?>">
                             </div>
                         </div>
                     </div>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <label><?php esc_html_e( 'Datetime unit', 'product-deal-countdown' ) ?></label>
                         </h4>
                         <div class=" equal width fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-datetime-unit-color"
                                     name="woo_ctr_datetime_unit_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_unit_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_datetime_unit_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Background', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-datetime-unit-bg-color"
                                     name="woo_ctr_datetime_unit_bg_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_unit_bg_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_datetime_unit_bg_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Font size(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" class="woo-sctr-datetime-unit-font-size"
                                     name="woo_ctr_datetime_unit_font_size[]" min="0"
                                     value="<?php echo esc_attr( $this->settings->get_datetime_unit_font_size()[ $i ] ) ?>">
                             </div>
                         </div>
                     </div>

                 </div>
                 <div class="title">
                     <i class="dropdown icon"></i>
                     <?php esc_html_e( 'WooCommerce Product', 'product-deal-countdown' ) ?>
                 </div>
                 <div class="content">
                     <div class="field">

                         <div class="equal width fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Make countdown timer sticky when scroll', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui toggle checkbox">
                                     <input type="hidden" name="woo_ctr_stick_to_top[]" class="woo-sctr-stick-to-top"
                                         value="<?php echo isset( $this->settings->get_stick_to_top()[ $i ] ) ? esc_attr( $this->settings->get_stick_to_top()[ $i ] ) : ''; ?>">
                                     <input type="checkbox" class="woo-sctr-stick-to-top-check"
                                         <?php echo ( isset( $this->settings->get_stick_to_top()[ $i ] ) && $this->settings->get_stick_to_top()[ $i ] ) ? 'checked' : ''; ?>><label></label>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Position on single product page', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui input"
                                     data-tooltip="<?php esc_attr_e( 'Position of countdown timer of main product on single product page(Can not set position for variations)', 'product-deal-countdown' ) ?>">
                                     <select name="woo_ctr_position[]" class="woo-sctr-position vi-ui fluid dropdown">
                                         <option value="before_price"
                                             <?php selected( $this->settings->get_position()[ $i ], 'before_price' ); ?>>
                                             <?php esc_html_e( 'Before price', 'product-deal-countdown' ) ?></option>
                                         <option value="after_price"
                                             <?php selected( $this->settings->get_position()[ $i ], 'after_price' ); ?>>
                                             <?php esc_html_e( 'After price', 'product-deal-countdown' ) ?></option>
                                         <option value="before_saleflash"
                                             <?php selected( $this->settings->get_position()[ $i ], 'before_saleflash' ); ?>>
                                             <?php esc_html_e( 'Before sale flash', 'product-deal-countdown' ) ?>
                                         </option>
                                         <option value="after_saleflash"
                                             <?php selected( $this->settings->get_position()[ $i ], 'after_saleflash' ); ?>>
                                             <?php esc_html_e( 'After sale flash', 'product-deal-countdown' ) ?>
                                         </option>
                                         <option value="before_cart"
                                             <?php selected( $this->settings->get_position()[ $i ], 'before_cart' ); ?>>
                                             <?php esc_html_e( 'Before cart', 'product-deal-countdown' ) ?></option>
                                         <option value="after_cart"
                                             <?php selected( $this->settings->get_position()[ $i ], 'after_cart' ); ?>>
                                             <?php esc_html_e( 'After cart', 'product-deal-countdown' ) ?></option>
                                         <option value="product_image"
                                             <?php selected( $this->settings->get_position()[ $i ], 'product_image' ); ?>>
                                             <?php esc_html_e( 'Product image', 'product-deal-countdown' ) ?></option>
                                     </select>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Position on archive page', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui input"
                                     data-tooltip="<?php esc_attr_e( 'Position of countdown timer on shop page, category page and related products', 'product-deal-countdown' ) ?>">
                                     <select name="woo_ctr_archive_page_position[]"
                                         class="woo-sctr-archive-page-position vi-ui fluid dropdown">
                                         <option value="before_price"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_price' ); ?>>
                                             <?php esc_html_e( 'Before price', 'product-deal-countdown' ) ?></option>
                                         <option value="after_price"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_price' ); ?>>
                                             <?php esc_html_e( 'After price', 'product-deal-countdown' ) ?></option>
                                         <option value="before_saleflash"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_saleflash' ); ?>>
                                             <?php esc_html_e( 'Before sale flash', 'product-deal-countdown' ) ?>
                                         </option>
                                         <option value="after_saleflash"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_saleflash' ); ?>>
                                             <?php esc_html_e( 'After sale flash', 'product-deal-countdown' ) ?>
                                         </option>
                                         <option value="before_cart"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'before_cart' ); ?>>
                                             <?php esc_html_e( 'Before cart', 'product-deal-countdown' ) ?></option>
                                         <option value="after_cart"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'after_cart' ); ?>>
                                             <?php esc_html_e( 'After cart', 'product-deal-countdown' ) ?></option>
                                         <option value="product_image"
                                             <?php selected( $this->settings->get_archive_page_position()[ $i ], 'product_image' ); ?>>
                                             <?php esc_html_e( 'Product image', 'product-deal-countdown' ) ?></option>
                                     </select>
                                 </div>
                             </div>
                         </div>
                         <div class="equal width fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Show on shop page', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui toggle checkbox">
                                     <input type="hidden" name="woo_ctr_shop_page[]" class="woo-sctr-shop-page"
                                         value="<?php echo esc_attr( $this->settings->get_shop_page()[ $i ] ); ?>">
                                     <input type="checkbox" class="woo-sctr-shop-page-check"
                                         <?php echo $this->settings->get_shop_page()[ $i ] ? 'checked' : ''; ?>><label></label>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Show on category page', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui toggle checkbox">
                                     <input type="hidden" name="woo_ctr_category_page[]" class="woo-sctr-category-page"
                                         value="<?php echo esc_attr( $this->settings->get_category_page()[ $i ] ); ?>">
                                     <input type="checkbox" class="woo-sctr-category-page-check"
                                         <?php echo $this->settings->get_category_page()[ $i ] ? 'checked' : ''; ?>><label></label>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Reduce size of countdown timer on shop/category page and on mobile(single product) by', 'product-deal-countdown' ) ?></label>
                                 <div class="inline field">
                                     <input type="number" name="woo_ctr_size_on_archive_page[]" min="30" max="100"
                                         class="woo-sctr-related-products"
                                         value="<?php echo isset( $this->settings->get_size_on_archive_page()[ $i ] ) ? esc_attr( $this->settings->get_size_on_archive_page()[ $i ] ) : '75'; ?>">%
                                 </div>
                             </div>

                         </div>
                     </div>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <?php esc_html_e( 'Upcoming sale', 'product-deal-countdown' ) ?></h4>
                         <div class="fields">
                             <div class="three wide field">
                                 <label><?php esc_html_e( 'Enable', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui toggle checkbox">
                                     <input type="hidden" name="woo_ctr_upcoming[]" class="woo-sctr-upcoming"
                                         value="<?php echo esc_attr( $this->settings->get_upcoming()[ $i ] ); ?>">
                                     <input type="checkbox" class="woo-sctr-upcoming-check"
                                         <?php echo $this->settings->get_upcoming()[ $i ] ? 'checked' : ''; ?>><label></label>
                                 </div>
                             </div>
                             <div class="thirteen wide field">
                                 <label><?php esc_html_e( 'Upcoming sale message', 'product-deal-countdown' ) ?></label>

                                 <input type="text" name="woo_ctr_upcoming_message[]" class="woo-sctr-upcoming-message"
                                     value="<?php echo esc_attr( $this->settings->get_upcoming_message()[ $i ] ); ?>">
                                 <p>{countdown_timer}
                                     -
                                     <?php esc_html_e( 'The countdown timer that you set on tab design', 'product-deal-countdown' ) ?>
                                 </p>
                             </div>
                         </div>

                     </div>
                     <div class="field">
                         <h4 class="vi-ui dividing header">
                             <?php esc_html_e( 'Progress bar', 'product-deal-countdown' ) ?></h4>
                         <div class="field">
                             <label><?php esc_html_e( 'Progress bar message', 'product-deal-countdown' ) ?></label>
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
                                 <label><?php esc_html_e( 'Progress bar type', 'product-deal-countdown' ) ?></label>
                                 <div class="vi-ui input"
                                     data-tooltip="<?php esc_attr_e( 'If select increase, the progress bar fill will increase each time the product is bought and vice versa', 'product-deal-countdown' ) ?>">
                                     <select name="woo_ctr_progress_bar_type[]"
                                         class="woo-sctr-progress-bar-type vi-ui fluid dropdown">
                                         <option value="increase"
                                             <?php selected( $this->settings->get_progress_bar_type()[ $i ], 'increase' ); ?>
                                             data-tooltip="asdasd">
                                             <?php esc_html_e( 'Increase', 'product-deal-countdown' ) ?></option>
                                         <option value="decrease"
                                             <?php selected( $this->settings->get_progress_bar_type()[ $i ], 'decrease' ); ?>>
                                             <?php esc_html_e( 'Decrease', 'product-deal-countdown' ) ?></option>
                                     </select>
                                 </div>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Order status', 'product-deal-countdown' ) ?></label>
                                 <input type="hidden" name="woo_ctr_progress_bar_order_status[]"
                                     value="<?php $this->settings->get_progress_bar_order_status()[ $i ] ?>"
                                     class="woo-sctr-progress-bar-order-status-hidden">
                                 <div class="vi-ui input"
                                     data-tooltip="<?php esc_attr_e( 'When new order created, update the progress bar when order status are(leave blank to apply for all order status):', 'product-deal-countdown' ) ?>">
                                     <select multiple class="woo-sctr-progress-bar-order-status vi-ui fluid dropdown">
                                         <option value="wc-completed" <?php if ( in_array( 'wc-completed', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Completed', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-on-hold" <?php if ( in_array( 'wc-on-hold', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'On-hold', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-pending" <?php if ( in_array( 'wc-pending', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Pending', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-processing" <?php if ( in_array( 'wc-processing', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Processing', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-failed" <?php if ( in_array( 'wc-failed', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Failed', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-refunded" <?php if ( in_array( 'wc-refunded', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Refunded', 'product-deal-countdown' ) ?></option>
                                         <option value="wc-cancelled" <?php if ( in_array( 'wc-cancelled', explode( ',', $this->settings->get_progress_bar_order_status()[ $i ] ) ) ) {
															echo 'selected';
														} ?>><?php esc_html_e( 'Cancelled', 'product-deal-countdown' ) ?></option>
                                     </select>
                                 </div>
                             </div>
                         </div>
                         <div class="equal width fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Position', 'product-deal-countdown' ) ?></label>
                                 <select name="woo_ctr_progress_bar_position[]"
                                     class="woo-sctr-progress-bar-position vi-ui dropdown">
                                     <option value="above_countdown"
                                         <?php selected( $this->settings->get_progress_bar_position()[ $i ], 'above_countdown' ); ?>>
                                         <?php esc_html_e( 'Above Countdown', 'product-deal-countdown' ) ?></option>
                                     <option value="below_countdown"
                                         <?php selected( $this->settings->get_progress_bar_position()[ $i ], 'below_countdown' ); ?>>
                                         <?php esc_html_e( 'Below Countdown', 'product-deal-countdown' ) ?></option>
                                 </select>
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Width(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" min="0" name="woo_ctr_progress_bar_width[]"
                                     class="woo-sctr-progress-bar-width"
                                     value="<?php echo esc_attr( $this->settings->get_progress_bar_width()[ $i ] ); ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Height(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" min="0" name="woo_ctr_progress_bar_height[]"
                                     class="woo-sctr-progress-bar-height"
                                     value="<?php echo esc_attr( $this->settings->get_progress_bar_height()[ $i ] ); ?>">
                             </div>
                         </div>
                         <div class="three fields">
                             <div class="field">
                                 <label><?php esc_html_e( 'Background', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-progress-bar-color"
                                     name="woo_ctr_progress_bar_bg_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_progress_bar_bg_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_progress_bar_bg_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Color', 'product-deal-countdown' ) ?></label>
                                 <input type="text" class="color-picker woo-sctr-progress-bar-color"
                                     name="woo_ctr_progress_bar_color[]"
                                     value="<?php echo esc_attr( $this->settings->get_progress_bar_color()[ $i ] ) ?>"
                                     style="background:<?php echo esc_attr( $this->settings->get_progress_bar_color()[ $i ] ) ?>">
                             </div>
                             <div class="field">
                                 <label><?php esc_html_e( 'Border radius(px)', 'product-deal-countdown' ) ?></label>
                                 <input type="number" min="0" name="woo_ctr_progress_bar_border_radius[]"
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
         <?php //esc_html_e( 'Save', 'product-deal-countdown' ); ?>
         <!--"></p>-->

         <p class="woo-sctr-button-save-container">
             <span
                 class="woo-sctr-save vi-ui primary button"><?php esc_html_e( 'Save', 'product-deal-countdown' ); ?></span>
         </p>
     </form>
 </div>
 <div class="woo-sctr-save-sucessful-popup">
     <?php esc_html_e( 'Settings saved', 'product-deal-countdown' ); ?>
 </div>
 <?php do_action( 'support_product-deal-countdown' ); ?>