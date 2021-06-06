<?php

/**
 *
 * Release the codes.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
// GET ALL THE CODES.
$codes_posts = get_posts( array(
    'post_type'      => 'custom-code',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
    'post_status'    => 'publish',
) );
// Safe Mode. Do not release codes.
if ( isset( $_GET['codes_safemode'] ) ) {
    // phpcs:ignore
    return;
}
/**
 * Location conditions of hooks.
 *
 * @param int    $post_ID Post ID of code.
 * @param string $location Location of code.
 */
function codes_location_conditions( $post_ID, $location )
{
    $condition = false;
    // Everywhere.
    if ( 'everywhere' === $location ) {
        $condition = true;
    }
    // Frontend.
    if ( 'frontend' === $location ) {
        $condition = !is_admin();
    }
    // Backend.
    
    if ( 'backend' === $location ) {
        $restrict_roles = get_post_meta( $post_ID, '_codes_adminroles', true );
        $restrict_roles = ( is_array( $restrict_roles ) ? $restrict_roles : array() );
        $user = wp_get_current_user();
        $condition = is_admin() && (empty($restrict_roles) || !empty($restrict_roles) && array_intersect( $restrict_roles, $user->roles ));
    }
    
    // Login.
    if ( 'login' === $location ) {
        $condition = codes_is_login_page();
    }
    return $condition;
}

/**
 * HTML, CSS, JS release.
 */
function codes_release()
{
    global 
        $codes_posts,
        $codes_langs,
        $codes_lang_groups,
        $wp_filesystem
    ;
    // Early exit if posts are not ready.
    if ( !is_array( $codes_posts ) ) {
        return;
    }
    $pro_frontend_locations = array(
        'page',
        'post',
        'post_type',
        'term',
        'taxonomy',
        'template'
    );
    foreach ( $codes_posts as $code_post ) {
        $post_ID = $code_post->ID;
        $language = get_post_meta( $post_ID, '_codes_language', true );
        $language_key = array_search( $language, array_column( $codes_langs, 'id' ), true );
        $language_data = $codes_langs[$language_key];
        $language_extension = $language_data->id;
        $language_editors = $language_data->editors;
        $language_compileable = $language_data->output;
        $language_group = $language_data->group;
        $language_group_key = array_search( $language_group, array_column( $codes_lang_groups, 'id' ), true );
        $language_group_data = $codes_lang_groups[$language_group_key];
        $language_group_extension = $language_group_data->extension;
        $save_count = get_post_meta( $post_ID, '_codes_savecount', true );
        $location = get_post_meta( $post_ID, '_codes_location', true );
        // LANGUAGE GROUP BASED OUTPUTS.
        
        if ( 'css' === $language_group_extension ) {
            // CSS Outputs.
            $file_name = "{$post_ID}-{$language_extension}-output";
            $file_full_name = "{$file_name}.{$language_group_extension}";
            $file_directory = CODES_FOLDER_DIR . $file_full_name;
            
            if ( file_exists( $file_directory ) ) {
                // CSS actions.
                $css_actions = array(
                    'wp_enqueue_scripts'    => array( 'everywhere', 'frontend' ),
                    'admin_enqueue_scripts' => array( 'everywhere', 'backend' ),
                    'login_enqueue_scripts' => array( 'everywhere', 'login' ),
                );
                // Hooks.
                foreach ( $css_actions as $hook => $run_on ) {
                    if ( !in_array( $location, $run_on, true ) ) {
                        continue;
                    }
                    add_action( $hook, function () use(
                        $file_name,
                        $file_full_name,
                        $save_count,
                        $post_ID,
                        $location
                    ) {
                        if ( codes_location_conditions( $post_ID, $location ) ) {
                            wp_enqueue_style(
                                "codes-{$file_name}",
                                CODES_FOLDER_URL . $file_full_name,
                                array(),
                                $save_count
                            );
                        }
                    }, 99999 );
                }
            }
        
        } elseif ( 'js' === $language_group_extension ) {
            // JS Outputs.
            // Loop each editor.
            foreach ( $language_editors as $editor ) {
                $editor_id = $editor->id;
                $editor_name = "{$post_ID}-{$editor_id}";
                
                if ( 'individual' === $language_compileable ) {
                    $editor_name .= '-output';
                } elseif ( true === $language_compileable ) {
                    $editor_name = "{$post_ID}-{$language_extension}-output";
                }
                
                $editor_file_name = "{$editor_name}.{$language_group_extension}";
                $editor_location = str_replace( $language_extension . '-', '', $editor_id );
                $file_directory = CODES_FOLDER_DIR . $editor_file_name;
                $editor_url = CODES_FOLDER_URL . $editor_file_name;
                
                if ( file_exists( $file_directory ) ) {
                    // JS Actions.
                    $js_actions = array(
                        'wp_body_open'          => array( 'everywhere', 'frontend' ),
                        'wp_enqueue_scripts'    => array( 'everywhere', 'frontend' ),
                        'admin_enqueue_scripts' => array( 'everywhere', 'backend' ),
                        'login_enqueue_scripts' => array( 'everywhere', 'login' ),
                    );
                    // Hooks.
                    foreach ( $js_actions as $hook => $run_on ) {
                        if ( !in_array( $location, $run_on, true ) ) {
                            continue;
                        }
                        add_action( $hook, function () use(
                            $editor_file_name,
                            $editor_url,
                            $save_count,
                            $editor_name,
                            $post_ID,
                            $location,
                            $editor_location
                        ) {
                            if ( codes_location_conditions( $post_ID, $location ) ) {
                                
                                if ( 'body-opening' === $editor_location ) {
                                    echo  '<script type="text/javascript" src="' . esc_url( "{$editor_url}?ver={$save_count}" ) . '" id="' . esc_attr( "codes-{$editor_name}" ) . '"></script>' ;
                                    // phpcs:ignore
                                } else {
                                    wp_enqueue_script(
                                        "codes-{$editor_name}",
                                        $editor_url,
                                        array( 'jquery' ),
                                        $save_count,
                                        'body-closing' === $editor_location
                                    );
                                }
                            
                            }
                        }, 99999 );
                    }
                }
            
            }
        } elseif ( 'html' === $language_group_extension ) {
            // HTML Outputs.
            // Loop each editor.
            foreach ( $language_editors as $editor ) {
                $editor_id = $editor->id;
                $editor_name = "{$post_ID}-{$editor_id}";
                
                if ( 'individual' === $language_compileable ) {
                    $editor_name .= '-output';
                } elseif ( true === $language_compileable ) {
                    $editor_name = "{$post_ID}-{$language_extension}-output";
                }
                
                $editor_file_name = "{$editor_name}.{$language_group_extension}";
                $editor_location = str_replace( $language_extension . '-', '', $editor_id );
                $file_directory = CODES_FOLDER_DIR . $editor_file_name;
                
                if ( file_exists( $file_directory ) ) {
                    // HTML Actions.
                    $html_actions = array(
                        'wp_body_open' => array( 'everywhere', 'frontend' ),
                        'wp_head'      => array( 'everywhere', 'frontend' ),
                        'wp_footer'    => array( 'everywhere', 'frontend' ),
                        'admin_head'   => array( 'everywhere', 'backend' ),
                        'admin_footer' => array( 'everywhere', 'backend' ),
                        'login_head'   => array( 'everywhere', 'login' ),
                        'login_footer' => array( 'everywhere', 'login' ),
                    );
                    // Hooks.
                    foreach ( $html_actions as $hook => $run_on ) {
                        if ( !in_array( $location, $run_on, true ) ) {
                            continue;
                        }
                        // Editor locations.
                        if ( 'head' === $editor_location && 'wp_head' !== $hook && 'admin_head' !== $hook && 'login_head' !== $hook || 'body-opening' === $editor_location && 'wp_body_open' !== $hook && 'admin_body_open' !== $hook && 'login_body_open' !== $hook || 'body-closing' === $editor_location && 'wp_footer' !== $hook && 'admin_footer' !== $hook && 'login_footer' !== $hook ) {
                            continue;
                        }
                        add_action( $hook, function () use(
                            $file_directory,
                            $wp_filesystem,
                            $post_ID,
                            $location,
                            $editor_location
                        ) {
                            
                            if ( codes_location_conditions( $post_ID, $location ) ) {
                                echo  $wp_filesystem->get_contents( $file_directory ) ;
                                // phpcs:ignore
                            }
                        
                        }, 99999 );
                    }
                }
            
            }
        }
    
    }
    // Codes Loop
}

