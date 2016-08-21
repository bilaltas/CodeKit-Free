<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// PERMISSION UPDATES
function cc_update_permissions() {
	global $wp_roles;


	// Call the settings
	$cc_permission_roles = cc_pull_option(
		'cc_permission_roles',
		array('cc_admin', 'administrator') // Default settings
	);


	// Check all the roles
	foreach ( $wp_roles->roles as $role_name => $role_details) {

			$role = get_role( $role_name );
			if ( in_array($role_name, $cc_permission_roles) || $role_name == 'cc_admin' )
				$role->add_cap( 'cc_full_access' );

			else
				$role->remove_cap( 'cc_full_access' );

	}


}
add_action('admin_init', 'cc_update_permissions');