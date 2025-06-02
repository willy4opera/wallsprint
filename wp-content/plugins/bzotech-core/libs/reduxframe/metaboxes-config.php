<?php

if(!function_exists('bzotech_register_custom_extension_loader')) :
    function bzotech_register_custom_extension_loader($ReduxFramework) {
        $path    = dirname( __FILE__ ) . '/extensions/';
            $folders = scandir( $path, 1 );
            foreach ( $folders as $folder ) {
                if ( $folder === '.' or $folder === '..' or ! is_dir( $path . $folder ) ) {
                    continue;
                }
                $extension_class = 'ReduxFramework_Extension_' . $folder;
                if ( ! class_exists( $extension_class ) ) {
                    // In case you wanted override your override, hah.
                    $class_file = $path . $folder . '/extension_' . $folder . '.php';
                    $class_file = apply_filters( 'redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file );
                    if ( $class_file ) {
                        require_once( $class_file );
                    }
                }
                if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
                    $ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
                }
            }
    }
    // Modify {$redux_opt_name} to match your opt_name
    if(function_exists('bzotech_get_option_name')) $bzotech_option_name = bzotech_get_option_name();
    else $bzotech_option_name = 'bzotech_theme_option';
    add_action("redux/extensions/".$bzotech_option_name."/before", 'bzotech_register_custom_extension_loader', 0);
endif;