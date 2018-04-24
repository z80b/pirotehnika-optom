$(document).ready(function() {
    $('#form-order .table.order .js-change-blocker-val, #form-order .table.order .js-change-paid-val').each(function(ix, el) {
        $(el).parents('td').removeAttr('onclick');
    });
    
	$('#form-order .table.order').on('click', '.js-change-blocker-val', function(event) {
        event.preventDefault();
        
		var $el = $(event.currentTarget);

		$.getJSON($el.attr('href'))
			.done(function(response) {
				if (parseInt(response.blocked)) {
					$el.removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"/>');
				} else {
					$el.removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"/>');
				}
			})
			.error(function() {
				console.log('Can not change blocker');
			});

		return false;
	});

	$('#form-order .table.order').on('click', '.js-change-paid-val', function(event) {
        event.preventDefault();
		var $el = $(event.currentTarget);

		$.getJSON($el.attr('href'))
			.done(function(response) {
				if (parseInt(response.full_paid)) {
					$el.removeClass('action-disabled').addClass('action-enabled').html('<i class="icon-check"/>');
				} else {
					$el.removeClass('action-enabled').addClass('action-disabled').html('<i class="icon-remove"/>');
				}
			})
			.error(function() {
				console.log('Can not change blocker');
			});

		return false;
	});
});