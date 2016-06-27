<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// CREATE AND GIVE PERMISSION TO THE CUSTOM CODES DIRECTORY
function cc_plugin_activate() {

	// Create the folder
	if (!file_exists(CC_DIR))
		mkdir(CC_DIR, 0755, true);

	// Give permission
	@chmod(CC_DIR, 0755);

}
register_activation_hook( CC_FILE, 'cc_plugin_activate' );




?>