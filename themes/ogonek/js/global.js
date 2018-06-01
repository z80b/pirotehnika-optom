/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
//global variables
var responsiveflag = false;

$(document).ready(function(){

	paginationToTop();
    keyOrderActivate();

	highdpiInit();
	responsiveResize();

	$('.price, .old-price').html(function(ix, html) {
		return html.replace(/([\d|\s]+)\,(\d+)\s*(.+)/, '$1,<span class="price__cents">$2</span> $3');
	});

	$('#short_description_content').on('click', function(event) {
		console.log(event);
		var $el = $(event.currentTarget);
		$el.toggleClass('content--open');
	});

	$(window).resize(responsiveResize);
	if (navigator.userAgent.match(/Android/i))
	{
		var viewport = document.querySelector('meta[name="viewport"]');
		viewport.setAttribute('content', 'initial-scale=1.0,maximum-scale=1.0,user-scalable=0,width=device-width,height=device-height');
		window.scrollTo(0, 1);
	}
	if (typeof quickView !== 'undefined' && quickView)
		quick_view();
	dropDown();

	if (typeof page_name != 'undefined' && !in_array(page_name, ['index', 'product']))
	{
		bindGrid();

		$(document).on('change', '.selectProductSort', function(e){
			if (typeof request != 'undefined' && request)
				var requestSortProducts = request;
			var splitData = $(this).val().split(':');
			var url = '';
			if (typeof requestSortProducts != 'undefined' && requestSortProducts)
			{
				url += requestSortProducts ;
				if (typeof splitData[0] !== 'undefined' && splitData[0])
				{
					url += ( requestSortProducts.indexOf('?') < 0 ? '?' : '&') + 'orderby=' + splitData[0];
					
					if (typeof splitData[1] !== 'undefined' && splitData[1]) 
						url += '&orderway=' + splitData[1];
				}
				document.location.href = url;
			}
		});

		$(document).on('change', '.selectManufFilter', function(e){

            var path = window.location.origin + window.location.pathname;
            var hash = window.location.hash;
            var parts = {};
            window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                parts[key] = value;
            });

            if ($(this).val() !== 'all'){
                parts['id_manufacturer'] = $(this).val();
			}
			else{
                parts['id_manufacturer'] = null;
			}
			var url = '';
            var i = 1;
            for (var k in parts){
                if (parts[k] === null) continue;
                var pt = k + '=' + parts[k];
                if (i != 1) pt = '&'+pt;
                url += pt;
                i++;
			}

			if(url !== '') url = path + '?' + url;
			else url = path;

            document.location.href = url + hash;


            // if (typeof request != 'undefined' && request)
			// 	var requestManufFilter = request;
			// var url = requestManufFilter;
			// if (typeof requestManufFilter != 'undefined' && requestManufFilter)
			// {
			// 	// url += requestManufFilter ;
			// 	if ($(this).val() !== 'all')
			// 	{
			// 		// url += ( requestManufFilter.indexOf('?') < 0 ? '?' : '&') + 'id_manufacturer=' + $(this).val();
			// 	}
			// 	document.location.href = url;
			// }
		});

		$(document).on('change', 'select[name="n"]', function(){
			$(this.form).submit();
		});

		$(document).on('change', 'select[name="currency_payment"]', function(){
			setCurrency($(this).val());
		});
	}

	$(document).on('change', 'select[name="manufacturer_list"], select[name="supplier_list"]', function(){
		if (this.value != '')
			location.href = this.value;
	});

	$(document).on('click', '.back', function(e){
		e.preventDefault();
		history.back();
	});

	jQuery.curCSS = jQuery.css;
	if (!!$.prototype.cluetip)
		$('a.cluetip').cluetip({
			local:true,
			cursor: 'pointer',
			dropShadow: false,
			dropShadowSteps: 0,
			showTitle: false,
			tracking: true,
			sticky: false,
			mouseOutClose: true,
			fx: {
				open:       'fadeIn',
				openSpeed:  'fast'
			}
		}).css('opacity', 0.8);

	if (typeof(FancyboxI18nClose) !== 'undefined' && typeof(FancyboxI18nNext) !== 'undefined' && typeof(FancyboxI18nPrev) !== 'undefined' && !!$.prototype.fancybox)
		$.extend($.fancybox.defaults.tpl, {
			closeBtn : '<a title="' + FancyboxI18nClose + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
			next     : '<a title="' + FancyboxI18nNext + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
			prev     : '<a title="' + FancyboxI18nPrev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
		});

	// Close Alert messages
	$(".alert.alert-danger").on('click', this, function(e){
		if (e.offsetX >= 16 && e.offsetX <= 39 && e.offsetY >= 16 && e.offsetY <= 34)
			$(this).fadeOut();
	});
});

