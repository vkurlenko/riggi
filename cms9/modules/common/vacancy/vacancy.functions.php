<?
function GetItems($tableName, $orderBy = "", $orderDir = "")
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml, $_VARS;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY ".$orderBy." ".$orderDir;
	$res = mysql_query($sql);
	
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
				<th></th>				
				<th><?=$_TEXT['TEXT_HEAD']?></th>				
				<!--<th>Салон</th>-->
				<th>Вакансия открыта</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
			
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
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['vacancy_name'];?></a></td>	
					
				<!--<td><?
					/*$vacancy_place = unserialize($row['vacancy_place']);
					$i = 0;
					
					foreach($vacancy_place as $k)
					{
						if($k != 'none')
						{
							$i++;
							echo $arrSalon[$k];
							if($i < count($vacancy_place)) echo "<br>";
						}
						
					}*/
				?></td>	-->
				<td align="center"><? if($row['vacancy_active'] == '1') {?><img src='<?=$_ICON['tick']?>'><? }?></td>		
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить вакансию?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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