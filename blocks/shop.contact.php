<?
include DIR_FRAMEWORK.'/class.image.php';

$shop_addr = strip_tags($_PAGE['p_add_text_1'.LANG], '<br>');
$shop_img = '';
$shop_email = $_PAGE['p_tags'];

if($_PAGE['p_img'] != 0)
{
	$img = new Image();
	$img -> imgCatalogId 	= $_VARS['env']['photo_alb_page'];
	$img -> imgId 			= $_PAGE['p_img'];
	$img -> imgAlt 			= $_PAGE['p_title'.LANG];
	$img -> imgWidthMax 	= 362;
	$img -> imgHeightMax 	= 243;		
	$img -> imgTransform	= "crop";
	$shop_img = $img -> showPic();				
}

$pattern = '(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})';
if(!preg_match($pattern, $shop_email))
	$shop_email = '';	

?>


<?
include 'blocks/map.ya.2.php';
?>


<div class="textBlockContent">
	<div class="contInfo">
		<?
		$infoHeader = 'Riggi';
		if($_PAGE['p_url'] == 'contacts')
			$infoHeader = TEXT_LANG(15);
		?>
		<div class="infoHeader"><?=$infoHeader?></div>
		<p class="infoAddr">
			<em>
				<?=$shop_addr;?>
			</em>
		</p>
		
		<div class="infoImg"><?=$shop_img?></div>
		
		<?
		include 'blocks/form.contact.php';
		?>
		<div style="clear:both"></div>
	</div>
<div style="clear:both"></div>
</div>
