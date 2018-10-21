{if isset($subcategories)}
<div class="ps-products__filter ps-filter js-products-filter" data-category-id="{$id_category}">
	{foreach from=$subcategories item=category}
    {if $category.active}
        {if isset($category.categories)}
        {assign var="button_class" value="ps-filter__item --haschildren"}
        {else}
        {assign var="button_class" value="ps-filter__item"}
        {/if}
        <div class="{$button_class}">
            {if !isset($category.categories)}
            <input
                class="ps-filter__item__checkbox{if !isset($category.categories)} js-filter-item-buttoncheckbox{/if}"
                type="checkbox"
                name="category"
                value="{$category.id_category}"
                {if isset($checked['categories'][$category.id_category])}checked="checked"{/if}
                id="filter-category-{$category.id_category}"/>
            {/if}
            <label
                class="ps-filter__item__button"
                for="filter-category-{$category.id_category}">{$category.name}{if $category.products_count} | <b>{$category.products_count}</b>{/if}</label>
            {if isset($category.categories)}
    		<div class="ps-filter__item__ticks">
                <div class="ps-filter__item__scroll">
                {foreach from=$category.categories item=subcategory}
                <div class="ps-filter__tick ps-tick">
        			<input
                        class="ps-tick__checkbox"
                        type="checkbox"
                        name="category"
                        {if isset($checked['categories'][$subcategory.id_category])}checked="checked"{/if}
                        value="{$subcategory.id_category}"
                        id="filter-category-{$subcategory.id_category}"/>
        			<label
                        class="ps-tick__label"
                        for="filter-category-{$subcategory.id_category}">{$subcategory.name}</label>
                    {if $subcategory.products_count}
                        <div class="ps-tick__products-count">{$subcategory.products_count} <span class="ps-tick__category-products-count">({$subcategory.category_products_count})</span></div>
                    {else}
                        <div class="ps-tick__products-count"><span class="ps-tick__category-products-count">({$subcategory.category_products_count})</span></div>
                    {/if}
                    {if isset($subcategory.categories)}
                    <div class="ps-filter__subticks">
                        {foreach from=$subcategory.categories item=subcategory2}
                        <div class="ps-subtick">
                            <input
                                class="ps-tick__checkbox"
                                type="checkbox"
                                value="{$subcategory2.id_category}"
                                {if isset($checked['categories'][$subcategory2.id_category])}checked="checked"{/if}
                                name="category"
                                id="filter-category-{$subcategory2.id_category}"/>
                            <label
                                class="ps-tick__label"
                                for="filter-category-{$subcategory2.id_category}">{$subcategory2.name}</label>
                            {if $subcategory2.products_count}
                                <div class="ps-tick__products-count">{$subcategory2.products_count} <span class="ps-tick__category-products-count">({$subcategory2.category_products_count})</span></div>
                            {else}
                                <div class="ps-tick__products-count"><span class="ps-tick__category-products-count">({$subcategory2.category_products_count})</span></div>
                            {/if}
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                </div>
                {/foreach}
                </div>
                <div class="ps-filter__item-controls">
                    <button class="ps-filter__submit js-filter-submit">Применить</button>
                    <button class="ps-filter__reset js-filter-reset-category">Сбросить</button>
                </div>
            </div>
            {/if}
        </div>
    {/if}
    {/foreach}
    {if $discounts > 0}
    <div class="ps-filter__item">
        <input
            class="ps-filter__item__checkbox js-filter-item-buttoncheckbox"
            type="checkbox"
            name="discount"
            value="1"
            {if isset($checked['discount']) && $checked['discount']}checked="checked"{/if}
            id="filter-price-drop"/>
        <label
            class="ps-filter__item__button"
            for="filter-price-drop">Со скидками | <b>{$discounts}</b></label>
    </div>
    {/if}
    {if isset($manufacturers)}
    <div class="ps-filter__item --haschildren">
        <span class="ps-filter__item__button">Производитель | <b>{$manufacturers_products_count}</b></span>
        <div class="ps-filter__item__ticks">
            <div class="ps-filter__item__scroll">
            {foreach from=$manufacturers item=manufact}
                <div class="ps-filter__tick ps-tick">
                    {if isset($checked['manufact'][$manufact.id_manufacturer])}
                    <input
                        class="ps-tick__checkbox"
                        type="checkbox"
                        value="{$manufact.id_manufacturer}"
                        checked="checked"
                        name="manufact"
                        id="filter-manufact-{$manufact.id_manufacturer}"/>
                    {else}
                    <input
                        class="ps-tick__checkbox"
                        type="checkbox"
                        value="{$manufact.id_manufacturer}"
                        name="manufact"
                        id="filter-manufact-{$manufact.id_manufacturer}"/>
                    {/if}
                    <label
                        class="ps-tick__label"
                        for="filter-manufact-{$manufact.id_manufacturer}">{$manufact.name}</label>
                    {if $manufact.products_count}    
                        <div class="ps-tick__products-count">{$manufact.products_count} <span class="ps-tick__category-products-count">({$manufact.manufacturer_products_count})</span></div>
                    {else}
                        <div class="ps-tick__products-count"><span class="ps-tick__category-products-count">({$manufact.manufacturer_products_count})</span></div>
                    {/if}
                </div>
            {/foreach}
            </div>
            <div class="ps-filter__item-controls">
                <button class="ps-filter__submit js-filter-submit">Применить</button>
                <button class="ps-filter__reset js-filter-reset-category">Сбросить</button>
            </div>
        </div>
    </div>
    {/if}
    {if isset($checked) && count($checked)}
    <button class="ps-filter__reset js-filter-reset">Сбросить</button>
    {/if}
</div>
{/if}