<?
include_once DIR_FRAMEWORK.'/class.image.php';

$title 		= $_PAGE['p_title'.LANG];
$content 	= $_PAGE['p_content'.LANG];

/*if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$lang 		= '_'.$_SESSION['lang'];
	$title 		= $_PAGE['p_title'.$lang];
	$content 	= $_PAGE['p_content'.$lang];
}*/



if($_PAGE['p_img'] != 0)
{
	$alb = $_VARS['env']['photo_alb_page'];
	
	$img = new Image();
	$img -> imgCatalogId 	= $alb;
	$img -> imgId 			= $_PAGE['p_img'];
	$img -> imgWidthMax 	= 1017;
	$img -> imgHeightMax 	= 643;		
	$img -> imgTransform	= "crop";
	$tag = $img -> showPic();	
	
	echo '<div class="textImgCont">'.$tag.'</div>';
}
?>

<div class="textBlockContent">
	<?
	if($_PAGE['p_url'] == 'sotrud' && TEXT_LANG(30) != '')
	{
		?>
		<div class="textHead"><?=TEXT_LANG(30)?></div>
		<?
	}
	?>
	<div class="textTitle"><?=$title?></div>
	<div class="textInner"><?=$content?></div>

	<div style="clear:left"></div>
</div>