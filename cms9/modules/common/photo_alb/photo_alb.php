<?
session_start();
include_once "../config.php" ;
include_once "../fckeditor/fckeditor.php";
include_once "../db.php";
check_access(array("admin", "editor"));


$table_name = $_VARS['tbl_photo_alb_name'];



$arrAlbMark = array(
	"none" 		=> "без метки",
	"gallery" 	=> "галерея",	
	"collection"=> "коллекция"
);



###################################
####		функции				###
###################################
function CreateTable()
{
	global $table_name;
	$sql = "CREATE TABLE `$table_name` (
		id 				int auto_increment primary key,
		alb_name		text,
		alb_title		text,
		alb_video		text,
		alb_text		text,
		alb_img			int default '0' not null,
		alb_mark		enum('none', 'gallery', 'collection') not null,
		alb_create		date NOW(),
		alb_update		date NOW(),
		alb_order		int
	)";
	$res = mysql_query($sql);
}

function editFlash($code)
{
	//echo htmlspecialchars($code);
	$addParam = 'wmode=\\"opaque\\"';
	$is_param = strpos($code, $addParam);
	//echo "pos = ".$is_param;
	if($is_param !== false)
	{}
	else
	{
		$code = str_replace("<embed", "<embed ".$addParam." ", $code);
	}
	return $code;
}

function AddItem($alb_name, $alb_title, $alb_video, $alb_text, $alb_img, $alb_mark)
{
	global $table_name, $_VARS;
	$alb_video = editFlash($alb_video);
	$sql = "INSERT INTO `$table_name` 
			(alb_name, alb_title, alb_video, alb_text, alb_img, alb_mark, alb_create, alb_update)
			VALUES 
			('$alb_name', '$alb_title', '$alb_video', '$alb_text', '$alb_img', '$alb_mark', '".date('Y-m-d')."', '".date('Y-m-d')."')";

	$res = mysql_query($sql);
	$id = mysql_insert_id();
	$alb_name = $id;
	
	$sql = "UPDATE `$table_name` 
			SET alb_name='$alb_name', alb_order=$id			 
			WHERE id=$id";
	$res = mysql_query($sql);
	
	$sql = "CREATE TABLE `".$_VARS['tbl_photo_name']."$alb_name` (
		id 			int auto_increment primary key,
		file_ext	text,
		name		text,
		tags		text,
		pub			int,
		url			text,
		order_by	int,
		img_create  date
	)";
	$res = mysql_query($sql);
	
	CreateFolder($alb_name);
	
	return $res;
}

function UpdateItem($id, $alb_title, $alb_video, $alb_text, $alb_img, $alb_mark)
{
	global $table_name, $_VARS;
	$alb_video = editFlash($alb_video);

	$sql = "update `$table_name` set 
	alb_title='$alb_title',
	alb_video='$alb_video',
	alb_text='$alb_text',
	alb_img='$alb_img',
	alb_mark='$alb_mark',
	alb_update='".date('Y-m-d')."'
	where id=$id";
	$res = mysql_query($sql);
	return $res;
}

function MoveItem($id, $direction)
{
	global $table_name, $_VARS;
	
	if($direction == "asc") 
		$arrow = ">";
	else
		$arrow = "<";
		
	$sql = "SELECT * FROM `".$table_name."` 
			WHERE id=".$id;
			
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "SELECT * FROM `".$table_name."` 
			WHERE (alb_order ".$arrow." ".$row['alb_order'].") 
			ORDER BY alb_order ".$direction." 
			LIMIT 1";
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "UPDATE `".$table_name."` 
			SET alb_order=0 where id=".$row_2['id'];
	//echo $sql."<br>";
	$res = mysql_query($sql);
	
	$sql = "UPDATE `".$table_name."` 
			SET alb_order=".$row_2['alb_order']."
			WHERE id=".$id;
	//echo $sql."<br>";
	$res = mysql_query($sql);
	
	$sql = "UPDATE `".$table_name."` 
			SET alb_order=".$row['alb_order']." 
			WHERE alb_order=0";
	//echo $sql."<br>";
	$res = mysql_query($sql);
}


function DelItem($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}

function DropTable($name)
{
	global $_VARS;
	/*$name = $name + 100;*/
	$name = $name;
	$sql = "drop table `".$_VARS['tbl_photo_name'].$name."`";
	//echo $sql;
	$res = mysql_query($sql);
	return $res;
}

/* удаление каталога */
function DeleteDir($directory) 
{
	global $_VARS;
	/*$directory = $_SERVER['DOC_ROOT']."/photo_alb/photo".($directory + 100);*/
	$directory = $_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']."/".$_VARS['tbl_photo_name'].($directory);
	//echo $directory;
	$id_arr = array();
	$p = explode("/", $directory);
	$parent_url = $p[count($p) - 2];
	
	$dir = opendir($directory);
	while(($file = readdir($dir)))
	{
		if(is_file ($directory."/".$file))
			unlink ($directory."/".$file);
		elseif(is_dir ($directory."/".$file) & ($file != ".") & ($file != ".."))
		{
			DeleteDir ($table_name, $id, $directory."/".$file);
			$id_arr[] = $file;
		}
	}
	closedir ($dir);
	
	$del = rmdir($directory);
	return $del;  
}

