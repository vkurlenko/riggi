<?
include "../config.php" ;
include ("../db.php" );

$photo_alb = 125;
$table_name = $_VARS['tbl_prefix']."_menu_items";
###################################
####		функции				###
###################################
function CreateTableMainMenu()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		item_type		int,
		item_parent		int,
		item_name		text,
		item_price		text,
		item_photo		text,
		item_show		int,
		item_rating		int
	)" ;
	$res = mysql_query($sql);
}

function AddMenuItem($item_type, $item_parent, $item_name, $item_price = 0, $item_photo = 0)
{
	global $table_name;
	
	$sql = "insert into `$table_name` (item_type, item_parent, item_name, item_price, item_photo)
	values ('$item_type', '$item_parent', '$item_name', '$item_price', '$item_photo')";
	$res = mysql_query($sql);
	
	$order = mysql_insert_id();	
	$sql_2 = "update `$table_name` set 
	item_rating = '$order'	where id=$order";
	$res_2 = mysql_query($sql_2);
	return $res;
}

function UpdateMenu($id, $item_parent, $item_name, $item_price = 0, $item_photo = 0)
{
	global $table_name;
	$sql = "update `$table_name` set 
	item_parent='$item_parent',
	item_name='$item_name',
	item_price='$item_price',
	item_photo='$item_photo'
	where id=$id";
	$res = mysql_query($sql);
	return $res;
}


function GetMenu()
{
	$sql = "select * from `actions` where 1 order by news_title asc";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		echo $row['id']." ".$row['news_title']."<br>";
	}
}

function DelMenu($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}


###################################

###################################
####		^^^функции			###
###################################

CreateTableMainMenu();

if(isset($set_sub_menu))
{
	AddMenuItem("sub_menu", $item_parent, $item_name);
}

if(isset($set_item))
{
	AddMenuItem("item", $sub_menu_parent, $item_name, $item_price, $item_photo);
}

if(isset($update_menu) and isset($id))
{
	UpdateMenu($id, $sub_menu_parent, $item_name, $item_price, $item_photo);
}

if(isset($del_menu) and isset($id))
{
	DelMenu($id);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="admin.css" type="text/css">
<title>Untitled Document</title>
</head>

<body>
<?
if(isset($_GET['add_menu']))
{
	/*echo "<strong style=\"padding:20px; display:block\">Разделы меню</strong>";
	GetMenu();*/
?>
<fieldset><legend><strong>Добавить подраздел меню</strong></legend>
<form method=post enctype=multipart/form-data action="?page=restourant_menu_items" name="form2" id="form2"><table>
	<tr>
		<td>Раздел меню</td><td><select name="item_parent">
		<?
		$sql = "select * from `".$_VARS['tbl_prefix']."_menu_volumes` where 1 order by news_title asc";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			echo "<option value='".$row['id']."'>".$row['news_title']."</option>";
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td>Наименование подраздела</td><td><input type="text" name="item_name" class="admInput" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="set_sub_menu" value="Добавить" /></td>
	</tr>
</table>
</form>
</fieldset>

<fieldset><legend><strong>Добавить блюдо</strong></legend>
<form method=post enctype=multipart/form-data action="?page=restourant_menu_items" name="form1" id="form1"><table>
	<tr>
		<td>Подраздел</td><td><select name="sub_menu_parent">
		<?
		$sql = "select * from `".$_VARS['tbl_prefix']."_menu_volumes` where 1 order by news_title asc";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			echo "<optgroup label='".$row['news_title']."'>";
			$sql_2 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
			$res_2 = mysql_query($sql_2);
			while($row_2 = mysql_fetch_array($res_2))
			{
				echo "<option value='".$row_2['id']."' selected>".$row_2['item_name']."</option>";
			}
			echo "</optgroup>";
			
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td>Наименование блюда</td><td><input type="text" name="item_name"  class="admInput" /></td>
	</tr>
	<tr>
		<td>Стоимость</td><td><input type="text" name="item_price" /></td>
	</tr>
	<tr>
		<td>Фотография</td>
		<td><select name="item_photo" >
			<option value='0' selected>Без картинки
		<?
		$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
		while($row = mysql_fetch_array($r))
		{
			echo "<option value='".$row['id']."'>".$row['name']."\n";
		}
		?>
		</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo<?=$photo_alb;?>" target="_self">Блюда</a>")</span></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="set_item" value="Сохранить" /></td>
	</tr>
</table>
</form>
</fieldset>
<?
}
elseif(isset($edit_menu) and isset($id))
{
	$sql_3 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where id=$id";
	$res_3 = mysql_query($sql_3);
	$row_3 = mysql_fetch_array($res_3);
	
?>
<fieldset><legend><strong>Редактирование позиции</strong></legend>
<form method=post enctype=multipart/form-data action="?page=restourant_menu_items" name="form1" id="form1"><table>
	<tr>
		<td>Подраздел</td><td><select name="sub_menu_parent">
		<?
		if($type == "item_menu")
		{
			$sql = "select * from `".$_VARS['tbl_prefix']."_menu_volumes` where 1 order by news_title asc";
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res))
			{
				echo "<optgroup label='".$row['news_title']."'>\n";
				$sql_2 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
				$res_2 = mysql_query($sql_2);
				while($row_2 = mysql_fetch_array($res_2))
				{
					if($row_3['item_parent'] == $row_2['id']) $selected = " selected ";
					else $selected = " ";
					echo "<option value='".$row_2['id']."' ".$selected.">".$row_2['item_name']."</option>\n";
				}
				echo "</optgroup>\n";			
			}
		}
		else
		{
			$sql = "select * from `".$_VARS['tbl_prefix']."_menu_volumes` where 1 order by news_title asc";
			$res = mysql_query($sql);
			/*while($row = mysql_fetch_array($res))
			{
				echo "<optgroup label='".$row['news_title']."'>\n";
				$sql_2 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
				$res_2 = mysql_query($sql_2);*/
				while($row = mysql_fetch_array($res))
				{
					if($row_3['item_parent'] == $row['id']) $selected = " selected ";
					else $selected = " ";
					echo "<option value='".$row['id']."' ".$selected.">".$row['news_title']."</option>\n";
				}
				/*echo "</optgroup>\n";			
			}*/
		}
		?>
		</select></td>
	</tr>
	<tr>
		<td>Наименование блюда</td><td><input type="text" name="item_name" value="<?=$row_3['item_name']?>"  class="admInput" />
		<input type="hidden" name="id" value="<?=$row_3['id']?>" />
		</td>
	</tr>
	<tr>
		<td>Стоимость</td><td><input type="text" name="item_price" value="<?=$row_3['item_price']?>" /></td>
	</tr>
