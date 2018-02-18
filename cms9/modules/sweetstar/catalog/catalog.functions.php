<?
function CreateTable($news_table_name)
{
	global $news_table_name;
	$sql = "create table `$news_table_name` (
		id 		int auto_increment primary key,
		news_title	text,
		news_text	text,
		news_text_2	text,
		news_img	text,
		news_date	date,
		news_show	int
	)" ;
	$res = mysql_query($sql);
	return $res;
}

function AddNews($news_table_name, $news_title, $news_text, $news_text_2, $news_img, $news_date, $news_show)
	{
	global $news_table_name;
	
	if($news_show == "on")
	{
		$news_show = 1;
	}
	else $news_show = 0;
	
	$sql = "insert into `$news_table_name` (news_title, news_text, news_text_2, news_img, news_date, news_show)
	values ('$news_title', '$news_text', '$news_text_2', '$news_img', '$news_date', '$news_show')";
	
	$res = mysql_query($sql);
	return $res;
	}
	
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
			echo "<td>[<a style='color:red' href=?page=catalog_volumes&del_news&id=".$row['id'].">X</a>]</td><td><b>".$row['news_title']."</b></td>
			<td><a href=?page=catalog_volumes&edit_news&id=".$row['id'].">edit</a></td>\n";
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
	return $res;
}

function ReadNews($news_table_name, $id)
{
	global $news_table_name;
	$sql = "select * from `$news_table_name` where id=$id";
	return SqlParseRes($sql);
}

function UpdateNews($news_table_name, $id, $news_title, $news_text, $news_text_2, $news_img, $news_date, $news_show)
{
	global $news_table_name;
	if($news_show == "on")
	{
		$news_show = 1;
	}
	else $news_show = 0;
	
	$sql = "update `$news_table_name` set 
	news_title = '$news_title',
	news_text='$news_text', 
	news_text_2='$news_text_2', 
	news_img='$news_img', 
	news_date='$news_date', 
	news_show='$news_show' 
	where id=$id";
 
 	$res = mysql_query($sql);
	return $res;
}
?>