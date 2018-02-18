<?
/********************************/
/* получим список всех альбомов */
/********************************/
function albList()
{
	global $_VARS;
	
	$arrAlb = array();
	
	$sql = "SELECT * FROM `".$_VARS['tbl_photo_alb_name']."`
			WHERE 1
			ORDER BY id ASC";
			
	$res = mysql_query($sql);	
	
	if($res && mysql_num_rows($res) > 0)
	{
		while($row = mysql_fetch_array($res))
		{
			$arrAlb[$row['alb_name']] = $row['alb_title'];
		}
	}
	
	return $arrAlb;
}
/*********************************/
/* /получим список всех альбомов */
/*********************************/



/*******************/
/* удалим картинку */
/*******************/
function imgDel($id)
{
	global $tbl, $_VARS, $parent_folder, $zhanr, $p, $last_type, $in_page, $page;
	
	$sql = "DELETE FROM `$tbl` 
			WHERE `id` = '$id'";
			
	mysql_query($sql);	
	
	$dir = opendir($_SERVER['DOC_ROOT']."/".$parent_folder.$_VARS['photo_alb_sub_dir']."$zhanr");
	
	chdir($_SERVER['DOC_ROOT']."/".$parent_folder.$_VARS['photo_alb_sub_dir']."$zhanr");
	
	while($file = readdir($dir))
	{
		//$template = "[^".$id."-?\w*.(jpg|gif|png)$]";
		$template = "[^".$id."(-\w*(-mono)?)?.(jpg|gif|png)$]";
		$result = preg_match($template, $file); 
		if($result)	
			unlink($file);
	}
	
	if(mysql_num_rows(mysql_query("SELECT `id` FROM `$tbl`")) >= $in_page * $p)
	{
		header("Location: ?page=$page&zhanr=$zhanr&p=$p&last_type=$last_type");
	} 
	else 
	{ 
		$p = $p - 1; 
		header("Location: ?page=$page&zhanr=$zhanr&p=$p&last_type=$last_type"); 
	}
	exit;
}
/********************/
/* /удалим картинку */
/********************/


/*******************************/
/* определяем расширение файла */
/*******************************/
function ext($f)
{
	$file_info = getimagesize($f);
	switch ($file_info[2])
	{
		case 1 : $ext = "gif"; break;
		case 2 : $ext = "jpg"; break;
		case 3 : $ext = "png"; break;
		default : break;			
	}
	return $ext;
}
/********************************/
/* /определяем расширение файла */
/********************************/


/*********************************/
/* создаем папку для фотоальбома */
/*********************************/
function CreateFolder($zhanr)
{
	mkdir($_SERVER['DOC_ROOT']."/photo".$zhanr);
	chmod($_SERVER['DOC_ROOT']."/photo".$zhanr, 0777);
}
/*********************************/
/* /создаем папку для фотоальбома */
/*********************************/


/***************************/
/* сохраняем с расширением */
/***************************/
function saveImage($f, $folder, $name)
{

	$file_info = getimagesize($f);
	
	switch ($file_info[2])
	{
		case 1 	: $src = imagecreateFROMgif($f); break;		
		case 3 	: $src = imagecreateFROMpng($f); break;
		default : $src = imagecreateFROMjpeg($f); break;
	}
	
	$x = $file_info[0];
	$y = $file_info[1];
	
	$im = imagecreatetruecolor($x, $y);
	
	// сохраняем прозрачность для png-24
	imageAlphaBlending($im, false);
	imagesavealpha($im, true);	
	
	imagecopyresampled($im, $src, 0, 0, 0, 0, $x, $y, $x, $y);
	
	$p = $_SERVER['DOC_ROOT']."/".$folder."/".$name;
	
	switch ($file_info[2])
	{
		case 1 : 
				$f = imagegif($im, $p.".gif"); 
				break;
		case 3 : 
				$f = imagepng($im, $p.".png"); 
				break;
		default: 
				$f = imagejpeg($im, $p.".jpg", 100); 
				break;
	}
	
	return $f;
	
}
/****************************/
/* /сохраняем с расширением */
/****************************/


/****************************************/
/* изменение порядка следования записей	*/
/****************************************/
function MoveItem($id, $direction)
{
	global $tbl;
	
	$table_name = $tbl;
	
	$order_field = "order_by";
	
	if($direction == "asc") 
		$arrow = ">";
	else
		$arrow = "<";
	
	$sql = "SELECT * FROM `".$table_name."` 
			WHERE id=".$id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "SELECT * FROM `".$table_name."` 
			WHERE (".$order_field." ".$arrow." ".$row[$order_field].") 
			ORDER BY ".$order_field." ".$direction." 
			LIMIT 1 ";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "UPDATE `".$table_name."` 
			SET ".$order_field."=".$row_2[$order_field]." 
			WHERE id=".$id;
	$res = mysql_query($sql);
	
	$sql = "UPDATE `".$table_name."` 
			SET ".$order_field."=".$row[$order_field]." 
			WHERE id=".$row_2['id'];
	$res = mysql_query($sql);
}
/*****************************************/
/* /изменение порядка следования записей */
/*****************************************/
?>