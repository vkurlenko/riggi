<?
/*~~~~~~~~~~~~~~~~~~~~~*/
/*  класс изображений  */
/*~~~~~~~~~~~~~~~~~~~~~*/
	
class Image
{
	var $imgCatalogId;			// id альбома с изображением в БД;
	var $imgId;	 				// id изображения в БД;
	
	var $imgPathAbs;				// абсолютный путь к изображению;
	var $imgPath;				// относительный путь к изображению;
	
	var $imgTransform 	= "crop";	// метод преобразования (crop - обрезание до заданных размеров, 
										// resize - ресайз до заданных размеров с полями)
										
	var $imgSize;				// задаваемые размеры изображения array(maxWidth, maxHeight)
	
	// атрибуты тега IMG
	var $imgAttrId		= "";	// атрибут id
	var $imgAlt 			= "";	// атрибут alt
	var $imgAlign 		= "";	// атрибут align
	var $imgTitle 		= "";	// атрибут title
	var $imgClass 		= "";	// атрибут class
	var $imgRel 			= "";	// атрибут rel
	var $imgW			= '';	// атрибут width
	var $imgH			= ''; 	// атрибут height
	var $imgLongdesc 	= "";	// атрибут longdesc
		
	
	var $imgMakeGrayScale= false;	// делать ли черно-белую копию (false|true)
	var $imgGrayScale 	= false;//показать черно-белую копию (false|true)
	var $imgWidthMax 	= 100;	// (максимальная ширина нового изображения | DEFAULT)
	var $imgHeightMax 	= 100;	// (максимальная высота нового изображения | DEFAULT)
	var $imgJpegQuality 	= 90;	// качество Jpeg
	var $imgWaterMark 	= "";	// картинка водяной знак
	var $imgServer		= false;
	
	
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* определим абсолютный путь к изображению */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	function absPath()
	{
		global $_VARS;
		
		/*~~~ считывание из БД инфы об изображении ~~*/
		$sql_img = "SELECT * FROM `".$_VARS['tbl_photo_name'].$this -> imgCatalogId."` 
					WHERE id = ".$this -> imgId;
		//echo $sql_img;
		$res_img = mysql_query($sql_img);
		$row_img = mysql_fetch_array($res_img);
		$ext = $row_img['file_ext'];
		
		$absPath = $_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']."/".$_VARS['photo_alb_sub_dir'].$this -> imgCatalogId."/".$this -> imgId.".".$ext;
				
		return $absPath;
	}	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* определим относительный путь к изображению */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	function relPath()
	{
		global $_VARS;
		
		/*~~~ считывание из БД инфы об изображении ~~*/
		$sql_img = "SELECT * FROM `".$_VARS['tbl_photo_name'].$this -> imgCatalogId."` 
					WHERE id = ".$this -> imgId;
		//echo $sql_img;
		$res_img = mysql_query($sql_img);
		$row_img = mysql_fetch_array($res_img);
		$ext = $row_img['file_ext'];
		
		$relPath = "/".$_VARS['photo_alb_dir']."/".$_VARS['photo_alb_sub_dir'].$this -> imgCatalogId."/".$this -> imgId.".".$ext;
				
		return $relPath;
	}	
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* определим размеры изображения к изображению */
	/* строка вида width="xxx" height="xxx"		   */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	function picSize()
	{
		$file = $this->absPath();
		if(file_exists($file))
		{
			$fileInfo = getimagesize($file);
			return $fileInfo[3];
		}
	}
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* вывод в браузер картинки с заданными размерами */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	function showPic()
	{
		global $_VARS;
		
		// найдем в БД запись о заданной картинке
		$sql_img = "SELECT * FROM `".$_VARS['tbl_photo_name'].$this -> imgCatalogId."` 
					WHERE id = ".$this -> imgId;
		//echo $sql_img;
		$res_img = mysql_query($sql_img);
		
		if($res_img && mysql_num_rows($res_img ) > 0)
		{
			$row_img = mysql_fetch_array($res_img);
			$ext = $row_img['file_ext'];
			
			
			// путь к новому изображению
			$imgNewName = $this -> imgId."-".$this -> imgWidthMax."x".$this -> imgHeightMax.".".$ext;
			$imgNewNameMono = $this -> imgId."-".$this -> imgWidthMax."x".$this -> imgHeightMax."-mono.".$ext;
						
			
			// путь к файлу от корня сервера
			$path = $_VARS['photo_alb_dir']."/".$_VARS['photo_alb_sub_dir'].$this -> imgCatalogId."/".$imgNewName;
			$pathMono = $_VARS['photo_alb_dir']."/".$_VARS['photo_alb_sub_dir'].$this -> imgCatalogId."/".$imgNewNameMono;
			$this -> imgPath = $path;
			
			// если файла не существует создадим его
			if(!file_exists($_SERVER['DOC_ROOT']."/".$path))
			{
				$this -> imageCreateNew();
			}
			
			if($this->imgGrayScale) 
				$path = $pathMono;
			
			$fileInfo = getimagesize($_SERVER['DOC_ROOT']."/".$path);	
			$this -> imgW = $fileInfo[0];
			$this -> imgH = $fileInfo[1];
			
			
			// если атрибут alt указан как DEFAULT, то берем в качестве alt название картинки
			if($this->imgAlt == "DEFAULT") $this->imgAlt = $row_img['name'];
			if($this->imgTitle == "DEFAULT") $this->imgTitle = $row_img['name'];
			
			$path = '/'.$path;
			if($this->imgServer == true) 
				$path = 'http://'.$_SERVER['HTTP_HOST'].$path;
				
			//echo 'this->imgServer'.$this->imgServer;
			
			$html = "<img id='$this->imgAttrId' src=\"$path\" class='$this->imgClass' rel='$this->imgRel' longdesc='$this->imgLongdesc' $fileInfo[3] title='$this->imgTitle' alt='$this->imgAlt' align='$this->imgAlign' />";
			
		}
		else $html = "Изображение не найдено.";
		
		return $html;		
					
	}
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* создание черно-белой копии картинки */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	function grayscale($filename, $mono_filename)
	{
	
		//echo $filename;
		//Получаем размеры изображения
		$img_size 	= getimagesize($filename);
		//echo $img_size;
		$width 		= $img_size[0];
		$height 	= $img_size[1];
		
		
		//Создаем новое изображение с такмими же размерами
		$img_mono = imagecreate($width,$height);
		
		
		//Задаем новому изображению палитру "оттенки серого" (grayscale)
		for($c = 0; $c < 256; $c++) 
		{
			imagecolorallocate($img_mono, $c,$c,$c);
		}
		
		//Содаем изображение из файла Jpeg
		switch ($img_size[2])
		{
			case 1	: $img2 = imagecreatefromgif	($filename); break;
			case 2	: $img2 = imagecreatefromjpeg($filename); break;
			case 3 	: $img2 = imagecreatefrompng	($filename); break;
			default : $img2 = imagecreatefromjpeg($filename); break;
		}
		//$img2 = imagecreatefromjpeg($filename);
		
		//Объединяем два изображения
		imagecopymerge($img_mono,$img2,0,0,0,0, $width, $height, 100);
		
		//Сохраняем полученное изображение
		imagejpeg($img_mono, $mono_filename);
		
		//Освобождаем память, занятую изображением
		imagedestroy($img_mono);
	}	
	
	

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* преобразование картинки до заданных размеров с сохранением на диск */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	function imageCreateNew()
	{
		// имя файла
		$name = basename($this->absPath());	
		$arr_name = explode(".", $name, 2);
		
		// имя мини-файла 
		$name_mini = $arr_name[0]."-".$this -> imgWidthMax."x".$this -> imgHeightMax.".".$arr_name[1];	
		
		// имя мини-файла в монохроме
		$name_mini_mono	= $arr_name[0]."-".$this -> imgWidthMax."x".$this -> imgHeightMax."-mono.".$arr_name[1];
		
		// папка файла	
		$folder	= dirname($this->absPath());
		
		// определяем графический формат файла и его размеры
		$file_type = getimagesize($this->absPath());
		switch ($file_type[2])
		{
			case 1	: $src = imagecreatefromgif	($this->absPath()); break;
			case 2	: $src = imagecreatefromjpeg($this->absPath()); break;
			case 3 	: $src = imagecreatefrompng	($this->absPath()); break;
			default : $src = imagecreatefromjpeg($this->absPath()); break;
		}
		
		// размеры исходного изображения
		$w_src = imagesx($src); 
		$h_src = imagesy($src);
		$ratio_src = $w_src / $h_src;
		
		// размеры нового изображения
		if($this -> imgWidthMax ==  'DEFAULT')
			$w = $file_type[0];
		else
			$w = $this -> imgWidthMax;  
			
		if($this -> imgHeightMax ==  'DEFAULT')
			$h = $file_type[1];
		else
			$h = $this -> imgHeightMax;	
			
		$ratio = $w / $h;
		
		// жестко задаем размеры выходного изображения
		$k = $w_src / $w;
		$w_min = ceil($w_src / $k);
		$h_min = ceil($h_src / $k);
		
		/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
		/*~~~ трансформация картинки ~~~*/
		/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
		switch($this -> imgTransform)
		{
			case "resize" 	: 
				$w_min = $w_src;
				$h_min = $h_src;
					
				if($w_min > $w || $h_min > $h)
				{
					if($w_min > $w)
					{
						$w_min = $w;
						$h_min = ceil($h_min / $k);
					}
					//echo $w_min."x".$h_min."<br>";
					if($h_min > $h)
					{
						$n = $h_min / $h;
						$h_min = $h;
						
						$w_min = ceil($w_min / $n);
					}
					//echo $w_min."x".$h_min."<br>";
				}
				else
				{}
				
				
				// создаём пустую картинку 
				$dest = imagecreatetruecolor($w_min, $h_min);	
				imageAlphaBlending($dest, false);
				
				imagesavealpha($dest, true);
				//echo "$dest, $src, 0, 0, $x_copy, $y_copy, $w, $h, $w_copy, $h_copy";
				//resource dst_im, resource src_im, int dstX, int dstY, int srcX, int srcY, int dstW, int dstH, int srcW, int srcH
				imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_min, $h_min, $w_src, $h_src); 
				
				break;
			
			
			
			
			
			
			
			case "crop"		: 
				if($w_min < $w or $h_min > $h)
				{
					$crop = "h";
				}
				elseif($h_min < $h)
				{
					$crop = "w";
				}
				else
				{
					$crop = "none";
				}
				
				switch($crop)
				{
					case "h" :	$w_copy = $w_src;
								$x_copy = 0;
								$h_copy = $w_src / $ratio;
								$y_copy = ($h_src / 2) - ($h_copy / 2);
								break;
								
					case "w" :	$h_copy = $h_src;
								$y_copy = 0;
								$w_copy = $h_src * $ratio;
								$x_copy = ($w_src / 2) - ($w_copy / 2);
								break;
				
					default : 	$w_copy = $w_src;
								$x_copy = 0;
								$h_copy = $w_src / $ratio;
								$y_copy = ($h_src / 2) - ($h_copy / 2);
								break;
				}
				// создаём пустую картинку 
				$dest = imagecreatetruecolor($w, $h);	
				imageAlphaBlending($dest, false);
				
				imagesavealpha($dest, true);
				//echo "$dest, $src, 0, 0, $x_copy, $y_copy, $w, $h, $w_copy, $h_copy";
				imagecopyresampled($dest, $src, 0, 0, $x_copy, $y_copy, $w, $h, $w_copy, $h_copy);			
				break;
				
				default: break;
		}
		/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
		/*~~~ /трансформация картинки ~~*/
		/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
		
		
		if($this -> imgWaterMark != "")
		{
			$dest = $this -> addWaterMark($dest, $file_type);	
			exit;		
		}
		/*else 
			echo 'no';*/
		
