<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* вывод результатов поиска по тегам */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

//echo $_SERVER['REQUEST_URI'];

$pos = strpos($_SERVER['REQUEST_URI'], "?");
if($pos !== false)
{
	$req = explode("=", substr($_SERVER['REQUEST_URI'], $pos+1));
	$tagString = urldecode($req[1]);
	/*echo $tagString;
	$tagString = str_replace("&nbsp;", htmlspecialchars("&nbsp;"), $tagString);*/
	
	// делаем выборку тегов из БД (страницы сайта)
	$sql = "SELECT * FROM `".$_VARS['tbl_pages_name']."` where ".$_VARS['tbl_pages_name'].".prim LIKE '%".$tagString."%'";
	//echo htmlspecialchars($sql);
	$res = mysql_query($sql);
	//echo mysql_num_rows($res);
	while($row = mysql_fetch_array($res))
	{
		?>
		<p><a href="/<?=$row['title_s'];?>/"><?=$row['title'.$langPrefix];?></a></p>
		<?
		echo "<p>".substr($row['text'.$langPrefix], 0, 300)."</p>";
	}
	
	if($_SESSION['access'])
	{
		// делаем выборку тегов из БД (каталог)
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_catalog_items` where item_tags LIKE '%".$tagString."%'";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			?>
			<p><a href="/item/<?=$row['id'];?>/"><?=$row['item_name'.$langPrefix];?></a></p>
			<?
			echo "<p>".substr($row['item_text'.$langPrefix], 0, 300)."</p>";
		}
	}
}


/*echo "<pre>";
print_r($_SERVER);
echo "</pre>";*/
?>