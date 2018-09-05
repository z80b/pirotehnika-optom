$(document).ready(initCategoriesFilter);

function filterReset($link) {
    $.cookie('categories', '');
    if ($link) {
        window.location.href = $link;
    } else {
        $.cookie('discount', '');
        $.cookie('manufact', '');
        window.location.reload();
    }
}

function setOrder($select) {
    var valueParts = $($select).val().split(':');
    if (valueParts && valueParts.length > 1) {
        $.cookie('order_by', valueParts[0]);
        $.cookie('order_dir', valueParts[1]);
    }
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
                        //'checked': 'checked',
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
        .on('change', '.ps-filter__item__checkbox', function(event) {
            var $parent = $(this).parents('.ps-filter__item');
            $parent.removeAttr('data-ticks-count');
            if ($(this).attr('checked')) {
                $parent
                    .find('.ps-filter__item__ticks [type=checkbox]')
                    .attr({ 'disabled': 'disabled' })
                    .removeAttr('checked');
            } else {
                $parent
                    .find('.ps-filter__item__ticks [type=checkbox]')
                    .removeAttr('disabled');
            }
        })
        .on('click', '.js-filter-submit', function(event) {
            var categories = [];
            $('.js-products-filter .ps-filter__item').each(function(ix, el) {
                console.log('el:', el);
                if ($(el).find('input[name=category]:checked').size()) {
                    categories.push($(el).find('input[name=category]:checked').get().map(function(input) { return input.value }).join(','));
                }
            });
            console.log(categories);

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
            for (var key in filterObj) {
                if (typeof(filterObj[key]) == 'array')
                    $.cookie(key, filterObj[key].join(','));
                else
                    $.cookie(key, filterObj[key]);
            }
            window.location.reload();
        })
        .on('click', '.js-filter-reset', function() {
            filterReset();
        });
    $('.js-products-filter .ps-filter__item').each(function(ix, el) {
        var ticksCount = $(el).find('.ps-filter__item__ticks [type=checkbox]:checked').size();
        if (ticksCount) $(el).attr('data-ticks-count', ticksCount);
    });

    $('.sf-menu').on('click', '.js-menu-subcategory', function(event) {
        event.preventDefault();
        filterReset(event.currentTarget.getAttribute('href'));
    });

    setOrder('#selectProductSort')
    $('#selectProductSort').on('change', function() {
        setOrder(this);
    });
}