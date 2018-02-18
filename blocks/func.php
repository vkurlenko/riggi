<?
/*
	получим путь к картинке
*/

function getImgSrc($imgId)
{
	global $_VARS;
	
	$img = new Image();
	$img -> imgCatalogId 	= $_VARS['env']['photo_alb_page'];
	$img -> imgId 			= $imgId;			
	$img -> imgWidthMax 	= 328;
	$img -> imgHeightMax 	= 246;		
	$img -> imgTransform	= "crop";
	$img -> showPic();
	$img_src = $img -> imgPath;	
	
	return $img_src;
}

?>