$(document).ready(function() {


$('#comm_ask').validate({
	submitHandler: function(form) {
		var $serialized = $(form).serialize();
		ajaxCall($('#comm_ask'),$serialized);
	},
	errorClass: "invalid",
	rules: {
		comm_q: "required",
		comm_cap: {
			required: true,
			range: [2,5]
		},
	    comm_email: {
	    	required: true,
	        email: true
		}
	},
	
	messages: {
		comm_q: '',
		comm_cap: '',
		comm_email: {
			required: '',
			email: ''
		}
	}

})



/* Ajax add request*/
function ajaxCall(caller,data) {
	$('#submitcomm').fadeOut();
	caller.append($('<div class="ajaxloader"><img src="'+baseDir+'modules/wn_site_comments/ajax-loader.gif"/></div>'));
	$('.comm_confirm, .comm_error').fadeOut('normal', function(){$(this).remove});
	$.ajax({
	type: 'POST',
	data: data,
	url: baseDir+'modules/wn_site_comments/ajax.php',
	success: function(data){
		if(data !=1)
			$('#submitcomm').fadeIn();

		if(data == 'err')
			$('<p class="comm_error">'+comm_error+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'mex')
			$('<p class="comm_error">'+comm_badcontent+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'name')
			$('<p class="comm_error">'+comm_badname+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'mail')
			$('<p class="comm_error">'+comm_bademail+'</p>').hide().appendTo(caller).fadeIn();
		else if (data == 1) // Okay
		{
			$('<p class="comm_confirm confirmation">'+comm_confirm+'</p>').hide().appendTo(caller).fadeIn();
			$('#comm_ask')[0].reset();
			
		}
			
		else alert(data);
		$('.ajaxloader').fadeOut('normal', function(){$(this).remove()}); //remove spinner
	}
	})
}

if(window.location.hash == '#commTab'){
	$('.commTabPointer').click();
	$('body').animate({ scrollTop: $("#comm_pointer").offset().top }, 500);
}

});