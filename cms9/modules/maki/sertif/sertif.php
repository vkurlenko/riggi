<?php
session_start();
error_reporting(E_ALL);
/*~~~~~~~~~~~~~~~*/
/* СЕРТИФИКАТЫ	 */
/*~~~~~~~~~~~~~~~*/

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "sertif.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
/*include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";*/

check_access(array("admin", "editor"));

$tags = "<strong><a><br><span><img><embed><em>";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$tableName = $_VARS['tbl_prefix']."_sertif";

$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"sertif_name"	=> "text",	// название сертификата
	"sertif_img_1"	=> "int",	// картинка (лицевая сторона) 	
	"sertif_img_2"	=> "int",	// картинка (оборотная сторона) 	
	"sertif_text_1"	=> "text",	// краткое описание
	"sertif_text_2"	=> "text",	// полное описание
	"sertif_period"	=> "text",	// срок действия (текст)
	"sertif_price"	=> "int",	// стоимость (руб.)
	"sertif_salon"	=> "int",	// привязка к салону
	"sertif_service"	=> "int",	// привязка к услуге
	"sertif_active"	=> "enum('0','1') not null"	// активность сертификата
);

// создание новой таблицы БД

$db_Table = new DB_Table();
$db_Table -> debugMode = false;
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// добавленние новой записи
if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	
	// обработка checkbox'а
	switch($arrData['sertif_active']) 
	{
		case "" : 	$arrData['sertif_active'] = 0; break;
		default : 	$arrData['sertif_active'] = 1; break;
	}
	
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
}


// удаление записи
if(isset($del_block) and isset($id))
{
	// параметры запроса на удаление
	$db_Table -> tableWhere = array("id" => $id);
	
	// удаление записи
	$db_Table -> delItem();	
}



