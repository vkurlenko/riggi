<?
$center = '55.76, 37.64';

if(isset($row['map_polygon']) && trim($row['map_polygon']) != '')
	$coord = $row['map_polygon'];
else
	$coord = '[]';
	
if(isset($row['map_pointer']) && trim($row['map_pointer']) != '')
{
	$pointerCoord = $row['map_pointer'];
	$center = $row['map_pointer'];
}
else
	$pointerCoord = '[]';

?>
<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=ru-RU" type="text/javascript"></script>

<script type="text/javascript">
ymaps.ready(init);
var myMap;
	
function init(){     
		myMap = new ymaps.Map("map", {
			center: [<?=$center?>],
			zoom: 13,
			behaviors:['default', 'scrollZoom']
		});
		
	// Создание экземпляра элемента управления
	myMap.controls.add(
	   new ymaps.control.ZoomControl()	  	   
	);
	
	myMap.controls.add( new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite']))
	/*myMap.controls.add('trafficControl');
	myMap.controls.add('mapTools');*/
		
	//----------------------------------------------
	
	<?
	if($pointerCoord != '[]')
	{
	?>
		myPlacemark = new ymaps.Placemark([<?=$pointerCoord?>], 
		{
			// Свойства.
			//hintContent: 'Местоположение объекта'
		}, 
		{
			// Опции.
			// Своё изображение иконки метки.
			iconImageHref: '/img/tpl/icon_pointer.png',
			// Размеры метки.
			iconImageSize: [39, 83],
			// Смещение левого верхнего угла иконки относительно
			// её "ножки" (точки привязки).
			iconImageOffset: [-25, -90],
			
			draggable: true
		}
		);
	
		// Добавляем все метки на карту.
		myMap.geoObjects
			.add(myPlacemark);
	<?
	}
	?>
	
	function addPointer(ymaps, myMap, coord)
	{
		myPlacemark = new ymaps.Placemark(coord, 
		{
			// Свойства.
			hintContent: 'Местоположение объекта'
		}, 
		{
			// Опции.
			// Своё изображение иконки метки.
			iconImageHref: '/img/tpl/icon_pointer.png',
			// Размеры метки.
			iconImageSize: [39, 83],
			// Смещение левого верхнего угла иконки относительно
			// её "ножки" (точки привязки).
			//iconImageOffset: [-35, -60],
			
			draggable: true
		}
		);
	
		// Добавляем все метки на карту.
		myMap.geoObjects.add(myPlacemark);
	}
	
	
	$('#addPointer').click(function()
	{
		var coord = [<?=$center?>];
		addPointer(ymaps, myMap, coord)
			
		return false
	})
	
	function delPointer(myMap, myPlacemark)
	{
		myMap.geoObjects.remove(myPlacemark);
		myPlacemark.geometry.setCoordinates('');
		$('input[name="map_pointer"]').attr('value', '')
	}
	
	$('#delPointer').click(function()
	{
		delPointer(myMap, myPlacemark)
	})
	
	//----------------------------------------------
		
	var polyline = new ymaps.Polyline(<?=$coord;?>, {}, {
					strokeColor: '#ff0000',
					strokeWidth: 5 // ширина линии
				});
 
	myMap.geoObjects.add(polyline);
	/*polyline.editor.startEditing();	
	polyline.editor.startDrawing();	*/



	// Обработка нажатия на кнопку.
	$('#copyCoord').click(function() 
	{
		/*polyline.editor.stopEditing();
		printGeometry(polyline.geometry.getCoordinates());		
		return false*/
	});	
	
	// Кнопка редактирования линии
	$('#editCoord').click(function()
	{
		polyline.editor.startEditing();	
		return false
	})
	
	// Кнопка удаления линии
	$('#delCoord').click(function()
	{
		myMap.geoObjects.remove(polyline);
		polyline.geometry.setCoordinates('');
		$('input[name="map_polygon"]').attr('value', '')
		return false
	})
	
	// Добавляем линию на карту.
    $('#addCoord').click(function()
	{
		myMap.geoObjects.add(polyline);
		polyline.editor.startEditing();	
		polyline.editor.startDrawing();
		return false
	})
 
		
 
	// Выводит массив координат геообъекта в <div id="geometry">
	function printGeometry (coords) {

		$('input[name="map_polygon"]').attr('value', stringify(coords))

		function stringify (coords) 
		{
			var res = '';
			if ($.isArray(coords)) 
			{
				res = '[ ';
				for (var i = 0, l = coords.length; i < l; i++) 
				{
					if (i > 0) 
					{
						res += ', ';
					}
					res += stringify(coords[i]);
				}
				res += ' ]';
			} 
			else if (typeof coords == 'number') 
			{
				res = coords.toPrecision(6);
			} 
			else if (coords.toString) 
			{
				res = coords.toString();
			}

			return res;
		}
	}		
	
	
	$('#saveForm').click(function()
	{
	
		$('input[name="map_pointer"]').attr('value', myPlacemark.geometry.getCoordinates())
		polyline.editor.stopEditing();
		printGeometry(polyline.geometry.getCoordinates());		
		
		return true
	})
	
	$('#go').click(function()
	{
		var string = $('#search').attr('value')
		var myGeocoder = ymaps.geocode(string);
		myGeocoder.then(
			function (res) {
			
				//alert('Координаты объекта :' + res.geoObjects.get(0).geometry.getCoordinates());
				
				//delPointer(myMap, myPlacemark)
				
				var coord = res.geoObjects.get(0).geometry.getCoordinates();
				addPointer(ymaps, myMap, coord)
				myMap.panTo(coord, {flying: 1})				
			},
			function (err) {
				alert('Ошибка');
			}
		)
	})
}
 
</script>


<input type="button" id="addCoord" value="Добавить линию"/>
<input type="button" id="editCoord" value="Включить редактирование"/>
<input type="button" id="delCoord" value="Удалить линию"/><br>
<!--<input type="button" id="copyCoord" value="Запомнить координаты ломаной"/>-->

<br>

<input type="button" id="addPointer" value="Добавить метку"/>
<input type="button" id="delPointer" value="Удалить метку"/><br>
<input type="text" id="search" value="" /><input type="button" id="go" value="Найти на карте"/>
<!--<input type="button" id="copyPointer" value="Запомнить координаты метки"/>-->


<div class="mapYa">
	<div id="map" style="width: 100%; height: 100%;"></div>
</div>