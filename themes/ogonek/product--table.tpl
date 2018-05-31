<thead>
    <tr>
        <th>Артикул</th>
        <th>Изобр.</th>
        <th>Наименование</th>
        <th>Ед</th>
        <th>Цена</th>
        <th>В упаковке</th>
        <th>В блоке</th>
        <th>В коробке</th>
        <th>Нал.</th>
        <th>В заказе</th>
        <th>Сумма</th>
    </tr>
</thead>
<tbody>
{foreach from=$products item=product name=products}
    {foreach from=$product.features item=feat}
        {if $feat.name == 'Фасовка'}{assign var='fasovka' value=$feat.value}{else}{assign var='fasovka' value=''}{/if}
    {/foreach}
    <tr class="ps-products__item ps-product" data-product-id="{$product.id_product}">
        <td class="ps-product__articule">{$product.reference}</td>
        <td>
            <img
                class="ps-product__image"
                alt="{$product.name|escape:'html':'UTF-8'}"
                title="{$product.name|escape:'html':'UTF-8'}"
                src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
        </td>
        <td class="ps-product__name">
            <a class="ps-product__link" href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
        </td>
        <td>{Product::SGetProductUnity($product.sale_unity)}</td>
        <td class="ps-product__price">
            <span class="ps-price__value">
                {convertPrice price=$product.price}
            </span>
            {if $product.specific_prices.reduction > 0}
                <span class="ps-price__value ps-price__value--old">
                    {convertPrice price=$product.price_without_reduction}
                </span>
            {/if}
        </td>
        <td>{$product.r1}</td>
        <td>{$product.r2}</td>
        <td>{$product.r3}</td>
        <td>
            {$product.quantity}
            {Product::SGetProductUnity($product.sale_unity)}
        </td>
        <td>
            {if $product.quantity > 0}
            <div class="ps-product__quantity ps-quantity">
                <button
                    class="ps-quantity__button ps-quantity__button--dec"
                    data-field-qty="qty">-</button>
                <input
                    id="quantity_wanted"
                    name="qty"
                    class="ps-quantity__value ajax_input_prod_{$product.id_product}"
                    type="number"
                    value="0"
                    data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                    min="0"
                    max="{$product.quantity}"/>
                <button
                    class="ps-quantity__button ps-quantity__button--inc"
                    data-field-qty="qty">+</button>
            </div>
            <a class="ps-product__button ps-product__button--tocart"
                id="btnid{$product.id_product}" 
                btncatid="{$product.id_category_default}" 
                type="submit"
                name="Submit"
                title="{l s='Add to cart'}"
                onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;"
            >{l s="В заказ"}</a>
            {/if}
        </td>
        <td class="js-product-summ">0</td>
    </tr>
{/foreach}
</tbody>