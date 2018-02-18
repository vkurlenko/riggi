<?php
include "../config.php" ;
include "../fckeditor/fckeditor.php";
include "../db.php";

$news_table_name = $_VARS['tbl_prefix']."_menu_volumes";
$photo_alb = 125;
include "menu_functions.php";



/*switch($news_table_name)
{
	case "f_news" : $name = "Новости";
					$name_2 = "новости";
	break;
	
	case "actions" :*/ $name = "Разделы меню";
					$name_2 = "раздела меню";
	/*break;
	
}*/

CreateTable($news_table_name);

if(isset($set_news))
{
	echo AddNews($news_table_name, $news_title, $news_text, $news_text_2, $news_img, $news_date, $news_show);
}

if(isset($del_news) and isset($id))
{
	DelNews($news_table_name, $id);
}

if(isset($update_news) and isset($id))
{
	$res = UpdateNews($news_table_name, $id, $news_title, $news_text, $news_text_2, $news_img, $news_date, $news_show);
	if($res) echo "<span style='color:green; display:block; padding:5px; border:1px solid green; float:left; background:#B9FFB9'>Раздел изменен</span>";
	else echo "<span style='color:#FF4040; display:block; padding:5px; border:1px solid #FF4040; float:left; background:#FFCACA'>Раздел неизменен</span>";
	echo "<div style='clear:left'></div>";
}
?>

<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="admin.css" type="text/css">
<script type="text/javascript" src="js/jscolor/jscolor.js"></script>
</head>
<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<p align="right"><a href="javascript:history.back();">&laquo;&laquo;&nbsp;Вернуться</a></p>

<?
if(!isset($edit_news))
{
	?>
	<fieldset><legend>Разделы меню</legend>
	<?	
	GetMenuPos($news_table_name);
	?>
	
</fieldset>

<fieldset><legend><strong>Создать раздел меню</strong></legend>

<form method=post enctype=multipart/form-data action="?page=restourant_menu_volumes" name="form1" id="form1">
<table cellpadding="5">
	<tr>
		<td>
			Заголовок раздела меню</td><td>
			<input type="text" name="news_title" style="width:400px" />
		</td>
	</tr>
	<tr>
		<td>Картинка</td>
		<td><select name="news_img" >
		<?
		$r = mysql_query("select * from `photo$photo_alb` order by `id` desc ");
		echo "<option value='0'>Без картинки\n";
		while($row = mysql_fetch_array($r))
		{
			echo "<option value='".$row['id']."'>".$row['name']."\n";
		}
		?>
		</select><span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Картинки для <?=$name;?></a>")</span></td>
	</tr>
	<tr><td>Показывать на сайте</td><td><input type="checkbox" name="news_show" checked="checked"><span style="font-size:10px;">()</span></td></tr>

</table>
</fieldset>

<fieldset><legend><strong>Текст раздела меню</strong></legend>
<?
$news_text="";
$oFCKeditor->BasePath = '../../fckeditor/editor/' ;	// '/fckeditor/' is the default value.	//******************
$sBasePath = '../../fckeditor/' ;	// '/fckeditor/' is the default value.	//******************
$oFCKeditor = new FCKeditor('news_text') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Height     = '200'; // Высота 
$oFCKeditor->Value		= "$news_text" ;
$oFCKeditor->Create() ;
?>
</fieldset>

<fieldset><legend><strong>Дополнительный текст раздела меню</strong></legend>

<?
$news_text_2="";
//if(isset($razdel[0]['block_text_value'])) $block_text_value=eregi_replace("<p>[[:space:]]*</p>","<p>&nbsp;</p>",$razdel[0]['block_text_value']);
$oFCKeditor->BasePath = '../../fckeditor/editor/' ;	// '/fckeditor/' is the default value.	//******************
$sBasePath = '../../fckeditor/' ;	// '/fckeditor/' is the default value.	//******************
$oFCKeditor = new FCKeditor('news_text_2') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Height     = '200'; // Высота 
$oFCKeditor->Value		= "$news_text_2" ;
$oFCKeditor->Create() ;
?>
</fieldset>
<input type=submit name="set_news" value='Создать'  >
</form>

<?
}

if(isset($edit_news) and isset($id))
{
	$res = ReadNews($news_table_name, $id);
?>
<fieldset><legend><strong>Редактирование раздела меню</strong></legend>
<form method=post enctype=multipart/form-data action="?page=restourant_menu_volumes" name="form1" id="form1">
<table cellpadding="5">
	
	<tr>
		<td>
			Заголовок раздела меню</td><td>
			<input type="text" name="news_title" value="<?=$res[0]['news_title']?>" style="width:400px" />
			<input type="hidden" name="id" value="<?=$res[0]['id']?>">
		</td>
	</tr><tr>
		<td>Картинка</td>
		<td><select name="news_img" >
		<?
		
		$r=mysql_query("select * from `photo$photo_alb` order by `id` desc ");
		if($res[0]['news_img'] == 0) echo "<option value='0' selected>Без картинки\n";
		else echo "<option value='0'>Без картинки\n";
		while($row = mysql_fetch_array($r))
		{
			if ($res[0]['news_img'] == $row['id']) $selected = " selected";
			else $selected = " ";
			echo "<option value='".$row['id']."' ".$selected.">".$row['name']."\n";
		}
		?>
		</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$photo_alb;?>" target="_self">Картинки для <?=$name;?></a>")</span></td>
	</tr>
	<tr><td>Показывать на сайте</td><td><input type="checkbox" name="news_show" <? if($res[0]['news_show'] == 1) echo " checked";?>><span style="font-size:10px;">()</span></td></tr>

</table>
</fieldset>

<fieldset><legend><strong>Текст раздела меню</strong></legend>
<?
$news_text="";
if(isset($res[0]['news_text'])) $news_text=eregi_replace("<p>[[:space:]]*</p>","<p>&nbsp;</p>",$res[0]['news_text']);
$oFCKeditor->BasePath = '../../fckeditor/editor/' ;	// '/fckeditor/' is the default value.	//******************
$sBasePath = '../../fckeditor/' ;	// '/fckeditor/' is the default value.	//******************
$oFCKeditor = new FCKeditor('news_text') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Height     = '200'; // Высота 
$oFCKeditor->Value		= "$news_text" ;
$oFCKeditor->Create() ;
?>
</fieldset>

<fieldset><legend><strong>Дополнительный текст раздела меню</strong></legend>
<?
$news_text_2="";
if(isset($res[0]['news_text_2'])) $news_text_2=eregi_replace("<p>[[:space:]]*</p>","<p>&nbsp;</p>",$res[0]['news_text_2']);
$oFCKeditor->BasePath = '../../fckeditor/editor/' ;	// '/fckeditor/' is the default value.	//******************
$sBasePath = '../../fckeditor/' ;	// '/fckeditor/' is the default value.	//******************
$oFCKeditor = new FCKeditor('news_text_2') ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Height     = '200'; // Высота 
$oFCKeditor->Value		= "$news_text_2" ;
$oFCKeditor->Create() ;
?>
</fieldset>
<input type=submit name="update_news" value='Изменить'  >
</form>
<?
}
?>
</body>
</html>
