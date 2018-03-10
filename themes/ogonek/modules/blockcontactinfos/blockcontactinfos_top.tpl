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

<!-- MODULE Block contact infos -->
<section id="block_contact_infos" class="header-block col-xs-12 col-sm-3 mt-20">
    <div class="out_box">
        <div class="phone">
            {if $blockcontactinfos_phone != ''}
                <div>
                    <span></span>{$blockcontactinfos_phone|escape:'html':'UTF-8'}
                </div>
            {/if}
        </div>
 <!--        <p class="fs-18">(Звонок бесплатный)</p>
 -->        <a class="contactUsBtn" href="/contact-us">Свяжитесь с нами</a>
        <div class="clearfix"></div>
    </div>
<!--Закрывается в другом модуле: blockcart>
<!-- /MODULE Block contact infos -->
