<?
include $_SERVER['DOC_ROOT']."/db_structure.php";

$db_debug = 0;

#-------обращение к базе
function sql ($query, $show_query=0){
	global $db_debug;

	$result = mysql_query ($query);
	if (!$result and $db_debug) {
		//echo ("<b>$query</b><br>" . mysql_error() . "<br>");
		$show_query = 0;
	}

	//if ($show_query) echo ("<b>$query</b><br>");

	return ($result);
}

#-------соединение с базой
function DBconnect (){
	global $db_host, $dbname, $db_login, $db_pwd, $site_host, $admin_mail, $is_site_admin;
	//$db = MYSQL_PCONNECT ($db_host, $db_login, $db_pwd) OR DIE ("Не могу создать соединение. Проверьте настройки базы данных в конфигурационных файлах");
	$db = MYSQL_CONNECT ($db_host, $db_login, $db_pwd);
	if (!$db) {
//		@mail("daver@visions.ru", "oil-gas.ru - mysql error", "ne mogu sozdat' soedinenie s bd");
		
		$url = $GLOBALS['REQUEST_URI'];
		$crc = md5($url);

		if (file_exists ("$site_host/cache/$crc")) { 
			include ("$site_host/cache/$crc");
			if ($is_site_admin) echo "БД недоступна! взяли страницу из кэша.";
			exit();
		}
		else die ("Не могу создать соединение. Проверьте настройки базы данных в конфигурационных файлах");
	}
	mysql_select_db ($dbname) or die ("Не могу выбрать базу данных");
	mysql_set_charset('utf8'); 
	return ($db);
}

#-------
function SqlParseRes($query, $show_query=0) {
	if ($show_query) echo ("<b>$query</b><br>");
	
	$res = sql($query);
//echo $query;
	if ($res){
		$i = 0;
		while ($row = mysql_fetch_array ($res)){
			foreach($row as $key=>$value) $result[$i][$key] = $value;
	        $i++;
		}
	}

	if (isset($result) and is_array($result)) return ($result); else return 0;
}

#-------
function SqlParseRes2($query) {
	$res = sql($query);

	if ($res){
		$i = 0;
		while ($row = mysql_fetch_array ($res)){
			foreach($row as $key=>$value) $result[$i] = $value;
	        $i++;
		}
	}

	if (isset($result) and is_array($result)) return ($result); else return 0;
}

