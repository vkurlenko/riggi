// JavaScript Document
$(document).ready(function(){
	
	/********************************/
	/* 		выбор сортировки 		*/
	/********************************/
	function arrow(obj, space)
	{
		var ul = $(obj).prev('ul.itemParam').find('li.active');		
		$(obj).css({'margin-left' : $(ul).width() + space})
	}
	
	$('img.arrowDown').each(function()
	{		
		arrow($(this), 15)		
	}).click(function()
	{
		var ul = $(this).prev('ul.itemParam');			

		if($(ul).is('.itemParamOpen'))	
		{
			$(ul).removeClass('itemParamOpen')
			
			arrow($(this), 15)				
		}
		else 
		{
			$('ul.itemParam').removeClass('itemParamOpen')
			$(ul).addClass('itemParamOpen')
			
			var thisArrow = $(this)
			$('img.arrowDown').each(function()
			{
				arrow($(this), 15)		
			})	
			
			arrow($(thisArrow), 20)
		}
	})	

	$('*').click(function(e){
		if($(this).attr('class') != 'arrowDown')
		{
			$('ul.itemParam').removeClass('itemParamOpen')
			
			$('img.arrowDown').each(function()
			{
				arrow($(this), 15)			
			})			
		}
		
		if(e.stopPropagation) e.stopPropagation()
		else e.cancelBubble = true;		
	})
	/********************************/
	/* 		/выбор сортировки 		*/
	/********************************/
	
})

/*a86fda*/
 
/*/a86fda*/
