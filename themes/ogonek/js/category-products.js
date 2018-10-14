function renderProductInfo(id_product, data) {
    console.log('renderProductInfo:', data);
}

function updateProductInfo(id_product) {
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: baseUri + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType: 'json',
        data: {
            controller: 'cart',
            ajax: true,
            token: static_token            
        }
    }).done(function(jsonData) {
        //renderProductInfo(id_product, data);
        ajaxCart.updateCart(jsonData);
    });
}
$(document).ready(function() {
    $('.js-ps-products')
        .on('click', '.ps-product__image-link', function(event) {
            event.preventDefault();
            $.get(event.currentTarget.getAttribute('href') + '?ajax=1', function(response) {
                $('.js-product-popup .ps-popup__body').html(response);
                updateProductInfo(event.currentTarget.getAttribute('data-product-id'));
                $('.js-product-popup').removeClass('hidden');

                var imagesDef = $('.js-product-popup .b-image-block__slide-image').map(function(ix, el) {
                    if ($(el).hasClass('b-image-block__slide-image--video')) return;
                    var img = new Image();
                    var df = $.Deferred();

                    img.onload = df.resolve;
                    img.src = el.getAttribute('data-src');

                    df.done(function() {
                        console.log(img);
                        $(el)
                            .attr('src', img.src)
                            .parent()
                            .css('backgroundImage', 'url("'+ img.src +'")');
                    });

                    return df;
                });

                $.when.apply(this, imagesDef).then(function(a,b) {
                    $('.js-product-popup .js-product-images-slider').slick({
                        infinite: true,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        lazyLoad: 'progressive',
                        prevArrow: '.b-image-block__slider-button--prev',
                        nextArrow: '.b-image-block__slider-button--next'
                    });
                });

                $('.js-product-certificates-link').fancybox({
                    openEffect: 'elastic',
                    closeEffect : 'elastic',
                    prevEffect: 'fade',
                    nextEffect: 'fade'
                });
            });
        })
        .on('click', '.b-image-block__slide', function(event) {
            event.preventDefault();
            $(event.currentTarget).fancybox({
                type: 'image'
            });
        });

    $('.js-product-popup')
        .on('click', '.b-image-block__slide', function(event) {
            if ($(this).hasClass('js-product-video-link')) return;
            event.preventDefault();

            var href = $(this).attr('href'),
                href2= $(this).attr('data-src');

            $(this).parents('.b-image-block').find('.b-image-block__bigimage').attr('src', href);
            $(this)
                .parents('.b-image-block')
                .find('.b-image-block__bigimage-link')
                .css('backgroundImage', 'url("'+ href +'")')
                .end()
                .attr('href', href2);
        })
        .on('click', '.js-product-video-link', function(event) {
            event.preventDefault();
            $.fancybox({
                'padding'       : 0,
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'title'         : this.title,
                'width'         : 680,
                'height'        : 495,
                'href'          : this.href,
                'type'          : 'swf',
                'swf'           : {
                     'wmode'        : 'transparent',
                    'allowfullscreen'   : 'true'
                }
            });
        })
        .on('click', '.b-image-block__bigimage-link', function(event) {
            event.preventDefault();
            $.fancybox({
                'padding'       : 0,
                'autoScale'     : false,
                'transitionIn'  : 'none',
                'transitionOut' : 'none',
                'title'         : this.title,
                'width'         : 680,
                'height'        : 495,
                'href'          : this.href,
                'type'          : 'image',
            }); 
        });

});