<?


$arrMenuFoot = array(
	array('/site_map/',		TEXT_LANG(27)),
	array('/subscribe/', 	TEXT_LANG(28)),	
	//array('/eshop/', 'Интернет-магазин')
	array('/contacts/', 	TEXT_LANG(29))
);

$code = '';
foreach($arrMenuFoot as $k)
{
	$code .= '<li><a href="'.$k[0].'">'.$k[1].'</a></li>'.NL;
}
?> 
<ul class='menuFoot'><?=$code?><div style="clear:left"></div></ul>