// изменение записи
if(isset($updateItem) and isset($id))
{	
	// предварительно удалим ненужные в запросе элементы
	$arrData = delArrayElem($_POST, array("updateItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['sertif_active']) 
	{
		case "" : 	$arrData['sertif_active'] = 0; break;
		default : 	$arrData['sertif_active'] = 1; break;
	}
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

$arrSalon = array();
$sql = "select * from `".$_VARS['tbl_pages_name']."` where p_parent_id = 13 and p_show = '1' order by p_order asc";
$res_salon = mysql_query($sql);
while($row_salon = mysql_fetch_array($res_salon))
{
	$arrSalon[$row_salon['id']] = $row_salon['p_title'];
}
?>


<?
include_once "head.php";
?>
<body>


<?
if(!isset($editItem) && !isset($setItem))
{
	?>
	<fieldset><legend>Сертификаты</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый сертификат</a>
		<?
		GetBlocks();
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый сертификат</a>
	</fieldset>
	<?
}

else
{
	$caption = "Добавить новый сертификат";
	$id = "";
	$sertif_name 	= "";
	$sertif_service = 0;
	$checked_sertif_active = "";
	$sertif_img_1 = $sertif_img_2 = 0;
	$sertif_period = "";
	$sertif_price = 0;
	$sertif_salon = 0;
	$editor_text_edit_1 = "";
	$editor_text_edit_2 = "";
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
		$id = $_GET['id'];
		$res = ReadBlock($id);
		$caption = "Редактирование сертификата ".$res[0]['sertif_name'];
		
		$sertif_name 	= $res[0]['sertif_name'];
		$sertif_service = $res[0]['sertif_service'];
		if($res[0]['sertif_active'] == 1) $checked_sertif_active = " checked ";
		$sertif_img_1 = $res[0]['sertif_img_1'];
		$sertif_img_2 = $res[0]['sertif_img_2'];
		$sertif_period = $res[0]['sertif_period'];
		$sertif_price = $res[0]['sertif_price'];
		$sertif_salon = $res[0]['sertif_salon'];
		$editor_text_edit_1 = $res[0]['sertif_text_1'];
		$editor_text_edit_2 = $res[0]['sertif_text_2'];
		
		
		$submit = array('updateItem', 'Изменить');
	}
	?>
	<fieldset><legend><?=$caption;?></legend>
	
		<form method=post enctype=multipart/form-data action="?page=sertif" name="form1" id="form1">
		<table>
			<tr>
				<td>
					Название сертификата</td><td>
					<input type="text" name="sertif_name" size="40" value="<?=$sertif_name?>" />
					<input type="hidden" name="id" value="<?=$id?>">		
				</td>
			</tr>
			<tr>
				<td>Привязка к услуге</td>
				<td>
					<select name="sertif_service">
					<?
					$sql_3 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where item_type='sub_menu' order by item_rating asc";
					$res_3 = mysql_query($sql_3);
					while($row_3 = mysql_fetch_array($res_3))
					{
						?>
						<optgroup label='<?=$row_3['item_name']?>'>
						<?
						$sql_2 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where (item_parent = ".$row_3['id']." and item_type = 'item') order by item_rating";
						$res_2 = mysql_query($sql_2);
						while($row_2 = mysql_fetch_array($res_2))
						{
							$selected = "";
							if($row_2['id'] == $sertif_service) $selected = " selected ";
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
			<tr>
				<td>Изображение лицевой стороны</td>
				<td>
					<select name="sertif_img_1" >
					<?							
					$r = mysql_query("select * from `photo".$_VARS['env']['alb_sertif']."` order by `id` desc");
					?>
					<option value='0'>Без картинки
					<?
					while($row = mysql_fetch_array($r))
					{
						$selected = "";
						if($sertif_img_1 == $row['id']) $selected = " selected ";
						?><option value='<?=$row['id']?>' <?=$selected?>><?=$row['name']?>
					<?
					}
					?>
					</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_sertif']?>" target="_self">Изображения сертификатов</a>")</span>
					<?
					$pic_width 	= 50;	// заданная ширина итогового изображения
					$pic_height = 50;	// заданная высота итогового изображения
					
					$img_alb_id	= $_VARS['env']['alb_sertif'];	// id альбома в базе
					$img_id	= $sertif_img_1;				// id изображения в базе	
					$pic_align 	= "left";	// способ выравнивания тега <IMG>
					$pic_transform = "resize";
					if($sertif_img_1 > 0)
					{
						include $_SERVER['DOC_ROOT']."/modules/img/image.inc.php";	
					}
					?>
				</td>
			</tr>
			
			<tr>
				<td>Изображение оборотной стороны</td>
				<td>
					<select name="sertif_img_2" >
					<?							
					$r = mysql_query("select * from `photo".$_VARS['env']['alb_sertif']."` order by `id` desc");
					?>
					<option value='0'>Без картинки
					<?
					while($row = mysql_fetch_array($r))
					{
						$selected = "";
						if($sertif_img_2 == $row['id']) $selected = " selected ";
						?><option value='<?=$row['id']?>' <?=$selected?>><?=$row['name']?>
					<?
					}
					?>
					</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_sertif']?>" target="_self">Изображения сертификатов</a>")</span>
					<?
					$img_id	= $sertif_img_2;				// id изображения в базе	
					$pic_align 	= "left";	// способ выравнивания тега <IMG>
					if($sertif_img_2 > 0)
					{
						include $_SERVER['DOC_ROOT']."/modules/img/image.inc.php";	
					}
					?>
				</td>
			</tr>
			
			<tr>
				<td>Период действия</td>
				<td><input type="text" name="sertif_period" value="<?=$sertif_period?>" /></td>
			</tr>
			<tr>
				<td>Стоимость сертификата</td>
				<td><input type="text" name="sertif_price" value="<?=$sertif_price?>" /></td>
			</tr>	
			<tr>
				<td>Привязка к салону</td>
				<td>
					<select name="sertif_salon">
						<?					
						foreach($arrSalon as $k => $v)
						{
							$selected = "";
							if($sertif_salon == $k) $selected = " selected ";
							?><option value="<?=$k?>" <?=$selected?> ><?=$v?></option><?
						}
						?>
					</select>
				</td>
			</tr>		
			<tr>
				<td>Сертификат активен</td>
				<td><input type="checkbox" name="sertif_active" <?=$checked_sertif_active;?> /></td>
			</tr>
	
		</table>
		
		<fieldset><legend>Краткое описание</legend>
			<?
			$editor_text_edit = $editor_text_edit_1;
			$editor_text_name = 'sertif_text_1';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
			?>	
		</fieldset>
		
		<fieldset><legend>Полное описание</legend>
			<?
			$editor_text_edit = $editor_text_edit_2;
			$editor_text_name = 'sertif_text_2';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
			?>	
		</fieldset>
		
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		</form>
	</fieldset>
	<?
}
?>

<?
//include "blocks_info.php";
?> 
</body>
</html>
