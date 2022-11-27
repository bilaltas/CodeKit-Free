<?php

/**
 *
 * The single code (Editors) area.
 *
 * @since   2.0.0
 * @package Custom_Codes
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );
/**
 *
 * The editor area.
 */
function codes_editor_area()
{
    global  $post, $codes_langs, $wp_filesystem ;
    if ( !$post || 'custom-code' !== $post->post_type ) {
        return;
    }
    // Call JS and CSS files.
    wp_enqueue_script( 'codes_scripts' );
    wp_enqueue_style( 'codes_styles' );
    // Registered language.
    $current_language = get_post_meta( $post->ID, '_codes_language', true );
    ?>

	<div id="codes_editor_area" :class="{ loaded: initialized, fullscreen: fullscreen, loading: loading }" style="opacity: 0;">

		<div id="topbar">
			<div class="left">

				<select name="language" v-model="selectedLangID" @change="switchLang">
					<option value="" selected disabled><?php 
    esc_html_e( 'SELECT EDITOR TYPE', 'custom-codes' );
    ?></option>
					<optgroup :label="langGroup.name" v-for="langGroup in langGroups" :key="langGroup.id">
						<option :value="lang.id" v-for="lang in langs.filter(lang => lang.group == langGroup.id)" :key="lang.id" :disabled="lang.pro && ! isPremium">{{ lang.name + (lang.pro && ! isPremium ? ' (PRO)' : '') }}</option>
					</optgroup>
				</select>
				<input type="hidden" name="language-group" :value="currentLang.group" v-if="currentLang">

				<div class="indicators">

					<span class="tooltip-not-contained bottom-tooltip" v-if="loading" data-tooltip="<?php 
    esc_html_e( 'Saving...', 'custom-codes' );
    ?>">
						<span class="dashicons dashicons-admin-generic spin"></span>
					</span>

					<span class="dashicons dashicons-no bottom-tooltip tooltip-sub" style="color: red;" data-tooltip v-if="Object.keys(errors).length">
						<span class="tooltip">
							<ul style="margin: 0px;list-style: disc;padding-left: 20px;line-height: normal;">
								<li v-for="(error, index) in errors" :key="index">{{ error }} ({{ index }})</li>
							</ul>

							{{ processTime }}
						</span>
					</span>
					<span class="dashicons dashicons-yes bottom-tooltip" style="color: white;" :data-tooltip="'Last Process Time: ' + processTime" v-else-if="processTime && !hasUnsaved"></span>

					<span class="indicator unsaved bottom-tooltip" data-tooltip="Unsaved" v-if="hasUnsaved">&bullet;</span>
				</div>

			</div>
			<div class="center" v-if="fullscreen">
				{{ currentTitle }}
			</div>
			<div class="right">

				<a href="#" @click.prevent class="tooltip-sub tooltip-not-contained bottom-tooltip tooltip-focus" :class="{ 'left-tooltip': fullscreen }" data-tooltip>
					<span class="tooltip shortcuts">
						<strong class="title"><?php 
    esc_html_e( 'SHORTCUTS', 'custom-codes' );
    ?></strong> <br>
						<div class="options">
							<b><?php 
    esc_html_e( 'Save', 'custom-codes' );
    ?>: </b> Command/Ctrl + S <br>
							<b><?php 
    esc_html_e( 'Find', 'custom-codes' );
    ?>: </b> Command/Ctrl + F <br>
							<b><?php 
    esc_html_e( 'Find & Replace', 'custom-codes' );
    ?>: </b> Command + Option + F / Shift + Ctrl + F <br>
							<b><?php 
    esc_html_e( 'Multiple Lines', 'custom-codes' );
    ?>: </b> <?php 
    esc_html_e( 'Option/Alt + Click and Drag', 'custom-codes' );
    ?> <br>
							<b><?php 
    esc_html_e( 'Add Multi Cursor', 'custom-codes' );
    ?>: </b> <?php 
    esc_html_e( 'Command/Ctrl + Click', 'custom-codes' );
    ?> <br>
							<b><?php 
    esc_html_e( 'Comment the Line', 'custom-codes' );
    ?>: </b> Command/Ctrl + 7 <br>
							<b><?php 
    esc_html_e( 'Tidy Codes', 'custom-codes' );
    ?>: </b> Command/Ctrl + 8 <br>
							<b><?php 
    esc_html_e( 'Toggle Fullscreen Mode', 'custom-codes' );
    ?>: </b> Command/Ctrl + G <br>
							<b><?php 
    esc_html_e( 'Space Hierarchy', 'custom-codes' );
    ?>: </b> <?php 
    esc_html_e( '(Select) + Shift + Tab', 'custom-codes' );
    ?> <br>
							<b><?php 
    esc_html_e( 'Emmet Abbreviations', 'custom-codes' );
    ?>: </b> <?php 
    esc_html_e( 'Write Abbs. + Tab', 'custom-codes' );
    ?> <br>

							<?php 
    ?>
						</div>
					</span>
					<?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-question.svg' ), codes_svg_args() ) ;
    ?>
				</a>

				<div class="subtabs" v-if="(activeEditor && currentLangGroupID != 'style') || ( activeEditor && currentLangGroupID == 'style' && useBreakpoints)">
					<button
						v-for="editor in currentLang.editors"
						:class="{
							active: activeEditor.id == editor.id,
							saved: isEditorSaved('editor-' + editor.id),
							disabled: editor.id.endsWith('-body-opening') && (location == 'backend' || location == 'login'),
							hidden: editor.id.endsWith('-default')
						}"
						@click.prevent="switchEditor(editor.id)"
						class="tooltip-sub tooltip-not-contained bottom-tooltip tooltip-delay"
						data-tooltip
					>
						<span class="tooltip" style="text-align: center;" v-if="editor.description">
							{{ editor.description }}
							<div v-if="currentLangGroupID == 'style'">{{ getMediaQueryText(editor.id) }}</div>
						</span>
						<span class="dashicons dashicons-admin-site-alt" v-if="getMediaQuery(editor.id) == 'global'"></span>
						<span class="dashicons" :class="editor.icon" v-else-if="editor.icon"></span>
						<span class="label" :class="{'has-icon' : editor.icon}">{{ editor.name }}</span>
					</button>
				</div>

				<label class="switch-vertical tooltip-not-contained left-tooltip" v-if="currentLangGroupID == 'style'" :data-tooltip="(useBreakpoints ? 'Hide' : 'Show') + ' Breakpoint Editors'">
					<input type="checkbox" v-model="useBreakpoints" name="useBreakpoints" id="">
					<div class="switch-fill"></div>
				</label>

				<a href="#" @click.prevent="toggleFullScreen" v-if="!fullscreen" class="tooltip-not-contained left-tooltip" data-tooltip="Toggle Fullscreen (Cmd/Ctrl G)"><?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-fullscreen.svg' ), codes_svg_args() ) ;
    ?></a>
				<a href="#" @click.prevent="toggleFullScreen" v-if="fullscreen" class="tooltip-not-contained left-tooltip" data-tooltip="Toggle Fullscreen (Cmd/Ctrl G)"><?php 
    echo  wp_kses( $wp_filesystem->get_contents( CODES_PLUGIN_DIR . '/assets/image/icon-fullscreen-exit.svg' ), codes_svg_args() ) ;
    ?></a>

			</div>
		</div>

		<div class="editors" :class="{ spacing: putEditorHtmlTags || putEditorMediaQueries, larger: putEditorHtmlTags && putEditorMediaQueries }" :current-lang="currentLang.id" v-if="currentLang">

			<div class="editor-addition before CodeMirror" :class="'cm-s-'+ theme" v-show="putEditorHtmlTags || putEditorMediaQueries">
				<span><span v-if="putEditorHtmlTags">&lt;{{ currentLangGroupID }} type=&quot;{{ currentLangGroup.mode }}&quot;&gt;</span>
				<span v-if="putEditorMediaQueries"><br v-if="putEditorHtmlTags"> {{ getMediaQueryText(activeEditor.id) }} {</span></span>
			</div>

			<?php 
    
    if ( '' !== $current_language ) {
        ?>
				<?php 
        $language_key = array_search( $current_language, array_column( $codes_langs, 'id' ), true );
        $language_data = $codes_langs[$language_key];
        $language_editors = $language_data->editors;
        $language_extension = $language_data->id;
        foreach ( $language_editors as $editor ) {
            $editor_id = $editor->id;
            $editor_name = $editor->name;
            $editor_rotation = '';
            
            if ( substr( $editor_id, -2 ) === '-l' ) {
                $editor_rotation = ' (Landscape)';
            } elseif ( substr( $editor_id, -2 ) === '-p' ) {
                $editor_rotation = ' (Portrait)';
            }
            
            $editor_file_name = "{$post->ID}-{$editor_id}.{$language_extension}";
            $file_directory = CODES_FOLDER_DIR . $editor_file_name;
            $editor_exists = file_exists( $file_directory );
            // Try fixing permission first.
            if ( $editor_exists && !codes_is_writable( $file_directory ) ) {
                chmod( $file_directory, 0644 );
            }
            $editor_writable = $editor_exists && CODES_FOLDER_EXECUTABLE && codes_is_writable( $file_directory );
            if ( !$editor_exists ) {
                $editor_writable = CODES_FOLDER_EXECUTABLE && CODES_FOLDER_WRITABLE;
            }
            $editor_readable = $editor_exists && CODES_FOLDER_EXECUTABLE && CODES_FOLDER_READABLE && codes_is_readable( $file_directory );
            $editor_content = ( $editor_exists ? $wp_filesystem->get_contents( $file_directory ) : '' );
            $editor_placeholder = ( !empty($editor->placeholder) ? $editor->placeholder : '/* ' . sprintf(
                /* translators: 1: Editor Name 2: Language name */
                __( 'Write your custom %1$s %2$s', 'custom-codes' ),
                $editor_name . $editor_rotation,
                $language_data->name
            ) . ' */' );
            if ( (!$editor_exists || empty($editor_content)) && !$editor_writable ) {
                $editor_content = __( 'Insufficient permissions to write this editor', 'custom-codes' );
            }
            if ( $editor_exists && !$editor_readable ) {
                $editor_content = __( 'Editor file might exist but content is not readable and writable', 'custom-codes' );
            }
            ?>

			<div class="editor <?php 
            echo  esc_attr( $editor_id ) ;
            ?>" v-show="'<?php 
            echo  esc_js( $editor_id ) ;
            ?>' == activeEditor.id && !outputOpen" :style="'font-size: ' + fontSize + 'px;'" <?php 
            echo  ( $editor_exists ? 'exists' : '' ) ;
            ?> <?php 
            echo  ( $editor_writable ? 'writable' : '' ) ;
            ?>>

				<div class="codes-debug">(Editor Exists: <?php 
            echo  ( $editor_exists ? 'Yes' : 'No' ) ;
            ?> | Readable: <?php 
            echo  ( codes_is_readable( $file_directory ) ? 'Yes' : 'No' ) ;
            ?> | Writable: <?php 
            echo  ( codes_is_writable( $file_directory ) ? 'Yes' : 'No' ) ;
            ?> | CHMOD: <?php 
            echo  esc_html( codes_chmod_check( $file_directory ) ) ;
            ?>)</div>

				<div class="codes-debug">(Codes Exists: <?php 
            echo  ( $editor_exists ? 'Yes' : 'No' ) ;
            ?> | Readable: <?php 
            echo  ( $editor_readable ? 'Yes' : 'No' ) ;
            ?> | Writable: <?php 
            echo  ( $editor_writable ? 'Yes' : 'No' ) ;
            ?> | CHMOD: <?php 
            echo  esc_html( codes_chmod_check( $file_directory ) ) ;
            ?>)</div>

				<textarea id="editor-<?php 
            echo  esc_js( $editor_id ) ;
            ?>" name="editor-<?php 
            echo  esc_js( $editor_id ) ;
            ?>" placeholder="<?php 
            echo  esc_attr( $editor_placeholder ) ;
            ?>" <?php 
            echo  ( !$editor_writable ? 'readonly' : '' ) ;
            ?> disabled v-pre><?php 
            echo  esc_textarea( $editor_content ) ;
            ?></textarea>
			</div>

					<?php 
        }
        ?>

			<?php 
    } else {
        ?>
			<div v-for="editor in currentLang.editors" class="editor" v-show="editor.id == activeEditor.id && !outputOpen" :style="'font-size: ' + fontSize + 'px;'" writable>
				<textarea :id="'editor-' + editor.id" :name="'editor-' + editor.id" :placeholder="editor.placeholder ? editor.placeholder : '/* Write your custom '+ editor.name +' '+ currentLang.name +' */'"></textarea>
			</div>
			<?php 
    }
    
    ?>

			<div class="editor output" v-if="currentLang.output" v-show="outputOpen" :style="'font-size: ' + fontSize + 'px;'">
				<textarea :id="'output-' + currentLang.id" :placeholder="currentLang.name + ' output loading...'" readonly disabled></textarea>
			</div>


			<div class="editor-addition after CodeMirror" :class="'cm-s-'+ theme" v-if="putEditorHtmlTags || putEditorMediaQueries">
				<span><span v-if="putEditorMediaQueries">}</span> <span v-if="putEditorHtmlTags">&lt;/{{ currentLangGroupID }}&gt;</span></span>
			</div>

		</div>

		<div class="lang-notice" v-if="currentLang && currentLang.id != postLanguage && postLanguage != ''">
			<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.90321 7.29677C1.90321 10.341 4.11041 12.4147 6.58893 12.8439C6.87255 12.893 7.06266 13.1627 7.01355 13.4464C6.96444 13.73 6.69471 13.9201 6.41109 13.871C3.49942 13.3668 0.86084 10.9127 0.86084 7.29677C0.860839 5.76009 1.55996 4.55245 2.37639 3.63377C2.96124 2.97568 3.63034 2.44135 4.16846 2.03202L2.53205 2.03202C2.25591 2.03202 2.03205 1.80816 2.03205 1.53202C2.03205 1.25588 2.25591 1.03202 2.53205 1.03202L5.53205 1.03202C5.80819 1.03202 6.03205 1.25588 6.03205 1.53202L6.03205 4.53202C6.03205 4.80816 5.80819 5.03202 5.53205 5.03202C5.25591 5.03202 5.03205 4.80816 5.03205 4.53202L5.03205 2.68645L5.03054 2.68759L5.03045 2.68766L5.03044 2.68767L5.03043 2.68767C4.45896 3.11868 3.76059 3.64538 3.15554 4.3262C2.44102 5.13021 1.90321 6.10154 1.90321 7.29677ZM13.0109 7.70321C13.0109 4.69115 10.8505 2.6296 8.40384 2.17029C8.12093 2.11718 7.93465 1.84479 7.98776 1.56188C8.04087 1.27898 8.31326 1.0927 8.59616 1.14581C11.4704 1.68541 14.0532 4.12605 14.0532 7.70321C14.0532 9.23988 13.3541 10.4475 12.5377 11.3662C11.9528 12.0243 11.2837 12.5586 10.7456 12.968L12.3821 12.968C12.6582 12.968 12.8821 13.1918 12.8821 13.468C12.8821 13.7441 12.6582 13.968 12.3821 13.968L9.38205 13.968C9.10591 13.968 8.88205 13.7441 8.88205 13.468L8.88205 10.468C8.88205 10.1918 9.10591 9.96796 9.38205 9.96796C9.65819 9.96796 9.88205 10.1918 9.88205 10.468L9.88205 12.3135L9.88362 12.3123C10.4551 11.8813 11.1535 11.3546 11.7585 10.6738C12.4731 9.86976 13.0109 8.89844 13.0109 7.70321Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
			<?php 
    esc_html_e( 'Please click "Update" to confirm the new language.', 'custom-codes' );
    ?>
		</div>
		<input type="hidden" id="ajax-saver" name="codes_doing_ajax" :value="1" v-else-if="ajaxSave && currentLang && postName != '' && postStatus == 'publish'">

		<div id="bottombar" v-if="(currentLang && currentLang.id == '<?php 
    echo  esc_js( $current_language ) ;
    ?>') || ('<?php 
    echo  esc_js( $current_language ) ;
    ?>' == '' && currentLang)">
			<div class="left">

				<select name="theme" v-model="theme" @change="switchTheme">
					<option value="dark" selected><?php 
    esc_html_e( 'Dark Theme', 'custom-codes' );
    ?></option>
					<option value="default"><?php 
    esc_html_e( 'Light Theme', 'custom-codes' );
    ?></option>
					<option value="monokai">Monokai</option>
					<option value="mdn-like">MDN</option>
					<option value="neo">Neo</option>
				</select>

				<label>
					<?php 
    esc_html_e( 'Font Size:', 'custom-codes' );
    ?>
					<select name="font-size" v-model="fontSize">
						<option value="10">10px</option>
						<option value="11">11px</option>
						<option value="12">12px</option>
						<option value="13">13px</option>
						<option value="14" selected>14px</option>
						<option value="15">15px</option>
						<option value="16">16px</option>
						<option value="17">17px</option>
						<option value="18">18px</option>
						<option value="19">19px</option>
						<option value="20">20px</option>
					</select>
				</label>

				<label style="text-transform: capitalize;">
					{{ indentType }}s:
					<select name="indent" v-model="indent" @change="switchIndent" style="width: 45px;">
						<optgroup label="Space">
							<option value="space-2">2</option>
							<option value="space-3">3</option>
							<option value="space-4" selected>4</option>
							<option value="space-5">5</option>
							<option value="space-6">6</option>
							<option value="space-7">7</option>
							<option value="space-8">8</option>
						</optgroup>
						<optgroup label="Tab">
							<option value="tab-2">2</option>
							<option value="tab-3">3</option>
							<option value="tab-4">4</option>
							<option value="tab-5">5</option>
							<option value="tab-6">6</option>
							<option value="tab-7">7</option>
							<option value="tab-8">8</option>
						</optgroup>
					</select>
				</label>

			</div>
			<div class="right">

				<span class="last-edit">
					{{ lastEditedText }}
				</span>
				<button @click.prevent="toggleOutput" :class="{ active: outputOpen }" v-if="currentLang.output && currentLang && currentLang.id == '<?php 
    echo  esc_js( $current_language ) ;
    ?>'"><?php 
    esc_html_e( 'OUTPUT', 'custom-codes' );
    ?></button>
				<button @click.prevent="jQuery('#publish').trigger('click')" class="save tooltip-not-contained" data-tooltip="Cmd/Ctrl + S" :disabled="loading ? true : null" v-if="fullscreen"><?php 
    esc_html_e( 'SAVE', 'custom-codes' );
    ?></button>

			</div>
		</div>

	</div>

	<?php 
}

add_action( 'edit_form_after_title', 'codes_editor_area' );