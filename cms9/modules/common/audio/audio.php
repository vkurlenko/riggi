<?
include "../config.php" ;
include "../db.php";
include "../functions_sql.php";

$debugMode = "error"; // none|error|all

$arrParam = array(
	'none' 	=> 'без параметров', 
	'plus'	=> 'плюсовка', 
	'minus'	=> 'минусовка'
);

$table_name = $_VARS['tbl_prefix']."_audio";
$table_param = array(
	"id" 			=> "int auto_increment primary key",
	"track_title"	=> "text",
	"track_file"	=> "text",
	"track_folder"	=> "text",
	"track_artist"	=> "text",
	"track_author"	=> "text",
	"track_length"	=> "text",
	"track_text"	=> "text",
	"track_link"	=> "text",
	"track_param"	=> "enum('none', 'plus', 'minus')",
	"track_price"	=> "decimal(11,2)"
);

###################################
####		функции				###
###################################
function CreateTableAudio()
{
	global $table_name, $table_param;
	$i = 0;
	$sql = "create table IF NOT EXISTS `$table_name` (";
	foreach($table_param as $k => $v)
	{
		$sql .= $k." ".$v;
		$i++;
		if($i < count($table_param)) $sql .= ", ";
	}
	$sql .= ")";
	$res = mysql_query($sql);
	debugMsg($sql, $res);
}

function AddItem($arr)
{
	global $table_name, $table_param;
	
	$set_str = $val_str = '';
	$i = 0;
	
	foreach($arr as $k => $v)
	{
		$set_str .= $k;		
	
		if($table_param[$k] == "text")
		{
			$v = "'".$v."'"; 
		};
		if($table_param[$k] == "date") $v = "'".$v."'";		
		if($table_param[$k] == "enum('none', 'plus', 'minus')") $v = "'".$v."'";		
		$val_str .= $v;
	
		$i++;
		if($i < count($arr)) 
		{
			$set_str .= ", ";
			$val_str .= ", ";
		}
	}
	
	$sql = "insert into `$table_name` ($set_str) values($val_str)";
	$res = mysql_query($sql);
	debugMsg($sql, $res);
	
}


function UpdateItem($id, $arr)
{
	global $table_name, $table_param;

	$sql = "update `$table_name` set ";
	$i = 0;
	
	foreach($arr as $k => $v)
	{
		if($table_param[$k] == "text") $v = "'".$v."'";	
		if($table_param[$k] == "date") $v = "'".$v."'";	
		if($table_param[$k] == "enum('none', 'plus', 'minus')") $v = "'".$v."'";	

		$sql .= $k." = ".$v;
		$i++;
		if($i < count($arr)) $sql .= ", ";
	}
	$sql .= " where id=".$id;
	 
	$res = mysql_query($sql);
	debugMsg($sql, $res);
	//return $res;
}


function DelItem($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}

CreateTableAudio();

if(isset($set_item))
{
	if($track_title == '')  $track_title = $track_file;
	
	$arrId3 = readframe($_SERVER['DOC_ROOT']."/".$_VARS['audio_alb_dir']."/".$track_file);
	$track_length = $arrId3['lengths'];

	$arr = array(
		"track_title"	=> $track_title,
		"track_file"	=> $track_file,
		"track_folder"	=> $track_folder,
		"track_artist"	=> $track_artist,
		"track_author"	=> $track_author,
		"track_length"	=> $track_length,
		"track_text"	=> $track_text,
		"track_link"	=> $track_link,
		"track_param"	=> $track_param,
		"track_price"	=> $track_price		
	);
	AddItem($arr);
}

if(isset($update_item) and isset($id))
{	
	if($track_length == '')
	{
		$arrId3 = readframe($_SERVER['DOC_ROOT']."/".$_VARS['audio_alb_dir']."/".$track_file);
		$track_length = $arrId3['lengths'];
	}
	
	if($track_title == '')  $track_title = $track_file;

	$arr = array(
		"track_title"	=> $track_title,
		"track_file"	=> $track_file,
		"track_folder"	=> $track_folder,
		"track_artist"	=> $track_artist,
		"track_author"	=> $track_author,
		"track_length"	=> $track_length,
		"track_text"	=> $track_text,
		"track_link"	=> $track_link,
		"track_param"	=> $track_param,
		"track_price"	=> $track_price		
	);
	UpdateItem($id, $arr);
}



