
$(document).ready(function(){

    $(document)
        .on('input', '.js-qty-input', function(e) {
            e.preventDefault();
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input');
                $(this).val(parseInt($(this).val()));

            if ($boxes_input && $boxes_input.length) {
                var _qty_value = parseInt($qty_input.val() || 0)
                    _inbox = parseInt($qty_input.data('inbox'));
                    
                $boxes_input.val(Math.floor(_qty_value / _inbox));
            }
        })
        .on('change', '.js-qty-input', function(e) {
            var qty = $(this).val();
            var price = $(this).attr('data-price');
            var summ = qty * price;
            $(this).parents('.ps-products__item').find('.js-product-summ').text(summ > 0 ? summ.toFixed(2) : '-');
        })
        .on('keypress', '.js-boxes-input', function(e) {
            if (e.keyCode == 44 || e.keyCode == 46) {
                e.preventDefault();
            }
        })
        .on('input', '.js-boxes-input', function(e, a, b) {
            e.preventDefault();
            $(this).val(parseInt($(this).val()));
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input'),
                _inbox = parseInt($qty_input.data('inbox')),
                _boxes = parseInt($boxes_input.val() || 0);

                $qty_input.val(_inbox * _boxes);
        })
        // The button to increment the product value
        .on('click', '.ps-quantity__button--inc', function(e){
            e.preventDefault();
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input');

            if ($qty_input && $qty_input.val()) {
                var qty_value = parseInt($qty_input.val() || 0);
                $qty_input.val(qty_value + 1);
            }

            if ($boxes_input && $boxes_input.length) {
                var _qty_value = parseInt($qty_input.val() || 0)
                    _inbox = parseInt($qty_input.data('inbox'));
                $boxes_input.val(Math.floor(_qty_value / _inbox));
            }
            $qty_input.trigger('change');
        })
        .on('click', '.ps-quantity__button--incbox', function(e){
            e.preventDefault();
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input');

            if ($boxes_input && $boxes_input.length) {
                $boxes_input.val(parseInt($boxes_input.val() || 0) + 1);
            }
            if ($qty_input && $qty_input.length) {
                var _qty_value = parseInt($qty_input.val() || 0)
                    _inbox = parseInt($qty_input.data('inbox'))
                $qty_input.val(_qty_value + _inbox);
            }
            $qty_input.trigger('change');
        })
        // The button to decrement the product value
        .on('click', '.ps-quantity__button--dec', function(e){
            e.preventDefault();
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input');

            if ($qty_input && $qty_input.val()) {
                var qty_value = parseInt($qty_input.val() || 0);
                (qty_value > 0) && $qty_input.val(qty_value - 1);
            }

            if ($boxes_input && $boxes_input.length) {
                var _qty_value = parseInt($qty_input.val() || 0)
                    _inbox = parseInt($qty_input.data('inbox'));
                $boxes_input.val(Math.floor(_qty_value / _inbox));
            }
            $qty_input.trigger('change');
        })
        .on('click', '.ps-quantity__button--decbox', function(e){
            e.preventDefault();
            var
                $controls    = $(this).parents('.ps-product__controls'),
                $boxes_input = $controls.find('.js-boxes-input'),
                $qty_input   = $controls.find('.js-qty-input');

            if ($qty_input && $qty_input.length) {
                var _qty_value = parseInt($qty_input.val() || 0)
                    _inbox = parseInt($qty_input.data('inbox'));
                ($boxes_input.val() > 0 && _qty_value >= _inbox) && $qty_input.val(_qty_value - _inbox);
            }
            if ($boxes_input && $boxes_input.length) {
                ($boxes_input.val() > 0) && $boxes_input.val(parseInt($boxes_input.val() || 0) - 1);
            }
            $qty_input.trigger('change');
        })
        .on('focus', '.ps-quantity__value', function(event) {
            $(this).parents('.ps-quantity').addClass('ps-quantity--focus');
        })
        .on('blur', '.ps-quantity__value', function(event) {
            $(this).parents('.ps-quantity').removeClass('ps-quantity--focus');
        });

    ajaxCart.overrideButtonsInThePage();

    $(document).on('click', '.block_cart_collapse', function(e){
        e.preventDefault();
        ajaxCart.collapse();
    });
    $(document).on('click', '.block_cart_expand', function(e){
        e.preventDefault();
        ajaxCart.expand();
    });

    var current_timestamp = parseInt(new Date().getTime() / 1000);

    if (typeof $('.ajax_cart_quantity').html() == 'undefined' || (typeof generated_date != 'undefined' && generated_date != null && (parseInt(generated_date) + 30) < current_timestamp))
        ajaxCart.refresh();

    /* roll over cart */
    var cart_block = new HoverWatcher('#header .cart_block');
    var shopping_cart = new HoverWatcher('#header .shopping_cart');
    var is_touch_enabled = false;

    if ('ontouchstart' in document.documentElement)
        is_touch_enabled = true;

    // $(document).on('click', '#header .shopping_cart > a:first', function(e){
    //  e.preventDefault();
    //  e.stopPropagation();

    //  // Simulate hover when browser says device is touch based
    //  if (is_touch_enabled)
    //  {
    //      if ($(this).next('.cart_block:visible').length && !cart_block.isHoveringOver())
    //          $("#header .cart_block").stop(true, true).slideUp(450);
    //      else if (ajaxCart.nb_total_products > 0 || parseInt($('.ajax_cart_quantity').html()) > 0)
    //          $("#header .cart_block").stop(true, true).slideDown(450);
    //      return;
    //  }
    //  else
    //      window.location.href = $(this).attr('href');
    // });

    $("#header .shopping_cart a:first").click(function() {
        if (ajaxCart.nb_total_products <= 0) return false;
    });

    $("#header .shopping_cart a:first").hover(
        function(){
            if (ajaxCart.nb_total_products > 0 || parseInt($('.ajax_cart_quantity').html()) > 0)
                $("#header .cart_block").stop(true, true).slideDown(450);
        },
        function(){
            setTimeout(function(){
                if (!shopping_cart.isHoveringOver() && !cart_block.isHoveringOver())
                    $("#header .cart_block").stop(true, true).slideUp(450);
            }, 200);
        }
    );

    $("#header .cart_block").hover(
        function(){
        },
        function(){
            setTimeout(function(){
                if (!shopping_cart.isHoveringOver())
                    $("#header .cart_block").stop(true, true).slideUp(450);
            }, 200);
        }
    );

    $(document).on('click', '.delete_voucher', function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            async: true,
            cache: false,
            url:$(this).attr('href') + '?rand=' + new Date().getTime()
        });
        $(this).parent().parent().remove();
        ajaxCart.refresh();
        if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')
        {
            if (typeof(updateAddressSelection) != 'undefined')
                updateAddressSelection();
            else
                location.reload();
        }
        
    });

    $(document).on('click', '#cart_navigation input', function(e){
        $(this).prop('disabled', 'disabled').addClass('disabled');
        $(this).closest("form").get(0).submit();
    });

    $(document).on('click', '#layer_cart .cross, #layer_cart .continue, .layer_cart_overlay', function(e){
        e.preventDefault();
        $('.layer_cart_overlay').hide();
        $('#layer_cart').fadeOut('fast');
    });

    $('#columns #layer_cart, #columns .layer_cart_overlay').detach().prependTo('#columns');
});

