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
{if !isset($content_only) || !$content_only}



					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->


{if $page_name == 'product'}
<div class="container">
{if isset($accessories) && $accessories}
			<!--Accessories -->
			<section class="page-product-box">
				<h3 class="page-product-heading">{l s='Сопутствующие товары'}</h3>
				<div class="block products_block accessories-block clearfix">
					<div class="block_content">
						<ul id="bxslider" class="bxslider clearfix">
							{foreach from=$accessories item=accessory name=accessories_list}
								{if ($accessory.allow_oosp || $accessory.quantity_all_versions > 0 || $accessory.quantity > 0) && $accessory.available_for_order && !isset($restricted_country_mode)}
									{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
									<li class="item product-box ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if} product_accessories_description">
										<div class="product_desc">
											<a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory.legend|escape:'html':'UTF-8'}" class="product-image product_image">
												<img class="lazyOwl" src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$accessory.legend|escape:'html':'UTF-8'}" width="{$homeSize.width}" height="{$homeSize.height}"/>
											</a>
											<div class="block_description">
												<a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{l s='More'}" class="product_description">
													{$accessory.description_short|strip_tags|truncate:25:'...'}
												</a>
											</div>
										</div>
										<div class="s_title_block">
											<h5 class="product-name">
												<a href="{$accessoryLink|escape:'html':'UTF-8'}">
													{$accessory.name|truncate:20:'...':true|escape:'html':'UTF-8'}
												</a>
											</h5>
											{if $accessory.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
											
											<p id="product_reference"{if empty($product->reference) || !$product->reference} style="display: none;"{/if}>
											<label>{l s='Артикул:'} </label>
											<span class="editable" itemprop="sku"{if !empty($product->reference) && $product->reference} content="{$product->reference}"{/if}>{if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}</span>
											</p>

											<span class="price">
												{if $priceDisplay != 1}
													{displayWtPrice p=$accessory.price}
												{else}
													{displayWtPrice p=$accessory.price_tax_exc}
												{/if}
												{hook h="displayProductPriceBlock" product=$accessory type="price"}
											</span>
											{/if}
											{hook h="displayProductPriceBlock" product=$accessory type="after_price"}
										</div>
										<div class="clearfix" style="margin-top:5px">
											<p id="availability_statut"{if !$PS_STOCK_MANAGEMENT || ($product->quantity <= 0 && !$product->available_later && $allow_oosp) || ($product->quantity > 0 && !$product->available_now) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
				{*<span id="availability_label">{l s='Availability:'}</span>*}
				<span id="availability_value" class="label{if $product->quantity <= 0 && !$allow_oosp} label-danger{elseif $product->quantity <= 0} label-warning{else} label-success{/if}">{if $product->quantity <= 0}{if $PS_STOCK_MANAGEMENT && $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{elseif $PS_STOCK_MANAGEMENT}{$product->available_now}{/if}</span>
			</p>
											
											{if !$PS_CATALOG_MODE && ($accessory.allow_oosp || $accessory.quantity > 0) && isset($add_prod_display) && $add_prod_display == 1}
												<div class="no-print">
													<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" data-id-product="{$accessory.id_product|intval}" title="{l s='Add to cart'}">
														<span>{l s='ДОБАВИТЬ В КОРЗИНУ'}</span>
													</a>
												</div>
											{/if}

										</div>
									</li>
								{/if}
							{/foreach}
						</ul>
					</div>
				</div>
			</section>
			<!--end Accessories -->
		{/if}
		</div>
{/if}
{if $page_name !== 'index' && $page_name !== 'order'}
	{include file="$tpl_dir./consult.tpl"}
{/if}

<script type="text/javascript">

		jQuery(document).ready(function() {


	jQuery( 'body' ).upScrollButton(
		{
			upScrollButtonText   :'Наверх',
			heightForButtonAppear:420,
			scrollTopTime        :1500
		}
	);
		});
jQuery.fn.upScrollButton = function( options ) {
	var options = jQuery.extend( {

		heightForButtonAppear : 100, // дистанция от верхнего края окна браузера, при превышении которой кнопка становится видимой
		heightForScrollUpTo : 0, // дистанция от верхнего края окна браузера к которой будет прокручена страница
		scrollTopTime : 500, // длительность прокрутки
		upScrollButtonId : 'move_up', // id кнопки
		upScrollButtonText : 'Наверх', // текст кнопки
		upScrollButtonFadeInTime :0, // длительность эффекта появления кнопки
		upScrollButtonFadeOutTime :300,// длительность ффекта исчезновения кнопки	

	}, options );
	return this.each( function( ) {
	
		jQuery( 'body' ).append( '<a id="' + options.upScrollButtonId + '" href="#">' + options.upScrollButtonText + '</a>' );
		jQuery( window ).scroll( function () {
			if ( jQuery( this ).scrollTop()  > options.heightForButtonAppear )
				jQuery( 'a#' + options.upScrollButtonId  ).fadeIn(options.upScrollButtonFadeInTime );
			else
				jQuery( 'a#' + options.upScrollButtonId ).fadeOut( options.upScrollButtonFadeOutTime );
		});
		jQuery( 'a#' + options.upScrollButtonId ).click( function ( ) {
			jQuery( 'body,html' ).animate( {
				scrollTop: options.heightForScrollUpTo
			}, options.scrollTopTime );
			return false;
		});
	});

}

</script> 





			{if isset($HOOK_FOOTER)}
				<!-- Footer -->
				<div class="footer-container">
					<footer id="footer"  class="container">
						<div class="row">{$HOOK_FOOTER}</div>
						<copyright>© «Огонёк» {date('Y')}</copyright>
					</footer>
				</div><!-- #footer -->
			{/if}
		</div><!-- #page -->
{/if}

{include file="$tpl_dir./global.tpl"}
<div id="test111222" style="width:50px;height:15px;border:1px solid red" onclick="UpdatePriceInMenu()" hidden>333

</div>

<!-- //Падает снег -->
<!-- <script type="text/javascript" src="snow-fall.js"></script> -->


	</body>
</html>