add_action( 'init', 'codes_release', 99999 );
// PHP RELEASE.
foreach ( $codes_posts as $code_post ) {
    $code_post_id = $code_post->ID;
    $language = get_post_meta( $code_post_id, '_codes_language', true );
    if ( 'php' !== $language || isset( $_GET['custom-codes-saving'] ) ) {
        // phpcs:ignore
        continue;
    }
    $language_key = array_search( $language, array_column( $codes_langs, 'id' ), true );
    $language_data = $codes_langs[$language_key];
    $language_extension = $language_data->id;
    $language_editors = $language_data->editors;
    $language_compileable = $language_data->output;
    $language_group = $language_data->group;
    $language_group_key = array_search( $language_group, array_column( $codes_lang_groups, 'id' ), true );
    $language_group_data = $codes_lang_groups[$language_group_key];
    $language_group_extension = $language_group_data->extension;
    $save_count = get_post_meta( $code_post_id, '_codes_savecount', true );
    $location = get_post_meta( $code_post_id, '_codes_location', true );
    // Loop each editor.
    foreach ( $language_editors as $editor ) {
        $editor_id = $editor->id;
        $editor_name = "{$code_post_id}-{$editor_id}";
        $editor_file_name = "{$editor_name}.{$language_group_extension}";
        $editor_location = str_replace( $language_extension . '-', '', $editor_id );
        $file_directory = CODES_FOLDER_DIR . $editor_file_name;
        if ( file_exists( $file_directory ) && 'default' === $editor_location && !defined( 'DOING_AJAX' ) ) {
            
            if ( 'frontend' === $location && !is_admin() || 'backend' === $location && is_admin() && empty($restrict_roles) || 'login' === $location && codes_is_login_page() || 'everywhere' === $location ) {
                try {
                    require $file_directory;
                } catch ( Exception $e ) {
                    if ( CODES_DEBUG ) {
                        wp_die( 'CodeKit - Caught exception on code #' . esc_html( $editor_name ) . ': ' . esc_html( $e->getMessage() ) . "\n" );
                    }
                }
            } else {
                // Or, run it in an action.
                add_action( 'wp', function () use(
                    $file_directory,
                    $wp_filesystem,
                    $code_post_id,
                    $location
                ) {
                    if ( codes_location_conditions( $code_post_id, $location ) ) {
                        try {
                            require $file_directory;
                        } catch ( Exception $e ) {
                            if ( CODES_DEBUG ) {
                                wp_die( 'CodeKit - Caught exception on code #' . esc_html( $editor_name ) . ': ' . esc_html( $e->getMessage() ) . "\n" );
                            }
                        }
                    }
                } );
            }
        
        }
    }
}