<?
include_once DIR_FRAMEWORK.'/class.image.php';
include 'func.php';

$lang = '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$lang = '_'.$_SESSION['lang'];
}

// массив url страниц-городов
$arrUrl = array(
	'moscow',
	'kostroma'	
);


// массив размеров картинок
// в соответствующих позициях
$arrSize = array(
	/* 1 - 4 */
	array(373, 246),
	array(217, 144),
	array(342, 230),
	array(424, 318),
	
	/* 5 - 8 */
	array(252, 183),	
	array(212, 154),
	array(260, 195),
	array(370, 278),
	
	/* 9 - 10 */
	array(295, 160),
	array(360, 295)
	
	
);


if(isset($_VARS['env']['currentID']))
{
	$currentId = $_VARS['env']['currentID'];
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pages`
			WHERE p_parent_id = $currentId
			AND p_show = '1'
			ORDER BY p_order ASC
			";
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{		
		/*
			сформируем массив данных по городам
		*/
		while($row = mysql_fetch_array($res))
		{
			$img_src = getImgSrc($row['p_img']);			
			
			$arrCity[] = array(
				'id'		=> $row['id'],
				'p_url' 	=> $row['p_url'],
				'p_title'	=> $row['p_title'.$lang],
				'coord' 	=> $row['p_tags'],
				'p_img'		=> '/'.$img_src
			);
		}
	}
	else
		echo 'Нет страниц городов.';
}
else 
{
	echo 'Не определен id корневой страницы городов.';
}



?>

<div class="cityGallery horizontal-only">
			
	<div class="cityGalleryInner">
		
		<div class="cityCenter">	
			
			<?
			if(!empty($arrCity))
			{
				$i = 0; $j = 0;
				foreach($arrCity as $k)
				{
					/*if($j > 29)
						continue;*/
				
					?><div class="cityPlash cityPlash<?=$j+1?> imgCont"><a href="/shops_map/<?=$k['id']?>/"><img src="<?=$k['p_img']?>" width="<?=$arrSize[$i][0]?>" height="<?=$arrSize[$i][1]?>" /><em><!--<strong><?=$j+1?></strong>--><?=$k['p_title']?></em></a></div><?
					echo NL;
					$i++;
					$j++;
					if($i > 9)
						$i = 0;
						
					
				}
				
			}
			?>
			
		</div>
		
	</div>
	
	
</div>