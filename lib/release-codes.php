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
$codes_posts = get_posts(
	array(
		'post_type'      => 'custom-code',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	)
);




/**
 * HTML, CSS, JS release.
 */
function codes_release() {
	global $codes_posts, $codes_langs, $codes_lang_groups, $wp_filesystem;

	foreach ( $codes_posts as $code_post ) {

		$language                 = get_post_meta( $code_post->ID, '_codes_language', true );
		$language_key             = array_search( $language, array_column( $codes_langs, 'id' ), true );
		$language_data            = $codes_langs[ $language_key ];
		$language_extension       = $language_data->id;
		$language_editors         = $language_data->editors;
		$language_compileable     = $language_data->output;
		$language_group           = $language_data->group;
		$language_group_key       = array_search( $language_group, array_column( $codes_lang_groups, 'id' ), true );
		$language_group_data      = $codes_lang_groups[ $language_group_key ];
		$language_group_extension = $language_group_data->extension;
		$save_count               = get_post_meta( $code_post->ID, '_codes_savecount', true );
		$location                 = get_post_meta( $code_post->ID, '_codes_location', true );
		$roles                    = get_post_meta( $code_post->ID, '_codes_adminroles', true );
		$user                     = wp_get_current_user();

		// CSS Outputs.
		if ( 'css' === $language_group_extension ) {

			$file_name      = "$code_post->ID-$language_extension-output";
			$file_full_name = "$file_name.$language_group_extension";
			$file_directory = CODES_FOLDER_DIR . $file_full_name;

			if ( file_exists( $file_directory ) ) {

				if ( 'frontend' === $location || 'everywhere' === $location ) {

					add_action(
						'wp_enqueue_scripts',
						function() use ( $file_name, $file_full_name, $save_count ) {
							wp_enqueue_style( "codes-$file_name", CODES_FOLDER_URL . $file_full_name, array(), $save_count );
						},
						99999
					);

				}

				if (
					( 'backend' === $location && ( count( $roles ) === 0 || count( $roles ) > 0 && array_intersect( $roles, $user->roles ) ) )
					|| 'everywhere' === $location
				) {

					add_action(
						'admin_enqueue_scripts',
						function() use ( $file_name, $file_full_name, $save_count ) {
							wp_enqueue_style( "codes-$file_name", CODES_FOLDER_URL . $file_full_name, array(), $save_count );
						},
						99999
					);

				}

				if ( 'login' === $location || 'everywhere' === $location ) {

					add_action(
						'login_enqueue_scripts',
						function() use ( $file_name, $file_full_name, $save_count ) {
							wp_enqueue_style( "codes-$file_name", CODES_FOLDER_URL . $file_full_name, array(), $save_count );
						},
						99999
					);

				}
			}
		} elseif ( 'js' === $language_group_extension ) { // JS Outputs.

			// Loop each editor.
			foreach ( $language_editors as $editor ) {

				$editor_id             = $editor->id;
				$editor_file_name      = "$code_post->ID-$editor_id";
				$editor_file_full_name = "$editor_file_name.$language_group_extension";
				$editor_location       = str_replace( $language_extension . '-', '', $editor_id );
				$file_directory        = CODES_FOLDER_DIR . $editor_file_full_name;

				if ( file_exists( $file_directory ) ) {

					if ( 'frontend' === $location || 'everywhere' === $location ) {

						if ( 'body-opening' === $editor_location ) {

							add_action(
								'wp_body_open',
								function() use ( $editor_file_full_name, $save_count, $editor_file_name ) {
									?>
									<script type="text/javascript" src="<?php echo esc_url( CODES_FOLDER_URL . "$editor_file_full_name?ver=$save_count" ); ?>" id="<?php echo esc_attr( "codes-$editor_file_name" ); ?>"></script>
									<?php
								}
							);

						} else {

							add_action(
								'wp_enqueue_scripts',
								function() use ( $editor_file_name, $editor_file_full_name, $save_count, $editor_location ) {
									wp_enqueue_script( "codes-$editor_file_name", CODES_FOLDER_URL . $editor_file_full_name, array( 'jquery' ), $save_count, 'body-closing' === $editor_location );
								},
								99999
							);

						}
					}

					if (
						(
							'backend' === $location
							&& ( count( $roles ) === 0 || count( $roles ) > 0 && array_intersect( $roles, $user->roles ) )
						)
						|| 'everywhere' === $location
					) {

						if ( 'body-opening' !== $editor_location ) {

							add_action(
								'admin_enqueue_scripts',
								function() use ( $editor_file_name, $editor_file_full_name, $save_count, $editor_location ) {
									wp_enqueue_script( "codes-$editor_file_name", CODES_FOLDER_URL . $editor_file_full_name, array( 'jquery' ), $save_count, 'body-closing' === $editor_location );
								},
								99999
							);

						}
					}

					if ( 'login' === $location || 'everywhere' === $location ) {

						if ( 'body-opening' !== $editor_location ) {

							add_action(
								'login_enqueue_scripts',
								function() use ( $editor_file_name, $editor_file_full_name, $save_count, $editor_location ) {
									wp_enqueue_script( "codes-$editor_file_name", CODES_FOLDER_URL . $editor_file_full_name, array( 'jquery' ), $save_count, 'body-closing' === $editor_location );
								},
								99999
							);

						}
					}
				}
			}
		} elseif ( '' === $language_group_extension ) { // HTML Outputs.

			// Loop each editor.
			foreach ( $language_editors as $editor ) {

				$editor_id             = $editor->id;
				$editor_file_name      = "$code_post->ID-$editor_id";
				$editor_file_full_name = "$editor_file_name.$language_group_extension";
				$editor_location       = str_replace( $language_extension . '-', '', $editor_id );
				$file_directory        = CODES_FOLDER_DIR . $editor_file_full_name;

				if ( file_exists( $file_directory ) ) {

					if ( 'frontend' === $location || 'everywhere' === $location ) {

						if ( 'body-opening' === $editor_location ) {

							add_action(
								'wp_body_open',
								function() use ( $file_directory, $wp_filesystem ) {
									echo $wp_filesystem->get_contents( $file_directory );
								}
							);

						} else {

							add_action(
								( 'head' === $editor_location ? 'wp_head' : 'wp_footer' ),
								function() use ( $file_directory, $wp_filesystem ) {
									echo $wp_filesystem->get_contents( $file_directory );
								}
							);

						}
					}

					if (
						( 'backend' === $location && ( count( $roles ) === 0 || count( $roles ) > 0 && array_intersect( $roles, $user->roles ) ) )
						|| 'everywhere' === $location
					) {

						if ( 'body-opening' !== $editor_location ) {

							add_action(
								( 'head' === $editor_location ? 'admin_head' : 'admin_footer' ),
								function() use ( $file_directory, $wp_filesystem ) {
									echo $wp_filesystem->get_contents( $file_directory );
								}
							);

						}
					}

					if ( 'login' === $location || 'everywhere' === $location ) {

						if ( 'body-opening' !== $editor_location ) {

							add_action(
								( 'head' === $editor_location ? 'login_head' : 'login_footer' ),
								function() use ( $file_directory, $wp_filesystem ) {
									echo $wp_filesystem->get_contents( $file_directory );
								}
							);

						}
					}
				}
			}
		}
	} // Codes Loop

}
add_action( 'init', 'codes_release', 99999 );




