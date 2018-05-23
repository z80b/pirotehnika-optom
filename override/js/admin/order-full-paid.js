function initFullPaid() {

    function initPopup() {
        $('[name=full_paid_date]', $popup).datepicker();
        $('[name=full_paid_date]', $popup).datepicker('setDate', 'today');
        

        $popup
            .on('submit', '.ps-form', function(event) {
                event.preventDefault();

                var data = {};

                $(':input', $popup).each(function(ix, el) {
                    if (el.value) {
                        if (el.name == 'full_paid_date')
                            data[el.name] = el.value.replace(/^(\d{2})\.(\d{2})\.(\d{4})/, '$3-$2-$1');
                        else
                            data[el.name] = el.value;
                    }
                });

                $.post($('.ps-form', $popup).attr('action'), data)
                    .done(function(response) {
                        setFullPaidFlag($('#full-paid-' + response.id_order), response.full_paid);
                        $('#full-paid-' + response.id_order).parents('td').next().text(response.total_paid_real);
                        $popup.parent().hide();
                    })
                    .error(function() {
                        console.log('Не получается установить флаг full_paid');
                    });
                
                return false;
            })
            .on('click', '.ps-popup__close', function() { $popup.parent().hide() });
    }

    function showPopup(data) {
        $('[name=total_paid_tax_incl]', $popup).val(data.total_paid_tax_incl);
        $('.ps-form', $popup).attr('action', data.url);
        $popup.parent().show();
    }

    function setFullPaidFlag($el, value) {
        if (parseInt(value)) {
            $el.removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"/>');
        } else {
            $el.removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"/>');
        }
    }

    var $popup = $('.js-ps-popup');

    $('#form-order .table.order .js-change-paid-val').each(function(ix, el) {
        $(el).parents('td').removeAttr('onclick');
    });
    
    $('#form-order .table.order').on('click', '.js-change-paid-val', function(event) {
        event.preventDefault();
        var $el = $(event.currentTarget);
        var url = $el.attr('href');

        $.getJSON(url)
            .done(function(response) {
                response.url = url;
                response.el = $el;
                switch (response.event) {
                    case 'full_paid_date:required':
                        $('[name=full_paid_reason]', $popup).attr('disabled', 'disabled');
                        showPopup(response);
                        break;
                    case 'full_paid_reason:required':
                        $('[name=full_paid_reason]', $popup).removeAttr('disabled');
                        showPopup(response);
                        break;
                    case 'full_paid:changed':
                    default:
                        setFullPaidFlag($el, response.full_paid);
                        break;
                }
            })
            .error(function() {
                console.log('Не получается установить флаг full_paid');
            });

        return false;
    });

    initPopup();  
}

$(document).ready(initFullPaid);