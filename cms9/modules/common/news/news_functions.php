<?
function GetItems($tableName)
{
	global $_MODULE_PARAM, $_ICON;
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY id DESC";
	$res = mysql_query($sql);
	
	if(mysql_num_rows($res) > 0)
	{
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
				<th></th>		
				<th>Логин</th>
				<th>Заблокирован</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td align="center" width=45>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
				</td>
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['user_login'];?></a></td>
				<td align="center"><? if($row['user_block']) $icon = "user_ok"; else $icon = "user_block";?><img src='<?=$_ICON[$icon]?>'></td>
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить виджет?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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
	global $_MODULE_PARAM;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."`
			WHERE id = ".$id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row;
}
?>