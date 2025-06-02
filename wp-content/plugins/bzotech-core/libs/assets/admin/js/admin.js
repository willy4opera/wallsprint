
(function($) {
	'use strict';

	$('document').ready(function(){

		// SELECT
		var bb_edo_icon = $('#bb_edo_icon');

		$(bb_edo_icon).on('change', function(){
			$('.bb_edo_icon_depend').css({display: 'none'});
			$( '#bb_edo_' + $(this).val() ).css({display: 'block'});
		});
		$(bb_edo_icon).trigger('change');

		var bb_edo_option_by_themselves = $('#bb_edo_option_by_themselves');
		$(bb_edo_option_by_themselves).on('change', function(){
			$('.bb_edo_icon_depend').css({display: 'none'});
			if($(this).val() == 'custom') {
				$( '#bb_edo_option_by_themselves_custom' ).css({display: 'block'});
			}
		});
		$(bb_edo_option_by_themselves).trigger('change');

		// ENTER ID
		$("#idSlugTxt").keypress(function(event){
	        if ((event.charCode >= 48 && event.charCode <= 57) ||
	            (event.charCode >= 65 && event.charCode <= 90) ||
	            (event.charCode >= 97 && event.charCode <= 122) ||
				event.charCode == 95) {
	            return;
            } else {
	            return false;
            }
	    });

		// Upload icon
		var bb_custom_image_icon = $('#bb_custom_image_icon');
		if(bb_custom_image_icon.length > 0) {

			var frame,
				bb_upload_image_icon = $('#bb_upload_image_icon'),
				bb_delete_image_icon = $('#bb_delete_image_icon'),
				bb_custom_image_icon_val = $('#bb_custom_image_icon_val');

			bb_delete_image_icon.on('click', function(){
				bb_custom_image_icon.html( '' );
				bb_custom_image_icon_val.val( '' );
			});

			bb_upload_image_icon.on('click', function(){
				event.preventDefault();

			    // If the media frame already exists, reopen it.
			    if ( frame ) {
			      frame.open();
			      return;
			    }

			    // Create a new media frame
			    frame = wp.media({
			      title: 'Choose icon image',
			      button: {
			        text: 'Use this image'
			      },
			      multiple: false  // Set to true to allow multiple files to be selected
			    });


			    // When an image is selected in the media frame...
			    frame.on( 'select', function() {

			      // Get media attachment details from the frame state
			      var attachment = frame.state().get('selection').first().toJSON();

			      // Send the attachment URL to our custom image input field.
			      bb_custom_image_icon.html( '<img src="'+attachment.url+'" alt="" />' );

			      // Send the attachment id to our hidden input
			      bb_custom_image_icon_val.val( attachment.url );

			    });

			    // Finally, open the modal on click
			    frame.open();
			});
		}

	});
}(window.jQuery));
