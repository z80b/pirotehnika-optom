$(document).ready(initCategoriesFilter);

function initCategoriesFilter() {
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
            var filterData = '',
                filterObj = {
                    'categories': $(this)
                        .parents('.js-products-filter')
                        .find('input[name=category]:checked')
                        .get()
                        .map(function(input) { if (input.value) return input.value }),
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
            $.cookie('categories', '');
            $.cookie('discount', '');
            $.cookie('manufact', '');
            window.location.reload();  
        });
    $('.js-products-filter .ps-filter__item').each(function(ix, el) {
        var ticksCount = $(el).find('.ps-filter__item__ticks [type=checkbox]:checked').size();
        if (ticksCount) $(el).attr('data-ticks-count', ticksCount);
    });
}