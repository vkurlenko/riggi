<?
switch(LANG)
{
	case '_eng' : $language = 'en-US'; break;
	default : $language = 'ru-RU'; break;
}
?>
<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.full&lang=<?=$language?>" type="text/javascript"></script>
<?

// координаты поинтера
/*if(isset($_PAGE['p_tags']) && trim($_PAGE['p_tags']) != '')
	$objCoord = $_PAGE['p_tags'];
else
	$objCoord = '55.76, 37.64';*/
	
	
	
function getObjects()
{
	global $_VARS, $_PAGE;
	
	$arrObjects = array(
			'map_pointer' => '', 
			'map_polygon' => '[]'
		);
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_yandex_map`
			WHERE map_item = ".$_PAGE['id']."
			LIMIT 0,1";
			
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);		
		
		$arrObjects = array(
			'map_pointer' => trim($row['map_pointer']), 
			'map_polygon' => trim($row['map_polygon'])
			);
	}
	/*else
	{}*/
	

	return $arrObjects;			
}	

$arrObjects = getObjects();

?>
<script type="text/javascript">

ymaps.ready(init);

var myMap, center;


function init(){     
		
	myMap = new ymaps.Map("map", {
		center: [<?=$arrObjects['map_pointer']?>],
		zoom: 13,
		behaviors:['default', 'scrollZoom']
	});
		
	// Создание экземпляра элемента управления
	myMap.controls.add(
	   new ymaps.control.ZoomControl()	  	   
	);
	
	myMap.controls.add( new ymaps.control.TypeSelector(['yandex#map', 'yandex#satellite']))
	myMap.controls.add('trafficControl');
	myMap.controls.add('mapTools');
		
	//----------------------------------------------
	
	myPlacemark2 = new ymaps.Placemark([<?=$arrObjects['map_pointer']?>], {
		// Свойства.
		//hintContent: 'Собственный значок метки'
	}, {
		// Опции.
		// Своё изображение иконки метки.
		iconImageHref: '/img/tpl/icon_pointer.png',
		// Размеры метки.
		iconImageSize: [39, 83],
		// Смещение левого верхнего угла иконки относительно
		// её "ножки" (точки привязки).
		iconImageOffset: [-25, -90]
	});
	
	// Добавляем все метки на карту.
	myMap.geoObjects
		.add(myPlacemark2);
	
	
	
	
	// Добавляем ломаную на карту
	
	var polyline = new ymaps.Polyline(<?=$arrObjects['map_polygon']?>, {}, {
					strokeColor: '#ff0000',
					strokeWidth: 5 // ширина линии
				});
	
	myMap.geoObjects.add(polyline);
}
</script>

<div class="mapYa">
	<div id="map" style="width: 100%; height: 100%;"></div>
</div>