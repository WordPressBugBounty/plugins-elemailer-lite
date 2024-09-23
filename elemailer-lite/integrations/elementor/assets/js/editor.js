(function ($) {
    "use strict";

    // call the the functionality of add, edit form when elementor editor panel is open for edit
    elementor.hooks.addAction('panel/open_editor/widget/elemailer-selected-posts', function (panel, model, view) {
        //call initially to set the already saved data
        elemailer_get_taxonomy_for_selected_posts();

        elemailer_addControlSpinner('taxonomy_type'); //add spinner while loading
        elemailer_addControlSpinner('post_select'); //add spinner while loading

        //function to get taxonomy based on post type 
        function elemailer_get_taxonomy_for_selected_posts(onload = true) {
            
            elemailer_addControlSpinner('taxonomy_type'); //add spinner while loading
            
            var elTaxonomy = $('[data-setting="taxonomy_type"]');

            $('[data-setting="taxonomy_type"]').empty();
            //only trigger change to reset selected taxonomy option when post type is actively changed
            if (onload == false && event.type == 'change') {
                //this is needed to reset the selected taxonomy
                $('[data-setting="taxonomy_type"]').trigger('change');
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_taxonomies',
                postTypeNonce: elemailer_lite.wpRestNonce,
                post_type: post_type
            };

            var i = 0;
            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var taxonomy_name = JSON.parse(response);
                $('[data-setting="taxonomy_type"]').empty();

                if(Object.keys(taxonomy_name).length!==0){
                    $.each(taxonomy_name, function () {
                        if (this.name == 'post_format') {
                            return;
                        }

                        // console.log('taxonomy loop: '+(i++));
                        $('[data-setting="taxonomy_type"]').append('<option value="' + this.name + '">' + this.name + '</option>');

                    });
                    //set already selected value
                    $('[data-setting="taxonomy_type"]').val(model.attributes.settings.attributes.taxonomy_type);

                }else{
                     $('[data-setting="taxonomy_type"]').val(0).trigger('change');
                }
               


                elemailer_get_posts_for_selected_posts($('[data-setting="taxonomy_type"]'));

                elemailer_removeControlSpinner('taxonomy_type'); //add spinner while loading
       
                if ($('[data-setting="taxonomy_type"]').has('option').length == 0) {
                    $('[data-setting="taxonomy_type"]').attr('disabled', 'disabled');
                } else {
                    $('[data-setting="taxonomy_type"]').removeAttr('disabled');
                }
            });//$.post                
        }//elemailer_get_taxonomy_for_selected_posts()

        //function to get posts based on taxonomy
        function elemailer_get_posts_for_selected_posts(onload = true) {

            elemailer_addControlSpinner('post_select'); //add spinner while loading

            setTimeout(function () {

    // console.log('get posts');
            var elPostSelect = $('[data-setting="post_select"]');

            if (typeof (onload) !== 'object') {
                //var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
                var taxonomy_type = onload;
            } else {
                var taxonomy_type = onload.val();
                $('[data-setting="post_select"]').empty();
            }

            //if no taxonomy selected stop the function to avoid showing null value in posts
            // if (taxonomy_type == null) {
            //     return;
            // }
            
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_posts',
                postTypeNonce: elemailer_lite.wpRestNonce,
                taxonomy_type: taxonomy_type,
                post_type: post_type
            };

            var j = 0;
            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var posts = JSON.parse(response);
                $('[data-setting="post_select"]').empty();
                $.each(posts, function (idx, value) {
                    // console.log('posts loop: '+(j++));                    
                    $('[data-setting="post_select"]').append('<option value="' + idx + '">' + value + '</option>');
                });

                console.log(typeof (onload));
                //set already selected value
                if (typeof (onload) === 'object') {
                    $('[data-setting="post_select"]').val(model.attributes.settings.attributes.post_select);
                }
                elemailer_removeControlSpinner('post_select'); //add spinner while loading

            });

            }, 1);

            

        

        }//elemailer_get_posts_for_selected_posts()

        //when moving from Advanced tab to content model variable is null so to pass it's data
        function elemailer_pass_around_model_for_selected_posts(panel, model, view) {
         
                elemailer_get_posts_for_selected_posts($('[data-setting="post_type"]'));
          
        }

        const settingsModel = model.get( 'settings' );
        
        settingsModel.on( 'change', ( changedModel ) => {

            // if(changedModel.changed.post_type){
            //     // pass onload value false, means the value was actively changed  
            //     elemailer_get_taxonomy_for_selected_posts(false);
            //     $('[data-setting="taxonomy_type"]').selectedIndex = -1;
            // }
            if(changedModel.changed.post_type){
                //pass $this to keep the changes to each different taxonomy
                elemailer_get_posts_for_selected_posts($('[data-setting="post_type"]'));
               $('[data-setting="post_select"]').selectedIndex = -1;
            }
             
        } );

        //this ensures the data remains the same even after switching back from advanced tab to content tab

            elementor.channels.editor.on('elemailer:selectedposts:clicked', function(panel, model, view) {
                elemailer_pass_around_model_for_selected_posts(panel, model, view);

                console.log('clicked!!!');
            });



    });

        elementor.channels.editor.on('editor:widget:elemailer-selected-posts:elemailer_sp_content_section:activated', (panelView)=>{



            elementor.channels.editor.trigger( 'elemailer:selectedposts:clicked', require );


               // console.log('outer one', panelView);

        });


    // call the the functionality of add, edit form when elementor editor panel is open for edit
    elementor.hooks.addAction('panel/open_editor/widget/elemailer-latest-posts', function (panel, model, view) {

        //call initially to set the already saved data
        elemailer_get_taxonomy_for_latest_posts();

        //function to get taxonomy based on post type 
        function elemailer_get_taxonomy_for_latest_posts(onload = true) {
            
            elemailer_addControlSpinner('taxonomy_type'); //add spinner while loading
            elemailer_addControlSpinner('terms'); //add spinner while loading

            var elTaxonomy = $('[data-setting="taxonomy_type"]');

            $('[data-setting="taxonomy_type"]').empty();

            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_taxonomies',
                postTypeNonce: elemailer_lite.wpRestNonce,
                post_type: post_type
            };

            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var taxonomy_name = JSON.parse(response);
                $('[data-setting="taxonomy_type"]').empty();
                if(Object.keys(taxonomy_name).length!==0){
                    $.each(taxonomy_name, function () {
                        if (this.name == 'post_format') {
                            return;
                        }

                        $('[data-setting="taxonomy_type"]').append('<option value="' + this.name + '">' + this.name + '</option>');

                        $('[data-setting="taxonomy_type"]').val(model.attributes.settings.attributes.taxonomy_type);

                    });
                }else{
                    $('[data-setting="taxonomy_type"]').val(0).trigger('change');
                }
             
                //set already selected value
                elemailer_removeControlSpinner('taxonomy_type');
               
                elemailer_get_terms_for_latest_posts($('[data-setting="taxonomy_type"]'));

                if ($('[data-setting="taxonomy_type"]').has('option').length == 0) {
                    $('[data-setting="taxonomy_type"]').attr('disabled', 'disabled');
                } else {
                    $('[data-setting="taxonomy_type"]').removeAttr('disabled');
                }
            });//$.post                
        }//elemailer_get_taxonomy_for_latest_posts()

        //function to get terms based on taxonomy
        function elemailer_get_terms_for_latest_posts(onload = true) {
            var elPostSelect = $('[data-setting="terms"]');

            if (typeof (onload) !== 'object') {
                //var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
                var taxonomy_type = onload;
            } else {

                var taxonomy_type = onload.val();
                $('[data-setting="terms"]').empty();
            }

            //if no taxonomy selected stop the function to avoid showing null value in terms
            if (taxonomy_type == null) {
                return;
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_terms',
                postTypeNonce: elemailer_lite.wpRestNonce,
                taxonomy_type: taxonomy_type,
                post_type: post_type
            };

            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var terms = JSON.parse(response);
                 $('[data-setting="terms"]').empty();
                $.each(terms, function (idx, value) {
                    $('[data-setting="terms"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                });

                //set already selected value
                if (typeof (onload) === 'object') {
                    $('[data-setting="terms"]').val(model.attributes.settings.attributes.terms);
                }
                elemailer_removeControlSpinner('terms');

            });

        }//elemailer_get_terms_for_latest_posts()

        //when moving from Advanced tab to content model variable is null so to pass it's data
        function elemailer_pass_around_model_for_latest_posts(panel, model, view) {
              
                elemailer_get_taxonomy_for_latest_posts();

        }

        
        const settingsModel = model.get( 'settings' );
        
        settingsModel.on( 'change', ( changedModel ) => {

            console.log('Setting changed');
            if(changedModel.changed.post_type){
                // pass onload value false, means the value was actively changed  
                elemailer_get_taxonomy_for_latest_posts(false);
                $('[data-setting="taxonomy_type"]').selectedIndex = -1;
            }
            if(changedModel.changed.taxonomy_type){
                //pass $this to keep the changes to each different taxonomy
                elemailer_get_terms_for_latest_posts($('[data-setting="taxonomy_type"]'));
                $('[data-setting="post_select"]').selectedIndex = -1;
            }
             
        } );

        //this ensures the data remains the same even after switching back from advanced tab to content tab
        elementor.channels.editor.on('section:activated',function(){
            if(elementor.getPanelView().currentPageView.activeSection == 'elemailer_lp_content_section'){

                elemailer_pass_around_model_for_latest_posts(panel, model, view);

              }
        });

    });

    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        elemailer_hide_unwanted_column_controls();

        function elemailer_hide_unwanted_column_controls() {
            var parentLayoutControls = panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-controls');
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-style').hide();
            var layoutControlsChildrens = parentLayoutControls.children();
            layoutControlsChildrens.css('display', 'none');
            //layoutControlsChildrens.eq(0).css('display', 'block');
            layoutControlsChildrens.eq(2).css('margin-top', '10px');
            layoutControlsChildrens.eq(2).css('padding-top', '20px');
            layoutControlsChildrens.eq(2).css('display', 'block');
            layoutControlsChildrens.eq(2).find('.elementor-control-responsive-switchers').hide();
        }

    });

    // remove extra control of section control hooks 
    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        elemailer_hide_unwanted_section_controls();

        function elemailer_hide_unwanted_section_controls() {
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-style').find('a').trigger('click');
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-style').trigger('click'); // needed for new version of elementor
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-layout').hide();

            var parentLayoutControls = panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('#elementor-controls');
            var layoutControlsChildrens = parentLayoutControls.children();

            for (let i = 0; i < layoutControlsChildrens.length; i++) {
                if( !layoutControlsChildrens.eq(i).prop('class').includes("elemailer-section-control") ){
                        layoutControlsChildrens.eq(i).css('display', 'none');
                }
            }

            // layoutControlsChildrens.eq(0).css('display', 'none');
            // layoutControlsChildrens.eq(1).css('display', 'none');
            // layoutControlsChildrens.eq(2).css('display', 'none');

            // layoutControlsChildrens.eq(-4).css('display', 'none');
            // layoutControlsChildrens.eq(-3).css('display', 'none');
            // layoutControlsChildrens.eq(-2).css('display', 'none');
            // layoutControlsChildrens.eq(-1).css('display', 'none');
            // layoutControlsChildrens.eq(-7).css('margin-top', '10px');
            // layoutControlsChildrens.eq(-7).css('padding-top', '20px');
        }

    });

    function change_exit_link_attr() {
       
        // change exit URL target as otherwise it will have issue with iframe of elemailer

       $('#elementor-panel-header-menu-button').click('click', function() {
            setTimeout(function() {
               $(".elementor-panel-menu-item-exit a").attr('target', '_top');
               $(".elementor-panel-menu-item-view-page a").attr('target', '_top');
            }, 100);
                 
        }, false);

    }
    
    elementor.on('preview:loaded', change_exit_link_attr);

 /**
   * Add a spinner to a control inside its control title.
   * @since 4.1.2
   * @param {string} controlName - The control name to add the spinner to.
   *
   * @return {void}
   */
    // add a preloader for options to load -> pass control name
    function elemailer_addControlSpinner(controlName) {
        const thecontrol= '.elementor-control-'+controlName;   
        setTimeout(() => {
        
         //Exit if there is a spinner already.
        if ($(thecontrol).find('.elementor-control-spinner').length) {
          return;
        }

        const $input = $(thecontrol).find(':input')||$(thecontrol).find('select');
        $input.attr('disabled', true);

         $(thecontrol).find('.elementor-control-title').after('<span class="elementor-control-spinner"><i class="eicon-spinner eicon-animation-spin"></i>&nbsp;</span>');
        }, "600");
    }

    // remove preloader when loaded -> pass control name
    function elemailer_removeControlSpinner(controlName) {

        const thecontrol= '.elementor-control-'+controlName;

        setTimeout(() => {
            $(thecontrol).find('.elementor-control-spinner').remove();
            const $input = $(thecontrol).find(':input')||$(thecontrol).find('select');
            $input.attr('disabled', false);

        }, "800");
    }

})(jQuery);
