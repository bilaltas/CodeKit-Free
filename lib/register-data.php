<?php

/**
 *
 * Register saveable data.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Register data.
 */
function codes_register_data()
{
    // REGISTER POST DATA.
    // Language.
    register_post_meta( 'custom-code', '_codes_language', array(
        'type'        => 'string',
        'description' => __( 'Selected language for the custom code', 'custom-codes' ),
        'single'      => true,
        'default'     => '',
    ) );
    // Location.
    register_post_meta( 'custom-code', '_codes_location', array(
        'type'        => 'string',
        'description' => __( 'Location of the code', 'custom-codes' ),
        'single'      => true,
        'default'     => 'frontend',
    ) );
    // Breakpoints Usage.
    register_post_meta( 'custom-code', '_codes_show_breakpoints', array(
        'type'        => 'boolean',
        'description' => __( 'Whether or not using breakpoints.', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // Admin Roles to Apply.
    register_post_meta( 'custom-code', '_codes_adminroles', array(
        'type'        => 'array',
        'description' => __( 'Roles that the code will be applied', 'custom-codes' ),
        'single'      => true,
        'default'     => array(),
    ) );
    // Save Count.
    register_post_meta( 'custom-code', '_codes_savecount', array(
        'type'        => 'integer',
        'description' => __( 'Save count of each code post', 'custom-codes' ),
        'single'      => true,
        'default'     => 0,
    ) );
    // REGISTER USER DATA.
    // Theme.
    register_meta( 'user', '_codes_theme', array(
        'type'        => 'string',
        'description' => __( 'User defined editor theme', 'custom-codes' ),
        'single'      => true,
        'default'     => 'dark',
    ) );
    // Font Size.
    register_meta( 'user', '_codes_fontsize', array(
        'type'        => 'integer',
        'description' => __( 'User defined editor font size', 'custom-codes' ),
        'single'      => true,
        'default'     => 14,
    ) );
    // Indent.
    register_meta( 'user', '_codes_indent', array(
        'type'        => 'string',
        'description' => __( 'User defined editor indent option', 'custom-codes' ),
        'single'      => true,
        'default'     => 'tab-4',
    ) );
    // EDITOR SETTINGS.
    // AJAX Saver.
    register_setting( 'codes_settings', '_codes_ajax', array(
        'type'        => 'boolean',
        'description' => __( 'AJAX Saver', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // Play Sound When Saved.
    register_setting( 'codes_settings', '_codes_sound', array(
        'type'        => 'boolean',
        'description' => __( 'Play sound when saved', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // Save with Command S.
    register_setting( 'codes_settings', '_codes_shortcut', array(
        'type'        => 'boolean',
        'description' => __( 'Save with "Cmd/Ctrl S"', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // Emmet Feature.
    register_setting( 'codes_settings', '_codes_emmet', array(
        'type'        => 'boolean',
        'description' => __( 'Emmet Feature', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // STYLE SETTINGS.
    // Initial Editor Tab.
    register_setting( 'codes_settings', '_codes_initial_editor', array(
        'type'        => 'string',
        'description' => __( 'Initial Editor Tab', 'custom-codes' ),
        'single'      => true,
        'default'     => 'first',
    ) );
    // Output Order.
    register_setting( 'codes_settings', '_codes_output_order', array(
        'type'        => 'string',
        'description' => __( 'Output Order', 'custom-codes' ),
        'single'      => true,
        'default'     => 'desktop-first',
    ) );
    // Desktop Media Query.
    register_setting( 'codes_settings', '_codes_desktop', array(
        'type'        => 'string',
        'description' => __( 'Desktop <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '',
    ) );
    // Tablet Landscape Media Query.
    register_setting( 'codes_settings', '_codes_tablet_l', array(
        'type'        => 'string',
        'description' => __( 'Tablet Landscape <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '@media (max-width: 1199px)',
    ) );
    // Tablet Portrait Media Query.
    register_setting( 'codes_settings', '_codes_tablet_p', array(
        'type'        => 'string',
        'description' => __( 'Tablet Portrait <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '@media (max-width: 991px)',
    ) );
    // Smartphone Landscape Media Query.
    register_setting( 'codes_settings', '_codes_phone_l', array(
        'type'        => 'string',
        'description' => __( 'Smartphone Landscape <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '@media (max-width: 767px)',
    ) );
    // Smartphone Portrait Media Query.
    register_setting( 'codes_settings', '_codes_phone_p', array(
        'type'        => 'string',
        'description' => __( 'Smartphone Portrait <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '@media (max-width: 479px)',
    ) );
    // Retina Displays Media Query.
    register_setting( 'codes_settings', '_codes_retina', array(
        'type'        => 'string',
        'description' => __( 'Retina Displays <br> Media Query', 'custom-codes' ),
        'single'      => true,
        'default'     => '@media (min-device-pixel-ratio: 1.5)',
    ) );
    // PLUGIN SETTINGS.
    // Show admin bar menu.
    register_setting( 'codes_settings', '_codes_admin_bar', array(
        'type'        => 'boolean',
        'description' => __( 'Show admin bar menu', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
    // Store codes after uninstallation.
    register_setting( 'codes_settings', '_codes_store', array(
        'type'        => 'boolean',
        'description' => __( 'Store codes after uninstallation', 'custom-codes' ),
        'single'      => true,
        'default'     => true,
    ) );
}

add_action( 'init', 'codes_register_data' );