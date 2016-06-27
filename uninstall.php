<?php

if( defined( 'ABSPATH') && defined('WP_UNINSTALL_PLUGIN') ) {

	// STORE SETTINGS?
	$cc_store_custom_files = get_option( 'cc_store_files', 'true' ) == "" ? false : true;


	// APPEND THE CUSTOM CSS AND JS FILES
	$custom_codesizations = $cc_store_custom_files ? "


// CUSTOM-CODES CUSTOMIZATIONS ###############
if ( !is_admin() ) { // Front-End


	// RELEASE CUSTOM CSS AND JS FILES
	if ( !function_exists('cc_public_codes') ) {

		function cc_public_codes() {

			// CSS File
			if( file_exists(WP_CONTENT_DIR .'/custom_codes/custom_public.css') )
				wp_enqueue_style( 'custom', WP_CONTENT_URL .'/custom_codes/custom_public.css' );

			// Call jQuery
			wp_enqueue_script('jquery');

			// JS File Head
			if( file_exists(WP_CONTENT_DIR .'/custom_codes/custom_public_head.js') )
				wp_enqueue_script( 'custom-head', WP_CONTENT_URL .'/custom_codes/custom_public_head.js', array( 'jquery' ), '1.0.0');

			// JS File Bottom
			if( file_exists(WP_CONTENT_DIR .'/custom_codes/custom_public.js') )
				wp_enqueue_script( 'custom-bottom', WP_CONTENT_URL .'/custom_codes/custom_public.js', array( 'jquery' ), '1.0.0', true);

		}
		add_action( 'wp_enqueue_scripts', 'cc_public_codes', 99999 );

	}


} elseif ( is_admin() ) { // Back-End


	// RELEASE ADMIN CUSTOM CSS AND JS FILES
	if ( !function_exists('cc_admin_panel_codes') ) {

		function cc_admin_panel_codes() {

			if ( !current_user_can('administrator') ) { // FIX THIS - EXCEPTIONS

				// CSS File
				if( file_exists(WP_CONTENT_DIR .'/custom_codes/admin_panel.css') )
					wp_enqueue_style( 'custom-admin', WP_CONTENT_URL .'/custom_codes/admin_panel.css' );

				// Call jQuery
				wp_enqueue_script('jquery');

				// JS File Head
				if( file_exists(WP_CONTENT_DIR .'/custom_codes/admin_panel_head.js') )
					wp_enqueue_script( 'custom-admin-head', WP_CONTENT_URL .'/custom_codes/admin_panel_head.js', array( 'jquery' ), '1.0.0');

				// JS File Bottom
				if( file_exists(WP_CONTENT_DIR .'/custom_codes/admin_panel.js') )
					wp_enqueue_script( 'custom-admin-bottom', WP_CONTENT_URL .'/custom_codes/admin_panel.js', array( 'jquery' ), '1.0.0', true);

			}

		}
		add_action( 'admin_enqueue_scripts', 'cc_admin_panel_codes', 99999 );

	}


}


// RELEASE CUSTOM functions.php
if ( !function_exists('cc_include_custom_functions') ) { // Both

	function cc_include_custom_functions() {

		if(file_exists(WP_CONTENT_DIR .'/custom_codes/admin_functions.php'))
			include( WP_CONTENT_DIR .'/custom_codes/admin_functions.php' );

	}
	cc_include_custom_functions();

}
// CUSTOM-CODES CUSTOMIZATIONS END ###############
	" : "";

	// APPEND IT
	@chmod(get_stylesheet_directory(). "/functions.php", 0755);
	@file_put_contents( get_stylesheet_directory(). "/functions.php", $custom_codesizations, FILE_APPEND);
	@chmod(get_stylesheet_directory(). "/functions.php", 0644);



	//Remove the plugin's settings
	if ( get_option( 'cc_admin_notes' ) ) delete_option( 'cc_admin_notes' );
	if ( get_option( 'cc_style_mode' ) ) delete_option( 'cc_style_mode' );
	if ( get_option( 'cc_store_files' ) ) delete_option( 'cc_store_files' );
	if ( get_option( 'cc_tablet_l' ) ) delete_option( 'cc_tablet_l' );
	if ( get_option( 'cc_tablet_p' ) ) delete_option( 'cc_tablet_p' );
	if ( get_option( 'cc_phone_l' ) ) delete_option( 'cc_phone_l' );
	if ( get_option( 'cc_phone_p' ) ) delete_option( 'cc_phone_p' );

}

?>