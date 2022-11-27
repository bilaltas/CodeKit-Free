<?php
/**
 *
 * The "Description" metabox area. (Vue JS)
 *
 * @since   2.0.0
 * @package Custom_Codes
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


/**
 * Register the "Description" metabox.
 */
function codes_create_description_box() {
	add_meta_box(
		'codes_description_box',             // Unique ID.
		__( 'Description', 'custom-codes' ), // Box title.
		'codes_description_box_html',        // Content callback, must be of type callable.
		'custom-code',                       // Post type.
		'normal',
		'core'
	);

}
add_action( 'add_meta_boxes', 'codes_create_description_box' );


/**
 * Description metabox content.
 *
 * @param object $post Returns the global post object.
 */
function codes_description_box_html( $post ) {
	?>
	<label class="screen-reader-text" for="excerpt"><?php esc_html_e( 'The code purpose', 'custom-codes' ); ?></label>
	<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt" placeholder="<?php esc_html_e( 'The code purpose', 'custom-codes' ); ?>"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
	<?php
}
