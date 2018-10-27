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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
    <head>
        <meta charset="utf-8" />
        <title>{$meta_title|escape:'html':'UTF-8'}</title>
        {if isset($meta_description) AND $meta_description}
            <meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
        {/if}
        {if isset($meta_keywords) AND $meta_keywords}
            <meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
        {/if}
        <meta name="generator" content="PrestaShop" />
        <meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
        <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
        <link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
        {if isset($css_files)}
            {foreach from=$css_files key=css_uri item=media}
                {if $css_uri == 'lteIE9'}
                    <!--[if lte IE 9]>
                    {foreach from=$css_files[$css_uri] key=css_uriie9 item=mediaie9}
                    <link rel="stylesheet" href="{$css_uriie9|escape:'html':'UTF-8'}" type="text/css" media="{$mediaie9|escape:'html':'UTF-8'}" />
                    {/foreach}
                    <![endif]-->
                {else}
                    <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
                {/if}
            {/foreach}
        {/if}


        {if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
            {$js_def}
            {foreach from=$js_files item=js_uri}
            <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
            {/foreach}
        {/if}
        {$HOOK_HEADER}
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css" media="all" />
        <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
        <!--[if IE 8]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic" rel="stylesheet">
<script type="text/javascript">{literal}
!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?154",t.onload=function(){VK.Retargeting.Init("VK-RTRG-243535-ckbi5"),VK.Retargeting.Hit()},document.head.appendChild(t)}();{/literal}</script>
<noscript><img src="https://vk.com/rtrg?p=VK-RTRG-243535-ckbi5" style="position:fixed; left:-999px;" alt=""/></noscript>
<script>
$(document).ready(function() {
    if(page_name == 'category' || page_name == 'products-comparison' || page_name == 'new-products'){
        $('.button-minus').live('click', function () {
            var $input = $(this).parent().find('input');
            var count = parseInt($input.val()) - 1;
            count = count < 0 ? 0 : count;
            $input.val(count);
            $input.change();
            return false;
        });
        $('.button-plus').live('click', function () {
            var $input = $(this).parent().find('input');
            $input.val(parseInt($input.val()) + 1);
            $input.change();
            return false;
        });
    }
    $("a.fancyImg").fancybox();

});
</script>
<!-- <script type="text/javascript" src="snow-fall.js"></script> -->

    </head>
    <body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'} page--{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{else} show-left-column{/if}{if $hide_right_column} hide-right-column{else} show-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
    <!-- BEGIN CALLPY CODE --><script>{literal}(function(w,t,p,v,c,f,s,r,h,l,d){w[p]="//callpy.com/";w[v]="5.09";w[c]=false;if(t==w){var tmp=l.callpy_data;if(tmp==null||!l.callpy_html||!l[c]){w[f]=false}else{w[f]=true;w[s]=JSON.parse(tmp);var tm=new Date().getTime();if(tm-w[s].lastSave<20000){if(w[s].insertcode){eval(w[s].insertcode)}else{w[f]=false}}else{w[f]=false}}}else{w[f]=false}var callpy_script=d.createElement("script");try{var tmp=parent.window.location.href?1:0}catch(e){var tmp=0}callpy_script.type="text/javascript";callpy_script.async=true;if(!w[f]||!l[h]){l[h]=new Date().getTime()}callpy_script.src=w[p]+"c/"+w.location.host.replace(/www./i,"")+"/"+(t==w?(w[f]?1:2):(tmp==1?4:3))+".js?id=15293&m="+l[h];callpy_script.onload=function(){iowisp.init()};d.body.appendChild(callpy_script)})(window,window.top,"callpy_path","callpy_version","tiny","sven","callpy_storage","callpy_chat_scroller","callpy_lastchat",localStorage,document);{/literal}</script><!-- END CALLPY CODE -->
    {if !isset($content_only) || !$content_only}
        {if isset($restricted_country_mode) && $restricted_country_mode}
            <div id="restricted-country">
                <p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>{/if}</p>
            </div>
        {/if}
        <div id="page">
            <div class="header-container">
                <header id="header">
                    {capture name='displayBanner'}{hook h='displayBanner'}{/capture}
                    {if $smarty.capture.displayBanner}
                        <div class="banner">
                            <div class="container">
                                <div class="row">
                                    {$smarty.capture.displayBanner}
                                </div>
                            </div>
                        </div>
                    {/if}
                    {capture name='displayNav'}{hook h='displayNav'}{/capture}
                    {if $smarty.capture.displayNav}
                        <div class="nav">
                            <div class="container">
                                <div class="row">
                                    {*<nav>{$smarty.capture.displayNav}</nav>*}
                                    <nav>
                                        {hook h="displayNav" mod="blockpermanentlinks"}
                                        {hook h="displayLeftColumn" mod="blockmyaccount" location='top'}
                                        {*include file="$tpl_dir./profile-block.tpl"*}
                                        {hook h="displayTop" mod="blockcart"}
                                        {hook h="displayNav" mod="blocksearch"}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div>
                        <div class="container" >
                            <div class="row">
                                {include file="$tpl_dir./header-top.tpl"}
                                {hook h="displayTop" mod="blocktopmenu"}
                            </div>
                            {if $page_name !='index' && $page_name !='pagenotfound'}
                            {include file="$tpl_dir./breadcrumb.tpl"}
                            {/if}
                        </div>
                    </div>
                </header>
            </div>
            
            <!-- For set background -->
            {if $page_name == 'index'}
            <div class="columns-container slider_home">
                <div id="columns" class="container">
                    <div id="slider_row" class="row">
                        {capture name='displayTopColumn'}{hook h='displayTopColumn'}{/capture}
                        {if $smarty.capture.displayTopColumn}
                            <div id="top_column" class="center_column col-xs-12 col-sm-12">{$smarty.capture.displayTopColumn}</div>
                        {/if}
                    </div>

                </div>
            </div>
            {/if}

            <div class="columns-container">
                <div id="columns" class="container">
                    
                    

                    <div class="row">
                        {if isset($left_column_size) && !empty($left_column_size)}
                        <div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval}">{$HOOK_LEFT_COLUMN}</div>
                        {/if}
                        {if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
                        <div id="center_column" class="center_column col-xs-12 col-sm-{$cols|intval}">
    {/if}
