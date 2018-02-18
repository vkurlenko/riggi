<?
// \/(\\s?".$str.")\/i
//echo preg_replace('(сотр)', 'test', 'сотрудничать');


$strlen_min 	= 3;	// минимально допустимая длина строки запроса
$resNum			= 0;		// кол-во найденных совпадений
$str_limit 		= 400;	// максимальная длина текста описания
$arrItems 		= array();
$arrSql 		= array();


$lang = '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$lang = '_'.$_SESSION['lang'];
}

$arrL = array(
	
	''	=> array(
		'Строка запроса не может быть менее '.$strlen_min.' символов'
	),
	
	'_eng' => array(
		'eng Строка запроса не может быть менее '.$strlen_min.' символов'		
	)

);




$str_pages 		=  TEXT_LANG(9); 

$arrResMsg = array(
		"not_found"		=> "<p class='msg'><em>".TEXT_LANG(10)."</em></p>",
		"min_string" 	=> "<p class='msg'><em>".$arrL[$lang][0]."</em></p>"
);

$i = 0;
if(isset($_POST['fieldSearch']) && trim($_POST['fieldSearch']) != '')
{
	
// массив сообщений о результате
	$arrResMsg["ok"] = "<p class='searchFind'>".TEXT_LANG(12)." <span class='searchQuery'>'".trim($_POST['fieldSearch'])."'</span> <span class='searchPageNum'>resNum str_pages</span></p>";
		
	
	$search_string = trim($_POST['fieldSearch']);
	
	mb_internal_encoding("UTF-8");
	
	if(mb_strlen($search_string) > $strlen_min)
	{
		$arrSql['pages'] = "SELECT * FROM `".$_VARS['tbl_prefix']."_pages`
							WHERE MATCH (p_content, p_title, p_content".LANG.", p_title".LANG.")
							AGAINST ('".$search_string."')
							AND ".$_VARS['tbl_prefix']."_pages.p_show = '1'
							";	
		//echo 	$arrSql['pages'];				
		
		
		$arrSql['news'] = "SELECT * FROM `".$_VARS['tbl_prefix']."_news`
							WHERE MATCH (news_title".LANG.", news_text_1".LANG.", news_text_2".LANG.", news_text_3".LANG.")
							AGAINST ('".$search_string."')
							AND news_show = '1'";
							
		/*~~~~~~~~~~~~~~~~~~~*/
		/* поиск в картинках */
		/*~~~~~~~~~~~~~~~~~~~*/
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pic_catalogue`
				WHERE 1";
		$res_pic = mysql_query($sql);			
		
		if(mysql_num_rows($res_pic) > 0)
		{
			$i = 0;
			$arr = array();
			
			while($row_pic = mysql_fetch_array($res_pic))
			{
				$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pic_".$row_pic['alb_name']."`
						WHERE name LIKE '%".$search_string."%'";
				$res_i = mysql_query($sql);
				
				if($res_i && mysql_num_rows($res_i) > 0)
				{
					while($row_i = mysql_fetch_array($res_i))
					{
						$arr[] = array($row_pic['alb_name'], $row_i['id'], $row_i['file_ext'], $row_i['name']);
					}
				}					
			}		
		
		}
		/* /поиск в картинках */
		/*~~~~~~~~~~~~~~~~~~~~*/
		
		
		foreach($arrSql as $k => $v )		
		{
			$res = mysql_query($v);
		
			if($res && mysql_num_rows($res) > 0)
			{
				while($row = mysql_fetch_array($res))
				{
					switch($k)
					{
						case 'pages': 	$url = $row['p_url'];
										$header = '<a href="/'.$url.'/">'.$row['p_title'.LANG].'</a>';
										$text = strip_tags($row['p_content'.LANG]);
										break;
						case 'news'	: 	$url = 'news_item/'.$row['id'];
										$header = '<a href="/'.$url.'/">'.$row['news_title'.LANG].'</a>';
										$text = strip_tags($row['news_text_2'.LANG]);
										break;
						
					}						
					
					$arrItems[$i++] = array($url, $header, $text);
				}
			}	
			
		}
	}
	else 
		echo $arrResMsg["min_string"];
}	



if($i > 0)
{
	$resNum = $i;	
	
	/* --- */
	$lastChar = substr($resNum, strlen($resNum) - 1);	
	switch($lastChar)
	{
		case '1' : $str_pages .= TEXT_LANG(13); break;
		case '2' : $str_pages .= TEXT_LANG(14); break;
		case '3' : $str_pages .= TEXT_LANG(14); break;
		case '4' : $str_pages .= TEXT_LANG(14); break;
		default : break;
	}
	/* --- */
	
	$arrResMsg["ok"] = str_replace("resNum", $resNum, $arrResMsg["ok"]);
	$arrResMsg["ok"] = str_replace("str_pages", $str_pages, $arrResMsg["ok"]);
	echo $arrResMsg["ok"];	
}
else
{
	echo $arrResMsg["not_found"];
}

