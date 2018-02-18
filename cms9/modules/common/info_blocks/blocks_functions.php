<?
$block_table_name = $_VARS['tbl_iblocks'];
$tags = "<strong><a><br><span><img><embed><em>";

/*function CreateTable()
{
	global $block_table_name;
	$sql = "create table `$block_table_name` (
		id 				int auto_increment primary key,
		block_marker	text,
		block_name 		text,
		block_bg_color	text,
		block_text_color text,
		block_text_value text,
		block_text_value_en text,
		block_html		int
	)" ;
	$res = mysql_query($sql);
}*/

function StripP($string)
{
	$string = trim($string);
	$string = str_replace("<p>&nbsp;</p>","",$string);
	$string = str_replace("<p>","",$string);
	$string = str_replace("</p>","<br/>",$string);
	
	return $string;	
}

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
	global $block_table_name, $_ICON;
	$sql = "select * from `$block_table_name` where 1 order by id desc";
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5  class="list">
			<tr>				
				<th>Маркер</th>
				<th>Название блока</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td><?=$row['block_marker'];?></td>
				<td><a href=?page=blocks&editItem&id=<?=$row['id'];?>><strong><?=$row['block_name'];?></strong></a></td>
				<td><a href=?page=blocks&editItem&id=<?=$row['id'];?>><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить инфоблок?')){document.location='?page=blocks&del_block&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
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

?>