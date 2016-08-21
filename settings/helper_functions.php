<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// PLUGIN VERSION CHECK
function cc_get_the_plugin_version() {
	$plugin_data = get_plugin_data( CC_FILE );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}

function cc_get_the_latest_released_version() {
	global $cc_UpdateChecker;

	return $cc_UpdateChecker->requestInfo()->version;
}




// SETTINGS LINK IN THE PLUGINS PAGE
function cc_settings_link($actions, $file) {
if(false !== strpos($file, 'custom-codes') && current_user_can('cc_full_access'))
 $actions['settings'] = '<a href="'.admin_url('admin.php?page=custom-codes').'">Custom Codes</a>';
return $actions;
}
add_filter('plugin_action_links', 'cc_settings_link', 2, 2);




// AVOID DISABLING THIS PLUGIN
function cc_disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {

		// Remove edit link for all
		if ( array_key_exists( 'edit', $actions ) ) {
			if ( !current_user_can('cc_admin') ) unset( $actions['edit'] );
		}

		// Remove deactivate link for crucial plugins
		if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(
			'custom-codes/custom-codes.php'
		// You can add any other plugins here
		))) {
			if ( !current_user_can('cc_admin') ) unset( $actions['deactivate'] );
		}

		return $actions;

}
add_filter( 'plugin_action_links', 'cc_disable_plugin_deactivation', 10, 4 );





// Remove 'Custom Codes Admin' from the list of roles if the current user is not an admin
function cc_editable_roles_func( $roles ){

	$args = array('role' => 'cc_admin');
	$cc_admins = get_users($args);

    if ( isset( $roles['cc_admin'] ) &&	!current_user_can('cc_admin') ) {

		unset( $roles['cc_admin']);

	}

    return $roles;
}
add_filter( 'editable_roles', 'cc_editable_roles_func');


// If someone is trying to edit or delete and admin and that user isn't an admin, don't allow it
function cc_map_meta_cap_func( $caps, $cap, $user_id, $args ){

    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'cc_admin' ) ){
                if(!current_user_can('cc_admin')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'cc_admin' ) ){
                if(!current_user_can('cc_admin')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
}
add_filter( 'map_meta_cap', 'cc_map_meta_cap_func',10,4);






// OPTION CALLER
function cc_pull_option($setting_name, $default_value) {

	$option = get_option( $setting_name, $default_value );

	return !isset($option) || $option == "" ? $default_value : $option;

}




// PAGE FORWARDER
function cc_forward_page($direction) {
	if (!headers_sent()) {
		wp_redirect($direction);
		exit;
	} else {
		print '<script type="text/javascript">';
		print 'window.location.href="' . $direction . '";';
		print '</script>';
		print '<noscript>';
		print '<meta http-equiv="refresh" content="0;url=' . $direction . '" />';
		print '</noscript>';
	}
}




// PROCESS TIMER
function cc_process_timer_start() {
	global $starttime;

	//$starttime = getrusage();
	$starttime = gettimeofday();

    //RETRIEVE SECONDS AND MICROSECONDS (ONE MILLIONTH OF A SECOND)
    //CONVERT MICROSECONDS TO SECONDS AND ADD TO RETRIEVED SECONDS
    //MULTIPLY BY 1000 TO GET MILLISECONDS
    $starttime = 1000*($starttime['sec'] + ($starttime['usec'] / 1000000));
}
function cc_process_timer_finish() {
	global $starttime;

	//$ru = getrusage();
	//return rutime($ru, $starttime, "utime")." ms";

	//echo "This process used ". rutime($ru, $rustart, "utime") ." ms for its computations\n";
	//echo "It spent ". rutime($ru, $rustart, "stime") ." ms in system calls\n";

	//return microtime(true) - $starttime;


    $timeofday = gettimeofday();
    //RETRIEVE SECONDS AND MICROSECONDS (ONE MILLIONTH OF A SECOND)
    //CONVERT MICROSECONDS TO SECONDS AND ADD TO RETRIEVED SECONDS
    //MULTIPLY BY 1000 TO GET MILLISECONDS
    $endtime = 1000*($timeofday['sec'] + ($timeofday['usec'] / 1000000));

    return ($endtime - $starttime)." ms";
	unset($starttime);

}
/*
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000)) - ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}
*/


?>