/******************************/
/* функция подсветки в тексте */
/******************************/
function highlight($search_string, $text)
{
	//global $strlen_min;
	$arrSearch = explode(' ', $search_string);
	$pattern = array();
	$replace = array();
	$text = str_replace('&nbsp;', ' ', $text);
	
	// массив шаблонов в нижнем регистре
	foreach($arrSearch as $w)
	{
		$w = trim($w);
		for($m = mb_strlen($w); $m >= 4; $m--)
		{
			$str = mb_substr($w, 0, $m);
			//echo $str;							
			//$pattern[] = "\/(\\s?".$str.")\/i";
			$pattern[] = "(".$str.")";
			$replace[] = " <strong>".$str."</strong>";		
			//$replace[] = '';
		}	
	}
	// массив шаблонов с первой буквой в верхнем регистре
	/*foreach($arrSearch as $w)
	{
		$w = trim($w);
		for($i = mb_strlen($w); $i >= 4; $i--)
		{
			$str = mb_substr($w, 0, $i);
			$str = mb_strtoupper(mb_substr($str, 0, 1)).mb_substr($str, 1);
			$pattern[] = "\/(\\s?".$str.")\/i";
			$replace[] = " <strong>".$str."</strong>";		
		}	
	}*/
	//printArray($pattern);
					
	$text = preg_replace($pattern, $replace, $text);
	return $text;
}
/*******************************/
/* /функция подсветки в тексте */
/*******************************/




$j = 0;
$hText = '';
foreach($arrItems as $k => $v)
{
	$j++;
	$text = '';
	$arrSearch = explode(' ', $search_string);
	$h = highlight($search_string, $v[1]);
	
	$v[2] = str_replace('&nbsp;', ' ', $v[2]);
	$search_str_start = false;
	
	?>
	<div class="searchItem">
		<p class="searchItemTitle"><?=($k+1).". ".$h?></p>
		<?
		
		// начальная позиция вхождения в тексте одного из слов запроса
		foreach($arrSearch as $s)
		{
			$search_str_start = mb_strpos(mb_strtolower($v[2]), mb_strtolower(mb_substr(trim($s), 0, 4)));	
			
			// если первое нашли вхождение какого-либо слова,
			// то стоп, это будет начальной позицией печати сниппета
			if($search_str_start !== false) 
			{
				break;
			}
		}

		
		// если начальная позиция найдена
		if($search_str_start !== false)
		{			
			/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
			/* выделение вхождения в тексте */
			/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/			
			
			// найдем начало предложения до вхождения слова,
			// за начало примем точку или начало текста			
			$text_left = mb_substr($v[2], 0, $search_str_start);
			$start = @mb_strrpos($text_left, '.');		

			// если нашли точку, то к начальной позиции (start) прибавим один знак
			if($start > 0) $start++; 		
			
			// в полученном тексте длиной 400 символов найдем последнюю точку или конец текста
			$end = @mb_strrpos(mb_substr($v[2], $start, $str_limit), '.');
			
			// полученный кусок текста и будет сниппетом
			if($end > 0)
			{
				$text = mb_substr($v[2], $start, $end + 1);
			}
			else $text = mb_substr($v[2], $start);
			
			// подсветим вхождения слов запроса
			$text = highlight($search_string, $text);
			
			/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
			/* /выделение вхождения в тексте */
			/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/			
		}
		// если начальная позиция не найдена, выведем весь текст до первой точки
		else 
		{
			$end = @mb_strrpos(mb_substr($v[2], 0, $str_limit), '.');
			$text = mb_substr($v[2], 0, $end + 1);
		}
		
		// собственно печать сниппета
		echo "<p class='searchItemText'>".$text."</p>";
		?>
	</div>
	<?
	
	/*if(count($arr) > 0)
	{
		if(count($arrItems) > 3)
		{
			if($j == 3)
			{
				?><div class="searchItem"><?
				include $_SERVER['DOCUMENT_ROOT']."/blocks/gallery.search.php";
				?></div><?
			}
		}
		else
		{
			if($j == count($arrItems))
			{
				?><div class="searchItem"><?
				include $_SERVER['DOCUMENT_ROOT']."/blocks/gallery.search.php";
				?></div><?
			}
		}
	}*/
	
	
	
	
	/*if($j == 3 || $j == count($arrItems))
	{
		?><div class="searchItem"><?
		include $_SERVER['DOCUMENT_ROOT']."/blocks/gallery.search.php";
		?></div><?
	}*/
}
?>
