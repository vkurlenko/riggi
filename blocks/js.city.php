<?
// javascript-код
// координаты городов на google карте


/*
locations = [		 
		 	['Москва', 	55.790473,  37.606201,  4, ['/img/pic/city_moscow.jpg', 'МОСКВА', 		32, 'link']],
		  	['Кострома', 	57.771325,	40.950937, 	5, ['/img/pic/city_kostroma.jpg', 'КОСТРОМА', 	10, 'link']]
		];


locCity = [[
		// МОСКВА
		  ['Москва1', 		55.766628,37.605,  		4, ['/img/pic/city_moscow.jpg', 'Москва1', 32, 'link']],
		  ['Москва2', 		55.757839,37.631693, 	5, ['/img/pic/city_kostroma.jpg', 'Москва2', 10, 'link']],
		  ['Замоскворечье', 55.741221,37.626543, 	5, ['/img/pic/city_kostroma.jpg', 'Замоскворечье', 10, 'link']],
		  ['Донской', 		55.719327,37.607403, 	5, ['/img/pic/city_kostroma.jpg', 'Донской', 10, 'link']]
		],
		
		// КОСТРОМА
		[
		  ['Пятницкая', 	57.771405,40.921755,  	4, ['/img/pic/city_moscow.jpg', 'Пятницкая', 32, 'link']],
		  ['Ленина', 		57.7968,40.951881, 		5, ['/img/pic/city_kostroma.jpg', 'Ленина', 10, 'link']]
		]
		];
		
*/



include_once DIR_FRAMEWORK.'/class.image.php';

include 'func.php';

$arrCity = array(); // 	массив данных городов
$arrCurCity = array(); // массив id городов 
$currentCity = -1; 



// прочитаем координаты маркеров городов
function getObjects($cityId)
{
	global $_VARS, $_PAGE;
	
	$arrObjects = array('map_pointer' => '');
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_yandex_map`
			WHERE map_item = ".$cityId."
			LIMIT 0,1";
			
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);		
		
		$arrObjects = array(
			'map_pointer' => trim($row['map_pointer'])
			);
	}

	return $arrObjects;			
}	


/* 
	занесем в массив инфу о всех магазинах,
	сгруппированным по городам
*/
function getShops($arrCity)
{
	global $_VARS;
	
	$s = '';
	$i = 1;
	$arrShop = array();
	
	if(!empty($arrCity))
	{
		foreach($arrCity as $k)
		{
			$s .= 'p_parent_id = '.$k['id'];
			if($i++ < count($arrCity))
				$s .= ' OR ';
		}
	
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pages`
				WHERE ".$s."
				ORDER BY p_parent_id ASC";
					
		$res = mysql_query($sql);
		
		if($res && mysql_num_rows($res) > 0)
		{
			
			while($row = mysql_fetch_array($res))
			{
			
				$img_src = getImgSrc($row['p_img']);				
				
				$arr = getObjects($row['id']);
				
				if($arr['map_pointer'] != '')
				{			
					$arrShop[$row['p_parent_id']][]  = array(
						'shopTitle' => $row['p_title'.LANG],
						'shopCoord' => $arr['map_pointer'],
						'shopImg'	=> '/'.$img_src,
						'shopUrl'   => '/'.$row['p_url'].'/'
						);
				}
			}	
		}
	}
	else
	{
		echo 'Нет массива городов.';
		
	}		
	
	return $arrShop;
}

/*
	сформируем JS массив магазинов в городе
*/
function getCityShop($arrShop, $cityId)
{
	global $arrCity;
	$str = '';
	
	
	if(isset($arrShop[$cityId]))
	{
		$a = $arrShop[$cityId];	
		$str = NL.'['.NL.'//'.$cityId.NL;
		$i = count($a);
		foreach($a as $k)
		{
			$str .= "['".$k['shopTitle']."', ".$k['shopCoord'].", ".$i--.", ['".$k['shopImg']."', '".$k['shopTitle']."', '', '".$k['shopUrl']."']]";
			if($i > 0)
				$str .= ','.NL;
		}
		$str .= '],';
		
	}
	
	return $str;
	
}










if(isset($_VARS['env']['currentID']))
{
	// id корневой страницы
	$currentId = $_VARS['env']['currentID'];
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pages`
			WHERE p_parent_id = $currentId
			ORDER BY p_order ASC";
			
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{		
		/*
			сформируем массив данных по городам
		*/
		while($row = mysql_fetch_array($res))
		{
			// картинка города
			$img_src = getImgSrc($row['p_img']);	
			
			// в массиве координаты маркера города
			$arr = getObjects($row['id']);		
			
			// если координаты есть, то будем показывать город на карте
			if($arr['map_pointer'] != '')
			{			
				$arrCity[] = array(
					'id'		=> $row['id'],
					'p_url' 	=> $row['p_url'],
					'p_title'	=> $row['p_title'.LANG],
					'coord' 	=> $arr['map_pointer'],
					'p_img'		=> '/'.$img_src
				);
			}
		}
	}
	else
		echo 'Нет страниц городов.';
	
}
else 
{
	echo 'Не определен id корневой страницы городов.';
}



if(!empty($arrCity))
{

	// инфа о всех магазинах по городам
	$arrShop = getShops($arrCity);	

	$strLocations = ''; // js массив locations (города)
	$strlocCity = '';// js массив locCity   (магазины в городах)
	
	$i = count($arrCity);
	$j = 0;
	
	foreach($arrCity as $k)
	{

		if(isset($_GET['param2']) && $_GET['param2'] == $k['id'])
		{
			$currentCity = $j;
		}

		if(isset($arrShop[$k['id']]))
		{
			// определим кол-во магазинов в этом городе
			$n = count($arrShop[$k['id']]);
			
			// очередной элемент массива магазинов в городе
			$strlocCity .= getCityShop($arrShop, $k['id']);
		}
		else
		{
			$n = 0;
			// если нет ни одной метки магазина в этом городе на карте,
			// то и сам город показывать не будем
			continue;
		}
		
		
		$strLocations .= "['".$k['p_title']."', ".$k['coord'].", ".$i--.", ['".$k['p_img']."', '".$k['p_title']."',	".$n.", '']]";
		
		if($i > 0) 
			$strLocations .= ", ".NL;				
			
		$j++;
	}
	
	echo 'locCity = ['.NL.$strLocations.NL.'];';	
	
	echo 'locShop = ['.NL.$strlocCity.NL.'];';	
}

//printArray($arrShop);
echo 'currentCity='.$currentCity;

?> 
