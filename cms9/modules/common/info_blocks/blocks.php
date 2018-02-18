<?php
session_start();
/*~~~~~~~~~~~~~~~*/
/* CMS ИНФОБЛОКИ */
/*~~~~~~~~~~~~~~~*/


include_once $_SERVER['DOC_ROOT']."/config.php" ;
include_once $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include_once $_SERVER['DOC_ROOT']."/db.php";
include_once "blocks_functions.php";

check_access(array("admin", "editor"));

CreateTable();

if(isset($set_block))
{
	AddBlock($block_marker, $block_name, @$block_image_id, @$block_image_alt, @$block_bg_color, @$block_text_color, @$block_text_value, @$block_text_value_en, @$block_html);
}

if(isset($del_block) and isset($id))
{
	DelBlock($id);
}

if(isset($update_block) and isset($id))
{
	$res = UpdateBlock($id, $block_marker, $block_name, @$block_bg_color, @$block_text_color, @$block_text_value, @$block_text_value_en, @$block_html);
	if($res) echo "<span style='color:green; display:block; padding:5px; border:1px solid green; float:left; background:#B9FFB9'>Запись для блока изменена</span>";
	else echo "<span style='color:#FF4040; display:block; padding:5px; border:1px solid #FF4040; float:left; background:#FFCACA'>Запись неизменена</span>";
}


?>
<?
include_once "head.php";
?>
<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<p align="right"><a href="javascript:history.back();">&laquo;&laquo;&nbsp;Вернуться</a></p>


<?
if(!isset($edit_block) && !isset($add_block))
{
	?>
	<fieldset><legend>Информационные блоки</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_block"><img src='<?=$_ICON["add_item"]?>'>Добавить новый инфоблок</a>
		<?
		GetBlocks();
		?>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_block"><img src='<?=$_ICON["add_item"]?>'>Добавить новый инфоблок</a>
	</fieldset>
	<?
}



elseif(isset($add_block))
{
?>
<fieldset><legend>Добавить новый инфоблок</legend>
	<form method=post enctype=multipart/form-data action="?page=blocks" name="form1" id="form1">
	<table>
		<tr>
			<td>
				Название блока</td><td>
				<input type="text" name="block_name" size="40" /><span style="font-size:10px;">(например, назначение или место размещения блока)</span>
			</td>
		</tr><tr>
			<td>
				Маркер блока</td><td>
				<input type="text" name="block_marker" value="iblock_" size="40" /><span style="font-size:10px;">(уникальное имя, используется при расставлении маркеров в шаблоне страницы)</span>
			</td>
		</tr>
		<tr><td>Вырезать все HTML-теги</td><td><input type="checkbox" name="block_html"><span style="font-size:10px;">(кроме <?=htmlspecialchars($tags)?>)</span></td></tr>
	
	</table>
	
	<fieldset><legend>Текст блока</legend>
		<?
		$editor_text_name = 'block_text_value';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
		
		<fieldset><legend>Текст блока (eng)</legend>
		<?
		$editor_text_name = 'block_text_value_en';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
	<input type=submit name="set_block" value='Изменить'  >
	</form>
</fieldset>
<?
}



elseif(isset($edit_block) and isset($id))
{
	$res = ReadBlock($id);
	//echo "block_text_valueedit=".$block_text_value;
	?>
	<fieldset><legend>Редактирование блока "<?=$res[0]['block_name']?>"</legend>
	
	<form method=post enctype=multipart/form-data action="?page=blocks" name="form1" id="form1">
	<table>
		<tr>
			<td>
				Название блока</td><td>
				<input type="text" name="block_name" size="40" value="<?=$res[0]['block_name']?>" />
				<input type="hidden" name="id" value="<?=$res[0]['id']?>">		
			</td>
			</tr><tr>
			<td>
				Маркер блока</td><td>
				<input type="text" name="block_marker" size="40" value="<?=$res[0]['block_marker']?>" />
			</td>
		</tr>
		<tr><td>Вырезать все HTML-теги</td><td><input type="checkbox" name="block_html" <? if($res[0]['block_html'] == 1) echo "checked";?> ><span style="font-size:10px;">(кроме <?=htmlspecialchars($tags)?>)</span></td></tr>
	</table>
	
	<fieldset><legend>Текст блока</legend>
		<?
		$editor_text_edit = $res[0]['block_text_value'];
		$editor_text_name = 'block_text_value';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
		
		<fieldset><legend>Текст блока (eng)</legend>
		<?
		$editor_text_edit = $res[0]['block_text_value_en'];
		$editor_text_name = 'block_text_value_en';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
	<input type=submit name="update_block" value='Изменить'  >
	</form>
</fieldset>
<?
}
?>
</body>
</html>
