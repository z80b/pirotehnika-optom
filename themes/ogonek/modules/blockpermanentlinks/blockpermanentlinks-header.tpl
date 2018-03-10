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


<!-- Block permanent links module HEADER -->
<ul id="header_links" class="hidden-xs">
	<!-- <li id="header_link_contact"><a href="{$link->getPageLink('contact', true)|escape:'html'}" title="{l s='contact' mod='blockpermanentlinks'}">{l s='contact' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_sitemap"><a href="{$link->getPageLink('sitemap')|escape:'html'}" title="{l s='sitemap' mod='blockpermanentlinks'}">{l s='sitemap' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_bookmark">
		<script type="text/javascript">writeBookmarkLink('{$come_from}', '{$meta_title|addslashes|addslashes}', '{l s='bookmark' mod='blockpermanentlinks' js=1}');</script>
	</li>
	<li id="header_link_bookmark"><a href="{$link->getCMSLink('1', 'Доставка')|escape:'html'}" title="{l s='Доставка' mod='blockpermanentlinks'}">{l s='Доставка' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_contact"><a href="{$link->getCMSLink('10', 'Оплата')|escape:'html'}" title="{l s='Оплата' mod='blockpermanentlinks'}">{l s='Оплата' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_contact"><a href="{$link->getCMSLink('12', 'o-nas')|escape:'html'}" title="{l s='О нас' mod='blockpermanentlinks'}">{l s='О нас' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_contact"><a href="{$link->getCMSLink('11', 'Отзывы')|escape:'html'}" title="{l s='Отзывы' mod='blockpermanentlinks'}">{l s='Отзывы' mod='blockpermanentlinks'}</a></li>
	<li id="header_link_contact"><a href="{$link->getCMSLink('9', 'Контакты')|escape:'html'}" title="{l s='Контакты' mod='blockpermanentlinks'}">{l s='Контакты' mod='blockpermanentlinks'}</a></li> -->
	{if $is_logged}
	<li id="header_link_contact"><a href="{$link->getCMSLink('45', 'optovyj-prajs-list')|escape:'html'}" title="{l s='Оптовый прайс-лист' mod='blockpermanentlinks'}">{l s='Оптовый прайс-лист' mod='blockpermanentlinks'}</a></li>
	{/if}
</ul>
<!-- /Block permanent links module HEADER -->
