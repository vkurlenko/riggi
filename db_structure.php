<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ СТРУКТУРЫ ТАБЛИЦ БД  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/


// таблица пользователей CMS
$authTableFields = array(
	"id"			=> "int auto_increment primary key",
	"userLogin"		=> "text",
	"userPwd" 		=> "text",
	"userName"		=> "text",
	"userPatr"		=> "text",
	"userGroup"		=> "text",
	"userMail" 		=> "text",
	"userPhone" 	=> "text",
	"userBlock" 	=> "enum('1', '0') not null",
	"userDate" 		=> "datetime not null",	
	"userLastVisit"	=> "datetime not null"	
);

// таблица страниц
$pagesTableFields = array(
	"id"				=> "int auto_increment primary key",
	"p_title"			=> "text",
	"p_title_en"		=> "text",	
	"p_url"				=> "text",
	"p_redirect"		=> "text",
	"p_content"			=> "text",
	"p_content_en"		=> "text",
	"p_add_text_1"		=> "text",
	"p_add_text_1_en"	=> "text",
	"p_add_text_2"		=> "text",
	"p_add_text_2_en"	=> "text",
	"p_parent_id"		=> "int",
	"p_nosearch"		=> "enum('0','1') not null",	
	"p_order"			=> "int",
	"p_tags"			=> "text",
	"p_show"			=> "enum('1','0') not null",
	"p_meta_title"		=> "text",
	"p_meta_title_en"	=> "text",	
	"p_meta_kwd"		=> "text",
	"p_meta_kwd_en"		=> "text",
	"p_meta_dscr"		=> "text",
	"p_meta_dscr_en"	=> "text",
	"p_tpl"				=> "text",
	"p_main_menu"		=> "enum('0','1') not null",	
	"p_video"			=> "text",
	"p_photo_alb"		=> "int",
	"p_photo_alb_2"		=> "int",
	"p_img"				=> "int",
	"p_protect"			=> "enum('0','1') not null",
);

// таблица каталога альбомов картинок
$picCatalogueTableFields = array(
	"id" 		=> "int auto_increment primary key",
	"alb_name" 	=> "text",
	"alb_title" => "text",
	"alb_video" => "text",
	"alb_text" 	=> "text",
	"alb_img" 	=> "int",
	"alb_mark" 	=> "enum('none', 'gallery')"
);

$picTableFields = array(
	"id" 		=> "int auto_increment primary key",
	"file_ext" 	=> "text",
	"name" 		=> "text",
	"tags" 		=> "text",
	"pub" 		=> "int",
	"url" 		=> "text",
	"order_by" 	=> "int"
);
?>