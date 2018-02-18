<?
$news_table_name = $_VARS['tbl_prefix']."_news"; 
$news_favorit_table_name = "news_favorit"; 

function CreateTable($news_table_name)
{
	global $news_table_name;
	$sql = "create table `$news_table_name` (
		id 				int auto_increment primary key,
		news_cat		text,
		news_title		text,
		news_title_en	text,
		news_date		date,
		news_text_1		text,
		news_text_1_en	text,
		news_text_2		text,
		news_text_2_en	text,
		news_img_s		text,
		news_img_b		text,
		news_alb		int,
		vimeo_link		text,
		code_iPod		text,
		code_Hdvideo 	text,
		news_mark		enum('0','1'),
		news_show		enum('0','1'),
		news_src		text
	)" ;
	$res = mysql_query($sql);
	return $res;
}

//AddNews($news_table_name, $news_cat, $news_title, $news_date, $news_text_1, $news_text_2, $news_img_s,  $news_img_b, $news_mark, $news_show)
function AddNews($news_cat, $news_title, $news_title_en, $news_date, $news_text_1, $news_text_1_en, $news_text_2, $news_text_2_en, $news_img_s,  $news_img_b, $news_alb, $vimeo_link, $code_iPod, $code_HDvideo, $news_mark, $news_show, $news_src)
	{
	//echo "$news_img_b";
	global $news_table_name;
	
	if($news_show == "on")
	{
		$news_show = 1;
	}
	else $news_show = 0;
	
	if($news_mark == "on")
	{
		$news_mark = 1;
	}
	else $news_mark = 0;
	
	$sql = "insert into `$news_table_name` (news_cat, news_title, news_title_en, news_date, news_text_1, news_text_1_en, news_text_2, news_text_2_en, news_img_s,  news_img_b, news_alb, vimeo_link, code_iPod, code_HDvideo, news_mark, news_show, news_src)
	values ('$news_cat', '".addslashes($news_title)."', '".addslashes($news_title_en)."', '$news_date', '".addslashes($news_text_1)."', '".addslashes($news_text_1_en)."', '".addslashes($news_text_2)."', '".addslashes($news_text_2_en)."', '$news_img_s',  '$news_img_b', '$news_alb', '$vimeo_link', '$code_iPod', '$code_HDvideo','$news_mark', '$news_show', '$news_src')";
	//echo $sql;
	$res = mysql_query($sql);
	//msgStack($sql, $res);
	return mysql_insert_id();
	}
	
function AddToFavorit($news_id)
{
	global $news_favorit_table_name;
	
	$sql = "insert into `$news_favorit_table_name` (news_id)
	values ('$news_id')";
	$res = mysql_query($sql);
	//echo $sql;
	
	$sql = "update `$news_favorit_table_name` set news_order = ".mysql_insert_id()." where id=".mysql_insert_id();
	//echo $sql;
	$res = mysql_query($sql);
	return $res;
}

function GetNews($news_table_name, $cat)
{
	global $news_table_name, $_ICON;
	$sql = "select * from `$news_table_name` where news_cat='$cat' order by news_date desc";
	//echo $sql;
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>				
				<th>Дата</th>
				<th>Заголовок</th>
				<th>edit</th>
				<th>del</th>
			</tr>
			<?
			while($row = mysql_fetch_array($res))
			{
				?>
				<tr>
					<td><?=$row['news_date']?></td>
					<td><a href=?page=news&edit_news&id=<?=$row['id']?>&news_cat=<?=$cat?>><strong><?=$row['news_title']?></strong></a></td>
					<td><a href=?page=news&edit_news&id=<?=$row['id']?>&news_cat=<?=$cat?>><img src='<?=$_ICON["edit"]?>'></a></td>
					<td><a style='color:red' href="javascript:if (confirm('Удалить раздел?')){document.location='?page=news&del_news&id=<?=$row['id']?>&news_cat=<?=$cat?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
}

function GetMenuPos($news_table_name)
{
	global $news_table_name;
	$sql = "select * from `$news_table_name` where 1";
	$res = mysql_query($sql);
	if($res)
	{
		echo "<table border=0 cellpadding=5>\n";
		echo "<tr><td></td><td>Заголовок</td><td></td></tr>";
		while($row = mysql_fetch_array($res))
		{
			echo "<tr>\n";
			echo "<td>[<a style='color:red' href=?page=news&news_table_name=".$news_table_name."&del_news&id=".$row['id'].">X</a>]</td><td><b>".$row['news_title']."</b></td>
			<td><a href=?page=news&news_table_name=".$news_table_name."&edit_news&id=".$row['id'].">edit</a></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
}

function DelNews($news_table_name, $id)
{
	global $news_table_name;
	$sql = "delete from `$news_table_name` where id=$id";
	$res = mysql_query($sql);
	DelFavorit($id);
	return $res;
	
}

function ReadNews($news_table_name, $id)
{
	global $news_table_name;
	$sql = "select * from `$news_table_name` where id=$id";
	return SqlParseRes($sql);
}

function UpdateNews($id, $news_cat, $news_title, $news_title_en, $news_date, $news_text_1, $news_text_1_en, $news_text_2, $news_text_2_en, $news_img_s,  $news_img_b, $news_alb, $vimeo_link, $code_iPod, $code_HDvideo, $news_mark, $news_show, $news_src)
{
	global $news_table_name;
	if($news_show == "on")
	{
		$news_show = 1;
	}
	else $news_show = 0;
	
	if($news_mark == "on")
	{
		$news_mark = 1;
	}
	else $news_mark = 0;
	
	$sql = "update `$news_table_name` set 
	news_cat	= '$news_cat',
	news_title	= '".addslashes($news_title)."',
	news_title_en	= '".addslashes($news_title_en)."',
	news_date	= '$news_date', 
	news_text_1	= '".addslashes($news_text_1)."', 
	news_text_1_en	= '".addslashes($news_text_1_en)."', 
	news_text_2 = '".addslashes($news_text_2)."', 
	news_text_2_en = '".addslashes($news_text_2_en)."', 
	news_img_s  = '$news_img_s',
	news_img_b  = '$news_img_b', 
	news_alb	= '$news_alb',
	vimeo_link  = '$vimeo_link',
	code_iPod	= '$code_iPod', 
	code_HDvideo= '$code_HDvideo',	
	news_mark   = '$news_mark', 	
	news_show	='$news_show',
	news_src	= '$news_src'
	where id=$id";
	
	//echo $sql;
 
 	$res = mysql_query($sql);
	
	UpdateFavorit($id, $news_mark);
	
	return $res;
}

function UpdateFavorit($news_id, $news_mark)
{
	global $news_favorit_table_name;
	
	if($news_mark == 1)	
	{
		$sql = "select * from `$news_favorit_table_name` where news_id=".$news_id;
		//echo $sql;
		$res = mysql_query($sql);
		//echo "res=".$res;
		if(mysql_num_rows($res) == 0)
		{
			AddToFavorit($news_id);
		}
	}
	else
	{
		$sql = "select * from `$news_favorit_table_name` where news_id=".$news_id;
		//echo $sql;
		$res = mysql_query($sql);
		if(@$res)
		{
			$sql = "delete from `$news_favorit_table_name` where news_id=".$news_id;
			//echo $sql;
			$res = mysql_query($sql);
		}
	}	
	return $res;
}

function DelFavorit($news_id)
{
	global $news_favorit_table_name;
	$sql = "delete from `$news_favorit_table_name` where news_id=$news_id";
	//echo $sql;
	$res = mysql_query($sql);
	return $res;
}

function MysqlDate()
{
	
}
?>