// JavaScript Document


// минимальная высота окна
var winHmin = 0;
//var winHmin = 0;
var winWmin = 0;

var plashArr = [];
var plashB;


// определим размеры окна
function windowSize()
{
	
	var winW =  $(window).width();	
	var winH =  $(window).height();	
	//alert(winW+' x '+winH)
	
	if(winHmin > 0)
	{
		winH = winH < winHmin ? winHmin : winH;
	}
	
	if(winWmin > 0)
	{
		winW = winW < winWmin ? winWmin : winW;
	}
	
	var arrWinSize = [winW, winH];
	
	return arrWinSize;
}

/*~~~~~~~~~*/
/* 5px => 5*/
/*~~~~~~~~~*/
function getRealSize(value)
{	
	if(value != null)
	{
		value = value.replace("px", "") * 1;
	}
	else value = 0;
	
	return value;
}

function getRealWidth(obj)
{
	var w = $(obj).width() 
			+ getRealSize($(obj).css('margin-left')) + getRealSize($(obj).css('margin-right')) 
			+ getRealSize($(obj).css('padding-right')) + getRealSize($(obj).css('padding-left'))
	return w
}



/* 
	форматирование элементов страницы 
	относительно размеров окна 
*/

function resize()
{
	// определим размеры окна
	var winSize = windowSize();
	
	var winW =  winSize[0] //$(window).width();	
	var winH =  winSize[1] //$(window).height();	
	
	
	
	/*if(winHmin > 0)
	{
		winH = winH < winHmin ? winHmin : winH;
	}
	
	if(winWmin > 0)
	{
		winW = winW < winWmin ? winWmin : winW;
	}*/
			
	// растянем основной DIV по высоте окна
	$('#divMain').css(
	{
		'height' : winH - 1
	});
	
	
	
	// установим высоту content'a
	var bHeaderH  = getRealSize($('.blockHeader').css('height'));
	//var bContentH = getRealSize($('.blockContent').css('height'));	
	
	// форматирование подвала
	formatFooter()
	
	var bFooterH = getRealSize($('.blockFooter').css('height'));	
	
	var bContentH = winH - bHeaderH - bFooterH - 1;
	
	$('.blockContent').css(
	{
		'height' : bContentH
	});
	
	// форматирование главного меню
	menuMainSpace()			
	
	// 
	formatNewsLine()
	
	// форматирование новостной ленты в новостях (tplNews)
	newsTape(bContentH)
	
	// форматирование элементов страницы Контакты
	formatContacts(bContentH)
	
	// форматирование RiggiTube
	formatTube(bContentH)
	
	// форматирование городов
	formatShopCity(bContentH)
	
	// прокрутка городов
	initScrollCity()
		
	// форматирование картинок по размерам родительского контейнера
	resizeImgByCont()
	
	formatTextPage(bContentH)
	
	// инициальзация локального скролла
	localScroll(bContentH)
	
	// форматирование блока новостей в ленте (tplNews)
	formatNewsItemBox()	
	
	// инициализация галереи в Коллекциях
	initGallery(bContentH)
	
	//initTube()
		
}

function formatTextPage(bContentH)
{
	if($('#divMain').is('.tplText'))
	{
		if($('.textImgCont').length)
		{
			var h = $('.textImgCont').find('img').css('height')
			var marginTop = (bContentH - getRealSize(h)) / 2
			$('.textImgCont').css('margin-top', marginTop)
		}
	}
}

function initTube()
{
	if($('#divMain').is('.tplTube'))
	{
		var startItem = 0;	
		
		
		$('.tubeItem').each(function()
		{
			if($(this).is('.active'))
				startItem = $(this).index()
		})

		function CarouselMouseWheel(event, delta) 
		{
			if (delta < 0) 
				$('.jcarousel-next-horizontal').click();  
			else if (delta > 0)
				$('.jcarousel-prev-horizontal').click();
		}
	
		jQuery('#mycarousel').jcarousel(
		{
			start: startItem
		});	
		
		$('#mycarousel').mousewheel(function (event, delta)
		{
			CarouselMouseWheel(event, delta);
			return false;
		});
	}	
	
}


