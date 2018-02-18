<?
include_once DIR_FRAMEWORK.'/class.image.php';
include_once DIR_CLASS.'/class.mod.news.php';




if(isset($_GET['param2']))
{
	$id = intval($_GET['param2']);
	
	$news_item = new MOD_NEWS;
	
	$news_item -> item_id = $id;
	$news_item -> news_alb = $_VARS['env']['photo_alb_news'];
	$news_item -> img_width = 1017;
	$news_item -> img_height = 643;
	
	if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
	{
		$news_item -> lang = '_'.$_SESSION['lang'];
	}

	
	$a = $news_item -> getNewsItem();
	$a = $a[$id];

}

if(!empty($a))
{
	?>
	<div class="textImgCont"><?=$a['news_img']?></div>
	<div class="textBlockContent">
		
		<div class="textHead"><?=$a['news_date']?></div>
		
		<div class="textTitle"><?=$a['news_title']?></div>
		<div class="textInner"><?=$a['news_text_2']?></div>
	
		<div style="clear:left"></div>
	</div>
	<?
}
else
{
	header('location: /news/');
}
?>
