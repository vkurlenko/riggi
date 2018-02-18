<?
session_start();



error_reporting(E_ALL);
// param2 => param3 => param4

include_once 'config.php';
include_once DIR_CLASS.SL.'class.db.php';
include_once DIR_CLASS.SL.'class.html.php';

	

if(isset($_GET['param2']) && $_GET['param2'] == 'eng')
{
	$_SESSION['lang'] = 'eng';
	header('Location:'.$_SERVER['HTTP_REFERER']);
}
elseif(isset($_GET['param2']) && $_GET['param2'] == 'rus')
{
	$_SESSION['lang'] = '';
	header('Location:'.$_SERVER['HTTP_REFERER']);
}
	
		
		
		
$lang = '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$lang = '_'.$_SESSION['lang'];
	define ('LANG', '_'.$_SESSION['lang']);
}
else
	define ('LANG', '');		
		

$langPrefix = $imgPrefix = "";
$langIndex = 0;
/*if(isset($_SESSION['lang']) && $_SESSION['lang'] == "en")
{
	$langPrefix = "_en";
	$imgPrefix = "en/";
	$langIndex = 1;
}*/
/* /выбор языковой версии */


$_PAGE = array();



include_once "lang.php";
include_once "functions.php" ;
include_once "functions_sql.php" ;

//printArray($_GET);


$razdel_table	= $_VARS['tbl_pages_name'];	// имя таблицы страниц
$title_prefix = "";

// разбираем QUERY_STRING на параметры 
$QUERY_STRING = $_SERVER['QUERY_STRING'];

// из строки запроса удаляем лишнее
$na_fig	= array("http:","%","\/","\.php","ftp:","'","\."," ",":",">","<","lang=en");

$_PAGE['url'] = str_replace($na_fig, "", strtolower(strip_tags($QUERY_STRING)));


$razza	= explode("&",$_PAGE['url']);	// остается только имя нужного раздела
if(isset($razza[0]))
{
	$_PAGE['url'] = $razza[0];
}
/* ^^^разбираем QUERY_STRING на параметры */





/* Страницы, доступные только зарегистрированным пользователям */
/*$arrProtect = array("catalog", "catalog2", "basket", "your_orders", "item");
foreach($arrProtect as $k)
{
	if($_PAGE['url'] == $k)
	{
		if(!isset($_SESSION['access']) or $_SESSION['access'] == false)
		{
			header("Location: /error_403/");
		}
	}
}*/




/* /Страницы, доступные только зарегистрированным пользователям */


/* узнаем маркеры шаблона индексной страницы и шаблона по умолчанию */
$sql = "SELECT * FROM `".$_VARS['tbl_template_name']."` 
		WHERE tpl_index = '1' OR tpl_default = '1'";
$res = mysql_query($sql);

while($row = mysql_fetch_array($res))
{
	// шаблон индексной
	if($row['tpl_index'] == '1') 	
		$index_marker = $row['tpl_marker']; 		
	
	// шаблон по умолчанию	
	if($row['tpl_default'] == '1') 	
		$default_marker = $row['tpl_marker']; 		
}