function formatNewsLine()
{
	if($('#divMain').is('.tplMain'))
	{
		//var winW =  $(window).width();
		// определим размеры окна
		var winSize = windowSize();
		
		var winH =  winSize[0] //$(window).height();	
		var winW =  winSize[1] //$(window).width();	
		
		var obj = $('.newsLine');
		
		$(obj).css(
		{
			'margin-left' : ($(obj).width() / 2 * -1) 
		})	
		
		var objItem = $('.item')
		$(objItem).width($(obj).width())
		
		
		var wMin = 1220	// номинальная ширина блока без изменения размера шрифта
		var k 	 = 0.9	// коэффициент уменьшения шрифта в зависимости от ширины блока
			
		if($(obj).width()  < wMin)
		{
			var newFontSize = getRealSize($('.newsTitle').css('font-size')) * k			
			changeCSS($('.newsTitle'), 'font-size', newFontSize)
			
			var newFontSize = getRealSize($('.newsShText').css('font-size')) * k			
			changeCSS($('.newsShText'), 'font-size', newFontSize)
		}
		else 
			$('.newsTitle, .newsShText').removeAttr('style')
		
		$('.items').css({'left' : 0})
		$(".scrollable").scrollable(
		{
			circular: true
		})
	
	}
}


// инициализация галереи в Коллекциях

function initGallery(bContentH)
{
	if($('#divMain').is('.tplColl'))
	{
		// начальные установки при реинициализации
		$('.slide_control, .slide_overlay').remove()		
		
		$('#gallery_slider').css({'top' : 0})
		
		// высота блока с галереей принимается как высота картинки в слайде
		var h = $('#gallery_slider .slide a img').height()
		
		// высота заголовка слайда
		var hInfo = getRealSize($('#gallery_slider .slide-info').css('height'))		
		
		
		// определим высоту блока с галереей
		if(h > bContentH) 
		{
			h = bContentH
		}
		else
		{
			var top = (bContentH - h) / 2
			$('#gallery_slider').css({'top' : top})
		}
		
		// установки для элементов галереи
		$('#gallery_slider, #gallery_slider .slide a, #gallery_slider .slide span.wrap').css(
		{
			'height' : 	h
		})	
		
		$(' #gallery_slider .slide a > div').css(
		{
			'height' : h - hInfo/*,
			'width'  : $(this).find('img').width()*/
		})	
		
		// собственно инициализация
		$('#gallery_slider').gallerySlider();
		
		$('.slide_overlay').css({'height' : h - hInfo})
		
	}
}


function initScrollCity()
{
	if($('#divMain').is('.tplShopsPic'))
	{
		// определим ширину блока со скроллингом
		var w = 0;
		var maxLeft = 0;
		var maxRight = 0;
		
		$('.cityPlash').each(function()
		{
			var p = $(this).position()
			var thisLeft = p.left
			var thisRight = p.left + $(this).width()
			
			if(thisLeft < maxLeft)
				maxLeft = thisLeft
				
			if(thisRight > maxRight)
				maxRight = thisRight
			
		})
		
		w = Math.abs(maxLeft) + maxRight
		
		if($(window).height() < w)
			$('.cityGalleryInner').width(w + 400)
		
		var pane = $('.cityGallery')		
		
		pane.jScrollPane(
		{
			showArrows : true,
			wheelSpeed : 50,
			/*animateScroll				: true,*/
			/*arrowButtonSpeed			:0,
			arrowRepeatFreq  : 0,*/
			speed: 500//,
			//animateScroll				: false,
			//scrollPagePercent : 50	
		});	
		
		var api = pane.data('jsp');
		
		var сenter = (getRealSize($('.cityGalleryInner').css('width')) - getRealSize($('.cityGallery').css('width'))) / 2
		
		api.scrollTo(сenter);
	
	}
	
}

