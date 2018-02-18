<?
/******************/
/* класс НОВОСТИ  */
/******************/

/*
	Пример 1.	 
	Получить данные о новости по ее id в виде массива.
	
	$news_item = new MOD_NEWS;	
	$news_item -> item_id = $id; // id новости
	$news_item -> news_alb = $_VARS['env']['photo_alb_news']; // id альбома с картинками для новостей
	$news_item -> img_width = 1017;	// ширина картинки
	$news_item -> img_height = 643; // высота картинки
	
	$a = $news_item -> getNewsItem(); // получим в массив данные о новости
	$a = $a[$id];
	
	
	
	Пример 2.
	Получим в виде массива данные о всех новостях.
	
	$news = new MOD_NEWS;
	$news -> news_alb 	=  $_VARS['env']['photo_alb_news'];
	$news -> img_height = 75;
	$news -> img_width 	= 125;
	$news -> img_align 	= '';
	$arrNews = $news -> getNewsList();

*/

class MOD_NEWS
{
		
	var $news_cat = 'news';	// категория новостей
	var $news_alb;			// альбом картинок к новостям
	
	
	/* картинка превью */
	var $img_width 	= 100;	// ширина картинки превью
	var $img_height 	= 100;	// высота картинки превью
	var $img_align   = 'left';	// выравнивание картинки первью
	var $img_server  = false;
	
	var $item_id;			// id новостной статьи
	
	var $lang	= '';


	
	
	
	/************************************/
	/* 	получение списка всех новостей 	*/
	/*	в зависимости от категории		*/
	/************************************/
	function getNewsList()
	{	
		global $_VARS;
	
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_news`
				WHERE news_cat = '".$this->news_cat."'
				AND news_show = '1'
				ORDER BY news_date DESC";
		
		$res = mysql_query($sql);
		
		if($res && mysql_num_rows($res) > 0)
		{
			$arr = array();
			while($row = mysql_fetch_array($res))
			{
				$date = explode(' ', $row['news_date'], 2);
				$html = '';
				if($row['news_img'] > 0)
				{
					$img = new Image();
					$img -> imgCatalogId 	= $this -> news_alb;
					$img -> imgId 			= $row['news_img'];
					$img -> imgAlt 			= $row['news_title'.$this -> lang];
					$img -> imgAlign		= $this -> img_align;
					$img -> imgWidthMax 	= $this -> img_width;
					$img -> imgHeightMax 	= $this -> img_height;	
					$img -> imgMakeGrayScale= false;
					$img -> imgGrayScale 	= false;
					$img -> imgTransform	= "crop";
					$img -> imgServer	= $this -> img_server;
					$html = $img -> showPic();
					//echo $this -> img_server; 
					//printArray($img);
				}
				
				$arr[$row['id']] = array(
					'news_date' 	=> format_date_to_str($date[0], $sel_2 = " ", $year = true, $this -> lang),
					'news_title'	=> trim(strip_tags($row['news_title'.$this -> lang])),
					'news_text_1'	=> trim(strip_tags($row['news_text_1'.$this -> lang])),
					'news_text_2'	=> trim($row['news_text_2'.$this -> lang]),
					'news_img'		=> $html
				);
			}
		}
		
		return $arr;		
	}	
	
	
	/*********************************************************/
	/* получение в массив данных контретной новостной статьи */
	/*********************************************************/
	function getNewsItem()
	{	
		global $_VARS;
	
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_news`
				WHERE id = '".$this->item_id."'
				AND news_show = '1'
				LIMIT 1";
		//echo $sql;
		$res = mysql_query($sql);
		
		if($res && mysql_num_rows($res) > 0)
		{
			$arr = array();
			while($row = mysql_fetch_array($res))
			{
				$date = explode(' ', $row['news_date'], 2);
				$html = '';
				if($row['news_img'] > 0)
				{
					$img = new Image();
					$img -> imgCatalogId 	= $this -> news_alb;;
					$img -> imgId 			= $row['news_img'];
					$img -> imgAlt 			= $row['news_title'.$this -> lang];
					$img -> imgAlign		= $this -> img_align;
					$img -> imgWidthMax 	= $this -> img_width;
					$img -> imgHeightMax 	= $this -> img_height;
					$img -> imgMakeGrayScale= false;
					$img -> imgGrayScale 	= false;
					$img -> imgTransform	= "crop";
					$img -> imgServer	= $this -> img_server;
					$html = $img -> showPic();
				}
				
				$arr[$row['id']] = array(
					'news_date' 	=> format_date_to_str($date[0], $sel_2 = " ", $year = true, $this -> lang),
					'news_title'	=> trim(strip_tags($row['news_title'.$this -> lang])),
					'news_text_1'	=> trim(strip_tags($row['news_text_1'.$this -> lang])),
					'news_text_2'	=> trim($row['news_text_2'.$this -> lang]),
					'news_img'		=> $html
				);
			}
		}
		
		return $arr;		
	}	
}
?>