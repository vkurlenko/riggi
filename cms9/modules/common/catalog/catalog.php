<?php
/*~~~~~~~~~~~~~~~*/
/* CMS НОВОСТИ   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "catalog.functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "catalog",
	"tableName" 	=> $_VARS['tbl_prefix']."_catalog",
	"userAccess" 	=> array("admin", "editor"),
	"arrConstr"		=> $_VARS['item_constr'],
	"arrColor1"		=> $_VARS['arrColor1'],
	"arrColor2"		=> $_VARS['arrColor2'],
	"thisId"		=> 0
);

if(isset($_GET['id'])) $_MODULE_PARAM['thisId'] = $_GET['id'];

$_TEXT = array(
	"TEXT_HEAD"		=> "Позиции каталога",
	"TEXT_ADD_ITEM"	=> "Добавить позицию",
	"TEXT_EDIT_ITEM"=> "Редактировать позицию"		
);
/*~~~~~~~~~~~~~~~~~~~*/
/* /параметры модуля */
/*~~~~~~~~~~~~~~~~~~~*/

check_access($_MODULE_PARAM['userAccess']);

$tags = "<strong><a><br><span><img><embed><em>";

$jumpCount = 4;


/*~~~~~~~~~~~~~~~~~~~~~~*/
/* структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~*/
$arrTableFields = array(
	"id" 				=> "int auto_increment primary key",
	"item_type" 		=> "text",						// тип позиции
	"item_parent" 		=> "int default '0' not null",	// родительская позиция
	"item_art" 			=> "text",						// артикул
	"item_name" 		=> "text",						// наименование
	"item_name_2" 		=> "text",						// наименование альтернативное
	"item_price_from"	=> "decimal(11,2) default '0.00' not null",	// цена от
	"item_price_to" 	=> "decimal(11,2) default '0.00' not null", // цена оформления
	/*"item_price_array" 	=> "text", 								// массив цен*/
	"item_price_array" 	=> "decimal(11,2) default '0.00' not null", // стоимость муляжа
	"item_photo" 		=> "int default '0' not null",	// картинка к позиции
	"item_alb" 			=> "int default '0' not null",	// альбом картинок по теме
	"item_text_1" 		=> "text",						// описание краткое
	"item_text_2" 		=> "text",						// описание полное
	"item_tags" 		=> "text",						// теги
	"item_show" 		=> "enum('1', '0') not null",	// показывать на сайте
	"item_show_main" 	=> "enum('1', '0') not null",	// показывать на главной
	"item_order" 		=> "int default '0' not null",	// сортировка
	"item_action" 		=> "enum('0', '1') not null",	// позиция участвует в акции
	"item_discount" 	=> "enum('0', '1') not null",	// на позицию предоставляется скидка
	"item_data"			=> "int default '0' not null",	// доп. информация
	"item_size"			=> "text",						// габариты (общая масса)
	"item_color_1"		=> "text",						// цвет массива
	"item_color_2"		=> "text",						// цвет декора
	"item_constr"		=> "text",						// цвет массива
	"item_material"		=> "text",						// начинка	
	"item_maker"		=> "text",
	"item_rate"			=> "decimal(11,2) default '0.00' not null"
);
/*~~~~~~~~~~~~~~~~~~~~~~~*/
/* /структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* структура формы редактирования записи */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrFormFields = array(
	"id"			=> array(
						"name"	=> "id", 				
						"title" => "id", 					
						"type"	=> "inputHidden", 		
						"value" => ""),
	"item_show"		=> array(
						"name"	=> "item_show", 	
						"title" => "Показывать на сайте", 	
						"type"	=> "inputCheckbox", 		
						"value" => true),	
	/*"item_show_main"=> array(
						"name"	=> "item_show_main", 	
						"title" => "Показывать на главной", 	
						"type"	=> "inputCheckbox", 		
						"value" => false),			*/		
	"item_parent"	=> array(
						"name"	=> "item_parent", 	
						"title" => "Родительская позиция", 	
						"type"	=> "selectParentIdCatalog", 		
						"value" => 0,
						"thisId"=> $_MODULE_PARAM['thisId'],
						"table" => array("table_name" => $_MODULE_PARAM['tableName'], "parent_field" => "item_parent", "order_by" => "item_order", "order_dir" => "ASC", "item_title" => "item_name")),
	
	"item_name"		=> array(
						"name"	=> "item_name", 	
						"title" => "Наименование (во множ.числе)", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
						
	"item_name_2"		=> array(
						"name"	=> "item_name_2", 	
						"title" => "Наименование альтернативное<br>(в един.числе)", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
										
	"item_art"		=> array(
						"name"	=> "item_art", 	
						"title" => "Код", 	
						"type"	=> "inputText", 		
						"value" => 0,
						"class" => "inputText"),
						
	"item_constr"		=> array(
						"name"	=> "item_constr", 	
						"title" => "Форма", 	
						"type"	=> "selectObjectArr", 
						"mode"	=> "multiSelect",		
						"value" => "",
						"arrData" => $_MODULE_PARAM['arrConstr']),
						
	"item_color_1"		=> array(
						"name"	=> "item_color_1", 	
						"title" => "Цвет массива", 	
						"type"	=> "selectObjectArr", 
						"mode"	=> "multiSelect",		
						"value" => "",
						"arrData" => $_MODULE_PARAM['arrColor1']),
						
	"item_color_2"		=> array(
						"name"	=> "item_color_2", 	
						"title" => "Цвет декора", 	
						"type"	=> "selectObjectArr", 
						"mode"	=> "multiSelect",		
						"value" => "",
						"arrData" => $_MODULE_PARAM['arrColor2']),
						
	"item_material"		=> array(
						"name"	=> "item_material", 	
						"title" => "Начинка", 	
						"type"	=> "selectObject", 		
						"value" => "",
						"order" => "mat_order", 
						"order_dir" => "asc",
						"field" => "mat_name",
						"mode"	=> "multiSelect",
						"table" => $_VARS['tbl_prefix']."_catalog_material"),
	"item_size"		=> array(
						"name"	=> "item_size", 	
						"title" => "Габариты", 	
						"type"	=> "selectObject", 		
						"value" => "",
						"order" => "size_order", 
						"order_dir" => "asc",
						"field" => "size_name",
						"mode"	=> "multiSelect",
						"table" => $_VARS['tbl_prefix']."_catalog_size"),
	
	/*"item_maker"		=> array(
						"name"	=> "item_maker", 	
						"title" => "Производитель", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText")	,*/
										
	"item_price_from"	=> array(
						"name"	=> "item_price_from", 	
						"title" => "Цена", 	
						"type"	=> "inputText", 		
						"value" => 0,
						"class" => "inputDate"),
						
	"item_price_to"	=> array(
						"name"	=> "item_price_to", 	
						"title" => "Цена оформления", 	
						"type"	=> "inputText", 		
						"value" => 0,
						"class" => "inputDate"),			
	"item_price_array"	=> array(
						"name"	=> "item_price_array", 	
						"title" => "Стоимость муляжа", 	
						"type"	=> "inputText", 		
						"value" => 500,
						"class" => "inputDate"),
	/*"item_discount"	=> array(
						"name"	=> "item_discount", 	
						"title" => "На позицию действует скидка", 	
						"type"	=> "inputCheckbox", 		
						"value" => false),					
	"item_data"		=> array(
						"name"	=> "item_data", 	
						"title" => "Размер скидки (%)", 	
						"type"	=> "inputText", 		
						"value" => "0",
						"class" => "inputText"),		*/																			
	
	"item_photo"	=> array(
						"name"	=> "item_photo", 	
						"title" => "Картинка к позиции", 	
						"type"	=> "selectPic", 		
						"value" => 0,
						"alb"	=> $_VARS['env']['pic_catalogue_services']),
						
	/*"item_alb"		=> array(
						"name"	=> "item_alb", 	
						"title" => "Альбом картинок по теме", 	
						"type"	=> "selectAlb", 		
						"value" => 0),*/
						
	"item_text_1"	=> array(
						"name"	=> "item_text_1", 	
						"title" => "Краткое описание позиции", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),
	"item_rate"	=> array(
						"name"	=> "item_rate", 	
						"title" => "Рейтинг", 	
						"type"	=> "inputText", 		
						"value" => 0,
						"class" => "inputDate"),
	"item_text_2"	=> array(
						"name"	=> "item_text_2", 	
						"title" => "Нижнее меню", 	
						"type"	=> "textHTML", 		
						"value" => ""),
						
	
						
	/*"item_action"	=> array(
						"name"	=> "item_action", 	
						"title" => "Позиция участвует в акции", 	
						"type"	=> "inputCheckbox", 		
						"value" => false),*/
												
	
						
	/*"item_size"		=> array(
						"name"	=> "item_size", 	
						"title" => "Размеры", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText")*/
						
	
						
	
	
);
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* /структура формы редактирования записи */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




// создание новой таблицы БД
$db_Table = new DB_Table();
$db_Table -> tableName = $_MODULE_PARAM['tableName'];
$db_Table -> tableFields = $arrTableFields;
/*$db_Table -> debugMode = true;*/
$db_Table -> createTestRecord = false;
$db_Table -> create();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// добавленние новой записи
if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	
	// обработка checkbox и select:multiselect	
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
		
		if(isset($v["mode"]) && $v["mode"] == "multiSelect")
		{
			if(!isset($_POST[$k])) $arrData[$k] = serialize(array(0 => 'none'));
			else $arrData[$k] = serialize($_POST[$k]);
		}
	}	
	
	$arrData['item_text_2'] = strip_tags($arrData['item_text_2'], '<ul><li><a>');
	$arrData['item_text_2'] = str_replace('&nbsp;', '', $arrData['item_text_2']);
	
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$sql = "SELECT item_order FROM `".$_VARS['tbl_prefix']."_catalog`
			WHERE 1
			ORDER BY item_order DESC 
			LIMIT 1";
	//echo $sql;
	$res_max = mysql_query($sql);
	$row_max = mysql_fetch_array($res_max);
	
	$arrData["item_order"] = $row_max["item_order"] + 1;
	
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}




