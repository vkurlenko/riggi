<?php
session_start();
error_reporting(E_ALL);
/*~~~~~~~~~~~~~~~*/
/* CMS ИНФОБЛОКИ */
/*~~~~~~~~~~~~~~~*/

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "blocks_functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
/*include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";*/

check_access(array("admin", "editor"));

$tags = "<strong><a><br><span><img><embed><em>";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$tableName = $_VARS['tbl_iblocks'];

$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"block_marker"	=> "text",
	"block_name"	=> "text",	
	"block_text"	=> "text",
	"block_text_en"	=> "text",
	"block_tpl"		=> "text",
	"block_html"	=> "enum('0','1') not null",
	"block_show"	=> "enum('1','0') not null"
);

// создание новой таблицы БД

$db_Table = new DB_Table();
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
	switch(@$arrData['block_html']) 
	{
		case "" : 	$arrData['block_html'] = 0; break;
		default : 
					$arrData['block_html'] 		= 1; 
					$arrData['block_text'] 		= strip_tags($arrData['block_text'], $tags);
					if($_VARS['multi_lang']) $arrData['block_text_en'] 	= strip_tags($arrData['block_text_en'], $tags);
					break;
	}
	
	switch(@$arrData['block_show']) 
	{
		case "" : 	$arrData['block_show'] = 0; break;
		default : 	$arrData['block_show'] = 1; 					
					break;
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
	switch(@$arrData['block_html']) 
	{
		case "" : 	$arrData['block_html'] = 0; break;
		default : 	$arrData['block_html'] = 1; 
					$arrData['block_text'] 		= strip_tags($arrData['block_text'], $tags);
					if($_VARS['multi_lang']) $arrData['block_text_en'] 	= strip_tags($arrData['block_text_en'], $tags);
				break;
	}
	
	switch(@$arrData['block_show']) 
	{
		case "" : 	$arrData['block_show'] = 0; break;
		default : 	$arrData['block_show'] = 1; 					
					break;
	}
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
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
	<fieldset><legend>Информационные блоки</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый инфоблок</a>
		<?
		GetBlocks();
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый инфоблок</a>
	</fieldset>
	<?
}

else
{
	$caption = "Добавить новый инфоблок";
	$id = "";
	$block_name 	= "";
	$block_marker 	= "iblock_";
	$checked_block_html = "";	
	$checked_block_show = "";
	$block_tpl_checked = "";
	$editor_text_edit_ru = "";
	$editor_text_edit_en = "";
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
		$id = $_GET['id'];
		$res = ReadBlock($id);
		$caption 		= "Редактирование блока ".$res[0]['block_name'];
		$block_name 	= $res[0]['block_name'];
		$block_marker 	= $res[0]['block_marker'];		
		if($res[0]['block_html'] == 1) $checked_block_html = " checked ";
		if($res[0]['block_show'] == 1) $checked_block_show = " checked ";		
		$editor_text_edit_ru = $res[0]['block_text'];
		if($_VARS['multi_lang']) $editor_text_edit_en = $res[0]['block_text_en'];
		$block_tpl_checked = $res[0]['block_tpl'];
		$submit = array('updateItem', 'Изменить');
	}
	?>
	<fieldset><legend><?=$caption;?></legend>
	
		<form method=post enctype=multipart/form-data action="?page=blocks" name="form1" id="form1">
		<table>
			<tr>
				<td>
					Название блока</td><td>
					<input type="text" name="block_name" size="40" value="<?=$block_name?>" />
					<input type="hidden" name="id" value="<?=$id?>">		
				</td>
			</tr>
			<tr>
				<td>
					Маркер блока</td><td>
					<input type="text" name="block_marker" size="40" value="<?=$block_marker?>" />
				</td>
			</tr>
			
			<tr>
				<td>
					Шаблон блока</td><td>
					<select name="block_tpl">
						<option value=''>Без шаблона</option>
						<?
						$sql = "select * from `".$_VARS['tbl_prefix']."_templates` where 1";
						$res3 = mysql_query($sql);
						  
						while($row3 = mysql_fetch_array($res3))
						{					  	
							if($block_tpl_checked == $row3['tpl_marker'])
							{
								$sel1 = " selected='selected' ";
							}
							else $sel1 = "";
							?>
							<option <?=$sel1;?> value='<?=$row3['tpl_marker'];?>'  ><?=$row3['tpl_name']?></option>
							<?
						}
						?>
					</select>
						
				</td>
			</tr>
			
			<tr>
				<td>Показывать блок</td>
				<td><input type="checkbox" name="block_show" <?=$checked_block_show;?> /></td>
			</tr>
			<tr>
				<td>Вырезать все HTML-теги</td>
				<td><input type="checkbox" name="block_html" <?=$checked_block_html;?> ><span style="font-size:10px;">(кроме <?=htmlspecialchars($tags)?>)</span></td>
			</tr>
			
		</table>
		
		<fieldset><legend>Текст блока</legend>
			<?
			$editor_text_edit = $editor_text_edit_ru;
			$editor_text_name = 'block_text';
			$editor_height = 400;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
			?>	
		</fieldset>
		
		<?
		if($_VARS['multi_lang'] == true)
		{
		?> 
		<fieldset><legend>Текст блока (eng)</legend>
			<?
			$editor_text_edit = $editor_text_edit_en;
			$editor_text_name = 'block_text_en';
			$editor_height = 400;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
			?>	
		</fieldset>
		<?
		}
		?>
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		</form>
	</fieldset>
	<?
}
?>

<?
include "blocks_info.php";
?> 
</body>
</html>
