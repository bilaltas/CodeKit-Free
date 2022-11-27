<?php

/**
 *
 * Files and data to include editor page view.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Register scripts and styles.
 */
function codes_load_sources()
{
    global  $post ;
    // Early exit if on another post type.
    if ( !$post || 'custom-code' !== $post->post_type ) {
        return;
    }
    // Vue - https://cdn.jsdelivr.net/npm/vue/dist/vue(.min).js.
    wp_register_script(
        'codes_vuejs',
        CODES_PLUGIN_URL . 'assets/script/vue/vue.min.js',
        array(),
        CODES_VERSION,
        true
    );
    // CodeMirror.
    wp_register_script(
        'codes_codemirror',
        CODES_PLUGIN_URL . 'assets/script/codemirror/lib/codemirror.js',
        array(),
        CODES_VERSION,
        true
    );
    wp_register_style(
        'codes_codemirror',
        CODES_PLUGIN_URL . 'assets/script/codemirror/lib/codemirror.css',
        array(),
        CODES_VERSION
    );
    // Formatter.
    wp_register_script(
        'codes_codemirror-formatting',
        CODES_PLUGIN_URL . 'assets/script/codemirror/lib/util/formatting.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // CodeMirror Modes.
    wp_enqueue_script(
        'codes_codemirror-htmlmixed',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/htmlmixed/htmlmixed.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_enqueue_script(
        'codes_codemirror-css',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/css/css.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_enqueue_script(
        'codes_codemirror-sass',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/sass/sass.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_enqueue_script(
        'codes_codemirror-javascript',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/javascript/javascript.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // PHP.
    wp_enqueue_script(
        'codes_codemirror-xml',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/xml/xml.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_enqueue_script(
        'codes_codemirror-clike',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/clike/clike.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_enqueue_script(
        'codes_codemirror-php',
        CODES_PLUGIN_URL . 'assets/script/codemirror/mode/php/php.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // CodeMirror Addons.
    wp_register_script(
        'codes_codemirror-autorefresh',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/display/autorefresh.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-placeholder',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/display/placeholder.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-active-line',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/selection/active-line.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-closebrackets',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/edit/closebrackets.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-matchbrackets',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/edit/matchbrackets.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-trailingspace',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/edit/trailingspace.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-comment',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/comment/comment.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // Search.
    wp_register_style(
        'codes_codemirror-dialog',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/dialog/dialog.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_script(
        'codes_codemirror-dialog',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/dialog/dialog.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-searchcursor',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/searchcursor.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-search',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/search.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-annotatescrollbar',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/scroll/annotatescrollbar.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_style(
        'codes_codemirror-matchesonscrollbar',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/matchesonscrollbar.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_script(
        'codes_codemirror-matchesonscrollbar',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/matchesonscrollbar.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-jump-to-line',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/jump-to-line.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    wp_register_script(
        'codes_codemirror-match-highlighter',
        CODES_PLUGIN_URL . 'assets/script/codemirror/addon/search/match-highlighter.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // Emmet.
    wp_register_style(
        'codes_codemirror-emmet',
        CODES_PLUGIN_URL . 'assets/script/emmet/emmet.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_script(
        'codes_codemirror-emmet',
        CODES_PLUGIN_URL . 'assets/script/emmet/browser.js',
        array( 'codes_codemirror' ),
        CODES_VERSION,
        true
    );
    // CodeMirror Themes.
    wp_register_style(
        'codes_codemirror-dark',
        CODES_PLUGIN_URL . 'assets/script/codemirror/theme/dark.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_style(
        'codes_codemirror-monokai',
        CODES_PLUGIN_URL . 'assets/script/codemirror/theme/monokai.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_style(
        'codes_codemirror-mdn-like',
        CODES_PLUGIN_URL . 'assets/script/codemirror/theme/mdn-like.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    wp_register_style(
        'codes_codemirror-neo',
        CODES_PLUGIN_URL . 'assets/script/codemirror/theme/neo.css',
        array( 'codes_codemirror' ),
        CODES_VERSION
    );
    // JS dependencies list.
    $js_dependencies = array(
        'codes_vuejs',
        'codes_codemirror',
        // Formatter.
        'codes_codemirror-formatting',
        // Modes.
        'codes_codemirror-htmlmixed',
        'codes_codemirror-css',
        'codes_codemirror-sass',
        'codes_codemirror-javascript',
        'codes_codemirror-xml',
        'codes_codemirror-clike',
        'codes_codemirror-php',
        // Addons.
        'codes_codemirror-autorefresh',
        'codes_codemirror-placeholder',
        'codes_codemirror-active-line',
        'codes_codemirror-closebrackets',
        'codes_codemirror-matchbrackets',
        'codes_codemirror-trailingspace',
        'codes_codemirror-comment',
        // Search.
        'codes_codemirror-dialog',
        'codes_codemirror-searchcursor',
        'codes_codemirror-search',
        'codes_codemirror-annotatescrollbar',
        'codes_codemirror-matchesonscrollbar',
        'codes_codemirror-jump-to-line',
        // Emmet.
        'codes_codemirror-emmet',
    );
    wp_register_script(
        'codes_scripts',
        CODES_PLUGIN_URL . 'assets/script/script.js',
        $js_dependencies,
        CODES_VERSION,
        true
    );
    // CSS dependencies list.
    $css_dependencies = array(
        'codes_codemirror',
        // Themes.
        'codes_codemirror-dark',
        'codes_codemirror-monokai',
        'codes_codemirror-mdn-like',
        'codes_codemirror-neo',
        // Addons.
        'codes_codemirror-dialog',
        'codes_codemirror-matchesonscrollbar',
    );
    wp_register_style(
        'codes_styles',
        CODES_PLUGIN_URL . 'assets/style/style.css',
        $css_dependencies,
        CODES_VERSION
    );
}

add_action( 'admin_enqueue_scripts', 'codes_load_sources' );
/**
 * Bring data to frontend.
 */
function codes_bring_data()
{
    global  $post, $codes_lang_groups, $codes_langs ;
    // Early exit if on another post type.
    if ( !$post || 'custom-code' !== $post->post_type ) {
        return;
    }
    // Backend data to frontend.
    $data = array(
        'ajaxUrl'            => admin_url( 'post.php?custom-codes-saving' ),
        'postName'           => $post->post_name,
        'postID'             => $post->ID,
        'langGroups'         => $codes_lang_groups,
        'langs'              => $codes_langs,
        'isPremium'          => codes_fs()->is_premium(),
        'isPremiumOnly'      => codes_fs()->is__premium_only(),
        'postLanguage'       => get_post_meta( $post->ID, '_codes_language', true ),
        'postLocation'       => ( get_post_meta( $post->ID, '_codes_location', true ) === '' ? 'frontend' : get_post_meta( $post->ID, '_codes_location', true ) ),
        'postUseBreakpoints' => ( get_post_meta( $post->ID, '_codes_show_breakpoints', true ) === '' ? true : get_post_meta( $post->ID, '_codes_show_breakpoints', true ) ),
        'postRoles'          => ( get_post_meta( $post->ID, '_codes_adminroles', true ) === '' ? array() : get_post_meta( $post->ID, '_codes_adminroles', true ) ),
        'saveCount'          => ( get_post_meta( $post->ID, '_codes_savecount', true ) === '' ? 0 : get_post_meta( $post->ID, '_codes_savecount', true ) ),
        'userTheme'          => ( get_user_meta( get_current_user_id(), '_codes_theme', true ) === '' ? 'dark' : get_user_meta( get_current_user_id(), '_codes_theme', true ) ),
        'userFontSize'       => ( get_user_meta( get_current_user_id(), '_codes_fontsize', true ) === '' ? 14 : get_user_meta( get_current_user_id(), '_codes_fontsize', true ) ),
        'userIndent'         => ( get_user_meta( get_current_user_id(), '_codes_indent', true ) === '' ? 'space-4' : get_user_meta( get_current_user_id(), '_codes_indent', true ) ),
        'custom_codes_uri'   => CODES_FOLDER_URL,
        'lastEditedText'     => codes_last_edited_text( $post ),
        'ajaxSave'           => get_option( '_codes_ajax' ),
        'playSound'          => get_option( '_codes_sound' ),
        'commandS'           => get_option( '_codes_shortcut' ),
        'useEmmet'           => get_option( '_codes_emmet' ),
        'initialStyleTab'    => get_option( '_codes_initial_editor' ),
        'query-desktop'      => get_option( '_codes_desktop' ),
        'query-tablet-l'     => get_option( '_codes_tablet_l' ),
        'query-tablet-p'     => get_option( '_codes_tablet_p' ),
        'query-mobile-l'     => get_option( '_codes_phone_l' ),
        'query-mobile-p'     => get_option( '_codes_phone_p' ),
        'query-retina'       => get_option( '_codes_retina' ),
    );
    wp_localize_script( 'codes_scripts', 'customCodesData', $data );
}

add_action( 'admin_enqueue_scripts', 'codes_bring_data' );
/**
 * Audio element.
 */
function codes_audio_element()
{
    global  $post ;
    // Early exit if on another post type.
    if ( !$post || 'custom-code' !== $post->post_type ) {
        return;
    }
    ?>
	<script>
		var codes_audioElement = document.createElement('audio' );
		codes_audioElement.setAttribute('src', '<?php 
    echo  esc_url( CODES_PLUGIN_URL ) . 'assets/sound/glass.mp3' ;
    ?>' );
	</script>
	<?php 
}

add_action( 'admin_head', 'codes_audio_element' );