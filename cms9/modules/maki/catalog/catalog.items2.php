<?
session_start();

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

check_access(array("admin", "manager"));

$photo_alb = 12;

$tableName = $_VARS['tbl_prefix']."_catalog_items";

$arrTableFields = array(
	"id" 				=> "int auto_increment primary key",
	"item_type" 		=> "text",
	"item_parent" 		=> "int default '0' not null",
	"item_art" 			=> "int default '0' not null",
	"item_name" 		=> "text",
	"item_name_en" 		=> "text",
	"item_price" 		=> "decimal(11,2) default '0.00' not null",
	"item_price_2" 		=> "decimal(11,2) default '0.00' not null",
	"item_photo" 		=> "int default '0' not null",
	"item_text" 		=> "text",
	"item_text_en" 		=> "text",
	"item_tags" 		=> "text",
	"item_show" 		=> "enum('1', '0') not null",
	"item_rating" 		=> "int default '0' not null",
	"item_action" 		=> "enum('0', '1') not null",
	"item_discount" 	=> "enum('0', '1') not null"
);

// создание новой таблицы БД

$db_Table = new DB_Table();
$db_Table -> debugMode = false;
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();

/*----------------------------------*/

/*----------------------------------*/


###################################
####		функции				###
###################################
$arrSalon = array();
$sql = "select * from `".$_VARS['tbl_pages_name']."` where p_parent_id = 13 and p_show = '1' order by p_order asc";
$res = mysql_query($sql);
while($row = mysql_fetch_array($res))
{
	$arrSalon[$row['id']] = $row['p_title'];
}


/*	изменение порядка следования записей	*/
function MoveItem($id, $direction)
{
	global $tableName;
	
	$order_field = "item_rating";
	
	if($direction == "asc") $arrow = ">";
	elseif($direction == "desc") $arrow = "<";
	
	$sql = "select * from `".$tableName."` where id=".$id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "select * from `".$tableName."` where (".$order_field." ".$arrow." ".$row[$order_field]." and item_parent = ".$row['item_parent'].") order by ".$order_field." ".$direction." limit 1 ";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "update `".$tableName."` set ".$order_field."=".$row_2[$order_field]." where id=".$id;
	$res = mysql_query($sql);
	
	$sql = "update `".$tableName."` set ".$order_field."=".$row[$order_field]." where id=".$row_2['id'];
	$res = mysql_query($sql);
}


###################################

###################################
####		^^^функции			###
###################################

//CreateTableMainMenu();

if(isset($addSubVolume))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addSubVolume"));
	$arrData['item_type'] = "sub_menu";
	
	// обработка checkbox'а
	switch(@$arrData['item_action']) 
	{
		case "" : 	$arrData['item_action'] = 0; break;
		default :   $arrData['item_action'] = 1; break;
	}
	
	switch(@$arrData['item_discount']) 
	{
		case "" : 	$arrData['item_discount'] = 0; break;
		default :   $arrData['item_discount'] = 1; break;
	}
		
	$db_Table -> tableData = $arrData;
	
	$db_Table -> addItem();	
	
	unset($arrData);
	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData['item_rating'] = mysql_insert_id();
	$db_Table -> tableData = $arrData;
	
	$db_Table -> updateItem();	
	
	
}

if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	$arrData['item_type'] = "item";
	
	// обработка checkbox'а
	switch(@$arrData['item_action']) 
	{
		case "" : 	$arrData['item_action'] = 0; break;
		default :   $arrData['item_action'] = 1; break;
	}
	
	switch(@$arrData['item_discount']) 
	{
		case "" : 	$arrData['item_discount'] = 0; break;
		default :   $arrData['item_discount'] = 1; break;
	}
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();		
	unset($arrData);
	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData['item_rating'] = mysql_insert_id();
	$db_Table -> tableData = $arrData;
	
	$db_Table -> updateItem();	
}

