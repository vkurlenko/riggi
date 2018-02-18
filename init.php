<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ ИНИЦИАЛИЗАЦИЯ CMS ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/

/*

Порядок установки CMS9

1. Скопировать в www/ файл init.php и архив init_site.php

2. Распаковать архив, будут созданы необходимые для работы cms файлы

3. Завести новую БД и пользователя, параметры доступа к БД указать в файле config.php

4. Указать префикс БД в config.php

5. Запустить файл init.php. Будут созданы необходимые папки сайта и таблицы БД

*/

include $_SERVER['DOCUMENT_ROOT'].'/config.php' ;
include $_SERVER['DOCUMENT_ROOT'].'/db.php' ;
include $_SERVER['DOCUMENT_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

function CreateFolder($folder_name)
{
	global $_VARS;
	$result = "Без результата";
	if(!is_dir($_SERVER['DOCUMENT_ROOT']."/".$folder_name))
	{
		$mkdir = mkdir($_SERVER['DOCUMENT_ROOT']."/".$folder_name);
		if($mkdir) $result = "<span class='msgOk'>Создана папка <strong>".$folder_name."</strong></span><br>";
		else $result = "<span class='msgError'>Ошибка создания папки <strong>".$folder_name."</strong></span><br>";
		chmod($_SERVER['DOCUMENT_ROOT']."/".$folder_name, 0777);
	}
	else $result = "<span class='msgNormal'>Папка <strong>".$folder_name."</strong> уже существует</span><br>";
	
	return $result;
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Инициализация</title>
<link href="/cms9/admin.css" rel="stylesheet" type="text/css" />
</head>

<body>

<table>
	<tr>
		<th>Операция</th>
		<th>Результат</th>
	</tr>	
<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ автоматом создадим папки ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrNewFolder = array(
	"css", 
	"js", 
	"templates", 
	"blocks",
	$_VARS['tpl_dir'], 
	$_VARS['photo_alb_dir'], // папка фотоальбомов
	$_VARS['photo_alb_dir']."/".$_VARS['tbl_photo_name']."1", // папка фотоальбом "Разное"
	$_VARS['photo_alb_dir']."/".$_VARS['tbl_photo_name']."2", // папка фотоальбом "Новости"
	"img",
	"img/tpl",
	"img/pic"
);

foreach($arrNewFolder as $k)
{
	?>
	<tr>
		<td>Создание папки <strong><?=$k?></strong></td>
		<td>
		<?
		$res = CreateFolder($k);
		echo $res;
		?>
		</td>
	<?
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /автоматом создадим папки ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ создание таблиц БД ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrNewTbl = array(
	$_VARS['tbl_cms_users']			=> $authTableFields,
	$_VARS['tbl_pages_name']		=> $pagesTableFields,
	$_VARS['tbl_photo_alb_name']	=> $picCatalogueTableFields,
	$_VARS['tbl_photo_name']."1"	=> $picTableFields,
	$_VARS['tbl_photo_name']."2"	=> $picTableFields	
);

foreach($arrNewTbl as $k => $v)
{
	?>
	<tr>
		<td>Создание таблицы <strong><?=$k?></strong></td>
		<td>
		<?
		$db_Table = new DB_Table();
		$db_Table -> debugMode = true;
		$db_Table -> tableName = $k;
		$db_Table -> tableFields = $v;
		$db_Table -> create();
		?>
		</td>
	</tr>
	<?	
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /создание таблиц БД ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ инициализация записей таблиц БД ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrNewRecord = array(
	array("tbl" => $_VARS['tbl_cms_users'], "param" => array("userLogin" => "admin", "userPwd" => "admin", "userGroup" => "admin", "userBlock" => 0)),
	
	array("tbl" => $_VARS['tbl_photo_alb_name'], "param" => array("alb_name" => "1", "alb_title" => "Разное")),
	array("tbl" => $_VARS['tbl_photo_alb_name'], "param" => array("alb_name" => "2", "alb_title" => "Новости")),
);

//for($i = 0; $i < count($arrNewRecord); $i++)

foreach($arrNewRecord as $k)
{
	?>
	<tr>
		<td>Создание записи в таблицу <strong><?=$k['tbl']?></strong></td>
		<td>
		<?
		
		// проверим, существует ли уже такая запись
		$string = "";
		$i = 0;
		foreach($k['param'] as $a => $b)
		{
			$string .= $a." = '".$b."'";
			$i++;
			if($i < count($k['param'])) $string .= " and ";
		}		
		$sql = "select * from `".$k['tbl']."` where ".$string;
		//echo $sql;
		$res = mysql_query($sql);
		
		
		if(mysql_num_rows($res) == 0)
		{
			// если такой записи нет, пишем в таблицу
			$db_Table = new DB_Table();
			$db_Table -> debugMode = true;
			$db_Table -> tableName = $k['tbl'];
			$db_Table -> tableFields = $arrNewTbl[$k['tbl']];
			$db_Table -> tableData = $k['param'];
			$db_Table -> addItem();	
		}
		else
		{	
			// иначе пропускаем
			?><span class="msgNormal">Запись (<?=$string?>) уже существует</span><?
		}	
		?>
		</td>
	</tr>
	<?	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /инициализация записей таблиц БД ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
?>


</table>


<p>
<a href="/cms9/">Перейти к CMS</a>
</p>

</body>
</html>
