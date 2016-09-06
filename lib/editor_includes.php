<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// CUSTOM CODES ADMIN STYLES AND SCRIPTS
function cc_load() {
	global $cc_result_level;

		// CODE MIRROR
		wp_enqueue_style ( 'cc-codemirror-css', plugin_dir_url( __FILE__ ) .'includes/codemirror/lib/codemirror.css' );
		wp_enqueue_script( 'cc-codemirror-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/lib/codemirror.js', array(), '20150913', true );


		// EMMET
		wp_enqueue_script( 'cc-emmet-js', plugin_dir_url( __FILE__ ) .'includes/emmet/dist/emmet.js', array(), '20150913', true );


		// MODES
		wp_enqueue_script( 'cc-codemirror-htmlmixed-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/htmlmixed/htmlmixed.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-xml-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/xml/xml.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-css-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/css/css.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-sass-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/sass/sass.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-js-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/javascript/javascript.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-php-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/php/php.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-codemirror-clike-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/mode/clike/clike.js', array(), '20150913', true );


		// THEMES
		wp_enqueue_style ( 'cc-monokai-css', plugin_dir_url( __FILE__ ) .'includes/codemirror/theme/monokai.css' );


		// ADDONS
		wp_enqueue_script( 'cc-active-line-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/selection/active-line.js', array(), '20150913', true );

		wp_enqueue_script( 'cc-closebrackets-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/edit/closebrackets.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-matchbrackets-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/edit/matchbrackets.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-trailingspace-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/edit/trailingspace.js', array(), '20150913', true );

		wp_enqueue_script( 'cc-foldcode-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/fold/foldcode.js', array(), '20150913', true );
		wp_enqueue_style ( 'cc-foldgutter-css', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/fold/foldgutter.css' );
		wp_enqueue_script( 'cc-foldgutter-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/fold/foldgutter.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-brace-fold-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/fold/brace-fold.js', array(), '20150913', true );

		wp_enqueue_script( 'cc-match-highlighter-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/search/match-highlighter.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-search-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/search/search.js', array(), '20150913', true );
		wp_enqueue_script( 'cc-searchcursor-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/search/searchcursor.js', array(), '20150913', true );

		wp_enqueue_script( 'cc-dialog-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/dialog/dialog.js', array(), '20150913', true );
		wp_enqueue_style ( 'cc-dialog-css', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/dialog/dialog.css' );

		wp_enqueue_script( 'cc-fullscreen-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/display/fullscreen.js', array(), '20150913', true );
		wp_enqueue_style ( 'cc-fullscreen-css', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/display/fullscreen.css' );

		wp_enqueue_script( 'cc-comment-js', plugin_dir_url( __FILE__ ) .'includes/codemirror/addon/comment/comment.js', array(), '20150913', true );


		wp_enqueue_style ( 'cc-font-awesome-css', plugin_dir_url( __FILE__ ) .'includes/font-awesome/css/font-awesome.min.css' );


		wp_enqueue_style ( 'cc-custom-codes-css', plugin_dir_url( __FILE__ ) .'css/custom_codes.css' );
		wp_enqueue_script( 'cc-custom-codes-js', plugin_dir_url( __FILE__ ) .'js/custom_codes.js', array('jquery'), '20150913', true );
		wp_localize_script( 'cc-custom-codes-js', 'cc_vars', array(
				'cc_nonce' => wp_create_nonce('cc-nonce'),
				'cc_plugin_dir_url' => plugin_dir_url( __FILE__ ),
				'cc_admin' => $cc_result_level
			)
		);

		wp_enqueue_script( 'cc-saver-js', plugin_dir_url( __FILE__ ) .'includes/mousetrap/mousetrap.min.js', array(), '20150913', false );

}
add_action( 'admin_enqueue_scripts', 'cc_load' );

?>