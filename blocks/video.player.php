<!--<img src="/img/pic/pleer.jpg" width="640" height="390" />-->
<?
/* воспроизведение видео */

$video_html 	= '';
$video_title 	= '';
$video_descr 	= '';
$video_date 	= '';
$video_W = 640;
$video_H = 390;

if (!function_exists('htmlspecialchars_decode')) {
        function htmlspecialchars_decode($str) {
                $trans = get_html_translation_table(HTML_SPECIALCHARS);

                $decode = ARRAY();
                foreach ($trans AS $char=>$entity) {
                        $decode[$entity] = $char;
                }

                $str = strtr($str, $decode);

                return $str;
        }
}

function get_youtube_id($url)
{
	if (strpos( $url,"v=") !== false)
	{
		return substr($url, strpos($url, "v=") + 2, 11);
	}
	elseif(strpos( $url,"embed/") !== false)
	{
		return substr($url, strpos($url, "embed/") + 6, 11);
	}

}

if(isset($_GET['param2']))
{
	$video_id 		= intval($_GET['param2']);
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_video`
			WHERE id = ".$video_id."
			AND video_show = '1'";	
	
}
else 
{
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_video`
			WHERE video_show = '1'
			ORDER BY video_create DESC
			LIMIT 0,1";	
}


			
$res = mysql_query($sql);

if($res && mysql_num_rows($res) > 0)
{
	$row = mysql_fetch_array($res);
	
	$video_id = $row['id'];
	
	$video_html 	= htmlspecialchars_decode($row['video_html']);
	
	// подставляем размеры
	$pattern[0] = '(width="\d+")';
	$replace[0] = 'width="'.$video_W.'"';
	$pattern[1] = '(height="\d+")';
	$replace[1] = 'height="'.$video_H.'"';
	
	$video_html = preg_replace($pattern, $replace, $video_html);
	
	
	$video_title 	= $row['video_title'];
	$video_descr 	= $row['video_descr'];
	$video_date 	= $row['video_create'];
	
	//echo $video_html;	
	
}
else
{
	echo 'Видео не найдено.';
}
?>
<!-- 1. The <iframe> (and video player) will replace this <div> tag. -->
    <div id="player"></div>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
    <script>
      var tag = document.createElement('script');

      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      var player;
	  
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '<?=$video_H?>',
          width: '<?=$video_W?>',
          videoId: '<?=get_youtube_id($row['video_html']);?>',
          events: {
            /*'onReady': onPlayerReady,*/
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) 
	  {
        event.target.playVideo();
      }

      function onPlayerStateChange(event) 
	  {
	  	//alert('statusV = '+$.cookie('status'))
		if (event.data == YT.PlayerState.PLAYING || event.data == YT.PlayerState.BUFFERING)
		{ 
			$.cookie('status', '1', {path : '/'})
			$.cookie('status2', '1', {path : '/'})
			//alert('statusV = '+$.cookie('status'))
		}
		else
		{
			$.cookie('status', '0', {path : '/'})
			//alert('statusV = '+$.cookie('status'))
		}
      }
	  
	  $(document).ready(function()
	  {
	  	var playerH = $('.tubePleer').height()
		var k = <?=$video_W?> / <?=$video_H?>;
		//alert(k)
		$('#player').css(
		{
			'height' : playerH,
			'width'  : playerH * k
		})
		
		
		$('.tubeContent').css('visibility', 'visible')
		
		
		$('.tplTube a').click(function()
		{
			$.cookie('status', '0', {path : '/'})
			/*if($.cookie('status') == '0' && $.cookie('status2') == '1' && $.cookie('status3') == '1')
			{
				$('#status2').text('0')
			}*/
			//$('#status, #status2').text('0')
			//my_jPlayer.jPlayer("play");
		})
	  })
    </script>

	<!--1<a id="status" style="display:inline"  href="#">0</a>
	2<a id="status2" style="display:inline" href="#">0</a>
	3<a id="status33" style="display:inline" href="#">0</a>-->

	<script>
	
	
		
		
		
	
	/*if($.cookie('status2') != null && $.cookie('status2') == '1')
	{
		$('#status2').text('1')
	}
	
	if($.cookie('status3') != null && $.cookie('status3') == '1')
	{
		$('#status3').text('1')
	}
	else
		$('#status3').text('0')*/
	</script>
	