function CreateFolder($name)
{
	global $_VARS;
	if(!is_dir($_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']))
	{
		mkdir($_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']);
		chmod($_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir'], 0777);
	}
	
	mkdir($_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']."/".$_VARS['tbl_photo_name'].$name);
	chmod($_SERVER['DOC_ROOT']."/".$_VARS['photo_alb_dir']."/".$_VARS['tbl_photo_name'].$name, 0777);
}

/*function imgInAlb($albId)
{
	$sql = "SELECT * FROM ``"
}*/

CreateTable();

if(isset($set_item))
{
	$res = AddItem(@$alb_name, @$alb_title, @$alb_video, @$alb_text, @$alb_img, @$alb_mark);
	
	if($res)
		header('location:/'.$_VARS['cms_dir'].'/workplace.php?page=photo_alb');
}

if(isset($update_item) and isset($id))
{	
	$res = UpdateItem($id, $alb_title, @$alb_video, @$alb_text, $alb_img, $alb_mark);

	if($res)
		header('location:/'.$_VARS['cms_dir'].'/workplace.php?page=photo_alb');
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
}

if(isset($del_item) and isset($id))
{
	DelItem($id);
	DropTable($id);
	DeleteDir($id);
}


?>
<?
include_once "head.php";
?>

<body>
<?
if(isset($edit_item) and isset($id))
{
	$sql = "select * from `$table_name` where id='$id'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	?>
	<strong style="padding:20px; display:block">Редактирование</strong>
	<form method="post" enctype="multipart/form-data" action="" name="form2" id="form2">
	<table>
		<tr>
			<td>Название</td>
			<td>
			<input type="text" name="alb_title" value="<?=$row['alb_title']?>" size="83" />
			<input type="hidden" name="id" value="<?=$row['id']?>" />
			</td>
		</tr>
		<tr>
			<td>Картинка превью</td>
			<td><select name="alb_img" >
			<?			
			$r = mysql_query("select * from `".$_VARS['tbl_photo_name'].$row['alb_name']."` order by `id` desc ");
			
			if($row['alb_img'] == 0) echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			while($res = mysql_fetch_array($r))
			{
				if ($row['alb_img'] == $res['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$res['id']."' ".$selected.">".$res['name']."\n";
			}
			?>
			</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$row['alb_name']?>" target="_self"><?=$row['alb_title']?></a>")</span></td>
		</tr>
		<tr>
			<td>Метка альбома</td>
			<td><select name="alb_mark" >
			<?			
			foreach($arrAlbMark as $k => $v)
			{
				$sel = "";
				if($k == $row['alb_mark']) $sel = " selected ";
				?>
				<option value="<?=$k?>" <?=$sel?>><?=$v?></option>
				<?
			}
			?>
			</select>
			</td>
		</tr>
		
		<tr>
			<td>
				Код вставки видео</td><td>
				<textarea name="alb_video" cols="60" rows="10" /><?=$row['alb_video'];?></textarea><span style="font-size:10px;">(вставляется html-код видео-ролика)</span>
			</td>
		</tr>
		<tr>
			<td>
				Текст</td><td>
				<textarea name="alb_text" cols="60" rows="10" ><?=$row['alb_text']?></textarea>
			</td>
		</tr>
	</table>
			<input type="submit" name="update_item" value="Сохранить" />	
	</form>
	<?	
}
elseif(isset($add_item))
{
?> 

<strong style="padding:20px; display:block">Добавить альбом</strong>
<form method=post enctype=multipart/form-data action="" name="form2" id="form2"><table>
	<table>
		<tr>
			<td>Название</td>
			<td>
			<input type="text" name="alb_title" value="" size="40" />
			</td>
		</tr>	
		<tr>
			<td>Текст</td>
			<td>
			<textarea style="width:500" name="alb_text"></textarea>
			</td>
		</tr>		
	</table>
		
	<input type="submit" name="set_item" value="Добавить" />
</form>
<?
}
else
{
	$sql = "select * from `$table_name` where 1 order by alb_order asc";
	//echo $sql;
	$res = mysql_query($sql);
	?>
	<fieldset><legend>Фотоальбомы</legend>
		<a class="serviceLink" href="?page=photo_alb&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить новый альбом</a>
		<table cellpadding="5"  class="list">
			<tr>
				<th><strong>вверх</strong></th>
				<th><strong>вниз</strong></th>
				<th><strong>Название</strong></th>
				<th><strong>Папка</strong></th>
				<th><strong>Метка</strong></th>
				<th><strong>Создан</strong></th>
				<th><strong>Обновлен</strong></th>
				<th><strong>edit</strong></th>
				<th><strong>del</strong></th>				
			</tr>
			<?
			while($row = mysql_fetch_array($res))
			{
			?>
			<tr>
				<td align="center"><a href="?page=photo_alb&move&dir=asc&id=<?=$row['id']?>"><img src='<?=$_ICON["down"]?>'></a></td>
				<td align="center"><a href="?page=photo_alb&move&dir=desc&id=<?=$row['id']?>"><img src='<?=$_ICON["up"]?>'></a></td>				
				<td><strong><a href="?page=photo&zhanr=<?=$row['alb_name'];?>"><?=$row['alb_title'];?></a></strong></td>
				<td><?="/".$_VARS['tbl_photo_name'].$row['alb_name'];?></td>
				<td align="center"><? 
				if($row['alb_mark'] == 'gallery')
				{
					?><img title="В галерею" src='<?=$_ICON["pictures"]?>'><? 
				}
				elseif($row['alb_mark'] == 'collection')
				{
					?><img title="В коллекцию" src='<?=$_ICON["collection"]?>'><?
				}
				?>
				</td>
				<td><?=$row['alb_create'];?></td>
				<td><?=$row['alb_update'];?></td>
				
				<td align="center"><a href="?page=photo_alb&edit_item&id=<?=$row['id']?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td align="center"><a href="javascript:if (confirm('Удалить раздел?')){document.location='?page=photo_alb&del_item&id=<?=$row['id']?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
				
			</tr>
			<?
			}
			?>		
		</table>
		<a class="serviceLink" href="?page=photo_alb&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить новый альбом</a>
	</fieldset>
	<?
}


?>
</body>
</html>
