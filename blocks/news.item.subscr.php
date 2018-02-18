<?
include_once DIR_FRAMEWORK.'/class.image.php';
include_once DIR_CLASS.'/class.mod.news.php';


if(isset($news_id))
{
	$id = intval($news_id);
	
	$news_item = new MOD_NEWS;
	
	$news_item -> item_id = $id;
	$news_item -> news_alb = $_VARS['env']['photo_alb_news'];
	$news_item -> img_width = 280;
	$news_item -> img_height = 160;
	$news_item -> img_server = true;
	
	$a = $news_item -> getNewsItem();
	$a = $a[$id];

}

if(!empty($a))
{
	
	$news_html = '<table><tr><td valign="top" style="padding-right:20px">'.$a['news_img'].'</td>';
	
	$news_html .= '<td>
		<div style="font-size: 14px;text-transform: uppercase;color: #999;">'.$a['news_date'].'</div>
		
		<div style="color:#ffffff; font-size: 24px;text-transform: uppercase;padding: 25px 0;margin-top: 25px;">'.$a['news_title'].'</div>
		<div style="color:#ffffff; font-size: 17px;font-family: "Times New Roman", Times, serif;font-style: italic;">'.$a['news_text_1'].'
		<br><br>
		<a style="color:#ffffff; font-size:12px;" href="http://'.$_SERVER['HTTP_HOST'].'/news_item/'.$id.'/">Далее</a>
		<br><br>
		</div>
		
	</td></tr></table>';
	
}

