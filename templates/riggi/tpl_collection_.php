<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
HTML::insertMeta();
?>
<link rel="stylesheet" href="/css/style.css" type="text/css" />

<script language="javascript" type="text/javascript" src="/js/jquery.1.7.1.min.js"></script>
<script language="javascript" type="text/javascript" src="/js/ui/jquery-ui-1.8.20.custom.js"></script>
<script language="javascript" type="text/javascript" src="/js/script.js"></script>
<!--<script language="javascript" type="text/javascript" src="/js/transition.js"></script>-->

<link rel="stylesheet" href="/css/gallery.css" type="text/css" />
<script language="javascript" type="text/javascript" src="/js/gallery/func.js"></script>

<!--<script language="javascript" type="text/javascript" src="/js/kwicks/kwicks.js"></script>-->

<!--<link rel="stylesheet" href="/js/accordion/acc.css" type="text/css" />
<script type="text/javascript" src="/js/accordion/jquery.ui.core.js"></script>
<script type="text/javascript" src="/js/accordion/jquery.ui.widget.js"></script>
<script type="text/javascript" src="/js/accordion/jquery.ui.mouse.js"></script>

<script language="javascript" type="text/javascript" src="/js/accordion/jquery.ui.accordion.js"></script>-->

<script language="javascript" type="text/javascript">
/*jQuery(document).ready(function($){

var s = $('.acc_ul li').size();
$('.acc_ul').css('width', s * 100);

								
var _acc=$("#acc_wrapper").accordion(
{
	_startItem: 2,
	_currentFrame:0,
	_showBorder:false,
	_borderColor:"#3D5261",
	_borderWidth:3,
	_selectAction:"mouseenter",
	_visibleItems:8,
	_isAutoScroll:false,
	_orientation:"horizontal",
	_containerHeight:300,
	_containerWidth:960,
	_activeWidth:730,
	_inactiveAplha:1,
	_contentPosition:{x:30,y:250},
	_contentClass:".insideContent",
	_inactiveClass:"_inactiveClass",
	_isManualEnter:true,
	_intervalDelay:100000,
	_buttons:{"p":".previous","n":".next"}
});
})*/


</script>

<style>
		.acc{display:block; padding:0; margin:0}
		.acc li{display:block; float:left; width:100px; overflow:hidden}
		.acc li{} 
		
		.accControl span{display:block; float:left; cursor:pointer}
		</style>
		
		<script language="javascript">
		function setW()
		{
			
		}
		
		
		$(document).ready(function()
		{
			/*// первоначально открытый кадр
			var startFrame = 2
			
			// кол-во кадров всего
			var frameNum = $('.acc li').size()
			
			// ширина кадра (закрыты все)
			var frameSize = ($('.acc').width() - 600) / (frameNum - 1);
			
			// установим ширину каждого кадра
			$('.acc li').css('width', frameSize)
			
			
			$('.acc li').each(function(){
				if($(this).attr('class') == 'item'+startFrame)
					$(this).transition({ width: '600px' });
			})
			
			
		
			$('.acc li').click(function(){
				$('.acc li').each(function()
				{
					if($(this).css('width') != frameSize)
						$(this).transition({ width: frameSize+'px' })
				});
				$(this).transition({ width: '600px' });				
			})
			
			$('.accControl a').click(function()
			{
				
				return false
			})*/
			
			
			
		})
		</script>

</head>

<body>
<?
	include 'blocks/fb.php';
	?>
	
	<div id="divMain" class="tplColl">
		<div class="blockHeader">
			<?
			include 'blocks/menu.main.php';
			?>
		</div>
		
		
		
		
		<div class="blockContent">
		
		
		<!-- gallery_slider -->
	
		
		<div id="gallery_slider">
		
		<?
		$arrImg = array(
			array('13564497306085_w800h500.jpg', 'Модель 1'),
			array('13564497677381_w800h500.jpg', 'Модель 2'),
			array('13564498074413_w800h500.jpg', 'Модель 3'),
			array('13564505994579_w800h500.jpg', 'Модель 4')/*,
			array('13564506793035_w800h500.jpg', 'Модель 5'),
			array('13564506406708_w800h500.jpg', 'Модель 6')*/
		);
		
		
		foreach($arrImg as $k)
		{
		?>
		 <div class="slide"> 
		 	<a href="#"><img src="/img/111_files/<?=$k[0]?>" alt="<?=$k[1]?>" /></a>
			<div class="slide-info">
			  <div class="slide-text">
				<p><?=$k[1]?></p>
			  </div>
			</div>
		  </div>
		<?
		}
		
		?>		
		 
		
		<div id="gallery_slider-subtitle"></div>
		
		<!-- /gallery_slider -->
		</div>
		</div>
		
		
		<div class="blockFooter">
		<?
			include 'blocks/footer.php';
		?>
		</div>
	</div>
	
	<div id="bg" class="bgMain"><img src="/img/tpl/bg_coll.jpg" width="100%" height="100%" /></div>
</body>
</html>
