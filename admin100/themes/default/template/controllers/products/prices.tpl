﻿{*
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

<script type="text/javascript">
var Customer = new Object();
var product_url = '{$link->getAdminLink('AdminProducts', true)|addslashes}';
var ecotax_tax_excl = parseFloat({$ecotax_tax_excl});
var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};

$(document).ready(function () {
	Customer = {
		"hiddenField": jQuery('#id_customer'),
		"field": jQuery('#customer'),
		"container": jQuery('#customers'),
		"loader": jQuery('#customerLoader'),
		"init": function() {
			jQuery(Customer.field).typeWatch({
				"captureLength": 1,
				"highlight": true,
				"wait": 50,
				"callback": Customer.search
			}).focus(Customer.placeholderIn).blur(Customer.placeholderOut);
		},
		"placeholderIn": function() {
			if (this.value == '{l s='All customers'}') {
				this.value = '';
			}
		},
		"placeholderOut": function() {
			if (this.value == '') {
				this.value = '{l s='All customers'}';
			}
		},
		"search": function()
		{
			Customer.showLoader();
			jQuery.ajax({
				"type": "POST",
				"url": "{$link->getAdminLink('AdminCustomers')|addslashes}",
				"async": true,
				"dataType": "json",
				"data": {
					"ajax": "1",
					"token": "{getAdminToken tab='AdminCustomers'}",
					"tab": "AdminCustomers",
					"action": "searchCustomers",
					"customer_search": Customer.field.val()
				},
				"success": Customer.success
			});
		},
		"success": function(result)
		{
			if(result.found) {
				var html = '<ul class="list-unstyled">';
				jQuery.each(result.customers, function() {
					html += '<li><a class="fancybox" href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1">'+this.firstname+' '+this.lastname+'</a>'+(this.birthday ? ' - '+this.birthday:'');
					html += ' - '+this.email;
					html += '<a onclick="Customer.select('+this.id_customer+', \''+this.firstname+' '+this.lastname+'\'); return false;" href="#" class="btn btn-default">{l s='Choose'}</a></li>';
				});
				html += '</ul>';
			}
			else
				html = '<div class="alert alert-warning">{l s='No customers found'}</div>';
			Customer.hideLoader();
			Customer.container.html(html);
			jQuery('.fancybox', Customer.container).fancybox();
		},
		"select": function(id_customer, fullname)
		{
			Customer.hiddenField.val(id_customer);
			Customer.field.val(fullname);
			Customer.container.empty();
			return false;
		},
		"showLoader": function() {
			Customer.loader.fadeIn();
		},
		"hideLoader": function() {
			Customer.loader.fadeOut();
		}
	};
	Customer.init();
});
</script>
{capture assign=priceDisplayPrecisionFormat}{'%.'|cat:$smarty.const._PS_PRICE_DISPLAY_PRECISION_|cat:'f'}{/capture}
<div id="product-prices" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Prices" />
	<h3>{l s='Product price'}</h3>
	<div class="alert alert-info" {if !$country_display_tax_label || $tax_exclude_taxe_option}style="display:none;"{/if}>
		{l s='You must enter either the pre-tax retail price, or the retail price with tax. The input field will be automatically calculated.'}
	</div>
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Prices"}
	<div class="form-group">
<!-- Единица измерения для продажи  -->	
	<div id="sale_unity" class="form-group">
		<label class="control-label col-lg-3">Товар продается в:</label>
		<div class="col-lg-9">
			<p class="radio">
				<label id="label_sale_unity_1" for="sale_unity_1">
					<input onchange="javascript:calcEd(0); chng1(); chngprc()" type="radio" id="sale_unity_1" name="sale_unity" checked="checked" value="0" class="sale_unity" {if $product->sale_unity == 0} checked="checked"  {/if}>
					штуках
				</label>
			</p>
			<p class="radio">
				<label id="label_sale_unity_2" for="sale_unity_2">
					<input onchange="javascript:calcEd(1); chng1(); chngprc()" type="radio" id="sale_unity_2" name="sale_unity" value="1" class="sale_unity" {if $product->sale_unity == 1} checked="checked" {/if}>
					упаковках
				</label>
			</p>
			<p class="radio">
				<label id="label_sale_unity_3" for="sale_unity_3">
					<input onchange="javascript:calcEd(2); chng1(); chngprc()" type="radio" id="sale_unity_3" name="sale_unity" value="2" class="sale_unity" {if $product->sale_unity == 2} checked="checked" {/if}>
					блоках
				</label>
			</p>
		</div>
	</div>	
	
