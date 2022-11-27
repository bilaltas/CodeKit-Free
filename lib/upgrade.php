<?php
/**
 *
 * Upgrade actions
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Upgrade from old versions.
 */
function codes_upgrade() {

	$old_version = get_option( '_codes_version', '0' );
	$new_version = CODES_VERSION;
	if ( $old_version === $new_version ) {
		return;
	}

	// Check an old setting to see a new installation.
	do_action( 'codes_upgrade', $new_version, $old_version );
	update_option( '_codes_version', $new_version );

}
add_action( 'setup_theme', 'codes_upgrade' );




// FOR BEFORE 2.3.2 =======================================================.
/**
 * Create mixin posts.
 *
 * @param string $new_version New version number.
 * @param string $old_version Old version number.
 */
function codes_move_multisite_codes( $new_version, $old_version ) {
	global $wp_filesystem;

	if ( ! version_compare( $old_version, '2.3.2', '<' ) ) {
		return;
	}

	// List all the sites.
	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {

		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );

			// Get all the codes belong to current blog ID.
			$codes = get_posts(
				array(
					'post_type'      => 'custom-code',
					'posts_per_page' => -1,
					'post_status'    => 'any',
				)
			);

			// Early exit if posts are not ready.
			if ( ! is_array( $codes ) ) {
				continue;
			}

			// Process all the codes.
			foreach ( $codes as $code ) {

				// List & move all the files.
				foreach ( glob( WP_CONTENT_DIR . "/custom_codes/$code->ID-*.*" ) as $source_file_path ) {
					$file_name             = basename( $source_file_path );
					$destination_file_path = WP_CONTENT_DIR . "/custom_codes/site-$site->blog_id/$file_name";
					$wp_filesystem->move( $source_file_path, $destination_file_path );
				}
			}

			restore_current_blog();
		}
	}
}
add_action( 'codes_upgrade', 'codes_move_multisite_codes', 13, 2 );





// FOR BETWEEN 2.0.0 AND 2.0.3 =======================================================.

/**
 * Create mixin posts.
 *
 * @param string $new_version New version number.
 * @param string $old_version Old version number.
 */
function codes_create_mixin_posts( $new_version, $old_version ) {
	global $codes_langs, $wp_filesystem;

	if ( ! version_compare( $old_version, '2.0.3', '<' ) || ! version_compare( $old_version, '2.0.0', '>=' ) ) {
		return;
	}

	// Find the mixin files.
	foreach ( glob( CODES_FOLDER_DIR . '*-scss-mixins.scss' ) as $file_path ) {

		$file_name = basename( $file_path );
		$post_ID   = explode( '-', $file_name )[0];

		// Location.
		$location = get_option( '_codes_location', 'frontend' );

		// Type Label.
		$type_label = 'frontend' === $location ? __( 'Public Mixins', 'custom-codes' ) : __( 'Admin Mixins', 'custom-codes' );

		// Create mixin post.
		$mixin_id = wp_insert_post(
			array(
				'post_title'  => $type_label,
				'post_type'   => 'custom-code',
				'post_status' => 'private',
				'meta_input'  => array(
					'_codes_language'   => 'scss',
					'_codes_location'   => 'everywhere',
					'_codes_adminroles' => array(),
					'_codes_savecount'  => 0,
				),
			)
		);

		if ( ! is_wp_error( $mixin_id ) ) {

			// Create mixin editor.
			$new_file = CODES_FOLDER_DIR . "$mixin_id-scss-desktop.scss";
			$wp_filesystem->move( $file_path, $new_file );

			// Create mixin output.
			$output_file = CODES_FOLDER_DIR . "$mixin_id-scss-output.scss";
			$wp_filesystem->copy( $new_file, $output_file );

			// Find the post editors.
			foreach ( glob( CODES_FOLDER_DIR . "$post_ID-*.scss" ) as $post_file_path ) {
				if ( $post_file_path === $new_file || $post_file_path === $output_file ) {
					continue;
				}

				$editor_content = $wp_filesystem->get_contents( $post_file_path );
				$editor_content = '/* ' . __( 'Import Mixins', 'custom-codes' ) . " */\n@import \"$mixin_id-scss-desktop\"; \n\n$editor_content";

				$wp_filesystem->put_contents( $post_file_path, $editor_content, FILE_TEXT );

			}
		}
	} // Old mixins loop

}
add_action( 'codes_upgrade', 'codes_create_mixin_posts', 12, 2 );




// FOR BEFORE 2.0.0 =======================================================.

/**
 * Import old mixins.
 *
 * @param string $type Admin or Public files.
 */
