<?
/*~~~~~~~~~~~~~~*/
/* облако тегов */
/*~~~~~~~~~~~~~~*/

// объявляем массив, в который будут собираться теги
$arrTags = array();


// делаем выборку тегов из БД (страницы сайта, каталог)
//$sql = "SELECT ".$_VARS['tbl_pages_name'].".prim FROM `".$_VARS['tbl_pages_name']."` where ".$_VARS['tbl_pages_name'].".prim <> ''";

$sql = "SELECT ".$_VARS['tbl_pages_name'].".prim, ".$_VARS['tbl_prefix']."_catalog_items.item_tags 
		FROM `".$_VARS['tbl_pages_name']."`, `".$_VARS['tbl_prefix']."_catalog_items` where ".$_VARS['tbl_pages_name'].".prim <> '' 
		OR ".$_VARS['tbl_prefix']."_catalog_items.item_tags <> null";
		
		//echo $sql;
$res = mysql_query($sql);


// каждую строку тегов (разделенную запятой) рабираем на теги и формируем массив [тег] => [частота упоминания]
if(mysql_num_rows($res) > 0)
{
	while($row = mysql_fetch_array($res))
	{
		for($i = 0; $i < mysql_num_fields($res); $i++)
		{
			$arr = explode(",", $row[$i]);
			foreach($arr as $k)
			{
				if($k <> '')
				{
					$k = trim($k);
					if(isset($arrTags[$k]))
					{
						$arrTags[$k] = $arrTags[$k]+1;
					}
					else $arrTags[$k] =  1;
				}				
			}
		}				
	}
}

/*echo "<pre>";
print_r($arrTags);
echo "</pre>";*/

// диапазон изменения масштаба шрифта с шагом 10(%)
$arrRange = array(50, 180); 


// определяем max и min частоту упоминания тегов
$min = $max = $i = 0; 
foreach($arrTags as $k => $v)
{
	if($i == 0) $min = $max = $v;
	else
	{
		if($v > $max) $max = $v;
		if($v < $min) $min = $v;
	}	
	$i++;
}

// коэффициент масштабирования
$c = $arrRange[1] / $max;


// вывод тегов
foreach($arrTags as $k => $v)
{
	$h = ceil($v * $c);
	if($h < $arrRange[0]) $h = $arrRange[0];
	?>
	<a style="font-size: <?=$h;?>%;" href="/tagcloud/?tag=<?=$k;?>"><?=$k;?><!--(<?=$v;?>)--></a> 
	<?
}
?> 	