//JS Object : update the cart by ajax actions
var ajaxCart = {
    nb_total_products: 0,
    //override every button in the page in relation to the cart
    overrideButtonsInThePage : function(){


        //for every 'add' buttons...
        $(document).off('click', '.ajax_add_to_cart_button').on('click', '.ajax_add_to_cart_button', function(e){
            e.preventDefault();
            var idProduct =  parseInt($(this).data('id-product'));
            var idProductAttribute =  parseInt($(this).data('id-product-attribute'));

      var minimalQuantity = $(this).parent().parent().parent().parent().find('#quantity_wanted_p input').val()

            if (!minimalQuantity){
        minimalQuantity =  parseInt($(this).data('minimal_quantity'));
      }
            if (!minimalQuantity)
                minimalQuantity = 1;
            if ($(this).prop('disabled') != 'disabled')
                ajaxCart.add(idProduct, idProductAttribute, false, this, minimalQuantity);
        });
        //for product page 'add' button...
        if ($('.cart_block').length) {
            $(document).off('click', '#add_to_cart button').on('click', '#add_to_cart button', function(e){
                e.preventDefault();
                ajaxCart.add($('#product_page_product_id').val(), $('#idCombination').val(), true, null, $('#quantity_wanted').val(), null);
            });
        }

        //for 'delete' buttons in the cart block...
        $(document).off('click', '.cart_block_list .ajax_cart_block_remove_link').on('click', '.cart_block_list .ajax_cart_block_remove_link', function(e){
            e.preventDefault();
            // Customized product management
            var customizationId = 0;
            var productId = 0;
            var productAttributeId = 0;
            var customizableProductDiv = $($(this).parent().parent()).find("div[data-id^=deleteCustomizableProduct_]");
            var idAddressDelivery = false;

            if (customizableProductDiv && $(customizableProductDiv).length)
            {
                var ids = customizableProductDiv.data('id').split('_');
                if (typeof(ids[1]) != 'undefined')
                {
                    customizationId = parseInt(ids[1]);
                    productId = parseInt(ids[2]);
                    if (typeof(ids[3]) != 'undefined')
                        productAttributeId = parseInt(ids[3]);
                    if (typeof(ids[4]) != 'undefined')
                        idAddressDelivery = parseInt(ids[4]);
                }
            }

            // Common product management
            if (!customizationId)
            {
                //retrieve idProduct and idCombination from the displayed product in the block cart
                var firstCut = $(this).parent().parent().data('id').replace('cart_block_product_', '');
                firstCut = firstCut.replace('deleteCustomizableProduct_', '');
                ids = firstCut.split('_');
                productId = parseInt(ids[0]);

                if (typeof(ids[1]) != 'undefined')
                    productAttributeId = parseInt(ids[1]);
                if (typeof(ids[2]) != 'undefined')
                    idAddressDelivery = parseInt(ids[2]);
            }

            // Removing product from the cart
            ajaxCart.remove(productId, productAttributeId, customizationId, idAddressDelivery);
            $('.ps-product__table [data-product-id='+ productId +']').removeClass('ps-product--incard');
            //console.log("removed"+productId);
            //testfunc();
        });
    },

    // try to expand the cart
    expand : function(){
        if ($('.cart_block_list').hasClass('collapsed'))
        {
            $('.cart_block_list.collapsed').slideDown({
                duration: 450,
                complete: function(){
                    $(this).parent().show(); // parent is hidden in global.js::accordion()
                    $(this).addClass('expanded').removeClass('collapsed');
                }
            });

            // save the expand statut in the user cookie
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                data: 'ajax_blockcart_display=expand',
                complete: function(){
                    $('.block_cart_expand').fadeOut('fast', function(){
                        $('.block_cart_collapse').fadeIn('fast');
                    });
                }
            });
        }
    },

    // try to collapse the cart
    collapse : function(){
        if ($('.cart_block_list').hasClass('expanded'))
        {
            $('.cart_block_list.expanded').slideUp('slow', function(){
                $(this).addClass('collapsed').removeClass('expanded');
            });

            // save the expand statut in the user cookie
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                data: 'ajax_blockcart_display=collapse' + '&rand=' + new Date().getTime(),
                complete: function(){
                    $('.block_cart_collapse').fadeOut('fast', function(){
                        $('.block_cart_expand').fadeIn('fast');
                    });
                }
            });
        }
    },
    // Fix display when using back and previous browsers buttons
    refresh : function(){
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: (typeof(baseUri) !== 'undefined') ? baseUri + '?rand=' + new Date().getTime() : '',
            async: true,
            cache: false,
            dataType : "json",
            data: (typeof(static_token) !== 'undefined') ? 'controller=cart&ajax=true&token=' + static_token : '',
            success: function(jsonData)
            {
                ajaxCart.updateCart(jsonData);
            }
        });
        
    },

    // Update the cart information
    updateCartInformation : function (jsonData, addedFromProductPage){
        ajaxCart.updateCart(jsonData);
        //reactive the button when adding has finished
        if (addedFromProductPage)
        {
            $('#add_to_cart button').removeProp('disabled').removeClass('disabled');
            if (!jsonData.hasError || jsonData.hasError == false)
                $('#add_to_cart button').addClass('added');
            else
                $('#add_to_cart button').removeClass('added');
        }
        else
            $('.ajax_add_to_cart_button').removeProp('disabled');

    },
    // close fancybox
    updateFancyBox : function (){},
    // add a product in the cart via ajax
    add : function(idProduct, idCombination, addedFromProductPage, callerElement, quantity, whishlist){

        if (addedFromProductPage && !checkCustomizations())
        {
            if (contentOnly)
            {
                var productUrl = window.document.location.href + '';
                var data = productUrl.replace('content_only=1', '');
                window.parent.document.location.href = data;
                return;
            }
            if (!!$.prototype.fancybox)
                $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + fieldRequired + '</p>'
                    }
                ], {
                    padding: 0
                });
            else
                alert(fieldRequired);
            return;
        }

        //disabled the button when adding to not double add if user double click
        if (addedFromProductPage)
        {
            $('#add_to_cart button').prop('disabled', 'disabled').addClass('disabled');
            $('.filled').removeClass('filled');
        }
        else
            $(callerElement).prop('disabled', 'disabled');

        if ($('.cart_block_list').hasClass('collapsed'))
            this.expand();
        //send the ajax request to the server

        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'controller=cart&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + idProduct + '&token=' + static_token + ( (parseInt(idCombination) && idCombination != null) ? '&ipa=' + parseInt(idCombination): '' + '&id_customization=' + ((typeof customizationId !== 'undefined') ? customizationId : 0)),
            success: function(jsonData,textStatus,jqXHR)
            {
                // add appliance to whishlist module
                if (whishlist && !jsonData.errors)
                    WishlistAddProductCart(whishlist[0], idProduct, idCombination, whishlist[1]);

                if (!jsonData.hasError)
                {
                    if (contentOnly)
                        window.parent.ajaxCart.updateCartInformation(jsonData, addedFromProductPage);
                    else
                        ajaxCart.updateCartInformation(jsonData, addedFromProductPage);

                    if (jsonData.crossSelling)
                        $('.crossseling').html(jsonData.crossSelling);

                    if (idCombination)
                        $(jsonData.products).each(function(){
                            if (this.id != undefined && this.id == parseInt(idProduct) && this.idCombination == parseInt(idCombination))
                                if (contentOnly)
                                    window.parent.ajaxCart.updateLayer(this);
                                else
                                    ajaxCart.updateLayer(this);
                        });
                    else
                        $(jsonData.products).each(function(){
                            if (this.id != undefined && this.id == parseInt(idProduct))
                                if (contentOnly)
                                    window.parent.ajaxCart.updateLayer(this);
                                else
                                    ajaxCart.updateLayer(this);
                        });
                    if (contentOnly)
                        parent.$.fancybox.close();
                }
                else
                {
                    if (contentOnly)
                        window.parent.ajaxCart.updateCart(jsonData);
                    else
                        ajaxCart.updateCart(jsonData);
                    if (addedFromProductPage)
                        $('#add_to_cart button').removeProp('disabled').removeClass('disabled');
                    else
                        $(callerElement).removeProp('disabled');
                }

                emptyCustomizations();

            },
            error: function(XMLHttpRequest, textStatus, errorThrown)
            {
                var error = "Impossible to add the product to the cart.<br/>textStatus: '" + textStatus + "'<br/>errorThrown: '" + errorThrown + "'<br/>responseText:<br/>" + XMLHttpRequest.responseText;
                if (!!$.prototype.fancybox)
                    $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + error + '</p>'
                    }],
                    {
                        padding: 0
                    });
                else
                    alert(error);
                //reactive the button when adding has finished
                if (addedFromProductPage)
                    $('#add_to_cart button').removeProp('disabled').removeClass('disabled');
                else
                    $(callerElement).removeProp('disabled');
            }
        });
    },

    //remove a product from the cart via ajax
    remove : function(idProduct, idCombination, customizationId, idAddressDelivery, count){
        //send the ajax request to the server
        var qty='';
        if(count && count != 'undefined' && count != null){
            qty = '&qty='+count;
        }
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType : "json",
            data: 'controller=cart&delete=1&id_product=' + idProduct + qty+'&ipa=' + ((idCombination != null && parseInt(idCombination)) ? idCombination : '') + ((customizationId && customizationId != null) ? '&id_customization=' + customizationId : '') + '&id_address_delivery=' + idAddressDelivery + '&token=' + static_token + '&ajax=true',
            success: function(jsonData) {
                ajaxCart.updateCart(jsonData);
                if ($('body').attr('id') == 'order' || $('body').attr('id') == 'order-opc')
                    deleteProductFromSummary(idProduct+'_'+idCombination+'_'+customizationId+'_'+idAddressDelivery);
            },
            error: function()
            {
                var error = 'ERROR: unable to delete the product';
                if (!!$.prototype.fancybox)
                {
                    $.fancybox.open([
                        {
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: error
                        }
                    ], {
                        padding: 0
                    });
                }
                else
                    alert(error);
            }
        });
    },

    //hide the products displayed in the page but no more in the json data
    hideOldProducts : function(jsonData){
        //delete an eventually removed product of the displayed cart (only if cart is not empty!)
        if ($('.cart_block_list:first dl.products').length > 0)
        {
            var removedProductId = null;
            var removedProductData = null;
            var removedProductDomId = null;
            //look for a product to delete...
            $('.cart_block_list:first dl.products dt').each(function(){
                //retrieve idProduct and idCombination from the displayed product in the block cart
                var domIdProduct = $(this).data('id');
                var firstCut = domIdProduct.replace('cart_block_product_', '');
                var ids = firstCut.split('_');

                //try to know if the current product is still in the new list
                var stayInTheCart = false;
                for (aProduct in jsonData.products)
                {
                    //we've called the variable aProduct because IE6 bug if this variable is called product
                    //if product has attributes
                    if (jsonData.products[aProduct]['id'] == ids[0] && (!ids[1] || jsonData.products[aProduct]['idCombination'] == ids[1]))
                    {
                        stayInTheCart = true;
                        // update the product customization display (when the product is still in the cart)
                        ajaxCart.hideOldProductCustomizations(jsonData.products[aProduct], domIdProduct);
                    }
                }
                //remove product if it's no more in the cart
                if (!stayInTheCart)
                {
                    removedProductId = $(this).data('id');
                    if (removedProductId != null)
                    {
                        var firstCut =  removedProductId.replace('cart_block_product_', '');
                        var ids = firstCut.split('_');

                        $('dt[data-id="' + removedProductId + '"]').addClass('strike').fadeTo('slow', 0, function(){
                            $(this).slideUp('slow', function(){
                                $(this).remove();
                                // If the cart is now empty, show the 'no product in the cart' message and close detail
                                if($('.cart_block:first dl.products dt').length == 0)
                                {
                                    $('.ajax_cart_quantity').html('0');
                                    $("#header .cart_block").stop(true, true).slideUp(200);
                                    $('.cart_block_no_products:hidden').slideDown(450);
                                    $('.cart_block dl.products').remove();
                                }
                            });
                        });
                        $('dd[data-id="cart_block_combination_of_' + ids[0] + (ids[1] ? '_'+ids[1] : '') + (ids[2] ? '_'+ids[2] : '') + '"]').fadeTo('fast', 0, function(){
                            $(this).slideUp('fast', function(){
                                $(this).remove();
                            });
                        });
                    }
                }
            });
        }
    },

    hideOldProductCustomizations : function (product, domIdProduct){
        var customizationList = $('ul[data-id="customization_' + product['id'] + '_' + product['idCombination'] + '"]');
        if(customizationList.length > 0)
        {
            $(customizationList).find("li").each(function(){
                $(this).find("div").each(function(){
                    var customizationDiv = $(this).data('id');
                    var tmp = customizationDiv.replace('deleteCustomizableProduct_', '');
                    var ids = tmp.split('_');
                    if ((parseInt(product.idCombination) == parseInt(ids[2])) && !ajaxCart.doesCustomizationStillExist(product, ids[0]))
                        $('div[data-id="' + customizationDiv + '"]').parent().addClass('strike').fadeTo('slow', 0, function(){
                            $(this).slideUp('slow');
                            $(this).remove();
                        });
                });
            });
        }

        var removeLinks = $('.deleteCustomizableProduct[data-id="' + domIdProduct + '"]').find('.ajax_cart_block_remove_link');
        if (!product.hasCustomizedDatas && !removeLinks.length)
            $('div[data-id="' + domIdProduct + '"]' + ' span.remove_link').html('<a class="ajax_cart_block_remove_link" rel="nofollow" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + product['id'] + '&amp;ipa=' + product['idCombination'] + '&amp;token=' + static_token + '">&nbsp;x</a>');
        if (product.is_gift)
            $('div[data-id="' + domIdProduct + '"]' + ' span.remove_link').html('');
    },

    doesCustomizationStillExist : function (product, customizationId){
        var exists = false;

        $(product.customizedDatas).each(function(){
            if (this.customizationId == customizationId)
            {
                exists = true;
                // This return does not mean that we found nothing but simply break the loop
                return false;
            }
        });
        return (exists);
    },

    //refresh display of vouchers (needed for vouchers in % of the total)
    refreshVouchers : function (jsonData){
        if (typeof(jsonData.discounts) == 'undefined' || jsonData.discounts.length == 0)
            $('.vouchers').hide();
        else
        {
            $('.vouchers tbody').html('');

            for (i=0;i<jsonData.discounts.length;i++)
            {
                if (parseFloat(jsonData.discounts[i].price_float) > 0)
                {
                    var delete_link = '';
                    if (jsonData.discounts[i].code.length)
                        delete_link = '<a class="delete_voucher" href="'+jsonData.discounts[i].link+'" title="'+delete_txt+'"><i class="icon-remove-sign"></i></a>';
                    $('.vouchers tbody').append($(
                        '<tr class="bloc_cart_voucher" data-id="bloc_cart_voucher_'+jsonData.discounts[i].id+'">'
                        +'  <td class="quantity">1x</td>'
                        +'  <td class="name" title="'+jsonData.discounts[i].description+'">'+jsonData.discounts[i].name+'</td>'
                        +'  <td class="price">-'+jsonData.discounts[i].price+'</td>'
                        +'  <td class="delete">' + delete_link + '</td>'
                        +'</tr>'
                    ));
                }
            }
            $('.vouchers').show();
        }

    },

    // Update product quantity
    updateProductQuantity : function (product, quantity){
        $('dt[data-id=cart_block_product_' + product.id + '_' + (product.idCombination ? product.idCombination : '0')+ '_' + (product.idAddressDelivery ? product.idAddressDelivery : '0') + '] .quantity').fadeTo('fast', 0, function(){
            $(this).text(quantity);
            $(this).fadeTo('fast', 1, function(){
                $(this).fadeTo('fast', 0, function(){
                    $(this).fadeTo('fast', 1, function(){
                        $(this).fadeTo('fast', 0, function(){
                            $(this).fadeTo('fast', 1);
                        });
                    });
                });
            });
        });
    },

    //display the products witch are in json data but not already displayed
    displayNewProducts : function(jsonData){
        //add every new products or update displaying of every updated products
        
        $(jsonData.products).each(function(){
            //fix ie6 bug (one more item 'undefined' in IE6)
            if (this.id != undefined)
            {
                //create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)
                
                if ($('.cart_block:first dl.products').length == 0)
                {
                    $('.cart_block_no_products').before('<dl class="products"></dl>');
                    $('.cart_block_no_products').hide();
                }
                //if product is not in the displayed cart, add a new product's line
                var domIdProduct = this.id + '_' + (this.idCombination ? this.idCombination : '0') + '_' + (this.idAddressDelivery ? this.idAddressDelivery : '0');
                var domIdProductAttribute = this.id + '_' + (this.idCombination ? this.idCombination : '0');
                
                if ($('dt[data-id="cart_block_product_' + domIdProduct + '"]').length == 0)
                {
                    var productId = parseInt(this.id);
                    //var categoryId = parseInt(this.id);
                    var productAttributeId = (this.hasAttributes ? parseInt(this.attributes) : 0);
                    var content =  '<dt class="unvisible" data-id="cart_block_product_' + domIdProduct + '">';
                    var name = $.trim($('<span />').html(this.name).text());
                    name = (name.length > 12 ? name.substring(0, 10) + '...' : name);
                    content += '<a class="cart-images" href="' + this.link + '" title="' + name + '"><img  src="' + this.image_cart + '" alt="' + this.name +'"></a>';
                    content += '<div class="cart-info"><div class="product-name">' + '<span class="quantity-formated"><span class="quantity">' + this.quantity + '</span>&nbsp;x&nbsp;</span><a href="' + this.link + '" title="' + this.name + '" class="cart_block_product_name">' + name + '</a></div>';
                    if (this.hasAttributes)
                          content += '<div class="product-atributes"><a href="' + this.link + '" title="' + this.name + '">' + this.attributes + '</a></div>';
                    if (typeof(freeProductTranslation) != 'undefined'){
                        var q1 = document.getElementById("btnid"+productId);//maxim
                        var idcategorydefault = q1.getAttribute("btncatid");//maxim
                        //var this->idcategorydefault = idcategorydefault;
                        myPrice = this.priceByLine.replace(' руб', '');//maxim
                        myPrice = myPrice.replace(' ', '');//maxim
                        //console.log('price'+myPrice);
                        content += '<span class="price" cartprice="'+(parseFloat(this.price_float) > 0 ? myPrice.replace(',', '.') : freeProductTranslation)+'" cartcatid="'+idcategorydefault+'" >' + (parseFloat(this.price_float) > 0 ? this.price_float : freeProductTranslation) + '</span></div>';//this.priceByLine.replace(',', '.') maxim
                    }
                    if (typeof(this.is_gift) == 'undefined' || this.is_gift == 0)
                        content += '<span class="remove_link"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;token=' + static_token + (this.hasAttributes ? '&amp;ipa=' + parseInt(this.idCombination) : '') + '" >&nbsp;x</a></span>';
                    else
                        content += '<span class="remove_link"></span>';
                    content += '</dt>';
                    if (this.hasAttributes)
                        content += '<dd data-id="cart_block_combination_of_' + domIdProduct + '" class="unvisible">';
                    if (this.hasCustomizedDatas)
                        content += ajaxCart.displayNewCustomizedDatas(this);
                    if (this.hasAttributes) content += '</dd>';

                    $('.cart_block dl.products').append(content);
                }
                //else update the product's line
                else
                {
                    var jsonProduct = this;
                    if($.trim($('dt[data-id="cart_block_product_' + domIdProduct + '"] .quantity').html()) != jsonProduct.quantity || $.trim($('dt[data-id="cart_block_product_' + domIdProduct + '"] .price').html()) != jsonProduct.priceByLine)
                    {
                        // Usual product
                        if (!this.is_gift)
                            $('dt[data-id="cart_block_product_' + domIdProduct + '"] .price').html(jsonProduct.price_float);
                        else
                            $('dt[data-id="cart_block_product_' + domIdProduct + '"] .price').html(freeProductTranslation);
                        ajaxCart.updateProductQuantity(jsonProduct, jsonProduct.quantity);

                        // Customized product
                        if (jsonProduct.hasCustomizedDatas)
                        {
                            customizationFormatedDatas = ajaxCart.displayNewCustomizedDatas(jsonProduct);
                            if (!$('ul[data-id="customization_' + domIdProductAttribute + '"]').length)
                            {
                                if (jsonProduct.hasAttributes)
                                    $('dd[data-id="cart_block_combination_of_' + domIdProduct + '"]').append(customizationFormatedDatas);
                                else
                                    $('.cart_block dl.products').append(customizationFormatedDatas);
                            }
                            else
                            {
                                $('ul[data-id="customization_' + domIdProductAttribute + '"]').html('');
                                $('ul[data-id="customization_' + domIdProductAttribute + '"]').append(customizationFormatedDatas);
                            }
                        }
                    }
                }
                $('.cart_block dl.products .unvisible').slideDown(450).removeClass('unvisible');

            var removeLinks = $('dt[data-id="cart_block_product_' + domIdProduct + '"]').find('a.ajax_cart_block_remove_link');
            if (this.hasCustomizedDatas && removeLinks.length)
                $(removeLinks).each(function(){
                    $(this).remove();
                });
            }
            

            
            
            
        });
        
    },

    displayNewCustomizedDatas : function(product){
        var content = '';
        var productId = parseInt(product.id);
        var productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);
        var hasAlreadyCustomizations = $('ul[data-id="customization_' + productId + '_' + productAttributeId + '"]').length;

        if (!hasAlreadyCustomizations)
        {
            if (!product.hasAttributes)
                content += '<dd data-id="cart_block_combination_of_' + productId + '" class="unvisible">';
            if ($('ul[data-id="customization_' + productId + '_' + productAttributeId + '"]').val() == undefined)
                content += '<ul class="cart_block_customizations" data-id="customization_' + productId + '_' + productAttributeId + '">';
        }

        $(product.customizedDatas).each(function(){
            var done = 0;
            customizationId = parseInt(this.customizationId);
            productAttributeId = typeof(product.idCombination) == 'undefined' ? 0 : parseInt(product.idCombination);
            content += '<li name="customization"><div class="deleteCustomizableProduct" data-id="deleteCustomizableProduct_' + customizationId + '_' + productId + '_' + (productAttributeId ?  productAttributeId : '0') + '"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;ipa=' + productAttributeId + '&amp;id_customization=' + customizationId + '&amp;token=' + static_token + '">&nbsp;x</a></div>';

            // Give to the customized product the first textfield value as name
            $(this.datas).each(function(){
                if (this['type'] == CUSTOMIZE_TEXTFIELD)
                {
                    $(this.datas).each(function(){
                        if (this['index'] == 0)
                        {
                            content += ' ' + this.truncatedValue.replace(/<br \/>/g, ' ');
                            done = 1;
                            return false;
                        }
                    })
                }
            });

            // If the customized product did not have any textfield, it will have the customizationId as name
            if (!done)
                content += customizationIdMessage + customizationId;
            if (!hasAlreadyCustomizations) content += '</li>';
            // Field cleaning
            if (customizationId)
            {
                $('#uploadable_files li div.customizationUploadBrowse img').remove();
                $('#text_fields input').attr('value', '');
            }
        });

        if (!hasAlreadyCustomizations)
        {
            content += '</ul>';
            if (!product.hasAttributes) content += '</dd>';
        }
        return (content);
    },

    updateLayer : function(product){
        $('#layer_cart_product_title').text(product.name);
        $('#layer_cart_product_attributes').text('');
        if (product.hasAttributes && product.hasAttributes == true)
            $('#layer_cart_product_attributes').html(product.attributes);
        $('#layer_cart_product_price').text(product.price);
        $('#layer_cart_product_quantity').text(product.quantity);
        $('.layer_cart_img').html('<img class="layer_cart_img img-responsive" src="' + product.image + '" alt="' + product.name + '" title="' + product.name + '" />');

        var n = parseInt($(window).scrollTop()) + 'px';

        $('.layer_cart_overlay').css('width','100%');
        $('.layer_cart_overlay').css('height','100%');
        $('.layer_cart_overlay').show();
        $('#layer_cart').css({'top': n}).fadeIn('fast');
        crossselling_serialScroll();
        
    },

    //genarally update the display of the cart
    updateCart : function(jsonData){
        //user errors display
        if (jsonData.hasError)
        {
            var errors = '';
            for (error in jsonData.errors)
                //IE6 bug fix
                if (error != 'indexOf')
                    errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
            if (!!$.prototype.fancybox)
                $.fancybox.open([
                    {
                        type: 'inline',
                        autoScale: true,
                        minHeight: 30,
                        content: '<p class="fancybox-error">' + errors + '</p>'
                    }
                ], {
                    padding: 0
                });
            else
                alert(errors);
        }
        else
        {
            ajaxCart.updateCartEverywhere(jsonData);
            ajaxCart.hideOldProducts(jsonData);
            ajaxCart.displayNewProducts(jsonData);
            ajaxCart.refreshVouchers(jsonData);

            //update 'first' and 'last' item classes
            $('.cart_block .products dt').removeClass('first_item').removeClass('last_item').removeClass('item');
            $('.cart_block .products dt:first').addClass('first_item');
            $('.cart_block .products dt:not(:first,:last)').addClass('item');
            $('.cart_block .products dt:last').addClass('last_item');
        }
    },

    //update general cart informations everywhere in the page
    updateCartEverywhere : function(jsonData){

        $('.ajax_cart_total').text($.trim(jsonData.productTotal));

        if (typeof hasDeliveryAddress == 'undefined')
            hasDeliveryAddress = false;

        if (parseFloat(jsonData.shippingCostFloat) > 0)
            $('.ajax_cart_shipping_cost').text(jsonData.shippingCost).parent().find('.unvisible').show();
        else if ((hasDeliveryAddress || typeof(orderProcess) !== 'undefined' && orderProcess == 'order-opc') && typeof(freeShippingTranslation) != 'undefined')
            $('.ajax_cart_shipping_cost').html(freeShippingTranslation);
        else if ((typeof toBeDetermined !== 'undefined') && !hasDeliveryAddress)
            $('.ajax_cart_shipping_cost').html(toBeDetermined);

        if (!jsonData.shippingCostFloat && !jsonData.free_ship)
            $('.ajax_cart_shipping_cost').parent().find('.unvisible').hide();
        else if (hasDeliveryAddress && !jsonData.isVirtualCart)
            $('.ajax_cart_shipping_cost').parent().find('.unvisible').show();

        $('.ajax_cart_tax_cost').text(jsonData.taxCost);
        $('.cart_block_wrapping_cost').text(jsonData.wrappingCost);
        $('.ajax_block_cart_total').html(jsonData.total);
        $('.ajax_block_products_total').text(jsonData.productTotal);
        $('.ajax_total_price_wt').text(jsonData.total_price_wt);
        $('.ajax_cart_prod_num').text(jsonData.products.length);
        //$('.ajax_cart_prod_IdCat').text(jsonData.products.length);
        console.log(jsonData);
        //console.log(jsonData.products[0].id);
        //console.log(jsonData->$products);
        //_localStorage["jsonData"] ='';
        //_localStorage["jsonData"] = jsonData.products; 
        
        // Обновляем продукты
        //Вначале установим все на 0 по умолчанию. Нужно при удалении
        $('.ajax_block_cart_total_price_to_null').text('0,00 '+currencySign);
        $('.ajax_block_cart_total_price_to_null2').text('');

        $('.ajax_block_cart_total_count_to_null').text('0');

        $('.ajax_input_prod_to_null').val(0);
        $('.ajax_input_prod_to_null').attr('data-prev-val', 0);
        $('.ajax_table_tr_bg_null').removeClass('isInCart');

        jsonData.products.forEach(function (item, i, items) {
            $('.ajax_block_cart_total_price_id_'+item.id).html(item.price);
            $('.ajax_block_cart_total_price2_id_'+item.id).html(item.price_float || '-');
            $('.js-product-count-' + item.id).text(item.quantity);
            
            if (parseFloat(item.price) > 0) {
                $('.ajax_block_cart_total_price2_id_'+item.id).parents('.ps-products__item').addClass('ps-product--incard');
            } else {
                $('.ajax_block_cart_total_price2_id_'+item.id).parents('.ps-products__item').removeClass('ps-product--incard');
            }

            $('.ajax_block_cart_count_id_'+item.id).text(item.quantity);

            $('.ajax_input_prod_'+item.id).val(item.quantity);
            var _inbox = parseInt($('.ajax_box_input_prod_'+item.id).attr('data-inbox'));
            $('.ajax_box_input_prod_'+item.id).val(Math.floor(item.quantity / _inbox));
            $('.ajax_input_prod_'+item.id).attr('data-prev-val', item.quantity);

            $('.ajax_table_tr_bg_'+item.id).addClass('isInCart');

        });

        if (parseFloat(jsonData.freeShippingFloat) > 0)
        {
            $('.ajax_cart_free_shipping').html(jsonData.freeShipping);
            $('.freeshipping').fadeIn(0);
        }
        else if (parseFloat(jsonData.freeShippingFloat) == 0)
            $('.freeshipping').fadeOut(0);

        this.nb_total_products = jsonData.nbTotalProducts;

        if (parseInt(jsonData.nbTotalProducts) > 0)
        {
            $('.ajax_cart_no_product').hide();
            $('.ajax_cart_quantity').text(jsonData.nbTotalProducts);
            $('.ajax_cart_quantity').fadeIn('slow');
            $('.ajax_cart_total').fadeIn('slow');

            if (parseInt(jsonData.nbTotalProducts) > 1)
            {
                $('.ajax_cart_product_txt').each( function (){
                    $(this).hide();
                });

                $('.ajax_cart_product_txt_s').each( function (){
                    $(this).show();
                });
            }
            else
            {
                $('.ajax_cart_product_txt').each( function (){
                    $(this).show();
                });

                $('.ajax_cart_product_txt_s').each( function (){
                    $(this).hide();
                });
            }
        }
        else
        {
            $('.ajax_cart_quantity, .ajax_cart_product_txt_s, .ajax_cart_product_txt, .ajax_cart_total').each(function(){
                $(this).hide();
            });
            $('.ajax_cart_no_product').show('slow');
        }
    }
    
};

