<?
include "../config.php";
include "../db.php";

//$photo_alb = 125;
$table_name 	= $_VARS['tbl_prefix']."_reserve";
$table_param 	= array(
	"id" 				=> "int auto_increment primary key",
	"rsrv_date"			=> "date",
	"rsrv_time"			=> "int",
	"rsrv_tbl_num"		=> "int",
	"rsrv_client_name"	=> "text",
	"rsrv_client_mail"	=> "text",
	"rsrv_client_phone"	=> "text",
	"rsrv_client_msg"	=> "text"
);

$rsrv_period 	= 31; // на какой период резервируются столы
$rsrv_tbl_num 	= 27; // кол-во столов
$rsrv_time		= array("12", "29"); // время открытия и закрытия ресторана
$this_page		= "restourant_reserve";
$debugMode 		= "none"; // none|error|all

$d 	= date("d");
$m	= date("m");
$y	= date("Y");
$today = formatDate($d, $m, $y);

###################################
####		функции				###
###################################

/*~~~ добавляем предваряющий 0 ~~~*/
function formatDate($day, $month, $year)
{
	$m_arr = array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
	if(strlen($day) < 2) $day = "0".$day;
	/*if(strlen($month) < 2) $month = "0".$month;*/
	$month = $m_arr[$month-1];
	
	return $day."-".$month."-".$year;
}
/*~~~ /добавляем предваряющий 0 ~~~*/

/*~~~ приводим дату к формату SQL ~~~*/
function formatDateSQL($date)
{
	$arr = explode("-", $date);
	return $arr[2]."-".$arr[1]."-".$arr[0];	
}
/*~~~ /приводим дату к формату SQL ~~~*/

/*~~~ вывод сообщений отладки ~~~*/
function debugMsg($str, $res)
{
	global $debugMode;
	
	switch($debugMode)
	{
		case "all" : 
			if(!$res){
			?>
			<span class="msgError"><strong>Ошибка:</strong> <?=$str;?></span>
			<? }
			else{
			?>
			<span class="msgOk"><strong>Ok:</strong> <?=$str;?></span>
			<? }
			break;
		case "error" : 
			if(!$res){
			?>
			<span class="msgError"><strong>Ошибка:</strong> <?=$str;?></span>
			<? }			
			break;
		default : break;
	}	
}
/*~~~ /вывод сообщений отладки ~~~*/

/*~~~ удаляем записи по прошедшим датам ~~~*/
function clearOld()
{
	global $table_name;
	$d 	= date("d");
	$m	= date("m");
	$y	= date("Y");
	$today = $y."-".$m."-".$d;
	
	$sql = "delete from `$table_name` where rsrv_date < '$today'";
	$res = mysql_query($sql);
	debugMsg($sql, $res);
}
/*~~~ /удаляем записи по прошедшим датам ~~~*/

function CreateTable()
{
	global $table_name;
	$sql = "create table `$table_name` (
		id 					int auto_increment primary key,
		rsrv_date			date,
		rsrv_time			int,
		rsrv_tbl_num		int,
		rsrv_client_name	text,
		rsrv_client_mail	text,
		rsrv_client_phone	text,
		rsrv_client_msg		text
	)" ;
	$res = mysql_query($sql);
	debugMsg($sql, $res);
}

function AddItem($table_name, $arr)
{
	global $table_param;
	
	$set_str = $val_str = '';
	$i = 0;
	
	foreach($arr as $k => $v)
	{
		$set_str .= $k;		
	
		if($table_param[$k] == "text") $v = "'".$v."'";	
		if($table_param[$k] == "date") $v = "'".$v."'";		
		$val_str .= $v;
	
		$i++;
		if($i < count($arr)) 
		{
			$set_str .= ", ";
			$val_str .= ", ";
		}
	}
	
	$sql = "insert into `$table_name` ($set_str) values($val_str)";
	$res = mysql_query($sql);
	debugMsg($sql, $res);
}

function DelItem($itemId)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$itemId";
	$res = mysql_query($sql);
	debugMsg($sql, $res);
}


CreateTable();

/*~~~ создаем новую запись ~~~*/
if(isset($new_rsrv_submit))
{
	$arr = array(
	"rsrv_date" 		=> $new_rsrv_date,
	"rsrv_time"			=> $new_rsrv_time,
	"rsrv_tbl_num"		=> $new_rsrv_tbl,
	"rsrv_client_name"	=> $new_rsrv_name,
	"rsrv_client_mail"	=> $new_rsrv_mail,
	"rsrv_client_phone"	=> $new_rsrv_phone,
	"rsrv_client_msg"	=> $new_rsrv_msg	
	);
	
	$sql = "select * from `$table_name` where (rsrv_date = '".$new_rsrv_date."' and rsrv_time = ".$new_rsrv_time." and rsrv_tbl_num = ".$new_rsrv_tbl.")";
	//echo $sql;
	$res = mysql_query($sql);
	if(mysql_num_rows($res) == 0)
	{
		AddItem($table_name, $arr);
		?>
		<span class="msgOk"><strong>Добавлен резерв: стол №<?=$new_rsrv_tbl;?> на <?=$new_rsrv_time.".00 ".$new_rsrv_date;?></strong></span>
		<?
	}
	else
	{
	?>
	<span class="msgError"><strong>Ошибка создания резерва: стол №<?=$new_rsrv_tbl;?> на <?=$new_rsrv_time.".00 ".$new_rsrv_date;?> занят</strong></span>
	<?
	}
	
	
}
/*~~~ /создаем новую запись ~~~*/

