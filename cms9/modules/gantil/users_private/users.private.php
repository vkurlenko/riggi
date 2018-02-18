<?php
/*~~~~~~~~~~~~~~~*/
/* CMS ВИДЖЕТЫ   */
/*~~~~~~~~~~~~~~~*/

session_start();
error_reporting(E_ALL);

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "users.private.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

/*~~~~~~~~~~~~~~~~~~*/
/* параметры модуля */
/*~~~~~~~~~~~~~~~~~~*/
$_MODULE_PARAM = array(
	"name"			=> "users_private",
	"tableName" 	=> $_VARS['tbl_prefix']."_users_private",
	"userAccess" 	=> array("admin", "editor")	
);

$_TEXT = array(
	"TEXT_HEAD"		=> "Данные личного кабинета",
	"TEXT_ADD_ITEM"	=> "Добавить нового пользователя",
	"TEXT_EDIT_ITEM"=> "Редактировать пользователя"		
);
/*~~~~~~~~~~~~~~~~~~~*/
/* /параметры модуля */
/*~~~~~~~~~~~~~~~~~~~*/

//check_access($_MODULE_PARAM['userAccess']);

$tags = "<strong><a><br><span><img><embed><em>";


/*~~~~~~~~~~~~~~~~~~~~~~*/
/* структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~*/
$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"user_id"	 	=> "int default '0' not null",
	"user_photo"	=> "int default '0' not null",
	"user_alb"		=> "int default '0' not null",

	"user_credo"		=> "text",
	"user_fav_salon"	=> "text",
	"user_fav_masters"	=> "text",	// array
	
	"user_order_history"=> "text" 	// array
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
						"value" => "",
						"class" => "inputText"),
	"user_id"		=> array(
						"name"	=> "user_id", 	
						"title" => "user_id", 	
						"type"	=> "inputHidden", 		
						"value" => $_GET['id'],
						"class" => "inputHidden"),
	"user_photo"	=> array(
						"name"	=> "user_photo", 	
						"title" => "Фотография пользователя", 	
						"type"	=> "selectPic", 		
						"value" => 0,
						"alb"	=> $_VARS['env']['pic_catalogue_users']),
	/*"user_alb"	=> array(
						"name"	=> "user_alb", 	
						"title" => "Альбом look'ов", 	
						"type"	=> "inputText", 		
						"value" => "",
						"class" => "inputText"),*/
	"user_credo"	=> array(
						"name"	=> "user_credo", 	
						"title" => "Жизненное кредо", 	
						"type"	=> "textareaText", 		
						"value" => "",
						"class" => "inputText"),
	"user_fav_salon"	=> array(
						"name"	=> "user_fav_salon", 	
						"title" => "Любимый салон", 	
						"type"	=> "selectSalon", 		
						"value" => "",
						"p_parent_id" => 9),
	"user_fav_masters"	=> array(
						"name"	=> "user_fav_masters", 	
						"title" => "Любимые мастера", 	
						"type"	=> "selectObject", 		
						"value" => "",
						"mode"	=> "multiSelect",
						"table" => $_VARS['tbl_prefix'].'_masters',
						"order" => "master_order",
						"field" => "master_name",
						"order_dir" => "ASC")
);
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* /структура формы редактирования записи */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




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
	foreach($arrFormFields as $k => $v) 
	{
		if($v["type"] == "inputCheckbox")
		{
			if(@$arrData[$k] != "") $arrData[$k] = 1;
			else $arrData[$k] = 0;
		}
	}
	
	if(!isset($_POST["user_fav_masters"])) $arrData["user_fav_masters"] = serialize(array(0 => 'none'));
	else $arrData["user_fav_masters"] = serialize($_POST["user_fav_masters"]);
	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();		
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
	
	if(!isset($_POST["user_fav_masters"])) $arrData["user_fav_masters"] = serialize(array(0 => 'none'));
	else $arrData["user_fav_masters"] = serialize($_POST["user_fav_masters"]);
	
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}
?>


<?
include_once $_SERVER['DOC_ROOT']."/cms9/head.php";

/*echo "<pre>";
print_r($arrFormFields);
echo "</pre>";*/
?>


<body>
<style>
tr{vertical-align:top}
td{padding:10px 5px}
input.inputText{width:300px}
textarea.inputText{width:300px; height:100px}

</style>

<?


	$elem = new FormElement();


	$caption = $_TEXT['TEXT_ADD_ITEM'];
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users_private`
			WHERE user_id = ".$_GET['id'];
	//echo $sql;
	$res = mysql_query($sql);

	if(mysql_num_rows($res) > 0) 
	{
		$row = mysql_fetch_array($res);

		$editItem = true;
		
		$caption = $_TEXT['TEXT_EDIT_ITEM'];
		
		$arrFormFields["id"]["value"]				= $row['id'];
		$arrFormFields["user_id"]["value"]			= $row['user_id'];
		$arrFormFields["user_photo"]["value"]		= $row['user_photo'];
		$arrFormFields["user_credo"]["value"]		= $row['user_credo'];
		$arrFormFields["user_fav_salon"]["value"]	= $row['user_fav_salon'];
		$arrFormFields["user_fav_masters"]["value"]	= $row['user_fav_masters'];
				
		$submit = array('updateItem', 'Сохранить');
		/*echo "<pre>";
		print_r($row_user);
		echo "</pre>";*/
	}
	else $submit = array('addItem', 'Создать');	
	
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users`
			WHERE id = ".$_GET['id'];
	$res_user = mysql_query($sql);
	$row_user = mysql_fetch_array($res_user);
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
				elseif($v['title'] == 'user_id')
				{
					if(isset($row_user))
					{
					
						$elem -> createFormElem();	
					?>
					<tr>
						<td><strong>ФИО пользователя</strong></td>
						<td>
							<?=$row_user['user_patr'].' '.$row_user['user_name'].' '.$row_user['user_surn'];?>
						</td>
					</tr>
					
					<tr>
						<td><strong>Номер карты</strong></td>
						<td>
							<?=$row_user['user_data'];?>
						</td>
					</tr>
					<?
					}
				}
				else
				{	
					?>
					<tr>
						<td><strong><?=$v['title']?></strong></td>
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
		
		</form>
	</fieldset>
	

</body>
</html>