/* Выбираем id раздела */
if($_PAGE['url'] == "") 
{
	// если строка запроса пустая, значит выбираем главную страницу
	$_PAGE = mysql_fetch_array(mysql_query("SELECT * FROM `$razdel_table` 
											WHERE `p_tpl`='".$index_marker."' 
											LIMIT 0,1"));
}
else 
{
	$sql = "SELECT * FROM `$razdel_table` 
			WHERE `p_url`='".$_PAGE['url']."' 
			LIMIT 0,1";
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
		$_PAGE = mysql_fetch_array($res);
	else 
	{
		// если такого url нет в БД
		$_PAGE = mysql_fetch_array(mysql_query("SELECT * FROM `$razdel_table` 
											WHERE `p_tpl`='".$index_marker."' 
											LIMIT 0,1"));
	}
											
	
}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* ограничение доступа к закрытым разделам */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
// если страница только для зарегистрированных пользователей 
// или является дочерней для закрытого раздела и нет авторизации,
// то редирект
function pageTree($parent_id)
{
	global $arrTree, $_VARS;
	
	$sql = "SELECT * FROM `".$_VARS['tbl_pages_name']."`
			WHERE id = ".$parent_id;
			
	$res_tree = mysql_query($sql);	
	
	while($row_tree = mysql_fetch_array($res_tree))
	{
		if($row_tree['p_protect'] == '1')
		{
			header("Location: /main/");
		}
		else
		{
			pageTree($row_tree['p_parent_id']);
		}
	}	
}

$arrOpen = array("auth", "register", "rules");

if(!(isset($_SESSION['user_access'])) || $_SESSION['user_access'] != true)
{	
	if(!in_array($_PAGE['p_url'], $arrOpen))
	{
		if($_PAGE['p_protect'] == '1') header("Location: /main/");
		pageTree($_PAGE['p_parent_id']);
	}	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* /ограничение доступа к закрытым разделам */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// если id раздела не определен, то выбираем самую первую запись в БД
if(!isset($_PAGE["id"]) or $_PAGE["id"] == "")
{	
	$em		= mysql_fetch_array(mysql_query("select `id` from `$razdel_table` where `p_parent_id`='0' AND `on`='1' order by `p_order` limit 0,1 "));
	$_PAGE["id"]	= $em["id"];
}
$nannies = is_razdel_on($_PAGE["id"]);


// id страницы в БД
//$ID	= $_PAGE["id"]; 


// если не нашли заданный id в БД, то перенаправляем на главную
if(!$em = mysql_fetch_array(mysql_query("select * from `$razdel_table` where `id`='".$_PAGE["id"]."' "))) 
{ 
	header("Location: /"); 
	exit; 
}




$razd  = $_PAGE["id"];	// id раздела


if($razza[0] != "order_view" and $razza[0] != "feedback")
{
	$alb	= $_PAGE["p_photo_alb"]; 
}

$parent = $_PAGE["p_parent_id"]; 


/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ читаем шаблон страницы ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
if($_PAGE["p_tpl"] == "")
{
	$tpl = $default_marker;
}
else $tpl = $_PAGE["p_tpl"];	


$sql = "select * from `".$_VARS['tbl_prefix']."_templates` where tpl_marker='".$tpl."'";
$res = mysql_query($sql);
while($row = mysql_fetch_array($res))
{
	$f = $row['tpl_file'];	
}
$tpl = file($_SERVER['DOC_ROOT']."/".$_VARS['tpl_dir']."/".$f);

// прочитаем шаблон в строку
$tpl_str = "";	
foreach($tpl as $line)
{
	$tpl_str .= $line;	// 
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /читаем шаблон страницы ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/





/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ читаем из БД контент страницы ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


$content 		= $em["p_content".$langPrefix]; if(!isset($content)) $content  = ""; // прочитали контент (текст) страницы
$pageTitle 		= $em["p_title".$langPrefix];	// прочитали заголовок страницы
$pageTags 		= $em["p_tags"];				// прочитали поле Тег страницы
$pageVideo 		= $em["p_video"]; 			// поле код вставки видео
$pageRightText 	= $em["p_add_text_1"]; 		// правая часть страницы


/* мета-данные */
if(strlen(trim($em["p_meta_title".$langPrefix])) > 0) $top_title = $em["p_meta_title".$langPrefix];
else $top_title = $pageTitle;


if(strlen(trim($em["p_meta_kwd".$langPrefix])) > 0)	 $kwords	= $em["p_meta_kwd".$langPrefix];
else $kwords = $pageTitle;

if(strlen(trim($em["p_meta_dscr".$langPrefix])) > 0) $dscrptn	= $em["p_meta_dscr".$langPrefix];
else $dscrptn = $pageTitle;

$kwords = kw_forming($kwords) ;
$dscrptn =  get_description($dscrptn);
/* ^^^мета-данные */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /читаем из БД контент страницы ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




//include "visitors.php";  // Статистика




/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* генерируем страницу и сохраняем ее в строку для дальнейших подстановок */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$str = stripslashes($tpl_str);
ob_start();
eval("?>$str<?");
$output = ob_get_contents();
ob_end_clean();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* /генерируем страницу и сохраняем ее в строку для дальнейших подстановок */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ Вставляем все в шаблон ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

$arr_search_replace = array(
	":::top_title:::"	=> $top_title, 		// META title
	":::kwords:::" 		=> $kwords,			// META keywords
	":::dscrptn:::" 	=> $dscrptn,		// META descr
	":::pageTitle:::" 	=> $pageTitle,		// заголовок страницы
	":::content:::" 	=> stripslashes($content), // контент страницы
	":::flash_code:::" 	=> $pageVideo,		// код вставки flash
	":::right_text:::" 	=> $pageRightText	// ?
);

foreach($arr_search_replace as $k => $v)
{
	$output	= str_replace($k, $v, $output);
}

/*~~~ вставка инфоблоков ~~~*/

include_once "modules/iblocks/iblocks.php";	

/*~~~ /вставка инфоблоков ~~~*/



/*~~~ вставка баннеров ~~~*/

include_once "modules/banners/banners.group.php";	

/*~~~ /вставка баннеров ~~~*/

/*~~~ рассылка новостей ~~~*/
/*if(file_exists(($_SERVER['DOC_ROOT']."/subscribe.htm")))
{*/
	$show_msg = false; // показывать сообщения о процессе рассылки
	/*$file = file($_SERVER['DOC_ROOT']."/subscribe.htm");*/
	include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/subscribe/subscribe.mail.php";
/*}*/
/*~~~ рассылка новостей ~~~*/

/*~~~ рассылка новостей ~~~*/
$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_subscribe_news`
		WHERE subscribe_status = '0'";
$res = mysql_query($sql);

if($res && mysql_num_rows($res) > 0)
{
	include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/subscribe_news/subscribe_news.mail.php";
}
/*~~~ рассылка новостей ~~~*/

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /Вставляем все в шаблон ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


/* выводим в браузер */
echo $output;
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/
//echo "access = ".$_SESSION['access'];

?>