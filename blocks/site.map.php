<?
include DIR_CLASS.'/class.site.map.php';

$html = new SITE_MAP;

$html -> table_name 	= $_VARS['tbl_prefix'].'_pages';
$html -> parent_field 	= 'p_parent_id';
$html -> order_by_field = 'p_order';
$html -> page_title 	= 'p_title'.LANG;
$html -> lang			= LANG;

$code = $html -> selectLevel(0, 0);

//print_r($code);
?>