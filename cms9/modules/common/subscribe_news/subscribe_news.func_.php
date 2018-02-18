<?
$tableName = $_VARS['tbl_prefix']."_subscribe_news";

function msgOk($str)
{
	$html = '<span class="msgOk">'.$str.'</span>'.BR;
	return $html;
}

function msgError($str)
{
	$html = '<span class="msgError">'.$str.'</span>'.BR;
	return $html;
}

function getItems()
{
	global $tableName, $_ICON;
	$sql = "select * from `".$tableName."` where 1 order by id desc";
	$res = mysql_query($sql);
	if($res)
	{
		?>
		<table border=0 cellpadding=5  class="list">
			<tr>				
				<th>e-mail</th>
				<th>Статус текущей рассылки</th>
				<th>Дата регистрации</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		while($row = mysql_fetch_array($res))
		{
			?><tr>
				<td><?=$row['subscribe_mail'];?></td>
				<td align="center">
				<?
					$status_icon = "user_block";
					if($row['subscribe_status'] == '1') $status_icon = "user_ok";;
				?>
				<img src='<?=$_ICON[$status_icon]?>'>
				</td>
				<td><?=$row['subscribe_reg_date'];?></td>
				<td><a href="?page=subscribe_news&editItem&id=<?=$row['id'];?>"><img src='<?=$_ICON["edit"]?>'></a></td>
				<td><a style='color:red' href="javascript:if (confirm('Удалить адрес?')){document.location='?page=subscribe_news&del_item&id=<?=$row['id'];?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
}

function readItem($id)
{
	global $tableName;
	$sql = "select * from `".$tableName."` where id = ".$id;
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);
	}	
	else $row = array();
	
	return $row;
}

/**
 * отправка письма с вложениями
 *
 * @param string $from от кого
 * @param string $to кому

 * @param string $subject тема
 * @param string $text собственно текст (html)
 * @param string $cc копия
 * @return unknown
 */
function multipart_mail_2($from, $to, $subject, $text, $cc=null) 
{
       //global $domain; //Не забудьте проинициализировать 
	   $domain = "http://".$_SERVER['HTTP_HOST'];
	   //echo $domain;
       $headers ="From: $from\n";
       //$headers.="To: $to\n";
       if (!is_null($cc))      {

         $headers.="Cc: $cc\n";
       }
       //$headers.="Subject: $subject\n";
       $headers.="Date: ".date("r")."\n";

       $headers.="Return-Path: $from\n";
       $headers.="X-Mailer: zm php script\n";
       $headers.="MIME-Version: 1.0\n";

       $headers.="Content-Type: multipart/alternative;\n";
       $baseboundary="------------".strtoupper(md5(uniqid(rand(), true)));

       $headers.="  boundary=\"$baseboundary\"\n";
       $headers.="This is a multi-part message in MIME format.\n";
       $message="--$baseboundary\n";

       $message.="Content-Type: text/plain; charset=utf-8\n";
       $message.="Content-Transfer-Encoding: 7bit\n\n";
       $text_plain=str_replace('<p>',"\n",$text);

       $text_plain=str_replace('<b>',"",$text_plain);
       $text_plain=str_replace('</b>',"",$text_plain);

       $text_plain=str_replace('<br>',"\n",$text_plain);
       $text_plain= preg_replace('/<a(\s+)href="([^"]+)"([^>]+)>([^<]+)/i',"\$4\n\$2",$text_plain);
       $message.=strip_tags($text_plain);
	   
       //
       $message.="\n\nIts simple text. Switch to HTML view!\n\n";

       $message.="--$baseboundary\n";
       $newboundary="------------".strtoupper(md5(uniqid(rand(), true)));

       $message.="Content-Type: multipart/related;\n";
       $message.="  boundary=\"$newboundary\"\n\n\n";
       $message.="--$newboundary\n";
       $message.="Content-Type: text/html; charset=utf-8\n";

       $message.="Content-Transfer-Encoding: 7bit\n\n";
       $message.=($text)."\n\n";
	   
	   
       //preg_match_all('/img(\s+)src="([^"]+)"/i',$text,$m);
	   preg_match_all('/(\s+)src="([^"]+)"/i',$text,$m);

       if (isset($m[2])) 
	   {
		   $img_f=$m[2];
		   if (is_array($img_f)) 
		   {
			   foreach ($img_f as $k => $v) 
			   {
				   $img_f[$k]=str_ireplace($domain.'/','',$v);
			   }
		   }
       }
	   
	   	$attachment_files=$img_f;
		if (is_array($attachment_files)) 
		{
			foreach($attachment_files as $filename)  
			{
				$file_content = file_get_contents($_SERVER['DOC_ROOT']."/".$filename,true);
	  
				$mime_type='image/png';
				if(function_exists("mime_content_type"))  
				{
	            	$mime_type=mime_content_type($filename);
    			}
				else 
				{
	   				$f = getimagesize($_SERVER['DOC_ROOT']."/".$filename);
					switch ($f[2])    
			   		{
                       case 'jpg': $mime_type='image/jpeg';break;
                       case 'gif': $mime_type='image/gif';break;
                       case 'png': $mime_type='image/png';break;
                       default:;
               		}
       			}
							
				$message=str_replace($domain.'/'.$filename,'cid:'.basename($filename),$message);
				$filename=basename($filename);
				$message.="--$newboundary\n";
				$message.="Content-Type: $mime_type;\n";
				$message.=" name=\"$filename\"\n";
				
				$message.="Content-Transfer-Encoding: base64\n";
				$message.="Content-ID: <$filename>\n";
				$message.="Content-Disposition: inline;\n";
				$message.=" filename=\"$filename\"\n\n";
				
				$message.=chunk_split(base64_encode($file_content));
			}
		}
		$message.="--$newboundary--\n\n";
		$message.="--$baseboundary--\n";
		
		return mail($to, $subject, $message , $headers);
}
?> 