function highdpiInit()
{
	if (typeof highDPI === 'undefined')
		return;
	if(highDPI && $('.replace-2x').css('font-size') == "1px")
	{
		var els = $("img.replace-2x").get();
		for(var i = 0; i < els.length; i++)
		{
			src = els[i].src;
			extension = src.substr( (src.lastIndexOf('.') +1) );
			src = src.replace("." + extension, "2x." + extension);

			var img = new Image();
			img.src = src;
			img.height != 0 ? els[i].src = src : els[i].src = els[i].src;
		}
	}
}

// Used to compensante Chrome/Safari bug (they don't care about scroll bar for width)
function scrollCompensate()
{
	var inner = document.createElement('p');
	inner.style.width = "100%";
	inner.style.height = "200px";

	var outer = document.createElement('div');
	outer.style.position = "absolute";
	outer.style.top = "0px";
	outer.style.left = "0px";
	outer.style.visibility = "hidden";
	outer.style.width = "200px";
	outer.style.height = "150px";
	outer.style.overflow = "hidden";
	outer.appendChild(inner);

	document.body.appendChild(outer);
	var w1 = inner.offsetWidth;
	outer.style.overflow = 'scroll';
	var w2 = inner.offsetWidth;
	if (w1 == w2) w2 = outer.clientWidth;

	document.body.removeChild(outer);

	return (w1 - w2);
}

function responsiveResize()
{
	compensante = scrollCompensate();
	if (($(window).width()+scrollCompensate()) <= 767 && responsiveflag == false)
	{
		accordion('enable');
		accordionFooter('enable');
		responsiveflag = true;
	}
	else if (($(window).width()+scrollCompensate()) >= 768)
	{
		accordion('disable');
		accordionFooter('disable');
		responsiveflag = false;
		if (typeof bindUniform !=='undefined')
			bindUniform();
	}
	blockHover();
}

function blockHover(status)
{
	var screenLg = $('body').find('.container').width() == 1170;

	if ($('.product_list').is('.grid'))
		if (screenLg)
			$('.product_list .button-container').hide();
		else
			$('.product_list .button-container').show();

	$(document).off('mouseenter').on('mouseenter', '.product_list.grid li.ajax_block_product .product-container', function(e){
		if (screenLg)
		{
			var pcHeight = $(this).parent().outerHeight();
			var pcPHeight = $(this).parent().find('.button-container').outerHeight() + $(this).parent().find('.comments_note').outerHeight() + $(this).parent().find('.functional-buttons').outerHeight();
			$(this).parent().addClass('hovered').css({'height':pcHeight + pcPHeight, 'margin-bottom':pcPHeight * (-1)});
			$(this).find('.button-container').show();
		}
	});

	$(document).off('mouseleave').on('mouseleave', '.product_list.grid li.ajax_block_product .product-container', function(e){
		if (screenLg)
		{
			$(this).parent().removeClass('hovered').css({'height':'auto', 'margin-bottom':'0'});
			$(this).find('.button-container').hide();
		}
	});
}

function quick_view()
{
	$(document).on('click', '.quick-view:visible, .quick-view-mobile:visible', function(e){
		e.preventDefault();
		var url = this.rel;
		var anchor = '';

		if (url.indexOf('#') != -1)
		{
			anchor = url.substring(url.indexOf('#'), url.length);
			url = url.substring(0, url.indexOf('#'));
		}

		if (url.indexOf('?') != -1)
			url += '&';
		else
			url += '?';

		if (!!$.prototype.fancybox)
			$.fancybox({
				'padding':  0,
				'width':    1087,
				'height':   610,
				'type':     'iframe',
				'href':     url + 'content_only=1' + anchor
			});
	});
}

