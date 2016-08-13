<?php
/*
Plugin Name: Custom SASS, CSS, JS and PHP
Plugin URI: http://www.bilaltas.net/
Description: Your custom SASS, CSS, JS and PHP customizations in same directory with the best advanced code editor CodeMirror.
Author: Bilal TAS
Author URI: http://bilaltas.net
Version: 0.0.3
Last Updated: 2016-08-13 22:41 EET
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'CC_FILE', __FILE__ );
define( 'CC_DIR', WP_CONTENT_DIR ."/custom_codes/" );
define( 'CC_DEBUG', false );


if ( is_admin() ) { // Back-End

	require_once( dirname( CC_FILE ).'/settings/plugin_activation.php' ); // ADD NEW ROLES!
	require_once( dirname( CC_FILE ).'/settings/helper_functions.php' );
	require_once( dirname( CC_FILE ).'/settings/settings.php' );

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset($_POST['cc_editor_contents']) )
		require_once( dirname( CC_FILE ).'/lib/editor_ajax_saver.php' );

	if ( isset($_GET['page']) && $_GET['page'] == "custom-codes" )
		require_once( dirname( CC_FILE ).'/lib/editor_page.php' );

}
require_once( dirname( CC_FILE ).'/settings/admin_menu.php' );
require_once( dirname( CC_FILE ).'/lib/release_codes.php' );



// CHECK FOR PLUGIN UPDATES
require 'plugin-update-checker/plugin-update-checker.php';
$cc_UpdateChecker = PucFactory::buildUpdateChecker(
	'http://bilaltas.net/updates_cc/?action=get_metadata&slug=custom-codes', //Metadata URL.
	__FILE__, //Full path to the main plugin file.
	'custom-codes' //Plugin slug. Usually it's the same as the name of the directory.
);