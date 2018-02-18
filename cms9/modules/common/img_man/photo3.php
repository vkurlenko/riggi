<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ CMS МЕНЕДЖЕР КАРТИНОК ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
session_start();

error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/cms9/modules/framework/class.image.php";
include 'function.php';

check_access(array('admin', 'editor'));

$arrExt = array('',	'gif', 'jpeg', 'png');

$last_type = 0;	

if(isset($name)) 
	$name = str_replace("'","#039;",$name);
	
if(isset($pub)) 
	$pub = str_replace("'","#039;",$pub);	
	
if(!isset($zhanr)) 
	$zhanr = '';

$in_page = $_VARS['cms_pic_in_page'];

$parent_folder	= '/';
if(isset($_VARS['photo_alb_dir']))
	$parent_folder = $_VARS['photo_alb_dir'].'/';	
	
$pathToFolder = $_SERVER['DOC_ROOT']."/".$parent_folder.$_VARS['photo_alb_sub_dir'];

$tbl = $_VARS['tbl_photo_name'].$zhanr;

$arrAlb = albList();


/*~~~ удаление картинки ~~~*/
if(isset($del)) 
	imgDel($id);
/*~~~ /удаление картинки ~~~*/

/*~~~ изменение записи ~~~*/
if(isset($upd)) 
{
	$sql = "SELECT * FROM `".$_VARS['tbl_photo_name'].$zhanr."`
			WHERE id = ".$id;
	$res = mysql_query($sql);
	$row_img = mysql_fetch_array($res);
	$ext = $row_img['file_ext'];
		
	if($_POST['removeTo'] != '')
	{
		// имя нового альбома
		$newDir = $_POST['removeTo'];
		
		// скопируем файл-оригинал в новый альбом		
		$copy = copy($pathToFolder."$zhanr/$id.".$ext, $pathToFolder."$newDir/$id.".$ext);
		
		// если копирование прошло удачно
		if($copy)
		{
			$tbl = $_VARS['tbl_photo_name'].$newDir; 
			
			mysql_query("insert into `$tbl` SET `name`='$name', `file_ext`='$ext', `tags`='$tags', url='$url'");
			$newId = mysql_insert_id();
			$sql = "UPDATE `$tbl` SET `order_by` = '".$newId."' WHERE `id` = '".$newId."'";
			$res = mysql_query($sql);
			
			rename($pathToFolder."$newDir/$id.".$ext, $pathToFolder."$newDir/".$newId.".".$ext);
			
			mysql_query("delete FROM `".$_VARS['tbl_photo_name'].$zhanr."` WHERE `id` = '$id'");
			$old_dir = $pathToFolder."$zhanr";
			
			$dir = opendir($old_dir);
			chdir($old_dir);
						
			while($file = readdir($dir))
			{
				$template = "[^".$id."-?\w*.(jpg|gif|png)$]";
				$result = preg_match($template, $file); 
				if($result)	
				{
					unlink($file);
				}		
			}			
		}
	}
	else
	{
		$sql = "UPDATE `$tbl` 
				SET `name`='$name', `tags`='$tags', `tags_eng`='$tags_eng', pub = '$pub', `url`='$url' 
				WHERE `id` = '$id'";
		mysql_query($sql);	
	}	
	
	
	header("Location: ?page=$page&zhanr=$zhanr&p=$p&last_type=$last_type");
	exit;
}
/*~~~ /изменение записи ~~~*/