function bindGrid()
{

	// Узнаем текщий режим отображения
	// Если его нет, тогда отображаем list

	function displayView(event) {
		var $productsBlock = $('.js-ps-products').get(0);
		var defaultViewId = $.cookie('display') || 'list';
		var viewClasses = {
			'list': 'ps-products ps-products--list js-ps-products',
			'grid': 'ps-products ps-products--grid js-ps-products',
			'tab': 'ps-products ps-products--table js-ps-products'
		};		

		if (event) {
			event.preventDefault();
			var $currentTab = $(event.currentTarget);
			var currentViewId = $currentTab.attr('id');
			$currentTab.addClass('selected').siblings().removeClass('selected');
			$.cookie('display', currentViewId);
		} else {
			$('#displayType').find('#' + defaultViewId).addClass('selected');
		}

		if ($productsBlock) $productsBlock.className = viewClasses[currentViewId || defaultViewId];
	}

	// var view = $.cookie('display') || 'list';

	// if (!view && (typeof displayList != 'undefined') && displayList) {
	// 	view = 'list';
	// }

	$('#displayType').on('click', 'li', displayView);
	displayView();



// ////	$('#displayType').attr('data-display', view);

// 	if(view && view === 'grid'){
//         $('.display').find('li#grid').addClass('selected');
// 	}
// 	else if(view && view === 'tab'){
//         $('.display').find('li#tab').addClass('selected');
// 	}
//     else if(view && view === 'extend'){
//         $('.display').find('li#extend').addClass('selected');
//     }
// 	else {
// 		view = 'list';
//         $('.display').find('li#list').addClass('selected');
// 	}

//     display(view); // отображаем


// 	// Привязываем обработчики событий к каждой кнопке
// 	$(document).on('click', '#grid', function(e){
// 		e.preventDefault();
// 		display('grid');
// 	});

// 	$(document).on('click', '#list', function(e){
// 		e.preventDefault();
// 		display('list');
// 	});
// 	$(document).on('click', '#tab', function(e){
// 		e.preventDefault();
// 		display('tab');
// 	});
//     $(document).on('click', '#extend', function(e){
//         e.preventDefault();
//         display('extend');
//     });

}

