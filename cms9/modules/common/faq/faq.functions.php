<?
function GetItems($tableName, $orderBy = "", $orderDir = "", $where = '1')
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml, $_VARS;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE ".$where."
			ORDER BY ".$orderBy." ".$orderDir;
	$res = mysql_query($sql);
	mb_internal_encoding("UTF-8");
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
						
				<th><?=$_TEXT['TEXT_HEAD']?></th>
				<th>привязка</th>
				<th>дата</th>
				<th>есть ответ</th>						
				<th>показывать на сайте</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><? if(mb_strlen($row['faqQuestion']) > 100) echo mb_substr($row['faqQuestion'], 0, 100).'...'; else echo $row['faqQuestion'];?></a></td>		
				<td>
				<?
				if($row['faqType'] == 'ref_master')
				{
					$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_masters`
							WHERE id = ".$row['faqPerson'];
					$res_obj = mysql_query($sql);
					$row_obj = mysql_fetch_array($res_obj);
					echo $row_obj['master_name'];
				}
				elseif($row['faqType'] == 'ref_salon')
				{
					$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_catalog`
							WHERE id = '".$row['faqPerson']."'";
					//echo $sql;
					$res_obj = mysql_query($sql);
					$row_obj = mysql_fetch_array($res_obj);
					echo $row_obj['item_name'];
				
				}
				else{}
				?>
				</td>
				<td><?=$row['faqDate']?></td>
				<td align="center"><? if(trim($row['faqAnswer']) != ''){?><img src='<?=$_ICON['user_ok']?>'><? }?></td>
				<td align="center"><? if($row['faqShow'] == '1'){?><img src='<?=$_ICON['user_ok']?>'><? }?></td>			
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				
				<td><a style='color:red' href="javascript:if (confirm('Удалить сообщение?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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