$(document).ready(initCategoriesFilter);

function filterReset($link) {
    $.cookie('filter', '');
    window.location.reload();
}

function setOrder($select) {
    var valueParts = $($select).val() && $($select).val().split(':');
    if (valueParts && valueParts.length > 1) {
        $.cookie('order_by', valueParts[0]);
        $.cookie('order_dir', valueParts[1]);
    }
}

function filterApply(event) {
    var categories = [];

    if ($(this).hasClass('js-filter-reset-category')) {
        $(this).parents('.ps-filter__item').find('[type=checkbox]').removeAttr('checked');
    }

    $('.js-products-filter .ps-filter__item').each(function(ix, el) {
        if ($(el).find('input[name=category]:checked').size()) {
            categories.push($(el).find('input[name=category]:checked').get().map(function(input) { return input.value }).join(','));
        }
    });
    
    var category_id = $(this).parents('.js-products-filter').data('category-id');
    var filterData = '',
        filterObj = {
            'categories': categories.join('|'),
            'discount': $(this)
                .parents('.js-products-filter')
                .find('input[name=discount]:checked').length ? 1 : 0,
            'manufact': $(this)
                .parents('.js-products-filter')
                .find('input[name=manufact]:checked')
                .get()
                .map(function(input) { if (input.value) return input.value })
                
        };
    console.log(filterObj);
    var currentFilter = JSON.parse($.cookie('filter') || '{}');
    currentFilter[category_id] = filterObj;

    $.cookie('filter', JSON.stringify(currentFilter));

    for (var key in filterObj) {
        if (typeof(filterObj[key]) == 'array')
            $.cookie(key, filterObj[key].join(','));
        else
            $.cookie(key, filterObj[key]);
    }
    window.location.reload(); 
}

function initCategoriesFilter(e,a,b) {
    console.log(e,a,b);
    $('.js-products-filter')
        .on('change', '.ps-tick > [type=checkbox]', function(event) {
            var $parent = $(this).parents('.ps-filter__item');
            var $subticks = $(this)
                .parents('.ps-filter__tick')
                .find('.ps-filter__subticks [type=checkbox]');
                console.log($subticks);
            if ($subticks.length) {
                if ($(this).attr('checked'))
                    $subticks.attr({
                        'disabled': 'disabled'
                    }).removeAttr('checked');
                else {
                    $subticks.removeAttr('checked');
                    $subticks.removeAttr('disabled');
                }
            }
            var ticksCount = $parent.find('.ps-filter__item__ticks [type=checkbox]:checked').size();
            if (ticksCount)
                $parent.attr('data-ticks-count', ticksCount);
            else
                $parent.removeAttr('data-ticks-count');
        })
        .on('change', '.ps-filter__subticks [type=checkbox]', function(event) {
            var $parent = $(this).parents('.ps-filter__item');
            var ticksCount = $parent.find('.ps-filter__item__ticks [type=checkbox]:checked').size();
            if (ticksCount)
                $parent.attr('data-ticks-count', ticksCount);
            else
                $parent.removeAttr('data-ticks-count');
        })
        .on('click', '.js-filter-submit, .js-filter-reset-category', filterApply)
        .on('change', '.js-filter-item-buttoncheckbox', filterApply)
        .on('click', '.js-filter-reset', filterReset);
    $('.js-products-filter .ps-filter__item').each(function(ix, el) {
        var ticksCount = $(el).find('.ps-filter__item__ticks [type=checkbox]:checked').size();
        if (ticksCount) $(el).attr('data-ticks-count', ticksCount);
    });

    setOrder('#selectProductSort')
    $('#selectProductSort').on('change', function() {
        setOrder(this);
    });
}