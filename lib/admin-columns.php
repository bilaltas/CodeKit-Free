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
function codes_admin_columns( $columns )
{
    $new_column = $columns;
    array_splice( $new_column, 2 );
    $new_column['language'] = __( 'Language / Purpose', 'custom-codes' );
    $new_column['location'] = __( 'Location', 'custom-codes' );
    $new_column['menu_order'] = __( 'Release Order', 'custom-codes' );
    $new_column['author'] = __( 'Author', 'custom-codes' );
    return array_merge( $new_column, $columns );
}

add_filter( 'manage_custom-code_posts_columns', 'codes_admin_columns' );
/**
 * Column contents.
 *
 * @param string $column Returns the current column name.
 * @param int    $post_ID Returns the current post ID.
 */
function codes_column_content( $column, $post_ID )
{
    global  $wp_filesystem, $codes_langs ;
    
    if ( 'language' === $column ) {
        $language_text = __( 'Not selected', 'custom-codes' );
        $language = get_post_meta( $post_ID, '_codes_language', true );
        
        if ( !empty($language) ) {
            $language_key = array_search( $language, array_column( $codes_langs, 'id' ), true );
            $language_data = $codes_langs[$language_key];
            $language_text = ( 'Custom Functions' === $language_data->name ? 'PHP' : $language_data->name );
        }
        
        echo  esc_html( $language_text ) ;
        // Append the description.
        $post_excerpt = get_post( $post_ID )->post_excerpt;
        echo  '<br><small class="description" style="' . (( empty($post_excerpt) ? 'opacity: 0.6; font-style: italic;' : '' )) . '">' ;
        
        if ( empty($post_excerpt) ) {
            esc_html_e( 'No description', 'custom-codes' );
        } else {
            echo  esc_html( $post_excerpt ) ;
        }
        
        echo  '</small>' ;
    } elseif ( 'location' === $column ) {
        $location = get_post_meta( $post_ID, '_codes_location', true );
        echo  '<div class="codes-location-column">' ;
        $location_icon = '';
        $location_text = __( 'Other', 'custom-codes' );
        // Prepend Icons.
        
        if ( 'frontend' === $location ) {
            $location_icon = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-welcome-widgets-menus.svg' );
            $location_text = __( 'Frontend', 'custom-codes' );
        } elseif ( 'backend' === $location ) {
            global  $wp_roles ;
            $location_icon = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-wordpress.svg' );
            $location_text = __( 'Backend', 'custom-codes' );
            // Append admin roles.
            $roles_text = __( 'All roles', 'custom-codes' );
            $admin_roles = get_post_meta( $post_ID, '_codes_adminroles', true );
            
            if ( count( $admin_roles ) > 0 ) {
                foreach ( $admin_roles as $key => $role ) {
                    if ( isset( $wp_roles->roles[$role] ) ) {
                        $admin_roles[$key] = translate_user_role( $wp_roles->roles[$role]['name'] );
                    }
                }
                $roles_text = implode( ', ', $admin_roles );
            }
            
            $location_text .= ' (' . esc_html( $roles_text ) . ')';
        } elseif ( 'login' === $location ) {
            $location_icon = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-admin-network.svg' );
            $location_text = __( 'Login', 'custom-codes' );
        } elseif ( 'everywhere' === $location ) {
            $location_icon = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-globe.svg' );
            $location_text = __( 'Everywhere', 'custom-codes' );
        } elseif ( 'nowhere' === $location ) {
            $location_icon = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-hidden.svg' );
            $location_text = __( 'Nowhere', 'custom-codes' );
        }
        
        echo  wp_kses( $location_icon, codes_svg_args() ) . ' ' . esc_html( $location_text ) ;
        echo  '</div>' ;
    } elseif ( 'menu_order' === $column ) {
        echo  intval( get_post( $post_ID )->menu_order ) ;
    }

}

add_action(
    'manage_custom-code_posts_custom_column',
    'codes_column_content',
    10,
    2
);
/**
 * Sortable order column.
 */
