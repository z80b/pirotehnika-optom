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
<section id="block_contact_infos" class="footer-block header-block col-xs-12 col-sm-3 f-con">
    <h4 class="title_block">{l s='Contact us' mod='blockcontactinfos'}</h4>
    <ul class="toggle-footer">
        {if 0 && $blockcontactinfos_company != ''}<li><strong>{$blockcontactinfos_company|escape:'html':'UTF-8'}</strong></li>{/if}
        {if $blockcontactinfos_address != ''}<li>Адрес: {$blockcontactinfos_address|escape:'html':'UTF-8'|nl2br}</li>{/if}
        {if $blockcontactinfos_phone != ''}<li>{l s='Tel' mod='blockcontactinfos'} {$blockcontactinfos_phone|escape:'html':'UTF-8'}</li>{/if}
        {if $blockcontactinfos_email != ''}<li>{l s='Email:' mod='blockcontactinfos'} {mailto address=$blockcontactinfos_email|escape:'html':'UTF-8' encode="hex"}</li>{/if}
    </ul>
</section>
<!-- MODULE Block contact infos -->
<!-- <section id="block_contact_infos" class="footer-block f-block col-xs-12 col-sm-4">
	<div class="phone">
    {if $blockcontactinfos_phone != ''}
        <div>
            <span></span>{$blockcontactinfos_phone|escape:'html':'UTF-8'}
        </div>  
    {/if}
    <a href="#">Заказать звонок</a>
    </div>
</section> -->
<!-- /MODULE Block contact infos -->
