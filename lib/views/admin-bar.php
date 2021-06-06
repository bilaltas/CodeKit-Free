<?php
/**
 *
 * WP Top Admin Bar view.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 *
 * Add menu items.
 *
 * @param object $wp_admin_bar Returns the WordPress Admin Bar object.
 */
function codes_wp_adminbar( $wp_admin_bar ) {
	global $codes_posts;

	if (
		! current_user_can( 'administrator' ) // Only show for admins.
		|| ! boolval( get_option( '_codes_admin_bar' ) ) // If hidden from the settings.
	) {
		return;
	}

	// Create the menu.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'codes',
			'title' => '
				<span class="ab-icon custom-codes"><img src="' . CODES_PLUGIN_URL . 'assets/image/icon-codes-light.svg"></span><span class="ab-label">' . __( 'Codes', 'custom-codes' ) . '</span>
				<style> #wp-admin-bar-codes .ab-icon.custom-codes{ opacity: 0.6; } #wp-admin-bar-codes:hover .ab-icon.custom-codes{ opacity: 1; } </style>
			',
			'href'  => admin_url( 'edit.php?post_type=custom-code' ),
		)
	);

	foreach ( $codes_posts as $key => $code_post ) {

		if ( $key > 8 ) {
			break;
		}

		$wp_admin_bar->add_node(
			array(
				'id'     => 'codes-' . $code_post->ID,
				'title'  => empty( $code_post->post_title ) ? __( 'Untitled Code', 'custom-codes' ) : $code_post->post_title,
				'href'   => admin_url( "post.php?post=$code_post->ID&action=edit" ),
				'parent' => 'codes',
			)
		);

	}

	$wp_admin_bar->add_node(
		array(
			'id'     => 'all_codes',
			'title'  => __( 'All Codes', 'custom-codes' ),
			'href'   => admin_url( 'edit.php?post_type=custom-code' ),
			'parent' => 'codes',
		)
	);

	$wp_admin_bar->add_node(
		array(
			'id'     => 'new_code',
			'title'  => __( '+ New Code', 'custom-codes' ),
			'href'   => admin_url( 'post-new.php?post_type=custom-code' ),
			'parent' => 'codes',
		)
	);

}
add_action( 'admin_bar_menu', 'codes_wp_adminbar', 9999 );
