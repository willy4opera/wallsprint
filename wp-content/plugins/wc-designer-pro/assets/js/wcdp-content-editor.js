/**********************************
* Woocommerce Designer Pro Editor *
***********************************/
(function($){
	'use strict';
    $(document).ready(function(){
		// Set style the object control corners
        fabric.util.object.extend(fabric.Object.prototype,{
			snapAngle           : 45,
			snapThreshold       : 5,
			centeredScaling     : wcdp_style.centered_scaling ? true : false,
			cornerStyle         : wcdp_style.corner_style,
			cornerSize          : parseInt(wcdp_style.corner_size),
            cornerColor         : wcdp_style.corner_color.RGB,
			borderColor         : wcdp_hex_to_rgba(wcdp_style.corner_color.RGB, 0.75),	
            borderDashArray     : [1, 1],
            hasRotatingPoint    : false,
			transparentCorners  : true
        });

        // Add Canvas Fabric & Set global variables
        var wcdp_canvas_editor     = new fabric.Canvas('wcdp-canvas-editor', {controlsAboveOverlay: true}),
            wcdp_loading_img       = WCDP_URL +'/assets/images/ajax-loader.gif',
			wcdp_save_loader       = '<img src="'+ WCDP_URL +'/assets/images/save-loader.gif' +'"/>',
			wcdp_number_save_user  = $('.dp-my-design-contain').length,
            wcdp_sources_opt       = ({'token': 0, 'page': 1, 'totals': 0}),		
		    wcdp_canvas_bleed_area = wcdp_draw_bleed_area(),
			wcdp_canvas_grid       = wcdp_draw_grid(),
            wcdp_tooltip_lock      = wcdp_jbox_tooltip('#wcdp-btn-lock', true),
			wcdp_tooltip_group     = wcdp_jbox_tooltip('#wcdp-btn-group', true),
			wcdp_rtl_mode          = wcdp_settings.rtl_mode == 'on' ? true : false,
			wcdp_font_sizes        = [8,9,10,11,12,13,14,15,16,17,18,20,22,24,26,28,30,32,36,42,48,72,96],
			wcdp_product_type      = $('#wcdp-product-type').val(),
			wcdp_product_color     = 0,
			wcdp_background_color  = 0,
			wcdp_design_name       = 0,
			wcdp_popup_preview	   = true,
			wcdp_paint_order       = 'stroke',
			wcdp_storage_fonts	   = [],
			wcdp_last_obj_lock     = 0,
			wcdp_layers_panel      = 0,
			wcdp_corners_actions   = 0,
			wcdp_zoom_min          = 1;

        // Drawing "controls & actions" corners
        fabric.Object.prototype._drawControl = function(control, ctx, methodName, left, top){
            if(!this.isControlVisible(control)){
                return;
            }
            var size = this.cornerSize,
				ico_f = wcdp_style.corner_icons_color ? wcdp_style.corner_icons_color.RGB : '#ffffff',
				ico_s = wcdp_style.corner_border_color ? wcdp_style.corner_border_color.RGB : '#679cd1',
				strok = wcdp_style.border_corners == 'on' ? true : false,
				out_c = wcdp_style.corners_outside == 'on' ? true : false,
				top_n = out_c ? top - size / 2 : top,
				top_p = out_c ? top + size / 2 : top,
				left_n = out_c ? left - size / 2 : left,
				left_p = out_c ? left + size / 2 : left,
				corners = {
					'tl': {'ico': '\ue965', 'top': top_n, 'left': left_n}, 
					'tr': {'ico': '\ue968', 'top': top_n, 'left': left_p}, 
					'bl': {'ico': '\ue969', 'top': top_p, 'left': left_n}, 
					'br': {'ico': '\ue967', 'top': top_p, 'left': left_p}
				};

		    ctx.fillStyle = this.cornerColor;

            if(control == 'tl' || control == 'tr' || control == 'bl' || control == 'br'){
				var corner = corners[control];
				if(this.cornerStyle == 'circle'){
                    ctx.beginPath();
				    ctx.arc(corner.left + size / 2, corner.top + size / 2, size / 2, 0, 2 * Math.PI);
				    ctx.fill();
				}
				else{
					this.transparentCorners || ctx.clearRect(corner.left, corner.top, size, size);
				    ctx.fillRect(corner.left, corner.top, size, size);
				}
			    ctx.font = size +'px wcdp-sprites';
				ctx.fillStyle = ico_f;
				ctx.textAlign = 'left';
				ctx.textBaseline = 'top';
				ctx.fillText(corner.ico, corner.left, corner.top);
				
				if(strok){
				    ctx.strokeStyle = ico_s;
				    ctx.lineWidth = 1.1;
					if(this.cornerStyle == 'circle')
						ctx.stroke();
					else
				        ctx.strokeRect(corner.left, corner.top, size, size);
				}
            }
			else{
				ctx.beginPath();
    			ctx.arc(left + size / 2, top + size / 2, 3, 0, 2 * Math.PI);
			    ctx.fill();
				
				if(strok){
				    ctx.strokeStyle = ico_s;
				    ctx.lineWidth = 1.1;
				    ctx.stroke();
				}
			}
        };
        fabric.Canvas.prototype._setCornerCursor = function(corner, target, e){
			if(corner === 'mt' || corner === 'mr' || corner === 'br' || corner === 'mb' || corner === 'ml'){
                this.setCursor(this._getRotatedCornerCursor(corner, target, e));
            }
            else if(corner === 'tr'){
                this.setCursor(this.rotationCursor);
            }
			else if(corner === 'tl'){
			    this.setCursor('copy');
		    }
			else if(corner == 'bl'){
			    this.setCursor('pointer');  
		    }
            else{
                this.setCursor(this.defaultCursor);
                return false;
            }
        };
	    fabric.Canvas.prototype._setupCurrentTransform = function(e, target){
            if(!target){
                return;
            }
            var pointer = this.getPointer(e),
                corner = target._findTargetCorner(this.getPointer(e, true)),
                action = this._getActionFromCorner(target, corner, e),
                origin = this._getOriginFromCorner(target, corner);

			if(corner === 'tl'){
				wcdp_corners_actions = 'duplicate';
				return;
			}
			else if(corner === 'bl'){
				wcdp_corners_actions = 'remove';
				return;
			}
			else if(corner === 'tr'){
				action = 'rotate';
			}
            this._currentTransform = {
                target: target,
                action: action,
                corner: corner,
                scaleX: target.scaleX,
                scaleY: target.scaleY,
                skewX: target.skewX,
                skewY: target.skewY,
                offsetX: pointer.x - target.left,
                offsetY: pointer.y - target.top,
                originX: origin.x,
                originY: origin.y,
                ex: pointer.x,
                ey: pointer.y,
                lastX: pointer.x,
                lastY: pointer.y,
                left: target.left,
                top: target.top,
                theta: fabric.util.degreesToRadians(target.angle),
                width: target.width * target.scaleX,
                mouseXSign: 1,
                mouseYSign: 1,
                shiftKey: e.shiftKey,
                altKey: e[this.centeredKey]
            };
            this._currentTransform.original = {
                left: target.left,
                top: target.top,
                scaleX: target.scaleX,
                scaleY: target.scaleY,
                skewX: target.skewX,
                skewY: target.skewY,
                originX: origin.x,
                originY: origin.y
            };
            this._resetCurrentTransform();
		};

		// Sets the coordinates of the draggable boxes in the corners	
        fabric.Object.prototype._setCornerCoords = function(){
            var coords = this.oCoords,
                angle = fabric.util.degreesToRadians(this.angle),
                sine = this.cornerSize * Math.sin(angle),
                cosine = this.cornerSize * Math.cos(angle),
                topFactor = -0.5,
                bottomFactor = 0.5,
                leftFactor = -0.5,
                rightFactor = 0.5,
                x, y;

            for(var point in coords){
                x = coords[point].x;
                y = coords[point].y;

                if(wcdp_style.corners_outside == 'on'){
                    if(point === 'tl' || point === 'tr'){
                      topFactor = -1;
                      bottomFactor = 0;
                    } else if (point === 'bl' || point === 'br'){
                      topFactor = 0;
                      bottomFactor = 1;
                    } else{
                      topFactor = -0.5;
                      bottomFactor = 0.5;
                    }
                    if(point === 'tl' || point === 'bl'){
                      leftFactor = -1;
                      rightFactor = 0;
                    } else if (point === 'tr' || point === 'br'){
                      leftFactor = 0;
                      rightFactor = 1;
                    } else{
                      leftFactor = -0.5;
                      rightFactor = 0.5;
                    }
                }
                coords[point].corner = {
                    tl: {
                        x: x + leftFactor * cosine - topFactor * sine,
                        y: y + leftFactor * sine + topFactor * cosine
                    },
                    tr: {
                        x: x + rightFactor * cosine - topFactor * sine,
                        y: y + rightFactor * sine + topFactor * cosine
                    },
                    bl: {
                        x: x + leftFactor * cosine - bottomFactor * sine,
                        y: y + leftFactor * sine + bottomFactor * cosine
                    },
                    br: {
                        x: x + rightFactor * cosine - bottomFactor * sine,
                        y: y + rightFactor * sine + bottomFactor * cosine
                    }
                };
            }
        }
		
		// Draw clipping area
        fabric.Object.prototype._drawClipArea = function(ctx){
			var bound = wcdp_get_bounding_bleed();
	        ctx.save();
	        var m = this.calcTransformMatrix(),
			    invertedM = fabric.util.invertTransform(m);

		    ctx.transform.apply(ctx, invertedM);
	        ctx.beginPath();
	        ctx.roundRect(bound.x, bound.y, bound.w, bound.h, bound.r <= 0 ? 0 : bound.r);
	        ctx.fillStyle = 'transparent';
	        ctx.fill();
	        ctx.closePath();
	        ctx.restore();
        }
        CanvasRenderingContext2D.prototype.roundRect = function(x, y, w, h, r){
            if (w < 2 * r) r = w / 2;
            if (h < 2 * r) r = h / 2;
            this.beginPath();
            this.moveTo(x+r, y);
            this.arcTo(x+w, y,   x+w, y+h, r);
            this.arcTo(x+w, y+h, x,   y+h, r);
            this.arcTo(x,   y+h, x,   y,   r);
            this.arcTo(x,   y,   x+w, y,   r);
            this.closePath();
            return this;
        }
		
		// Set new cursor position when scaling objects
        fabric.Canvas.prototype._scaleObject = function(x, y, by){
            var t = this._currentTransform,
                target = t.target,
                lockScalingX = target.get('lockScalingX'),
                lockScalingY = target.get('lockScalingY'),
                lockScalingFlip = target.get('lockScalingFlip');

            if(lockScalingX && lockScalingY){
                return false;
            }
            var constraintPosition = target.translateToOriginPoint(target.getCenterPoint(), t.originX, t.originY),
                localMouse = target.toLocalPoint(new fabric.Point(x, y), t.originX, t.originY),
                dim = target._getTransformedDimensions(),
				scaled = false;

            this._setLocalMouse(localMouse, t);
	        if(wcdp_style.corners_outside == 'on' && t.corner == 'br'){
				var zoom = this.getZoom(),
				    size = target.cornerSize / zoom;
					
		        dim.x += size;
	            dim.y += size;
	        }
            scaled = this._setObjectScale(localMouse, t, lockScalingX, lockScalingY, by, lockScalingFlip, dim);
            target.setPositionByOrigin(constraintPosition, t.originX, t.originY);
            return scaled;
        }
		
		// Draw stroke outside or inside text
        fabric.Text.prototype._renderText = function(ctx){
			if(wcdp_paint_order == 'stroke'){
                this._renderTextStroke(ctx);
                this._renderTextFill(ctx);
			} else{
				this._renderTextFill(ctx);
				this._renderTextStroke(ctx);
			}
        };
		
        // Drawing bleed area
		function wcdp_draw_bleed_area(){
			var params  = wcdp_parameters,
			    bleed_w = params.border_bleed_w,
				border  = parseInt(params.border_bleed_e == 'on' && bleed_w >= 1 ? bleed_w : 0);
			
			if(border != 0){
			    var type       = params.type,
				    width      = params.canvas_w,
				    height     = params.canvas_h,
			        lr_m       = parseInt(params.margin_bleed_lr),
				    tb_m       = parseInt(params.margin_bleed_tb),
					os_y       = typeof params.bleed_top !== 'undefined' ? parseInt(params.bleed_top) : 0,
					os_x       = typeof params.bleed_left !== 'undefined' ? parseInt(params.bleed_left) : 0,
					bleed_r    = params.bleed_radius,
				    radius_m   = bleed_r ? bleed_r - (lr_m /2) : 0,
				    radius     = radius_m <= 0 ? 0 : radius_m,
				    bleedGroup = new fabric.Group([], {						
						clas       : 'bleed',
						evented    : false,
						selectable : false
					}),
				    bleedOpts  = {
					    rx     : radius,
                        ry     : radius,	
				        top    : tb_m + os_y,
				        left   : lr_m + os_x,
                        width  : width - (lr_m *2 + border),
                        height : height - (tb_m *2 + border),
                        fill   : 'rgba(0, 0, 0, 0)' 				
				    },				
				    lineOpts = {
					    stroke          : wcdp_style.border_bleed_color.RGB,
					    strokeDashArray : [5 *border, 2 + (border /2)],
					    strokeWidth     : border						
				    },
				    guides = [];
			    if(type == 'typeDIP')
				    guides.push(width /2 - parseInt(border /2));							
			    else if(type == 'typeTRIP')
                    guides.push(width /3, (width - width /3) - border);
				
			    for(var i=0; i < guides.length; i++)
				    bleedGroup.add(new fabric.Line([guides[i], 0, guides[i], height], lineOpts));
			
                for(var key in lineOpts)
				    bleedOpts[key] = lineOpts[key];

                bleedGroup.add(new fabric.Rect(bleedOpts));
                return bleedGroup;
			}
			else{
				return false;
			}
		}
		function wcdp_get_bounding_bleed(){
			var bound = 0;
			if(wcdp_canvas_bleed_area){
			    var bleed = wcdp_canvas_bleed_area,
			        rect  = bleed._objects.slice(-1)[0],
			        strok = rect.strokeWidth;
				bound = {
			        x: rect.left + strok,
			        y: rect.top + strok,
			        w: rect.width - strok,
			        h: rect.height - strok,
					r: rect.rx - strok
				}
			}
			return bound;
		}
			
	    (function(){
		    var thumb_h = 100 * wcdp_parameters.canvas_h / wcdp_parameters.canvas_w,
			html  = '<div class="dp-loader-editor"></div>';
			html += '<div class="wcdp-snap-line-h"></div>';
			html += '<div class="wcdp-snap-line-v"></div>';
			html += '<div id="wcdp-zoom-move" style="height:' + thumb_h +'px"><span></span></div>';	   
			$('.canvas-container').append(html);
            $('body').append('<div class="wcdp_overlay_loader"></div>');
			$('#wcdp-text-fontFamily > option').each(function(){
				wcdp_storage_fonts.push(this.value);
            });
	    })();
		
		var wcdp_loading = $('.dp-loader-editor'),
        wcdp_snap_lineh = $('.wcdp-snap-line-h'),
		wcdp_snap_linev = $('.wcdp-snap-line-v'),
		wcdp_overlay_loading = $('.wcdp_overlay_loader');
		wcdp_overlay_loading.show();
		wcdp_check_selected_attr_actions();
		wcdp_canvas_responsive();
        wcdp_create_scrollbar('.wcdp-tab-section .mc__sb', 'y');
		$.spectrum.installPicker('spectrum-js');
		wcdp_lazy_load('.wcdp-tab-section .dp-img-contain img.lazyload');
		document.onkeydown = wcdp_short_cut_keys;

        // Load fonts and canvas
        wcdp_font_on_load(wcdp_parameters.fontsDesign).then(function(){
		    wcdp_load_json_canvas(Object.keys(wcdp_parameters.jsonSides).reverse(), 0);
		});

        function wcdp_load_json_canvas(side, index){
			var thumbs = $('#wcdp-canvas-thumbs-container');
			if(wcdp_parameters.type != 'typeF' && thumbs.find('[data-id="back"]').length == 0 && side.indexOf('back') != -1){
				side = side.filter(function(key){
                    return key != 'back';
                });
			}
            if(index < side.length){
                var countSize = side[index];
			    if(!wcdp_parameters.jsonSides[countSize][0]){
				    wcdp_parameters.jsonSides[countSize] = ['{"objects":[],"background":"transparent","backgroundCMYK":"0,0,0,0"}'];	
				}
                wcdp_canvas_editor.loadFromJSON(JSON.parse(wcdp_parameters.jsonSides[countSize]),function(){
                    wcdp_canvas_editor.renderAll.bind(wcdp_canvas_editor);
					wcdp_set_pr_color();
			        ++index;
					wcdp_loading.show();
                    wcdp_refresh_thumb_side(countSize).then(function(){
					    wcdp_load_json_canvas(side, index);
				    });
		        });
            } else{
		        setTimeout(function(){
                    var act = 'dp-canvas-active',
					sides = thumbs.find('div');
                    if(sides.hasClass(act)) sides.removeClass(act);					
				    sides.eq(0).addClass(act);
			        wcdp_loading.hide();
					wcdp_overlay_loading.hide();
			        wcdp_update_undo_redo_state(sides.eq(0).attr('data-id'));
			    }, 100);
				wcdp_auto_bleed_color(0);
		    }
        }

	    // Save canvas state
		var wcdp_save_canvas_state = function(){
            var canvasTemp = wcdp_canvas_get_json(),
		    side = $('#wcdp-canvas-thumbs-container .dp-canvas-active').attr('data-id');
	        if(wcdp_parameters.jsonSides[side][wcdp_parameters.jsonStates[side]] !== canvasTemp){
		        var removeRedoStates = wcdp_parameters.jsonSides[side].length - wcdp_parameters.jsonStates[side] -1;
		        if(removeRedoStates != 0){			
			        for(var i = 0; i < removeRedoStates; i++){
			            wcdp_parameters.jsonSides[side].pop();
			        }
		        }
			    wcdp_parameters.jsonSides[side].push(canvasTemp);  
			    wcdp_parameters.jsonStates[side]++;
			    wcdp_update_undo_redo_state(side);
				wcdp_refresh_thumb_side();
		    }
        }
        wcdp_canvas_editor.on('object:modified', wcdp_save_canvas_state);
        wcdp_spectrum_functions.saveState = wcdp_save_canvas_state;	
		
		// Get canvas JSON without layers bleed & grid
		function wcdp_canvas_get_json(blob){
			var getJSON = wcdp_canvas_editor.toJSON();			
            getJSON.objects.reduce(function(items, obj, index){ 
                if(obj.clas == 'bleed' || obj.clas == 'grid')
				    items.push(index);				
                return items;
            }, []).reverse().forEach(function(index){
                getJSON.objects.splice(index, 1);
            });
			var json = JSON.stringify(getJSON);
			if(blob)
				json = new Blob([json], {type: 'application/octet-stream'});
            
			return json;
		}	
	
        // Change canvas side
        $('#wcdp-canvas-thumbs-container').on('click', 'div', function(e, callback){
			e.preventDefault();
            var side = $(this).attr('data-id'),
			    chg  = !$(this).hasClass('dp-canvas-active'),
		        obj  = wcdp_canvas_editor.getActiveObject();
				
			// Fix: if i-text is editing overwrite the side
		    if(obj && obj.clas == 'i-text' && obj.isEditing)
			    wcdp_canvas_editor.discardActiveObject(obj);
			
			wcdp_loading.show();
            wcdp_refresh_thumb_side(this).then(function(){
    			if(chg){
				    wcdp_overlay_loading.show();
                    wcdp_change_canvas_side(side).then(function(){					
						wcdp_loading.hide();
						wcdp_overlay_loading.hide();
						wcdp_tip_content('lock');
						wcdp_draw_layers();
						wcdp_update_undo_redo_state(side);
						if(typeof callback === 'function') callback();
						if($('#wcdp-btn-preview').hasClass('btn-enabled')){							
					        wcdp_create_img_preview().then(function(){                                								
					            wcdp_jbox_preview_image();
                                $('#wcdp-btn-preview').removeClass('btn-enabled');								
					        });							
						}						                    					
					});
                } else{
					wcdp_loading.hide();
				}                				
		    });		    
        });
		function wcdp_change_canvas_side(side){
			var r = $.Deferred();
            wcdp_canvas_editor.clear();				
            wcdp_canvas_editor.loadFromJSON(JSON.parse(wcdp_parameters.jsonSides[side][wcdp_parameters.jsonStates[side]]),function(){
                wcdp_canvas_editor.renderAll.bind(wcdp_canvas_editor);
				wcdp_set_pr_color();
				wcdp_auto_bleed_color(0);
				wcdp_manage_overlay_layers('add');
                r.resolve();		
	        });		    
			return r.promise();
		}
        function wcdp_refresh_thumb_side(el){
	        var r = $.Deferred(),
		    act = 'dp-canvas-active',
		    side = '#wcdp-canvas-thumbs-container div',
			init = el == 'front' || el == 'back',
			selector = init ? side +'[data-id="'+ el +'"]' : side +'.'+ act,
			multpCover = 100 / wcdp_canvas_editor.getWidth(),
			bg_color = wcdp_canvas_editor.backgroundColor;
			wcdp_canvas_editor.setBackgroundColor('transparent').renderAll();
            $(selector).find('img').attr('src', wcdp_canvas_data_url(multpCover, 'png')).css('background-color', bg_color);
			wcdp_canvas_editor.setBackgroundColor(bg_color).renderAll();
            if(typeof el !== 'undefined' && !init){
                $(side).removeClass(act);
	            $(el).addClass(act);			  
			}
	        r.resolve(); 
	        return r.promise();
        }
        function wcdp_refresh_thumbs_static(){
			var thumbs = $('#wcdp-canvas-thumbs-container div'),
            cv = document.createElement('canvas');
            cv.width = wcdp_parameters.canvas_w;
            cv.height = wcdp_parameters.canvas_h;
            cv = new fabric.StaticCanvas(cv);

			var init = function(i){
				if(i == thumbs.length){
					cv.dispose();
				} else{
					var thumb = thumbs.eq(i);					
					i++;
				    var side = thumb.attr('data-id');
					if(side && wcdp_parameters.jsonSides[side] && typeof wcdp_parameters.jsonSides[side][wcdp_parameters.jsonStates[side]] !== 'undefined'){
				        cv.loadFromJSON(JSON.parse(wcdp_parameters.jsonSides[side][wcdp_parameters.jsonStates[side]]), function(){
                            cv.renderAll.bind(cv);
					        cv.setBackgroundColor('transparent').renderAll();
					        thumb.find('img').attr('src', cv.toDataURL({format: 'png', multiplier: 100 / wcdp_parameters.canvas_w}));
					        init(i);
                        });
					} else{
						init(i);
					}
				}
			}
			init(0);
        }		
	
	    // Responsive canvas
        var wcdp_get_window_width = $(window).width();
        $(window).resize(function(){		    
			var newWidth = $(window).width();		
			if(newWidth != wcdp_get_window_width){
				wcdp_get_window_width = newWidth;
				wcdp_canvas_responsive();
			}                    
        });
        function wcdp_canvas_responsive(){
	        $('.canvas-container').width('auto');			
            var contain_w = $('#wcdp-canvas-container').outerWidth(),
			    toolpanel = $('#wcdp-custom-tool-panel'),
			    optsBelow = $('#wcdp-opts-below-canvas'),
				toolbar   = $('#wcdp-toolbar-options'),
				toolbar_w = toolbar.outerWidth(),
		        param_w   = wcdp_parameters.canvas_w,
		        param_h   = wcdp_parameters.canvas_h,
			    canvas_w  = param_w > contain_w ? contain_w : param_w,
                ratio     = (canvas_w / param_w).toFixed(2),
                newWidth  = param_w * ratio;				
			wcdp_canvas_editor.setDimensions({width: newWidth, height: param_h * ratio});
			if(newWidth < 380)
				optsBelow.addClass('zoom-disabled');
			else
				optsBelow.removeAttr('class');
			
			optsBelow.css({'visibility': 'visible', 'max-width': newWidth +'px'});
			toolbar.css('visibility', 'visible').removeAttr('class');
            
			if(toolbar_w < 460)
				toolbar.addClass('dp-sensor-460');			
			else if(toolbar_w < 811)
				toolbar.addClass('dp-sensor-811');
			
			if(Math.round(window.devicePixelRatio * 100) < 100)
				toolbar.addClass('dp-brw-perc');

            // Fix in IE browser, position of the page is jumping from bottom to center in big designs
            toolpanel.removeAttr('style');
			if(window.innerWidth > 768)
				toolpanel.css('height', $('#wcdp-editor-container').height() +'px');
			
			wcdp_recall_canvas_radius(ratio);
			wcdp_zoom_min = ratio;			
			wcdp_zoom_reset();
        }
		
        // Zoom and panning
	    $('#wcdp-zoom-canvas').on('input change', function(e){
			e.preventDefault();
			var value = $(this).val(),
			    zoomVal = value / 100,
			    zoomMin = wcdp_zoom_min;
		    if(zoomVal == zoomMin){
			    wcdp_zoom_reset();
		    }
			else if(zoomVal > zoomMin){ 
                var move   = $('#wcdp-zoom-move'),
				    thumb  = move.find('span'),
                    width  = (wcdp_canvas_editor.getWidth() - wcdp_parameters.canvas_w * zoomVal),
				    height = (wcdp_canvas_editor.getHeight() - wcdp_parameters.canvas_h * zoomVal),					    
					vpt    = wcdp_canvas_editor.viewportTransform;						
				move.fadeIn();
                if(vpt[4] >= 0)
					wcdp_canvas_editor.viewportTransform[4] = 0;
                else if(vpt[4] < width)
                    wcdp_canvas_editor.viewportTransform[4] = width;                    
                if(vpt[5] >= 0)
					wcdp_canvas_editor.viewportTransform[5] = 0;
                else if(vpt[5] < height)
                    wcdp_canvas_editor.viewportTransform[5] = height;				
				wcdp_canvas_editor.setZoom(zoomVal).calcOffset().renderAll();					
			    var newVpt = wcdp_canvas_editor.viewportTransform,
				    coordX = Math.abs(newVpt[4] / (width / (move.width() - thumb.width()))) +1,
                    coordY = Math.abs(newVpt[5] / (height / (move.height() - thumb.width()))) +1;				
		        thumb.css({'top': + coordY + 'px', 'left': + coordX + 'px'});
			}
			wcdp_recall_canvas_radius(zoomVal);	
            wcdp_zoom_perc(value);           	
	    });
		$('#wcdp-zoom-move span').draggable({
			containment: '#wcdp-zoom-move',
			drag: function(event){
				var zoom   = wcdp_canvas_editor.getZoom(),
				    move   = $('#wcdp-zoom-move'),
				    thumb  = move.find('span'),
					coords = $(this).position(),
                    width  = coords.left * (wcdp_canvas_editor.getWidth() - wcdp_parameters.canvas_w * zoom) / (move.width() - thumb.width()),
                    height = coords.top * (wcdp_canvas_editor.getHeight() - wcdp_parameters.canvas_h * zoom) / (move.height() - thumb.width());
                wcdp_canvas_editor.viewportTransform[4] = width;
				wcdp_canvas_editor.viewportTransform[5] = height;
				wcdp_canvas_editor.renderAll();
            },
			stop: function(event){
			    var group = wcdp_canvas_editor.getActiveGroup(),
				    getObjs = wcdp_canvas_editor.getObjects();
			    if(group) 
				    group.setCoords();		 
                for(var i = (getObjs.length - 1); i >= 0; i--)
				    getObjs[i].setCoords();            	
			    wcdp_canvas_editor.renderAll();	
			}
		});				
        function wcdp_zoom_reset(){
	        var zoom = wcdp_zoom_min,
			    zoomVal = Math.round(zoom *100);
	        if(wcdp_canvas_editor.getZoom() != zoom){
                $('#wcdp-zoom-move').fadeOut();
		        $("#wcdp-zoom-canvas").attr('min', zoomVal).val(zoomVal);
                wcdp_canvas_editor.viewportTransform = [zoom, 0, 0, zoom, 0, 0];
                wcdp_canvas_editor.setZoom(zoom).calcOffset().renderAll();	    
	            wcdp_zoom_perc(zoomVal);				
	        }
        }
        function wcdp_zoom_perc(zoom){
	        $('#wcdp-zoom-canvas-container .dp-zoom-value').html(zoom +'%');
        }		
		function wcdp_recall_canvas_radius(ratio){
			var radius = wcdp_parameters.radius >= 0 ? wcdp_parameters.radius : 0; 
		    $('#wcdp-canvas-container .lower-canvas, #wcdp-canvas-container .upper-canvas').css('border-radius', radius * ratio + 'px');
			$('#wcdp-canvas-thumbs-container img').css('border-radius', radius / (wcdp_parameters.canvas_w / 100) + 'px');
		}

		// Pop-up preview thumbs
        $('#wcdp-tabs-content').on('mouseenter mouseleave', '.dp-img-res, .dp-img-contain, .dp-my-design-cover', function(e){
			e.preventDefault();
			if(wcdp_style.popup_show == 'on'){
				if(wcdp_popup_preview){
			        var el  = $(this),
			            img = el.find('img'),
					    src = el.attr('data-source'),
			            id  = el.parents('.wcdp-tab-section').attr('id').slice(5,-6),
			            url = src ? src : (id == 'images' || id == 'my-designs') ? img.attr('src') : img.attr('data-src');
			        if(url)
			            wcdp_popup_preview_display(el, url, e.type);
				} else{
					$('#wcdp-thumbs-preview').hide();
				}
			}
        });		
		function wcdp_popup_preview_display(el, url, ev){
			var getName = el.attr('data-name'),
			    getTags = el.attr('data-tags'),
			    loading = 'loading-thumb-preview',
		        contain = $('#wcdp-thumbs-preview'),
			    title   = contain.find('.dp-title'),
			    picture = contain.find('picture'),
                tags    = contain.find('.dp-tags'),	
                timer   = contain.data('activetimer');
				
		    if(ev == 'mouseenter'){
		        if(timer){
                    clearTimeout(timer);
                    contain.removeData('activetimer');
                }
				if(title.html())
				    title.empty();
                if(tags.html())
    		        tags.empty();
					
		        picture.removeAttr('style class');
		        contain.addClass(loading).show();
			
                var img = new Image();
		        img.crossOrigin = 'Anonymous';
                img.onload = function(){
					picture.css('background-image', 'url('+ this.src +')').addClass('loaded');
			        contain.removeClass(loading);
					if(getName)
                        title.html(getName);
					if(getTags)
    			        tags.html(getTags);				        
                };
                img.src = url;
		    } else if(ev == 'mouseleave'){
		        timer = setTimeout(function(){
                    contain.hide();				
		        }, 250);
		        contain.data('activetimer', timer);	
            }
    	}		
		
        // Manage overlay layers bleed area & grid
		function wcdp_manage_overlay_layers(opt){
			var grid = $('#wcdp-btn-grid').hasClass('btn-enabled');
			if(opt == 'add'){
				if(wcdp_canvas_bleed_area)
			        wcdp_canvas_editor.add(wcdp_canvas_bleed_area);
			    if(grid)
				    wcdp_canvas_editor.add(wcdp_canvas_grid);			
			} else if(opt == 'remove'){
				if(wcdp_canvas_bleed_area)
			        wcdp_canvas_editor.remove(wcdp_canvas_bleed_area);
			    if(grid)
				    wcdp_canvas_editor.remove(wcdp_canvas_grid);	
			} else if(opt == 'bring'){
				if(wcdp_canvas_bleed_area)
			        wcdp_canvas_bleed_area.bringToFront();
				if(grid)
                    wcdp_canvas_grid.bringToFront();	
			}
            wcdp_canvas_editor.renderAll();			
		}
        function wcdp_check_auto_hide_bleed(ins){
			if(wcdp_parameters.auto_hide_bleed == 'on' && wcdp_canvas_bleed_area){
			    wcdp_canvas_bleed_area.visible = ins;
			    wcdp_canvas_editor.renderAll();
			}
		}
		
		// Save Canvas Design
        $('.post-type-wcdp-designs #publish').click(function(e){
			if(!$(this).attr('data-save')){
                e.preventDefault();
		        wcdp_save_canvas_design('publish');
			}			
        });
		$('#wcdp-btn-save').parent().click(function(e){
			e.preventDefault();
			if(wcdp_parameters.editor == 'frontend'){
				var save_max = wcdp_settings.number_save_user ? wcdp_settings.number_save_user : 'unlimited';
				if(save_max == 'unlimited' || wcdp_number_save_user < save_max){
					var productName = wcdp_parameters.productName;
			        new jBox('Confirm',{
				        title: wcdp_translations.design_title,
			            content: '<input type="text" class="dp-design-name" placeholder="'+ productName +'"/>',
				        addClass: 'jBox-Modal' + (wcdp_rtl_mode ? ' md__rtl':''),
				        closeOnEsc: true,
				        closeButton: 'title',
	                    cancelButton: wcdp_translations.cancel,
	                    confirmButton: wcdp_translations.save_design,
                        confirm: function(){
					        var value = $('#' + this.id).find('.dp-design-name').val();
					        wcdp_design_name = value != '' ? value : productName;
				            wcdp_save_canvas_design('save');
				        },
                        onCloseComplete: function(){
                            this.destroy();
                        }
			        }).open();	
				}
				else{
				    wcdp_jbox_msg_modal(wcdp_translations.save_max_user +': '+ (save_max <= 0 ? 0 : save_max));
				}
			} else{
			    wcdp_jbox_msg_confirm(wcdp_translations.save_content, function(){
				    wcdp_save_canvas_design('save');
			    });
			}
		});
		
		// Modes: Publish admin | Save user & admin | Download user | Add to cart.
        function wcdp_save_canvas_design(mode, product_data){
			var publish      = mode == 'publish',
			    addtocart    = mode == 'addtocart',
				download     = mode == 'download',
				save         = mode == 'save',
				settings     = wcdp_settings,
				params       = wcdp_parameters,
				editor       = params.editor,
			    designID     = params.designID,
				productID    = params.productID,
			    frontend     = editor == 'frontend',
                uniqueNumber = new Date().getTime(),
			    uniqZipUser  = wcdp_random_string(),
			    uniqZipAdmin = wcdp_random_string(),
				addImg       = download || addtocart,				
			    regex        = /([\/\\\"\*\?\|\<\>\:])/g,
			    getZipName   = settings.zip_name,
			    zipName      = getZipName ? getZipName.replace(regex, '') : 'wcdp',
				getDesgName  = addtocart ? params.productName : (save && frontend ? wcdp_design_name : 0),
				szDesignName = getDesgName ? getDesgName.replace(regex, '') : '',
				designName   = szDesignName.trim().length ? szDesignName : wcdp_translations.untitled,
				multpOutput  = params.output_w / wcdp_canvas_editor.getWidth(),
				multpCover   = 350 / wcdp_canvas_editor.getWidth(),
				watermark    = params.watermark_img,
				watermarkRep = params.watermark_rep,
				outputSvg    = settings.output_svg == 'on',
				userSvg      = settings.add_svg_user == 'on',
				addSvg       = outputSvg && (addtocart || (download && userSvg)),
				outputPdf    = settings.output_pdf == 'on',
				userPdf      = settings.add_pdf_user == 'on',
				addPdf       = outputPdf && (addtocart || (download && userPdf)),
				pdfSvg       = addPdf && settings.output_pdf_svg == 'on',
				paramsPdf    = {'width': params.pdf_w, 'height': params.pdf_h, 'margin': params.pdf_margin, 'scale': params.pdf_scale, 'strech': params.pdf_strech},
				outputPng    = settings.output_png == 'on',
				userPng      = settings.add_png_user == 'on',
				addPng       = outputPng && (addtocart || (download && userPng)),
                addCMYK      = addtocart && settings.CMYK == 'on' && settings.output_cmyk == 'on',
				userCMYK     = addCMYK && settings.output_cmyk_user == 'on',
                outputJson   = addtocart && settings.output_json == 'on',
				sides        = $('#wcdp-canvas-thumbs-container div');

			var jBoxSave = new jBox('Modal',{
				content: wcdp_translations[(publish || save ? 'save_process':'cart_process')] + wcdp_save_loader,
				id: 'wcdp-jbox-save',
				addClass: wcdp_rtl_mode ? 'md__rtl':'',
				closeOnEsc: false,
				closeButton: false,
				closeOnClick: false,
				onCloseComplete: function(){
                    this.destroy();
                }
			}).open();

			var ins = function(i, files){
				if(editor == 'backend'){
				    wcdp_canvas_editor.forEachObject(function(obj){
						if(obj.clas == 'i-text')
							obj.set('placeholder', true);					
					});							
				}
			    if(wcdp_product_type == 'variable' && i == 1 && (addtocart || (save && frontend))){
					var attributes = {};
		            $('#wcdp-settings-panel .variations select').each(function(){
				        var this_ = $(this),
				            this_id = this_.attr('id'),
				            this_value = this_.val();
				        if(this_id && this_value){
					        attributes[this_id] = this_value;
				        }
		            });
			    }
				var act    = sides.parent().find('.dp-canvas-active'),
				    label  = act.find('label').text().replace(regex, ''),
				    side   = act.attr('data-id'),
					addCov = !download && side == 'front',
                    data   = {
				        'editor'       : editor,
		                'designID'     : designID,
						'designName'   : designName,
					    'productID'    : productID,
					    'zipName'      : zipName,
					    'uniqZipUser'  : uniqZipUser,
					    'uniqZipAdmin' : uniqZipAdmin,
					    'uniq'         : uniqueNumber.toString(),
					    'saveList'     : save && frontend && i == 1 ? true : 0,
					    'productData'  : addtocart && i == 1 ? product_data : 0,
						'attrData'     : typeof attributes !== 'undefined' ? attributes : 0,
					    'cover'        : addCov ? true : 0,
					    'watermark'    : watermark ? watermark : 0,
					    'watermarkRep' : watermarkRep ? true : 0,
					    'multpCover'   : multpCover,
					    'multpOutput'  : multpOutput,
					    'outputSvg'    : outputSvg,
					    'userSvg'      : userSvg,
						'addPdf'       : addPdf,
					    'userPdf'      : userPdf,
						'pdfSvg'       : pdfSvg,
						'pdf'          : paramsPdf,
					    'userPng'      : userPng,			
					    'addCMYK'      : addCMYK,
						'userCMYK'     : userCMYK,
                        'outputJson'   : outputJson,
					    'label'        : label,
				        'mode'         : mode,
					    'side'         : side,
					    'sides'        : i
					};
					
				setTimeout(function(){
			        var types = [];
					if(addImg)
						types.push('jpeg');
					if(addPng)
						types.push('png');
					if(addSvg || pdfSvg)
						types.push('svg');
					if(addPdf)
					    types.push('pdf');
					
					// Add JSON Back Side - Attribute action: Show product sides "sideF"
				    if(!download && frontend && params.type != 'typeF' && sides.parent().find('[data-id="back"]').length == 0 && typeof params.jsonSides['back'] !== 'undefined'){
				        var lastSide = params.jsonSides['back'][params.jsonStates['back']],
				            jsonBack = new Blob([lastSide], {type: 'application/octet-stream'});
				        files.upload.push({'name': 'back', 'ext': 'json', 'data': jsonBack});	
				    }
					
					wcdp_generate_output_files(types, files, jBoxSave, data).then(function(response){
                        if(i > 1){
				            sides.not('.dp-canvas-active').trigger('click', function(){
						        ins(i-=1, response);
                            });
			            } else{
                            if(download){
								jBoxSave.setContent(wcdp_translations.zip_files + wcdp_save_loader);
								wcdp_compress_jszip_files(response.user).then(function(zipFile){
									saveAs(zipFile, zipName +'_'+ uniqZipUser +'.zip');
									jBoxSave.destroy();
								});
						    }
							else{
						        if(publish){
								    $('.post-type-wcdp-designs #publish').attr('data-save', true).click();
						        }
							    else if(addtocart){
									wcdp_jbox_msg_confirm('<p><b>'+ wcdp_translations.product_added +'</b></p>'+ wcdp_translations.view_cart, function(){
				                        window.open(response.cart_url, '_self');
			                        });
							    }
							    else if(save){
								    if(response.userID){										
						                if(frontend){
						                    var el = $('#wcdp-my-designs-panel .dp-contain-box'),
											    sb = el.find('.mCSB_container');
							                if($('.dp-my-design-contain').length == 0)
								                sb.html('');

								            var html  = '<div class="dp-my-design-contain dp-border-style" page-id="'+ params.pageID +'" pid="'+ productID +'" sid="'+ uniqueNumber +'">';
								                html +=	'<span class="dp-my-design-cover" title="'+ designName +'" data-name="'+ designName +'"><img src="'+ WCDP_URL_UPLOADS +'/save-user/'+ uniqueNumber +'/cover.jpg"/></span>';                          									
											    html += '<div class="dp-loader-box"></div><div class="dp-remove-my-design"></div></div>';

									        wcdp_change_select_tab('wcdp-btn-my-designs');
								            sb.append(html);
											setTimeout(function(){
											    el.mCustomScrollbar('scrollTo', 'bottom');
											}, 100);
                                            wcdp_number_save_user++;
								        }
								        wcdp_jbox_msg_modal(wcdp_translations.save);
								    }
								    else{
								        window.open(response.wc_myaccount, '_self');
							        }
								}
							    jBoxSave.destroy();
							}
						}
					});
				}, 500);
			}
            ins(sides.length,{
				'user'  : {},
				'admin' : {},
				'upload': []
			});
		}

		// Generate output files
		function wcdp_generate_output_files(types, files, jBoxSave, params){
			var r = $.Deferred(),
			tempFiles = {},
			init = function(i){
				if(i == types.length){
					if(params.mode == 'download'){
					    r.resolve(files);
					}
					else{
						var getJSON = wcdp_canvas_get_json(true);
						files.upload.push({'name': params.side, 'ext': 'json', 'data': getJSON});
						if(params.outputJson)
							files.admin[params.label + '.json'] = getJSON;
						if(params.cover)
							files.upload.push({'name': 'cover', 'ext': 'jpg', 'data': wcdp_canvas_data_url(params.multpCover, 'jpeg', true)});						
					    if(params.addCMYK)
				            files.upload.push({'name': params.label, 'ext': 'CMYK', 'data': files.admin[params.label + '.jpg']});
					    if(params.sides > 1){
							r.resolve(files);
					    }
						else{
                            var saveFiles = function(){
				                var formData = new FormData();
                                params.files = [];
								for(var f = 0; f < files.upload.length; f++){
									var file = files.upload[f];
									formData.append(f, file.data);
									params.files.push({'count': f, 'name': file.name, 'ext': file.ext});
								}
					            formData.append('action', 'wcdp_save_canvas_design_ajax');					
					            formData.append('params', JSON.stringify(params));
					
                                $.ajax({
                                    url: AJAX_URL,
                                    type: 'POST',
                                    contentType: false,
                                    processData: false,
                                    data: formData,
					                success: function(response){
					                    response = JSON.parse(response);
                                        if(response.success){
											var sCMYK = response.filesCMYK;
								            if(sCMYK.length > 0){
												files.upload = [];
											    var getFilesCMYK = function(s){
                                                    var xhr = new XMLHttpRequest();
                                                    xhr.open('GET', sCMYK[s].url +'/'+ sCMYK[s].name);
                                                    xhr.responseType = 'blob';
                                                    xhr.onload = function(){
														if(params.userCMYK)
														    files.user[sCMYK[s].name] = xhr.response;
					                                    
														files.admin[sCMYK[s].name] = xhr.response;
														
														if(++s === sCMYK.length)
	                                                        outputZipFiles();
					                                    else
						                                    getFilesCMYK(s);
				                                    }
													xhr.send();
												}
												getFilesCMYK(0);
								            } else{
									            r.resolve(response);
								            }
							            } else{
				                            jBoxSave.destroy();
											wcdp_jbox_msg_modal(wcdp_translations.error_process);
			                            }
						            }
					            });			
                            },
                            outputZipFiles = function(){
								jBoxSave.setContent(wcdp_translations.zip_files + wcdp_save_loader);
								wcdp_compress_jszip_files(files.user).then(function(zipFileUser){
								   	files.upload.push({'name': params.zipName +'_'+ params.uniqZipUser,'ext': 'zip', 'data': zipFileUser});
									if(Object.keys(files.user).length == Object.keys(files.admin).length && !params.watermark){
										params.uniqZipAdmin = params.uniqZipUser;
										jBoxSave.setContent(wcdp_translations.save_files + wcdp_save_loader);
										saveFiles();
									} else{
								        wcdp_compress_jszip_files(files.admin).then(function(zipFileAdmin){
									        files.upload.push({'name': params.zipName +'_'+ params.uniqZipAdmin, 'ext': 'zip', 'data': zipFileAdmin});
										    jBoxSave.setContent(wcdp_translations.save_files + wcdp_save_loader);
										    saveFiles();
									    });
									}
								});
							}
                            if(params.addCMYK || params.mode == 'publish' || params.mode == 'save')
						        saveFiles();
					        else
						        outputZipFiles();
					    }
					}
					tempFiles = null;
				}
				else{  
					var type = types[i]; i++;
                    if(type == 'jpeg'){
                        tempFiles['sideJPG'] = wcdp_canvas_data_url(params.multpOutput, 'jpeg', false, true);
						var jpgBlob = wcdp_base64_to_blob(tempFiles.sideJPG, 'image/jpeg');

						if(params.mode == 'addtocart')
							files.admin[params.label + '.jpg'] = jpgBlob;
						
						if(params.watermark){
							wcdp_add_watermark_image([tempFiles.sideJPG, params.watermark], params.watermarkRep, 'jpeg').then(function(data){
						        tempFiles['sideWatermark'] = data;
								files.user[params.label + '.jpg'] = wcdp_base64_to_blob(tempFiles.sideWatermark, 'image/jpeg');
								init(i);
					        });
						} else{
							files.user[params.label + '.jpg'] = jpgBlob;
							init(i);
						}
					}
					else if(type == 'png'){
                        var sidePNG = wcdp_canvas_data_url(params.multpOutput, 'png', false, true),
						    pngBlob = wcdp_base64_to_blob(sidePNG, 'image/png');
						
						if(params.mode == 'addtocart')
							files.admin[params.label + '.png'] = pngBlob;
						
                        if(params.userPng){
						    if(params.watermark){
							    wcdp_add_watermark_image([sidePNG, params.watermark], params.watermarkRep, 'png').then(function(data){
								    files.user[params.label + '.png'] = wcdp_base64_to_blob(data, 'image/png');
								    init(i);
					            });
						    } else{
							    files.user[params.label + '.png'] = pngBlob;
							    init(i);
						    }
						} else{
							init(i);
						}
					}
                    else if(type == 'svg'){
						wcdp_encode_fonts(params.pdfSvg).then(function(fonts){
						    if(fonts){
							    var svg = wcdp_canvas_data_url(params.multpOutput, 'svg', false, true);
								if(params.addPdf && params.pdfSvg)
								    tempFiles['fonts'] = fonts;

								wcdp_convert_svg_elements_base64(svg, fonts).then(function(svgString){
							        tempFiles['sideSVG'] = svgString;
                                    if(params.outputSvg){
								        var svgBlob = new Blob([tempFiles.sideSVG], {type: 'image/svg+xml;charset=utf-8'});
								        if(params.userSvg)
							                files.user[params.label + '.svg'] = svgBlob;
								        if(params.mode == 'addtocart')
									        files.admin[params.label + '.svg'] = svgBlob;
							        }
							        init(i);
								});
							}
							else{
								jBoxSave.destroy();
							}
						});
					}
					else if(type == 'pdf'){
					    var paramsPdf = {
							'author'   : 'WooCommerce Designer Pro',
							'output_w' : wcdp_canvas_editor.getWidth() * params.multpOutput,
							'output_h' : wcdp_canvas_editor.getHeight() * params.multpOutput,
							'output_m' : params.multpOutput,
				            'width'    : params.pdf.width * 72 / 25.4,
				            'height'   : params.pdf.height * 72 / 25.4,
				            'margin'   : params.pdf.margin * 72 / 25.4,
							'scale'    : params.pdf.scale ? params.pdf.scale : 0.8,
							'strech'   : params.pdf.strech == 'on' ? true : false,
                            'pdfSvg'   : params.pdfSvg,
                            'label'    : params.label,
						}
                        if(params.pdfSvg){
							wcdp_create_output_pdf(paramsPdf, tempFiles.sideSVG, tempFiles.fonts).then(function(pdfSvg){
						 		if(params.userPdf)
					                files.user[params.label + '.pdf'] = pdfSvg;
								if(params.mode == 'addtocart')
								    files.admin[params.label + '.pdf'] = pdfSvg;
								
								init(i);
							});
						} else{
							var watermark = params.userPdf && params.watermark,
							    sideJPG = watermark ? tempFiles.sideWatermark: tempFiles.sideJPG;
							
						    wcdp_create_output_pdf(paramsPdf, sideJPG).then(function(pdfImg){
						 		if(params.userPdf)
					                files.user[params.label + '.pdf'] = pdfImg;
								if(params.mode == 'addtocart'){
									if(watermark){
										wcdp_create_output_pdf(paramsPdf, tempFiles.sideJPG).then(function(pdfWithoutWatermark){
										    files.admin[params.label + '.pdf'] = pdfWithoutWatermark;
											init(i);
										});
									} else{
						                files.admin[params.label + '.pdf'] = pdfImg;
										init(i);
									}
								} else{
									init(i);
								}
						    });
						}
					}
				}
			}
			init(0);
			return r.promise();
		}
		
		// Compress files ZIP from JSZip library
		function wcdp_compress_jszip_files(sides){
			var r = $.Deferred(),
			    zip = new JSZip(),
			    arrFiles = Object.keys(sides),
			    countFiles = 0;
			arrFiles.forEach(function(name){
				zip.file(name, sides[name]);
				countFiles++;
                if(countFiles == arrFiles.length){
                    zip.generateAsync({
						type: 'blob',
                        compression: "DEFLATE",
                        compressionOptions: {
                            level: 9
                        }
					}).then(function(content){
						r.resolve(content);
					});
				}
			});
			return r.promise();
		}
		
		// Add watermark to the design
		function wcdp_add_watermark_image(imgs, repeat, type){
			var r = $.Deferred(),
			init = function(){
				var im = imgs[0],
				    wt = imgs[1],
                    im_w = im.width,
                    im_h = im.height,
                    wt_w = wt.width,
                    wt_h = wt.height,
          		    cv = document.createElement('canvas');
            
			    cv.width = im_w;
                cv.height = im_h;
			    var ctx = cv.getContext('2d');
			    ctx.drawImage(im, 0, 0, im_w, im_h);

			    if(repeat){
                    var x = 0;
                    while(x < im_w){
                        var y = 0;
                        while(y < im_h){
					        ctx.drawImage(wt, x, y, wt_w, wt_h);
                            y += wt_h;
                        }
                        x += wt_w;
                    }
			    } else{
                    var x = (im_w / 2) - (wt_w / 2),
                        y = (im_h / 2) - (wt_h / 2);
				    ctx.drawImage(wt, x, y, wt_w, wt_h);
			    }
				r.resolve(cv.toDataURL('image/'+ type));
			},
			loadImages = function(i){
                var img = new Image();
                img.onload = function(){
                    imgs[i] = this;
					if(++i === imgs.length)
						init();
					else
						loadImages(i);
				}
                img.src = imgs[i];
                img.onerror = function(){
			        wcdp_jbox_msg_modal(wcdp_translations.error_process);
		        }
			}
			loadImages(0);
			return r.promise();
		}
		
		// Get canvas fonts & base64 encode
        function wcdp_get_canvas_fonts(){
	        var arr   = [],
			    fonts = [],
			    items = [],
                objs  = wcdp_canvas_editor.getObjects();
            for(var o = 0; o < objs.length; o++){
			    if(objs[o].clas != 'bleed' && objs[o].clas != 'grid')
                    items.push(objs[o]);
            }
			var init = function(items){
				if(items.length > 0){
					var item = items[0];
					if(item.clas == 'i-text' && fonts.indexOf(item.fontFamily) == -1){
	                    fonts.push(item.fontFamily);
					}
					else if(item.type == 'group' && item._objects){
					    for(var i = 0; i < item._objects.length; i++){
						    items.push(item._objects[i]);
					    }
				    }
					items.splice(item, 1);
					init(items);
				}
				else{
	                if(fonts.length > 0){
		                var fontNames = wcdp_data_fonts.map(function(value, index){
				            return value['name'];
				        });
			            fonts.sort();
			            fonts.forEach(function(font, index){
				            var searchFont = fontNames.indexOf(font);
				            if(searchFont != -1){
				                var sFont = wcdp_data_fonts[searchFont];
				                if(typeof sFont.id !== 'undefined' && typeof sFont.url !== 'undefined' && typeof sFont.name !== 'undefined' && sFont.name === font){
					                arr.push(sFont);
				                }
				            }
                        });
	                }
				}
			}
			init(items);
			return arr;
		}
		function wcdp_encode_fonts(pdf){
			var r = $.Deferred(),
			fonts = wcdp_get_canvas_fonts(),
			encodeFonts = function(i, wf){
				var url = 0,
			    font = fonts[i]; i++;
			    if(font.id == 'wf'){
			        if(wf){
					    var item = wf.items.filter(function(f){
						    return f.family === font.name;
					    });
				        url = item.length > 0 && typeof item[0].files.regular !== 'undefined' ? item[0].files.regular.replace('http:', '') : 0;
			        }
			    } else{
				    url = font.url;
			    }					
				if(url){
				    var xhr = new XMLHttpRequest();
                    xhr.open('GET', url, true);
                    xhr.responseType = 'blob';
                    xhr.onload = function(){
						var loaded = this.status == 200,
                            reader = new FileReader();
                        reader.onloadend = function(e){
							if(loaded)
					            font.base64 = e.target.result.split('base64,')[1];
						    if(i === fonts.length)
			                    r.resolve(fonts);
							else
								encodeFonts(i, wf);
                        }
                        reader.readAsDataURL(xhr.response);
	                }
		            xhr.send();
				} else{
					if(i === fonts.length)
			            r.resolve(fonts);
					else
						encodeFonts(i, wf);	
			    }
			}
			if(fonts.length > 0){
				if(pdf){
					var json = WCDP_URL +'/assets/js/webfonts.json';
                    $.getJSON(json, function(data){
                        encodeFonts(0, data);
                    }).fail(function(jqxhr){
                        wcdp_jbox_msg_modal(wcdp_translations.error_file +': webfonts.json');
						r.resolve(false);
                    });					
				} else{
					encodeFonts(0, 0);
				}
			} else{
                r.resolve(fonts);
			}
            return r.promise();
		}		
		
		// Add google fonts and convert custom fonts & images to base64
        function wcdp_convert_svg_elements_base64(data, fonts){
			var r = $.Deferred(),
			svg = $('<div>'+ data +'</div>');
			svg.find('desc').html('Created with WooCommerce Designer Pro - https://www.jmaplugins.com');

			var cv_w = parseInt(wcdp_parameters.canvas_w),
				cv_h = parseInt(wcdp_parameters.canvas_h),
				cv_f = wcdp_parameters.hide_bc == 'on' ? 'transparent' : wcdp_canvas_editor.backgroundColor;
			
			$('<rect x="0" y="0" width="'+ cv_w +'" height="'+ cv_h +'" fill="'+ cv_f +'"></rect>').insertAfter(svg.find('defs'));

			// Add google & custom fonts
			if(fonts.length > 0){
			    var wf = [], cf = '';
				for(var i = 0; i < fonts.length; i++){
					var font = fonts[i];
                    if(font.id == 'wf'){
						var fontName = font.name.replace(/\s/g,'+');
			            wf.push(fontName);
			        }
					else if(typeof font.base64 !== 'undefined'){
						var type = 'application/'+ (font.url.substr(font.url.lastIndexOf('.') + 1) == 'eot' ? 'vnd.ms-fontobject' : 'octet-stream');
						cf += "\t@font-face{\n\t\tfont-family: "+ font.name +";\n\t\tsrc: url(\"data:"+ type+ ";base64,"+ font.base64 +"\");\n\t}\n";										
					}
				}
				var css = '<style type=\"text/css\"><![CDATA[\n';
				if(wf.length > 0)
                    css += '\t@import url("http://fonts.googleapis.com/css?family='+ wf.join('|') +'");\n';
			    
				if(cf)
					css += cf;
                
				css += ']]></style>\n';
				svg.find('defs').append(css);
			}

			// Add letter spacing & paint order
			svg.find('text').each(function(){
				var obj = wcdp_get_obj_by_id(this.parentNode.getAttribute('id'));
				if(obj && obj.fontSize && obj.charSpacing && obj.charSpacing > 0)
                    this.setAttribute('letter-spacing', (obj.charSpacing * obj.fontSize) / 1000);
				
				if(wcdp_paint_order == 'stroke')
				    this.setAttribute('style', this.getAttribute('style') + ' paint-order: stroke;');
			});

			// Convert images to base64
			var imgs = svg.find('image'),
			convert = function(i){
				if(i == imgs.length){
					r.resolve(svg.html());
				} else{
					var img = imgs[i],
					    src = img.getAttribute('xlink:href'); i++;
				    if(src.indexOf('http') === 0){
	                    var ext = src.substr(src.lastIndexOf('.') + 1),
						    typ = ext == 'png' || ext == 'gif' ? 'png' : 'jpeg',
		                    obj = wcdp_get_obj_by_id(img.getAttribute('id'));
						if(obj){
		                    img.setAttribute('xlink:href', obj.toDataURL({format: typ, multiplier: obj._element.width / obj.getWidth()}));
							convert(i);
						} else{
							if(src.search('maps.googleapis.com') !== -1){
			                    var xhr = new XMLHttpRequest();
                                xhr.open('GET', src, true);
                                xhr.responseType = 'blob';
                                xhr.onload = function(){
						            var loaded = this.status == 200,
                                    reader = new FileReader();
                                    reader.onloadend = function(e){
							            if(loaded)
		                                    img.setAttribute('xlink:href', e.target.result);
							            
										convert(i);
                                    }
                                    reader.readAsDataURL(xhr.response);
	                            }
		                        xhr.send();								
							} else{
                                var im = new Image();
                                im.onload = function(){
                                    var cv = document.createElement('canvas');
                                    cv.width = this.naturalWidth;
                                    cv.height = this.naturalHeight;
                                    cv.getContext('2d').drawImage(this, 0, 0);
		                            img.setAttribute('xlink:href', cv.toDataURL('image/'+ typ));
					                convert(i);
				                }
                                im.src = src;
							}
						}
				    } else{
				        convert(i);
				    }
				}
			}
			if(imgs.length > 0)
			    convert(0);
			else
				r.resolve(svg.html());

			return r.promise();
		}

		// Add clipping area svg
        fabric.util.object.extend(fabric.Canvas.prototype,{
            _setSVGObjects: function(markup, reviver){
				var bound = wcdp_get_bounding_bleed(),
		            clipping = wcdp_parameters.bleed_clip == 'on' && bound ? true : false;
		        if(clipping){
                    var radius = bound.r <= 0 ? 0 : bound.r;
	                markup.push('<clipPath id="wcdp-bleed-area"><rect x="' + bound.x + '" y="' + bound.y + '" rx="'+ radius +'" ry="'+ radius +'" width="'+ bound.w +'" height="' + bound.h + '"></rect></clipPath><g clip-path="url(#wcdp-bleed-area)">');
	            }
                var instance;
                for(var i = 0, objects = this.getObjects(), len = objects.length; i < len; i++){
                    instance = objects[i];
                    if(instance.excludeFromExport){
                        continue;
                    }
                    this._setSVGObject(markup, instance, reviver);
                }
	            if(clipping){
	                markup.push('</g>');
				}
            }
        });
		
		// Create PDF from JPG / SVG
		function wcdp_create_output_pdf(params, img, fonts){
			var r = $.Deferred(),
		    newDoc = function(){
				var pdfdoc = new PDFDocument({
                    compress: false,
                    size: [params.width, params.height]
                });
	            pdfdoc.info['Title'] = params.label;
	            pdfdoc.info['Author'] = params.author;
	            pdfdoc.info['Creator'] = params.author;
			    return pdfdoc;
			}
			var doc = newDoc(),
                ratio = params.width / params.output_w,
			    scale = ratio > params.scale ? params.scale : ratio,
                img_l = params.strech ? 0 : (params.width - (params.output_w * scale)) / 2, 
			    img_t = params.strech ? 0 : (params.margin <= 0 ? 0 : params.margin),
				img_w = params.strech ? params.width : params.output_w * scale,
				img_h = params.strech ? params.height : params.output_h * scale,
				img_s = {width: img_w, height: img_h};

			if(params.pdfSvg){
				if(fonts.length > 0){
				    for(var i = 0; i < fonts.length; i++){
					    var font = fonts[i];
					    if(typeof font.base64 !== 'undefined'){
						    var addFont = font.id != 'wf' && font.url.substr(font.url.lastIndexOf('.') + 1) != 'ttf' ? false : true;
						    if(addFont){
						        var raw = window.atob(font.base64),
                                bytes = new Uint8Array(raw.length);
                                for(var j = 0; j < raw.length; j++){
                                    bytes[j] = raw.charCodeAt(j);
                                }
						        doc.registerFont(font.name, bytes.buffer);
						    }
					    }
				    }
				}
                try{
					if(params.strech)
						img_s['preserveAspectRatio'] = 'none';
					
					SVGtoPDF(doc, img, img_l, img_t, img_s);
                }
				catch(e){
                    doc = newDoc();
					img = wcdp_canvas_data_url(params.output_m, 'jpeg', false, true);
					doc.image(img, img_l, img_t, img_s);
				}
			} else{
			    doc.image(img, img_l, img_t, img_s);
			}
			var stream = doc.pipe(blobStream());
            stream.on('finish', function(res){
                var pdf = stream.toBlob('application/pdf');
		        r.resolve(pdf);
            });		
            doc.end();
            return r.promise();						
		}
		
		// Export canvas to image & svg
		function wcdp_canvas_data_url(output, type, blob, output_files){
		    var viewport = wcdp_canvas_editor.viewportTransform,
			    params = wcdp_parameters,
			    zoom = wcdp_zoom_min,
				crop = 0;

			if(output_files)
			    wcdp_check_visible_layers(false);

			wcdp_manage_overlay_layers('remove');
			wcdp_canvas_editor.viewportTransform = [zoom, 0, 0, zoom, 0, 0];
			
			if(output_files && params.output_inside_bleed == 'on'){
			    var bleed_w = params.border_bleed_w,
			        border  = parseInt(params.border_bleed_e == 'on' && bleed_w >= 1 ? bleed_w : 0);
						
				if(border != 0){
					crop = true;
				    var lr_m = parseInt(params.margin_bleed_lr),
			            tb_m = parseInt(params.margin_bleed_tb),
						os_y = typeof params.bleed_top !== 'undefined' ? parseInt(params.bleed_top) : 0,
					    os_x = typeof params.bleed_left !== 'undefined' ? parseInt(params.bleed_left) : 0,
						nw_w = (params.canvas_w - (lr_m *2 + (border *2))),
						nw_h = (params.canvas_h - (tb_m *2 + (border *2)));
				}
			}
			if(type == 'svg'){
				var cv_w = wcdp_canvas_editor.getWidth(),
				    cv_h = wcdp_canvas_editor.getHeight(),
					cv_f = wcdp_canvas_editor.backgroundColor,
				    svg_s = {width: cv_w * output, height: cv_h * output};
					
				if(crop){
					svg_s['width'] = (nw_w * output) * zoom;
					svg_s['height'] = (nw_h * output) * zoom;
					svg_s['viewBox'] = {x: lr_m + os_x + border, y: tb_m + os_y + border, width: nw_w, height: nw_h};
				}
				wcdp_canvas_editor.backgroundColor = null;
                var img = wcdp_canvas_editor.toSVG(svg_s);
				wcdp_canvas_editor.backgroundColor = cv_f;

				if(typeof rect !== 'undefined')
				    wcdp_canvas_editor.remove(rect);
				
                if(blob)
                    img = new Blob([img], {type: 'image/svg+xml;charset=utf-8'});
			}				
			else{
				var img_s = {format: type, multiplier: output};
				if(crop){
					img_s['top'] = (tb_m + os_y + border) * zoom;
					img_s['left'] = (lr_m + os_x + border) * zoom;
					img_s['width'] = nw_w * zoom;
					img_s['height'] = nw_h * zoom;
				}
			    var fix_bg_color = type == 'jpeg' && wcdp_canvas_editor.backgroundColor == 'transparent';
			    if(fix_bg_color)
					wcdp_canvas_editor.backgroundColor = '#ffffff';
				
				var img = wcdp_canvas_editor.toDataURL(img_s);
				
			    if(fix_bg_color)
					wcdp_canvas_editor.backgroundColor = 'transparent';
                
				if(blob)
					img = wcdp_base64_to_blob(img, 'image/'+ type);
			}				
			wcdp_canvas_editor.viewportTransform = viewport;

			if(output_files)
			    wcdp_check_visible_layers(true);

			wcdp_manage_overlay_layers('add');
			return img;
		}

    	// Load design user
        $('#wcdp-my-designs-panel').on( 'click', '.dp-my-design-cover', function(e){ 
	        e.preventDefault();
			var el = $(this).parent();
			if(el.attr('page-id')){				
			    wcdp_jbox_msg_confirm(wcdp_translations.load, function(){
				    window.open(SITE_URL +'/?page_id='+ el.attr('page-id') +'&dp_mode=save&design_id='+ el.attr('sid'), '_self');
			    });				
			} else{
				wcdp_jbox_msg_modal(wcdp_translations.product_id +' <b>"'+ el.attr('pid') +'"</b> '+ wcdp_translations.product_unavailable);
			}
        });	

		// Delete design user
        $('#wcdp-my-designs-panel').on( 'click', '.dp-remove-my-design', function(e){ 
	        e.preventDefault();
			var el = $(this).parent(),			
			    sid = el.attr('sid'),
				loader = el.find('.dp-loader-box');				
			wcdp_jbox_msg_confirm(wcdp_translations.delete, function(){
                loader.show();
				wcdp_overlay_loading.show();
                $.ajax({
                    url: AJAX_URL,
                    type: 'POST',
                    data: {
                        'action': 'wcdp_remove_canvas_design_ajax',
			            'designID': sid	
     			    },						
				    success: function(response){						    
                        if(response == 'delete_successful'){
                            el.remove();
							wcdp_number_save_user--;
                            if($('.dp-my-design-contain').length == 0){
				    		    var html  ='<div class="dp-label-center">';
							        html +='<span class="dp-search-empty" id="wcdp-btn-mydesigns-empty"></span>';
								    html +='<label>'+ wcdp_translations.no_designs +'</label></div>';
								$('#wcdp-my-designs-panel .dp-contain-box .mCSB_container').append(html);						           
							}									
				        } else{
                            wcdp_jbox_msg_modal(wcdp_translations.error_process);									
			            }
				    	loader.hide();
						wcdp_overlay_loading.hide();
					}
                });	
			});	
        });	
		
		// Download design user
		$('#wcdp-download-my-design').click(function(e){
			e.preventDefault();
			wcdp_jbox_msg_confirm(wcdp_translations.download_content, function(){
			    wcdp_save_canvas_design('download');
			});
        });

		// Check selected attribute actions before adding to canvas
		function wcdp_check_selected_attr_actions(){
		    if((wcdp_parameters.editor == 'frontend' && wcdp_product_type == 'variable') && wcdp_parameters.mode == 'designer_editor' || wcdp_parameters.attr_data){
			    $('#wcdp-settings-panel .variations select').each(function(){
					var value = $(this).val();
					if(value){
				        wcdp_apply_attr_actions(this, false);
					}
			    });
		    }
		}
		// Change product colors with variations
		$('#wcdp-settings-panel .variations [data-layout="product_colors"] .dp-attr-colors span').click(function(e){
			e.preventDefault();
			var this_ = $(this),
			    act = 'sp-thumb-active';
			if(!this_.hasClass(act)){
				var value = this_.attr('name'),
					bgColor = this_.css('background-color'),
					spd = 'sp-thumb-dark',
				    spl = 'sp-thumb-light';
				this_.parent().find('span').removeClass(act+' '+spd+' '+spl);
				this_.addClass(act+' '+(bgColor != 'rgba(0, 0, 0, 0)' ? (tinycolor('#'+ wcdp_rgb_to_hex(bgColor)).toHsl().l < 0.5 ? spd:spl):''));
			    this_.closest('.dp-row').find('select').val(value).change();
			}
		});
		// Set product color
		function wcdp_set_pr_color(){
		    if(wcdp_product_color){
				wcdp_canvas_editor.setBackgroundColor(wcdp_product_color).renderAll();
			}
		}
		// Change radio checkbox with variations
		$('#wcdp-settings-panel .variations [data-layout="radio_checkbox"] .dp-attr-radio input').change(function(e){
			e.preventDefault();
			var this_ = $(this),
			    value = this_.val();
			this_.closest('.dp-row').find('select').val(value).change();
		});
		// Apply attribute actions
		$('#wcdp-settings-panel .variations select').on('change', function(e){
			e.preventDefault();
		    var value = $(this).val();
			if(value){
			    wcdp_apply_attr_actions(this, true);
			}
		});
		function wcdp_apply_attr_actions(el, chg){
			var this_ = $(el),
			    attr_name = this_.attr('data-attribute_name').replace('attribute_',''),
				this_value = this_.find('option:selected').val(),
				attr_value = this_.parent().find('.dp-attr-values input[name="'+ this_value +'"]').val(),
				actions = wcdp_parameters.attr_actions;
			if(actions && actions[attr_name] && typeof actions[attr_name]['actions'] !== 'undefined'){
				var select_actions = actions[attr_name]['actions'][attr_value];
				if(typeof select_actions !== 'undefined'){
				    var updateBleedArea = 0,
					setDimensions = 0,
					thumbs = $('#wcdp-canvas-thumbs-container'),
					init = function(i){
						var keys = Object.keys(select_actions);
						if(keys.length == i){
					        if(updateBleedArea){
								if(setDimensions){
								    var newHeight = 100 * wcdp_parameters.canvas_h / wcdp_parameters.canvas_w;
								    thumbs.find('img').height(newHeight);
									$('#wcdp-zoom-move').height(newHeight);
								}
					            if(chg){
									wcdp_manage_overlay_layers('remove');
									if(setDimensions){
									    wcdp_canvas_grid = wcdp_draw_grid();
								    }
						            wcdp_canvas_bleed_area = wcdp_draw_bleed_area();
									wcdp_manage_overlay_layers('add');
									wcdp_check_auto_hide_bleed(false);
									wcdp_auto_bleed_color(0);
									if(setDimensions){
                                        wcdp_canvas_responsive();
                                        wcdp_refresh_thumbs_static();
								    }
								} else{
									if(setDimensions){
									    wcdp_canvas_grid = wcdp_draw_grid();
								    }
									wcdp_canvas_bleed_area = wcdp_draw_bleed_area();
								}
					        }
						}
						else{
							var action = keys[i]; i++;
							if(select_actions[action]['active'] == 'on'){
								var value = select_actions[action]['value'];
						        if(action == 'bg_color' && (value == '' || /^#[0-9A-F]{6}$/i.test(value))){
							        value = value ? value : 'transparent';
									wcdp_product_color = value;
								    if(chg){
									    wcdp_set_pr_color();
								        thumbs.find('img').css('background-color', value);
										wcdp_auto_bleed_color(value);
									}
								    init(i);
						        }
							    else if(value && (action == 'pr_img_front' || action == 'pr_img_back')){
								    var side = action.substring(7);
								    if(typeof wcdp_parameters.jsonSides[side] !== 'undefined'){
					                    if(!wcdp_parameters.jsonSides[side][0]){
				                            wcdp_parameters.jsonSides[side] = ['{"objects":[],"background":"transparent","backgroundCMYK":"0,0,0,0"}'];	
				                        }
										var set_img = typeof actions[attr_name]['set_img'] !== 'undefined' ? actions[attr_name]['set_img'] : 'bg_img',
										imgParams = {
										    'crossOrigin': 'Anonymous',
	                                        'originY': 'top',
	                                        'originX': 'left',
	                                        'width': parseInt(wcdp_parameters.canvas_w),
	                                        'height': parseInt(wcdp_parameters.canvas_h)
									    }
									    if(chg){
					                        wcdp_loading.show();
			                                wcdp_overlay_loading.show();
										    var img = new Image();
			                                img.crossOrigin = 'Anonymous';
                                            img.onload = function(){
											    var thumb = thumbs.find('[data-id="'+ side +'"]');
									            if(thumb.hasClass('dp-canvas-active')){
			                                        var pr_img = new fabric.Image(img, imgParams);
													if(set_img == 'bg_img'){
												        wcdp_canvas_editor.setBackgroundImage(pr_img).renderAll();
													} else{
												        wcdp_canvas_editor.setOverlayImage(pr_img).renderAll();
													}
			                                        wcdp_refresh_thumb_side(side);
								                    wcdp_parameters.jsonSides[side] = [wcdp_canvas_get_json()];
	                                                wcdp_parameters.jsonStates[side] = 0;
												    wcdp_update_undo_redo_state(side);
											        init(i);
									            }
									            else{
										            imgParams['src'] = this.src;
							                        var lastSide = JSON.parse(wcdp_parameters.jsonSides[side][wcdp_parameters.jsonStates[side]]);
													if(set_img == 'bg_img'){
	                                                    lastSide['backgroundImage'] = imgParams;
													} else{
													    lastSide['overlayImage'] = imgParams;
													}
											        wcdp_parameters.jsonSides[side] = [JSON.stringify(lastSide)];
		                                            wcdp_parameters.jsonStates[side] = 0;
												    wcdp_update_undo_redo_state(side);
												    var cv = document.createElement('canvas');
												    cv.width = imgParams.width;
                                                    cv.height = imgParams.height;
												    cv = new fabric.StaticCanvas(cv);
												    cv.loadFromJSON(lastSide, function(){
                                                        cv.renderAll.bind(cv);
													    cv.setBackgroundColor('transparent').renderAll();
													    thumb.find('img').attr('src', cv.toDataURL({format: 'png', multiplier: 100 / imgParams.width}));
													    cv.dispose();
													    init(i);
		                                            });
									            }
											    wcdp_loading.hide();
			                                    wcdp_overlay_loading.hide();
                                            };
                                            img.src = value;
                                            img.onerror = function(){
			                                    wcdp_jbox_msg_modal(wcdp_translations.error_process);
		                                    }
										}
										else{
										    imgParams['src'] = value;
											var firstSide = JSON.parse(wcdp_parameters.jsonSides[side][0]);
											if(set_img == 'bg_img'){
											    firstSide['backgroundImage'] = imgParams;
											} else{
										        firstSide['overlayImage'] = imgParams;
											}
											wcdp_parameters.jsonSides[side][0] = JSON.stringify(firstSide);
                                            init(i);
										}										
								    }
									else{
										init(i);
									}
							    }
								else if(value && action == 'pr_sides' && wcdp_parameters.type != 'typeF'){
								    var sideFront = thumbs.find('[data-id="front"]'),
								        sideBack  = thumbs.find('[data-id="back"]');

									if(value == 'sideF' && sideBack.length == 1){
										if(sideBack.hasClass('dp-canvas-active')){
											sideFront.trigger('click', function(){
									            sideBack.remove();
										        init(i);
											});
										} else{
									        sideBack.remove();
										    init(i);
										}
									}
									else if(value == 'sideFB' && sideBack.length == 0){
								        var height = 100 * wcdp_parameters.canvas_h / wcdp_parameters.canvas_w +'px',
										    radius = wcdp_parameters.radius >= 0 ? wcdp_parameters.radius / (wcdp_parameters.canvas_w / 100) : 0,
										    style  = 'style="height:'+ height +'; border-radius:'+ radius + 'px"',
											base64 = 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="';

                                        thumbs.append('<div data-id="back"><img width="100" '+ base64 + style +'/><label>'+ wcdp_parameters.label_b +'</label></div>');	
										thumbs.find('[data-id="back"]').trigger('click', function(){
										    wcdp_refresh_thumb_side();
										    init(i);
										});
								    }
									else{
										init(i);
									}
								}
								else if(value && (action == 'canvas_w' || action == 'canvas_h')){
									wcdp_parameters[action] = value;
									if(action == 'canvas_w'){
									    wcdp_canvas_editor.setWidth(value);
									} else{
										wcdp_canvas_editor.setHeight(value);
									}
									updateBleedArea = true;
									setDimensions = true;
									init(i);
								}
								else if(value && (action == 'output_w' || action == 'pdf_w' || action == 'pdf_h')){
									wcdp_parameters[action] = value;
									init(i);
								}
							    else if(value && (action == 'margin_bleed_lr' || action == 'margin_bleed_tb' || action == 'bleed_top' || action == 'bleed_left' || action == 'bleed_radius')){
								    wcdp_parameters[action] = value;
								    updateBleedArea = true;
								    init(i);
							    }
							    else if(value && action == 'radius'){
								    wcdp_parameters[action] = value;
									wcdp_canvas_responsive();
								    init(i);
							    }
								else{
								    init(i);
								}
							}
							else{
								init(i);
							}
						}
					}
					init(0);
				}
			}			
		}

		// Add to cart
		$('#wcdp-btn-addtocart').parent().click(function(e){
			e.preventDefault();
		    var el  = $('#wcdp-product-input-qty'),
			    qty = el.find('input').val();
            if(el.length > 0){
				if(qty > 0){
				    var product_data = {
						'type': wcdp_product_type,
						'variation_id': 0,
						'attributes': {},
						'qty': qty
					}
				    if(wcdp_product_type == 'variable'){
						var varns = $('#wcdp-settings-panel .variations .dp-row');
						if($('.woocommerce-variation-add-to-cart').hasClass('woocommerce-variation-add-to-cart-enabled')){
							varns.each(function(){
								var key = $(this).find('select').attr('data-attribute_name'),
								    val = $(this).find('select option:selected').val();
								product_data['attributes'][key] = val;
							});
							product_data['variation_id'] = $('#wcdp-variation-id').val();
						} else{
		                    varns.find('select').each(function(){
			                    if(!$(this).val())
									$(this).parent().addClass('dp-disabled');
                            });
			                if(!$('#wcdp-btn-settings').hasClass('vtab-selected')){
			                    wcdp_change_select_tab('wcdp-btn-settings');
							}								
							wcdp_jbox_msg_modal(wcdp_translations.addtocart_disabled);
							return false;
						}
				    }
		            var ins = function(){
			                wcdp_save_canvas_design('addtocart', product_data);
				        },
                        textBox  = $('<div/>').html(wcdp_settings.text_confirm_box).text(),
		                labelBox = wcdp_settings.label_confirm_box;
							
		            if(wcdp_settings.confirm_box && textBox && labelBox){
		                var confirmDesign = new jBox('Confirm',{
				            id: 'wcdp-confirm-design',
							addClass: wcdp_rtl_mode ? 'md__rtl':'',
		                    content: textBox +'<label><input type="checkbox"/>'+ labelBox +'</label>',				    
                            cancelButton: wcdp_translations.cancel,
                            confirmButton: wcdp_translations.confirm,
			                closeOnConfirm: false,
                            confirm: function(){
				                if($('#wcdp-confirm-design input').is(':checked')){					                    
					                confirmDesign.close();
									ins();
				                } else{
									var cl = 'dp-no-approved',
									    ck = $('#wcdp-confirm-design label');										    
									ck.addClass(cl);
									setTimeout(function(){
										ck.removeClass(cl);
									}, 1000);
								}
			                },
                            onCloseComplete: function(){
                                this.destroy();
                            }
		                }).open();
		            } else{
                        ins();
		            }				   
				} else{
					wcdp_jbox_msg_modal(wcdp_translations.qty_zero);
				}					
			} else{
				wcdp_jbox_msg_modal(wcdp_translations.out_stock);
			}
        });
		$('#wcdp-settings-panel .variations .dp-row').on('change', 'select', function(e){
			e.preventDefault();
	        if($(this).val())
				$(this).parent().removeClass('dp-disabled');
        });
		$(document).on('change', '#wcdp-confirm-design input', function(e){
			e.preventDefault();
			var el = $(this).parent();
			el.removeAttr('class');
			if($(this).is(':checked'))
				el.addClass('dp-approved');	
		});		
		
	    // Vertical Tabs
        $('#wcdp-tabs-icons > span').click(function(e){
			e.preventDefault();
			wcdp_canvas_editor.deactivateAll().renderAll();
			wcdp_check_auto_hide_bleed(false);
		    wcdp_change_select_tab($(this).attr('id'));
        }).eq(0).click();
	
	    function wcdp_change_select_tab(tab){            		
		    var el = $('#'+tab),
		    vtab = 'vtab-selected';		   
		    if(el.length !== 0 && !el.hasClass(vtab)){
		        $('#wcdp-tabs-icons > span').removeClass(vtab);
		        el.addClass(vtab);
		        $('#wcdp-tabs-content > .wcdp-tab-section').hide().eq(el.index()).fadeIn();
				wcdp_reset_options_by_default();
				wcdp_canvas_bg_color_checked();
				wcdp_draw_layers();
		    }
	    }
	
        // Select Obj
	    wcdp_canvas_editor.on('object:selected', function(e){
	        var obj = e.target;
			
			// Layers Tab
            if(wcdp_layers_panel){
		        wcdp_layer_item('clear');
		        var getObjects = wcdp_get_selection_obj();			    
		        getObjects.forEach(function(o){
					wcdp_layer_item('select', o.id);
				});
            } else{
		        if(obj.clas == 'i-text'){
			
			        // Select Tab
  	                wcdp_change_select_tab('wcdp-btn-text');     

			        // Check Tools selected
			        wcdp_add_class_items_selected(obj, $('.dp-col-8 span'), wcdp_tools_itext());
                    $('#wcdp-text-fontFamily').val(obj.fontFamily);
	                $('#wcdp-text-fontSize').val(obj.fontSize);
	                $('#wcdp-text-color-font').spectrum('set', obj.fill).spectrum('setCMYK', obj.fillCMYK);
	                $('#wcdp-text-outline-color').spectrum('set', obj.stroke).spectrum('setCMYK', obj.strokeCMYK);
			        $('#wcdp-text-stroke').val(obj.strokeWidth);
			        $('#wcdp-text-opacity').val(obj.opacity);        

                    // Add text & placeholder to textarea
                    var el   = $('#wcdp-new-textbox'),
		                ph   = 'placeholder',
	                    newt = wcdp_translations.new_text;					
	                if(obj.text == newt)
					    el.attr(ph, newt).val('');				
				    else if(obj[ph] == true) 
					    el.attr(ph, obj.text).val('');				
				    else 
					    el.val(obj.text).removeAttr(ph);		    
                    el.removeAttr('disabled');

	                // Check effects curved text
				    var i_text  = obj.type == 'i-text',
			            radius  = i_text ? 300 : obj.radius,
			            spacing = i_text ? obj.charSpacing / 25 : obj.spacing,
			            effect  = i_text ? 'none' : (obj.reverse == true) ? 'reverse' : obj.effect;					
		        
				    $('#wcdp-text-radius').val(radius);
		            $('#wcdp-text-spacing').val(spacing);
			        $('#wcdp-text-curved').val(effect);
	  
                } else if(obj.clas == 'img' || obj.clas == 'svg' || obj.clas == 'groupSVG'){
			
			        // Select Tab
                    wcdp_change_select_tab('wcdp-btn-images');
				
				    // Check Filters and colors
			        wcdp_check_images_filters_and_colors_svg(obj, 'images');
				
				    // Identify obj patch
				    var o = obj._objects ? obj._objects[0] : (obj.paths ? obj.paths[0] : obj);

				    // Check Image Stroke
				    $('#wcdp-svg-stroke').val(o.strokeWidth);
				
				    // Check Image Opacity
			        $('#wcdp-image-opacity').val(o.opacity);
				
                } else if( obj.clas == 'shap'){
				
				    // Select Tab
		            wcdp_change_select_tab('wcdp-btn-shapes');
				
				    // Check Tools selected
	                $('#wcdp-shap-color').spectrum('set', obj.fill).spectrum('setCMYK', obj.fillCMYK);
	                $('#wcdp-shap-outline-color').spectrum('set', obj.stroke).spectrum('setCMYK', obj.strokeCMYK);
			        $('#wcdp-shap-stroke').val(obj.paths ? obj.paths[0].strokeWidth : obj.strokeWidth);
                    $('#wcdp-shap-opacity').val(obj.opacity);
	  
                } else if(obj.clas == 'clip-img' || obj.clas == 'clip-svg'){

			        // Select Tab
                    wcdp_change_select_tab('wcdp-btn-cliparts');
				
				    // Check Filters and colors
			        wcdp_check_images_filters_and_colors_svg(obj, 'cliparts');
				
				    // Check Cliparts Stroke
				    $('#wcdp-clip-svg-stroke').val(obj.paths ? obj.paths[0].strokeWidth : obj.strokeWidth);
				
				    // Check Cliparts Opacity
			        $('#wcdp-clipart-opacity').val(obj.opacity);
				
                } else if( obj.clas == 'qr'){
			
				    // Select Tab
                    wcdp_change_select_tab('wcdp-btn-qr');	
				
			        // Check Tools selected
				    $('#wcdp-bg-qr-color').spectrum('set', obj.paths[0].fill).spectrum('setCMYK', obj.paths[0].fillCMYK);
	                $('#wcdp-fg-qr-color').spectrum('set', obj.paths[1].fill).spectrum('setCMYK', obj.paths[1].fillCMYK);
				    $('#wcdp-text-box-qr').val(obj.dataDP.text);
				    $('#wcdp-qr-level').val(obj.dataDP.level);
				    $('#wcdp-qr-border').val(obj.dataDP.border);
				    $('#wcdp-qr-range').val(obj.dataDP.range);
  
                } else if( obj.clas == 'maps'){

				    // Select Tab
                    wcdp_change_select_tab('wcdp-btn-maps');
				
				    // Check Tools selected
				    $('#wcdp-map-icon-color').spectrum('set', obj.dataDP.fill).spectrum('setCMYK', obj.dataDP.fillCMYK);
				    $('#wcdp-text-box-map').val(obj.dataDP.address.replace(/\+/g,' '));
				    $('#wcdp-map-zoom').val(obj.dataDP.zoom);
			        $('#wcdp-map-type').val(obj.dataDP.type);
				    $('#wcdp-map-icon-size').val(obj.dataDP.size);					
				    $('#wcdp-map-icon-label').val(obj.dataDP.label);
                }
            }
	        wcdp_check_group_ungroup_obj(obj);
			wcdp_manage_overlay_layers('bring');
			wcdp_check_auto_hide_bleed(true);
        });
		
		function wcdp_reset_options_by_default(){

	        // Default Tab Text
		    if($('#wcdp-text-panel').css('display') == 'block'){
		        $('.dp-col-8 span').removeClass('dp-item-selected');
		        $('#wcdp-text-fontFamily').val('titillium');
	            $('#wcdp-text-fontSize').val(wcdp_parameters.font_size);
		        $('#wcdp-new-textbox').removeAttr('placeholder').attr('disabled', 'disabled').val('');
	            $('#wcdp-text-color-font').spectrum('set', wcdp_style.text_color_editor.RGB).spectrum('setCMYK', wcdp_style.text_color_editor.CMYK);
	            $('#wcdp-text-outline-color').spectrum('set', wcdp_style.text_color_editor_outline.RGB).spectrum('setCMYK', wcdp_style.text_color_editor_outline.CMYK);
		        $('#wcdp-text-stroke, #wcdp-text-spacing').val(0);
		        $('#wcdp-text-radius').val(300);
		        $('#wcdp-text-curved').val('none');
		        $('#wcdp-text-opacity').val(1);
		    } 
			// Default Tab Images
			else if($('#wcdp-images-panel').css('display') == 'block'){
				$('.new-spectrum-fill-js, .new-spectrum-stroke-js').spectrum('destroy');
				$('#wcdp-svg-color, #wcdp-svg-outline-color').spectrum('set', '#000000').spectrum('setCMYK', '0,0,0,100');
				$('.wcdp-box-svg-multicolor .mCSB_container, .wcdp-box-svg-stroke-multicolor .mCSB_container').html('');
				$('.wcdp-box-svg-multicolor, .wcdp-box-svg-stroke-multicolor').parent().hide();
				$('#wcdp-images-panel .wcdp-box-filters-btn span').removeClass('dp-item-selected');
				$('#wcdp-images-panel .wcdp-box-filters-rng input').val(0);
				$('#wcdp-svg-stroke').val(0);
				$('#wcdp-image-opacity').val(1);
		    }
			// Default Tab Shapes 
			else if($('#wcdp-shapes-panel').css('display') == 'block'){				
	            $('#wcdp-shap-color').spectrum('set', wcdp_style.color_shapes_editor.RGB).spectrum('setCMYK', wcdp_style.color_shapes_editor.CMYK);
	            $('#wcdp-shap-outline-color').spectrum('set', wcdp_style.color_shapes_editor_outline.RGB).spectrum('setCMYK', wcdp_style.color_shapes_editor_outline.CMYK);
			    $('#wcdp-shap-stroke').val(0);
				$('#wcdp-shap-opacity').val(1);
			}
			// Default Tab Cliparts  
			else if($('#wcdp-cliparts-panel').css('display') == 'block'){
				$('.new-spectrum-fill-js, .new-spectrum-stroke-js').spectrum('destroy');
				$('#wcdp-clip-svg-color, #wcdp-clip-svg-outline-color').spectrum('set', '#000000').spectrum('setCMYK', '0,0,0,100');
				$('.wcdp-box-svg-multicolor .mCSB_container, .wcdp-box-svg-stroke-multicolor .mCSB_container').html('');
				$('.wcdp-box-svg-multicolor, .wcdp-box-svg-stroke-multicolor').parent().hide();
				$('#wcdp-cliparts-panel .wcdp-box-filters-btn span').removeClass('dp-item-selected');
				$('#wcdp-cliparts-panel .wcdp-box-filters-rng input').val(0);
				$('#wcdp-clip-svg-stroke').val(0);
				$('#wcdp-clipart-opacity').val(1);
		    }	    
            // Default Tab QR
			else if($('#wcdp-qrcode-panel').css('display') == 'block'){
				$('#wcdp-bg-qr-color').spectrum('set', wcdp_style.bg_color_qr_editor.RGB).spectrum('setCMYK', wcdp_style.bg_color_qr_editor.CMYK);
	            $('#wcdp-fg-qr-color').spectrum('set', wcdp_style.color_qr_editor.RGB).spectrum('setCMYK', wcdp_style.color_qr_editor.CMYK);
				$('#wcdp-text-box-qr').val('');
				$('#wcdp-qr-level').val('LOW');
				$('#wcdp-qr-border').val(0);
				$('#wcdp-qr-range').val(1);
			}
            // Default Tab Maps
			else if($('#wcdp-maps-panel').css('display') == 'block'){
				$('#wcdp-map-icon-color').spectrum('set', wcdp_style.map_color_ico.RGB).spectrum('setCMYK', wcdp_style.map_color_ico.CMYK);
				$('#wcdp-text-box-map').val('');
				$('#wcdp-map-zoom').val(1);
			    $('#wcdp-map-type').val('roadmap');
				$('#wcdp-map-icon-size').val('normal');					
				$('#wcdp-map-icon-label').val('');
			}

            // Default Toolbar options
	        wcdp_tip_content('group');
			
            if(wcdp_last_obj_lock && !wcdp_last_obj_lock.active){
			    wcdp_tip_content('lock');
			    wcdp_layer_item('clear');
			}
			wcdp_draw_mask_layers();
			wcdp_check_auto_hide_bleed(false);
			
			// Enable Shortcutkeys
			if(!document.onkeydown) document.onkeydown = wcdp_short_cut_keys;
        };
	        
		// Selection Cleared
        wcdp_canvas_editor.on('selection:cleared', wcdp_reset_options_by_default);
		
		// Check images filters and append SVG fill & stroke colors
		function wcdp_check_images_filters_and_colors_svg(o, el){
			wcdp_reset_options_by_default();
			if(o.clas == 'img' || o.clas == 'clip-img'){
			    var btn = 'dp-item-selected',
			        box = '#wcdp-'+ el +'-panel .wcdp-box-filters';
			    $(box +'-btn span').removeClass(btn);				
				if(o.filters.length > 0){			
				    for(var i in o.filters){
		                var df = o.filters[i].type, 
					        mx = (df == 'ColorMatrix' || df == 'Convolute') && o.filters[i].matrix.toString().length;					
						df = df == 'Grayscale' ? 'grayscale':
					         df == 'Invert' ? 'invert':
						     df == 'Sepia' ? 'purple':
						     df == 'Sepia2' ? 'yellow':
						     df == 'Noise' ? 'noise':
						     df == 'Pixelate' ? 'pixelate':
						     df == 'GaussianBlur' ? 'blur':
							 df == 'Brightness' ? 'brightness':
							 df == 'Saturate' ? 'saturate':
							 df == 'Contrast' ? 'contrast':
                             mx == 102 ? 'sepia':							 
						     mx == 59 ? 'warm':
						     mx == 48 ? 'cold':
						     mx == 251 ? 'kodachrome':
						     mx == 245 ? 'vintage':
						     mx == 252 ? 'brownie':
					         mx == 81 ? 'polaroid':
						     mx == 249 ? 'technicolor':
						     mx == 56 ? 'acid':
						     mx == 39 ? 'shiftToBGR':
						     mx == 197 ? 'fantasy':
						     mx == 51 ? 'ghost':
						     mx == 248 ? 'predator':
						     mx == 50 ? 'night':
						     mx == 21 ? 'sharpen':
						     mx == 23 && 'emboss';
                        if(df == 'brightness' || df == 'saturate' || df == 'contrast')
							$(box +'-rng [data-filter="'+ df +'"]').val(o.filters[i][df]/100);						
						else						
					        $(box +'-btn [data-filter="'+ df +'"]').addClass(btn);
		            }	
				} else{
					$(box +'-btn [data-filter="none"]').addClass(btn);
				}	
			} else if(o.clas == 'svg' || o.clas == 'clip-svg' || o.clas == 'groupSVG'){
				var gr = o.clas == 'groupSVG',
                    cl = gr ? 'svg' : o.clas,
					ph = gr ? o._objects[0] : (o.paths ? o.paths[0] : o),
					tb = gr ? 'images' : el,
					tp = 'transparent',
					ct = '0,0,0,0';                    
                		
	            if(wcdp_is_same_color(o, 'fill'))
				    $('#wcdp-'+ cl +'-color').spectrum('set', ph.fill ? ph.fill : tp).spectrum('setCMYK', ph.fillCMYK ? ph.fillCMYK : ct);									
				else
				    wcdp_append_color_paths_svg(o, tb, gr, 'fill');
									
				if(wcdp_is_same_color(o, 'stroke'))
    				$('#wcdp-'+ cl +'-outline-color').spectrum('set', ph.stroke ? ph.stroke : tp).spectrum('setCMYK', ph.strokeCMYK ? ph.strokeCMYK : ct);
    			else
				    wcdp_append_color_paths_svg(o, tb, gr, 'stroke');								
			}
		}
        function wcdp_is_same_color(o, fy){
		    if(o._objects || o.paths){	
                var fc = o.getObjects()[0].get(fy) || '';
                if(typeof fc !== 'string'){
                    return false;
                }
                fc = fc.toLowerCase();
                return o.getObjects().every(function(path){
                    var op = path.get(fy) || '';
                    return typeof op === 'string' && (op).toLowerCase() === fc;
                });
			} else{
                if(typeof o.get(fy) !== 'string'){
                    return false;
                }
                return true;
			}
        }
		function wcdp_append_color_paths_svg(o, tb, gr, fy){
            var clrs  = [],
                count = 0,
				paths = gr ? o._objects : (o.paths ? o.paths : [o]),
				box   = $('#wcdp-'+ tb +'-panel .wcdp-box-svg-'+ (fy == 'fill' ? '': fy + '-') +'multicolor');
            for(var i = 0; i < paths.length; i++){
                var arr  = [],                    			
                    op   = paths[i][fy],
					cm   = paths[i][fy +'CMYK'],
					path = op;			        
				if(op && (op.type == 'linear' || op.type == 'radial')){
				    for(var j = 0; j < op.colorStops.length; j++){
						var stopLR = {colorStops: j}
						stopLR[fy] = op.colorStops[j].color;
						stopLR[fy +'CMYK'] = op.colorStops[j][fy +'CMYK'];
					    arr.push(stopLR);
					}
			    } else{
                    arr.push({colorStops: 'patch'});
			    }
                for(var k = 0; k < arr.length; k++){
                    var val = arr[k].colorStops;
                    if(val != 'patch'){	
                        op = path.colorStops[val].color;
					    cm = path.colorStops[val][fy +'CMYK'];
					}
					op = op ? op : 'transparent';
					cm = cm ? cm : '0,0,0,0';
                    var cc = $.inArray(op, clrs);
                    if(cc == -1 && paths[i].type != 'image'){						
						box.find('.mCSB_container').append(
						    '<input type="text" '+
							    'id="clip_'+ fy +'_'+ count +'" '+
							    'class="dp-picker-'+ fy +'-'+ count +' new-spectrum-'+ fy +'-js" '+
							    'callback="change_svg_fill_multicolor" '+
								'data-paths="' + i + ':' + val + '"'+
							    'data-value="' + op + '" '+
							    'data-fill="'+ fy +'" '+
							    'data-id="' + i + '" '+	
								'value="' + op + '" '+
							    'cmyk="'+ cm +'"'+					    
							'/>'
						);
    			        clrs.push(op);
                        count++;
			        } else{
                        $('#clip_'+ fy +'_'+ cc).attr('data-paths', function(){
							return $(this).attr('data-paths') + ',' + i +':'+ val;
						});
                    }
				}    					
		    }
			var ins = function(n){
                setTimeout(function(){
	                $.spectrum.installPicker('dp-picker-'+ fy +'-'+n); n++;
			        if(count !== n) ins(n);
			        else if(o.active) box.parent().show();		        
                },1);		
			}
			ins(0);			
		}
		
        // Check obj center		
        function wcdp_check_obj_center(obj){
            if(wcdp_style.obj_center == 'on' || obj.clas == 'maps'){
			    var point = obj.getPointByOrigin('center', 'center'),
				    bound = wcdp_get_bounding_bleed();
									
				point.x = bound ? (bound.w / 2) + bound.x : wcdp_parameters.canvas_w / 2;
				point.y = bound ? (bound.h / 2) + bound.y : wcdp_parameters.canvas_h / 2;
			                    
				obj.setPositionByOrigin(point, 'center', 'center');
                obj.setCoords();
		        wcdp_canvas_editor.renderAll();
	        }
        }
		
        // Check visible layers and background or overlay image in the design	
        function wcdp_check_visible_layers(ins){
			if(wcdp_parameters.hide_bg == 'on' && wcdp_canvas_editor.backgroundImage)
                wcdp_canvas_editor.backgroundImage.visible = ins;
			
			if(wcdp_parameters.hide_ov == 'on' && wcdp_canvas_editor.overlayImage)
                wcdp_canvas_editor.overlayImage.visible = ins;
			
			if(wcdp_parameters.hide_bc == 'on'){
				if(ins && wcdp_background_color){
					wcdp_canvas_editor.backgroundColor = wcdp_background_color;
				} else{
					wcdp_background_color = wcdp_canvas_editor.backgroundColor;
                    wcdp_canvas_editor.backgroundColor = 'transparent';
				}
			}
			
            wcdp_canvas_editor.forEachObject(function(obj){
				if(obj.hide)
					obj.visible = ins;
			});
        }
        
        // Shortcutkeys
        function wcdp_short_cut_keys(e){
		    var getKey = (e.shiftKey) ? 'shift+' : (e.altKey) ? 'alt+' :(e.ctrlKey) ? 'ctrl+' : '';
            for(var i in wcdp_shortcutkeys){
                if(wcdp_shortcutkeys[i] === getKey + e.keyCode){
                    var key = i;
                }
            }
            if(wcdp_shortcutkeys[key+'_ck'] == 'on'){
				var obj = wcdp_get_selection_obj(),
                    group = wcdp_canvas_editor.getActiveGroup(),
					objs = 'all', cmd = true, set = {};

                // Move Obj Keys					
                if(key == 'moveup' || key == 'movedown' || key == 'moveleft' || key == 'moveright'){
                    var item = obj.length == 1 ? obj[0] : (group ? group : false);
					if(item){
						var ins = key.substring(4);
					    if(ins == 'up')
						    item.set('top', item.getTop() - 1);
						
					    else if(ins == 'down')
						    item.set('top', item.getTop() + 1);
						
					    else if(ins == 'left')
						    item.set('left', item.getLeft() - 1);
						
					    else if(ins == 'right')
						    item.set('left', item.getLeft() + 1);

						item.setCoords();
                        wcdp_canvas_editor.renderAll();
                        wcdp_save_canvas_state();
					}
        	    }
				// Select All
				else if(key == 'select_all'){
                    $('#wcdp-btn-select-all').click();			
	            }
				// Erase All
				else if(key == 'erase_all'){
                    $('#wcdp-btn-clear').click();		
	            }
				// Grid
				else if(key == 'grid'){
                    $('#wcdp-btn-grid').click();		
	            }
				// Center Vertically
				else if(key == 'center_vertical'){
                    $('#wcdp-btn-center-v').click();		
	            }
				// Center Horizontally
				else if(key == 'center_horizontal'){
                    $('#wcdp-btn-center-h').click();		
	            }
				// Flip Vertically
				else if(key == 'flip_vertical'){
                    $('#wcdp-btn-flipY').click();		
	            }
				// Flip Horizontally
				else if(key == 'flip_horizontal'){
                    $('#wcdp-btn-flipX').click();		
	            }
				// Clone Side To Side
				else if(key == 'clone_sides'){						
                    if(obj.length > 0 && wcdp_parameters.type != 'typeF'){
                        var sides = $('#wcdp-canvas-thumbs-container div');
						sides.not('.dp-canvas-active').trigger('click', function(){
							var reverse = group && group.clas != 'clone' ? true : false;
	                        wcdp_clone_obj(obj, reverse, false);
						});
				    }                         					
	            } 
				// Bring to front
				else if(key == 'bring_front'){
				    $('#wcdp-btn-bringForward').click();				
	            } 
				// Send to back
				else if(key == 'send_back'){
				    $('#wcdp-btn-sendBackwards').click();		
	            } 
				// Lock & Unlock
				else if(key == 'lock'){
				    $('#wcdp-btn-lock').click(); 				
	            } 
				// Clone
				else if(key == 'duplicate'){
				    wcdp_duplicate_obj();				        				
	            } 
				// Align Vertical Group With Spaces
				else if(key == 'magic_more' || key == 'magic_less'){					
					if(obj.length > 1){
                        if(group.clas != 'magic'){
                            group.clas = 'magic';                               								
                            var coords = [];
                            for(var i = 0; i < obj.length; i++){
                                coords.push(obj[i].getTop());  
                            } 
				            OUTER_LOOP: for(var j = 0; j < obj.length; j++){
	                            var valueMin = Math.min.apply(null, coords);  
                                INNER_LOOP: for(var k = 0; k < obj.length; k++){   
			                        if(obj[k].getTop() == valueMin){
			                            obj[k].bringToFront();   
                                        coords.splice(coords.indexOf(valueMin), 1);
                                        continue OUTER_LOOP;
                                    }
                                }
                            }                                							
				        }		                                
                        for(var l=1; l < obj.length; l++){
			                obj[l].set({top: obj[l].getTop()+((key == 'magic_more') ? +l:-l)}).setCoords();
                        }
						group.addWithUpdate();
                        wcdp_canvas_editor.renderAll();
                        wcdp_save_canvas_state();
					}						
	            } 
				// Align Left, Right
				else if(key == 'align_left' || key == 'align_right'){
					wcdp_obj_align(key.substring(6));
	            } 
				// Rotate
				else if(key == 'rotate'){
				    $('#wcdp-btn-rotate').click();
	            } 
				// Reset State
				else if(key == 'return_state'){
                    set	= {angle:0,skewX:0,skewY:0}
                    cmd = false;					
	            } 
				// Align Horizontal, Vertical Group Synchronized
				else if(key == 'align_horizontal'|| key == 'align_vertical'){
                    if(group){
				        group.forEachObject(function(tar){
					        var point = tar.getPointByOrigin('center', 'center');
					        if(key == 'align_horizontal')
					            point.x = 0;

			                else if(key == 'align_vertical')
								point.y = 0;

			                tar.setPositionByOrigin(point, 'center', 'center');
                            tar.setCoords();
				        });
                        wcdp_canvas_editor.renderAll();
                        wcdp_save_canvas_state();						
			        }						
	            } 
				// Group Ungroup
				else if(key == 'group'){						
                    $('#wcdp-btn-group').click();				        			
	            } 
				// Delete Obj
				else if(key == 'delete'){
				    wcdp_delete_obj();
	            }
				// Undo
				else if(key == 'undo'){
				    $('#wcdp-btn-undo').click();
	            } 
				// Redo
				else if(key == 'redo'){
				    $('#wcdp-btn-redo').click();
	            } 
				// Line Height Text
				else if(key == 'line_text_large' || key == 'line_text_small'){
			        var param = (key == 'line_text_large') ? '+' : '-';
                    set = {lineHeight: 'obj.lineHeight' + param + '0.1'}
				    objs = 'i-text';
	            }  
				if(Object.keys(set).length){
			        wcdp_apply_changes_obj({  
		                'obj': objs,
			            'set': set,
			            'cmd': cmd
		            });
				}
		        return false;
			}			
		}
		
		// Disable Shortcutkeys
	    $('body').on('mousedown, keydown', 'input[type=text], input[type=number], [contenteditable], textarea', function(e){
			e.stopPropagation();
			if(document.onkeydown) document.onkeydown = null;
		});
	    wcdp_canvas_editor.on('text:editing:entered', function(e){
			e.target.placeholder = false;
			document.onkeydown = null;
		}).on('text:changed', function(e){
            $('#wcdp-new-textbox').val(e.target.text);
		});

		// Toolbar Keyboard Shortcuts
        $('#wcdp-btn-info').jBox('Modal',{
			animation: 'zoomIn',
			closeButton: 'title',
			draggable: 'title',
			maxHeight: 600,
            title: wcdp_translations.shortcuts.title,
            content: wcdp_content_shortcuts(),
			addClass: wcdp_rtl_mode ? 'md__rtl':'',
        });
		function wcdp_content_shortcuts(){
		    var html = '<ul>';
			var keys = wcdp_shortcutkeys;
			
			if(wcdp_parameters.type == 'typeF')
			    keys.clone_sides_ck = false;			
			
		    for(var i in keys){
				if(keys[i] != 'on' && keys[i+'_ck'] == 'on'){
			        var getValue = keys[i].split('+');
					getValue = (getValue.length == 1) ? wcdp_translations.shortcuts.keyCodes[getValue[0]] : getValue[0]+ '+'+ wcdp_translations.shortcuts.keyCodes[getValue[1]];
				    html += '<li><b>'+ getValue.toUpperCase() +':</b> '+ wcdp_translations.shortcuts[i] +'</li>';
				}
			}
			if(typeof getValue === 'undefined') html += '<li>'+ wcdp_translations.shortcuts.keys_empty +'</li>';
            return html +'</ul>';
		}
		
		// Toolbar Select All
        $('#wcdp-btn-select-all').click(function(e){
            e.preventDefault();
			wcdp_canvas_editor.discardActiveObject();
			wcdp_canvas_editor.discardActiveGroup();
			var objs = [],
			getObjs = wcdp_canvas_editor.getObjects();
            for(var i = (getObjs.length - 1); i >= 0; i--){
				if(getObjs[i].selectable)
					objs.push(getObjs[i].set('active', true));
            }
		    if(objs.length == 1)
			    wcdp_canvas_editor.setActiveObject(objs[0]);
			
			else if(objs.length > 1)
	            wcdp_set_active_group(objs.reverse());
			
			wcdp_canvas_editor.renderAll();
		});
		function wcdp_set_active_group(objs, clone){
            var group = new fabric.Group(objs, {
				clas: clone ? 'clone' : '',
                canvas: wcdp_canvas_editor
            });
            group.addWithUpdate(null);
            wcdp_canvas_editor.setActiveGroup(group);
            group.setCoords();
            wcdp_canvas_editor.trigger('selection:created', {
                target: group
            });            			
		}
		
		// Toolbar Erase all
		$('#wcdp-btn-clear').click(function(e){
            e.preventDefault();
			wcdp_jbox_msg_confirm(wcdp_translations.clear, function(){
				wcdp_canvas_editor.discardActiveGroup();
			    var getObjs = wcdp_canvas_editor.getObjects();
                for(var i = (getObjs.length - 1); i >= 0; i--){
				    if(getObjs[i].selectable)
					    wcdp_canvas_editor.remove(getObjs[i]);
                }
                wcdp_loading.show();
			    wcdp_refresh_thumb_side().then(function(){
				    wcdp_save_canvas_state();
				    wcdp_loading.hide();
					wcdp_draw_layers();
			    });
			});		
	    });
		
        // Toolbar grid
        $('#wcdp-btn-grid').click(function(e){
			e.preventDefault();
			var btn = 'btn-enabled';
            if($(this).hasClass(btn)){
				wcdp_canvas_editor.remove(wcdp_canvas_grid);
				$(this).removeClass(btn);
			} else{
				wcdp_canvas_editor.add(wcdp_canvas_grid);
			    $(this).addClass(btn);
			}
			wcdp_canvas_editor.renderAll();
        });
		
        // Drawing grid
        function wcdp_draw_grid(){
            var width     = wcdp_parameters.canvas_w,
                height    = wcdp_parameters.canvas_h,
				size      = wcdp_parameters.grid_size,
                gridGroup = new fabric.Group([], {						
					clas       : 'grid',
					evented    : false,
					selectable : false
				}),
                lineOpts  = {
					stroke          : 'rgba(0,0,0,.2)',					
					strokeDashArray : [3, 3],
					strokeWidth     : 1
				};
            for(var i = Math.ceil(width/size); i--;)
                gridGroup.add(new fabric.Line([size*i, 0, size*i, height], lineOpts));
            for(var i = Math.ceil(height/size); i--;)
                gridGroup.add(new fabric.Line([0, size*i, width, size*i], lineOpts));
		
			return gridGroup;
		}

	    // Auto snap guides & Snapping to grid
        wcdp_canvas_editor.on('object:moving', function(e){
            var obj = e.target;
	        if($('#wcdp-btn-grid').hasClass('btn-enabled')){
		        var size = wcdp_parameters.grid_size;
                obj.set({
				    left: Math.round(obj.left / size) * size,
					top: Math.round(obj.top / size) * size
				});
	        }
	        else if(wcdp_style.auto_snap == 'on'){
                obj.setCoords();
	            var tol = parseInt(wcdp_style.snap_tolerance >= 1 ? wcdp_style.snap_tolerance : 5),
				    zoom = this.getZoom(),
	                vpt = this.viewportTransform,
                    point = obj.getPointByOrigin('center', 'center'),
                    obr = obj.getBoundingRect(),
					bbr = wcdp_get_bounding_bleed(),
				    cx = bbr ? (bbr.w / 2) + bbr.x : wcdp_parameters.canvas_w / 2,
					cy = bbr ? (bbr.h / 2) + bbr.y : wcdp_parameters.canvas_h / 2,
	                vs = 'none',
	                hs = 'none',
	                vl = 0,
	                ht = 0;
				
                this.forEachObject(function(tar){
                    if(tar.clas != 'bleed' && tar.clas != 'grid' && tar != obj && !tar.active){
						var tbr = tar.getBoundingRect();
                        if(Math.abs(obr.left - tbr.left) < tol){
			                point.x = (tbr.left - vpt[4] + (obr.width / 2)) / zoom;
			                vl = tbr.left;
			                vs = 'block';
		                }
                        if(Math.abs(obr.left + obr.width - tbr.left) < tol){
                            point.x = (tbr.left - vpt[4] - (obr.width / 2)) / zoom;
			                vl = tbr.left;
			                vs = 'block';
                        }
                        if(Math.abs(obr.left - tbr.left - tbr.width) < tol){
		                    point.x = (tbr.left - vpt[4] + tbr.width + (obr.width / 2)) / zoom;
			                vl = tbr.left + tbr.width;
			                vs = 'block';
		                }
                        if(Math.abs(obr.left + obr.width - tbr.left - tbr.width) < tol){
		                    point.x = (tbr.left - vpt[4] + tbr.width - (obr.width / 2)) / zoom;
			                vl = tbr.left + tbr.width;
			                vs = 'block';
		                }
		                if(Math.abs(obr.top - tbr.top) < tol){
			                point.y = (tbr.top - vpt[5] + (obr.height / 2)) / zoom;
			                ht = tbr.top;
			                hs = 'block';
		                }
		                if(Math.abs(obr.top + obr.height - tbr.top) < tol){
			                point.y = (tbr.top - vpt[5] - (obr.height / 2)) / zoom;
			                ht = tbr.top;
			                hs = 'block';
		                }
                        if(Math.abs(obr.top - tbr.top - tbr.height) < tol){
			                point.y = (tbr.top - vpt[5] + tbr.height + (obr.height / 2)) / zoom;
			                ht = tbr.top + tbr.height;
			                hs = 'block';
		                }
                        if(Math.abs(obr.top + obr.height - tbr.top - tbr.height) < tol){
			                point.y = (tbr.top - vpt[5] + tbr.height - (obr.height / 2)) / zoom;
			                ht = tbr.top + tbr.height;
			                hs = 'block';
                        }
                    }
                });
		        if(Math.abs((obr.left - vpt[4] + obr.width / 2) - (cx * zoom)) < tol){
		            point.x = cx;
			        vl = (cx * zoom) + vpt[4];
			        vs = 'block';
		        }
		        if(Math.abs((obr.top - vpt[5] + obr.height / 2) - (cy * zoom)) < tol){
		            point.y = cy;
			        ht = (cy * zoom) + vpt[5];
			        hs = 'block';
		        }
				if(vs == 'block' || hs == 'block'){
		            obj.setPositionByOrigin(point, 'center', 'center');
                    obj.setCoords();
				}
                wcdp_snap_linev.css({'display': vs, 'left': vl + 'px'});
                wcdp_snap_lineh.css({'display': hs, 'top': ht + 'px'});
		    }
        });

		// Toolbar Align Horizontal, Vertical
		$('#wcdp-btn-center-h, #wcdp-btn-center-v').click(function(e){
		    e.preventDefault();
			wcdp_obj_align($(this).attr('id').substring(9));
		});

        // Object alignment
        function wcdp_obj_align(ins, obj){
            if(wcdp_get_selection_obj().length == 1)
		        obj = wcdp_canvas_editor.getActiveObject();
			else
				obj = wcdp_canvas_editor.getActiveGroup();
		
			if(obj){
				if(obj.type == 'i-text' && obj.isEditing)
			        obj.exitEditing();

			    var zoom = wcdp_canvas_editor.getZoom(),
				    point = obj.getPointByOrigin('center', 'center'),
			        obr = obj.getBoundingRect(),
				    bbr = wcdp_get_bounding_bleed();

			    if(ins == 'left')
				    point.x = (bbr ? bbr.x : 0) + (obr.width / 2 / zoom);

			    else if(ins == 'right')
				    point.x = (bbr ? bbr.x + bbr.w : wcdp_parameters.canvas_w) - (obr.width / 2 / zoom);		

			    else if(ins == 'center-h')
				    point.x = bbr ? (bbr.w / 2) + bbr.x : wcdp_parameters.canvas_w / 2;

			    else if(ins == 'center-v')
				    point.y = bbr ? (bbr.h / 2) + bbr.y : wcdp_parameters.canvas_h / 2;

			    obj.setPositionByOrigin(point, 'center', 'center');
                obj.setCoords();
			    wcdp_canvas_editor.renderAll();
			    wcdp_save_canvas_state();
			}
		}

        // Toolbar Flip Horizontal, Vertical
        $('#wcdp-btn-flipX, #wcdp-btn-flipY').click(function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'all',
			    'set': {toggle: $(this).attr('id').substring(9)}
		    });			
		});
		
        // Toolbar Rotate
        $('#wcdp-btn-rotate').click(function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'all',
			    'set': {angle: 'obj.getAngle()-15'},
				'cmd': true
		    });
			var group = wcdp_canvas_editor.getActiveGroup();
			if(group){
				group.addWithUpdate();
				wcdp_canvas_editor.renderAll();
			}				
		});

		// Toolbar z-index
        $('#wcdp-btn-bringForward, #wcdp-btn-sendBackwards').click(function(e){
			e.preventDefault();
            var param = $(this).attr('id').substring(9),
			el = wcdp_canvas_editor.getActiveObject();
			if(el) el[param](); else return false;
			wcdp_manage_overlay_layers('bring');
			wcdp_canvas_editor.renderAll();
			wcdp_save_canvas_state();
			wcdp_draw_layers();
		});
		
        // Toolbar Lock Unlock Obj		
		$('#wcdp-btn-lock').click(function(e){
			e.preventDefault();
		    wcdp_lock_unlock_obj();
		});
        wcdp_canvas_editor.on('mouse:dblclick', wcdp_lock_unlock_obj).on('mouse:down', function(e){
			wcdp_check_lock_unlock_obj(e.target, true); 
        
		}).on('mouse:up', function(e){
	        if(!wcdp_canvas_editor.selection)
				wcdp_canvas_editor.selection = true;

			if(wcdp_corners_actions == 'remove')
			    wcdp_delete_obj();

			else if(wcdp_corners_actions == 'duplicate')
				wcdp_duplicate_obj();

			wcdp_corners_actions = 0;
			wcdp_snap_linev.hide();
            wcdp_snap_lineh.hide();
            wcdp_draw_mask_layers();
        
		}).on('mouse:move', function(e){
	        var obj = e.target;
			if(obj){
	            if(obj.selectable)
                    obj.set('hoverCursor', 'move');
	            else
				    obj.set('hoverCursor', 'pointer');
            }
        
		}).on('object:added', function(e){
			var obj = e.target;
			if(obj.lockMovementX){
                obj.set({
					selectable: false,
		            lockMovementX: false,
                    lockMovementY: false
				});				
			}
			if(wcdp_last_obj_lock && wcdp_last_obj_lock.active){
			    wcdp_tip_content('lock'); 
                wcdp_last_obj_lock.active = false;
			}
			if(wcdp_parameters.bleed_clip == 'on' && wcdp_canvas_bleed_area){
				if(obj.clas != 'bleed' && obj.clas != 'grid' && typeof obj.clipTo != 'function'){
				    obj.set('clipTo', function(ctx){ 
					    try{
						    return this._drawClipArea(ctx);
					    } catch(e){}
				    });
				}
			}
			if(!obj.id)
			    obj.set('id', wcdp_random_string());
			
			if(!obj.layerThumb)
			    wcdp_add_layer_thumb(obj);

			if(obj.lockUser && wcdp_parameters.editor == 'frontend')
				obj.set({'evented': false, 'selectable': false});
			
			if(wcdp_style.hide_middle_corners == 'on')
                obj.setControlsVisibility({mt: false, mb: false, ml: false, mr: false});
			
			obj.setCoords();
			
			setTimeout(function(){
			    wcdp_draw_mask_layers();
			}, 100);
		});
		function wcdp_lock_unlock_obj(){
			var objs = [],
			getObjs = wcdp_canvas_editor.getObjects();
            for(var i = (getObjs.length - 1); i >= 0; i--){
				if(getObjs[i].active)
					objs.push(getObjs[i]);
            }			
            if(objs.length == 1){ 
				var obj = objs[0];
		        var lock = obj.selectable ? false : true;
				if(!obj.isEditing){
                    obj.set({
		                active: false,
                        selectable: lock
					});
                    wcdp_save_canvas_state();		
				    wcdp_check_lock_unlock_obj(obj);
				}			    
			} else{
				wcdp_canvas_editor.deactivateAll().renderAll();
				wcdp_layer_item('clear');
			}
		}
        function wcdp_check_lock_unlock_obj(obj, target){
			wcdp_tip_content('lock');
			if(obj && (obj.type != 'group' || obj.clas == 'grouped' || obj.clas == 'groupSVG')){
                if(obj.selectable){  
                    obj.set({
						editable: true,
		                hasControls: true,
					});
					if(!obj.active)
					    wcdp_canvas_editor.setActiveObject(obj);
					
					wcdp_layer_item('lock', obj.id);
			    }
				else if(!obj.isEditing){
					wcdp_last_obj_lock = obj; 
					if(target)
					    wcdp_canvas_editor.selection = false;
					
					wcdp_canvas_editor.discardActiveObject(obj);
                    obj.set({
						active: true,
						editable: false,
		                hasControls: false,
					});
                    wcdp_tip_content('unlock');
                    wcdp_layer_item('unlock', obj.id);
					wcdp_layer_item('select', obj.id);
			    }
				wcdp_canvas_editor.renderAll();
			}
            else if(!obj){
                wcdp_layer_item('clear');
			}
            if(!document.onkeydown) document.onkeydown = wcdp_short_cut_keys;			
	    }
		
		// Change tooltip content lock & group
		function wcdp_tip_content(ins){
			var	bt = 'btn-enabled',
				ct = 'data-jbox-content',
				tr = wcdp_translations[ins],
				id = ins.replace('un', ''),
				el = $('#wcdp-btn-'+ id);
            
			if(ins == 'lock' || ins == 'group')
    			el.removeClass(bt).attr(ct, tr);                			
			else
				el.addClass(bt).attr(ct, tr);	

			if(id == 'lock')
                wcdp_tooltip_lock.setContent(tr);
        //    else		
        //        wcdp_tooltip_group.setContent(tr);				
		}

        // Toolbar Group Ungroup
        $('#wcdp-btn-group').click(function(e){
			e.preventDefault();
			var obj = wcdp_get_selection_obj(),
			    req = wcdp_parameters.editor == 'backend' || wcdp_parameters.ungroup_svg == 'on';
            if(obj.length == 1){
                if(obj[0].clas == 'grouped' || (req && obj[0].clas == 'groupSVG'))
                    wcdp_ungroup_grouped(obj[0]);
         	    
				else if(req && obj[0].clas != 'qr' && obj[0].type == 'path-group' && obj[0].paths.length > 1)
	                wcdp_ungroup_svg(obj[0]);				
				
            } else if(obj.length > 1)
	            wcdp_create_grouped(obj, req);          
		});
        function wcdp_create_grouped(items, req){
	        wcdp_loading.show();
			wcdp_overlay_loading.show();
			setTimeout(function(){
				var onlyPath = true,
				    group = wcdp_canvas_editor.getActiveGroup();
				if(group.clas == 'clone')
					group._objects.reverse();				
                group.clone(function(grouped){
                    wcdp_canvas_editor.discardActiveGroup();
                    items.forEach(function(o){
					    if(o.type == 'path-group' || o.clas == 'grouped' || o.clas == 'groupSVG')
							onlyPath = false;
		                wcdp_canvas_editor.remove(o);  
	                });
		            grouped.clas = (req && onlyPath) ? 'groupSVG' : 'grouped';
                    wcdp_canvas_editor.add(grouped).setActiveObject(grouped).renderAll();
                    wcdp_save_canvas_state();
                    wcdp_loading.hide();
			        wcdp_overlay_loading.hide();
					wcdp_draw_layers();
                });				
			}, 100);
		}
        function wcdp_ungroup_grouped(item){
	        wcdp_loading.show();
			wcdp_overlay_loading.show();
			setTimeout(function(){				
                var items = item._objects;
                item._restoreObjectsState();
                for(var i = 0; i < items.length; i++){
                    wcdp_canvas_editor.add(items[i]).item(wcdp_canvas_editor.size()-1).set('active', true);
                }
                wcdp_canvas_editor.remove(item);
				wcdp_set_active_group(items);
				wcdp_canvas_editor.renderAll();
                wcdp_save_canvas_state();
                wcdp_loading.hide();
			    wcdp_overlay_loading.hide();
                wcdp_draw_layers();				
			}, 100);			
		}
        function wcdp_ungroup_svg(item){
	        wcdp_loading.show();
			wcdp_overlay_loading.show();
			setTimeout(function(){				
                for(var i = 0; i < item.paths.length; i++){  
				    var o = item.paths[i]; 
					o._removeTransformMatrix();
				    if(o.type == 'text'){
					    var closestSize = 0,
						    smallestSize = Math.abs(o.fontSize - wcdp_font_sizes[0]),							
						    getFont = o.fontFamily.replace(/["']/g, '').split(',')[0],
							swt = o.strokeWidth ? Math.round(o.strokeWidth *10)/ 10 : 0,
							orig_w = o.width,
						    orig_h = o.height;
                        for(var s = 1; s < wcdp_font_sizes.length; s++){
                            var currentSize = Math.abs(o.fontSize - wcdp_font_sizes[s]);
                            if(currentSize < smallestSize){
                                smallestSize = currentSize;
                                closestSize = s;
                            }
                        }
                        o = new fabric.IText(o.text,{
		                    clas: 'i-text',		                        
		                    top: o.top,
				    		left: o.left,
				            fontSize: wcdp_font_sizes[closestSize],
							fontStyle: o.fontStyle ? o.fontStyle : '',
							fontWeight: o.fontWeight ? o.fontWeight : '',
							textAlign: o.textAlign ? o.textAlign : 'left',
							textDecoration: o.textDecoration ? o.textDecoration : '',						
							fill: o.fill ? o.fill : 'transparent',
							fillCMYK: o.fillCMYK ? o.fillCMYK :  '0,0,0,0',
							stroke: o.stroke ? o.stroke : 'transparent',
							strokeCMYK: o.strokeCMYK ? o.strokeCMYK : '0,0,0,0',
							strokeWidth: swt > 10 ? 10 : swt,
							strokeLineJoin: 'round',
							fontFamily: $.inArray(getFont, wcdp_storage_fonts) == -1 ? 'titillium' : getFont	
	                    });
						o.set({
							scaleX: orig_w / o.width,
							scaleY: orig_h / o.height
						});
				    }
					else if(o.type == 'image'){
                        o.clas = item.clas.replace('svg', 'img');					 
				    }
					else{
						var swp = o.strokeWidth ? Math.round(o.strokeWidth) : 0;
						o.set({
							clas: item.clas,
							opacity: o.opacity ? o.opacity : 1,
							fill: o.fill ? o.fill : 'transparent',
							fillCMYK: o.fillCMYK ? o.fillCMYK : '0,0,0,0',
							stroke: o.stroke ? o.stroke : 'transparent',
							strokeCMYK: o.strokeCMYK ? o.strokeCMYK : '0,0,0,0',
							strokeWidth: swp > 50 ? 50 : swp		
						});
					}
                    item.paths[i] = o;
					o.set({'id': wcdp_random_string(), 'layerThumb': true});
				    wcdp_canvas_editor.add(o).item(wcdp_canvas_editor.size()-1).set('active', true); 
                }
                var group = new fabric.Group(item.paths, {
				    skewX: item.skewX,
                    scaleX: item.scaleX,
                    scaleY: item.scaleY,	
                    canvas: wcdp_canvas_editor
                }).addWithUpdate(null).setAngle(item.getAngle());
				group.set({
				    top: item.top,
				    left: item.left,
					width: item.getWidth(),
					height: item.getHeight(),                   				
				}).addWithUpdate().setCoords();
                wcdp_canvas_editor.trigger('selection:created', {
                    target: group
                });
                wcdp_canvas_editor.remove(item).setActiveGroup(group).renderAll();
			    group.getObjects().forEach(function(obj){
					wcdp_add_layer_thumb(obj);
     			});
                wcdp_save_canvas_state();
                wcdp_loading.hide();
			    wcdp_overlay_loading.hide();
				wcdp_draw_layers();
			}, 100);					
        }
        function wcdp_check_group_ungroup_obj(obj){
			var cl  = obj.clas,
			    req = wcdp_parameters.editor == 'backend' || wcdp_parameters.ungroup_svg == 'on';			
			wcdp_tip_content('group');
            if(cl == 'grouped' || (req && (cl == 'groupSVG' || (cl != 'qr' && obj.type == 'path-group' && obj.paths.length > 1))))
                wcdp_tip_content('ungroup');			
		}
		
		// Toolbar Clone
        $('#wcdp-btn-duplicate').click(function(e){
			e.preventDefault();
            wcdp_duplicate_obj();
		});

		// Toolbar Delete Obj
        $('#wcdp-btn-delete').click(function(e){
			e.preventDefault();
			wcdp_delete_obj();
		});
		
	    // Toolbar Redo, Undo
        $('#wcdp-btn-undo, #wcdp-btn-redo').click(function(e){
			e.preventDefault();
            var side = $('#wcdp-canvas-thumbs-container .dp-canvas-active').attr('data-id'),
		    position = wcdp_parameters.jsonStates[side],
		    getID = $(this).attr('id').substring(9);
		    if(getID == 'undo')	position--; else position++;
            if(!$(this).hasClass('btn-disabled')){
                wcdp_canvas_editor.clear();
                wcdp_parameters.jsonStates[side] = position;
                wcdp_canvas_editor.loadFromJSON(JSON.parse(wcdp_parameters.jsonSides[side][position]), function(){
                    wcdp_canvas_editor.renderAll.bind(wcdp_canvas_editor);
					wcdp_set_pr_color();
					wcdp_auto_bleed_color(0);
					wcdp_manage_overlay_layers('add');
					wcdp_draw_layers();
				});
                wcdp_update_undo_redo_state(side);
            }	
        });   
	    function wcdp_update_undo_redo_state(side){
            var dSb = 'btn-disabled',
            undo = $("#wcdp-btn-undo"),
		    redo = $("#wcdp-btn-redo"),
		    position = wcdp_parameters.jsonSides[side];
	        if(position.length == 1 || wcdp_parameters.jsonStates[side] == 0){
                undo.addClass(dSb);
		    } else{
                undo.removeClass(dSb);
		    }
            if(position.length > 0 && wcdp_parameters.jsonStates[side] < position.length - 1){
		        redo.removeClass(dSb);
		    } else{
                redo.addClass(dSb);
		    }			
			setTimeout(function(){
				wcdp_canvas_bg_color_checked();
			}, 100);
	    }
		
		// Toolbar Preview Design
        $('#wcdp-btn-preview').click(function(e){
			e.preventDefault();		
			if(wcdp_parameters.preview_e == 'on'){
				var el = $(this),
                btn = 'btn-enabled',				
			    sides = $('#wcdp-canvas-thumbs-container div');
			    el.addClass(btn);
			    wcdp_create_img_preview().then(function(){
				    if(sides.length == 1){ 
                        el.removeClass(btn);
					    wcdp_jbox_preview_image();
				    } else{
					    sides.not('.dp-canvas-active').click();
				    }			
			    });
			}
		});
		function wcdp_create_img_preview(){
			wcdp_loading.show();
			var r = $.Deferred();
			setTimeout(function(){
                var img = wcdp_canvas_data_url(wcdp_parameters.preview_w / wcdp_canvas_editor.getWidth(), 'jpeg'),
			    url = URL.createObjectURL(wcdp_base64_to_blob(img, 'image/jpeg')),
				label = $('.dp-canvas-active label').html();
    		    $('body').append('<a class="wcdp-preview-img" href="'+ url +'" data-jbox-image title="'+ label +'"></a>');
                r.resolve(); 
			}, 500);			
			return r.promise();	
		}		
        function wcdp_base64_to_blob(data, mime){ 
            var arr = data.split(','),
            bstr = atob(arr[1]),
			n = bstr.length,
			u8arr = new Uint8Array(n);
            while(n--){
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new Blob([u8arr], {type:mime});
        }		
		function wcdp_jbox_preview_image(){
            new jBox('Image',{
				closeButton: 'title',
				closeOnClick: 'body',
				title: wcdp_translations.preview,
				maxWidth: wcdp_parameters.preview_w + 'px',
				onCloseComplete: function(){
					this.destroy();
					$('.wcdp-preview-img').remove();
				}
            });
			$('.wcdp-preview-img').eq(0).click();		
            wcdp_loading.hide();	
		}

		// Action duplicate object
		function wcdp_duplicate_obj(){
            var group = wcdp_canvas_editor.getActiveGroup(),
			    reverse = group && group.clas != 'clone' ? true : false;
            wcdp_clone_obj(wcdp_get_selection_obj(), reverse, true);			
		}
		function wcdp_clone_obj(objs, reverse, set){
            wcdp_canvas_editor.discardActiveGroup();
			var count = objs.length;
			if(count > 0){
			    if(reverse) objs.reverse();
			    var init = function(i){
				    if(i == 0){
					    wcdp_select_clone_objs(count);
				    } else{
					    i--;
                        if(fabric.util.getKlass(objs[i].type).async){
       		                objs[i].clone(function(clone){
                                wcdp_add_canvas_clone(clone, set);
							    init(i);
                            });
                        } else{
       	                    wcdp_add_canvas_clone(objs[i].clone(), set);
						    init(i);
                        }					
				    }
			    }
			    init(count);
            }		
		}
		function wcdp_add_canvas_clone(clone, set){
			if(set == true){
				clone.set({
					left: clone.left +20,
					top: clone.top +20
				});
			}
			clone.set('id', wcdp_random_string());
            wcdp_canvas_editor.add(clone.setCoords());
        };
        function wcdp_select_clone_objs(items){
            wcdp_canvas_editor.deactivateAll().discardActiveGroup();
            var objs = [], count = 0,
            getObjs = wcdp_canvas_editor.getObjects();
            for(var i = (getObjs.length - 1); i >= 0; i--){
                if(count < items) objs.push(getObjs[i].set('active', true)); count++;
            }
			if(objs.length == 1)
				wcdp_canvas_editor.setActiveObject(objs[0]);
			else
                wcdp_set_active_group(objs, true);
			
			wcdp_canvas_editor.setCursor('default');
			wcdp_canvas_editor.renderAll();
			wcdp_save_canvas_state();
			wcdp_draw_layers();
        }
		
		// Action delete object
		function wcdp_delete_obj(){
			var obj = wcdp_get_selection_obj();
			if(obj.length >= 1){
				wcdp_canvas_editor.discardActiveObject();
			    wcdp_canvas_editor.discardActiveGroup();
		        $.each(obj,function(key, obj){
					obj.editable = false;
			        wcdp_canvas_editor.remove(obj);
			    });
			    wcdp_canvas_editor.setCursor('default');
				wcdp_canvas_editor.renderAll();
			    wcdp_save_canvas_state();
			    wcdp_draw_layers();
			}
		}

        // Get Selection obj
        function wcdp_get_selection_obj(){
            if(wcdp_canvas_editor.getActiveObject()){
                return [wcdp_canvas_editor.getActiveObject()];
            } else if(wcdp_canvas_editor.getActiveGroup()){
                return wcdp_canvas_editor.getActiveGroup().getObjects();
            }
            return [];
        }

		// Get object by id
		function wcdp_get_obj_by_id(id){
		    var getObjs = wcdp_canvas_editor.getObjects();
		    for(var i = 0; i < getObjs.length; ++i){
			    if(getObjs[i].id && getObjs[i].id == id){
				    return getObjs[i];
				    break;
			    }
		    }
		}
		
		// Add templates
		$('#wcdp-templates-panel .dp-tpl-content').on('click', '.dp-img-contain', function(e){
			e.preventDefault();
            var el = $(this),
			    pageID = el.attr('page-id'),
			    designID = el.attr('design-id');
			if(wcdp_parameters.loadObjs === 'on'){
                wcdp_loading.show();
				wcdp_overlay_loading.show();
                var templateURL = WCDP_URL_UPLOADS +'/save-admin/designID'+ designID +'/front.json';
	            $.getJSON(templateURL +'?'+ new Date().getTime(), function(data){
	                wcdp_canvas_editor.discardActiveObject();
				    wcdp_canvas_editor.discardActiveGroup();
			        var getObjs = wcdp_canvas_editor.getObjects();
                    for(var i = (getObjs.length - 1); i >= 0; i--){
				        if(getObjs[i].clas != 'bleed' && getObjs[i].clas != 'grid')
					        wcdp_canvas_editor.remove(getObjs[i]);
                    }
				    if(data.objects && data.objects.length > 0){
					    var fonts = [];
					    data.objects.forEach(function(o){
		                    if(o.clas == 'i-text' && fonts.indexOf(o.fontFamily) == -1)
                                fonts.push(o.fontFamily);
                        });
						wcdp_font_on_load(fonts).then(function(){
                            fabric.util.enlivenObjects(data.objects, function(objects){
		                        if(objects.length == 1){
							        var obj = objects[0];
			                        wcdp_canvas_editor.add(obj);
			                    }
			                    else if(objects.length > 1){
                                    objects.forEach(function(o){
                                        wcdp_canvas_editor.add(o);
                                    });
									wcdp_set_active_group(objects);
									var obj = wcdp_canvas_editor.getActiveGroup();
					            }
			                    var point = obj.getPointByOrigin('center', 'center'),
								    bound = wcdp_get_bounding_bleed();
									
								point.x = bound ? (bound.w / 2) + bound.x : wcdp_parameters.canvas_w / 2;
								point.y = bound ? (bound.h / 2) + bound.y : wcdp_parameters.canvas_h / 2;
			                    
								obj.setPositionByOrigin(point, 'center', 'center');
                                obj.setCoords();
			                    wcdp_canvas_editor.discardActiveGroup();
						        wcdp_canvas_editor.renderAll();
                                wcdp_save_canvas_state();
                                wcdp_loading.hide();
					            wcdp_overlay_loading.hide();
                            });
						});
				    } else{
                        wcdp_loading.hide();
					    wcdp_overlay_loading.hide();					
					}
	            }).fail(function(data){
                    wcdp_jbox_msg_modal(wcdp_translations.error_process);
                    wcdp_loading.hide();
					wcdp_overlay_loading.hide();
		        });
            } else if(wcdp_parameters.loadAjax === 'on' && pageID){
                wcdp_loading.show();
				wcdp_overlay_loading.show();
			    $.ajax({
                    url: AJAX_URL,
                    type: 'POST',
                    data: {
                        'action': 'wcdp_get_json_design_ajax',
					    'designID': designID,
     		        },
			        success: function(response){ 
				        var params = JSON.parse(response);
				        if(params){
						    setTimeout(function(){ 
							    wcdp_font_on_load(params.fontsDesign).then(function(){
			                        if(wcdp_parameters.paramID !== params.paramID){
				                        var newsParams = [
							                'border_bleed_e', 'auto_hide_bleed', 'bleed_clip', 'border_bleed_w', 'margin_bleed_lr',
											'margin_bleed_tb', 'bleed_top',	'bleed_left', 'bleed_radius', 'canvas_h', 'canvas_w',
											'font_size', 'image_size', 'shape_size', 'qr_size', 'grid_size', 'label_f', 'label_b',
											'maps_h', 'maps_w', 'output_w', 'paramID', 'pdf_h', 'pdf_margin', 'pdf_w', 'pdf_scale',
											'pdf_strech', 'preview_w', 'radius', 'type', 'watermark_img', 'watermark_rep', 'hide_bc',
											'hide_bg','hide_ov'
						                ];
						                for(var i = 0; i < newsParams.length; i++)
							                wcdp_parameters[newsParams[i]] = params[newsParams[i]];

							            wcdp_canvas_bleed_area = wcdp_draw_bleed_area();
							            wcdp_canvas_grid = wcdp_draw_grid();							
							            wcdp_canvas_editor.setWidth(params.canvas_w);
							            wcdp_canvas_editor.setHeight(params.canvas_h);

								        var sides  = {'front': params.label_f},
                                            thumbs = $('#wcdp-canvas-thumbs-container'),
										    height = 100 * params.canvas_h / params.canvas_w +'px',
										    style  = 'style="height:'+ height +'"',
										    base64 = 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="';
									
									    thumbs.empty();
									    if(params.type != 'typeF')
										    sides['back'] = params.label_b;									
									
									    for(var key in sides)
										    thumbs.append('<div data-id="'+ key +'"><img width="100" '+ base64 + style +'/><label>'+ sides[key] +'</label></div>');										
									
    								    $('#wcdp-zoom-move').css('height', height);
    							    }
								    wcdp_canvas_responsive();
								    wcdp_parameters.pageID = pageID;
								    wcdp_parameters.designID = designID;
                                    wcdp_parameters.jsonSides = params.jsonSides;
			                        wcdp_parameters.jsonStates = params.jsonStates;
									wcdp_check_selected_attr_actions();
                                    wcdp_load_json_canvas(Object.keys(wcdp_parameters.jsonSides).reverse(), 0);
		                        });					        
						    }, 100);
					    } else{
						    wcdp_jbox_msg_modal(wcdp_translations.error_process);
							wcdp_loading.hide();
							wcdp_overlay_loading.hide();
					    }
				    }
                });
		    } else{
			    if(pageID){
			        wcdp_jbox_msg_confirm(wcdp_translations.load_template, function(){
			            window.open(SITE_URL +'/?page_id='+ pageID +'&dp_mode=designer&product_id='+ wcdp_parameters.productID +'&design_id='+ designID, '_self');
			        });				
			    } else{
				    wcdp_jbox_msg_modal(wcdp_translations.template_id +' <b>"'+ designID +'"</b> '+ wcdp_translations.product_unavailable);
			    }
		    }
		});

        // Text - Bold, Italic, Decoration, Align
        function wcdp_tools_itext(){
			var arrAlign = ['left', 'center', 'right'];
			if(wcdp_rtl_mode){
			    arrAlign[0] = 'right';
			    arrAlign[2] = 'left';
			}
	        return {
		        'fontWeight': ['bold'],
		        'fontStyle': ['italic'],
		        'textDecoration': ['underline', 'line-through', 'overline'],
		        'textAlign': arrAlign
	        }
        }
        $('#wcdp-text-panel .dp-col-8 span').click(function(e){
			e.preventDefault();
		    var params = wcdp_tools_itext(),
	        el = $(this).attr('id').substring(9), set = '', arr = {};
            $.map(params, function(item, key){  
	            if(item.indexOf(el) != -1){
					set = key;
				}  
            });
            arr[set] = el;
		    wcdp_apply_changes_obj({
			    'obj': 'i-text',			    
			    'set': arr,
				'default': true,
		    });
		    var obj = wcdp_get_selection_obj();
		    if(obj.length == 1){
			    wcdp_add_class_items_selected(obj[0], $('.dp-col-8 span'), params);
		    }
        });
        function wcdp_add_class_items_selected(obj,ds,params){
	        var i = 0,
		    btn = 'dp-item-selected';
	        ds.removeClass(btn);
            for(var key in params){
                for(var j = 0; j < params[key].length; j++){  
				    if(obj[key] == params[key][j]){
					    ds.eq(i).addClass(btn);
			        }
                    i++;				
                }
            }
        }

        // Text - FontFamily, Size, Radius, Spacing, Opacity
	    $('#wcdp-text-fontFamily, #wcdp-text-fontSize').change(function(e){
			e.preventDefault();
            var arr = {};			
			arr[$(this).attr('id').substring(10)] = $(this).val();
		    wcdp_apply_changes_obj({
		        'obj': 'i-text',
			    'set': arr,
				'point': true
		    });			
	    });	
		$('#wcdp-text-radius, #wcdp-text-spacing, #wcdp-text-opacity').on('input change', function(e){
			e.preventDefault();
            var arr = {},
			ins = $(this).attr('id').substring(10);
			arr[ins] = $(this).val();
		    wcdp_apply_changes_obj({
		        'obj': 'i-text',
			    'set': arr,
				'save': false,
				'point': ins != 'opacity' ? true : false
		    });
			if(e.type == 'change')
                wcdp_save_canvas_state();
	    });

        // Add New Text
        $('#wcdp-add-new-text').click(function(e){
			e.preventDefault();
	        var newText = wcdp_translations.new_text;
            $('#wcdp-new-textbox').attr('placeholder', newText);
            var text = new fabric.IText(newText,{
		        placeholder: true,
		        clas: 'i-text',
		        left: 50,
		        top: 50,
				textAlign: wcdp_rtl_mode ? 'right':'left',
		        fontFamily: 'titillium',
		        fontSize: wcdp_parameters.font_size,
		        fill: wcdp_style.text_color_editor.RGB,
		        fillCMYK: wcdp_style.text_color_editor.CMYK,
		        stroke: wcdp_style.text_color_editor_outline.RGB,
		        strokeCMYK: wcdp_style.text_color_editor_outline.CMYK,
		        strokeWidth: 0		
	        });
	        wcdp_canvas_editor.discardActiveGroup().add(text).renderAll().setActiveObject(text);
			wcdp_check_obj_center(text);
	        wcdp_save_canvas_state();
        });
	
        // Write Text
	    $('#wcdp-new-textbox').keyup(function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'i-text',
			    'set': {writeText: $(this).val(), placeholder: false},
				'point': true
		    });
	    });

        // Text Color
        wcdp_spectrum_functions.change_text_color = function(colorRGB, colorCMYK){		
		    wcdp_apply_changes_obj({
		        'obj': 'i-text',
			    'set': {fill: '#'+ colorRGB, fillCMYK: colorCMYK},
			    'save' : false
		    });
        }
	
        // Text Stroke
	    $('#wcdp-text-stroke').change(function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'i-text',
			    'set': {strokeWidth: parseFloat($(this).val()), strokeLineJoin: 'round'}
		    });	
	    });

        // Text Color Stroke
	    wcdp_spectrum_functions.change_text_color_outline = function(colorRGB, colorCMYK){
		    wcdp_apply_changes_obj({
		        'obj': 'i-text',
			    'set': {stroke: '#'+colorRGB, strokeCMYK: colorCMYK},
			    'save' : false
		    });		
	    }
	
	    // Apply Text Curved Effects
	    $('#wcdp-text-curved').change(function(e){
			e.preventDefault();
		    var obj = wcdp_get_selection_obj();
		    if(obj.length == 1 && obj[0].clas != 'grouped'){
			    var newObj = 'IText',
			    props = obj[0].toObject(),
                params = {
					'textAlign': 'center',
					'radius': 150,
					'spacing': 10,
					'effect': $(this).val(),
					'reverse': false
				};
			    delete props['type'];
                for(var i in params) delete props[i];	
			    if($(this).val() != 'none'){
				    newObj = 'CurvedText';
                    for(var j in params) props[j] = params[j];				
                    if($(this).val() == 'reverse'){
			            props['reverse'] = true;
				        props['effect'] = 'curved';
		            }                
		        }
			    newObj = new fabric[newObj](obj[0].getText(), props);	
                wcdp_canvas_editor.add(newObj).renderAll().setActiveObject(newObj).remove(obj[0]);
	            wcdp_save_canvas_state();
		    }
	    });

        // Upload Logo Image & SVG
        $('#wcdp-upload-images').click(function(e){
			e.preventDefault();	
		    $(this).prev().click();
        });
		$('#wcdp-ajax-upload-user').change(function(e){
			e.preventDefault();		
			var file = $(this).prop('files')[0];
			if(typeof file !== 'undefined'){
			    var ext = file.name.split('.').pop(),
			        arr = ['jpg','jpeg','png','gif'];
				if(wcdp_settings.upload_svg == 'on'){
					arr.push('svg');
				}
                if($.inArray(ext.toLowerCase(), arr) === -1){
					wcdp_jbox_msg_modal(wcdp_translations.supported +' '+ arr.toString());
                }
				else{
				    wcdp_loading.show();
					wcdp_overlay_loading.show();
                    var formData = new FormData();
					formData.append('file', file);
					formData.append('action', 'wcdp_upload_img_file_ajax');
                    $.ajax({
                        url: AJAX_URL,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function(response){
						    var data = JSON.parse(response);
                            if(data.success){
                                wcdp_url_verify(data.url, data.filename, true);
							}
			                else{
								ins();
                                wcdp_jbox_msg_modal(wcdp_translations.error_process);
							}
						},  
                        error: function(response){
							ins();
					        wcdp_jbox_msg_modal(response.statusText);
                        }
                    });
			    }
                var ins = function(){	
                    wcdp_loading.hide();
					wcdp_overlay_loading.hide();
			    }			
			}
		});
		
		// Upload wp enqueue media
		$.wcdp_upload_file_wp_enqueue_media_backend = function(url, name){ 
		    wcdp_url_verify(url, name, true);
		}
		
		// Resources Images - Pixabay, Unsplash, Pexels, Flaticon
	    $('#wcdp-images-panel .dp-hori-tab span').click(function(e){
			e.preventDefault();
            var el = $(this).attr('data-id'),
			    ct = $('#wcdp-images-panel'),
				mu = 'dp-mode-upload',
				ms = 'dp-mode-sources',
				ts = 'htab-selected',
				ht = ct.find('.dp-hori-tab');
				
            if(!$(this).hasClass(ts)){
			    ht.find('span').removeClass(ts);
			    ht.find('[data-id="'+ el +'"]').addClass(ts);			
			    if(el == 'upload'){
				    ct.removeClass(ms).addClass(mu);
			    }
			    else{
				    var ph = wcdp_translations.search_in +' '+ wcdp_text_ctz(el);
                    $('#wcdp-text-box-search-res').attr('placeholder', ph);
				    ct.removeClass(mu).addClass(ms);		
			        wcdp_manage_resources();
			    }
            } else if(!$('#wcdp-box-'+ el).hasClass('dp-loading-sources') && el != 'upload'){
				ct.find('.dp-row.md__res').eq(1).toggleClass('md__dsb');
			}
	    });
		$('#wcdp-btn-search-res').click(function(e){
		    e.preventDefault();
            wcdp_manage_resources();
        });
		$('#wcdp-text-box-search-res').keyup(function(e){
		    e.preventDefault(); 
			if(e.keyCode === 13)
			    wcdp_manage_resources();
		});			
		function wcdp_manage_resources(){
			var ct  = $('#wcdp-images-panel'),
			    res = ct.find('.dp-hori-tab .htab-selected').attr('data-id'),
			    el  = $('#wcdp-box-' + res);
			ct.find('.dp-row.md__res').eq(1).removeClass('md__dsb');
			ct.find('.dp-res-content').hide();	
			el.find('.dp-res-items').empty().append('<div class="dp-res-overlay"></div>');
			el.mCustomScrollbar('update').show();
            wcdp_sources_opt.page = 1;
			if(res == 'flaticon' && !wcdp_sources_opt.token){
				el.addClass('dp-loading-sources');
				wcdp_request_token_flaticon().then(function(token){
					if(token){
						wcdp_sources_opt.token = token;
						wcdp_append_resources_thumbs(res);
					} else{
						wcdp_jbox_msg_modal('Token not found');
					}
				});
			} else{
	            wcdp_append_resources_thumbs(res);
			}
		}
        function wcdp_append_resources_thumbs(res){
			var px  = res == 'pixabay',
				up  = res == 'unsplash',
				pe  = res == 'pexels',
				fl  = res == 'flaticon',
			    el  = '#wcdp-box-'+ res,
				sb  = el + ' .dp-res-items',
			    lr  = 'dp-loading-sources',
				pg  = wcdp_sources_opt.page,
				bs  = $('#wcdp-text-box-search-res'),
	            sh  = encodeURIComponent(bs.val()),
				ap  = fl ? 'Bearer '+ wcdp_sources_opt.token : wcdp_settings[res +'_api'],
				url = 'https://' + (
  				    px ? 'pixabay.com/api/?key='+ ap +'&page='+ pg +'&per_page=40&q='+ sh :					
					up ? 'api.unsplash.com/'+ (sh && 'search/') +'photos?client_id='+ ap + '&per_page=20&page='+ pg + (sh && '&query=' + sh) :
					pe ? 'api.pexels.com/v1/'+ (sh ? 'search?query='+ sh +'+query&' : 'curated?') +'per_page=20&page='+ pg :
					fl && 'api.flaticon.com/v2/search/icons?page='+ pg +'&q='+ sh
			    );
				if(pe || fl)
				    url = {url: url, headers: {'Authorization': ap}};

			$(el).addClass(lr);
	        $.getJSON(url, function(data){
                var results = px ? data.hits : up ? (sh ? data.results : data) : pe ? data.photos : fl && data.data;
                wcdp_sources_opt.totals = parseInt(px ? data.totalHits : up ? (sh ? data.total : 500) : pe ? (sh ? data.total_results : 500) : fl && data.metadata.total);
	            if(wcdp_sources_opt.totals > 0){
	                $.each(results, function(i, o){
                        var fi = px ? o.largeImageURL : up ? o.urls.regular : pe ? o.src.large : fl && (wcdp_settings.flaticon_svg == 'on' ? o.images.svg : o.images.png['512']),
                            tb = px ? o.previewURL : up ? o.urls.thumb : pe ? o.src.small : fl && o.images.png['128'],
                            tg = px || fl ? 'Tags: '+ o.tags : up ? 'Author: '+ o.user.name : pe && 'Photographer: '+ o.photographer,
                            hg = fl ? 106 : (px ? o.webformatHeight / o.webformatWidth : o.height / o.width) * 106,
                            it = '<span class="dp-img-res" data-source="'+ fi +'" data-tags="'+ tg +'" data-name="Image by '+ wcdp_text_ctz(res) +'">' +
						         '<img class="lazyload dp-loading-lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-src="'+ tb +'" style="height:'+ hg +'px;"/></span>';
  					    $(it).appendTo(sb);
		            });
                    if($(sb).data('masonry')){
	                    $(sb).masonry('reloadItems').masonry();
                    } else{
                        $(sb).masonry({
                            itemSelector: '.dp-img-res',
                            transitionDuration: 0
                        });
                    }
	                $(el).removeClass(lr);
                    wcdp_lazy_load(el +' img.lazyload');
	            } else{
				    $(el).removeClass(lr);
				    $(sb).empty();
			        if($(sb).data('masonry')){
			            $(sb).masonry('destroy');
					}
					$(el).mCustomScrollbar('update');
					$('<span class="wcdp-icon-res-empty dp-search-empty"></span><label>'+ wcdp_translations.no_resources +'</label>').appendTo(sb);
	            }
	        }).fail(function(data){
				var err = function(msg){
			        $(el).removeClass(lr);
				    wcdp_jbox_msg_modal(msg);					
				};
			    if(fl){
					var resm = JSON.parse(data.responseText);
					if(resm.message == 'Expired token'){
						wcdp_request_token_flaticon().then(function(token){
							if(token){
							    wcdp_sources_opt.token = token;
							    wcdp_append_resources_thumbs(res);
							} else{
								err('Token not found');
							}
						});
					}
					else{
						err(resm.message);
					}
				} else{
			        err(wcdp_translations.error_process);
				}
		    });
        }
		function wcdp_request_token_flaticon(){
		    var r = $.Deferred();
            $.ajax({
                url: 'https://api.flaticon.com/v2/app/authentication',
                method: 'post',
                data: {'apikey': wcdp_settings.flaticon_api},
                success: function(response){
	                r.resolve(response.data.token); 
                },
                error: function(response){
					r.resolve(false);
                }
			}); 
			return r.promise();
		}
		
		// Add images to canvas from resources
        $('#wcdp-images-panel .dp-contain-res').on('click', '.dp-img-res', function(e){
			e.preventDefault();			
            wcdp_loading.show();
			wcdp_overlay_loading.show();
			var this_ = $(this),
			    url   = this_.attr('data-source'),
			    res   = this_.parents('.dp-res-content').attr('id').substring(9);
			if(res != 'flaticon' && wcdp_settings.CMYK == 'on' && wcdp_settings.sources_cmyk == 'on'){
                $.ajax({
                    url: AJAX_URL,
                    type: 'POST',
                    data: {
                        'action': 'wcdp_convert_resource_cmyk',
			            'url': url	
     			    },						
				    success: function(response){ 
						var data = JSON.parse(response);				
                        if(data.success){
                             wcdp_add_img_canvas_logo(data.string, 'img');
						}						
                        else{
                            wcdp_jbox_msg_modal(wcdp_translations.error_process);
				    	    wcdp_loading.hide();
						    wcdp_overlay_loading.hide();							
			            }
					}
                });				
			} else{
				if(res == 'flaticon' && wcdp_settings.flaticon_svg == 'on'){
					wcdp_add_svg_canvas_logo(url, 'svg');
				} else{
                    wcdp_convert_image_to_string(url, function(string){
                        wcdp_add_img_canvas_logo(string, 'img'); 
                    });
				}
			}
        });
		
		// Convert images to string
        function wcdp_convert_image_to_string(url, callback){
            var xhr = new XMLHttpRequest();
            xhr.onload = function(){
				if(this.status == 200){
                    var reader = new FileReader();
                    reader.onloadend = function(){
                        callback(reader.result);
                    }
                    reader.readAsDataURL(xhr.response);
				} 
				else{
					wcdp_jbox_msg_modal(wcdp_translations.error_file);
				}
            };
            xhr.open('GET', url);
            xhr.responseType = 'blob';
            xhr.send();
        }
		
		// Convert GIF to PNG string
        function wcdp_convert_gif_to_png_string(url, callback){
		    var img = new Image();
            img.onload = function(){
                var cv = document.createElement('canvas');
                cv.width = this.naturalWidth;
                cv.height = this.naturalHeight;
                cv.getContext('2d').drawImage(this, 0, 0);
                callback(cv.toDataURL('image/png'));
            };
            img.src = url;
        }
		
		// Verify URL type image
        function wcdp_url_verify(url, name, thumb, clip){
            var ext = url.substr(url.lastIndexOf('.') + 1);
            if(ext == 'jpg' || ext == 'jpeg' || ext == 'png'){
                wcdp_convert_image_to_string(url, function(string){
					wcdp_add_img_canvas_logo(string, (clip ? 'clip-img' : 'img'), name, (thumb ? url : 0));
                });			
            } else if(ext == 'gif'){
                wcdp_convert_gif_to_png_string(url, function(string){
                    wcdp_add_img_canvas_logo(string, (clip ? 'clip-img' : 'img'), name, (thumb ? url : 0));
                });
            } else if(ext == 'svg'){			
                wcdp_add_svg_canvas_logo(url, (clip ? 'clip-svg' : 'svg'), name, thumb);
            } else{
				wcdp_jbox_msg_modal(wcdp_translations.error_process);
			}
        }
		
		// Add thumbnails to box uploads
		function wcdp_add_thumbnail_box(url, clas, name){
			var box = $('#wcdp-images-panel .dp-box-img .mCSB_container');
			if(!box.find('.dp-img-contain').length) box.html('');
            box.append('<span class="dp-img-contain dp-'+ clas +'" data-name="'+ name +'"><img class="loaded" src="'+ url +'"/></span>');
		}

		// Add images to canvas from thumbnails box
        $('#wcdp-images-panel .dp-box-img').on('click', '.dp-img-contain', function(e){
			e.preventDefault();
			var el = $(this);
		    wcdp_url_verify(el.find('img').attr('src'), el.attr('data-name'));
        });
		
		// Add IMG Canvas Logo
        function wcdp_add_img_canvas_logo(string, clas, name, thumb){
		    wcdp_loading.show();
			wcdp_overlay_loading.show();
            var img = new Image();
			img.crossOrigin = 'Anonymous';
            img.onload = function(){
			    var ratio = img.height / img.width,
			    size = typeof wcdp_parameters.image_size !== 'undefined' ? parseInt(wcdp_parameters.image_size) : 150,
                imgInstance = new fabric.Image(img,{ 
	                top: 40,
	                left: 40,
			        clas: clas,
					name: name,
                    width: size,
                    height: size * ratio,
					crossOrigin: 'Anonymous'
	            });
                wcdp_canvas_editor.discardActiveGroup().add(imgInstance).setActiveObject(imgInstance).renderAll();
				wcdp_check_obj_center(imgInstance);
				wcdp_save_canvas_state();
                wcdp_loading.hide();
				wcdp_overlay_loading.hide();
			    if(thumb)
				    wcdp_add_thumbnail_box(thumb, clas, name);
            };
            img.src = string;
            img.onerror = function(){
			    wcdp_jbox_msg_modal(wcdp_translations.error_process);
		    }			
		}
        
        // Add SVG Canvas Logo
        function wcdp_add_svg_canvas_logo(url, clas, name, thumb){
		    wcdp_loading.show();
			wcdp_overlay_loading.show();
            fabric.loadSVGFromURL(url, function(objects, options){ 
                var obj = fabric.util.groupSVGElements(objects, options),
                size = typeof wcdp_parameters.image_size !== 'undefined' ? parseInt(wcdp_parameters.image_size) : 150,
				colors = wcdp_patch_cliparts_CMYK(obj);
                obj.set({
	                top: 50,
	                left: 50,
		            clas: clas,
					name: name,
		            fill: colors[0],
			        fillCMYK: colors[1],
					stroke: colors[2],
					strokeCMYK: colors[3]
		        }).scaleToWidth(size).scaleToHeight(size * obj.height / obj.width);				
                wcdp_canvas_editor.discardActiveGroup().add(obj).setActiveObject(obj).renderAll();
				wcdp_check_obj_center(obj);
			    wcdp_save_canvas_state();
                wcdp_loading.hide();
				wcdp_overlay_loading.hide();
				if(thumb)
					wcdp_add_thumbnail_box(url, clas, name);  
            });			
	    }
		
        // Add Images Canvas Background or Overlay	
        function wcdp_add_image_canvas_bg_or_ov(url, props, ins){
            var img = new Image();
			img.crossOrigin = 'Anonymous';
            img.onload = function(){
				var imgInstance = new fabric.Image(img,{
                    left: 0,
                    top: 0,
					dataDP: props,
					opacity: props.opacity,
					crossOrigin: 'Anonymous',
                    width: parseInt(wcdp_parameters.canvas_w),
                    height: parseInt(wcdp_parameters.canvas_h)
				});
				if(ins == 'bg'){
				    wcdp_canvas_editor.setBackgroundImage(imgInstance).renderAll();
				}
				else if(ins == 'ov'){
                    wcdp_canvas_editor.setOverlayImage(imgInstance).renderAll();
				}
				wcdp_save_canvas_state();
            };
            img.src = url;
            img.onerror = function(){
			    wcdp_jbox_msg_modal(wcdp_translations.error_process);
		    }			
		}
		
		// Toolbar for images and cliparts tabs
	    $('#wcdp-tabs-content .dp-toolbar-img span').click(function(e){
			e.preventDefault();
			var this_ = $(this),
                el = this_.attr('data-id');
			
			if(el == 'fill' || el == 'filters' || el == 'mask'){
				var tb = $('.dp-toolbar-img'),
				    pn = $('.wcdp-tab-section'),
					os = 'opt-selected',
					bd = 'box__dsb';
				
				if(!this_.hasClass(os)){
					tb.find('span').removeClass(os);
					pn.find('.dp-row.box__tool').addClass(bd);
					tb.find('span[data-id="'+ el +'"]').addClass(os);
					pn.find('.dp-row.box__tool[data-id="'+ el +'"]').removeClass(bd);
				}
				else{
					tb.find('[data-id="'+ el +'"]').removeClass(os);
					pn.find('.dp-row[data-id="'+ el +'"]').addClass(bd);
				}
				wcdp_draw_mask_layers();
			}
			else if(el == 'crop'){
				wcdp_cropper_init();
			}			
            else if(el == 'ov' || el == 'bg'){
				wcdp_set_bg_ov(el);
			}
			else if(el == 'angle'){
				wcdp_change_bg_angle();
			}
		});

		// Draw list of mask layers
        function wcdp_draw_mask_layers(){
			if(wcdp_parameters.editor == 'backend' || wcdp_parameters.user_mask == 'on'){
				var mask = 0;
				var arr = ['images', 'cliparts'];
				for(var i in arr){
				    var el = $('#wcdp-'+ arr[i] +'-panel');
					if(el.css('display') == 'block'){
						mask = el.find('.box__tool[data-id="mask"]');
						break;
					}
				}
			    if(mask && !mask.hasClass('box__dsb')){
					var obj = wcdp_get_selection_obj(),
						remove  = mask.find('.dp-remove-mask'),
					    contain = mask.find('.mCSB_container');
					remove.hide();
					contain.empty();					
			        if(obj.length == 1 && (obj[0].clas == 'img' || obj[0].clas == 'clip-img')){
						var obj = obj[0],
                            html = wcdp_get_html_layers(obj);
					    if(obj.mask) remove.show();
			        }
					if(!html) html = '<div class="dp-mask-guide"></div><label>'+ wcdp_translations.no_mask +'</label>';
					contain.append(html);
			    }
			}
		}

		// Manage Mask Layers
		$('#wcdp-tabs-content').on('click', '.dp-contain-mask .dp-layer-row', function(e){
			e.preventDefault();
		    var obj = wcdp_get_selection_obj();
			if(obj.length == 1 && (obj[0].clas == 'img' || obj[0].clas == 'clip-img')){
				var obj = obj[0],
				    target = wcdp_get_obj_by_id($(this).attr('data-id'));
                if(target){
				    wcdp_loading.show();
			        wcdp_overlay_loading.show();				
				    setTimeout(function(){
			            var zval = wcdp_canvas_editor.viewportTransform[0];
				        wcdp_canvas_editor.setZoom(1);
						target.clipTo = null;
				        var url = target.toDataURL({multiplier: wcdp_parameters.output_w / (target.width * target.scaleX)});
						target.visible = false;
			            wcdp_canvas_editor.setZoom(zval);	
                        var mask = new Image();
                        mask.onload = function(){
                            obj.getMaskLayer(target, mask, function(data){
								obj.setSrc(data.mask, function(){
					                wcdp_canvas_editor.remove(target);
					                wcdp_canvas_editor.renderAll();
					                wcdp_save_canvas_state();
							        wcdp_loading.hide();
							        wcdp_overlay_loading.hide();
									wcdp_draw_mask_layers();
				                }, data.params);
				            });
                        }
                        mask.src = url;
				    }, 1);
				}
			}
		});
		$('#wcdp-tabs-content .dp-remove-mask').click(function(e){
			e.preventDefault();
		    var obj = wcdp_get_selection_obj();
			if(obj.length == 1 && obj[0].mask){
			    var obj = obj[0]; 
			    wcdp_loading.show();
			    wcdp_overlay_loading.show();	
		        setTimeout(function(){
			        var url = obj.mask,
                        img = new Image();
                    img.onload = function(){
                        obj.setSrc(img.src, function(){
							wcdp_add_layer_thumb(obj);
				            wcdp_canvas_editor.renderAll();
				            wcdp_save_canvas_state();
				            wcdp_loading.hide();
					        wcdp_overlay_loading.hide();
                            wcdp_draw_mask_layers();					
				        }, {left: obj.left, top: obj.top, width: obj.width, height: obj.height, mask: false});
                    }
                    img.src = url;
			    }, 1);
			}
		});
		
		// Patch cliparts CMYK color
        function wcdp_patch_cliparts_CMYK(obj){
		    var cf, cs, fc, sc, jsonCMYK = typeof obj.paramsCMYK !== 'undefined' ? JSON.parse(obj.paramsCMYK) : {};		    
	        $.each(obj.paths, function(key, path){
				var ps = path,
				    fy = 'fill',
					tp = 'transparent',
					op = path.fill ? path.fill : tp;
				for(var x = 0; x < 2; x++){
					var arr = [];
					if(x == 1){
						path = ps;
					    fy = 'stroke',
					    op = path.stroke ? path.stroke : tp;		    
					}
			        if(op.type == 'linear' || op.type == 'radial'){
				        for(var i = 0; i < op.colorStops.length; i++){							
						    var stopLR = {colorStops: i};
						    stopLR[fy] = op.colorStops[i].color;
					        arr.push(stopLR);
						}
					} else{
						var stopPH = {colorStops: 'patch'}
						stopPH[fy] = op;
			            arr.push(stopPH);
					}					
                    for(var j = 0; j < arr.length; j++){
				        var val = arr[j].colorStops,
					        clr = arr[j][fy];
				        if(val != 'patch')
					        path = op.colorStops[val];   
                    				
                        if(clr.match(/^(rgb|rgba)\(\s*([0-9]){1,3}\s*,\s*([0-9]){1,3}\s*,\s*([0-9]){1,3}\s*,*\s*([0-9])*\.*([0-9])*\s*\)$/i) || clr == tp){
                            clr = clr == tp ? tp : '#' + wcdp_rgb_to_hex(clr); 
						    if(val == 'patch')
				                path[fy] = clr;
	                        else	                           
                                path.color = clr;				    
				        }
	                    var cm = jsonCMYK[clr];	                
					    cm = clr == '#000000' ? '0,0,0,100' : typeof cm === 'undefined' ? '0,0,0,0' : cm;	                
				        path[fy +'CMYK'] = cm;
					    
						if(x == 0){						
						    cf = clr; fc = cm;						
					    } else{
							cs = clr; sc = cm;
						}						
                    }
				}
	        });
		    delete obj.paramsCMYK;
            return [cf, fc, cs, sc];		
	    }
		
        // SVG Color
        wcdp_spectrum_functions.change_svg_fill_color = function(colorRGB, colorCMYK, id){
			$('.new-spectrum-fill-js').spectrum('destroy');
			var el = $('.wcdp-box-svg-multicolor');
			el.find('.mCSB_container').html('');
			el.parent().hide();
			var items = ['svg', 'groupSVG'];
			for(var key in items){
		        wcdp_apply_changes_obj({
		            'obj': items[key],
			        'set': {fill: '#'+ colorRGB, fillCMYK: colorCMYK},
				    'paths': true,
				    'save' : false
		        });  
			}		
		}

        // SVG Multi Color
        wcdp_spectrum_functions.change_svg_fill_multicolor = function(colorRGB, colorCMYK, id){
			var obj = wcdp_get_selection_obj();
		    if(obj.length == 1 && (obj[0].clas == 'svg' || obj[0].clas == 'clip-svg' || obj[0].clas == 'groupSVG')){
				var el = $('#'+ id),
				    fy = el.attr('data-fill'),
                    dp = el.attr('data-paths').split(',');
                $.each(dp, function(key, value){ 
			        var o = obj[0],
					    val = value.split(':'),
			            path = o.clas == 'groupSVG' ? o._objects[val[0]] : (o.paths ? o.paths[val[0]] : o);	
				    if(val[1] == 'patch'){
					    path[fy] = '#' + colorRGB;
				    } else{
					    path = path[fy].colorStops[val[1]];
                        path.color = '#' + colorRGB;
				    }
                    path[fy +'CMYK'] = colorCMYK;
			    });
                wcdp_canvas_editor.renderAll();
            }
		}

		// SVG & Group Stroke
	    $('#wcdp-svg-stroke').on('input change', function(e){
			e.preventDefault();			
			var items = ['svg', 'groupSVG'];
			for(var key in items){
		        wcdp_apply_changes_obj({
		            'obj': items[key],
				    'paths': true,
			        'set': {strokeWidth: parseInt($(this).val())},
				    'save': false
		        });  
			}
			if(e.type == 'change')
                wcdp_save_canvas_state();
		});

        // SVG & GroupSVG Stroke Color
        wcdp_spectrum_functions.change_svg_fill_color_outline = function(colorRGB, colorCMYK, id){
			$('.new-spectrum-stroke-js').spectrum('destroy');
			var el = $('.wcdp-box-svg-stroke-multicolor');
			el.find('.mCSB_container').html('');
			el.parent().hide();
			var items = ['svg', 'groupSVG'];
			for(var key in items){
		        wcdp_apply_changes_obj({
		            'obj': items[key],
			        'set': {stroke: '#'+ colorRGB, strokeCMYK: colorCMYK},
				    'paths': true,
				    'save' : false
		        });  
			}			
    	}
		
	    // Image & SVG Opacity
	    $('#wcdp-image-opacity').on('input change', function(e){
			e.preventDefault();
			var items = ['img', 'svg', 'groupSVG'];
			for(var key in items){
		        wcdp_apply_changes_obj({
		            'obj': items[key],
			        'set': {opacity: $(this).val()},
					'paths': true,
					'save' : false
		        });  
			}
			if(e.type == 'change')
                wcdp_save_canvas_state();	
	    });	

		// Change Background Angle
		function wcdp_change_bg_angle(){
			var bgImg = wcdp_canvas_editor.backgroundImage;
			if(bgImg){
				var deg = bgImg.angle,
		            width = parseInt(wcdp_parameters.canvas_w),
			        height = parseInt(wcdp_parameters.canvas_h); 
                deg = deg == 270 ? 0 : deg += 90;
			    bgImg.set({
				    top: deg == 180 || deg == 270 ? height : 0,
				    left: deg == 90 || deg == 180 ? width : 0,
                    width: deg == 90 || deg == 270 ? height : width,
			        height: deg == 90 || deg == 270 ? width : height,
				    angle: deg,
			    });
			    wcdp_canvas_editor.renderAll();
				wcdp_save_canvas_state();
			}			
		}

	    // Crop image
		var wcdp_cropper_data = {};
		function wcdp_cropper_init(){
		    var obj = wcdp_get_selection_obj();
			if(obj.length == 1 && (obj[0].clas == 'img' || obj[0].clas == 'clip-img' || obj[0].clas == 'maps')){
				var trl = wcdp_translations,
			    tools = {
				    'move'         : trl.move, 
				    'crop'         : trl.crop,
				    'square'       : trl.square,
				    'zoom-in'      : trl.zoom_in,
				    'zoom-out'     : trl.zoom_out,
				    'reset-resize' : trl.reset,
					'cancel'       : trl.cancel,
					'save'         : trl.save_crop
			    },
				img = new Image();
                img.onload = function(){
				    var html = '<div id="wcdp-crop-resize"><img src="'+ this.src +'"/></div><div id="wcdp-crop-tools">';				
    			    for(var key in tools){
					    if(key == 'cancel' || key == 'save')
						    html += '<span id="wcdp-crp-'+ key +'">'+ tools[key] +'</span>';
					    else 
                            html += '<span class="dp-tooltip'+ (key == 'crop' ? ' btn-enabled' : '') +'" title="'+ tools[key] +'" id="wcdp-btn-'+ key +'"></span>';
				    }
			        html += '</div>';
			        wcdp_cropper_data.contain = new jBox('Modal',{
				        id: 'wcdp_cropper_contain',
                        addClass: wcdp_rtl_mode ? 'md__rtl':'',
					    content: html,
			            closeButton: false,
                        onCloseComplete: function(){
                            this.destroy();
						    wcdp_cropper_data.tooltip.destroy();
                        }
                    }).open();
					var cropImg = $('#wcdp-crop-resize img').get(0);
					wcdp_cropper_data.image = {'obj': obj[0]};
                    wcdp_cropper_data.tooltip = wcdp_jbox_tooltip('#wcdp-crop-tools .dp-tooltip');			
                    wcdp_cropper_data.cropper = new Cropper(cropImg, {
						toggleDragModeOnDblclick: false,
					    crop: function(event){
						    wcdp_cropper_data.image.coords = event.detail;
                        }						
					});
                };
                img.src = obj[0]._element.src;
			}			
		}
        $('body').on('click', '#wcdp-crop-tools span', function(e){
		    e.preventDefault();
		    var this_ = $(this),
			    btn = 'btn-enabled',
			    el = this_.attr('id').substring(9),
                cropper = wcdp_cropper_data.cropper;				
				
			if(el == 'move' || el == 'crop'){
                $('#wcdp-btn-move, #wcdp-btn-crop').removeClass(btn);	
				this_.addClass(btn);
				cropper.setDragMode(el);
			}
			else if(el == 'square'){
				if(this_.hasClass(btn)){
					this_.removeClass(btn);
				    cropper.setAspectRatio(NaN);			
				} else{
					this_.addClass(btn);
				    cropper.setAspectRatio(1.1);		
				}
			}
			else if(el == 'zoom-in'){		
		        cropper.zoom(0.1);
			}
			else if(el == 'zoom-out'){		
		        cropper.zoom(-0.1);
			}
			else if(el == 'reset-resize'){		
		        cropper.reset();
			}
			else if(el == 'cancel'){
                wcdp_cropper_data.contain.close();
			}
            else if(el == 'save'){
				wcdp_loading.show();
				setTimeout(function(){
					var data = wcdp_cropper_data.image,
					    width = data.obj._element.naturalWidth / data.obj.width,
                        heigh = data.obj._element.naturalHeight / data.obj.height,	
					    props = data.obj.toObject(),
			            img = new Image();					 
			        img.crossOrigin = 'Anonymous';
                    img.onload = function(){
                        if(props.clas == 'maps')
							props.clas = 'img';
					    props.filters = [];
						props.width = data.coords.width / width;
						props.height = data.coords.height / heigh;
						props.left = data.obj.left + (data.coords.x / width * data.obj.scaleX);
						props.top = data.obj.top + (data.coords.y / heigh * data.obj.scaleY);
                        var cropImg = new fabric.Image(img, props);
                        wcdp_canvas_editor.add(cropImg).setActiveObject(cropImg);
					    wcdp_canvas_editor.remove(data.obj).renderAll();
				        wcdp_save_canvas_state();
					    wcdp_loading.hide();
                    };
                    img.src = cropper.getCroppedCanvas().toDataURL('image/png');
                    img.onerror = function(){
			            wcdp_jbox_msg_modal(wcdp_translations.error_process);
		            } 
				}, 500);				
				wcdp_cropper_data.contain.close();				
			}
		});	

		// Set Image Background or Overlay
		function wcdp_set_bg_ov(ins){
		    var obj = wcdp_get_selection_obj(),
			    set = (ins == 'bg' ? 'background':'overlay') + 'Image',
			    im  = wcdp_canvas_editor[set],
			    ck  = obj.length == 1,
			    o   = ck && obj[0],
			    c   = o && o.clas;
			if(im || (ck && ((o.paths && (c == 'svg' || c == 'clip-svg')) || c == 'groupSVG' || c == 'img' || c == 'clip-img' || c == 'maps'))){
			    wcdp_loading.show();
			    setTimeout(function(o){
					if(o)
					    wcdp_canvas_editor.discardActiveObject(o);
			        if(im){
					    var img = im.dataDP;
				        wcdp_canvas_editor.discardActiveGroup();
						wcdp_canvas_editor[set] = null;
					    if(typeof img === 'undefined'){
						    wcdp_add_img_canvas_logo(im._element.src, 'img');
					    } else{
                            new fabric[fabric.util.string.camelize(fabric.util.string.capitalize(img.type))].fromObject(img, function(io){
                                wcdp_canvas_editor.add(io).setActiveObject(io).renderAll();
						        wcdp_save_canvas_state();
                            });
					    }
			        }
			        if(ck){
			            var url = '',
						    props = o.toObject();							
			            if(o.type == 'image'){
					        url = o._element.src;
			            } else{					    
					        var zval = wcdp_canvas_editor.viewportTransform[0];
						    wcdp_canvas_editor.setZoom(1);							
						    url = o.toDataURL({multiplier: wcdp_parameters.output_w / (o.width * o.scaleX)});	
						    wcdp_canvas_editor.setZoom(zval);
			            }
				        if(url){
				            wcdp_add_image_canvas_bg_or_ov(url, props, ins);
                            wcdp_canvas_editor.remove(o);	
				        }
			        }			
			        wcdp_loading.hide();
			    }, 100, o);
			}
        }

        // Image Filters
	    $('#wcdp-tabs-content .wcdp-box-filters-btn span').click(function(e){
			e.preventDefault();
            wcdp_apply_img_filters($(this).attr('data-filter'));
		});
		$('#wcdp-tabs-content .wcdp-box-filters-rng input').change(function(e){
			e.preventDefault();
		    wcdp_apply_img_filters($(this).attr('data-filter'), parseFloat(this.value*100));
		});	
		function wcdp_apply_img_filters(df, val){
		    var obj = wcdp_get_selection_obj();
			if(obj.length == 1 && (obj[0].clas == 'img' || obj[0].clas == 'clip-img')){
				wcdp_loading.show();
                wcdp_overlay_loading.show();				
				var box = '.wcdp-box-filters',
				    btn = 'dp-item-selected',
				    fss = obj[0].filters,
				    json = JSON.stringify(fss),
					f = fabric.Image.filters,
				    filter = df == 'grayscale' ? new f.Grayscale():
					    df == 'invert' ? new f.Invert():						
						df == 'purple' ? new f.Sepia():
				        df == 'yellow' ? new f.Sepia2():
                        df == 'noise' ? new f.Noise({ noise: 150 }): 
                        df == 'pixelate' ? new f.Pixelate({ blocksize: 10 }):
						df == 'blur' ? new f.GaussianBlur({ radius: 10 }):
                        df == 'brightness' ? new f.Brightness({ brightness: val }):
                        df == 'saturate' ? new f.Saturate({ saturate: val }):
                        df == 'contrast' ? new f.Contrast({ contrast: val }):
                        df == 'sepia' ? new f.ColorMatrix({
							matrix: [
							    0.393, 0.7689999, 0.18899999, 0, 0,
                                0.349, 0.6859999, 0.16799999, 0, 0,
                                0.272, 0.5339999, 0.13099999, 0, 0,
                                0, 0, 0, 1, 0,
							]
						}):							
						df == 'cold' ? new f.ColorMatrix({
							matrix: [
						        1, 0, 0, 0, 0,
                           	    0, 1, 0, 0, 0,
							    -0.2, 0.2, 0.1, 0.4, 0,
							    0, 0, 0, 1, 0
						    ]
						}):
                        df == 'warm' ? new f.ColorMatrix({
							matrix: [
				                0.8, 0.2, 0, 0, 0,
				                0.258, 0.742, 0, 0, 0,
				                0, 0.142, 0.858, 0, 0,
                                0, 0, 0, 1, 0
							]
						}):
                        df == 'shiftToBGR' ? new f.ColorMatrix({
							matrix: [
						        0, 0, 1, 0, 0,
			                    0, 1, 0, 0, 0,
			                    1, 0, 0, 0, 0,
                                0, 0, 0, 1, 0
						    ]
						}):
                        df == 'vintage' ? new f.ColorMatrix({
							matrix: [
						        0.6279345635605994, 0.3202183420819367, -0.03965408211312453, 0, 9.651285835294123,
			                    0.02578397704808868, 0.6441188644374771, 0.03259127616149294, 0, 7.462829176470591,
			                    0.0466055556782719, -0.0851232987247891, 0.5241648018700465, 0, 5.159190588235296,
                                0, 0, 0, 1, 0
						    ]
						}):
                        df == 'kodachrome' ? new f.ColorMatrix({
							matrix: [
							    1.1285582396593525, -0.3967382283601348, -0.03992559172921793, 0, 63.72958762196502,
			                    -0.16404339962244616, 1.0835251566291304, -0.05498805115633132, 0, 24.732407896706203,
			                    -0.16786010706155763, -0.5603416277695248, 1.6014850761964943, 0, 35.62982807460946,
                                0, 0, 0, 1, 0
							]
						}):
                        df == 'technicolor' ? new f.ColorMatrix({
							matrix: [
                                1.9125277891456083, -0.8545344976951645, -0.09155508482755585, 0, 11.793603434377337,
                                -0.3087833385928097, 1.7658908555458428, -0.10601743074722245, 0, -70.35205161461398,
                                -0.231103377548616, -0.7501899197440212, 1.847597816108189, 0, 30.950940869491138,
                                0, 0, 0, 1, 0
							]
						}):
                        df == 'brownie' ? new f.ColorMatrix({
							matrix: [
							    0.5997023498159715, 0.34553243048391263, -0.2708298674538042, 0, 47.43192855600873,
			                    -0.037703249837783157, 0.8609577587992641, 0.15059552388459913, 0, -36.96841498319127,
			                    0.24113635128153335, -0.07441037908422492, 0.44972182064877153, 0, -7.562075277591283,
                                0, 0, 0, 1, 0
							]
						}):
                        df == 'polaroid' ? new f.ColorMatrix({
							matrix: [
							    1.438, -0.062, -0.062, 0, 0,
			                    -0.122, 1.378, -0.122, 0, 0,
			                    -0.016, -0.016, 1.483, 0, 0,
                                0, 0, 0, 1, 0
							]
						}):
                        df == 'fantasy' ? new f.ColorMatrix({
							matrix: [
                                0.299651227462123, -0.21049374091738843, 0.9108425134552656, 0, 0,
                                0.26909938758978186, 0.9487574320612636, -0.21785681965104553, 0, 0,
								-0.5976969623597386, 1.1223221520157451, 0.4753748103439934, 0, 0,
								0, 0, 0, 1, 0
							]
						}):	
                        df == 'predator' ? new f.ColorMatrix({
							matrix: [
                                4.48965225219726, -1.9177947998046876, -1.1498447418212892, 0, 0.16136975288391114,
								-1.4532279014587404, 3.677262878417969, -1.1807243347167968, 0, -0.5264540195465088,
								-1.2873679161071778, -1.695001220703125, 2.9905792236328126, 0, 0.32177836894989015,
								0, 0, 0, 1, 0
							]
						}):
                        df == 'acid' ? new f.ColorMatrix({
							matrix: [
                                2, -0.4, 0.5, 0, 0,
                                -0.5, 2, -0.4, 0, 0,
                                -0.4, -0.5, 3, 0, 0,
                                0, 0, 0, 1, 0
							]
						}):							
                        df == 'night' ? new f.ColorMatrix({
							matrix: [
							    -1, -0.5, 0, 0, 0,
							    -0.5, 0, 0.5, 0, 0,
							    0, 0.5, 1, 0, 0,
							    0, 0, 0, 1, 0
							]
						}):
                        df == 'ghost' ? new f.ColorMatrix({
							matrix: [
                                0, 0.9, 0.9, 0, 0,
                                0.9, 0, 0.9, 0, 0,
                                0.9, 0.9, 0, 0, 0,
                                0, 0, 0, 1, 0,
							]
						}):
					    df == 'sharpen' ? new f.Convolute({
							matrix: [
						        0, -1, 0, -1, 5,
							    -1, 0, -1, 0
						    ]
						}):					    						
					    df == 'emboss' && new f.Convolute({
							matrix: [
						        1, 1, 1, 1, 0.7,
							    -1, -1, -1, -1
						    ]
						}),
                    fte = filter.type,
                    fmx = filter.matrix,
                    matrix = (fte == 'ColorMatrix' || fte == 'Convolute') && fmx.toString().length;
                if(df == 'none'){
					obj[0].filters = [];                    
					$(box +'-btn span').removeClass(btn);					
					$(box +'-btn [data-filter="none"]').addClass(btn);
					$(box +'-rng input').val(0);
				}
                else if(!matrix && json.indexOf(fte) == -1 || matrix && json.indexOf(fmx) == -1){			
				    fss.push(filter);
				    $(box +'-btn [data-filter="none"]').removeClass(btn);
					$(box +'-btn [data-filter="'+ df +'"]').addClass(btn);
				}
				else{ 
				    for(var i in fss){
					    if(!matrix && fss[i].type == fte || (fss[i].type == 'ColorMatrix' || fss[i].type == 'Convolute') && fss[i].matrix.toString().length == matrix){
							if(df == 'brightness' || df == 'saturate' || df == 'contrast')								
								fss[i][df] = val;															
							else
							    delete fss[i];							
						}
				    }
					$(box +'-btn [data-filter="'+ df +'"]').removeClass(btn);
				}
				setTimeout(function(){
				    obj[0].applyFilters(function(){
				        wcdp_canvas_editor.renderAll();
				        wcdp_save_canvas_state();
	                    wcdp_loading.hide();
						wcdp_overlay_loading.hide();
				    });
				}, 10);			
			}	    			
		}

        // Add Shapes
        $('#wcdp-shapes-panel .dp-box-shap').on('click', '.dp-img-contain', function(e){
			e.preventDefault();
            fabric.loadSVGFromString(this.innerHTML, function(objects, options){   
                var obj = fabric.util.groupSVGElements(objects, options),
				size = typeof wcdp_parameters.shape_size !== 'undefined' ? parseInt(wcdp_parameters.shape_size) : 80,
				props = {
					clas: 'shap',
		            fill: wcdp_style.color_shapes_editor.RGB,
			        fillCMYK: wcdp_style.color_shapes_editor.CMYK,
					stroke: wcdp_style.color_shapes_editor_outline.RGB,
					strokeCMYK: wcdp_style.color_shapes_editor_outline.CMYK,
					strokeWidth: 0
				}
				if(obj.paths){
					for(var i in obj.paths)						
						obj.paths[i].set(props);
				}
                props['top'] = 50;
                props['left'] = 50;
				obj.set(props).scaleToWidth(size).scaleToHeight(size * obj.height / obj.width);
                wcdp_canvas_editor.discardActiveGroup().add(obj).setActiveObject(obj).renderAll();
				wcdp_check_obj_center(obj);
			    wcdp_save_canvas_state();  
            });		
        });

        // Shapes Color
        wcdp_spectrum_functions.change_shap_fill_color = function(colorRGB, colorCMYK, id){
	        wcdp_apply_changes_obj({
	            'obj': 'shap',
		        'set': {fill: '#'+ colorRGB, fillCMYK: colorCMYK},
			    'paths': true,
			    'save' : false
	        });	
		}
		
        // Shapes Stroke
	    $('#wcdp-shap-stroke').on('input change', function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'shap',				
			    'set': {strokeWidth: parseInt($(this).val())},
				'paths': true,
				'save': false
		    });
			if(e.type == 'change')
                wcdp_save_canvas_state();
		});
		
        // Shapes Stroke Color
        wcdp_spectrum_functions.change_shap_fill_color_outline = function(colorRGB, colorCMYK, id){
	        wcdp_apply_changes_obj({
	            'obj': 'shap',
		        'set': {stroke: '#'+ colorRGB, strokeCMYK: colorCMYK},
			    'paths': true,
			    'save': false
		    });  
    	}
		
	    // Shapes Opacity
	    $('#wcdp-shap-opacity').on('input change', function(e){
			e.preventDefault();
	        wcdp_apply_changes_obj({
	            'obj': 'shap',
		        'set': {opacity: $(this).val()},
				'paths': true,
				'save': false
	        });
			if(e.type == 'change')
                wcdp_save_canvas_state();				
	    });
		
		// Add Cliparts
        $('#wcdp-cliparts-panel .dp-clip-content').on('click', '.dp-img-contain', function(e){
			e.preventDefault();
			var el = $(this),
			    img = el.find('img');
			if(img.hasClass('loaded'))
		        wcdp_url_verify(el.attr('data-source'), el.attr('data-name'), false, true);
        });

        // Cliparts Color
        wcdp_spectrum_functions.change_clip_fill_color = function(colorRGB, colorCMYK, id){
			$('.new-spectrum-fill-js').spectrum('destroy');
			var el = $('.wcdp-box-svg-multicolor');
			el.find('.mCSB_container').html('');
			el.parent().hide();
	        wcdp_apply_changes_obj({
	            'obj': 'clip-svg',
		        'set': {fill: '#'+ colorRGB, fillCMYK: colorCMYK},
			    'paths': true,
			    'save' : false
	        });					
		}

		// Cliparts Stroke
	    $('#wcdp-clip-svg-stroke').on('input change', function(e){
			e.preventDefault();
		    wcdp_apply_changes_obj({
			    'obj': 'clip-svg',
				'paths': true,
			    'set': {strokeWidth: parseInt($(this).val())},
				'save': false
		    });
			if(e.type == 'change')
                wcdp_save_canvas_state();
		});
		
        // Cliparts Stroke Color
        wcdp_spectrum_functions.change_clip_fill_color_outline = function(colorRGB, colorCMYK, id){
			$('.new-spectrum-stroke-js').spectrum('destroy');
			var el = $('.wcdp-box-svg-stroke-multicolor');
			el.find('.mCSB_container').html('');
			el.parent().hide();
		    wcdp_apply_changes_obj({
	            'obj': 'clip-svg',
		        'set': {stroke: '#'+ colorRGB, strokeCMYK: colorCMYK},
			    'paths': true,
			    'save' : false
		    });  
    	}
		
	    // Cliparts Opacity
	    $('#wcdp-clipart-opacity').on('input change', function(e){
			e.preventDefault();
			var items = ['clip-img', 'clip-svg'];
			for(var key in items){
		        wcdp_apply_changes_obj({
		            'obj': items[key],
			        'set': {opacity: $(this).val()},
					'paths': true,
					'save': false
		        });  
			}
			if(e.type == 'change')
                wcdp_save_canvas_state();			
	    });

        // Make QR Code
		$('#wcdp-btn-make-qr').click(function(e){
		    e.preventDefault();	
			wcdp_make_qrcode_canvas();
        });
		$('#wcdp-qr-level, #wcdp-qr-border, #wcdp-qr-range').change(function(e){
			e.preventDefault();
			wcdp_make_qrcode_canvas('change');
		});
		$('#wcdp-text-box-qr').keyup(function(e){
		    e.preventDefault();
			wcdp_make_qrcode_canvas(e.keyCode !== 13 && 'change');
		});
		function wcdp_make_qrcode_canvas(ins){
			var obj = wcdp_get_selection_obj(); 
			ins = obj.length == 1 && obj[0].clas == 'qr' ? true : ins !== 'change' ? false : null;
			if(ins == null) return false;
			
            var fg     = $('#wcdp-fg-qr-color'),
			    bg     = $('#wcdp-bg-qr-color'),
				qrText = $('#wcdp-text-box-qr').val(),
			    level  = $('#wcdp-qr-level').val(),
	            range  = parseInt($('#wcdp-qr-range').val()),
			    border = parseInt($('#wcdp-qr-border').val()),
				params = {'text': qrText, 'level': level, 'range': range, 'border': border},
	            qr     = qrcodegen.QrCode.encodeSegments(qrcodegen.QrSegment.makeSegments(qrText), qrcodegen.QrCode.Ecc[level], range),			    
	            qrSvg  = qr.toSvgString(border, fg.spectrum('get').toHexString(), bg.spectrum('get').toHexString());
				
            fabric.loadSVGFromString(qrSvg, function(objects, options){	   
                var newQR = fabric.util.groupSVGElements(objects, options),
				    size = typeof wcdp_parameters.qr_size !== 'undefined' ? parseInt(wcdp_parameters.qr_size) : 80;
              	newQR.paths[0].fillCMYK = bg.attr('cmyk');
				newQR.paths[1].fillCMYK = fg.attr('cmyk');				
				if(ins){
					obj[0].paths[0] = newQR.paths[0];
					obj[0].paths[1] = newQR.paths[1];						
                    obj[0].set({
						width: newQR.width,
						height: newQR.height,
						dataDP: params
					}).setCoords();
     			} else{
                    newQR.set({
						top: 50,
						left: 50,
						clas: 'qr',
						strokeWidth: 0,
						dataDP: params
					}).scaleToWidth(size).scaleToHeight(size * newQR.height / newQR.width);
                    wcdp_canvas_editor.discardActiveGroup().add(newQR).setActiveObject(newQR);				
				}
				wcdp_canvas_editor.renderAll();
				wcdp_check_obj_center(newQR);
			    wcdp_save_canvas_state();                				
            });	
		}

        // QR Color Foreground && Background
		wcdp_spectrum_functions.change_qrcode_fill_color = function(colorRGB, colorCMYK, id){ 
	        wcdp_apply_changes_obj({
	            'obj': 'qr',
		        'set': {fill: '#'+ colorRGB, fillCMYK: colorCMYK},
				'path': id.slice(5,-9) == 'fg' ? '1':'0',
				'save': false
	        });	
        }
		
		// Make Map
		$('#wcdp-btn-make-map').click(function(e){
		    e.preventDefault();
            wcdp_make_map_canvas();
        });
		$('#wcdp-map-type, #wcdp-map-zoom, #wcdp-map-icon-label, #wcdp-map-icon-size').change(function(e){
			e.preventDefault();
			wcdp_make_map_canvas('change');
		});
		$('#wcdp-text-box-map').keyup(function(e){
		    e.preventDefault(); 
			if(e.keyCode === 13) wcdp_make_map_canvas();
		});
		wcdp_spectrum_functions.change_map_icon_color = function(colorRGB, colorCMYK){
			wcdp_make_map_canvas('change', colorRGB, colorCMYK, false);
		}
        function wcdp_make_map_canvas(ins, colorRGB, colorCMYK, save){
			var maps_api = wcdp_settings.maps_api;
			if(maps_api){			
			    var obj = wcdp_get_selection_obj(); 
				ins = obj.length == 1 && obj[0].clas == 'maps' ? true : ins !== 'change' ? false : null;
			    if(ins == null) return false;				
			    wcdp_loading.show();
				
                var address  = $('#wcdp-text-box-map').val().replace(/\s/g,'+'),
                    maps_w   = parseInt(wcdp_parameters.maps_w),
                    maps_h   = parseInt(wcdp_parameters.maps_h),
					zoom     = $('#wcdp-map-zoom').val(),
					type     = $('#wcdp-map-type').val(),
					size     = $('#wcdp-map-icon-size').val(),
					label    = $('#wcdp-map-icon-label').val(),
					iconFill = $('#wcdp-map-icon-color'),
					fill     = typeof colorRGB === 'undefined' ? iconFill.spectrum('get').toHexString().replace('#','') : colorRGB,
					fillCMYK = typeof colorCMYK === 'undefined' ? iconFill.attr('cmyk') : colorCMYK,
                    mapsUrl  = 'https://maps.googleapis.com/maps/api/staticmap?center=';
				    mapsUrl += address +'&zoom='+ zoom +'&scale=4&size='+ maps_w +'x'+ maps_h;
                    mapsUrl += '&maptype='+ type +'&format=jpg&markers=size:'+ size;
					mapsUrl += '|color:0x'+ fill +'|label:'+ label +'|'+ address +'&key='+ maps_api +'&.jpg';
					
                var img = new Image();
			    img.crossOrigin = 'Anonymous';
                img.onload = function(){
					var params = {
						width: maps_w,
						height: maps_h,
						clas: 'maps',
						crossOrigin: 'Anonymous',
						dataDP: {
							'address' : address,
							'type'    : type,
							'zoom'    : zoom,
							'label'   : label,
							'size'    : size,
							'fill'    : fill,
							'fillCMYK': fillCMYK
						}
					};					
				    if(ins){
						obj[0].setElement(img).set(params);			
				    } else{
			            var imgMap = new fabric.Image(img, params);
                        wcdp_canvas_editor.discardActiveGroup().add(imgMap).setActiveObject(imgMap);
			            wcdp_check_obj_center(imgMap);				
				    }
				    wcdp_canvas_editor.renderAll();
                    if(save !== false) wcdp_save_canvas_state();
				    wcdp_loading.hide();					
                };
                img.src = mapsUrl;
                img.onerror = function(){
			        wcdp_jbox_msg_modal(wcdp_translations.error_process);
		        }					
	        } else{
				wcdp_jbox_msg_modal(wcdp_translations.api_maps);
			}
        }
		
	    // Encode the static map url to export in svg
        fabric.Image.prototype.getSvgSrc = function(){
			var imgSrc = this._element.src;
			if(this.clas == 'maps')
				imgSrc = imgSrc.replace(/&/g, '&amp;');
            return imgSrc;
        };	 		
		
		// Add Calendars		
		$('#wcdp-calendars-panel .dp-contain-caz').on('click', '.dp-img-contain', function(e){
			e.preventDefault();
			var el = $(this),
			    img = el.find('img');
			if(img.hasClass('loaded'))
			    wcdp_url_verify(img.attr('src'), el.attr('data-name'));
		});
		
        // Change Background Colors
        $('#wcdp-bgcolors-panel .dp-box-bgcolors span').click(function(e){
			e.preventDefault();
			var el = $(this),
			    rgb = el.css('background-color'),
				cmyk = el.attr('data-cmyk');
			wcdp_spectrum_functions.change_bg_color(wcdp_rgb_to_hex(rgb), cmyk, true);
			wcdp_auto_bleed_color(rgb);
		});
		wcdp_spectrum_functions.change_bg_color = function(colorRGB, colorCMYK, saveState){
            wcdp_canvas_editor.backgroundColor = '#' + colorRGB;
	        wcdp_canvas_editor.backgroundColorCMYK = colorCMYK;
			wcdp_canvas_editor.renderAll();
			if(saveState == true) wcdp_save_canvas_state();
			else wcdp_canvas_bg_color_checked(false);
		}
        function wcdp_canvas_bg_color_checked(updateColor){
			if($('#wcdp-bgcolors-panel').css('display') == 'block'){			
			    var getBgColor = wcdp_canvas_editor.backgroundColor,
				getBgColorCMYK = wcdp_canvas_editor.backgroundColorCMYK,
			    items = $('#wcdp-bgcolors-panel .dp-box-bgcolors span');
			    items.removeAttr('class');
				if(updateColor !== false){
					$('#wcdp-bg-color').spectrum('set', getBgColor).spectrum('setCMYK', getBgColorCMYK);
			        if(getBgColor){
			            items.each(function(){
						    var hex = '#' + wcdp_rgb_to_hex($(this).css('background-color'));
				            if(hex == getBgColor){						    
							    var tiny = tinycolor(hex);
						     	$(this).addClass('sp-thumb-active ' + (tiny.toHsl().l < 0.5 ? 'sp-thumb-dark':'sp-thumb-light'));
					        }
			            });
			        }
				}
			}
        }
		
		// Display color of the light bleed area on dark backgrounds
        function wcdp_auto_bleed_color(rgb){
			if(wcdp_style.auto_bleed_color == 'on'){
				rgb = rgb ? rgb : (wcdp_canvas_editor.backgroundColor ? wcdp_canvas_editor.backgroundColor : 0);
				if(rgb){
				    var color = rgb == 'rgba(0, 0, 0, 0)' || rgb == 'transparent' ? '#000000' : (tinycolor('#'+ wcdp_rgb_to_hex(rgb)).toHsl().l > 0.5 ? '#000000':'#ffffff');
				    if(wcdp_canvas_bleed_area){
				        var bleed = wcdp_canvas_bleed_area;
						if(bleed._objects && bleed._objects[0].stroke != color){
				            for(var b = 0; b < bleed._objects.length; b++){
				                bleed._objects[b].set('stroke', color);
			                }
						}
				    }
				    if(wcdp_canvas_grid){
				        var grid = wcdp_canvas_grid,
						    new_s = wcdp_hex_to_rgba(color, 0.2);
						if(grid._objects && grid._objects[0].stroke != new_s){
				            for(var g = 0; g < grid._objects.length; g++){
				                grid._objects[g].set('stroke', new_s);
			                }
						}
				    }
                    fabric.Object.prototype.set('borderColor', wcdp_hex_to_rgba(color, 0.75));
				    wcdp_canvas_editor.renderAll();
				}
			}
        }
		
        // Draw list of layers
		function wcdp_draw_layers(){
			if(wcdp_parameters.layers_e == 'on'){
				var contain = $('#wcdp-contain-layers'),
				    sb = contain.find('.wcdp-layers-items');
			    if($('#wcdp-layers-panel').css('display') == 'block'){
					wcdp_layers_panel = true;					
				    setTimeout(function(){
						sb.empty();
			            var html = wcdp_get_html_layers();		
                        if(!html) html = '<div class="dp-box-note dp-border-style"><p>'+ wcdp_translations.no_layers +'</p></div>';
			            sb.append(html);
						contain.show();
			        }, 100);
			    } else{
					wcdp_layers_panel = 0;
				    contain.hide();
			    }
			}
		}
        function wcdp_get_html_layers(sr){
			var html = '',						    
			    objs = wcdp_canvas_editor.getObjects(),
				tr = wcdp_translations;
				
			if(sr) sr.setCoords();
				
			for(var i = (objs.length - 1); i >= 0; i--){
			    var o = objs[i], tp = o.clas;
                if(tp != 'bleed' && tp != 'grid' && (!sr || sr && o !== sr && o.selectable && o.intersectsWithObject(sr))){
			        var ly = !sr ? true : '',
					    tx = tp == 'i-text',
				        sh = tp == 'shap',
			            qr = tp == 'qr',
				        mp = tp == 'maps',
			            im = tp == 'img' || tp == 'clip-img',
				        sv = tp == 'svg' || tp == 'clip-svg',
				        gr = tp == 'grouped' || tp == 'groupSVG',
						lt = o.get('layerThumb'),						
				        cl = tx ? (tinycolor(o.fill).toHsl().l < 0.5 ? '#fbfbfb':'#333333') : qr ? o.paths[1].fill : '',
				        bg = tx ? o.fill : qr ? o.paths[0].fill : '',
				        st = 'style="color:' + cl +';background-color:' + bg +';"',
						mt = !ly ? ' title="'+ tr.select_mask +'"' : '';
					
					if(ly){
                        var ac = o.active ? ' dp-active' : '',
                            bd = wcdp_parameters.editor == 'backend',									
				            lh = bd && o.hide ? ' dp-hide-layer' : '',
				            lu = bd && o.lockUser ? ' dp-lock-user' : '',
				            ud = !bd && o.lockUser ? ' dp_layer_dsb' : '',
				            ic = bd || !o.lockUser ? true : '',
				            lc = !o.selectable ? ' dp-locked' : '',
							lk = tr[(o.selectable ? 'lock':'unlock' ) + '_layer'];
					}
                    
					if(!o.id)
				        o.id = wcdp_random_string();
			        
					if(!o.name && !tx)
				        o.name = im ? tr.img : sv ? tr.svg : gr ? tr.group : sh ? tr.shap : qr ? tr.qr : mp && tr.map;
			
                    var name = o.name ? o.name : (tx ? (o.text ? o.text.replace(/\n/g, ' ') : tr.text) : tr.new_layer);

					html += [
					    '<div class="dp-layer-row'+ (ly && ac + lh + lu + ud) +'"'+ mt +' data-id="'+ o.id +'">',
		                    '<div class="dp-layer-thumb"'+ (ly && ' title="'+ tr.layer_options +'"') +'>',
                               (tx ? '<span class="wcdp-icon-text" '+ st +'></span>' :
 						        qr ? '<span class="wcdp-icon-qr" '+ st +'></span>' :
								mp ? '<span class="wcdp-icon-map"></span>' :
								gr ? '<span class="wcdp-icon-group"></span>' :
								o.mask ? '<span class="wcdp-icon-mask"></span>' :
					            (typeof lt !== 'undefined' ? '<img src="'+ lt +'"/>' : '')),
							'</div>',
					        '<div class="dp-layer-name"'+ (ly && ' title="'+ name +'"') +'>',
							    '<span'+ (ly && ic && ' contenteditable="true"') +'>'+ name +'</span>',
							'</div>',
							(ly &&
							'<div class="dp-layer-ins">' +
							    (lh && '<span class="wcdp-icon-hide-layer" title="'+ tr.show_layer +'"></span>') +
							    (lu && '<span class="wcdp-icon-lock-user" title="'+ tr.unlock_layer_user +'"></span>') +											
                                (ic && '<span class="wcdp-icon-sort" title="'+ tr.sort_layer +'"></span>') +
					            '<span class="wcdp-icon-lock'+ lc +'" title="'+ lk +'"></span>' +
					            (ic &&  '<span class="wcdp-icon-close" title="'+ tr.delete_layer +'"></span>') +
					        '</div>'),
						'</div>'
					].join('');			
                }
			}
			return html;
		}

		// Manage Layers
        $('#wcdp-contain-layers').on('click', '.dp-layer-row', function(e){
			e.preventDefault();
			var el = $(e.target),
            obj = wcdp_get_obj_by_id($(this).attr('data-id'));
			if(obj){
				if(el.hasClass('dp-layer-thumb') && obj.selectable && obj.clas != 'grouped')
 				    wcdp_layers_panel = 0;

				if(wcdp_parameters.editor == 'backend' || obj.evented){
				    wcdp_canvas_editor.deactivateAll().setActiveObject(obj).renderAll();
				    wcdp_check_lock_unlock_obj(obj);
				
				    if(el.hasClass('wcdp-icon-lock-user'))
				        $('#wcdp-btn-lock-user').click();
				
			        else if(el.hasClass('wcdp-icon-hide-layer'))
				        $('#wcdp-btn-out-hide').click();

			        else if(el.hasClass('wcdp-icon-lock'))
				        $('#wcdp-btn-lock').click();

			        else if(el.hasClass('wcdp-icon-close'))
			            wcdp_delete_obj();
				}
			}
		}).on('keyup', '[contenteditable]', function(e){
			e.preventDefault();
			var el  = $(this),
			    tx  = el.html(),
			    id  = el.parents('.dp-layer-row').attr('data-id'),
			    obj = wcdp_get_obj_by_id(id),
				arr = [13,16,17,18,20,37,38,39,40]; // Discard keys
			if(obj && obj.name != tx && $.inArray(e.keyCode, arr) == -1){
			    obj.name = tx;
				wcdp_save_canvas_state();
			}
        }).on('keydown', '[contenteditable]', function(e){
			return e.which != 13; // Prevent line break
		});
		$('#wcdp-contain-layers .wcdp-layers-items').sortable({
			axis: 'y',
			scroll: false,
			opacity: 0.7,			
			items: '.dp-layer-row',
			handle: '.wcdp-icon-sort',
			containment: 'parent',
			placeholder: 'dp-layer-row dp-sortable-placeholder',
            update: function(evt, ui){
				var items = $(this).children();
				setTimeout(function(){
		            var objs = wcdp_canvas_editor.getObjects();
                    items.each(function(key, item){
			            var id = $(item).attr('data-id');
		                for(var i = 0; i < objs.length; ++i){
					        var o = objs[i];
			                if(o.id && o.id == id){
					            wcdp_canvas_editor.sendToBack(o);
				                break;
			                }
		                }
                    });
                    wcdp_canvas_editor.renderAll();
                    wcdp_save_canvas_state();
				}, 10);
            }
	    });

        // Lock layer to user & Hide layer in the output files
		$('#wcdp-btn-lock-user, #wcdp-btn-out-hide').click(function(e){
		    e.preventDefault(); 
			var el = $('#wcdp-contain-layers .dp-layer-row.dp-active');
			if(el.length == 1){
				var obj = wcdp_get_obj_by_id(el.attr('data-id'));
				if(obj){
					var lc = $(this).attr('id').substring(9) == 'lock-user',
					    st = lc ? (obj.lockUser ? false : true) : (obj.hide ? false : true),
						tl = st && wcdp_translations[(lc ? 'unlock_layer_user':'show_layer')],
						cl = lc ? 'lock-user':'hide-layer';

					if(st)
						el.addClass('dp-'+ cl).find('.dp-layer-ins span:eq('+ (lc ? -3:0) +')').before('<span class="wcdp-icon-'+ cl +'" title="'+ tl +'"></span>');
					else
                        el.removeClass('dp-'+ cl).find('.wcdp-icon-'+ cl).remove();

					obj.set((lc ? 'lockUser':'hide'), st);
			        wcdp_canvas_editor.renderAll();
			        wcdp_save_canvas_state();
				}
			}
        });

        // Add layer thumbnail
		function wcdp_add_layer_thumb(obj){
    		if(wcdp_parameters.layers_e == 'on'){
				var arr = ['bleed', 'grid', 'i-text', 'qr', 'maps', 'grouped', 'groupSVG'];
				if($.inArray(obj.clas, arr) == -1 && !obj.mask){
				    var img = new Image();
		            img.onload = function(){
	    		        obj.set('layerThumb', this.src);
    		        };
		            img.src = obj.getLayerThumb();
				}
			}
		}
		
		// Select & lock & unlock layers
		function wcdp_layer_item(ins, id){
			if(ins == 'clear')
				$('#wcdp-contain-layers .dp-layer-row').removeClass('dp-active');
			else if(ins == 'select')
				$('#wcdp-contain-layers [data-id="'+ id +'"]').addClass('dp-active');
			else if(ins == 'lock')
				$('#wcdp-contain-layers [data-id="'+ id +'"] .wcdp-icon-lock').attr('title', wcdp_translations.lock_layer).removeClass('dp-locked');			
			else if(ins == 'unlock')
				$('#wcdp-contain-layers [data-id="'+ id +'"] .wcdp-icon-lock').attr('title', wcdp_translations.unlock_layer).addClass('dp-locked');
		}
	
        // Apply changes set obj
    	function wcdp_apply_changes_obj(params){
	        var obj = wcdp_get_selection_obj(),
			    objs = obj.length;
		    obj.forEach(function(obj){
			    if(obj.clas == params.obj || params.obj == 'all'){
					for(var key in params.set){
				        var i = params.set[key], arr = {},
						value = params.cmd ? new Function('obj', 'return ' +i)(obj) : obj[key] == i && params.default ? '' : i;
						if(obj.type == 'i-text' && obj.isEditing)
							obj.exitEditing();
						
						if(params.point){
							var origX = obj.textAlign;
						    if(origX !== 'left')
						        var point = obj.getPointByOrigin(origX, 'top');
						}
                        if(key == 'writeText'){
					        obj.setText(value);
				        }
						else if(key == 'spacing' && obj.type == 'i-text'){
							obj.set('charSpacing', value*25);
						}
						else if(key == 'angle'){
						    obj.setAngle(value);
						}
						else if(key == 'toggle'){
						    obj.toggle(value);
						}
						else{
							var applySet = true;
							arr[key] = value;
                            if(params.paths && obj.paths){
                                for(var p in obj.paths) obj.paths[p].set(arr);
								key == 'strokeWidth' && (arr[key] = 0);
					        } 
							else if(params.paths && obj.clas == 'groupSVG' && obj._objects){
								applySet = false;
							    for(var o in obj._objects){
									if(obj._objects[o].type != 'image' || (obj._objects[o].clas != 'maps' && key == 'opacity'))									    
									    obj._objects[o].set(arr);																		
								}							    
								if(objs == 1 && key == 'strokeWidth')
								    obj.addWithUpdate();                                						
							} 
							else if(params.path){
                                obj.paths[params.path].set(arr);
							}							
                            if(applySet)
                                obj.set(arr);
	                    }
						if(typeof point !== 'undefined')
							obj.setPositionByOrigin(point, origX, 'top');
						
						obj.setCoords();
		            }
				}			    
		    });
            wcdp_canvas_editor.renderAll();
            if(params.save != false) wcdp_save_canvas_state();			
	    }
	
        // Search Templates & Cliparts & Calendars
		$('#wcdp-btn-search-tpl, #wcdp-btn-search-clip, #wcdp-btn-search-caz').click(function(e){
		    e.preventDefault(); 
            wcdp_search_items($(this).attr('id').substring(16));
        });
		$('#wcdp-text-box-search-tpl, #wcdp-text-box-search-clip, #wcdp-text-box-search-caz').keyup(function(e){
		    e.preventDefault(); 
			if(e.keyCode === 13) wcdp_search_items($(this).attr('id').substring(21));
		});
        function wcdp_search_items(ins){
			var el = $('#dp-box-search-'+ ins),
			    sb = el.find('.mCSB_container'),
				tpl = ins == 'tpl',
				clip = ins == 'clip',
			    folder = $('.dp-folder-contain.dp-'+ ins),
			    inputText = $('#wcdp-text-box-search-'+ ins).val();
			if(tpl || folder.length > 0){
			    sb.html('');
			    $('#wcdp-'+ (tpl ? 'templates' : clip ? 'cliparts' : 'calendars') +'-panel .dp-img-contain').each(function(){
					var this_ = $(this),
                        patient = [ this_.attr('data-name') ].filter(function(e, i, a){
                        return e.search(new RegExp(inputText, 'i')) != -1;
                    });
                    if(patient.length > 0){
						var ext = clip ? ' dp-'+ (this_.hasClass('dp-svg') ? 'svg':'img') : '',
						    source = clip ? ' data-source="'+ this_.attr('data-source') +'"' : '',
						    pageID = tpl ? ' page-id="'+ this_.attr('page-id') +'"' : '',
							designID = tpl ? ' design-id="'+ this_.attr('design-id') +'"' : '',
						    imgSRC = this_.find('img').attr('data-src');							
					    sb.append('<span class="dp-img-contain'+ ext +'"'+ source +' data-name="'+ patient[0] +'"'+ pageID + designID +'><img class="lazyload dp-loading-lazy" src="'+ wcdp_loading_img +'" data-src="'+ imgSRC +'"/></span>');
				    }
			    });
			    $('.dp-'+ ins +'-content').hide();
			    folder.removeClass('dp-folder-open');
				$('.dp-contain-'+ ins).show();
			    el.show();
	            setTimeout(function(){
                    wcdp_lazy_load('#dp-box-search-'+ ins + ' img.lazyload');
                }, 100);			
			    if(el.find('span').length == 0){ 
				    sb.append('<span class="dp-search-empty" id="wcdp-btn-'+ ins +'-empty"></span><label>'+ wcdp_translations['no_search_'+ ins] +'</label>');
			    }
			}
        }
		
    	// Manage Folders
		$('#wcdp-templates-panel .dp-folder-contain').click(function(e){ 
  	        e.preventDefault();
			wcdp_manage_folders($(this), 'tpl');
		});
		if(typeof wcdp_parameters.multipleDesigns === 'undefined')
			$('#wcdp-templates-panel .dp-folder-contain').eq(0).click();		
		
		$('#wcdp-cliparts-panel .dp-folder-contain').click(function(e){ 
  	        e.preventDefault();
			wcdp_manage_folders($(this), 'clip');
		}).eq(0).click();
		$('#wcdp-calendars-panel .dp-folder-contain').click(function(e){ 
  	        e.preventDefault();
			wcdp_manage_folders($(this), 'caz');
		}).eq(0).click();

		function wcdp_manage_folders(el, ins){
            var cl = 'dp-folder-open',
                ac = 'dp-'+ ins +'-content',
				hc = el.hasClass(cl);
				$('.dp-folder-contain.dp-'+ ins).removeClass(cl);
				$('.' + ac).hide();
            if(!hc){                
			    el.addClass(cl);
                $('.' + ac +'[data-id='+ el.attr('data-id') +'], .dp-contain-'+ ins).show();
            } else{
				$('.dp-contain-'+ ins).hide();
				if(ins == 'tpl' && typeof wcdp_parameters.multipleDesigns !== 'undefined')
					$('#wcdp-tpl-general, .dp-contain-tpl').show();
			}			
		}
		
        // Tooltip
		function wcdp_jbox_tooltip(el, content){
		    return $(el).jBox('Tooltip',{
                offset: { 
			        y: parseInt(wcdp_style.tooltip_offset_y),
				    x: parseInt(wcdp_style.tooltip_offset_x)
			    },
				delayOpen: 300,
				addClass: wcdp_rtl_mode ? 'md__rtl':'',
				getContent: content ? 'data-jbox-content' : 'title'
            });
		}
		wcdp_jbox_tooltip('#wcdp-container .dp-tooltip');
		
	    // Msg alert type Modal
        function wcdp_jbox_msg_modal(msg){
            new jBox('Modal',{
			    closeButton: 'box',
                content: msg,
				addClass: wcdp_rtl_mode ? 'md__rtl':'',
                onCloseComplete: function(){
                    this.destroy();
                }
            }).open();
			wcdp_loading.hide();
		}
		
		// Msg alert type Confirm
        function wcdp_jbox_msg_confirm(content, data){
			new jBox('Confirm',{
			    content: content,
				addClass: wcdp_rtl_mode ? 'md__rtl':'',
	            cancelButton: wcdp_translations.cancel,
	            confirmButton: wcdp_translations.confirm,		
                confirm: data,
                onCloseComplete: function(){
                    this.destroy();
                }
			}).open();			
		}

		// Create mCustomScrollbar
		function wcdp_create_scrollbar(el, axis){
	        $(el).mCustomScrollbar({
				scrollbarPosition: 'outside',
				autoExpandScrollbar: true,
				autoHideScrollbar: true,
				scrollInertia: 200,
				axis: axis,
                mouseWheel: {
				    preventDefault: true,
				},
				callbacks: {
				    onScrollStart: function(){
						wcdp_popup_preview = false;
				    },
				    onScroll: function(){
						wcdp_popup_preview = true;
				    },
				    onTotalScroll:function(){
					    var this_ = $(this);
						if(this_.hasClass('dp-res-content')){
					        var res = this_.attr('id').substring(9);
				            wcdp_sources_opt.page++;
				            if(this_.find('.dp-img-res').length < wcdp_sources_opt.totals)
		                        wcdp_append_resources_thumbs(res);
						}							
				    }
				}
		    });
		}

		// Lazy load images
		function wcdp_lazy_load(el){
            var myLazyLoad = new WCDP_LazyLoad({
                elements_selector: el,
	            class_loading: 'dp-loading-lazy'
            });			
		}	

		// Load Fonts
		function wcdp_font_on_load(fonts){
		    var r = $.Deferred();
		    if(fonts.length){
                WebFont.load({
                    custom: {families: fonts},
                    active: function(){
					    r.resolve();								        
                    }
                });		
			} else{
				r.resolve();                
			}
			return r.promise();
		}		
		
		// Execute code
        $('#wcdp-code-run').click(function(e){
			e.preventDefault();
			new Function('canvas', $('#wcdp-canvas-console').val())(wcdp_canvas_editor);
			wcdp_canvas_editor.renderAll();
			wcdp_save_canvas_state();
        });		
        $('#wcdp-code-get').click(function(e){
			e.preventDefault();
	        $('#wcdp-canvas-console').val(wcdp_canvas_get_json()).select();
        });
        $('#wcdp-code-add').click(function(e){
			e.preventDefault();
			wcdp_canvas_editor.clear();
            wcdp_canvas_editor.loadFromJSON(JSON.parse($('#wcdp-canvas-console').val()),function(){
                wcdp_canvas_editor.renderAll.bind(wcdp_canvas_editor);
				wcdp_manage_overlay_layers('add');
				wcdp_save_canvas_state();
			});
        });
        $('#wcdp-code-img').click(function(e){
			e.preventDefault();
			var img = wcdp_canvas_data_url(wcdp_parameters.output_w / wcdp_canvas_editor.getWidth(), 'png', false, true),
			    label = $('.dp-canvas-active label').html() + '_design_'+ wcdp_parameters.designID + '.png',
                blob = wcdp_base64_to_blob(img, 'image/png');
			if(navigator.msSaveBlob){
                navigator.msSaveBlob(blob, label);
			} else{
				var link = document.createElement("a");
				link.href = URL.createObjectURL(blob);
                link.download = label;  
                document.body.appendChild(link);
				link.click();
			}            			
        });		
        $('#wcdp-code-clear').click(function(e){
			e.preventDefault();
	        $('#wcdp-canvas-console').val('');
        });	
		
        // Convert color RGB to HEX
        function wcdp_rgb_to_hex(el){
	        var hex,
            color = el;
            if(color.indexOf('#')>-1){
                hex = color;
            } else{
                var rgb = color.match(/\d+/g);
                hex = ('0' + parseInt(rgb[0], 10).toString(16)).slice(-2) +
		              ('0' + parseInt(rgb[1], 10).toString(16)).slice(-2) + 
			          ('0' + parseInt(rgb[2], 10).toString(16)).slice(-2); 
            } 
            return hex;
        }
		
		// Convert color HEX to RGBA
        function wcdp_hex_to_rgba(hex, alpha){
            var r = parseInt(hex.slice(1, 3), 16),
                g = parseInt(hex.slice(3, 5), 16),
                b = parseInt(hex.slice(5, 7), 16);
            return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';    
        }		

		// Text transform capitalize
        function wcdp_text_ctz(s){
            return s.toLowerCase().replace(/\b./g, function(a){
		        return a.toUpperCase();
	        });
        }
		
		// Generate uniq random
        function wcdp_random_string(){
			var string = '',
            chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';            
            for(var i = 0; i < 18; i++){
                var letterOrNumber = Math.floor(Math.random() * 2);
                if(letterOrNumber == 0){
                    var newNum = Math.floor(Math.random() * 9);
                    string += newNum;
                } else{
                    var rnum = Math.floor(Math.random() * chars.length);
                    string += chars.substring(rnum, rnum + 1);
                }
            }
            return string;
        }
    });
})(jQuery);