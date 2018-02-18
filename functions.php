<?php
##########################################
/*			функции						*/
##########################################

function printArray($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

// проверка есть ли у данной позиции в каталоге дочерние позиции
function isChild($itemId)
{
	global $_VARS;
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_catalog`
			WHERE item_parent = ".$itemId;
	$res_child = mysql_query($sql);
	
	if($res_child && mysql_num_rows($res_child) > 0)	
	{
		return true;	
	}
	else return false;	
}

function readframe($file) 
{
	if (! ($f = fopen($file, 'rb')) ) die("Unable to open " . $file);
	$res['filesize'] = filesize($file);
	do {
	while (fread($f,1) != Chr(255)) { // Find the first frame
	if (feof($f)) die( "No mpeg frame found") ;
	}
	fseek($f, ftell($f) - 1); // back up one byte
	
	$frameoffset = ftell($f);
	
	$r = fread($f, 4);
	
	$bits = sprintf("%'08b%'08b%'08b%'08b", ord($r{0}), ord($r{1}), ord($r{2}), ord($r{3}));
	}
	while (!$bits[8] and !$bits[9] and !$bits[10]); // 1st 8 bits true from the while
	
	// Detect VBR header
	if ($bits[11] == 0) {
	if (($bits[24] == 1) && ($bits[25] == 1)) {
	$vbroffset = 9; // MPEG 2.5 Mono
	} else {
	$vbroffset = 17; // MPEG 2.5 Stereo
	}
	} else if ($bits[12] == 0) {
	if (($bits[24] == 1) && ($bits[25] == 1)) {
	$vbroffset = 9; // MPEG 2 Mono
	} else {
	$vbroffset = 17; // MPEG 2 Stereo
	}
	} else {
	if (($bits[24] == 1) && ($bits[25] == 1)) {
	$vbroffset = 17; // MPEG 1 Mono
	} else {
	$vbroffset = 32; // MPEG 1 Stereo
	}
	}
	
	fseek($f, ftell($f) + $vbroffset);
	$r = fread($f, 4);
	
	switch ($r) {
	case 'Xing':
	$res['encoding_type'] = 'VBR';
	case 'VBRI':
	default:
	if ($vbroffset != 32) {
	// VBRI Header is fixed after 32 bytes, so maybe we are looking at the wrong place.
	fseek($f, ftell($f) + 32 - $vbroffset);
	$r = fread($f, 4);
	
	if ($r != 'VBRI') {
	$res['encoding_type'] = 'CBR';
	break;
	}
	} else {
	$res['encoding_type'] = 'CBR';
	break;
	}
	
	$res['encoding_type'] = 'VBR';
	}
	
	fclose($f);
	
	if ($bits[11] == 0) {
	$res['mpeg_ver'] = "2.5";
	$bitrates = array(
	'1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
	'2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
	'3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
	);
	} else if ($bits[12] == 0) {
	$res['mpeg_ver'] = "2";
	$bitrates = array(
	'1' => array(0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256, 0),
	'2' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
	'3' => array(0,  8, 16, 24, 32, 40, 48,  56,  64,  80,  96, 112, 128, 144, 160, 0),
	);
	} else {
	$res['mpeg_ver'] = "1";
	$bitrates = array(
	'1' => array(0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448, 0),
	'2' => array(0, 32, 48, 56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 384, 0),
	'3' => array(0, 32, 40, 48,  56,  64,  80,  96, 112, 128, 160, 192, 224, 256, 320, 0),
	);
	}
	
	$layer = array(
	array(0,3),
	array(2,1),
	);
	$res['layer'] = $layer[$bits[13]][$bits[14]];
	
	if ($bits[15] == 0) {
	// It's backwards, if the bit is not set then it is protected.
	$res['crc'] = true;
	}
	
	$bitrate = 0;
	if ($bits[16] == 1) $bitrate += 8;
	if ($bits[17] == 1) $bitrate += 4;
	if ($bits[18] == 1) $bitrate += 2;
	if ($bits[19] == 1) $bitrate += 1;
	$res['bitrate'] = $bitrates[$res['layer']][$bitrate];
	
	$frequency = array(
	'1' => array(
	'0' => array(44100, 48000),
	'1' => array(32000, 0),
	),
	'2' => array(
	'0' => array(22050, 24000),
	'1' => array(16000, 0),
	),
	'2.5' => array(
	'0' => array(11025, 12000),
	'1' => array(8000, 0),
	),
	);
	$res['frequency'] = $frequency[$res['mpeg_ver']][$bits[20]][$bits[21]];
	
	$mode = array(
	array('Stereo', 'Joint Stereo'),
	array('Dual Channel', 'Mono'),
	);
	$res['mode'] = $mode[$bits[24]][$bits[25]];
	
	$samplesperframe = array(
	'1' => array(
	'1' => 384,
	'2' => 1152,
	'3' => 1152
	),
	'2' => array(
	'1' => 384,
	'2' => 1152,
	'3' => 576
	),
	'2.5' => array(
	'1' => 384,
	'2' => 1152,
	'3' => 576
	),
	);
	$res['samples_per_frame'] = $samplesperframe[$res['mpeg_ver']][$res['layer']];
	
	if ($res['encoding_type'] != 'VBR') {
	if ($res['bitrate'] == 0) {
	$s = -1;
	} else {
	$s = ((8*filesize($file))/1000) / $res['bitrate'];
	}
	$res['length'] = sprintf('%02d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
	$res['lengthh'] = sprintf('%02d:%02d:%02d',floor($s/3600),floor($s/60),floor($s-(floor($s/60)*60)));
	$res['lengths'] = (int)$s;
	
	$res['samples'] = ceil($res['lengths'] * $res['frequency']);
	if(0 != $res['samples_per_frame']) {
	$res['frames'] = ceil($res['samples'] / $res['samples_per_frame']);
	} else {
	$res['frames'] = 0;
	}
	$res['musicsize'] = ceil($res['lengths'] * $res['bitrate'] * 1000 / 8);
	} else {
	$res['samples'] = $res['samples_per_frame'] * $res['frames'];
	$s = $res['samples'] / $res['frequency'];
	
	$res['length'] = sprintf('%02d:%02d',floor($s/60),floor($s-(floor($s/60)*60)));
	$res['lengthh'] = sprintf('%02d:%02d:%02d',floor($s/3600),floor($s/60),floor($s-(floor($s/60)*60)));
	$res['lengths'] = (int)$s;
	
	$res['bitrate'] = (int)(($res['musicsize'] / $s) * 8 / 1000);
	}
	
	return $res;
} 

function get_data2($smtp_conn)
{
    $data2="";
    while($str = fgets($smtp_conn,515))
    {
        $data2 .= $str;
        if(substr($str,3,1) == " ") { break; }
    }
    return $data2;
}

function sendSmtp($to_user, $subject, $msg)
{
	$from_user = "Sweetstar";
	$from_mail = "site@sweetstar.ru";
	$server		= "smtp.rambler.ru";
	$port	= 587;
	$login	= "vkurlenko";
	$pwd	= "DxDtSP";
	$mail	= "vkurlenko@rambler.ru";

	$header="Date: ".date("D, j M Y G:i:s")." +0700\r\n";
	$header.="From: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode($from_user)))."?= <".$from_mail.">\r\n";
	$header.="X-Mailer: The Bat! (v3.99.3) Professional\r\n";
	$header.="Reply-To: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode($from_user)))."?= <".$from_mail.">\r\n";
	$header.="X-Priority: 3 (Normal)\r\n";
	$header.="Message-ID: <172562218.".date("YmjHis")."@sweetstar.ru>\r\n";
	$header.="To: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode($to_user)))."?= <".$to_user.">\r\n";
	$header.="Subject: =?windows-1251?Q?".str_replace("+","_",str_replace("%","=",urlencode($subject)))."?=\r\n";
	$header.="MIME-Version: 1.0\r\n";
	$header.="Content-Type: text/plain; charset=utf-8\r\n";
	$header.="Content-Transfer-Encoding: 8bit\r\n";
	
	$text=$msg;
	
	$smtp_conn = fsockopen($server, $port,$errno, $errstr, 10);
	if(!$smtp_conn) {print "соединение с сервером не прошло"; fclose($smtp_conn); exit;}
	$data = get_data2($smtp_conn);
	fputs($smtp_conn,"EHLO vasya\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 250) {print "ошибка приветсвия EHLO"; fclose($smtp_conn); exit;}
	fputs($smtp_conn,"AUTH LOGIN\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 334) {print "сервер не разрешил начать авторизацию"; fclose($smtp_conn); exit;}
	
	fputs($smtp_conn,base64_encode($login)."\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 334) {print "ошибка доступа к такому юзеру"; fclose($smtp_conn); exit;}
	
	
	fputs($smtp_conn,base64_encode($pwd)."\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 235) {print "не правильный пароль"; fclose($smtp_conn); exit;}
	
	$size_msg=strlen($header."\r\n".$text);
	
	fputs($smtp_conn,"MAIL FROM:<".$mail."> SIZE=".$size_msg."\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 250) {print "сервер отказал в команде MAIL FROM"; fclose($smtp_conn); exit;}
	
	fputs($smtp_conn,"RCPT TO:<".$to_user.">\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 250 AND $code != 251) {print "Сервер не принял команду RCPT TO"; fclose($smtp_conn); exit;}
	
	fputs($smtp_conn,"DATA\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 354) {print "сервер не принял DATA"; fclose($smtp_conn); exit;}
	
	fputs($smtp_conn,$header."\r\n".$text."\r\n.\r\n");
	$code = substr(get_data2($smtp_conn),0,3);
	if($code != 250) {print "ошибка отправки письма"; fclose($smtp_conn); exit;}
	
	fputs($smtp_conn,"QUIT\r\n");
	fclose($smtp_conn);
}





/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ вывод текста на разных языках ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function lang($label)
{
	global $langPrefix, $langIndex, $LANG;
	$string = '';
	
	if($LANG[$label][$langIndex] == '')
	{
		$string = $label;
	}
	else $string = $LANG[$label][$langIndex];
	
	return $string;	
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /вывод текста на разных языках ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ вывод отладочных сообщений ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$arrMsg = array();

// запись сообщений в массив
function msgStack($msg, $type)
{	
	global $arrMsg;
	switch($type)
	{
		case true 	: $arrMsg[] = "<span class='msgOk'>".$msg."</span>"; break;
		case false 	: $arrMsg[] = "<span class='msgError'>".$msg."</span>"; break;
		default 	: break;
	}
}

// печать сообщений
function showMsg($showDebug)
{
	global $arrMsg;
	if($showDebug)
	{
		foreach($arrMsg as $k)
		{
			echo $k;
		}
	}	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /вывод отладочных сообщений ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ преобразование формата вывода даты ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

// из YYYY-MM-DD в DD/MM/YYYY
function format_date($date, $sel_2 = "/" )
{
	$sel_1 = "-";
	//$sel_2 = "/";
	$arr = explode("-", $date);
	$str = $arr[2].$sel_2.$arr[1].$sel_2.$arr[0];
	return $str;
	
}

function format_date_time($date_time, $sel_2 = "/" )
{
	$sel = " ";
	$sel_1 = "-";
	//$sel_2 = "/";
	$arr = explode($sel, $date_time);
	$date = $arr[0];
	$time = $arr[1];
	
	$arr_date = explode($sel_1, $date);
	$date_str = $arr_date[2].$sel_2.$arr_date[1].$sel_2.$arr_date[0];
	
	$str = $date_str." ".$time; 
	
	return $str;	
}

// из YYYY-MM-DD в DD месяц YYYY
function format_date_to_str($date, $sel_2 = " ", $year = false, $lang)
{
	$sel_1 = "-";
	
	$arrMonth = array(
		''		=> array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'),
		'_eng' 	=> array('', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'oktober', 'november', 'december')
	
	);
	$arr = explode("-", $date);
	
	
	$str = ($arr[2] * 1).$sel_2.$arrMonth[$lang][($arr[1] * 1)];
	
	if($year) 
		$str .= $sel_2.$arr[0] * 1;
		
	return $str;
	
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /преобразование формата вывода даты ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ преобразование формата цены в 0.00 ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function format_price($price)
{
	if(strpos($price, ".") === false) $price = $price.".00";
	return $price;
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /преобразование формата цены в 0.00 ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/









/*~~~ замена цифр картинками ~~~*/
function digitToImg($num, $color)
{
	$len = strlen($num);
	$number = '';
	for($i = 0; $i < $len; $i++)
	{
		$number .= '<img src="/img/digit/'.$color.'/'.substr($num, $i, 1).'.png" alt="'.substr($num, $i, 1).'" />';
	}
	return $number;
}
/*~~~ /замена цифр картинками ~~~*/


function convert_date($sql_date, $flag = 0)
{
	$arr = explode("-", $sql_date);
	switch($arr[1])
	{
		case 1 : $m = 'января'; $m1 = 'Январь';  break;
		case 2 : $m = 'февраля';$m1 = 'Февраль';break;
		case 3 : $m = 'марта'; 	$m1 = 'Март'; break;
		case 4 : $m = 'апреля'; $m1 = 'Апрель'; break;
		case 5 : $m = 'мая'; 	$m1 = 'Май'; break;
		case 6 : $m = 'июня'; 	$m1 = 'Июнь'; break;
		case 7 : $m = 'июля'; 	$m1 = 'Июль'; break;
		case 8 : $m = 'августа'; 	$m1 = 'Август'; break;
		case 9 : $m = 'сентября'; 	$m1 = 'Сентябрь'; break;
		case 10 : $m = 'октября'; 	$m1 = 'Октябрь'; break;
		case 11 : $m = 'ноября'; 	$m1 = 'Ноябрь'; break;
		case 12 : $m = 'декабря'; 	$m1 = 'Декабрь'; break;
		default: $m = ''; break;
	}
	
	if($flag == 1) $date = $m1."&nbsp;".$arr[0];
	else $date = $arr[2]."&nbsp;".$m."&nbsp;".$arr[0];
	return $date;
}

function insertCit()
{
	global $_VARS;
	$arr = array();
	$sql = "select `id` from `".$_VARS['tbl_prefix']."_cit` where cit_active = '1'";
	$res = mysql_query($sql);
	
	while($row = mysql_fetch_array($res))
	{
		$arr[] = $row['id'];
	}
	mt_srand(time()+(double)microtime()*1000000);
	$n = mt_rand(0, (count($arr) - 1));
	
	$sql = "select * from `".$_VARS['tbl_prefix']."_cit` where id=".$arr[$n];
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	
	$tpl_file = "slogan.intext.php";
	$dir = chdir($_SERVER['DOC_ROOT']."/inc");
	$code = file($tpl_file);
	$tpl_code = "";
	foreach($code as $str)
	{
		$tpl_code .= $str; 
	}
	$tpl_code = str_replace("{cit_text}", $row['cit_text'], $tpl_code);
	$tpl_code = str_replace("{cit_note}", $row['cit_note'], $tpl_code);
	
	return $tpl_code;
}

function is_razdel_on($razd) 
{
	global $_VARS;
	$rm=mysql_query("select `p_parent_id`, `p_show`, `p_url` from `".$_VARS['tbl_pages_name']."` where `id`='$razd'");
	$em=mysql_fetch_array($rm);
	if(!mysql_num_rows($rm)>0 OR $em["p_show"]==0) { header("Location: /"); exit; }
	$nannies[]=$em["p_url"];
	if($em["p_parent_id"]!=0)
	{
		$parent=$em["p_parent_id"];
		while( !isset($e5) OR $e5["p_parent_id"] != 0 ) 
		{
			$e5=mysql_fetch_array(mysql_query(" select `p_parent_id`, `p_show`, `p_url` from `".$_VARS['tbl_pages_name']."` where `id`='$parent'   "));
			if($e5["p_show"]==0){ header("Location: /"); exit; 
		}
		$parent=$e5["p_parent_id"];
		$nannies[]=$e5["p_url"];
		}
	}
	return $nannies;
}

///////////////////////////////////////////////
/*	вставка инфоблоков	*/
function InsertBlockBD($block_marker)// вставка инфоблока напрямую из БД (если HTML-шаблон не используется )
{
	global $langPrefix, $_VARS;
	$sql = "select * from `".$_VARS['tbl_prefix']."_iblocks` where block_marker='$block_marker'";// из БД читаем запись с маркером
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$string = $row['block_text'.$langPrefix];// в строке-шаблоне заменяем маркер на текст инфоблока
	return $string;// полученную строку вставляем в общий поток вывода html-страницы
}

function InsertBlockTemp($file_name)// вставка инфоблока из файла-шаблона (если HTML-шаблон используется )
{	
	$string = trim(str_from_file($file_name));	// читаем в строку html-шаблон
	$str_len = strlen($string);					// выделяем строку-маркер (заключена в скобки { } ) 
	$marker_begin = strpos($string, "{");
	$marker_end = strpos($string, "}");
	$marker_len = $marker_end-$marker_begin-1;
	$marker = substr($string, $marker_begin+1, $marker_len);
	$sql = "select *from `".$_VARS['tbl_prefix']."_iblocks` where block_marker='$marker'"; // из БД читаем запись с маркером
	$res = mysql_query($sql);	
	$row = mysql_fetch_array($res);
	//echo $row['block_text_value'];
	$string = str_replace("{".$marker."}", $row['block_text_value'], $string); // в строке-шаблоне заменяем маркер на текст инфоблока
	if(strpos($string, "[style]") === ""){}	// если в БД заданы какие-то стили, то в строку шаблон подставляем параметр style
	else
	{
		$style_string = "";
		if($row['block_bg_color'] != "") $style_string .= "background:".$row['block_bg_color']."; ";
		if($row['block_text_color'] != "") $style_string .= "color:".$row['block_text_color']."; ";
		$string = str_replace("[style]", $style_string, $string);
	}	
	return $string;	// полученную строку вставляем в общий поток вывода html-страницы
}

/*	^^^вставка инфоблоков	*/

function InsertMainMenu()
{
	$string = "";
	$sql = "select * from `".$_VARS['tbl_pages_name']."` where in_left_menu = 1 order by order_by asc";
	$res = mysql_query($sql);
	$i = 1;
	while($row = mysql_fetch_array($res))
	{
		if($i == 5) $border = "border-right:0px;";
		else $border = "";
		$string .= "<li><a href='/".$row['p_url']."/'>".strtoupper($row['title'])."</a>\n";
		$i++;
	}
	return $string;
}
/*	^^^вставка главного меню	*/

/*	вставка блока с акциями (новостями)	*/
function InsertActions()
{
	$string = "<br>";
	$res = mysql_query("select * from `actions` where news_show = '1' order by news_date desc limit 0,5");
	while($row = mysql_fetch_array($res))
	{
		if($row['news_img'] != 0)
		{
			$img_str = "<img src='/photo2/".$row['news_img']."-small.jpg' align='left' hspace=5 vspace=5 />";
		}
		else $img_str = "";
		$date = explode("-", $row['news_date'], 3);
		$month = array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
		$string .= "<p><strong>".$date[2]."&nbsp;".$month[($date[1]*1)]."&nbsp;".$date[0]."&nbsp;г.</strong><br>".$img_str.$row['news_title']."<br><a href='/actions/?id=".$row['id']."' class='more'>подробнее...</a></p>";
	}
	return $string;
}

/*	вставка галерии портретов	*/
function InsertGallery()
{
	$string = "";
	$res = mysql_query("select * from `gallery` where person_show = '1' order by person_order asc");
	while($row = mysql_fetch_array($res))
	{
		if($row['person_photo_id'] != 0)
		{
			$img_str = "<img src='/photo3/".$row['person_photo_id']."-small.jpg' hspace=5 vspace=5 width=180 />";
		}
		else $img_str = "";
		
		if(trim($row['person_link']) != "")
		{
			$person_link = "<br><a href='".$row['person_link']."'>подробнее...</a>";
		}
		else $person_link = "";
		
		$string .= "<div class='portrait' style='background:".$row['person_bg_color'].";'>".$img_str."<p align='left' style='padding:5px'><strong>".$row['person_name']."</strong><br>".$row['person_text'].$person_link."</p></div>\n";
	}
	return $string;
}

/*	вставка формы отправки сообщения	*/
function InsertForm($form_name, $form_fam, $form_phone, $form_mail, $form_city, $form_time, $form_msg, $result)
{
	$string = "";
	$string .= $result;
	$string .= '<div id="form"><form action="/contacts/" method=post enctype=multipart/form-data >
<table border="0" cellspacing="0" cellpadding="5" width="90%">
	<tr valign="top"><td width="20%"><strong>Имя :</strong><span>*</span></td><td><input type="text" name="form_name" value="'.$form_name.'" size="50" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td><strong>Фамилия :</strong><span>*</span></td><td><input type="text" name="form_fam" value="'.$form_fam.'" size="50" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td><strong>Телефон :</strong></td><td><input type="text" name="form_phone" value="'.$form_phone.'" size="50" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td><strong>E-mail :</strong></td><td><input type="text" name="form_mail" value="'.$form_mail.'" size="50" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td><strong>Город :</strong></td><td><input type="text" name="form_city" value="'.$form_city.'" size="50" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td colspan="2"><strong>Время</strong> (для желающих записаться на прием) :&nbsp;<input type="text" name="form_time" value="'.$form_time.'" /></td></tr>
	<tr><td colspan="2" class="empty"></td></tr>
	<tr valign="top"><td><strong>Сообщение :</strong><span>*</span></td><td><textarea name="form_msg" cols=50 rows=5 />'.$form_msg.'</textarea></td></tr>
</table>	<br>
<input type="submit" name="send_form" value="Отправить">
</form><br>
<!--<a href="" >Отправить</a>--></div>';
return $string;
}

/*	отправка формы	*/
function SendForm($form_name, $form_fam, $form_phone, $form_mail, $form_city, $form_time, $form_msg)
{	
		global $admin_mail;
		global $from_site_mail;
		$string = "Имя : ".$form_name."\n";
		$string .= "Фамилия : ".$form_fam."\n";
		$string .= "Телефон : ".$form_phone."\n";
		$string .= "E-mail : ".$form_mail."\n";
		$string .= "Город : ".$form_city."\n";
		if(isset($form_time)) $string .= "Время приема : ".$form_time."\n";
		$string .= "Сообщение : ".$form_msg."\n";
		$header = "From:".$from_site_mail." \r\n".
		"Content-type: text/plain; charset=koi8-r \r\n";
		
		$address = $admin_mail;
		if(isset($form_mail))
		{
			if(strpos($form_mail, "@") !== false)
			{
				$address .= " ".$form_mail;
			}
		}
		return mail($address, convert_cyr_string("Сообщение с сайта", "w", "k"), convert_cyr_string($string, "w", "k"), convert_cyr_string($header, "w", "k"));
}

function razdel_level($p_parent_id=0,$fon="#ffffff",$level=0)
{
	global $razdel_content,$nolevel;
	if(!isset($nolevel)) $nolevel = 100000;
	$r=mysql_query("select id, title, p_url, `p_show` from `".$_VARS['tbl_pages_name']."` where (p_parent_id = '$p_parent_id' and `p_show`='1') order by order_by ");
	if (mysql_num_rows($r)>0)
	{
		if ($p_parent_id==0) 
		{
			$razdel_content .= "<table width='97%'  border='0'   cellpadding='0' cellspacing='0'>";
			$fon="#ff0000";
		}
		else  $razdel_content .=
		"<tr><td >
		<table width='97%'  border='0'  align='right' cellpadding='0' cellspacing='0'>
		";
		while ($ex=mysql_fetch_array($r))
		{
			$url="about.html?{$ex['id']}"; ////////
			if($ex['p_show']=="0" ) 
			{

				$fon="#ffdddd";
				$nolevel=$level;
			}
			if($level<$nolevel) $fon="#ffffff";
			if($level==$nolevel AND $ex['p_show']!="0")
			{
				$fon="#ffffff";
				$nolevel=100000;
			}
			$razdel_content .=
			"<tr ><td>
			<li style='list-style:none;  line-height:30px'><a class='more' href=\"/{$ex['p_url']}/\">{$ex['title']}</a>			
			</li></td></tr>";
			razdel_level($ex['id'],$fon,$level+1);			
		}
		if ($p_parent_id==0) $razdel_content .= "</table>";
		else $razdel_content .= "</table></td></tr>";
	}
	else $fon="#ffffff";
	return $razdel_content;
}
##########################################
/*			^^^функции					*/
##########################################

function make_left_menu()
{
	$left_menu = "";
	$sql = "SELECT * FROM `".$_VARS['tbl_pages_name']."` WHERE (`p_parent_id`=0 and `p_show`='1' and `in_left_menu` = '1') order by `order_by` asc ";
	//echo $sql;
	$res = mysql_query($sql);
	//echo $res;
	while($row = mysql_fetch_array($res))
	{
		$left_menu .= "<strong class=\"leftColomnHeader\">".$row['title']."</strong><br />\n<ul>\n";
		$sub_res = mysql_query(" select * from `".$_VARS['tbl_pages_name']."` WHERE (`p_parent_id`=".$row['id']." and `p_show`='1') order by `order_by` asc   ");
		while($sub_row = mysql_fetch_array($sub_res))
		{
			$left_menu .= "<li><a href=\"/".$sub_row['p_url']."/\">".$sub_row['title']."</a></li>\n";
		}
		$left_menu .= "</ul>\n";
	}
	
	return $left_menu;
}

//=====================================Превью из новости ==================================================
function get_preview ($text,$prev_len) {
	$descr=strip_tags($text);
	$descr=substr($descr,0,$prev_len);
	$descr=str_replace ("&nbsp;", " ", $descr);
	$descr=str_replace (array(".",",","!","?"), array(". ",", ","! ","? "), $descr);
	$descr=substr($descr,0,$prev_len);
	$descr=strrev($descr);
	$descr=strstr($descr," ");
	$descr=strrev($descr)." ...";
return $descr;
}


//=====================================e-mail - в javascript =============================================
function javamail($s) {
	$str=eregi_replace("<a href=\"mailto:([^>]*)\">([^<]*)</a>"," \\2 ",$s);
	$str=preg_replace("/(([\w-.]+)@([\w-.]+)(\\.[\w-]+)*)/","<script type=\"text/javascript\">writeAddress(\"$3$4\",\"$2\")</script>",$str);
	return $str;
}
//=========================================================================================================

//===================================== Description ==========================================
function get_description($top_title) {

$Trans=array_flip(get_html_translation_table());
$top_title=strtr($top_title, $Trans);
return htmlspecialchars($top_title);

}
//=========================================================================================================

//=====================================Строку - в ключевые слова ==========================================
function kw_forming($top_title) {
$kwords=str_replace (",," , "," , $top_title ) ;
$zamena= array ("\"","\'","\\","/","&gt;","&lt;","&quot;","&laquo;","&raquo;","<",">",":",";","!","+","(",")","]","[","{","}","|",",");
$kwords=strtolower(str_replace ($zamena , "" , $kwords )) ;
$kwords=(str_replace ("-" , " " , $kwords )) ;
$kwords=(str_replace ("  " , " " , $kwords )) ;
$kwords=(str_replace ("  " , " " , $kwords )) ;
$kwords=(str_replace ("  " , " " , $kwords )) ;
return trim($kwords);
}
//=========================================================================================================
//====================================СТРОКА ИЗ ФАЙЛА =====================================================
function str_from_file ($file)
{
$fff=fopen ($file, "r") ;
$str=fread($fff, 200000) ;
fclose ($fff) ;
return $str;
}
//=========================================================================================================


//====================================ПРОВЕРКА E-MAIL ======================================================
function valid_email($address)  { 
  if (ereg("^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9_\-]+\.[a-zA-Z0-9\-\.]+$", $address))
 {    return true; }    else   {    return false; }					 }
//=========================================================================================================


//===================================КОНВЕРТАЦИЯ ДАТ======================================================
	function dat_conv($d) {
	return substr($d, 8,2) . "." .substr($d, 5,2) . "." . substr($d, 0,4) ; }
//=========================================================================================================
	function dat_time_conv($d) {
	return substr($d, 8,2) . "." .substr($d, 5,2) . "." . substr($d, 0,4) . " " . substr($d, 11,5)     ; }
//=========================================================================================================
	function dat_conv_words($d) {
	$months= Array ("января", "февраля" , "марта" , "апреля" , "мая" , "июня" , "июля" , "августа" , "сентября" , "октября" , "ноября" , "декабря" ) ;
	$months_im= Array ("январь", "февраль" , "март" , "апрель" , "май" , "июнь" , "июль" , "август" , "сентябрь" , "октябрь" , "ноябрь" , "декабрь" ) ;
		if ( strlen ($d) > 7 )
		{
	$day= substr($d, 8,2) ;
	$year=substr($d, 0,4) ;
	$mon=(int)substr($d, 5,2) ;
	$month=$months[$mon-1] ;
	return "$day $month $year г." ;
		}
		else
		{
			if ( strlen ($d) == 7 )
			{
	$year=substr($d, 0,4) ;
	$mon=(int)substr($d, 5,2) ;
	$month=$months_im[$mon-1] ;
	return "$month $year г." ;
			}
			else
			{
	$year=substr($d, 0,4) ;    
	$mon=(int)substr($d, 5,1) ;   
	$month=$months_im[$mon-1] ; 
	return "$month $year г." ;
			}
		}
											}
//===================================КОНЕЦ КОНВЕРТАЦИИ ДАТ================================================
//=========================================================================================================
//=========================================================================================================
	function dat_conv_words_without_year($d) {
	$months= Array ("января", "февраля" , "марта" , "апреля" , "мая" , "июня" , "июля" , "августа" , "сентября" , "октября" , "ноября" , "декабря" ) ;
	$months_im= Array ("январь", "февраль" , "март" , "апрель" , "май" , "июнь" , "июль" , "август" , "сентябрь" , "октябрь" , "ноябрь" , "декабрь" ) ;
	
//										if($dt>date("Y")."-00-00 00:00:00") $dt=substr($dt,4,5);
	$day= substr($d, 8,2) ;
	$year=substr($d, 0,4) ;
	$mon=(int)substr($d, 5,2) ;
	$month=$months[$mon-1] ;
	$year=str_replace(date("Y"),"",$year);
	return "$day $month $year" ;
											}
											
//=========================================================================================================
											
	function dat_conv_words_without_year_en($d) {
	$months= Array ("January", "Februaru" , "mMarth" , "April" , "May" , "June" , "July" , "August" , "September" , "October" , "November" , "December" ) ;
	$months_im= Array ("January", "Februaru" , "mMarth" , "April" , "May" , "June" , "July" , "August" , "September" , "October" , "November" , "December"  ) ;
	
//										if($dt>date("Y")."-00-00 00:00:00") $dt=substr($dt,4,5);
	$day= substr($d, 8,2) ;
	$year=substr($d, 0,4) ;
	$mon=(int)substr($d, 5,2) ;
	$month=$months[$mon-1] ;
	$year=str_replace(date("Y"),"",$year);
	if(strlen($year)>0) $year=", $year";
	return "$month $day$year" ;
											}
//===================================КОНЕЦ КОНВЕРТАЦИИ ДАТ================================================
										
										
//=========================================================================================================
//===================================ЛИСТАЛКА=============================================================
function get_page($page, $total, $in_page) {
  if ($page<0)
    return 0;
  elseif ($total>0) {
    $max = $total/$in_page;
    if (intval($max)==$max)
      $max = intval($max)-1;
    else
      $max = intval($max);
    if ($page>$max)
      return $max;
    else
      return $page;
    }
  else
    return 0;
  }
  
  
function draw_bar ($page, $total, $in_page, $url) {
  $page = get_page($page, $total, $in_page);
  
$endend= $total/$in_page ;
if (intval($endend)==$endend)
$endend=intval($endend)-1 ;
else
$endend=intval($endend) ;

  if ($total>0 && intval($total/$in_page)>0) {
    $start=$page-4; $end=$page+4;
    if ($start<0) {
      $start=0;
      $end=$start+9;
      };
    $end1 = intval(($total-1)/$in_page);
    if ($end>$end1 && $start>$end-$end1) {
      $end=$end1;
      $start=$end-9;
      }
    elseif ($end>$end1) {
      $end=$end1;
      $start=0;
      };
    if ($start>0)
      $nav_panel[] = "<a href=".$url."0>Начало</a> || ";
	  
	if (($page) >= 100) 
      $nav_panel[] = "<a href=$url". ($page-100) .">-100</a> | ";
	if (($page) >= 50) 
      $nav_panel[] = "<a href=$url". ($page-50) .">-50</a> || ";
	  
/*    if ($page>$start)
      $nav_panel[] = "<a href=$url". ($page-1). ">Пред.</a> | ";  */
	  
    for ($a=$start; $a<=$end; $a++) {
      if ($a==$page)
        $qqq = "". ($a+1). "";
      else
        $qqq = "<a href=$url$a><b>". ($a+1). "</b></a>";
		if ( $a != $end ) $qqq.=" | " ;
		$nav_panel[] =$qqq ;
		};
	  
/*    if ($page<$end)
      $nav_panel[] = "<a href=$url". ($page+1) .">След.</a>";    */
	  
	if (($endend -$page) >= 50) 
      $nav_panel[] = " || <a href=$url". ($page+50) .">+50</a>";
	if (($endend -$page) >= 100) 
      $nav_panel[] = " | <a href=$url". ($page+100) .">+100</a>";
	  
    if ($page != $endend )
      $nav_panel[] = " || <a href=".$url.  $endend. ">Конец</a>"; 
	  

    return implode("", $nav_panel);
    };
  }

function draw_bar_o ($page, $total, $in_page, $url) {  /////////////////Это ф-я для псевдостатики, с адресами типа /xxxxxx/page_number/
  $page = get_page($page, $total, $in_page);
  
$endend= $total/$in_page ;
if (intval($endend)==$endend)
$endend=intval($endend)-1 ;
else
$endend=intval($endend) ;

  if ($total>0 && intval($total/$in_page)>0) {
    $start=$page-4; $end=$page+4;
    if ($start<0) {
      $start=0;
      $end=$start+9;
      };
    $end1 = intval(($total-1)/$in_page);
    if ($end>$end1 && $start>$end-$end1) {
      $end=$end1;
      $start=$end-9;
      }
    elseif ($end>$end1) {
      $end=$end1;
      $start=0;
      };
    if ($start>0)
      $nav_panel[] = "<a href=".$url.">Начало</a> || ";
	  
	if (($page) >= 100) 
      $nav_panel[] = "<a href=$url/". ($page-100) ."/>-100</a> | ";
	if (($page) >= 50) 
      $nav_panel[] = "<a href=$url/". ($page-50) ."/>-50</a> || ";
	  
/*    if ($page>$start)
      $nav_panel[] = "<a href=$url/". ($page-1). "/>Пред.</a> | ";  */
	  
    for ($a=$start; $a<=$end; $a++) {
      if ($a==$page)
        $qqq = "". ($a+1). "";
      else
        if($a==0)$qqq = "<a href=$url/><b>". ($a+1). "</b></a>";
		else $qqq = "<a href=$url/$a/><b>". ($a+1). "</b></a>";
		if ( $a != $end ) $qqq.=" | " ;
		$nav_panel[] =$qqq ;
		};
	  
/*    if ($page<$end)
      $nav_panel[] = "<a href=$url". ($page+1) .">След.</a>";    */
	  
	if (($endend -$page) >= 50) 
      $nav_panel[] = " || <a href=$url/". ($page+50) ."/>+50</a>";
	if (($endend -$page) >= 100) 
      $nav_panel[] = " | <a href=$url/". ($page+100) ."/>+100</a>";
	  
    if ($page != $endend )
      $nav_panel[] = " || <a href=$url/".  $endend. "/>Конец</a>"; 
	  

    return implode("", $nav_panel);
    };
  }
//===================================КОНЕЦ ЛИСТАЛКИ=======================================================
//=========================================================================================================

  //=========================================================================================================
//====================================КАЛЕНДАРЬ===========================================================
function calendar2 ($year , $mnth_num, $parent_file, $admin )


{$mm= array ( "" , "январь" , "февраль" , "март" , "апрель" , "май" , "июнь" , "июль" , "август" , "сентябрь" , "октябрь" , "ноябрь" , "декабрь"     ) ;
$cal2="
<table border=\"0\" cellspacing=\"1\" cellpadding=\"3\" class=\"table-red\">
  <tr>
	<td style=\"font-size: 14px;\">Календарь новостей</td>
  </tr>
  <tr valign=\"top\">
	<td>
  <form name=\"calendar\" method=\"get\" action=\"\">
<TABLE>
<TR>
	<TD>
<select name=\"c_mnth_num\" onchange=\"document.calendar.submit();\"> 



" ;for ( $i = 1 ; $i <= 12 ; $i ++){
if ( $i != $mnth_num ) { $cal2.="<option value=$i>" . $mm[$i] . "</option>" ; }


else {  $cal2.="<option value=$i selected>" . $mm[$i] . "</option>" ;  }}$cal2.=




"</select>	</TD>	<TD>&nbsp;&nbsp;<select name=\"c_year\"  onchange=\"document.calendar.submit();\">" ; 



for ( $i = 2005 ; $i <= (int)date("Y") ; $i++   ){if ($i != $year) { $cal2.="<option value=$i>$i</option>\n" ; }
else {  $cal2.="<option value=$i selected>$i</option>\n" ;  }


}$cal2.=

"</select>
	</TD>
	<TD>
	<!--
<a href='#tripps' title=\"Перейти к выбранному месяцу\" onClick=\"javascript:document.calendar.submit();\" style=\"font-size: 14px;\">&raquo;&raquo;</a>
  -->
	</TD>
</TR>
</TABLE>
";

$cal2.="";

//$year=(int)date ("Y" ) ; // год (2005) 
$year_next=$year ;
$year_prev=$year ;

$wkd=Array ("" , "Пн" , "Вт" , "Ср" , "Чт" , "Пт" , "Сб" , "Вс") ; // дни нед.
$mnth=Array ( "" , "Январь" , "Февраль" , "Март" , "Апрель" , "Май" , "Июнь" , "Июль" , "Август" , "Сентябрь" , "Октябрь" , "Ноябрь" , "Декабрь" ) ; // м-цы
$mnth_rod=Array ( "" , "января" , "февраля" , "марта" , "апреля" , "мая" , "июня" , "июля" , "августа" , "сентября" , "октября" , "ноября" , "декабря" ) ; // м-цы в род. падеже
$mnth_length= Array (0 , 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31) ; if  ( checkdate( 2, 29 , $year)) $mnth_length[2] = 29 ;  // дней в м-це

$cal2.= "<table border=0 bgcolor='#CCCCCC' cellspacing=1 cellpadding=3>
<tr bgcolor='#DCE6EF'>
<td>Пн</td>
<td>Вт</td>
<td>Ср</td>
<td>Чт</td>
<td>Пт</td>
<td>Сб</td>
<td>Вс</td>
</tr>
" ;

			for  ( $mes=0; $mes<1 ; $mes++) {
//===============================календарь тек. м-ца============================================================================

$mnth_num=$mnth_num + $mes ; // номер м-ца
if ( $mnth_num > 12 ) { $mnth_num =1 ; $year=$year+1 ; $year_next=$year ; $year_prev=$year -1 ;  }
if ( $mnth_num == 1+$mes ) { $year_prev=$year ; }
$mnth_next_num=$mnth_num+1 ; if ( $mnth_next_num > 12 )  { $mnth_next_num = 1 ; $year_next=$year+1 ; }   // номер предыд. м-ца
$mnth_prev_num=$mnth_num-1 ; if ( $mnth_prev_num < 1 )  { $mnth_prev_num = 12 ; $year_prev=$year-1 ; }   // номер след. м-ца
$wkd_num=(int) date("w") ; if ($wkd_num  == 0 ) $wkd_num = 7 ;  // номер дня недели (Вс - 7)
$wkd_num_1=(int) date("w" , mktime ( 1,1,1, $mnth_num, 1, $year )   ) ; if ($wkd_num_1  == 0 ) $wkd_num_1 = 7 ;  // номер дня недели для 1-го числа м-ца (Вс - 7)
$wkd_num_p=(int) date("w" , mktime ( 1,1,1, $mnth_num, $mnth_length[$mnth_num], $year )   ) ; if ($wkd_num_p  == 0 ) $wkd_num_p = 7 ;  // номер дня недели для последнего числа м-ца (Вс - 7)
$month_day=(int) date ("j") ; // число в месяце
//if ( $mes>0) $month_day=$mnth_length[$mnth_num] ;
if ( $mes>0) $month_day=1  ;

$mnth_curr_length=$mnth_length [$mnth_num] ; // дней в тек. м-це
$mnth_prev_length=$mnth_length [$mnth_prev_num] ; // дней в пред. м-це
$mnth_next_length=$mnth_length [$mnth_next_num] ; // дней в след. м-це

$m_prev=0 ; $m_next=0;
if ( $wkd_num_1 > 1 )  { 	for ( $i=1 ; $i < $wkd_num_1 ; $i++) { $m_prev=$i ; $dates[$i]=$mnth_prev_length-$wkd_num_1+1+$i ;  $ymd[$i]=$year_prev . "-" . $mnth_prev_num . "-"  . $dates[$i]   ;     }	} // дополняем датами перед тек. месяцем 
for ($i=1 ; $i < $mnth_curr_length+1 ; $i++ ) {  $dates[$wkd_num_1+$i-1]= $i ;   $ymd[$wkd_num_1+$i-1]=$year . "-" . $mnth_num . "-"  . $dates[$wkd_num_1+$i-1]   ; } // заполняем датами текущего м-ца
if ( $wkd_num_p < 7 )  { 	for ( $i=1 ; $i < 7-$wkd_num_p+1 ; $i++) { $m_next=$i ; $dates[$i+$m_prev+$mnth_curr_length]=$i ;  $ymd[$i+$m_prev+$mnth_curr_length]=$year_next . "-" . $mnth_next_num . "-"  . $dates[$i+$m_prev+$mnth_curr_length]   ;  }	} // дополняем датами после тек. месяца 
for ($i=1 ; $i <=sizeof ($ymd) ; $i++ ) 
{
$eee=explode ( "-" , $ymd[$i] ) ;
if ( strlen($eee[1]) == 1) $eee[1] = "0" . $eee[1] ;
if ( strlen($eee[2]) == 1) $eee[2] = "0" . $eee[2] ;
$ymd[$i] = $eee[0] . "-" . $eee[1] . "-" . $eee[2] ;

$td=$ymd[$i]; 
$andwhere="";
$daynow=date("Y-m-d H:i:s") ;
if ( $admin == "no" ) $andwhere=" AND `pub` = 'yes'  AND `date` <= '$daynow'  ";
$result = mysql_query("SELECT `id` FROM conf_news  where `date` LIKE '$td%'  $andwhere  ");
if ( mysql_num_rows ($result) > 0 ) 	{ $ah1[$i]="<a href='$parent_file?page=news&show=date&p=&date=$td&year=&month=' target=_parent>" ; $ah2[$i]="</a>" ; }
										else		{ $ah1[$i]="" ; $ah2[$i]="" ; }
										
}



$kvo_nedel=(int) (sizeof($dates)/7) ;
			for ( $n=1; $n <= $kvo_nedel ; $n ++ )
			{
			$cal2.= "<tr bgcolor='#ffffff'>\n" ;
					for ( $i=1 ; $i <= 7 ; $i++ )
					{
					$ii=$i+($n-1)*7 ;
//    ?page=news&show=date&p=&date=$td&year=&month=
						if ( $ii <= $m_prev  OR  $ii > $m_prev+$mnth_curr_length ) 
														{	
/*														if ($ii <= $m_prev  ) {   $mmm=$mnth_prev_num ; $cal2.= "<td>" . $ah1[$ii] . $dates[$ii] . $ah2[$ii] . "</td>" ;    unset($dates[$ii]) ;    }    */
/*														if ($ii > $m_prev+$mnth_curr_length   ) {   $mmm=$mnth_next_num ; $cal2.= "<td>" . $ah1[$ii] . $dates[$ii] . $ah2[$ii] . "</td>" ;    unset($dates[$ii]) ;    } */

														if ($ii <= $m_prev  ) {   $mmm=$mnth_prev_num ; $cal2.= "<td></td>" ;    unset($dates[$ii]) ;   }
														if ($ii > $m_prev+$mnth_curr_length   ) {   $mmm=$mnth_next_num ; $cal2.= "<td></td>" ;    unset($dates[$ii]) ;    }
														}
						else { 
						if ( $dates[$ii] == $month_day AND $mes==0) {  $cal2.= "<td>" . $ah1[$ii] . $dates[$ii] . $ah2[$ii] . "</td>" ;     unset($dates[$ii]) ;    } 
						else {  
									if (  $dates[$ii] < $month_day AND $mes==0 ) {$cal2.= "<td>" . $ah1[$ii] . $dates[$ii] . $ah2[$ii] . "</td>" ;    unset($dates[$ii]) ;     }
									else {  $cal2.= "<td>" . $ah1[$ii] . $dates[$ii] . $ah2[$ii] . "</td>";    unset($dates[$ii]) ;    }
								}
						 }
					$cal2.= "\n" ;
					}
            $cal2.= "</tr>\n" ;
			}
unset ($dates) ;
																								}
//============================ Конец формирования календаря	 =======================================
$cal2.= "</table>\n" ;

$result = mysql_query("SELECT `id` FROM conf_news  where `date` LIKE '" . date("Y-m-d") . "%'   ");
if ( mysql_num_rows ($result) > 0 ) 	{ $aah1="<a href=\"$parent_file?page=news&show=date&p=&date=" . date("Y-m-d") . "&year=&month=\" target=_parent>" ; $aah2="</a>" ; }
										else		{ $aah1="<span style=\"color:gray;\">" ; $aah2="</span>" ; }

$cal2.="
	  <table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"0\">
		<tr align=\"left\">
		  <td>
		  <nobr>&raquo; <a href=\"$parent_file?page=news&show=month&p=&date=&year=$year&month=$mnth_num\" target=_parent>новости за " . dat_conv_words( $year . "-" . $mnth_num ) . "</a></nobr><br>
		  &raquo; " . $aah1 . "новости за сегодня" . $aah2 . "<br>
		  &raquo; <a href=\"$parent_file?page=news&show=all&p=&date=&year=&month=\" target=_parent>все новости</a>
		</tr>
	  </table>
	</td>
  </tr>
  </form>
</table>
";
return $cal2 ;
}
//============================КОНЕЦ КАЛЕНДАРЯ=============================================================

//============================УДАЛЯЕМ НУЛИ ИЗ НОМЕРА МЕСЯЦА ==============================================
function strip_zero ($num) 
{
$nnum=$num ;
if ( (int)substr ($num , 0 , 1 ) == 0 ) $nnum == (int)substr ($num , 1 , 1 ) ;
return $nnum ;
}
//=========================================================================================================
//============================Есть ли IP в BlackList'е ==============================================
function is_blacklisted ($ip)
{
if ( mysql_num_rows ( mysql_query ( " select `id` from  `conf_blacklist`  where `ip` = '$ip'  " ) ) > 0 ) { return true ; }
else {  return false ; }
}
//=========================================================================================================

?>