		// сохраняем обработанную картинку на диск
		switch ($file_type[2])
		{
			case 1 :	imagegif($dest, $folder."/".$name_mini);
						break;
						
			case 2 :	imagejpeg($dest, $folder."/".$name_mini, $this -> imgJpegQuality);
						break;
						
			case 3 :	imagepng($dest, $folder."/".$name_mini);
						break;
						
			default :	imagejpeg($dest, $folder."/".$name_mini, $this -> imgJpegQuality);
						break;
		}
		
		// создаем ч/б копию
		if($this -> imgMakeGrayScale)
		{
			$this -> grayscale($folder."/".$name_mini, $folder."/".$name_mini_mono); 
		}	
	}	
	
	
	
	
	
	function addWaterMark($dest, $dest_file_type)
	{
	
		$wmFile = $_SERVER['DOCUMENT_ROOT'].$this -> imgWaterMark;
		//$dest = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'].'/img/pic/banner_small_1.jpg');
		/*imageAlphaBlending($dest, true);
			imagesavealpha($dest, true);	*/
				
		if(file_exists($wmFile))
		{
			$wm = imagecreatefrompng($wmFile);
			/*imageAlphaBlending($wm, true);
			imagesavealpha($wm, true);	*/	
		
		}
		else 
			echo 'File not found';
		
		// imagecreatetruecolor - создаёт новое изображение true color
		$image = imagecreatetruecolor($dest_file_type[0], $dest_file_type[1]);
		/*imageAlphaBlending($image, false);
		imagesavealpha($image, true);	*/	
				
		
		/*imagesettile($image, $wm);
		imagefilledrectangle($image, 0, 0, $dest_file_type[0], $dest_file_type[1], IMG_COLOR_TILED);*/
		//imagecopyresampled ($image, $wm, 0, 0, 0, 0, 200,200,200,200);
		imagecopyresampled($image, $dest, 0, 0, 0, 0, $dest_file_type[0], $dest_file_type[1], $dest_file_type[0], $dest_file_type[1]);
		imagecopyresampled($image, $wm, 0, 0, 0, 0, $dest_file_type[0], $dest_file_type[1], $dest_file_type[0], $dest_file_type[1]);
		//imagecopy($dest, $wm, 0, 0, 0, 0, 200, 200); 
		

		imagepng($image, $_SERVER['DOCUMENT_ROOT'].'/1.png');
		
		// узнаем размер изображения
		//$size=getimagesize($img);
		
		// указываем координаты, где будет располагаться водный знак
		/*
		* $size[0] - ширина изображения
		* $size[1] - высота изображения
		* В нашем примере изображение размером 448x336
		* Координаты соответственно будут
		* $x=448-88-10=277
		* $y=336-31-10=265
		* - 10 -это расстояние от границы исходного изображения
		*/
		/*$cx=$size[0];
		$cy=$size[1];*/
		
		/*** imagecopyresampled - копирует и изменяет размеры части изображения
		* с пересэмплированием
		*/
		//imagecopyresampled ($image, $wm, $cx, $cy, 0, 0, $wmW, $wmH, $wmW, $wmH);
		
		/*
		* imagejpeg - создаёт JPEG-файл filename из изображения image
		* третий параметр - качество нового изображение 
		* параметр является необязательным и имеет диапазон значений 
		* от 0 (наихудшее качество, наименьший файл)
		* до 100 (наилучшее качество, наибольший файл)
		* По умолчанию используется значение по умолчанию IJG quality (около 75)
		*/
		
		/*imagejpeg($image, $img, 90);
		
		// imagedestroy - освобождает память
		imagedestroy($image);
		
		imagedestroy($wm);
		
		// на всякий случай
		unset($image,$img);*/
		
		//return $dest;
	}
}
?>