$(document).ready(initCategoriesFilter);

function initCategoriesFilter() {
    $('.js-products-filter')
        .on('change', '.ps-tick > [type=checkbox]', function(event) {
            var $subticks = $(this)
                .parents('.ps-filter__tick')
                .find('.ps-filter__subticks [type=checkbox]');
                console.log($subticks);
            if ($subticks.length) {
                if ($(this).attr('checked'))
                    $subticks.attr({
                        'checked': 'checked',
                        'disabled': 'disabled'
                    });
                else {
                    $subticks.removeAttr('checked');
                    $subticks.removeAttr('disabled');
                }
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
        });
}