function codes_sortable_columns()
{
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
function codes_edit_columns_load()
{
    add_filter( 'request', function ( $vars ) {
        if ( isset( $vars['post_type'] ) && 'custom-code' === $vars['post_type'] && isset( $vars['orderby'] ) ) {
            if ( 'menu_order' === $vars['orderby'] ) {
                $vars = array_merge( $vars, array(
                    'orderby' => 'menu_order',
                ) );
            }
        }
        return $vars;
    } );
}

add_action( 'load-edit.php', 'codes_edit_columns_load' );
/**
 * SVG alignment.
 */
function codes_admin_columns_svg_alignment()
{
    ?>
	<style> .codes-location-column > svg { vertical-align: middle; } </style>
	<?php 
}

add_action( 'admin_head', 'codes_admin_columns_svg_alignment' );
/**
 * Duplicate Quick Action Link.
 *
 * @param array  $actions Actions data.
 * @param object $post Returns the global post object.
 */
function codes_duplicate_code_link( $actions, $post )
{
    if ( 'custom-code' !== $post->post_type || !current_user_can( 'administrator' ) ) {
        return $actions;
    }
    $url = wp_nonce_url( add_query_arg( array(
        'action' => 'codes_duplicate_as_draft',
        'post'   => $post->ID,
    ), 'admin.php' ), 'duplicate-post_' . $post->ID );
    $actions['duplicate'] = '<a href="' . $url . '" title="Duplicate this code" rel="permalink">Duplicate</a>';
    return $actions;
}

add_filter(
    'post_row_actions',
    'codes_duplicate_code_link',
    10,
    2
);
/**
 * Create post duplicate as a draft and redirect to the edit post screen
 */
function codes_duplicate_as_draft()
{
    // Post ID check.
    if ( !isset( $_GET['post'] ) || !is_numeric( $_GET['post'] ) ) {
        wp_die( 'No code to duplicate has been provided!' );
    }
    $post_ID = absint( $_GET['post'] );
    // Nonce check.
    if ( !isset( $_GET['_wpnonce'] ) ) {
        wp_die( 'No nonce provided!' );
    }
    // Nonce verification.
    $nonce = sanitize_key( $_GET['_wpnonce'] );
    if ( !wp_verify_nonce( $nonce, 'duplicate-post_' . $post_ID ) ) {
        wp_die( 'Nonce not verified!' );
    }
    // Post check.
    $post = get_post( $post_ID );
    if ( !$post ) {
        wp_die( 'Code not found!' );
    }
    // Insert the new post.
    $new_post_id = wp_insert_post( array(
        'post_status'  => 'draft',
        'post_title'   => sprintf(
        /* translators: 1: Code Title */
        __( 'Copy of %1$s', 'custom-codes' ),
        $post->post_title
    ),
        'post_name'    => $post->post_name,
        'post_excerpt' => $post->post_excerpt,
        'post_author'  => wp_get_current_user()->ID,
        'post_type'    => $post->post_type,
        'menu_order'   => $post->menu_order,
    ) );
    if ( !$new_post_id ) {
        wp_die( 'Draft could not be created!' );
    }
    // Apply the code categories.
    $taxonomies = get_object_taxonomies( get_post_type( $post ) );
    if ( $taxonomies ) {
        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = wp_get_object_terms( $post_ID, $taxonomy, array(
                'fields' => 'slugs',
            ) );
            wp_set_object_terms(
                $new_post_id,
                $post_terms,
                $taxonomy,
                false
            );
        }
    }
    // Apply all the meta info.
    $post_meta_keys = get_post_custom_keys( $post_ID );
    
    if ( $post_meta_keys ) {
        $meta_blacklist = array();
        $meta_blacklist[] = '_edit_lock';
        // Edit lock.
        $meta_blacklist[] = '_edit_last';
        // Edit lock.
        $meta_blacklist[] = '_dp_is_rewrite_republish_copy';
        $meta_blacklist[] = '_dp_has_rewrite_republish_copy';
        $meta_keys = array_diff( $post_meta_keys, $meta_blacklist );
        foreach ( $meta_keys as $meta_key ) {
            $meta_values = get_post_custom_values( $meta_key, $post_ID );
            foreach ( $meta_values as $meta_value ) {
                $meta_value = maybe_unserialize( $meta_value );
                add_post_meta( $new_post_id, $meta_key, wp_slash( $meta_value ) );
            }
        }
    }
    
    // Find and copy the saved codes.
    foreach ( glob( CODES_FOLDER_DIR . "{$post_ID}-*.*" ) as $file ) {
        $new_file = str_replace( $post_ID, $new_post_id, $file );
        copy( $file, $new_file );
    }
    // Redirect to the edit post screen for the new draft.
    wp_safe_redirect( add_query_arg( array(
        'action' => 'edit',
        'post'   => $new_post_id,
    ), admin_url( 'post.php' ) ) );
    exit;
}

add_action( 'admin_action_codes_duplicate_as_draft', 'codes_duplicate_as_draft' );