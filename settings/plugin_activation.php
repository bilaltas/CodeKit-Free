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



// ADD THE CUSTOMER ROLE AND CAPABILITIES WHEN INSTALL
if( !get_role( 'cc_admin' ) ) {


	// Copy all administrator capabilities
	$admin = get_role('administrator');
	$caps = $admin->capabilities;
	//$caps['newcap'] = true;


	// Add the new role
	add_role('cc_admin', 'Custom Codes Admin', $caps);


	// ASSIGN CURRENT USER THE ROLE
	$u = new WP_User( get_current_user_id() );
	$u->add_role( 'cc_admin' );


}




?>