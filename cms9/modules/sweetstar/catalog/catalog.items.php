<?
session_start();

include "../config.php" ;
include "../db.php";

check_access(array("admin", "manager"));

$photo_alb = 36;
$table_name = $_VARS['tbl_prefix']."_catalog_items";

/*----------------------------------*/
$arrItems = array(
	/*
	
	артикул(int) => array("наименование(string)", стоимость(int), "имя файла в папке img/items/(string)", "имя мини файла в папке img/items/(string)", "описание(text)"
	),
	
	*/

	
);

/*foreach($arrItems as $k => $v)
{
	$sql = "insert into `$table_name` (item_type, item_parent, item_art, item_name, item_price, item_photo, item_text)
	values ('item', 10, $k, '$v[0]', $v[1], '', '$v[4]')";
	$res = mysql_query($sql);
	
	$order = mysql_insert_id();	
	$sql_2 = "update `$table_name` set 
	item_rating = '$order'	where id=$order";
	$res_2 = mysql_query($sql_2);
	//echo $sql;
	
}*/
/*----------------------------------*/


###################################
####		функции				###
###################################
function CreateTableMainMenu()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		item_type		text,
		item_parent		int,
		item_art		int,
		item_name		text,
		item_name_en	text,
		item_price		decimal(11,2),
		item_price_2	decimal(11,2),
		item_photo		int,
		item_text		text,
		item_text_en	text,
		item_tags		text,
		item_show		enum('1', '0') not null,
		item_rating		int,
		item_action		enum('0', '1') not null,
		item_discount	enum('0', '1') not null
	)" ;
	$res = mysql_query($sql);
}

function AddMenuItem($item_type, $item_parent, $item_art = 0, $item_name, $item_name_en, $item_price = 0, $item_price_2 = 0, $item_photo = 0, $item_text, $item_text_en, $item_tags, $item_action = 0, $item_discount = 0)
{
	global $table_name;
	
	if($item_action) $item_action = 1;
	else $item_action = 0; 
	
	if($item_discount) $item_discount = 1;
	else $item_discount = 0; 
	
	$sql = "insert into `$table_name` (item_type, item_parent, item_art, item_name, item_name_en, item_price, item_price_2, item_photo, item_text, item_text_en, item_tags, item_action, item_discount)
	values ('$item_type', '$item_parent', '$item_art', '$item_name', '$item_name_en', '$item_price', '$item_price_2', '$item_photo', '$item_text', '$item_text_en', '$item_tags', '$item_action', '$item_discount')";
	echo $sql;
	$res = mysql_query($sql);
	
	$order = mysql_insert_id();	
	$sql_2 = "update `$table_name` set 
	item_rating = '$order'	where id=$order";
	$res_2 = mysql_query($sql_2);
	return $res;
}

