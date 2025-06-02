<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package htvietnam
 */

?>
	    <?php echo bzotech_get_template('footer-default');?>
        <?php echo bzotech_get_template('scroll-top');?>
        <?php echo bzotech_get_template('wishlist-notification');?>
        <?php echo bzotech_get_template('tool-panel');?>
        <?php echo bzotech_get_template('footer-after');?>
        
    </div>
<?php wp_footer(); ?>
</body>
</html>