<!-- Единиц измерения в коробке -->
	<div id='edizm' class="form-group">
		<label class="control-label col-lg-3" for="r3">
			<span class="label-tooltip" data-toggle="tooltip"
				title="Количество блоков в транспортной коробке">
				{$bullet_common_field} Единиц измерения в коробке
			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="13" type="text" id="r3" name="r3" value="{$product->r3|htmlentitiesUTF8}" />
		</div>
	</div>
	<!-- Упаковок в блоке -->
	<div id='upvbl' class="form-group">
		<label class="control-label col-lg-3" for="r2">
			<span class="label-tooltip " data-toggle="tooltip"
				title="Количество упаковок в блоке">
				{$bullet_common_field} Упаковок в блоке
			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="13" type="text" id="r2" name="r2" value="{$product->r2|htmlentitiesUTF8}" />
		</div>
	</div>
	<!-- Штук в упаковке -->
	<div id='shtvup' class="form-group">
		<label class="control-label col-lg-3" for="r1">
			<span class="label-tooltip" data-toggle="tooltip"
				title="Количество единиц товара в упаковке">
				{$bullet_common_field} Штук в упаковке
			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="13" type="text" id="r1" name="r1" value="{$product->r1|htmlentitiesUTF8}" />
		</div>
	</div>

	
<!-- Единица измерения для продажи ^ -->	
	<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="wholesale_price" type="default"}</span></div>
		<label class="control-label col-lg-2" for="wholesale_price">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='The wholesale price is the price you paid for the product. Do not include the tax.'}">{if !$country_display_tax_label || $tax_exclude_taxe_option}{l s='Wholesale price'}{else}{l s='Pre-tax wholesale price'}{/if}</span>
		</label>
		<div class="col-lg-2">
			<div class="input-group">
				<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
				<input maxlength="27" name="wholesale_price" id="wholesale_price" type="text" value="{{toolsConvertPrice price=$product->wholesale_price}|string_format:$priceDisplayPrecisionFormat}" onchange="this.value = this.value.replace(/,/g, '.');" />
			</div>
			{if isset($pack) && $pack->isPack($product->id)}<p class="help-block">{l s='The sum of wholesale prices of the products in the pack is %s%s%s' sprintf=[$currency->prefix,{toolsConvertPrice price=$pack->noPackWholesalePrice($product->id)|string_format:$priceDisplayPrecisionFormat},$currency->suffix]}</p>{/if}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="price" type="price"}</span></div>
		<label class="control-label col-lg-2" for="priceTE">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='The pre-tax retail price is the price for which you intend sell this product to your customers. It should be higher than the pre-tax wholesale price: the difference between the two will be your margin.'}">{if !$country_display_tax_label || $tax_exclude_taxe_option}{l s='Retail price'}{else}{l s='Pre-tax retail price'}{/if}</span>
		</label>
		<div class="col-lg-2">
			<div class="input-group">
				<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
				<input type="hidden" id="priceTEReal" name="price" value="{toolsConvertPrice price=$product->price}"/>
				<input size="11" maxlength="27" id="priceTE" name="price_displayed" type="text" value="{{toolsConvertPrice price=$product->price}|string_format:'%.6f'}" onchange="noComma('priceTE'); $('#priceTEReal').val(this.value);" onkeyup="$('#priceType').val('TE'); $('#priceTEReal').val(this.value.replace(/,/g, '.')); if (isArrowKey(event)) return; calcPriceTI();" />
			</div>
		</div>
	</div>
	<script>
	function chng(){
	if(document.getElementById('sale_unity_1').checked) {
	calcEd(0);
  document.getElementById('shtvup').style.display='none';
  document.getElementById('r1').value='1';
  document.getElementById('upvbl').style.display='none';
  document.getElementById('r2').value='1';
}else if(document.getElementById('sale_unity_2').checked) {
calcEd(1);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='none';
 document.getElementById('r2').value='1';
}
else if(document.getElementById('sale_unity_3').checked) {
calcEd(2);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='block';
 
}}
function chng1(){
	if(document.getElementById('sale_unity_1').checked) {
	calcEd(0);
  document.getElementById('shtvup').style.display='none';
 // document.getElementById('r1').value='1';
  document.getElementById('upvbl').style.display='none';
 // document.getElementById('r2').value='1';
}else if(document.getElementById('sale_unity_2').checked) {
calcEd(1);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='none';
// document.getElementById('r2').value='1';
}
else if(document.getElementById('sale_unity_3').checked) {
calcEd(2);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='block';
 
}}
chng();
</script>
<script>
function chngprc(){
	if(document.getElementById('sale_unity_1').checked) {
	calcEd(0);
  document.getElementById('unit_price').value='0';
  document.getElementById('unity').value=' ';
 // document.getElementById('r1').value='1';
 // document.getElementById('r2').value='1';
}else if(document.getElementById('sale_unity_2').checked) {
calcEd(1);
document.getElementById('unit_price').value=document.getElementById('priceTE').value/document.getElementById('r1').value;
  document.getElementById('unity').value='шт.';
// document.getElementById('r2').value='1';
}
else if(document.getElementById('sale_unity_3').checked) {
calcEd(2);
 document.getElementById('unit_price').value=document.getElementById('priceTE').value/document.getElementById('r2').value;
  document.getElementById('unity').value='уп.';
}}

