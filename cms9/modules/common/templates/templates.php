<?
session_start();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ CMS ШАБЛОНЫ СТРАНИЦ ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


error_reporting(E_ALL);
include_once "../config.php" ;
include_once "../fckeditor/fckeditor.php";
include_once "../db.php";
include_once "templates_functions.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

$db_Table = new DB_Table();
$db_Table -> tableName = $_VARS['tbl_template_name'];

/*
id 				int auto_increment primary key,
		tpl_name 		text,
		tpl_marker		text,
		tpl_file		text,
		tpl_default		enum('0','1'),
		tpl_index		enum('0','1'),
		tpl_order		int default '0' not null
*/
/*~~~~~~~~~~~~~~~~~~~~~~*/
/* структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~*/
$arrTableFields = array(
	"id"			=> "int auto_increment primary key",
	"tpl_name"		=> "text",
	"tpl_marker"	=> "text",
	"tpl_file"		=> "text",
	"tpl_default"	=> "enum('0','1') not null",
	"tpl_index"		=> "enum('0','1') not null",
	"tpl_order"		=> "int default '0' not null"

);
/*~~~~~~~~~~~~~~~~~~~~~~~*/
/* /структура таблицы БД */
/*~~~~~~~~~~~~~~~~~~~~~~~*/

$db_Table -> tableFields = $arrTableFields;


check_access(array("admin", "editor"));
CreateTable();

if(isset($set_tpl))
{
	AddTpl($tpl_name, $tpl_marker, $tpl_file.".".$tpl_file_ext, $tpl_code, @$tpl_default, @$tpl_index);
}

if(isset($del_tpl) and isset($id))
{
	DelTpl($id);
}

if(isset($update_tpl) and isset($id))
{
	$res = UpdateTpl($id, $tpl_name, $tpl_marker, $tpl_file, $tpl_code, @$tpl_default, @$tpl_index);
	if($res) echo "<span style='color:green; display:block; padding:5px; border:1px solid green; float:left; background:#B9FFB9'>Запись изменена</span>";
	else echo "<span style='color:#FF4040; display:block; padding:5px; border:1px solid #FF4040; float:left; background:#FFCACA'>Запись неизменена</span>";
}

if(isset($move) and isset($dir) and isset($id))
{
	$db_Table -> tableOrderField = "tpl_order";
	$db_Table -> tableWhere = array("id" => $id, "dir" => $dir);
	$db_Table -> reOrderItem();	
}
?>

<?
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/head.php";
?>

<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<p align="right"><a href="javascript:history.back();">&laquo;&laquo;&nbsp;Вернуться</a></p>
<?
if(!isset($edit_tpl) && !isset($add_tpl))
{
	?>
	<fieldset><legend>Шаблоны</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_tpl"><img src='<?=$_ICON["add_item"]?>'>Добавить новый шаблон</a>
	<?
	GetTpl();
	?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_tpl"><img src='<?=$_ICON["add_item"]?>'>Добавить новый шаблон</a>
	</fieldset>
	<?	
}

elseif(isset($add_tpl))
{
?>
	<fieldset><legend>Добавление нового шаблона</legend>
	
	<form method=post enctype=multipart/form-data action="?page=templates" name="form1" id="form1">
	<table>
		<tr>
			<td>Название шаблона</td>
			<td>
				<input type="text" name="tpl_name" size="40" /><span style="font-size:10px;">(например, назначение шаблона)</span>
			</td>
		</tr>
		<tr>
			<td>Имя файла</td>
			<td>
				<input type="text" name="tpl_file" value="tpl_" size="32" /><select name="tpl_file_ext">
					<option value="php">php</option>
					<option value="htm">htm</option>
				</select><span style="font-size:10px;">()</span>
			</td>
		</tr>
		<tr>
			<td>Метка шаблона</td>
			<td>
				<input type="text" name="tpl_marker" size="40" /><span style="font-size:10px;">(например, "main")</span>
			</td>
		</tr>
		<tr>
			<td>Шаблон индексной страницы</td>
			<td>
				<input type="checkbox" name="tpl_index" />
			</td>
		</tr>
		<tr>
			<td>Шаблон по умолчанию</td>
			<td>
				<input type="checkbox" name="tpl_default" />
			</td>
		</tr>
		
		
	</table>
	<b>Код шаблона:</b> <br>
	<textarea class="t" name="tpl_code" ></textarea>
	<input type=submit name="set_tpl" value='Сохранить'  >
	</form>
	
	</fieldset>
<?
}

elseif(isset($edit_tpl) and isset($id))
{
	$res = ReadTpl($id);
	$code = ReadTplFile($id);
?>
	
	<fieldset><legend>Редактирование шаблона "<?=$res[0]['tpl_name']?>"</legend>
	<form method=post enctype=multipart/form-data action="?page=templates" name="form1" id="form1">
	<table>
		<tr>
			<td>
				Название шаблона</td><td>
				<input type="text" name="tpl_name" size="40" value="<?=$res[0]['tpl_name']?>" />
				<input type="hidden" name="id" value="<?=$res[0]['id']?>">		
			</td>
		</tr>
		<tr>
			<td>
				Метка шаблона</td><td>
				<input type="text" name="tpl_marker" size="40" value="<?=$res[0]['tpl_marker']?>" />
			</td>
		</tr>
		<tr>
			<td>
				Имя файла</td><td><input type="hidden" name="tpl_file" value="<?=$res[0]['tpl_file']?>"><?=$res[0]['tpl_file']?>
			</td>
		</tr>
		<tr>
			<td>Шаблон индексной страницы</td>
			<td>
				<?
				if($res[0]['tpl_index'] == '1') $chk = " checked ";
				else $chk = "";
				?>
				<input type="checkbox" name="tpl_index" <?=$chk?>  />
			</td>
		</tr>
		<tr>
			<td>Шаблон по умолчанию</td>
			<td>
				<?
				if($res[0]['tpl_default'] == '1') $chk = " checked ";
				else $chk = "";
				?>
				<input type="checkbox" name="tpl_default" <?=$chk?>  />
			</td>
		</tr>
		
	</table>
	<b>Код шаблона:</b> <br>
	<textarea  class="t" name="tpl_code" wrap="off" ><?
		if(isset($code)) echo trim($code);
	?></textarea>
	<input type=submit name="update_tpl" value='Сохранить'  >
	</form>
	</fieldset>
<?
}
?>

<?
include "templates_info.php";
?>
</body>
</html>
