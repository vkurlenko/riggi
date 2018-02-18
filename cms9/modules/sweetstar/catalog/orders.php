<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ просмотр и редатирование заказов ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

session_start();

/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/

error_reporting(E_ALL);
include "../config.php" ;
include "../db.php";

check_access(array("admin", "manager", "finans"));


// проверка прав доступа к списку пользователей по регионам
if($_SESSION['cms_user_group'] == "manager")
{
	$reg_access = $_VARS['user_access'][$_SESSION['cms_user_login']];
}
elseif($_SESSION['cms_user_group'] == "admin" || $_SESSION['cms_user_group'] == "finans") $reg_access = 0; // полный доступ
// /проверка прав доступа к списку пользователей по регионам




$table_name = $_VARS['tbl_prefix']."_orders";
$ext = "xls";


include "orders.functions.php";
include_once $_SERVER['DOC_ROOT']."/modules/shop/shop.functions.php";


// загрузка счета на сервер
if(!empty($_FILES))
{
	load_xls();	
}
//~~~


// выбор id клиентов, доступных для данного менеджера (в зависимости от региона)
$arr_in_this_region = array();
if($reg_access != 0) 
{
	$sql = "select id from `".$_VARS['tbl_prefix']."_users"."` where regRegion = $reg_access";
	$res = mysql_query($sql);	
	while($row = mysql_fetch_array($res))
	{
		$arr_in_this_region[] = $row['id'];
	}
}
//~~~


// удаление позиции из заказа
if(isset($_GET['item_id']) && isset($_GET['del_item']) && isset($_GET['order_id']))
{
	delOrderItem($_GET['item_id'], $_GET['order_id']);	
}
//~~~


// пересчет заказа после изменения кол-ва позиций
if(isset($_POST['recalc']))
{
	recalcOrder($_POST, $_GET);
}
//~~~


// добавление позиции в заказ
if(isset($_POST['add_item']))
{	
	addItem($_POST, $_GET);
}
//~~~



if(isset($_GET['id']))
{
	// выборка заказов конкретного клиента
	$sql = "select * from `$table_name` where orderUser = ".$_GET['id']." order by orderDate desc";
} 
else
{
	// фильтр для бухгалтера
	if($_SESSION['cms_user_group'] == "finans")
	{
		$sql = "select * from `$table_name` where orderStatus = 'confirmed' order by orderDate desc";
	}
	elseif(isset($_GET['filter_by']))
	{		
		// с фильтром по статусу
		$sql = "select * from `$table_name` where orderStatus = '".$_GET['filter_by']."' order by orderDate desc";
	}
	else $sql = "select * from `$table_name` where 1 order by orderDate desc";	
} 

$res = mysql_query($sql);	
?>

<?
include "head.php";
?>



<body>

<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$(".itemCount").click(function()
	{
		$(this).select();
	})
})
</script>
	
<?	

$userName= user_login(@$_GET['id']);



/*~~~ установка статуса заказа ~~~*/
if(isset($_GET['set_status']))
{
	set_status($_GET['set_status'], $_GET['order_id']);	
}
/*~~~ /установка статуса заказа ~~~*/


