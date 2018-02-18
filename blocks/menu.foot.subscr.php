<?
$arrMenuFoot = array(
	array('/site_map/', 'Карта сайта'),
	array('/subscribe/', 'Подписка'),	
	array('/eshop/', 'Интернет-магазин')
);

$code = '<table style="padding:0; margin:0; border:0; background:#333;"><tr>';
foreach($arrMenuFoot as $k)
{
	$code .= '<td style="padding-right:20px"><a style="color: #ffcc99;font-size: 11px;text-decoration: none;" href="http://'.$_SERVER['HTTP_HOST'].$k[0].'">'.$k[1].'</a></td>'.NL;
}
$code .= '</tr></table>';

echo $code;
?> 

