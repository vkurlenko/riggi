<?
include_once DIR_FRAMEWORK.'/class.image.php';

// добавим в галерею видео по убыванию даты
$sql = "SELECT * FROM  `".$_VARS['tbl_prefix']."_video`
		WHERE video_show = '1'
		ORDER BY video_create DESC";
				
$res = mysql_query($sql);

if($res && mysql_num_rows($res) > 0)
{
	while($row = mysql_fetch_array($res))
	{
		$th_url = '';
		if($row['video_img'] == 0)
		{
			if(trim($row['video_html']) != '')
			{
				// генерируем ссылку на превьюшку
				$url = getVideoUrl(htmlspecialchars_decode(trim($row['video_html'])));			
				$th_url = getVideoThumbUrl($url);
				$th_url = '<img src="'.$th_url.'" width=77 height=44 />';
			}
		}
		else
		{
			$img = new Image();
			$img -> imgCatalogId 	= $_VARS['env']['photo_alb_video_preview'];
			$img -> imgId 			= $row['video_img'];		
			$img -> imgClass 		= "viewGallery";
			
			$img -> imgWidthMax 	= 77;
			$img -> imgHeightMax 	= 44;	
			$img -> imgMakeGrayScale= true;
			
			if(isset($video_id) && $video_id == $row['id'])
				$img -> imgGrayScale 	= false;
			else
				$img -> imgGrayScale 	= true;
				
			$img -> imgTransform	= "crop";
			$th_url = $img -> showPic();			
		}				
		
		$arrItem[$row['video_create']][] = array('video', $row['id'], $th_url);
	}
}

//printArray($arrItem);

$arr = array();

foreach($arrItem as $k => $v)
{
	$date = explode('-', $k);
	
	/*
	создадим массив
	
	array(
		[год] =>
			[месяц] =>
				array(id, img)
		)
	
	*/
	
	$arr[$date[0]][intval($date[1])] = array($v[0][1], $v[0][2]);
}
	


//printArray($arr);
?>
<ul id="mycarousel" class="jcarousel-skin-tango">
<?
	foreach($arr as $k => $v)
	{
		?><li class="year"><?=$k?></li><?		
					
		
		//for($i = 1; $i < 13; $i++)
		for($i = 1; $i < 4; $i++)
		{
			if(!isset($v[$i]))
			{
				$v[$i] = array();
			}
		}
		
		$m = ksort($v);
		
		$n = 3;
		
		foreach($v as $data)
		{
		
			$cls = '';			
			
		
			if(empty($data))
			{
				?><!--<li class="tubeItem"></li>--><?
			}
			else
			{
				if(isset($_GET['param2']) && $_GET['param2'] == $data[0])
					$cls = 'active'; 
				?><li class="tubeItem <?=$cls?>"><a href="/<?=$_PAGE['p_url']?>/<?=$data[0]?>/"><?=$data[1]?></a></li><?
				
				$n--;
			}
		}
		
		if($n > 0)
		{
			for($m = 0; $m < $n; $m++)
			{
				?><li class="tubeItem"></li><?
			}
		}
		
	}
?>
<li class="year"></li>
</ul>
