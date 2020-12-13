<?php
/**
 *
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

	// Pro Features Link !!!PRO.
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
					<?php echo wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/custom_codes.svg' ), codes_svg_args() ); ?>
					<?php esc_html_e( 'Custom Codes Settings', 'custom-codes' ); ?>
					<a href="https://wordpress.org/plugins/custom-codes/#developers" target="_blank" class="version">v<?php echo esc_html( CODES_VERSION ); ?></a>
				</div>

				<div class="navigation">
					<a href="mailto:info@pressx.co?subject=Support"><?php echo wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-support.svg' ), codes_svg_args() ); ?> <?php esc_html_e( 'Support', 'custom-codes' ); ?></a>
					<a href="mailto:info@pressx.co?subject=Feedback"><?php echo wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-check.svg' ), codes_svg_args() ); ?> <?php esc_html_e( 'Feedback', 'custom-codes' ); ?></a>
				</div>
			</div>

			<div class="settings-tabs">
				<a href="#editor-settings" class="active"><?php esc_html_e( 'Editor', 'custom-codes' ); ?></a>
				<a href="#style-settings"><?php esc_html_e( 'Style', 'custom-codes' ); ?></a>
				<a href="#plugin-settings"><?php esc_html_e( 'Plugin', 'custom-codes' ); ?></a>
			</div>

		</div>

		<h2 class="screen-reader-text">Custom Codes Settings</h2>

		<?php settings_errors( null, null, true ); ?>

		<?php if ( isset( $_GET['settings-updated'] ) && sanitize_key( $_GET['settings-updated'] ) ) : ?>
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
									<span class="default mobile-first">@media (min-width: 1200px)</span>
									<span class="default desktop-first empty hidden"><?php esc_html_e( 'No media query', 'custom-codes' ); ?></span>
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
									<span class="default mobile-first">@media (min-width: 992px)</span>
									<span class="default desktop-first hidden">@media (max-width: 1199px)</span>
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
									<span class="default mobile-first">@media (min-width: 768px)</span>
									<span class="default desktop-first hidden">@media (max-width: 991px)</span>
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
									<span class="default mobile-first">@media (min-width: 480px)</span>
									<span class="default desktop-first hidden">@media (max-width: 767px)</span>
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
									<span class="default mobile-first empty"><?php esc_html_e( 'No media query', 'custom-codes' ); ?></span>
									<span class="default desktop-first hidden">@media (max-width: 479px)</span>
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


			<p class="submit" style="position: sticky; bottom: 0; display: inline-block;">
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
