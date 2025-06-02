( function( $ ) {
	/**
 	 * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	 */ 
	// elmentor js
	$('body').on('click','.search-icon-popup',function(e){
		e.preventDefault();
		$(this).parents('.elbzotech-search-wrap').addClass('open-search-popup');
	})
	$('body').on('click','.elbzotech-close-search-form',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).parents('.elbzotech-search-wrap').removeClass('open-search-popup');
	})
	$('body').on('click','.elbzotech-account-manager',function(){
		$(this).find('.elbzotech-popup-overlay').addClass('elbzotech-popup-open');
	})
	$('body').on('click','.elbzotech-mailchimp-style2 .elbzotech-close-popup',function(){
		$(this).parents('.elbzotech-mailchimp-style2').addClass('hide');
	})
	$('body').on('click','.elbzotech-close-popup',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).parents('.elbzotech-popup-overlay').removeClass('elbzotech-popup-open');
		$(this).parents('.elbzotech-mailchimp-style2').addClass('hide');
	})
	$('body').on('click','.elbzotech-mini-cart-side .mini-cart-link',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).parents('.elbzotech-mini-cart').toggleClass('open-side');		
		$(this).parents('.elbzotech-mini-cart').find('.list-mini-cart-item').each(function(){
			var seff = $(this).parents('.mini-cart-content');
			var c_height = seff.height() - $('#wpadminbar').height() - seff.find('.mini-cart-footer').height() - seff.find('> h2').outerHeight() - 20;
			$(this).css('max-height',c_height);
		})
	})
	$('body').on('click','.mini-cart-side-overlay,.elbzotech-close-mini-cart',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).parents('.elbzotech-mini-cart-side').removeClass('open-side');
	})
	 function bzotech_packery_masory_e(){
    	if($('.grid-masory-packery').length>0){
    		setTimeout(function() {
    		$('.grid-masory-packery').each(function(){
    			
	    			$(this).packery({
					  // options
					  itemSelector: '.width_masory',
					  gutter: 0
					});
				
    		});
		 	},1000);
		 }
    }
    function slider_accordion_slider(){
		$('.accordion-slider').each(function(){
			var width =  $(this).attr('data-width');
			var height =  $(this).attr('data-height');
			var responsivemode = $(this).attr('data-responsivemode');
			var visiblepanels =  $(this).attr('data-visiblepanels');
			var autoplay = $(this).attr('data-autoplay');
			var startpanel =  Number($(this).attr('data-startpanel'));
			if(!width) width = 1000;
			if(!height) height = 620;
			if(!responsivemode) responsivemode = 'auto';
			if(!visiblepanels) visiblepanels = 3;
			if(!autoplay) autoplay = 'false';
			if(!startpanel) startpanel = 0;
			$(this).accordionSlider({
				width: Number(width),
				height: Number(height),
				responsiveMode: responsivemode,
				visiblePanels:Number(visiblepanels),
				closePanelsOnMouseOut: false,
				autoplay: autoplay,
				startPanel: Number(startpanel),
			});
		})

	}
	function bzotech_accordion_e(){
    	if($('.elbzotech-accordion').length>0){
    		 $('.elbzotech-accordion').each(function(){
    		 	var active = $(this).data('active');
    		 	var animate = $(this).data('animate');
    		 	var heightStyle = $(this).data('heightstyle');
    		 	$(this).accordion(
    		 		{
					  active:active,
					  animate: animate,
					  heightStyle: 'content'
					}
    		 	);
    		 })
        }
    }
	function elbzotech_mailchimp_fix(){
		$('.sv-mailchimp-form').each(function(){
            var placeholder = $(this).attr('data-placeholder');
             var submit = $(this).attr('data-submit');
             var icon = $(this).attr('data-icon');
             if(placeholder) $(this).find('input[name="EMAIL"]').attr('placeholder',placeholder);
             if(submit) {
             	$(this).find('input[type="submit"]').val(submit);
             	$(this).find('button[type="submit"]').html(submit);
             }
             if(icon) {
             	$(this).find('button[type="submit"]').html('<i class="'+icon+'"></i>');
             }
             if(icon && submit) {
             	$(this).find('button[type="submit"]').html('<i class="'+icon+'"></i>'+submit);
             }
        })
	}
	function bzotech_column_grid(){
    	if($('.blog-grid-view').length>0){
	       	$('.blog-grid-view').each(function(){
 				var items_custom = $(this).data('column-grid');
 				if(items_custom){
					items_custom = items_custom.split(',');
					
					var i;
					for (i = 0; i < items_custom.length; i++) { 
						items_custom[i] = items_custom[i].split(':');
					    if($(window).width()>items_custom[i][0]){
					    	$(this).find('.list-col-item').css('width',100/items_custom[i][1]+'%');
					    }
					}
					
				
				}
	       	})
	    }
    }
	function elbzotech_swiper_slider(){
		setTimeout(function() {
			$('.elbzotech-swiper-slider:not(.swiper-container-initialized)').each(function(){

			var slidesPerView = Number($(this).attr('data-items'));
			var items_custom = $(this).attr('data-items-custom');
			var direction = $(this).attr('data-direction');
			var slidertype = $(this).attr('data-slidertype');
			var effect = $(this).attr('data-effect');
			var scrollbar = $(this).parent().find('.swiper-scrollbar');
			var draggable = false;
			if(scrollbar.length){
				draggable = true;
			}
			if(!effect) effect = false;
			if(!direction) direction = 'horizontal';			
			if(!slidesPerView) slidesPerView = 1;			
			var number_active = slidesPerView;

			var spaceBetween = Number($(this).attr('data-space'));
			if(!spaceBetween) spaceBetween = 0;

			var slidesPerColumn = Number($(this).attr('data-column'));
			if(!slidesPerColumn) slidesPerColumn = 1;

			var slidesPerColumnFill = 'column';
			if(slidesPerColumn>1)  slidesPerColumnFill = 'row';

			var loop = $(this).attr('data-loop');
			if(loop != 'yes') loop = false;
			else loop = true;

			var auto = $(this).attr('data-auto');
			if(auto != 'yes') auto = false;
			else auto = true;			
			if(auto) slidesPerView = 'auto';


			var centeredSlides = $(this).attr('data-center');
			if(centeredSlides != 'yes') centeredSlides = false;
			else centeredSlides = true;

			var breakpoints = {};
			var items_widescreen = Number($(this).attr('data-items-widescreen'));
			var items_laptop = Number($(this).attr('data-items-laptop'));
			var items_tablet_extra = Number($(this).attr('data-items-tablet-extra'));
			var items_tablet = Number($(this).attr('data-items-tablet'));
			var items_mobile_extra = Number($(this).attr('data-items-mobile-extra'));
			var items_mobile = Number($(this).attr('data-items-mobile'));

			var space_widescreen = Number($(this).attr('data-space-widescreen'));
			var space_laptop = Number($(this).attr('data-space-laptop'));
			var space_tablet_extra = Number($(this).attr('data-space-tablet-extra'));
			var space_tablet = Number($(this).attr('data-space-tablet'));
			var space_mobile_extra = Number($(this).attr('data-space-mobile-extra'));
			var space_mobile = Number($(this).attr('data-space-mobile'));



			if(items_tablet || items_mobile || items_widescreen || items_laptop || items_tablet_extra || items_mobile_extra || space_tablet || space_mobile || space_widescreen || space_laptop || space_tablet_extra || space_mobile_extra){
				if(auto) items_tablet = items_mobile = 'auto';
				if(items_widescreen == '') items_widescreen = slidesPerView;
				if(items_laptop == '') items_laptop = slidesPerView;
				if(items_tablet_extra == '') items_tablet_extra = items_laptop;
				if(items_tablet == '') items_tablet = items_tablet_extra;
				if(items_mobile_extra == '') items_mobile_extra = items_tablet;
				if(items_mobile == '') items_mobile = items_mobile_extra;

				if(space_widescreen == '') space_widescreen = spaceBetween;
				if(space_laptop == '') space_laptop = spaceBetween;
				if(space_tablet_extra == '') space_tablet_extra = space_laptop;
				if(space_tablet == '') space_tablet = space_tablet_extra;
				if(space_mobile_extra == '') space_mobile_extra = space_tablet;
				if(space_mobile == '') space_mobile = space_mobile_extra;
				
				breakpoints = {
					0: {
				      	slidesPerView: items_mobile,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_mobile
				    },
				    768: {
				      	slidesPerView: items_mobile_extra,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_mobile_extra
				    },
				    881: {
				      	slidesPerView: items_tablet,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_tablet
				    },
				    1025: {
				      	slidesPerView: items_tablet_extra,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_tablet_extra
				    },
				    1201: {
				      	slidesPerView: items_laptop,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_laptop
				    },
				    1367: {
				      	slidesPerView: slidesPerView,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: spaceBetween
				    },
				    2401: {
				      	slidesPerView: items_widescreen,
				      	grid: {
					        rows: slidesPerColumn,
					   		fill:slidesPerColumnFill,
					    },
				      	spaceBetween: space_widescreen
				    }
				}
				
			}
			
			if(items_custom){
				items_custom = items_custom.split(',');
				var breakpoints = {};
				var i;
				
				for (i = 0; i < items_custom.length; i++) { 

				    items_custom[i] = items_custom[i].split(':');
				    
				    var res_dv = {};
				    res_dv.slidesPerView = parseInt(items_custom[i][1], 10);
				    
				    if(767 > Number(items_custom[i][0]) && Number(items_custom[i][0]) >= 0 && space_mobile){
				    	res_dv.spaceBetween = space_mobile;
				    }else if(1170 > Number(items_custom[i][0]) && Number(items_custom[i][0]) >= 767 && space_tablet){
				    	res_dv.spaceBetween = space_tablet;
				    }else if(Number(items_custom[i][0]) >= 1170 && spaceBetween){
				    	res_dv.spaceBetween = spaceBetween;
				    }
				    res_dv.slidesPerColumn = slidesPerColumn;
				    breakpoints[items_custom[i][0]] = res_dv;
					var max_items_custom = items_custom[0][0];
				    var max_items_custom_v = items_custom[0][1];
				    if(max_items_custom<items_custom[i][0]) {
				    	max_items_custom = items_custom[i][0]; 
				    	max_items_custom_v = parseInt(items_custom[i][1], 10);
				    }
				}
				if(Number(max_items_custom) < 1170) {
					var breakpoints_c = {
					    1170: {
					      	slidesPerView: Number(max_items_custom_v),
					      	spaceBetween: spaceBetween,
					      	slidesPerColumn: slidesPerColumn,
					    }
					}
					
					let a = {...breakpoints_c, ...breakpoints};
					breakpoints = a;
				}
			}
			var autoplay = false;
			var speed = Number($(this).attr('data-speed'));
			if(speed){
				autoplay = {};
				autoplay.delay = speed;
			}

			var navigation = $(this).attr('data-navigation');
			if(navigation == '') navigation = {};
			else navigation = {
		        	nextEl: $(this).parent().find('.swiper-button-next').get(0),
		            prevEl: $(this).parent().find('.swiper-button-prev').get(0),
		        };

		    var pagination = $(this).attr('data-pagination');
			if(pagination == '') pagination = {};
			else if(pagination == 'number')
			{
				pagination = {

			        	el: $(this).parent().find('.swiper-pagination').get(0),
		        		clickable: true,
		        		renderBullet: function (index, className) {
				           return '<span class="' + className + ' sw-pev'+ (index + 1) +'">' + (index + 1) + '</span>';
				        },

			    };
		    } else
			pagination = {
		        	el: $(this).parent().find('.swiper-pagination').get(0),
	        		clickable: true,
	        		renderBullet: function (index, className) {
				           return '<span class="' + className + ' sw-pev'+ (index + 1) +'"></span>';
				        },
		     };
		    var galleryThumbs ='';
		    if($(this).parent().find('.gallery-thumbs').length){
		    	var navigation_gallery = $(this).parent().find('.gallery-thumbs').attr('data-navigation');
				if(navigation_gallery == '') navigation_gallery = {};
				else navigation_gallery = {
			        	nextEl: $(this).parent().find('.swiper-button-gallery-next').get(0),
			            prevEl: $(this).parent().find('.swiper-button-gallery-prev').get(0),
		        };


		    	var spaceBetween_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space'));
				if(!spaceBetween_gallery) spaceBetween_gallery = 0;

		    	var slidesPerView_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items'));	
				if(!slidesPerView_gallery) slidesPerView_gallery = 3;

		    	var items_widescreen_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-widescreen'));
				var items_laptop_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-laptop'));
				var items_tablet_extra_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-tablet-extra'));
				var items_tablet_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-tablet'));
				var items_mobile_extra_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-mobile-extra'));
				var items_mobile_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-items-mobile'));

				var space_widescreen_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-widescreen'));
				var space_laptop_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-laptop'));
				var space_tablet_extra_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-tablet-extra'));
				var space_tablet_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-tablet'));
				var space_mobile_extra_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-mobile-extra'));
				var space_mobile_gallery = Number($(this).parent().find('.gallery-thumbs').attr('data-space-mobile'));



				if(items_widescreen_gallery == '') items_widescreen_gallery = slidesPerView_gallery;
				if(items_laptop_gallery == '') items_laptop_gallery = slidesPerView_gallery;
				if(items_tablet_extra_gallery == '') items_tablet_extra_gallery = items_laptop_gallery;
				if(items_tablet_gallery == '') items_tablet_gallery = items_tablet_extra_gallery;
				if(items_mobile_extra_gallery == '') items_mobile_extra_gallery = items_tablet_gallery;
				if(items_mobile_gallery == '') items_mobile_gallery = items_mobile_extra_gallery;

				if(space_widescreen_gallery == '') space_widescreen_gallery = spaceBetween_gallery;
				if(space_laptop_gallery == '') space_laptop_gallery = spaceBetween_gallery;
				if(space_tablet_extra_gallery == '') space_tablet_extra_gallery = space_laptop_gallery;
				if(space_tablet_gallery == '') space_tablet_gallery = space_tablet_extra_gallery;
				if(space_mobile_extra_gallery == '') space_mobile_extra_gallery = space_tablet_gallery;
				if(space_mobile_gallery == '') space_mobile_gallery = space_mobile_extra_gallery;

			    var direction2 = $(this).parent().find('.gallery-thumbs').attr('data-direction');
		    	if(!direction2)  direction2 = 'horizontal';
		    	var select_gallery = $(this).parent().find('.gallery-thumbs').get(0);
				galleryThumbs = new Swiper(select_gallery, {
					spaceBetween: spaceBetween_gallery,
					slidesPerView: slidesPerView_gallery,
					direction: direction2,
					centeredSlides: centeredSlides,
					navigation: navigation_gallery,
					slideToClickedSlide: true,
					loop: loop,
					//freeMode: true,
					loopedSlides: 4, //looped slides should be the same
					breakpoints: {
					    0: {
					      	slidesPerView: items_mobile_gallery,
					      	spaceBetween: space_mobile_gallery
					    },
					    768: {
					      	slidesPerView: items_mobile_extra_gallery,
					      	spaceBetween: space_mobile_extra_gallery
					    },
					    881: {
					      	slidesPerView: items_tablet_gallery,
					      	spaceBetween: space_tablet_gallery
					    },
					    1025: {
					      	slidesPerView: items_tablet_extra_gallery,
					      	spaceBetween: space_tablet_extra_gallery
					    },
					    1201: {
					      	slidesPerView: items_laptop_gallery,
					      	spaceBetween: space_laptop_gallery
					    },
					    1367: {
					      	slidesPerView: slidesPerView_gallery,
					      	spaceBetween: spaceBetween_gallery
					    },
					    2401: {
					      	slidesPerView: items_widescreen_gallery,
					      	spaceBetween: space_widescreen_gallery
					    }
					},
					watchSlidesVisibility: true,
					watchSlidesProgress: true,
				});
			   
			}
		    if(slidertype == 'marquee'){
		    	var swiper2 = new Swiper(this,{
		    		spaceBetween: 0,
					centeredSlides: true,
					speed: 3000,
					autoplay: {
						delay: 1,
					},
					loop: true,
					slidesPerView:'auto',
					allowTouchMove: false,
					disableOnInteraction: true
		    	});
		    }else{
		    	var swiper = new Swiper(this, {
					autoHeight: false,
					direction: direction,
			      	slidesPerView: slidesPerView,
			      	spaceBetween: spaceBetween,
			      	grid: {
				        rows: slidesPerColumn,
				   		fill:slidesPerColumnFill,
				    },
			      	loop: loop,
			      	centeredSlides: centeredSlides,
			      	breakpoints: breakpoints,
			      	autoplay: autoplay,
			        navigation: navigation,
			        pagination: pagination,
			        observer: true,
					observeParents: true,
					effect: effect,
					fadeEffect: {
					    crossFade: true
					  },
			        scrollbar: {
				        el: scrollbar.get(0),
				        hide: true,
			            draggable : draggable,
				    },
				    thumbs: {
				        swiper: galleryThumbs,
				    },
				    on: {
				        
				        init: function () {
				          var activeIndex = this.activeIndex;
				          $('.swiper-slide').removeClass('bzotech-active-swiper');
				          var i;
				          for (i = activeIndex; i < number_active+activeIndex-1; i++){
				          	$('.swiper-slide:nth-child('+i+')').addClass('bzotech-active-swiper');
				          }
							bzotech_packery_masory();
				          
				        },
						slideChange: function () {

				          var activeIndex = this.activeIndex+1;
				          $('.swiper-slide').removeClass('bzotech-active-swiper');
				          var i;
				          for (i = activeIndex; i < number_active+activeIndex; i++){
				          	$('.swiper-slide:nth-child('+i+')').addClass('bzotech-active-swiper');
				          }
				        },
				        transitionEnd: function () {
				        },
				    }
			    });
		    }
			if(slidertype == 'marquee'){
				$(this).on('mouseenter', function(e){
					swiper2.autoplay.stop();
				});
				$(this).on('mouseleave', function(e){
					swiper2.autoplay.start();
				});
			}
		})
		},2000);
		
	}
	 function bzotech_packery_masory(){
    	if($('.grid-masory-packery').length>0){
		 	$('.grid-masory-packery').packery({
			  // options
			  itemSelector: '.width_masory ',
			  gutter: 0
			});
		 }
    }
	function background_slider_swiper(){
		$('.bg-slider-swiper .swiper-thumb').each(function(){
			$(this).find('img').css('height',$(this).find('img').attr('height'));
			var src=$(this).find('img').attr('src');
			$(this).css('background-image','url("'+src+'")');
		});	
	}
    $(window).resize(function(){
    	$('.list-mini-cart-item').each(function(){
			var seff = $(this).parents('.mini-cart-content');
			var c_height = seff.height() - $('#wpadminbar').height() - seff.find('.mini-cart-footer').height() - seff.find('> h2').outerHeight() - 20;
			$(this).css('max-height',c_height);
		})
    });

	var WidgetHelloWorldHandler = function( $scope, $ ) {
		elbzotech_swiper_slider();
		 background_slider_swiper();
		 bzotech_column_grid();
		 bzotech_accordion_e();
		 slider_accordion_slider();
		 bzotech_packery_masory_e();
		 
		if($('.bzotech-countdown').length>0){
       
            $('.bzotech-countdown').each(function(){
                var self = $(this);
                var finalDate = self.data('date');
                var html_date = self.html();
                self.countdown(finalDate, function(event) {
                    self.html(event.strftime(''+html_date
                    ));
                });
            });
        }
		if($('.box-hover-dir').length>0){
			$('.box-hover-dir').each( function() {
				$(this).hoverdir(); 
			});
		}
		//List Item Masonry
		if($('.realestate-grid-view.grid-masonry .list-realestate-wrap').length>0){
			var $content = $('.realestate-grid-view.grid-masonry .list-realestate-wrap');
			$content.imagesLoaded( function() {
			    $content.masonry();
			});
			$('.nav-tabs a').on('click',function(){

				$content.imagesLoaded( function() {
				    $content.masonry();
				});

			});
		}
		//List Item Masonry
		if($('.blog-grid-view.grid-masonry .list-post-wrap').length>0){
			var $content = $('.blog-grid-view.grid-masonry .list-post-wrap');
			$content.imagesLoaded( function() {
			    $content.masonry();
			});
		}
		if($('.product-grid-view.grid-masonry .list-product-wrap').length>0){
			var $content2 = $('.product-grid-view.grid-masonry .list-product-wrap');
			$content2.imagesLoaded( function() {
			    $content2.masonry();
			});
		}
		if($('.ktrv').length>0){
	        var Width = $('.ktrv .item-slider-global-style5').outerWidth();
	        var container_Width = $('.ktrv').parent().outerWidth();
	        var Height = $('.ktrv .item-slider-global-style5').outerHeight();
	        console.log(container_Width);
	        $('.ktrv').css('width',container_Width);
	        $('.ktrv').css('height',Height);
	        var ktrv = $(".ktrv").waterwheelCarousel({
	            flankingItems: 3,
	            containerWidth:container_Width,
	            containerHeight:Height,
	            forcedImageWidth:Width,
	            forcedImageHeight:Height,
	            opacityMultiplier:1,
	            separation:300,
	            flankingItems:2,
	            movingToCenter: function($item) {
	                $('#callback-output').prepend('movingToCenter: ' + $item.attr('id') + '<br/>');
	            },
	            movedToCenter: function($item) {
	                $('#callback-output').prepend('movedToCenter: ' + $item.attr('id') + '<br/>');
	            },
	            movingFromCenter: function($item) {
	                $('#callback-output').prepend('movingFromCenter: ' + $item.attr('id') + '<br/>');
	            },
	            movedFromCenter: function($item) {
	                $('#callback-output').prepend('movedFromCenter: ' + $item.attr('id') + '<br/>');
	            },
	            clickedCenter: function($item) {
	                $('#callback-output').prepend('clickedCenter: ' + $item.attr('id') + '<br/>');
	            }
	        });
	    }
	};

	var WidgetMailchimpHandler = function( $scope, $ ) {
		elbzotech_mailchimp_fix();
	};
	
	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-posts.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-products.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-instagram.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-mailchimp.default', WidgetMailchimpHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech_realestate.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-slider.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-accordion.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech_info_box.default', WidgetHelloWorldHandler );

		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-posts-global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-products-global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-instagram-global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-mailchimp-global.default', WidgetMailchimpHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech_realestate-global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-slider_global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech-accordion-global.default', WidgetHelloWorldHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bzotech_info_box_global.default', WidgetHelloWorldHandler );
	} );
} )( jQuery );
