<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/cms9/admin.css" type="text/css">

<link type="text/css" href="/cms9/css/smoothness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
<link type="text/css" href="/cms9/css/timePicker.css" rel="stylesheet" />	

<script type="text/javascript" src="/cms9/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/cms9/js/jquery-ui-1.8.16.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="/cms9/js/fancy/jquery.fancybox-1.3.4.pack.js"></script>

<script language="javascript" type="text/javascript" src="/cms9/js/scroll_gallery/jquery.tools.min(1).js"></script>
<script language="javascript" type="text/javascript" src="/cms9/js/jquery.mousewheel-3.0.4.pack.js"></script>

<script language="javascript" type="text/javascript" src="/cms9/js/jquery.form.js"></script>
<script language="javascript" type="text/javascript" src="/cms9/js/jquery.timePicker.min.js"></script>

<script language="javascript" type="text/javascript">
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*	инициализация скролла фотогалереи	*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
function scrollGalleryInit()
{
	$("#gallery").each(function()
	{
		// определим ширину блока с галереей
		if($(this).parent("div").is(".jspPane"))
		{
			var scrollBarW = $(".jspPane").width();	
		}
		else
		{
			var scrollBarW = $(this).parent("div").width() - 20;
		}
		
		$("#gallery").css(
		{
			"width"	: scrollBarW
		});
		
		// инициализация
		$(".scrollable").css(
		{
			"width" : scrollBarW - 80				 
		}).scrollable();

	})
}

$(document).ready(function(){
	$('table.list tr').mouseover(function(){
		$(this).addClass('highlight')
	}).mouseout(function(){
		$(this).removeClass('highlight')
	})
	
	$('.datepicker').datepicker({
		dateFormat : 'yy-mm-dd'
	});	
	
	scrollGalleryInit();
	
	$("a.view").click(function(){
		$("#imgId").attr("value", $(this).attr("title"));
		$(".scrollable .items a").removeClass("selected");
		$(this).addClass("selected");
		return false;
	})
	
	$("#timepicker").timePicker();
	
	/*$("#previewPage").click(function(){
		window.open();
		$("#form1").submit();
		return false
	})*/
	
})
</script>
</head>

