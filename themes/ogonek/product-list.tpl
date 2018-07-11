{if isset($products) && $products}
<div class="ps-products ps-products--grid js-ps-products">
    <div class="ps-product__grid">{include file="{$tpl_dir}/product--grid.tpl"}</div>
    <div class="ps-product__list">{include file="{$tpl_dir}/product--list.tpl"}</div>
    <div class="ps-product__table">{include file="{$tpl_dir}/product--table.tpl"}</div>
</div>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}