<?
/*~~~~~~~~~~~~~*/
/* ваши заказы */
/*~~~~~~~~~~~~~*/
$table_name = $_VARS['tbl_prefix']."_orders";

// если сессия не открыта, перенаправление
if(!isset($_SESSION['userLogin']))
{
	header("Location:/catalog/");	
}
else
{
	// если попытка открыть заказы другого пользователя, перенаправление на свою страницу
	$sql = "select * from `".$_VARS['tbl_prefix']."_users` where regLogin = '".$_SESSION['userLogin']."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	if($row['id'] != $_GET['p'])
	{
		header("Location:/your_orders/".$row['id']."/");		
	}
}


// выводим инфу о конкретном заказе
if(isset($_GET['pg']))
{
	$sql = "select * from `$table_name` where orderUser = ".$_GET['p']." and id = ".$_GET['pg'];
	$res = mysql_query($sql);	
	$row = mysql_fetch_array($res);
	$listOrder = unserialize($row['orderList']);
	?>
	<div class="list">
		<table width="100%" cellpadding="10" cellspacing="1">
			<tr class="color">
				<th>№</th>
				<th><?=lang('TH_GOOD_ART');?></th>
				<th><?=lang('TH_GOOD_NAME');?></th>
				<th><?=lang('TH_GOOD_COUNT');?></th>
				<th><?=lang('TH_GOOD_PRICE');?> (<?=$_VARS['env']['cur']?>)</th>				
				<th><?=lang('TH_GOOD_SUM');?></th>
			</tr>
			<?
				$i = $j = 1;
				foreach($listOrder as $k => $v)
				{
					if($j > 1) 
					{
						$bg = " class='color'";
						$j = 1;
					}
					else 
					{
						$bg = "";
						$j++;
					}
					
					if($k == "sum")
					{
					?>
					<tr>
						<td colspan=5 style="text-align:right"><?=lang('TH_GOOD_SUM');?>:</td>
						<td><?=$v;?></td>
					</tr>
					<?
					}
					else
					{
						$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id = ".$k;
						$res = mysql_query($sql);
						$row = mysql_fetch_array($res);
						?>
						<tr <?=$bg;?>>
							<td><?=$i++;?></td>
							<td><?=$row['item_art'];?></td>
							<td><?
							if(trim($row['item_name'.$langPrefix]) != "") echo $row['item_name'.$langPrefix];
							else echo $row['item_name'];?></td>
							<td><?=$v[0];?></td>
							<td><?=$v[1];?></td>
							<td><?=($v[0]*$v[1]);?></td>
						</tr>
						<?
					}		
				}
			?>
		</table>
	</div>
	<?	
}




else
{
// выводим список всех заказов
	$sql = "select * from `$table_name` where orderUser = ".$_GET['p']." order by orderDate desc";
	$res = mysql_query($sql);	
	?>
	<div class="list">
		<table width="100%" cellpadding="10" cellspacing="1">
			<tr class="color">
				<th>№</th>
				<th><?=lang('TH_GOOD_DATE');?></th>
				<th><?=lang('TH_GOOD_ORDER_SUM');?> (<?=$_VARS['env']['cur']?>)</th>
				<th><?=lang('TH_GOOD_ORDER_STATUS');?></th>
				<th><?=lang('TH_GOOD_ORDER_RECEIPT');?></th>
			</tr>
			<?
			$i = $j = 1;
			while($row = mysql_fetch_array($res))
			{
				$arrOrder = unserialize($row['orderList']);
				
				if($j > 1) 
				{
					$bg = " class='color'";
					$j = 1;
				}
				else 
				{
					$bg = "";
					$j++;
				}
				?>
				<tr <?=$bg;?>>
					<td><?=$row['id']?></td>
					<td><a href="/your_orders/<?=$_GET['p'];?>/<?=$row['id']?>/"><?=$row['orderDate'];?></a></td>
					<td><?=format_price($arrOrder['sum']);?></td>
					<td><?=$_VARS['order_status'][$row['orderStatus']];?></td>
					<td><?
					if(file_exists($_SERVER['DOC_ROOT']."/files/".$row['id'].".xls") && $row['orderStatus'] == "accepted")
					{
						?>
						<a href="/files/<?=$row['id'].".xls"?>"><?=lang('TH_GOOD_ORDER_RECEIPT')." ".$row['id'].".xls";?></a>
						<?
					}
					?></td>
				</tr>
				<?
			}
			?>
		</table>
	</div>
	<?
}

/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
?>