// формат галереи городов
function formatShopCity(bContentH)
{
	// номинальная высота блока с контентом
	var hNormal = 770
	
	// процентное соотношение реальной высоты и номинальной
	var k = bContentH * 100 / hNormal
	
	$('.cityGalleryInner, .jspContainer').css('height', bContentH)	
	
	k > 100 ? k = 100 : k = k

	var b = Math.ceil(plashB * k/100)
	//alert(b)
	
	$('.cityPlash').each(function()
	{
		var img = $(this).find('img')
		var left = plashArr[$(this).attr('class')][0]
		var top = plashArr[$(this).attr('class')][1]
		var w = $(img).attr('width')
		var h = $(img).attr('height')		
		
		
		$(this).css(
		{
			'width'	: w,
			'height': h
		})
		
		$(this).css(
		{
			'left'  : left * k / 100 - b,
			'top'	: top * k / 100 - b,
			'width'	: Math.ceil(w * k / 100),
			'height': Math.ceil(h * k / 100),
			'border-width' : b
		})
		
	})
	
	$('.cityGallery').css('visibility', 'visible')
}

// формат подвала
function formatFooter()
{
	var a = $('.footerBarLeft').width()
	var b = $('.menuFoot').width() + getRealSize($('.menuFoot').css('margin-left')) + 40
	var w = a - b - 10
	$('.footerBarRSearch').width(w)
	
	// ширина блока с формой поиска (вычитаем ширину значка звука и разделителя)
	var sw = w - 72 - 5
	var swMax = getRealSize($('.search').css('max-width'))
	if(sw > swMax)
		sw = swMax
	
	$('.search').width(sw)
	
	// ширина поля ввода
	var m = getRealSize($('.search input').css('margin-left'))	
	$('.search input').width(sw - m - $('.search a img').width())	
	
	$('.blockFooter').css('visibility', 'visible')
}

// форматирование riggiTube
function formatTube(bContentH)
{
	if($('#divMain').is('.tplTube'))
	{
		var a = $('.tubeTape').height()
		
		$('.tubeContent').css({
			'height' : 	bContentH - a				  
		})
		
		var playerH = $('.tubePleer').height()
		var k = $('.tubePleer iframe').attr('width') / $('.tubePleer iframe').attr('height');
		
		$('.tubePleer iframe').css(
		{
			'height' : playerH,
			'width'  : playerH * k
		})
		
		
		$('.tubeContent').css('visibility', 'visible')
	}
}



// форматирование элементов страницы Контакты

function formatContacts(bContentH)
{
	if($('#divMain').is('.tplCont'))	
	{
		$('.mapYa, .textBlockContent, .ymaps-map').css(
		{
			'height' : 	bContentH
		})
	}
}



// динамическое изменение стиля объекта

function changeCSS(obj, css, value)
{
	$(obj).css(css, value)
}


// форматирование блока новостей в ленте (tplNews)
function formatNewsItemBox()
{
	
	var w = parseInt($('.newsItem').find('img').width())
	
	$('.newsItem').each(function()
	{
		// изменение ширины блока в зависимости от ширины картинки		
		$(this).css(
		{
			'width' : w	
		})
	})	
	
	
	// изменение шрифта заголовка
	
	var wMin = 400	// номинальная ширина блока без изменения размера шрифта
	var k 	 = 0.05	// коэффициент уменьшения шрифта в зависимости от ширины блока
		
	if(w  < wMin)
	{
		var newFontSize = w * k			
		changeCSS($('.newsSnTitle'), 'font-size', newFontSize)
	}
	else 
		$('.newsSnTitle').removeAttr('style')
}


// форматирование новостной ленты в новостях
function newsTape(bContentH)
{
	if($('#divMain').is('.tplNews'))
	{
		$('.newsItem').css('height', bContentH - 95)	
		$('.scrollable').css({
			'height' : bContentH,
			'visibility' : 'visible'}
			)
		
		$(".newsTape .scrollable").scrollable(
		{
			circular: false
		})	
	}
}



