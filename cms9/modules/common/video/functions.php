<?
function GetItems($tableName, $orderBy = "", $orderDir = "")
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml, $_VARS;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."` 
			WHERE 1
			ORDER BY ".$orderBy." ".$orderDir;
	$res = mysql_query($sql);
	
		?>
		<table border=0 cellpadding=5 class="list">
			<tr>		
				<th></th>					
				<th>Наименование</th>
				<th>Источник</th>
				<th>Дата</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		
		<?
			
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td align="center" width=45>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=asc"><img src='<?=$_ICON["down"]?>' alt="down"></a>
					<a href="?page=<?=$_MODULE_PARAM['name']?>&id=<?=$row["id"]?>&move&dir=desc"><img src='<?=$_ICON["up"]?>' alt="up"></a>
				</td>
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><?=$row['video_title'];?></a></td>	
				<td><?=$row['video_url'];?></td>
				<td><?=$row['video_create'];?></td>		
						
				
				<td><a href="?page=<?=$_MODULE_PARAM['name']?>&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить?')){document.location='?page=<?=$_MODULE_PARAM['name']?>&delItem&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	
}

function readItem($id)
{
	global $_MODULE_PARAM;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."`
			WHERE id = ".$id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row;
}

function getVideoUrl ($data) {
	//echo htmlspecialchars($data);
    if (preg_match("/<iframe.*?src=\"([^\"]+)\".*?><\/iframe>/i", $data, $matches)) {
        $url = $matches[1];
    }
	
    if (preg_match("/<object.*?>.*?<param name=\"movie\" value=\"([^\"]+)\"( \/>|><\/param>).*?<\/object>/i", $data, $matches)) {
        $url = $matches[1];
    }
    return $url;
}

function getVideoThumbUrl ($url) 
{
	//echo $url;
    if (!is_string($url) || empty($url)) return false;
    $url = str_replace("&amp;", "&", $url);
	$url = str_replace("//www.", "http://www.", $url);
    $arr = parse_url($url);
    $arr['host'] = str_replace('www.', '', $arr['host']);
    $url = "";
    switch ($arr['host']) {
        case 'rutube.ru':
            if (preg_match("/\/tracks\/(.+)\.html/i", $arr['path'], $matches)) {
                $xml = simplexml_load_file("http://rutube.ru/cgi-bin/xmlapi.cgi?rt_mode=movie&rt_movie_id=".$matches[1]."&utf=1");
                if ($xml) {
                    $url = (string) $xml->response->movie->thumbnailLink;
                }
            }
            break;
        case 'video.rutube.ru':
            if (preg_match("/\/(.+)/i", $arr['path'], $matches)) {
                $s[0] = substr($arr['path'], 1, 2);
                $s[1] = substr($arr['path'], 3, 2);
                $url = "http://tub.rutube.ru/thumbs/".$s[0]."/".$s[1]."/".$matches[1]."-1-1.jpg";
            }
            //$url = "http://img-1.rutube.ru/thumbs/".$link[0].$link[1]."/".$link[2].$link[3]."/".$link."-2.jpg";
            break;
        case 'youtube.com':
            if (preg_match("/\/(embed|v)\/(.+)\/?/i", $arr['path'], $matches)) {
                $url = "http://img.youtube.com/vi/".$matches[2]."/0.jpg";
            }
            break;
        case 'player.vimeo.com':
            if (preg_match("/\/video\/(.+)\/?/i", $arr['path'], $matches)) {
                $clip_id = $matches[1];
            }
            $xml = simplexml_load_file('http://vimeo.com/api/v2/video/'.$clip_id.'.xml');
            if ($xml) {
                $url = (string) $xml->video->thumbnail_medium;
            }
            break;
        case 'vimeo.com':
            parse_str($arr[query], $query);
            $clip_id = $query['clip_id'];
            $xml = simplexml_load_file('http://vimeo.com/api/v2/video/'.$clip_id.'.xml');
            if ($xml) {
                $url = (string) $xml->video->thumbnail_medium;
            }
            break;
        default:
            $url = "";
            break;
    }
    return $url;
}

/* копирование тумба ролика с youtube */
function copyThumb($data)
{
	global $_VARS;
	$id = 0;
	
	$url = getVideoUrl(trim(stripslashes($data)));			
	$th_url = getVideoThumbUrl($url);
	
	// в таблицу превьюшек добавим запись новую
	$sql = "INSERT INTO `".$_VARS['tbl_prefix']."_pic_".$_VARS['env']['photo_alb_video_preview']."`
			(file_ext, name, img_create)
			VALUES('jpg', '', '".date('Y-m-d')."')";
	$res = mysql_query($sql);
	
	$id = mysql_insert_id();
	
	if($res)
	{
		// обновим запись
		$sql = "UPDATE `".$_VARS['tbl_prefix']."_pic_".$_VARS['env']['photo_alb_video_preview']."`
				SET order_by = ".$id."
				WHERE id = ".$id;
		$res = mysql_query($sql);
		
		// имя файла
		$th_name = $id.'.jpg';
		$full_path = $_SERVER['DOCUMENT_ROOT'].'/pic_catalogue/'.$_VARS['tbl_prefix'].'_pic_'.$_VARS['env']['photo_alb_video_preview'].'/';
		
		// копируем файл на диск
		copy($th_url, $full_path.$th_name);
		
	}
	
	return $id;
}
?>