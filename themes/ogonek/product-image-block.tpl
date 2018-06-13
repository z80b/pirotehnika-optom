<a  class="ps-product__image-zoom fancyImg"
    href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}">
    <img
        class="ps-product__image"
        alt="{$product.name|escape:'html':'UTF-8'}"
        title="{$product.name|escape:'html':'UTF-8'}"
        src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"/>
</a>