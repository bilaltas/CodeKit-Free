<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Sample Content
require_once( dirname(__file__).'/editor_defaults.php' );


// SASS
require_once( dirname(__file__).'/vendor/scssphp/scss.inc.php' );
use Leafo\ScssPhp\Compiler;

if ($cc_sass) {

	$cc_scss = new Compiler();
	$cc_scss->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
	//$cc_scss->setLineNumberStyle(Compiler::LINE_COMMENTS);
	$cc_scss->addImportPath(function($path) {
	    if (!file_exists(CC_DIR.$path)) return null;
	    return CC_DIR.$path;
	});

}


// SAVE THE FILES WITH AJAX
function cc_saver_ajax() {
	global $cc_sass, $cc_scss, $cc_sass_responsivity_codes, $cc_result_level;

	// THE DATA
	$data = $_POST;





	// Security Check
	if (
		!current_user_can('cc_full_access') ||
		!isset($data['cc_editor_contents']) ||
		!wp_verify_nonce( $data['cc_nonce'], 'cc-nonce')
	) {
		$cc_json_result['error'][] = "Permission check failed";
		echo json_encode($cc_json_result);
		exit();
	}


	// Response Images
	$cc_ok_image = '<img class="ok responser result" src="'.admin_url("/images/yes.png").'">';
	$cc_error_image = '<img class="error responser result" src="'.admin_url("/images/no.png").'">';
	$css_done = $js_done = false;
	$cc_json_result = array();



	// Custom Folder Exists?
	if (!file_exists(CC_DIR))
		mkdir(CC_DIR, 0755, true);
	@chmod(CC_DIR, 0755);





	// START CHECKING EACH FILE
	foreach ($data['cc_editor_contents'] as $key => $file) {

		// Obtained data
		$custom_codes_file_lang = $file[0];
		$custom_codes_file_name = $file[1];
		$custom_codes_file_content = stripslashes_deep( $file[2] );
		$cc_admin = substr($custom_codes_file_name, 0, 6) == "admin_" ? true : false;


		// Permission Check
		if ( substr($custom_codes_file_name, 0, 6) == "admin_" && !current_user_can('cc_full_access') ) {
			$cc_json_result['error'][] = "Permission check failed";
			echo json_encode($cc_json_result);
			exit();
		}



		// IF SASS FILE
		if ( $custom_codes_file_lang == 'sass' ) {

			$css_output_done = "";
			$file_scss = CC_DIR."$custom_codes_file_name.scss";
			$file_css_output = CC_DIR."$custom_codes_file_name.css";

			if ( substr($custom_codes_file_name, 0, 6) == "admin_" ) {
				$file_main_css_output = CC_DIR."admin_panel.css";
			} else {
				$file_main_css_output = CC_DIR."custom_public.css";
			}


			// Data to compile
			if ( $custom_codes_file_name == "custom_public" ) {

				$data_to_compile = cc_css_main_file(false);

			} elseif ( $custom_codes_file_name == "admin_panel" ) {

				$data_to_compile = cc_css_main_file(true);

			} else {

				if ( $custom_codes_file_name == "mixins" ) {

					$data_to_compile = cc_css_main_file(false, true);
					$mixin_output = "custom_public";

				} elseif ( $custom_codes_file_name == "admin_mixins" ) {

					$data_to_compile = cc_css_main_file(true, true);
					$mixin_output = "admin_panel";

				} else {

					// ADMIN NORMAL SASS
					if ( substr($custom_codes_file_name, 0, 6) == "admin_" ) {

						if ( file_exists(CC_DIR."admin_mixins.scss") ) {
							$data_to_compile = "@import 'admin_mixins.scss'; \n".$custom_codes_file_content;
						} else {
							$data_to_compile = $custom_codes_file_content;
						}

					// PUBLIC NORMAL SASS
					} else {

						if ( file_exists(CC_DIR."mixins.scss") ) {
							$data_to_compile = "@import 'mixins.scss'; \n".$custom_codes_file_content;
						} else {
							$data_to_compile = $custom_codes_file_content;
						}

					}

				}

			}


			// SASS FILE SAVE
			$scss_content = stripslashes_deep( $custom_codes_file_content ) == "" ? cc_empty_codes('sass', $custom_codes_file_name) : $custom_codes_file_content;

			cc_process_timer_start();
				$scss_done = file_put_contents($file_scss, $scss_content, FILE_TEXT );
			$scss_end = cc_process_timer_finish();

			// SASS FILE ERROR CATCHING
			if ( stripslashes_deep( $custom_codes_file_content ) != "" && isset($scss_done) && $scss_done == 0 ) {
				$cc_json_result['error'][] = $cc_error_image." '$custom_codes_file_name.scss' File Not Saved for $cc_result_level<br>";
			} else {
				$cc_json_result['success']["$custom_codes_file_name.scss"] = $cc_ok_image." '$custom_codes_file_name.scss' File Saved for $cc_result_level: $scss_done ($scss_end)<br>";
			}



			// OUTPUT FILE SAVE
			try {


				// DO NOT OUTPUT SELF SCSS IF MIXINS FILE
				if ( $custom_codes_file_name != "mixins" && $custom_codes_file_name != "admin_mixins" ) {

					cc_process_timer_start();
						$css_output_content = $cc_scss->compile( $data_to_compile );
						$css_output_done = file_put_contents($file_css_output, $css_output_content, FILE_TEXT );
					$css_output_end = cc_process_timer_finish();

				} else {

					cc_process_timer_start();
						$css_output_content = $cc_scss->compile( $data_to_compile );
						$css_output_done = file_put_contents($file_main_css_output, $css_output_content, FILE_TEXT );

						// Increase the version number
						$css_version = cc_pull_option('cc_'.($cc_admin ? 'admin_' : '').'css_save_count', 0);
						update_option( 'cc_'.($cc_admin ? 'admin_' : '').'css_save_count', $css_version+1 );

					$css_output_end = cc_process_timer_finish();

				}


				if ( substr($custom_codes_file_name, 0, 6) == "admin_" ) {
					$cc_json_result['css_output'] = cc_css_main_file(true);
				} else {
					$cc_json_result['css_output'] = cc_css_main_file(false);
				}

			} catch (Exception $e) {
			    $cc_json_result['error'][] = "<b>Error:</b> ".$e->getMessage()."<br>";
				$cc_json_result['css_output'] = "*** Fix this error *** \n".$e->getMessage();
			}

			// OUTPUT ERROR CATCHING
			if ( $custom_codes_file_name == "mixins" || $custom_codes_file_name == "admin_mixins" ) $custom_codes_file_name = $mixin_output;

			if ( stripslashes_deep( $custom_codes_file_content ) != "" && isset($css_output_done) && $css_output_done == 0 ) {
				$cc_json_result['error'][] = $cc_error_image." Output '$custom_codes_file_name.css' Not Saved for $cc_result_level<br>";
			} else {
				$cc_json_result['success']["$custom_codes_file_name.css"] = $cc_ok_image." Output '$custom_codes_file_name.css' Saved for $cc_result_level: $css_output_done ($css_output_end)<br>";
			}



		// IF CSS FILE
		} elseif ( $custom_codes_file_lang == 'css' ) {

			$file_css = CC_DIR."$custom_codes_file_name.css";

			// REMOVE THE OLD SASS FILE
			if(file_exists(CC_DIR."$custom_codes_file_name.scss"))
				@unlink(CC_DIR."$custom_codes_file_name.scss");



			// MAIN CSS FILE SAVE
			if ( $custom_codes_file_name == "custom_public" ) {

				$css_content = $cc_json_result['css_output'] = cc_css_main_file(false);

			} elseif ($custom_codes_file_name == "admin_panel" ) {

				$css_content = $cc_json_result['css_output'] = cc_css_main_file(true);

			} else {
				$css_content = $custom_codes_file_content == "" ? cc_empty_codes('css', $custom_codes_file_name) :  $custom_codes_file_content;
			}

			cc_process_timer_start();
				$css_done = file_put_contents($file_css, $css_content, FILE_TEXT );

				// Increase the version number
				$css_version = cc_pull_option('cc_'.($cc_admin ? 'admin_' : '').'css_save_count', 0);
				update_option( 'cc_'.($cc_admin ? 'admin_' : '').'css_save_count', $css_version+1 );

			$css_end = cc_process_timer_finish();


			// CSS FILE ERROR CATCHING
			if ( $custom_codes_file_content != "" && isset($css_done) && $css_done == 0 ) {
				$cc_json_result['error'][] = $cc_error_image." '$custom_codes_file_name.css' Not Saved for $cc_result_level<br>";
			} else {
				$cc_json_result['success']["$custom_codes_file_name.css"] = $cc_ok_image." '$custom_codes_file_name.css' Saved for $cc_result_level: $css_done ($css_end)<br>";
			}



		// IF OTHER FILE
		} else {

			// SAVE
			$file_name = "$custom_codes_file_name.$custom_codes_file_lang";
			$file_other = CC_DIR.$file_name;

			cc_process_timer_start();
				$other_done = file_put_contents($file_other, $custom_codes_file_content, FILE_TEXT );

				if ( $file_name == "custom_public.js" || $file_name == "admin_panel.js" ) {

					// Increase the version number
					$js_bottom_version = cc_pull_option('cc_'.($cc_admin ? 'admin_' : '').'js_bottom_save_count', 0);
					update_option( 'cc_'.($cc_admin ? 'admin_' : '').'js_bottom_save_count', $js_bottom_version+1 );

				} elseif ( $file_name == "custom_public_head.js" || $file_name == "admin_panel_head.js" ) {

					// Increase the version number
					$js_head_version = cc_pull_option('cc_'.($cc_admin ? 'admin_' : '').'js_head_save_count', 0);
					update_option( 'cc_'.($cc_admin ? 'admin_' : '').'js_head_save_count', $js_head_version+1 );

				}


			$other_end = cc_process_timer_finish();

			// ERROR CATCHING
			if ( $custom_codes_file_content != "" && isset($other_done) && $other_done == 0 ) {
				$cc_json_result['error'][] = $cc_error_image." '$custom_codes_file_name.$custom_codes_file_lang' Not Saved for $cc_result_level<br>";
			} else {
				$cc_json_result['success']["$custom_codes_file_name.$custom_codes_file_lang"] = $cc_ok_image." '$custom_codes_file_name.$custom_codes_file_lang' Saved for $cc_result_level: $other_done ($other_end)<br>";
			}

		}


	}



	echo json_encode($cc_json_result);
	die();
}
add_action('wp_ajax_cc_ajax_action', 'cc_saver_ajax');




