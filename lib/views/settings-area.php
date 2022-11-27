<?php
/**
 * The settings page view.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Register settings menu.
 */
function codes_settings_menu() {
	add_submenu_page(
		'edit.php?post_type=custom-code', // admin page slug.
		__( 'Settings', 'custom-codes' ), // page title.
		__( 'Settings', 'custom-codes' ), // menu title.
		'manage_options',                 // capability required to see the page.
		'settings',                       // admin page slug, e.g. options-general.php?page=codes_settings.
		'codes_settings_page'             // callback function to display the options page.
	);

}
add_action( 'admin_menu', 'codes_settings_menu' );

/**
 * Settings page content.
 */
function codes_settings_page() {
	global $wp_filesystem;

	$registered_settings = get_registered_settings();

	?>

	<div id="custom-codes-settings" class="wrap">

		<div class="codes-header">

			<div class="top">
				<div class="branding">
					<svg class="logo" width="107" height="23" viewBox="0 0 107 23" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M30.1396 17.2931C28.7132 15.8511 28 13.9637 28 11.631C28 9.29835 28.7132 7.41096 30.1396 5.96892C31.566 4.52688 33.3738 3.80585 35.563 3.80585C37.4695 3.80585 39.0866 4.33602 40.4142 5.39634C41.756 6.45667 42.568 7.89164 42.8505 9.70125H39.5457C39.3197 8.76818 38.8537 8.04716 38.1475 7.5382C37.4413 7.01511 36.5798 6.75356 35.563 6.75356C34.2071 6.75356 33.1479 7.19183 32.3852 8.06836C31.6367 8.94485 31.2624 10.1324 31.2624 11.631C31.2624 13.1296 31.6367 14.3172 32.3852 15.1937C33.1479 16.0702 34.2071 16.5085 35.563 16.5085C36.5798 16.5085 37.4413 16.254 38.1475 15.7451C38.8537 15.222 39.3197 14.4939 39.5457 13.5608H42.8505C42.568 15.3704 41.756 16.8054 40.4142 17.8657C39.0866 18.926 37.4695 19.4562 35.563 19.4562C33.3738 19.4562 31.566 18.7352 30.1396 17.2931Z" fill="white"/>
						<path d="M53.8945 17.7597C52.8212 18.8766 51.4159 19.435 49.6788 19.435C47.9415 19.435 46.5364 18.8766 45.463 17.7597C44.3896 16.6287 43.853 15.2503 43.853 13.6244C43.853 11.9986 44.3896 10.6272 45.463 9.51044C46.5364 8.37938 47.9415 7.81387 49.6788 7.81387C51.4159 7.81387 52.8212 8.37938 53.8945 9.51044C54.9678 10.6272 55.5045 11.9986 55.5045 13.6244C55.5045 15.2503 54.9678 16.6287 53.8945 17.7597ZM47.645 11.3129C47.1224 11.8784 46.8612 12.6489 46.8612 13.6244C46.8612 14.5999 47.1224 15.3704 47.645 15.9359C48.1816 16.5014 48.8596 16.7842 49.6788 16.7842C50.4979 16.7842 51.1688 16.5014 51.6913 15.9359C52.228 15.3704 52.4963 14.5999 52.4963 13.6244C52.4963 12.6489 52.228 11.8784 51.6913 11.3129C51.1688 10.7474 50.4979 10.4646 49.6788 10.4646C48.8596 10.4646 48.1816 10.7474 47.645 11.3129Z" fill="white"/>
						<path d="M64.8313 9.25596V3.2757H67.8395V19.1806H65.0643V17.8234C64.3298 18.8979 63.1576 19.4351 61.5476 19.4351C59.9799 19.4351 58.7372 18.8837 57.8191 17.781C56.9011 16.6783 56.4422 15.2928 56.4422 13.6245C56.4422 11.9563 56.9011 10.5708 57.8191 9.46796C58.7372 8.36526 59.9799 7.81389 61.5476 7.81389C62.2821 7.81389 62.9317 7.95527 63.4966 8.23802C64.0756 8.50664 64.5205 8.84594 64.8313 9.25596ZM64.1533 15.8936C64.6618 15.2998 64.916 14.5435 64.916 13.6245C64.916 12.7056 64.6618 11.9492 64.1533 11.3554C63.659 10.7616 63.0024 10.4647 62.1831 10.4647C61.364 10.4647 60.7002 10.7616 60.1918 11.3554C59.6975 11.9492 59.4503 12.7056 59.4503 13.6245C59.4503 14.5435 59.6975 15.2998 60.1918 15.8936C60.7002 16.4874 61.364 16.7843 62.1831 16.7843C63.0024 16.7843 63.659 16.4874 64.1533 15.8936Z" fill="white"/>
						<path d="M80.5029 14.4939H72.4527C72.5517 15.2573 72.8341 15.844 73.3001 16.254C73.7662 16.6499 74.3806 16.8478 75.1432 16.8478C75.6234 16.8478 76.0542 16.7488 76.4355 16.5509C76.8309 16.3389 77.1134 16.0349 77.2829 15.639H80.3123C79.9875 16.7701 79.3449 17.689 78.3845 18.3959C77.4382 19.0886 76.3296 19.435 75.0585 19.435C73.4202 19.435 72.0785 18.8907 71.0334 17.8021C69.9883 16.7135 69.4657 15.3139 69.4657 13.6032C69.4657 11.9491 69.9883 10.5707 71.0334 9.46794C72.0785 8.36524 73.4131 7.81387 75.0374 7.81387C76.6615 7.81387 77.982 8.36524 78.9989 9.46794C80.0299 10.5565 80.5453 11.935 80.5453 13.6032L80.5029 14.4939ZM75.0161 10.2314C74.3382 10.2314 73.7803 10.4222 73.3425 10.804C72.9048 11.1715 72.6223 11.6805 72.4951 12.3308H77.5371C77.4241 11.6946 77.1487 11.1857 76.7109 10.804C76.2731 10.4222 75.7081 10.2314 75.0161 10.2314Z" fill="white"/>
						<path d="M95.8394 19.1805H92.0261L87.7468 12.7125L85.4589 15.2149V19.1805H82.3659V4.12393H85.4589V11.2917L91.5812 4.12393H95.6063L89.8865 10.3798L95.8394 19.1805Z" fill="white"/>
						<path d="M99.2365 6.11735C98.8972 6.45666 98.4597 6.62631 97.9229 6.62631C97.3862 6.62631 96.9486 6.45666 96.6097 6.11735C96.2706 5.77805 96.1012 5.34685 96.1012 4.82376C96.1012 4.30066 96.2706 3.86946 96.6097 3.53016C96.9486 3.17672 97.3862 3 97.9229 3C98.4597 3 98.8972 3.17672 99.2365 3.53016C99.5758 3.86946 99.7445 4.30066 99.7445 4.82376C99.7445 5.34685 99.5758 5.77805 99.2365 6.11735ZM96.419 19.1805V8.06835H99.427V19.1805H96.419Z" fill="white"/>
						<path d="M106.979 16.5086V19.117C106.64 19.2018 106.167 19.2442 105.559 19.2442C103.285 19.2442 102.148 18.1132 102.148 15.8512V10.4435H100.644V8.06836H102.148V5.31152H105.157V8.06836H107V10.4435H105.157V15.5331C105.157 16.2541 105.531 16.6146 106.28 16.6146L106.979 16.5086Z" fill="white"/>
						<path d="M9.2 4.5231H4.59999V18.6578H9.2V23H0V0H9.2V4.5231Z" fill="#D13334"/>
						<path d="M16.5662 0.0595808L8.7619 11.5L16.9495 23H23L14.2048 11.4404L22.4524 0.0595933L16.5662 0.0595808Z" fill="#D13334"/>
					</svg>
					<a href="https://wordpress.org/plugins/custom-codes/#developers" target="_blank" class="version">v<?php echo esc_html( CODES_VERSION ); ?></a>
				</div>

				<div class="navigation">
					<a href="mailto:info@pressx.co?subject=<?php echo codes_fs()->is_premium() ? 'Premium ' : ''; ?>Support"><?php echo wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-support.svg' ), codes_svg_args() ); ?> <?php echo codes_fs()->is_premium() ? esc_html__( 'Premium Support', 'custom-codes' ) : esc_html__( 'Support', 'custom-codes' ); ?></a>
					<a href="mailto:info@pressx.co?subject=Feedback<?php echo codes_fs()->is_premium() ? '(Premium)' : ''; ?>"><?php echo wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-check.svg' ), codes_svg_args() ); ?> <?php esc_html_e( 'Feedback', 'custom-codes' ); ?></a>
				</div>
			</div>

		</div>

		<div class="settings-tabs">
			<a href="#editor-settings" class="active"><?php esc_html_e( 'Editor', 'custom-codes' ); ?></a>
			<a href="#style-settings"><?php esc_html_e( 'Style', 'custom-codes' ); ?></a>
			<a href="#plugin-settings"><?php esc_html_e( 'Plugin', 'custom-codes' ); ?></a>
			<a href="#pro"><?php esc_html_e( 'PRO Version', 'custom-codes' ); ?></a>
		</div>

		<h2 class="screen-reader-text">CodeKit Settings</h2>

	<?php settings_errors( null, null, true ); ?>

		<?php if ( isset( $_GET['settings-updated'] ) && sanitize_key( $_GET['settings-updated'] ) ) : //phpcs:ignore ?>
		<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
			<p><strong><?php esc_html_e( 'Settings saved.', 'custom-codes' ); ?></strong> <?php esc_html_e( 'Note: If you just update the media queries, you need to update the style codes to apply new ones.', 'custom-codes' ); ?></p><!-- Do this automatically ??? -->
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'custom-codes' ); ?></span></button>
		</div>
	<?php endif; ?>


		<form id="codes-settings-form" method="post" action="options.php" autocomplete="off">
	<?php settings_fields( 'codes_settings' ); ?>


			<div id="editor-settings" class="tab-content active">

				<div class="section-title">
					<h3 class="title"><?php esc_html_e( 'Editor Settings', 'custom-codes' ); ?></h3>
					<p><?php esc_html_e( 'Change the general settings', 'custom-codes' ); ?></p>
				</div>

				<table class="form-table editor-settings">
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_ajax']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_ajax" value="1" <?php echo get_option( '_codes_ajax' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Yes, please', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended for better experience', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_ajax" value="0" <?php echo ! get_option( '_codes_ajax' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'No, use default WP post saver', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo wp_kses( $registered_settings['_codes_sound']['description'], array( 'br' => true ) ); ?> <br>
							<p class="description"><small><?php esc_html_e( 'Only works if AJAX saver enabled', 'custom-codes' ); ?></small></p>
						</th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_sound" value="1" <?php echo get_option( '_codes_sound' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Yes, please', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended for better experience', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_sound" value="0" <?php echo ! get_option( '_codes_sound' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'No sound', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_shortcut']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_shortcut" value="1" <?php echo get_option( '_codes_shortcut' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Yes, use keyboard shortcut', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended for better experience', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_shortcut" value="0" <?php echo ! get_option( '_codes_shortcut' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'No keyboard shortcut', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_emmet']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_emmet" value="1" <?php echo get_option( '_codes_emmet' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Active', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_emmet" value="0" <?php echo ! get_option( '_codes_emmet' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Deactive', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
				</table>

			</div>


			<div id="style-settings" class="tab-content">

				<div class="section-title">
					<h3 class="title"><?php esc_html_e( 'Style Settings', 'custom-codes' ); ?></h3>
					<p><?php esc_html_e( 'Change the settings related to styles', 'custom-codes' ); ?></p>
				</div>

				<table class="form-table style-settings">
					<tr>
						<th scope="row"><?php esc_html_e( 'Initial Editor Tab', 'custom-codes' ); ?></th>
						<td>

							<fieldset>
								<label>
									<input type="radio" name="_codes_initial_editor" value="first" <?php echo get_option( '_codes_initial_editor' ) === 'first' ? 'checked' : ''; ?>>
									<?php esc_html_e( 'First Editor', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Default', 'custom-codes' ); ?>)</small>
								</label><br>
								<label>
									<input type="radio" name="_codes_initial_editor" value="global" <?php echo get_option( '_codes_initial_editor' ) === 'global' ? 'checked' : ''; ?>>
									<?php esc_html_e( 'Global Editor', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Editor without Media Query', 'custom-codes' ); ?>)</small>
								</label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_output_order']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label>
									<input type="radio" name="_codes_output_order" value="mobile-first" <?php echo get_option( '_codes_output_order' ) === 'mobile-first' ? 'checked' : ''; ?>>
									<?php esc_html_e( 'Mobile First', 'custom-codes' ); ?> &nbsp;
									<span class="dashicons dashicons-smartphone"></span> > <span class="dashicons dashicons-tablet"></span> > <span class="dashicons dashicons-desktop"></span> <small>(<?php esc_html_e( 'Recommended for mobile performance', 'custom-codes' ); ?>)</small>
								</label><br>
								<label>
									<input type="radio" name="_codes_output_order" value="desktop-first" <?php echo get_option( '_codes_output_order' ) === 'desktop-first' ? 'checked' : ''; ?>>
									<?php esc_html_e( 'Desktop First', 'custom-codes' ); ?>
									<span class="dashicons dashicons-desktop"></span> > <span class="dashicons dashicons-tablet"></span> > <span class="dashicons dashicons-smartphone"></span>
								</label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_desktop']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_desktop" value="<?php echo esc_attr( get_option( '_codes_desktop' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first hidden">@media (min-width: 1200px)</span>
									<span class="default desktop-first empty"><?php esc_html_e( 'No media query', 'custom-codes' ); ?></span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_tablet_l']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_tablet_l" value="<?php echo esc_attr( get_option( '_codes_tablet_l' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first hidden">@media (min-width: 992px)</span>
									<span class="default desktop-first">@media (max-width: 1199px)</span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_tablet_p']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_tablet_p" value="<?php echo esc_attr( get_option( '_codes_tablet_p' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first hidden">@media (min-width: 768px)</span>
									<span class="default desktop-first">@media (max-width: 991px)</span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_phone_l']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_phone_l" value="<?php echo esc_attr( get_option( '_codes_phone_l' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first hidden">@media (min-width: 480px)</span>
									<span class="default desktop-first">@media (max-width: 767px)</span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_phone_p']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_phone_p" value="<?php echo esc_attr( get_option( '_codes_phone_p' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first empty hidden"><?php esc_html_e( 'No media query', 'custom-codes' ); ?></span>
									<span class="default desktop-first">@media (max-width: 479px)</span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_retina']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<input class="regular-text" placeholder="<?php esc_html_e( 'No media query', 'custom-codes' ); ?>" type="text" name="_codes_retina" value="<?php echo esc_attr( get_option( '_codes_retina' ) ); ?>">
								<p class="description"><?php esc_html_e( 'Default', 'custom-codes' ); ?>:
									<span class="default mobile-first desktop-first">@media (min-device-pixel-ratio: 1.5)</span>
								</p>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Reset Media Queries as', 'custom-codes' ); ?></th>
						<td>

							<a href="#" class="button reset-queries mobile-first"><?php esc_html_e( 'Mobile First (Min Width)', 'custom-codes' ); ?></a>
							<a href="#" class="button reset-queries desktop-first"><?php esc_html_e( 'Desktop First (Max Width)', 'custom-codes' ); ?></a>

						</td>
					</tr>
				</table>

			</div>


			<div id="plugin-settings" class="tab-content">

				<div class="section-title">
					<h3 class="title"><?php esc_html_e( 'Plugin Settings', 'custom-codes' ); ?></h3>
					<p><?php esc_html_e( 'Change the core plugin settings', 'custom-codes' ); ?></p>
				</div>

				<table class="form-table plugin-settings">
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_admin_bar']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_admin_bar" value="1" <?php echo get_option( '_codes_admin_bar' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Yes, show the menu', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended for easy access', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_admin_bar" value="0" <?php echo ! get_option( '_codes_admin_bar' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Hide the menu on admin bar', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo wp_kses( $registered_settings['_codes_store']['description'], array( 'br' => true ) ); ?></th>
						<td>

							<fieldset>
								<label><input type="radio" name="_codes_store" value="1" <?php echo get_option( '_codes_store' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Yes, please', 'custom-codes' ); ?> <small>(<?php esc_html_e( 'Recommended for later use', 'custom-codes' ); ?>)</small></label><br>
								<label><input type="radio" name="_codes_store" value="0" <?php echo ! get_option( '_codes_store' ) ? 'checked' : ''; ?>> <?php esc_html_e( 'Delete the codes', 'custom-codes' ); ?></label>
							</fieldset>

						</td>
					</tr>
				</table>

			</div>


			<div id="pro" class="tab-content">

				<div class="section-title">
					<h3 class="title"><?php esc_html_e( 'CodeKit PRO', 'custom-codes' ); ?></h3>
					<p><?php esc_html_e( 'Here are all the additional professional features available:', 'custom-codes' ); ?></p>
				</div>

				<ul class="ul-disc">
					<li><?php esc_html_e( 'Priority support', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'LESS Editor', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Stylus Editor', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'CoffeeScript Editor', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'PUG Editor', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Editor Code Folding', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Editor Code Hints', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Custom Code Groups/Categories', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Custom Code Includes', 'custom-codes' ); ?></li>
					<li><?php esc_html_e( 'Advanced Code Release Locations', 'custom-codes' ); ?></li>
					<li><b><?php esc_html_e( 'And, much more coming soon...', 'custom-codes' ); ?></b></li>
				</ul><br>

				<p>
				<?php if ( codes_fs()->is_premium() ) : ?>
					<b><?php esc_html_e( 'Thank you for purchasing CodeKit PRO!', 'custom-codes' ); ?></b><br><br>
					<a href="mailto:info@pressx.co?subject=Feedback<?php echo codes_fs()->is_premium() ? '(Premium)' : ''; ?>" class="button"><?php esc_html_e( 'Share Us Your Feedback', 'custom-codes' ); ?></a>
				<?php else : ?>
					<a href="<?php echo esc_url( codes_fs()->get_upgrade_url() ); ?>" class="button button-hero button-primary"><?php esc_html_e( 'UPGRADE NOW', 'custom-codes' ); ?></a>
				<?php endif; ?>
				</p>

			</div>

			<p class="submit" style="position: sticky; bottom: 0;">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'custom-codes' ); ?>">
			</p>


		</form>


	</div> <!-- end .wrap -->

	<script>
		jQuery(document).ready(function($) {


			function openTab(tabName) {

				if ( !$(tabName).length ) return;

				$('.settings-tabs > a, .tab-content').removeClass('active');
				$('.settings-tabs > a[href="'+tabName+'"], .tab-content' + tabName).addClass('active');

				// Update URL
				history.pushState(null, null, tabName);

				// Update form URL
				$('#codes-settings-form').attr('action', 'options.php' + tabName);

				// Hide submit button on PRO tab
				console.log(tabName);
				if ( tabName == "#pro" ) $('.submit').addClass('hidden');
				else $('.submit').removeClass('hidden');

			}

			// Initial Tab
			openTab(window.location.hash);


			// TABS
			$('.settings-tabs > a').click(function(e) {

				openTab($(this).attr('href'));
				e.preventDefault();

			});


			// RESET QUERIES
			$('.reset-queries').click(function(e) {

				var direction = $(this).hasClass("mobile-first") ? "mobile-first" : "desktop-first";

				// Update the order
				$('input[value="'+ direction +'"]').prop('checked', true);

				// Update the queries
				$('.style-settings .default.' + direction).each(function() {
					var input = $(this).parents('fieldset').find('input[type="text"]');
					var defaultText = $(this).hasClass("empty") ? '' : $(this).text();
					input.val(defaultText);
				});

				e.preventDefault();

			});


		});
	</script>

	<?php
}




/**
 * Enqueue admin styles.
 *
 * @param string $hook Returns current admin page hook.
 */
function codes_settings_styles( $hook ) {
	// Early exit if on another admin page.
	if ( 'custom-code_page_settings' !== $hook ) {
		return;
	}

	// Admin Styles.
	wp_enqueue_style( 'codes_settings_styles', CODES_PLUGIN_URL . 'assets/style/settings.css', null, CODES_VERSION );

}
add_action( 'admin_enqueue_scripts', 'codes_settings_styles' );
