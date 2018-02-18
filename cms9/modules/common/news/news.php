<?php
/*~~~~~~~~~~~~~~~*/
/* CMS НОВОСТИ   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "news.functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
if(!isset($_CAT)) $_CAT = "news";



$_CAT_PARAM = array(
	"news"		=> "События",
	"actions"	=> "Статьи"/*,
	"conf"		=> "Конференции"*/	
);

$_MODULE_PARAM = array(
	"name"			=> "news",
	"tableName" 	=> $_VARS['tbl_prefix']."_news",
	"param"			=> $_CAT,
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> $_CAT_PARAM[$_CAT],
	"TEXT_ADD_ITEM"	=> "Добавить статью",
	"TEXT_EDIT_ITEM"=> "Редактировать статью"		
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
	"id"			=> "int auto_increment primary key",
	"news_cat" 		=> "text",			// категория статьи
	"news_title" 	=> "text",			// заголовок
	"news_title_eng"	=> "text",			// заголовок
	"news_date"		=> "datetime not null",	// дата публикации	
	"news_text_1"	=> "text",			// текст 1
	"news_text_2" 	=> "text",			// текст 2
	"news_text_3" 	=> "text",			// текст 3
	"news_text_1_eng"	=> "text",			// текст 1
	"news_text_2_eng" 	=> "text",			// текст 2
	"news_text_3_eng" 	=> "text",			// текст 3
	"news_img" 		=> "int default 0",	// картинка
	"news_alb" 		=> "int",			// альбом картинок по теме
	"news_mark" 	=> "enum('0', '1') not null",	// пометить как архивную
	"news_show" 	=> "enum('1', '0') not null",	// показывать на сайте	
	"news_src"		=> "text"			// ссылка на внешний ресурс
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
	"news_cat"	=> array(
						"name"	=> "news_cat", 	
						"title" => "Категория статьи", 	
						"type"	=> "inputHidden", 		
						//"type"	=> "inputText", 		
						"value" => $_CAT,
						"class" => "inputText"),
	"news_title"	=> array(
						"name"	=> "news_title", 	
						"title" => "Заголовок статьи", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"news_title_eng"	=> array(
						"name"	=> "news_title_eng", 	
						"title" => "Заголовок статьи (eng)", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"news_date"	=> array(
						"name"	=> "news_date", 	
						"title" => "Дата публикации", 	
						"type"	=> "inputDate", 		
						"value" => date("Y-m-d"),
						"class" => "inputDate"),
	"news_time"	=> array(
						"name"	=> "news_time", 	
						"title" => "Время публикации", 	
						"type"	=> "inputTime", 		
						"value" => date("H:i").":00",
						"class" => "inputDate"),	
	"news_img"	=> array(
						"name"	=> "news_img", 	
						"title" => "Картинка к статье", 	
						"type"	=> "selectPic", 		
						"value" => 0,
						"alb"	=> $_VARS['env']['photo_alb_news']),
	"news_alb"	=> array(
						"name"	=> "news_alb", 	
						"title" => "Альбом картинок по теме", 	
						"type"	=> "selectAlb", 		
						"value" => 0),
	"news_mark"	=> array(
						"name"	=> "news_mark", 	
						"title" => "Пометить как архивную", 	
						"type"	=> "inputCheckbox", 		
						"value" => false),
	"news_show"	=> array(
						"name"	=> "news_show", 	
						"title" => "Показывать на сайте", 	
						"type"	=> "inputCheckbox", 		
						"value" => true),
	"news_src"	=> array(
						"name"	=> "news_src", 	
						"title" => "Ссылка на внешний ресурс", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"news_text_1"	=> array(
						"name"	=> "news_text_1", 	
						"title" => "Краткий текст", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),
	"news_text_2"	=> array(
						"name"	=> "news_text_2", 	
						"title" => "Полный текст", 	
						"type"	=> "textHTML", 		
						"value" => "",
						"class" => ""),
	"news_text_3"	=> array(
						"name"	=> "news_text_3", 	
						"title" => "Дополнительный текст", 	
						"type"	=> "textHTML", 		
						"value" => "",
						"class" => ""),
	"news_text_1_eng"	=> array(
						"name"	=> "news_text_1_eng", 	
						"title" => "Краткий текст (eng)", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),
	"news_text_2_eng"	=> array(
						"name"	=> "news_text_2_eng", 	
						"title" => "Полный текст (eng)", 	
						"type"	=> "textHTML", 		
						"value" => "",
						"class" => ""),
	"news_text_3_eng"	=> array(
						"name"	=> "news_text_3_eng", 	
						"title" => "Дополнительный текст (eng)", 	
						"type"	=> "textHTML", 		
						"value" => "",
						"class" => "")
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
	$arrData = delArrayElem($_POST, array("addItem", "test", "id", "news_time"));
	
	// обработка checkbox'а
	switch(@$arrData['news_mark']) 
	{
		case "" : 	$arrData['news_mark'] = 0; break;
		default : 	$arrData['news_mark'] = 1; break;
	}
	
	switch(@$arrData['news_show']) 
	{
		case "" : 	$arrData['news_show'] = 0; break;
		default : 	$arrData['news_show'] = 1; break;
	}
	
	$arrData['news_date'] = $_POST['news_date']." ".$_POST['news_time'];
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();		
}

// предварительный просмотр
if(isset($test))
{
	// предварительно удалим ненужные элементы
	$_POST['id'] = 1;
	$arrData = delArrayElem($_POST, array("test"));
	
	// обработка checkbox'а
	switch(@$arrData['news_mark']) 
	{
		case "" : 	$arrData['news_mark'] = 0; break;
		default : 	$arrData['news_mark'] = 1; break;
	}
	
	switch(@$arrData['news_show']) 
	{
		case "" : 	$arrData['news_show'] = 0; break;
		default : 	$arrData['news_show'] = 1; break;
	}
	
		
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $_POST['id']);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();
	?>
	<script language="javascript">
		window.open("/news_one/<?=$_POST['id']?>/")
	</script>
	<?	
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
	$arrData = delArrayElem($_POST, array("updateItem", "id", "news_time"));
	
	// обработка checkbox'а
	switch(@$arrData['news_mark']) 
	{
		case "" : 	$arrData['news_mark'] = 0; break;
		default : 	$arrData['news_mark'] = 1; break;
	}
	
	switch(@$arrData['news_show']) 
	{
		case "" : 	$arrData['news_show'] = 0; break;
		default : 	$arrData['news_show'] = 1; break;
	}
	
	$arrData['news_date'] = $_POST['news_date']." ".$_POST['news_time'];
	
	
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
		
		$date = explode(" ", $row['news_date'], 2);
		
		$arrFormFields["id"]["value"]			= $row['id'];
		$arrFormFields["news_cat"]["value"]		= $row['news_cat'];
		$arrFormFields["news_title"]["value"]	= $row['news_title'];
		$arrFormFields["news_title_eng"]["value"]	= $row['news_title_eng'];
		$arrFormFields["news_date"]["value"]	= $date[0];
		$arrFormFields["news_time"]["value"]	= $date[1];
		$arrFormFields["news_text_1"]["value"]	= $row['news_text_1'];
		$arrFormFields["news_text_2"]["value"]	= $row['news_text_2'];
		$arrFormFields["news_text_3"]["value"]	= $row['news_text_3'];
		$arrFormFields["news_text_1_eng"]["value"]	= $row['news_text_1_eng'];
		$arrFormFields["news_text_2_eng"]["value"]	= $row['news_text_2_eng'];
		$arrFormFields["news_text_3_eng"]["value"]	= $row['news_text_3_eng'];
		$arrFormFields["news_img"]["value"]		= $row['news_img'];
		$arrFormFields["news_alb"]["value"]		= $row['news_alb'];
		$arrFormFields["news_mark"]["value"]	= $row['news_mark'];
		$arrFormFields["news_show"]["value"]	= $row['news_show'];
		$arrFormFields["news_src"]["value"]		= $row['news_src'];
		
		
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
