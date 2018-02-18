<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ вставка баннерных групп в шаблон ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



// выбраем все активные баннерные группы
$sql = "select * from `".$_VARS['tbl_prefix']."_banners` where banner_group_show = '1'";
$res = mysql_query($sql);

// если есть активные баннерные группы
if(mysql_num_rows($res) > 0)
{
	
	$arrReplace = array();	// массив замен маркер => код
	
	// перебираем все баннерные группы
	while($row = mysql_fetch_array($res))
	{	
		
		// если для группы выбран альбом 
		if($row['banner_group_alb'] != 0)
		{	
		
			if($row['banner_group_tpl'] != '0')
			{
				/*~~~ читаем код шаблона баннера ~~~*/
				$sql = "select * from `".$_VARS['tbl_template_name']."` where tpl_marker = '".$row['banner_group_tpl']."'";
				$res4 = mysql_query($sql);
				$row4 = mysql_fetch_array($res4);
				
				$dir = chdir($_SERVER['DOC_ROOT']."/".$_VARS['tpl_dir']);
				$code = file($row4['tpl_file']);
				$tpl_code = "";
				
				foreach($code as $str)
				{
					$tpl_code .= $str; 
				}
			}
			// если шаблон не указан, то шаблон по умолчанию
			else $tpl_code = "<img src=':::banner_src:::' />";
			$code = $tpl_code;	
			/*~~~ /читаем код шаблона баннера ~~~*/
			
			
			
			// перебираем все маркеры
			foreach($_VARS['banners_place'] as $k => $v)
			{			
				$banner_block = "";		
				
				if($k == $row['banner_group_place'] and (!isset($arrReplace[$k]) or @$arrReplace[$k] == "")/* && $row['banner_group_show'] == '1'*/)
				{		
					
					$banner_block = "";
					$sql = "select * from `".$_VARS['tbl_prefix']."_pic_".$row['banner_group_alb']."` where 1 order by order_by asc";
					$res5 = mysql_query($sql);
									
					if(mysql_num_rows($res5) > 0)
					{				
						/* формируем код вставки баннерной группы */
						while($row5 = mysql_fetch_array($res5))
						{
						
						
							/*// пропускаем баннеры, запрещенные для этой страницы
							if(trim($enorm['p_add_text_1']) != "")
							{
								$arrBanners = unserialize($enorm['p_add_text_1']);
								
								if(isset($arrBanners[$row5['id']]) && $arrBanners[$row5['id']] == "off")								
								{
									continue;
								}
								
							}
							// /пропускаем баннеры, запрещенные для этой страницы*/
						
						
							$banner_code = $code;
							$banner_link = $row5['url'];
							$banner_src	= "/".$_VARS['photo_alb_dir']."/".$_VARS['photo_alb_sub_dir'].$row['banner_group_alb']."/".$row5['id'].".".$row5['file_ext'];
							
							$banner_code = str_replace(":::banner_link:::", $banner_link, $code);
							$banner_code = str_replace(":::banner_src:::", $banner_src, $banner_code);
							$banner_block .= $banner_code;
						}
						/* /формируем код вставки баннерной группы */
					}
					
					$arrReplace[$row['banner_group_place']] = $banner_block;
				}
				else 
				{ 
					if(!isset($arrReplace[$k])) $arrReplace[$k] = "";
				}
			}
		}	
	}
	
	// производим замену маркеров на код или на "" если соответствия не нашлось
	foreach($arrReplace as $k => $v)
	{
		$output = str_replace(":::".$k.":::", $v, $output);	
	}
}

// если активных баннерных групп нет, то просто удаляем маркеры из шаблона
else
{	
	foreach($_VARS['banners_place'] as $k => $v)
	{
		$output = str_replace(":::".$k.":::", "", $output);	
	}
}
?>