function codes_import_old_mixins( $type = 'Public' ) {
	global $wp_filesystem;

	// Type Label.
	$type_label = 'Admin' === $type ? __( 'Admin Mixins', 'custom-codes' ) : __( 'Public Mixins', 'custom-codes' );

	// Admin Prefix.
	$prefix = 'Admin' === $type ? 'admin_' : '';

	// Exit earlier if no file exists.
	$old_file = CODES_FOLDER_DIR . $prefix . 'mixins.scss';
	if ( ! file_exists( $old_file ) ) {
		return false;
	}

	// Create post.
	$post_ID = wp_insert_post(
		array(
			'post_title'  => $type_label,
			'post_type'   => 'custom-code',
			'post_status' => 'private',
			'meta_input'  => array(
				'_codes_language'   => 'scss',
				'_codes_location'   => 'everywhere',
				'_codes_adminroles' => array(),
				'_codes_savecount'  => 0,
			),
		)
	);
	if ( ! is_wp_error( $post_ID ) ) {

		$new_file = CODES_FOLDER_DIR . "$post_ID-scss-desktop.scss";
		$wp_filesystem->move( $old_file, $new_file );

		$output_file = CODES_FOLDER_DIR . "$post_ID-scss-output.scss";
		$wp_filesystem->copy( $new_file, $output_file );

		return $post_ID;

	}

	return 'Error';

}

/**
 * Import old styles.
 *
 * @param string $type Admin or Public files.
 * @param int    $mixin_id Mixin ID.
 */
function codes_import_old_styles( $type = 'Public', $mixin_id = false ) {
	global $wp_filesystem;

	// Admin Prefix.
	$prefix = 'Admin' === $type ? 'admin_' : '';

	// Type Label.
	$type_label = 'Admin' === $type ? __( 'Admin', 'custom-codes' ) : __( 'Public', 'custom-codes' );

	// Style Mode (SCSS || CSS).
	$style_lang = get_option( 'cstm_cds_style_mode', 'scss' ) === 'sass' ? 'scss' : get_option( 'cstm_cds_style_mode', 'scss' );

	// Location.
	$location = 'Public' === $type ? 'frontend' : 'backend';

	// Admin Roles.
	$roles = 'Public' === $type ? array() : get_option( 'cstm_cds_permission_roles', array() );

	// Save Count.
	$save_count = get_option( 'cstm_cds_' . $prefix . 'css_save_count', 0 );

	// Create post.
	$post_ID = wp_insert_post(
		array(
			'post_title'  => sprintf(
				/* translators: 1: Admin or Public 2: Language selected */
				__( '%1$s Side %2$s', 'custom-codes' ),
				$type_label,
				strtoupper( $style_lang )
			),
			'post_type'   => 'custom-code',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_codes_language'   => $style_lang,
				'_codes_location'   => $location,
				'_codes_adminroles' => $roles,
				'_codes_savecount'  => $save_count,
			),
		)
	);
	if ( ! is_wp_error( $post_ID ) ) {

		// Rename old editors.
		$old_editors = array(
			$prefix . 'desktop'  => 'desktop',
			$prefix . 'tablet-l' => 'tablet-l',
			$prefix . 'tablet-p' => 'tablet-p',
			$prefix . 'mobile-l' => 'mobile-l',
			$prefix . 'mobile-p' => 'mobile-p',
			$prefix . 'retina'   => 'retina',
			( 'Public' === $type ? 'custom_public' : 'admin_panel' ) => 'output',
		);

		foreach ( $old_editors as $old => $new ) {

			// SCSS.
			$old_file_scss = CODES_FOLDER_DIR . "$old.scss";
			$new_file_scss = CODES_FOLDER_DIR . "$post_ID-scss-$new.scss";
			if ( file_exists( $old_file_scss ) ) {

				$old_file_content = $wp_filesystem->get_contents( $old_file_scss );
				$new_file_content = $old_file_content;
				$wp_filesystem->move( $old_file_scss, $new_file_scss );

				$mixin_filename = "$mixin_id-scss-desktop";
				$mixin_file     = CODES_FOLDER_DIR . "$mixin_filename.scss";

				if ( is_numeric( $mixin_id ) && file_exists( $mixin_file ) ) {

					$new_file_content = '/* ' . __( 'Import Mixins', 'custom-codes' ) . " */\n@import \"$mixin_filename\"; \n\n$new_file_content";
					$wp_filesystem->put_contents( $new_file_scss, $new_file_content, FILE_TEXT );

				}
			}

			// CSS.
			$old_file_css = CODES_FOLDER_DIR . "$old.css";
			$new_file_css = CODES_FOLDER_DIR . "$post_ID-" . ( 'output' === $new ? 'scss' : 'css' ) . "-$new.css";
			if ( file_exists( $old_file_css ) ) {
				$wp_filesystem->move( $old_file_css, $new_file_css );
			}
		}
	}

}

