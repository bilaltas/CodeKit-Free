<?php
/**
 *
 * Admin posts list columns
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Admin Columns.
 *
 * @param array $columns Returns current admin columns.
 */
function codes_admin_columns( $columns ) {

	$new_column = $columns;
	array_splice( $new_column, 2 );

	$new_column['language']   = __( 'Language', 'custom-codes' );
	$new_column['location']   = __( 'Location', 'custom-codes' );
	$new_column['menu_order'] = __( 'Order', 'custom-codes' );
	$new_column['author']     = __( 'Author', 'custom-codes' );

	return array_merge( $new_column, $columns );

}
add_filter( 'manage_custom-code_posts_columns', 'codes_admin_columns' );




/**
 * Column contents.
 *
 * @param string $column Returns the current column name.
 * @param int    $post_ID Returns the current post ID.
 */
function codes_column_content( $column, $post_ID ) {
	global $wp_filesystem;

	if ( 'language' === $column ) {

		$language = strtoupper( get_post_meta( $post_ID, '_codes_language', true ) );
		if ( empty( $language ) ) {
			$language = __( 'Not selected', 'custom-codes' );
		}
		echo $language;

	} elseif ( 'location' === $column ) {

		$location = get_post_meta( $post_ID, '_codes_location', true );

		echo '<div class="codes-location-column">';

		$location_text = __( 'Other', 'custom-codes' );

		// Prepend Icons.
		if ( 'frontend' === $location ) {

			$location_text = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-welcome-widgets-menus.svg' ) . ' ' . __( 'Frontend', 'custom-codes' );

		} elseif ( 'backend' === $location ) {

			$location_text = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-wordpress.svg' ) . ' ' . __( 'Backend', 'custom-codes' );

		} elseif ( 'login' === $location ) {

			$location_text = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-admin-network.svg' ) . ' ' . __( 'Login', 'custom-codes' );

		} elseif ( 'everywhere' === $location ) {

			$location_text = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-globe.svg' ) . ' ' . __( 'Everywhere', 'custom-codes' );

		}

		echo $location_text;

		// Append admin roles.
		if ( 'backend' === $location ) {
			global $wp_roles;

			$roles_text  = __( 'All roles', 'custom-codes' );
			$admin_roles = get_post_meta( $post_ID, '_codes_adminroles', true );
			if ( count( $admin_roles ) > 0 ) {

				foreach ( $admin_roles as $key => $role ) {
					$admin_roles[ $key ] = translate_user_role( $wp_roles->roles[ $role ]['name'] );
				}
				$roles_text = implode( ', ', $admin_roles );

			}

			echo " ($roles_text)";

		}

		echo '</div>';

	} elseif ( 'menu_order' === $column ) {

		echo get_post( $post_ID )->menu_order;

	}

}
add_action( 'manage_custom-code_posts_custom_column', 'codes_column_content', 10, 2 );




/**
 * Sortable order column.
 */
function codes_sortable_columns() {
	return array(
		'title'      => 'title',
		'menu_order' => 'menu_order',
		'date'       => 'date',
	);
}
add_filter( 'manage_edit-custom-code_sortable_columns', 'codes_sortable_columns' );

/**
 * Sortable menu order column.
 */
function codes_edit_columns_load() {

	add_filter(
		'request',
		function( $vars ) {

			if ( isset( $vars['post_type'] ) && 'custom-code' === $vars['post_type'] && isset( $vars['orderby'] ) ) {
				if ( 'menu_order' === $vars['orderby'] ) {
					$vars = array_merge( $vars, array( 'orderby' => 'menu_order' ) );
				}
			}

			return $vars;

		}
	);

}
add_action( 'load-edit.php', 'codes_edit_columns_load' );




/**
 * SVG alignment.
 */
function codes_admin_columns_svg_alignment() {
	?>
	<style> .codes-location-column > svg { vertical-align: middle; } </style>
	<?php
}
add_action( 'admin_head', 'codes_admin_columns_svg_alignment' );
