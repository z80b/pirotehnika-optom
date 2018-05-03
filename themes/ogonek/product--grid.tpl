{foreach from=$products item=product name=products}
    {foreach from=$product.features item=feat}
        {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
    {/foreach}
    <div class="ps-products__item ps-product">
        <a class="ps-product__info" href="{$product.link|escape:'html':'UTF-8'}">
            <img
                class="ps-product__image"
                alt="{$product.name|escape:'html':'UTF-8'}"
                title="{$product.name|escape:'html':'UTF-8'}"
                src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
            <span class="ps-product__name" href="{$product.link|escape:'html':'UTF-8'}">
                {$product.name|escape:'html':'UTF-8'}
            </span>
        </a>
        <span class="ps-product__info">
            <div class="ps-product__option ps-option">
                <b class="ps-option__title">{l s='Артикул'}:</b>
                <span class="ps-option__value">{$product.reference}</span>
            </div>
            <div class="ps-product__option">
                {if $product.quantity > 0}
                <b class="ps-option__title">{l s='В наличии'}:</b>
                <span class="ps-option__value">{$product.quantity}</span>
                {else}
                <b class="ps-option__title">{l s='Отсутствует'}</b>
                {/if}
            </div>
            <input class="ps-switch" type="checkbox" id="ps-switch-{$product.id_product}"/>
            <div class="ps-product__description ps-description">
            {if $product.description_short}
                <label
                    class="ps-description__button"
                    for="ps-switch-{$product.id_product}">{l s='Description'}</label>
                <span class="ps-description__text">
                    {$product.description_short|strip_tags:'UTF-8'}
                </span>
            {else}
                <span class="ps-description__empty"></span>
            {/if}
            </div>
            
            {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
            <div class="ps-product__price ps-price">
                <span class="ps-price__value">
                    {convertPrice price=$product.price}
                </span>
                {if $product.specific_prices.reduction > 0}
                    <span class="ps-price__value ps-price__value--old">
                        {convertPrice price=$product.price_without_reduction}
                    </span>
                {/if}
            </div>
            {/if}
        </span>
        {if $product.specific_prices.reduction_type == 'percentage'}
        <span class="ps-product__discount">
            -{$product.specific_prices.reduction * 100}%
        </span>
        {/if}

        
        <div class="ps-product__controls">
        {if $product.quantity > 0}
            <a class="ps-product__button ps-product__button--tocart"
                id="btnid{$product.id_product}" 
                btncatid="{$product.id_category_default}" 
                type="submit"
                name="Submit"
                title="{l s='Add to cart'}"
                onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;"
            ></a>
            <div class="ps-product__quantity ps-quantity">
                <button
                    class="ps-quantity__button ps-quantity__button--dec"
                    data-field-qty="qty">-</button>
                <input
                    id="quantity_wanted"
                    name="qty"
                    class="ps-quantity__value ajax_input_prod_{$product.id_product}"
                    type="number"
                    value="1"
                    data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                    min="1"
                    max="{$product.quantity}"/>
                <button
                    class="ps-quantity__button ps-quantity__button--inc"
                    data-field-qty="qty">+</button>
            </div>
            <a class="ps-product__button ps-product__button--compare js-product-compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}"></a>
        {/if}
        </div>
    </div>
{/foreach}