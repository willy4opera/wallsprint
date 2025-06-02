<?php

	/**
	* @Description : Plugin main core
	* @Package : Drag & Drop Multiple File Upload - Contact Form 7
	* @Author : Glen Don L. Mongaya
	*/

	if ( ! defined( 'ABSPATH' ) || ! defined('dnd_upload_cf7') ) {
		exit;
	}

	/**
	* Begin : begin plugin hooks
	*/

	add_action( 'wpcf7_init', 'dnd_cf7_upload_add_form_tag_file' );
	add_action( 'wpcf7_enqueue_scripts', 'dnd_cf7_scripts' );

	// Hook on plugins loaded
	add_action('plugins_loaded','dnd_cf7_upload_plugins_loaded');

	// Ajax Upload
	add_action( 'wp_ajax_dnd_codedropz_upload', 'dnd_upload_cf7_upload' );
	add_action( 'wp_ajax_nopriv_dnd_codedropz_upload', 'dnd_upload_cf7_upload' );

	// Hook - Ajax Delete
	add_action('wp_ajax_nopriv_dnd_codedropz_upload_delete', 'dnd_codedropz_upload_delete');
	add_action('wp_ajax_dnd_codedropz_upload_delete','dnd_codedropz_upload_delete');

    // Ajax Nonce check
    add_action('wp_ajax__wpcf7_check_nonce', 'dnd_wpcf7_nonce_check');
    add_action('wp_ajax_nopriv__wpcf7_check_nonce', 'dnd_wpcf7_nonce_check');

	// Hook mail cf7
	add_filter('wpcf7_posted_data', 'dnd_wpcf7_posted_data', 10, 1);
	add_action('wpcf7_before_send_mail','dnd_cf7_before_send_mail', 30, 1);
	add_action('wpcf7_mail_components','dnd_cf7_mail_components', 50, 2);

	// Auto clean up dir/files
	add_action('shutdown', 'dnd_cf7_auto_clean_dir', 20, 1 );

	// Add row meta links
	add_filter( 'plugin_row_meta', 'dnd_custom_plugin_row_meta', 10, 2 );

	// Add custom mime-type
	add_filter('upload_mimes', 'dnd_extra_mime_types', 1, 1);

    // Plugin settings
    add_filter( 'plugin_action_links_' . plugin_basename( dnd_upload_cf7_directory ) .'/drag-n-drop-upload-cf7.php', 'dnd_cf7_upload_links' );

	// Add Submenu - Settings
	add_action('admin_menu', 'dnd_admin_settings');

	// Add custom script in footer
	add_action('wp_footer','dnd_custom_scripts');

	// Flamingo Hooks
	add_action('before_delete_post', 'dnd_remove_uploaded_files');

	// Generate uqique id/random
	add_action( 'wp_footer', 'dnd_cf7_generate_cookie' );

    // Nonce
    function dnd_wpcf7_nonce_check() {
		// Block curl request.
		if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'curl' ) !== false ) {
			wp_send_json_error('Request blocked: cURL access is forbidden.');
		}

        if( ! check_ajax_referer( 'dnd-cf7-security-nonce', false, false ) ){
            wp_send_json_success( wp_create_nonce( "dnd-cf7-security-nonce" ) );
        }
    }

	// Return created cookie with unique id.
	function dnd_cf7_get_unique_id() {
		if ( isset( $_COOKIE['wpcf7_guest_user_id'] ) ) {
			return $_COOKIE['wpcf7_guest_user_id'];
		}
	}

    // Add links to settings
    function dnd_cf7_upload_links( $actions ) {
        $upload_links = array('<a href="' . admin_url( 'admin.php?page=drag-n-drop-upload' ) . '">' . esc_html__( 'Settings', 'drag-and-drop-multiple-file-upload-contact-form-7' ) . '</a>' );
        $actions = array_merge( $upload_links, $actions );
        return $actions;
    }

	// Load plugin text-domain
	function dnd_cf7_upload_plugins_loaded() {

		// Load language domain
		load_plugin_textdomain( 'drag-and-drop-multiple-file-upload-contact-form-7', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' );

		// Create dir
		$dir = dnd_get_upload_dir();
		if( isset( $dir['upload_dir'] ) && is_dir( $dir['upload_dir'] ) ) {
			// Generate .htaccess & index.php file`
			$htaccess_file = path_join( dirname( $dir['upload_dir'] ), '.htaccess' );
			$dirs = array(
				dirname( $dir['upload_dir'] ),
				$dir['upload_dir']
			);

			if ( ! file_exists( $htaccess_file ) ) {
				if ( $handle = fopen( $htaccess_file, 'w' ) ) {
					fwrite( $handle, "Options -Indexes \n <Files *.php> \n deny from all \n </Files>" );
					fclose( $handle );
				}
			}

			foreach( $dirs as $dir ) {
				if ( ! file_exists( path_join( $dir, 'index.php' ) ) ) {
					if ( $handle = fopen( path_join( $dir, 'index.php' ), 'w' ) ) {
						fwrite( $handle, "<?php // Silence is golden." );
						fclose( $handle );
					}
				}
			}

		}

        // fix spam
        if( dnd_cf7_settings('drag_n_drop_fix_spam') == 'yes' ) {
            add_filter('wpcf7_spam', '__return_false');
        }
	}

	// Remove uploaded files when item is deleted permanently.
	function dnd_remove_uploaded_files( $post_id ) {
		// no post id bail early.
		if ( ! $post_id ) {
			return;
		}

		// Get the post type and post
		$post_type = get_post_type( $post_id );
		$page      = get_post( $post_id );

		if ( $post_type == 'flamingo_inbound' ) {
			preg_match_all( '/https?:\/\/[^\'"\s]+\/'.preg_quote(wpcf7_dnd_dir, '/').'\/wpcf7-files\/[^\'"\s]+/i', $page->post_content, $matches );

			if ( $matches[0] && count( $matches[0] ) > 0 ) {
				foreach ( $matches[0] as $file_url ) {

					// Convert url to dir
					$file = str_replace( site_url() . '/', wp_normalize_path( ABSPATH ), $file_url );

					// Only allowed local paths. (skip: phar://, file://)
					if ( parse_url( $file, PHP_URL_SCHEME ) ) {
						continue;
					}

					// Check if it's a regular file.
					if ( ! file_exists( $file ) || ! is_file( $file ) ) {
						continue;
					}

					// Extract and sanitize the filename
					$file_path = dirname( $file ) . '/' . sanitize_file_name( wp_basename( $file ) );

					// Prevent traversal attack
					$real_path   = realpath( $file_path );
					$wp_dir      = wp_get_upload_dir(); //WordPress dir
					$uploads_dir = wp_normalize_path( realpath( $wp_dir['basedir'] ). '/'. wpcf7_dnd_dir );

					// Check if the file exists and is within the uploads directory
					if ( $real_path && file_exists( $real_path ) && strpos( $real_path, $uploads_dir ) === 0 ) {
						wp_delete_file( $real_path );
					}

				}
			}
		}
	}

	// Modify contact form posted_data
	function dnd_wpcf7_posted_data( $posted_data ){

		// Subbmisson instance from CF7
		$submission = WPCF7_Submission::get_instance();

		// Make sure we have the data
		if ( ! $posted_data ) {
            $posted_data = $submission->get_posted_data();
        }

		// Scan and get all form tags from cf7 generator
		$forms_tags  = $submission->get_contact_form();
		$uploads_dir = dnd_get_upload_dir();

		// Send as link?
		$send_link = ( dnd_cf7_settings('drag_n_drop_mail_attachment') == 'yes' ? true : false );

		if( $forms = $forms_tags->scan_form_tags() ) {
			foreach( $forms as $field ) {
				$field_name = $field->name;
				if( $field->basetype == 'mfile' && isset( $posted_data[$field_name] ) && ! empty( $posted_data[$field_name] ) ) {
					if ( is_array( $posted_data ) ) {
						foreach( $posted_data[$field_name] as $key => $file ) {
							if ( $send_link || strpos( dirname($file), 'wpcf7-files' ) !== false ) {
								$file = wp_basename( $file ); // remove duplicate path "/12/file.jpg" to just "/file.jpg"
							}
							$posted_data[$field_name][$key] = trailingslashit( $uploads_dir['upload_url'] ) . $file;
						}
					}
				}
			}
		}

		return $posted_data;
	}

	// Hooks for admin settings
	function dnd_admin_settings() {
		add_submenu_page( 'wpcf7', __( 'Drag & Drop Uploader - Settings', 'drag-and-drop-multiple-file-upload-contact-form-7' ), __( 'Drag & Drop Upload', 'drag-and-drop-multiple-file-upload-contact-form-7' ), 'manage_options', 'drag-n-drop-upload','dnd_upload_admin_settings');
		add_action('admin_init','dnd_upload_register_settings');
	}

	// Add custom mime-types
	function dnd_extra_mime_types( $mime_types ){
		$mime_types['xls'] = 'application/excel, application/vnd.ms-excel, application/x-excel, application/x-msexcel';
		$mime_types['xlsx'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
		return $mime_types;
	}

	// Default Error Message
	function dnd_cf7_error_msg( $error_key ) {

		// Array of default error message
		$errors = array(
			'server_limit'		=>	__('The uploaded file exceeds the maximum upload size of your server.','drag-and-drop-multiple-file-upload-contact-form-7'),
			'failed_upload'		=>	__('Uploading a file fails for any reason','drag-and-drop-multiple-file-upload-contact-form-7'),
			'large_file'		=>	__('Uploaded file is too large','drag-and-drop-multiple-file-upload-contact-form-7'),
			'invalid_type'		=>	__('Uploaded file is not allowed for file type','drag-and-drop-multiple-file-upload-contact-form-7'),
			'max_file_limit'	=>	__('Note : Some of the files are not uploaded ( Only %count% files allowed )','drag-and-drop-multiple-file-upload-contact-form-7'),
			'required'			=>	__('This field is required.', 'drag-and-drop-multiple-file-upload-contact-form-7' ),
			'min_file'			=>	__('The minimum file upload is','drag-and-drop-multiple-file-upload-contact-form-7'),
		);

		// return error message based on $error_key request
		if( isset( $errors[ $error_key ] ) ) {
			return $errors[ $error_key ];
		}

		return false;
	}

	// Get folder path
	function dnd_get_upload_dir( $dir = false ) {
		$upload        = wp_upload_dir();
		$uploads_dir   = wpcf7_dnd_dir . '/wpcf7-files';

		// Add random folder.
		if ( true === $dir ) {
			$random_folder = dnd_cf7_get_unique_id();
			if ( $random_folder ) {
				$uploads_dir   = $uploads_dir .'/'. $random_folder;
			}
		}

		// If save as attachment ( also : Check if upload use year and month folders )
		if( dnd_cf7_settings('drag_n_drop_mail_attachment') == 'yes' ) {
			$uploads_dir = ( get_option('uploads_use_yearmonth_folders') ? wpcf7_dnd_dir . $upload['subdir'] : wpcf7_dnd_dir );
		}

		// Create directory
		if ( ! is_dir( trailingslashit( $upload['basedir'] ) . $uploads_dir ) ) {
			wp_mkdir_p( trailingslashit( $upload['basedir'] ) . $uploads_dir );
            chmod( trailingslashit( $upload['basedir'] ) . $uploads_dir, 0755 );
		}

		// Make sure directory exist before returning
		if( file_exists( trailingslashit( $upload['basedir'] ) . $uploads_dir ) ) {
			return array(
				'upload_dir'	=>	trailingslashit( $upload['basedir'] ) . $uploads_dir,
				'upload_url'	=>	trailingslashit( $upload['baseurl'] ) . $uploads_dir
			);
		}

		return trailingslashit( $upload['basedir'] ) . $uploads_dir;
	}

	// Clean up directory - From Contact Form 7
	function dnd_cf7_auto_clean_dir( $dir_path = null ) {
		if ( is_admin() ) {
			return;
		}

        // Disable auto delete
        if( dnd_cf7_settings('drag_n_drop_disable_auto_delete') == 'yes' || get_option( 'drag_n_drop_disable_auto_delete' ) == 'yes' ) {
			return;
        }

		// Setup dirctory path
		$upload  = wp_upload_dir();

		// Setup/Get dir
		if ( ! $dir_path ) {
			$dir = trailingslashit( $upload['basedir'] ) . wpcf7_dnd_dir . '/wpcf7-files/'; // upload path.
		} else {
			$dir = trailingslashit( $dir_path ); // get path from recursive
		}

		// Make sure dir is readable or writable
		if ( ! is_dir( $dir ) || ! is_readable( $dir ) || ! wp_is_writable( $dir ) ) {
			return;
		}

		$seconds = apply_filters( 'dnd_cf7_auto_delete_files', 3600 );  // 1 hour
		$max     = apply_filters( 'dnd_cf7_max_file', 40 );
		$count   = 0;

		if ( $handle = @opendir( $dir ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				// exclude .htaccess and index.php file.
				if ( $file == "." || $file == ".." || $file == '.htaccess' || $file == 'index.php' ) {
					continue;
				}

				// Check if current path is directory (recursive)
				if ( is_dir( $dir . $file ) ) {
					dnd_cf7_auto_clean_dir( $dir . $file );
					continue;
				}

				// Get file time of files OLD files.
				$mtime = @filemtime( $dir . $file );

				// Compare modified time and time before deletion.
				if ( $mtime && time() < $mtime + absint( $seconds ) ) { // less than $seconds old
					continue;
				}

				// Delete files from dir
				wp_delete_file( $dir . $file );

				$count += 1;

				if ( absint( $max ) <= $count ) {
					break;
				}
			}
			@closedir( $handle );
		}
	}

	// Hooks before sending the email - ( append links to body email )
	function dnd_cf7_before_send_mail( $wpcf7 ){
		global $_mail;

		// Mail Counter
		$_mail = 0;

		// Check If send attachment as link
		if( ! dnd_cf7_settings('drag_n_drop_mail_attachment') ) {
			return $wpcf7;
		}

		// cf7 instance
		$submission = WPCF7_Submission::get_instance();

		// Check for submission
		if( $submission ) {

			// Get posted data
			$submitted['posted_data'] = $submission->get_posted_data();

			// Parse fields
			$fields = $wpcf7->scan_form_tags();

			// Links
			$links = array();

			// Prop email
			$mail = $wpcf7->prop('mail');
			$mail_2 = $wpcf7->prop('mail_2');

			// Loop fields and replace mfile code
			foreach( $fields as $field ) {
				if( $field->basetype == 'mfile') {
					if( isset( $submitted['posted_data'][$field->name] ) && ! empty( $submitted['posted_data'][$field->name] ) ) {

						// Get posted_data files
						$files = $submitted['posted_data'][$field->name];

						// Links - 1
						$mail_links = dnd_cf7_links( $files, $mail['use_html'] );
						$mail['body'] = str_replace( "[$field->name]", "\n" . implode( "\n", $mail_links ), $mail['body'] );

						// Links - 2
						if( $mail_2['active'] ) {
							$mail_links_2 = dnd_cf7_links( $files, $mail_2['use_html'] );
							$mail_2['body'] = str_replace( "[$field->name]", "\n" . implode( "\n", $mail_links_2 ), $mail_2['body'] );
						}
					}
				}
			}

            // For debug
            if( defined('dnd_cf7_debug') ){
                print_r($mail);
                print_r($mail_2);
            }

			// Save the email body
			$wpcf7->set_properties( array("mail" => $mail) );

			// if mail 2
			if( $mail_2['active'] ) {
				$wpcf7->set_properties( array("mail_2" => $mail_2) );
			}
		}

		return $wpcf7;
	}

	// hooks - Custom cf7 Mail components ( Attached File on Email )
	function dnd_cf7_mail_components( $components, $form ) {
		global $_mail;

		if( ! $form ) {
			return;
		}

		// Get upload directory
		$uploads_dir = dnd_get_upload_dir();

		// cf7 - Submission Object
		$submission = WPCF7_Submission::get_instance();

		// Posted data
		$posted_data = $submission->get_posted_data();

		// get all form fields
		$fields = $form->scan_form_tags();

		// Default counter
		$_mail = isset( $_mail ) ? $_mail : 0;

		// Display file links in email (no attachment)
		if( dnd_cf7_settings('drag_n_drop_mail_attachment') == 'yes' ) {
			return $components;
		}

		// Get mail,mail_2 attachment [tags]
		$mail       = array('mail','mail_2');
		$props_mail = array();

		foreach( $mail as $single_mail ) {
			$props_mail[] = $form->prop( $single_mail );
		}

		// Get email attachments (mail, mail_2)
		$mail = $props_mail[ $_mail ];
		if( $mail['active'] && $mail['attachments'] ) {

			// Loop fields get mfile only.
			foreach( $fields as $field ) {

				// If field type equal to mfile which our default field.
				if( $field->basetype == 'mfile') {

					// Check and make sure [upload-file-xxx] exists in attachments - fields
					if ( false !== strpos( $mail['attachments'], "[{$field->name}]" ) ) {

						// Loop all the files and attach to cf7 components
						if ( isset( $posted_data[ $field->name ] ) && ! empty( $posted_data[ $field->name ] ) ) {
							if ( is_array( $posted_data[ $field->name ] ) ) {
								foreach( $posted_data[ $field->name ] as $_file ) {

									// Convert url to dir
									$new_file_name = str_replace( $uploads_dir['upload_url'], $uploads_dir['upload_dir'], $_file );

									// Check if submitted and file exists then file is ready.
									if ( $submission && file_exists( $new_file_name )  ) {
										$components['attachments'][] = $new_file_name;
									}
								}
							}
						}

					}
				}
			}

		}

        // Debug
        if( defined('dnd_cf7_debug') ){
            print_r($components);
        }

		// Increment mail counter
		$_mail = $_mail + 1;

		// Return setup components
		return $components;
	}

	// Get file links.
	function dnd_cf7_links( $files, $use_html = false) {

		// check and make sure we have files
		if( ! $files ) {
			return;
		}

		// Setup html links
		$links = array();
		foreach( $files as $file ) {
			$links[] = ( $use_html ? '<a href="'. esc_url( $file ) .'">'. esc_html( wp_basename( $file ) ) .'</a>' : esc_url( $file ) );
		}

		// Allow other themes/plugin to modify data.
		return apply_filters('dndcf7_before_send_files', $links, $files );
	}

	// Log message...
	function dnd_logs( $message, $email = false ) {
		$uploads_dir = dnd_get_upload_dir();
		$file = fopen( $uploads_dir['upload_dir']."/logs.txt", "a") or die("Unable to open file!");
		fwrite( $file, "\n". ( is_array( $message ) ? print_r( $message, true ) : $message ) );
		fclose( $file );
	}

	// Load js and css
	function dnd_cf7_scripts() {
		global $post;

		// Get plugin version
		$version = dnd_upload_cf7_version;

		// Loads assets when needed load/unload js
		$load_on_cf7_page = apply_filters( 'dnd_cf7_load_on_cf7_page', false );

		// Don't load styles/scripts on regular pages that don't have CF7 shortcode.
		if ( $load_on_cf7_page && $post && ! has_shortcode( $post->post_content, 'contact-form-7' ) ) {
			return;
		}

		// enque script (Use native Javascript or jQuery)
        if( dnd_cf7_settings('drag_n_drop_use_jquery') == 'yes' ){
            wp_enqueue_script( 'codedropz-uploader', plugins_url ('/assets/js/codedropz-uploader-jquery.js', dirname(__FILE__) ), array('jquery','contact-form-7'), $version, true );
        }else{
            wp_enqueue_script( 'codedropz-uploader', plugins_url ('/assets/js/codedropz-uploader-min.js', dirname(__FILE__) ), '', $version, true );
        }

        // All data options
        $data_options = apply_filters('dnd_cf7_data_options',
            array(
                'tag'				=>	( dnd_cf7_settings('drag_n_drop_heading_tag') ? dnd_cf7_settings('drag_n_drop_heading_tag') : 'h3' ),
                'text'				=>	( dnd_cf7_settings('drag_n_drop_text') ? dnd_cf7_settings('drag_n_drop_text') : __('Drag & Drop Files Here','drag-and-drop-multiple-file-upload-contact-form-7') ),
                'or_separator'		=>	( dnd_cf7_settings('drag_n_drop_separator') ? dnd_cf7_settings('drag_n_drop_separator') : __('or','drag-and-drop-multiple-file-upload-contact-form-7') ),
                'browse'			=>	( dnd_cf7_settings('drag_n_drop_browse_text') ? dnd_cf7_settings('drag_n_drop_browse_text') : __('Browse Files','drag-and-drop-multiple-file-upload-contact-form-7') ),
                'server_max_error'	=>	( dnd_cf7_settings('drag_n_drop_error_server_limit') ? dnd_cf7_settings('drag_n_drop_error_server_limit') : dnd_cf7_error_msg('server_limit') ),
                'large_file'		=>	( dnd_cf7_settings('drag_n_drop_error_files_too_large') ? dnd_cf7_settings('drag_n_drop_error_files_too_large') : dnd_cf7_error_msg('large_file') ),
                'inavalid_type'		=>	( dnd_cf7_settings('drag_n_drop_error_invalid_file') ? dnd_cf7_settings('drag_n_drop_error_invalid_file') : dnd_cf7_error_msg('invalid_type') ),
                'max_file_limit'	=>	( dnd_cf7_settings('drag_n_drop_error_max_file') ? dnd_cf7_settings('drag_n_drop_error_max_file') : dnd_cf7_error_msg('max_file_limit') ),
                'required'			=>	dnd_cf7_error_msg('required'),
                'delete'			=>	array(
                    'text'		=>	__('deleting','drag-and-drop-multiple-file-upload-contact-form-7'),
                    'title'		=>	__('Remove','drag-and-drop-multiple-file-upload-contact-form-7')
                )
            )
        );

		//  registered script with data for a JavaScript variable.
		wp_localize_script( 'codedropz-uploader', 'dnd_cf7_uploader',
			array(
				'ajax_url' 				=> 	apply_filters( 'dnd_cf7_ajax_url', admin_url( 'admin-ajax.php' ) ),
				'ajax_nonce'			=>	wp_create_nonce( "dnd-cf7-security-nonce" ),
				'drag_n_drop_upload' 	=>  $data_options,
				'dnd_text_counter'	=>	__('of','drag-and-drop-multiple-file-upload-contact-form-7'),
				'disable_btn'		=>	( dnd_cf7_settings('drag_n_drop_disable_btn') == 'yes' ? true : false )
			)
		);

		// enque style
		wp_enqueue_style( 'dnd-upload-cf7', plugins_url ('/assets/css/dnd-upload-cf7.css', dirname(__FILE__) ), '', $version );
	}

	// Generate tag
	function dnd_cf7_upload_add_form_tag_file() {
		wpcf7_add_form_tag(	array( 'mfile ', 'mfile*'), 'dnd_cf7_upload_form_tag_handler', array( 'name-attr' => true ) );
	}

	// Form tag handler from the tag - callback
	function dnd_cf7_upload_form_tag_handler( $tag ) {

		// check and make sure tag name is not empty
		if ( empty( $tag->name ) ) {
			return '';
		}

		// Validate our fields
		$validation_error = wpcf7_get_validation_error( $tag->name );

		// Generate class
		$class = wpcf7_form_controls_class( 'drag-n-drop-file d-none' );

		// Add not-valid class if there's an error.
		if ( $validation_error ) {
			$class .= ' wpcf7-not-valid';
		}

		// Get current form Object
		$form = WPCF7_ContactForm::get_current();

		// Setup element attributes
		$atts = array();

		$atts['size'] = $tag->get_size_option( '40' );
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		// If file is required
		if ( $tag->is_required() ) {
			$atts['aria-required'] = 'true';
		}

		// Set invalid attributes if there's validation error
		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		// Set input type and name
		$atts['type'] = 'file';
		$atts['multiple'] = 'multiple';
		$atts['data-name'] = $tag->name;
		$atts['data-type'] = $tag->get_option( 'filetypes','', true);
		$atts['data-limit'] = $tag->get_option( 'limit','', true);
        $atts['data-min'] = $tag->get_option( 'min-file', '', true );
		$atts['data-max'] = $tag->get_option( 'max-file','', true);
		$atts['data-id'] = ( $form ? $form->id() : 0 );
        $atts['data-version'] = 'free version '. dnd_upload_cf7_version;

        // Accept data attributes
        $types = explode('|', $atts['data-type'] );

        if( $types && ! wp_is_mobile() ) {
            $type = implode(', .', array_map( 'trim', $types ) );
            if( $type != '*' ) {
                $atts['accept'] = '.' . $type;
            }
        }

        // Allow blacklist
        if( $tag->get_option('blacklist-types', '', true) ){
            $atts['data-black-list'] = $tag->get_option('blacklist-types', '', true);
        }

		// Combine and format attrbiutes
		$atts = wpcf7_format_atts( $atts );

		// Return our element and attributes
		return sprintf('<span class="wpcf7-form-control-wrap" data-name="%1$s"><input %2$s />%3$s</span>',	sanitize_html_class( $tag->name ), $atts, $validation_error );
	}

	// Encode type filter to support multipart since this is input type file
	add_filter( 'wpcf7_form_enctype', 'dnd_upload_cf7_form_enctype_filter' );

	function dnd_upload_cf7_form_enctype_filter( $enctype ) {
		$multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'drag_drop_file', 'drag_drop_file*' ) ) );

		if ( $multipart ) {
			$enctype = 'multipart/form-data';
		}

		return $enctype;
	}

	// 3rd party compatability...
	function dnd_cf7_conditional_fields( $form_id ) {

		if( ! $form_id ) {
			return false;
		}

		// Get visible groups
		$groups = array();

		// Get current form object
		$cf7_post = get_post( $form_id );

		// Extract group shortcode
		$regex = get_shortcode_regex( array('group') );

		// Match pattern
		preg_match_all( '/'. $regex .'/s', $cf7_post->post_content, $matches );

		if( array_key_exists( 3, $matches )) {
			foreach( $matches[3] as $index => $group_name ) {
				$name = array_filter( explode(" ", $group_name ) );
				preg_match('/\[mfile[*|\s].*?\]/', $matches[0][$index], $file_matches );
				if( $file_matches ) {
					$field_name = shortcode_parse_atts( $file_matches[0] );
					$field_name = preg_replace( '/[^a-zA-Z0-9-_]/','', $field_name[1] );
					$groups[ $field_name ] = $name[1];
				}
			}
		}

		return $groups;
	}

	// Validation + upload handling filter
	add_filter( 'wpcf7_validate_mfile', 'dnd_upload_cf7_validation_filter', 10, 2 );
	add_filter( 'wpcf7_validate_mfile*', 'dnd_upload_cf7_validation_filter', 10, 2 );

	function dnd_upload_cf7_validation_filter( $result, $tag ) {
		$name = $tag->name;
		$id = $tag->get_id_option();
		$multiple_files = ( ( isset( $_POST[ $name ] ) && is_countable( $_POST[ $name ] ) && count( $_POST[ $name ] ) > 0 ) ? $_POST[ $name ] : null );
		$min_file = $tag->get_option( 'min-file','', true);

        // Check minimum upload
		if( $multiple_files && count( $multiple_files ) < (int) $min_file ) {
			$min_file_error = ( dnd_cf7_settings('drag_n_drop_error_min_file') ? dnd_cf7_settings('drag_n_drop_error_min_file') : dnd_cf7_error_msg('min_file') );
			$result->invalidate( $tag, $min_file_error .' '. (int)$min_file );
			return $result;
		}

		// Cf7 Conditional Field
		if(
			in_array('cf7-conditional-fields/contact-form-7-conditional-fields.php', get_option('active_plugins') ) ||
		    in_array('cf7-conditional-fields/conditional-fields.php', get_option('active_plugins') )
		){

			$hidden_groups = json_decode( stripslashes( $_POST['_wpcf7cf_hidden_groups'] ) );
			$form_id = WPCF7_ContactForm::get_current()->id();
			$group_fields = dnd_cf7_conditional_fields( $form_id );

			if( is_null( $multiple_files ) && $tag->is_required() ) {
				if( isset( $group_fields[ $name ] ) && ! in_array( $group_fields[ $name ], $hidden_groups ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				}elseif( ! array_key_exists( $name, $group_fields ) ) {
					$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
				}
				return $result;
			}

			return $result;
		}

		// Check if we have files or if it's empty
		if( is_null( $multiple_files ) && $tag->is_required() ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			return $result;
		}

		return $result;
	}

	// Generate Admin From Tag
	add_action( 'wpcf7_admin_init', 'dnd_upload_cf7_add_tag_generator', 50 );

	function dnd_upload_cf7_add_tag_generator() {
		$tag_generator = WPCF7_TagGenerator::get_instance();

		// Version 2
		if ( version_compare( WPCF7_VERSION, '6.0', '>=' ) ) {
			$tag_generator->add(
				'upload-file',
				__( 'multiple file upload', 'drag-and-drop-multiple-file-upload-contact-form-7' ),
				'dnd_upload_cf7_tag_generator_file_v2',
				array( 'version' => '2' )
			);
		} else {
			$tag_generator->add(
				'upload-file', __( 'multiple file upload', 'drag-and-drop-multiple-file-upload-contact-form-7' ),
				'dnd_upload_cf7_tag_generator_file'
			);
		}
	}

	// Version 2.0
	function dnd_upload_cf7_tag_generator_file_v2( $contact_form, $options ) {

		$field_types = array(
			'mfile' => array(
				'display_name' => __( 'Drag & Drop Multiple File Upload', 'drag-and-drop-multiple-file-upload-contact-form-7' ),
				'heading'      => __( 'Drag & Drop File Upload Field - Form-tag Generator', 'drag-and-drop-multiple-file-upload-contact-form-7' ),
				'description'  => __( 'Generate a form-tag for a "drag & drop multiple file upload" field.', 'drag-and-drop-multiple-file-upload-contact-form-7' )
			),
		);

		$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

		// Load v2 form generator template.
		include dnd_upload_cf7_directory . '/admin/form-generator-v2.php';
	}

	// Display form in admin ( Version 1.0 )
	function dnd_upload_cf7_tag_generator_file( $contact_form, $args = '' ) {

		// Parse data and get our options
		$args = wp_parse_args( $args, array() );

		// Our multiple upload field
		$type = 'mfile';

		$description = __( "Generate a form-tag for a file uploading field. For more details, see %s.", 'contact-form-7' );
		$desc_link   = wpcf7_link( __( 'https://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File Uploading and Attachment', 'contact-form-7' ) );

		// Load v1 form generator template.
		include dnd_upload_cf7_directory . '/admin/form-generator-v1.php';
	}

    // Get option
    function dnd_cf7_get_option( $form_id, $option_name ) {

        $tags = dnd_get_upload_form( $form_id );
        $options = array();
        $args = array(
            'limit'     =>  10485760,
            'filetypes' =>  dnd_upload_default_ext()
        );

        // Loop all upload tags
		if( $tags && is_array( $tags ) ) {
			foreach( $tags as $tag ) {
                if( $option = $tag->get_option( $option_name, '', true ) ){
                    $options[ $tag->name ] = $option;
                }else{
                    $options[ $tag->name ] = ( isset( $args[ $option_name ] ) ? $args[ $option_name ] : '' );
                }
			}
		}

        return $options;
    }

    // Get contact form data
    function dnd_get_upload_form( $form_id ){

        // Initialize contact form instance
		$form = WPCF7_ContactForm::get_instance( $form_id );

		// Check if not valid object and null
		if( ! $form && ! is_object( $form ) ) {
			return false;
		}

		// Get specific tag (mfile is for dnd file upload)
		$tags = $form->scan_form_tags( array( 'type' => array('mfile', 'mfile*') ) );

        if( $tags ){
            return $tags;
        }

        return false;
    }

	// Begin process upload
	function dnd_upload_cf7_upload() {

		// cf7 form id & upload name
		$cf7_id = sanitize_text_field( (int)$_POST['form_id']);

		// Get the name of upload field.
		$cf7_upload_name = sanitize_text_field( $_POST['upload_name'] );

		// Get allowed ext list @expected : png|jpeg|jpg
		$allowed_types = dnd_cf7_get_option( $cf7_id, 'filetypes' );

        // File size limit
        $size_limit = dnd_cf7_get_option( $cf7_id, 'limit' );

        // Blacklist Option
        $blacklist = dnd_cf7_get_option( $cf7_id, 'blacklist-types' );

		// check and verify ajax request
        if( ! check_ajax_referer( 'dnd-cf7-security-nonce', 'security', false ) ) {
            wp_send_json_error('The security nonce is invalid or expired.');
        }

        // Get blacklist Types
        $blacklist_types = ( isset( $blacklist["$cf7_upload_name"] ) ?  explode( '|', $blacklist["$cf7_upload_name"] ) : '' );

		// Get upload dir
		$path = dnd_get_upload_dir( true ); // ok

		// input type file 'name'
		$name = 'upload-file';

		// Get File ( name, type, tmp_name, size, error )
		$file = isset( $_FILES[$name] ) ? $_FILES[$name] : null;

		// Tmp file
		$tmp_file = preg_replace('/^.*?:\/\//', '', $file['tmp_name'] );

		// Tells whether the file was uploaded via HTTP POST
		if ( ! is_uploaded_file( $tmp_file ) ) {
			$failed_error = dnd_cf7_settings('drag_n_drop_error_failed_to_upload');
			wp_send_json_error( '('. $file['error'] .') ' . ( $failed_error ? $failed_error : dnd_cf7_error_msg('failed_upload') ) );
		}

		/* Get allowed extension */
		$supported_type = ( isset( $allowed_types["$cf7_upload_name"] ) ? $allowed_types["$cf7_upload_name"] : dnd_upload_default_ext() );

		// Create type pattern for anti script
		$file_type_pattern = dnd_upload_cf7_filetypes( $supported_type );

		// Get file extension
        $extension = strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) );

        // Create file name
		$filename = wp_basename( $file['name'] );
		$filename = wpcf7_canonicalize( $filename, 'as-is' );

        // Check unique name
        $filename = wp_unique_filename( $path['upload_dir'], $filename );

        // Validate File Types
        if( $blacklist_types && in_array( $extension, $blacklist_types ) && $supported_type == '*' ){
            wp_send_json_error( dnd_cf7_settings('drag_n_drop_error_invalid_file') ? dnd_cf7_settings('drag_n_drop_error_invalid_file') : dnd_cf7_error_msg('invalid_type') );
        }

		// validate file type
		if ( ( ! preg_match( $file_type_pattern, $file['name'] ) || ! dnd_cf7_validate_type( $extension, $supported_type ) ) && $supported_type != '*' ) {
		    wp_send_json_error( dnd_cf7_settings('drag_n_drop_error_invalid_file') ? dnd_cf7_settings('drag_n_drop_error_invalid_file') : dnd_cf7_error_msg('invalid_type') );
		}

        // validate mime type
        if( $supported_type && $supported_type != '*' ){

            // wheather if we validate mime type
            $validate_mime = apply_filters('dnd_cf7_validate_mime', false );

            if( $validate_mime ){

                if( ! function_exists('wp_check_filetype_and_ext') ){
                    require_once ABSPATH .'wp-admin/includes/file.php';
                }

                // Get file type and extension name
                $wp_filetype = wp_check_filetype_and_ext( $tmp_file, $file['name'] ); //[ext, type]
                $valid_mimes = explode('|', $supported_type); // array[png, jpg]

                if( empty( $wp_filetype['type'] ) || empty( $wp_filetype['ext'] ) || ! in_array( $wp_filetype['ext'], $valid_mimes ) ){
                    wp_send_json_error( dnd_cf7_settings('drag_n_drop_error_invalid_file') ? dnd_cf7_settings('drag_n_drop_error_invalid_file') : dnd_cf7_error_msg('invalid_type') );
                }
            }
        }

		// validate file size limit
		if( isset( $size_limit["$cf7_upload_name"] ) && $file['size'] > $size_limit["$cf7_upload_name"] ) {
			wp_send_json_error( dnd_cf7_settings('drag_n_drop_error_files_too_large') ? dnd_cf7_settings('drag_n_drop_error_files_too_large') : dnd_cf7_error_msg('large_file') );
		}

		// Check if string is ascii then proceed with antiscript function ( remove or clean filename )
		if( dnd_cf7_check_ascii( $filename ) ){
			$filename = wpcf7_antiscript_file_name( $filename );
		}

		// Randomize filename
		if( 'yes' == dnd_cf7_settings('drag_n_drop_enable_unique_name') ) {
			$random_name = md5( uniqid( rand(), true ) .'-'. mt_rand() .'-'. time() );
			$filename    = $random_name .'.'. $extension;
		}

		// Add filter on upload file name
		$filename = apply_filters( 'wpcf7_upload_file_name', $filename,	$file['name'] );

		// Generate new filename
		$new_file = path_join( $path['upload_dir'], $filename );

		// Upload File
		if ( false === move_uploaded_file( $tmp_file, $new_file ) ) {
			$failed_error = dnd_cf7_settings('drag_n_drop_error_failed_to_upload');
			wp_send_json_error( '('. $file['error'] .') ' . ( $failed_error ? $failed_error : dnd_cf7_error_msg('failed_upload') ) );
		}else{

            // Setup path and files url
			$files = array(
				'path'	=>	wp_basename( $path['upload_dir'] ),
				'file'	=>	str_replace('/','-', $filename)
			);

			// Change file permission to 0400
			chmod( $new_file, 0644 );

            // Allow other plugin to hook
            do_action('wpcf7_upload_file_name_custom', $new_file, $filename );

			// Custom filter after upload
			$files = apply_filters( 'dnd_cf7_after_upload', $files );

            // Return json files
			wp_send_json_success( $files );
		}

		die;
	}

	// Check if a string is ASCII.
	function dnd_cf7_check_ascii( $string ) {
		if ( function_exists( 'mb_check_encoding' ) ) {
			if ( mb_check_encoding( $string, 'ASCII' ) ) {
				return true;
			}
		} elseif ( ! preg_match( '/[^\x00-\x7F]/', $string ) ) {
			return true;
		}

		return false;
	}

	// Delete file
	function dnd_codedropz_upload_delete() {

		// Get folder directory
		$dir = dnd_get_upload_dir();

		// check and verify ajax request.
        if( ! check_ajax_referer( 'dnd-cf7-security-nonce', 'security', false ) ) {
            wp_send_json_error('The security nonce is invalid or expired.');
        }

		// Block curl request.
		if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'curl' ) !== false ) {
			wp_send_json_error('Request blocked: cURL access is forbidden.');
		}

		// Sanitize Path.
		$path = ( isset( $_POST['path'] ) ? sanitize_text_field( $_POST['path'] ) : null );

		// Use only filename
		if ( dnd_cf7_settings('drag_n_drop_mail_attachment') == 'yes' || strpos( dirname( $path ), 'wpcf7-files' ) !== false ) {
			$path = wp_basename( $path ); // remove duplicate path "/12/file.jpg" to just "/file.jpg"
		}

		// Make sure path is set
		if ( ! is_null( $path ) ) {

			// Check valid filename & extensions
			if ( preg_match( '/wp-|(\.php|\.exe|\.js|\.phtml|\.cgi|\.aspx|\.asp|\.bat)(?!_\.txt$)/', $path ) ) {
                wp_send_json_error( 'Error: File not safe' );
            }

			// Validate path if it's match on the current folder
			$unique_id      = dnd_cf7_get_unique_id();
			$current_folder = dirname( $path );
			$current_path   = $dir['upload_dir'] .'/'. $unique_id .'/'. wp_basename( $path );

			// Show an error
			if ( 'yes' !== dnd_cf7_settings('drag_n_drop_mail_attachment') ) {
				if ( ( $unique_id && $unique_id !== trim( $current_folder ) ) || ! file_exists( $current_path ) ) {
					wp_send_json_error( 'Error: Unauthorized Request!' );
				}
			}

			// Concatenate path and upload directory
			$file_path = realpath( trailingslashit( $dir['upload_dir'] ) . trim( $path ) );

			// Check if is in the correct upload_dir
			if ( ! preg_match("/". wpcf7_dnd_dir ."/i", $file_path ) ) {
				wp_send_json_error('It\'s not a valid upload directory');
			}

			// Check if file exists
			if( file_exists( $file_path ) ){
				wp_delete_file( $file_path );
				if( ! file_exists( $file_path ) ) {
					wp_send_json_success('File Deleted!');
				}
			}
		}

		die;
	}

	// Setup file type pattern for validation
	function dnd_upload_cf7_filetypes( $types ) {
		$file_type_pattern = '';

		// If contact form 7 5.0 and up
		if( function_exists('wpcf7_acceptable_filetypes') ) {
			$file_type_pattern = wpcf7_acceptable_filetypes( $types, 'regex' );
			$file_type_pattern = '/\.(' . $file_type_pattern . ')$/i';
		}else{
			$allowed_file_types = array();
			$file_types = explode( '|', $types );

			foreach ( $file_types as $file_type ) {
				$file_type = trim( $file_type, '.' );
				$file_type = str_replace( array( '.', '+', '*', '?' ), array( '\.', '\+', '\*', '\?' ), $file_type );
				$allowed_file_types[] = $file_type;
			}

			$allowed_file_types = array_unique( $allowed_file_types );
			$file_type_pattern = implode( '|', $allowed_file_types );

			$file_type_pattern = trim( $file_type_pattern, '|' );
			$file_type_pattern = '(' . $file_type_pattern . ')';
			$file_type_pattern = '/\.' . $file_type_pattern . '$/i';
		}

		return $file_type_pattern;
	}

	// Add more validation for file extension
	function dnd_cf7_validate_type( $extension, $supported_types ) {
		$valid = true;
		$extension = preg_replace( '/[^A-Za-z0-9,|]/', '', $extension );

		// not allowed file types
		$not_allowed = array( 'php', 'php3','php4','phtml','exe','script', 'app', 'asp', 'bas', 'bat', 'cer', 'cgi', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'csr', 'dll', 'drv', 'fxp', 'flv', 'hlp', 'hta', 'htaccess', 'htm', 'htpasswd', 'inf', 'ins', 'isp', 'jar', 'js', 'jse', 'jsp', 'ksh', 'lnk', 'mdb', 'mde', 'mdt', 'mdw', 'msc', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'pl', 'prg', 'ps1', 'ps2', 'py', 'rb', 'reg', 'scr', 'sct', 'sh', 'shb', 'shs', 'sys', 'swf', 'tmp', 'torrent', 'url', 'vb', 'vbe', 'vbs', 'vbscript', 'wsc', 'wsf', 'wsf', 'wsh' );

		// allowed ext.
		$allowed_ext = array('ipt');

		// Search in $not_allowed extension and match
		foreach( $not_allowed as $single_ext ) {
			if ( strpos( $single_ext, $extension, 0 ) !== false && ! in_array( $extension, $allowed_ext )) {
				$valid = false;
				break;
			}
		}

		// If pass on first validation - check extension if exists in allowed types
		if( $valid === true ) {
			$extensions = explode('|', strtolower( $supported_types ) );
			if( ! in_array( $extension, $extensions ) ) {
				$valid = false;
			}
		}

		return $valid;
	}

	// Admin Settings
	function dnd_upload_admin_settings( ) {
		echo '<div class="wrap">';
			echo '<h1>' . esc_html__( 'Drag & Drop Uploader - Settings', 'drag-and-drop-multiple-file-upload-contact-form-7' ) . '</h1>';

				echo '<div class="update-nag notice" style="width: 98%;padding: 0px 10px;margin-bottom: 5px;">';
				echo '<p>' . sprintf( esc_html__( '%1$sUpgrade Now%2$s for Extra Features: Explore the %3$sPro Version%4$s Today!', 'drag-and-drop-multiple-file-upload-contact-form-7' ),'<span style="color:#038d03;">','</span>','<a href="https://codedropz.com/purchase-plugin/" target="_blank">','</a>') . '</p>';
				echo '</div>';

				// Error settings
				settings_errors();

				echo '<form method="post" action="options.php"> ';
					settings_fields( 'drag-n-drop-upload-file-cf7' );
					do_settings_sections( 'drag-n-drop-upload-file-cf7' );
		?>

                <table class="form-table" style="display:none;">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Translate To','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><?php wp_dropdown_languages( array('name' => 'drag_n_drop_lang', 'id' => 'drag_n_drop_lang') ); ?>
                            <div style="margin-top:20px;">
								<strong><?php esc_html_e( 'Translated: ', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?></strong><a href=""><?php esc_html_e( 'abc', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?></a>
                            </div>
                        </td>
					</tr>
				</table>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Send Attachment as links?','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input name="dndmfu_settings[drag_n_drop_mail_attachment]" type="checkbox" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_mail_attachment')); ?>></td>
					</tr>
				</table>

				<h2><?php esc_html_e('Uploader Info','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Heading Tag','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td>
							<select name="dndmfu_settings[drag_n_drop_heading_tag]">
								<option value="h1" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'h1'); ?>>H1</option>
								<option value="h2" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'h2'); ?>>H2</option>
								<option value="h3" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag','h3'), 'h3'); ?>>H3</option>
								<option value="h4" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'h4'); ?>>H4</option>
								<option value="h5" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'h5'); ?>>H5</option>
								<option value="h6" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'h6'); ?>>H6</option>
                                <option value="span" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'span'); ?>>Span</option>
                                <option value="div" <?php selected( dnd_cf7_settings('drag_n_drop_heading_tag'), 'div'); ?>>Div</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Drag & Drop Text','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_text]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_text') ); ?>" placeholder="<?php esc_attr_e( 'Drag & Drop Files Here ', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_separator]" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_separator') ); ?>" placeholder="<?php esc_attr_e( 'or', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Browse Text','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_browse_text]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_browse_text') ); ?>" placeholder="<?php esc_attr_e( 'Browse Files', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?>" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e('Error Message','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('File exceeds server limit','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_error_server_limit]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_server_limit') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('server_limit'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Failed to Upload','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_error_failed_to_upload]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_failed_to_upload') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('failed_upload'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Files too large','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_error_files_too_large]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_files_too_large') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('large_file'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Invalid file Type','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_error_invalid_file]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_invalid_file') ); ?>" placeholder="<?php echo dnd_cf7_error_msg('invalid_type'); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Max File Limit','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td>
							<input type="text" name="dndmfu_settings[drag_n_drop_error_max_file]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_max_file') ); ?>" />
							<p class="description"><?php esc_html_e( 'Example: `Note : Some of the files are not uploaded ( Only %count% files allowed )`', 'drag-and-drop-multiple-file-upload-contact-form-7' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Minimum File','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="text" name="dndmfu_settings[drag_n_drop_error_min_file]" class="regular-text" value="<?php echo esc_attr( dnd_cf7_settings('drag_n_drop_error_min_file') ); ?>" /></td>
					</tr>
				</table>

				<h2><?php esc_html_e('Auto Delete Files','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>
				<table class="form-table">
					<tr valign="top">
							<th scope="row"><?php esc_html_e('Don\'t delete files','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="checkbox" name="dndmfu_settings[drag_n_drop_disable_auto_delete]" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_disable_auto_delete')); ?>> Yes <br><p class="description"><em><?php esc_html_e( 'The default will automatically delete files 1-2 hours after submissions, if you want to keep files check "Yes" above. ', 'drag-and-drop-multiple-file-upload-contact-form-7'); ?></em></p></td>
					</tr>
				</table>

                <h2><?php esc_html_e('Unique Filename','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Randomize','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="checkbox" name="dndmfu_settings[drag_n_drop_enable_unique_name]" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_enable_unique_name')); ?>> Yes <br><p class="description"><em><?php esc_html_e('If checked, it will generate a unique/randomized filename.', 'drag-and-drop-multiple-file-upload-contact-form-7'); ?></em></p></td>
					</tr>
				</table>

                <h2><?php esc_html_e('Spam Filtering Issue','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Fix Spam','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="checkbox" name="dndmfu_settings[drag_n_drop_fix_spam]" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_fix_spam')); ?>> Yes <p class="description"><em>If a “spam” answer is the response, Contact Form 7 will suspend the email and show a message saying, “There was an error trying to send your message", force to send message by checking this option..</em></p></td>
					</tr>
				</table>

                <h2><?php esc_html_e('Use jQuery','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Enable jQuery','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="checkbox" name="dndmfu_settings[drag_n_drop_use_jquery]" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_use_jquery')); ?>> Yes <p class="description"><em>Activate this option in case there are any problems with our plugin when utilizing native Javascript.</em></p></td>
					</tr>
				</table>

				<h2 style="display:none;"><?php esc_html_e('Disable Button','drag-and-drop-multiple-file-upload-contact-form-7'); ?></h2>

				<table style="display:none;" class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Disable Submit button','drag-and-drop-multiple-file-upload-contact-form-7'); ?></th>
						<td><input type="checkbox" name="dndmfu_settings[drag_n_drop_disable_btn]" value="yes" <?php checked('yes', dnd_cf7_settings('drag_n_drop_disable_btn')); ?>> Yes <p class="description">Disable submit button if there's an error.</p></td>
					</tr>
				</table>

				<?php submit_button(); ?>

		<?php
			echo '</form>';
		echo '</div>';
	}

	// Add script in footer
	function dnd_custom_scripts() {
		if( ! in_array('jquery-validation-for-contact-form-7/jquery-validation-for-contact-form-7.php', get_option('active_plugins') ) ){
			return;
		}
		?>
		<script type="text/javascript">
			// Contact form 7 - Jquery validation
			jQuery(document).ready(function($){
				jQuery('.wpcf7-form-control.wpcf7-submit').click(function(e){
					var uploadFields = $(this).parents('form').find('.wpcf7-drag-n-drop-file');
					var valid = true;
					if( uploadFields.length > 0 ) {
						jQuery.each(uploadFields, function(i,field){
							if( $(field).attr('aria-required') == 'true' ) {
								parentsWrap = $(field).parents('.codedropz-upload-wrapper');
								parentsWrap.removeClass('invalid');
								parentsWrap.find('label').remove();
								if( $('[type="hidden"][name="'+$(field).attr('data-name')+'[]"]').length == 0 ) {
									parentsWrap.append('<label class="error-new">'+ dnd_cf7_uploader.drag_n_drop_upload.required +'</label>').addClass('invalid');
									valid = false;
								}
							}
						});
						if( ! valid ) {
							return false;
						}
					}
					return true;
				});
			});
		</script>
		<?php
	}

	// Define custom (safe) file extension.
	function dnd_upload_default_ext() {
		return apply_filters('dnd_cf7_default_ext', 'jpg|jpeg|JPG|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv|xls' );
	}

	// Add custom links
	function dnd_custom_plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'drag-n-drop-upload-cf7.php' ) !== false ) {
			$new_links = array('pro-version' => '<a href="https://codedropz.com/purchase-plugin/" target="_blank" style="font-weight:bold; color:#f4a647;">Pro Version</a>');
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	// Save admin settings
	function dnd_upload_register_settings() {

		// Specific for "Auto delete" settings (Default value if not set)
		$auto_delete_key = 'drag_n_drop_disable_auto_delete';
		if ( isset( $_POST['dndmfu_settings'] ) && ! isset( $_POST['dndmfu_settings'][ $auto_delete_key ] ) ) {
			$_POST['dndmfu_settings'][ $auto_delete_key ] = '';
			if ( get_option( $auto_delete_key ) ) {
				delete_option( $auto_delete_key ); // Delete old settings
			}
		}

		// Save option
		register_setting( 'drag-n-drop-upload-file-cf7', 'dndmfu_settings',  array( 'type' => 'array' ));
	}

	// Get admin option settings
	function dnd_cf7_settings( $option_name, $default_value = false ) {

		// Get option settings of the plugin
		$settings = get_option( 'dndmfu_settings' );

		// Auto delete option (Added to compatible with previous version, after the migration)
		$key = 'drag_n_drop_disable_auto_delete';
		if ( $option_name == $key ) {
			if ( get_option( $key ) == 'yes' ) {
				return get_option( $key );
			}
		}

		// Check if option_name is set
		if ( $settings && isset( $settings[ $option_name ] ) ) {
			return ( ! is_array( $settings[ $option_name ] ) ? trim( $settings[ $option_name ] ) : $settings[ $option_name ] );
		}

		return $default_value;
	}

	// Generate cookie (Cookie expiration 12 Hours)
	function dnd_cf7_generate_cookie() {

		// if file send as link don't generate cookie as folder will added to /uploads/year/month
		if ( 'yes' === dnd_cf7_settings('drag_n_drop_mail_attachment') ) {
			return;
		}
		?>
		<script type="text/javascript">
			document.addEventListener("DOMContentLoaded", function() {
				if ( ! document.cookie.includes("wpcf7_guest_user_id")) {
					document.cookie = "wpcf7_guest_user_id=" + crypto.randomUUID() + "; path=/; max-age=" + (12 * 3600) + "; samesite=Lax";
				}
			});
		</script>
		<?php
	}

	// Get current language (Polylang & WPML)
    function dnd_upload_cf7_lang() {
        $lang = null;

        // Polylang & WPML compatiblity
        if( function_exists('pll_current_language') ) {
            $lang = pll_current_language();
        }elseif( class_exists('SitePress') ) {
            $lang = ICL_LANGUAGE_CODE;
        }

        // If english / default lang leave empty.
        if( $lang ) {
            $lang = ( $lang == 'en' ? '' : '-'.$lang );
        }

        return apply_filters('dndmfu_wc_lang', $lang );
    }