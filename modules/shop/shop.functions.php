<?
/*~~~ узнаем скидку для этого пользователя ~~~*/
function userDiskount()
{
	global $_VARS;
	$sql_2 = "select * from `".$_VARS['tbl_prefix']."_users` where regLogin = '".$_SESSION['userLogin']."'";
	$res_2 = mysql_query($sql_2);
	$discount = 0;
	if($res_2)
	{
		$row_2 = mysql_fetch_array($res_2);
		$discount = $row_2['regDiscount'];
	}
	return $discount;
}
/*~~~ /узнаем скидку для этого пользователя ~~~*/
?>