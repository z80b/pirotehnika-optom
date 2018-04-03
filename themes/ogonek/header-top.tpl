<div class="ps-head">
    <div class="ps-head__logo-block ps-head__block">
        <a  class="ps-head__logo"
            title="{$shop_name|escape:'html':'UTF-8'}"
            href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}"
            style="background-image: url({$logo_url})"></a>
    </div>
    {if $page_name =='index'}
    <div class="ps-head__title-block ps-head__block">
        <h1 class="ps-head__title">Первый оптовый<br/>интернет-магазин пиротехники<br/>с доставкой по России</h1>
    </div>
    {/if}
    <div class="ps-head__contacts-block ps-head__block">
    {hook h="displayTop" mod="blockcontactinfos"}
    </div>
</div>
<div class="ps-head__cart">{hook h="top" mod="blockcart"}</div>