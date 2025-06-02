/**
 * CodeDropz Uploader
 * Copyright 2018 Glen Mongaya
 * CodeDrop Drag&Drop Uploader
 * @version 1.3.8.8
 * @author CodeDropz, Glen Don L. Mongaya
 * @license The MIT License (MIT)
 */

// CodeDropz Drag and Drop Plugin
!function(e){e.fn.CodeDropz_Uploader=function(a){this.each(function(){var d=e(this),r=e.extend({handler:d,color:"#000",background:"",server_max_error:"Uploaded file exceeds the maximum upload size of your server.",max_file:d.data("max")?d.data("max"):10,max_upload_size:d.data("limit")?d.data("limit"):"10485760",supported_type:d.data("type")?d.data("type"):"jpg|jpeg|JPG|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv|xls",text:"Drag & Drop Files Here",separator:"or",button_text:"Browse Files",on_success:""},a),t=d.data("name")+"_count_files";localStorage.setItem(t,1);var s='<div class="codedropz-upload-handler"><div class="codedropz-upload-container"><div class="codedropz-upload-inner"><'+dnd_cf7_uploader.drag_n_drop_upload.tag+">"+r.text+"</"+dnd_cf7_uploader.drag_n_drop_upload.tag+"><span>"+r.separator+'</span><div class="codedropz-btn-wrap"><a class="cd-upload-btn" href="#">'+r.button_text+'</a></div></div></div><span class="dnd-upload-counter"><span>0</span> '+dnd_cf7_uploader.dnd_text_counter+" "+parseInt(r.max_file)+"</span></div>";r.handler.wrapAll('<div class="codedropz-upload-wrapper"></div>'),r.supported_type=r.supported_type.replace(/[^a-zA-Z0-9| ]/g,"");var o=r.handler.parents("form"),n=r.handler.parents(".codedropz-upload-wrapper"),p=e('input[type="submit"], button[type="submit"]',o);r.handler.after(s),e(".codedropz-upload-handler",n).on("drag dragstart dragend dragover dragenter dragleave drop",function(e){e.preventDefault(),e.stopPropagation()}),e(".codedropz-upload-handler",n).on("dragover dragenter",function(a){e(this).addClass("codedropz-dragover")}),e(".codedropz-upload-handler",n).on("dragleave dragend drop",function(a){e(this).removeClass("codedropz-dragover")}),e("a.cd-upload-btn",n).on("click",function(e){e.preventDefault(),r.handler.val(null),r.handler.click()}),e(".codedropz-upload-handler",n).on("drop",function(e){l(e.originalEvent.dataTransfer.files,"drop")}),r.handler.on("change",function(e){l(this.files,"click")}),/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)&&d.removeAttr("accept"),d.attr("data-random-id",function(e=20){let a="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",d=a.length,r="";for(let t=0;t<e;t++){let s=Math.floor(Math.random()*d);r+=a[s]}let o=Math.floor(Date.now()/1e3);return r+o}());var l=function(a,s){if(!(!a.length>1)){var p=new FormData;p.append("action","dnd_codedropz_upload"),p.append("type",s),p.append("security",dnd_cf7_uploader.ajax_nonce),p.append("form_id",d.data("id")),p.append("upload_name",d.data("name")),p.append("upload_folder",d.data("random-id")),d.data("black-list")&&p.append("blacklist-types",d.data("black-list")),e("span.has-error",r.handler).remove(),e.each(a,function(a,s){if(void 0!==p.delete&&p.delete("upload-file"),localStorage.getItem(t)>r.max_file)return!e("span.has-error-msg",n).length>0&&(err_msg=dnd_cf7_uploader.drag_n_drop_upload.max_file_limit,e(".codedropz-upload-handler",n).next('<span class="has-error-msg">'+err_msg.replace("%count%",r.max_file)+"</span>")),!1;var l=i.createProgressBar(s),u=!1;s.size>r.max_upload_size&&(e(".dnd-upload-details",e("#"+l)).append('<span class="has-error">'+dnd_cf7_uploader.drag_n_drop_upload.large_file+"</span>"),u=!0),regex_type=RegExp("(.*?).("+r.supported_type+")$"),!1!==u||regex_type.test(s.name.toLowerCase())||(e(".dnd-upload-details",e("#"+l)).append('<span class="has-error">'+dnd_cf7_uploader.drag_n_drop_upload.inavalid_type+"</span>"),u=!0),localStorage.setItem(t,Number(localStorage.getItem(t))+1),!1===u&&(p.append("upload-file",s),e.ajax({url:r.ajax_url,type:o.attr("method"),data:p,dataType:"json",cache:!1,contentType:!1,processData:!1,xhr:function(){var e=new window.XMLHttpRequest;return e.upload.addEventListener("progress",function(e){if(e.lengthComputable){var a=parseInt(100*(e.loaded/e.total));i.setProgressBar(l,a-1)}},!1),e},complete:function(){i.setProgressBar(l,100)},success:function(a){a.success?(i.setProgressBar(l,100),e.isFunction(r.on_success)&&r.on_success.call(this,d,l,a)):(e(".dnd-progress-bar",e("#"+l)).remove(),e(".dnd-upload-details",e("#"+l)).append('<span class="has-error">'+a.data+"</span>"),e('input[type="submit"], button[type="submit"]',o).removeClass("disabled").prop("disabled",!1),e("#"+l).removeClass("in-progress"))},error:function(a,d,t){e(".dnd-progress-bar",e("#"+l)).remove(),e(".dnd-upload-details",e("#"+l)).append('<span class="has-error">'+r.server_max_error+"</span>"),e('input[type="submit"],button[type="submit"]',o).removeClass("disabled").prop("disabled",!1),e("#"+l).removeClass("in-progress")}}))})}},i={createProgressBar:function(a){var d=e(".codedropz-upload-handler",n),r="dnd-file-"+Math.random().toString(36).substr(2,9),s='<div class="dnd-upload-image"><span class="file"></span></div><div class="dnd-upload-details"><span class="name"><span>'+a.name+"</span><em>("+i.bytesToSize(a.size)+')</em></span><a href="#" title="'+dnd_cf7_uploader.drag_n_drop_upload.delete.title+'" class="remove-file" data-storage="'+t+'"><span class="dnd-icon-remove"></span></a><span class="dnd-progress-bar"><span></span></span></div>';return d.after('<div id="'+r+'" class="dnd-upload-status">'+s+"</div>"),r},setProgressBar:function(a,d){var r=e(".dnd-progress-bar",e("#"+a));return r.length>0&&(p&&i.disableBtn(p),progress_width=d*r.width()/100,e("#"+a).addClass("in-progress"),100==d?e("span",r).width("100%").text(d+"% "):e("span",r).animate({width:progress_width},10).text(d+"% "),100==d&&e("#"+a).addClass("complete").removeClass("in-progress")),!1},bytesToSize:function(e){return 0===e?"0":fileSize=(kBytes=e/1024)>=1024?(kBytes/1024).toFixed(2)+"MB":kBytes.toFixed(2)+"KB"},disableBtn:function(e){e.length>0&&e.addClass("disable").prop("disabled",!0)}}}),e(document).on("click",".dnd-icon-remove",function(d){d.preventDefault();var r=e(this),t=r.parents(".dnd-upload-status"),s=r.parents(".codedropz-upload-wrapper"),o=r.parent("a").attr("data-storage"),n=Number(localStorage.getItem(o));if(t.hasClass("in-progress"))return!1;if(e(".has-error",t).length>0)return t.remove(),localStorage.setItem(o,n-1),!1;r.addClass("deleting").text(dnd_cf7_uploader.drag_n_drop_upload.delete.text+"...");var p={path:t.find('input[type="hidden"]').val(),action:"dnd_codedropz_upload_delete",security:dnd_cf7_uploader.ajax_nonce};e.post(a.ajax_url,p,function(a){a.success?(t.remove(),localStorage.setItem(o,n-1),e(".dnd-upload-status",s).length<=1&&e("span.has-error-msg",s).remove(),e(".dnd-upload-counter span",s).text(Number(localStorage.getItem(o))-1)):e(".dnd-upload-details",t).append('<span class="has-error-msg">'+a.data+"</span>")}),e("span.has-error-msg").remove()})}}(jQuery);
// End CodeDropz fn.

