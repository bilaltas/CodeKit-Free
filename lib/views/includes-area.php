<?php

/**
 *
 * The "Includes" metabox area. (Vue JS)
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Register the "Includes" metabox.
 */
function codes_create_includes_box()
{
    global  $post ;
    // Registered Language.
    $current_language = get_post_meta( $post->ID, '_codes_language', true );
    if ( empty($current_language) ) {
        return;
    }
    $pro_link = ( !codes_fs()->is_premium() ? '<a href="' . esc_url( codes_fs()->get_upgrade_url() ) . '" target="_blank"><b>' . __( 'PRO Feature', 'custom-codes' ) . '</b></a>' : '' );
    add_meta_box(
        'codes_includes_box',
        // Unique ID.
        __( 'Includes', 'custom-codes' ) . $pro_link,
        // Box title.
        'codes_includes_box_html',
        // Content callback, must be of type callable.
        'custom-code'
    );
}

add_action( 'add_meta_boxes', 'codes_create_includes_box', 100 );
/**
 * Includes metabox content.
 *
 * @param object $post Returns the global post object.
 */
function codes_includes_box_html( $post )
{
    global  $codes_posts, $codes_langs ;
    // Registered Language.
    $current_language = get_post_meta( $post->ID, '_codes_language', true );
    ?>

	<div id="codes_includes" :class="{ loaded: initialized }" style="opacity: 0;" v-if="(currentLang && currentLang.id == '<?php 
    echo  esc_js( $current_language ) ;
    ?>') || ('<?php 
    echo  esc_js( $current_language ) ;
    ?>' == '' && currentLang)">

		<p><?php 
    esc_html_e( 'You can simply include a file or URL to this code instead of writing snippets inside of the editor.', 'custom-codes' );
    ?></p>

		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
				<tr>
					<th scope="col" class="column-primary" width="180">
						<span><?php 
    esc_html_e( 'Type', 'custom-codes' );
    ?></span>
					</th>
					<th scope="col" class="column-primary">
						<span><?php 
    esc_html_e( 'Code or URL', 'custom-codes' );
    ?></span>
					</th>
					<th scope="col" class="column-postss">
						<span><?php 
    esc_html_e( 'Editor', 'custom-codes' );
    ?></span>
					</th>
					<th scope="col" class="column-posts">
						<span><?php 
    esc_html_e( 'Placement', 'custom-codes' );
    ?></span>
					</th>
					<th scope="col" class="column-posts num">
						<span><?php 
    esc_html_e( 'Order', 'custom-codes' );
    ?></span>
					</th>
					<th scope="col" class="column-posts">
						<span class="screen-reader-text"><?php 
    esc_html_e( 'Delete', 'custom-codes' );
    ?></span>
					</th>
			</thead>

			<tbody>
				<?php 
    ?>
				<tr v-if="! includes.length">
					<td colspan="6"><?php 
    esc_html_e( 'No file or URL has been included yet.', 'custom-codes' );
    ?></td>
				</tr>
			</tbody>

		</table>

		<p>
			<?php 
    ?>
				<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank" class="button tooltip-not-contained dark-tooltip" :data-tooltip="!isPremium ? 'Click here to upgrade' : null"> <?php 
    esc_html_e( '+ Include a File or URL', 'custom-codes' );
    ?></a>
			<?php 
    ?>
			<span class="codes-pro-link" v-if="!isPremium"><a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank"><b><?php 
    esc_html_e( 'Upgrade Now', 'custom-codes' );
    ?></b></a></span>
		</p>

	</div>

	<div v-else>
		...
	</div>

	<?php 
}