function display(view)
{

    // var storage = false;
    // if (typeof(getStorageAvailable) !== 'undefined') {
    //     storage = getStorageAvailable();
    // }
    // if (!storage) {
    //     return;
    // }

    // $.totalStorage.setItem('display', view);

    $.cookie('display', view);


	if (view == 'list')
	{
        $('#appTabView').html('');
		$('ul.product_list').show();
		$('ul.product_list').removeClass('grid').addClass('list row');
		$('.product_list > li').removeClass('col-xs-12 col-sm-6 col-md-4').addClass('col-xs-12');
		$('.product_list > li').each(function(index, element) {



			var html = '';
			html = '<div class="product-container"><div class="row">';
			html += '<div class="left-block col-xs-4 col-sm-5 col-md-4">' + $(element).find('.left-block').html() + '</div>';
			html += '<div class="center-block col-xs-4 col-sm-7 col-md-4">';
			html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
			html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';

			html += '<p class="product-desc" itemprop="description">'+ $(element).find('.product-desc').html() + '</p>';

			var hookReviews = $(element).find('.hook-reviews');
			if (hookReviews.length) {
				html += hookReviews.clone().wrap('<div>').parent().html();
			}
			var price = $(element).find('.content_price').html();       // check : catalog mode is enabled
			if (price != null) {
				html += '<div class="content_price col-xs-5 col-md-12">'+ price + '</div>';
			}
			// html += '<p class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
			// var colorList = $(element).find('.color-list-container').html();
			// if (colorList != null) {
			// 	html += '<div class="color-list-container">'+ colorList +'</div>';
			// }
			// var availability = $(element).find('.availability').html();	// check : catalog mode is enabled
			// if (availability != null) {
			// 	html += '<span class="availability">'+ availability +'</span>';
			// }
			html += '</div>';
			html += '<div class="right-block col-xs-11 col-sm-12 col-md-4"><div class="right-block-content row">';
			
			html += '<div class="button-container col-xs-12 col-md-12">'+ $(element).find('.button-container').html() +'</div>';
			html += '<div class="functional-buttons clearfix col-sm-12">' + $(element).find('.functional-buttons').html() + '</div>';
			html += '</div>';
			html += '</div></div>';
			$(element).html(html);

            var inp = $(element).find('#quantity_wanted');
            $(inp).val($(inp).attr('data-prev-val'))

		});
		$('.display').find('li#list').addClass('selected');
		$('.display').find('li#grid').removeAttr('class');
		$('.display').find('li#tab').removeAttr('class');
        $('.display').find('li#extend').removeAttr('class');
////        $('#displayType').attr('data-display', 'list');

		//localStorage.display1='list';
		//console.log('vibran list localStorage.display1 '+localStorage.display1);

	}
	else if (view == 'grid')
	{


		$('#appTabView').html('');
        $('ul.product_list').show();
		$('ul.product_list').removeClass('list').addClass('grid row');
		$('.product_list > li').removeClass('col-xs-12').addClass('col-xs-12 col-sm-6 col-md-4');
		$('.product_list > li').each(function(index, element) {



			var html = '';
			html += '<div class="product-container">';
			html += '<div class="left-block">' + $(element).find('.left-block').html() + '</div>';
			html += '<div class="right-block">';
			html += '<div class="product-flags">'+ $(element).find('.product-flags').html() + '</div>';
			html += '<h5 itemprop="name">'+ $(element).find('h5').html() + '</h5>';

			var hookReviews = $(element).find('.hook-reviews');
			if (hookReviews.length) {
				html += hookReviews.clone().wrap('<div>').parent().html();
			}
			html += '<p itemprop="description" class="product-desc">'+ $(element).find('.product-desc').html() + '</p>';
			var price = $(element).find('.content_price').html(); // check : catalog mode is enabled
			if (price != null) {
				html += '<div class="content_price">'+ price + '</div>';
			}
			html += '<div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="button-container">'+ $(element).find('.button-container').html() +'</div>';
			var colorList = $(element).find('.color-list-container').html();
			if (colorList != null) {
				html += '<div class="color-list-container">'+ colorList +'</div>';
			}
			// var availability = $(element).find('.availability').html(); // check : catalog mode is enabled
			// if (availability != null) {
			// 	html += '<span class="availability">'+ availability +'</span>';
			// }
			html += '</div>';
			html += '<div class="functional-buttons clearfix">' + $(element).find('.functional-buttons').html() + '</div>';
			html += '</div>';
			$(element).html(html);

            var inp = $(element).find('#quantity_wanted');
            $(inp).val($(inp).attr('data-prev-val'))
		});
		$('.display').find('li#grid').addClass('selected');
		$('.display').find('li#list').removeAttr('class');
		$('.display').find('li#tab').removeAttr('class');
		$('.display').find('li#extend').removeAttr('class');
////        $('#displayType').attr('data-display', 'grid');

		//localStorage.display1 ='grid';
		//console.log('vibran gridlocalStorage.display1 '+localStorage.display1);

	}
    else if (view == 'tab')
    {

        $('ul.product_list').hide();


        var html = '';
        html += "<table class='table table-striped table-bordered table-hover'>";
        html += "<thead>";
        html += "<tr><th>Артикул</th><th>Изобр.</th><th>Наименование</th><th>Ед</th><th>Цена</th><th>Нал.</th><th>В заказе (<a href='javascript:void(0)' onclick='appAlert(\"Для заказа сразу коробки в поле ввода нажмите Ctrl + Вверх или Ctrl + Вниз\");'>?</a>)</th><th>Сумма</th></tr>";
        html += "</thead>";
        html += "<tbody>";
        $('.ps-product__table tbody > tr').each(function(index, element) {

            var prodId = parseInt($(element).attr('data-product-id'));

			var  sku = '';
			sku = $(element).find('[itemprop=sku]').first().html();
        	var name = '';
            name = $(element).find('[itemprop=productname]').first().html();

            var img = '';
            img = $(element).find('[itemprop=image]').first().attr('src');
            var fullImg = $(element).find('[itemprop=image]').first().attr('data-full-img');
            img = "<a class='fancyImg' href='"+fullImg+"'><img width='65' src='"+img+"'/></a>";

			var price = '';
            price = $(element).find('[itemprop=priceDisc]').first().text();
            price = price.replace('руб', '');

            var descr = '';
            descr = $(element).find('[itemprop=description]').first().html();

            var total = '';
            total = $(element).find('[itemprop=totalprice]').first();

            total = $(total).text();
            // total = total.replace('руб', '');
            var intTotal = parseInt(total.replace('руб', ''));
            if(intTotal === 0) total = '';
            total = '<span class="ajax_block_cart_total_price_to_null2 ajax_block_cart_total_price2_id_'+prodId+'">'+total.replace('руб', '')+'</span>';


            var fasovka = $(element).attr('data-product-fasovka');
            var r1 = $(element).attr('data-product-r1');
            var r2 = $(element).attr('data-product-r2');
            var r3 = $(element).attr('data-product-r3');

            var quantity = parseInt($(element).attr('data-product-quantity'));

            var countEl = "ajax_block_cart_count_id_"+prodId;
            var inputEl = "ajax_input_prod_"+prodId;


            var _count = $(element).find('[itemprop=countInCart]').html()
            var inCart = '<div style="width:80px">';
            //inCart += '<span  data-field-qty="qty" class="btn btn-default button-minus product_quantity_down" onclick="changeOneProduct('+ prodId +', \''+countEl+'\', false)"></span>';
            inCart += '<input data-product-r3="'+ r3 +'" type="text" min="1" name="qty" id="quantity_wanted" data-prev-val="'+parseInt(_count)+'" class="'+inputEl+' ajax_input_prod_to_null text" value="'+parseInt(_count)+'" onchange="changeProductCountInCart('+ prodId +', \''+inputEl+'\', '+ quantity +')" />'
            // inCart += '<b><span class="'+ countEl +' ajax_block_cart_total_count_to_null">'+_count+'</span></b><span class="ml-5">шт.</span>'
            // inCart += '<span data-field-qty="qty" class="btn btn-default button-plus product_quantity_up" onclick="changeOneProduct('+ prodId +', \''+countEl+'\')"></span>';
            inCart += '</div>';


			var cssClass = '';
			if(intTotal !== 0){
                cssClass = 'isInCart'
			}


            html += "<tr class='ajax_table_tr_bg_null ajax_table_tr_bg_"+prodId+ " "+ cssClass +"'>";
            html += "<td>"+ sku +"</td>";
            html += "<td>"+ img +"</td>";
            html += "<td>"+ name +"</td>";
            html += "<td>"+ fasovka +"</td>";
            // html += "<td>"+ descr +"</td>";
            html += "<td>"+ price +"</td>";
            html += "<td>"+ quantity +"</td>";
            html += "<td>"+ inCart +"</td>";
            html += "<td>"+ total +"</td>";
            html += "</tr>";


		});
        html += "</tbody>";
        html += "</table>";
        $('#appTabView').html(html);
        $('.display').find('li#tab').addClass('selected');
        $('.display').find('li#extend').removeAttr('class');
        $('.display').find('li#grid').removeAttr('class');
        $('.display').find('li#list').removeAttr('class');
////        $('#displayType').attr('data-display', 'tab');
        //var localStorage.display1='tab';
        //console.log('vibran tab localStorage.display1 '+localStorage.display1);
	}
    else if (view == 'extend')
    {

        $('ul.product_list').hide();


        var html = '';
        html += "<table class='table table-striped table-bordered table-hover' style='font-size:0.8em'>";
        html += "<thead>";
        html += "<tr><th>Артикул</th><th>Наименование</th><th>Ед</th><th>В уп.</th><th>В бл.</th><th>В кор.</th><th>Цена</th><th>Нал.</th><th>В заказе (<a href='javascript:void(0)' onclick='appAlert(\"Для заказа сразу коробки в поле ввода нажмите Ctrl + Вверх или Ctrl + Вниз\");'>?</a>)</th><th>Сумма</th></tr>";
        html += "</thead>";
        html += "<tbody>";
        $('.product_list > li').each(function(index, element) {

            var prodId = parseInt($(element).attr('data-product-id'));

            var  sku = '';
            sku = $(element).find('[itemprop=sku]').first().html();
            var name = '';
            name = $(element).find('[itemprop=productname]').first().html();

            var img = '';
            img = $(element).find('[itemprop=image]').first().attr('src');
            var fullImg = $(element).find('[itemprop=image]').first().attr('data-full-img');
            img = "<a class='fancyImg' href='"+fullImg+"'><img width='65' src='"+img+"'/></a>";

            var price = '';
            price = $(element).find('[itemprop=priceDisc]').first().text();
            price = price.replace('руб', '');

            var descr = '';
            descr = $(element).find('[itemprop=description]').first().html();

            var total = '';
            total = $(element).find('[itemprop=totalprice]').first();

            total = $(total).text();
            // total = total.replace('руб', '');
            var intTotal = parseInt(total.replace('руб', ''));
            if(intTotal === 0) total = '';
            total = '<span class="ajax_block_cart_total_price_to_null2 ajax_block_cart_total_price2_id_'+prodId+'">'+total.replace('руб', '')+'</span>';


            var fasovka = $(element).attr('data-product-fasovka');
            var r1 = $(element).attr('data-product-r1');
            if (parseInt(r1) === 1) r1 = '-';
            var r2 = $(element).attr('data-product-r2');
            if (parseInt(r2) === 1) r2 = '-';
            var r3 = $(element).attr('data-product-r3');

            var quantity = parseInt($(element).attr('data-product-quantity'));

            var countEl = "ajax_block_cart_count_id_"+prodId;
            var inputEl = "ajax_input_prod_"+prodId;


            var _count = $(element).find('[itemprop=countInCart]').html()
            var inCart = '<div style="width:80px">';
            //inCart += '<span  data-field-qty="qty" class="btn btn-default button-minus product_quantity_down" onclick="changeOneProduct('+ prodId +', \''+countEl+'\', false)"></span>';
            inCart += '<input data-product-r3="'+ r3 +'" type="text" min="1" name="qty" id="quantity_wanted" data-prev-val="'+parseInt(_count)+'" class="'+inputEl+' ajax_input_prod_to_null text" value="'+parseInt(_count)+'" onchange="changeProductCountInCart('+ prodId +', \''+inputEl+'\', '+ quantity +')" />'
            // inCart += '<b><span class="'+ countEl +' ajax_block_cart_total_count_to_null">'+_count+'</span></b><span class="ml-5">шт.</span>'
            // inCart += '<span data-field-qty="qty" class="btn btn-default button-plus product_quantity_up" onclick="changeOneProduct('+ prodId +', \''+countEl+'\')"></span>';
            inCart += '</div>';


            var cssClass = '';
            if(intTotal !== 0){
                cssClass = 'isInCart'
            }


            html += "<tr class='ajax_table_tr_bg_null ajax_table_tr_bg_"+prodId+ " "+ cssClass +"'>";
            html += "<td>"+ sku +"</td>";
            //html += "<td>"+ img +"</td>";
            html += "<td>"+ name +"</td>";
            html += "<td>"+ fasovka +"</td>";
            // html += "<td>"+ descr +"</td>";

            html += "<td>"+ r1 +"</td>";
            html += "<td>"+ r2 +"</td>";
            html += "<td>"+ r3 +"</td>";
            html += "<td>"+ price +"</td>";
            html += "<td>"+ quantity +"</td>";
            html += "<td>"+ inCart +"</td>";
            html += "<td>"+ total +"</td>";
            html += "</tr>";


        });
        html += "</tbody>";
        html += "</table>";
        $('#appTabView').html(html);
        $('.display').find('li#extend').addClass('selected');
        $('.display').find('li#tab').removeAttr('class');
        $('.display').find('li#grid').removeAttr('class');
        $('.display').find('li#list').removeAttr('class');
////        $('#displayType').attr('data-display', 'tab');
        //var localStorage.display1='tab';
        //console.log('vibran tab localStorage.display1 '+localStorage.display1);
    }


}