/**
 * Import old scripts.
 *
 * @param string $type Admin or Public files.
 */
function codes_import_old_scripts( $type = 'Public' ) {
	global $wp_filesystem;

	// Exit earlier if no file exists.
	if (
		( 'Public' === $type && ! file_exists( CODES_FOLDER_DIR . 'custom_public.js' ) && ! file_exists( CODES_FOLDER_DIR . 'custom_public_head.js' ) )
		|| ( 'Admin' === $type && ! file_exists( CODES_FOLDER_DIR . 'admin_panel.js' ) && ! file_exists( CODES_FOLDER_DIR . 'admin_panel_head.js' ) )
	) {
		return;
	}

	// Admin Prefix.
	$prefix = 'Admin' === $type ? 'admin_panel' : 'custom_public';

	// Type Label.
	$type_label = 'Admin' === $type ? __( 'Admin', 'custom-codes' ) : __( 'Public', 'custom-codes' );

	// Extension.
	$extension = 'js';

	// Location.
	$location = 'Public' === $type ? 'frontend' : 'backend';

	// Admin Roles.
	$roles = 'Public' === $type ? array() : get_option( 'cstm_cds_permission_roles', array() );

	// Save Count.
	$save_count = get_option( 'cstm_cds_js_head_save_count', 0 );

	// Create post.
	$post_ID = wp_insert_post(
		array(
			'post_title'  => sprintf(
				/* translators: 1: Admin or Public 2: Language selected */
				__( '%1$s Side %2$s', 'custom-codes' ),
				$type_label,
				strtoupper( $extension )
			),
			'post_type'   => 'custom-code',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_codes_language'   => $extension,
				'_codes_location'   => $location,
				'_codes_adminroles' => $roles,
				'_codes_savecount'  => $save_count,
			),
		)
	);
	if ( ! is_wp_error( $post_ID ) ) {

		// Rename old editors.
		$old_editors = array(
			$prefix           => 'head',
			$prefix . '_head' => 'body-closing',
		);

		foreach ( $old_editors as $old => $new ) {

			$old_file = CODES_FOLDER_DIR . "$old.$extension";
			$new_file = CODES_FOLDER_DIR . "$post_ID-$extension-$new.$extension";
			if ( file_exists( $old_file ) ) {
				$wp_filesystem->move( $old_file, $new_file );
			}
		}
	}
}

/**
 * Import old custom PHP functions.
 */
function codes_import_old_php() {
	global $wp_filesystem;

	// Exit earlier if no file exists.
	$old_file = CODES_FOLDER_DIR . 'admin_functions.php';
	if ( ! file_exists( $old_file ) ) {
		return;
	}

	// Location.
	$location = 'everywhere';

	// Admin Roles.
	$roles = array();

	// Save Count.
	$save_count = 0;

	// Create post.
	$post_ID = wp_insert_post(
		array(
			'post_title'  => __( 'Custom PHP Functions', 'custom-codes' ),
			'post_type'   => 'custom-code',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_codes_language'   => 'php',
				'_codes_location'   => $location,
				'_codes_adminroles' => $roles,
				'_codes_savecount'  => $save_count,
			),
		)
	);
	if ( ! is_wp_error( $post_ID ) ) {

		$new_file = CODES_FOLDER_DIR . "$post_ID-php-default.php";
		$wp_filesystem->move( $old_file, $new_file );

	}

}

/**
 * Save the old admin notes.
 */
function codes_import_admin_notes() {
	global $wp_filesystem;

	// Exit earlier if no file exists.
	$old_notes = get_option( 'cstm_cds_admin_notes', array() );
	if ( ! count( $old_notes ) ) {
		return;
	}

	// Location.
	$location = 'backend';

	// Admin Roles.
	$roles = array( 'administrator' );

	// Save Count.
	$save_count = 0;

	// Create post.
	$post_ID = wp_insert_post(
		array(
			'post_title'  => __( 'Admin Notes', 'custom-codes' ),
			'post_type'   => 'custom-code',
			'post_status' => 'private',
			'meta_input'  => array(
				'_codes_language'   => 'html',
				'_codes_location'   => $location,
				'_codes_adminroles' => $roles,
				'_codes_savecount'  => $save_count,
			),
		)
	);
	if ( ! is_wp_error( $post_ID ) ) {

		$content  = "<!--\n";
		$content .= __( 'Admin Notes', 'custom-codes' ) . ":\n\n";
		foreach ( $old_notes as $user_ID => $note ) {

			$user = get_user_by( 'id', $user_ID );

			$content .= "#$user_ID ($user->user_email):\n";
			$content .= "$note\n\n";

		}
		$content .= '-->';

		$wp_filesystem->put_contents( CODES_FOLDER_DIR . "$post_ID-html-head.html", $content );

	}

}


