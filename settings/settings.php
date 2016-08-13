<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// Public/Admin Side?
$custom_codes_public = $cc_admin = false;
if ( isset($_GET['admin_panel']) && $_GET['admin_panel'] == true ) {
	$cc_admin = true;
} else {
	$custom_codes_public = true;
}
$cc_result_level = $custom_codes_public ? "Public" : "Admin";



// REGISTER SETTINGS
function cc_register_custom_codes_settings() {

	register_setting( 'cc_admin_settings' , 'cc_admin_roles' );

	register_setting( 'cc_notes_settings' , 'cc_admin_notes' );

	register_setting( 'cc_general_settings' , 'cc_style_mode' );
	register_setting( 'cc_general_settings' , 'cc_store_files' );

	register_setting( 'cc_responsivity_settings' , 'cc_tablet_l' );
	register_setting( 'cc_responsivity_settings' , 'cc_tablet_p' );
	register_setting( 'cc_responsivity_settings' , 'cc_phone_l' );
	register_setting( 'cc_responsivity_settings' , 'cc_phone_p' );

	register_setting( 'cc_editor_settings' , 'cc_editor_theme' );

}
add_action( 'admin_init', 'cc_register_custom_codes_settings' );




// Calls
$cc_style_mode = cc_pull_option( 'cc_style_mode', 'sass' );
$cc_sass = $cc_style_mode == "sass" ? true : false;