function dropDown()
{
	elementClick = '#header .current';
	elementSlide =  'ul.toogle_content';
	activeClass = 'active';

	$(elementClick).on('click', function(e){
		e.stopPropagation();
		var subUl = $(this).next(elementSlide);
		if(subUl.is(':hidden'))
		{
			subUl.slideDown();
			$(this).addClass(activeClass);
		}
		else
		{
			subUl.slideUp();
			$(this).removeClass(activeClass);
		}
		$(elementClick).not(this).next(elementSlide).slideUp();
		$(elementClick).not(this).removeClass(activeClass);
		e.preventDefault();
	});

	$(elementSlide).on('click', function(e){
		e.stopPropagation();
	});

	$(document).on('click', function(e){
		e.stopPropagation();
		var elementHide = $(elementClick).next(elementSlide);
		$(elementHide).slideUp();
		$(elementClick).removeClass('active');
	});
}

function accordionFooter(status)
{
	if(status == 'enable')
	{
		$('#footer .footer-block h4').on('click', function(e){
			$(this).toggleClass('active').parent().find('.toggle-footer').stop().slideToggle('medium');
			e.preventDefault();
		})
		$('#footer').addClass('accordion').find('.toggle-footer').slideUp('fast');
	}
	else
	{
		$('.footer-block h4').removeClass('active').off().parent().find('.toggle-footer').removeAttr('style').slideDown('fast');
		$('#footer').removeClass('accordion');
	}
}

