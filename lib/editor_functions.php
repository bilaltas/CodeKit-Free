<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Sample Content
require_once( dirname(__file__).'/editor_defaults.php' );



function cc_restrictions() {
	global $cc_user_ID;

	if ( !current_user_can('cc_full_access') )
		die('You have no access to edit admin custom codes!');

}
if ( $cc_admin ) add_action('admin_init', 'cc_restrictions');



// ADD A NEW EDITOR
function cc_add_custom_editor($lang, $file_name, $extra_classes="", $putsample = true) {
	global $cc_admin, $cc_sass, $cc_editor_theme;

	// File Name
	if ( $cc_admin) $file_name = "admin_".$file_name;

	// File Extension
	$file_extension = $lang;
	if ( $lang == "sass" ) {
		$file_extension = "scss";
	}


	// Editor Content
	$file = CC_DIR."$file_name.$file_extension";
	$main_file_scss = CC_DIR.($cc_admin ? "admin_panel":"custom_public").".scss";
	$main_file_css = CC_DIR.($cc_admin ? "admin_panel":"custom_public").".css";
	$file_content = file_exists($file) ? @file_get_contents( $file ) : "";


	// EXCEPTIONS
	$file_empty = false;
	if ( ($file_name == "desktop" || $file_name == "admin_desktop") && $file_content == "" ) {

		if ( ($lang == "sass" || $lang == "css" ) && !file_exists($file) && file_exists($main_file_css) ) {
			$file_content = @file_get_contents( $main_file_css );
		} else {
			$file_content = cc_empty_codes($lang, $file_name);
			$file_empty = true;
		}


	} elseif ( $file_content == "" && $putsample ) {
		$file_content = cc_empty_codes($lang, $file_name);
		$file_empty = true;
	} elseif ( $file_content == "" && !$putsample ) {
		$file_content = "";
		$file_empty = true;
	} else {
		$file_content = esc_html( $file_content );
	}


	// Appearance
	$appearance = "not-active";
	if ( $file_name == "desktop" || $file_name == "admin_desktop" ) {
		$appearance = "active";
	} elseif ( ( $file_name == "custom_public" && $lang == "js" ) || ( $file_name == "admin_panel" && $lang == "js" ) ) {
		$appearance = "active";
	}


	// Theme
	$theme = "theme-dark";
	if ( $cc_editor_theme == "light" ) {
		$theme = "theme-light";
	}


	// Check existing SASS file
	$existing_sass = false;
	if ( !$cc_sass && file_exists(CC_DIR."$file_name.scss") ) $existing_sass = true;

	echo '<textarea class="code-editor '.$theme.' '.$appearance.' '.$lang.' '.$extra_classes.''.($existing_sass ? " existing-sass" : "").''.($file_empty ? " empty-file" : "").'" data-filename="'.str_replace('admin_', '', $file_name).'" data-fileextension="'.$file_extension.'">'.$file_content.'</textarea>';

}



/*
// NON-AJAX SAVER - NEEDS TO BE FIXED
function cc_saver () {
	global $_REQUEST, $custom_codes_public, $cc_admin;


	if (!file_exists(CC_DIR))
		mkdir(CC_DIR, 0755, true);

	if ( $custom_codes_public ) {

		@chmod(CC_DIR, 0755);
			$file_css = CC_DIR.'custom_public.css';
			$css_done = @file_put_contents($file_css, stripslashes_deep( $_REQUEST['css-file'] ), FILE_TEXT );

			$file_js = CC_DIR.'custom_public.js';
			$js_done = @file_put_contents($file_js, stripslashes_deep( $_REQUEST['js-file'] ), FILE_TEXT );


			if ( $css_done == 0 && $js_done == 0 ) {
				wp_redirect( "admin.php?page=cc-dev-custom-codes&settings-not-updated=".$css_done."-".$js_done ); exit;
			} else {
				wp_redirect( "admin.php?page=cc-dev-custom-codes&settings-updated" ); exit;
			}

	 } elseif ($cc_admin) {

		@chmod(CC_DIR, 0755);
			$file_css = CC_DIR.'admin_panel.css';
			$css_done = @file_put_contents($file_css, stripslashes_deep( $_REQUEST['css-file'] ), FILE_TEXT );

			$file_js = CC_DIR.'admin_panel.js';
			$js_done = @file_put_contents($file_js, stripslashes_deep( $_REQUEST['js-file'] ), FILE_TEXT );

			if ( $css_done == 0 && $js_done == 0 ) {
				wp_redirect( "admin.php?page=cc-dev-custom-codes&admin_panel=true&settings-not-updated=".$css_done."-".$js_done ); exit;
			} else {
				wp_redirect( "admin.php?page=cc-dev-custom-codes&admin_panel=true&settings-updated" ); exit;
			}

	 }

}
if ( isset($_POST['action']) && $_POST['action'] == "cc_save" ) add_action('admin_init', 'cc_saver');
*/


?>