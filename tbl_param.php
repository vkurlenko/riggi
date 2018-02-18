<?
$_VARS['tbl_pages_param'] = "
	id	int auto_increment primary key,
	p_url			text,
	p_parent_id		int,
	p_title			text,
	p_menu_title	text,
	p_content		text,
	p_tpl			text,
	p_photo_alb		int,
	p_meta_title	text,
	p_meta_dscr		text,
	p_meta_kwd		text,
	p_order			int,
	p_show			enum('0', '1')
";
?>
