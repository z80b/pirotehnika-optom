{if isset($categories)}
<div class="ps-products__filter ps-filter js-products-filter">
	{foreach from=$categories item=category}
        {if isset($category.categories)}
        {assign var="button_class" value="ps-filter__item --haschildren"}
        {else}
        {assign var="button_class" value="ps-filter__item"}
        {/if}
        <div class="{$button_class}">
            <input
                class="ps-filter__item__checkbox"
                type="checkbox"
                name="category"
                value="{$category.id_category}"
                {if isset($checked['categories'][$category.id_category])}checked="checked"{/if}
                id="filter-category-{$category.id_category}"/>
            <label
                class="ps-filter__item__button"
                for="filter-category-{$category.id_category}">{$category.name}</label>
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
                        </div>
                        {/foreach}
                    </div>
                    {/if}
                </div>
                {/foreach}
                </div>
            </div>
            {/if}
        </div>
    {/foreach}
    <div class="ps-filter__item">
        <input
            class="ps-filter__item__checkbox"
            type="checkbox"
            name="discount"
            value="1"
            id="filter-price-drop"/>
        <label
            class="ps-filter__item__button"
            for="filter-price-drop">Со скидками</label>
    </div>
    {if isset($manufacts)}
    <div class="ps-filter__item --haschildren">
        <span class="ps-filter__item__button">Производитель</span>
        <div class="ps-filter__item__ticks">
            <div class="ps-filter__item__scroll">
            {foreach from=$manufacts item=manufact}
                <div class="ps-filter__tick ps-tick">
                    <input
                        class="ps-tick__checkbox"
                        type="checkbox"
                        value="{$manufact.id_manufacturer}"
                        name="manufact"
                        id="filter-manufact-{$manufact.id_manufacturer}"/>
                    <label
                        class="ps-tick__label"
                        for="filter-manufact-{$manufact.id_manufacturer}">{$manufact.name}</label>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
    {/if}
    <div class="ps-filter__controls">
        <button class="ps-filter__submit js-filter-submit">Применить</button>
        <button class="ps-filter__reset js-filter-reset">Сбросить</button>
    </div>
</div>
{/if}