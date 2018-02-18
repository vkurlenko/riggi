<?
$tableName = $_VARS['tbl_prefix']."_subscribe";

function getItems()
{
	global $tableName, $_ICON;
	$sql = "select * from `".$tableName."` where 1 order by id desc";
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5  class="list">
			<tr>				
				<th>e-mail</th>
				<th>Статус текущей рассылки</th>
				<th>Дата регистрации</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td><?=$row['subscribe_mail'];?></td>
				<td align="center">
				<?
					$status_icon = "user_block";
					if($row['subscribe_status'] == '1') $status_icon = "user_ok";;
				?>
				<img src='<?=$_ICON[$status_icon]?>'>
				</td>
				<td><?=$row['subscribe_reg_date'];?></td>
				<td><a href="?page=subscribe&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить адрес?')){document.location='?page=subscribe&del_item&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
}

function readItem($id)
{
	global $tableName;
	$sql = "select * from `".$tableName."` where id = ".$id;
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);
	}	
	else $row = array();
	
	return $row;
}
?> 