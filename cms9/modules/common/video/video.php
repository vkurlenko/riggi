<?php
session_start();
error_reporting(E_ALL);

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "video",
	"tableName" 	=> $_VARS['tbl_prefix']."_video",
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Видео",
	"TEXT_ADD_ITEM"	=> "Добавить ролик",
	"TEXT_EDIT_ITEM"=> "Редактировать ролик"		
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
	"video_title"	=> "text", 		// название
	"video_descr"	=> "text", 		// описание 
	"video_url"		=> "text", 		// источник
	"video_html"	=> "text", 		// источник
	"video_create"	=> "date",		// дата создания
	"video_alb"		=> "int default '0' not null", 	// привязка к альбому
	"video_img"		=> "int default '0' not null", 	// картинка превью
	"video_show"	=> "enum('1', '0') not null",	// показывать
	"video_order"	=> "int default '0' not null"	// сортировка	
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
	"video_title"	=> array(
						"name"	=> "video_title", 	
						"title" => "Название", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"video_show"		=> array(
						"name"	=> "video_show", 	
						"title" => "Показывать на сайте", 	
						"type"	=> "inputCheckbox", 		
						"value" => true),
	"video_descr"	=> array(
						"name"	=> "video_descr", 	
						"title" => "Описание", 	
						"type"	=> "textareaText",
						"class" => "inputText", 		
						"value" => ""),	
	"video_url"	=> array(
						"name"	=> "video_url", 	
						"title" => "Источник", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),	
	"video_html"	=> array(
						"name"	=> "video_html", 	
						"title" => "Код вставки видео", 	
						"type"	=> "textareaText", 
						"class" => "inputText",		
						"value" => ""),	
	"video_create"	=> array(
						"name"	=> "video_create", 	
						"title" => "Дата создания", 	
						"type"	=> "inputDate", 		
						"value" => date("Y-m-d"),
						"class" => "inputDate")/*,
	"video_alb"	=> array(
						"name"	=> "video_alb", 	
						"title" => "Привязка к альбому", 	
						"type"	=> "selectAlb", 		
						"value" => 0),
	"video_img"	=> array(
						"name"	=> "video_img", 	
						"title" => "Картинка к статье", 	
						"type"	=> "selectPic", 		
						"value" => 0,
						"alb"	=> $_VARS['env']['photo_alb_video_preview'])*/
	
	
						
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
	
	// обработка checkbox'а	
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
		
		if($v['name'] == 'video_html')
		{
			$tumbId = copyThumb($arrData[$k]);
			$arrData[$k] = htmlspecialchars($arrData[$k]);
			
		}
	}
	
	
	
	/* сохранение превью */
	
	/*if($_POST['video_html'] != '')
	{
		echo $_POST['video_html'];
		// генерируем ссылку на превьюшку
		$url = getVideoUrl(trim($_POST['video_html']));			
		$th_url = getVideoThumbUrl($url);
		echo $th_url;
	}*/
	
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => mysql_insert_id());
	$arrData["video_order"] = mysql_insert_id();
	$arrData["video_img"] = $tumbId;
	
	
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
		
		if($v['name'] == 'video_html')
		{
			//copyThumb($arrData[$k]);
			$arrData[$k] = htmlspecialchars($arrData[$k]);
		}
	}
		
	
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "video_order";
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
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "video_order", $orderDir = "ASC");
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
//include_once "banners_info.php";
?>


</body>
</html>
