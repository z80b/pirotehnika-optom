{foreach from=$products item=product name=products}
    {foreach from=$product.features item=feat}
        {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
    {/foreach}
    <div class="ps-products__item ps-product">
        <div class="ps-product__row">
            <div class="ps-product__cell ps-product__cell--left ps-product__cell--image">
                <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">
                    <img
                        class="ps-product__image"
                        alt="{$product.name|escape:'html':'UTF-8'}"
                        title="{$product.name|escape:'html':'UTF-8'}"
                        src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
                </a>
                <a  class="ps-product__compare js-product-compare"
                    href="{$product.link|escape:'html':'UTF-8'}"
                    data-id-product="{$product.id_product}"
                    title="{l s='К сравнению'}">
                    <i class="fa fa-list"></i>
                    {l s='К сравнению'}
                </a>
            </div>
            <div class="ps-product__cell">
                <div class="ps-product__row">
                    <div class="ps-product__cell ps-product__cell--left">
                        <h2 class="ps-product__name">
                            <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                        </h2>
                        <div class="ps-product__option ps-option">
                            <b class="ps-option__title">{l s='Артикул'}:</b>
                            <span class="ps-option__value">{$product.reference}</span>
                        </div>
                    </div>
                    <div class="ps-product__cell">
                    {if isset($product.features)}
                        {foreach from=$product.features item=feature}
                        <div class="ps-product__option ps-option">
                            <b class="ps-option__title">{$feature.name}:</b>
                            <span class="ps-option__value">{$feature.value}</span>
                        </div>
                        {/foreach}
                    {/if}
                    </div>
                </div>
                <hr class="ps-product__separator" />
                <div class="ps-product__row">
                    <div class="ps-product__cell ps-product__cell--left">
                        <div class="ps-product__price ps-price">
                            {if $product.specific_prices.reduction > 0}
                                <span class="ps-price__value ps-price__value--old">
                                    {convertPrice price=$product.price_without_reduction}
                                </span>
                                <span class="ps-product__discount">
                                    -{$product.specific_prices.reduction * 100}%
                                </span>
                                <br/>
                            {/if}
                            <span class="ps-price">
                                <span class="ps-price__title">{l s="Цена"}:</span>
                                <span class="ps-price__value">
                                    {convertPrice price=$product.price}
                                </span>
                            </span>
                        </div>
                        <div class="ps-price__descript">
                            {if isset($product.sale_unity_pack)}
                                {$product.sale_unity_pack}
                            {/if}
                        </div>
                        <div class="ps-price__descript">
                            {if isset($product.r3)}В коробке - {$product.r3}&nbsp;{Product::SGetProductUnity($product.sale_unity)}{/if}
                        </div>
                        <div class="ps-price__descript"> 
                        {if !empty($product.unity) && $product.unit_price_ratio > 0.000000}
                            {math
                                equation="pprice / punit_price"
                                pprice=$product.price  punit_price=$product.unit_price_ratio
                                assign=unit_price}
                                <p class="unit-price">
                                    {l s='Цена за 1'}{$product.unity|escape:'html':'UTF-8'}{' (справочно) - '}
                                    <span id="unit_price_display">{convertPrice price=$unit_price}</span> 
                                </p>
                        {/if}
                        </div>
                    </div>
                    <div class="ps-product__cell">
                        <div class="ps-product__option ps-product__option--checked">
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
                        <div class="ps-product__option ps-product__option--checked">
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
                        <div class="ps-product__option ps-product__option--checked">
                            <b class="ps-option__title">{l s='На сумму'}:</b>
                            <span class="ps-option__value">
                                {if !empty($productsCart) }
                                    <span class="ajax_block_cart_total_price2_id_{$product.id_product}">{convertPrice price=$productsCart.total}</span> руб.
                                {else}
                                    <span class="ajax_block_cart_total_price2_id_{$product.id_product}">0</span> руб.
                                {/if}
                            </span>
                        </div>
                        <div class="ps-product__controls">
                        {if $product.quantity > 0}
                        <div class="ps-product__row">
                            <div class="ps-product__cell">
                                <div class="ps-product__control">
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
                                </div>
                                <div class="ps-product__control">
                                    <div class="ps-product__quantity ps-quantity">
                                        <button
                                            class="ps-quantity__button ps-quantity__button--dec"
                                            data-field-qty="qty">&lt;</button>
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
                                            data-field-qty="qty">&gt;</button>
                                    </div>
                                    <span class="ps-quantity__title">{Product::SGetProductUnity($product.sale_unity)}</span>
                                </div>
                            </div>
                            <div class="ps-product__cell">
                            <a class="ps-product__button ps-product__button--tocart"
                                id="btnid{$product.id_product}" 
                                btncatid="{$product.id_category_default}" 
                                type="submit"
                                name="Submit"
                                title="{l s='Add to cart'}"
                                onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;"
                            >{l s="В заказ"}</a>
                            </div>
                        </div>
                        {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/foreach}