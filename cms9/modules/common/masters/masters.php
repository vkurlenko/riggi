<?php
/*~~~~~~~~~~~~~~~*/
/* CMS МАСТЕРА   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "masters.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "masters",
	"tableName" 	=> $_VARS['tbl_prefix']."_masters",
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Мастера",
	"TEXT_ADD_ITEM"	=> "Добавить мастера",
	"TEXT_EDIT_ITEM"=> "Редактировать мастера"		
);
/*~~~~~~~~~~~~~~~~~~~*/
/* /параметры модуля */
/*~~~~~~~~~~~~~~~~~~~*/

check_access($_MODULE_PARAM['userAccess']);

$tags = "<strong><a><br><span><img><embed><em>";


/*~~~~~~~~~~~~~~~~~~~~~~*/
/* структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~*/
$arrTableFields = array(
	"id" 				=> "int auto_increment primary key",
	"master_name"		=> "text",
	"master_spec"		=> "text",
	"master_salon"		=> "text",
	"master_service"	=> "text",
	"master_descr"		=> "text",
	"master_photo"		=> "int default '0' not null",
	"master_alb"		=> "int default '0' not null",
	"master_show"		=> "enum('1', '0') not null",
	"master_order"		=> "int default '0' not null"
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
	"master_name"	=> array(
						"name"	=> "master_name", 	
						"title" => "Имя мастера", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"master_spec"	=> array(
						"name"	=> "master_spec", 	
						"title" => "Специализация", 	
						"type"	=> "selectSpec", 		
						"value" => ""),
	/*"master_salon"	=> array(
						"name"	=> "master_salon", 	
						"title" => "Салон", 	
						"type"	=> "selectSalon", 		
						"value" => "",
						"p_parent_id" => 9),*/
						
	/*"master_service"=> array(
						"name"	=> "master_service", 	
						"title" => "Привязать услуги", 	
						"type"	=> "selectParentId", 	
						"mode" 	=> "multiSelect",	
						"value" => 0,
						"table" => array("table_name" => $_VARS['tbl_prefix']."_catalog", "parent_field" => "item_parent",  "order_by" => "item_order", "order_dir" => "ASC", "item_title" => "item_name")),
	*/"master_descr"	=> array(
						"name"	=> "master_descr", 	
						"title" => "Опыт и карьера", 	
						"type"	=> "textHTML", 		
						"value" => ""),
	"master_photo"	=> array(
						"name"	=> "master_photo", 	
						"title" => "Фото мастера", 	
						"type"	=> "selectPic", 		
						"value" => 0,
						"alb"	=> $_VARS['env']['pic_catalogue_masters']),
	"master_alb"		=> array(
						"name"	=> "master_alb", 	
						"title" => "Альбом картинок по теме", 	
						"type"	=> "selectAlb", 		
						"value" => 0),
	"master_show"		=> array(
						"name"	=> "master_show", 	
						"title" => "Показывать на сайте", 	
						"type"	=> "inputCheckbox", 		
						"value" => true)	
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
	$arrData = delArrayElem($_POST, array("addItem", "id", "master_service"));
	
	// обработка checkbox'а	
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
	
	if(!isset($_POST["master_service"])) $arrData["master_service"] = serialize(array(0 => 'none'));
	else $arrData["master_service"] = serialize($_POST["master_service"]);
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData["master_order"] = mysql_insert_id();
	
	
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
	$arrData = delArrayElem($_POST, array("updateItem", "id", "master_service"));
	
	// обработка checkbox'а
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
		
	if(!isset($_POST["master_service"])) $arrData["master_service"] = serialize(array(0 => 'none'));
	else $arrData["master_service"] = serialize($_POST["master_service"]);
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "master_order";
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
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "master_order", $orderDir = "ASC");
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
		
		
		$arrFormFields["id"]["value"]				= $row['id'];
		$arrFormFields["master_name"]["value"]		= $row['master_name'];
		$arrFormFields["master_spec"]["value"]		= $row['master_spec'];
		/*$arrFormFields["master_salon"]["value"]		= $row['master_salon'];
		$arrFormFields["master_service"]["value"]	= $row['master_service'];*/
		$arrFormFields["master_descr"]["value"]		= $row['master_descr'];
		$arrFormFields["master_photo"]["value"]		= $row['master_photo'];
		$arrFormFields["master_alb"]["value"]		= $row['master_alb'];
		$arrFormFields["master_show"]["value"]		= $row['master_show'];
		
		
		
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
