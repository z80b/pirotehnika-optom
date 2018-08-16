{foreach from=$products item=product name=products}
    {foreach from=$product.features item=feat}
        {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
    {/foreach}
    <div class="ps-products__item ps-product">
        <div class="ps-product__cell">
            <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">
                <img
                    class="ps-product__image"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    title="{$product.name|escape:'html':'UTF-8'}"
                    src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
                {if $product.specific_prices.reduction_type == 'percentage'}
                <span class="ps-product__discount">
                    -{$product.specific_prices.reduction * 100}%
                </span>
                {/if}
            </a>
        </div>
        <div class="ps-product__cell">
            <h2 class="ps-product__name" href="{$product.link|escape:'html':'UTF-8'}">
                <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
            </h2>
            <div class="ps-product__option ps-option">
                <b class="ps-option__title">{l s='Артикул'}:</b>
                <span class="ps-option__value">{$product.reference}</span>
            </div>
            <span class="ps-product__description">
                {$product.description_short|strip_tags:'UTF-8'}
            </span>
            <div class="ps-product__price ps-price">
                <span class="ps-price__title">{l s="Цена"}:</span>
                <span class="ps-price__value">
                    {convertPrice price=$product.price}
                </span>
                {if $product.specific_prices.reduction > 0}
                    <span class="ps-price__value ps-price__value--old">
                        {convertPrice price=$product.price_without_reduction}
                    </span>
                {/if}
            </div>
        </div>
        <div class="ps-product__cell ps-product__cell--right">
            <div class="ps-product__option">
                {if $product.quantity > 0}
                <b class="ps-option__title">{l s='В наличии'}:</b>
                <span class="ps-option__value">
                    {$product.quantity}
                    {Product::SGetProductUnity($product.sale_unity)}
                </span>
                {else}
                <b class="ps-option__title">{l s='Отсутствует'}</b>
                {/if}
            </div>
            <div class="ps-product__option">
                <b class="ps-option__title">{l s='В заказе'}:</b>
                <span class="ps-option__value">
                    <span class="js-product-count-{$product.id_product}">
                    {if !empty($productsCart) }
                        {$productsCart.cart_quantity}
                    {else} 0 {/if}
                    </span>
                    {Product::SGetProductUnity($product.sale_unity)}
                </span>
            </div>
            <div class="ps-product__option">
                <b class="ps-option__title">{l s='На сумму'}:</b>
                <span class="ps-option__value">
                    {if !empty($productsCart) }
                        <span class="ajax_block_cart_total_price2_id_{$product.id_product}">{convertPrice price=$productsCart.total}</span> руб.
                    {else}
                        <span class="ajax_block_cart_total_price2_id_{$product.id_product}">0</span> руб.
                    {/if}
                </span>
            </div>
            <hr class="ps-product__separator" />
            <div class="ps-product__controls">
            {if $product.quantity > 0}
                <div class="ps-product__quantity ps-quantity">
                    <button
                        class="ps-quantity__button ps-quantity__button--decbox"
                        data-field-qty="boxqty">&lt;</button>
                    <input
                        name="boxqty"
                        class="ps-quantity__value ajax_box_input_prod_{$product.id_product} js-boxes-input"
                        type="number"
                        value="0"
                        data-inbox="{$product.r3}"
                        min="0"
                        max="{$product.quantity / $product.r3}"/>
                    <button
                        class="ps-quantity__button ps-quantity__button--incbox"
                        data-field-qty="boxqty">&gt;</button>
                </div>
                <span class="ps-quantity__title">кор.</span>
                <div class="ps-product__quantity ps-quantity">
                    <button
                        class="ps-quantity__button ps-quantity__button--dec"
                        data-field-qty="qty">-</button>
                    <input
                        name="qty"
                        class="ps-quantity__value ajax_input_prod_{$product.id_product} js-qty-input"
                        type="number"
                        value="0"
                        data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                        data-inbox={$product.r3}
                        min="0"
                        max="{$product.quantity}"/>
                    <button
                        class="ps-quantity__button ps-quantity__button--inc"
                        data-field-qty="qty">+</button>
                </div>
                <span class="ps-quantity__title">{Product::SGetProductUnity($product.sale_unity)}</span>
                <a class="ps-product__button ps-product__button--tocart"
                    id="btnid{$product.id_product}" 
                    btncatid="{$product.id_category_default}" 
                    type="submit"
                    name="Submit"
                    title="{l s='Add to cart'}"
                    onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;"
                >{l s="В заказ"}</a>
                <a class="ps-product__button ps-product__button--compare js-product-compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='К сравнению'}">{l s='К сравнению'}</a>
            {/if}
            </div>
        </div>
    </div>
{/foreach}