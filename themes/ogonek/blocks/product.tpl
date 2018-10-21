{include file="$tpl_dir./errors.tpl"}

{if !isset($priceDisplayPrecision)}
    {assign var='priceDisplayPrecision' value=2}
{/if}
{assign var='productPriceDisc' value=$product->getPriceDisc($product->id)}
{assign var='isShowPriceDisc' value=$product->getIsShowPriceDisc()}
{assign var='isShowPriceWoDisc' value=$product->getIsShowPriceWoDisc()}
{if !$priceDisplay || $priceDisplay == 2}
    {assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
    {assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}
<div class="ps-product__compare-block">
    {include file="$tpl_dir./product-compare.tpl" paginationId='top'}
</div>
<div class="ps-content--product">
    {if isset($prevProduct)}
        <a  class="ps-product__linkbutton ps-product__linkbutton--prev"
            href="{$prevProduct|escape:'html':'UTF-8'}"></a>
    {/if}
    {if isset($nextProduct)}
        <a  class="ps-product__linkbutton ps-product__linkbutton--next"
            href="{$nextProduct|escape:'html':'UTF-8'}"></a>
    {/if}
    <h1 class="ps-content__title">
        <span class="ps-content__title-left">{$product->name|escape:'html':'UTF-8'}</span>
        <span class="ps-content__title-right">{l s='Артикул:'} {if !isset($groups)}{$product->reference|escape:'html':'UTF-8'}{/if}</span>
    </h1>
    <div class="ps-content__row">
        <div class="ps-content__cell">
            <div id="image-block" class="clearfix">
                {if $have_image}
                <a  class="ps-product-page__image-zoom fancyImg"
                    title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"
                    href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}">
                    <img
                        class="ps-product-page__image"
                        src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}"
                        alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
                </a>
                {else}
                <img
                    class="ps-product-page__image"
                    src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg"
                    alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"
                    title="{$product->name|escape:'html':'UTF-8'}"/>
                {/if}
            </div>
            {if isset($images) && count($images) > 0}
                <!-- thumbnails -->
                <div id="views_block" class="clearfix {if isset($images) && count($images) < 2 && (!isset($video_id) || !$video_id)}hidden{/if}">
                    {if isset($images) && count($images) > 2}
                        <span class="view_scroll_spacer">
                            <a id="view_scroll_left" class="" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
                                {l s='Previous'}
                            </a>
                        </span>
                    {/if}
                    <div id="thumbs_list">
                        <ul id="thumbs_list_frame">
                        {if isset($video_id)}
                            <li class="ps-thumbs__video">
                                <a  class="ps-thumbs__video-link"
                                    data-rel="media"
                                    href="https://www.youtube.com/v/{$video_id}"
                                    style="background-image: url('/themes/ogonek/img/youtube_icon_small.png') !important">
                                    <img class="img-responsive ps-thumbs__video-image" src="/themes/ogonek/img/youtube_icon_small.png"/>
                                </a>
                            </li>
                        {/if}
                        {if isset($images)}
                            {foreach from=$images item=image name=thumbnails}
                                {assign var=imageIds value="`$product->id`-`$image.id_image`"}
                                {if !empty($image.legend)}
                                    {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                                {else}
                                    {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
                                {/if}
                                <li id="thumbnail_{$image.id_image}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
                                    <a{if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{literal}{{/literal}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{literal}}{/literal}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}" data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if} title="{$imageTitle}">
                                        <img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}"{if isset($cartSize)} height="{$cartSize.height}" width="{$cartSize.width}"{/if} itemprop="image" />
                                    </a>
                                </li>
                            {/foreach}
                        {/if}
                        </ul>
                    </div> <!-- end thumbs_list -->
                    {if isset($images) && count($images) > 2}
                        <a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
                            {l s='Next'}
                        </a>
                    {/if}
                </div> <!-- end views-block -->
                <!-- end thumbnails -->
            {/if}
        </div>
        <div class="ps-content__cell">
            <div class="ps-content__buttons">
                <a  class="ps-product__compare js-product-compare"
                    href="{$product->link|escape:'html':'UTF-8'}"
                    data-id-product="{$product->id}"
                    title="{l s='К сравнению'}">
                    <i class="fa fa-list"></i>
                    {l s='К сравнению'}
                </a>
            </div>
            {if isset($features) && $features}
            <div class="ps-features">
                <h2 class="ps-features__title">{l s='Характеристики'}</h2>
                <ul class="ps-features__list">
                    {section loop=$features name=id max=2}
                    <li class="ps-features__item ps-feature">
                        <span class="ps-feature__title">{$features[id].name|escape:'htmlall':'UTF-8'}</span>
                        <span class="ps-feature__value">{$features[id].value|escape:'htmlall':'UTF-8'}</span>
                    </li>
                    {/section}
                </ul>
            </div>
            {/if}
            <div class="ps-white-block">
                <div class="ps-price__block">
                    <span class="ps-product__price ps-price">
                        <span class="ps-price">
                            <span class="ps-price__value">
                                {Tools::displayOgonekPrice($product->price, 1)}
                            </span>
                        </span>
                    </span> 
                    {if $product->specificPrice.reduction > 0}
                        <span class="ps-price__value ps-price__value--old">
                            {Tools::displayOgonekPrice($product->base_price, 1)}
                        </span>
                        <span class="ps-price__discount">
                            -{$product->specificPrice.reduction * 100}%
                        </span>
                    {/if}
                </div>
                <div class="ps-price__descripts">
                    <div class="ps-price__descript">
                        {if isset($product->sale_unity_pack)}
                            {$product->sale_unity_pack}
                        {/if}
                    </div>
                    <div class="ps-price__descript">
                        {if isset($product->r3)}В коробке - {$product->r3}&nbsp;{Product::SGetProductUnity($product->sale_unity)}{/if}
                    </div>
                    <div class="ps-price__descript"> 
                    {if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
                        {math
                            equation="pprice / punit_price"
                            pprice=$product->price  punit_price=$product->unit_price_ratio
                            assign=unit_price}
                            <p class="unit-price">
                                {l s='Цена за 1'}{$product->unity|escape:'html':'UTF-8'}{' (справочно) - '}
                                <span id="unit_price_display">{convertPrice price=$unit_price}</span> 
                            </p>
                    {/if}
                    </div>
                    {if isset($product_attachments) && isset($product_attachments[0])}
                    <div class="ps-price__descript">
                        <a  href="{$product_attachments[0]['src']}"
                            class="ps-product__attachments"
                            data-fancybox-group="attachements-group"
                            data-fancybox-type="image"><span><b>EAC</b> сертифицировано</span></a>
                        {if isset($product_attachments[1]['src'])}
                        <a href="{$product_attachments[1]['src']}" class="ps-product__attachments" data-fancybox-group="attachements-group" data-fancybox-type="image"></a>
                        {/if}
                    </div>
                    {/if}
                </div>
                <div class="ps-product__options">
                    <div class="ps-product__option ps-product__option--checked">
                        {if $product->quantity > 0}
                        <b class="ps-option__title">{l s='В наличии'}:</b>
                        <span class="ps-option__value">
                            {$product->quantity}
                            {Product::SGetProductUnity($product->sale_unity)} / {round($product->quantity / $product->r3)} кор.
                        </span>
                        {else}
                        <b class="ps-option__title">{l s='Отсутствует'}</b>
                        {/if}
                    </div>
                    <div class="ps-product__option ps-product__option--checked">
                        <b class="ps-option__title">{l s='В заказе'}:</b>
                        <span class="ps-option__value">
                            <span class="js-product-count-{$product->id}">
                            {if !empty($productsCart) }
                                {$productsCart.cart_quantity}
                            {else} 0 {/if}
                            </span>
                            {Product::SGetProductUnity($product->sale_unity)}  / <span class="js-boxes-count-{$product->id}" data-inbox="{$product->r3}">0</span> кор.
                        </span>
                    </div>
                    <div class="ps-product__option ps-product__option--checked">
                        <b class="ps-option__title">{l s='На сумму'}:</b>
                        <span class="ps-option__value">
                            {if !empty($productsCart) }
                                <span class="ajax_block_cart_total_price2_id_{$product->id}">{convertPrice price=$productsCart.total}</span> руб.
                            {else}
                                <span class="ajax_block_cart_total_price2_id_{$product->id}">0</span> руб.
                            {/if}
                        </span>
                    </div>                    
                </div>
            </div>
            <div class="ps-grey-block">
                <div class="ps-product__controls">
                    {if $product->quantity > 0}
                    <div class="ps-product__row">
                        <div class="ps-product__cell">
                            <div class="ps-product__control">
                                <div class="ps-product__quantity ps-quantity">
                                    <button
                                        class="ps-quantity__button ps-quantity__button--decbox"
                                        data-field-qty="boxqty">&lt;</button>
                                    <input
                                        name="boxqty"
                                        class="ps-quantity__value ajax_box_input_prod_{$product->id} js-boxes-input"
                                        type="number"
                                        value="0"
                                        data-inbox="{$product->r3}"
                                        min="0"
                                        max="{$product->quantity / $product->r3}"/>
                                    <button
                                        class="ps-quantity__button ps-quantity__button--incbox"
                                        data-field-qty="boxqty">&gt;</button>
                                </div>
                                <span class="ps-quantity__title">кор.</span>
                            </div>
                            <div class="ps-product__control">
                                <div class="ps-product__quantity ps-quantity">
                                    <button
                                        class="ps-quantity__button ps-quantity__button--dec"
                                        data-field-qty="qty">&lt;</button>
                                    <input
                                        name="qty"
                                        class="ps-quantity__value ajax_input_prod_{$product->id} js-qty-input"
                                        type="number"
                                        value="0"
                                        data-prev-val="{if isset($productsCart.cart_quantity)}{$productsCart.cart_quantity}{else}0{/if}"
                                        data-inbox="{$product->r3}"
                                        min="0"
                                        max="{$product->quantity}"/>
                                    <button
                                        class="ps-quantity__button ps-quantity__button--inc"
                                        data-field-qty="qty">&gt;</button>
                                </div>
                                <span class="ps-quantity__title">{Product::SGetProductUnity($product->sale_unity)}</span>
                            </div>
                        </div>
                        <div class="ps-product__cell">
                        <a class="ps-product__button ps-product__button--tocart"
                            id="btnid{$product->id}" 
                            btncatid="{$product->id_category_default}" 
                            type="submit"
                            name="Submit"
                            title="{l s='Add to cart'}"
                            onClick="fancyChangeProductCountInCart(event, {$product->id}, 'ajax_input_prod_{$product->id}'); this.yaCounter46713966 && (yaCounter46713966.reachGoal('ADDCART')); return true;">{l s="В заказ"}</a>
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{include file="$tpl_dir/blocks/more-product-info.tpl"}

{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
        <!-- description & features -->
        {if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
            {if isset($attachments) && $attachments}
            <!--Download -->
            <section class="page-product-box">
                <h3 class="page-product-heading">{l s='Download'}</h3>
                {foreach from=$attachments item=attachment name=attachements}
                    {if $smarty.foreach.attachements.iteration %3 == 1}<div class="row">{/if}
                        <div class="col-lg-4">
                            <h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">{$attachment.name|escape:'html':'UTF-8'}</a></h4>
                            <p class="text-muted">{$attachment.description|escape:'html':'UTF-8'}</p>
                            <a class="btn btn-default btn-block" href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">
                                <i class="icon-download"></i>
                                {l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})
                            </a>
                            <hr />
                        </div>
                    {if $smarty.foreach.attachements.iteration %3 == 0 || $smarty.foreach.attachements.last}</div>{/if}
                {/foreach}
            </section>
            <!--end Download -->
            {/if}
            {if isset($product) && $product->customizable}
            <!--Customization -->
            <section class="page-product-box">
                <h3 class="page-product-heading">{l s='Product customization'}</h3>
                <!-- Customizable products -->
                <form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
                    <p class="infoCustomizable">
                        {l s='After saving your customized product, remember to add it to your cart.'}
                        {if $product->uploadable_files}
                        <br />
                        {l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
                    </p>
                    {if $product->uploadable_files|intval}
                        <div class="customizableProductsFile">
                            <h5 class="product-heading-h5">{l s='Pictures'}</h5>
                            <ul id="uploadable_files" class="clearfix">
                                {counter start=0 assign='customizationField'}
                                {foreach from=$customizationFields item='field' name='customizationFields'}
                                    {if $field.type == 0}
                                        <li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
                                            {if isset($pictures.$key)}
                                                <div class="customizationUploadBrowse">
                                                    <img src="{$pic_dir}{$pictures.$key}_small" alt="" />
                                                        <a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html':'UTF-8'}" title="{l s='Delete'}" >
                                                            <img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
                                                        </a>
                                                </div>
                                            {/if}
                                            <div class="customizationUploadBrowse form-group">
                                                <label class="customizationUploadBrowseDescription">
                                                    {if !empty($field.name)}
                                                        {$field.name}
                                                    {else}
                                                        {l s='Please select an image file from your computer'}
                                                    {/if}
                                                    {if $field.required}<sup>*</sup>{/if}
                                                </label>
                                                <input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="form-control customization_block_input {if isset($pictures.$key)}filled{/if}" />
                                            </div>
                                        </li>
                                        {counter}
                                    {/if}
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                    {if $product->text_fields|intval}
                        <div class="customizableProductsText">
                            <h5 class="product-heading-h5">{l s='Text'}</h5>
                            <ul id="text_fields">
                            {counter start=0 assign='customizationField'}
                            {foreach from=$customizationFields item='field' name='customizationFields'}
                                {if $field.type == 1}
                                    <li class="customizationUploadLine{if $field.required} required{/if}">
                                        <label for ="textField{$customizationField}">
                                            {assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
                                            {if !empty($field.name)}
                                                {$field.name}
                                            {/if}
                                            {if $field.required}<sup>*</sup>{/if}
                                        </label>
                                        <textarea name="textField{$field.id_customization_field}" class="form-control customization_block_input" id="textField{$customizationField}" rows="3" cols="20">{strip}
                                            {if isset($textFields.$key)}
                                                {$textFields.$key|stripslashes}
                                            {/if}
                                        {/strip}</textarea>
                                    </li>
                                    {counter}
                                {/if}
                            {/foreach}
                            </ul>
                        </div>
                    {/if}
                    <p id="customizedDatas">
                        <input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
                        <input type="hidden" name="submitCustomizedDatas" value="1" />
                        <button class="button btn btn-default button button-small" name="saveCustomization">
                            <span>{l s='Save'}</span>
                        </button>
                        <span id="ajax-loader" class="unvisible">
                            <img src="{$img_ps_dir}loader.gif" alt="loader" />
                        </span>
                    </p>
                </form>
                <p class="clear required"><sup>*</sup> {l s='required fields'}</p>
            </section>
            <!--end Customization -->
            {/if}
        {/if}
    
</div> <!-- itemscope product wrapper -->

{strip}
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{if isset($smarty.get.ad) && $smarty.get.ad}
    {addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
    {addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
{addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
{addJsDef attributesCombinations=$attributesCombinations}
{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
{if isset($combinations) && $combinations}
    {addJsDef combinations=$combinations}
    {addJsDef combinationsFromController=$combinations}
    {addJsDef displayDiscountPrice=$display_discount_price}
    {addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
{/if}
{if isset($combinationImages) && $combinationImages}
    {addJsDef combinationImages=$combinationImages}
{/if}
{addJsDef customizationId=$id_customization}
{addJsDef customizationFields=$customizationFields}
{addJsDef default_eco_tax=$product->ecotax|floatval}
{addJsDef displayPrice=$priceDisplay|intval}
{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
{if isset($cover.id_image_only)}
    {addJsDef idDefaultImage=$cover.id_image_only|intval}
{else}
    {addJsDef idDefaultImage=0}
{/if}
{addJsDef img_ps_dir=$img_ps_dir}
{addJsDef img_prod_dir=$img_prod_dir}
{addJsDef id_product=$product->id|intval}
{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
{addJsDef minimalQuantity=$product->minimal_quantity|intval}
{addJsDef noTaxForThisProduct=$no_tax|boolval}
{if isset($customer_group_without_tax)}
    {addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
{else}
    {addJsDef customerGroupWithoutTax=false}
{/if}
{if isset($group_reduction)}
    {addJsDef groupReduction=$group_reduction|floatval}
{else}
    {addJsDef groupReduction=false}
{/if}
{addJsDef oosHookJsCodeFunctions=Array()}
{addJsDef productHasAttributes=isset($groups)|boolval}
{addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
{addJsDef productPriceTaxIncluded=($product->getPriceWithoutReduct(false)|default:'null' - $product->ecotax * (1 + $ecotaxTax_rate / 100))|floatval}
{addJsDef productBasePriceTaxExcluded=($product->getPrice(false, null, 6, null, false, false) - $product->ecotax)|floatval}
{addJsDef productBasePriceTaxExcl=($product->getPrice(false, null, 6, null, false, false)|floatval)}
{addJsDef productBasePriceTaxIncl=($product->getPrice(true, null, 6, null, false, false)|floatval)}
{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
{addJsDef productPrice=$productPrice|floatval}
{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
{if $product->specificPrice && $product->specificPrice|@count}
    {addJsDef product_specific_price=$product->specificPrice}
{else}
    {addJsDef product_specific_price=array()}
{/if}
{if $display_qties == 1 && $product->quantity}
    {addJsDef quantityAvailable=$product->quantity}
{else}
    {addJsDef quantityAvailable=0}
{/if}
{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
    {addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
{else}
    {addJsDef reduction_percent=0}
{/if}
{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
    {addJsDef reduction_price=$product->specificPrice.reduction|floatval}
{else}
    {addJsDef reduction_price=0}
{/if}
{if $product->specificPrice && $product->specificPrice.price}
    {addJsDef specific_price=$product->specificPrice.price|floatval}
{else}
    {addJsDef specific_price=0}
{/if}
{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
{addJsDef stock_management=$PS_STOCK_MANAGEMENT|intval}
{addJsDef taxRate=$tax_rate|floatval}
{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
{addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
{addJsDefL name='product_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
{/strip}