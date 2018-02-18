<?php 
error_reporting(E_ALL);
include $_SERVER['DOC_ROOT']."/config.php";
// f - имя файла 
// type - способ масштабирования 
// q - качество сжатия 
// src - исходное изображение 
// dest - результирующее изображение 
// w - ширина изображения 
// ratio - коэффициент пропорциональности 
// str - текстовая строка 

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ функция создания ч/б изображения ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function grayscale($filename, $mono_filename)
{

	echo $filename;
	//Получаем размеры изображения
	$img_size 	= GetImageSize($filename);
	$width 		= $img_size[0];
	$height 	= $img_size[1];
	
	
	//Создаем новое изображение с такмими же размерами
	$img = imageCreate($width,$height);
	
	
	//Задаем новому изображению палитру "оттенки серого" (grayscale)
	for($c = 0; $c < 256; $c++) 
	{
		ImageColorAllocate($img, $c,$c,$c);
	}
	
	//Содаем изображение из файла Jpeg
	$img2 = ImageCreateFromJpeg($filename);
	
	//Объединяем два изображения
	ImageCopyMerge($img,$img2,0,0,0,0, $width, $height, 100);
	
	//Сохраняем полученное изображение
	imagejpeg($img, $mono_filename);
	
	//Освобождаем память, занятую изображением
	imagedestroy($img);
}




// имя файла с путем
$f = $_SERVER['DOC_ROOT']."/".$_GET['f'];
//echo $f;
// делать ли ч/б копию?

if(!isset($_GET['pic_mono'])) $_GET['pic_mono'] = false;
$make_mono = $_GET['pic_mono']; 

// качество jpeg по умолчанию 
if (!isset($q)) $q = 100;

// определяем тип преобразования файла
$type = $_GET['type'];	
//echo $type;	

// имя файла
$name = basename($_SERVER['DOC_ROOT']."/".$_GET['f']);	
$arr_name = explode(".", $name, 2);

// имя мини файла 
$name_mini = $arr_name[0]."-mini".".".$arr_name[1];			

// имя мини файла в монохроме
$name_mini_mono	= $arr_name[0]."-mini-mono".".".$arr_name[1];

// папка файла	
$folder	= dirname($_SERVER['DOC_ROOT']."/".$_GET['f']);

// массив форматов преобразования файла
$arrType = $_VARS['cms_tumb_types'];

// определяем графический формат файла и его размеры
$file_type = getimagesize($f);
switch ($file_type[2])
{
	case 1 : $src = imagecreatefromgif($f); break;
	case 2 : $src = imagecreatefromjpeg($f); break;
	case 3 : $src = imagecreatefrompng($f); break;
	default : $src = imagecreatefromjpeg($f); break;
}
$w_src = imagesx($src); 
$h_src = imagesy($src);
$ratio_src = $w_src/$h_src;

//echo $src;
foreach($arrType as $k => $v)
{
	
	if($k == $type and $v[0] != '' and $v[1] != '')
	{
		// с изменением размера
		$w = $v[0];  
		$h = $v[1];	
		$ratio = $w/$h;
	}
	elseif($k == $type and $v[0] == '0' and $v[1] == '0')
	{
		//echo $v[0]."x".$v[1];
		// без изменения размера	
		$w = $w_src; 		
	}
	else
	{
		
	}
}


//echo "$w_src != $w";
if ($w_src != $w) 
{// операции для получения прямоугольного файла с заданной шириной 
//echo $type;
   if ($type == 0) 
   { 
	   // вычисление пропорций 	   
	   $ratio = $w_src/$w; 
	   $w_dest = round($w_src/$ratio); 
	   $h_dest = round($h_src/$ratio); 

	   // создаём пустую картинку 
	   // важно именно truecolor!, иначе будем иметь 8-битный результат 
	   $dest = imagecreatetruecolor($w_dest,$h_dest); 
	   imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src); 
	} 	
	//echo $dest;
	
	// операции для получения файла с заданными размерами (шириной и высотой)
	else
	{ 
		// жестко задаем размеры выходного изображения
		$k = $w_src / $w;
		$w_min = ceil($w_src / $k);
		$h_min = ceil($h_src / $k);
		
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
		imagecopyresampled($dest, $src, 0, 0, $x_copy, $y_copy, $w, $h, $w_copy, $h_copy); 
	 } 	
} 
else
{
		// просто копируем один в один
		//echo "$w_src != $w";
		$dest = imagecreatetruecolor($w_src, $h_src); 
		//echo $dest;
		imageAlphaBlending($dest, false);

		imagesavealpha($dest, true);
	    imagecopyresampled($dest, $src, 0, 0, 0, 0, $w_src, $h_src, $w_src, $h_src); 
}


// отправляем заголовок
// и выводим обработанную картинку в браузер
switch ($file_type[2])
{
	case 1 :	header("Content-type: image/gif"); 	
				imagegif($dest); 				
				imagegif($dest, $folder."/".$name_mini);
				break;
				
	case 2 :	header("Content-type: image/jpeg"); 	
				imagejpeg($dest,'',$q); 				
				imagejpeg($dest, $folder."/".$name_mini, $q);
				break;
				
	case 3 :	header("Content-type: image/png"); 	
				imagepng($dest); 	
				imagepng($dest, $folder."/".$name_mini);
				break;
				
	default :	header("Content-type: image/jpeg"); 	
				imagejpeg($dest,'',$q); 				
				imagejpeg($dest, $folder."/".$name_mini, $q);
				break;
}


// создаем ч/б копию
if($make_mono == 1)
{
	grayscale($folder."/".$name_mini, $folder."/".$name_mini_mono); 
}

// очистка памяти 
imagedestroy($dest); 
imagedestroy($src); 
?>