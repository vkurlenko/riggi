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
		
	var locInfo = [
		'<div><img src="/img/pic/city_moscow.jpg" width=328 height=246><span id="showMoscow">Всего магазинов</span></div>',
		'<div><img src="/img/pic/city_kostroma.jpg" width=334 height=228></div>'
	]
  
  	//var m1, m2
	
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
	
		
	setMarkers(map, locations);
	
	// маркеры в начальном положении	
	function setMarkers(map, locations) 
	{	 
	
		var markerImg = 'http://'+'<?=$_SERVER['HTTP_HOST']?>'+'/img/tpl/icon_pointer.png';
		
		for (var i = 0; i < locations.length; i++) 
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
			
			var contentString  = place[4]
			
			var infowindow = new google.maps.InfoWindow(
			{
				content: contentString
			});
			
						
			google.maps.event.addListener(marker, 'click', (function(marker, i, contentString) 
			{
				return function() 
				{
					var infowindow = new google.maps.InfoWindow()
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
				}
			})(marker, i, contentString));
						
		}
		
		
	}
	
	
  }
</script>

<div id="map_canvas" style="width:100%; height:100%"></div>