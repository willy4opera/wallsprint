<?php

// Add to skin custom styling 
function wcdp_add_skin_custom_style($wcdp_settings){
	
	$general      = $wcdp_settings['general'];
	$style        = $wcdp_settings['style'];
	$parameters   = $wcdp_settings['parameters'];
	$loaderGif    = $general['loader_gif'];
	$loader_w     = $general['loader_w'];
	$loader_h     = $general['loader_h'];	
	$btnRGBA      = wcdp_hex_to_rgba($style['color_icons']['RGB'], 0.3);
	$snapGuides   = isset($style['snap_color_border']) ? $style['snap_color_border']['RGB'] : '#e10886';
	$scrollRGB    = isset($style['scrollbar_bg']) ? $style['scrollbar_bg']['RGB'] : '#ffffff';
	$scrollRGBA   = wcdp_hex_to_rgba($scrollRGB, 0.8);
	
	if(empty($loaderGif))
		$loaderGif = WCDP_URL.'/assets/images/loader.gif';
	
	if(empty($loader_w) || $loader_w <= 0)
		$loader_w = 64;
	
	if(empty($loader_h) || $loader_h <= 0)
		$loader_h = 64;
	
    ?>	
    <style type="text/css">
        @font-face {
            font-family: 'wcdp-sprites';
            src: url('<?php echo WCDP_URL;?>/assets/fonts/wcdp-sprites.eot?qgd1zo');
            src: url('<?php echo WCDP_URL;?>/assets/fonts/wcdp-sprites.eot?qgd1zo#iefix') format('embedded-opentype'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/wcdp-sprites.ttf?qgd1zo') format('truetype'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/wcdp-sprites.woff?qgd1zo') format('woff'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/wcdp-sprites.svg?qgd1zo#wcdp-sprites') format('svg');
            font-weight: normal;
            font-style: normal;
        }
        @font-face { 
	        font-family: 'titillium';
	        src: url('<?php echo WCDP_URL;?>/assets/fonts/titillium-regular.eot');
	        src: url('<?php echo WCDP_URL;?>/assets/fonts/titillium-regular.svg#titillium-regular') format('svg'),
		         url('<?php echo WCDP_URL;?>/assets/fonts/titillium-regular.eot?#iefix') format('embedded-opentype'),
		         url('<?php echo WCDP_URL;?>/assets/fonts/titillium-regular.woff') format('woff'),
		         url('<?php echo WCDP_URL;?>/assets/fonts/titillium-regular.ttf') format('truetype');
	        font-weight: 400;
	        font-style: normal;
        }
        @font-face {
            font-family: 'titillium';
            src: url('<?php echo WCDP_URL;?>/assets/fonts/titillium-semibold.eot');
            src: url('<?php echo WCDP_URL;?>/assets/fonts/titillium-semibold.svg#titillium-semibold') format('svg'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/titillium-semibold.eot?#iefix') format('embedded-opentype'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/titillium-semibold.woff') format('woff'),
                 url('<?php echo WCDP_URL;?>/assets/fonts/titillium-semibold.ttf') format('truetype');
	        font-weight: 600;
	        font-style: normal;
        }
	    <?php
	    if($wcdp_settings['fonts']){
	        foreach($wcdp_settings['fonts'] as $font){				
                if($font['id'] == 'wf'){
                    $fontName = sanitize_title($font['name']);
                    wp_register_style($fontName, $font['url'], array(), false, 'all');
                    wp_enqueue_style($fontName);					
				}
				else{
				    ?>
                    @font-face{
				        font-family:<?php echo $font['name']; ?>; src: url('<?php echo $font['url']; ?>');
				    }
					<?php 
				}
	        }
	    }
	    ?>	
	    #wcdp-tabs-content, #wcdp-box-options-btn, #wcdp-tabs-icons > span.vtab-selected{
		    background-color: <?php echo $style['tabs_bg']['RGB'];?>;
	    }
	    #wcdp-tabs-content .dp-col-8 span,
	    #wcdp-tabs-content .dp-input-btn span,
		#wcdp-tabs-content .dp-hori-tab span,
		#wcdp-tabs-content .dp-toolbar-img span,
	    #wcdp-vertical-tab .dp-btn-style span,
	    #wcdp-vertical-tab .dp-btn-style-ico,
	    #wcdp-vertical-tab .dp-btn-style-ico span,
	    #wcdp-vertical-tab .dp-btn-style-ico label,
		#wcdp-tabs-content .dp-contain-mask .dp-layer-row,
		#wcdp-contain-layers .dp-layer-row{
		    color: <?php echo $style['buttons_color']['RGB'];?>;
		    background-color: <?php echo $style['buttons_bg']['RGB'];?>;
	    }
	    #wcdp-tabs-content .dp-col-8 span:hover,
	    #wcdp-tabs-content .dp-input-btn span:hover,
		#wcdp-tabs-content .dp-hori-tab span:hover,
		#wcdp-tabs-content .dp-toolbar-img span:hover,
	    #wcdp-vertical-tab .dp-btn-style span:hover,
	    #wcdp-vertical-tab .dp-btn-style-ico:hover,
        #wcdp-vertical-tab .dp-btn-style-ico:hover span,
        #wcdp-vertical-tab .dp-btn-style-ico:hover label,
        #wcdp-upload-images.dp-btn-upload-hover,
        #wcdp-upload-images.dp-btn-upload-hover label,
        #wcdp-upload-images.dp-btn-upload-hover span,
	    #wcdp-tabs-content .dp-col-8 span.dp-item-selected,
		#wcdp-tabs-content .dp-hori-tab span.htab-selected,
		#wcdp-tabs-content .dp-toolbar-img span.opt-selected,
		#wcdp-tabs-content .dp-contain-mask .dp-layer-row:hover,
		#wcdp-contain-layers .dp-layer-row:hover,
		#wcdp-contain-layers .dp-layer-row.dp-active{
		    cursor: pointer;
		    color: <?php echo $style['buttons_color_hover']['RGB'];?>;
		    background-color: <?php echo $style['buttons_bg_hover']['RGB'];?>;
	    }
        #wcdp-container input[type=range]::-webkit-slider-thumb{
            background: <?php echo $style['buttons_bg_hover']['RGB'];?>;
        }
        #wcdp-container input[type=range]::-moz-range-thumb{
            background: <?php echo $style['buttons_bg_hover']['RGB'];?>;
        }
        #wcdp-container input[type=range]::-ms-thumb{
            background: <?php echo $style['buttons_bg_hover']['RGB'];?>;
        }
        #wcdp-editor-container .wcdp-snap-line-h{
	        border-top: 1px dashed <?php echo $snapGuides;?>;
        }
        #wcdp-editor-container .wcdp-snap-line-v{
	        border-left: 1px dashed <?php echo $snapGuides;?>;
        }
        #wcdp-settings-panel .variations .dp-row .dp-attr-radio .dp-radio-item input + label:after{
	        background: <?php echo $style['buttons_bg_hover']['RGB'];?>;
        }
		#wcdp-settings-panel .variations .dp-row .dp-attr-radio .dp-radio-item input + label:before{
		    border: 2px solid <?php echo $style['buttons_color']['RGB'];?>;
		}
		#wcdp-tabs-content .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{
			background: <?php echo $scrollRGBA;?>;
		}
	    #wcdp-tabs-content label, #wcdp-tabs-content .dp-title-label,
		#wcdp-zoom-canvas-container .dp-zoom-value{
		    color: <?php echo $style['text_color']['RGB'];?>;
	    }
	    .jBox-Tooltip .jBox-container,
	    .jBox-Tooltip .jBox-pointer:after{
		    color: <?php echo $style['tooltip_color']['RGB'];?>;
		    background: <?php echo $style['tooltip_bg']['RGB'];?>;
	    }	
       .jBox-Confirm-button-cancel, .jBox-Confirm-button-submit{
		    color: <?php echo $style['buttons_color_jbox']['RGB'];?> !important;
		    background: <?php echo $style['buttons_bg_jbox']['RGB'];?> !important;
	    }
        .jBox-Confirm-button-cancel:hover, .jBox-Confirm-button-cancel:active,
	    .jBox-Confirm-button-submit:hover, .jBox-Confirm-button-submit:active{
		    color: <?php echo $style['buttons_color_hover_jbox']['RGB'];?> !important;
		    background: <?php echo $style['buttons_bg_hover_jbox']['RGB'];?> !important;
	    }
	    #wcdp-tabs-content .dp-contain-box .dp-folder-contain{
		    background: <?php echo $style['buttons_color_folders_bg']['RGB'];?>;
	    }
	    #wcdp-tabs-content .dp-contain-box .dp-folder-open{
		    background: <?php echo $style['buttons_color_folders_bg_select']['RGB'];?>;
	    }
        #wcdp-tabs-content .dp-contain-box .dp-folder-contain span,
	    #wcdp-tabs-content .dp-contain-box .dp-folder-contain label{
		    color: <?php echo $style['buttons_color_folders']['RGB'];?>
	    }
	    #wcdp-tabs-content .dp-contain-box .dp-folder-open span,
	    #wcdp-tabs-content .dp-contain-box .dp-folder-open label{
		    color: <?php echo $style['buttons_color_folders_select']['RGB'];?>
	    }
        #wcdp-toolbar-options > span,
		#wcdp-tabs-content .wcdp-box-filters-rng label,
		#wcdp-tabs-content .wcdp-content-tools label{
            border-right: 1px solid <?php echo $style['border_color']['RGB'];?>;
        }
        #wcdp-container.md__rtl #wcdp-toolbar-options > span,
		#wcdp-container.md__rtl #wcdp-tabs-content .wcdp-box-filters-rng label,
		#wcdp-container.md__rtl #wcdp-tabs-content .wcdp-content-tools label{
            border-left: 1px solid <?php echo $style['border_color']['RGB'];?>;
			border-right: 0;
        }
		#wcdp-container.md__rtl #wcdp-tabs-icons>span.vtab-selected,
        #wcdp-container.md__rtl #wcdp-tabs-content .wcdp-tab-section .dp-hori-tab span:not(:last-child),
        #wcdp-container.md__rtl #wcdp-tabs-content .wcdp-tab-section .dp-toolbar-img span:not(:last-child){
	        border-right: 1px solid <?php echo $style['border_color']['RGB'];?>;
        }
		#wcdp-container [id^="wcdp-btn-"],		
		#wcdp_cropper_contain [id^="wcdp-btn-"],
		#wcdp-zoom-canvas-container .wcdp-icon-zoom{
            color: <?php echo $style['color_icons']['RGB'];?>;       
        }
        #wcdp-toolbar-options > span, #wcdp-tabs-icons > span{
	        background-color: <?php echo $style['bg_icons']['RGB'];?>;
        }
        #wcdp-toolbar-options > span:hover, #wcdp-tabs-icons > span:hover,
	    #wcdp-toolbar-options span.btn-enabled, #wcdp_cropper_contain span.btn-enabled,
		#wcdp_cropper_contain [id^="wcdp-btn-"]:hover{
            cursor: pointer;
            color: <?php echo $style['color_icons_hover']['RGB'];?> !important;
            background-color: <?php echo $style['bg_icons_hover']['RGB'];?> !important;
        }
		#wcdp-tabs-content .wcdp-box-filters-btn span.dp-item-selected,
		#wcdp-tabs-content .wcdp-box-filters-btn span:hover,
        #wcdp-tabs-content .wcdp-box-svg-multicolor .sp-replacer:hover,
        #wcdp-tabs-content .wcdp-box-svg-stroke-multicolor .sp-replacer:hover,
        #wcdp-tabs-content .dp-box-bgcolors span:hover,
        #wcdp-tabs-content .dp-box-bgcolors span.sp-thumb-active,
		#wcdp-tabs-content .dp-box-bgcolors span.sp-thumb-close.sp-thumb-active:hover{
			cursor: pointer;
			border-color: <?php echo $style['buttons_bg_hover']['RGB'];?>;
		}
		#wcdp-tabs-content .wcdp-box-filters-btn span{
		    border: 3px solid <?php echo $style['tabs_content']['RGB'];?>;
		}
		#wcdp-toolbar-options.dp-sensor-811 > span:not(:nth-child(1n+10)),
		#wcdp-toolbar-options.dp-sensor-460 > span:not(:nth-child(1n+13)),		
        #wcdp-vertical-tab .dp-title-label,
		#wcdp-vertical-tab .dp-border-btn-style,
		#wcdp-tabs-content .dp-contain-mask .dp-layer-row:not(:last-child),
		#wcdp-vertical-tab .wcdp-content-tools .dp-row:not(:last-child){
	        border-bottom: 1px solid <?php echo $style['border_color']['RGB'];?>;
        }
		#wcdp-tabs-content .wcdp-box-filters-rng .dp-row{
			border-top: 1px solid <?php echo $style['border_color']['RGB'];?>;
		}
		<?php
		if($parameters['box_shadow'] == 'on'){
			?>
			#wcdp-editor-container .upper-canvas{
			    -webkit-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.17), 0 2px 10px 0 rgba(0,0,0,0.13);
                -moz-box-shadow: 0 2px 5px 0 rgba(0,0,0,0.17), 0 2px 10px 0 rgba(0,0,0,0.13);
                box-shadow: 0 2px 5px 0 rgba(0,0,0,0.17), 0 2px 10px 0 rgba(0,0,0,0.13);
			}
            #wcdp-canvas-thumbs-container img,
			#wcdp-tabs-content .dp-contain-caz img,
            #wcdp-tabs-content .dp-contain-tpl img{
                box-shadow: 1px 1px 3px 0px rgba(0, 0, 0, 0.2);
            }			
			<?php
		}
		if($parameters['border_solid'] == 'on'){
			?>
			#wcdp-editor-container .upper-canvas,
			#wcdp-canvas-thumbs-container img,
			#wcdp-tabs-content .dp-contain-caz img,
            #wcdp-tabs-content .dp-contain-tpl img,			
			<?php
		}
		?>
        #wcdp-toolbar-options,
        #wcdp-tabs-icons > span,
        #wcdp-tabs-content .dp-col-8 span,
	    #wcdp-tabs-content .dp-input-btn span,
		#wcdp-tabs-content .dp-hori-tab span,
		#wcdp-tabs-content .dp-toolbar-img span,
        #wcdp-tabs-content,
        #wcdp-box-options-btn,
	    #wcdp-tabs-content input,
        #wcdp-tabs-content select,
        #wcdp-tabs-content textarea,
        #wcdp-tabs-content .sp-replacer,
        #wcdp-tabs-content .sp-preview,
		#wcdp-tabs-content .dp-title-box,
        #wcdp-vertical-tab .dp-border-style,
	    #wcdp-vertical-tab .dp-folder-contain,
		#wcdp-images-panel .dp-img-res img,
	    #wcdp-vertical-tab .dp-caz-img,
        #wcdp-vertical-tab .dp-btn-style span,
	    #wcdp-tabs-content .dp-img-contain,
        #wcdp-tabs-content .wcdp-box-svg-multicolor .sp-replacer,
        #wcdp-tabs-content .wcdp-box-svg-stroke-multicolor .sp-replacer,
		#wcdp-tabs-content .dp-contain-mask .dp-layer-thumb,
		#wcdp-tabs-content .dp-box-bgcolors span.sp-thumb-close.sp-thumb-active,
		#wcdp-tabs-content .dp-box-bgcolors span,
		#wcdp-contain-layers .dp-layer-row,
		#wcdp-contain-layers .dp-layer-thumb,
		#wcdp-contain-layers .dp-sortable-placeholder,
	    #wcdp-settings-panel .woocommerce-variation,
		#wcdp-zoom-canvas-container,
		.sp-container .sp-palette .sp-thumb-el{
            border: 1px solid <?php echo $style['border_color']['RGB'];?>;
        }
	    #wcdp-tabs-content .sp-replacer,
        #wcdp-tabs-content input,	
        #wcdp-tabs-content select,
        #wcdp-tabs-content textarea,
		#wcdp-tabs-content .dp-title-box,
	    #wcdp-tabs-content .dp-box-note p,
	    #wcdp-tabs-content .dp-content-style,
	    #wcdp-settings-panel .price span,
	    #wcdp-settings-panel p,
		#wcdp-zoom-canvas-container,
		#wcdp-crop-tools{
            color: <?php echo $style['text_color']['RGB'];?>;
		    background-color: <?php echo $style['tabs_content']['RGB'];?> !important;
        }
        #wcdp-btn-undo.btn-disabled, #wcdp-btn-redo.btn-disabled,
	    #wcdp-btn-preview.btn-disabled,	#wcdp-tabs-content span.dp-search-empty{
		    color: <?php echo $btnRGBA; ?> !important;
	    }
	    #wcdp-shapes-panel .dp-box-shap svg{
	        fill: <?php echo $style['color_shapes_editor']['RGB'];?>;
	    }
	    .dp-loader-box{		    
		    width: 18px;
            height: 18px;
		    display: none;
			background: url('<?php echo WCDP_URL.'/assets/images/ajax-loader.gif'; ?>') no-repeat;
	    }
        .dp-loader-editor{
            width: <?php echo $loader_w; ?>px;
            height: <?php echo $loader_h; ?>px;
		    background: url('<?php echo $loaderGif; ?>') no-repeat;
	    }
		.sp-container{
			border: 1px solid <?php echo $style['picker_color_border']['RGB'];?> !important;
			background-color: <?php echo $style['picker_color_bg']['RGB'];?> !important;
		}
        .sp-palette .sp-thumb-el:hover, .sp-palette .sp-thumb-el.sp-thumb-active{
            border-color: <?php echo $style['buttons_bg_hover']['RGB'];?>;
		}
        .sp-palette-container{
            border-right: 0 !important;
        }
		.sp-picker-container{
			border-left: 1px solid <?php echo $style['picker_color_border']['RGB'];?> !important;
		}
		.sp-cmyk-container label{
			color: <?php echo $style['picker_color_text']['RGB'];?> !important;
		}
    </style>
	<?php
}