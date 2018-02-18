<?
session_start();
error_reporting(E_ALL);
include_once "../config.php" ;
include_once "../db.php";
check_access(array("admin"));
$table_name = $_VARS['tbl_prefix']."_presets";
$size = 50;

//$menu_t = array("left" => "глобальное (слева)", "right" => "для главной (справа)");
###################################
####		функции				###
###################################
function CreateTable()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 				int auto_increment primary key,
		var_name		text,
		var_value		text,
		var_default		text,
		var_note		text,		
		var_protected	enum('0', '1')
	)" ;
	$res = mysql_query($sql);
	//echo "res = ".$res;
}

function AddItem($var_name, $var_value, $var_default, $var_note, $var_protected)
{
	global $table_name;
	if($var_protected == 'on')
	{
		$var_protected = 1;
	}
	else $var_protected = 0;
	
	$sql = "insert into `$table_name` (var_name, var_value, var_default, var_note, var_protected)
	values ('$var_name', '$var_value', '$var_default', '$var_note', '$var_protected')";
	//echo $sql;
	$res = mysql_query($sql);
	
	return $res;
}

function UpdateItem($id, $var_value, $var_note)
{
	global $table_name;
		
	$sql = "update `$table_name` set 
	var_value='$var_value',	
	var_note='$var_note'
	where id=$id";
	$res = mysql_query($sql);
	return $res;
}



function DelItem($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}

CreateTable();

if(isset($set_item))
{
	AddItem($var_name, $var_value, @$var_default, $var_note, @$var_protected);
}

if(isset($update_item) and isset($id))
{	
	UpdateItem($id, $var_value, $var_note);
}

if(isset($move) and isset($dir) and isset($id))
{
	MoveItem($id, $dir);
}

if(isset($del_item) and isset($id))
{
	DelItem($id);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="admin.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="pragma" content="no-cache">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?
/*if(isset($menu_place))
{*/
	$sql = "select * from `$table_name` where 1 order by id asc";
	$res = mysql_query($sql);
	?>
	<strong style="padding:20px; display:block">Настройки</strong>
	<table cellpadding="5">
		<tr>
			<td>del</td>			
			<td>Имя переменной</td>
			<td>Значение</td>
			<td>По умолчанию</td>
			<td>edit</td>	
			<td>Пояснение</td>		
		</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
		?>
		<tr>
			<?
			if($row['var_protected'] == 1)
			{
				?>
				<td>lock</td>
				<?
			}
			else
			{
			?>
			<td><a href="javascript:if (confirm('Удалить раздел?')){document.location='?page=presets&del_item&id=<?=$row['id']?>'}">X</a></td>
			<?
			}
			?>
			<td><?=$row['var_name'];?></td>
			<?
			if($row['var_name'] == "bgimage")
			{
				$sql = "select * from `photo9` where id=".$row['var_value'];
				$r = mysql_query($sql);
				$r2 = mysql_fetch_array($r);
				?>
				<td><?=$r2['name'];?></td>
				<?
			}
			else
			{
			?>
			<td><?=$row['var_value'];?></td>
			<?
			}
			?>
			
			<td><?=$row['var_default'];?></td>
			<?
			if($row['var_protected'] == 1)
			{
				?>
				<td>lock</td>
				<?
			}
			else
			{
			?>
			<td><a href="?page=presets&edit_item&id=<?=$row['id']?>">edit</a></td>
			<?
			}
			?>
			<td><span style="font-size:10px;"><?=$row['var_note'];?></span></td>
						
		</tr>
		<?
		}
		?>		
	</table>
	<?
/*}
else*/if(isset($edit_item) and isset($id))
{
	$sql = "select * from `$table_name` where id='$id'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	?>
	<strong style="padding:20px; display:block">Редактирование переменной "<?=$row['var_name']?>"</strong>
	<form method="post" enctype="multipart/form-data" action="?page=presets" name="form2" id="form2">
	<table cellpadding="5">
		<tr>
			<td>Имя переменной</td>
			<td>$<?=$row['var_name']?>
			<input type="hidden" name="id" value="<?=$row['id']?>" />
			</td>
		</tr>
		<?
		if($row['var_name'] == "bgimage")
		{
		?>
		<tr>
			<td>Фоновый рисунок</td><td><select name="var_value" >
			<?			
			$r = mysql_query("select * from `photo9` order by `id` desc ");
			
			if($row['var_value'] == 0) echo "<option value='0' selected>Без картинки\n";
			else echo "<option value='0'>Без картинки\n";
			while($res = mysql_fetch_array($r))
			{
				if ($row['var_value'] == $res['id']) $selected = " selected";
				else $selected = " ";
				echo "<option value='".$res['id']."' ".$selected.">".$res['name']."\n";
			}
			?>
			</select> <span style="font-size:10px;">(название картинки из фотобанка "<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=photo&zhanr=9" target="_self">Фоновые рисунки</a>")</span></td>
		</tr>
		<?
		}
		else
		{
		?>
		<tr>
			<td>Значение</td><td><input type="text" name="var_value" value="<?=$row['var_value']?>" size="<?=$size?>" /></td>
		</tr>
		<?
		}
		?>		
		<tr>
			<td>По умолчанию</td><td><?=$row['var_default']?></td>
		</tr>
		<tr>
			<td>Пояснение</td><td><input type="text" name="var_note" value="<?=$row['var_note']?>" size="<?=$size?>" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="update_item" value="Сохранить" /></td>
		</tr>
	</table>
	</form>
	<?	
}
else
{
?> 

<strong style="padding:20px; display:block">Добавить переменную</strong>
<form method=post enctype=multipart/form-data action="?page=presets" name="form2" id="form2">
<table cellpadding="5">
		<tr>
			<td>Имя переменной</td>
			<td>
			<input type="text" name="var_name" size="<?=$size?>" />
			<input type="hidden" name="id"  />
			</td>
		</tr>
		
		<tr>
			<td>Значение</td><td><input type="text" name="var_value" size="<?=$size?>"/></td>
		</tr>
		<tr>
			<td>По умолчанию</td><td><input type="text" name="var_default" size="<?=$size?>"/></td>
		</tr>
		<!--<tr>
			<td>Неизменяемая</td><td><input  name="var_protected" type="checkbox" /></td>
		</tr>-->
		<tr>
			<td>Пояснение</td><td><input type="text" name="var_note" size="<?=$size?>"/></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="set_item" value="Добавить" /></td>
		</tr>
	</table>


</form>
<?
}
?>
</body>
</html>
