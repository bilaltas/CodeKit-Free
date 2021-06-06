<?php

/**
 *
 * Editor saver.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Save the data.
 *
 * @param int    $post_ID Returns the current post ID.
 * @param object $post Returns the current post ID.
 */
function codes_save_data( $post_ID = false, $post = false )
{
    global 
        $codes_langs,
        $codes_lang_groups,
        $wp_roles,
        $wp_filesystem
    ;
    // Don't save whole thing when autosaving.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // Nonce check. Also return when on "Add New" page.
    if ( !isset( $_POST['_wpnonce'] ) ) {
        return;
    }
    // Nonce verification.
    $nonce = sanitize_key( $_POST['_wpnonce'] );
    if ( !wp_verify_nonce( $nonce, 'update-post_' . $post_ID ) ) {
        codes_respond( array(
            'success'        => false,
            'savedEditors'   => array(),
            'errors'         => array(
            'Security' => 'Security error. Please try refreshing this page.',
        ),
            'processTime'    => '',
            'lastEditedText' => codes_last_edited_text( $post ),
        ) );
    }
    // Administration check.
    if ( !current_user_can( 'administrator' ) ) {
        codes_respond( array(
            'success'        => false,
            'savedEditors'   => array(),
            'errors'         => array(
            'Permission' => 'You don\'t have permission to do this.',
        ),
            'processTime'    => '',
            'lastEditedText' => codes_last_edited_text( $post ),
        ) );
    }
    // LANGUAGE AND LOCATION DATA.
    // Save the language.
    $available_languages = array_column( $codes_langs, 'id' );
    if ( isset( $_POST['language'] ) && in_array( sanitize_key( $_POST['language'] ), $available_languages, true ) ) {
        update_post_meta( $post_ID, '_codes_language', sanitize_key( $_POST['language'] ) );
    }
    // Save the breakpoint visibility.
    $use_breakpoints = ( isset( $_POST['useBreakpoints'] ) ? '1' : '0' );
    update_post_meta( $post_ID, '_codes_show_breakpoints', $use_breakpoints );
    // Save the locations.
    $available_locations = array(
        'frontend',
        'backend',
        'login',
        'everywhere',
        'nowhere'
    );
    if ( isset( $_POST['location'] ) && in_array( sanitize_key( $_POST['location'] ), $available_locations, true ) ) {
        update_post_meta( $post_ID, '_codes_location', sanitize_key( $_POST['location'] ) );
    }
    // Save the admin roles.
    $available_roles = array();
    foreach ( $wp_roles->roles as $role => $role_details ) {
        $available_roles[] = $role;
    }
    $roles_sent = ( isset( $_POST['roles'] ) ? array_map( 'sanitize_key', wp_unslash( $_POST['roles'] ) ) : array() );
    
    if ( array_intersect( $roles_sent, $available_roles ) === $roles_sent ) {
        update_post_meta( $post_ID, '_codes_adminroles', $roles_sent );
    } else {
        update_post_meta( $post_ID, '_codes_adminroles', array() );
    }
    
    // USER DATA.
    // Save the theme preference.
    $available_themes = array(
        'dark',
        'default',
        'monokai',
        'mdn-like',
        'neo'
    );
    // Update here when new theme added.
    if ( isset( $_POST['theme'] ) && in_array( sanitize_key( $_POST['theme'] ), $available_themes, true ) ) {
        update_user_meta( get_current_user_id(), '_codes_theme', sanitize_key( $_POST['theme'] ) );
    }
    // Save the font size preference.
    $available_font_sizes = array(
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20
    );
    if ( isset( $_POST['font-size'] ) && in_array( intval( $_POST['font-size'] ), $available_font_sizes, true ) ) {
        update_user_meta( get_current_user_id(), '_codes_fontsize', intval( $_POST['font-size'] ) );
    }
    // Save the indent preference.
    $available_indents = array(
        'space-2',
        'space-3',
        'space-4',
        'space-5',
        'space-6',
        'space-7',
        'space-8',
        'tab-2',
        'tab-3',
        'tab-4',
        'tab-5',
        'tab-6',
        'tab-7',
        'tab-8'
    );
    if ( isset( $_POST['indent'] ) && in_array( sanitize_key( $_POST['indent'] ), $available_indents, true ) ) {
        update_user_meta( get_current_user_id(), '_codes_indent', sanitize_key( $_POST['indent'] ) );
    }
    // CODES.
    $language = get_post_meta( $post_ID, '_codes_language', true );
    
    if ( empty($language) ) {
        // AJAX RESPONSE.
        if ( isset( $_POST['codes_doing_ajax'] ) ) {
            codes_respond( array(
                'success'        => true,
                'savedEditors'   => array(),
                'errors'         => array(),
                'processTime'    => '',
                'lastEditedText' => codes_last_edited_text( $post ),
            ) );
        }
        // Don't do the rest.
        return;
    }
    
    $language_key = array_search( $language, array_column( $codes_langs, 'id' ), true );
    $language_data = $codes_langs[$language_key];
    $language_extension = $language_data->id;
    $language_editors = $language_data->editors;
    $language_compileable = $language_data->output;
    $language_group = $language_data->group;
    $language_group_key = array_search( $language_group, array_column( $codes_lang_groups, 'id' ), true );
    $language_group_data = $codes_lang_groups[$language_group_key];
    // Start process timer.
    codes_process_timer_start();
    // Errors and saved editors lists.
    $errors = array();
    $saved_editors = array();
    // Responsive Variables (CSS, SCSS, LESS, STYLUS).
    
    if ( true === $language_compileable && 'style' === $language_group ) {
        $compileable = '';
        $breakpoints = array(
            'desktop'  => get_option( '_codes_desktop' ),
            'tablet-l' => get_option( '_codes_tablet_l' ),
            'tablet-p' => get_option( '_codes_tablet_p' ),
            'mobile-l' => get_option( '_codes_phone_l' ),
            'mobile-p' => get_option( '_codes_phone_p' ),
            'retina'   => get_option( '_codes_retina' ),
            'default'  => '',
        );
        $comments = array(
            'desktop'  => '/* xl - ' . __( 'DESKTOP STYLES', 'custom-codes' ) . " */ \n",
            'tablet-l' => '/* lg - ' . __( 'TABLET LANDSCAPE STYLES', 'custom-codes' ) . " */ \n",
            'tablet-p' => '/* md - ' . __( 'TABLET PORTRAIT STYLES', 'custom-codes' ) . " */ \n",
            'mobile-l' => '/* sm - ' . __( 'SMARTPHONE LANDSCAPE STYLES', 'custom-codes' ) . " */ \n",
            'mobile-p' => '/* xs - ' . __( 'SMARTPHONE PORTRAIT STYLES', 'custom-codes' ) . " */ \n",
            'retina'   => '/* ' . __( 'RETINA DISPLAY STYLES', 'custom-codes' ) . " */ \n",
            'default'  => '',
        );
        // Reorder the style editors for mobile first.
        
        if ( get_option( '_codes_output_order' ) === 'mobile-first' ) {
            // Reverse the order.
            $language_editors = array_reverse( $language_editors );
            // Put the retina to the end.
            $retina = array_shift( $language_editors );
            $language_editors[] = $retina;
        }
    
    }
    
    // Each editor works.
    foreach ( $language_editors as $editor ) {
        $editor_id = $editor->id;
        $editor_name = "{$post_ID}-{$editor_id}";
        $editor_file_name = "{$editor_name}.{$language_extension}";
        $file_directory = CODES_FOLDER_DIR . $editor_file_name;
        $editor_content = ( isset( $_POST["editor-{$editor_id}"] ) ? $_POST["editor-{$editor_id}"] : null );
        // phpcs:ignore
        $editor_value = codes_normalize_line_endings( stripslashes( $editor_content ) );
        
        if ( isset( $editor_content ) && !empty($editor_content) ) {
            // Pure editor save (JS, HTML, PHP, and all other langs).
            // Try fixing permission.
            if ( file_exists( $file_directory ) && !codes_is_writable( $file_directory ) ) {
                $wp_filesystem->chmod( $file_directory, 0644 );
            }
            // WRITE: Pure editor content save.
            $data_written = $wp_filesystem->put_contents( $file_directory, $editor_value, FILE_TEXT );
            
            if ( false === $data_written ) {
                $errors[$editor_file_name] = __( 'Could not be written to the file.', 'custom-codes' );
            } else {
                $saved_editors[] = "editor-{$editor_id}";
            }
        
        } elseif ( isset( $editor_content ) && empty($editor_content) && file_exists( $file_directory ) ) {
            // Empty content editor removal.
            // Delete the file.
            $deleted = unlink( $file_directory );
            
            if ( false === $deleted ) {
                $errors[$editor_file_name] = __( 'File could not be deleted.', 'custom-codes' );
            } else {
                $saved_editors[] = "editor-{$editor_id}";
            }
            
            // Individual outputs delete.
            
            if ( 'individual' === $language_compileable ) {
                $output_editor_name = "{$post_ID}-{$editor_id}-output";
                $output_editor_file_name = "{$output_editor_name}.{$language_group_data->extension}";
                $output_file_directory = CODES_FOLDER_DIR . $output_editor_file_name;
                
                if ( file_exists( $output_file_directory ) ) {
                    // Delete the file.
                    $deleted = unlink( $output_file_directory );
                    if ( false === $deleted ) {
                        $errors[$output_editor_file_name] = __( 'File output could not be deleted.', 'custom-codes' );
                    }
                }
            
            }
        
        }
        
        // COMPILE PREPARATION.
        
        if ( 'individual' === $language_compileable ) {
            // Compiler check.
            $compiler_file = CODES_PLUGIN_DIR . "/lib/compilers/{$language_group}/{$language}/{$language}.php";
            $compiler_found = file_exists( $compiler_file );
            if ( false === $compiler_found ) {
                $errors[$editor_file_name] = __( 'No compiler found', 'custom-codes' );
            }
            
            if ( file_exists( $file_directory ) && $compiler_found && !empty($editor_value) ) {
                // Call the function file.
                require_once $compiler_file;
                // Compile.
                $compiler_function = "codes_compile_{$language}";
                $compile_result = $compiler_function( $editor_value );
                
                if ( 'success' === $compile_result['status'] ) {
                    // Compileable file.
                    $compileable_file_name = "{$post_ID}-{$editor_id}-output.{$language_group_data->extension}";
                    $file_directory = CODES_FOLDER_DIR . $compileable_file_name;
                    // Try fixing permission.
                    if ( file_exists( $file_directory ) && !codes_is_writable( $file_directory ) ) {
                        $wp_filesystem->chmod( $file_directory, 0644 );
                    }
                    // WRITE: Save the compileable file first.
                    $data_written = $wp_filesystem->put_contents( $file_directory, $compile_result['compiled'], FILE_TEXT );
                    if ( false === $data_written ) {
                        $errors[$compileable_file_name] = __( 'Compiled output could not be written to the file.', 'custom-codes' );
                    }
                } else {
                    // Error.
                    $errors[$editor_file_name] = $compile_result['message'];
                }
            
            }
        
        } elseif ( true === $language_compileable && 'style' === $language_group ) {
            // Add Compileable Data (CSS, SCSS, LESS, STYLUS).
            // Responsive Breakpoints.
            
            if ( file_exists( $file_directory ) ) {
                $editor_value = $wp_filesystem->get_contents( $file_directory );
                $editor_screen = str_replace( "{$language_extension}-", '', $editor_id );
                $editor_breakpoint_media = $breakpoints[$editor_screen];
                $editor_comments = $comments[$editor_screen];
                $editor_value_import = codes_import_string( $editor_file_name, $language_extension, $editor_value );
                // Add the comments.
                $compileable .= $editor_comments;
                
                if ( '' === $editor_breakpoint_media ) {
                    // No media query.
                    $compileable .= "{$editor_value_import}\n\n";
                } else {
                    // With media query.
                    // Indent for media queried styles.
                    $indented_editor_value_import = str_replace( "\n", "\n  ", '  ' . $editor_value_import );
                    $media_queried_import = "{$editor_breakpoint_media} {\n{$indented_editor_value_import}\n}\n\n";
                    $compileable .= $media_queried_import;
                }
            
            }
        
        }
    
    }
    // Editors loop
    // Compileable Output Processing (CSS, SCSS, LESS, STYLUS).
    
    if ( true === $language_compileable && 'style' === $language_group ) {
        // Compileable file.
        $compileable_file_name = "{$post_ID}-{$language_extension}-output.{$language_extension}";
        $file_directory = CODES_FOLDER_DIR . $compileable_file_name;
        // Try fixing permission.
        if ( file_exists( $file_directory ) && !codes_is_writable( $file_directory ) ) {
            $wp_filesystem->chmod( $file_directory, 0644 );
        }
        // WRITE: Save the compileable file first.
        $data_written = $wp_filesystem->put_contents( $file_directory, $compileable, FILE_TEXT );
        if ( false === $data_written ) {
            $errors[$compileable_file_name] = __( 'Output could not be written to the file.', 'custom-codes' );
        }
        // Compiler check.
        $compiler_file = CODES_PLUGIN_DIR . "/lib/compilers/{$language_group}/{$language}/{$language}.php";
        $compiler_found = file_exists( $compiler_file );
        if ( false === $compiler_found && 'css' !== $language_extension ) {
            $errors[$compileable_file_name] = __( 'No compiler found', 'custom-codes' );
        }
        
        if ( false !== $data_written && $compiler_found ) {
            // Call the function file.
            require_once $compiler_file;
            // Compile.
            $compiler_function = "codes_compile_{$language}";
            $compile_result = $compiler_function( $compileable );
            
            if ( 'success' === $compile_result['status'] ) {
                $compileable_file_name = "{$post_ID}-{$language_extension}-output.{$language_group_data->extension}";
                $file_directory = CODES_FOLDER_DIR . $compileable_file_name;
                // Try fixing permission.
                if ( file_exists( $file_directory ) && !codes_is_writable( $file_directory ) ) {
                    $wp_filesystem->chmod( $file_directory, 0644 );
                }
                // WRITE: Save the compileable file first.
                $data_written = $wp_filesystem->put_contents( $file_directory, $compile_result['compiled'], FILE_TEXT );
                if ( false === $data_written ) {
                    $errors[$editor_id] = __( 'Compiled output could not be written to the file.', 'custom-codes' );
                }
            } else {
                // Error.
                $errors[$compileable_file_name] = $compile_result['message'];
            }
        
        }
    
    }
    
    // Increase the save count.
    $save_count = get_post_meta( $post_ID, '_codes_savecount', true ) + 1;
    update_post_meta( $post_ID, '_codes_savecount', $save_count );
    // Stop process timer.
    $process_timer = codes_process_timer_finish();
    // AJAX RESPONSE.
    if ( isset( $_POST['codes_doing_ajax'] ) ) {
        codes_respond( array(
            'success'        => ( count( $errors ) ? false : true ),
            'savedEditors'   => $saved_editors,
            'errors'         => $errors,
            'saveCount'      => $save_count,
            'processTime'    => $process_timer,
            'lastEditedText' => codes_last_edited_text( $post ),
        ) );
    }
}

add_action(
    'save_post_custom-code',
    'codes_save_data',
    10,
    2
);