<?php
/*~~~~~~~~~~~~~~~*/
/* CMS МАСТЕРА   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "spec.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "master_spec",
	"tableName" 	=> $_VARS['tbl_prefix']."_masters_spec",
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Специализации",
	"TEXT_ADD_ITEM"	=> "Добавить специализацию",
	"TEXT_EDIT_ITEM"=> "Редактировать специализацию"		
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
	"id" 		=> "int auto_increment primary key",
	"spec_name"	=> "text",
	"spec_label"=> "text",
	"spec_descr"=> "text",
	"spec_show"	=> "enum('1', '0') not null",
	"spec_order"=> "int default '0' not null"
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
	"spec_name"	=> array(
						"name"	=> "spec_name", 	
						"title" => "Название специализации", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),							
	"spec_descr"	=> array(
						"name"	=> "spec_descr", 	
						"title" => "Описание", 	
						"type"	=> "textHTML", 		
						"value" => ""),
	"spec_show"		=> array(
						"name"	=> "spec_show", 	
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
	
	//метка специализации не должна меняться!!! 
	$arrData['spec_label'] = translit($_POST['spec_name']);
	
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData["spec_order"] = mysql_insert_id();
	
	
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
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
	
	//метка специализации не должна меняться!!! 
	//$arrData['spec_label'] = translit($_POST['spec_name']);	

	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "spec_order";
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
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "spec_order", $orderDir = "ASC");
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
		$arrFormFields["spec_name"]["value"]		= $row['spec_name'];
		$arrFormFields["spec_descr"]["value"]		= $row['spec_descr'];
		$arrFormFields["spec_show"]["value"]		= $row['spec_show'];
		
		
		
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
