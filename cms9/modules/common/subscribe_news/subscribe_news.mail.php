<?
/*
	прочитаем новости и сформируем html письма
*/
$tableName 	= $_VARS['tbl_prefix']."_subscribe_news";
$domain 	= $_SERVER['HTTP_HOST'];

$news_url 	= "news_item"; // url страницы с новостью
$mail_admin = $_VARS['env']['mail_admin'];
$from 		= $_VARS['env']['mail_from'];


if(!isset($show_msg))
	$show_msg = true;

include_once "subscribe_news.func.php";

/*if(isset($_GET['param2'])) 
{	// если задан диапазон дат, то применяем фильтр по дате
	$filter = " AND news_date LIKE '%".$_GET['param2']."%'";
}*/

$html = ''; // html письма


// сформируем текст рассылки
function makeHtml($arrNews)
{
	global $news_url, $show_msg, $_VARS;
	
	$html = '';
	$marker = '{SUBSCRIBE}';
	
	if(!isset($_VARS['env']['subscribe_tpl']))
	{
		if($show_msg) echo msgError("Не определен путь к файлу-шаблону в переменной окружения 'subscribe_tpl'");
	}
	else
	{
		if(file_exists(DIR_ROOT.$_VARS['env']['subscribe_tpl']))
		{
			// прочитаем файл рассылки в массив
			$file = file_get_contents(DIR_ROOT.$_VARS['env']['subscribe_tpl']);
			
			
			ob_start();
			eval("?>$file<?");
			$output = ob_get_contents();
			ob_end_clean();
			
						
			if(!$file) 
			{
				if($show_msg) echo msgError("Ошибка открытия файла шаблона");
				
			}	
			else
			{
				foreach($arrNews as $k)
				{
					$news_id = $k['id'];
					//echo $k;
					$news_html = '';
					
					include DIR_ROOT.'/blocks/news.item.subscr.php';
					/*$html .= '<p><strong>'.$k['news_title'].'</strong>
								<br>'.$k['news_text_1'].'<br>
								<a href="http://'.$_SERVER['HTTP_HOST'].'/'.$news_url.'/'.$k['id'].'/">Далее</a></p>';*/
					$html .= $news_html;
				}	
								
				$html = str_replace($marker, $html, $output);	
				
				$html .= "<a style='color:#ffffff;' href='http://".$_SERVER['HTTP_HOST']."/'>Перейти на сайт ".$_SERVER['HTTP_HOST']."</a>";		
			}
		}
		else
			if($show_msg) echo msgError('Файл шаблона не найден');	
	}
	
	
	
	
	
	return $html;
}


// сделаем выборку новостей за текущую дату
$sql = "SELECT * FROM `".$_VARS['tbl_news']."`
		WHERE news_show = '1' 
		AND news_mark = '0'  
		AND news_date LIKE '%".date('Y-m-d')."%'
		ORDER BY news_date DESC";
		
$res_article = mysql_query($sql);

if($res_article && mysql_num_rows($res_article) > 0)
{
	$arrNews = array();
	
	while($row_article = mysql_fetch_array($res_article))
	{
		$arrNews[] = array(
			'id'			=> $row_article['id'],
			'news_title' 	=> $row_article['news_title'],
			'news_text_1' 	=> $row_article['news_text_1']			
		);		
	}	
	
	if(!empty($arrNews))
		$html = makeHtml($arrNews);	
	else
		if($show_msg) echo msgError("Нет свежих новостей для рассылки");
}
else 
{
	if($show_msg) echo msgError("Нет свежих новостей для рассылки");	
}

//printArray($arrNews);
// если сформировано тело письма,
// то сделаем рассылку
if($html != '')
{
	// проверяем в какое время закончилась последняя пачка рассылки
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_presets` 
			WHERE var_name = 'subscribe_news_count'";
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);
		$last_time = $row['var_value'];
	}
	else
	{
		if($show_msg) echo msgError('Неизвестно subscribe_news_count (нет переменной окружения)');
	}
	
	
	if(isset($last_time))
	{
		// если больше, чем $period назад, то запускаем следующую пачку
		if(time() > ($last_time + $period))
		{
			$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_subscribe_news` 
					WHERE subscribe_status = '0' 
					ORDER BY id DESC 
					LIMIT 0, ".$_VARS['env']['subscribe_news_count'];
			$res = mysql_query($sql);
			
			if($res && mysql_num_rows($res) > 0)
			{
				while($row = mysql_fetch_array($res))
				{			
					// отправляем письма адресатам и в случае успеха изменяем их статус рассылки
					if(multipart_mail_2($mail_admin, $row['subscribe_mail'], "Рассылка новостей сайта ".$_SERVER['HTTP_HOST'], $html, $cc=null))
					{
						$sql = "UPDATE `".$_VARS['tbl_prefix']."_subscribe_news` 
								SET subscribe_status = '1' 
								WHERE subscribe_mail = '".$row['subscribe_mail']."'";
						$res2 = mysql_query($sql);	
					}
					else
					{
						if($show_msg) echo msgError('Не удалось отправить рассылку адресату '.$row['subscribe_mail']);
					}
				}
				
				// обновим время рассылки текущей пачки писем
				$sql = "UPDATE `".$_VARS['tbl_prefix']."_presets` 
						SET var_default = ".time()." 
						WHERE var_name = 'subscribe_news_count'";
				$res = mysql_query($sql);
			}			
		}
	}	
}
else
	if($show_msg) echo msgError('Нет текста для формирования письма.');
?>