// PHP RELEASE.
foreach ( $codes_posts as $code_post ) {

	$language = get_post_meta( $code_post->ID, '_codes_language', true );
	if ( 'php' !== $language || isset( $_GET['custom-codes-saving'] ) ) {
		continue;
	}

	$language_key             = array_search( $language, array_column( $codes_langs, 'id' ), true );
	$language_data            = $codes_langs[ $language_key ];
	$language_extension       = $language_data->id;
	$language_editors         = $language_data->editors;
	$language_compileable     = $language_data->output;
	$language_group           = $language_data->group;
	$language_group_key       = array_search( $language_group, array_column( $codes_lang_groups, 'id' ), true );
	$language_group_data      = $codes_lang_groups[ $language_group_key ];
	$language_group_extension = $language_group_data->extension;
	$save_count               = get_post_meta( $code_post->ID, '_codes_savecount', true );
	$location                 = get_post_meta( $code_post->ID, '_codes_location', true );


	// Loop each editor.
	foreach ( $language_editors as $editor ) {

		$editor_id             = $editor->id;
		$editor_file_name      = "$code_post->ID-$editor_id";
		$editor_file_full_name = "$editor_file_name.$language_group_extension";
		$editor_location       = str_replace( $language_extension . '-', '', $editor_id );
		$file_directory        = CODES_FOLDER_DIR . $editor_file_full_name;

		if ( file_exists( $file_directory ) && 'default' === $editor_location && ! defined( 'DOING_AJAX' ) ) {


			if (
				( 'frontend' === $location && ! is_admin() )
				|| ( 'backend' === $location && is_admin() )
				|| ( 'login' === $location && codes_is_login_page() )
				|| 'everywhere' === $location
			) {

				try {
					require $file_directory;
				} catch ( Exception $e ) {
					if ( CODES_DEBUG ) {
						error_log( "Custom Codes - Caught exception on code #$editor_file_name: " . $e->getMessage() . "\n" );
					}
				}
			}
		}
	}
}
