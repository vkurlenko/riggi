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
				<th></th>				
				<th><?=$_TEXT['TEXT_HEAD']?></th>	
				<!--<th>Услуги</th>	-->		
				<th>Специализация</th>
				<!--<th>Салон</th>-->
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
		$arrSpec = array();
		foreach($_VARS['master_spec'] as $k) $arrSpec[$k[0]] = $k[1];	
		
		$sql = "SELECT * FROM `".$_VARS['tbl_pages_name']."` 
				WHERE p_parent_id = 9";
		$res_s = mysql_query($sql);
		$arrSalon = array();
		while($row_s = mysql_fetch_array($res_s))
		{
			$arrSalon[$row_s['p_url']] = $row_s['p_title'];
		}
		
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td align="center" width=45>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
				</td>
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['master_name'];?></a></td>	
				<!--<td>
				<?
				/*if(trim($row['master_service']) != '' && is_array(unserialize($row['master_service'])))
				{
					$arrMasterService = unserialize($row['master_service']);
					
					foreach($arrMasterService as $k)
					{
						if(isset($arrService[$k])) echo $arrService[$k]."<br>";
					}
				}*/
				?>
				</td>-->
				<td><?=$arrSpec[$row['master_spec']];?></td>		
				
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить позицию?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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