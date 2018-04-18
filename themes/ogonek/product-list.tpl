{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($products) && $products}
    {*define number of products per line in other page for desktop*}
    {if $page_name !='index' && $page_name !='product'}
        {assign var='nbItemsPerLine' value=3}
        {assign var='nbItemsPerLineTablet' value=2}
        {assign var='nbItemsPerLineMobile' value=3}
    {else}
        {assign var='nbItemsPerLine' value=4}
        {assign var='nbItemsPerLineTablet' value=3}
        {assign var='nbItemsPerLineMobile' value=2}
    {/if}
    {*define numbers of product per line in other page for tablet*}
    {assign var='nbLi' value=$products|@count}
    {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
    {math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
    <!-- Products list -->
    <div id="appTabView"></div>
    <div class="ps-products">
    {foreach from=$products item=product name=products}
        {math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
        {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}

        {foreach from=$product.features item=feat}
            {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
        {/foreach}
        <div class="ps-products__item ps-product">
            <img
                class="ps-product__image"
                src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
            <span class="ps-product__info">
                <a class="ps-product__name" href="{$product.link|escape:'html':'UTF-8'}">
                    {$product.name|escape:'html':'UTF-8'}
                </a>
                <span class="ps-product__description">
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

            {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
                {if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
                    {capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
                    <a class="ps-product__button"
                        id="btnid{$product.id_product}" 
                        btncatid="{$product.id_category_default}" 
                        type="submit"
                        name="Submit"
                        onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); yaCounter46713966.reachGoal('ADDCART'); return true;"
                    ></a>
                {else}
                    <span class="button ajax_add_to_cart_button btn btn-default disabled">
                        <span>{l s='ДОБАВИТЬ В КОРЗИНУ'}</span>
                    </span>
                {/if}
            {/if}
        </div>
    {/foreach}
    </div>
{/if}