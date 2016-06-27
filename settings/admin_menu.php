<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// ADMIN MENU
function cc_admin_menu() {
    global $cc_page, $cc_sass;


	if ( current_user_can('administrator') ) {

	    $cc_page_main = add_menu_page (
	        'Custom Codes',
	        'Custom Codes',
	        'administrator',
	        'custom-codes',
	        'cc_editor_page',
	        plugin_dir_url( CC_FILE ).'lib/images/cc_dev_icon.png',
	        '500'
	    );

	    	$cc_page = add_submenu_page(
		    	'custom-codes',
		    	'Custom '.($cc_sass ? 'SASS' : 'CSS').' & JS',
		    	'Public Side Codes',
		    	'administrator',
		    	'custom-codes',
		    	'cc_editor_page'
		    );
		    add_action( 'load-' . $cc_page, 'cc_settings' );


			$cc_admin_page = add_submenu_page(
				'custom-codes',									// admin page slug
				'Custom '.($cc_sass ? 'SASS' : 'CSS').' & JS',  // page title
				'Admin Side Codes', 							// menu title
				'administrator',               					// capability required to see the page
				'custom-codes&admin_panel=true',           		// admin page slug, e.g. options-general.php?page=cc_options
				'cc_editor_page'             					// callback function to display the options page
			);

	}

}
add_action( 'admin_menu', 'cc_admin_menu' );



// ADMIN BAR MENU
function cc_wp_toolbar( $wp_admin_bar ) {
	global $cc_sass;
	if ( current_user_can('administrator') ) {

		$args = array(
			'id'    => 'cc_toolbar_custom_codes',
			'title' => '<span class="ab-icon"><img src="'.plugin_dir_url( CC_FILE ).'lib/images/cc_dev_icon.png"></span>
						<span class="ab-label">Custom Codes</span>',
			'href'  => admin_url('admin.php?page=custom-codes'),
			'meta'  => array( 'class' => 'cc-toolbar-custom-codes' )
		);
		$wp_admin_bar->add_node( $args );

			$args = array(
				'id'		=>	'cc_toolbar_custom_codes_public',
				'title'		=>	'Public Side Codes',
				'href'		=>	admin_url('admin.php?page=custom-codes'),
				'parent'	=>	'cc_toolbar_custom_codes',
			);
			if ( current_user_can('administrator') ) $wp_admin_bar->add_node($args);

			$args = array(
				'id'		=>	'cc_toolbar_custom_codes_admin',
				'title'		=>	'Admin Side Codes',
				'href'		=>	admin_url('admin.php?page=custom-codes&admin_panel=true'),
				'parent'	=>	'cc_toolbar_custom_codes',
			);
			if ( current_user_can('administrator') ) $wp_admin_bar->add_node($args);

	}
}
add_action( 'admin_bar_menu', 'cc_wp_toolbar', 9999 );

// Toolbar Style
function cc_toolbar_custom_codes_style() {

	if ( current_user_can('administrator') ) {

		echo "
			<style type='text/css'>

				#wp-admin-bar-cc_toolbar_custom_codes .ab-item .ab-icon {
					-webkit-filter: grayscale(80%);
					filter: grayscale(80%);
				}
				#wp-admin-bar-cc_toolbar_custom_codes .ab-item:hover .ab-icon {
					-webkit-filter: grayscale(0%);
					filter: grayscale(0%);
				}

			</style>
		";

	}
}
add_action( 'wp_head', 'cc_toolbar_custom_codes_style' );
add_action( 'admin_head', 'cc_toolbar_custom_codes_style' );


?>