<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ скрипт вывода отформатированного изображения с сохранением на диск ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ установка параметров по умолчанию ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

//способ выравнивания тега <IMG>
if(!isset($pic_align))  
	$pic_align = "";
else 
	$pic_align = " align='".$pic_align."' ";	 

// атрибут ALT тега <IMG> 
if(!isset($pic_alt))  
	$pic_alt = "";			

// атрибут TITLE тега <IMG> 
if(!isset($pic_title))  
	$pic_title = "";		

// атрибут CLASS тега <IMG> 
if(!isset($img_class)) 
	$img_class = "";			

	// делать ли ч/б копию
if(!isset($pic_mono))  
	$pic_mono = false;		

// метод трансформации
if(!isset($pic_transform))  
	$pic_transform = "crop";		

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /установка параметров по умолчанию ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ считывание из БД инфы об изображении ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$sql_img = "SELECT * FROM `".SITE_PREFIX."_pic_".$img_alb_id."` 
			WHERE id = ".$img_id;

$res_img = mysql_query($sql_img);
$row_img = mysql_fetch_array($res_img);
$ext = $row_img['file_ext'];
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /считывание из БД инфы об изображении ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



// путь к исходному изображению
$path_or = $_VARS['photo_alb_dir']."/".SITE_PREFIX."_pic_".$img_alb_id."/".$img_id.".".$ext;

// путь к новому изображению
if($pic_mono == true) 
	$path = $_VARS['photo_alb_dir']."/".SITE_PREFIX."_pic_".$img_alb_id."/".$img_id."-".$pic_width."x".$pic_height."-mono.".$ext;
else 
	$path = $_VARS['photo_alb_dir']."/".SITE_PREFIX."_pic_".$img_alb_id."/".$img_id."-".$pic_width."x".$pic_height.".".$ext;

if(file_exists($_SERVER['DOC_ROOT']."/".$path))
{
	// если файл уже существует, то выводим его в браузер
	?>
	<img class="<?=$img_class?>" src="/<?=$path?>" width="<?=$pic_width?>" height="<?=$pic_height?>" <?=$pic_align?> title="<?=$pic_title?>" alt="<?=$pic_alt?>" />
	<?
}
else
{
	// иначе создаем файл
	?>
	<img class="<?=$img_class?>" src="/modules/img/image.php?file=<?=$path_or?>&type=<?=$pic_width."x".$pic_height?>&w=<?=$pic_width?>&h=<?=$pic_height?>&transform=<?=$pic_transform?>&pic_mono=<?=$pic_mono?>"  <?=$pic_align?> title="<?=$pic_title?>"  alt="<?=$pic_alt?>" />
	<?
}
?>