// удаление записи
if(isset($delItem) and isset($id))
{
	// параметры запроса на удаление
	$db_Table -> tableWhere = array("id" => $id, "item_parent" => $id);
	
	// удаление записи
	$db_Table -> delItem();	
}



// изменение записи
if(isset($updateItem) and isset($id))
{	
	// предварительно удалим ненужные в запросе элементы
	$arrData = delArrayElem($_POST, array("updateItem", "id"));	
	
	
	// обработка checkbox и select:multiselect	
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
		
		if(isset($v["mode"]) && $v["mode"] == "multiSelect")
		{
			if(!isset($_POST[$k])) $arrData[$k] = serialize(array(0 => 'none'));
			else $arrData[$k] = serialize($_POST[$k]);
		}		
	}
		
	$arrData['item_text_2'] = strip_tags($arrData['item_text_2'], '<ul><li><a>');
	$arrData['item_text_2'] = str_replace('&nbsp;', '', $arrData['item_text_2']);
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	
	$db_Table -> tableOrderField = "item_order";
	$db_Table -> tableWhere = array("id" => $id, "dir" => $dir);
	
	if(isset($jump))
	{
		$db_Table -> tableFieldJump = $jump;
		$db_Table -> tableJumpCount = $jumpCount;
		$db_Table -> reOrderItemJump();	
	}
	else $db_Table -> reOrderItem();	
}
?>


