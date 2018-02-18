<?
$banner_group_id = $_VARS['env']['banner_group_right_colomn_id']; // id группы баннеров (см. НАСТРОЙКИ)
$sql = "select * from `".$_VARS['tbl_prefix']."_banners` where id = ".$banner_group_id;
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$banner_group_alb_id = $row['banner_group_alb']; // id альбома для группы баннеров

$sql = "select * from `photo".$banner_group_alb_id."` where 1 order by order_by asc";
$res = mysql_query($sql);
$arrBanner = array(); // массив картинок в качестве баннеров

while($row = mysql_fetch_array($res))
{
	$arrBanner[$row['id']] = $row['id'].".".$row['file_ext'];
}
?>