<?php 
$style = bzotech_get_value_by_id('bzotech_style_post_detail');
$size = bzotech_get_option('post_single_size');
$check_thumb = bzotech_get_option('post_single_thumbnail','1');
$check_meta  = bzotech_get_option('post_single_meta','yes');
$item_meta_select  = bzotech_get_option('single_item_meta_select');
if(empty($item_meta_select)) $item_meta_select = ['author','date','comments'];
$size = bzotech_get_size_crop($size);

$data = array(
            'size'              => $size,
            'check_thumb'       => $check_thumb,
            'check_meta'        => $check_meta,
            'item_meta_select'  => $item_meta_select,
            'style'  => $style,
        );

?>
<div id="main-content"  class="main-page-default single-blog-<?php echo esc_attr($style); ?>">
    <div class="bzotech-container">
        <div class="bzotech-row">
            <?php bzotech_output_sidebar('left')?>
            <div class="<?php echo esc_attr(bzotech_get_main_class()); ?>">
                <?php
                    global $post;
                    echo '<div class="content-single-blog">';

                         ?>
                        <div class="content-post-default">
                            <?php 
                            bzotech_display_metabox('detail-post',array('cats'),'','style-item-meta__cats');
                            if( ! empty( $post->post_title ) ){ ?>
                                 <h2 class="title48 font-title title-post-single color-title text-capitalize">
                                    <?php the_title()?>
                                    <?php echo (is_sticky()) ? '<i class="sticky-icon las la-star"></i>':''?>
                                </h2>
                            <?php }?>

                            <?php if($check_meta == 'yes') bzotech_display_metabox('detail-post',$item_meta_select,'','meta-post-style1'); 

                            bzotech_get_template_post( 'single-content/content',get_post_format(),$data,true );
                            ?>
                            
                           
                            <div class="detail-content-wrap clearfix"><?php the_content(); ?></div>
                        </div>
                        <?php
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bw-printxtore' ),
                            'after'  => '</div>',
                            'link_before' => '<span>',
                            'link_after'  => '</span>',
                        ) );
                        if($check_meta == 'yes') bzotech_display_metabox('tags-share','', '','meta-post-tags-share-style2');
                        bzotech_get_template_post( 'single-content/author','',false,true );
                        bzotech_get_template_post( 'single-content/navigation','',false,true );
                        bzotech_get_template_post( 'single-content/related','',false,true );
                        if ( comments_open() || get_comments_number() ) { comments_template(); }
                    echo '</div>'; ?>
            </div>
            <?php bzotech_output_sidebar('right')?>
        </div>
    </div>
</div>