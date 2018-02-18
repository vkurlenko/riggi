<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
body, #mycanvas{padding:0; margin:0}
#mycanvas{cursor:pointer}
#loader{position:absolute; top:50%; left:50%; margin-left:-50px; background:#FF0000; color:#000000; width:100px; height:20px; text-align:center}
</style>

<script language="javascript" src="/js/jquery.1.7.1.min.js"></script>
<script language="javascript" src="/js/excanvas_r3/excanvas.js"></script>
</head>

<body>
<!--

<div class="test"></div>
<input type="button" class="button">-->

<canvas id="mycanvas"></canvas>

<div id="loader">0</div>


<script language="javascript">



$(document).ready(function()
{
	var canvas = document.getElementById('mycanvas'); // создаем элемент canvas

	if($.browser.msie && $.browser.version<9) { G_vmlCanvasManager.initElement(canvas); } // костыль для IE
	
	var ctx = canvas.getContext("2d"); // инициализируем контекст
	
	var h = $(window).height() - 5
	var w = $(window).width()
	
	//alert(w+'x'+h)
	
	ctx.canvas.height = h
	ctx.canvas.width = w
	
	function draw(obj)
	{
		ctx.drawImage(obj, 0, 0, 1200, 700, 0, 0, w, h)
		//return true
	}


	var arr = new Array();
	var arrLoaded = new Array();
	
	var n = 100 	// кол-во кадров
	var intCheck 	= 1000	// интервал между проверками загрузки картинок
	var intDraw 	= 100 	// интервал мужду кадрами
	var countStop	= 50	// максимальное кол-во циклов проверок на загрузку (во избежание зацикливания)
	var p = 0;
	
	
	// загрузим все картинки
	for(i = 1; i < n; )
	{
		arr[i] = new Image();
		arr[i].src = '/img/pic/anime2/'+i+'.jpg';	
		arrLoaded[i] = 	arr[i].src;
		//i = i + 2
		i++
		//alert(i)
		
	}
	
	//alert(arr.length)
	
	
	// проверим, загружены ли все картинки
	function loadedAll()
	{
		var a = true
		
		for(i = 1; i < arr.length; i++)
		{
			if(arr[i].width > 0)
			{
				//$('.test').append(arr[i].src+' загружен<br>')
			}
			else
			{
				$('#loader').text(i+'%')
				//$('.test').append(arr[i].src+' Не загружен<br>')
				var a = false
				break;
			}
		}
		
		return a
	}
	
	
	j = 1;
	
	var count = 0;  // счетчик кол-ва циклов проверки загрузки картинок 
	
	
	k = setInterval(function()
	{
		count++
		// если загружены все картинки,
		// то запустим прорисовку
		if(loadedAll())
		{
			//$('.test').append('Загружены все')
			$('#loader').remove()
			
			clearInterval(k);
			
			// цикл прорисовки
			m = setInterval(function()
			{
				draw(arr[j])
				j++
				
				if(j > (arr.length - 1))
				{
					clearInterval(m);
					//window.location.href = '/main/'
				}
			}, intDraw)
			
		}
		
		if(count > countStop)
		{
			clearInterval(k);
			alert('Ограничение кол-ва циклов')
			window.location.href = '/main/'
			//$('.test').append('Ограничение кол-ва циклов')
		}
			
		
	}, intCheck)
	
	
	$('#mycanvas').click(function(){window.location.href = '/main/'})
		
})


</script>

</body>
</html>
