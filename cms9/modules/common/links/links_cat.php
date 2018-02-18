<?
include "../config.php" ;
include("../fckeditor/fckeditor.php") ;
include "../db.php";


$table_name = "links_cat";

###################################
####		функции				###
###################################
function CreateTable()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		cat_name		text,
		cat_order		int
	)";
	$res = mysql_query($sql);
}

function AddItem($cat_name)
{
	global $table_name;
	
	$sql = "insert into `$table_name` (cat_name)
	values ('$cat_name')";
	$res = mysql_query($sql);
	$id = mysql_insert_id();
	$sql = "update `$table_name` set cat_order='$id' where id=$id";
	$res = mysql_query($sql);
	return $res;
}

function UpdateItem($id, $cat_name)
{
	global $table_name;

	$sql = "update `$table_name` set 
	cat_name='$cat_name'
	where id=$id";
	$res = mysql_query($sql);
	return $res;
}

/*	изменение порядка следования записей	*/
function MoveItem($id, $direction)
{
	global $table_name;
	if($direction == "asc") $arrow = ">";
	elseif($direction == "desc") $arrow = "<";
	$sql = "select * from `".$table_name."` where id=".$id." order by cat_order asc";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "select * from `".$table_name."` where (cat_order ".$arrow." ".$row['cat_order'].") order by cat_order ".$direction." limit 1 ";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "update `".$table_name."` set cat_order=".$row_2['cat_order']." where id=".$id;
	$res = mysql_query($sql);
	
	$sql = "update `".$table_name."` set cat_order=".$row['cat_order']." where id=".$row_2['id'];
	$res = mysql_query($sql);
}



function DelItem($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}



CreateTable();

if(isset($set_item))
{
	AddItem($cat_name);
}

if(isset($update_item) and isset($id))
{	
	UpdateItem($id, $cat_name);
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
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
	$sql = "select * from `$table_name` where 1 order by cat_order asc";
	$res = mysql_query($sql);
	?>
	<strong style="padding:20px; display:block">Категории ссылок</strong>
	<table cellpadding="5">
		<tr>
			<td><strong>del</strong></td>
			<td><strong>Название</strong></td>
			<td><strong>edit</strong></td>
			<td><strong>down</strong></td>
			<td><strong>up</strong></td>
		</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
		?>
		<tr>
			<td><a href="javascript:if (confirm('Удалить раздел?')){document.location='?page=links_cat&del_item&id=<?=$row['id']?>'}">X</a></td>
			<td><?=$row['cat_name'];?></td>
			<td><a href="?page=links_cat&move&dir=asc&id=<?=$row['id']?>">down</a></td>
			<td><a href="?page=links_cat&move&dir=desc&id=<?=$row['id']?>">up</a></td>
			<td><a href="?page=links_cat&edit_item&id=<?=$row['id'];?>">edit</a></td>
		</tr>
		<?
		}
		?>		
	</table>
	<br /><br />
<?	

if(!isset($edit_item))
{
	?>
	<strong style="padding:20px; display:block">Добавить категорию</strong>
	<form method=post enctype=multipart/form-data action="" name="form2" id="form2"><table>
		<table>
			<tr>
				<td>Категория</td>
				<td>
				<input type="text" name="cat_name" value="" size="40" />
				</td>
			</tr>		
		</table>
		<br /><br />
				<input type="submit" name="set_item" value="Добавить" />
	</form>
	<?
}

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
			<td>Категория</td>
			<td>
			<input type="text" name="cat_name" value="<?=$row['cat_name']?>" size="83" />
			<input type="hidden" name="id" value="<?=$row['id']?>" />
			</td>
		</tr>
	</table>
<br /><br />
			<input type="submit" name="update_item" value="Сохранить" />	
	</form>
	<?	
}

?>

</body>
</html>
