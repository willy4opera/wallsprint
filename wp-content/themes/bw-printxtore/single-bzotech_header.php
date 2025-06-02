<?php
/**
 * The template for displaying all single posts.
 *
 * @package BzoTech-Framework
 */
?>
<?php get_header('none');?>
    <div id="main-content"  class="main-page-mega <?php echo 'bzotech-'.str_replace ('.php','',get_page_template_slug(get_the_ID()));?>">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile; ?>
    </div>
<?php get_footer('none');?>