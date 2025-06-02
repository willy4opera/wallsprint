(function($){

   var EmiconsAdmin = {

    init: function() {
         $( document )
             .on( 'click.EmiconsAdmin', '.emicons-menu-opener', this.openEmiconsModalWithIconSettings )
             .on( 'click.EmiconsAdmin', '.emicons_set_icon_toggle_in_nav_item', this.openEmiconsModalWithIconSettings )
             .on( 'click.EmiconsAdmin', '.emicons-menu-modal-closer', this.closeEmiconsModal )
             .on( 'click.EmiconsAdmin', '.save-rt-menu-item-options', this.updateEmiconsItemSettings )
             .on( 'click.EmiconsAdmin', '.emicons_remove_icon_toggle_in_nav_item', this.deleteEmiconsItemSettings )
             .on( 'click.EmiconsAdmin', '.emicons_pro_warning_img', this.alertForLicenseActive )
             .on( 'click.EmiconsAdmin', '.emicons_set_icon_toggle_in_nav_item_free', this.alertForLicenseActive )
             .on( 'change.EmiconsAdmin', '#emicons_source_select', this.getMenuIconOptionsBySource )
             .on( 'click.EmiconsAdmin', '.emicons-notice .notice-dismiss', this.ignorePluginNotice )
             ;
    },
    ignorePluginNotice: function (that) { 

        let notice_id = $(this).parent().data('notice_id');

        $.ajax({
            type: 'POST',
            url: emicons_ajax.ajaxurl,
            data: {
                action    : "emicons_ignore_plugin_notice",
                notice_id : notice_id,
                nonce : emicons_ajax.nonce,
            },
            cache: false,
        });
    },
    alertForLicenseActive: function () { 
        alert('Please activate plugin license to use this advanced features!');
    },
    openEmiconsModal: function (that) { 
        $('#emicons-menu-setting-modal').css('display', 'flex');
        $('div#emicons-menu-setting-modal #tabs-nav li').removeClass('active');
        $('div#emicons-menu-setting-modal #tabs-nav li:first-child').addClass('active');
        let menuItemId = $(this).attr('data-menu_item_id');
        let icon_source = $(this).attr('data-icon_source');
        $('.save-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        $('.delete-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        EmiconsAdmin.showEmiconsModalAjaxLoader($(this));
        EmiconsAdmin.getMenuItemOptions(menuItemId, icon_source);
    },
    hideEmiconsModal: function (that) { 
        $('#emicons-menu-setting-modal').css('display', 'none');
    },
    openEmiconsModalWithIconSettings:  function (that) { 
        $('#emicons-menu-setting-modal').css('display', 'flex');
        $('div#emicons-menu-setting-modal #tabs-nav li').removeClass('active');
        $('div#emicons-menu-setting-modal #tabs-nav li:first-child').addClass('active');
        let menuItemId = $(this).attr('data-menu_item_id');
        let icon_source = $(this).attr('data-icon_source');
        if(icon_source == ''){
            icon_source = 'dashicon';
        }
        $('.save-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        $('.delete-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        EmiconsAdmin.showEmiconsModalAjaxLoader($(this));
        EmiconsAdmin.getMenuItemOptions(menuItemId, icon_source);
    },
    getMenuIconOptionsBySource:  function (that) { 

        let menuItemId = $(this).attr('data-menu_item_id');
        let icon_source = $(this).val();
        $('#emicons_set_icon_dialog').css('display', 'none');
        $('.save-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        $('.delete-rt-menu-item-options').attr('data-menu_item_id', menuItemId);
        EmiconsAdmin.showEmiconsModalAjaxLoader($(this));
        EmiconsAdmin.getMenuItemOptions(menuItemId, icon_source);
    },
    closeEmiconsModal: function () {
        $('#emicons-menu-setting-modal').css('display', 'none');
    },
    showEmiconsModalAjaxLoader: function () { 
        $('#emicons-menu-setting-modal .ajax-loader').css('display', 'flex');
    },
    hideEmiconsModalAjaxLoader: function () {
        $('#emicons-menu-setting-modal .ajax-loader').css('display', 'none');
    },
    deleteEmiconsItemSettings: function( that ){

        EmiconsAdmin.showEmiconsModalAjaxLoader($(this));
        let menu_id = $("#nav-menu-meta-object-id").val();
        let menu_item_id = $(this).attr('data-menu_item_id');
        let btnParent = $(this).parent().parent();
        let set_icon_btn = $(btnParent).find('.emicons_set_icon_toggle_in_nav_item');

        $.ajax({
            type: 'POST',
            url: emicons_ajax.ajaxurl,
            data: {
                action          : "emicons_delete_menu_options",
                menu_id         : menu_id,
                menu_item_id    : menu_item_id,
                nonce : emicons_ajax.nonce,
            },
            cache: false,
            success: function(response) {
                if(response.success == true){
                    $(btnParent).find('.emicons_saved_icon').html('');
                    $(btnParent).removeClass('has-icon');
                    $(set_icon_btn).html('Add Icon');
                }
                
            }
        });
    },
    getMenuItemOptions: function (menu_item_id, icon_source) { 
        $.ajax({
            type: 'POST',
            url: emicons_ajax.ajaxurl,
            data: {
                action          : "emicons_get_menu_options",
                menu_item_id    : menu_item_id,
                icon_source     : icon_source,
                nonce : emicons_ajax.nonce,
            },
            cache: false,
            success: function(response) {
                $('#emicons-menu-setting-modal .tab-contents-wrapper').html(response);
                $('#emicons_set_icon_dialog').css('display', 'block');
                EmiconsAdmin.menuIconSettingScripts()
                EmiconsAdmin.hideEmiconsModalAjaxLoader($(this));
            }
        });
    },
    updateEmiconsItemSettings: function( that ){

        EmiconsAdmin.showEmiconsModalAjaxLoader($(this));
        let menu_id = $("#nav-menu-meta-object-id").val();
        let menu_item_id = $(this).attr('data-menu_item_id');
        let action = $(this).attr('action');

        let status_form = $('#emicons-menu-setting-modal .form-status');
        let btnParent = $('li#menu-item-'+menu_item_id);

        console.log('menu_item_id', menu_item_id);
        

        var css = {};
        var settings = {};

        // Iterate over each input in the form
        $('#emicons_items_settings').find('input, select').each(function() {
            // Exclude the submit button from the values
            if ($(this).attr('type') !== 'submit' && $(this).attr('name') !== 'search_rt_icon') {
                settings[$(this).attr('name')] = $(this).val();
            }
        });

        // Iterate over each input in the form
        $('#emicons_items_css').find('input, select').each(function() {
            // Exclude the submit button from the values
            if ($(this).attr('type') !== 'submit') {
                css[$(this).attr('name')] = $(this).val();
            }
        });

        $.ajax({
            type: 'POST',
            url: emicons_ajax.ajaxurl,
            data: {
                action          : "emicons_update_menu_options",
                settings        : settings,
                css             : css,
                menu_id         : menu_id,
                menu_item_id    : menu_item_id,
                nonce : emicons_ajax.nonce,
            },
            cache: false,
            success: function(response) {
                
                if(response.success == true){
                    $(status_form).html('<span class="emicons-text-success">Settings Saved!</span>');
                    $(btnParent).find('.emicons_saved_icon_wrapper').addClass('has-icon');
                    $(btnParent).find('.emicons_saved_icon_wrapper .emicons_set_icon_toggle_in_nav_item').html('Change');
                    setTimeout(() => {
                        $(status_form).html('');
                    }, 1000);
                    EmiconsAdmin.hideEmiconsModalAjaxLoader($(this));
                    if(action == 'save_close') EmiconsAdmin.hideEmiconsModal();
                }
                
            }
        });

    },
    menuIconTabScripts: function (that) { 

        // Show the first tab and hide the rest
        $('div#emicons-menu-setting-modal #tabs-nav li:first-child').addClass('active');
        $('div#emicons-menu-setting-modal .tab-content').hide();
        $('div#emicons-menu-setting-modal .tab-content:first').show();

        // Click function
        $('div#emicons-menu-setting-modal #tabs-nav li').click(function(){
            $('div#emicons-menu-setting-modal #tabs-nav li').removeClass('active');
            $(this).addClass('active');
            $('div#emicons-menu-setting-modal .tab-content').hide();
            
            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    },
    menuIconSettingScripts: function (that) { 

       
        $('input[type="wpcolor"]').wpColorPicker();
        


        $('#emicons_remove_icon_toggle').click(function (e) { 
            e.preventDefault();
            $(this).hide();
            $('#emicons_items_settings input[name="menu_icon"]').val('');
            $('#emicons_items_settings .saved_icon').html('');
            $('#emicons_set_icon_toggle').html('Add Icon');
        });

        $('#emicons_set_icon_toggle').click(function (e) { 
            e.preventDefault();
            $('#emicons_set_icon_dialog').css('display', 'block');
        });

        $(document).on('click', '.emicons-icon-button', function (e) { 
            e.preventDefault();
           
            let menuItemId = $('.save-rt-menu-item-options').attr('data-menu_item_id');
            let iconClass = $(this).attr('icon_class');
            $('.emicons-icon-button').removeClass('active-emicons');
            $(this).addClass('active-emicons');
            $('#emicons_items_settings input[name="menu_icon"]').val(iconClass);
            $('#emicons_items_settings .saved_icon').html('<i class="'+iconClass+'"></i>');
            $('#menu-item-'+menuItemId).find('.emicons_saved_icon').html('<i class="'+iconClass+'"></i>');
            $('#emicons_remove_icon_toggle').show();
            $('#emicons_set_icon_toggle').html('Change');
        });


        // Icon Tabs
         // Show the first tab and hide the rest
        $('div#emicons-menu-setting-modal #icon-tabs-nav li:first-child').addClass('active');
        $('div#emicons-menu-setting-modal .icon-tab-content').hide();
        $('div#emicons-menu-setting-modal .icon-tab-content:first').show();
        $('div#emicons-menu-setting-modal .icon-tab-content:first').addClass('active');


        $('div#emicons-menu-setting-modal #icon-tabs-nav li').click(function(){
            $('div#emicons-menu-setting-modal #icon-tabs-nav li').removeClass('active');
            $(this).addClass('active');
            $('div#emicons-menu-setting-modal .icon-tab-content').hide();
            
            let activeTab = $(this).find('a').attr('href');
            $(activeTab).show();
            $(activeTab).addClass('active');
            return false;
        });

        $('input.search_rt_icon').quicksearch('.emicons_icons-selection-wrapper .emicons-icon-button');
    },
    pluginOptions: function (that) {
        // Show the first tab and hide the rest
        $('#tabs-nav li:first-child').addClass('active');
        $('.tab-content').hide();
        $('.tab-content:first').show();

        // Click function
        $('#tabs-nav li').click(function(){
            $('#tabs-nav li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });

        $('input[type="wpcolor"]').wpColorPicker();
    }

   }

   EmiconsAdmin.init(),
   EmiconsAdmin.menuIconTabScripts();
   EmiconsAdmin.pluginOptions();

})(jQuery);