// локальный скролл
function localScroll(bContentH)
{
	
	if($('#divMain').is('.tplText') || $('#divMain').is('.tplCont') || $('#divMain').is('.tplSiteMap') || $('#divMain').is('.tplSubscr'))
	{	
	
		var a = getRealSize($('.textBlockContent').css('margin-top'))
		
		if($('#divMain').is('.tplText') || $('#divMain').is('.tplSiteMap'))
			var h = bContentH - (a + 20)
		else 
			var h = bContentH

		$('.textBlockContent').css(
		{
			'height' : h
		}).jScrollPane(
		{
			contentWidth : '90%'	
		});
	}	
}



/*
	форматирование главного меню	
*/
function menuMainSpace()
{
	
	var wMin = 1220	// номинальная ширина блока без изменения размера шрифта
	var k 	 = 0.7	// коэффициент уменьшения шрифта в зависимости от ширины блока
		
	if($(window).width()  < wMin)
	{
		//alert($(window).width())
		$('ul.menuMain li a').removeAttr('style')
		var newFontSize = getRealSize($('ul.menuMain li a').css('font-size')) * k	
		var newMarginTop = Math.ceil(newFontSize / 2)	
		changeCSS($('ul.menuMain li a'), 'font-size', newFontSize)	
		changeCSS($('ul.menuMain li a').not('.logo, .tube'), 'margin-top', newMarginTop)	
	}
	else 
		$('ul.menuMain li a').removeAttr('style')
		
	// определим размеры окна
	var winSize = windowSize();
	
	var winW =  winSize[0] //$(window).width();	
	var winH =  winSize[1] //$(window).height();	
	
	
	
	// растянем div главного меню по ширине окна		
	var ulMenuMainW =  winW
						- getRealSize($('ul.menuMain').css('padding-left')) 
						- getRealSize($('ul.menuMain').css('padding-right'));
						
	var ulMenuMainH = $('ul.menuMain').height();					
						
	$('ul.menuMain').width(ulMenuMainW);
	
	// отступ сверху
	var bHeaderH  = getRealSize($('.blockHeader').css('height'))
	$('ul.menuMain').css({
		'margin-top' : (bHeaderH / 2 - 10)
	})
	
	// подсчитаем сумму длин всех пунктов гл.меню
	var liW = 0;
	$('ul.menuMain li').each(function()
	{
		liW = liW + $(this).width()		
	})
	
	// определим свободное простр-во,
	// поделим его на равные части
	// и установим отступы между пунктами меню
	var freeSpaceAll = ulMenuMainW - liW;
	
	var freeSpace = Math.floor(freeSpaceAll / ($('ul.menuMain li').size() - 1))
	
	if(freeSpace < 15)
	{
		freeSpace = 15
		//var newFontSize = getRealSize($('ul.menuMain li a').css('font-size')) * k			
		//var newFontSize = 	getRealSize($('ul.menuMain li a').css('font-size')) * 0.8			
		//changeCSS($('ul.menuMain li a'), 'font-size', newFontSize)	
	}
	else
		//$('ul.menuMain li a').removeAttr('style')
		
		
	$('ul.menuMain li').not($('ul.menuMain li').last()).css(
	{
		'margin-right' : freeSpace
	})	
	
	$('ul.menuMain li').eq($('ul.menuMain li').size() - 2).css(
	{
		'margin-right' : 0
	})	
}



/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*	ресайз картинки относительно родительского блока	*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function getImageSize(image)
{
	var arrImgSize = [];
	
	// загружаем картинку
	var img = new Image();
	img.src = $(image).attr("src");
	
	
	// если читаются атрибуты размеров, то промежуточным переменным присваиваем их значения
	// иначе (для IE) пытаемся прочитать и присваиваем значения размеров объекта Image
	if($(this).attr("width") > 0) 
		var imgW = $(image).attr("width")
	else 
		var imgW = img.width;
	
	
	if($(this).attr("height") > 0) 
		var imgH = $(image).attr("height")
	else 
		var imgH = img.height;
		
	//alert(imgW)
		
	return arrImgSize[imgW, imgH]
}

