<?php

/**
 * CodeKit
 *
 * @link    https://pressx.co
 * @since   2.0.0
 * @package Custom_Codes
 *
 * Plugin Name: CodeKit - Custom Codes Editor
 * Plugin URI: https://wordpress.org/plugins/custom-codes/
 * Description: Your custom SASS, CSS, JS and PHP customizations in same directory.
 * Author: PressX
 * Author URI: https://www.codekitwp.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: custom-codes
 * Domain Path: /languages
 * Version: 2.2.7
 *
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
define( 'CODES_VERSION', '2.2.7' );
define( 'CODES_DEBUG', false );
// Paths.
define( 'CODES_FILE', __FILE__ );
define( 'CODES_PLUGIN_DIR', dirname( CODES_FILE ) );
define( 'CODES_PLUGIN_URL', plugin_dir_url( CODES_FILE ) );
define( 'CODES_FOLDER_DIR', WP_CONTENT_DIR . '/custom_codes/' );
define( 'CODES_FOLDER_URL', str_replace( array( 'http:', 'https:' ), '', WP_CONTENT_URL ) . '/custom_codes/' );
// Freemius SDK.

if ( function_exists( 'codes_fs' ) ) {
    codes_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'codes_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         */
        function codes_fs()
        {
            global  $codes_fs ;
            
            if ( !isset( $codes_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $codes_fs = fs_dynamic_init( array(
                    'id'             => '7183',
                    'slug'           => 'custom-codes',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_4c4440eed53a6dd7637b96b2b82c0',
                    'is_premium'     => false,
                    'premium_suffix' => 'PRO',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'       => 'edit.php?post_type=custom-code',
                    'first-path' => 'edit.php?post_type=custom-code',
                    'contact'    => false,
                    'support'    => false,
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $codes_fs;
        }
        
        // Init Freemius.
        codes_fs();
        // Signal that SDK was initiated.
        do_action( 'codes_fs_loaded' );
    }
    
    // Early call the WP File System API.
    
    if ( !isset( $wp_filesystem ) ) {
        if ( !function_exists( 'WP_Filesystem' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        if ( !WP_Filesystem() ) {
            add_action( 'admin_notices', function () {
                $class = 'notice notice-error';
                $message = sprintf(
                    /* translators: 1: WP Filesystem Method */
                    __( 'Your WordPress filesystem method "%1$s" is not configured correctly. Please configure it, or use another method like "direct" to be able to continue using the plugin.', 'custom-codes' ),
                    get_filesystem_method()
                );
                printf( '<div class="notice notice-error"><p><b>CodeKit:</b> %1$s</p></div>', esc_html( $message ) );
            } );
            return;
        }
        
        global  $wp_filesystem ;
    }
    
    // Permission check and fix.
    require_once CODES_PLUGIN_DIR . '/lib/helper-functions.php';
    require_once CODES_PLUGIN_DIR . '/lib/permissions.php';
    // Final permissions.
    define( 'CODES_FOLDER_EXISTS', file_exists( CODES_FOLDER_DIR ) );
    define( 'CODES_FOLDER_READABLE', codes_is_readable( CODES_FOLDER_DIR ) );
    define( 'CODES_FOLDER_WRITABLE', codes_is_writable( CODES_FOLDER_DIR ) );
    define( 'CODES_FOLDER_EXECUTABLE', codes_is_executable( CODES_FOLDER_DIR ) );
    // Global variables.
    $codes_posts = array();
    $codes_langs_json = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/data/langs.json' );
    $codes_lang_groups_json = $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/data/langGroups.json' );
    $codes_langs = json_decode( $codes_langs_json );
    $codes_lang_groups = json_decode( $codes_lang_groups_json );
    // Backend.
    
    if ( is_admin() ) {
        // Plugin.
        require_once CODES_PLUGIN_DIR . '/lib/activation.php';
        require_once CODES_PLUGIN_DIR . '/lib/upgrade.php';
        // Settings.
        require_once CODES_PLUGIN_DIR . '/lib/post-type.php';
        require_once CODES_PLUGIN_DIR . '/lib/admin-columns.php';
        require_once CODES_PLUGIN_DIR . '/lib/register-data.php';
        // Save the data.
        require_once CODES_PLUGIN_DIR . '/lib/editor-saver.php';
        // Views.
        require_once CODES_PLUGIN_DIR . '/lib/editor-includes.php';
        require_once CODES_PLUGIN_DIR . '/lib/views/editor-area.php';
        require_once CODES_PLUGIN_DIR . '/lib/views/locations-area.php';
        require_once CODES_PLUGIN_DIR . '/lib/views/settings-area.php';
        require_once CODES_PLUGIN_DIR . '/lib/views/includes-area.php';
    }
    
    // Both frontend and backend.
    require_once CODES_PLUGIN_DIR . '/lib/release-codes.php';
    require_once CODES_PLUGIN_DIR . '/lib/views/admin-bar.php';
    require_once CODES_PLUGIN_DIR . '/lib/uninstallation.php';
}