/******************/
/* новая картинка */
/******************/
if(isset($new)) 
{
	if(isset($_FILES["small"]["name"]))
	{
		// название картинки
		if(trim($name) == '')
			$name = $_FILES["small"]["name"]; 
		
		// сохраним файл на диск с уникальным именем
		if(isset($_FILES["small"]))
		{	
			$arrError = array(
				0 => 'Ошибок не было, файл загружен', 
				1 => 'Размер загруженного файла превышает размер установленный параметром upload_max_filesize в php.ini',
				2 => 'Размер загруженного файла превышает размер установленный параметром MAX_FILE_SIZE в HTML форме', 
				3 => 'Загружена только часть файла',
				4 => 'Файл не был загружен (Пользователь в форме указал неверный путь к файлу)'			
			);
			
			if($_FILES["small"]["error"] != 0)
			{
				echo 'Ошибка загрузки файла: '.$arrError[$_FILES["small"]["error"]].'<br><a href="javascript:window.history.back();">назад</a>';
				exit;
			}
			else
			{				
				// запишем в БД
				$sql = "INSERT INTO `$tbl` 
						SET `name`='$name', `tags`='$tags', `tags_eng`='$tags_eng', pub = '$pub', url='$url', img_create='".date('Y-m-d')."'";
				mysql_query($sql);		
				
				$id = mysql_insert_id();
				
				$s = saveImage($_FILES["small"]["tmp_name"], $parent_folder.$_VARS['photo_alb_sub_dir']."$zhanr", $id); 
				if(!$s)
				{
					echo 'Ошибка сохранения файла';
					$sql = "DELETE FROM `$tbl` 
							WHERE id = ".$id;
					mysql_query($sql);	
					exit;
				}
			}
		}
		else
		{
			echo 'Файл не был загружен';
			exit;
		}
		
		// определим тип закачанного файла
		$file_type 	= getimagesize($_FILES["small"]["tmp_name"]);
		switch ($file_type[2])
		{
			case 1 	: $ext = "gif"; break;			
			case 3 	: $ext = "png"; break;
			default : $ext = "jpg"; break;
		}
		
		// обновим запись в БД
		$sql = "UPDATE `$tbl` 
				SET `file_ext`='$ext', `order_by` = '$id' 
				WHERE `id` = '$id'";
		$res = mysql_query($sql);
		
		// запишем дату обновления альбома
		$sql = "UPDATE `".$_VARS['tbl_prefix']."_pic_catalogue`
				SET alb_update = '".date('Y-m-d')."'
				WHERE alb_name = ".$zhanr;
		$res = mysql_query($sql);
		
		header("Location: ?page=$page&zhanr=$zhanr&last_type=$last_type");
		exit;
	}
	else
	{
		echo 'Файл не загружен';
	}
	$error = "<p style=\"color:red;font-weight:bold;\">Ты забыл что-то ввести, родной!!!</p>"; 
}
/*******************/
/* /новая картинка */
/*******************/

if(isset($move) and isset($dir) and isset($id))
	MoveItem($id, $dir);

/*~~~ вывод всех картинок ~~~*/
if(!isset($p) || $p < 0) 
	$p = 0;

$start = $in_page * $p;

$sql = "SELECT * FROM `$tbl` 
		WHERE 1";
$res = mysql_query($sql);

$total = mysql_num_rows($res);

$uurl = "?page=$page&zhanr=$zhanr&p=";

$sql = "SELECT * FROM `$tbl`
		WHERE 1 
		ORDER BY `order_by` ASC 
		LIMIT $start, $in_page";
$r = mysql_query($sql);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charSET=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="admin.css" type="text/css">
<script language="javascript" type="text/javascript" src="js/jquery-1.5.min.js"></script>
<script language="javascript">

/*$(document).ready(function(){
	alert("test");
})*/
</script>
</head>


