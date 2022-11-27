<?php
/**
 *
 * Codes post type actions.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Register post type.
 */
function codes_register_post_type() {

	$labels = array(
		'name'          => __( 'Codes', 'custom-codes' ),
		'singular_name' => __( 'Code', 'custom-codes' ),
		'all_items'     => __( 'Custom Codes', 'custom-codes' ),
		'edit_item'     => __( 'Edit Code', 'custom-codes' ),
		'add_new_item'  => __( 'Add New Code', 'custom-codes' ),
		'not_found'     => __( 'No code added yet.', 'custom-codes' ),
		'search_items'  => __( 'Search Codes', 'custom-codes' ),
	);

	$args = array(
		'label'               => __( 'Custom Codes', 'custom-codes' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'has_archive'         => false,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'delete_with_user'    => false,
		'exclude_from_search' => true,
		'capability_type'     => 'page',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => false,
		'query_var'           => false,
		'menu_icon'           => CODES_PLUGIN_URL . 'assets/image/icon-codes-light.svg',
		'supports'            => array( 'title', 'page-attributes' ),
	);

	register_post_type( 'custom-code', $args );
}
add_action( 'init', 'codes_register_post_type' );




/**
 * Disable block editor.
 *
 * @param bool   $current_status Returns current block editor status.
 * @param string $post_type Returns the post type.
 */
function codes_disable_gutenberg( $current_status, $post_type ) {

	if ( 'custom-code' === $post_type ) {
		return false;
	}

	return $current_status;

}
add_filter( 'use_block_editor_for_post_type', 'codes_disable_gutenberg', 10, 2 );




/**
 * Delete the codes when post removed.
 *
 * @param int $post_ID Returns the deleted post ID.
 */
function codes_delete_codes( $post_ID ) {
	global $post_type;

	if ( 'custom-code' !== $post_type ) {
		return;
	}

	// Find the post editors and delete.
	foreach ( glob( CODES_FOLDER_DIR . "$post_ID-*.*" ) as $file ) {
		unlink( $file );
	}

}
add_action( 'before_delete_post', 'codes_delete_codes' );




/**
 * Show premium status on admin body classes.
 *
 * @param int $classes Current classes list.
 */
function codes_body_classes( $classes ) {
	global $post_type;

	if ( 'custom-code' !== $post_type ) {
		return $classes;
	}

	$classes .= codes_fs()->is_premium() ? 'codes-pro' : 'codes-free';
	return $classes;
}
add_filter( 'admin_body_class', 'codes_body_classes' );
