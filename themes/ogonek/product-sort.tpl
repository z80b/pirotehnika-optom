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
{if isset($orderby) AND isset($orderway)}
	{if $page_name == 'best-sales' && (!isset($smarty.get.orderby) || empty($smarty.get.orderby))}{$orderby = ''}{$orderbydefault = ''}{/if}
	<form id="productsSortForm{if isset($paginationId)}_{$paginationId}{/if}" action="{$request|escape:'html':'UTF-8'}" class="productsSortForm">
		<div class="select selector1">
			<!-- <label for="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}">{l s='Sort by'}</label> -->
			<label for="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}">Сортировка;</label>
			<select id="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}" class="selectProductSort form-control">
				<option value="{if $page_name != 'best-sales'}{$orderbydefault|escape:'html':'UTF-8'}:{$orderwaydefault|escape:'html':'UTF-8'}{/if}"{if !in_array($orderby, array('price', 'name', 'quantity', 'reference')) && $orderby eq $orderbydefault} selected="selected"{/if}>--</option>
				{if !$PS_CATALOG_MODE}
					<option value="price:asc"{if $orderby eq 'price' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Price: Lowest first'}</option>
					<option value="price:desc"{if $orderby eq 'price' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Price: Highest first'}</option>
				{/if}
				<option value="name:asc"{if $orderby eq 'name' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Product Name: A to Z'}</option>
				<option value="name:desc"{if $orderby eq 'name' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Product Name: Z to A'}</option>
				{if $PS_STOCK_MANAGEMENT && !$PS_CATALOG_MODE}
					<option value="quantity:desc"{if $orderby eq 'quantity' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='In stock'}</option>
				{/if}
				<option value="reference:asc"{if $orderby eq 'reference' AND $orderway eq 'asc'} selected="selected"{/if}>{l s='Reference: Lowest first'}</option>
				<option value="reference:desc"{if $orderby eq 'reference' AND $orderway eq 'desc'} selected="selected"{/if}>{l s='Reference: Highest first'}</option>
			</select>
		</div>
		<div class="{if isset($is_show_skid) && $is_show_skid == 1}checkbox{else}hidden{/if}">
			<!--<label><input type="checkbox" value="one" {if isset($is_with_skid) && $is_with_skid == '1'}checked="checked"{/if}>Цены со скидкой</label>-->
		</div>
	</form>
            <div class="page-heading__wrapper content_sortPagiBar">
                {include file="./product-sort-display-type.tpl"}
            </div>
	{if false}
	<form id="productsSortForm{if isset($paginationId)}_{$paginationId}{/if}" action="{$request|escape:'html':'UTF-8'}" class="{if $manufacts && count($manufacts) && true}productsManufForm{else}hidden{/if}">
		<div class="select selector1">
			<!-- <label for="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}">{l s='Sort by'}</label> -->
			<label for="selectManufFilter{if isset($paginationId)}_{$paginationId}{/if}">&nbsp;&nbsp;Торг.марка</label>
			<select id="selectManufFilter{if isset($paginationId)}_{$paginationId}{/if}" class="selectManufFilter form-control">
				<option value="all" {if $current_manuf == ''}selected="selected"{/if}>Все</option>
				{foreach from=$manufacts item=manufact}
					<option value="{$manufact.id_manufacturer}" {if $current_manuf == $manufact.id_manufacturer}selected="selected"{/if}>{$manufact.name}</option>
				{/foreach}
			</select>
		</div>
		<div class="{if isset($is_show_skid) && $is_show_skid == 1}checkbox{else}hidden{/if}">
			<!--<label><input type="checkbox" value="one" {if isset($is_with_skid) && $is_with_skid == '1'}checked="checked"{/if}>Цены со скидкой</label>-->
		</div>
	</form>
	{/if}
<!-- /Sort products -->
	{if !isset($paginationId) || $paginationId == ''}
		{addJsDef request=$request}
	{/if}
{else}	
	<form id="productsSortForm{if isset($paginationId)}_{$paginationId}{/if}" action="{$request|escape:'html':'UTF-8'}" class="{if $manufacts && count($manufacts) && true}productsManufForm{else}hidden{/if}">
		<div class="select selector1">
			<!-- <label for="selectProductSort{if isset($paginationId)}_{$paginationId}{/if}">{l s='Sort by'}</label> -->
			<label for="selectManufFilter{if isset($paginationId)}_{$paginationId}{/if}">&nbsp;&nbsp;Торг.марка</label>
			<select id="selectManufFilter{if isset($paginationId)}_{$paginationId}{/if}" class="selectManufFilter form-control">
				<option value="all" {if $current_manuf == ''}selected="selected"{/if}>Все</option>
				{foreach from=$manufacts item=manufact}
					<option value="{$manufact.id_manufacturer}" {if $current_manuf == $manufact.id_manufacturer}selected="selected"{/if}>{$manufact.name}</option>
				{/foreach}
			</select>
		</div>
		<div class="{if isset($is_show_skid) && $is_show_skid == 1}checkbox{else}hidden{/if}">
			<!--<label><input type="checkbox" value="one" {if isset($is_with_skid) && $is_with_skid == '1'}checked="checked"{/if}>Цены со скидкой</label>-->
		</div>
	</form>
{/if}
