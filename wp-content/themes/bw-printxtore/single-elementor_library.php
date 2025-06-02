<?php
/**
 * The template for displaying all single posts.
 *
 * @package BzoTech-Framework
 */

get_header();
?>
<div id="main-content"  class="main-page-default">
    <div class="bzotech-container">
        <div class="bzotech-row">
            <div class="content-single-el-library bzotech-col-md-12">
                <?php
                
                while ( have_posts() ) : the_post();
					the_content();
                endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php

get_footer();