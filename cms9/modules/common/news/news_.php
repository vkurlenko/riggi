<?php
/*~~~~~~~~~~~~~~~~~~~*/
/*~~~ CMS НОВОСТИ ~~~*/
/*~~~~~~~~~~~~~~~~~~~*/
session_start();

include "../config.php" ;
include "../fckeditor/fckeditor.php";
include "../db.php";
include "news_functions.php";

check_access(array("admin", "editor"));

$news_category = $_VARS['news_category'];
//$news_category[$news_cat][0];

CreateTable($news_table_name);

if(isset($set_news))
{
	/*echo "news_img_b=".$news_img_b;
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	$id = AddNews($news_cat, $news_title, @$news_title_en, $news_date, $news_text_1, @$news_text_1_en, @$news_text_2, @$news_text_2_en, @$news_img_s,  $news_img_b, $news_alb, @$vimeo_link, @$code_iPod, @$code_HDvideo, @$news_mark, @$news_show, @$news_src);
	if(@$news_mark == "on")
	{
		AddToFavorit($id);
	}
}

if(isset($del_news) and isset($id))
{
	DelNews($news_table_name, $id);
}

if(isset($update_news) and isset($id))
{
	$res = UpdateNews($id, $news_cat,  $news_title, @$news_title_en, $news_date, $news_text_1, @$news_text_1_en, @$news_text_2, @$news_text_2_en, @$news_img_s,  $news_img_b, $news_alb, @$vimeo_link, @$code_iPod, @$code_HDvideo, @$news_mark, @$news_show, @$news_src);
	if($res) echo "<span style='color:green; display:block; padding:5px; border:1px solid green; float:left; background:#B9FFB9'>Новость изменена</span>";
	else echo "<span style='color:#FF4040; display:block; padding:5px; border:1px solid #FF4040; float:left; background:#FFCACA'>Новость неизменена</span>";
}
?>

<?
include_once "head.php";
?>
<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<p align="right"><a href="javascript:history.back();">&laquo;&laquo;&nbsp;Вернуться</a></p>

<?
// выводим список новостей из данной категории
if(!isset($edit_news) and !isset($add_news))
{	
	?>
	<fieldset><legend>Новости категории "<?=$news_category[$news_cat][0];?>"</legend>
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_news"><img src='<?=$_ICON["add_item"]?>'>Добавить новость в эту категорию</a>
	<?
	GetNews($news_table_name, $news_cat);
	?>
	<!--<p><a href='?page=news&news_cat=<?=$news_cat;?>&add_news'>Добавить новость в эту категорию</a></p>-->
		<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_news"><img src='<?=$_ICON["add_item"]?>'>Добавить новость в эту категорию</a>
	</fieldset>
	<?		
}


// форма добавления новости
elseif(isset($add_news))
{	
	?>
	<fieldset><legend>Добавление новости</legend>
	
	<form method=post enctype=multipart/form-data action="?page=news&news_cat=<?=$news_cat;?>" name="form1" id="form1">
	<table cellpadding="5">
		<tr>	
			<td>
				Дата публикации новости</td><td>
				<input type="text" name="news_date" value="<?=date ("Y-m-d")?>" /><span style="font-size:10px;">(YYYY-MM-DD)</span>
			</td>
		</tr>
		<tr>
			<td>Категория новости</td>
			<td>
				<select name="news_cat">
				<?
				foreach($news_category as $k => $v)
				{
					if($k == $news_cat) $sel = " selected ";
					else $sel = "";
				?>
					<option value="<?=$k;?>" <?=$sel;?>><?=$v[0];?></option>
				<?
				}
				?>					
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Заголовок новости</td><td>
				<textarea name="news_title" cols="40" /></textarea><span style="font-size:10px;">()</span>
			</td>
		</tr>
		<?
		if($_VARS['multi_lang'] == true)
		{
			?>
			<tr>
				<td>
					Заголовок новости (eng)</td><td>
					<textarea name="news_title_en" cols="40" /></textarea><span style="font-size:10px;">()</span>
				</td>
			</tr>
			<?
		}
		?>
		<!--<tr>
			<td>Картинка превью</td>
			<td><select name="news_img_s" >
			<?
					
			/*$r = mysql_query("select * from `photo".$news_category[$news_cat][1]."` order by `id` desc");
			echo "<option value='0'>Без картинки\n";
			while($row = mysql_fetch_array($r))
			{
				echo "<option value='".$row['id']."'>".$row['name']."\n";
			}*/
			?>
			</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_action']?>" target="_self">Картинки для новостей</a>")</span></td>
		</tr>-->
		<tr>
			<td>Картинка в тексте</td>
			<td><select name="news_img_b" >
			<?
					
			$r = mysql_query("select * from `photo".$_VARS['env']['alb_action']."` order by `id` desc");
			echo "<option value='0'>Без картинки\n";
			while($row = mysql_fetch_array($r))
			{
				echo "<option value='".$row['id']."'>".$row['name']."\n";
			}
			?>
			</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_action']?>" target="_self">Картинки для новостей</a>")</span></td>
		</tr>
		<tr>
			<td>Альбом фотографий по теме</td>
			<td><select name="news_alb">
			<?
					
			$r = mysql_query("select * from `".$_VARS['tbl_prefix']."_photo_alb` order by `id` desc ");
			echo "<option value='0'>Без альбома\n";
			while($row = mysql_fetch_array($r))
			{
				echo "<option value='".$row['id']."'>".$row['alb_title']."\n";
			}
			?>
			</select></td>
		</tr>
		
		<tr>
			<td>
				Ссылка на источник</td><td>
				<input type="text" name="news_src" size="43" /><span style="font-size:10px;">()</span>
			</td>
		</tr>
		<tr>
			<td>Показывать на сайте</td>
			<td><input type="checkbox" name="news_show" checked="checked"><span style="font-size:10px;">()</span></td>
		</tr>
		<!--<tr>
			<td>Пометить как избранное</td>
			<td><input type="checkbox" name="news_mark" ><span style="font-size:10px;">(избранные новости выводятся на гл. странице)</span></td>
		</tr>-->
	</table>
	<fieldset><legend>Текст анонса</legend>
	<textarea name="news_text_1" class="admTextarea" style="width:700px; height:100px "></textarea>
	</fieldset>
	<?
	if($_VARS['multi_lang'] == true)
	{
		?>
		<fieldset><legend>Текст анонса (eng)</legend>
		<textarea name="news_text_1_en" class="admTextarea" style="width:700px; height:100px "></textarea>
		</fieldset>
		<?
	}
	?>
	
	<fieldset><legend>Полный текст</legend>
	<?
	$editor_text_name = 'news_text_2';
	$editor_height = 400;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>	
	</fieldset>
	
	<?
	if($_VARS['multi_lang'] == true)
	{
		?>
		<fieldset><legend>Полный текст (eng)</legend>
		<?
		$editor_text_name = 'news_text_2_en';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
		<?
	}
	?>
	
	<input type=submit name="set_news" value='Сохранить' >
	
	</form>
	</fieldset>
	<?
}


