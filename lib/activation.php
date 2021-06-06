<?php
/**
 *
 * Plugin activation actions.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Create and give permission to the custom codes directory.
 */
function codes_plugin_activate() {

	// Create the folder.
	if ( ! file_exists( CODES_FOLDER_DIR ) ) {
		mkdir( CODES_FOLDER_DIR, 0755, true );
	}

	// Give permission.
	if ( ! is_readable( CODES_FOLDER_DIR ) || ! codes_is_writable( CODES_FOLDER_DIR ) ) {
		chmod( CODES_FOLDER_DIR, 0755 );
	}

}
register_activation_hook( CODES_FILE, 'codes_plugin_activate' );




/**
 * Shortcut in the plugins page.
 *
 * @param array $actions Plugin action links array.
 * @param array $file Returns plugin file name.
 */
function codes_plugins_page_link( $actions, $file ) {

	if ( strpos( $file, 'custom-codes' ) !== false ) {

		$actions['settings'] = '<a href="' . admin_url( 'edit.php?post_type=custom-code&page=settings' ) . '">' . __( 'Settings', 'custom-codes' ) . '</a>';

	}

	return $actions;

}
add_filter( 'plugin_action_links', 'codes_plugins_page_link', 2, 2 );




/**
 * Call the translation.
 */
function codes_load_plugin_textdomain() {

	load_textdomain( 'custom-codes', CODES_PLUGIN_DIR . '/languages/custom-codes-' . get_locale() . '.mo' );

}
add_action( 'plugins_loaded', 'codes_load_plugin_textdomain' );
