<?
function GetItems($tableName, $orderBy = "", $orderDir = "")
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml, $_VARS;
	
	$sql = "DELETE FROM `".$_MODULE_PARAM['tableName']."`
			WHERE note_date < '".date('Y-m-d')."'";
	$res = mysql_query($sql);
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY ".$orderBy." ".$orderDir;
	$res = mysql_query($sql);
	
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
					
				<th>Пользователь</th>	
				<th>Заголовок</th>
				<th>Дата</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
			
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<?
				$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users`
						WHERE id = ".$row['note_user'];
				$res_u = mysql_query($sql);
				$row_u = mysql_fetch_array($res_u);
				?>
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row_u['user_mail'];?></a></td>	
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['note_title'];?></a></td>	
				<td><?=$row['note_date'];?></td>		
						
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	
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