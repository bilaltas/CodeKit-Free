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



// ADD THE CUSTOM CODES ADMIN ROLE AND CAPABILITIES WHEN INSTALL
if( !get_role( 'cc_admin' ) ) {

	$caps['cc_full_access'] = true;

	// Add the new role
	add_role('cc_admin', 'Custom Codes Admin', $caps);

}



// Add the new role to current user
function cc_add_role_to_current_user() {

	$cc_admins = get_users(['role' => 'cc_admin']);

	// If no one is cc_admin and I'm the administrator, make me the cc_admin
	if ( count($cc_admins) == 0 && !current_user_can('cc_admin') && current_user_can('administrator') ) {

		// ASSIGN CURRENT USER THE ROLE
		$u = wp_get_current_user();
		$u->add_role( 'cc_admin' );

	} elseif ( current_user_can('cc_admin') && !current_user_can('administrator') ) {

		// ASSIGN CURRENT USER THE ADMIN ROLE
		$u = wp_get_current_user();
		$u->add_role( 'administrator' );

	}


}
add_action( 'admin_init', 'cc_add_role_to_current_user' );