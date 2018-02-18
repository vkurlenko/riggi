<?php
/*~~~~~~~~~~~~~~~*/
/* CMS ВИДЖЕТЫ   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "widgets_functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";


$_MODULE_PARAM = array(
	"name"	=> "widgets",
	"tableName" => $_VARS['tbl_prefix']."_widgets"
);


$_TEXT = array(
	"TEXT_HEAD"		=> "Виджеты",
	"TEXT_ADD_ITEM"	=> "Добавить новый виджет",
	"TEXT_EDIT_ITEM"=> "Редактировать виджет"		
);

check_access(array("admin", "editor"));

$tags = "<strong><a><br><span><img><embed><em>";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"widget_title"	=> "text",						// заголовок виджета
	"widget_sub_title"	=> "text",					// подзаголовок виджета
	"widget_text"	=> "text",						// текст виджета
	"widget_pic"	=> "int",						// картинка
	"widget_link"	=> "text",						// ссылка
	"widget_order"	=> "int",						// сортировка
	"widget_tpl"	=> "text",						// шаблон виджета
	"widget_active"	=> "enum('0','1') not null",	// активность
	"widget_show"	=> "enum('1','0') not null",	// доступен ли для показа
	"widget_data"	=> "text"						// доп. данные
);


$arrFormFields = array(
	"id"			=> array(
						"name"	=> "id", 				
						"title" => "id", 					
						"type"	=> "inputHidden", 		
						"value" => ""),
	"widget_title"	=> array(
						"name"	=> "widget_title", 	
						"title" => "Заголовок виджета", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"widget_sub_title"	=> array(
						"name"	=> "widget_sub_title", 	
						"title" => "Подзаголовок виджета", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"widget_text"	=> array(
						"name"	=> "widget_text", 	
						"title" => "Текст виджета", 	
						"type"	=> "textareaText", 		
						"value" => "test",
						"class" => "inputText"),	
	"widget_pic"	=> array(
						"name"	=> "widget_pic", 	
						"title" => "Картинка", 	
						"type"	=> "selectPic", 		
						"value" => ""),
	"widget_tpl"	=> array(
						"name"	=> "widget_tpl", 	
						"title" => "Шаблон", 	
						"type"	=> "selectTpl", 		
						"value" => ""),
	"widget_link"	=> array(
						"name"	=> "widget_link", 	
						"title" => "Ссылка <br>(пример: http://".$_SERVER['HTTP_HOST'].")", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),						
	"widget_active"	=> array(
						"name"	=> "widget_active", 	
						"title" => "Активен по умолчанию", 	
						"type"	=> "inputCheckbox", 		
						"value" => false),
	"widget_show"	=> array(
						"name"	=> "widget_show", 	
						"title" => "Показывать на сайте", 	
						"type"	=> "inputCheckbox", 		
						"value" => true)		
);





// создание новой таблицы БД
$db_Table = new DB_Table();
$db_Table -> tableName = $_MODULE_PARAM['tableName'];
$db_Table -> tableFields = $arrTableFields;
/*$db_Table -> debugMode = true;*/
$db_Table -> create();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// добавленние новой записи
if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['widget_active']) 
	{
		case "" : 	$arrData['widget_active'] = 0; break;
		default : 	$arrData['widget_active'] = 1; break;
	}
	
	switch(@$arrData['widget_show']) 
	{
		case "" : 	$arrData['widget_show'] = 0; break;
		default : 	$arrData['widget_show'] = 1; break;
	}
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData["widget_order"] = mysql_insert_id();
	
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();
}


// удаление записи
if(isset($delItem) and isset($id))
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
	switch(@$arrData['widget_active']) 
	{
		case "" : 	$arrData['widget_active'] = 0; break;
		default : 	$arrData['widget_active'] = 1; break;
	}
	
	switch(@$arrData['widget_show']) 
	{
		case "" : 	$arrData['widget_show'] = 0; break;
		default : 	$arrData['widget_show'] = 1; break;
	}
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "widget_order";
	$db_Table -> tableWhere = array("id" => $id, "dir" => $dir);
	$db_Table -> reOrderItem();	
}
?>


<?
include_once "head.php";
?>


<body>
<style>
tr{vertical-align:top}
td{padding:10px 5px}
input.inputText{width:300px}
textarea.inputText{width:300px; height:100px}

</style>

