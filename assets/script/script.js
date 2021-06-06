var customCodes;

(function ($) {
	'use strict';


	// Initialize VueJS
	customCodes = new Vue({
		el: '#post',
		data: {
			initialized: true,
			loading: false,
			fullscreen: false,

			langGroups: customCodesData.langGroups,
			langs: customCodesData.langs,

			isPremium: customCodesData.isPremium,
			isPremiumOnly: customCodesData.isPremiumOnly,

			postID: customCodesData.postID,
			postName: customCodesData.postName,
			postLanguage: customCodesData.postLanguage,
			currentLangID: customCodesData.postLanguage,
			selectedLangID: customCodesData.postLanguage,
			activeEditorID: "",
			outputOpen: false,
			useBreakpoints: parseInt(customCodesData.postUseBreakpoints),

			location: customCodesData.postLocation,
			adminRoles: customCodesData.postRoles,

			includes: customCodesData.postIncludes || [],

			

			theme: customCodesData.userTheme,
			fontSize: customCodesData.userFontSize,
			indent: customCodesData.userIndent,

			editors: {},
			savedVals: {},
			editorSaved: {},
			saveCount: parseInt(customCodesData.saveCount),
			currentTitle: document.getElementById('title').value,
			currentTitleTag: document.getElementsByTagName('title')[0].innerText,

			ajaxSave: parseInt(customCodesData.ajaxSave),
			errors: {},
			lastEditedText: customCodesData.lastEditedText,
			processTime: null
		},
		computed: {
			currentLang() {

				if (this.currentLangID == "") return null;
				return this.langs.find(lang => lang.id == this.currentLangID);

			},
			currentLangGroup() {

				if (this.currentLangID == "") return null;
				return this.langGroups.find(langGroup => langGroup.id == this.currentLang.group);

			},
			currentLangGroupID() {

				if (!this.currentLangGroup || typeof this.currentLangGroup.id === "undefined") return "";
				return this.currentLangGroup.id;

			},
			activeEditor() {

				// If no language selected
				if (!this.currentLang) return null;

				// Find the active editor
				let editorFound = this.currentLang.editors.find(editor => editor.id == this.activeEditorID);

				// If editor not found
				if (typeof editorFound === "undefined") {

					if (customCodesData.initialStyleTab == "global" && this.currentLangGroup.id == "style") {

						editorFound = this.currentLang.editors.find(editor => this.getMediaQuery(editor.id) == "global");
						if (typeof editorFound === "undefined") editorFound = this.currentLang.editors[0];

					} else {

						editorFound = this.currentLang.editors[0];

					}


				}

				// If breakpoints are disabled
				if (this.currentLangGroup.id == "style" && !this.useBreakpoints) {

					editorFound = this.currentLang.editors.find(editor => editor.id.endsWith('-default'));

				}

				// Return the editor
				return editorFound;

			},
			indentType() {
				return this.indent.split('-')[0];
			},
			indentSize() {
				return this.indent.split('-')[1];
			},
			hasUnsaved() {

				let hasUnsaved = null;
				Object.values(this.editorSaved).forEach(saved => {
					if (!saved) hasUnsaved = true;
				});

				// Prevent closing window
				window.onbeforeunload = () => hasUnsaved;

				// Update the window title
				document.getElementsByTagName('title')[0].innerText = hasUnsaved ? 'UNSAVED - ' + this.currentTitleTag : this.currentTitleTag;

				return hasUnsaved;

			},
			putEditorHtmlTags() {
				return this.currentLang.id == 'css' || this.currentLang.id == 'js';
			},
			putEditorMediaQueries() {
				return this.currentLangGroupID == 'style' && this.getMediaQuery(this.activeEditor.id) != 'global';
			},

			
		},
		methods: {
			switchEditor(editorID) {
				this.activeEditorID = editorID;
				this.outputOpen = false;

				// Focus the switched editor
				this.$nextTick(() => {
					this.editors["editor-" + editorID].focus();
				});

			},
			switchLang() {

				var from = this.currentLangID;
				var to = this.selectedLangID;

				// Current Language Editors Values
				var edited = false;
				if (this.editors) {

					Object.values(this.editors).forEach(editor => {
						//console.log('VALUE: ', editor.getValue());
						if (editor.getValue() && !edited) edited = true;
					});

				}

				if (from == "" || !edited || confirm('Are you sure you want to change the language of this custom code?')) {

					console.log('CHANGED TO: ', to);

					// Destroy current editors
					this.destroyEditors();

					// Change the lang
					this.outputOpen = false;
					this.currentLangID = to;

					// Initialize New editors
					this.initializeEditors();

				} else {

					console.log('REVERT TO: ', from);
					this.selectedLangID = from;

				}

			},
			switchTheme() {

				Object.values(this.editors).forEach(editor => {
					editor.setOption("theme", this.theme);
				});

			},
			switchIndent() {
				var indentWithTabs = this.indentType == "tab";

				Object.values(this.editors).forEach(editor => {
					editor.setOption("indentWithTabs", indentWithTabs);
					editor.setOption("indentUnit", this.indentSize);
				});

			},
			async toggleOutput(event, forceRenew = false) {
				if (!forceRenew) this.outputOpen = !this.outputOpen;

				// If opening the output
				if (this.outputOpen || forceRenew) {

					// Empty the current value
					this.editors["output-" + this.currentLang.id].setValue("");

					// Fetch the output content
					let outputUnique = this.currentLang.output === "individual" ? this.activeEditor.id : this.currentLang.id;
					let outputFileUrl = customCodesData.custom_codes_uri + customCodesData.postID + "-" + outputUnique + "-output." + this.currentLangGroup.extension + "?v=" + this.saveCount;
					let output = await this.fetchData(outputFileUrl);
					if (output == "") output = "/* No content */";
					this.editors["output-" + this.currentLang.id].setValue(output);
					this.editors["output-" + this.currentLang.id].refresh();
				}

			},
			toggleFullScreen() {
				this.fullscreen = !this.fullscreen;
			},
			initializeEditors() {

				let codeMirrorSettings = {
					mode: this.currentLang.mode,
					lineNumbers: true,
					styleActiveLine: true,
					autoCloseBrackets: true,
					matchBrackets: true,
					indentWithTabs: this.indentType == "tab",
					indentUnit: 4,
					tabSize: this.indentSize,
					showTrailingSpace: true,
					autoRefresh: true,
					autoFocus: true,
					theme: this.theme,
					extraKeys: {
						"Cmd-F": "findPersistent",
						"Ctrl-F": "findPersistent",
						"Cmd-G": (cm) => false,
						"Ctrl-G": (cm) => false,
						"Cmd-7": function (cm) {
							cm.toggleComment({ fullLines: false });
						},
						"Ctrl-7": function (cm) {
							cm.toggleComment({ fullLines: false });
						},
						"Cmd-8": this.autoFormatAll,
						"Ctrl-8": this.autoFormatAll,
					}
				};

				// Emmet
				if (parseInt(customCodesData.useEmmet)) {

					codeMirrorSettings.emmet = {
						mark: true,
						markTagPairs: true,
						preview: false,
						previewOpenTag: false,
						config: {
							markup: {
								snippets: {
									'foo': 'ul.nav>li'
								}
							}
						}
					};

					codeMirrorSettings.extraKeys = {
						...codeMirrorSettings.extraKeys,
						'Tab': 'emmetExpandAbbreviation',
						'Esc': 'emmetResetAbbreviation',
						'Enter': 'emmetInsertLineBreak',
						'Ctrl-E': 'emmetExpandAbbreviationAll',
						'Ctrl-Space': 'emmetCaptureAbbreviation',
						'Ctrl-.': 'emmetEnterAbbreviationMode',
						'Ctrl-W': 'emmetWrapWithAbbreviation',
						'Cmd-D': 'emmetBalance',
						'Ctrl-D': 'emmetBalanceInward',
						'Cmd-/': 'emmetToggleComment',
						'Cmd-Y': 'emmetEvaluateMath',
						'Ctrl-Left': 'emmetGoToPreviousEditPoint',
						'Ctrl-Right': 'emmetGoToNextEditPoint',
						'Ctrl-P': 'emmetGoToTagPair',
						'Ctrl-Up': 'emmetIncrementNumber1',
						'Alt-Up': 'emmetIncrementNumber01',
						'Ctrl-Alt-Up': 'emmetIncrementNumber10',
						'Ctrl-Down': 'emmetDecrementNumber1',
						'Alt-Down': 'emmetDecrementNumber01',
						'Ctrl-Alt-Down': 'emmetDecrementNumber10',
						'Ctrl-\'': 'emmetRemoveTag',
						'Shift-Ctrl-\'': 'emmetSplitJoinTag',
						'Shift-Ctrl-Right': 'emmetSelectNextItem',
						'Shift-Ctrl-Left': 'emmetSelectPreviousItem'
					};

				}

				

				this.$nextTick(() => {

					Array.prototype.forEach.call(this.currentLang.editors, editor => {

						const editorID = "editor-" + editor.id;
						const editorElement = document.getElementById(editorID);

						if (editorElement) {

							const editorReadOnly = editorElement.hasAttribute('readonly');
							codeMirrorSettings.readOnly = editorReadOnly;

							this.editors[editorID] = CodeMirror.fromTextArea(document.getElementById(editorID), codeMirrorSettings);
							this.savedVals[editorID] = this.editors[editorID].getValue();
							//console.log('EDITOR INITIALIZED: ', editorID);

							this.editors[editorID].on('change', editor => {

								this.$set(this.editorSaved, editorID, this.savedVals[editorID] == editor.getValue());
								editor.save();

							});

						}

					});

					// Also initialize the output
					if (this.currentLang.output) {

						const outputID = "output-" + this.currentLang.id;
						codeMirrorSettings.readOnly = true;
						codeMirrorSettings.mode = this.currentLangGroup.mode;
						if (document.getElementById(outputID)) {

							this.editors[outputID] = CodeMirror.fromTextArea(document.getElementById(outputID), codeMirrorSettings);
							//console.log('OUTPUT INITIALIZED: ', outputID, codeMirrorSettings.mode);

						}

					}

				});

			},
			autoFormatAll(cm) {
				CodeMirror.commands["selectAll"](cm);
				cm.autoFormatRange(cm.getCursor(true), cm.getCursor(false));
				cm.setCursor(0);
			},
			destroyEditors() {

				Object.values(this.editors).forEach(editor => {
					editor.toTextArea();
				});

				this.editors = {};

			},
			disableEditors() {

				if (!Object.values(this.editors).length) return;

				Object.values(this.editors).forEach(editor => {
					editor.setOption("readOnly", true);
				});

			},
			enableEditors() {

				if (!Object.values(this.editors).length) return;

				Object.values(this.editors).forEach(editor => {
					var isReadOnly = editor.getTextArea().hasAttribute('readonly');
					editor.setOption("readOnly", isReadOnly);
				});

			},
			async fetchData(url) {

				function timeout(ms, promise) {
					return new Promise(function (resolve, reject) {
						setTimeout(function () {
							reject(new Error("timeout"))
						}, ms)
						promise.then(resolve, reject)
					})
				}

				return timeout(5000, fetch(url)).then(response => {
					if (!response.ok) {
						throw new Error("HTTP error " + response.status);
					}
					return response.text();
				}).then(text => {
					return text;
					//console.log('RESPONSE DATA: ', text);
				}).catch(function (error) {
					return "";
				});

			},
			decodeHtml(html) {
				var txt = document.createElement("textarea");
				txt.innerHTML = html;
				return txt.value;
			},
			isEditorSaved(editorID) {
				return typeof this.editorSaved[editorID] === "undefined" || this.editorSaved[editorID];
			},
			editorActive(editorID) {
				if (!this.ajaxSave) return null;
				return editorID === this.activeEditor.id ? null : true;
			},
			updateSavedStatus(editorID) {
				this.savedVals[editorID] = this.editors[editorID].getValue();
				this.$set(this.editorSaved, editorID, true);
			},
			getMediaQuery(editorID) {
				if (this.currentLangGroup.id != 'style') return "";
				var query = customCodesData['query-' + editorID.replace(this.currentLang.id + '-', '')];
				if (query == "" || typeof query === "undefined") return "global";

				return query;
			},
			getMediaQueryText(editorID) {
				if (this.currentLangGroup.id != 'style') return "";
				var query = this.getMediaQuery(editorID);
				if (query == "global") return "No media query";

				return query;
			},
			modeSaving() {

				// Temporarily disable the editors
				this.disableEditors();

				// Update the window title
				$('title').text('SAVING... - ' + this.currentTitleTag);

				// Activate the spinner and disable the update button
				this.loading = true;
				$('.spinner').addClass('is-active');
				$('#publish').addClass('disabled');

			},
			modeSaved() {

				// Re-Enable the editors
				this.enableEditors();

				// Update the window title
				$('title').text(this.currentTitleTag);

				// Stop loading indicators
				this.loading = false;
				$('.spinner').removeClass('is-active');
				$('#publish').removeClass('disabled');

			},

			
		},
		mounted() {

			docReady(() => {
				if (this.currentLang) this.initializeEditors();

				// Update the page title
				this.currentTitleTag = this.currentTitle.length ? this.currentTitle : this.currentTitleTag;
			});

		}
	});


	// Document Ready
	$(document).ready(function () {


		// Clicking "Publish/Update" button
		$('form#post').submit(function (e) {

			console.log('Submitted');


			// Show loading indicator
			customCodes.loading = true;


			if ($('#ajax-saver').length) {


				// Collate all post form data
				var data = $('form#post').serializeArray();
				console.log('Sending the data...');


				// Disable editors, activate the spinner, and disable the update button
				customCodes.modeSaving();


				// Send the request
				$.ajax({
					type: "POST",
					url: customCodesData.ajaxUrl,
					data: data,
					timeout: 30000,
					success: function (response) {

						console.log('RESPONSE: ', typeof response, response);

						if (typeof response === "string") {

							// Extract only the JSON part
							var regex = /({.*?})/g;
							var found = response.toString().match(regex);
							response = jQuery.parseJSON(found[found.length - 1]);
							console.log('EXTRACTED RESPONSE: ', response);

						}

						// Update information
						var resultSuccess = response.success || false;
						customCodes.errors = response.errors || { Server: "Bad response" };
						customCodes.processTime = response.processTime || "...";
						customCodes.saveCount = response.saveCount || customCodes.saveCount;
						customCodes.lastEditedText = response.lastEditedText || customCodes.lastEditedText;


						// Update the saved editors
						var savedEditors = response.savedEditors || [];
						savedEditors.forEach(editorID => {

							console.log('SAVED EDITOR: ', editorID);
							customCodes.updateSavedStatus(editorID);

						});


						// Result logs
						if (resultSuccess && !Object.keys(customCodes.errors).length) {

							console.log('Successfully saved post!');

							// Play the sound
							if (parseInt(customCodesData.playSound)) codes_audioElement.play();

						} else {
							console.log('Something went wrong.', customCodes.errors);
						}


						// Renew output if open
						if (customCodes.outputOpen)
							customCodes.toggleOutput(null, true);


						// Re-Enable the editors
						customCodes.modeSaved();

					},
					error: function (jqXHR, textStatus, errorThrown) {

						console.error('Not saved!');

						// Update the errors
						customCodes.errors[textStatus] = errorThrown;

						// Re-Enable the editors
						customCodes.modeSaved();

					}
				});


				e.preventDefault();

			} else window.onbeforeunload = null; // Allow changing window

		});


		// Click to Copy File Names
		var defaultCopyText = $('.editor-files a').attr('data-tooltip');
		var defaultCopiedText = $('.editor-files a').attr('data-copied');
		$(document).on('mouseover', '.editor-files a', function (e) {

			$(this).attr('data-tooltip', defaultCopyText);

		}).on('click', '.editor-files a', function (e) {

			var text = $(this).attr('data-copy') ? $(this).attr('data-copy') : $(this).text();

			copyToClipboard(text);
			$(this).attr('data-tooltip', defaultCopiedText);

			e.preventDefault();
			return false;
		});


		// Title changes
		var initialPostTitle = $('#title').val();
		$('#title').on('input', function () {

			var newTitle = $(this).val();
			customCodes.currentTitle = newTitle;
			customCodes.currentTitleTag = newTitle.length ? newTitle : "Untitled Code";

			if (initialPostTitle == newTitle) window.onbeforeunload = null; // Allow changing window

		});


	});


	// KEYBOARD SHORTCUTS
	$(document).keydown(function (event) {


		// SAVE SHORTCUT (Cmd S)
		if ((event.ctrlKey || event.metaKey) && event.which == 83 && parseInt(customCodesData.commandS)) {

			// Allow changing window if AJAX is not available
			if (!$('#ajax-saver').length) window.onbeforeunload = null;

			// Click the save button
			$("#publish").click();

			event.preventDefault();
			return false;
		}


		// FULLSCREEN SHORTCUT (Cmd G)
		if ((event.ctrlKey || event.metaKey) && event.which == 71) {

			// Toggle fullscreen
			customCodes.toggleFullScreen();

			event.preventDefault();
			return false;
		}


		// OUTPUT SHORTCUT (Cmd O)
		if ((event.ctrlKey || event.metaKey) && event.which == 79) {

			// Toggle output
			customCodes.toggleOutput();

			event.preventDefault();
			return false;
		}

		//console.log(event.which);


	});


	// Copy text to clipboard
	function copyToClipboard(text) {

		var temp = $("<input>");
		$("body").append(temp);

		temp.val(text).select();

		document.execCommand("copy");
		temp.remove();

	}


	// Check if document ready
	function docReady(fn) {
		// see if DOM is already available
		if (document.readyState === "complete" || document.readyState === "interactive") {
			// call on next available tick
			setTimeout(fn, 1);
		} else {
			document.addEventListener("DOMContentLoaded", fn);
		}
	}


})(jQuery);
