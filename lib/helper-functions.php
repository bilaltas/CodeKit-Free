<?php

/**
 *
 * Helper functions.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Debug info hider.
 */
function codes_hide_debug_info()
{
    if ( CODES_DEBUG ) {
        return;
    }
    ?>
	<style> .codes-debug { display: none; }</style>
	<?php 
}

add_action( 'admin_head', 'codes_hide_debug_info' );
/**
 * Hide notification label.
 */
function codes_hide_notification_label()
{
    ?>
	<style> .fs-slug-custom-codes > .fs-plugin-title { display: none; }</style>
	<?php 
}

add_action( 'admin_head', 'codes_hide_notification_label' );
/**
 * Respond as JSON
 *
 * @param array $data Data to return.
 */
function codes_respond( $data )
{
    header( 'Content-type: application/json' );
    die( wp_json_encode( $data ) );
}

/**
 * Last edited text.
 *
 * @param object $post Post object.
 */
function codes_last_edited_text( $post )
{
    
    if ( 'auto-draft' !== $post->post_status ) {
        $last_user = get_userdata( get_post_meta( $post->ID, '_edit_last', true ) );
        if ( $last_user ) {
            /* translators: 1: Name of most recent post author, 2: Post edited date, 3: Post edited time. */
            return sprintf(
                __( 'Last edited by %1$s on %2$s at %3$s' ),
                esc_html( $last_user->display_name ),
                mysql2date( __( 'F j, Y' ), $post->post_modified ),
                mysql2date( __( 'g:i a' ), $post->post_modified )
            );
        }
        /* translators: 1: Post edited date, 2: Post edited time. */
        return sprintf( __( 'Last edited on %1$s at %2$s' ), mysql2date( __( 'F j, Y' ), $post->post_modified ), mysql2date( __( 'g:i a' ), $post->post_modified ) );
    }
    
    return '';
}

/**
 * Chmod check.
 *
 * @param string $dir Directory path.
 */
function codes_chmod_check( $dir )
{
    if ( !file_exists( $dir ) ) {
        return 0;
    }
    return intval( decoct( fileperms( $dir ) & 0777 ) );
}

/**
 * File executable check.
 *
 * @param string $dir Directory path.
 */
function codes_is_executable( $dir )
{
    // Do not check directory executability on Windows servers.
    $is_executable = ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ? true : is_executable( $dir ) );
    if ( !$is_executable || substr( strval( codes_chmod_check( $dir ) ), 0, 1 ) !== '5' && substr( strval( codes_chmod_check( $dir ) ), 0, 1 ) !== '7' ) {
        return false;
    }
    return true;
}

/**
 * File readable check.
 *
 * @param string $dir Directory path.
 */
function codes_is_readable( $dir )
{
    if ( is_dir( $dir ) && codes_chmod_check( $dir ) < 500 || (!is_readable( $dir ) || codes_chmod_check( $dir ) < 400) ) {
        return false;
    }
    return true;
}

/**
 * File writeable check.
 *
 * @param string $dir Directory path.
 */
function codes_is_writable( $dir )
{
    if ( is_dir( $dir ) && codes_chmod_check( $dir ) < 600 || (!is_writable( $dir ) || codes_chmod_check( $dir ) < 600 || !codes_is_readable( $dir )) ) {
        return false;
    }
    return true;
}

/**
 * Process timer start.
 */
function codes_process_timer_start()
{
    global  $starttime ;
    $starttime = gettimeofday();
    // RETRIEVE SECONDS AND MICROSECONDS (ONE MILLIONTH OF A SECOND).
    // CONVERT MICROSECONDS TO SECONDS AND ADD TO RETRIEVED SECONDS.
    // MULTIPLY BY 1000 TO GET MILLISECONDS.
    $starttime = 1000 * ($starttime['sec'] + $starttime['usec'] / 1000000);
}

/**
 * Process timer finish.
 */
function codes_process_timer_finish()
{
    global  $starttime ;
    $timeofday = gettimeofday();
    // RETRIEVE SECONDS AND MICROSECONDS (ONE MILLIONTH OF A SECOND).
    // CONVERT MICROSECONDS TO SECONDS AND ADD TO RETRIEVED SECONDS.
    // MULTIPLY BY 1000 TO GET MILLISECONDS.
    $endtime = 1000 * ($timeofday['sec'] + $timeofday['usec'] / 1000000);
    return round( $endtime - $starttime, 3 ) . ' ms';
}

/**
 * Check current page is login page.
 */
function codes_is_login_page()
{
    return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), true );
}

/**
 * SVG Allowed arguments.
 */
function codes_svg_args()
{
    return array(
        'svg'      => array(
        'class'           => true,
        'aria-hidden'     => true,
        'aria-labelledby' => true,
        'role'            => true,
        'xmlns'           => true,
        'width'           => true,
        'height'          => true,
        'viewbox'         => true,
        'd'               => true,
        'fill'            => true,
        'fill-rule'       => true,
        'clip-rule'       => true,
        'stroke'          => true,
        'stroke-opacity'  => true,
        'stroke-width'    => true,
        'stroke-linecap'  => true,
        'stroke-linejoin' => true,
    ),
        'g'        => array(
        'fill' => true,
    ),
        'title'    => array(
        'title' => true,
    ),
        'path'     => array(
        'd'               => true,
        'fill'            => true,
        'fill-rule'       => true,
        'clip-rule'       => true,
        'stroke'          => true,
        'stroke-opacity'  => true,
        'stroke-width'    => true,
        'stroke-linecap'  => true,
        'stroke-linejoin' => true,
    ),
        'polyline' => array(
        'points' => true,
    ),
    );
}

/**
 * Normalize line endings.
 *
 * @param string $string Text.
 */
function codes_normalize_line_endings( $string )
{
    // Convert all line-endings to UNIX format.
    $string = str_replace( array( "\r\n", "\r", "\n" ), "\n", $string );
    return $string;
}

/**
 * Recursive sanization for an array.
 *
 * @param array $array Array to sanitize.
 */
function codes_recursive_sanitize_text_field( $array )
{
    foreach ( $array as $key => &$value ) {
        
        if ( is_array( $value ) ) {
            $value = codes_recursive_sanitize_text_field( $value );
        } else {
            $value = sanitize_text_field( $value );
        }
    
    }
    return $array;
}

/**
 * File import string by language.
 *
 * @param string $file_path File path that will be imported.
 * @param string $lang Language extension.
 * @param string $file_content Editor file content. This is needed on STYLUS, JS, HTML.
 * @param string $file_lang Included file language.
 */
function codes_import_string(
    $file_path,
    $lang,
    $file_content = null,
    $file_lang = null
)
{
    
    if ( 'css' === $lang ) {
        if ( !empty($file_content) ) {
            // Known issue.
            return $file_content;
        }
        return "@import '{$file_path}';";
    }
    
    if ( 'scss' === $lang ) {
        return "@import '{$file_path}';";
    }
    if ( 'php' === $lang ) {
        return "<?php include CODES_FOLDER_DIR . '{$file_path}'; ?>";
    }
    return $file_content;
}
