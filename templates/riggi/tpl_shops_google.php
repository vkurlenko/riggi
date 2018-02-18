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


<script language="javascript" type="text/javascript" src="/js/jquery.1.7.1.min.js"></script>
<!--<script language="javascript" type="text/javascript" src="/js/jquery.1.6.4.min.js"></script>
-->
<script language="javascript" type="text/javascript" src="/js/script.js"></script>

<script language="javascript" type="text/javascript">
$(document).ready(function()
{		
		
})

</script>

</head>

<body>
	
	<?
	include 'blocks/fb.php';
	?>

	<div id="divMain" class="tplShops">
	
		<div class="blockHeader">
			<?
			include 'blocks/menu.main.php';
			?>
		</div>
		
		
		
		<div class="blockContent">
			
			<?
			include 'blocks/map.google.php';
			?>
		</div>
		
		
		
		
		<div class="blockFooter">
			
					
			<?
			include 'blocks/footer.php';
			?>	
			
			
		</div>
		
	</div>
	
	<div id="bg" class="bgMain"><img src="/img/tpl/bg_text.jpg" width="100%" height="100%" /></div>
</body>
</html>