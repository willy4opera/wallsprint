<?php
/**
 * The template for displaying all single posts.
 *
 * @package BzoTech-Framework
 */

get_header();
?>
<?php
while ( have_posts() ) : the_post(); 
    do_action('bzotech_before_main_content')?>
    <div id="main-content" class="main-page-default">
        <div class="bzotech-container">
            <div class="bzotech-row">
                <?php bzotech_output_sidebar('left')?>
                <div class="<?php echo esc_attr(bzotech_get_main_class()); ?>">
                    <div class="content-page-default">
                        <?php
                        

                            /*
                            * Include the post format-specific template for the content. If you want to
                            * use this in a child theme, then include a file called called content-___.php
                            * (where ___ is the post format) and that will be used instead.
                            */
                            ?>
                            	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        							<div class="entry-content clearfix">
        								<?php the_content(); ?>
        							</div><!-- .entry-content -->
                                    <?php
                                        wp_link_pages( array(
                                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'bw-printxtore' ),
                                            'after'  => '</div>',
                                        ) );
                                        bzotech_get_template( 'share','',false,true );
                                    ?>
        						</article><!-- #post-## -->
                            <?php

                            if ( comments_open() || get_comments_number()) :
                                comments_template();
                            endif;
                         ?>
                        
                    </div> 
                </div> 
                <?php bzotech_output_sidebar('right')?>
            </div>
        </div>
    </div>
    <?php do_action('bzotech_after_main_content')?>
    <?php
endwhile;
get_footer();