</script>
<!-- <!-- Иванов  -->	
<!-- Максимальная скидка -->	
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="id_tax_rules_group" type="default"}</span></div>
		<label class="control-label col-lg-2" for="priceTE">
				<span class="label-tooltip">Скидка не может быть больше чем</span>
		</label>
			<div class="input-group">
				<input maxlength="12" name="max_discount" id="max_discount" type="text" value="{$product->max_discount}"  />
			</div>
	</div>
	
	
	
	<div class='block' id="blockkk">
	<p class='zagolovok'>Партионный учет</p>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="id_tax_rules_group" type="default"}</span></div>
		<label class="control-label col-lg-2"> <!--for="priceTE"-->
				<span class="label-tooltip">Всего товара</span>
		</label>
			<div class="input-group">
				<input maxlength="12" name="vsego" id="vsego" type="text"  readonly='true' value="{$product->quantity}"/> <!-- value="{$product->max_discount}"-->
			</div>
	</div>
	
	
	<!--<table class='table' border="1">
   <caption>Партионный учет</caption>
   <tr><th>Количество товара</th>
   <th>Срок годности товара</th>
   <th>Срок годности товара до</th>
   </tr>
   style="display:none;"
<tr>
<td>{$ppp1['sg1']}</td><td>{$pppp['sgt1']}</td><td>{$row->quantity}</td>
</tr>

   
</table>-->

<div>{$product->gg()}</div>
<a href="#blockkk" onclick="partuch('partia')">Добавить партию</a>
<script>
function partuch(id) {
    if ( document.getElementById(id).style.display != "none" ) {
        document.getElementById(id).style.display = 'none';
    }
    else {
        document.getElementById(id).style.display = 'block';
    }
}
</script>



<div id="partia" style="display:none;">


<div class="osnovnoe">
<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="quantity1" type="default"}</span></div>
		<label class="control-label col-lg-2"> <!--for="priceTE"-->
				<span class="label-tooltip">Количество товара</span>
		</label>
			<div class="input-group">
			
				<input maxlength="12" name="quantity1" id="quantity1" type="text" value="{$product->quantity1}"/> <!-- value="{$product->max_discount}"-->
			</div>
	</div>
		<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="sg1" type="default"}</span></div>
		<label class="control-label col-lg-2"> <!--for="priceTE"-->
				<span class="label-tooltip">Срок годности</span>
		</label>
			<div class="input-group">
			
				<input maxlength="12" name="sg1" id="sg1" type="text" value="{$product->sg1}"/> <!-- value="{$product->max_discount}"-->
			</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="sgt1" type="default"}</span></div>
		<label class="control-label col-lg-2"> <!--for="priceTE"-->
				<span class="label-tooltip">Срок годности до</span>
		</label>
			<div class="input-group">
				<input maxlength="12" name="sgt1" id="sgt1" type="text" value="{$product->sgt1}" /> <!-- value="{$product->max_discount}"-->
			</div>
	</div>
	</div>
	
	<div class="prosr">
	  <input type="radio" id="type1"
     name="type1" value="0">
    <label for="type1">Просрочен</label><br>

    <input type="radio" id="type2"
     name="type1" value="1" checked>
    <label for="type2">Продается</label><br>
		</div>
	<!--<button onclick="" type="submit" name="submitAddpart" class="btn btn-default">Добавить партию</button>-->

	</div></div>
	