function accordion(status)
{
	if(status == 'enable')
	{
		var accordion_selector = '#right_column .block .title_block, #left_column .block .title_block, #left_column #newsletter_block_left h4,' +
								'#left_column .shopping_cart > a:first-child, #right_column .shopping_cart > a:first-child';

		$(accordion_selector).on('click', function(e){
			$(this).toggleClass('active').parent().find('.block_content').stop().slideToggle('medium');
		});
		$('#right_column, #left_column').addClass('accordion').find('.block .block_content').slideUp('fast');
		if (typeof(ajaxCart) !== 'undefined')
			ajaxCart.collapse();
	}
	else
	{
		$('#right_column .block .title_block, #left_column .block .title_block, #left_column #newsletter_block_left h4').removeClass('active').off().parent().find('.block_content').removeAttr('style').slideDown('fast');
		$('#left_column, #right_column').removeClass('accordion');
	}
}

function bindUniform()
{
	if (!!$.prototype.uniform)
		$("select.form-control,input[type='radio'],input[type='checkbox']").not(".not_uniform,.ps-switch").uniform();
}

// Функция превращает ссылки в пагинации в ссылки для
// определенного местоположения на странице
function paginationToTop() {

	$('ul.pagination a').each(function(indx, el){

		$(el).attr('href', $(el).attr('href') + '#subcategories-box');

    });

}

//Функция активирует управление стрелочками
function keyOrderActivate(){

    //if(!focus) focus = $('#appTabView table input:first');


	$('body').on('keydown', function(e) {
        var focus = $(document.activeElement);
        if (focus.is("#appTabView input")){
            var input = null;
            switch (e.keyCode) {
                // case 39: // right
                //     td = $(this).parent('td').next();
                //     break;
                //
                // case 37: // left
                //     td = $(this).parent('td').prev();
                //     break;

                case 40: // down
					if(e.ctrlKey){
						focus.val(parseInt(focus.val())-parseInt(focus.attr('data-product-r3')));
                        focus.change();
					}
					else{
                        input = focus.closest('#appTabView tr').next().find('input');
					}
                    break;
                case 38: // up
                    if(e.ctrlKey){
                        focus.val(parseInt(focus.val())+parseInt(focus.attr('data-product-r3')));
                        focus.change();
                    }
                    else{
                        input= focus.closest('#appTabView tr').prev().find('input');
                    }
                    break;
            }
            if(input !== null) input.focus();
		}
    });

}