// CSS MAIN FILE CREATOR
function cc_css_main_file($cc_admin, $mixin_output = false) {
	global
		$cc_tablet_l,
		$cc_tablet_p,
		$cc_phone_l,
		$cc_phone_p;

$cc_result_level = $cc_admin ? "Admin" : "Public";


if ( $mixin_output ) {

	$desktop  = '@import "'.($cc_admin ? "admin_" : "").'desktop.scss";';
	$tablet_l = '@import "'.($cc_admin ? "admin_" : "").'tablet-l.scss";';
	$tablet_p = '@import "'.($cc_admin ? "admin_" : "").'tablet-p.scss";';
	$mobile_l = '@import "'.($cc_admin ? "admin_" : "").'mobile-l.scss";';
	$mobile_p = '@import "'.($cc_admin ? "admin_" : "").'mobile-p.scss";';
	$retina   = '@import "'.($cc_admin ? "admin_" : "").'retina.scss";';

} else {

	$desktop  = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."desktop.css" ) ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."desktop.css"  ) : "";
	$tablet_l = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."tablet-l.css") ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."tablet-l.css" ) : "";
	$tablet_p = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."tablet-p.css") ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."tablet-p.css" ) : "";
	$mobile_l = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."mobile-l.css") ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."mobile-l.css" ) : "";
	$mobile_p = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."mobile-p.css") ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."mobile-p.css" ) : "";
	$retina   = file_exists(CC_DIR.( $cc_admin ? "admin_" : "" )."retina.css"  ) ? @file_get_contents( CC_DIR.( $cc_admin ? "admin_" : "" )."retina.css"   ) : "";

}


