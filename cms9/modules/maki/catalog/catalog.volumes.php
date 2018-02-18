<?php
session_start();
include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php";
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

check_access(array("admin", "manager"));

$tableName = $_VARS['tbl_prefix']."_catalog_volumes";
$photo_alb = 12;

include "catalog.functions.php";

$name = "Разделы каталога";

$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"cat_vol_title" => "text",
	"cat_vol_text" 	=> "text",
	"cat_vol_text_2"=> "text",
	"cat_vol_img" 	=> "int",
	"cat_vol_show" 	=> "enum('1', '0') not null"
);


// создание новой таблицы БД

$db_Table = new DB_Table();
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();

if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['cat_vol_show']) 
	{
		case "" : 	$arrData['cat_vol_show'] = 0; break;
		default :   $arrData['cat_vol_show'] = 1; break;
	}
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
}

if(isset($del_news) and isset($id))
{
	// параметры запроса на удаление
	$db_Table -> tableWhere = array("id" => $id);
	
	// удаление записи
	$db_Table -> delItem();	
}

if(isset($updateItem) and isset($id))
{
	// предварительно удалим ненужные в запросе элементы
	$arrData = delArrayElem($_POST, array("updateItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['cat_vol_show']) 
	{
		case "" : 	$arrData['cat_vol_show'] = 0; break;
		default : 	$arrData['cat_vol_show'] = 1; break;
	}
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}
?>

<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="admin.css" type="text/css">
<script type="text/javascript" src="js/jscolor/jscolor.js"></script>
</head>
<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<p align="right"><a href="javascript:history.back();">&laquo;&laquo;&nbsp;Вернуться</a></p>

<?
if(isset($setItem))
{
?>
<form method=post enctype=multipart/form-data action="?page=catalog_volumes" name="form1" id="form1">
	<fieldset>
		<legend><strong>Создать раздел каталога</strong></legend>
	
		
			<table cellpadding="5">
				<tr>
					<td>
						Заголовок раздела каталога</td><td>
						<input type="text" name="cat_vol_title" style="width:400px" />
					</td>
				</tr>
				<tr>
					<td>Картинка</td>
					<td><select name="cat_vol_img" >
					<?
					$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
					echo "<option value='0'>Без картинки\n";
					while($row = mysql_fetch_array($r))
					{
						echo "<option value='".$row['id']."'>".$row['name']."\n";
					}
					?>
					</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Картинки для <?=$name;?></a>")</span>
					</td>
				</tr>
				<tr>
					<td>Показывать на сайте</td>
					<td><input type="checkbox" name="cat_vol_show" checked="checked"><span style="font-size:10px;">()</span></td>
				</tr>
			</table>
	</fieldset>
	
	
	<fieldset><legend><strong>Текст раздела каталога</strong></legend>
	<?
	$editor_text_name 	= 'cat_vol_text';
	$editor_height		= 200;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
	?>
	</fieldset>
	
	<fieldset><legend><strong>Дополнительный текст раздела каталога</strong></legend>
	<?
	$editor_text_name 	= 'cat_vol_text_2';
	$editor_height		= 200;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
	?>
	</fieldset>
	
	<input type=submit name="addItem" value='Создать'  >
</form>	
<?
}

elseif(isset($editItem) and isset($id))
{
	$res = ReadNews($tableName, $id);
	?>
	<form method=post enctype=multipart/form-data action="?page=catalog_volumes" name="form1" id="form1">
	<fieldset>
		<legend><strong>Редактирование раздела каталога</strong></legend>
		
		
		<table cellpadding="5">			
			<tr>
				<td>
					Заголовок раздела каталога</td><td>
					<input type="text" name="cat_vol_title" value="<?=$res[0]['cat_vol_title']?>" style="width:400px" />
					<input type="hidden" name="id" value="<?=$res[0]['id']?>">
				</td>
			</tr>
			<tr>
				<td>Картинка</td>
				<td>
					<select name="cat_vol_img" >
					<?					
					$r=mysql_query("select * from `photo$photo_alb` order by `id` desc ");
					if($res[0]['cat_vol_img'] == 0) echo "<option value='0' selected>Без картинки\n";
					else echo "<option value='0'>Без картинки\n";
					while($row = mysql_fetch_array($r))
					{
						if ($res[0]['cat_vol_img'] == $row['id']) $selected = " selected";
						else $selected = " ";
						echo "<option value='".$row['id']."' ".$selected.">".$row['name']."\n";
					}
					?>
					</select> 
					<span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Картинки для <?=$name;?></a>")</span>
				</td>
			</tr>
			<tr>
				<td>Показывать на сайте</td>
				<td><input type="checkbox" name="cat_vol_show" <? if($res[0]['cat_vol_show'] == '1') echo " checked";?>><span style="font-size:10px;">()</span></td>
			</tr>
		
		</table>		
	</fieldset>
	
	<fieldset><legend><strong>Текст раздела каталога</strong></legend>
	<?
	$editor_text_edit = $res[0]['cat_vol_text'];
	$editor_text_name = 'cat_vol_text';
	$editor_height = 200;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>
	</fieldset>
	
	<fieldset><legend><strong>Дополнительный текст раздела каталога</strong></legend>
	<?
	
	$editor_text_edit = $res[0]['cat_vol_text_2'];
	$editor_text_name = 'cat_vol_text_2';
	$editor_height = 200;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
				
	?>
	</fieldset>
	<input type=submit name="updateItem" value='Изменить'  >
	</form>
	<?
}

else
{
?>
	<fieldset><legend>Разделы каталога</legend>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый раздел</a>
	<?	
		GetMenuPos($tableName);
	?>	
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый раздел</a>
	</fieldset>
	<?
}
?>
</body>
</html>