function resizeImgByCont()
{
	
	var obj = $(".mainCont1 img, .mainCont2 img, .textImgCont img, .newsItem img, .infoImg img, .tubePleer img, .imgCont img, .slide a div img");
	
	$(obj).each(function()
	{
													   
		// родительский контейнер для картинки
		var imgCont = $(this).parents("div").slice(0,1);
		
		
		//alert($(imgCont).height())
		
		/*var arrImgSize = getImageSize($(this))
		var imgW = arrImgSize[0]
		var imgH = arrImgSize[1]*/
		
		
		// загружаем картинку
		var img = new Image();
		img.src = $(this).attr("src");
		
		
		// если читаются атрибуты размеров, то промежуточным переменным присваиваем их значения
		// иначе (для IE) пытаемся прочитать и присваиваем значения размеров объекта Image
		if($(this).attr("width") > 0) 
			var imgW = $(this).attr("width")
		else 
			var imgW = img.width;
		
		
		if($(this).attr("height") > 0) 
			var imgH = $(this).attr("height")
		else 
			var imgH = img.height;
			
	
		var k = imgW / imgH;	// коэффициент пропорциональности сторон картинки
		
		
		// сбрасываем css width и height картинки
		$(this).attr("style", "");
		
		if($('#divMain').is('.tplText'))
		{
			var imgCssH = $('.textBlock').height();
			var imgCssW = imgCssH * k;		
			
			if(imgCssW > $(imgCont).width())
			{
				imgCssW = $(imgCont).width()
				imgCssH = imgCssW / k
			}
		}
		else
		{
		
			// определим новые css width и height картинки 
			// в зависимости от размеров родительского контейнера
			//$(imgCont).height(454)
			
			if($('#divMain').is('.tplColl'))
			{
				$(imgCont).height($('.blockContent').height() - 20)
			}
				
			
			var imgCssH = $(imgCont).height();
			var imgCssW = $(imgCont).height() * k;	
			
			//alert(imgCssH)
		}
		
		// если ширина получилось больше родительского блока, 
		// то еще раз пересчитываем (tplCont)
		if($('#divMain').is('.tplCont') || $('#divMain').is('.tplSubscr'))
		{
			if(imgCssW > $(imgCont).width())
			{
				imgCssW = $(imgCont).width()
				imgCssH = imgCssW / k
			}
		}
		
		// если текстовая страница, 
		// то размеры картинки не масштабируются 
		// больше ее реальных размеров
		if($('#divMain').is('.tplText'))
		{
			if(imgCssW > imgW || imgCssH > imgH)	
			{
				imgCssW = 	imgW;
				imgCssH = 	imgH;
			}
			
			//alert(imgCssW+' x' +imgCssH)
		}
		
		
		// присвоим их картинке и покажем ее
		$(this).css({
			"width" 		: imgCssW,
			"height"		: imgCssH
			}).fadeIn();
		
		
		
		if($('#divMain').is('.tplMain'))
		{
			$(imgCont).css({
				'margin-left' 	: imgCssW / 2 * -1
			})
		}
	})	
	
	// отступы между плашками на гл. странице
	if($('#divMain').is('.tplMain'))
	{
		var t = null;

		if (t != null) 
		{
			clearTimeout(t);
			t = null;
		}
	//	$('.mainCont1 .plash1, .mainCont1 .plash3').fadeOut()
	/*$('.mainCont1 .plash1').css({'left':-10000})
	$('.mainCont1 .plash3').css({'left':10000})	*/
		t = setTimeout(setSpace, 500);
	}
		/*setSpace()*/
	
}


