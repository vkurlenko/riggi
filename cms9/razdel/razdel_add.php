<?
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT']."/config.php";
$razdel_table = $_VARS['tbl_pages_name'];
require_once "razdel.php";
require_once $_SERVER['DOC_ROOT']."/db.php";
include_once $_SERVER['DOC_ROOT']."/functions_sql.php";
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/auth/auth.checkuser.php";
?>
<html>
<head>
<title>Редакторский интерфейс сайта<?=$HTTP_SERVER_VARS["SERVER_NAME"] ?></title>
<link rel="stylesheet" href="../admin.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="pragma" content="no-cache">
<script language="JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>
<body>
&nbsp;<a href=index.php>Все разделы</a>
<p>&nbsp;</p>

<fieldset><legend><strong>Добавить раздел</strong></legend>

<form method="post" enctype="multipart/form-data" action="/cms9/workplace.php?page=pages"  name="form1" id="form1">
	<table>
		<tr>
			<td>Название:</td>
			<td><input type="text" name="p_title" id="p_title" size=70 value=""></td>
		</tr>
		<?
		if($_VARS['multi_lang'] == true)
		{
		?> 
		<tr>
			<td>Название (eng):</td>
			<td><input type="text" name="p_title_eng" id="p_title" size=70 value=""></td>
		</tr>
		<?
		}
		?>
		<tr>
			<td>URL ( только латиница, цифры, - и _ ):</td>
			<?
			$sql = "select MAX(id) as max_id from `".$_VARS['tbl_pages_name']."` where 1";
			//echo $sql;
			$res = mysql_query($sql);
			$row = mysql_fetch_array($res);
			$max_id = $row['max_id'];
			?>
			<td><input type="text" name="p_url" size=70 value="<?=$max_id++?>"></td>
		</tr>
		<tr>
			<td>URL редиректа (без окружающих слешей):</td>
			<td><input type="text" name="p_redirect" size=70 value=""></td>
		</tr>
		<tr>
			<td>Шаблон раздела:</td>
			<td>
				<select name="p_tpl">
					<?
					  $sql = "select * from `".$_VARS['tbl_prefix']."_templates` where 1 order by tpl_order asc";
					  $res = mysql_query($sql);
					  
					  while($row = mysql_fetch_array($res))
					  {					  	
						if($row['tpl_default'] == '1')
						{
							$sel = " selected='selected' ";
						}
						else $sel = "";
					?>
					<option <?=$sel;?> value='<?=$row['tpl_marker'];?>'  >
					<?=$row['tpl_name']?>
					</option>
					<?
					  }
					?>
					<option>очистить</option>
				</select>
			</td>
		</tr>
		
		<tr>
		<?
		$arr = array(8);
		
		$sql = "SELECT id FROM `".$_VARS['tbl_pages_name']."`
				WHERE p_parent_id = 8";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
			$arr[] = $row['id'];
		
		if(in_array($_GET['parent'], $arr))
		{
		?>
			<td>email</td>
			<td><input type="text" name="p_tags" size=70 value=""> <span style="font-size:10px;"></span>
			</td>
		<?
		}
		else
		{
		?>
			<td>Теги (через запятую)</td>
			<td><input type="text" name="p_tags" size=70 value="">
				<select>
					<?
					$sql = "select distinct `p_tags` from `".$_VARS['tbl_pages_name']."` where 1 ";
					//echo $sql;
					$res = mysql_query($sql);
					while($row = mysql_fetch_array($res))
					{
					?>
						<option value="<?=$row['p_tags']?>">
						<?=$row['p_tags']?>
						</option>
					<?
					}
					?>
				</select>
			</td>
		<?
		}
		?>			
		</tr>
		
		<tr>
			
		</tr>
		<!--<tr>
			<td>Код вставки видео</td>
			<td><textarea name="p_video" cols=67></textarea></td>
		</tr>-->
		<tr>
			<td>Картинка к статье</td>
			<td><select name="p_img" >
			<?
			$sql_2 = "select * from `".$_VARS['tbl_photo_name'].$_VARS['env']['photo_alb_page']."` order by `id` desc ";
			$res_2 = mysql_query($sql_2);
			if(mysql_num_rows($res_2) == 0)  echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			
			while($row_2 = mysql_fetch_array($res_2))
			{
				echo "<option value='".$row_2['id']."'>".$row_2['name']."\n";
			}
			?>
			</select>
			<span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['photo_alb_page']?>" target="_self">Картинки к статьям</a>")</span>
			</td>
		</tr>
		<!--<tr>
			<td>Альбом фотографий для слайд-шоу</td>
			<td><select name="p_photo_alb">
					<?			
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
			</td>
		</tr>-->
		<tr>
			<td>Альбом фотографий по теме</td>
			<td><select name="p_photo_alb_2">
					<?			
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Показывать на сайте?</td>
			<td><input type="radio" name="p_show" value="1" checked >
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_show" value="0" >
				Нет </td>
		</tr>
		<tr>
			<td>Показывать в главном меню?</td>
			<td><input type="radio" name="p_main_menu" value="1">
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_main_menu" value="0" checked  >
				Нет </td>
		</tr>
		<tr>
			<td>Показывать в карте сайта?</td>
			<td><input type="radio" name="p_site_map" value="1">
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_site_map" value="0" checked  >
				Нет </td>
		</tr>
		<tr>
			<td>Закрытый раздел?</td>
			<td><input type="radio" name="p_protect" value="1">
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_protect" value="0" checked  >
				Нет </td>
		</tr>
	</table>
	</fieldset>
	
	<fieldset>
	<legend><strong>Текст страницы</strong></legend>	
	<?
	$editor_text_name = 'p_content';
	$editor_height = 400;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";		
	?>	
	</fieldset>
	
	<?
	if($_VARS['multi_lang'] == true)
	{
	?> 
	<fieldset>
	<legend><strong>Текст страницы (eng)</strong></legend>	
	<?
	$editor_text_name = 'p_content_eng';
	$editor_height = 400;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>
	</fieldset>
	<?
	}
	?>
	
	<fieldset>
	<legend><strong>Дополнительный текст</strong></legend>	
	<?
	$editor_text_name = 'p_add_text_1';
	$editor_height = 200;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>
	</fieldset>
	
	<?
	if($_VARS['multi_lang'] == true)
	{
	?> 
	<fieldset>
	<legend><strong>Дополнительный текст (eng)</strong></legend>	
	<?
	$editor_text_name = 'p_add_text_1_eng';
	$editor_height = 200;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>
	</fieldset>
	<?
	}
	?>
	
	<!--
	<fieldset>
	<legend><strong>Дополнительный текст 2</strong></legend>	
	<?
	/*$editor_text_name = 'p_add_text_2';
	$editor_height = 200;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";*/	
	?>
	</fieldset>-->
	
		
	
	<fieldset>
	<legend><strong>Meta теги</strong></legend>
	<table width="100%">
		<tr>
			<td>Title:</td>
			<td><input type="text" name="p_meta_title" size=70 value=""></td>
		</tr>
		
		
		<tr>
			<td>Keywords:</td>
			<td><input type="text" name="p_meta_kwd" size=70 value=""></td>
		</tr>
		
		<tr>
			<td>Description:</td>
			<td><input type="text" name="p_meta_dscr" size=70 value=""></td>
		</tr>	
		
		<?
		if($_VARS['multi_lang'] == true)
		{
		?> 
		<tr>
			<td>Title (eng):</td>
			<td><input type="text" name="p_meta_title_eng" size=70 value=""></td>
		</tr>
		<tr>
			<td>Keywords (eng):</td>
			<td><input type="text" name="p_meta_kwd_eng" size=70 value=""></td>
		</tr>
		<tr>
			<td>Description (eng):</td>
			<td><input type="text" name="p_meta_dscr_eng" size=70 value=""></td>
		</tr>
		<?
		}
		?>
		
	</table>
	</fieldset>
	<p>&nbsp;</p>
	<input type="hidden" name="parent" value=<?=$parent;?>>
	<input type="hidden" name="whattodo" value="razdel">
	<input type="submit" class="bigSubmit" value='Добавить раздел' name="addPage" style=""  onClick="document.form1.action='index.php'; document.form1.target='_self'; MM_openBrWindow('/<?=$_VARS['cms_dir'];?>/log_write.php?text=<? echo "Добавлен раздел "; ?>'+document.getElementById('p_title').value,'logwin','width=100,height=100');">
</form>
</body>
</html>
