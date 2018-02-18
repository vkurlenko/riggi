<?
include_once DIR_CLASS.'/class.menu.php';

$arrMenuMain = array();

$arr = new MENU;
$arr -> mainMenu = true;

if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$arr -> lang = '_'.$_SESSION['lang'];
}


$arrMenuMain = $arr -> menuSimple();


$code = '';

foreach($arrMenuMain as $k)
{
	if($k['p_url'] == 'eshop')
		$code .= '<li><a class="'.$cls.'" target="top" href="http://store.riggi.ru/">'.$k['p_title'].'</a></li>'.NL;
	else
	{
		$cls = '';
		if($_PAGE['p_url'] == $k['p_url'])
			$cls = 'active';
		$code .= '<li><a class="'.$cls.'" href="/'.$k['p_url'].'/">'.$k['p_title'].'</a></li>'.NL;
	}
}
?> 
<ul class='menuMain'><?=$code?><div style="clear:left"></div></ul>
<style>
.menuLang{position:absolute; top:5px; right:0}
.menuLang ul{display:block; padding:0; margin:0}
.menuLang ul li{display:block; padding:0; margin:0; float:left; margin-right:25px; font-size:12px; color:#fff}
.menuLang ul li a{font-size:12px; color:#fc9; text-decoration:none;}
.menuLang ul li a:hover{text-decoration:underline}
</style>
<div class="menuLang">
	<ul>
		<li><? 
			if(!isset($_SESSION['lang']) || $_SESSION['lang'] == '') 
				echo 'RUS';
			else 
				echo '<a href="/main/rus/">RUS</a>';
		?>
</li>
		<li><? 
			if(isset($_SESSION['lang']) && $_SESSION['lang'] == 'eng') 
				echo 'ENG';
			else 
				echo '<a href="/main/eng/">ENG</a>';
		?> </li>
		
		
				
	</ul>
</div>

<div style="clear:left"></div>