<body bgcolor="#FFFFFF">

	<a href="?page=photo_alb">Все альбомы</a>
	
	<h3>Фотоальбом "<?=$arrAlb[$zhanr]?>"</h3>
	
	<?
	if(!isset($error))
		$error = '';
		
	if(!isset($name))
		$name = ''; 

	?>
	<fieldset>
		<legend><strong>Новая картинка</strong></legend>
		<?=$error?>
		<form action="#new" method="post" enctype="multipart/form-data" name="newform">
			<table border="0" cellspacing="2" cellpadding="2">
				<tr>
					<td>Выберите картинку:</td>
					<td><input type="file" name="small"></td>
				</tr>
				<tr>
					<td>Название:</td>
					<td><input type="hidden" 	name="new" value="new">
						<input type="text" 		name="name" value=""  style="width:300px;">
						<input type="hidden" 	name="p" value="<?=$p?>">
						<input name="zhanr" 	type="hidden" id="zhanr" value="<?=$zhanr?>">
					</td>
				</tr>
				<?
				if($_VARS['multi_lang'])
				{
				?>
				<tr>
					<td>Название (eng):</td>
					<td>
						<input type="text" 		name="pub" value=""  style="width:300px;">
						
					</td>
				</tr>
				<?
				}
				?>
				<tr>
					<td>Описание:</td>
					<td><input type="text" name="tags" value="" style="width:300px;"></td>
				</tr>
				<?
				if($_VARS['multi_lang'])
				{
				?>
				<tr>
					<td>Описание (eng):</td>
					<td><input type="text" name="tags_eng" value="" style="width:300px;"></td>
				</tr>
				<?
				}
				?>
				<tr>
					<td>Ссылка:</td>
					<td><input type="text" name="url" value="" style="width:300px;"> (формат: http://url.ru)</td>
				</tr>
				
				
				<tr>
					<td colspan=2>
						<input type="submit" name="Submit" value="Загрузить">
					</td>
				</tr>
			</table>
		</form>		
	</fieldset>

	<p>&nbsp;</p>

	<table border="0" cellspacing="0" cellpadding="4">
	<?
	while($e = mysql_fetch_array($r)) 
	{
		if(!isset($fon) || $fon =="#ffffff") 
			$fon="#eee"; 
		else 
			$fon="#ffffff";
	?>
		<form action="?page=<?=$page?>&zhanr=<?=$zhanr?>" method="post" enctype="multipart/form-data" name="form<?=$e['id']?>">
			<tr bgcolor="<?=$fon?>" valign="top">			
				<td rowspan=6 width="10"><?=$e['id']?></td>
				<td rowspan=6 align="center" width=45>
					<a href="?page=<?=$page?>&zhanr=<?=$zhanr?>&p=<?=$p?>&id=<?=$e["id"]?>&last_type=<?=$last_type?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=<?=$page?>&zhanr=<?=$zhanr?>&p=<?=$p?>&id=<?=$e["id"]?>&last_type=<?=$last_type?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
				</td>
				<td rowspan="6" width="100" align="center">
				<?							
				$img = new Image();
				$img -> imgCatalogId 	= $zhanr;
				$img -> imgId 			= $e["id"];
				$img -> imgAlt 			= "";
				$img -> imgWidthMax 	= 200;
				$img -> imgHeightMax 	= 100;	
				$img -> imgMakeGrayScale= false;
				$img -> imgGrayScale 	= false;
				$img -> imgTransform	= "resize";				
				$html = $img -> showPic();
				echo $html;
				
				if(file_exists($img -> absPath()))
				{
					$info = getimagesize($img -> absPath());
					echo '<span class="imgInfo">'.$info[0].' x '.$info[1].' '.$arrExt[$info[2]].'</span>';				
				}
				?>
				</td>
				<td>Название:</td>
				<td><input type="text" name="name" value="<?=htmlspecialchars($e["name"])?>" style="width:300px;">&nbsp;</td>
				<td align="right" valign="top" rowspan=2><a href="javascript:if(confirm('Удалить эту картинку?'))location.replace('?page=<?=$page?>&zhanr=<?=$zhanr?>&p=<?=$p?>&id=<?=$e["id"]?>&last_type=<?=$last_type?>&del=del');" style="color:red;font-size:10px;" title="Удалить картинку" ><img src='<?=$_ICON["del"]?>' alt="del"></a> </td>
			</tr>
			
			<?
			if($_VARS['multi_lang'])
			{
			?>
				<tr bgcolor="<?=$fon?>">
				<td>Название (eng):</td>
				<td><input type="text" name="pub" value="<?=htmlspecialchars($e["pub"])?>" style="width:300px;">&nbsp;</td>
				</tr>
			<?
			}
			?>
			
			<tr bgcolor="<?=$fon?>">
				<td>Описание:</td>
				<td><input type="text" name="tags" value="<?=htmlspecialchars($e["tags"])?>" style="width:300px;"></td>
			</tr>
			
			<?
			if($_VARS['multi_lang'])
			{
			?>
			<tr bgcolor="<?=$fon?>">
				<td>Описание (eng):</td>
				<td><input type="text" name="tags_eng" value="<?=htmlspecialchars($e["tags_eng"])?>" style="width:300px;"></td>
			</tr>
			<?
			}
			?>
			
			
			<tr bgcolor="<?=$fon?>">
				<td>Ссылка:</td>
				<td colspan="2"><input type="text" name="url" value="<?=htmlspecialchars(@$e["url"])?>" style="width:300px;"></td>
			</tr>
			<tr bgcolor="<?=$fon?>">
				<td>Перенести в альбом</td>
				<td>
					<select name="removeTo">
						<option value=""></option>
						<?
						foreach($arrAlb as $k => $v)
						{
							$sel = '';
							if($zhanr == $k) 
								continue;
							?><option value="<?=$k?>"><?=$v?></option>
							<?
						}
						?> 
					</select>					
				</td>
				<td align=right>
					<input name="id" 	type="hidden" value="<?=$e["id"]?>">
					<input name="zhanr" type="hidden" id="zhanr" value="<?=$zhanr?>">
					<input name="upd" 	type="hidden" id="upd" value="upd">
					<input name="last_type" type="hidden" value="<?=$last_type;?>">
					<input name="p" 	type="hidden" id="p" value="<?=$p?>">
					<input type="submit" name="Submit" value="Сохранить">
				 </td>
			</tr>
		</form>
		<?
	}	
	?>
	</table>
	<br>
	</body>
</html>
