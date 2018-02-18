<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
html{background:#333}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
HTML::insertMeta();
?>
<style>
html{}
html, body{padding:0; margin:0; }
#mainFrame{border:0; width:100%; height:100%; position:absolute; /*min-height:600px; min-width:800px*/ overflow:hidden}
#player{position:absolute; bottom:0; z-index:100; background:none; /*width:100px; height:50px;*/ left:50%; margin-left:-60px}
#trackList{display:none}
#trackControls{display:block; margin:0; padding:0}
#trackControls li{display:block; list-style:none; margin:0; padding:0}
#trackControls li a{display:block; width:50px; height:50px}
.jp-play, .jp-pause{position:absolute; border:none; outline:none}
.jp-play{z-index:2; background:url(/img/tpl/transparent.gif); border:0px solid #0f0}
.jp-pause{z-index:3; background:url(/img/tpl/transparent.gif);  border:0px solid #f00; display:none}
.jp-paus{display:none}
</style>

<script language="javascript" type="text/javascript" src="/js/jquery.1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>

<script language="javascript">
$(document).ready(function()
{
	
	/*$.cookie('status', 	null, {path : '/'})
	$.cookie('status2', null, {path : '/'})
	$.cookie('status3', null, {path : '/'})*/
	
	
	//if($.cookie('status') == null)
		$.cookie('status', '0', {path : '/'})
		
	//if($.cookie('status2') == null)
		$.cookie('status2', '0', {path : '/'})
		
	//if($.cookie('status3') == null)
		$.cookie('status33', '0', {path : '/'})
	
	// функция переформатирования страницы
	function resizeFrame()	
	{
		var h = $(window).height();
	
		$('#mainFrame').height(h)
		//$('#mainFrame').css({'height' : '100%'})
	}
	
	/*
		при изменении размеров окна вызов функции переформатирования страницы
	*/
	
	resizeFrame()
	
	var resizeFrameTimer = null;
	
	$(window).bind('resize', function()
	{
		if (resizeFrameTimer != null) 
		{
			clearTimeout(resizeFrameTimer);
			resizeFrameTimer = null;
		}
		
		resizeFrameTimer = setTimeout(resizeFrame, 100);
		
	})	
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	
	// Local copy of jQuery selectors, for performance.
	var	my_jPlayer 			= $("#jquery_jplayer"),
		my_trackName 		= $("#jp_container .track-name"),
		my_playState 		= $("#jp_container .play-state"),
		my_extraPlayInfo 	= $("#jp_container .extra-play-info");

	// Some options
	var	opt_play_first 		= true, // If true, will attempt to auto-play the default track on page loads. No effect on mobile devices, like iOS.
		opt_auto_play 		= true, // If true, when a track is selected, it will auto-play.
		opt_text_playing 	= "Now playing", // Text when playing
		opt_text_selected 	= "Track selected"; // Text when not playing

	// A flag to capture the first track
	var first_track = true;

	// Change the time format
	$.jPlayer.timeFormat.padMin = false;
	$.jPlayer.timeFormat.padSec = false;
	$.jPlayer.timeFormat.sepMin = " min ";
	$.jPlayer.timeFormat.sepSec = " sec";

	// Initialize the play state text
	my_playState.text(opt_text_selected);
	
	
	
	
	
	
	

	// Instance jPlayer
	my_jPlayer.jPlayer({
		ready: function () 
		{
			var firstTrack = $('.track').eq(0).attr('href');
			my_jPlayer.jPlayer("setMedia", {
				mp3: firstTrack
			});
			//alert(opt_auto_play)
			if(opt_auto_play) 
			{	
				my_jPlayer.jPlayer("play");
			}
			first_track = false;
			$(this).blur();
			//$("#jp_container .track-default").click();
		},
		timeupdate: function(event) 
		{
			//my_extraPlayInfo.text(parseInt(event.jPlayer.status.currentPercentAbsolute, 10) + "%");
		},
		play: function(event) 
		{
			$('#mainFrame').contents().find('.iconSound img').attr('src', '/img/tpl/icon_sound.gif')
			//$('#mainFrame').contents().find('#status2').text('0')
			$.cookie('status2', '0', {path : '/'})
			//$.cookie('status3', '0')
		},
		pause: function(event) 
		{
			$('#mainFrame').contents().find('.iconSound img').attr('src', '/img/tpl/icon_sound_off.gif')
			//$('#mainFrame').contents().find('#status2').text('1')
			$.cookie('status2', '1', {path : '/'})
			//$.cookie('status3', '1')
			//alert($.cookie('status2'))
		},
		ended: function(event) 
		{
			my_playState.text(opt_text_selected);
		},
		loop : true,
		swfPath: "js/jplayer",
		cssSelectorAncestor: "#jp_container",
		supplied: "mp3",
		wmode: "window"
	});

	// Create click handlers for the different tracks
	$("#jp_container .track").click(function(e) 
	{
		my_trackName.text($(this).text());
		my_jPlayer.jPlayer("setMedia", 
		{
			mp3: $(this).attr("href")
		});
		if((opt_play_first && first_track) || (opt_auto_play && !first_track)) 
		{
			my_jPlayer.jPlayer("play");
		}
		first_track = false;
		$(this).blur();
		return false;
	});
	
	
	
	
	
	
	
	
	m = setInterval(function()
	{
//		alert('f')
		/*if($('#mainFrame').contents().find('a').is('#status'))
		{*/
			/*var status 	= $('#mainFrame').contents().find('#status').text()
			var status2 = $('#mainFrame').contents().find('#status2').text()
			var status3 = $('#mainFrame').contents().find('#status3').text()*/
			
			var status 	= $.cookie('status')
			var status2 = $.cookie('status2')
			var status33 = $.cookie('status33')
			
			
			$('#mainFrame').contents().find('#status').text(status)
			$('#mainFrame').contents().find('#status2').text(status2)
			$('#mainFrame').contents().find('#status33').text(status33)

			//alert('status = '+$.cookie('status')+' status2 = '+$.cookie('status2')+' status33 = '+$.cookie('status33'))
				
			if(status == '1')
			{
				//alert('1')
				my_jPlayer.jPlayer("pause");
				//clearInterval(m);
			}
			else
			{
				if(status == '0' && status33 == '0')
				{
					//alert('2')
					my_jPlayer.jPlayer("play");
				}				
				else
				{
					//alert('3')
					my_jPlayer.jPlayer("pause");
				}
			}
			
			
				
		/*}*/
		
	}, 1000)
	
	//alert($.cookie('status3'))
	
	//$.cookie('status33', null)
	
	$('.jp-pause, .jp-play').click(function()
	{
		if($.cookie('status33') == null)
		{
			$.cookie('status33', '1', {path : '/'}) // off
			//$('#mainFrame').contents().find('#status3').text('1')
		}
		else
		{
			if($.cookie('status33') == '1')
			{
				$.cookie('status33', '0', {path : '/'}) // on
				//$('#mainFrame').contents().find('#status3').text('0')
			}
			else
			{
				$.cookie('status33', '1', {path : '/'}) // off
				//$('#mainFrame').contents().find('#status3').text('1')
			}
		}
		
	})
	
	
})
</script>
</head>
<body><iframe id="mainFrame" scrolling="no" src="/main/" frameborder="0" marginheight="0" marginwidth="0" style="border:0px solid #003366"></iframe>

<div id="player">

	<div id="jquery_jplayer"></div>

	<!-- Using the cssSelectorAncestor option with the default cssSelector class names to enable control association of standard functions using built in features -->

	<div id="jp_container" class="demo-container">
		<ul id="trackList">
			<li><a href="/audio/soundBack.mp3" class="track track-default">soundBack.mp3</a></li>
		</ul>
		
		
		
		<!--<p>
			<span class="play-state"></span> :
			<span class="track-name">nothing</span>
			at <span class="extra-play-info"></span>
			of <span class="jp-duration"></span>, which is
			<span class="jp-current-time"></span>
		</p>-->
		<ul id="trackControls">
			<li><a class="jp-play" href="#"><!--Play--></a></li>
			<li><a class="jp-pause" href="#"><!--Pause--></a></li>
			<li><a class="jp-stop" href="#"><!--Stop--></a></li>
		</ul>
		<!--<ul>
			<li>volume :</li>
			<li><a class="jp-mute" href="#">Mute</a></li>
			<li><a class="jp-unmute" href="#">Unmute</a></li>
			<li> <a class="jp-volume-bar" href="#">|&lt;----------&gt;|</a></li>
			<li><a class="jp-volume-max" href="#">Max</a></li>
		</ul>-->
	</div>


</div>

</body>
</html>