/*~~~ удаляем запись ~~~*/
if(isset($rsrv_del))
{
	DelItem($rsrv_del);
}
/*~~~ /удаляем запись ~~~*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="admin.css" type="text/css">
<title>Untitled Document</title>
</head>

<body>
<?
clearOld();

if(!isset($rsrv_date))
{
?>
	<fieldset><legend><strong>Дата резерва:</strong></legend>
	<div style="float:left; padding:10px">
	<?
	$j = 1;
	for($i = 0; $i < $rsrv_period; $i++)
	{
		if(checkdate($m, $d, $y))
		{
			$sql = "select * from `$table_name` where rsrv_date = '$y-$m-$d'";
			$res = mysql_query($sql);			
			$n = mysql_num_rows($res);
			if($n > 0) $n = " [резервов - $n]";
			else $n = "";
			?>
			<a href="workplace.php?page=restourant_reserve&rsrv_date=<?=$y."-".$m."-".$d;?>"><?=formatDate($d, $m, $y);?></a><?=$n;?><br>
			<?		
			$d++;
		}
		else
		{
			$m++;
			?>
			</div><div style="float:left;  padding:10px">
			<?
			$d = 1;
		}
		$j++;
		if($j > $rsrv_period) break;
	}
	?>
	</div>
	</fieldset>
	<? 
}

else
{
	?>
	<fieldset><legend><strong>Резерв столов на <?=$rsrv_date;?></strong></legend>
	<table width="100%" border=1 bordercolor="#CCCCCC">
		<tr align="center">
			<th>№ стола</th>
			<th>время</th>
			<th>резерв</th>
			<th>имя клиента</th>
			<th>e-mail клиента</th>
			<th>телефон клиента</th>
			<th>пожелания</th>			
		</tr>
	<?
	$sql = "select * from `$table_name` where rsrv_date = '".$rsrv_date."' order by rsrv_tbl_num asc";
	$res = mysql_query($sql);
	while($row = mysql_fetch_array($res))
	{
	?>
		<tr>
			<td align="center"><?=$row['rsrv_tbl_num'];?></td>
			<td align="center"><?=$row['rsrv_time'].".00";?></td>
			<td align="center"><a href="?page=<?=$this_page;?>&rsrv_del=<?=$row['id'];?>&rsrv_date=<?=$row['rsrv_date'];?>">снять</a></td>
			<td><?=$row['rsrv_client_name'];?></td>
			<td><?=$row['rsrv_client_mail'];?></td>
			<td><?=$row['rsrv_client_phone'];?></td>
			<td><?=$row['rsrv_client_msg'];?></td>			
		</tr>
	<?
	}
	?>
	</table>
	</fieldset>
	<?
}

if(!isset($rsrv_date)) $rsrv_date = $today;
?>

<fieldset><legend><strong>Создать резерв на <?=$rsrv_date;?></strong></legend>
	<form method=post enctype=multipart/form-data action="?page=<?=$this_page;?>" name="form2" id="form2">
		<table>
			<tr>
				<td>№ стола&nbsp;
					&nbsp;
					</td>
				<td>
					<select name="new_rsrv_tbl">
						<?
						for($i = 1; $i < $rsrv_tbl_num + 1; $i++)
						{
						?>
						<option value="<?=$i;?>"><?=$i;?></option>
						<?
						}
						?>
					</select>
					&nbsp;&nbsp;&nbsp;
					время&nbsp;
					<select name="new_rsrv_time">
						<?
						for($i = $rsrv_time[0]; $i < $rsrv_time[1] + 1; $i++)
						{
							if($i > 23) $h = $i - 24;
							else $h = $i;
						?>
						<option value="<?=$h;?>"><?=$h.".00";?></option>
						<?
						}
						?>
					</select>
					<input class="admInput" type="hidden" name="new_rsrv_date" value="<?=$rsrv_date;?>">
					<!--<input type="hidden" name="new_rsrv_date" value="<?=$rsrv_date;?>">-->
				</td>
			</tr>
			<!--<tr>
				<td>время</td>
				<td>
					
				</td>
			</tr>-->
			<tr>				
				<td>имя клиента</td>
				<td><input class="admInput" type="text" name="new_rsrv_name"></td>
			</tr>
			<tr>
				<td>e-mail клиента</td>
				<td><input class="admInput" type="text" name="new_rsrv_mail"></td>
			</tr>
			<tr>
				<td>телефон клиента</td>
				<td><input type="text" name="new_rsrv_phone"></td>
			</tr>
			<tr>
				<td>пожелания</td>
				<td><textarea class="admInput" name="new_rsrv_msg"></textarea></td>
			</tr>
			<tr>
				<td colspan=2><input type=submit name="new_rsrv_submit" value="Создать резерв" /></td>
			</tr>
			
		</table>
	</form>
</fieldset>



</body>
</html>