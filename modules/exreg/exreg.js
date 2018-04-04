$(document).ready(function(){

	if($('#exreg_form').length == 0)
		return;
	$('#content').find('form').after($('#exreg_form'));
});