<?

switch(LANG)
{
	case '_eng' : $language = 'en'; break;
	default : $language = 'ru'; break;
}
?>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAEOxUQE6MuU_xMTruwU2Ols-Mu7WExjUA&sensor=false&language=<?=$language?>"></script>
<script language="javascript" type="text/javascript" src="/js/infobox.js"></script>
<script type="text/javascript">

var map;

var markersArray = [];

// стили карты google.map
var styleArray = [
  {
	featureType: "all",
	stylers: [
	  { saturation: -80 }
	]
  }
];	


// объект - всплывающее окно
var	infowindow = new google.maps.InfoWindow()

// иконка маркера
var markerImg = 'http://'+'<?=$_SERVER['HTTP_HOST']?>'+'/img/tpl/icon_pointer.png';

// массив городов
var locCity = []

// массив магазинов по городам		
var locShop = []

<?
	// сформируем массивы маркеров городов и магазинов в городах
	include 'blocks/js.city.php';
	
	// координаты центра карты в начальном положении
	if(isset($_VARS['env']['googleMapDef']))
		$coordDef = $_VARS['env']['googleMapDef'];
	else
		$coordDef = '56.752723,56.337891'; 
		
		
	
	// если есть параметр id города, 
	// то отцентрируем карту по этому городу
	if(isset($_GET['param2']))
	{
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_yandex_map`
				WHERE map_item = ".$_GET['param2'];
				
		$res = mysql_query($sql);
		
		if($res && mysql_num_rows($res))
		{
			$row = mysql_fetch_array($res);
			if($row['map_pointer'] != '' && $row['map_pointer'] != '[]')
				$coordDef = $row['map_pointer'];
		}		
	}
		
	
?>
		

var ib = new InfoBox();
var boxText = document.createElement("div");
boxText.style.cssText = "border: 0px solid black; padding: 0;";


		
function initialize(locCity) 
{  
	// параметры карты google.map начальные
	var mapOptions = {
	  center	: new google.maps.LatLng(<?=$coordDef?>),
	  zoom		: 5,
	  mapTypeId	: google.maps.MapTypeId.ROADMAP	  
	};
	
	// создадим карту	
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);	
	
	// применим стили к карте
	map.setOptions({styles: styleArray});
	
	// расставим маркеры (только города)
	setMarkers(map, locCity);			
	
}


// маркеры в начальном положении (города)	
function setMarkers(map, locCity) 
{	 	
	
	// в цикле создадим маркеры
	for (var i = 0; i < locCity.length; i++) 
	{
		var place = locCity[i];
		
		var myLatLng = new google.maps.LatLng(place[1], place[2]);
		
		var marker = new google.maps.Marker(
		{
			position: myLatLng,
			map: map,			
			icon: markerImg,			
			title: place[0],
			zIndex: place[3]
		});
		
		// html всплывающего окна
		var tagImg = '';
		if(place[4][0] != '/')
			tagImg = '<img src="'+place[4][0]+'"  width=311 height=246>';
			
		var contentString = '<div class="infoWindowContent">'+tagImg+'<div><strong>'+place[4][1]+'</strong><span><?=TEXT_LANG(1)?> '+place[4][2]+'</span><div><a  href="javascript:zoomCity('+i+'); ib.close(); void(0)"><?=TEXT_LANG(2)?></a></div></div></div><div style="background:none; height:40px">&nbsp;</div>'
		
		// по клику на маркер открываем всплывающее окно (infoWindow)
		google.maps.event.addListener(marker, 'click', (function(marker, i, contentString, infowindow) 
		{
			return function() 
			{
				//infowindow.setContent(contentString);
				//infowindow.open(map, marker);								
				
				boxText.innerHTML = contentString;
						
				var myOptions = {
						 content: boxText,
						 alignBottom : true
						,disableAutoPan: false
						,maxWidth: 0
						,pixelOffset: new google.maps.Size(-40, 0)
						,zIndex: null
						,boxStyle: { 
						   width : 351
						 }
						,closeBoxMargin: "4px 4px 4px 2px"
						,closeBoxURL: "/img/tpl/button_close.png"
						,infoBoxClearance: new google.maps.Size(1, 1)
						,isHidden: false
						,pane: "floatPane"
						,enableEventPropagation: false
				};
				
				ib.close();
				ib.setOptions(myOptions)
				ib.open(map, this);
					
			}
		
		})(marker, i, contentString, infowindow));	
		
		
		
		// занесем маркеры городов в массив
		markersArray[i] = marker
	}		
}

// маркеры магазинов в городах	
function setMarkersShop(map, locCity) 
{	 	
	// в цикле создадим маркеры
	for(var i = 0; i < locCity.length; i++) 
	{
		var place = locCity[i];
		
		var myLatLng = new google.maps.LatLng(place[1], place[2]);
		
		var marker = new google.maps.Marker(
		{
			position: myLatLng,
			map: map,			
			icon: markerImg,			
			title: place[0],
			zIndex: place[3]
		});
		
		// html всплывающего окна
		var tagImg = '';
		if(place[4][0] != '/')
			tagImg = '<img src="'+place[4][0]+'">';
		
		var contentString = '<div class="infoWindowContent">'+tagImg+'<div><strong>'+place[4][1]+'</strong><span></span><div><a  href="'+place[4][3]+'"><?=TEXT_LANG(3)?></a></div></div></div><div style="background:none; height:40px">&nbsp;</div>'
		
		// по клику на маркер открываем всплывающее окно
		google.maps.event.addListener(marker, 'click', (function(marker, i, contentString, infowindow) 
		{
			
			return function() 
			{
				//infowindow.setContent(contentString);
//				infowindow.open(map, marker);		

				boxText.innerHTML = contentString;
						
				var myOptions = {
						 content: boxText,
						 alignBottom : true
						,disableAutoPan: false
						,maxWidth: 0
						,pixelOffset: new google.maps.Size(-40, 0)
						,zIndex: null
						,boxStyle: { 
						   width : 351
						 }
						,closeBoxMargin: "4px 4px 4px 2px"
						,closeBoxURL: "/img/tpl/button_close.png"
						,infoBoxClearance: new google.maps.Size(1, 1)
						,isHidden: false
						,pane: "floatPane"
						,enableEventPropagation: false
				};
				
				ib.close();
				ib.setOptions(myOptions)
				ib.open(map, this);			
			}			
		})(marker, i, contentString, infowindow));			
	}		
}

// покажем маркеры магазинов в городе	
function zoomCity(i)
{
	// удалим маркер самого города
	markersArray[i].setMap(null);

	// координаты центра города
	var myLatlng = new google.maps.LatLng(locCity[i][1], locCity[i][2]);
	
	// расставим маркеры магазинов
	setMarkersShop(map, locShop[i]) 
	
	// центруем карту 
	map.setCenter(myLatlng)
	
	map.setZoom(12)		
}




google.maps.event.addDomListener(window, 'load', function(){
	initialize(locCity)

<?
if($currentCity != -1)
	echo 'zoomCity('.$currentCity.')';
?>	
	
	
	
	
});


 		




//
</script>

<div id="map_canvas" style="width:100%; height:100%"></div>