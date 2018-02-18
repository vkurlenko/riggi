<?
include_once DIR_FRAMEWORK.'/class.image.php';
include DIR_CLASS.'/class.mod.news.php';

mb_internal_encoding("UTF-8");

$news = new MOD_NEWS;
$news -> news_alb 	=  $_VARS['env']['photo_alb_news'];
$news -> img_height = 75;
$news -> img_width 	= 125;
$news -> img_align 	= '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$news -> lang = LANG;
}

$arrNews = $news -> getNewsList();

?> 
<div class="newsLineCont">
	<div class="newsLine">
	
		<a class="arrow arrowL prev browse left" href="#"><img src="/img/tpl/arrow_left.png" /></a>
		
		<div class="scrollable">
		
			<div class='items'>
			
			<?
			foreach($arrNews as $k => $v)
			{
				$news_title = new HTML;
				$news_title -> text = $v['news_title'];
				$news_title -> length = 40;
				$news_title -> stop = ' ';
				$text1 = $news_title -> textCrop();
				
				$news_text_1 = new HTML;
				$news_text_1 -> text = $v['news_text_1'];
				$news_text_1 -> length = 175;
				$news_text_1 -> stop = ' ';
				$text2 = $news_text_1 -> textCrop();
			
			?>
			
				<div class='item'>
			
					<div class="newsHeader ">
						<strong><?=TEXT_LANG(6)?></strong>
						<div class="newsDate"><?=$v['news_date']?></div>
						<div style="clear:left"></div>
						<a class="newsTitle" href="/news_item/<?=$k?>/"><?=$text1?></a>
					</div>
					
					<div class="newsAbout">
						<a class="newsMore" href="/news_item/<?=$k?>/"><?=TEXT_LANG(4)?></a>
						<a class="newsArch" href="/news/"><?=TEXT_LANG(5)?></a>
						<div style="clear:both"></div>
						<p class="newsShText"><?=$text2?></p>
									
					</div>
					
					<a class="newsTumb" href="/news_item/<?=$k?>/"><?=$v['news_img']?></a>
				
				 </div>
			<?
			}
			?>
			</div>
		
		</div>
		
		<a class="arrow arrowR next browse right" href="#"><img src="/img/tpl/arrow_right.png" /></a>
		
		<div style="clear:left"></div>
		
	</div>
</div>