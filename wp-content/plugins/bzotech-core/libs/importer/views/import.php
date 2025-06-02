<?php
/**
 * Created by Sublime Text 3.
 * User: mbach90
 * Date: 12/08/15
 * Time: 10:20 AM
 */
$template_path = get_template_directory();
$files_image_preview=glob($template_path."/data-import/image-preview/*.jpg");
global $bzotech_number_demo;
$theme = wp_get_theme(); // gets the current theme
$demo_to = 1;
?>
<div class="wrap">
    <h2><?php _e('Import Demo Content','bzotech-core') ?></h2>
</div>

    <div id="message" class="updated">
        <p>
        The Demo content is a replication of the Live Content. By importing it, you could get several sliders, sliders,
        pages, posts, theme options, widgets, sidebars and other settings.<br>
        To be able to get them, make sure that you have installed and activated these plugins:  Contact form 7 , Redux Framework and Elementor<br> <span style="color:#f0ad4e">
    WARNING: By clicking Import Demo Content button, your current theme options, sliders and widgets will be replaced. It can also take a minute to complete. <br><span style="color:red"><b>Please back up your database before  it.</b></span>
    
        </p>
        <h3 class="title">Recommended settings:</h3>

        <table class="recommended">
          <tr>
            <th>Directive</th>
            <th>Recommended</th>
            <th>Actual</th>
          </tr>
          <tr>
            <td><strong>max_input_time</strong></td>
            <td><span class="ok">6000</span></td>
            <?php 
            $max_input_time = ini_get('max_input_time');
            if((int)$max_input_time>=6000) $class_max_input_time = 'ok';
            else if(6000 > (int)$max_input_time && (int)$max_input_time >= 4000) $class_max_input_time = 'tam-ok';
            else $class_max_input_time = 'no-ok';
            ?>
            <td><span class="<?php echo $class_max_input_time; ?>"><?php  echo $max_input_time; ?></span></td>
          </tr>
          
          <tr>
            <td><strong>memory_limit</strong></td>
            <td><span class="ok">128M</span></td>
            <?php 
            $memory_limit = ini_get('memory_limit');
            $memory_limit = str_replace('M','',$memory_limit);
            if((int)$memory_limit>=128) $class_memory_limit = 'ok';
            else if(128 > (int)$memory_limit && (int)$memory_limit >= 64) $class_memory_limit = 'tam-ok';
            else $class_memory_limit = 'no-ok';
            ?>
            <td><span class="<?php echo $class_memory_limit; ?>"><?php  echo ini_get('memory_limit'); ?></span></td>
          </tr>
          <tr>
            <td><strong>max_execution_time</strong></td>
            <td><span class="ok">6000</span></td>
             <?php 
            $max_execution_time = ini_get('max_execution_time');
            if((int)$max_execution_time>=6000) $class_max_execution_time = 'ok';
            else if(6000 > (int)$max_execution_time && (int)$max_execution_time >= 4000) $class_max_execution_time = 'tam-ok';
            else $class_max_execution_time = 'no-ok';
            ?>
            <td><span class="<?php echo $class_max_execution_time; ?>"><?php  echo ini_get('max_execution_time'); ?></span></td>
          </tr>
          <tr>
            <td><strong>post_max_size</strong></td>
            <td><span class="ok">64M</span></td>
            <?php 
            $post_max_size = ini_get('post_max_size');
            $post_max_size = str_replace('M','',$post_max_size);
            if((int)$post_max_size>=64) $class_post_max_size = 'ok';
            else if(64 > (int)$post_max_size && (int)$post_max_size >= 32) $class_post_max_size= 'tam-ok';
            else $class_post_max_size = 'no-ok';
            ?>
            <td><span class="<?php echo $class_post_max_size; ?>"><?php  echo ini_get('post_max_size'); ?></span></td>
          </tr>
          
          <tr>
            <td><strong>upload_max_filesize</strong></td>
            <td><span class="ok">32M</span></td>
            <?php 
            $upload_max_filesize = ini_get('upload_max_filesize');
            $upload_max_filesize = str_replace('M','',$upload_max_filesize);
            if((int)$upload_max_filesize>=32) $class_upload_max_filesize = 'ok';
            else if(32 > (int)$upload_max_filesize && (int)$upload_max_filesize >= 16) $class_upload_max_filesize= 'tam-ok';
            else $class_upload_max_filesize= 'no-ok';
            ?>
            <td><span class="<?php echo $class_upload_max_filesize; ?>"><?php  echo ini_get('upload_max_filesize'); ?></span></td>
          </tr>
          <tr>
            <td><strong>WordPress config (wp-config.php)</strong></td>
            <td><code>set_time_limit (600);</code></td>
            <td></td>
          </tr>
          
        </table>
        


    <br>
    <div class="list-demo-import" style="display:flex; flex-wrap: wrap;">
        <?php for($demo=$demo_to; $demo <= $bzotech_number_demo; $demo++) {?>
            <div class="list-demo-import__col" >
                <div class="list-demo-import__item" >
                    <?php
                    if($demo == '1')
                        $link_preview = 'https://bw-printxtore.bzotech.com/';
                    else
                        $link_preview = 'https://bw-printxtore.bzotech.com/home-'.$demo; ?>
                    <a target="_blank" href="<?php echo $link_preview; ?>">
                        <span class="import-preview"><span>Preview</span></span>
                        <img class="img-demo" src="<?php echo get_template_directory_uri().'/data-import/image-preview/demo'.$demo.'.jpg'; ?>">
                    </a>
                    <div class="list-demo-import__action">
                        <h3>Demo <?php echo $demo; ?></h3>
                        <a href="#" onclick="return false" data-url="<?php echo admin_url('?bzotech_do_import='.$demo) ?>" class="btn_stp_do_import button button-primary"><?php _e('Import All','bzotech-core')?></a>
                        <a href="#" onclick="return false" data-url="<?php echo admin_url('?bzotech_do_import='.$demo.'&media=0') ?>" class="btn_stp_do_import button button-primary"><?php _e('Import Without Media','bzotech-core')?></a>
                        <a href="#" onclick="return false" data-url="<?php echo admin_url('?bzotech_do_import='.$demo.'&media=2') ?>" class="btn_stp_do_import button button-primary"><?php _e('Import Media','bzotech-core')?></a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
        
    <div class="import_debug-css">
        <div id="import_debug">
        </div>
    </div>
<style>
    #message .recommended{
       text-align: left;
        border-collapse: collapse;
        width: 100%;
        max-width: 500px;
    }
    #message .recommended .ok{
         background: #06ba0b;
        color: #fff;
        padding: 0px 5px 2px;
        border-radius: 3px;
    }
    #message .recommended .no-ok{
         background: #e40202;
        color: #fff;
        padding: 0px 5px 2px;
        border-radius: 3px;
    }
    #message .recommended .tam-ok{
         background: #ed9400;
        color: #fff;
        padding: 0px 5px 2px;
        border-radius: 3px;
    }
    #message .recommended td, #message .recommended th {
      border: 1px solid #fff;
      text-align: left;
      padding: 8px;
    }

    #message .recommended tr {
      background-color: #f4f4f4;
    }
    #message .recommended tr:nth-child(even) {
      background-color: #e3e3e3;
    }
    #import_debug >h3{
        margin-bottom: 0;
    }
    .import_debug-css{
        display:none;
          left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        z-index: 11111;
        background: #000000ba;
        position: fixed;
    }

    .import_debug-css .bt-done{
           display: inline-block;
        padding: 10px 25px;
        background: #4c9d00;
        color: #fff;
        font-size: 14px;
        margin-top: 15px;
        border-radius: 5px;
        text-transform: uppercase;
        text-decoration: none;
        font-weight: 500;
        margin-right: 10px;
        transition: all 0.3s ease-out 0s;
        -webkit-transition: all 0.3s ease-out 0s;
    }
    .import_debug-css .bt-next{
       background: #3582c4;
    }
    .import_debug-css .bt-done .dashicons-before{
        display: inline-block;
        height: 16px;
        line-height: 16px;
    }
    .import_debug-css .bt-done .dashicons-before:before{
        font-size: 14px;
    }
    .import_debug-css .bt-done:hover{
         color: #fff;
        box-shadow: 0px 5px 15px #0000005e;
    }
    .import_debug-css .loading_import{
         margin-bottom: -10px;
         display: inline-block;
    }
    .import_debug-css.active{
        display:flex;
        justify-content: center;
        align-items: center;
    }
    .list-demo-import {
      margin-left: -15px;
            width: 100%;
    }
    .list-demo-import .list-demo-import__col{
        margin-bottom: 30px;
        width: 25%;
    }
    .list-demo-import .list-demo-import__item{
        padding: 0px 15px;
    }
   .wp-core-ui .list-demo-import .button-primary{
        font-size: 12px;
        padding: 0px 7px;
        margin-bottom: 3px;
    }
    .img-demo{
        width: 100%;
    }
    #import_debug{
        
        height: 300px;
        margin-top: 30px;
        overflow-y: auto;
        padding: 30px;
        font-style: normal;
        max-width: 650px;
        width: 100%;
        background: #fff;
        border-radius: 12px;
    }
    #import_debug >span{
        color:#0C0;
    }
    #import_debug .red{
        color: #ff0000;
    }
    .list-demo-import__action{
            text-align: center;
    margin-top: 10px;
    }
    .list-demo-import__action h3{
        float: left;
        margin: 0;
        text-transform: uppercase;
        margin-top: 5px;
    }
    .list-demo-import__item>a{
        position: relative;
        display: inline-block;
        border-radius: 10px;
    overflow: hidden;
    }
    .list-demo-import__item .import-preview{
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        /* margin: auto; */
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
            background: color(a98-rgb 0 0 0 / 0.7);
        color: #fff;
            transition: all 0.2s ease-out 0s;
    -webkit-transition: all 0.2s ease-out 0s;
    opacity: 0;
    }
    .list-demo-import__item .import-preview span{
           display: inline-block;
        background: #80b4ff;
        padding: 15px 30px;
        border-radius: 25px;
        text-transform: uppercase;
        font-size: 16px;
        font-weight: 500;
          transition: all 0.2s ease-out 0s;
    -webkit-transition: all 0.2s ease-out 0s;
    }
    .list-demo-import__item .import-preview span:hover{
        background: #ffc12b;
        color: #fff;
    }
    .list-demo-import__item:hover .import-preview{
       
        opacity:1;
    }
</style>