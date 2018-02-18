<?
$table_name = $_VARS['tbl_template_name'];		// таблица с шаблонами страниц
$tpl_dir = $_VARS['tpl_dir']; // папка с шаблонами страниц


function CreateTable()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		tpl_name 		text,
		tpl_marker		text,
		tpl_file		text,
		tpl_default		enum('0','1'),
		tpl_index		enum('0','1'),
		tpl_order		int default '0' not null
	)" ;
	$res = mysql_query($sql);
}

function StripP($string)
{
	$string = trim($string);
	/*$str_len = strlen($string);
	$tag_begin = strpos($string, "<p>");
	if($tag_begin == 0)
	{
		$tag_end = strrpos($string, "</p>");
		$string = substr($string, 3, ($str_len)-7);
	}*/
	$string = str_replace("<p>&nbsp;</p>","",$string);
	$string = str_replace("<p>","",$string);
	$string = str_replace("</p>","<br/>",$string);
	
	return $string;	
}

/*~~~ запись в файл ~~~*/
function WriteFile($code, $file)
{
	global $tpl_dir;
	$dir = chdir($_SERVER['DOC_ROOT']."/".$tpl_dir);
	$file = fopen($file, "w");
	$fw = fwrite($file, stripslashes($code));
	return $file;
}
/*~~~ /запись в файл ~~~*/

function AddTpl($tpl_name, $tpl_marker, $tpl_file, $tpl_code, $tpl_default, $tpl_index)
	{
	global $table_name;
	
	$f = WriteFile($tpl_code, $tpl_file);
	
	if(isset($tpl_default)) 
	{
		$tpl_default = '1';
		$sql = "update `$table_name` set 
				tpl_default = '0'
				where tpl_default='1'";
		$res = mysql_query($sql);
	}
	else $tpl_default = '0';
	
	if(isset($tpl_index)) 
	{
		$tpl_index = '1';
		$sql = "update `$table_name` set 
				tpl_index = '0'
				where tpl_index='1'";
		$res = mysql_query($sql);
	}
	else $tpl_index = '0';
	
	$sql = "insert into `$table_name` (tpl_name, tpl_marker, tpl_file, tpl_default, tpl_index)
	values ('$tpl_name', '$tpl_marker', '$tpl_file', '$tpl_default', '$tpl_index')";
	
	$res = mysql_query($sql);
	
	$sql = "UPDATE `$table_name` SET tpl_order = ".mysql_insert_id()."
			WHERE id = ".mysql_insert_id();
	$res = mysql_query($sql);
	
	return $res;
	}
	
function GetTpl()
{
	global $table_name, $_ICON;
	$sql = "select * from `$table_name` where 1 
			ORDER BY tpl_order asc";
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5  class="list">
			<tr>
				<th></th>
				<th>Метка шаблона</th>
				<th></th>
				<th>Название шаблона</th>
				<th>Имя файла</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			$icon_index = $icon_def = '';
			if($row['tpl_index'] == '1') $icon_index = '<img src='.$_ICON["tpl_index"].' title="шаблон индексной страницы">';
			if($row['tpl_default'] == '1') $icon_def = '<img src='.$_ICON["tpl_def"].' title="шаблон по умолчанию">';
			?>
			<tr>		
					<td>
					<a href="?page=templates&id=<?=$row["id"]?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=templates&id=<?=$row["id"]?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
					</td>
					<td><?=$row['tpl_marker'];?></td>
					<td><?=$icon_index.$icon_def?></td>
					<td><a href=?page=templates&edit_tpl&id=<?=$row['id'];?>><strong><?=$row['tpl_name'];?></strong></a></td>
					<td><?=$row['tpl_file'];?></td>
					<td><a href=?page=templates&edit_tpl&id=<?=$row['id'];?>><img src='<?=$_ICON["edit"]?>'></a></td>
					<td><a style='color:red' href="javascript:if (confirm('Удалить раздел?')){document.location='?page=templates&del_tpl&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
	//return SqlParseRes($res);
}

function DelTpl($id)
{
	global $table_name, $tpl_dir;
	
	/*~~~ удаляем файл ~~~*/
	$sql = "select `tpl_file` from `$table_name` where id=$id";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);	
	$dir = chdir($_SERVER['DOC_ROOT']."/".$tpl_dir);	
	$del_f = unlink($row['tpl_file']);
	/*~~~ /удаляем файл ~~~*/
	
	/*if($del_f)
	{*/
		$sql = "delete from `$table_name` where id=$id";
		$res = mysql_query($sql);
	/*}*/	
	
	return $res;
}

function ReadTpl($id)
{
	global $table_name, $tpl_dir;
	$sql = "select * from `$table_name` where id=$id";	
	return SqlParseRes($sql);
}

/*~~~ читаем файл шаблона для редактирования ~~~*/
function ReadTplFile($id)
{
	global $table_name, $tpl_dir;
	$sql = "select `tpl_file` from `$table_name` where id=$id";	
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$tpl_file = $row['tpl_file'];
	$dir = chdir($_SERVER['DOC_ROOT']."/".$tpl_dir);
	$code = file($tpl_file);
	$tpl_code = "";
	foreach($code as $str)
	{
		$tpl_code .= $str; 
	}
	return htmlspecialchars($tpl_code);
}
/*~~~ /читаем файл шаблона для редактирования ~~~*/

function UpdateTpl($id, $tpl_name, $tpl_marker, $tpl_file, $tpl_code, $tpl_default, $tpl_index)
{
	global $table_name;
	
	WriteFile($tpl_code, $tpl_file);	
	
	$tpl_code = addslashes($tpl_code);
	
	if(isset($tpl_default)) 
	{
		$tpl_default = '1';
		$sql = "update `$table_name` set 
				tpl_default = '0'
				where tpl_default='1'";
		$res = mysql_query($sql);
	}
	else $tpl_default = '0';
	
	if(isset($tpl_index)) 
	{
		$tpl_index = '1';
		$sql = "update `$table_name` set 
				tpl_index = '0'
				where tpl_index='1'";
		$res = mysql_query($sql);
	}
	else $tpl_index = '0';
		
	$sql = "update `$table_name` set 
	tpl_name = '$tpl_name',
	tpl_marker = '$tpl_marker',
	tpl_default	= '$tpl_default',
	tpl_index	= '$tpl_index'
	where id=$id";
	$res = mysql_query($sql) or die(mysql_error());
	return $res;
}

?>