jQuery(document).ready(function($){

	// Custom event handler
    var dnd_upload_cf7_event = function( target, name, data ) {
        var event = new CustomEvent( 'dnd_upload_cf7_' + name, {
			bubbles     : true,
			detail     : data
		});
		$(target).get(0).dispatchEvent( event );
    }

	// Fires when an Ajax form submission has completed successfully, and mail has been sent.
	document.addEventListener( 'wpcf7mailsent', function( event ) {

		// Get input type file element
		var inputFile = $('.wpcf7-drag-n-drop-file');
		var $form = inputFile.parents('form');

		// Reset upload list for multiple fields
		if( inputFile.length > 0 ) {
			$.each( inputFile, function(){
				// Reset file counts
				localStorage.setItem( $(this).attr('data-name') + '_count_files', 1 );
			});
		}else {
			// Reset file counts
			localStorage.setItem( inputFile.attr('data-name') + '_count_files', 1 );
		}

		// Remove status / progress bar
		$('.dnd-upload-status', $form ).remove();
		$('.dnd-upload-counter span', $form ).text('0');
		$('span.has-error-msg', $form ).remove();

	}, false );

	window.initDragDrop = function () {

		// Get text object options/settings from localize script
		var TextOJB = dnd_cf7_uploader.drag_n_drop_upload;

		// Support Multiple Fileds
		$('.wpcf7-drag-n-drop-file').CodeDropz_Uploader({
			'color'				:	'#fff',
			'ajax_url'			: 	dnd_cf7_uploader.ajax_url,
			'text'				: 	TextOJB.text,
			'separator'			: 	TextOJB.or_separator,
			'button_text'		:	TextOJB.browse,
			'server_max_error'	: 	TextOJB.server_max_error,
			'on_success'		:	function( input, progressBar, response ){

				// Progressbar Object
				var $progressDetails = $('#' + progressBar, input.parents('.codedropz-upload-wrapper') );
				var $form = input.parents('form');
				var $span =  $('.wpcf7-acceptance', $form );
				var $input = $('input:checkbox', $span);

				// If it's complete remove disabled attribute in button
				if( $span.hasClass( 'optional' ) || $input.is( ':checked' ) || $span.length == 0 || $form.hasClass('wpcf7-acceptance-as-validation') )  {
					setTimeout(function(){
                        const buttonSubmit = $('input[type="submit"], button[type="submit"]', $form);
                        if( buttonSubmit ){
                            buttonSubmit.removeAttr('disabled');
                        }
                    }, 1);
				}

				// Append hidden input field
				$progressDetails
					.find('.dnd-upload-details')
						.append('<span><input type="hidden" name="'+ input.attr('data-name') +'[]" value="'+ response.data.path +'/'+ response.data.file +'"></span>');

				// Update counter
				var $files_counter = ( Number( localStorage.getItem( input.data('name') + '_count_files' ) ) - 1 );
				$('.dnd-upload-counter span', input.parents('.codedropz-upload-wrapper')).text( $files_counter );

				// Js hook/event trigger after successful upload.
				dnd_upload_cf7_event( $progressDetails, 'success', response );
			}
		});

	}

	window.initDragDrop();

	// Usage: Custom js hook after success upload
	document.addEventListener( 'dnd_upload_cf7_success', function( event ) {
		//console.log('success');
	});

});