function HoverWatcher(selector)
{
    this.hovering = false;
    var self = this;

    this.isHoveringOver = function(){
        return self.hovering;
    }

    $(selector).hover(function(){
        self.hovering = true;
    }, function(){
        self.hovering = false;
    })
}

function crossselling_serialScroll()
{
    if (!!$.prototype.bxSlider)
        $('#blockcart_caroucel').bxSlider({
            minSlides: 2,
            maxSlides: 4,
            slideWidth: 178,
            slideMargin: 20,
            moveSlides: 1,
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false
        });
        
}


// Дополнения
function appAlert(mes){
    if (!!$.prototype.fancybox)
    {
        $.fancybox.open([
            {
                type: 'inline',
                autoScale: true,
                minHeight: 30,
                content: '<p class="fancybox-error">' + mes + '</p>'
            }
        ], {
            padding: 10
        });
    }
    else
        alert(mes);
}

function addOneProduct(idProduct){
    ajaxCart.add(idProduct, 0, false, this, 1);

}

function removeOneProduct(productId){

    var qty = 1;
    var customizationId = 0;
    var productAttributeId = 0;
    var id_address_delivery = 0;


    if (true)
    {
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: 'json',
            data: 'controller=cart'
            + '&ajax=true'
            + '&add=true'
            + '&getproductprice=true'
            + '&summary=true'
            + '&id_product='+productId
            + '&ipa='+productAttributeId
            + '&id_address_delivery='+id_address_delivery
            + '&op=down'
            + ((customizationId !== 0) ? '&id_customization='+customizationId : '')
            + '&qty='+qty
            + '&token='+static_token
            + '&allow_refresh=1',
            success: function(jsonData)
            {
                ajaxCart.updateCart(jsonData);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                var error = 'ERROR: unable to delete the product';
                if (!!$.prototype.fancybox)
                {
                    $.fancybox.open([
                        {
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: error
                        }
                    ], {
                        padding: 0
                    });
                }
                else
                    alert(error);
            }
        });

    }
    
}

