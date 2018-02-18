<?
function GetItems($tableName, $orderBy = "", $orderDir = "")
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml, $_VARS;
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_catalog`
			WHERE 1";
	$res = mysql_query($sql);
	$arrService = array();
	
	while($row = mysql_fetch_array($res))
	{
		$arrService[$row['id']] = $row['item_name'];
	}
	
	/*echo "<pre>";
	print_r($arrService);
	echo "</pre>";*/
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY ".$orderBy." ".$orderDir;
	$res = mysql_query($sql);
	
	
	
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
				
				<th><?=$_TEXT['TEXT_HEAD']?></th>	
						
				<th>Дата начала</th>
				<th>Дата окончания</th>
				<th>Активен</th>
				<th>Кол-во участников</th>
				<th>Победитель</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
		
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['contest_name'];?></a></td>	
				
				<td><?=$row['contest_start'];?></td>		
				<td><?=$row['contest_length'];?></td>	
				<td align="center"><? if($row['contest_active'] == '1'){ ?><img src='<?=$_ICON["tick"]?>'><? };?></td>	
				<?
				if($row['contest_items'] != '' && is_array(unserialize($row['contest_items'])))
				{
					$n = '<a href="/cms9/modules/common/contests/contests.members.php?id='.$row['id'].'">'.count(unserialize($row['contest_items'])).'</a>';
				}
				else $n = 0;
				?>
				<td><?=$n;?></td>
				<td><?=$row['contest_winner'];?></td>	
					
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить конкурс?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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