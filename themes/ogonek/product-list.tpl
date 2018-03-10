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
	<ul{if isset($id) && $id} id="{$id}"{else} id="product_list"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
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

		<li class="ajax_block_product{if $page_name == 'index' || $page_name == 'product'} col-xs-12 col-sm-4 col-md-3{else} col-xs-12 col-sm-6 col-md-4{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModulo)} last-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModuloMobile)} last-mobile-line{/if}"
			data-product-id="{$product.id_product}"
			data-product-fasovka="{Product::SGetProductUnity($product.sale_unity)}"
			data-product-quantity="{$product.quantity}"
			data-product-r1="{$product.r1}"
			data-product-r2="{$product.r2}"
			data-product-r3="{$product.r3}"
		>
			<div class="product-container" itemscope itemtype="https://schema.org/Product">
				<div class="left-block">
					<div class="product-image-container">
						<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive"
								 src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"
								 alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}"
								 title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}
								 itemprop="image"
								 data-full-img="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}"
							/>
						</a>
						{if isset($quick_view) && $quick_view}
							<div class="quick-view-wrapper-mobile">
							<a class="quick-view-mobile" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
								<i class="icon-eye-open"></i>
							</a>
						</div>
						<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
							<span>{l s='Quick view'}</span>
						</a>
						{/if}

						{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							<div class="content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}

									<span class="priceText">{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}Оптовая цена: {else}Цена: {/if}</span>
									<{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}strike{else}span{/if} itemprop="price{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}{else}Disc{/if}" class="price product-price">
										{hook h="displayProductPriceBlock" product=$product type="before_price"}
										{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}{convertPrice price=$product.price_discount}{else}{convertPrice price=$product.price}{/if}
									</{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}strike{else}span{/if}>

										<span class="{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}priceText{else}hidden{/if}">Ваша цена:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> 
										<span id="our_price_display" class="{if isset($is_show_skid) && $is_show_skid == 1 && $product.price <> $product.price_discount && isset($isShowPriceWoDisc) && $isShowPriceWoDisc == 1}price{else}hidden{/if}" itemprop="priceDisc" content="{$productPrice}">{convertPrice price=$product.price|floatval}</span>


									<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
									{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
										{hook h="displayProductPriceBlock" product=$product type="old_price"}
										<br/>
										<span class="old-price product-price">
											{displayWtPrice p=$product.price_without_reduction}
										</span>
										{if $product.specific_prices.reduction_type == 'percentage'}
											<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
										{/if}
									{/if}
									<br>
									<br>
									<span class="priceDescript">{if isset($product.sale_unity_pack)}{$product.sale_unity_pack}{/if}</span>
									<span class="priceDescript">{if isset($product.r3)}В коробке - {$product.r3}&nbsp;{Product::SGetProductUnity($product.sale_unity)}{/if}</span>

									{if false && $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
										<span class="unvisible">
											{if ($product.allow_oosp || $product.quantity > 0)}
													<link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
											{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
													<link itemprop="availability" href="https://schema.org/LimitedAvailability" />{l s='Product available with different options'}

											{else}
													<link itemprop="availability" href="https://schema.org/OutOfStock" />{l s='Отсутствует'}
											{/if}
										</span>
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
								{/if}
							</div>
						{/if}

						{if false && isset($product.new) && $product.new == 1}
							<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
								<span class="new-label">{l s='New'}</span>
							</a>
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
								<span class="sale-label">{l s='Sale!'}</span>
							</a>
						{/if}
					</div>

					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
					{hook h="displayProductPriceBlock" product=$product type="weight"}
				</div>
				<div class="right-block">
					<h5 itemprop="name" >
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<span itemprop="productname"><a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.description_short|escape:'html':'UTF-8'}" itemprop="url" >
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
						</a></span>
						<p id="product_reference"{if empty($product.reference) || !$product.reference} style="display: none;"{/if}>
							<label>{l s='Артикул:'} </label>
								<span class="editable prodSku" itemprop="sku">{$product.reference}</span>
							</p>
					</h5>

					{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
					{if $smarty.capture.displayProductListReviews}
						<div class="hook-reviews">
						{hook h='displayProductListReviews' product=$product}
						</div>
					{/if}
					<p class="product-desc" itemprop="description">
						{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
					</p>
					{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					

					<p id="product_reference"{if empty($product->reference) || !$product->reference} style="display: none;"{/if}>
				<label>{l s='Артикул:'} </label>
				<span class="editable prodSku" itemprop="sku"{if !empty($product->reference) && $product->reference} content="{$product->reference}"{/if}>{if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}</span>
			</p>

					<!-- {if isset($product.reference)}
					<p class="product_ref">{l s='Артикул:'} #{$product.reference|escape:'htmlall':'UTF-8'}
					</p>
					{/if} -->
					<!-- {if $page_name == 'index'}
					<p class="product_ref">{l s='Артикул:'} #{$product.reference|escape:'htmlall':'UTF-8'}
					</p>
					{/if} -->


					<div class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							{hook h="displayProductPriceBlock" product=$product type='before_price'}
							<span class="price product-price">
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
								{if $product.specific_prices.reduction_type == 'percentage'}
									<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
								{/if}
							{/if}
							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
							{hook h="displayProductPriceBlock" product=$product type='after_price'}
						{/if}
					</div>
					{/if}


					
					

					<div class="button-container">

						{if false && (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span class="availability">
								{if ($product.allow_oosp || $product.quantity > 0)}
									<span class="{if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} label-warning{else} label-success{/if}">
										{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Отсутствует'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
									</span>
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="label-warning">
										{l s='Product available with different options'}
									</span>
								{else}
									<span class="label-danger">
										{l s='Отсутствует'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
						

					{if $page_name == 'category' || $page_name == 'new-products' || $page_name == 'search'}
					{assign var='productsCart' value=Product::getProductCartInfoStatic($product.id_product)}
					<div class="content_prices clearfix">
						<p>В наличии:
							<span style="padding: 0 2px;">{Product::SGetProductUnity($product.sale_unity)}</span>

							<span itemprop="inStock" >{$product.quantity}</span>

						</p>

						<p>В заказе:
							<span style="padding: 0 2px;">{Product::SGetProductUnity($product.sale_unity)}</span>

							<span itemprop="countInCart" class="ajax_block_cart_count_id_{$product.id_product} ajax_block_cart_total_count_to_null">
							{if !empty($productsCart) }
								{$productsCart.cart_quantity}
							{else}
								0
							{/if}
							</span>

						</p>

						<p>На сумму:
							<span itemprop="totalprice">
								<span class="ajax_block_cart_total_price_id_{$product.id_product} ajax_block_cart_total_price_to_null">
									{if !empty($productsCart) }
										{convertPrice price=$productsCart.total}
									{else}
										{convertPrice price=0}
									{/if}
								</span>
							</span>
						</p>
					</div>
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
					{/if}


					


						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
								{*<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">*}
									{*<span>{l s='ДОБАВИТЬ В КОРЗИНУ'}</span>*}
								{*</a>*}
								<a class="button addToCardBtn  btn-default exclusive"
										id="btnid{$product.id_product}" 
										btncatid="{$product.id_category_default}" 
										type="submit"
										name="Submit"
										
										style="display: inline-block"
										onClick="fancyChangeProductCountInCart(event, {$product.id_product}, 'ajax_input_prod_{$product.id_product}'); yaCounter46713966.reachGoal('ADDCART'); return true;"
								><span>{l s='В ЗАКАЗ'}</span>
								</a>
							{else}
								<span class="button ajax_add_to_cart_button btn btn-default disabled">
									<span>{l s='ДОБАВИТЬ В КОРЗИНУ'}</span>
								</span>
							{/if}
						{/if}
						<!-- <a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize'}{else}{l s='More'}{/if}</span>
						</a> -->
					</div>
					{if isset($product.color_list)}
						<div class="color-list-container">{$product.color_list}</div>
					{/if}
					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<!-- <span class="discount">{l s='Reduced price!'}</span> -->
							{/if}
					</div>
					



				</div>
				{if $page_name != 'index'}
					<div class="functional-buttons clearfix">
						{hook h='displayProductListFunctionalButtons' product=$product}
						{if isset($comparator_max_item) && $comparator_max_item}
							<div class="compare mb-30">
								<a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to Compare'}</a>
							</div>
						{/if}
					</div>
				{/if}
			</div><!-- .product-container> -->
		</li>
	{/foreach}
	</ul>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
<pre style="display:none">
	{print_r($products[0])}
</pre>