function changeOneProduct(productId, el, direct, qty){

    el = $('.'+el);
    var _count = parseInt(el.text());

    if(typeof(qty) == 'undefined' || !qty) qty = 1;
    var customizationId = 0;
    var productAttributeId = 0;
    var id_address_delivery = 0;

    if(typeof(direct) == 'undefined' || direct) {
        direct = '';
    }
    else {
        direct = '&op=down';
        if(_count <= 0) return;
    }
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: baseUri + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType: 'json',
        data: 'controller=cart'
        + '&ajax=true'
        + '&add=true'
        + '&getproductprice=true'
        + '&summary=true'
        + '&id_product='+productId
        + '&ipa='+productAttributeId
        + '&id_address_delivery='+id_address_delivery
        + direct
        + ((customizationId !== 0) ? '&id_customization='+customizationId : '')
        + '&qty='+qty
        + '&token='+static_token
        + '&allow_refresh=1',
        success: function(jsonData)
        {
            ajaxCart.updateCart(jsonData);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            appAlert('ERROR: unable to delete the product');
        }
    });
}

function changeProductCountInCart(productId, inputEl, total){

    var el = $('.'+inputEl);
    var prev = parseInt(el.attr('data-prev-val'));
    var cur = 0;
    var cur1 = el.val();
    if (cur1.length > 0) {
        cur = parseInt(el.val());
    }
    
    var exp = new RegExp("^[0-9]+$");
    if (exp.test(cur) != true){
        appAlert('Неверный формат числа');

        el.val(prev);
        return
    }

    if(cur < 0) {
        el.val(prev);
        appAlert('Значение не может быть меньше 0');

        el.val(prev);
        return
    }

    var customizationId = 0;
    var productAttributeId = 0;
    var id_address_delivery = 0;

    var qty = 0;
    var direct = '';
 //    if(cur>prev){
    //  qty = cur - prev;
    // }
    // else{
 //        qty = prev - cur;
 //        direct = '&op=down';
    // }

    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: baseUri + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType: 'json',
        data: 'controller=cart'
        + '&ajax=true'
        + '&add=true'
        + '&getproductprice=true'
        + '&summary=true'
        + '&id_product='+productId
        + '&ipa='+productAttributeId
        + '&id_address_delivery='+id_address_delivery
        + direct
        + ((customizationId !== 0) ? '&id_customization='+customizationId : '')
        + '&qty='+qty
        + '&token='+static_token
        + '&allow_refresh=1',
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(var error in jsonData.errors){
                    //IE6 bug fix
                    if(error !== 'indexOf'){
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                    }
                }
                appAlert(errors)
                cur = prev
            }
            else{
                ajaxCart.updateCart(jsonData);
            }
            el.attr('data-prev-val', cur);
            el.val(cur);

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            appAlert('Возникла ошибка');
        }
    });





}

