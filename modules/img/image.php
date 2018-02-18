<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ преобразование картинки в заданный формат ~~~*/
/*~~~ с сохранением копии на диск               ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ функция создания ч/б изображения ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function grayscale($filename, $mono_filename)
{

	//echo $filename;
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
$f = $_SERVER['DOC_ROOT']."/".$_GET['file'];

$make_mono = $_GET['pic_mono'];

// качество jpeg по умолчанию 
if (!isset($q)) $q = 90;

// имя файла
$name = basename($_SERVER['DOC_ROOT']."/".$_GET['file']);	
$arr_name = explode(".", $name, 2);

// имя мини файла 
$name_mini = $arr_name[0]."-".$_GET['type'].".".$arr_name[1];	

// имя мини файла в монохроме
$name_mini_mono	= $arr_name[0]."-".$_GET['type']."-mono.".$arr_name[1];

// папка файла	
$folder	= dirname($_SERVER['DOC_ROOT']."/".$_GET['file']);

// определяем графический формат файла и его размеры
$file_type = getimagesize($f);
switch ($file_type[2])
{
	case 1 : $src = imagecreatefromgif($f); break;
	case 2 : $src = imagecreatefromjpeg($f); break;
	case 3 : $src = imagecreatefrompng($f); break;
	default : $src = imagecreatefromjpeg($f); break;
}

// размеры исходного изображения
$w_src = imagesx($src); 
$h_src = imagesy($src);
$ratio_src = $w_src / $h_src;

// размеры нового изображения
$w = $_GET['w'];  
$h = $_GET['h'];	
$ratio = $w / $h;

// жестко задаем размеры выходного изображения
$k = $w_src / $w;
$w_min = ceil($w_src / $k);
$h_min = ceil($h_src / $k);

//echo $w_src."x".$h_src."<br>";
//echo $w."x".$h."<br>";
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ трансформация картинки ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
switch($_GET['transform'])
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

// создаем ч/б копию
if($make_mono == 1)
{
	grayscale($folder."/".$name_mini, $folder."/".$name_mini_mono); 
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

?>