if(isset($del_item) and isset($id))
{
	DelItem($id);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="admin.css" type="text/css">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?
if(isset($edit_item) and isset($id))
{
	$sql = "select * from `$table_name` where id='$id'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	?>
	<fieldset><legend><strong>Редактирование</strong></legend>
	<a class="serviceLink" href="?page=audio"><img src='<?=$_ICON["add_item"]?>'>Все аудио-файлы</a>
	<form method="post" enctype="multipart/form-data" action="" name="form2" id="form2">
	<table>
		<tr>
			<td>Название трека</td>
			<td>
				<input type="text" name="track_title" value="<?=$row['track_title']?>" class="admInput"/>
				<input type="hidden" name="id" value="<?=$row['id']?>" />
			</td>
		</tr>
		<tr>
			<td>Файл</td>
			<td>
			<select name="track_file" value=""  class="admInput"><?
				$dir = opendir($_SERVER['DOC_ROOT']."/".$_VARS['audio_alb_dir']."/");
				while(($file = readdir($dir)) !== false)
				{
					if($file != ".." && $file != ".")
					{
						if($file == $row['track_file']) $selected = " selected";
						else $selected = "";
					?>
					<option value="<?=$file;?>" <?=$selected;?>><?=$file;?></option>
					<?		
					}			
				}				
				?></select>		
			</td>
		</tr>
		<tr>
			<td>Привязка к альбому</td>
			<td>
				<select name="track_folder">
					<?			
					$check_val = $row['track_folder'];
					echo $check_val;
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
			</td>
		</tr>		
		<tr>
			<td>Параметр</td>
			<td>
				<select name="track_param">
				<?
				foreach($arrParam as $k => $v )
				{
						$sel = '';
						if($k == $row['track_param']) $sel = ' selected ';
					?>
					<option value="<?=$k?>" <?=$sel?>><?=$v?></option>
					<?
				}
				?>
				</select>
				
			</td>
		</tr>	
		<tr>
			<td>Исполнитель</td>
			<td>
				<input type="text" name="track_artist" value="<?=$row['track_artist']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Автор</td>
			<td>
				<input type="text" name="track_author" value="<?=$row['track_author']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Длительность трека (сек.)</td>
			<td>
				<input type="text" name="track_length" value="<?=$row['track_length']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Ссылка на текст трека</td>
			<td>
				<input type="text" name="track_text" value="<?=$row['track_text']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Ссылка на скачивание трека</td>
			<td>
				<input type="text" name="track_link" value="<?=$row['track_link']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Стоимость трека</td>
			<td>
				<input type="text" name="track_price" value="<?=$row['track_price']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="update_item" value="Сохранить" /></td>
		</tr>
	</table>
	</form>
	<a class="serviceLink" href="?page=audio"><img src='<?=$_ICON["add_item"]?>'>Все аудио-файлы</a>
	</fieldset>
	<?	
}
elseif(isset($add_item))
{
?> 

<fieldset><legend><strong>Добавить трек</strong></legend>
<a class="serviceLink" href="?page=audio"><img src='<?=$_ICON["add_item"]?>'>Все аудио-файлы</a>
<form method=post enctype=multipart/form-data action="" name="form2" id="form2">
	<table>
		<tr>
			<td>Название трека</td>
			<td>
			<?
			$sql = "select id from `".$table_name."` where 1 order by id desc limit 0,1";
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);
			?>
			<input type="text" name="track_title" value="track<?=$row['id']?>" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Файл</td>
			<td>
			<select name="track_file" value=""  class="admInput"><?
				$dir = opendir($_SERVER['DOC_ROOT']."/".$_VARS['audio_alb_dir']."/");
				while(($file = readdir($dir)) !== false)
				{
					if($file != ".." && $file != ".")
					{
					?>
					<option value="<?=$file;?>"><?=$file;?></option>
					<?		
					}			
				}				
				?></select>		
			</td>
		</tr>
		<tr>
			<td>Привязка к альбому</td>
			<td>
				<select name="track_folder">
					<?			
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
			</td>
		</tr>	
		
		<tr>
			<td>Параметр</td>
			<td>
				<select name="track_param">
				<?
				foreach($arrParam as $k => $v )
				{
					?>
					<option value="<?=$k?>"><?=$v?></option>
					<?
				}
				?>
				</select>
				
			</td>
		</tr>	
		
		<tr>
			<td>Исполнитель</td>
			<td>
				<input type="text" name="track_artist" value="" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Автор</td>
			<td>
				<input type="text" name="track_author" value="" class="admInput"/>
			</td>
		</tr>		
		<tr>
			<td>Ссылка на текст трека</td>
			<td>
				<input type="text" name="track_text" value="" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Ссылка на скачивание трека</td>
			<td>
				<input type="text" name="track_link" value="" class="admInput"/>
			</td>
		</tr>
		<tr>
			<td>Стоимость трека</td>
			<td>
				<input type="text" name="track_price" value="0.00" class="admInput"/>
			</td>
		</tr>		
		<tr>
			<td colspan="2"><input type="submit" name="set_item" value="Добавить" /></td>
		</tr>
	
</table>
</form>
<a class="serviceLink" href="?page=audio"><img src='<?=$_ICON["add_item"]?>'>Все аудио-файлы</a>
</fieldset>
<?
}
else
{
	
	$sql = "select * from `$table_name` where 1 order by track_folder asc";
	$res = mysql_query($sql);
	?>
	<fieldset><legend>Аудио-файлы</legend>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить новый аудио-файл</a>
	<table cellpadding="5" class="list">
		<tr>
			<th>del</th>
			<th>Название трека</th>
			<th>Имя файла</th>
			<th>Длительность</th>
			<th>Параметры</th>
			<th>Исполнитель</th>
			<th>Альбом</th>
			<th>edit</th>
		</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
		?>
		<tr>
			<td><a href="javascript:if (confirm('Удалить трек?')){document.location='?page=audio&del_item&id=<?=$row['id']?>'}">X</a></td>
			<td><a href="?page=audio&edit_item&id=<?=$row['id']?>"><?=$row['track_title'];?></a></td> 
			<td><?=$row['track_file'];?></td> 
			<td><?=$row['track_length'];?></td> 
			<td><? if($row['track_param'] != 'none') echo $arrParam[$row['track_param']];?></td> 
			<td><?=$row['track_artist'];?></td>
			<?
			$sql2 = "select * from `".$_VARS['tbl_photo_alb_name']."` where alb_name = '".$row['track_folder']."'";
			$res2 = mysql_query($sql2);
			$row2 = mysql_fetch_array($res2);
			$folder = $row2['alb_title'];
			?>
			<td><?=$folder;?></td>
			<td><a href="?page=audio&edit_item&id=<?=$row['id']?>">edit</a></td>
		</tr>
		<?
		}
		?>		
	</table>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить новый аудио-файл</a>
	</fieldset>
	<?
}
?>
</body>
</html>
