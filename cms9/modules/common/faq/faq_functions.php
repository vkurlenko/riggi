<?
$block_table_name = $tableName;


function AddBlock($block_marker, $block_name, $block_image_id, $block_image_alt, $block_bg_color, $block_text_color, $block_text_value, $block_text_value_en, $block_html)
	{
	global $block_table_name;
	global $tags;
	if($block_html == "on")
	{
		$block_text_value = strip_tags($block_text_value, $tags);
		$block_text_value_en = strip_tags($block_text_value_en, $tags);
		$block_html = 1;
	}
	else $block_html = 0;
	
	$sql = "insert into `$block_table_name` (block_marker, block_name, block_bg_color, block_text_color, block_text_value, block_text_value_en, block_html )
	values ('$block_marker', '$block_name', '$block_bg_color', '$block_text_color', '$block_text_value', '$block_text_value_en', '$block_html')";
	
	$res = mysql_query($sql);
	return $res;
	}
	
function GetBlocks()
{
	global $block_table_name, $_ICON, $faqType;
	$sql = "select * from `$block_table_name` where faqType = '".$faqType."' order by faqOrder asc";
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5  class="list">
			<tr>		
				<th></th>		
				<th>Вопрос</th>
				<th>Есть ответ</th>
				<th>Опубликован</th>				
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td align="center" width=45>
					<a href="?page=faq&faqType=<?=$faqType?>&id=<?=$row["id"]?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=faq&faqType=<?=$faqType?>&id=<?=$row["id"]?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
				</td>				
				<td><a href="?page=faq&faqType=<?=$faqType?>&editItem&id=<?=$row['id'];?>"><strong><?=$row['faqQuestion'];?></strong></a></td>
				
				<td align="center"><? if(trim($row['faqAnswer']) != '') $icon = "user_ok"; else  $icon = "user_block";?><img src='<?=$_ICON[$icon]?>'></td>
				<td align="center"><? if($row['faqShow'] == '1') $icon = "user_ok"; else  $icon = "user_block";?><img src='<?=$_ICON[$icon]?>'></td>
				<td><a href="?page=faq&faqType=<?=$faqType?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить вопрос?')){document.location='?page=faq&faqType=<?=$faqType?>&del_block&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
	//return SqlParseRes($res);
}

/*function DelBlock($id)
{
	global $block_table_name;
	$sql = "delete from `$block_table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}*/

function ReadBlock($id)
{
	global $block_table_name;
	$sql = "select * from `$block_table_name` where id=$id";
	return SqlParseRes($sql);
}

function UpdateBlock($id, $block_marker, $block_name, $block_bg_color, $block_text_color, $block_text_value, $block_text_value_en, $block_html)
{
	global $block_table_name;
	global $tags;
	if($block_html == "on")
	{
		$block_text_value = strip_tags($block_text_value, $tags);
		$block_text_value_en = strip_tags($block_text_value_en, $tags);
		$block_html = 1;
	}
	else $block_html = 0;
	
	$sql = "update `$block_table_name` set 
	block_marker = '$block_marker',
	block_name='$block_name', 
	block_bg_color='$block_bg_color', 
	block_text_color='$block_text_color', 
	block_text_value='$block_text_value' ,
	block_text_value_en='$block_text_value_en' ,
	block_html='$block_html'
	where id=$id";
	//block_text_value='".StripP($block_text_value)."' 
	$res = mysql_query($sql);
	return $res;
}


/*	изменение порядка следования записей	*/
function MoveItem($id, $direction)
{
	global $tableName;
	$table_name = $tableName;
	
	
	$order_field = "faqOrder";
	
	if($direction == "asc") $arrow = ">";
	elseif($direction == "desc") $arrow = "<";
	
	$sql = "select * from `".$table_name."` where id=".$id;
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);

	$sql = "select * from `".$table_name."` where (".$order_field." ".$arrow." ".$row[$order_field].") order by ".$order_field." ".$direction." limit 1 ";
	//echo $sql."<br>";
	$res = mysql_query($sql);
	$row_2 = mysql_fetch_array($res);
	
	$sql = "update `".$table_name."` set ".$order_field."=".$row_2[$order_field]." where id=".$id;
	//echo $sql."<br>";
	$res = mysql_query($sql);
	
	$sql = "update `".$table_name."` set ".$order_field."=".$row[$order_field]." where id=".$row_2['id'];
	//echo $sql."<br>";
	$res = mysql_query($sql);
}

?>