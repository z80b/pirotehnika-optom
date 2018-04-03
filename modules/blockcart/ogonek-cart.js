(function(w,$) {
    var $cartBlock = $('.ps-head__cart'), 
        cartTop = $cartBlock.offset().top,
        $cartElement = $cartBlock.find('.shopping_cart');
    $(w).on('scroll', function(event) {
        if (event.currentTarget.scrollY > cartTop) {
            $cartBlock.addClass('ps-head__cart--fixed');
        } else $cartBlock.removeClass('ps-head__cart--fixed');
    });
})(window, jQuery);