function fancyChangeProductCountInCart(e, productId, inputEl, total){

  e.preventDefault();

    var $productImage = $(e.currentTarget)
        .parents('.ps-products__item, .ps-product-page')
        .find('.ps-product__image, .ps-product-page__image');
    var $productImageOffset = $productImage.offset();
    var $productImageClone = $productImage.clone();
        $productImageClone.css({
            'position': 'absolute',
            'top'     :  $productImageOffset.top,
            'left'    : $productImageOffset.left,
            'width'   : $productImage.width(),
            'z-index' : '100500'
        });

    var $cardButtonOffset = $('#header .shopping_cart').offset();
    
    var el = $(e.currentTarget).parent('.ps-product__controls').find('[name="qty"]');
    var isEl = el.length ? true : false;

    var customizationId = 0;
    var productAttributeId = 0;
    var id_address_delivery = 0;

    var qty = el.val() || 1;
    var prev = 0;
    var direct = '';
    var cur = qty;

    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: baseUri + '?rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType: 'json',
        data: 'controller=cart'
        + '&ajax=true'
        + '&add=true'
        + '&getproductprice=true'
        + '&summary=true'
        + '&id_product='+productId
        + '&ipa='+productAttributeId
        + '&id_address_delivery='+id_address_delivery
        + direct
        + ((customizationId !== 0) ? '&id_customization='+customizationId : '')
        + '&qty='+qty
        + '&token='+static_token
        + '&allow_refresh=1',
        success: function(jsonData)
        {
            if (jsonData.hasError)
            {
                var errors = '';
                for(var error in jsonData.errors){
                    //IE6 bug fix
                    if(error !== 'indexOf'){
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                    }
                }
                appAlert(errors)
                //cur = prev
            }
            else{
                ajaxCart.updateCart(jsonData);
                
                // if(cur > 0) {

                    if($('*').is('#layer_cart_product_title')){
                        $(jsonData.products).each(function(){
                            if (this.id != undefined && this.id == parseInt(productId))
                                ajaxCart.updateLayer(this);
                        });
                    }
                    else{

                        $productImageClone.appendTo('body')
                            .animate({
                                opacity: .05,
                                left   : $cardButtonOffset.left,
                                top    : $cardButtonOffset.top,
                                width  : '20px'
                            }, 500, function() {
                                this.remove();
                            })
                    }
                //}
                // ajaxCart.updateCartInformation(jsonData, true);
                
            }
            if(isEl){
                el.attr('data-prev-val', cur);
                el.val(cur);
            }


        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            appAlert('Возникла ошибка');
        }
    });