// форма редактирования новости
elseif(isset($edit_news) and isset($id))
{
	$res = ReadNews($news_table_name, $id);
	?>
	<fieldset><legend>Редактирование новости</legend>
	<!--<strong style="padding:20px; display:block">Редактирование новости</strong>-->
	<form method=post enctype=multipart/form-data action="?page=news&news_table_name=<?=$news_table_name?>" name="form1" id="form1">
	<table cellpadding="5">
		
		<tr>
			<td>
				Дата публикации</td><td>
				<input type="text" name="news_date" value="<?=$res[0]['news_date']?>" /><span style="font-size:10px;">(YYYY-MM-DD)</span>
				
			</td>
		</tr>	
		<tr>
			<td>Категория новости</td>
			<td><input type="hidden" name="news_cat" value="<?=$res[0]['news_cat'];?>"><?=$news_category[$res[0]['news_cat']][0];?>
				
			</td>
		</tr>	
		<tr>
			<td>
				Заголовок </td><td>
				<textarea name="news_title" cols="40" /><?=$res[0]['news_title']?></textarea><span style="font-size:10px;">()</span>
				<input type="hidden" name="id" value="<?=$res[0]['id']?>">
			</td>
		</tr>
		
		<?
		if($_VARS['multi_lang'] == true)
		{
			?>
			<tr>
				<td>
					Заголовок (eng)</td><td>
					<textarea name="news_title_en" cols="40" /><?=$res[0]['news_title_en']?></textarea><span style="font-size:10px;">()</span>
				</td>
			</tr>
		<?
		}
		?>
		<!--<tr>
			<td>Картинка превью</td>
			<td><select name="news_img_s" >
			<?			
			/*$r=mysql_query("select * from `photo".$news_category[$res[0]['news_cat']][1]."` order by `id` desc ");
			if($res[0]['news_img_s'] == 0) echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			while($row = mysql_fetch_array($r))
			{
				if ($res[0]['news_img_s'] == $row['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$row['id']."' ".$selected.">".$row['name']."\n";
			}*/
			?>
			</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$news_category[$res[0]['news_cat']][1];?>" target="_self">Картинки для новости</a>")</span></td>
		</tr>-->
		<tr>
			<td>Картинка в тексте</td>
			<td><select name="news_img_b" >
			<?
			$r=mysql_query("select * from `photo".$_VARS['env']['alb_action']."` order by `id` desc ");
			if($res[0]['news_img_b'] == 0) echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			while($row = mysql_fetch_array($r))
			{
				if ($res[0]['news_img_b'] == $row['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$row['id']."' ".$selected.">".$row['name']."\n";
			}
			?>
			</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_action']?>" target="_self">Картинки для новостей</a>")</span></td>
		</tr>
		<tr>
			<td>Альбом фотографий по теме</td>
			<td><select name="news_alb">
			<?
					
			$r = mysql_query("select * from `".$_VARS['tbl_prefix']."_photo_alb` order by `id` desc ");
			echo "<option value='0'>Без альбома\n";
			while($row = mysql_fetch_array($r))
			{
				if ($res[0]['news_alb'] == $row['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$row['id']."' ".$selected.">".$row['alb_title']."\n";
			}
			?>
			</select></td>
		</tr>
		
		
		<tr>
			<td>
				Ссылка на источник</td><td>
				<input type="text" name="news_src" size="43" value="<?=$res[0]['news_src'];?>" /><span style="font-size:10px;">()</span>
			</td>
		</tr>
		<tr>
			<td>Показывать на сайте</td>
			<td><input type="checkbox" name="news_show" <? if($res[0]['news_show'] == 1) echo " checked";?>><span style="font-size:10px;">()</span></td>
		</tr>
		<!--<tr>
			<td>Пометить как избранное</td>
			<? if($res[0]['news_mark'] == 1) $check = " checked ";
			else $check = "";?>
			<td><input type="checkbox" name="news_mark" <?=$check;?>><span style="font-size:10px;">(избранные новости выводятся на гл. странице)</span></td>
		</tr>-->
	
	</table>	
	
	<fieldset><legend>Текст анонса</legend>
	<textarea name="news_text_1" class="admTextarea" style="width:700px; height:100px "><?=$res[0]['news_text_1'];?></textarea>
	</fieldset>
	
	<?
	if($_VARS['multi_lang'] == true)
	{
		?>
		<fieldset><legend>Текст анонса (eng)</legend>
		<textarea name="news_text_1_en" class="admTextarea" style="width:700px; height:100px "><?=$res[0]['news_text_1_en'];?></textarea>
		</fieldset>
		<?
	}
	?>
	
	<fieldset><legend>Полный текст</legend>
	<?
	$editor_text_edit = stripslashes($res[0]['news_text_2']);
	$editor_text_name = 'news_text_2';
	$editor_height = 400;
	include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
	?>	
	</fieldset>
	
	<?
	if($_VARS['multi_lang'] == true)
	{
		?>
		<fieldset><legend>Полный текст (eng)</legend>
		<?
		$editor_text_edit = stripslashes($res[0]['news_text_2_en']);
		$editor_text_name = 'news_text_2_en';
		$editor_height = 400;
		include $_VARS['cms_modules']."/common/editor/fck_editor.php";	
		?>	
		</fieldset>
		<?
	}
	?>
	
	<input type=submit name="update_news" value='Изменить'  >
	</form>
	</fieldset>
	<?
}
?>
</body>
</html>
