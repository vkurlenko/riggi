<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<style>
body{padding:0; margin:0}
#mainFrame{border:0; width:100%; height:100%}
</style>

<script language="javascript" type="text/javascript" src="/js/jquery.1.6.4.min.js"></script>

<script language="javascript">
$(document).ready(function()
{
	
	
	
	// функция переформатирования страницы
	function resizeFrame()	
	{
		var h = $(window).height();
	
		$('iframe').height(h)
	}
	
	/*
		при изменении размеров окна вызов функции переформатирования страницы
	*/
	
	resizeFrame()
	
	var resizeFrameTimer = null;
	
	$(window).bind('resize', function()
	{
		if (resizeFrameTimer != null) 
		{
			clearTimeout(resizeFrameTimer);
			resizeFrameTimer = null;
		}
		
		resizeFrameTimer = setTimeout(resizeFrame, 100);
		
	})	
	
})
</script>

<body>

<iframe id="mainFrame" src="http://riggi/main/"></iframe>

</body>
</html>
