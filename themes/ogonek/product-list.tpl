{if isset($products) && $products}
    <div class="ps-products">
    {foreach from=$products item=product name=products}
        {foreach from=$product.features item=feat}
            {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
        {/foreach}
        <div class="ps-products__item ps-product">
            <img
                class="ps-product__image"
                alt="{$product.name|escape:'html':'UTF-8'}"
                title="{$product.name|escape:'html':'UTF-8'}"
                src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
            <span class="ps-product__info">
                <a class="ps-product__name" href="{$product.link|escape:'html':'UTF-8'}">
                    {$product.name|escape:'html':'UTF-8'}
                </a>
                <input class="ps-switch" type="checkbox"/>
                <span class="ps-product__description">
                    <label class="ps-switch__label">{l s='Description'}</label>
                    {$product.description_short|strip_tags:'UTF-8'}
                </span>
                
                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                <span class="ps-product__price">
                    {hook h="displayProductPriceBlock" product=$product type='before_price'}
                    {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                    {if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                    {hook h="displayProductPriceBlock" product=$product type="old_price"}
                    {/if}
                </span>
                <span class="ps-product__price ps-product__price--old">
                    {displayWtPrice p=$product.price_without_reduction}
                    {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
                </span>
                {hook h="displayProductPriceBlock" product=$product type="price"}
                {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                {hook h="displayProductPriceBlock" product=$product type='after_price'}
                {/if}
            </span>
            {if $product.specific_prices.reduction_type == 'percentage'}
            <span class="ps-product__discount">
                -{$product.specific_prices.reduction * 100}%
            </span>
            {/if}
            {if $product.quantity > 0}
            <div class="ps-product__buttons">
                <a class="ps-product__button ps-product__button--tocart"
                    id="btnid{$product.id_product}" 
                    btncatid="{$product.id_category_default}" 
                    type="submit"
                    name="Submit"
                    title="{l s='Add to cart'}"
                    onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); yaCounter46713966.reachGoal('ADDCART'); return true;"
                ></a>
                        <p id="quantity_wanted_p"{if (!$allow_oosp && $product.quantity <= 0) || !$product.available_for_order || $PS_CATALOG_MODE} style="display: block;"{/if}>
                            <!-- <label for="quantity_wanted">{l s='Quantity'}</label> -->
                            <span  data-field-qty="qty" class="btn btn-default button-minus product_quantity_down">
                                <!-- <span><i class="icon-minus"></i></span> -->
                            </span>
                            <span itemprop="inputCount">
                                {*<input type="number" min="1" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" />*}
                                <input type="number"
                                       min="0"
                                       name="qty"
                                       id="quantity_wanted"
                                       data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                                       class="ajax_input_prod_{$product.id_product} ajax_input_prod_to_null text"
                                       value="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                                />
                            </span>
                            
                            <span data-field-qty="qty" class="btn btn-default button-plus product_quantity_up">
                                <!-- <span><i class="icon-plus"></i></span> -->
                            </span>
                            <span class="clearfix"></span>
                        </p>
                <a class="ps-product__button ps-product__button--compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}"></a>
            </div>
            {/if}
        </div>
    {/foreach}
    </div>
{/if}