<!-- Максимальная скидка ^ -->	
<!-- Единица измерения поставщика невидимо -->	
	<div id="sale_unity" class="form-group" style="display:none;">
		<label class="control-label col-lg-3">Единица измерения поставщика</label>
		<div class="col-lg-9">
			<p class="radio">
				<label id="label_parent_sale_unity_1" for="parent_sale_unity_1" style="display:none;">
					<input type="radio" id="parent_sale_unity_1" name="parent_sale_unity" checked="checked" value="0" class="parent_sale_unity" {if $product->parent_sale_unity == 0} checked="checked" {/if}>
					штука
				</label>
			</p>
			<p class="radio">
				<label id="label_parent_sale_unity_2" for="parent_sale_unity_2">
					<input type="radio" id="parent_sale_unity_2" name="parent_sale_unity" value="1" class="parent_sale_unity" {if $product->parent_sale_unity == 1} checked="checked" {/if}>
					упаковка
				</label>
			</p>
			<p class="radio">
				<label id="label_parent_sale_unity_3" for="parent_sale_unity_3">
					<input type="radio" id="parent_sale_unity_3" name="parent_sale_unity" value="2" class="parent_sale_unity" {if $product->parent_sale_unity == 2} checked="checked" {/if}>
					блок
				</label>
			</p>
		</div>
	</div>
<!-- Коэффициент цена и количества  невидимо -->	
			<div class="input-group" style="display:none;">
				<label class="control-label col-lg-3">Коэффициент передачи</label>
				<input maxlength="50" name="sale_unit_price_ratio" id="sale_unit_price_ratio" type="text" value="{{toolsConvertPrice price=$product->sale_unit_price_ratio}|string_format:$priceDisplayPrecisionFormat}"  />
			</div>
<!-- Описание фасовки  невидимо -->	
			<div class="input-group" style="display:none;">
				<label class="control-label col-lg-3">Описание фасовки для фронт офиса</label>
				<input maxlength="50" name="sale_unity_pack" id="sale_unity_pack" type="text" value="{$product->sale_unity_pack}"  />
			</div>
