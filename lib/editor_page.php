<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


require_once( dirname( CC_FILE ).'/lib/editor_functions.php' );


function cc_editor_page() {
	global $cc_sass, $cc_scss, $cc_admin, $cc_editor_theme;
?>

<!-- SAVING OVERLAY -->
<div id="cc-saving-overlay"><span>SAVING...</span></div>


<!-- PAGE WRAP -->
<div class="wrap fixedthis">

	<?php

		if ( current_user_can('cc_full_access') ) {

			// ADMIN PANEL SWITCHER LINK
			if ( $cc_admin ) {
				echo '<a href="'.admin_url('admin.php?page=custom-codes').'" class="switch-link">Switch to Public Custom CSS and JS Codes</a>';
			} else {
				echo '<a href="'.admin_url('admin.php?page=custom-codes&admin_panel=true').'" class="switch-link">Switch to Admin Panel Custom CSS and JS Codes</a>';
			}

		}

	?>


<!-- THE FORM -->
<form class="cc-custom-codesform" name="cc-form" action="" method="post" id="cc-custom-codes-form" enctype="multipart/form-data">




	<!-- Success Messages (Non-Ajax) -->
	<?php if ( isset( $_GET['settings-updated'] ) ) { ?>
		<h2></h2><div id="message" class="updated custom-codes" style=""><p><?php _e( 'All custom codes updated successfully.', 'custom-codes' ); ?></p></div>
	<?php } ?>




<!-- SASS/CSS SIDE -->
<div class="css-side sides theme-<?=$cc_editor_theme?>">




	<!-- SASS/CSS Side Title -->
	<h2>
		<span class="dynamic-title">
		<?php
			$cc_css_title = $cc_sass ? "Custom SASS" : "Custom CSS";
			$cc_css_admin_title = $cc_sass ? "Custom Admin SASS" : "Custom Admin CSS";
			$cc_current_css_title = $cc_admin ? $cc_css_admin_title : $cc_css_title;

			 _e( $cc_current_css_title, 'custom-codes' );
		?>
		</span>
		<span class="hider">(<a href="#" class="hider-css">Hide this</a><span class="show-both"> | <a href="#"> Show Both</a></span>)</span>
	</h2>




	<!-- TOP RESPONSIVITY TABS -->
	<div class="custom-tabs top-tabs css-tabs">
		<a href="#" data-select-file="desktop" class="active"><i class="fa fa-desktop"></i> Desktop</a>
		<a href="#" data-select-file="tablet-l"><i class="fa fa-tablet rotate"></i> Tablet</a>
		<a href="#" data-select-file="tablet-p"><i class="fa fa-tablet"></i> Tablet</a>
		<a href="#" data-select-file="mobile-l"><i class="fa fa-mobile rotate"></i> Mobile</a>
		<a href="#" data-select-file="mobile-p"><i class="fa fa-mobile"></i> Mobile</a>
		<a href="#" data-select-file="retina"><i class="fa fa-laptop"></i> Retina</a>
	</div>
	<!-- TOP RESPONSIVITY TABS END -->


	<?php

		// Responsivity Editors
		cc_add_custom_editor($cc_sass ? "sass" : "css", "desktop" , "mousetrap");
		cc_add_custom_editor($cc_sass ? "sass" : "css", "tablet-l", "mousetrap");
		cc_add_custom_editor($cc_sass ? "sass" : "css", "tablet-p", "mousetrap");
		cc_add_custom_editor($cc_sass ? "sass" : "css", "mobile-l", "mousetrap");
		cc_add_custom_editor($cc_sass ? "sass" : "css", "mobile-p", "mousetrap");
		cc_add_custom_editor($cc_sass ? "sass" : "css", "retina"  , "mousetrap");

		// functions.php Editor
		if ( $cc_admin && current_user_can('cc_full_access') ) cc_add_custom_editor("php", "functions", "functions-php");

		// Mixins
		if ($cc_sass) cc_add_custom_editor("sass", "mixins", "mousetrap");

		// SASS - CSS Output
		cc_add_custom_editor("css", $cc_admin ? "panel" : "custom_public", "css-output read-only", false);

	?>


	<!-- BOTTOM TABS -->
	<div class="custom-tabs bottom-tabs css-tabs">

		<?php if ( $cc_admin && current_user_can('cc_full_access') ) { ?>
		<a href="#" class="functions-php" data-select-file="functions"><i class="fa fa-code"></i> Theme Functions</a>
		<?php } ?>

		<?php if ($cc_sass) { ?>
		<a href="#" class="mixins" data-select-file="mixins"><i class="fa fa-codepen"></i> Mixins</a>
		<?php } ?>
		<a href="#" class="css-output-tab" data-select-file="<?=$cc_admin ? "panel" : "custom_public"?>"><i class="fa fa-css3"></i> CSS Output</a>

	</div>
	<!-- BOTTOM TABS END -->


</div> <!-- SASS/CSS SIDE END -->



<!-- JS SIDE -->
<div class="js-side sides theme-<?=$cc_editor_theme?>">


	<!-- JS Side Title -->
	<h2>
		<span class="dynamic-title"><?php _e( $cc_admin ? 'Custom Admin JS' : 'Custom JS', 'custom-codes' ); ?></span>
	    <span class="hider">(<a href="#" class="hider-js">Hide this</a><span class="show-both"> | <a href="#"> Show Both</a></span>)</span>
	</h2>


	<!-- TOP JS TABS -->
	<div class="custom-tabs top-tabs js-tabs">
		<a href="#" data-select-file="<?= $cc_admin ? "panel_head" : "custom_public_head" ?>">Head</a>
		<a href="#" class="active" data-select-file="<?= $cc_admin ? "panel" : "custom_public" ?>">Bottom</a>
	</div>
	<!-- TOP JS TABS END -->


	<?php

		// JS Editors
		cc_add_custom_editor("js", $cc_admin ? "panel_head" : "custom_public_head", "mousetrap");
		cc_add_custom_editor("js", $cc_admin ? "panel" : "custom_public", "mousetrap");

	?>


</div> <!-- JS SIDE END -->
<div style="clear: both;"></div>




	<!-- SAVE SECTION -->
	<div class="saver-container">


		<!-- ADDITIONAL DATA -->
		<input type="hidden" name="action" value="cc_save" />
		<input type="hidden" name="custom_type" id="custom_type" value="<?= $cc_admin ? "admin" : "public" ?>" />


		<!-- INFORMATION -->
		<span class="info-tooltip">?
			<span>
				<ul class="custom-codes-info">
					<li><b>Save:</b> Command/Ctrl + S</li>
					<li><b>Find:</b> Command/Ctrl + F</li>
					<li><b>Mark a Line:</b> Click a line number</li>
					<li><b>Choose Multi Line:</b> Option/Alt + Click</li>
					<li><b>Add Multi Cursor:</b> Command/Ctrl + Click</li>
					<li><b>Comment the Line:</b> Command/Ctrl + 7</li>
					<li><b>Full Screen Mode:</b> Command/Ctrl + E</li>
					<li><b>Exit Full Screen Mode:</b> Esc</li>
					<li><b>Arrange Space Hierarchy:</b> (Select codes, then) Shift + Tab</li>
					<li><b>Emmet Abbreviations:</b> Write Abs. + Tab</li>
				</ul>
			</span>
		</span>


		<!-- SUBMIT BUTTON -->
		<input type="submit" name="Submit" class="button-primary" id="cc_savethem" value="Save Changes">


		<!-- RESULT SCREEN -->
		<span class="error info-tooltip">
			<img class="responser" src="<?= admin_url('/images/wpspin_light.gif') ?>" srcset="<?= admin_url('/images/wpspin_light.gif') ?> 1x, <?= admin_url('/images/wpspin_light-2x.gif') ?> 2x" id="loading">
			<img class="responser" src="<?= admin_url('/images/yes.png') ?>" id="loaded">
			<img class="responser" src="<?= admin_url('/images/no.png') ?>" id="load-failed">
			<span class="error-content"></span>
		</span>


	</div>
	<!-- SAVE SECTION END -->






</form> <!-- THE FORM END -->
</div> <!-- PAGE WRAP END -->

<?php
} // END cc_editor_page
?>