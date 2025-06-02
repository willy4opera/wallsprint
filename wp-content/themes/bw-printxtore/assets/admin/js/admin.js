/**
 * Created by Administrator on 6/8/2015.
 */
(function($) {
    "use strict";
    $(document).ready(function() {
        $('.post-php #clear-gallery').on('click',function(){
            $(this).parent().find('.gallery_values').val('');
            $(this).parent().find('.screenshot').html('');
        })
        $( '.post-php #edit-gallery' ).on('click',function(){
            var el = $( this ).parent();
            var parent = el;
            if ( !el.hasClass( 'redux-field-container' ) ) {
                parent = el.parents( '.redux-field-container:first' );
            }
             wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
                template: function(view){
                    
                  return;
                }
            });       
                        
            var current_gallery = $( this ).closest( 'fieldset' );

            if ( event.currentTarget.id === 'clear-gallery' ) {
                //remove value from input

                var rmVal = current_gallery.find( '.gallery_values' ).val( '' );

                //remove preview images
                current_gallery.find( ".screenshot" ).html( "" );

                return;

            }

            // Make sure the media gallery API exists
            if ( typeof wp === 'undefined' || !wp.media || !wp.media.gallery ) {
                return;
            }
            event.preventDefault();

            var $$ = $( this );

            var val = current_gallery.find( '.gallery_values' ).val();
            var final;

            if ( !val ) {
                final = '[gallery ids="0"]';
            } else {
                final = '[gallery ids="' + val + '"]';
            }


            var frame = wp.media.gallery.edit( final );
            
            if (!val) {
                var uploader = $('body').find('#' + frame.el.id);
                var inline = uploader.find('.uploader-inline');
                var spinner = uploader.find('.media-toolbar .spinner');
                
                setTimeout(
                    function(){ 
                        if (inline.hasClass('hidden')) {
                            inline.removeClass('hidden');
                            spinner.removeClass('is-active');
                        }
                    }, 400
                );
            }

            // When the gallery-edit state is updated, copy the attachment ids across
            frame.state( 'gallery-edit' ).on(
                'update', function( selection ) {

                    //clear screenshot div so we can append new selected images
                    current_gallery.find( ".screenshot" ).html( "" );

                    var element, preview_html = "", preview_img;
                    var ids = selection.models.map(
                        function( e ) {
                            element = e.toJSON();
                            preview_img = (typeof element.sizes !== "undefined" && typeof element.sizes.thumbnail !== 'undefined') ? element.sizes.thumbnail.url : element.url;

                            preview_html = "<a class='of-uploaded-image' href='" + preview_img + "'><img class='redux-option-image' src='" + preview_img + "' alt='' /></a>";
                            current_gallery.find( ".screenshot" ).append( preview_html );

                            return e.id;
                        }
                    );

                    current_gallery.find( '.gallery_values' ).val( ids.join( ',' ) );
                    redux_change( current_gallery.find( '.gallery_values' ) );
                    frame.detach();
                }
            );

            return false;
        })       

        // Check purchase code ajax
        $('.check-verify').on('click', function(e){
            e.preventDefault();
            var envato_name = $('input[name="user_envato_name"]').val();
            var user_purchase_code = $('input[name="user_purchase_code"]').val();
            var seft = $(this);
            seft.append('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                crossDomain: true,
                data: { 
                    action: 'purchase_code_verify',
                    envato_name: envato_name,
                    user_purchase_code: user_purchase_code,
                },
                success: function(data){
                    $('#check-result').html(data);
                    if($('.vr-message.message-success').length > 0) $('#message').fadeOut();
                    else $('#message').fadeIn();
                    seft.find('i').remove();
                },
                error: function(MLHttpRequest, textStatus, errorThrown){  
                    console.log(errorThrown);  
                }
            });
            return false;
        });
        //end
        if($('#term-color').length>0){
            $( '#term-color' ).wpColorPicker();
        }
        $('.sv-remove-item').on('click',function () {
            $(this).parent().remove();
            return false;
        });
        $('.sv-button-remove-upload').on('click',function () {
            $(this).parent().find('img').attr('src','');
            $(this).parent().find('input').attr('value','');
            return false;
        });         
        //end

        $('.sv-button-upload').on('click',function () {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var seff = $(this);
            wp.media.editor.send.attachment = function (props, attachment) {
                seff.parent().find('.live-previews').html('<img alt="" src="'+attachment.url+'" />');
                seff.parent().find('input.sv-image-value').val(attachment.url);
                wp.media.editor.send.attachment = send_attachment_bkp;
            }
            wp.media.editor.open();
            return false;
        });

        $('.sv-button-upload-id').on('click',function () {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var seff = $(this);
            wp.media.editor.send.attachment = function (props, attachment) {
                seff.parent().find('.live-previews').html('<img alt="" src="'+attachment.url+'" />');
                seff.parent().find('input.sv-image-value').val(attachment.id);
                wp.media.editor.send.attachment = send_attachment_bkp;
            }
            wp.media.editor.open();
            return false;
        });

        $('.sv-button-remove').on('click',function () {
            var image_df = $(this).parent().find('.live-previews').attr('data-image');
            if(image_df) $(this).parent().find('.live-previews img').attr('src',image_df);
            else $(this).parent().find('.live-previews').html('');
            $(this).parent().find('input.sv-image-value').val('');
            return false;
        });


        $('.sv-button-upload-img').on("click",function(options){
            var default_options = {
                callback:null
            };
            options = $.extend(default_options,options);
            var image_custom_uploader;
            var self = $(this);
            //If the uploader object has already been created, reopen the dialog
            if (image_custom_uploader) {
                image_custom_uploader.open();
                return false;
            }
            //Extend the wp.media object
            image_custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: true
            });
            //When a file is selected, grab the URL and set it as the text field's value
            image_custom_uploader.on('select', function() {
                var selection = image_custom_uploader.state().get('selection');
                var ids = [], urls=[];
                selection.map(function(attachment)
                {
                    attachment  = attachment.toJSON();
                    ids.push(attachment.id);
                    urls.push(attachment.url);

                });
                var img_prev = '';
                for(var i=0;i<urls.length;i++)
                {
                    img_prev += '<img alt="" src="'+urls[i]+'" class="img-100">';
                }
                if(img_prev!='')
                    self.parent().find(".img-previews").html(img_prev);
                    self.parent().find("input.multi-image-url").val( JSON.stringify(urls) );


                if (typeof options.callback == 'function'){
                    options.callback({'self':self,'urls':urls});

                };


            });
            image_custom_uploader.open();
            return false;
        });

    });

    $('body').on('click', '.sv-del', function(e)
    {
        e.preventDefault();
        $(this).parent().remove();
    })
})(jQuery);