<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


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

			$cc_admin_active = false;
			foreach ( cc_pull_option( 'cc_admin_roles', array() ) as $cc_role ) {

				if ( current_user_can($cc_role) ) {
					$cc_admin_active = true;
					break;
				}

			}


			if ( !current_user_can('cc_admin') && $cc_admin_active ) {

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
if ( !function_exists('cc_include_custom_functions') ) {

	function cc_include_custom_functions() {


		if(file_exists(WP_CONTENT_DIR .'/custom_codes/admin_functions.php')) {

			error_reporting(0);
			require( WP_CONTENT_DIR .'/custom_codes/admin_functions.php' );

		}

	}
	cc_include_custom_functions();

}
?>