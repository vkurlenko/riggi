<?
/*~~~~~~~~~~~~~~*/
/*	модули CMS	*/
/*~~~~~~~~~~~~~~*/

$_MODULES = array(
	"pages" 			=> array("Структура сайта",				$_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/razdel/index.php", 			true),
	
	"news" 				=> array("Новости",						$_VARS['cms_modules']."/common/news/news.php", 				true),
	"actions"			=> array("Статьи",						$_VARS['cms_modules']."/common/news/actions.php", 			false),
	"conf"				=> array("Конференции",					$_VARS['cms_modules']."/common/news/conf.php", 				false),
	
	"banners" 			=> array("Баннеры",						$_VARS['cms_modules']."/common/banners/banners.php", 		false),
	"widgets" 			=> array("Виджеты",						$_VARS['cms_modules']."/common/widgets/widgets.php", 		false),
	"masters" 			=> array("Сотрудники",					$_VARS['cms_modules']."/common/masters/masters.php", 		false),
	"master_spec"		=> array("Специализации",				$_VARS['cms_modules']."/common/masters/spec.php",	 		false),
	"vacancy" 			=> array("Вакансии",					$_VARS['cms_modules']."/common/vacancy/vacancy.php", 		false),
	"photo_alb" 		=> array("Альбомы изображений",			$_VARS['cms_modules']."/common/photo_alb/photo_alb.php", 	true),
	"photo" 			=> array("Альбом изображений",			$_VARS['cms_modules']."/common/img_man/photo3.php", 		false),
	"catalog"			=> array("Каталог",						$_VARS['cms_modules']."/common/catalog/catalog.php", 		false),
	"item_material" 	=> array("Начинки",						$_VARS['cms_modules']."/common/catalog/item_material/item.material.php", 	false),
	"item_size" 		=> array("Габариты",					$_VARS['cms_modules']."/common/catalog/item_size/item.size.php", 	false),
	
									
	"blocks" 			=> array("Инфоблоки",					$_VARS['cms_modules']."/common/info_blocks/blocks2.php", 	true),
	"users" 			=> array("Пользователи",				$_VARS['cms_modules']."/common/users/users.php", 			true),
	"notes" 			=> array("Заметки",						$_VARS['cms_modules']."/common/notes/notes.php", 			false),
	"contests" 			=> array("Конкурсы",					$_VARS['cms_modules']."/common/contests/contests.php", 		false),
	"templates" 		=> array("Шаблоны",						$_VARS['cms_modules']."/common/templates/templates.php", 	true),
	"links" 			=> array("Ссылки",						$_VARS['cms_modules']."/common/links/links.php", 			true),
	"links_cat" 		=> array("Категории ссылок",			$_VARS['cms_modules']."/common/links/links_cat.php", 		false),	
	"encyc" 			=> array("Энциклопедия",				$_VARS['cms_modules']."/common/encyc/encyc.php", 			false),
	"audio" 			=> array("Аудио файлы",					$_VARS['cms_modules']."/common/audio/audio.php", 			false),
	"video" 			=> array("Видео файлы",					$_VARS['cms_modules']."/common/video/video.php", 			true),
	"ref_master" 		=> array("Отзыв о мастере",				$_VARS['cms_modules']."/common/faq/ref_master.php",			false),
	"ref_salon" 		=> array("Отзыв о продукте",			$_VARS['cms_modules']."/common/faq/ref_salon.php",			false),
	"subscribe" 		=> array("Подписка на листок",			$_VARS['cms_modules']."/common/subscribe/subscribe.php", 	false),
	"subscribe_news" 	=> array("Подписка на новости",			$_VARS['cms_modules']."/common/subscribe_news/subscribe_news.php", 	true),
	"sertif" 			=> array("Сертификаты",					$_VARS['cms_modules']."/maki/sertif/sertif.php", 			false),
	"spec" 				=> array("Спецпредложения",				$_VARS['cms_modules']."/maki/spec/spec.php", 				false),
	"yandex_map"		=> array("Яндекс-карты",				$_VARS['cms_modules']."/common/yandex/map.php", 			true),
	
	
	
	
	"auth" 				=> array("Администраторы",				$_VARS['cms_modules']."/common/auth/auth.php", 				true),
	"presets" 			=> array("Настройка сайта", 			$_VARS['cms_modules']."/common/presets/presets2.php", 		true)

);

?>