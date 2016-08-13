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


}



// Add the new role to current user
function cc_add_role_to_current_user() {


	$args = array('role' => 'cc_admin');
	$cc_admins = get_users($args);
	if ( count($cc_admins) == 0 && !current_user_can('cc_admin') ) {

		// ASSIGN CURRENT USER THE ROLE
		$u = wp_get_current_user();
		$u->add_role( 'cc_admin' );

	}


}
add_action( 'admin_init', 'cc_add_role_to_current_user' );