<!-- Иванов ^ -->	
		<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="id_tax_rules_group" type="default"}</span></div>
		<label class="control-label col-lg-2" for="id_tax_rules_group">
			{l s='Tax rule:'}
		</label>
		<div class="col-lg-8">
			<script type="text/javascript">
				noTax = {if $tax_exclude_taxe_option}true{else}false{/if};
				taxesArray = new Array();
				{foreach $taxesRatesByGroup as $tax_by_group}
					taxesArray[{$tax_by_group.id_tax_rules_group}] = {$tax_by_group|json_encode};
				{/foreach}
				ecotaxTaxRate = {$ecotaxTaxRate / 100};
			</script>
			<div class="row">
				<div class="col-lg-6">
					<select onchange="javascript:calcPrice(); unitPriceWithTax('unit');" name="id_tax_rules_group" id="id_tax_rules_group" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
						<option value="0">{l s='No Tax'}</option>
					{foreach from=$tax_rules_groups item=tax_rules_group}
						<option value="{$tax_rules_group.id_tax_rules_group}" {if $product->getIdTaxRulesGroup() == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
					{$tax_rules_group['name']|htmlentitiesUTF8}
						</option>
					{/foreach}
					</select>
				</div>
				<div class="col-lg-2">
					<a class="btn btn-link confirm_leave" href="{$link->getAdminLink('AdminTaxRulesGroup')|escape:'html':'UTF-8'}&amp;addtax_rules_group&amp;id_product={$product->id}"{if $tax_exclude_taxe_option} disabled="disabled"{/if}>
						<i class="icon-plus-sign"></i> {l s='Create new tax'} <i class="icon-external-link-sign"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
	{if $tax_exclude_taxe_option}
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert">
				{l s='Taxes are currently disabled:'}
				<a href="{$link->getAdminLink('AdminTaxes')|escape:'html':'UTF-8'}">{l s='Click here to open the Taxes configuration page.'}</a>
				<input type="hidden" value="{$product->getIdTaxRulesGroup()}" name="id_tax_rules_group" />
			</div>
		</div>
	</div>
	{/if}
	<div class="form-group" {if !$ps_use_ecotax} style="display:none;"{/if}>
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="ecotax" type="default"}</span></div>
		<label class="control-label col-lg-2" for="ecotax">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='The ecotax is a local set of taxes intended to "promote ecologically sustainable activities via economic incentives". It is already included in retail price: the higher this ecotax is, the lower your margin will be.'}">{l s='Ecotax (tax incl.)'}</span>
		</label>
		<div class="input-group col-lg-2">
			<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
			<input maxlength="27" id="ecotax" name="ecotax" type="text" value="{$product->ecotax|string_format:$priceDisplayPrecisionFormat}" onkeyup="$('#priceType').val('TI');if (isArrowKey(event))return; calcPriceTE(); this.value = this.value.replace(/,/g, '.'); if (parseInt(this.value) > getE('priceTE').value) this.value = getE('priceTE').value; if (isNaN(this.value)) this.value = 0;" />
		</div>
	</div>
	<div class="form-group" {if !$country_display_tax_label || $tax_exclude_taxe_option}style="display:none;"{/if} >
		<label class="control-label col-lg-3" for="priceTI">{l s='Retail price with tax'}</label>
		<div class="input-group col-lg-2">
			<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
			<input id="priceType" name="priceType" type="hidden" value="TE" />
			<input id="priceTI" name="priceTI" type="text" value="" onchange="noComma('priceTI');" maxlength="27" onkeyup="$('#priceType').val('TI');if (isArrowKey(event)) return;  calcPriceTE();" />
		</div>
		{if isset($pack) && $pack->isPack($product->id)}<p class="col-lg-9 col-lg-offset-3 help-block">{l s='The sum of prices of the products in the pack is %s%s%s' sprintf=[$currency->prefix,{toolsConvertPrice price=$pack->noPackPrice($product->id)|string_format:$priceDisplayPrecisionFormat},$currency->suffix]}</p>{/if}
	</div>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="unit_price" type="unit_price"}</span></div>
		<label class="control-label col-lg-2" for="unit_price">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='When selling a pack of items, you can indicate the unit price for each item of the pack. For instance, "per bottle" or "per pound".'}">{l s='Unit price (tax excl.)'}</span>
		</label>
		<div class="col-lg-4">
			<div class="input-group">
				<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
				<input id="unit_price" name="unit_price" type="text" value="{$unit_price|string_format:'%.6f'}" maxlength="27" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); unitPriceWithTax('unit');"/>
				<span class="input-group-addon">{l s='per'}</span>
				<input id="unity" name="unity" type="text" value="{$product->unity|htmlentitiesUTF8}"  maxlength="255" onkeyup="if (isArrowKey(event)) return ;unitySecond();" onchange="unitySecond();"/>
			</div>
		</div>
	</div>
	{if isset($product->unity) && $product->unity}
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-warning">
				<span>{l s='or'}
					{$currency->prefix}<span id="unit_price_with_tax">0.00</span>{$currency->suffix}
					{l s='per'} <span id="unity_second">{$product->unity}</span>{if $ps_tax && $country_display_tax_label} {l s='(tax incl.)'}{/if}
				</span>
			</div>
		</div>
	</div>
	{/if}
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right">{include file="controllers/products/multishop/checkbox.tpl" field="on_sale" type="default"}</span></div>
		<label class="control-label col-lg-2" for="on_sale">&nbsp;</label>
		<div class="col-lg-9">
			<div class="checkbox">
				<label class="control-label" for="on_sale" >
					<input type="checkbox" name="on_sale" id="on_sale" {if $product->on_sale}checked="checked"{/if} value="1" />
					{l s='Display the "on sale" icon on the product page, and in the text found within the product listing.'}
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-warning">
				<strong>{l s='Final retail price:'}</strong>
				<span>
					{$currency->prefix}
					<span id="finalPrice" >0.00</span>
					{$currency->suffix}
					<span{if !$ps_tax} style="display:none;"{/if}> ({l s='tax incl.'})</span>
				</span>
				<span{if !$ps_tax} style="display:none;"{/if} >
				{if $country_display_tax_label}
					/
				{/if}
					{$currency->prefix}
				<span id="finalPriceWithoutTax"></span>
					{$currency->suffix}
					{if $country_display_tax_label}({l s='tax excl.'}){/if}
				</span>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button onclick='chng(); chngprc()' type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button onclick='chng(); chngprc()' type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
{if isset($specificPriceModificationForm)}
<div class="panel">
	<h3>{l s='Specific prices'}</h3>
	<div class="alert alert-info">
		{l s='You can set specific prices for clients belonging to different groups, different countries, etc.'}
	</div>
	<div class="form-group">
		<div class="col-lg-12">
			<a class="btn btn-default" href="#" id="show_specific_price">
				<i class="icon-plus-sign"></i> {l s='Add a new specific price'}
			</a>
			<a class="btn btn-default" href="#" id="hide_specific_price" style="display:none">
				<i class="icon-remove text-danger"></i> {l s='Cancel new specific price'}
			</a>
		</div>
	</div>
	<script type="text/javascript">
		var product_prices = new Array();
		{foreach from=$combinations item='combination'}
			product_prices['{$combination.id_product_attribute}'] = '{$combination.price|@addcslashes:'\''}';
		{/foreach}
	</script>
	<div id="add_specific_price" class="well clearfix" style="display: none;">
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label col-lg-2" for="{if !$multi_shop}spm_currency_0{else}sp_id_shop{/if}">{l s='For'}</label>
				<div class="col-lg-9">
					<div class="row">
					{if !$multi_shop}
						<input type="hidden" name="sp_id_shop" value="0" />
					{else}
						<div class="col-lg-3">
							<select name="sp_id_shop" id="sp_id_shop">
								{if !$admin_one_shop}<option value="0">{l s='All shops'}</option>{/if}
								{foreach from=$shops item=shop}
								<option value="{$shop.id_shop}">{$shop.name|htmlentitiesUTF8}</option>
								{/foreach}
							</select>
						</div>
					{/if}
						<div class="col-lg-3">
							<select name="sp_id_currency" id="spm_currency_0" onchange="changeCurrencySpecificPrice(0);">
								<option value="0">{l s='All currencies'}</option>
								{foreach from=$currencies item=curr}
								<option value="{$curr.id_currency}">{$curr.name|htmlentitiesUTF8}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_id_country" id="sp_id_country">
								<option value="0">{l s='All countries'}</option>
								{foreach from=$countries item=country}
								<option value="{$country.id_country}">{$country.name|htmlentitiesUTF8}</option>
								{/foreach}
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_id_group" id="sp_id_group">
								<option value="0">{l s='All groups'}</option>
								{foreach from=$groups item=group}
								<option value="{$group.id_group}">{$group.name}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="customer">{l s='Customer'}</label>
				<div class="col-lg-4">
					<input type="hidden" name="sp_id_customer" id="id_customer" value="0" />
					<div class="input-group">
						<input type="text" name="customer" value="{l s='All customers'}" id="customer" autocomplete="off" />
						<span class="input-group-addon"><i id="customerLoader" class="icon-refresh icon-spin" style="display: none;"></i> <i class="icon-search"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-10 col-lg-offset-2">
					<div id="customers"></div>
				</div>
			</div>
			{if $combinations|@count != 0}
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_id_product_attribute">{l s='Combination:'}</label>
				<div class="col-lg-4">
					<select id="sp_id_product_attribute" name="sp_id_product_attribute">
						<option value="0">{l s='Apply to all combinations'}</option>
					{foreach from=$combinations item='combination'}
						<option value="{$combination.id_product_attribute}">{$combination.attributes}</option>
					{/foreach}
					</select>
				</div>
			</div>
			{/if}
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_from">{l s='Available'}</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon">{l s='from'}</span>
								<input type="text" name="sp_from" class="datepicker" value="" style="text-align: center" id="sp_from" />
								<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon">{l s='to'}</span>
								<input type="text" name="sp_to" class="datepicker" value="" style="text-align: center" id="sp_to" />
								<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_from_quantity">{l s='Starting at'}</label>
				<div class="col-lg-4">
					<div class="input-group">
						<span class="input-group-addon">{l s='unit'}</span>
						<input type="text" name="sp_from_quantity" id="sp_from_quantity" value="1" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_price">{l s='Product price'}
					{if $country_display_tax_label}
						{l s='(tax excl.)'}
					{/if}
				</label>
				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-4">
							<div class="input-group">
								<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
								<input type="text" disabled="disabled" name="sp_price" id="sp_price" value="{$product->price|string_format:$priceDisplayPrecisionFormat}" />
							</div>
							<p class="checkbox">
								<label for="leave_bprice">{l s='Leave base price:'}</label>
								<input type="checkbox" id="leave_bprice" name="leave_bprice"  value="1" checked="checked"  />
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-2" for="sp_reduction">{l s='Apply a discount of'}</label>
				<div class="col-lg-4">
					<div class="row">
						<div class="col-lg-3">
							<input type="text" name="sp_reduction" id="sp_reduction" value="0.00"/>
						</div>
						<div class="col-lg-6">
							<select name="sp_reduction_type" id="sp_reduction_type">
								<option value="amount" selected="selected">{$currency->name|escape:'html':'UTF-8'}</option>
								<option value="percentage">{l s='%'}</option>
							</select>
						</div>
						<div class="col-lg-3">
							<select name="sp_reduction_tax" id="sp_reduction_tax">
								<option value="0">{l s='Tax excluded'}</option>
								<option value="1" selected="selected">{l s='Tax included'}</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var currencyName = '{$currency->name|escape:'html':'UTF-8'|@addcslashes:'\''}';
		$(document).ready(function(){
			product_prices['0'] = $('#sp_current_ht_price').html();
			$('#id_product_attribute').change(function() {
				$('#sp_current_ht_price').html(product_prices[$('#id_product_attribute option:selected').val()]);
			});
			$('#leave_bprice').click(function() {
				if (this.checked)
					$('#sp_price').attr('disabled', 'disabled');
				else
					$('#sp_price').removeAttr('disabled');
			});
			$('.datepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText: '{l s='Now' js=1}',
				closeText: '{l s='Done' js=1}',
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: '{l s='Choose Time' js=1}',
				timeText: '{l s='Time' js=1}',
				hourText: '{l s='Hour' js=1}',
				minuteText: '{l s='Minute' js=1}'
			});
			$('#sp_reduction_type').on('change', function() {
				if (this.value == 'percentage')
					$('#sp_reduction_tax').hide();
				else
					$('#sp_reduction_tax').show();
			});
		});
	</script>
	<div class="table-responsive">
	<table id="specific_prices_list" class="table table-bordered">
		<thead>
			<tr>
				<th>{l s='Rule'}</th>
				<th>{l s='Combination'}</th>
				{if $multi_shop}<th>{l s='Shop'}</th>{/if}
				<th>{l s='Currency'}</th>
				<th>{l s='Country'}</th>
				<th>{l s='Group'}</th>
				<th>{l s='Customer'}</th>
				{if $country_display_tax_label}
					<th>{l s='Fixed price (tax excl.)'}</th>
				{else}
					<th>{l s='Fixed price'}</th>
				{/if}
				<th>{l s='Impact'}</th>
				<th>{l s='Period'}</th>
				<th>{l s='From (quantity)'}</th>
				<th>{l s='Action'}</th>
			</tr>
		</thead>
		<tbody>
			{$specificPriceModificationForm}
				<script type="text/javascript">
					$(document).ready(function() {
						delete_price_rule = '{l s="Do you really want to remove this price rule?"}';
						calcPriceTI();
						unitPriceWithTax('unit');
						});
				</script>
			{/if}
<script>
	if(document.getElementById('sale_unity_1').checked) {
	calcEd(0);
  document.getElementById('shtvup').style.display='none';
  document.getElementById('r1').value='1';
  document.getElementById('upvbl').style.display='none';
  document.getElementById('r2').value='1';
}else if(document.getElementById('sale_unity_2').checked) {
calcEd(1);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='none';
 document.getElementById('r2').value='1';
}
else if(document.getElementById('sale_unity_3').checked) {
calcEd(2);
document.getElementById('shtvup').style.display='block';
  document.getElementById('upvbl').style.display='block';
 
}</script>