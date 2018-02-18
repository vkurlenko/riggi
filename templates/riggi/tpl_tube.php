<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
html{background:#333}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
HTML::insertMeta();
?>
<link rel="stylesheet" href="/css/style.css" type="text/css" />


<script language="javascript" type="text/javascript" src="/js/jquery.1.6.4.min.js"></script>
<!--<script type="text/javascript" src="/js/lib/jquery-1.4.2.min.js"></script>-->

<script language="javascript" type="text/javascript" src="/js/script.js"></script>

<!-- jcarousel -->
<script language="javascript" type="text/javascript" src="/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="/js/lib/jquery.jcarousel.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/lib/skins/tango/skin.css" />
<!-- /jcarousel -->

<script type="text/javascript">

jQuery(document).ready(function() {

	
	
	
});

</script>






</head>

<body>
	
	<?
	include 'blocks/fb.php';
	?>

	<div id="divMain" class="tplTube">
	
		<div class="blockHeader">
			<?
			include 'blocks/menu.main.php';
			?>
		</div>
		
		
		
		<div class="blockContent">
			
			<div class="tubeContent">
				<div class="tubeLogo"><img src="/img/tpl/icon_tube.png" width="218" height="68" /></div>
				<div class="tubePleer"><!--<img src="/img/pic/pleer.jpg" width="640" height="390" />-->
				<?
				include 'blocks/video.player.php';
				?>
				</div>			
			</div>
			
			<div class="tubeTape" >
				<div>
					<?
					include 'blocks/slider.video.php';
					?>
				</div>
			</div>
			
		</div>
		
		
		
		
		<div class="blockFooter">
			
					
			<?
			include 'blocks/footer.php';
			?>	
			
			
		</div>
		
	</div>
	
	<div id="bg" class="bgMain"><img src="/img/tpl/bg_tube.jpg" width="100%" height="100%" /></div>
</body>
</html>