if (
	( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
	( isset($_GET['page']) && $_GET['page'] == "custom-codes" )
) {

	$cc_admin_roles = cc_pull_option( 'cc_admin_roles', array() );
	$cc_admin_notes = cc_pull_option( 'cc_admin_notes', '' );
	$cc_store_custom_files = cc_pull_option( 'cc_store_files', 'true' ) == 'true' ? true : false;
	$cc_tablet_l = cc_pull_option( 'cc_tablet_l', '1199' );
	$cc_tablet_p = cc_pull_option( 'cc_tablet_p', '991' );
	$cc_phone_l = cc_pull_option( 'cc_phone_l', '767' );
	$cc_phone_p = cc_pull_option( 'cc_phone_p', '479' );
	$cc_editor_theme = cc_pull_option( 'cc_editor_theme', 'dark' );

}


if ( isset($_GET['page']) && $_GET['page'] == "custom-codes" ) {


	// SETTINGS TAB
	function cc_settings() {
		global $cc_admin;

	    $screen = get_current_screen();

		if ($cc_admin) {
		    $screen -> add_help_tab( array(
		        'id'      => 'custom-codes-admin-permissions', // This should be unique for the screen.
		        'title'   => 'Admin Side Code Permissions',
		        'callback' => 'cc_admin_permissions_tab'
		    ) );
	    }

	    $screen -> add_help_tab( array(
	        'id'      => 'custom-codes-admin-notes', // This should be unique for the screen.
	        'title'   => 'Admin Notes',
	        'callback' => 'cc_admin_notes_tab'
	    ) );

	    $screen->add_help_tab( array(
	        'id'      => 'custom-codes-editor-settings', // This should be unique for the screen.
	        'title'   => 'Editor Settings',
	        'callback' => 'cc_editor_settings_tab'
	        // Use 'callback' instead of 'content' for a function callback that renders the tab content.
	    ) );

	    $screen->add_help_tab( array(
	        'id'      => 'custom-codes-general-settings', // This should be unique for the screen.
	        'title'   => 'General Settings',
	        'callback' => 'cc_general_settings_tab'
	    ) );

	    $screen -> add_help_tab( array(
	        'id'      => 'custom-codes-responsivity-settings', // This should be unique for the screen.
	        'title'   => 'Responsivity Settings',
	        'callback' => 'cc_responsivity_settings_tab'
	    ) );

	}


	// EDITOR SETTINGS
	function cc_editor_settings_tab() {
		global $cc_editor_theme;
		?>

		<form method="post" action="options.php" id="custom-codes-editor-settings-form" enctype="multipart/form-data">
			<?php settings_fields( 'cc_editor_settings' ); ?>
			<?php do_settings_sections( 'cc_editor_settings' ); ?>

			<p>
				<b>Editor Theme:</b><br>
				<label><input class="es-inputs" type="radio" name="cc_editor_theme" value="dark" <?=$cc_editor_theme == "dark" ? "checked" : ""?>> Dark</label>
				<label><input class="es-inputs" type="radio" name="cc_editor_theme" value="light" <?=$cc_editor_theme == "light" ? "checked" : ""?>> Light</label>
			</p>


			<input id="custom-codes-editor-settings-saver" value="Save" type="submit" class="button-primary">
		</form>

		<script>
			jQuery(document).ready(function($){

				// Settings Link
				$('#contextual-help-link').text('Notes / Settings');

			});
		</script>

		<?php
	}


	// GENERAL SETTINGS
	function cc_general_settings_tab() {
		global $cc_style_mode, $cc_store_custom_files;
		?>

		<form method="post" action="options.php" id="custom-codes-general-settings-form" enctype="multipart/form-data">
			<?php settings_fields( 'cc_general_settings' ); ?>
			<?php do_settings_sections( 'cc_general_settings' ); ?>

			<p>
				<b>Style Mode:</b><br>
				<label><input class="gs-inputs" type="radio" name="cc_style_mode" value="sass" <?=$cc_style_mode == "sass" ? "checked" : ""?>> SASS (Recommended, but slower)</label>
				<label><input class="gs-inputs" type="radio" name="cc_style_mode" value="css" <?=$cc_style_mode == "css" ? "checked" : ""?>> CSS (Faster, but less feature)</label>
			</p>

			<p>
				<b>Store custom CSS/JS after uninstall:</b><br>
				<label><input class="gs-inputs" type="checkbox" name="cc_store_files" value="true" <?=$cc_store_custom_files ? "checked" : ""?>> Yes, please</label>
			</p><br>




			<input id="custom-codes-general-settings-saver" value="Save" type="submit" class="button-primary">
		</form>

		<script>
			jQuery(document).ready(function($){

				var button_gs = $('#custom-codes-general-settings-saver');

				$('.gs-inputs').on('change', function() {

					if ( !button_gs.prop('disabled') )
						button_gs.val('Save');

				});

				$('#custom-codes-general-settings-form').submit(function() {
					var form = $(this);
					var data =  form.serialize();

					button_gs.prop("disabled", true).val('Saving...');

		            $.post( 'options.php', data ).error(function() {
		                alert('An error occured. Please try again.');
		            }).success( function() {

		                if ( $('input[name="cc_style_mode"]:checked').val() != "<?=$cc_style_mode?>" ) {
		                	button_gs.val('Refresh the page to apply settings');
		                } else {
							button_gs.prop("disabled", false).val('Saved!');
		                }

		            });

		            return false;

				});

			});
		</script>

		<?php
	}


	// RESPONSIVITY SETTINGS
	function cc_responsivity_settings_tab() {
		global
			$cc_tablet_l,
			$cc_tablet_p,
			$cc_phone_l,
			$cc_phone_p;
		?>

		<form method="post" action="options.php" id="custom-codes-responsivity-settings-form" enctype="multipart/form-data">
			<?php settings_fields( 'cc_responsivity_settings' ); ?>
			<?php do_settings_sections( 'cc_responsivity_settings' ); ?>

			<p>Making change is not recommended here. These are the best breakpoints.</p>

			<p>
				<b>Tablet Landscape Max Width:</b><br>
				<input class="rs-inputs" type="number" name="cc_tablet_l" value="<?=$cc_tablet_l?>" style="width: 60px;">px (<b>Default</b> @media (max-width: 1199px) {} )
			</p>

			<p>
				<b>Tablet Portrait Max Width:</b><br>
				<input class="rs-inputs" type="number" name="cc_tablet_p" value="<?=$cc_tablet_p?>" style="width: 60px;">px (<b>Default</b> @media (max-width: 991px) {} )
			</p>

			<p>
				<b>Phone Landscape Max Width:</b><br>
				<input class="rs-inputs" type="number" name="cc_phone_l" value="<?=$cc_phone_l?>" style="width: 60px;">px (<b>Default</b> @media (max-width: 767px) {} )
			</p>

			<p>
				<b>Phone Portrait Max Width:</b><br>
				<input class="rs-inputs" type="number" name="cc_phone_p" value="<?=$cc_phone_p?>" style="width: 60px;">px (<b>Default</b> @media (max-width: 479px) {} )
			</p>




			<input id="custom-codes-responsivity-settings-saver" value="Save" type="submit" class="button-primary">
		</form>

		<script>
			jQuery(document).ready(function($){

				var button_rs = $('#custom-codes-responsivity-settings-saver');

				$('.rs-inputs').on('change', function() {

					if ( !button_rs.prop('disabled') )
						button_rs.val('Save');

				});

				$('#custom-codes-responsivity-settings-form').submit(function() {
					var form = $(this);
					var data =  form.serialize();

					button_rs.prop("disabled", true).val('Saving...');

		            $.post( 'options.php', data ).error(function() {
		                alert('An error occured. Please try again.');
		            }).success( function() {

						button_rs.prop("disabled", false).val('Saved!');

		            });

		            return false;

				});

			});
		</script>

		<?php
	}



	// ADMIN NOTES
	function cc_admin_notes_tab() {
		global $cc_admin_notes;
		?>

		<p>Take your notes here:</p>

		<form method="post" action="options.php" id="custom-codes-admin-notes-form" enctype="multipart/form-data">
			<?php settings_fields( 'cc_notes_settings' ); ?>
			<?php do_settings_sections( 'cc_notes_settings' ); ?>

			<textarea id="custom-codes-admin-notes" name="cc_admin_notes[<?=get_current_user_id()?>]" rows="10" placeholder="Write something you shouldn't forget..."><?=is_array($cc_admin_notes) ? $cc_admin_notes[get_current_user_id()] : $cc_admin_notes?></textarea>
			<input id="custom-codes-admin-notes-saver" value="Save" type="submit" class="button-primary">
		</form>

		<script>
			jQuery(document).ready(function($){

				var button_nt = $('#custom-codes-admin-notes-saver');

				$('#custom-codes-admin-notes').on('input', function() {

					if ( !button_nt.prop('disabled') )
						button_nt.val('Save');

				});

				$('#custom-codes-admin-notes-form').submit(function() {
					var form = $(this);
					var notes = $('#custom-codes-admin-notes');
					var data =  form.serialize();

					notes.prop("disabled", true);
					button_nt.prop("disabled", true).val('Saving...');

		            $.post( 'options.php', data ).error(function() {
		                alert('An error occured. Please try again.');
		            }).success( function() {
		                notes.prop("disabled", false);
		                button_nt.prop("disabled", false).val('Saved!');
		            });

		            return false;

				});

			});
		</script>

		<?php
	}



	// ADMIN NOTES
	function cc_admin_permissions_tab() {
		global $wp_roles, $cc_admin_roles;
		?>

		<p>Select roles to activate the admin side CSS and JS codes:</p>

		<form method="post" action="options.php" id="custom-codes-admin-permissions-form" enctype="multipart/form-data">
			<?php settings_fields( 'cc_admin_settings' ); ?>
			<?php do_settings_sections( 'cc_admin_roles' ); ?>

			<?php


	$cc_roles_list = array();
	$cc_current_user_roles = array();
	$cc_selected_roles = array();
	foreach ( $wp_roles->roles as $role => $role_details ) {

		// Extract the current roles
		if ( current_user_can($role) ) {
			$cc_current_user_roles[] = $role; // Record current roles
			continue;
		}

		// Already recorded?
		$selected = in_array($role, $cc_admin_roles) ? "selected" : "";
		if ( in_array($role, $cc_admin_roles) ) {
			$cc_selected_roles[$role] = array(
				'name' => $role_details['name']
			);
		}

		$cc_roles_list[$role] = array(
			'name' => $role_details['name'],
			'selected' => $selected
		);

	}





	// SHOW THE ROLE SELECTOR
	echo '<select id="cc_admin_roles" name="cc_admin_roles[]" size="'.count($cc_roles_list).'" multiple>';
		foreach ( $cc_roles_list as $role => $role_details ) {

			$role_name = $role_details['name'];

			echo '<option value="'.$role.'" '.$role_details['selected'].'>'.$role_name.'</option>';

		}
	echo '</select><br/>';


			?>

			<input id="custom-codes-admin-permissions-saver" value="Save" type="submit" class="button-primary">
		</form>

		<script>
			jQuery(document).ready(function($){

				var button_nt = $('#custom-codes-admin-permissions-saver');

				$('#custom-codes-admin-permissions-form').submit(function() {
					var form = $(this);
					var roles = $('#cc_admin_roles');
					var data =  form.serialize();

					roles.prop("disabled", true);
					button_nt.prop("disabled", true).val('Saving...');

		            $.post( 'options.php', data ).error(function() {
		                alert('An error occured. Please try again.');
		            }).success( function() {
		                roles.prop("disabled", false);
		                button_nt.prop("disabled", false).val('Saved!');
		            });

		            return false;

				});

			});
		</script>

		<?php
	}





}


?>