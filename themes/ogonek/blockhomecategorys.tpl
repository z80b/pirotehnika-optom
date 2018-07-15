{$messageSmarty}
<h2>{l s='Полный каталог' mod='blockhomecategorys'}</h2>
<div class="ps-products__header">
	{include file="$tpl_dir./products-filter.tpl"}
	<div class="content_sortPagiBar">
    	<div class="sortPagiBar clearfix">
			{include file="./product-sort.tpl"}
			{include file="./nbr-product-page.tpl"}
		</div>
    	<div class="top-pagination-content clearfix">
        	{include file="./product-compare.tpl"}
            {include file="$tpl_dir./pagination.tpl" no_follow=1}
        </div>
	</div>
</div>
{if isset($products) && $products}
<div class="ps-products ps-products--grid js-ps-products">
    <div class="ps-product__grid">{include file="{$tpl_dir}/product--grid.tpl"}</div>
    <div class="ps-product__list">{include file="{$tpl_dir}/product--list.tpl"}</div>
    <div class="ps-product__table">{include file="{$tpl_dir}/product--table.tpl"}</div>
</div>
{/if}
<div class="content_sortPagiBar">
	<div class="bottom-pagination-content clearfix">
    	{include file="./product-compare.tpl"}
    	{if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
            {include file="$tpl_dir./pagination.tpl" paginationId='bottom' no_follow=1}
        {/if}
    </div>
</div>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
