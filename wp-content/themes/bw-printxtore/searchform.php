<form role="search" class="wg-search-form" method="get" action="<?php echo esc_url(home_url( '/' )); ?>">
    <input type="text" value="<?php echo get_search_query() ?>"  name="s" placeholder="<?php echo esc_attr__('Search..','bw-printxtore')?>">
    
    <button type="submit" value="<?php esc_attr_e("Search",'bw-printxtore')?>">
        <i class="icon-bzo icon-bzo-search"></i>
    </button>
</form>