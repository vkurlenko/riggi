<?
error_reporting(E_ALL);
include_once $_SERVER['DOCUMENT_ROOT']."/config.php";
$razdel_table = $_VARS['tbl_pages_name'];
require_once "razdel.php";
require_once $_SERVER['DOC_ROOT']."/db.php";
require_once $_SERVER['DOC_ROOT']."/functions_sql.php";
require_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php";
require_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
require_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/auth/auth.checkuser.php";

$razdel = GetRazdel('podr', $id);

?>
<html>
<head>
<title>Редакторский интерфейс сайта
<?=$HTTP_SERVER_VARS["SERVER_NAME"] ?>
</title>
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


<fieldset><legend><strong>Редактировать раздел</strong></legend>

<form method="post" enctype="multipart/form-data" action="/cms9/workplace.php?page=pages"  name="form1" id="form1">
	<table>
		<tr>
			<td>Название:</td>
			<td><input type="text" name="p_title" size=70 value="<?=htmlspecialchars($razdel[0]['p_title']);?>"></td>
		</tr>
		<?
		if($_VARS['multi_lang'] == true)
		{
		?>
		<tr>
			<td>Название (eng):</td>
			<td><input type="text" name="p_title_eng" size=70 value="<?=htmlspecialchars($razdel[0]['p_title_eng']);?>"></td>
		</tr>
		<?
		}?>
		<tr>
			<td>URL ( только латиница, цифры, - и _ ):</td>
			<td><strong><a href="http://<?=$_SERVER['HTTP_HOST']."/".htmlspecialchars($razdel[0]['p_url'])."/";?>" target="_blank">http://<?=$_SERVER['HTTP_HOST']."/".htmlspecialchars($razdel[0]['p_url'])."/";?></a></strong><input type="hidden" name="p_url" size=70 value="<?=htmlspecialchars($razdel[0]['p_url']);?>"></td>
		</tr>
		<tr>
			<td>URL редиректа (без окружающих слешей):</td>
			<td><input type="text" name="p_redirect" size=70 value="<?=htmlspecialchars($razdel[0]['p_redirect']);?>"></td>
		</tr>
		<tr>
			<td>Шаблон раздела:</td>
			<td>
				<select name="p_tpl">
					<?
					  $sql = "select * from `".$_VARS['tbl_prefix']."_templates` where 1 order by tpl_order asc";
					  $res = mysql_query($sql);
					  $i = 0;
					  while($row = mysql_fetch_array($res))
					  {
					  	$i++;
						if($razdel[0]['p_tpl'] == $row['tpl_marker'])
						{
							$sel = " selected ";
						}
						else $sel = "";
					?>
					<option <?=$sel;?> value="<?=$row['tpl_marker'];?>">
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
				<td><input type="text" name="p_tags" size=70 value="<?=$razdel[0]['p_tags']?>"><span style="font-size:10px;">()</span></td>
			<?
			}
			else
			{
			?>
		
		
			<td>Теги</td>
			<td><!--<input type="text" name="p_tags" size=70 value="<?=$razdel[0]['p_tags']?>">-->
				выберите тег: <select name="p_tags">
					<?
					$sql = "select distinct `p_tags` from `".$_VARS['tbl_pages_name']."` where 1 ";
					$res = mysql_query($sql);
					while($row = mysql_fetch_array($res))
					{
						$selected = '';
						if($row['p_tags'] == $razdel[0]['p_tags']) $selected = ' selected ';
						?>
						<option value="<?=$row['p_tags']?>" <?=$selected?>>
						<?=$row['p_tags']?>
						</option>
						<?
					}
					?>
				</select>
				или введите новый: <input type="text" name="p_tags_new"  size=30 value="">
			</td>
		<?
		}
		?>
		</tr>
		<!--<tr>
			<td>Код вставки видео</td>
			<td><textarea name="p_video" cols=67><?=htmlspecialchars($razdel[0]['p_video']);?></textarea></td>
		</tr>-->
		<tr>
			<td>Картинка к статье</td>
			<td><select name="p_img" >
			<?
			$sql_2 = "select * from `".$_VARS['tbl_photo_name'].$_VARS['env']['photo_alb_page']."` order by `id` desc ";
			echo $sql_2;
			$res_2 = mysql_query($sql_2);
			if(mysql_num_rows($res_2) == 0)  echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			
			while($row_2 = mysql_fetch_array($res_2))
			{
				if($razdel[0]['p_img'] == $row_2['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$row_2['id']."' ".$selected.">".$row_2['name']."\n";
			}
			?>
			</select>
			<span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['photo_alb_page']?>" target="_self">Картинки к статьям</a>")</span>
			<?
				$img = new Image();
				$img -> imgCatalogId 	= $_VARS['env']['photo_alb_page'];
				$img -> imgId 			= $razdel[0]['p_img'];
				$img -> imgAlign 		= 'left';
				$img -> imgClass 		= "";
				$img -> imgWidthMax 	= 50;
				$img -> imgHeightMax 	= 50;	
				$img -> imgMakeGrayScale= false;
				$img -> imgGrayScale 	= false;
				$img -> imgTransform	= "crop";
				$html = $img -> showPic();
				echo $html;			
				?>
			</td>
		</tr>
		<!--<tr>
			<td>Альбом фотографий для слайд-шоу</td>
			<td><select name="p_photo_alb">
				<?		
				$check_val = $razdel[0]['p_photo_alb'];
				echo $check_val;
				include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
				?>
				</select>
			</td>
		</tr>-->
		<tr>
			<td>Альбом фотографий по теме</td>
			<td><select name="p_photo_alb_2">
					<?			
					$check_val = $razdel[0]['p_photo_alb_2'];
					echo $check_val;
					include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/photo_alb/photo_alb_select.php";
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Показывать на сайте?</td>
			<td>
			 <?
				$on_yes=""; 
				$on_no=" checked ";
				if($razdel[0]['p_show'] == "1") 
				{ 
					$on_yes = " checked "; 
					$on_no  = ""; 
				}
			?>
			<input type="radio" name="p_show" value="1" id="on1" <?=$on_yes;?>>
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_show" value="0" id="on2" <?=$on_no;?>>
				Нет </td>
		</tr>
		<tr>
			<td>Показывать в главном меню?</td>
			<td>
			<?
			$in_left_menu_yes = ""; 
			$in_left_menu_no = " checked ";
			if($razdel[0]['p_main_menu'] == "1") 
			{ 
				$in_left_menu_yes = " checked "; 
				$in_left_menu_no  = ""; 
			}
			?>
			<input type="radio" name="p_main_menu" value="1" id="in_left_menu1" <?=$in_left_menu_yes;?>>
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_main_menu" value="0" id="in_left_menu1" <?=$in_left_menu_no;?>>
				Нет </td>
		</tr>
		
		<tr>
			<td>Показывать в карте сайта?</td>
			<td>
			<?
			$in_left_menu_yes = ""; 
			$in_left_menu_no = " checked ";
			if($razdel[0]['p_site_map'] == "1") 
			{ 
				$in_left_menu_yes = " checked "; 
				$in_left_menu_no  = ""; 
			}
			?>
			<input type="radio" name="p_site_map" value="1" id="in_left_menu2" <?=$in_left_menu_yes;?>>
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_site_map" value="0" id="in_left_menu2" <?=$in_left_menu_no;?>>
				Нет </td>
		</tr>
		
		<tr>
			<td>Закрытый раздел?</td>
			<td>
				<?
				$p_protect_1 = ""; 
				$p_protect_2 = " checked ";
				if($razdel[0]['p_protect'] == "1") 
				{ 
					$p_protect_1 = " checked "; 
					$p_protect_2  = ""; 
				}
				?>			
				<input type="radio" name="p_protect" id="p_protect" value="1" <?=$p_protect_1?>/>
				Да
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="p_protect" id="p_protect" value="0" <?=$p_protect_2?>/>
				Нет </td>
		</tr>
		
		<?
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_yandex_map`
				WHERE map_item = ".$razdel[0]['id'];
		$res = mysql_query($sql);
		if($res && mysql_num_rows($res) > 0)
		{
			$row_m = mysql_fetch_array($res);
			?><tr><td colspan=2><a href="/cms9/workplace.php?page=yandex_map&editItem&id=<?=$row_m['id']?>">C этой страницей связана <strong>карта</strong></a></td></tr><?
		}
		?>
		
	</table>
	</fieldset>
	
	<fieldset>
	<legend><strong>Текст страницы</strong></legend>
	<?
	$editor_text_edit = $razdel[0]['p_content'];
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
		$editor_text_edit = $razdel[0]['p_content_eng'];
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
	$editor_text_edit = $razdel[0]['p_add_text_1'];
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
	$editor_text_edit = $razdel[0]['p_add_text_1_eng'];
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
	/*$editor_text_edit = $razdel[0]['p_add_text_2'];
	$editor_text_name = 'p_add_text_2';
	$editor_height = 200;
	include "../".$_VARS['cms_modules']."/common/editor/fck_editor.php";	*/		
	?>
	</fieldset>-->
	
	
	
	<fieldset>
	<legend><strong>Meta теги</strong></legend>
	<table width="100%">
		<tr>
			<td>Title:</td>
			<td><input type="text" name="p_meta_title" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_title']);?>"></td>
		</tr>
		
		<tr>
			<td>Keywords:</td>
			<td><input type="text" name="p_meta_kwd" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_kwd']);?>"></td>
		</tr>
		
		<tr>
			<td>Description:</td>
			<td><input type="text" name="p_meta_dscr" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_dscr']);?>"></td>
		</tr>
		
		
		<?
		if($_VARS['multi_lang'] == true)
		{
		?>
		<tr>
			<td>Title (eng):</td>
			<td><input type="text" name="p_meta_title_eng" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_title_eng']);?>"></td>
		</tr>
		<tr>
			<td>Keywords (eng):</td>
			<td><input type="text" name="p_meta_kwd_eng" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_kwd_eng']);?>"></td>
		</tr>
		<tr>
			<td>Description (eng):</td>
			<td><input type="text" name="p_meta_dscr_eng" size=70 value="<?=htmlspecialchars($razdel[0]['p_meta_dscr_eng']);?>"></td>
		</tr>
		<?
		}
		?>
	</table>
	</fieldset>
	<p>&nbsp;</p>
	<input type="hidden" name="parent" value="<?=$parent;?>">
    <input type="submit" value='Изменить' class="bigSubmit" onClick="document.form1.action='index.php'; document.form1.target='_self';MM_openBrWindow('/<?=$_VARS['cms_dir'];?>/log_write.php?text=<? echo "Изменен раздел "; ?>'+document.getElementById('p_title').value,'logwin','width=100,height=100');">
    <input type="hidden" name="upd_id" value="<?=$razdel[0]['id'];?>">
</form>
</body>
</html>