/**
 * Create posts for existing files (SCSS/CSS, Admin SCSS/CSS, JS, Admin JS, Admin PHP, Admin Notes).
 *
 * @param string $new_version New version number.
 * @param string $old_version Old version number.
 */
function codes_create_posts( $new_version, $old_version ) {

	if (
		! version_compare( $old_version, '2.0.0', '<' )
		|| count( get_posts( array( 'post_type' => 'custom-code' ) ) )
	) {
		return;
	}

	// Import Mixins.
	$public_mixin_id = codes_import_old_mixins( 'Public' );
	$admin_mixin_id  = codes_import_old_mixins( 'Admin' );

	// Import Styles.
	codes_import_old_styles( 'Public', $public_mixin_id );
	codes_import_old_styles( 'Admin', $admin_mixin_id );

	// Delete style options.
	delete_option( 'cstm_cds_style_mode' );
	delete_option( 'cstm_cds_css_save_count' );
	delete_option( 'cstm_cds_admin_css_save_count' );

	// Import Scripts.
	codes_import_old_scripts( 'Public' );
	codes_import_old_scripts( 'Admin' );

	// Delete script options.
	delete_option( 'cstm_cds_js_head_save_count' );
	delete_option( 'cstm_cds_js_bottom_save_count' );
	delete_option( 'cstm_cds_admin_js_head_save_count' );
	delete_option( 'cstm_cds_admin_js_bottom_save_count' );
	delete_option( 'cstm_cds_permission_roles' );

	// Import Admin PHP.
	codes_import_old_php();

	// Import Admin Notes.
	codes_import_admin_notes();
	delete_option( 'cstm_cds_admin_notes' );

}
add_action( 'codes_upgrade', 'codes_create_posts', 10, 2 );


/**
 * Import & remove old settings.
 *
 * @param string $new_version New version number.
 * @param string $old_version Old version number.
 */
function codes_import_old_settings( $new_version, $old_version ) {
	global $wp_roles;

	if ( ! version_compare( $old_version, '2.0.0', '<' ) || get_role( 'cstm_cds_admin' ) === null ) {
		return;
	}

	// Update media query direction update.
	update_option( '_codes_output_order', 'desktop-first' );

	// Media query options.
	$desktop  = '';
	$tablet_l = '@media (max-width: ' . get_option( 'cstm_cds_tablet_l', 1199 ) . 'px)';
	$tablet_p = '@media (max-width: ' . get_option( 'cstm_cds_tablet_p', 991 ) . 'px)';
	$phone_l  = '@media (max-width: ' . get_option( 'cstm_cds_phone_l', 767 ) . 'px)';
	$phone_p  = '@media (max-width: ' . get_option( 'cstm_cds_phone_p', 479 ) . 'px)';
	$retina   = '@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (-o-min-device-pixel-ratio: 3/2), only screen and (min--moz-device-pixel-ratio: 1.5), only screen and (min-device-pixel-ratio: 1.5)';

	// Update.
	update_option( '_codes_desktop', $desktop );
	update_option( '_codes_tablet_l', $tablet_l );
	update_option( '_codes_tablet_p', $tablet_p );
	update_option( '_codes_phone_l', $phone_l );
	update_option( '_codes_phone_p', $phone_p );
	update_option( '_codes_retina', $retina );

	// Delete old query numbers.
	delete_option( 'cstm_cds_tablet_l' );
	delete_option( 'cstm_cds_tablet_p' );
	delete_option( 'cstm_cds_phone_l' );
	delete_option( 'cstm_cds_phone_p' );

	// Store files option.
	$store = get_option( 'cstm_cds_store_files', '' ) === 'yes' ? true : false;
	update_option( '_codes_store', $store );
	delete_option( 'cstm_cds_store_files' );

	// User options.
	update_user_meta( get_current_user_id(), '_codes_fontsize', 14 );
	update_user_meta( get_current_user_id(), '_codes_indent', 'space-4' );

	$theme = get_option( 'cstm_cds_editor_theme', 'dark' ) !== 'dark' ? 'default' : 'dark';
	update_user_meta( get_current_user_id(), '_codes_theme', $theme );
	delete_option( 'cstm_cds_editor_theme' );

	// Deprecated option.
	delete_option( 'cstm_cds_admin_roles' );

	// Remove the old capability.
	foreach ( $wp_roles->roles as $role_name => $role_details ) {

		$role = get_role( $role_name );
		$role->remove_cap( 'cstm_cds_full_access' );

	}

	// Remove the old role.
	remove_role( 'cstm_cds_admin' );

}
add_action( 'codes_upgrade', 'codes_import_old_settings', 11, 2 );