<!--	<tr>
		<td>Фотография</td><td><input type="text" name="item_photo" value="<?=$row_3['item_photo']?>" /></td>
	</tr>
-->	<tr>
		<td>Картинка</td>
		<td><select name="item_photo" >
		<?
		$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
		if($row_3['item_photo'] == 0) echo "<option value='0' selected>Без картинки\n";
		else echo "<option value='0'>Без картинки\n";
		while($row = mysql_fetch_array($r))
		{
			if ($row_3['item_photo'] == $row['id']) $selected = " selected";
			else $selected = " ";
			echo "<option value='".$row['id']."' ".$selected.">".$row['name']."\n";
		}
		?>
		</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo<?=$photo_alb;?>" target="_self">Блюда</a>")</span></td>
	</tr>
		<tr>
		<td colspan="2"><input type="submit" name="update_menu" value="Сохранить" /></td>
	</tr>
</table>
</form>
</fieldset>
<?
}
else
{
	?>
	<a href="?page=restourant_menu_items&add_menu">Добавить подраздел или блюдо</a><br />
<br />
<table cellpadding="5"><?
	$sql = "select * from `".$_VARS['tbl_prefix']."_menu_volumes` where 1 order by news_title";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		?>
		<tr>
			<td colspan=4><br /><strong><?=$row['news_title']?></strong></td>
		</tr>
		<?
		$sql_2 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where (item_type = 'sub_menu' and item_parent = ".$row['id'].") order by item_rating asc";
		$res_2 = mysql_query($sql_2);
		while($row_2 = mysql_fetch_array($res_2))
		{
			?>
			<tr>
				<td><strong><em>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_2['item_name']?></em></strong></td>
				<td></td>
				<td><a href="?page=restourant_menu_items&del_menu&id=<?=$row_2['id']?>">x</a></td>
				<td><a href="?page=restourant_menu_items&edit_menu&type=sub_menu&id=<?=$row_2['id']?>">edit</a></td>
			</tr>
			<?
			$sql_3 = "select * from `".$_VARS['tbl_prefix']."_menu_items` where (item_type = 'item' and item_parent = ".$row_2['id'].") order by item_rating asc";
			$res_3 = mysql_query($sql_3);
			while($row_3 = mysql_fetch_array($res_3))
			{
				?>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$row_3['item_name']?></td>
					<td><?=$row_3['item_price']?></td>
					<td><a href="?page=restourant_menu_items&del_menu&id=<?=$row_3['id']?>">x</a></td>
					<td><a href="?page=restourant_menu_items&edit_menu&type=item_menu&id=<?=$row_3['id']?>">edit</a></td>
				</tr>
				<?
			}
		}
	}
	?></table>
	<br /><br /><a href="?page=restourant_menu_items&add_menu">Добавить подраздел или блюдо</a>
	<?
}
?>

</body>
</html>
