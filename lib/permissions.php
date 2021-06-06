<?php
/**
 *
 * Permissions check and notices.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


// Try to create the folder if not exists.
if ( ! file_exists( CODES_FOLDER_DIR ) ) {
	$wp_filesystem->mkdir( CODES_FOLDER_DIR, 0755 );
}

// Try to give correct permissions.
if ( ! codes_is_readable( CODES_FOLDER_DIR ) || ! codes_is_writable( CODES_FOLDER_DIR ) ) {
	$wp_filesystem->chmod( CODES_FOLDER_DIR, 0755 );
}




/**
 * Folder access notice.
 */
function codes_folder_access_notice() {

	// No notice if writable and readable.
	if ( file_exists( CODES_FOLDER_DIR ) && codes_is_readable( CODES_FOLDER_DIR ) && codes_is_writable( CODES_FOLDER_DIR ) && codes_is_executable( CODES_FOLDER_DIR ) ) {
		return;
	}

	?>
	<div class="notice notice-error is-dismissible">
		<p><b>CodeKit:</b> <?php esc_html_e( '"wp-content/custom_codes" folder does not have correct permissions. Please update its permissions to be able to use the plugin.', 'custom-codes' ); ?> (CHMOD <?php echo esc_html( codes_chmod_check( CODES_FOLDER_DIR ) ); ?> -> 755) <br>
		<span class="codes-debug"><?php esc_html_e( 'Exists:', 'custom-codes' ); ?> <?php echo file_exists( CODES_FOLDER_DIR ) ? 'Yes' : 'No'; ?> | <?php esc_html_e( 'Readable:', 'custom-codes' ); ?> <?php echo codes_is_readable( CODES_FOLDER_DIR ) ? 'Yes' : 'No'; ?> | <?php esc_html_e( 'Writable:', 'custom-codes' ); ?> <?php echo codes_is_writable( CODES_FOLDER_DIR ) ? 'Yes' : 'No'; ?> | <?php esc_html_e( 'Executable:', 'custom-codes' ); ?> <?php echo codes_is_executable( CODES_FOLDER_DIR ) ? 'Yes' : 'No'; ?></span></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'codes_folder_access_notice' );
