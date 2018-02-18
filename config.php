<?

error_reporting(E_ALL);

/************************************/
/*				Define 				*/
/************************************/

define('SITE_PREFIX', 'riggi');

define('DIR_ROOT'	, dirname(__FILE__));
define('HOST'		, $_SERVER['HTTP_HOST']);
define('SL'			, '/');
define('NL'			, "\n");
define('BR'			, "<br>");
define('ERROR_LEVEL', E_ALL);


/******* Directory ********/

define('DIR_CSS'	, SL.'css');
define('DIR_IMG'	, SL.'img');
define('DIR_BLOCKS'	, DIR_ROOT.SL.'blocks');
define('DIR_PIC'	, SL.'pic_catalogue');
define('DIR_JS'		, SL.'js');
define('DIR_ICON'	, SL.'cms9/icon');
define('DIR_CMS'	, SL.'cms9');
define('DIR_FRAMEWORK', DIR_ROOT.SL.DIR_CMS.SL.'modules/framework');


$class_cfg = array(
	'riggi' 	=> 'z:/home/interface/class',
	'riggi.alansherdani.com'	=> DIR_ROOT.'/class',
	'riggi.ru'	=> DIR_ROOT.'/class',
	'www.riggi.ru'	=> DIR_ROOT.'/class'
);

$lng_cfg = array(
	'rus',
	'eng'
);

define('DIR_CLASS'	, $class_cfg[HOST]);

/******* /Directory ********/


/******* Mysql ******/

include_once DIR_CLASS.'/class.db.php';

$db_cfg = array(
	/* 'riggi' => array(
		'localhost', 			// db host
		SITE_PREFIX, 			// db name
		SITE_PREFIX.'_user', 	// db user
		SITE_PREFIX.'_pwd'		// db_pwd
	), */
	/* 'riggi.alansherdani.com' => array(
		'vinci-2.mysql', 			// db host
		'vinci-2_riggi', 			// db name
		'vinci-2_mysql', 	// db user
		'sbanfy3e'		// db_pwd
	), */
	'riggi.ru' => array(
		'u24518.mysql.masterhost.ru', 			// db host
		'u24518', 			// db name
		'u24518', 	// db user
		'con9bdoryara'		// db_pwd
	),
	'www.riggi.ru' => array(
		'u24518.mysql.masterhost.ru', 			// db host
		'u24518', 			// db name
		'u24518', 	// db user
		'con9bdoryara'		// db_pwd
	)
);

/* $mysql[host]   = "u24518.mysql.masterhost.ru";
$mysql[user]   = "u24518";
$mysql[pass]   = "con9bdoryara";

$mysql[db_default]     = "u24518";
$mysql[db]     = "u24518"; */

DB::db_set($db_cfg);
DB::db_connect();
DB::db_set_names();

/******* /Mysql ******/


/************************************/
/*				/ Define			*/
/************************************/

$_SERVER['DOC_ROOT'] = DIR_ROOT;

$_VARS = array(
	'cms_dir' 			=> 'cms9', 		// папка с CMS 
	'cms_modules'		=> 'modules',	// функциональные модули  
	'cms_pic_in_page'	=> 100, 		// кол-во выводимых картинок в менеджере картинок

	'multi_lang'		=> true,		// многоязычный сайт

	'mail_admin'		=> "victor@vincinelli.com", 	// e-mail администратора

	'tbl_prefix'		=> SITE_PREFIX,					// префикс для уникальных таблиц CMS
	'tbl_pages_name'	=> SITE_PREFIX."_pages",		// имя таблицы страниц
	'tbl_cms_users'		=> SITE_PREFIX."_cms_users",	// имя таблицы разделов
	'tbl_template_name' => SITE_PREFIX."_templates",	// имя таблицы шаблонов 
	'tbl_photo_alb_name'=> SITE_PREFIX."_pic_catalogue",// имя таблицы фотоальбомов
	'tbl_photo_name'	=> SITE_PREFIX."_pic_",			// префикс таблицы фотоальбома
	'tbl_news'			=> SITE_PREFIX."_news",			// имя таблицы новостей
	'tbl_iblocks'		=> SITE_PREFIX."_iblocks",		// имя таблицы инфоблоков

	'tpl_dir'			=> "templates/".SITE_PREFIX,	// папка с шаблонами
	'photo_alb_dir'		=> "pic_catalogue", 			// папка с фотоальбомами
	'photo_alb_sub_dir' => SITE_PREFIX."_pic_",
	
	'audio_alb_dir'		=> "files/audio", 				// папка с аудио-файлами
	'video_alb_dir'		=> "files/video", 				// папка с видео-файлами 
	
	// места баннеров
	'banners_place' 	=> array(	
							"banner_line_1" => "пара баннеров (линейка 1)",
							"banner_line_2" => "большой баннер (линейка 2)",
							"banner_line_3" => "пара баннеров (линейка 3)",
							"banner_line_4" => "большой баннер (линейка 4)"
						),
	
	// группы пользователей CMS 
	'arrGroups' 		=> array(
							"admin" 	=> array("Администраторы"),
							"manager" 	=> array("Менеджеры"),	
							"editor"	=> array("Контент-менеджеры"),
							"finans"	=> array("Бухгалтеры")
						)
);




