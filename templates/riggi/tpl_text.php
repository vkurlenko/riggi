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

<link rel="stylesheet" href="/js/jscrollpane/jquery.jscrollpane.css" type="text/css" />



<!--<script language="javascript" type="text/javascript" src="/js/jquery.1.7.1.min.js"></script>-->
<script language="javascript" type="text/javascript" src="/js/jquery.1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/ui/jquery-ui-1.8.20.custom.js"></script>
<script language="javascript" type="text/javascript" src="/js/script.js"></script>

<script language="javascript" type="text/javascript" src="/js/jquery.mousewheel.js"></script>
<script language="javascript" type="text/javascript" src="/js/jscrollpane/jquery.jscrollpane.min2.js"></script>


<script language="javascript" type="text/javascript">
$(document).ready(function()
{		
	 function blindH1()
	 {
	 
	 	$('.textBlock').css('visibility', 'visible')
	 
	 	if($.browser.msie && $.browser.version<9)
		{
			var pix = 0
			var range = 20;
		
			var interv = setInterval(function()
			{
				var h = getRealSize($(".hide1").css('height')) 
				//alert(h)
				if(h > pix)
				{
					h = h - range;
					if(h < 0) h = 0
					$(".hide1, .hide2").css('height', h);		
				}
				else
				{
					$(".hide1, .hide2").css('height', 0);
					//alert('0')			
					clearInterval(interv)
				}
			}, 1)
		}
		else
		{
			$('.hide1, .hide2').animate({'height' : 1}, 1000)
		}
	 
	 	
		
		
		//$('.hide1').animate({'margin-top' : -200}, 3000)
	 }	
	
	setTimeout(function(){$('.hideAll').remove(); blindH1()}, 1000)
})

</script>

</head>

<body>
	
	<?
	include 'blocks/fb.php';
	?>

	<div id="divMain" class="tplText">
	
		<div class="blockHeader">
			<?
			include 'blocks/menu.main.php';
			?>
		</div>
		
		
		
		<div class="blockContent">
			<div class="blockContentInner">
				<div class="hideAll"></div>
				<div class="hide1"></div>
				<div class="hide2"></div>
				
				<div class="textBlock">
					<?
					include 'blocks/text.php';
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
	
	<div id="bg" class="bgMain"><img src="/img/tpl/bg_text.jpg" width="100%" height="100%" /></div>
</body>
</html>