<div class="b-image-block">
	{if $have_image}
    <div class="b-image-block__preview">
    	<a  class="b-image-block__bigimage-link"
    		href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}">
	        <img
	        	class="b-image-block__bigimage"
	        	src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}"/>
        </a>
    </div>
    {/if}
    <div class="b-image-block__slider">
        <div class="b-image-block__slider-inner js-product-images-slider">
        {if isset($video_id)}
            <a  class="b-image-block__slide b-image-block__slide--video js-product-video-link"
                data-rel="media"
                href="https://www.youtube.com/v/{$video_id}"
                style="background-image: url('/themes/ogonek/img/youtube_icon_small.png') !important">
                <img class="b-image-block__slide-image b-image-block__slide-image--video" src="/themes/ogonek/img/youtube_icon_small.png"/>
            </a>
        {/if}
        {if isset($images) && count($images) > 0}
        	{foreach from=$images item=image name=thumbnails}
        	{assign var=imageIds value="`$product->id`-`$image.id_image`"}
            {if !empty($image.legend)}
                {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
            {else}
                {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
            {/if}
            <a 	class="b-image-block__slide"
            	title="{$imageTitle}"
                data-src="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}"
            	href="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}">
                <img
                	class="b-image-block__slide-image"
                	data-src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}">
            </a>
            {/foreach}
        {/if}
        </div>
        <button class="b-image-block__slider-button b-image-block__slider-button--prev"></button>
        <button class="b-image-block__slider-button b-image-block__slider-button--next"></button>
    </div>
</div>