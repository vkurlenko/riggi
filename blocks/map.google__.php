<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAEOxUQE6MuU_xMTruwU2Ols-Mu7WExjUA&sensor=false"></script>

<script type="text/javascript">
  function initialize() 
  {
  
  	// стили карты google.map
  	var styleArray = [
	  {
		featureType: "all",
		stylers: [
		  { saturation: -80 }
		]
	  }
	];
	
	
	var locations = [
		  ['Москва', 	55.790473,  37.606201,  4, '<div><img src="/img/pic/city_moscow.jpg" width=328 height=246><span id="showMoscow">Всего магазинов</span></div>'],
		  ['Кострома', 	57.780376,	40.954285, 	5, '<div><img src="/img/pic/city_kostroma.jpg" width=334 height=228></div>']
		];
		
	var markerImg = 'http://'+'<?=$_SERVER['HTTP_HOST']?>'+'/img/tpl/icon_pointer.png';
  
	
 	// параметры карты google.map начальные
	var mapOptions = {
	  center: new google.maps.LatLng(56.752723,56.337891),
	  zoom: 5,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	
	
	// создадим карту	
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);	
	
	// применим стили к карте
	map.setOptions({styles: styleArray});
	
		
	// маркеры в начальном положении	
	
	function setMarker(i, locations)
	{
		var place = locations[i];
		var myLatLng = new google.maps.LatLng(place[1], place[2]);
		var marker = new google.maps.Marker(
		{
			position: myLatLng,
			map: map,			
			icon: markerImg,			
			title: place[0],
			zIndex: place[3]
		});
		
		return marker
	}
	
	var infowindow = new google.maps.InfoWindow();	
	
	function setInfoW(i, marker, location, infowindow)
	{
		var place = locations[i];
		var contentString  = place[4]			
		/*var infowindow = new google.maps.InfoWindow(
		{
			content: contentString
		});	*/
		
		infowindow.setContent(contentString)
		
		google.maps.event.addListener(marker, 'click', function() 
		{
			infowindow.open(map, marker);			
		})	
	}
	
	// МОСКВА
	var marker0 = setMarker(0, locations)
	setInfoW(0, marker0, location)
	
	// КОСТРОМА
	var marker1 = setMarker(1, locations)
	setInfoW(1, marker1, location)	
	
	
  }
</script>

<div id="map_canvas" style="width:100%; height:100%"></div>