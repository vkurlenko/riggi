<?
include "../config.php" ;
include("../fckeditor/fckeditor.php") ;
include "../db.php";


$table_name = "wall_alb";

###################################
####		функции				###
###################################
function CreateTable()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		alb_name		text,
		alb_title		text,
		alb_video		text,
		alb_text		text,
		alb_img			int
	)";
	$res = mysql_query($sql);
}

function AddItem($alb_name, $alb_title, $alb_video, $alb_text, $alb_img)
{
	global $table_name;
	
	$sql = "insert into `$table_name` (alb_name, alb_title, alb_video, alb_text, alb_img)
	values ('$alb_name', '$alb_title', '$alb_video', '$alb_text', '$alb_img')";
	//echo $sql;
	$res = mysql_query($sql);
	$id = mysql_insert_id();
	$alb_name = $id + 500;
	$sql = "update `$table_name` set alb_name='$alb_name' where id=$id";
	$res = mysql_query($sql);
	
	$sql = "create table `wall_item` (
		id 		int auto_increment primary key,
		name	text,
		tags	text,
		pub		enum('1', '2', '3'),
		time	int,
		alb		int
	)";
	$res = mysql_query($sql);
	
	CreateFolder($alb_name);
	return $res;
}

function UpdateItem($id, $alb_title, $alb_video, $alb_text, $alb_img)
{
	global $table_name;

	$sql = "update `$table_name` set 
	alb_title='$alb_title',
	alb_video='$alb_video',
	alb_text='$alb_text',
	alb_img='$alb_img'
	where id=$id";
	$res = mysql_query($sql);
	return $res;
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
	$name = $name + 500;
	$sql = "drop table `photo".$name."`";
	//echo $sql;
	$res = mysql_query($sql);
	return $res;
}

/* удаление каталога */
function DeleteDir($directory) 
{
	$directory = $_SERVER['DOC_ROOT']."/wallpapers/photo".($directory + 500);
	//echo $directory;
	$id_arr = array();
	$p = explode("/", $directory);
	$parent_url = $p[count($p) - 2];
	
	$dir = opendir($directory);
	while(($file = readdir($dir)))
	{
		if(is_file ($directory."/".$file))
		{
			unlink ($directory."/".$file);
		}
		elseif(is_dir ($directory."/".$file) & ($file != ".") & ($file != ".."))
		{
			DeleteDir ($table_name, $id, $directory."/".$file);
			$id_arr[] = $file;
		}
	}
	closedir ($dir);
	
	$del = rmdir ($directory);
	return $del;  
}

function CreateFolder($name)
{
	mkdir($_SERVER['DOC_ROOT']."/wallpapers/photo".$name);
	chmod($_SERVER['DOC_ROOT']."/wallpapers/photo".$name, 0777);
}

CreateTable();

if(isset($set_item))
{
	AddItem(@$alb_name, @$alb_title, @$alb_video, @$alb_text, @$alb_img);
}

if(isset($update_item) and isset($id))
{	
	UpdateItem($id, $alb_title, @$alb_video, @$alb_text, $alb_img);
}



if(isset($del_item) and isset($id))
{
	DelItem($id);
	DropTable($id);
	DeleteDir($id);
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
			<td>Логотип</td>
			<td><select name="alb_img" >
			<?			
			$r = mysql_query("select * from `wall_item` where alb = ".$row['alb_name']." order by `id` desc ");
			
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
		
	</table>
		<br /><br />
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
	</table>
	<br /><br />			
			<input type="submit" name="set_item" value="Добавить" />
</form>
<?
}
else
{
	$sql = "select * from `$table_name` where 1 order by id asc";
	$res = mysql_query($sql);
	?>
	<strong style="padding:20px; display:block">Альбомы</strong>
	<table cellpadding="5">
		<tr>
			<td><strong>del</strong></td>
			<td><strong>Название</strong></td>
			<td><strong>Папка</strong></td>
			<td><strong>edit</strong></td>
		</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
		?>
		<tr>
			<td><a href="javascript:if (confirm('Удалить раздел?')){document.location='?page=wall_alb&del_item&id=<?=$row['id']?>'}">X</a></td>
			<td><?=$row['alb_title'];?></td>
			<td><a href="?page=photo&zhanr=<?=$row['alb_name'];?>"><?=$row['alb_name'];?></a></td>
			<td><a href="?page=wall_alb&edit_item&id=<?=$row['id']?>">edit</a></td>
		</tr>
		<?
		}
		?>		
	</table>
	<?
}
?>
</body>
</html>