<?
if(!isset($editItem) && !isset($setItem))
{
	?>
	<fieldset><legend><?=$_TEXT['TEXT_HEAD']?></legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'><?=$_TEXT['TEXT_ADD_ITEM']?></a>
		<?
		GetItems($_MODULE_PARAM['tableName']);
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'><?=$_TEXT['TEXT_ADD_ITEM']?></a>
	</fieldset>
	<?
}

else
{

	$elem = new FormElement();


	$caption = $_TEXT['TEXT_ADD_ITEM'];
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
	
		
	
	
		$caption = $_TEXT['TEXT_EDIT_ITEM'];
		$row = readItem($id);	
		
		if($row['widget_tpl'] == 'tpl_widget_salon')
		{
			$arrFormFields["widget_data"]	= array(
										"name"	=> "widget_data", 	
										"title" => "Дополнительные данные", 	
										"type"	=> "selectSalon", 		
										"value" => "",
										"p_parent_id" => 9);
			$arrFormFields["widget_data"]["value"]	= $row['widget_data'];
		}
		
		elseif($row['widget_tpl'] == 'tpl_widget_service')
		{
			$arrFormFields["widget_data"]	= array(
						"name"	=> "widget_data", 	
						"title" => "Привязать услуги", 	
						"type"	=> "selectParentId", 	
						"value" => 0,
						"table" => array("table_name" => $_VARS['tbl_prefix']."_catalog", 
										"parent_field" => "item_parent",  
										"order_by" => "item_order", 
										"order_dir" => "ASC", 
										"item_title" => "item_name")
			);
			$arrFormFields["widget_data"]["value"]	= $row['widget_data'];
		}
		
		
		$arrFormFields["id"]["value"]			= $row['id'];
		$arrFormFields["widget_title"]["value"]	= $row['widget_title'];
		$arrFormFields["widget_sub_title"]["value"]	= $row['widget_sub_title'];
		$arrFormFields["widget_text"]["value"]	= $row['widget_text'];
		
		$arrFormFields["widget_pic"]["value"]	= $row['widget_pic'];
		$arrFormFields["widget_tpl"]["value"]	= $row['widget_tpl'];
		$arrFormFields["widget_active"]["value"]= $row['widget_active'];
		$arrFormFields["widget_link"]["value"]	= $row['widget_link'];
		$arrFormFields["widget_show"]["value"]	= $row['widget_show'];
		
		
		$submit = array('updateItem', 'Сохранить');
	}
	?>
	<fieldset><legend><?=$caption?></legend>
	
		<form method="post" enctype="multipart/form-data" action="?page=<?=$_MODULE_PARAM['name']?>" name="form1" id="form1">
		<table>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_title'];			
			?>
			<tr>
				<td><?=$arrFormFields['widget_title']['title']?></td>
				<td>
					<?
					$elem -> createFormElem();	
					
					$elem -> fieldProperty = $arrFormFields['id'];	
					$elem -> createFormElem();					
					?>
				</td>
			</tr>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_sub_title'];			
			?>
			<tr>
				<td><?=$arrFormFields['widget_sub_title']['title']?></td>
				<td>
					<?
					$elem -> createFormElem();	
					?>
				</td>
			</tr>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_tpl'];			
			?>
			<tr>
				<td><?=$arrFormFields['widget_tpl']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			
			<?
			if(isset($arrFormFields['widget_data']))
			{
			$elem -> fieldProperty = $arrFormFields['widget_data'];			
			?>
			<tr>
				<td><?=$arrFormFields['widget_data']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			<?
			}
			?>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_pic'];	
			$elem -> picCatalogue = $_VARS['env']['pic_catalogue_widgets'];		
			?>
			<tr>
				<td><?=$arrFormFields['widget_pic']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_text'];	
			?>
			<tr>
				<td><?=$arrFormFields['widget_text']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_link'];			
			?>
			<tr>
				<td><?=$arrFormFields['widget_link']['title']?></td>
				<td>
					<?					
					$elem -> createFormElem();					
					?>
				</td>
			</tr>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_active'];	
			?>
			<tr>
				<td><?=$arrFormFields['widget_active']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			
			<?
			$elem -> fieldProperty = $arrFormFields['widget_show'];	
			?>
			<tr>
				<td><?=$arrFormFields['widget_show']['title']?></td>
				<td><?=$elem -> createFormElem();?></td>
			</tr>
			
		</table>		
		
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		
		</form>
	</fieldset>
	<?
}
?>

<?
//include "banners_info.php";
?>


</body>
</html>
