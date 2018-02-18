<?
include DIR_FRAMEWORK.'/class.image.php';
include DIR_CLASS.'/class.mod.news.php';




$news = new MOD_NEWS;
$news -> news_alb 	=  $_VARS['env']['photo_alb_news'];
$news -> img_height = 630;
$news -> img_width 	= 450;
$news -> img_align 	= '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$news -> lang = LANG;
}
$arrNews = $news -> getNewsList();



?> 

<div class="newsTape">

	<a class="arrow arrowL prev browse left" href="#"><img src="/img/tpl/arrow_left.png" /></a>
	
	<div class="scrollable">
	
		<div class="items">
		
		<?
		foreach($arrNews as $k => $v)
		{
			?>
			<div class="newsItem item">
				<a href="/news_item/<?=$k?>/"><?=$v['news_img']?></a>
				<div class="newsSnippet">
					<div class="newsSnDate"><?=$v['news_date']?></div>
					<a class="newsSnMore" href="/news_item/<?=$k?>/"><?=TEXT_LANG(4)?></a>		
					<div style="clear:both"></div>
					<a class="newsSnTitle" href="/news_item/<?=$k?>/"><?=$v['news_title']?></a>						
				</div>
			</div>
			
			<?
		}
		?>	
		
		</div>
	</div>

	<a class="arrow arrowR next browse right" href="#"><img src="/img/tpl/arrow_right.png" /></a>
	
	<div style="clear:left"></div>
</div>

