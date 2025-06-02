function openEMICONSmobile() { 
    document.querySelector('.enabled-mobile-menu .mobile-menu-area').classList.add('opened');
}

function closeEMICONSmobile() { 
    document.querySelector('.enabled-mobile-menu .mobile-menu-area').classList.remove('opened');
}

(function($) {

    $(document).ready(function(){

        $('.emicons-menu-vertical-expand-button-wrapper a').click(function (e) { 
            e.preventDefault();
            let widgetID = $(this).attr('widget_id');
            $('.enabled-vertical-menu .vertical-expaned-menu-area'+'.'+widgetID+ ' .emicons-menu-vertical-expanded').toggleClass('opened');
        })


        $(".emicons-menu-area .mobile-menu-area .emicons-emicons .menu-item-has-children > .menu-link").removeAttr('href', '#');
        if($(".emicons-menu-area .mobile-menu-area .emicons-menu-mobile-sidebar .emicons-emicons").length){
            $(".emicons-menu-area .mobile-menu-area .emicons-menu-mobile-sidebar .emicons-emicons").mgaccordion({
                theme: 'tree',
            });
        }

        if($(".emicons-menu-area .emicons-emicons.vertical.vertical-submenu-expand-mode-click").length){
            $(".emicons-menu-area .emicons-emicons.vertical.vertical-submenu-expand-mode-click").mgaccordion({
                theme: 'tree',
            });
        }

        let headerInnerWidth = $('.header-inner .e-con > .e-con-inner').width();
        $('.sub-menu.emicons-contents.full-width-mega-menu').css('width', headerInnerWidth+'px');
        $('.sub-menu.emicons-contents.full-width-mega-menu').css('max-width', headerInnerWidth+'px');
        $('.elementor-widget.elementor-widget-rt-mega-navigation-menu').css('position', 'static');
        $('.elementor-widget.elementor-widget-rt-mega-navigation-menu').parent().css('position', 'static');

        $(window).resize(function(){
            let headerInnerWidth = $('.header-inner .e-con > .e-con-inner').width();
            $('.sub-menu.emicons-contents.full-width-mega-menu').css('width', headerInnerWidth+'px');
            $('.sub-menu.emicons-contents.full-width-mega-menu').css('max-width', headerInnerWidth+'px');
        });

    });

})(jQuery);