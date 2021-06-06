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
    $new_column['language'] = __( 'Language', 'custom-codes' );
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