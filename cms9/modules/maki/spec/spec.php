<?php
session_start();
error_reporting(E_ALL);
/*~~~~~~~~~~~~~~~*/
/* СЕРТИФИКАТЫ	 */
/*~~~~~~~~~~~~~~~*/

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "spec.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
/*include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";*/

check_access(array("admin", "editor"));

$tags = "<strong><a><br><span><img><embed><em>";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$tableName = $_VARS['tbl_prefix']."_spec";

$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"spec_date"		=> "date",	// дата спецпредложения
	"spec_title"	=> "text",	// название спецпредложения	
	"spec_title_2"	=> "text",	// краткое название спецпредложения	
	"spec_img"		=> "int",	// картинка спецпредложения
	"spec_text"		=> "text",	// картинка спецпредложения
	"spec_salon"	=> "int",	// привязка к салону
	"spec_service"	=> "int",	// привязка к услуге
	"spec_order"	=> "int",	// сортировка
	"spec_active"	=> "enum('0','1') not null",	// активность спецпредложения
	"spec_on_main"	=> "enum('0','1') not null"
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
	switch(@$arrData['spec_active']) 
	{
		case "" : 	$arrData['spec_active'] = 0; break;
		default : 	$arrData['spec_active'] = 1; break;
	}
	
	switch(@$arrData['spec_on_main']) 
	{
		case "" : 	$arrData['spec_on_main'] = 0; break;
		default : 	$arrData['spec_on_main'] = 1; break;
	}
	
	
	
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData['spec_order'] = mysql_insert_id();
	$db_Table -> tableData = $arrData;
	
	$db_Table -> updateItem();	
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
	switch(@$arrData['spec_active']) 
	{
		case "" : 	$arrData['spec_active'] = 0; break;
		default : 	$arrData['spec_active'] = 1; break;
	}
	
	switch(@$arrData['spec_on_main']) 
	{
		case "" : 	$arrData['spec_on_main'] = 0; break;
		default : 	$arrData['spec_on_main'] = 1; break;
	}
	
	
	
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
	
	
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
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
	<fieldset><legend>Спецпредложения</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новое спецпредложение</a>
		<?
		GetBlocks();
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новое спецпредложение</a>
	</fieldset>
	<?
}

else
{
	$caption = "Добавить новое спецпредложение";
	$id = "";
	$spec_title = $spec_title_2 =  "";
	$spec_date = date("Y")."-".date("m")."-".date("d");
	$spec_img = 0;
	$editor_text_edit = "";
	$spec_service = 0;
	$checked_spec_active = "";
	$checked_spec_on_main = "";
	
	$spec_salon = 0;
	
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
		$id = $_GET['id'];
		$res = ReadBlock($id);
		$caption = "Редактирование спецпредложения ".$res[0]['spec_title'];
		
		$spec_title 	= $res[0]['spec_title'];
		$spec_title_2 	= $res[0]['spec_title_2'];
		$spec_date = $res[0]['spec_date'];
		$spec_img = $res[0]['spec_img'];
		$editor_text_edit = $res[0]['spec_text'];
		$spec_service = $res[0]['spec_service'];
		if($res[0]['spec_active'] == 1) $checked_spec_active = " checked ";
		if($res[0]['spec_on_main'] == 1) $checked_spec_on_main = " checked ";
		
		$spec_salon = $res[0]['spec_salon'];		
		
		$submit = array('updateItem', 'Изменить');
	}
	?>
	<fieldset><legend><?=$caption;?></legend>
	
		<form method=post enctype=multipart/form-data action="?page=spec" name="form1" id="form1">
		<table>
			<tr>
				<td>
					Название спецпредложения</td><td>
					<input type="text" name="spec_title" size="40" value="<?=$spec_title?>" />
					<input type="hidden" name="id" value="<?=$id?>">		
				</td>
			</tr>
			<tr>
				<td>
					Краткое название спецпредложения</td><td>
					<input type="text" name="spec_title_2" size="40" value="<?=$spec_title_2?>" />
					<span style="font-size:10px;">(используется как текст на баннере на главной)</span>
					
				</td>
			</tr>
			<tr>
				<td>
					Дата размещения спецпредложения</td><td>
					<input type="text" name="spec_date" size="40" value="<?=$spec_date?>" />					
				</td>
			</tr>
			<tr>
				<td>Картинка к спецпредложению</td>
				<td>
					<select name="spec_img" >
					<?							
					$r = mysql_query("select * from `photo".$_VARS['env']['alb_spec']."` order by `id` desc");
					?>
					<option value='0'>Без картинки
					<?
					while($row = mysql_fetch_array($r))
					{
						$selected = "";
						if($spec_img == $row['id']) $selected = " selected ";
						?><option value='<?=$row['id']?>' <?=$selected?>><?=$row['name']?>
					<?
					}
					?>
					</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_spec']?>" target="_self">Картинки для спецпредложений</a>")</span>
					<?
					$pic_width 	= 50;	// заданная ширина итогового изображения
					$pic_height = 50;	// заданная высота итогового изображения
					
					$img_alb_id	= $_VARS['env']['alb_spec'];	// id альбома в базе
					$img_id	= $spec_img;				// id изображения в базе	
					$pic_align 	= "left";	// способ выравнивания тега <IMG>
					$pic_transform = "crop";
					if($spec_img > 0)
					{
						include $_SERVER['DOC_ROOT']."/modules/img/image.inc.php";	
					}
					?>
				</td>
			</tr>
			<tr>
				<td>Привязка к услуге</td>
				<td>
					<select name="spec_service">
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
							if($row_2['id'] == $spec_service) $selected = " selected ";
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
				<td>Привязка к салону</td>
				<td>
					<select name="spec_salon">
						<?					
						foreach($arrSalon as $k => $v)
						{
							$selected = "";
							if($spec_salon == $k) $selected = " selected ";
							?><option value="<?=$k?>" <?=$selected?> ><?=$v?></option><?
						}
						?>
					</select>
				</td>
			</tr>		
			<tr>
				<td>Спецпредложение активно</td>
				<td><input type="checkbox" name="spec_active" <?=$checked_spec_active;?> /></td>
			</tr>
			<tr>
				<td>Спецпредложение на главную справа</td>
				<td><input type="checkbox" name="spec_on_main" <?=$checked_spec_on_main;?> /></td>
			</tr>
			
	
		</table>
		
		<fieldset><legend>Описание спецпредложения</legend>
			<?
			$editor_text_edit = $editor_text_edit;
			$editor_text_name = 'spec_text';
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
