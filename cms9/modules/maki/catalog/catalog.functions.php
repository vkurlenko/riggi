<?
function GetNews($news_table_name)
{
	global $news_table_name;
	$sql = "select * from `$news_table_name` where 1";
	$res = mysql_query($sql);
	if($res)
	{
		echo "<table border=0 cellpadding=5>\n";
		echo "<tr><td></td><td>Дата</td><td>Заголовок</td><td></td></tr>";
		while($row = mysql_fetch_array($res))
		{
			echo "<tr>\n";
			echo "<td>[<a style='color:red' href=?page=catalog_volumes&del_news&id=".$row['id'].">X</a>]</td><td>[ ".$row['news_date']." ]</td><td><b>".$row['news_title']."</b></td>
			<td><a href=?page=catalog_volumes&edit_news&id=".$row['id'].">edit</a></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
}

function GetMenuPos($news_table_name)
{
	global $_ICON;
	$sql = "select * from `$news_table_name` where 1";
	//echo $sql;
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5>
			<tr>				
				
				<th>Заголовок раздела</th>
				<th>edit</th>
				<th>del</th>
			</tr>
			<?
			while($row = mysql_fetch_array($res))
			{
				?>
				<tr>					
					<td><?=$row['cat_vol_title']?></td>
					<td><a href="?page=catalog_volumes&editItem&id=<?=$row['id']?>"><img src='<?=$_ICON["edit"]?>'></a></td>
					<td><a style='color:red' href="javascript:if (confirm('Удалить раздел?')){document.location='?page=catalog_volumes&del_news&id=<?=$row['id']?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
				</tr>
			<?
			}
			?>
		</table>
		<?
	}
}


function ReadNews($news_table_name, $id)
{
	$sql = "select * from `$news_table_name` where id=$id";
	return SqlParseRes($sql);
}

?>