function UpdateMenu($id, $item_parent, $item_art, $item_name, $item_name_en, $item_price = 0, $item_price_2 = 0, $item_photo = 0, $item_text, $item_text_en, $item_tags, $item_action, $item_discount)
{
	global $table_name;
	
	if($item_action) $item_action = 1;
	else $item_action = 0; 
	
	if($item_discount) $item_discount = 1;
	else $item_discount = 0; 
	
	$sql = "update `$table_name` set 
	item_parent='$item_parent',
	item_art='$item_art',
	item_name='$item_name',
	item_name_en='$item_name_en',
	item_price='$item_price',
	item_price_2='$item_price_2',
	item_photo='$item_photo',
	item_text='$item_text',
	item_text_en='$item_text_en',
	item_tags = '$item_tags',
	item_action = '$item_action',
	item_discount = '$item_discount'
	where id=$id";
	$res = mysql_query($sql);
	//echo $sql;
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

/*	изменение порядка следования записей	*/
function MoveItem($id, $direction)
{
	global $table_name;
	
	$order_field = "item_rating";
	
	if($direction == "asc") $arrow = ">";
	elseif($direction == "desc") $arrow = "<";
	
	$sql = "select * from `".$table_name."` where id=".$id;
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "select * from `".$table_name."` where (".$order_field." ".$arrow." ".$row[$order_field]." and item_parent = ".$row['item_parent'].") order by ".$order_field." ".$direction." limit 1 ";
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "update `".$table_name."` set ".$order_field."=".$row_2[$order_field]." where id=".$id;
	//echo $sql."<br>";
	$res = mysql_query($sql);
	
	$sql = "update `".$table_name."` set ".$order_field."=".$row[$order_field]." where id=".$row_2['id'];
	//echo $sql."<br>";
	$res = mysql_query($sql);
}


###################################

###################################
####		^^^функции			###
###################################

CreateTableMainMenu();

if(isset($set_sub_menu))
{
	AddMenuItem("sub_menu", $item_parent, $item_art=0, $item_name, $item_name_en, $item_price=0, $item_price_2=0, $item_photo=0, $item_text='', $item_text_en='', $item_tags, @$item_action, @$item_discount);
}

if(isset($set_item))
{
	AddMenuItem("item", $sub_catalog_parent, $item_art, $item_name, $item_name_en, $item_price, $item_price_2, $item_photo, $item_text, $item_text_en, $item_tags, @$item_action, @$item_discount);
}

if(isset($update_menu) and isset($id))
{
	UpdateMenu($id, $sub_catalog_parent, $item_art, $item_name, $item_name_en, $item_price, $item_price_2, $item_photo, $item_text, $item_text_en, $item_tags, @$item_action, @$item_discount);
}

if(isset($del_menu) and isset($id))
{
	DelMenu($id);
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
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
?>
<fieldset><legend><strong>Добавить подраздел каталога</strong></legend>
<form method=post enctype=multipart/form-data action="?page=catalog_items" name="form2" id="form2"><table>
	<tr>
		<td>Раздел каталога</td>
		<td><select name="item_parent">
		<?
		$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by news_title asc";
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

<fieldset><legend><strong>Добавить позицию</strong></legend>
<form method=post enctype=multipart/form-data action="?page=catalog_items" name="form1" id="form1"><table width=100%>
	<tr>
		<td>Подраздел</td>
		<td><select name="sub_catalog_parent">
		<?
		$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by news_title asc";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			echo "<optgroup label='".$row['news_title']."'>";
			$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
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
		<td>Артикул</td><td><input type="text" name="item_art"  class="admInput" /></td>
	</tr>
	<tr>
		<td>Наименование позиции</td><td><input type="text" name="item_name"  class="admInput" /></td>
	</tr>
	<tr>
		<td>Наименование позиции (eng)</td><td><input type="text" name="item_name_en"  class="admInput" /></td>
	</tr>
	<tr>
		<td>Стоимость упаковки</td><td><input type="text" name="item_price" /></td>
	</tr>
	<tr>
		<td>Стоимость за штуку</td><td><input type="text" name="item_price_2" /></td>
	</tr>
	<tr>
		<td>Картинка</td>
		<td><select name="item_photo" >
			<option value='0' selected>Без картинки
		<?
		$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
		while($row = mysql_fetch_array($r))
		{
			echo "<option value='".$row['id']."'>".$row['name']."\n";
		}
		?>
		</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Продукция</a>")</span></td>
	</tr>
	<tr>
		<td>Теги</td>
		<td><input type="text" name="item_tags"  class="admInput"/></td>
	</tr>
	<tr>
		<td>Участник акции</td>
		<td><input type="checkbox" name="item_action" /></td>
	</tr>
	<tr>
		<td>На товар предоставляется скидка</td>
		<td><input type="checkbox" name="item_discount" /></td>
	</tr>
	<tr>
		<td colspan=2>Описание
		<?
		$editor_text_name = 'item_text';
		$editor_height = 200;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
		?>
		</td>
	</tr>
	<tr>
		<td colspan=2>Описание (eng)
		<?
		$editor_text_name = 'item_text_en';
		$editor_height = 200;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
		?>
		</td>
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
	$sql_3 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id=$id";
	$res_3 = mysql_query($sql_3);
	$row_3 = mysql_fetch_array($res_3);
	
?>
<fieldset><legend><strong>Редактирование позиции</strong></legend>
<form method=post enctype=multipart/form-data action="?page=catalog_items" name="form1" id="form1">
<table width=100%>
	<tr>
		<td>Подраздел</td><td><select name="sub_catalog_parent">
		<?
		if($type == "item_menu")
		{
			$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by news_title asc";
			$res = mysql_query($sql);
			while($row = mysql_fetch_array($res))
			{
				echo "<optgroup label='".$row['news_title']."'>\n";
				$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
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
			$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by news_title asc";
			$res = mysql_query($sql);
			/*while($row = mysql_fetch_array($res))
			{
				echo "<optgroup label='".$row['news_title']."'>\n";
				$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_parent = ".$row['id']." and item_type = 'sub_menu') order by item_rating";
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
		<td>Артикул</td><td><input type="text" name="item_art" value="<?=$row_3['item_art']?>"  class="admInput" /></td>
	</tr>
	<tr>
		<td>Наименование позиции</td><td><input type="text" name="item_name" value="<?=$row_3['item_name']?>"  class="admInput" />
		<input type="hidden" name="id" value="<?=$row_3['id']?>" />
		</td>
	</tr>
	<tr>
		<td>Наименование позиции (eng)</td><td><input type="text" name="item_name_en" value="<?=$row_3['item_name_en']?>"  class="admInput" />
		</td>
	</tr>
	<tr>
		<td>Стоимость упаковки</td><td><input type="text" name="item_price" value="<?=$row_3['item_price']?>" /></td>
	</tr>
	<tr>
		<td>Стоимость за штуку</td><td><input type="text" name="item_price_2" value="<?=$row_3['item_price_2']?>" /></td>
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
		</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Продукция</a>")</span></td>
	</tr>
	<tr>
		<td>Теги</td>
		<td><input type="text" name="item_tags" value="<?=$row_3['item_tags']?>"  class="admInput"/></td>
	</tr>
	<tr>
		<td>Участник акции </td>
		<td><input type="checkbox" name="item_action" <? if($row_3['item_action']) echo "checked"?> />(размер скидки по акции устанавливается в переменной $action_discount в разделе <a href="?page=presets&edit_item&id=13">"Настройки"</a>)</td>
	</tr>
	<tr>
		<td>На товар предоставляется скидка</td>
		<td><input type="checkbox" name="item_discount" <? if($row_3['item_discount']) echo "checked"?> /></td>
	</tr>
	<tr>
		<td colspan=2><fieldset><legend>Описание</legend>
		<?
		$editor_text_edit = $row_3['item_text'];
		$editor_text_name = 'item_text';
		$editor_height = 200;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
		?>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan=2><fieldset><legend>Описание (eng)</legend>
		<?
		$editor_text_edit = $row_3['item_text_en'];
		$editor_text_name = 'item_text_en';
		$editor_height = 200;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
		?>
		</fieldset>
		</td>
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
	<a href="?page=catalog_items&add_menu">Добавить подраздел или позицию</a><br />
<br />
<table cellpadding="5" border="0"><?
	$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by news_title";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		?>
		<tr>
			<th colspan=9><br /><strong><?=$row['news_title']?></strong></th>			
		</tr>
		<tr>
			<th class="smalltext">артикул</th >
			<th class="smalltext" align="center">наименование</th >
			<th class="smalltext" align="center">цена (упак.)</th >
			<th class="smalltext" align="center">цена (шт.)</th >
			<th class="smalltext" align="center">картинка</th >
			<th class="smalltext" align="center">изменить</th >
			<th class="smalltext" align="center">вниз</th >
			<th class="smalltext" align="center">вверх</th >
			<th class="smalltext" align="center">удалить</th >
			
		</tr>
		<?
		$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_type = 'sub_menu' and item_parent = ".$row['id'].") order by item_rating asc";
		$res_2 = mysql_query($sql_2);
		while($row_2 = mysql_fetch_array($res_2))
		{
			?>
			<tr>				
				<td colspan="3"><strong><em><?=$row_2['item_name']?></em></strong></td>
				<td></td>	
				<td align="center"><?
				if($row_2['item_photo'] > 0)
				{
					?><img src='<?=$_ICON["image"]?>' alt="картинка" title="картинка"><?
				}
				?></td>			
				<td align="center"><a href="?page=catalog_items&edit_menu&type=sub_menu&id=<?=$row_2['id']?>"><img src='<?=$_ICON["edit"]?>' alt="edit"></a></td>
				<td align="center"><a href="?page=catalog_items&move&dir=asc&id=<?=$row_2['id']?>"><img src='<?=$_ICON["down"]?>' alt="down"></a></td>
				<td align="center"><a href="?page=catalog_items&move&dir=desc&id=<?=$row_2['id']?>"><img src='<?=$_ICON["up"]?>' alt="up"></a></td>
				<td align="center"><a href="?page=catalog_items&del_menu&id=<?=$row_2['id']?>"><img src='<?=$_ICON["del"]?>' alt="del"></a></td>
			</tr>
			<?
			$sql_3 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_type = 'item' and item_parent = ".$row_2['id'].") order by item_rating asc";
			$res_3 = mysql_query($sql_3);
			while($row_3 = mysql_fetch_array($res_3))
			{
				?>
				<tr>
					<td><?=$row_3['item_art']?></td>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=catalog_items&edit_menu&type=item_menu&id=<?=$row_3['id']?>"><?=$row_3['item_name']?></a><? if($row_3['item_action']){?><img src="/cms9/img/icon_action_small.png" width="20" height="14" class="noBg" /><? }?></td>
					<td align="center"><?=$row_3['item_price']?></td>	
					<td align="center"><?=$row_3['item_price_2']?></td>	
					<td align="center"><?
					if($row_3['item_photo'] > 0)
					{
						?><img src='<?=$_ICON["image"]?>' alt="картинка" title="картинка"><?
					}
					?></td>					
					<td align="center"><a href="?page=catalog_items&edit_menu&type=item_menu&id=<?=$row_3['id']?>"><img src='<?=$_ICON["edit"]?>' alt="edit"></a></td>
					<td align="center"><a href="?page=catalog_items&move&dir=asc&id=<?=$row_3['id']?>"><img src='<?=$_ICON["down"]?>' alt="down"></a></td>
					<td align="center"><a href="?page=catalog_items&move&dir=desc&id=<?=$row_3['id']?>"><img src='<?=$_ICON["up"]?>' alt="up"></a></td>
					<td align="center"><a href="?page=catalog_items&del_menu&id=<?=$row_3['id']?>"><img src='<?=$_ICON["del"]?>' alt="del"></a></td>
				</tr>
				<?
			}
		}
	}
	?></table>
	<br /><br /><a href="?page=catalog_items&add_menu">Добавить подраздел или позицию</a>
	<?
}
?>

</body>
</html>
