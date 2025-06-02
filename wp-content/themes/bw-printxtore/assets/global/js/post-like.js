(function($)
{
	"use strict";
	$(document).ready(function() {
		$('body').on('click','.sv-post-like',function(event){
			event.preventDefault();
			var heart = $(this);
			var post_id = heart.data("post_id");
			heart.html("<i id='icon-like' class='icon icon-heart1'></i><i id='icon-gear' class='las la-spinner xoayvong'></i>");
			$.ajax({
				type: "post",
				url: ajax_var.url,
				data: "action=sv-post-like-new&nonce="+ajax_var.nonce+"&bzotech_post_like_new=&post_id="+post_id,
				success: function(count){
					if( count.indexOf( "already" ) !== -1 )
					{
						var lecount = count.replace("already","");
						if (lecount === "0")
						{
							lecount = "0";
						}
						heart.prop('title', 'Like');
						heart.removeClass("liked");
						heart.html("<i id='icon-unlike' class='icon icon-heart1'></i>"+lecount);
					}
					else
					{
						heart.prop('title', 'Unlike');
						heart.addClass("liked");
						heart.html("<i id='icon-like' class='icon icon-heart1'></i>"+count);
					}
				}
			});
		});
	});
})(jQuery)
