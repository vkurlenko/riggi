<?
include_once DIR_CLASS.'/class.menu.php';

$arrMenuMain = array();

$arr = new MENU;
$arr -> mainMenu = true;

$arrMenuMain = $arr -> menuSimple();


$code = '';

$w = floor(100 / count($arrMenuMain));

$i = 0;
foreach($arrMenuMain as $k)
{
	$style = 'color: #fc9;font-size: 18px;text-transform: uppercase;text-decoration: none;';
	if($i == 0)
		$style = 'color: #fff;font-family: \'Times New Roman\';font-size: 36px;text-transform: uppercase;text-decoration: none;';
		
	if($i == count($arrMenuMain) - 1)
		$k['p_title'] = '<img src="http://'.$_SERVER['HTTP_HOST'].'/img/tpl/riggi_tube_light.png" width="62" height="19">';
	$code .= '<td ><a style="'.$style.'" href="http://'.$_SERVER['HTTP_HOST'].'/'.$k['p_url'].'/">'.$k['p_title'].'</a></td>'.NL;
	
	$i++;
}
echo $code;
?> 