/* $_VARS['news_category']	= array(
	"news_action" => array("Акции")
); */

//$_VARS['news_limit']	= 3; // кол-во выводимых последних новостей в списке (не архив)

/* $_VARS['banners_place'] = array(	
	"banner_line_1" => "пара баннеров (линейка 1)",
	"banner_line_2" => "большой баннер (линейка 2)",
	"banner_line_3" => "пара баннеров (линейка 3)",
	"banner_line_4" => "большой баннер (линейка 4)"
); */

//$_VARS['catalog_photo_alb']	= 12;
//$_VARS['item_prefix'] = "item";

// иконки cms
$_ICON = array(
	"down"		=> DIR_ICON."/down2.png"	,
	"up" 		=> DIR_ICON."/up.png"		,
	"del" 		=> DIR_ICON."/del.png"		,
	"edit" 		=> DIR_ICON."/hdsave.png"	,
	"next" 		=> DIR_ICON."/add file.png",
	"next_empty"=> DIR_ICON."/file.png"	,
	"main_menu"	=> DIR_ICON."/actions.png"	,
	"image"		=> DIR_ICON."/image.png"	,
	"user_ok"	=> DIR_ICON."/accept.png"	,
	"user_block"=> DIR_ICON."/delete.png"	,
	"add_item"	=> DIR_ICON."/addd.png"		,
	"tpl_index"	=> DIR_ICON."/flag_green.png",
	"tpl_def"	=> DIR_ICON."/flag_blue.png",
	"redo"		=> DIR_ICON."/redo.png"		,
	"lock"		=> DIR_ICON."/protectred.png",
	"money"		=> DIR_ICON."/creditcard.png",
	"tick"		=> DIR_ICON."/tick.png"		,
	"users1"	=> DIR_ICON."/users1.png"	,
	"pictures"  => DIR_ICON."/pictures.png"
);
/*~~~~~~~~~ /параметры CMS ~~~~~~~~~~~*/

/* // группы пользователей CMS 
$_VARS['arrGroups'] = array(
	"admin" 	=> array("Администраторы"),
	"manager" 	=> array("Менеджеры"),	
	"editor"	=> array("Контент-менеджеры"),
	"finans"	=> array("Бухгалтеры")
); */


// статусы заказа
/* $_VARS['order_status'] = array(
	'raw' 		=> 'не обработан', 
	'confirmed'	=> 'подтвержден', 
	'accepted'	=> 'принят', 
	'shipped'	=> 'отгружен', 
	'paid'		=> 'оплачен'
); */

// типовые формы
$_VARS['item_constr'] 	= array(
	"circle" => "круглая", 
	"square" => "квадратная"
	);
	
$_VARS['arrColor1']		= array(
	"white" => "белый", 
	"cream" => "кремовый", 
	"choko" => "шоколадный"
	);
	
$_VARS['arrColor2']	= array(
	"white" => "белый", 
	"cream" => "кремовый", 
	"choko" => "шоколадный"
	);

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ переменные, редактируемые через cms ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
$_VARS['env'] = array();

// по умолчанию
$_VARS['env']['photo_alb_other'] = 1; // фотоальбом "Разное"

 // переопределение из БД
$sql = "SELECT * FROM `".SITE_PREFIX."_presets` 
		WHERE 1";
$res = mysql_query($sql);
if($res)
{
	while($row = mysql_fetch_array($res))
	{
		$_VARS['env'][$row['var_name']] = $row['var_value'];
	}
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~/переменные, редактируемые через cms ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
?>