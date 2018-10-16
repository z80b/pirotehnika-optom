<div class="ps-product__more-info ps-more-info">
    <input
        class="ps-more-info__switcher ps-more-info__switcher--0"
        type="radio" name="more-info-tab"
        id="more-info-tab-0"
        checked="checked" />
    <input
        class="ps-more-info__switcher ps-more-info__switcher--1"
        type="radio"
        name="more-info-tab"
        id="more-info-tab-1"/>
    <div class="ps-more-info__tabs">
        <label
            class="ps-more-info__tab ps-more-info__tab--0"
            for="more-info-tab-0">{l s='ОПИСАНИЕ'}</label>
        <label
            class="ps-more-info__tab ps-more-info__tab--1"
            for="more-info-tab-1">{l s='ХАРАКТЕРИСТИКИ'}</label>
    </div>
    <div class="ps-more-info__contents">
        <div class="ps-more-info__content ps-more-info__content--0 ps-more-info__description">
            {$product->description}
        </div>
        <div class="ps-more-info__content ps-more-info__content--1 ps-more-info__features">
        {foreach from=$features item=feature}
        <div class="ps-more-info__feature">
            <span class="ps-more-info__feature-name">{$feature.name|escape:'htmlall':'UTF-8'}</span>
            <span class="ps-more-info__feature-value">{$feature.value|escape:'htmlall':'UTF-8'}</span>
        </div>
        {/foreach}
        <div class="ps-more-info__feature">
            <span class="ps-more-info__feature-name">{l s='Manufacturer'}</span>
            <span class="ps-more-info__feature-value">{$product_manufacturer->name}</span>
        </div>
        <div class="ps-more-info__feature">
            <span class="ps-more-info__feature-name">{l s='Weight'}</span>
            <span class="ps-more-info__feature-value">{$product->weight|intval} кг</span>
        </div>
        <div class="ps-more-info__feature">
            <span class="ps-more-info__feature-name">{l s='Sizes'}</span>
            <span class="ps-more-info__feature-value">{$product->width|intval} x {$product->height|intval} x {$product->depth|intval} мм</span>
        </div>
        <div class="ps-more-info__feature">
            <span class="ps-more-info__feature-name">{l s='Certificate'}</span>
            {if isset($product_attachments) && isset($product_attachments[0])}
            <a  class="ps-more-info__feature-value js-product-certificates-divnk"
                href="{$product_attachments[0]['src']}"
                data-fancybox-group="attachements-group"
                data-fancybox-type="image">{$product->sert}</a>
            <a href="{$product_attachments[1]['src']}" class="ps-product__attachments js-product-certificates-divnk" data-fancybox-group="attachements-group" data-fancybox-type="image"></a>
            {else}
            <span class="ps-more-info__feature-value">{$product->sert}</span>
            {/if}
        </div>
        </div>
    </div>
</div>