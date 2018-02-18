<?
error_reporting(E_ALL);



/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ загрузка счета в формате XLS  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function load_xls()
{
	global $ext, $table_name;
	
	
	$f_info = pathinfo($_FILES["filename"]["name"]);
	if($f_info["extension"] == $ext)
	{
		if($_FILES["filename"]["size"] > 1024*3*1024)
		{
			echo ("Размер файла превышает три мегабайта");
			exit;
		}
		// Проверяем загружен ли файл
		if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
		{
			// Если файл загружен успешно, перемещаем его
			// из временной директории в конечную
			
			$name = $_GET['order_id'].".".$ext;
			if(move_uploaded_file($_FILES["filename"]["tmp_name"], $_SERVER['DOC_ROOT']."/files/".$name))
			{
				//echo mime_content_type($_SERVER['DOC_ROOT']."/files/".$name);
				$sql = "update `$table_name` set orderStatus = 'accepted' where id=".$_GET['order_id'];
				//echo $sql;
				$res = mysql_query($sql);
			};
		} 
		else 
		{
			echo("Ошибка загрузки файла");
		}
	}
	else
	{
		echo("Неверный тип файла ".$f_info["extension"]);
	}	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /загрузка счета в формате XLS  ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ установка статуса заказа  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function set_status($status, $orderId)
{
	global $table_name;
	$sql = "update `$table_name` set orderStatus = '".$status."' where id=".$orderId;
	$res = mysql_query($sql);
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /установка статуса заказа  ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ удаление позиции из заказа  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function delOrderItem($item_id, $order_id)
{
	global $table_name;
	$sql = "select * from `".$table_name."` where id = ".$order_id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$order_list = unserialize($row['orderList']);
	
	unset($order_list[$item_id]);
	
	$sum = 0;
	foreach($order_list as $k => $v)
	{
		if($k != "sum")
		{
			$sum += $v[0] * $v[1];
		}
	}
	$order_list["sum"] = $sum;
	
	$order_list_new = serialize($order_list);
	
	$sql = "update `".$table_name."` set orderList = '".$order_list_new."' where id = ".$order_id;
	$res = mysql_query($sql);	
	return $res;
	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /удаление позиции из заказа  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ добавить позицию в заказ  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function addItem($arrPost, $arrGet)
{
	global $table_name, $_VARS;
	
	$sql = "select * from `".$table_name."` where id = ".$arrGet['order_id'];
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	
	$order_list = unserialize($row['orderList']);

	//printArr($order_list);

	// узнаем цену добавляемой позиции	с учетом возможных скидок
	$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id = ".$arrPost['new_item_id'];
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$price = $row['item_price'];
	
	if($row['item_action'] == '1') 
	{
		$discount = $_VARS['env']['action_discount'];
		$price_discount =  $row['item_price'] * $discount / 100; // скидка
		$price = $row['item_price'] - $price_discount; // цена со скидкой
	}
	elseif($row['item_discount'] == '1') 
	{
		$discount = userDiskount();
		$price_discount =  $row['item_price'] * $discount / 100; // скидка
		$price = $row['item_price'] - $price_discount; // цена со скидкой
	}
	//	
	
	$order_list[$_POST['new_item_id']] = array(0, $price); // добавляем новую позицию в конец массива
	
	// перемещаем элемент, содержащий итоговую сумму заказа, в конец массива
	$sum = $order_list["sum"];
	unset($order_list["sum"]);
	$order_list["sum"] = $sum;
	//

	//printArr($order_list);
	
	$order_list_new = serialize($order_list);
	
	$sql = "update `".$table_name."` set orderList = '".$order_list_new."' where id = ".$_GET['order_id'];
	$res = mysql_query($sql);	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /добавить позицию в заказ  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ пересчет заказа  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~*/
function recalcOrder($arrPost, $arrGet)
{
	global $table_name;
	
	//printArr($_POST);
	//printArr($_GET);
	
	$sql = "select * from `".$table_name."` where id = ".$arrGet['order_id'];
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	
	$order_list = unserialize($row['orderList']);
	
	//printArr($order_list);

	$sum = 0;
	foreach($order_list as $k => $v)
	{		
		if($k != "sum")
		{
			if($arrPost[$k] < 0)
			{
				$arrPost[$k] = $v[0];
			}
			else $order_list[$k][0] = $arrPost[$k];	
			
			settype($arrPost[$k], "float");	
			
			$sum += $arrPost[$k] * $v[1];
		}
	}
	$order_list["sum"] = $sum;
	
	//printArr($order_list);
	
	$order_list_new = serialize($order_list);
	
	$sql = "update `".$table_name."` set orderList = '".$order_list_new."' where id = ".$arrGet['order_id'];
	$res = mysql_query($sql);	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /пересчет заказа  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/



function printArr($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";	
}


function user_login($user_id = false)
{
	global $_VARS;
	if($user_id)
	{
		$sql_2 = "select regLogin from `".$_VARS['tbl_prefix']."_users` where id = ".$user_id;
		$res_2 = mysql_query($sql_2);
		$row_2 = mysql_fetch_array($res_2);
		$userLogin = $row_2['regLogin'];
	} 
	else $userLogin = '';
	
	return $userLogin;
}

?> 