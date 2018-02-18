<?php
session_start();
/*~~~~~~~~~~~~~~~~*/
/*~~~ CMS МЕНЮ ~~~*/
/*~~~~~~~~~~~~~~~~*/

include '../config.php';
include $_SERVER['DOC_ROOT'].'/'.$_VARS['cms_dir'].'/'.$_VARS['cms_modules'].'/modules.php';
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST;?></title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<link href="admin.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="/cms9/js/jquery-1.6.2.min.js"></script>
<style>
ul{margin:0; padding-left:20px;}
a:hover{text-decoration:underline}
a.active{color:#FF0000}
</style>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) 
{
  window.open(theURL,winName,features);
}

$(document).ready(function(){
	$("a").click(function()
	{
		$("a").removeClass("active");
		$(this).addClass("active");
	})
})
//-->
</script>
</head>

<body style="background-color:#eeeeee">


<?
foreach($_MODULES as $k => $v)
{
	if($v[2] == true)
	{		
		if(is_array($v[1]))
		{
			echo $v[0];
			print_r($v1);
			
			?>
			<ul>
				<?
				foreach($v[1] as $k1 => $v1)
				{
					if($v1[2] == true)
					{
						?><li><a href="/cms9/workplace.php?page=<?=$k1?>" target="content"><?=$v1[0]?></a></li><?
					}
				}
				?>
			</ul>
			<hr>
			<?
		}
		else
		{
		?>
		<p>
			<strong>
				<a href="/cms9/workplace.php?page=<?=$k?>" target="content"><?=$v[0]?></a>
			</strong>
		</p>
		<hr>
		<?
		}
	}
}
?> 

<!--<pre>
<?
print_r($_MODULES);
?>
</pre>-->


<!-- Логи -->
<!--<p><strong>логи</strong></p>
<ul>
  <li><a href="workplace.php?page=logs" target="content">список</a></li>
  <li><a href=#hhh onClick="if(confirm('Вы действилельно хотите очистить лог?'))parent.content.location.href='workplace.php?page=logs&delete=delete';" >очистить</a></li>
</ul>
<hr>
<p><strong>статистика</strong></p>
<ul>
  <li><a href="workplace.php?page=visitors" target="content">список</a></li>
</ul>
<hr>
<p><strong>конфигурация</strong></p>
<ul>
  <li><a href="workplace.php?page=contmainedit" target="content">список</a></li>
</ul>
<hr>
-->
<pre>
<?
//print_r($_SESSION);
?>
</pre>
</body>
</html>