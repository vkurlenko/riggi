<?php
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* CMS ВОПРОС-ОТВЕТ / ОТЗЫВЫ   */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "faq.functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";



/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "ref_salon",
	"tableName" 	=> $_VARS['tbl_prefix']."_faq",
	"tableNameCat" 	=> $_VARS['tbl_prefix']."_catalog",
	"userAccess" 	=> array("admin", "editor"),
	"faqType"		=> array(/*"faq" => "Вопрос-ответ", */"ref_salon" => "Отзыв о продукте"),
	"faqSign"		=> array("0" => "0", "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5")
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Вопрос-ответ (отзыв)",
	"TEXT_ADD_ITEM"	=> "Добавить вопрос-ответ (отзыв)",
	"TEXT_EDIT_ITEM"=> "Редактировать вопрос-ответ (отзыв)"		
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
	"id" 			=> "int auto_increment primary key",
	"faqType"		=> "enum('faq', 'ref_salon') not null",
	"faqUserName"	=> "text",
	"faqUserMail"	=> "text",	
	"faqQuestion"	=> "text",
	"faqAnswer"		=> "text",
	"faqText"		=> "text",
	"faqPerson"		=> "text",
	"faqShow"		=> "enum('0','1') not null",
	"faqSign"		=> "enum('0','1','2','3','4','5') not null",
	"faqDate"		=> "datetime not null",
	"faqOrder"		=> "int default '0' not null"
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
	"faqType"	=> array(
						"name"	=> "faqType", 	
						"title" => "Тип сообщения", 	
						"type"	=> "selectFaqType", 		
						"value" => "",
						"arrData" => $_MODULE_PARAM['faqType']),
	"faqPerson"	=> array(
						"name"	=> "faqPerson", 	
						"title" => "Привязка к объекту", 	
						"type"	=> "selectParentId", 		
						"value" => 0,
						"table" => array("table_name" => $_MODULE_PARAM['tableNameCat'], "parent_field" => "item_parent", "order_by" => "item_order", "order_dir" => "ASC", "item_title" => "item_name")),
	
	"faqSign"	=> array(
						"name"	=> "faqSign", 	
						"title" => "Рейтинг", 	
						"type"	=> "selectObjectArr", 		
						"value" => "",
						"arrData" => $_MODULE_PARAM['faqSign']),
	"faqUserName"	=> array(
						"name"	=> "faqUserName", 	
						"title" => "Имя пользователя", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"faqUserMail"	=> array(
						"name"	=> "faqUserMail", 	
						"title" => "E-mail пользователя", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"faqDate"	=> array(
						"name"	=> "faqDate", 	
						"title" => "Дата поступления сообщения", 	
						"type"	=> "inputDate", 		
						"value" => date("Y-m-d"),
						"class" => "inputDate"),
	"faqTime"	=> array(
						"name"	=> "faqTime", 	
						"title" => "Время поступления сообщения", 	
						"type"	=> "inputTime", 		
						"value" => date("H:i").":00",
						"class" => "inputDate"),	
	"faqQuestion"	=> array(
						"name"	=> "faqQuestion", 	
						"title" => "Вопрос (отзыв)", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),	
	"faqAnswer"	=> array(
						"name"	=> "faqAnswer", 	
						"title" => "Ответ", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),							
	"faqShow"		=> array(
						"name"	=> "faqShow", 	
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
	$arrData = delArrayElem($_POST, array("addItem", "id", "faqTime"));
	
	// обработка checkbox'а	
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
	
	$arrData['faqDate'] = $_POST['faqDate']." ".$_POST['faqTime'];
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData["faqOrder"] = mysql_insert_id();
	
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
	
	
	/* !!! обработка рейтинга продукта !!! */
	include_once $_SERVER['DOC_ROOT']."/blocks/functions.php";		
	setRating($_POST['faqPerson']);	
	/* !!! обработка рейтинга продукта !!! */
	
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
	$arrData = delArrayElem($_POST, array("updateItem", "id", "faqTime"));
	
	// обработка checkbox'а
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
		
	$arrData['faqDate'] = $_POST['faqDate']." ".$_POST['faqTime'];
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
	
	/* !!! обработка рейтинга продукта !!! */
	include_once $_SERVER['DOC_ROOT']."/blocks/functions.php";		
	setRating($_POST['faqPerson']);	
	/* !!! обработка рейтинга продукта !!! */
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "faqOrder";
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
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "faqDate", $orderDir = "DESC", $where = 'faqType = "ref_salon"');
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
		
		$date = explode(" ", $row['faqDate'], 2);
		$arrFormFields["id"]["value"]			= $row['id'];
		$arrFormFields["faqType"]["value"]		= $row['faqType'];
		$arrFormFields["faqDate"]["value"]		= $date[0];
		$arrFormFields["faqTime"]["value"]		= $date[1];
		$arrFormFields["faqUserName"]["value"]	= $row['faqUserName'];
		$arrFormFields["faqUserMail"]["value"]	= $row['faqUserMail'];
		$arrFormFields["faqQuestion"]["value"]	= $row['faqQuestion'];
		$arrFormFields["faqAnswer"]["value"]	= $row['faqAnswer'];
		$arrFormFields["faqPerson"]["value"]	= $row['faqPerson'];
		$arrFormFields["faqShow"]["value"]		= $row['faqShow'];
		$arrFormFields["faqSign"]["value"]		= $row['faqSign'];
		
		
		
		
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
//include_once "banners_info.php";
?>


</body>
</html>
