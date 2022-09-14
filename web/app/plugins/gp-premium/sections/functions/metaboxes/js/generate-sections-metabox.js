/*-----------------------------------------------------------------------------------*/
/* Generate Sections Metabox
/*
/* © Kathy Darling http://www.kathyisawesome.com
/* 2016-07-19. */
/*-----------------------------------------------------------------------------------*/

/**
 * @type {Object} JavaScript namespace for our application.
 */
var Generate_Sections = {
    backbone_modal: {
        __instance: undefined
    }
};

(function($, Generate_Sections) {


    // Model
    Generate_Sections.Section = Backbone.Model.extend({
        defaults: {
            "title": "",
            "box_type": "",
            "inner_box_type": "",
            "custom_classes": "",
            "custom_id": "",
            "top_padding": "",
            "bottom_padding": "",
			"top_padding_unit": "",
            "bottom_padding_unit": "",
            "background_color": "",
            "background_image": "",
            "background_image_preview": "",
            "parallax_effect": "",
            "background_color_overlay": "",
            "text_color": "",
            "link_color": "",
            "link_color_hover": "",
            "content": ""
        }
    });


    // Collection
    Generate_Sections.SectionsCollection = Backbone.Collection.extend({
        model: Generate_Sections.Section,
        el: "#generate_sections_container",
        comparator: function(model) {
            return model.get('index');
        }
    });


    /**
     * Primary Modal Application Class
     */
    Generate_Sections.backbone_modal.Application = Backbone.View.extend({

        attributes: {
            id: "generate-sections-modal-dialog",
            class: "generate-sections-modal",
            role: "dialog"
        },

        template: wp.template("generate-sections-modal-window"),

        mediaUploader: null,

        /*-----------------------------------------------------------------------------------*/
        /* tinyMCE settings
        /*-----------------------------------------------------------------------------------*/

        tmc_settings: {},

        /*-----------------------------------------------------------------------------------*/
        /* tinyMCE defaults
        /*-----------------------------------------------------------------------------------*/

        tmc_defaults: {
            theme: "modern",
            menubar: false,
            wpautop: true,
            indent: false,
            toolbar1: "bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen",
            plugins: "fullscreen,image,wordpress,wpeditimage,wplink",
            max_height: 500
        },

        /*-----------------------------------------------------------------------------------*/
        /* quicktags settings
        /*-----------------------------------------------------------------------------------*/

        qt_settings: {},

        /*-----------------------------------------------------------------------------------*/
        /* quicktags defaults
        /*-----------------------------------------------------------------------------------*/

        qt_defaults: {
            buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen"
        },

        model: Generate_Sections.Section,

        events: {
            "click .media-modal-backdrop, .media-modal-close, .media-button-close": "closeModal",
            "click .media-button-insert": "saveModal",
            "click .media-menu-item": "switchTab",
            "keydown": "keydown",
            "click .generate-sections-upload-button": "openMediaUploader",
            "click .generate-sections-remove-image": "removeImage",
            "click div.media-frame-title h1": "toggleMenu"
        },



        /**
         * Simple object to store any UI elements we need to use over the life of the application.
         */
        ui: {
            nav: undefined,
            content: undefined
        },


        /**
         * Instantiates the Template object and triggers load.
         */
        initialize: function() {
            _.bindAll(this, "render", "closeModal", "saveModal", "switchTab");

            this.focusManager = new wp.media.view.FocusManager({
                el: this.el
            });

            this.changeInsertText();
            //this.tinyMCEsettings();
            this.render();
        },

        /**
         * switch the insert button text to "insert section"
         */
        changeInsertText: function(restore) {

            var restore = typeof restore !== 'undefined' && restore == true ? true : false;

            if (restore == false && typeof(wp.media.view.l10n.insertIntoPost) !== "undefined") {
                this.insertIntoPost = wp.media.view.l10n.insertIntoPost;
                wp.media.view.l10n.insertIntoPost = generate_sections_metabox_i18n.insert_into_section;
                // switch the insert button text back
            } else if (restore == true && typeof(this.insertIntoPost) !== "undefined") {
                wp.media.view.l10n.insertIntoPost = this.insertIntoPost;
            }
        },

        /**
         * Assembles the UI from loaded template.
         * @internal Obviously, if the template fail to load, our modal never launches.
         */
        render: function() {

            "use strict";

            // Build the base window and backdrop, attaching them to the $el.
            // Setting the tab index allows us to capture focus and redirect it in Application.preserveFocus
            this.$el.attr("tabindex", "0").html(this.template);

            // Handle any attempt to move focus out of the modal.
            //jQuery(document).on("focusin", this.preserveFocus);

            // set overflow to "hidden" on the body so that it ignores any scroll events while the modal is active
            // and append the modal to the body.
            jQuery("body").addClass("generate-modal-open").prepend(this.$el);

            // aria hide the background
            jQuery("#wpwrap").attr("aria-hidden", "true");

            this.renderContent();

            this.renderPreview();

            this.selected();
            this.colorPicker();
            //this.startTinyMCE();
			this.launchEditor();

            // Set focus on the modal to prevent accidental actions in the underlying page
            this.$el.focus();

            return this;
        },

		launchEditor: function() {
			var id = this.ui.panels.find( ".generate-sections-editor-wrap" ).find( 'textarea' ).attr( 'id' ),
				customButtonsContainer = this.ui.panels.find( '.generate-sections-editor-wrap' ).find( '#custom-media-buttons' );

			customButtonsContainer.find( '.insert-media' ).remove();
			var customButtons = customButtonsContainer.html();
			customButtonsContainer.remove();

			var init_settings = true;

            if ( typeof tinyMCEPreInit == "object" && "mceInit" in tinyMCEPreInit && "content" in tinyMCEPreInit.mceInit ) {
                init_settings = tinyMCEPreInit.mceInit.content;
            } else if ( typeof window.wpEditorL10n !== "undefined" ) {
                init_settings = window.wpEditorL10n.tinymce.settings;
            } else {
				init_settings = this.tmc_defaults;
			}

			var thisPanel = this.ui.panels;

			var custom_settings = {
                wp_autoresize_on: false,
                cache_suffix: "",
                min_height: 400,
				wp_keep_scroll_position: false,
				setup: function( editor ) {
					editor.on( 'init', function( e ) {
						if ( 'html-active' === generate_sections_metabox_i18n.default_editor && generate_sections_metabox_i18n.user_can_richedit ) {
							thisPanel.find( '#wp-generate-sections-editor-wrap' ).removeClass( 'tmce-active' ).addClass( 'html-active' );
							editor.hidden = true;
						}
					} );
				}
            }

			init_settings = $.extend({}, init_settings, custom_settings);

			var qt_settings = true;

			if ( typeof tinyMCEPreInit == "object" && "qtInit" in tinyMCEPreInit && "content" in tinyMCEPreInit.qtInit ) {
				qt_settings = tinyMCEPreInit.qtInit.content;
			} else {
				qt_settings = this.qt_defaults;
			}

			if ( ! generate_sections_metabox_i18n.user_can_richedit ) {
				init_settings = false;
			}

			wp.sectionsEditor.initialize( id, {
				tinymce: init_settings,
				quicktags: qt_settings,
				mediaButtons: true
			} );

			var buttonsElement = this.ui.panels.find( '#wp-generate-sections-editor-wrap' ).find( '.wp-media-buttons' );
			buttonsElement.attr( 'id', 'custom-media-buttons' );
			$( customButtons ).appendTo( buttonsElement );

			if ( ! generate_sections_metabox_i18n.user_can_richedit ) {
				this.ui.panels.find( '#generate-sections-editor' ).addClass( 'no-rich-edit' );
			}
		},

        /**
         * Make the menu mobile-friendly
         */
        toggleMenu: function() {
            this.$el.find('.media-menu').toggleClass('visible');
        },

        /**
         * Create the nav tabs & panels
         */
        renderContent: function() {

            var model = this.model;

            var menu_item = wp.template("generate-sections-modal-menu-item");

            // Save a reference to the navigation bar"s unordered list and populate it with items.
            this.ui.nav = this.$el.find(".media-menu");

            // reference to content area
            this.ui.panels = this.$el.find(".media-frame-content");

            // loop through the tabs
            if (generate_sections_metabox_i18n.tabs.length) {

                // for...of is nicer, but not supported by minify, so stay with for...in for now
                for (var tab in generate_sections_metabox_i18n.tabs) {

                    if (generate_sections_metabox_i18n.tabs.hasOwnProperty(tab)) {

                        tab = generate_sections_metabox_i18n.tabs[tab];

						var $new_tab = $(menu_item({
							target: tab.target,
							name: tab.title
						}));

						var panel = wp.template("generate-sections-edit-" + tab.target);

                        var $new_panel = $(panel(model.toJSON()));

						if (tab.active == "true") {
                            $new_tab.addClass("active");
                            $new_panel.addClass("active");
                        }

						jQuery( 'body' ).on( 'generate_section_show_settings', function() {
							jQuery( '.generate-sections-modal .media-menu-item' ).removeClass('active');
							jQuery( '.generate-sections-modal .panel' ).removeClass('active');
							jQuery( '.generate-section-item-style' ).addClass('active');
							jQuery( '.generate-section-settings' ).addClass('active');
						});

                        this.ui.nav.append($new_tab);
                        this.ui.panels.append($new_panel);
                    }
                }
            }

        },


        /**
         * Render the background image preview
         */
        renderPreview: function(image_id) {

            var image_id = typeof image_id !== 'undefined' ? image_id : this.model.get("background_image");

            var $preview = $("#generate-section-image-preview");
            $preview.children().remove();

            if (image_id > 0) {
                this.background = new wp.media.model.Attachment.get(image_id);

                this.background.fetch({
                    success: function(att) {
                        if (_.contains(['png', 'jpg', 'gif', 'jpeg'], att.get('subtype'))) {
                            $("<img/>").attr("src", att.attributes.sizes.thumbnail.url).appendTo($preview);
                            $preview.next().find(".generate-sections-remove-image").show();
                        }
                    }
                });
            }

        },


        /**
         * Set the default option for the select boxes
         */
        selected: function() {

            var _this = this;

            this.$el.find("select").each(function(index, select) {

                var attribute = jQuery(select).attr("name");
                var selected = _this.model.get(attribute);
                jQuery(select).val(selected);

            });
        },

        /**
         * Start the colorpicker
         */
        colorPicker: function() {
            this.$el.find(".generate-sections-color").wpColorPicker();
        },

        /**
         * Launch Media Uploader
         */
        openMediaUploader: function(e) {

            var _this = this;

            $input = jQuery(e.currentTarget).prev("#generate-sections-background-image");

            e.preventDefault();

            // If the uploader object has already been created, reopen the dialog
            if (this.mediaUploader) {
                this.mediaUploader.open();
                return;
            }
            // Extend the wp.media object
            this.mediaUploader = wp.media.frames.file_frame = wp.media({
                title: generate_sections_metabox_i18n.media_library_title,
                button: {
                    text: generate_sections_metabox_i18n.media_library_button
                },
                multiple: false
            });


            // When a file is selected, grab the URL and set it as the text field"s value
            this.mediaUploader.on("select", function() {

                attachment = _this.mediaUploader.state().get("selection").first().toJSON();

                $input.val(attachment.id);

                _this.renderPreview(attachment.id);
            });
            // Open the uploader dialog
            this.mediaUploader.open();

        },

        /**
         * Remove the background image
         */
        removeImage: function(e) {
            e.preventDefault();
            $("#generate-section-image-preview").children().remove();
            $("#generate-section-image-preview").next().find(".generate-sections-remove-image").hide();
            $("#generate-sections-background-image").val("");
        },


        /**
         * Closes the modal and cleans up after the instance.
         * @param e {object} A jQuery-normalized event object.
         */
        closeModal: function(e) {
            "use strict";

            e.preventDefault();
            this.undelegateEvents();
            jQuery(document).off("focusin");
            jQuery("body").removeClass("generate-modal-open");
            jQuery("body").removeClass("generate-section-content");

            // remove restricted media modal tab focus once it's closed
            this.$el.undelegate('keydown');

			// remove the tinymce editor
			// this needs to be called before the modal is closed or it will fail in Firefox (that was fun to figure out...)
			if (typeof tinyMCE != "undefined") {
				tinymce.EditorManager.execCommand("mceRemoveEditor", true, "generate-sections-editor");
			}

            // remove modal and unset instances
            this.remove();
            Generate_Sections.backbone_modal.__instance = undefined;
            this.mediaUploader = null;
            Generate_Sections.modalOpen = null;

            // switch the insert button text back
            this.changeInsertText(true);

            // send focus back to where it was prior to modal open
            Generate_Sections.lastFocus.focus();

            // aria unhide the background
            jQuery("#wpwrap").attr("aria-hidden", "false");

			// Fix bug where the window scrolls down 50px on close
			var topDistance = jQuery("body").offset().top;
			if ( topDistance >= jQuery("body").scrollTop() ) {
				jQuery("body").scrollTop(0);
			}

        },

        /**
         * Responds to the btn-ok.click event
         * @param e {object} A jQuery-normalized event object.
         * @todo You should make this your own.
         */
        saveModal: function(e) {
            "use strict";

            this.model.get("index");

            var model = this.model;

            // send the tinymce content to the textarea
            if (typeof tinyMCE != "undefined") {
                tinymce.triggerSave();
            }

            var $inputs = this.ui.panels.find("input, select, textarea");

            $inputs.each(function(index, input) {

                var name = $(input).attr("name");

                if (model.attributes.hasOwnProperty(name)) {
                    var value = $(input).val();
                    model.set(name, value);
                }

            });

            this.closeModal(e);
        },

        /**
         * Handles tab clicks and switches to corresponding panel
         * @param e {object} A jQuery-normalized event object.
         */
        switchTab: function(e) {
            "use strict";
            e.preventDefault();

			// close lingering wp link windows
            if (typeof tinyMCE != "undefined" && 'style' == jQuery( e.currentTarget ).data( 'target' ) && this.ui.panels.find( '#wp-generate-sections-editor-wrap' ).hasClass( 'tmce-active' )) {
                tinyMCE.activeEditor.execCommand('wp_link_cancel');
                tinyMCE.activeEditor.execCommand('wp_media_cancel');
            }

            this.ui.nav.children().removeClass("active");
            this.ui.panels.children().removeClass("active");

            var target = jQuery(e.currentTarget).addClass("active").data("target");

            this.ui.panels.find("div[data-id=" + target + "]").addClass("active");
        },

        /**
         * close on keyboard shortcuts
         * @param {Object} event
         */
        keydown: function(e) {
            // Close the modal when escape is pressed.
            if (27 === e.which && this.$el.is(':visible')) {
                this.closeModal(e);
                e.stopImmediatePropagation();
            }
        }

    });


    // Singular View
    Generate_Sections.sectionView = Backbone.View.extend({

        model: Generate_Sections.Section,
        tagName: 'div',

        initialize: function() {
            // re-render on all changes EXCEPT index
            this.listenTo(this.model, "change", this.maybeRender);
        },

        attributes: {
            class: "ui-state-default section"
        },

        // Get the template from the DOM
        template: wp.template("generate-sections-section"),

        events: {
            'click .edit-section': 'editSection',
            'click .section-title > span': 'editSection',
            'click .delete-section': 'removeSection',
            'click .toggle-section': 'toggleSection',
            'reorder': 'reorder',
        },

        maybeRender: function(e) {
            if (this.model.hasChanged('index')) return;
            this.render();
        },

        // Render the single model - include an index.
        render: function() {
            this.model.set('index', this.model.collection.indexOf(this.model));
            this.$el.html(this.template(this.model.toJSON()));

            if (!this.model.get('title')) {
                this.$el.find('h3.section-title > span').text(generate_sections_metabox_i18n.default_title);
            }
            this.$el.find('textarea').val(JSON.stringify(this.model));

            return this;
        },


        // launch the edit modal
        editSection: function(e) {

            // stash the current focus
            Generate_Sections.lastFocus = document.activeElement;
            Generate_Sections.modalOpen = true;

            e.preventDefault();
            if (Generate_Sections.backbone_modal.__instance === undefined) {
                Generate_Sections.backbone_modal.__instance = new Generate_Sections.backbone_modal.Application({
                    model: this.model
                });
            }

        },

        // reorder after sort
        reorder: function(event, index) {
            this.$el.trigger('update-sort', [this.model, index]);
        },

        // remove/destroy a model
        removeSection: function(e) {
            e.preventDefault();
            if (confirm(generate_sections_metabox_i18n.confirm)) {
                this.model.destroy();
                Generate_Sections.sectionList.render(); // manually calling instead of listening since listening interferes with sorting
            }
        },
    });


    // List View
    Generate_Sections.sectionListView = Backbone.View.extend({

        el: "#generate_sections_container",
        events: {
            'update-sort': 'updateSort',
            //     'add-section': 'addOne'
        },

        // callback for clone button
        addSection: function(model) {
            this.collection.add(model);
            this.addOne(model);
        },

        addOne: function(model) {
            var view = new Generate_Sections.sectionView({
                model: model
            });
            this.$el.append(view.render().el);
        },

        render: function() {
            this.$el.children().remove();
            this.collection.each(this.addOne, this);
            return this;
        },

        updateSort: function(event, model, position) {
            this.collection.remove(model);

            // renumber remaining models around missing model
            this.collection.each(function(model, index) {

                var new_index = index;
                if (index >= position) {
                    new_index += 1;
                }
                model.set('index', new_index);
            });

            // set the index of the missing model
            model.set('index', position);

            // add the model back to the collection
            this.collection.add(model, {
                at: position
            });

            this.render();

        },

    });


    // The Buttons & Nonce
    Generate_Sections.ButtonControls = Backbone.View.extend({

        attributes: {
            class: "generate_sections_buttons"
        },

        tagName: 'p',

        el: "#_generate_sections_metabox",

        template: wp.template("generate-sections-buttons"),

        // Attach events
        events: {
            "click .button-primary": "newSection",
            "click #generate-delete-sections": "clearAll",
            'click .edit-section': 'editSection',
        },

        // create new
        newSection: function(e) {
            e.preventDefault();
            var newSection = new Generate_Sections.Section();
            Generate_Sections.sectionList.addSection(newSection);
        },

        // clear all models
        clearAll: function(e) {
            e.preventDefault();
            if (confirm(generate_sections_metabox_i18n.confirm)) {
                Generate_Sections.sectionCollection.reset();
                Generate_Sections.sectionList.render();
            }
        },

        render: function() {
            this.$el.find(".generate_sections_control").append(this.template);
            return this;
        },

    });


    // init
    Generate_Sections.initApplication = function() {

        // Create Collection From Existing Meta
        Generate_Sections.sectionCollection = new Generate_Sections.SectionsCollection(generate_sections_metabox_i18n.sections);

        // Create the List View
        Generate_Sections.sectionList = new Generate_Sections.sectionListView({
            collection: Generate_Sections.sectionCollection
        });
        Generate_Sections.sectionList.render();

        // Buttons
        Generate_Sections.Buttons = new Generate_Sections.ButtonControls({
            collection: Generate_Sections.sectionCollection
        });
        Generate_Sections.Buttons.render();

    };


    /*-----------------------------------------------------------------------------------*/
    /* Execute the above methods in the Generate_Sections object.
    /*-----------------------------------------------------------------------------------*/

	jQuery( function( $ ) {

        Generate_Sections.initApplication();

        $( '#generate_sections_container' ).sortable({
            axis: "y",
            opacity: 0.5,
            grid: [20, 10],
            tolerance: "pointer",
            handle: ".move-section",
            update: function(event, ui) {
                ui.item.trigger("reorder", ui.item.index());
            }
        } );

		if ( $( '.use-sections-switch' ).is( ':checked' ) ) {
			setTimeout( function() {
				generateShowSections();
			}, 200 );
		} else {
			generateHideSections();
		}

		$( '.use-sections-switch' ).on( 'change', function( e ) {
			var status = ( $(this).is( ':checked' ) ) ? 'checked' : 'unchecked';

			if ( 'checked' == status ) {
				generateShowSections();
			} else if ( 'unchecked' == status ) {
				generateHideSections();
			}
		} );

        function generateShowSections() {

            // Hide send to editor button
            $('.send-to-editor').css('display', 'none');

            // Hide the editor
            $('#postdivrich').css({
                'opacity': '0',
                'height': '0',
                'overflow': 'hidden'
            });

			$( '.block-editor-block-list__layout' ).hide();

			$( '.edit-post-layout .edit-post-visual-editor' ).css( {
				'flex-grow': 'unset',
				'flex-basis': '0'
			} );

			$( '.edit-post-visual-editor .block-editor-writing-flow__click-redirect' ).css( {
				'min-height': '0'
			} );

			$( '.edit-post-layout__metaboxes:not(:empty)' ).css( 'border-top', '0' );

            // Show the sections
            $('#_generate_sections_metabox').css({
                'opacity': '1',
                'height': 'auto'
            });

			// Remove and add the default editor - this removes any visible toolbars etc..
			// We need to set a timeout for this to work
			// if (typeof tinyMCE != "undefined") {
				// tinyMCE.EditorManager.execCommand("mceRemoveEditor", true, "content");
				// $( '.use-sections-cover' ).css( 'z-index','10000' );
				// setTimeout('tinyMCE.EditorManager.execCommand("mceAddEditor", true, "content");', 1);
				// setTimeout('jQuery( ".use-sections-cover" ).css( "z-index","-1" );', 1000);
			// }

			// Set a trigger
            $('body').trigger('generate_show_sections');

        }

        function generateHideSections() {

            // Show send to editor button
            $('.send-to-editor').css('display', 'inline-block');

            // Show the editor
            $('#postdivrich').css({
                'opacity': '1',
                'height': 'auto'
            });

			$( '.block-editor-block-list__layout' ).show();

			$( '.edit-post-layout .edit-post-visual-editor' ).css( {
				'flex-grow': '',
				'flex-basis': ''
			} );

			$( '.edit-post-visual-editor .block-editor-writing-flow__click-redirect' ).css( {
				'min-height': ''
			} );

			$( '.edit-post-layout__metaboxes:not(:empty)' ).css( 'border-top', '' );

            // Hide the sections
            $('#_generate_sections_metabox').css({
                'opacity': '0',
                'height': '0',
                'overflow': 'hidden'
            });

            $('body').trigger('generate_hide_sections');

        }

		$( document ).on( 'click', '.edit-section.edit-settings', function() {
			$( 'body' ).trigger( 'generate_section_show_settings' );
		});

    });


})(jQuery, Generate_Sections);
