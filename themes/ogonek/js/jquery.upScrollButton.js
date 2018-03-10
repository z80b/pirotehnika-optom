/**
 * код взят из блога Александра Шуйского http://shublog.ru/ajax/jquery/kak-sdelat-knopku-naverkh-kak-vkontakte/
 * код оформил EGORR
 * Date: 16.07.11
 */
jQuery.fn.upScrollButton = function( options ) {
	var options = jQuery.extend( {

		heightForButtonAppear : 100, // дистанция от верхнего края окна браузера, при превышении которой кнопка становится видимой
		heightForScrollUpTo : 0, // дистанция от верхнего края окна браузера к которой будет прокручена страница
		scrollTopTime : 900, // длительность прокрутки
		upScrollButtonId : 'move_up', // id кнопки
		upScrollButtonText : 'Наверх', // текст кнопки
		upScrollButtonFadeInTime :0, // длительность эффекта появления кнопки
		upScrollButtonFadeOutTime :300,// длительность ффекта исчезновения кнопки	

	}, options );
	return this.each( function( ) {
	
		jQuery( 'body' ).append( '<a id="' + options.upScrollButtonId + '" href="#">' + options.upScrollButtonText + '</a>' );
		jQuery( window ).scroll( function () {
			if ( jQuery( this ).scrollTop()  > options.heightForButtonAppear )
				jQuery( 'a#' + options.upScrollButtonId  ).fadeIn(options.upScrollButtonFadeInTime );
			else
				jQuery( 'a#' + options.upScrollButtonId ).fadeOut( options.upScrollButtonFadeOutTime );
		});
		jQuery( 'a#' + options.upScrollButtonId ).click( function ( ) {
			jQuery( 'body,html' ).animate( {
				scrollTop: options.heightForScrollUpTo
			}, options.scrollTopTime );
			return false;
		});
	});

}