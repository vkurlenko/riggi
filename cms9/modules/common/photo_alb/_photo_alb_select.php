<?	
	/*~~~ выпадающий список выбора фотоальбома ~~~*/
	
	
	// $check_val - переменная для проверки на совпадение, ее значение указывается до вызова этого модуля
	$tbl_photo_alb = $_VARS['tbl_photo_alb_name']; // таблица фотоальбомов	
	$sel = "";
	
	$sql = "select * from `".$tbl_photo_alb."` order by `id` desc";
	$res = mysql_query($sql);
	
	echo "<option value='0'>Без альбома\n";
	while($row2 = mysql_fetch_array($res))
	{
		if(isset($check_val))
		{
			if($row2['id'] == $check_val) $sel = " selected ";
		}
		echo "<option value='".$row2['id']."' ".$sel.">".$row2['alb_title']."\n";
		$sel = "";
	}
?>