if(isset($updateItem) and isset($id))
{
	// предварительно удалим ненужные в запросе элементы
	$arrData = delArrayElem($_POST, array("updateItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['item_action']) 
	{
		case "" : 	$arrData['item_action'] = 0; break;
		default :   $arrData['item_action'] = 1; break;
	}
	
	switch(@$arrData['item_discount']) 
	{
		case "" : 	$arrData['item_discount'] = 0; break;
		default :   $arrData['item_discount'] = 1; break;
	}
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($delItem) and isset($id))
{
	// параметры запроса на удаление
	$db_Table -> tableWhere = array("id" => $id);
	
	// удаление записи
	$db_Table -> delItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
}

?>

<?
include_once "head.php";
?>

<body>
<?
if(!isset($edit_menu) && !isset($add_menu))
{
	?>
	<fieldset><legend>Позиции каталога</legend>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_menu"><img src='<?=$_ICON["add_item"]?>'>Добавить подраздел или позицию</a>
	
	<table cellpadding="4" border="0">
	<?
	$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by cat_vol_title";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
		?>
		<tr>
			<th colspan=9><br /><strong><?=$row['cat_vol_title']?></strong></th>			
		</tr>
		<tr>
			<!--<th class="smalltext">артикул</th >-->
			<th class="smalltext" align="center">наименование</th >
			<th class="smalltext" align="center">цена</th >
			<th class="smalltext" align="center">цены в салонах</th >
			<th class="smalltext" align="center">акция в салонах</th >
			<th class="smalltext" align="center">галерея</th >
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
				<td align="center"><a href="?page=catalog_items&delItem&id=<?=$row_2['id']?>"><img src='<?=$_ICON["del"]?>' alt="del"></a></td>
			</tr>
			<?
			$sql_3 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_type = 'item' and item_parent = ".$row_2['id'].") order by item_rating asc";
			$res_3 = mysql_query($sql_3);
			while($row_3 = mysql_fetch_array($res_3))
			{
				?>
				<tr>
					<!--<td><?=$row_3['item_art']?></td>-->
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=catalog_items&edit_menu&type=item_menu&id=<?=$row_3['id']?>"><?=$row_3['item_name']?></a><? if($row_3['item_action']){?><img src="/cms9/img/icon_action_small.png" width="20" height="14" class="noBg" /><? }?></td>
					<td align="center"><?=$row_3['item_price']." - ".$row_3['item_price_2']?></td>	
					<td align="center"><a href="?page=catalog_salon&id=<?=$row_3['id']?>">Цены в салонах</a></td>					
					<td align="center">
					<?
					// если проводится акция, то покажем кол-во салонов, где она проводится
					$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_salon` where item_id=".$row_3['id'];
					//echo $sql;
					$res = mysql_query($sql);
					if($res && mysql_num_rows($res) > 0)
					{
						$row = mysql_fetch_array($res);
						
						$list_action = unserialize($row['item_salon_action']);
						$list_active = unserialize($row['item_salon_active']);
						$i = 0;
						$title = "";
						foreach($list_action  as $k => $v)					
						{
							if($v == 1)
							{
								if($list_active[$k] == 1)
								{
									$i++;
									$title .= $arrSalon[$k]." \n ";
								}
								
							} 
						}
						if($i > 0)
						{
							?><a style="padding:5px; border:#DDDDDD 1px solid" href="?page=catalog_salon&id=<?=$row_3['id']?>" title="<?=$title?>"><?=$i?></a><?
						}
					}
					
					?> 	
					</td>
					<td align="center"><?
					if($row_3['item_photo'] > 0)
					{
						?><a href="?page=photo&zhanr=<?=$row_3['item_photo']?>"><img src='<?=$_ICON["image"]?>' alt="картинка" title="картинка"></a><?
					}
					?></td>					
					<td align="center"><a href="?page=catalog_items&edit_menu&type=item_menu&id=<?=$row_3['id']?>"><img src='<?=$_ICON["edit"]?>' alt="edit"></a></td>
					<td align="center"><a href="?page=catalog_items&move&dir=asc&id=<?=$row_3['id']?>"><img src='<?=$_ICON["down"]?>' alt="down"></a></td>
					<td align="center"><a href="?page=catalog_items&move&dir=desc&id=<?=$row_3['id']?>"><img src='<?=$_ICON["up"]?>' alt="up"></a></td>
					<td align="center"><a href="javascript:if (confirm('Удалить раздел?')){document.location='?page=catalog_items&delItem&id=<?=$row_3['id']?>'}"><img src='<?=$_ICON["del"]?>' alt="del"></a></td>
				</tr>
				<?
			}
		}
	}
	?></table>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_menu"><img src='<?=$_ICON["add_item"]?>'>Добавить подраздел или позицию</a>
	</fieldset>
	<?
}

else
{
	$caption = "Добавить позицию";
	$id = "";
	$item_art = 0;
	$item_name = $item_name_en = "";
	$item_price = $item_price_2 = '0.00';
	$item_tags = "";
	$item_parent = 0;
	$item_photo = -1;
	$item_type = "item";
	$item_action = $item_discount = false;
	$editor_text_edit_ru = $editor_text_edit_en = "";
	$submit = array('addItem', 'Создать');
	
	if(isset($edit_menu) and isset($id))
	{
		$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id=".$_GET['id'];
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		
		$caption = "Изменить позицию";
		$id = $_GET['id'];
		$item_art = $row['item_art'];
		$item_name = $row['item_name'];
		$item_name_en = $row['item_name_en'];
		$item_price = $row['item_price'];
		$item_price_2 = $row['item_price_2'];
		$item_tags = $row['item_tags'];
		$item_parent = $row['item_parent'];
		$item_photo = $row['item_photo'];
		$item_type = $row['item_type'];
		
		if($row['item_action'] == '1') $item_action = " checked ";
		if($row['item_discount'] == '1') $item_discount = " checked ";
				
		$editor_text_edit_ru = $row['item_text']; 
		$editor_text_edit_en = $row['item_text_en']; 
		$submit = array('updateItem', 'Сохранить');
	}
	
	?>
	<fieldset>
		<legend><strong>Добавить подраздел каталога</strong></legend>
		<form method="post" enctype="multipart/form-data" action="?page=catalog_items" name="form2" id="form2"><table>
			<tr>
				<td>Раздел каталога</td>
				<td><select name="item_parent">
				<?
				$sql_cat = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by cat_vol_title asc";
				$res_cat = mysql_query($sql_cat);
				while($row_cat = mysql_fetch_array($res_cat))
				{
					echo "<option value='".$row_cat['id']."'>".$row_cat['cat_vol_title']."</option>";
				}
				?>
				</select></td>
			</tr>
			<tr>
				<td>Наименование подраздела</td><td><input type="text" name="item_name" class="admInput" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="addSubVolume" value="Добавить" /></td>
			</tr>
		</table>
		</form>
	</fieldset>

	
	
	<fieldset>
		<legend><strong><?=$caption?></strong></legend>
	<form method="post" enctype="multipart/form-data" action="?page=catalog_items" name="form1" id="form1"><table width=100%>

		<input type="hidden" name="id" value="<?=$id?>" />
		<?
		if($item_type == "item")
		{
		?>
		<tr>
			<td>Подраздел</td>
			<td>
				
				<select name="item_parent">
				<?
				$sql_3 = "select * from `".$_VARS['tbl_prefix']."_catalog_volumes` where 1 order by cat_vol_title asc";
				$res_3 = mysql_query($sql_3);
				while($row_3 = mysql_fetch_array($res_3))
				{
					?>
					<optgroup label='<?=$row_3['cat_vol_title']?>'>
					<?
					$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_parent = ".$row_3['id']." and item_type = 'sub_menu') order by item_rating";
					$res_2 = mysql_query($sql_2);
					while($row_2 = mysql_fetch_array($res_2))
					{
						$selected = "";
						if($row_2['id'] == $item_parent) $selected = " selected ";
						?>
						<option value='<?=$row_2['id']?>' <?=$selected?>><?=$row_2['item_name']?></option>
						<?
					}
					?>
					</optgroup>
				<?
				}
				?>
				</select>	
			</td>
		</tr>
		<?
		}
		?>
		<tr>
			<td>Артикул</td>
			<td><input type="text" name="item_art"  class="admInput" value="<?=$item_art?>" /></td>
		</tr>
		<tr>
			<td>Наименование позиции</td>
			<td><input type="text" name="item_name"  class="admInput" value="<?=$item_name?>" /></td>
		</tr>
		<?
		if($_VARS['multi_lang'])
		{
		?>
		<tr>
			<td>Наименование услуги (eng)</td>
			<td><input type="text" name="item_name_en"  class="admInput" value="<?=$item_name_en?>" /></td>
		</tr>
		<?
		}
		?>
		<tr>
			
			<?
			if($item_type == "sub_menu")
			{
			?>
			<td>Картинка</td>
				<td>
				<select name="item_photo">
					<option value='0' selected>Без картинки
					<?
					$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
					while($row = mysql_fetch_array($r))
					{
						$selected = "";
						if($row['id'] == $item_photo) $selected = " selected ";
					
						?>
						<option value='<?=$row['id']?>' <?=$selected?>><?=$row['name']?>
						<?
					}
					?>
				</select>
				<?
				$pic_width 	= 50;	// заданная ширина итогового изображения
				$pic_height = 50;	// заданная высота итогового изображения
				
				$img_alb_id	= $photo_alb;	// id альбома в базе
				$img_id	= $item_photo;				// id изображения в базе	
				$pic_align 	= "left";	// способ выравнивания тега <IMG>
				if($item_photo > 0)
				{
					include $_SERVER['DOC_ROOT']."/modules/img/image.inc.php";	
				}
				?>
				<?
				}
				else
				{
				?>
				<td>Фотогалерея</td>
				<td>
				<select name="item_photo">
					<?	
					$check_val = $item_photo;
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
				</td>
				<?
				}
				?>
				<!--<span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Продукция</a>")</span>-->
			</td>
		</tr>
		<?
		if($item_type == "item")
		{
		?>
		<tr>
			<td>Стоимость услуги по умолчанию</td>
			<td>от <input type="text" name="item_price" value="<?=$item_price?>" /> до <input type="text" name="item_price_2" value="<?=$item_price_2?>" /></td>
		</tr>
		<?
		}
		?>
		<?
		if(isset($edit_menu) && $item_type == "item")
		{
		?>
		<tr>
			<td>Стоимость в салонах</td>
			<td>
				<a href="?page=catalog_salon&id=<?=$id?>">редактировать</a>
			</td>
		</tr>
		<?
		}
		?>
		<!--<tr>
			<td>Стоимость за штуку</td>
			<td><input type="text" name="item_price_2" value="<?=$item_price_2?>" /></td>
		</tr>-->
		<!--<tr>
			<td>Участник акции</td>
			<td><input type="checkbox" name="item_action" <?=$item_action?> /></td>
		</tr>-->
		<!--<tr>
			<td>На услугу предоставляется сертификат</td>
			<td><input type="checkbox" name="item_discount" <?=$item_discount?> /></td>
		</tr>-->
		
		<!--<tr>
			<td>Теги</td>
			<td><input type="text" name="item_tags"  class="admInput" value="<?=$item_tags?>"/></td>
		</tr>-->
		
		<tr>
			<td colspan=2>Описание
			<?
			$editor_text_edit = $editor_text_edit_ru;
			$editor_text_name = 'item_text';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";				
			?>
			</td>
		</tr>
		<?
		if($_VARS['multi_lang'])
		{
		?>
		<tr>
			<td colspan=2>Описание (eng)
			<?
			$editor_text_edit = $editor_text_edit_en;
			$editor_text_name = 'item_text_en';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
			?>
			</td>
		</tr>
		<?
		}
		?>
		<tr>
			<td colspan="2">
				<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
			</td>
		</tr>
	</table>
	</form>
	</fieldset>
	<?
}
?>
</body>
</html>
