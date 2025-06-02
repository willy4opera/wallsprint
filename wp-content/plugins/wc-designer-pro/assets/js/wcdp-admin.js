(function($){
	'use strict';
    $(document).ready(function(){
		wcdp_admin_lazy_load();
        function wcdp_file_frame_open(this_){
   	        var file_frame,
		        el = $(this_).parent(),
		        getID = el.attr('id'),
		        format = el.attr('format'),
		        support = el.attr('support'),
				multiple = el.attr('multiple') == 'multiple' ? true : false;
            if(file_frame){
		    	file_frame.open();
				return;
			}
	        file_frame = wp.media.frames.file_frame = wp.media({ 
		        title: wcdp_translations.upload + ' "' + support + '"',
		        button: {text: wcdp_translations.file},
                library: {type: format}, 
		        multiple: multiple 
	        });
	        file_frame.on( 'select', function(e){
		        var attachment = file_frame.state().get('selection');
				attachment.map( function(attachment){
					attachment = attachment.toJSON();
			        var ext = attachment.filename.split('.').pop();			  
			        if(!(ext && new RegExp('^('+support.replace(/,/g,'|')+')$').test(ext))){
                        new jBox('Modal',{
                            content: wcdp_translations.unsupported + ' "' + support + '"',
				            closeButton: 'box',
                            onCloseComplete: function(){
                                this.destroy();
                            }
                        }).open();			    
			        } else{
						if(el.hasClass('wcdp-set-img-attr')){
							var contain = el.parent().prev();
							contain.find('img').attr('src', attachment.url);
							contain.find('input').val(attachment.url);
							contain.find('a').removeClass('dp-disabled');
						}
					    else if(getID == 'wcdp-media-editor-backend'){
							$.wcdp_upload_file_wp_enqueue_media_backend(attachment.url, attachment.title);
						}
						else if(el.find('.media-filename').length > 0){
		                    $('#'+getID+' .value-file').val(attachment.url); 
		                    $('#'+getID+' .media-filename').html(attachment.filename);
                            $('#'+getID+' .wcdp-select-file').hide();
		                    $('#'+getID+' .wcdp-remove-file').show();
					    }
						else{
							var slc = getID.slice(9,-8),
							contain = $('#wcdp-'+ slc +'s-contain'),
							ident = slc == 'font' ? 'font': slc == 'clipart' ? 'clip': slc == 'calendar' && 'caz';
						    if(contain.find('.dp-'+ ident).length == 0) contain.html('');							
							var html = '<div class="dp-'+ ident +'">';
							if(ident == 'font'){
								var font = attachment.title;
								if(!wcdp_check_font_selected(font)){									
                                    $('head').prepend('<style type="text/css">' +
								                            '@font-face{\n' +
														        '\tfont-family: '+ font +';\n' +
														        '\tsrc: url('+ attachment.url +');\n' +
														    '}\n' +
												       '</style>');						
                                    WebFont.load({
									    custom: {families: [font]}
								    });								
	                                html += '<p style="font-family:'+ font +'">'+ font +'</p>';
	                                html += '<button class="button wcdp-remove-font">'+ wcdp_translations.remove +'</button>';
								} else{
									return false;
								}
						    }
							else if(ident == 'clip' || ident == 'caz'){
								var imageURL = attachment.url;
                                if(attachment.sizes){
                                    if(attachment.sizes.thumbnail !== undefined)
										imageURL = attachment.sizes.thumbnail.url; 
                                    else if(attachment.sizes.medium !== undefined)
										imageURL = attachment.sizes.medium.url;
                                    else 
										imageURL = attachment.sizes.full.url;
                                } 
					            html +='<span class="dp-img-contain"><img class="lazyload dp-loading-lazy" data-src="'+ imageURL +'" src="'+ wcdp_lazy_loader +'"/></span>';
					            html +='<div class="wcdp-remove-'+ ident +'" title="'+ wcdp_translations.remove +'"></div>';                                   
							}
							html +='<input type="hidden" name="wcdp-uploads-'+ slc +'s[]" value="'+ attachment.id +'"></div>';
                            contain.append(html);
                            wcdp_admin_lazy_load();						
						}
			        }
	            });
		    });
	        file_frame.open();
	    }
		$('#wcdp-upload-images-backend, .wcdp-select-file.button').click(function(e){
			e.preventDefault();
			wcdp_file_frame_open(this);
		});
        $('.wcdp-remove-file.button').click(function(e){
	        e.preventDefault();
            var getID = $(this).parent().attr('id');
            $('#'+getID+' .value-file').attr('value', '');
            $('#'+getID+' .media-filename').html('');
            $('#'+getID+' .wcdp-select-file').show();
            $('#'+getID+' .wcdp-remove-file').hide();
        });		
        $('#wcdp-picker-table-update').click(function(e){
	        e.preventDefault();
            if(wcdp_settings.CMYK != 'on'){				
                new jBox('Modal',{
			        closeButton: 'box',
                    content: wcdp_translations.imagick_disabled,
                    onCloseComplete: function(){
                        this.destroy();
                    }
                }).open();
			} else{
				var prRGB = wcdp_settings.profile_rgb,
				    prCMYK = wcdp_settings.profile_cmyk;					
				prRGB = prRGB ? prRGB.substring(prRGB.lastIndexOf('/')+1) : 'sRGB-IEC61966-2.1.icc';
				prCMYK = prCMYK ? prCMYK.substring(prCMYK.lastIndexOf('/')+1) : 'ISOcoated_v2_eci.icc';
		        new jBox('Confirm',{
					id: 'jBox-UpdateConfirm',
		            content: wcdp_translations.update_content +':<br><br><b>RGB: '+ prRGB +'<br>CMYK: '+ prCMYK +'</b><br><br><p>'+ wcdp_translations.update_info +'</p>',
	                cancelButton: wcdp_translations.cancel,
	                confirmButton: wcdp_translations.confirm,		
                    confirm: function(){
			            var chunk = parseInt(wcdp_settings.chunk_colors),
			                colors = [],
			                convert = [];			    
			            wcdp_update_picker_table(0, 0, chunk, colors, convert);		
		            },
				    onCloseComplete: function(){
                        this.destroy();
                    }	
		        }).open();
			}
	    });	
		function wcdp_update_picker_table(start, prt, chunk, colors, convert){	
            var ajaxUpdate,
			    onClose = true,
				mode = 'convert',
                jBoxMsg = function(clas, msg, lock){
				    return new jBox('Modal',{
     	                id: 'jBox-UpdateTable',
			            title: '<b>'+ wcdp_translations.title_update +'</b>',
				        content: '<span class="dp-'+ clas +'">'+ msg +'</span>',
    			        closeButton: 'box',	
				        closeOnClick: lock,
                        onCloseComplete: function(){
					        if(onClose){
								end();
					            jBoxMsg('error', wcdp_translations.cancel_update, true);								
					            ajaxUpdate.abort();
     				        }
			                this.destroy();
                        }
                    }).open();	
			    },            				
			jBoxUpdate = jBoxMsg('counter', '0%', false),
            init = function(){
                for(var i = 0; i < chunk; i++){					
                    var temp = (start).toString(16);                       
	                if(temp.length < 3)                  
                        temp = '000'.substring(0, 3 - temp.length) + temp;				    
				    colors.push('#' + temp);
					start++;
				}					               
		   		if(start == 4096 + chunk){
				    colors = convert;
                    mode = 'update';					
				}					
                ajaxUpdate = $.ajax({
                    url: AJAX_URL,
                    type: 'POST',
                    data: {
                        'action': 'wcdp_update_picker_table',
                        'colors': colors.join(','),
						'mode'  : mode
					},
					success: function(response){
                        try{
                            var parsedData = JSON.parse(response);
							convert = convert.concat(parsedData);
							colors = [];
					        prt = (new Number(prt) + 100 / (4096 / chunk));
							jBoxUpdate.setContent('<span class="dp-counter">'+ prt.toFixed() +'%</span>');
					        setTimeout(function(){
                                init();
     					    }, 100);							
							
                        } catch(e){ 
                            end();
					        if(response == 'update_successful')
							    jBoxMsg('update', wcdp_translations.update, true);						
						    else if(response == 'error/profile')
							    jBoxMsg('error', wcdp_translations.error_icc, true);											
						    else if(response == 'error/chunk')
							    jBoxMsg('error', wcdp_translations.error_chunk, true);								
                        }
					},
                    statusCode: {
                        500: function(){
						    end();
						    jBoxMsg('error', wcdp_translations.error_icc, true);
                        }
					}					
			    });							
            },
            end = function(){		
				onClose = false;
				jBoxUpdate.close();
			}			
            init();		
		}		
        $('#wcdp-restore-all-defaults').click(function(e){
	        e.preventDefault();
            wcdp_jbox_confirm('restore-defaults');	
	    }); 
        if($('table.form-table').find('.dp-shortcutkeys').length > 0){
	        $('.dp-shortcutkeys').each(function(){
			    var getValue = $(this).next().val().split('+');
			    if(getValue.length == 1){
	                $(this).val(wcdp_translations.shortcuts.keyCodes[getValue[0]]);
			    } else{
			        $(this).val(getValue[0]+ '+' +wcdp_translations.shortcuts.keyCodes[getValue[1]]);
			    }
            });
            $('.dp-shortcutkeys').keydown(function(e){
                var getKey = '', 
				    keyDuplicate = false,
			        specialKeys = (e.keyCode < 16 || e.keyCode > 18);
                if((e.shiftKey || e.altKey || e.ctrlKey) && specialKeys){ 
                    getKey = (e.shiftKey ? 'shift':'')+(e.altKey ? 'alt':'')+(e.ctrlKey ? 'ctrl':'') +'+';
                }
                if(!e.metaKey){
                    e.preventDefault();
                }
			    var strKey = getKey + wcdp_translations.shortcuts.keyCodes[e.keyCode];
			    if($(this).val() != strKey){			  
			        $('.dp-shortcutkeys').each(function(){
			            if(getKey+e.keyCode == $(this).next().val()){
				            $('.jBox-Modal').remove();
 			                keyDuplicate = true;
                            new jBox('Modal',{
                                content: '<b>'+strKey.toUpperCase()+'</b> '+wcdp_translations.shortcuts.key_duplicate,
			    	            overlay: false,
			                    autoClose: 2000,                       
                                onCloseComplete: function(){
                                    this.destroy();
                                }
                            }).open();
			            }
			        });
			    }			  
			    if(specialKeys && !keyDuplicate){
                    $(this).val(strKey).next().val(getKey+e.keyCode);
			    }
            });	
		} else if($('table.form-table').find('.wcdp-colors-options').length > 0){
		    $.spectrum.installPicker('spectrum-js');
            $('#wcdp_option_skin_style').change(function(e){
			    e.preventDefault();
				var ins = [
				    'color_icons', 'color_icons_hover', 'bg_icons', 'bg_icons_hover', 'buttons_color', 'buttons_color_hover',
					'buttons_bg', 'buttons_bg_hover', 'buttons_color_jbox', 'buttons_color_hover_jbox', 'buttons_bg_jbox',
					'buttons_bg_hover_jbox', 'buttons_color_folders', 'buttons_color_folders_select', 'buttons_color_folders_bg',
					'buttons_color_folders_bg_select', 'text_color', 'tabs_bg', 'tabs_content', 'tooltip_color', 'tooltip_bg',
					'scrollbar_bg', 'border_color', 'picker_color_bg', 'picker_color_border', 'picker_color_text', 'corner_color',
					'corner_border_color', 'corner_icons_color'
				],
				skins = {
					'default'     : ['36495e','fff','fff','36495e','36495e','fff','fff','36495e','fff','fff','36495e','bcd800','36495e','36495e','fff','ececec','36495e','fbfbfb','fff','fff','36495e','fff','b6babd','f2f2f2','b6babd','292929','36495e','fff','fff'],
					'gray-blue'   : ['eee','eee','272c32','00bcd4','eee','eee','272c32','00bcd4','eee','eee','3f4551','00bcd4','eee','eee','3f4551','272c32','eee','3f4551','323844','eee','00bcd4','00bcd4','5a626d','3f4551','323844','eee','272c32','fff','eee'],
					'green-coral' : ['eee','eee','063f46','fd7350','eee','eee','063f46','fd7350','eee','eee','065059','fd7350','eee','eee','065059','063f46','eee','065059','06464f','eee','fd7350','fd7350','626d5a','065059','003f47','eee','063f46','fff','eee'],
					'blue-orange' : ['eee','eee','36495e','f27e00','eee','eee','36495e','f27e00','eee','eee','36495e','f27e00','eee','eee','446084','36495e','eee','446084','36495e','eee','f27e00','f27e00','738294','446084','36495e','eee','36495e','fff','eee'],
					'violet-blue' : ['eee','eee','142443','7e55a7','eee','eee','142443','7e55a7','eee','eee','142443','7e55a7','eee','eee','4d4b9a','142443','eee','4d4b9a','142443','eee','7e55a7','7e55a7','738294','4d4b9a','142443','eee','142443','fff','eee'],
					'black-red'   : ['eee','eee','1e1e1e','bc1e1e','eee','eee','1e1e1e','bc1e1e','eee','eee','333','bc1e1e','eee','eee','333','1e1e1e','eee','333','484848','eee','bc1e1e','bc1e1e','5a626d','333','484848','eee','1e1e1e','fff','eee'],
				};
				for(var i = 0; i < ins.length; i++){
					$('[name="wcdp-settings-style['+ ins[i] +'][RGB]"]').spectrum('set', '#' + skins[this.value][i]).spectrum('setCMYK', '0,0,0,0');
				}
            });
            $('.wcdp-add-color-palette').click(function(e){
			    e.preventDefault();
			    var el = 'sp_new_color',
    		    html ='<div class="color">';
		        html +='<input type="text" class="new-spectrum-js '+el+'" name="wcdp-settings-style['+ $(this).attr('data') +'][RGB][]" cmyk="0,0,0,0" value="#ffffff">'; 
			    html +='<input type="hidden" name="wcdp-settings-style['+ $(this).attr('data') +'][CMYK][]" value="0,0,0,0">';
		        html +='<button class="button wcdp-remove-color">'+wcdp_translations.remove+'</button>';
		        html +='</div>';			   
			    $(this).prev().append(html);
			    $.spectrum.installPicker(el);
			    $('.'+el).removeClass(el);
            });
		    $('.wcdp-colors-palette').on('click', '.wcdp-remove-color', function(e){
			    e.preventDefault();
			    $(this).parent().remove();
            });            	   
		} else if($('#wcdp-personalize-product, #wcdp-parameters-box').length > 0){
	        $('#wcdp_select_design_id, #wcdp_select_multiple_designs, #_wcdp_design_cat_cliparts, #_wcdp_design_cat_calendars').select2({  		       
				ajax:{
    			    url: AJAX_URL,
    			    dataType: 'json',
    			    delay: 250,
    			    data: function(params){
      				    return{
        				    q: params.term,
							type: $(this).attr('post-id'),
        				    action: 'wcdp_search_post_ajax'
      				    };
    			    },
    			    processResults: function(data){
				        var options = [];
				        if(data){
					        $.each(data, function(index, text){
						        options.push({id: text[0], text: text[1]});
					        }); 
				        }
				        return{
					        results: options
				        };
			        },
			        cache: true
		        },
				placeholder: '',
				allowClear: true,
		        minimumInputLength: 3
	        });
			// Expand & close all attribute items
			$('#wcdp_tab_product_data').on('click', '.wcdp-expand-all', function(){
				$(this).parents('.wc-metaboxes-wrapper').find('.wcdp-attr-item').removeClass('closed').find('.wcdp-item-contain').show();
				return false;
	        }).on('click', '.wcdp-close-all', function(){
		        $(this).parents('.wc-metaboxes-wrapper').find('.wcdp-attr-item').addClass('closed').find('.wcdp-item-contain').hide();
				return false;
			});
			// Open & close attribute item
            $('#wcdp-contain-attributes').on('click', 'h3', function(e){
	            $(this).parent().toggleClass('closed').find('.wcdp-item-contain').slideToggle('fast');
	        })
			// Select action table of attribute values
			.on('change', '.wcdp-attr-value', function(e){
				var this_ = $(this),
					item = this_.parents('.wcdp-item-contain');
				item.find('table').hide();
				item.find('table[data-attr="'+ this_.val() +'"]').show();
            })
			// Validate & append product color attribute value
			.on('change', '.wcdp-attr-content input[type="color"]', function(e){
				var this_ = $(this);
			    this_.parent().prev().find('input').val(this_.val());
            })
			.on('keyup', '.wcdp-attr-content input.dp-bg-color', function(e){
				var this_ = $(this),
				    value = /^#[0-9A-F]{6}$/i.test(this_.val()) ? this_.val() : '#000001';
				this_.parent().next().find('input').val(value);
            })
			// Set image attribute value
		    .on('click', '.wcdp-attr-content .wcdp-set-img-attr input', function(e){
			    wcdp_file_frame_open(this);
		    })
			// Remove image attribute value
			.on('click', '.wcdp-attr-content a.wcdp-remove-img-action', function(e){
				var this_ = $(this),
				    contain = this_.parent();
				contain.find('img').attr('src', wcdp_placeholder_img);
				contain.find('input').val('');
				this_.addClass('dp-disabled');
				return false;
            })
			// Set sides attribute value
			.on('change', '.wcdp-attr-content select.wcdp-set-sides-pr', function(e){
				var this_ = $(this);
			    this_.next().val(this_.val());
            });
			// Save attribute actions
	        $('#wcdp_save_actions').click(function(e){
				var contain = $('#wcdp_tab_product_data'), attr_actions = {};
				$('#wcdp-contain-attributes .wcdp-attr-item').each(function(){
					var this_   = $(this),
					    name    = this_.find('.wcdp-attr-name').val(),
					    slug    = this_.find('.wcdp-attr-name').attr('data-slug'),
					    layout  = this_.find('.wcdp-layout-type option:selected').val(),
						set_img = this_.find('.wcdp-set-img-pr option:selected').val(),
					    values  = this_.find('.wcdp-attr-value option'),
                        actions = {};
                    for(var i = 0; i < values.length; i++){
                        var attr_val = $(values[i]).val(),
						    attr_act = $('table[data-attr="'+ attr_val +'"]').find('.dp-row');
						actions[attr_val] = {};
						for(var j = 0; j < attr_act.length; j++){
							var contain = $(attr_act[j]),
							    action  = contain.attr('data-action'),
							    active  = contain.find('.dp-col1 input').is(':checked'),
							    value   = contain.find('.dp-col3 input').val();
							actions[attr_val][action] = {'active': (active ? 'on': ''), 'value': value};
						}
                    }
					attr_actions[slug] = {
						'name': name,
						'layout': layout,
						'set_img': set_img,
						'actions': actions
					};
				});
				if(Object.keys(attr_actions).length > 0)
				    wcdp_manage_attribute_actions('save', attr_actions);
            });
			// Empty all attribute actions
			$('#wcdp_empty_actions').click(function(e){
				var contain = $('#wcdp-contain-attributes .wcdp-item-contain'),
				    elems = contain.find('.wcdp-attr-content table');
				contain.find('.dp-col select option:selected').attr('selected', false);
                contain.find('.dp-col select option:first').attr('selected', 'selected');
				elems.find('.dp-col1 input').attr('checked', false);
				elems.find('.dp-col3 input').val('');
				elems.find('.dp-col3 img').attr('src', wcdp_placeholder_img);
				elems.find('.dp-col3 a').addClass('dp-disabled');
				elems.find('.dp-col3 select option:selected').attr('selected', false);
				elems.find('.dp-col3 select option:first').attr('selected', 'selected');
				elems.find('.dp-col4 input[type="color"]').val('#000001');
				elems.removeAttr('style');
			});
			// Refresh attribute actions
			$('#wcdp_refresh_attributes').click(function(e){
				wcdp_manage_attribute_actions('refresh', false);
			});
			// Save & refresh attribute actions by ajax query
			function wcdp_manage_attribute_actions(opt, actions){
				$('#wcdp_tab_product_data').addClass('ld__actv');
                $.ajax({
                    url: AJAX_URL,
                    type: 'POST',
                    data: {
                        'action': 'wcdp_manage_attribute_actions_ajax',
	    				'option': opt,
		                'product_id': $('#wcdp_product_id').val(),
					    'attr_actions': actions
    		        },						
			        success: function(response){
					    response = JSON.parse(response);
                        if(response.success){
						    if(opt == 'refresh' && response.actions)
							    $('#wcdp-contain-attributes').empty().append(response.actions);
			            }
						else{
  						    alert(wcdp_translations.error_process);	
						}
			    	    $('#wcdp_tab_product_data').removeClass('ld__actv');
				    }
                });					
			}
			$('#wcdp-personalize-product input.wcdp-settings-tpl').change(function(e){
				$('.wcdp-settings-tpl').not(this).attr('checked', false);
			});
		} else if($('#wcdp-fonts-contain').length > 0){
            $('#wcdp-select-web-fonts').select2();	
		    $('#wcdp-add-web-font').click(function(e){
		        e.preventDefault();		
			    var el = $('#wcdp-select-web-fonts'),
			        font = el.find('option:selected').text();
			    if(!wcdp_check_font_selected(font)){
                    WebFont.load({
                        google: {families: [font]},
                        active: function(){
			                var html  = '<div class="dp-font">';
			                    html += '<p style="font-family:'+ font +'">'+ font +'</p>';
	                            html += '<button class="button wcdp-remove-font">'+ wcdp_translations.remove +'</button>';
			                    html += '<input type="hidden" value="'+ el.val() +'">';
			                    html += '</div>';
			                $('#wcdp-fonts-contain').append(html);						        
                        }
                    });			
			    }
                if($('#wcdp-fonts-contain .dp-font').length == 0)
					$('#wcdp-fonts-contain').html('');					
		    });			
		    $('#wcdp-fonts-contain').on( 'click', '.wcdp-remove-font', function(e){ 
		        e.preventDefault();
		        wcdp_jbox_confirm($(this).parent());
            });
		    $('#wcdp-update-fonts').click(function(e){
		        e.preventDefault();
			    var fonts = [];
			    $('#wcdp-fonts-contain .dp-font input').each( function(){
				    fonts.push($(this).val());
			    });
			    wcdp_jbox_modal_update({'action': 'wcdp_update_manage_fonts_options', 'fonts': fonts});
		    });
		}
		$('#wcdp-update-shapes').click(function(e){
		    e.preventDefault();
			var shapes = [];
			$('#wcdp-shapes-contain .dp-shap input').each( function(){
				shapes.push($(this).is(":checked") ? 'on':'off');				
			});
			wcdp_jbox_modal_update({'action': 'wcdp_update_manage_shapes_options', 'shapes': shapes});
		});
		$('#wcdp-update-filters').click(function(e){
		    e.preventDefault();
			var filters = {};			
			$('#wcdp-filters-contain .dp-filter input').each( function(){
				var _this = $(this),
				    fname = _this.attr('data-filter');
				filters[fname] = _this.is(":checked") ? 'on':'off';
			});
			wcdp_jbox_modal_update({'action': 'wcdp_update_manage_filters_options', 'filters': filters});
		});
        $('#wcdp-cliparts-contain').on( 'click', '.wcdp-remove-clip', function(e){ 
	        e.preventDefault();
            wcdp_jbox_confirm($(this).parent());
        });
        $('#wcdp-calendars-contain').on( 'click', '.wcdp-remove-caz', function(e){ 
	        e.preventDefault();
            wcdp_jbox_confirm($(this).parent());
        });
        $('#wcdp-designs-box-editor').parents('form').keypress(function(e){
            if($('input').is(":focus") && e.keyCode == 13) e.preventDefault();
        });
        $('#wcdp-doc-sidebar > ul > li').click(function(e){
		    e.preventDefault();
		    if($(this).length !== 0 && !$(this).hasClass('dp-selected')){		    
		        $('#wcdp-doc-sidebar > ul > li').removeClass('dp-selected');
		        $(this).addClass('dp-selected');
		        $('#wcdp-doc-content > .dp-doc-section').hide().eq($(this).index()).fadeIn();
		    }			
        });
		// Demos Installer
		$('#wcdp-install-all-demos, #wcdp-demos-contain .dp-demo-btn').click(function(e){
			e.preventDefault();
			var trl = wcdp_translations,
                demoSlug = $(this).attr('data-value'),
			    demos = wcdp_data_demos['demos'],
				pages = wcdp_data_demos['pages'],
			    demoName = demoSlug == 'alldemos' ? trl.all_demos : demos[demoSlug],
			    htmlConfirm = '<h4>'+ trl.demo_select +': '+ demoName +'</h4><p>'+ trl.demo_page +':</p><select id="wcdp-page-demo">';
			for(var p = 0; p < pages.length; p++){
				htmlConfirm += '<option value="'+ pages[p]['id'] +'">'+ pages[p]['title'] +'</option>';
			}
			htmlConfirm += '</select>';
		    new jBox('Confirm',{
		        content: htmlConfirm,
	            cancelButton: trl.cancel,
	            confirmButton: trl.install,
                confirm: function(){
                    var jBoxMsg = function(msg, lock){
				        return new jBox('Modal',{
				            id: 'wcdp-jbox-intall-demo',
							content: msg,
				            closeOnEsc: lock,
				            closeOnClick: lock,
					    	closeButton: lock ? 'box' : false,
				            onCloseComplete: function(){
                                this.destroy();
                            }
			            }).open();	
			        },
					pageID = $('#wcdp-page-demo option:selected').val();
			        if(pageID !== undefined){
						var jBoxDemo = jBoxMsg('', false),
						arrDemos = demoSlug == 'alldemos' ? Object.keys(demos) : [demoSlug],
						loader = '<div class="install-demo-spinner"></div>',
			            init = function(i, response){
						    if(arrDemos.length == i){
								jBoxDemo.close();
								jBoxMsg(response, true);
						    } else{
                                var demo = arrDemos[i]; i++;
						        jBoxDemo.setContent(i+'/'+ arrDemos.length +' '+ trl.demo_install +': <b>'+ demos[demo] +'</b> ' + trl.wait + loader);
                                $.ajax({
                                    url: AJAX_URL,
                                    type: 'POST',
                                    data: {
                                        'action': 'wcdp_install_product_demos',
						                'pageID': pageID,
						                'demo': demo
     			                    },						
				                    success: function(response){
                                        response = JSON.parse(response);
                                        if(response.err){
										    jBoxDemo.close();
								            jBoxMsg(response.err, true);
						                } else if(response.success){
										    init(i, response.success);
				                        } else{
										    jBoxDemo.close();
								            jBoxMsg(trl.error_process, true);
						                }
                        	        }
                                });
							}
			            }
                        init(0, false);
				    } else{
						jBoxMsg(trl.none_demo_page, true);
				    }
		        },
                onCloseComplete: function(){
                    this.destroy();
                }				
		    }).open();
		});
		function wcdp_check_font_selected(font){
			var checkFont = 0;
			$('#wcdp-fonts-contain .dp-font p').each( function(){
				if(font == $(this).html()){
					checkFont = true;
                    new jBox('Modal',{
			            closeButton: 'box',
                        content: wcdp_translations.font_selected,
                        onCloseComplete: function(){
                            this.destroy();
                        }
                    }).open();                    				
				}
			});	
            return checkFont;			
		}	
		function wcdp_admin_lazy_load(){
            var myLazyLoad = new WCDP_LazyLoad({
                elements_selector: '#wcdp-calendars-contain img.lazyload, #wcdp-cliparts-contain img.lazyload',
	            class_loading: 'dp-loading-lazy'
            });			
		}	
		function wcdp_jbox_modal_update(data){
            new jBox('Modal',{
                ajax: {
                    url: AJAX_URL,
					type: 'POST',
			        data : data,
                    reload: 'strict',
			        success: function(response){
			            if(response == 'update_successful'){
			                this.setContent(wcdp_translations.update);
				            location.reload(true);					 
			            } else{
				            this.setContent(wcdp_translations.error_process);
			            }					
			        }
			    }
            }).open();		
		}
		function wcdp_jbox_confirm(ins){
		    new jBox('Confirm',{
		        content: wcdp_translations.confirm_question,
	            cancelButton: wcdp_translations.cancel,
	            confirmButton: wcdp_translations.confirm,		
                confirm: function(){
					if(ins == 'restore-defaults'){
                        new jBox('Modal',{
                            ajax: {
                                url: AJAX_URL,
								type: 'POST',
					            data : { 'action': 'wcdp_restore_defaults_all_settings' },
                                reload: 'strict',
					            success: function(response){
					                if(response == 'restore_successful'){
					                    this.setContent(wcdp_translations.success);
						                location.reload(true);					 
					                } else{
						                this.setContent(wcdp_translations.error_reset);
					                }					
					            }
				            }
                        }).open();						
					} else{
					    var el = ins.attr('class'), ident, none;
					    ins.remove();
					    if(el == 'dp-font'){
                            ident = 'fonts';
                            none = wcdp_translations.no_fonts;
					    } else if(el == 'dp-clip'){
                            ident = 'cliparts';
                            none = wcdp_translations.no_cliparts;                        
					    } else if(el == 'dp-caz'){
                            ident = 'calendars';
                            none = wcdp_translations.no_caz;                  
					    }
                        var contain = $('#wcdp-'+ ident +'-contain');		
			            if(contain.find('.' + el).length == 0){ 
			                var html ='<div id="wcdp-contain-search-empty">';
			                html +='<div class="wcdp-upload-cloud"></div>';
			                html +='<label>'+ none +'</label>';
                            html +='<input type="hidden" name="wcdp-uploads-'+ ident +'">'			
			                html +='</div>';
			                contain.append(html);	
			            }   
					}					
		        },
                onCloseComplete: function(){
                    this.destroy();
                }				
		    }).open();		
		}		
    }); 
})(jQuery);