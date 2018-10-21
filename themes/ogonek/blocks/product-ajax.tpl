{if !isset($priceDisplayPrecision)}
    {assign var='priceDisplayPrecision' value=2}
{/if}
{assign var='productPriceDisc' value=$product->getPriceDisc($product->id)}
{assign var='isShowPriceDisc' value=$product->getIsShowPriceDisc()}
{assign var='isShowPriceWoDisc' value=$product->getIsShowPriceWoDisc()}
{if !$priceDisplay || $priceDisplay == 2}
    {assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
    {assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}
<div class="ps-popup-content__ajax">
    {if false && isset($prevProduct)}
        <a  class="ps-product__linkbutton ps-product__linkbutton--prev"
            href="{$prevProduct|escape:'html':'UTF-8'}"></a>
    {/if}
    {if false && isset($nextProduct)}
        <a  class="ps-product__linkbutton ps-product__linkbutton--next"
            href="{$nextProduct|escape:'html':'UTF-8'}"></a>
    {/if}
    <h1 class="ps-popup-content__title">
        <span class="ps-popup-content__title-left">{$product->name|escape:'html':'UTF-8'}</span>
        <span class="ps-popup-content__title-right">{l s='Артикул:'} {if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}</span>
    </h1>
    <div class="ps-popup-content__row">
        <div class="ps-popup-content__cell">
            {include file="$tpl_dir/blocks/image-block.tpl"}
        </div>
        
        <div class="ps-popup-content__cell">
            <div class="ps-features">
                <ul class="ps-features__list">
                    {if isset($features) && $features}
                        {foreach from=$features item=feature}
                        {if $feature.value != ''}
                        <li class="ps-features__item ps-feature">
                            <span class="ps-feature__title">{$feature.name|escape:'htmlall':'UTF-8'}</span>
                            <span class="ps-feature__value">{$feature.value|escape:'htmlall':'UTF-8'}</span>
                        </li>
                        {/if}
                        {/foreach}
                    {/if}
                    <li class="ps-features__item ps-feature">
                        <span class="ps-feature__title">{l s='Manufacturer'}</span>
                        <span class="ps-feature__value">{$product_manufacturer->name}</span>
                    </li>
                    <li class="ps-features__item ps-feature">
                        <span class="ps-feature__title">{l s='Weight'}</span>
                        <span class="ps-feature__value">{$product->weight|intval} кг</span>
                    </li>
                    <li class="ps-features__item ps-feature">
                        <span class="ps-feature__title">{l s='Sizes'}</span>
                        <span class="ps-feature__value">{$product->width|intval} x {$product->height|intval} x {$product->depth|intval} мм</span>
                    </li>
                    <li class="ps-features__item ps-feature">
                        <span class="ps-feature__title">{l s='Certificate'}</span>
                        {if isset($product_attachments) && isset($product_attachments[0])}
                        <a  class="ps-feature__value js-product-certificates-link"
                            href="{$product_attachments[0]['src']}"
                            data-fancybox-group="attachements-group"
                            data-fancybox-type="image">{$product->sert}</a>
                        <a href="{$product_attachments[1]['src']}" class="ps-product__attachments js-product-certificates-link" data-fancybox-group="attachements-group" data-fancybox-type="image"></a>
                        {else}
                        <span class="ps-feature__value">{$product->sert}</span>
                        {/if}
                    </li>
                </ul>
            </div>
        </div>
        <div class="ps-popup-content__cell">
            <div class="ps-white-block">
                <div class="ps-price__block">
                    <span class="ps-product__price ps-price">
                        <span class="ps-price">
                            <span class="ps-price__value">
                                {Tools::displayOgonekPrice($product->price, 1)}
                            </span>
                        </span>
                    </span> 
                    {if $product->specificPrice.reduction > 0}
                        <span class="ps-price__value ps-price__value--old">
                            {Tools::displayOgonekPrice($product->base_price, 1)}
                        </span>
                        <span class="ps-price__discount">
                            -{$product->specificPrice.reduction * 100}%
                        </span>
                    {/if}
                </div>
                <div class="ps-price__descripts">
                    <div class="ps-price__descript">
                        {if isset($product->sale_unity_pack)}
                            {$product->sale_unity_pack}
                        {/if}
                    </div>
                    <div class="ps-price__descript">
                        {if isset($product->r3)}В коробке - {$product->r3}&nbsp;{Product::SGetProductUnity($product->sale_unity)}{/if}
                    </div>
                    <div class="ps-price__descript"> 
                    {if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
                        {math
                            equation="pprice / punit_price"
                            pprice=$product->price  punit_price=$product->unit_price_ratio
                            assign=unit_price}
                            <p class="unit-price">
                                {l s='Цена за 1'}{$product->unity|escape:'html':'UTF-8'}{' (справочно) - '}
                                <span id="unit_price_display">{convertPrice price=$unit_price}</span> 
                            </p>
                    {/if}
                    </div>
                </div>
                <div class="ps-product__options">
                    <div class="ps-product__option ps-product__option--checked">
                        {if $product->quantity > 0}
                        <b class="ps-option__title">{l s='В наличии'}:</b>
                        <span class="ps-option__value">
                            <span class="js-popup-product-quantity">{$product->quantity}</span>
                            {Product::SGetProductUnity($product->sale_unity)} / {round($product->quantity / $product->r3)} кор.
                        </span>
                        {else}
                        <b class="ps-option__title">{l s='Отсутствует'}</b>
                        {/if}
                    </div>
                    <div class="ps-product__option ps-product__option--checked">
                        <b class="ps-option__title">{l s='В заказе'}:</b>
                        <span class="ps-option__value">
                            <span class="js-product-count-{$product->id}">
                            {if !empty($productsCart) }
                                {$productsCart.cart_quantity}
                            {else} 0 {/if}
                            </span>
                            {Product::SGetProductUnity($product->sale_unity)} / <span class="js-boxes-count-{$product->id}" data-inbox="{$product->r3}">0</span> кор.
                        </span>
                    </div>
                    <div class="ps-product__option ps-product__option--checked">
                        <b class="ps-option__title">{l s='На сумму'}:</b>
                        <span class="ps-option__value">
                            {if !empty($productsCart) }
                                <span class="ajax_block_cart_total_price2_id_{$product->id}">{convertPrice price=$productsCart.total}</span> руб.
                            {else}
                                <span class="ajax_block_cart_total_price2_id_{$product->id}">0</span> руб.
                            {/if}
                        </span>
                    </div>                    
                </div>
            </div>
            <div class="ps-grey-block">
                <div class="ps-product__controls">
                    {if $product->quantity > 0}
                    <div class="ps-product__row">
                        <div class="ps-product__cell">
                            <div class="ps-product__control">
                                <div class="ps-product__quantity ps-quantity">
                                    <button
                                        class="ps-quantity__button ps-quantity__button--decbox"
                                        data-field-qty="boxqty">&lt;</button>
                                    <input
                                        name="boxqty"
                                        class="ps-quantity__value ajax_box_input_prod_{$product->id} js-boxes-input"
                                        type="number"
                                        value="0"
                                        data-inbox="{$product->r3}"
                                        min="0"
                                        max="{$product->quantity / $product->r3}"/>
                                    <button
                                        class="ps-quantity__button ps-quantity__button--incbox"
                                        data-field-qty="boxqty">&gt;</button>
                                </div>
                                <span class="ps-quantity__title">кор.</span>
                            </div>
                            <div class="ps-product__control">
                                <div class="ps-product__quantity ps-quantity">
                                    <button
                                        class="ps-quantity__button ps-quantity__button--dec"
                                        data-field-qty="qty">&lt;</button>
                                    <input
                                        name="qty"
                                        class="ps-quantity__value ajax_input_prod_{$product->id} js-qty-input"
                                        type="number"
                                        value="0"
                                        data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                                        data-inbox="{$product->r3}"
                                        min="0"
                                        max="{$product->quantity}"/>
                                    <button
                                        class="ps-quantity__button ps-quantity__button--inc"
                                        data-field-qty="qty">&gt;</button>
                                </div>
                                <span class="ps-quantity__title">{Product::SGetProductUnity($product->sale_unity)}</span>
                            </div>
                        </div>
                        <div class="ps-product__cell">
                        <a class="ps-product__button ps-product__button--tocart"
                            id="btnid{$product->id}" 
                            btncatid="{$product->id_category_default}" 
                            type="submit"
                            name="Submit"
                            title="{l s='Add to cart'}"
                            onClick="fancyChangeProductCountInCart(event, {$product->id}, 'ajax_input_prod_{$product->id}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;">{l s="В заказ"}</a>
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>