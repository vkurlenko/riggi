<?php
session_start();
error_reporting(E_ALL);
/*~~~~~~~~~~~~~~~*/
/* CMS ИНФОБЛОКИ */
/*~~~~~~~~~~~~~~~*/

$tableName = $_VARS['tbl_prefix']."_banners";

include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "banners_functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
/*include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";*/

check_access(array("admin", "editor"));

$tags = "<strong><a><br><span><img><embed><em>";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


$arrTableFields = array(
	"id"				=> "int auto_increment primary key",
	"banner_group_name"	=> "text",
	"banner_group_alb"	=> "text",	
	"banner_group_place"=> "text",
	"banner_group_tpl"	=> "text",
	"banner_group_show"	=> "enum('1','0') not null"
);


// создание новой таблицы БД

$db_Table = new DB_Table();
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// добавленние новой записи
if(isset($addItem))
{
	// предварительно удалим ненужные элементы
	$arrData = delArrayElem($_POST, array("addItem", "id"));
	
	// обработка checkbox'а
	switch(@$arrData['banner_group_show']) 
	{
		case "" : 	$arrData['banner_group_show'] = 0; break;
		default :   $arrData['banner_group_show'] = 1; break;
	}
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();	
}


// удаление записи
if(isset($del_block) and isset($id))
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
	switch(@$arrData['banner_group_show']) 
	{
		case "" : 	$arrData['banner_group_show'] = 0; break;
		default : 	$arrData['banner_group_show'] = 1; break;
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


<?
if(!isset($editItem) && !isset($setItem))
{
	?>
	<fieldset><legend>Баннеры</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новую группу</a>
		<?
		GetBlocks();
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новую группу</a>
	</fieldset>
	<?
}

else
{
	$caption = "Добавить новую группу баннеров";
	$id = "";
	$banner_group_name 	= "";
	$checked = "";	
	$banner_group_place_checked	= "";
	$banner_group_tpl_checked = "";
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
		$id = $_GET['id'];
		$res = ReadBlock($id);
		$caption 		= "Редактирование группы баннеров '".$res[0]['banner_group_name']."'";
		$banner_group_name 	= $res[0]['banner_group_name'];		
		if($res[0]['banner_group_show'] == 1) $checked = " checked ";		
		$banner_group_place_checked = $res[0]['banner_group_place'];
		$banner_group_tpl_checked = $res[0]['banner_group_tpl'];
		$submit = array('updateItem', 'Изменить');
	}
	?>
	<fieldset><legend><?=$caption;?></legend>
	
		<form method=post enctype=multipart/form-data action="?page=banners" name="form1" id="form1">
		<table>
			<tr>
				<td>
					Название группы баннеров</td><td>
					<input type="text" name="banner_group_name" size="40" value="<?=$banner_group_name?>" />
					<input type="hidden" name="id" value="<?=$id?>">		
				</td>
			</tr>
			<tr>
				<td>
					Шаблон баннера</td><td>
					<select name="banner_group_tpl">
						<option value=0 >Без шаблона</option>
						<?
						$sql = "select * from `".$_VARS['tbl_template_name']."` where 1";
						$res3 = mysql_query($sql);
						  
						while($row3 = mysql_fetch_array($res3))
						{					  	
							if($banner_group_tpl_checked == $row3['tpl_marker'])
							{
								$sel1 = " selected='selected' ";
							}
							else $sel1 = "";
							?>
							<option <?=$sel1;?> value='<?=$row3['tpl_marker'];?>'  ><?=$row3['tpl_name']?></option>
							<?
						}
						?>
					</select>
						
				</td>
			</tr>
			<tr>				
				<td>Привязка к альбому</td>
				<td>
					<select name="banner_group_alb">
						<?			
						$check_val = $res[0]['banner_group_alb'];
						//echo $check_val;
						include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
						?>
					</select>
				</td>
			</tr>	
			<tr>				
				<td>Место размещения</td>
				<td>
					<select name="banner_group_place">
						<?
						foreach($_VARS['banners_place'] as $k => $v)
						{
							$sel = "";
							if($banner_group_place_checked == $k) $sel = " selected ";
						?>
						<option value="<?=$k?>" <?=$sel?>><?=$v?></option>
						<?
						}
						?>
					</select>
				</td>
			</tr>
			<tr><td>Показывать на сайте</td><td><input type="checkbox" name="banner_group_show" <?=$checked;?> ></td></tr>
		</table>		
		
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		</form>
	</fieldset>
	<?
}
?>

<?
include "banners_info.php";
?>


</body>
</html>