return
($mixin_output ? '@import "'.($cc_admin ? "admin_" : "").'mixins.scss";' : '').
'/* =========================
	'.strtoupper($cc_result_level).' DESKTOP CSS
========================= */

'.$desktop.'

/* =========================
	'.strtoupper($cc_result_level).' DESKTOP CSS END
========================= */
/* =========================
	'.strtoupper($cc_result_level).' RESPONSIVE CSS
========================= */

/* TABLET LANDSCAPE */
@media (max-width: '.$cc_tablet_l.'px) {

'.$tablet_l.'

}

/* TABLET PORTRAIT */
@media (max-width: '.$cc_tablet_p.'px) {

'.$tablet_p.'

}

/* MOBILE LANDSCAPE */
@media (max-width: '.$cc_phone_l.'px) {

'.$mobile_l.'

}

/* MOBILE PORTRAIT */
@media (max-width: '.$cc_phone_p.'px) {

'.$mobile_p.'

}

/* RETINA FIXES */
@media only screen and (-webkit-min-device-pixel-ratio: 1.5),
 	   only screen and (-o-min-device-pixel-ratio: 3/2),
 	   only screen and (min--moz-device-pixel-ratio: 1.5),
       only screen and (min-device-pixel-ratio: 1.5) {

'.$retina.'

}
/* =========================
	'.strtoupper($cc_result_level).' RESPONSIVE CSS END
========================= */';

}


?>