(function ($) {

    $elementorscope = {};

    ElemailerElementorAdmin = {

        visited_pages: [],
        reset_remaining_posts: 0,
        site_imported_data: null,
        backup_taken: false,
        templateData: {},
        insertData: {},
        log_file: '',
        pages_list: '',
        insertActionFlag: false,
        page_id: 0,
        requiredPlugins: [],
        canImport: false,
        canInsert: false,
        type: 'pages',
        action: '',
        masonryObj: [],
        index: 0,
        processing: false,
        siteType: '',
        page: 1,
        per_page: 20,

        init: function () {
            this._bind();
        },

        /**
         * Binds events for the elemailer lite.
         *
         * @since 1.0.5
         * @access private
         * @method _bind
         */
        _bind: function () {

            if (elementorCommon) {

                $('body').on("click", "#elementor-panel-footer-sub-menu-item-save-template", ElemailerElementorAdmin._restrictSaveTemplate);
                $('body').on("click", ".elementor-context-menu-list__group.elementor-context-menu-list__group-save", ElemailerElementorAdmin._restrictSaveTemplate);

                let add_section_tmpl = $("#tmpl-elementor-add-section");

                if (add_section_tmpl.length > 0) {

                    let action_for_add_section = add_section_tmpl.text();
                    let white_label_class      = '';
                    let stylesheet             = '';
                    action_for_add_section     = action_for_add_section.replace('<div class="elementor-add-section-drag-title', stylesheet + '<div class="elementor-add-section-area-button elementor-add-elemailer-site-button ' + white_label_class + '" title="' + elemailerElementorData.plugin_name + '"> <i class="eicon-folder"></i> </div><div class="elementor-add-section-drag-title');

                    add_section_tmpl.text(action_for_add_section);

                    elementor.on("preview:loaded", function () {

                        let base_skeleton = wp.template('elemailer-lite-template-base-skeleton');
                        let header_template = $('#tmpl-elemailer-lite-template-modal__header').text();

                        if ( $('#elemailer-lite-modal').length == 0 ) {

                            $('body').append(base_skeleton());
                            $elementorscope = $('#elemailer-lite-modal');
                            $elementorscope.find('.elemailer-lite-content-wrap').before(header_template);
                        }

                        $(elementor.$previewContents[0].body).on("click", ".elementor-add-elemailer-site-button", ElemailerElementorAdmin._open);
                        $('body').on("click", ".elemailer-lite-modal__header__close", ElemailerElementorAdmin._close);
                        $('body').on("click", "#elemailer-lite-modal .theme-screenshot", ElemailerElementorAdmin._preview);
                        $('body').on("click", "#elemailer-lite-modal .back-to-layout", ElemailerElementorAdmin._goBack);

                        $(document).on("click", "#elemailer-lite-modal .elemailer-lite-library-template-insert", ElemailerElementorAdmin._insert);
                        $('body').on("click", "#elemailer-lite-modal .elementor-tooltip-icon", ElemailerElementorAdmin._toggleTooltip);
                        $(document).on("click", ".elementor-template-library-menu-item", ElemailerElementorAdmin._toggle);
                        $(document).on('click', '#elemailer-lite-modal .elemailer-lite__sync-wrap', ElemailerElementorAdmin._sync);
                        $(document).on('click', '#elemailer-lite-modal .elemailer-lite-modal__header__logo, #elemailer-lite-modal .back-to-layout-button', ElemailerElementorAdmin._home);
                        $(document).on('click', '#elemailer-lite-modal .notice-dismiss', ElemailerElementorAdmin._dismiss);
                        // Other events.
                        $(document).on('keyup input', '#elemailer-lite-modal #wp-filter-search-input', ElemailerElementorAdmin._search);
                        $(document).on('change', '#elemailer-lite-modal .elementor-template-library-order-input', ElemailerElementorAdmin._changeType);

                        // Triggers.
                        $(document).on("elemailer-lite__elementor-open-after", ElemailerElementorAdmin._initSites);
                        $(document).on("elemailer-lite__elementor-plugin-check", ElemailerElementorAdmin._pluginCheck);
                        $(document).on('elemailer-lite__elementor-close-before', ElemailerElementorAdmin._beforeClose);
                        $(document).on('elemailer-lite__elementor-do-step-2', ElemailerElementorAdmin._step2);

                        $(document).on('elemailer-lite__elementor-goback-step-1', ElemailerElementorAdmin._goStep1);

                    });
                }
            }
        },

        _restrictSaveTemplate: function (e) {
            console.log( 'Clicked' );
            $("#elementor-template-library-save-template-form").hide();
            $("#elementor-template-library-header-menu").hide();
            $(".elementor-templates-modal__header__logo-area").hide();
            $("#elementor-template-library-header-actions").hide();
            $(".elementor-template-library-blank-footer").hide();

            $(".elementor-template-library-blank-title").html( elemailerElementorData.getProText );
            $(".elementor-template-library-blank-message").html( elemailerElementorData.license_msg_save );
            $(".elementor-template-library-blank-message").append('<a href="https://elemailer.com/" target="_blank"><img src="'+elemailerElementorData.public_asset_dir+'/assets/img/elemailer-banner.png" style=" width: 100vh; display: block; margin: auto; margin-top: 16px; "></a>');
        },

        _open: function (e) {
            $(document).trigger('elemailer-lite__elementor-open-before');

            $('body').addClass('elemailer-lite__elementor-open');

            let add_section = $(this).closest('.elementor-add-section');

            if ( add_section.hasClass( 'elementor-add-section-inline' ) ) {
                ElemailerElementorAdmin.index = add_section.prevAll().length;
            } else {
                ElemailerElementorAdmin.index = add_section.prev().children().length;
            }

            ElemailerElementorAdmin._home();
            $elementorscope.fadeIn();

            if ($('.refreshed-notice').length == 1) {
                setTimeout(
                    function () {
                        $('.refreshed-notice').find('.notice-dismiss').click();
                    },
                    2500
                );
            }

            $(document).trigger('elemailer-lite__elementor-open-after');
        },

        _close: function (e) {
            console.groupEnd('Process Done.');
            $(document).trigger('elemailer-lite__elementor-close-before');
            setTimeout(function () {
                $elementorscope.fadeOut();
                $('body').removeClass('elemailer-lite__elementor-open');
            }, 300);
            $(document).trigger('elemailer-lite__elementor-close-after');
        },

        _myTemplateDelete: function (e) {
            console.groupEnd();

            if ( ! confirm( elemailerElementorData.confirmDeleteMessage ) ) {
                return;
            }

            $.ajax({
                url: elemailerElementorData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'elemailer-lite-my-template-delete-request',
                    template_id: $(this).data('saved-template-id'),
                    _ajax_nonce: elemailerElementorData._ajax_nonce,
                },
                beforeSend: function () {
                    console.groupCollapsed('Get Template Details.');
                },
            })
                .fail(function (jqXHR) {
                    console.log(jqXHR);
                    console.groupEnd();
                })
                .done(function (response) {
                    console.log( response );
                    console.groupEnd();

                    if( response.success ) {
                        $('#elemailer-lite-modal .elemailer-lite__sync-wrap').trigger('click');
                    } else {
                        console.log( 'not get Data' );
                    }
                });
        },

        _preview: function (e) {
            if (ElemailerElementorAdmin.processing) {
                return;
            }

            let step = $(this).attr('data-step');

            ElemailerElementorAdmin.page_id = $(this).closest('.elemailer-lite-theme').data('template-id');

            $elementorscope.find('.back-to-layout').css('visibility', 'visible');
            $elementorscope.find('.back-to-layout').css('opacity', '1');

            $elementorscope.find('.elemailer-lite-template-library-toolbar').hide();
            $elementorscope.find('.elemailer-lite-modal__header').removeClass('elemailer-lite-preview-mode');

            $elementorscope.find('.back-to-layout').attr('data-step', 2);
            $(document).trigger('elemailer-lite__elementor-do-step-2');
        },

        _goBack: function (e) {
            if ( ElemailerElementorAdmin.processing ) {
                return;
            }

            let step = $(this).attr('data-step');

            $elementorscope.find('#elemailer-lite-floating-notice-wrap-id.error').hide();

            $elementorscope.find('.elemailer-lite-step-1-wrap').show();
            $elementorscope.find('.elemailer-lite-preview-actions-wrap').remove();

            $elementorscope.find('.elemailer-lite-template-library-toolbar').show();
            $elementorscope.find('.elemailer-lite-modal__header').removeClass('elemailer-lite-preview-mode');

            if ('pages' == ElemailerElementorAdmin.type) {

                if (2 == step) {
                    $(this).attr('data-step', 1);
                    $(document).trigger('elemailer-lite__elementor-goback-step-1');
                }
            } else {
                $(this).attr('data-step', 1);
                $(document).trigger('elemailer-lite__elementor-goback-step-1');
            }

            $elementorscope.find('.elemailer-lite-content-wrap').trigger('scroll');
        },

        _insert: function (e) {
            if ( ! ElemailerElementorAdmin.canInsert ) {
                return;
            }

            ElemailerElementorAdmin.canInsert = false;
            var str = (ElemailerElementorAdmin.type == 'pages') ? elemailerElementorData.template : elemailerElementorData.block;

            $(this).addClass('installing');
            $(this).text('Importing ' + str + '...');

            ElemailerElementorAdmin.action = 'insert';

            ElemailerElementorAdmin._bulkPluginInstallActivate();
        },

        _toggleTooltip: function (e) {
            var wrap = $elementorscope.find('.elemailer-lite-tooltip-wrap');

            if (wrap.hasClass('elemailer-lite-show-tooltip')) {
                $elementorscope.find('.elemailer-lite-tooltip-wrap').removeClass('elemailer-lite-show-tooltip');
            } else {
                $elementorscope.find('.elemailer-lite-tooltip-wrap').addClass('elemailer-lite-show-tooltip');
            }
        },

        _toggle: function (e) {
            $elementorscope.find('.elementor-template-library-menu-item').removeClass('elementor-active');
            $elementorscope.find('.dialog-lightbox-content').hide();
            $elementorscope.find('.theme-preview').hide();
            $elementorscope.find('.theme-preview').html('');
            $elementorscope.find('.theme-preview-my-templates').hide();
            $elementorscope.find('.theme-preview-my-templates').html('');
            $elementorscope.find('.elemailer-lite-template-library-toolbar').show();

            $elementorscope.find('.dialog-lightbox-content').hide();
            $elementorscope.find('.dialog-lightbox-content-block').hide();

            $(this).addClass('elementor-active');
            let data_type = $(this).data('template-type');

            ElemailerElementorAdmin.type = data_type;
            ElemailerElementorAdmin._switchTo(data_type);
        },

        _sync: function (event) {
            event.preventDefault();
            var button = $(this).find('.elemailer-lite-sync-library-button');

            if ( button.hasClass( 'updating-message') ) {
                return;
            }

            button.addClass('updating-message');
            $elementorscope.find('#elemailer-lite-floating-notice-wrap-id').show().removeClass('error');
            $elementorscope.find('#elemailer-lite-floating-notice-wrap-id .elemailer-lite-floating-notice').html('<span class="message">Syncing template library in the background. The process can take some times. We will notify you once done.<span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + elemailerElementorData.dismiss_text + '</span></button>');
            $elementorscope.find('#elemailer-lite-floating-notice-wrap-id').addClass('slide-in').removeClass('refreshed-notice');

            $.ajax({
                url: elemailerElementorData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'elemailer-sync-page-builder',
                    _ajax_nonce: elemailerElementorData._ajax_nonce,
                },
                beforeSend: function () {
                    console.groupCollapsed('Get Template Details.');
                },
            })
                .fail(function (jqXHR) {
                    console.log(jqXHR);
                    console.groupEnd();
                })
                .done(function (response) {
                    console.groupEnd();

                    if( response.success ) {
                        console.log( 'get Data' );
                        elemailerElementorData.default_page_builder = response.data;

                        $elementorscope.find('#elemailer-lite-floating-notice-wrap-id.error').hide();
                        $elementorscope.find('.elemailer-lite-step-1-wrap').show();
                        $elementorscope.find('.elemailer-lite-preview-actions-wrap').remove();
                        $elementorscope.find('.elemailer-lite-template-library-toolbar').show();
                        $elementorscope.find('.elemailer-lite-modal__header').removeClass('elemailer-lite-preview-mode');

                        $(document).trigger('elemailer-lite__elementor-goback-step-1');

                        $elementorscope.find('#elemailer-lite-floating-notice-wrap-id').addClass('refreshed-notice').find('.elemailer-lite-floating-notice').html('<span class="message">' + elemailerElementorData.syncCompleteMessage + '</span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + elemailerElementorData.dismiss_text + '</span></button>');
                        button.removeClass('updating-message');
                    } else {
                        console.log( 'not get Data' );
                    }
                });

            $.ajax({
                url: elemailerElementorData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'elemailer-saved-my-templates',
                    _ajax_nonce: elemailerElementorData._ajax_nonce,
                },
                beforeSend: function () {
                    console.groupCollapsed('Get Template Details.');
                },
            })
                .fail(function (jqXHR) {
                    console.log(jqXHR);
                    console.groupEnd();
                })
                .done(function (response) {
                    console.groupEnd();

                    if( response.success ) {
                        console.log( 'get Data' );
                        elemailerElementorData.saved_my_templates = response.data;

                        $elementorscope.find('#elemailer-lite-floating-notice-wrap-id').addClass('refreshed-notice').find('.elemailer-lite-floating-notice').html('<span class="message">' + elemailerElementorData.syncCompleteMessage + '</span><button type="button" class="notice-dismiss"><span class="screen-reader-text">' + elemailerElementorData.dismiss_text + '</span></button>');
                        button.removeClass('updating-message');
                    } else {
                        console.log( 'not get Data' );
                    }
                });
        },

        _home: function () {
            if ( ElemailerElementorAdmin.processing ) {
                return;
            }

            $elementorscope.find('#wp-filter-search-input').val('');
            // Hide Back button.
            $elementorscope.find('.back-to-layout').css('visibility', 'hidden');
            $elementorscope.find('.back-to-layout').css('opacity', '0');
            $elementorscope.find('.elementor-template-library-menu-item:first-child').trigger('click');
        },

        _dismiss: function () {
            $(this).closest('.elemailer-lite-floating-notice-wrap').removeClass('slide-in');
            $(this).closest('.elemailer-lite-floating-notice-wrap').addClass('slide-out');

            setTimeout(function () {
                $(this).closest('.elemailer-lite-floating-notice-wrap').removeClass('slide-out');
            }, 200);

            $('#elemailer-lite-floating-notice-wrap-id').toggle();
        },

        _search: function () {
            let search_term = $(this).val() || '';
            search_term = search_term.toLowerCase();

            if ('pages' == ElemailerElementorAdmin.type) {

                var items = ElemailerElementorAdmin._getSearchedPages(search_term);

                if (search_term.length) {
                    $(this).addClass('has-input');
                    ElemailerElementorAdmin._addSites(items);
                } else {
                    $(this).removeClass('has-input');
                    ElemailerElementorAdmin._appendSites(elemailerElementorData.default_page_builder);
                }
            }
        },

        _changeType: function () {
            ElemailerElementorAdmin.siteType = $(this).val();
            $elementorscope.find('#wp-filter-search-input').trigger('keyup');
        },

        _initSites: function (e) {
            ElemailerElementorAdmin._appendSites( elemailerElementorData.default_page_builder );
            ElemailerElementorAdmin._goBack();
        },

        _beforeClose: function () {
            if (ElemailerElementorAdmin.action == 'insert') {
                $elementorscope.find('.elemailer-lite-library-template-insert').removeClass('installing');
                $elementorscope.find('.elemailer-lite-library-template-insert').text('Imported');
                $elementorscope.find('.elemailer-lite-library-template-insert').addClass('action-done');

                if ($elementorscope.find('.elemailer-lite-floating-notice-wrap').hasClass('slide-in')) {

                    $elementorscope.find('.elemailer-lite-floating-notice-wrap').removeClass('slide-in');
                    $elementorscope.find('.elemailer-lite-floating-notice-wrap').addClass('slide-out');

                    setTimeout(function () {
                        $elementorscope.find('.elemailer-lite-floating-notice-wrap').removeClass('slide-out');
                    }, 200);
                }
            }
        },

        _unescape: function (input_string) {
            var title = _.unescape(input_string);

            // @todo check why below character not escape with function _.unescape();
            title = title.replace('&#8211;', '-');

            return title;
        },

        _unescape_lower: function (input_string) {
            input_string = $("<textarea/>").html(input_string).text()
            var input_string = ElemailerElementorAdmin._unescape(input_string);
            return input_string.toLowerCase();
        },

        _addSites: function (data) {
            if (data) {
                let single_template = wp.template('elemailer-lite-search');
                pages_list = single_template(data);
                $elementorscope.find('.dialog-lightbox-content').html(pages_list);
                ElemailerElementorAdmin._loadLargeImages();

            } else {
                $elementorscope.find('.dialog-lightbox-content').html(wp.template('elemailer-lite-no-sites'));
            }
        },

        _getSearchedPages: function (search_term) {
            var items = [];
            search_term = search_term.toLowerCase();

            for (site_id in elemailerElementorData.default_page_builder) {

                var current_site = elemailerElementorData.default_page_builder[site_id];

                // Check in site title.
                if (current_site['title']) {
                    var page_title = ElemailerElementorAdmin._unescape_lower(current_site['title']);

                    if (page_title.toLowerCase().includes(search_term)) {
                        items[site_id] = current_site;
                    }
                }
            }

            return items;
        },

        _switchTo: function (type) {
            if ('pages' == type) {
                ElemailerElementorAdmin._initSites();
                $elementorscope.find('.dialog-lightbox-content').show();
                $elementorscope.find('.elementor-template-library-order').show();
                $elementorscope.find('.elemailer-lite-template-library-filter-text-wrapper').show();
            }

            $elementorscope.find('.elemailer-lite-content-wrap').trigger('scroll');
        },

        _appendSites: function (data) {
            let single_template = wp.template('elemailer-lite-list');
            pages_list = single_template(data);
            $elementorscope.find('.elemailer-lite-template-library-toolbar').show();
            $elementorscope.find('.dialog-lightbox-message-my-templates').hide();
            $elementorscope.find('.dialog-lightbox-message').show();
            $elementorscope.find('.dialog-lightbox-content').html(pages_list);
            ElemailerElementorAdmin._loadLargeImages();
        },

        _loadLargeImages: function () {
            $elementorscope.find('.theme-screenshot').each(function (key, el) {
                ElemailerElementorAdmin._loadLargeImage($(el));
            });
        },

        _loadLargeImage: function (el) {
            if ( el.hasClass('loaded') ) {
                return;
            }

            var large_img_url = el.data('src') || '';
            var imgLarge      = new Image();
            imgLarge.src      = large_img_url;
            imgLarge.onload   = function () {
                el.removeClass('loading');
                el.addClass('loaded');
                el.css('background-image', 'url(\'' + imgLarge.src + '\'');
            };
        },

        _step2: function (e) {
            $elementorscope.find('.dialog-lightbox-content').hide();
            $elementorscope.find('.dialog-lightbox-message').animate({ scrollTop: 0 }, 50);
            $elementorscope.find('.theme-preview').show();

            $elementorscope.find('.elemailer-lite-modal__header').addClass('elemailer-lite-preview-mode');

            if (undefined === ElemailerElementorAdmin.page_id) {
                return;
            }

            let import_template = wp.template('elemailer-lite-elementor-preview');
            let import_template_header = wp.template('elemailer-lite-elementor-preview-actions');
            let template_object = elemailerElementorData.default_page_builder[ElemailerElementorAdmin.page_id];

            if (undefined === template_object) {
                return;
            }

            ElemailerElementorAdmin.templateData = template_object;
            template_object['id'] = ElemailerElementorAdmin.page_id;

            preview_page_html = import_template(template_object);
            $elementorscope.find('.theme-preview').html(preview_page_html);

            $elementorscope.find('.elemailer-lite-step-1-wrap').hide();

            console.log( template_object );

            preview_action_html = import_template_header(template_object);
            $elementorscope.find('.elementor-templates-modal__header__items-area').append(preview_action_html);

            let actual_id = ElemailerElementorAdmin.page_id.replace('id-', '');
            $(document).trigger('elemailer-lite__elementor-plugin-check', { 'id': actual_id });
        },

        _goStep1: function (e) {
            // Reset site and page ids to null.
            ElemailerElementorAdmin.page_id = '';
            ElemailerElementorAdmin.requiredPlugins = [];
            ElemailerElementorAdmin.templateData = {};
            ElemailerElementorAdmin.canImport = false;
            ElemailerElementorAdmin.canInsert = false;

            // Hide Back button.
            $elementorscope.find('.back-to-layout').css('visibility', 'hidden');
            $elementorscope.find('.back-to-layout').css('opacity', '0');

            // Hide Preview Page.
            $elementorscope.find('.theme-preview').hide();
            $elementorscope.find('.theme-preview').html('');
            $elementorscope.find('.elemailer-lite-template-library-toolbar').show();

            // Show listing page.
            if ( ElemailerElementorAdmin.type == 'pages' ) {
                $elementorscope.find('.dialog-lightbox-content').show();
                $elementorscope.find('.dialog-lightbox-content-block').hide();

                // Set listing HTML.
                ElemailerElementorAdmin._appendSites(elemailerElementorData.default_page_builder);
            }
        },

        _pluginCheck: function (e, data) {
            ElemailerElementorAdmin._requiredPluginsMarkup( 1 );
        },

        _requiredPluginsMarkup: function (requiredPlugins) {
            if ( '' === requiredPlugins ) {
                return;
            }

            if (
                ElemailerElementorAdmin.type == 'pages' &&
                elemailerElementorData.default_page_builder[ElemailerElementorAdmin.page_id]['site-pages-type'] != 'free'
            ) {

                if ( ! elemailerElementorData.license_status ) {

                    output = '<p class="ast-validate">' + elemailerElementorData.license_msg + '</p>';

                    $elementorscope.find('.required-plugins-list').html(output);
                    $elementorscope.find('.elemailer-lite-tooltip-wrap').css('opacity', 1);
                    $elementorscope.find('.elemailer-lite-tooltip').css('opacity', 1);

                    /**
                     * Enable Demo Import Button
                     * @type number
                     */
                    ElemailerElementorAdmin.canImport = true;
                    ElemailerElementorAdmin.canInsert = true;
                    $elementorscope.find('.elemailer-lite-import-template-action > div').removeClass('disabled');

                    return;
                }

            }

            ElemailerElementorAdmin.canImport = true;
            ElemailerElementorAdmin.canInsert = true;
            $elementorscope.find('.elemailer-lite-import-template-action > div').removeClass('disabled');
        },

        _bulkPluginInstallActivate: function () {
            ElemailerElementorAdmin._enableImport();
        },

        _enableImport: function () {
            console.groupEnd();

            if ('pages' == ElemailerElementorAdmin.type) {

                $.ajax({
                    url: elemailerElementorData.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'elemailer-lite-remote-request',
                        url: ElemailerElementorAdmin.templateData['elemailer-page-api-url'],
                        _ajax_nonce: elemailerElementorData._ajax_nonce,
                    },
                    beforeSend: function () {
                        console.groupCollapsed('Get Template Details.');
                    },
                })
                    .fail(function (jqXHR) {
                        console.log(jqXHR);
                        console.groupEnd();
                    })
                    .done(function (response) {
                        console.log( response );
                        console.groupEnd();

                        if( response.success ) {
                            console.log( 'get Data' );
                            ElemailerElementorAdmin.insertData = response.data;
                            if ('insert' == ElemailerElementorAdmin.action) {
                                ElemailerElementorAdmin._insertDemo(response.data);
                            } else {
                                ElemailerElementorAdmin._createTemplate(response.data);
                            }
                        } else {
                            console.log( 'not get Data' );
                        }
                    });

            }
        },

        _insertDemo: function (data) {
            if (undefined !== data && undefined !== data['_elementor_data']) {
                let templateModel = new Backbone.Model({
                    getTitle() {
                        return data['title']
                    },
                });
                let page_content  = JSON.parse(data['_elementor_data']);
                let page_settings = '';
                let api_url       = '';
                api_url           = ElemailerElementorAdmin.templateData['elemailer-page-api-url'] + '/';

                $.ajax({
                    url: elemailerElementorData.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'elemailer-lite-elementor-batch-process',
                        id: elementor.config.document.id,
                        url: api_url,
                        _ajax_nonce: elemailerElementorData._ajax_nonce,
                    },
                    beforeSend: function () {
                        console.groupCollapsed('Inserting Demo.');
                    },
                })
                    .fail(function (jqXHR) {
                        console.log(jqXHR);
                        console.groupEnd();
                    })
                    .done(function (response) {

                        ElemailerElementorAdmin.processing = false;
                        $elementorscope.find('.elemailer-lite-content-wrap').removeClass('processing');

                        page_content = response.data;

                        page_content = page_content.map(function (item) {
                            item.id = Math.random().toString(36).substr(2, 7);
                            return item;
                        });

                        console.log(page_content);
                        console.groupEnd();
                        if (undefined !== page_content && '' !== page_content) {
                            if (undefined != $e && 'undefined' != typeof $e.internal) {
                                elementor.channels.data.trigger('document/import', templateModel);
                                elementor.getPreviewView().addChildModel(page_content, { at: ElemailerElementorAdmin.index } || {});
                                elementor.channels.data.trigger('template:after:insert', {});
                                $e.internal('document/save/set-is-modified', { status: true })
                            } else {
                                elementor.channels.data.trigger('document/import', templateModel);
                                elementor.getPreviewView().addChildModel(page_content, { at: ElemailerElementorAdmin.index } || {});
                                elementor.channels.data.trigger('template:after:insert', {});
                                elementor.saver.setFlagEditorChange(true);
                            }
                        }
                        ElemailerElementorAdmin.insertActionFlag = true;
                        ElemailerElementorAdmin._close();
                    });
            }
        },
    }

    /**
     * Initialize init
     */
    $(function () {
        ElemailerElementorAdmin.init();
    });

})(jQuery);