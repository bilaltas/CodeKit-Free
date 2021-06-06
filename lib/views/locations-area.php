<?php

/**
 *
 * The "Location" metabox area. (Vue JS)
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 * Register the "Location" metabox.
 */
function codes_create_location_box()
{
    // Location metabox.
    add_meta_box(
        'codes_location_box',
        // Unique ID.
        __( 'Location', 'custom-codes' ),
        // Box title.
        'codes_location_box_html',
        // Content callback, must be of type callable.
        'custom-code',
        // Post type.
        'side'
    );
    // Remove page attributes metabox update.
    remove_meta_box( 'pageparentdiv', 'custom-code', 'side' );
    // Remove slug metabox.
    remove_meta_box( 'slugdiv', 'custom-code', 'normal' );
}

add_action( 'add_meta_boxes', 'codes_create_location_box', 100 );
/**
 * Location metabox content.
 *
 * @param object $post Returns the global post object.
 */
function codes_location_box_html( $post )
{
    global  $post, $wp_roles, $wp_filesystem ;
    // Registered Language.
    $current_language = get_post_meta( $post->ID, '_codes_language', true );
    ?>

	<div id="codes_location" :class="{ loaded: initialized }" style="opacity: 0;">

		<label><input type="radio" name="location" v-model="location" value="frontend" checked> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-welcome-widgets-menus.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Frontend', 'custom-codes' );
    ?></label>


		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="page" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-page.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Pages', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>

		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="post" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-post.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Posts', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>

		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="post_type" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-post-type.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Post Types', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>

		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="term" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-category.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Categories / Terms', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>

		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="taxonomy" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-taxonomy.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Archives / Taxonomies', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>

		<label :disabled="!isPremium"><input type="radio" name="location" v-model="location" value="template" :disabled="!isPremium"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-layout.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Templates', 'custom-codes' );
    ?><span class="codes-pro-link" v-if="!isPremium"> (<a href="<?php 
    echo  esc_url( codes_fs()->get_upgrade_url() ) ;
    ?>" target="_blank">PRO</a>)</span></label>
		<?php 
    ?>


		<label><input type="radio" name="location" v-model="location" value="backend"> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-wordpress.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Backend', 'custom-codes' );
    ?></label>
		<div v-if="location == 'backend'">
			<b style="display: block; margin-top: 10px;"><i><?php 
    esc_html_e( 'Optional', 'custom-codes' );
    ?></i> - <?php 
    esc_html_e( 'Select specific role(s):', 'custom-codes' );
    ?></b>
			<select name="roles[]" v-model="adminRoles" style="width: 100%;" multiple>
				<?php 
    foreach ( $wp_roles->roles as $role => $role_details ) {
        ?>
				<option value="<?php 
        echo  esc_attr( $role ) ;
        ?>"><?php 
        echo  esc_html( translate_user_role( $role_details['name'] ) ) ;
        ?></option>
				<?php 
    }
    ?>
			</select>
			<p class="description" style="margin-bottom: 20px;"><?php 
    esc_html_e( 'If none of them selected, codes will be applied all roles.', 'custom-codes' );
    ?></p>
		</div>

		<label><input type="radio" name="location" v-model="location" value="login" checked> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-admin-network.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Login Screen', 'custom-codes' );
    ?></label>

		<label><input type="radio" name="location" v-model="location" value="everywhere" checked> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-globe.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Everywhere', 'custom-codes' );
    ?></label>

		<label><input type="radio" name="location" v-model="location" value="nowhere" checked> <?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-hidden.svg' ), codes_svg_args() ) ;
    ?> <?php 
    esc_html_e( 'Nowhere', 'custom-codes' );
    ?></label>

		<div class="editor-files" v-if="currentLang && currentLang.id == '<?php 
    echo  esc_js( $current_language ) ;
    ?>'">

			<hr>

			<p class="editor-file">

				<b><?php 
    esc_html_e( 'Current Editor File:', 'custom-codes' );
    ?></b> <br>
				<a :href="'<?php 
    echo  esc_url( CODES_FOLDER_URL ) ;
    ?>' + postID + '-' + activeEditor.id + '.' + currentLang.id" class="tooltip-not-contained left-tooltip" data-copied="<?php 
    esc_html_e( 'Copied!', 'custom-codes' );
    ?>" data-tooltip="<?php 
    esc_html_e( 'Click to Copy', 'custom-codes' );
    ?>">{{ postID }}-{{ activeEditor.id }}.{{ currentLang.id }}</a>

				<small v-if="currentLang.output === 'individual'">(<a :href="'<?php 
    echo  esc_url( CODES_FOLDER_URL ) ;
    ?>' + postID + '-' + activeEditor.id + '-output.' + currentLangGroup.extension" class="tooltip-not-contained bottom-tooltip" data-copied="<?php 
    esc_html_e( 'Copied!', 'custom-codes' );
    ?>" data-tooltip="<?php 
    esc_html_e( 'Click to Copy', 'custom-codes' );
    ?>" :data-copy="postID + '-' + activeEditor.id + '-output.' + currentLangGroup.extension"><?php 
    esc_html_e( 'Compiled', 'custom-codes' );
    ?></a>)</small>

			</p>

			<p class="output-file" v-if="currentLang.output === true">

				<b><?php 
    esc_html_e( 'Output File:', 'custom-codes' );
    ?></b> <br>
				<a :href="'<?php 
    echo  esc_url( CODES_FOLDER_URL ) ;
    ?>' + postID + '-' + currentLang.id + '-output.' + currentLang.id" class="tooltip-not-contained left-tooltip" data-copied="<?php 
    esc_html_e( 'Copied!', 'custom-codes' );
    ?>" data-tooltip="<?php 
    esc_html_e( 'Click to Copy', 'custom-codes' );
    ?>">{{ postID }}-{{ currentLang.id }}-output.{{ currentLang.id }}</a>

				<small v-if="currentLang.id != currentLangGroup.extension">(<a :href="'<?php 
    echo  esc_url( CODES_FOLDER_URL ) ;
    ?>' + postID + '-' + currentLang.id + '-output.' + currentLangGroup.extension" class="tooltip-not-contained bottom-tooltip" data-copied="<?php 
    esc_html_e( 'Copied!', 'custom-codes' );
    ?>" data-tooltip="<?php 
    esc_html_e( 'Click to Copy', 'custom-codes' );
    ?>" :data-copy="postID + '-' + currentLang.id + '-output.' + currentLangGroup.extension"><?php 
    esc_html_e( 'Compiled', 'custom-codes' );
    ?></a>)</small>

			</p>

		</div>

		<div class="order">

			<hr>

			<p>
				<b><?php 
    esc_html_e( 'Release Order:', 'custom-codes' );
    ?></b> <br>
				<input name="menu_order" type="text" size="4" id="menu_order" value="<?php 
    echo  esc_attr( $post->menu_order ) ;
    ?>">
			</p>

		</div>

	</div>

	<?php 
}