// вывод информации о конкретном заказе
if(isset($_GET['order_id']))
{
	$sql = "select * from `$table_name` where id = ".$_GET['order_id'];
	$res = mysql_query($sql);	
	$row = mysql_fetch_array($res);
	$listOrder = unserialize($row['orderList']);	
	$status = $row['orderStatus'];
	$user = $row['orderUser']
	?>
		<fieldset><legend>Заказ пользователя <a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=users&id=<?=$row['orderUser'];?>&edit_item"><?=user_login($user);?></a></legend>
		<span class="txt">Заказ №<?=$_GET['order_id']?>  сделан <?=format_date_time($row['orderDate'], ".")?></span>
		<form action="" method="post">
		<table cellpadding="10" cellspacing="1">
			<tr bgcolor="#FFFAC6">
				<th>№</th>
				<th>Артикул</th>
				<th>Наименование</th>
				<th>Количество</th>
				<th>Цена</th>
				<th>Итог</th>
				<th>del</th>
			</tr>
			<?
				$i = $j = 1;
				foreach($listOrder as $k => $v)
				{					
					if($k == "sum")
					{
					?>
					<tr>						
						<td>Добавить позицию:</td>
						<td colspan=2>
							<form action="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&order_id=<?=$_GET['order_id'];?>" method="post" enctype="multipart/form-data">
							<!--<select name="new_item_id[]" multiple="multiple" size="5">-->
							<select name="new_item_id" >
							<?
								$sql_4 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where item_type = 'sub_menu' and item_show = '1' order by item_rating asc";
								$res_4 = mysql_query($sql_4);
								while($row_4 = mysql_fetch_array($res_4))
								{
									?>
									<optgroup label="<?=$row_4['item_name'];?>"></optgroup>
									<?
									$sql_5 = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where item_type = 'item' and item_parent = ".$row_4['id']." and item_show = '1' order by item_rating asc";
									$res_5 = mysql_query($sql_5);
									while($row_5 = mysql_fetch_array($res_5))
									{
										// если такая позиция уже есть в заказе, то ее не выводим для добавления
										if(!array_key_exists($row_5['id'], $listOrder))
										{
											?>
											<option value="<?=$row_5['id'];?>"><?=$row_5['item_name'];?></option>
											<?
										}
									}
								}
							?>	
							</select>
							<input type="submit" name="add_item" value="Добавить" />
							</form>
						</td>					
						<td colspan=2 style="text-align:right">Итого:</td>
						<td align="center"><?=format_price($v);?></td>
					</tr>
					<tr>
						<td colspan=7 style="text-align:right"><!--<a href="#">Пересчитать</a>--><input type="submit" value="Пересчитать" name="recalc" /></td>
						
					</tr>
					<?
					}
					else
					{
						$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id = ".$k;
						$res = mysql_query($sql);
						$row = mysql_fetch_array($res);
						?>
						<tr>
							<td align="center"><?=$i++;?> [<?=$row['id'];?>]</td>
							<td><?=$row['item_art'];?></td>
							<td><?=$row['item_name'];?></td>
							<td align="center"><input type="text" class="itemCount" name="<?=$row['id']?>" size="5" value="<?=$v[0];?>" /></td>
							<td align="center"><?=format_price($v[1]);?></td>
							<td align="center"><?=format_price($v[0]*$v[1]);?></td>
							<td align="center">
								<a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&item_id=<?=$row['id']?>&del_item&order_id=<?=$_GET['order_id'];?>">
									<img src='<?=$_ICON["del"]?>' alt="del">
								</a>
							</td>
						</tr>
						<?
					}		
				}
			?>
		</table>
		</form>
		
		
		<table>
			
			<tr>
				<td>Прикрепить счет:</td>
				<td>
					<form action="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&id=<?=$user;?>&order_id=<?=$_GET['order_id'];?>" method="post" enctype="multipart/form-data">
					  <input type="file" name="filename"> 
					  <input type="submit" value="Загрузить">					
					</form>
					<br />
					<?
					if(file_exists($_SERVER['DOC_ROOT']."/files/".$_GET['order_id'].".".$ext))
					{
						?>
						<a href="/files/<?=$_GET['order_id'].".xls"?>">Скачать счет <?=$_GET['order_id'].".".$ext?></a>
						<?
					}
					?>
				</td>
			</tr>
			<tr>
				<td>Статус заказа:</td>
				<td>
					<?
					
					foreach($_VARS['order_status'] as $k => $v)
					{
						$sel = "";
						if($status == $k) $sel = " style='font-weight:bold' ";
						
						if($k != "accepted")
						{
							?><a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&order_id=<?=$_GET['order_id'];?>&set_status=<?=$k?>" class="<?=$k?>" <?=$sel?>><?=$v?></a>&nbsp;&nbsp;<?
						}
						else
						{
							?>
							<span class="<?=$k?>" <?=$sel?>><?=$v?></span>&nbsp;&nbsp;
							<?
						}
						$sel = "";
					}				
					
					?>
				</td>
			</tr>
		</table>
		<a class="serviceLink" href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&id=<?=$user;?>">Все заказы пользователя <?=user_login($user);?></a>	
		</fieldset>
		
<?	
}
else
{
	// вывод списка заказов
	if(isset($_GET['id']))
	{
		?>
		<fieldset><legend>Заказы пользователя <a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=users&id=<?=$_GET['id'];?>&edit_item"><?=$userName;?></a></legend>
		<?
	}
	else
	{
		?>
		<fieldset><legend>Все заказы</legend>
		<?
	}
?>	
	<table cellpadding="10" cellspacing="1">
		<tr>
			<th>№</th>
			<th>Клиент</th>
			<th>Дата</th>
			<th>Сумма заказа</th>
			<th>Статус заказа <br />(<?
				foreach($_VARS['order_status'] as $k => $v)
				{
					?><a class="<?=$k?>" href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&filter_by=<?=$k?>"><?=$v?></a>&nbsp;<?
				}
				?>)
			</th>
			
		</tr>
		<?
		$i = $j = 1;
		//echo "<pre>".print_r($arr_in_this_region)."</pre>";
		if($res)
		{	
			// если доступ только к конкретному региону
			if($reg_access != 0) 
			{
				while($row = mysql_fetch_array($res))
				{
					foreach($arr_in_this_region as $k => $v)
					{
						if($row['orderUser'] == $v)
						{
							$arrOrder = unserialize($row['orderList']);	
							$sql_3 = "select regLogin from `".$_VARS['tbl_prefix']."_users` where id=".$row['orderUser'];
							$res_3 = mysql_query($sql_3);
							$row_3 = mysql_fetch_array($res_3);
							$client = $row_3['regLogin'];
							?>
							<tr>
								<td align="center"><?=$row['id'];?></td>
								<td align="center"><?=$client;?></td>
								<td><a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&order_id=<?=$row['id'];?>"><?=format_date_time($row['orderDate'], ".");?></a></td>
								<td align="center"><?=format_price($arrOrder['sum']);?></td>
								<td align="center" class="<?=$row['orderStatus']?>"><?=$_VARS['order_status'][$row['orderStatus']];?></td>
							</tr>
							<?
							break;
						}
					}
				}
			}
			else
			{
				// если полный доступ
				while($row = mysql_fetch_array($res))
				{
					$arrOrder = unserialize($row['orderList']);	
					$sql_3 = "select regLogin from `".$_VARS['tbl_prefix']."_users` where id=".$row['orderUser'];					
					$res_3 = mysql_query($sql_3);
					$row_3 = mysql_fetch_array($res_3);
					$client = $row_3['regLogin'];			
					?>
					<tr>
						<td align="center"><?=$row['id'];?></td>
						<td align="center"><?=$client;?></td>
						<td><a href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders&order_id=<?=$row['id'];?>"><?=format_date_time($row['orderDate'], ".");?></a></td>
						<td align="center"><?=format_price($arrOrder['sum']);?></td>
						<td align="center" class="<?=$row['orderStatus']?>"><?=$_VARS['order_status'][$row['orderStatus']];?></td>
						
					</tr>
					<?
				}
			}			
		}
		
		?>
	</table>
	<a class="serviceLink" href="/<?=$_VARS['cms_dir'];?>/workplace.php?page=orders">Все заказы</a>	
	</fieldset>	
<?
}
?>
</body>
</html>
