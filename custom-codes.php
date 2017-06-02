<?php
/*
Plugin Name: Custom Codes
Plugin URI: http://www.bilaltas.net/
Description: Your custom SASS, CSS, JS and PHP customizations in same directory with the best advanced code editor CodeMirror.
Author: Bilal TAS
Author URI: http://www.bilaltas.net
License: GNU GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
Version: 0.2.2

Custom Codes is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Custom Codes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Custom Codes. If not, see http://www.gnu.org/licenses/gpl-3.0.txt.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'CC_FILE', __FILE__ );
define( 'CC_DIR', WP_CONTENT_DIR ."/custom_codes/" );
define( 'CC_DEBUG', false );


if ( is_admin() ) { // Back-End

	require_once( dirname( CC_FILE ).'/settings/plugin_activation.php' );
	require_once( dirname( CC_FILE ).'/settings/helper_functions.php' );
	require_once( dirname( CC_FILE ).'/settings/permission_update.php' );
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
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/bilaltas/custom-codes/',
	__FILE__,
	'unique-plugin-or-theme-slug'
);