<div class="ps-product__table-thead js-product-table-head">
    <div class="ps-product__table-row">
        <div class="ps-product__table-cell">Артикул</div>
        <div class="ps-product__table-cell">Изобр.</div>
        <div class="ps-product__table-cell">Наименование</div>
        <div class="ps-product__table-cell">Ед</div>
        <div class="ps-product__table-cell">Цена</div>
        <div class="ps-product__table-cell">В уп.</div>
        <div class="ps-product__table-cell">В бл.</div>
        <div class="ps-product__table-cell">В кор.</div>
        <div class="ps-product__table-cell">Нал.</div>
        <div class="ps-product__table-cell">В заказе</div>
        <div class="ps-product__table-cell">Сумма</div>
    </div>
</div>
<div class="ps-product__table-tbody">
{foreach from=$products item=product name=products}
    {foreach from=$product.features item=feat}
        {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
    {/foreach}
    <div class="ps-product__table-row ps-products__item ps-product" data-product-id="{$product.id_product}">
        <div class="ps-product__table-cell ps-product__articule">{$product.reference}</div>
        <div class="ps-product__table-cell">
            <a  class="ps-product__image-zoom fancyImg"
                href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}">
                <img
                    class="ps-product__image"
                    alt="{$product.name|escape:'html':'UTF-8'}"
                    title="{$product.name|escape:'html':'UTF-8'}"
                    src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
            </a>
        </div>
        <div class="ps-product__table-cell ps-product__name">
            <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
        </div>
        <div class="ps-product__table-cell">{Product::SGetProductUnity($product.sale_unity)}</div>
        <div class="ps-product__table-cell ps-product__price">
            <span class="ps-price__value">
                {Tools::displayPrice($product.price)}
            </span>
            {if $product.specific_prices.reduction > 0}
                <span class="ps-price__value ps-price__value--old">
                    {Tools::displayPrice($product.price_without_reduction)}
                </span>
            {/if}
        </div>
        <div class="ps-product__table-cell">{if $product.r1 < 2}-{else}{$product.r1}<div>шт.</div>{/if}</div>
        <div class="ps-product__table-cell">{if $product.r2 < 2}-{else}{$product.r2}<div>уп.</div>{/if}</div>
        <div class="ps-product__table-cell">{if $product.r3 < 2}-{else}{$product.r3}<div>{Product::SGetProductUnity($product.sale_unity)}</div>{/if}</div>
        <div class="ps-product__table-cell">
            {$product.quantity}
            {Product::SGetProductUnity($product.sale_unity)}
        </div>
        <div class="ps-product__table-cell ps-product__controls">
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
                    placeholder="0"
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
                    placeholder="0"
                    data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                    data-inbox={$product.r3}
                    data-price="{$product.price}"
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
            {/if}
        </div>
        <div class="ps-product__table-cell js-product-summ ajax_block_cart_total_price2_id_{$product.id_product}">-</div>
    </div>
{/foreach}
</div>