<?
include_once "head.php";
?>


<body>
<style>
tr{vertical-align:top}
td{padding:5px 5px}
input.inputText{width:300px}
input.inputDate{width:auto}
textarea.inputText{width:300px; height:100px}
</style>

<script language="javascript">
$(document).ready(function(){
	/*$("#previewPage").click(function(){
		var urlPreview = $(this).attr("href")+"test/";
		window.open(urlPreview);
		$("#form1").attr("action", urlPreview);
		$("#form1").submit();
		return false
	})*/
	
	$("#previewPage").click(function(){
		/*$.ajax({
		   type: "POST",
		   url: "/cms9/modules/framework/preview.page.php",
		   data: "",
		   success: function(){
			 alert("Data Saved");
		   }
		 });*/
		 
		 return false;
	})
})
</script>

<?
if(!isset($editItem) && !isset($setItem))
{
	?>
	<fieldset><legend><?=$_TEXT['TEXT_HEAD']?></legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'><?=$_TEXT['TEXT_ADD_ITEM']?></a>
		<?
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "item_order", $orderDir = "ASC");
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
		
		foreach($arrFormFields as $k => $v)
		{
			$arrFormFields[$k]["value"] = $row[$k];
		}		
		
		$submit = array('updateItem', 'Сохранить');
	}
	?>
	<fieldset><legend><?=$caption?></legend>
	
		<form method="post" enctype="multipart/form-data" action="" name="form1" id="form1">
		<table>
			<?
			foreach($arrFormFields as $k => $v)
			{
				$elem -> fieldProperty = $v;	
				if($v['type'] == 'inputHidden')
				{
					$elem -> createFormElem();	
				}
				else
				{	
					?>
					<tr>
						<td><?=$v['title']?></td>
						<td>
							<?
							$elem -> createFormElem();	
							?>
						</td>
					</tr>
					<?
				}
			}		
			?>	
		</table>		
		
		
		
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		<!--<input type="submit" name="test" id="previewPage" value="Предварительный просмотр">-->
		
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