// установим промежутки между плашками на главной странице 
function setSpace()
{
	
	var space = 88;
	var speed = 1000;
	
	/*$('.mainCont1 .plash1, .mainCont1 .plash3').hide()
	$('.mainCont1 .plash1').css({'left':-10000})
	$('.mainCont1 .plash3').css({'left':10000})	*/
	
	// (верхняя часть)
	var plash2Pos = $('.mainCont1 .plash2').position();
	var plash2PosLeft = plash2Pos.left  + getRealSize($('.mainCont1 .plash2').css('margin-left'));
	
	
	var plash1Left = plash2PosLeft 
					- space 
					- getRealSize($('.mainCont1 .plash1').css('width'));
	
	var plash3Left = plash2PosLeft 
					+ getRealSize($('.mainCont1 .plash2').css('width'))
					+ space;
																												  
	/*$('.mainCont1 .plash1').css(
	{
		'left' : plash1Left,
		'margin-left' : 0		
	})*/
	
	$('.mainCont1 .plash1').show().animate({
		'left' : plash1Left	,
		'margin-left' : 0
	}, speed)
	
	$('.mainCont1 .plash3').show().animate(
	{
		'left' : plash3Left,
		'margin-left' : 0
	}, speed)	
	
	//$('.mainCont1 .plash1, .mainCont1 .plash3').show()
	
	// (нижняя часть)
	var plash5Pos = $('.mainCont2 .plash5').position();
	
	var plash5PosLeft = plash5Pos.left  + getRealSize($('.mainCont2 .plash5').css('margin-left'));
	//alert(getRealSize($('.mainCont2 .plash5 img').css('width')))
	var plash4Left = plash5PosLeft 
					- space 
					- getRealSize($('.mainCont2 .plash4').css('width'));
	//alert(getRealSize($('.mainCont2 .plash5').css('width')))
	var plash6Left = plash5PosLeft + getRealSize($('.mainCont2 .plash5').css('width'))
					+ space;
																												  
	$('.mainCont2 .plash4').animate(
	{
		'left' : plash4Left,
		'margin-left' : 0		
	}, speed)
	
	$('.mainCont2 .plash6').animate(
	{
		'left' : plash6Left,
		'margin-left' : 0
	}, speed)
}






$(document).ready(function()
{
	
	/* 
		для главного меню 
	*/
	
	// первому пункту меню присвоим стиль логотипа
	$('ul.menuMain li a').first().addClass('logo');
	$('ul.menuMain li a').first().html('<img src="/img/tpl/riggi_logo.png" />');
	
	// вместо последнего пункта поставим картинку
	var src_over = '/img/tpl/riggi_tube_over.png';
	
	/*if($('#divMain').is('.tplColl'))
		var src = '/img/tpl/riggi_tube_dark.png';
	else
		var src = '/img/tpl/riggi_tube_light.png';
		*/
		var src = '/img/tpl/riggitube.png';
	
	/* if($('ul.menuMain li a').last().attr('class') == 'active')
		var src = src_over */

	var htmlRiggiTube = '<img src="'+src+'" width="62" height="19" />';
	$('ul.menuMain li a').last().addClass('tube').html(htmlRiggiTube);
	$('ul.menuMain li').last().css('float', 'right')
	
	
	
	/* $('.tube').hover(function()
	{
		$(this).find('img').attr('src', src_over)	
	},
	function(){
		$(this).find('img').attr('src', src)	
	}) */
	
	/* 
		/для главного меню 
	*/
	
	// в нижнем меню подставим буллеты
	$('ul.menuFoot li').not($('ul.menuFoot li').last()).after('<li class="bullet">&bull;</li>')
	
	initTube()
	
	/* считаем в массив начальные установки плашек городов */
	
	plashB = getRealSize($('.cityPlash').css('border-left-width'))
	
	//alert($('.cityPlash').css('border-left-width'))	
	
	$('.cityPlash').each(function()
	{
		var left = getRealSize($(this).css('left'))
		var top = getRealSize($(this).css('top'))
		
		plashArr[$(this).attr('class')] = [left, top]
	})
	/* /считаем в массив начальные установки плашек городов */
	
	
	// функция переформатирования страницы
	resize()	
	
	/*
		при изменении размеров окна вызов функции переформатирования страницы
	*/
	var resizeTimer = null;
	
	$(window).bind('resize', function()
	{
		if (resizeTimer != null) 
		{
			clearTimeout(resizeTimer);
			resizeTimer = null;
		}
 	   resizeTimer = setTimeout(resize, 100);
	})	
	
})



/*f06252*/
 
/*/f06252*/
