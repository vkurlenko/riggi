
<!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="//vk.com/js/api/openapi.js?96"></script>

<script type="text/javascript">
  //VK.init({apiId: 3680329, onlyWidgets: true});3883683
  VK.init({apiId: 3883683, onlyWidgets: true});
</script>


<div class="footerBar">
	<div class="footerBarLeft">
		<?
		include 'blocks/menu.foot.php';
		?>
		
		<div class="footerBarR footerBarRSearch">
			<script language="javascript">
			$(document).ready(function()
			{
				
				var valueDef = $('#fieldSearch').attr('value');
				//alert(valueDef)
				$('#fieldSearch').focus(function()
				{
					$(this).attr('value', '')
				}).blur(function()
				{
					if($(this).attr('value') == '')
						$(this).attr('value', valueDef)
				})
				
				$('#formSearch img').click(function()
				{
					$('#formSearch').submit();
					return false
				})
				
				
				var el = window.parent.document.getElementById('trackControls');
				var status = $(el).find('.jp-play').css('display')
				
				if(status == 'block')
				{
					$('.iconSound img').attr('src', '/img/tpl/icon_sound_off.gif')
					//alert($('.iconSound img').attr('src'))
				}
				else
					$('.iconSound img').attr('src', '/img/tpl/icon_sound.gif')				
					//$('.iconSound img').attr('src', '/img/tpl/sound_2.gif')				
			})
			
			
				
			
			</script>
			
			
			
			<div class="l"></div>
			<div class="iconSound"><img src="/img/tpl/icon_sound_off.gif" /></div>
			<div class="search">
				<form id="formSearch" action="/search/" method="post" enctype="multipart/form-data" ><input id="fieldSearch" name="fieldSearch" type="text" value="<?=TEXT_LANG(16)?>" />
				<a href="#"><img src="/img/tpl/button_search.png" width="27" height="18" /></a></form>
			</div>
		</div>				
		
	</div>
	
	
	<div class="footerBarRight">
		<?
		$arr = array(
			'fb' => '#',
			'tw' => '#',
			'vin'=> '#'
		);
		
		if(isset($_VARS['env']['link_fb']))
			$arr['fb'] = $_VARS['env']['link_fb'];
			
		if(isset($_VARS['env']['link_tw']))
			$arr['tw'] = $_VARS['env']['link_tw'];
			
		if(isset($_VARS['env']['link_vincinelli']))
			$arr['vin'] = $_VARS['env']['link_vincinelli'];
			
		?>				
		<a class="social fb" target="_blank" href="<?=$arr['fb']?>"><img src="/img/tpl/icon_fb.png" width="23" height="23" /></a>
		<a class="social tw" target="_blank" href="<?=$arr['tw']?>"><img src="/img/tpl/icon_tw.png" width="23" height="23" /></a>		
		<style>
		.fb_iframe_widget span{display:block !important; overflow:hidden}
		</style>
		<div class="fb-like social" data-send="false" data-layout="button_count" data-width="142" data-show-faces="false" data-colorscheme="light" data-action="like"></div>
		<script>
		/*$(document).ready(function()
		{
			$('.fb-like span').click(function()
			{
				alert('fb')
				$('.fb-like div').each(function()
				{
					if((this).attr('id') == 'u_0_6')
					{
						alert($('#u_0_6').html())
						$('#u_0_6').css('display', 'none')
					}
				})
			})
		})*/
		
		
		
		
		</script>
		
		<!-- Put this div tag to the place, where the Like block will be -->
<div id="vk_like"></div>
<style>
#vk_like{float:left !important; clear:none !important; margin-left:17px; margin-top:11px}
</style>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "full", width : 205});
</script>
		<a class="footerBarR vincinelli" target="_blank" href="<?=$arr['vin']?>"><img src="/img/tpl/vincinelli.png" /></a>						
	</div>
</div>