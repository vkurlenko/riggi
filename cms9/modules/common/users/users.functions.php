<?
function GetItems($tableName)
{
	global $_MODULE_PARAM, $_ICON;
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY user_register ASC, user_reg_date DESC";
	$res = mysql_query($sql);
	
	if(mysql_num_rows($res) > 0)
	{
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
				<th></th>		
				<th>Имя</th>
							
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
				<?
				$class = "";
				if($row['user_register'] != '1') $class = " style='color:#ccc' ";				
				?>
				<td>
					<a <?=$class?> href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>">
						<?=$row['user_name'];?>
					</a>
				</td>
				
				<td align="center"><? if($row['user_block']) {?><img src='<?=$_ICON['lock']?>'><? }?></td>
				<td>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>">
						<img src='<?=$_ICON["edit"]?>'>
					</a>
				</td>
				<td><a href="javascript:if (confirm('Удалить пользователя?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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