#--------выдача выпадающих списков для выбора даты
function getdataform($list_type, $oyear=0)
  {$out="";
    if ($list_type=="day") {
    $out.="<select name=day>";
    for ($i=1;$i<=31;$i++)
     {
      if ($i==date("d")) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="month") {
    $out.="<select name=month>";
    for ($i=1;$i<=12;$i++)
     {
      if ($i==date("m")) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="year") {
    $out.="<select name=year>";
    for ($i=2003;$i<=2008;$i++)
     {
      if (($oyear AND $i==$oyear) or (!$oyear and $i==date("Y"))) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="time") {
    $out.="<input type=text name=time value='".date("H:i")."' size=5 maxlength=5>";
    }
  return $out;
}

#--------выдача выпадающих списков для выбора даты
function getdataform3($list_type, $time, $nn="")
  {$out="";
	if (!$time) $time = time();

    $dayold = date("d",$time);
    $monthold = date("m",$time);
    $yearold = date("Y",$time);

    if ($list_type=="day") {
    $out.="<select name=day$nn>";
    for ($i=1;$i<=31;$i++)
     {
      if ($i==$dayold) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="month") {
    $out.="<select name=month$nn>";
    for ($i=1;$i<=12;$i++)
     {
      if ($i==$monthold) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="year") {
    $out.="<select name=year$nn>";
    for ($i=2003;$i<=2008;$i++)
     {
      if ($i==$yearold) {$selected="selected";}
      else {$selected="";}
      $out.="<option value=\"$i\" $selected>$i";
     }
    $out.="</select>";
    }

   if ($list_type=="time") {
    $out.="<input type=text name=time$nn value=".date("H:i",$time)." size=5 maxlength=5>";
    }
  return $out;
}

#-------перевод даты и времени из нормального вида в секунды
function normtosec($day,$month,$year,$timehm = ''){
  if ($timehm){
	  $second=substr($timehm,6,2);
	  $hour=substr($timehm,0,2);
	  $minute=substr($timehm,3,2);
  }
  else {
	  $second = date('s');
	  $hour = date('H');
	  $minute = date('i');
  }
  $time=mktime($hour,$minute,$second,$month,$day,$year);
  return $time;
}


function strips(&$el) { 
  if (is_array($el)) { 
    foreach($el as $k=>$v) { 
      if($k!='GLOBALS') { 
        strips($el[$k]); 
      } 
    } 
  } else { 
    $el = stripslashes($el); 
  } 
}


function GetPrevNext_($id, $table){
	$res = sql("select id from $table where (id<$id and is_show='1') order by id desc limit 1");
	if ($res and mysql_numrows($res)) {
		$result['id_prev'] = mysql_result($res, 0);
	}
	else $result['id_prev'] = '';

	$res = sql("select id from $table where (id>$id and is_show='1') order by id asc limit 1");
	if ($res and mysql_numrows($res)) {
		$result['id_next'] = mysql_result($res, 0);
	}
	else $result['id_next'] = '';

	return $result;
}

function GetPrevNext($id, $table, $ids, $param = ''){
	$res = sql("select id from $table where (id<$id and is_show='1') order by id desc limit 1");
	if ($res and mysql_numrows($res)) {
		$result['id_prev'] = mysql_result($res, 0);
	}
	else $result['id_prev'] = '';

	$res = sql("select id from $table where (id>$id and is_show='1') order by id asc limit 1");
	if ($res and mysql_numrows($res)) {
		$result['id_next'] = mysql_result($res, 0);
	}
	else $result['id_next'] = '';


	$res = '';
	for ($i = 0; $i < count($ids); $i++){
		if ($i) $res .=  "&nbsp; ";
		$j = $i + 1;
		if ($ids[$i] != $id) $res .= "<a href='?$param{$ids[$i]}'>$j</a>";
		else $res .= "<b>$j</b>";
		if ($i != (count($ids) - 1)) $res .=  " &nbsp;|";
	}

	$result['nav'] = $res;

	return $result;
}


function GetPrevNextNav($id, $ids, $param = ''){

	$page = array_search($id, $ids);

	if (isset($ids[$page - 1])) $result['id_prev'] = $ids[$page - 1];
	else $result['id_prev'] = '';

	if (isset($ids[$page + 1])) $result['id_next'] = $ids[$page + 1];
	else $result['id_next'] = '';


	if (count($ids) > 10) {
		$to = $page + 5;
		if ($to > count($ids)) {
			$from = $page - 4 - ($to - count($ids));
			$to = count($ids);
		}
		else {
			$from = $page - 4;
		}
	}
	else {
		$from = 0;
		$to = count($ids);
	}

	if ($from < 0) {
		$to -= $from;
		$from = 0;
	}


	$res = '';
	for ($i = $from; $i < $to; $i++){
		if ($i > $from) $res .=  "&nbsp; ";
		$j = $i + 1;
		if ($ids[$i] != $id) $res .= "<a href='?{$ids[$i]}$param'>$j</a>";
		else $res .= "<b>$j</b>";
		if ($i != ($to - 1)) $res .=  " &nbsp;|";
	}

	$result['nav'] = $res;

	return $result;
}


//выдача навигации
function GetNavLinks($page, $num, $param = '', $in_page = 0){
	global $num_items_in_page;

	if (!$in_page) $in_page = $num_items_in_page;
	
	$prev_page = $page - 1;
	$next_page = $page + 1;
	
	$res = '';
	
	if ($num > $in_page){
		if ($prev_page) $res .= "<a href='?$param&page=$prev_page' title='Предыдушая страница'><img src='/i/arr-1.gif' width=16 height=16 align=absmiddle border=0></a>";
		else $res .= "<img src='/i/empty.gif' width=16 height=16 align='absmiddle' border=0>";
		
		$num_pages = ceil($num / $in_page);
	
		if ($num_pages > 10) {
			$from = $page - 5;
			if ($from < 1) {
				$to = $page + 5 - $from;
				$from = 1;
			}
			else {
				$from++;
				$to = $page + 5;
			}
		}
		else {
			$from = 1;
			$to = 11;
		}

		if ($to > $num_pages) $to = $num_pages + 1;

		for ($i = $from; $i < $to; $i++){
			if ($i != 1) $res .=  "&nbsp; ";
			if ($i != $page) $res .= "<a href='?$param&page=$i'>$i</a>";
			else $res .= "<b>$i</b>";
			if ($i != $to - 1) $res .=  " &nbsp;|";
		}
	
		if ($next_page <= $num_pages) $res .= "<a href='?$param&page=$next_page' title='Следующая страница'><img src='/i/arr.gif' width=16 height=16 align=absmiddle border=0></a>";
		else $res .= "<img src='/i/empty.gif' width=16 height=16 align='absmiddle' border=0>";
	}
	
	return $res;
}



//выдача навигации
function GetNavLinks_old($page, $num, $param = '', $in_page = 0){
	global $num_items_in_page;

	if (!$in_page) $in_page = $num_items_in_page;
	
	$prev_page = $page - 1;
	$next_page = $page + 1;
	
	$res = '';
	
	if ($num > $in_page){
		if ($prev_page) $res .= "<a href='?$param&page=$prev_page' title='Предыдушая страница'><img src='/i/arr-1.gif' width=16 height=16 align=absmiddle border=0></a>";
		else $res .= "<img src='/i/empty.gif' width=16 height=16 align='absmiddle' border=0>";
		
		$num_pages = ceil($num / $in_page);
	
		for ($i = 1; $i <= $num_pages; $i++){
			if ($i != 1) $res .=  "&nbsp; ";
			if ($i != $page) $res .= "<a href='?$param&page=$i'>$i</a>";
			else $res .= "<b>$i</b>";
			if ($i != $num_pages) $res .=  " &nbsp;|";
		}
	
		if ($next_page <= $num_pages) $res .= "<a href='?$param&page=$next_page' title='Следующая страница'><img src='/i/arr.gif' width=16 height=16 align=absmiddle border=0></a>";
		else $res .= "<img src='/i/empty.gif' width=16 height=16 align='absmiddle' border=0>";
	}
	
	return $res;
}


function add_magic_quotes_gpc($str){
	return (!get_magic_quotes_gpc()) ? addslashes($str) : $str;
}


function strip_magic_quotes_gpc($str){
	return (get_magic_quotes_gpc()) ? stripslashes($str) : $str;
}

$months_names = array('','января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
$months_names2 = array('','январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь');


function InfoAddedMail ($razdel, $edit_url, $id, $title = '', $file = '') {
	global $_FILES, $from_site_mail, $admin_mail, $site_url, $site_host;
	require_once ('mailclass.inc');

	$date = date('d.m.Y H:i', time());

	$mail = new multi_mail;

	$mail->from = $from_site_mail;
	$mail->to = $admin_mail;
	$mail->subject = "[oil-gas.ru - письмо с сайта] Добавлено: $razdel";
	$mail->body = "$date\n\nДобавлена информация на сайте в раздел '$razdel'\n\n$title\n\nId: $id\n\nРедактирование информации:\nhttp://$site_url/admin/$edit_url";

	if ($file and isset($_FILES['userfile']['name']) and $_FILES['userfile']['name']) {
		$fd = @fopen("$site_host/$file", "r");
		$file_data = @fread($fd, filesize("$site_host/$file"));
		@fclose($fd);
		$mail->attach_file(strip_magic_quotes_gpc($_FILES['userfile']['name']), $file_data, $_FILES['userfile']['type']);
	}

	$mail->send_mail();
}


function win2koi ($text) {
	$from = array("…", "–", "—", "№", "«", "»", "“", "”", "‘", "’");
	$to = array("...", "-", "-", "#", "\"", "\"", "\"", "\"", "'", "'");

	return (convert_cyr_string (str_replace($from, $to, $text), "w", "k"));
	//return ((str_replace($from, $to, $text)));
}
?>