//UpdatePriceInMenu();
}

function plural(n, titles) {
  return titles[(n % 10 === 1 && n % 100 !== 11) ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2]
}

function UpdatePriceInMenu(){//maxim.
        var _localStorage = {};
        CategoryInfo =  $('span[categoryinfo]');
        CategoryInfo[0] && (CategoryInfo[0].innerHTML='');
        //обнуляем данные в меню
        CatMass = $('span[menucatid]');
        CountCatMass = CatMass.length;
        for(j=0;j<CountCatMass;j++){
            if (CatMass[j].innerHTML!=""){
                CatMass[j].innerHTML="";
            }}
    ElMass = $('span[cartcatid]');
    summAll = 0;
    CountElMass = ElMass.length;
        for(i=0;i<CountElMass;i++){
            Id = ElMass[i].attributes.cartcatid.value;
            q1 = ElMass[i].innerHTML.replace(/\s*/g, '');
            q1 = q1.replace('руб', '');
            q1 = q1.replace(',', '.');
            summAll=summAll+parseFloat(q1);
        }
        for(i=0;i<CountElMass;i++){
            Id = ElMass[i].attributes.cartcatid.value;
            CatId = "CatId"+Id;//summa
            TextCatId = "TextCatId"+Id;//Текстовая часть
            PercCatId = "PercCatId"+Id;//процент
            CountCatId = "CountCatId"+Id;
            CategoryInfo = "CategoryInfo"+Id;
            if(!!!_localStorage[CatId]) {_localStorage[CatId]=0;_localStorage[CountCatId]="0";_localStorage[PercCatId]="0";_localStorage[TextCatId]="";} 
            q1 = ElMass[i].innerHTML.replace(/\s*/g, '');
            q1 = q1.replace('руб', '');
            q1 = q1.replace(',', '.');
            summ=parseFloat(_localStorage[CatId])+parseFloat(q1);
            _localStorage[CatId] = summ.toFixed(2);
            locPerc = _localStorage[CatId] / summAll * 100;
            _localStorage[PercCatId] = locPerc.toFixed(0);
            _localStorage[CatId] = summ.toFixed(2)+" р";
            _localStorage[CountCatId] = parseInt(_localStorage[CountCatId])+parseInt(1);
            var ln = document.getElementById(CatId);
                ln.innerHTML = _localStorage[CatId];
            var Textln = document.getElementById(TextCatId);
                Textln.innerHTML = plural(_localStorage[CountCatId], ['товар', 'товара', 'товаров']) + " на сумму - ";
            var Percln = document.getElementById(PercCatId);
                Percln.innerHTML = "("+_localStorage[PercCatId]+" %)";
                Percln.setAttribute('title', _localStorage[PercCatId]+"% от суммы заказа");
            var Countln = document.getElementById(CountCatId);
                Countln.innerHTML= _localStorage[CountCatId]+' ';
            var CategoryInfoElement = document.getElementById(CategoryInfo);
            if(CategoryInfoElement != null){
                CategoryInfoElement.innerHTML=_localStorage[CountCatId] +' '+ plural(_localStorage[CountCatId], ['товар', 'товара', 'товаров']) + " на сумму - "+_localStorage[CatId]+" ("+_localStorage[PercCatId]+"%)";
                CategoryInfoElement.setAttribute('title', _localStorage[PercCatId]+"% от суммы заказа");
            }
        }
}
setInterval('UpdatePriceInMenu();', 3000);