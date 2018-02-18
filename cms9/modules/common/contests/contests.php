<?php
/*~~~~~~~~~~~~~~~*/
/* CMS МАСТЕРА   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "contests.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "contests",
	"tableName" 	=> $_VARS['tbl_prefix']."_contests",
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Конкурсы",
	"TEXT_ADD_ITEM"	=> "Добавить конкурс",
	"TEXT_EDIT_ITEM"=> "Редактировать конкурс"		
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
	"contest_type"		=> "enum('look') not null",		// тип конкурса
	"contest_name"		=> "text",						// название конкурса
	"contest_start"		=> "date not null",				// дата начала проведения
	"contest_length"	=> "date not null",				// дата окончания
	"contest_active"	=> "enum('0', '1') not null",	// закончен/активен
	"contest_items"		=> "text",						// массив конкурсных работ {id альбома|id фотки|кол-во голосов|показывать на сайте}
	"contest_winner"	=> "int default '0' not null",	// id пользователя-победителя
	"contest_winner_item"=> "text",						// массив работы победителя {id альбома|id фотки|кол-во голосов}
	"contest_users_vote"=> "text"						// массив проголосовавших пользователей	
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
	"contest_name"	=> array(
						"name"	=> "contest_name", 	
						"title" => "Название конкурса", 	
						"type"	=> "inputText", 		
						"value" => "Look недели",
						"class" => "inputText"),
	"contest_start"	=> array(
						"name"	=> "contest_start", 	
						"title" => "Дата начала проведения", 	
						"type"	=> "inputDate", 		
						"value" => date("Y-m-d"),
						"class" => "inputDate"),
	"contest_length"	=> array(
						"name"	=> "contest_length", 	
						"title" => "Дата окончания", 	
						"type"	=> "inputDate", 		
						"value" => date("Y-m-d"),
						"class" => "inputDate"),
						
	"contest_active"=> array(
						"name"	=> "contest_active", 	
						"title" => "Конкурс открыт", 	
						"type"	=> "inputCheckbox", 	
						"value" => false),
	"contest_items"	=> array(
						"name"	=> "contest_items", 	
						"title" => "Участники конкурса", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),
	"contest_winner"	=> array(
						"name"	=> "contest_winner", 	
						"title" => "Победитель конкурса", 	
						"type"	=> "inputText", 		
						"value" => "0",
						"class" => "inputText"),
	"contest_winner_item"		=> array(
						"name"	=> "contest_winner_item", 	
						"title" => "Работа победителя", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText")
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
	if(isset($_POST['contest_active']))
	{
		$sql = "UPDATE `".$_VARS['tbl_prefix']."_contests`
				SET contest_active = '0'
				WHERE 1";
		$res = mysql_query($sql);
	}
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
	}
	

	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
	
	unset($arrData);
	
	
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
	if(isset($_POST['contest_active']))
	{
		$sql = "UPDATE `".$_VARS['tbl_prefix']."_contests`
				SET contest_active = '0'
				WHERE 1";
		$res = mysql_query($sql);
	}
	
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
		GetItems($_MODULE_PARAM['tableName'], $orderBy = "contest_start", $orderDir = "DESC");
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
		$arrFormFields["contest_name"]["value"]		= $row['contest_name'];
		$arrFormFields["contest_start"]["value"]	= $row['contest_start'];
		$arrFormFields["contest_length"]["value"]	= $row['contest_length'];
		$arrFormFields["contest_active"]["value"]	= $row['contest_active'];
		$arrFormFields["contest_items"]["value"]	= $row['contest_items'];
		$arrFormFields["contest_winner"]["value"]	= $row['contest_winner'];
		$arrFormFields["contest_winner_item"]["value"]	= $row['contest_winner_item'];
		
		
		
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
							if($v['name'] == 'contest_items')
							{
								?><a href="/cms9/modules/common/contests/contests.members.php?id=<?=$row['id']?>">Посмотреть участников</a><?
							}
							else $elem -> createFormElem();	
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
	
	/*echo "<pre>";
	print_r(unserialize($row['contest_items']));
	echo "</pre>";*/
}
?>

<?
//include "banners_info.php";
?>


</body>
</html>
