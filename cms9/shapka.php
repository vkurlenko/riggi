<?
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="admin.css" type="text/css">
<style type="text/css">
<!--
body {
	margin: 1px;
	padding: 1px;
}
-->
</style>
</head>

<body bgcolor="#eeeeee" text="#000000" topmargin="0" leftmargin="0">
<h2 style="padding-left:0px;color:#999999;font-size:17px;text-align:right;">Администрирование сайта <?=$HTTP_HOST;?> [<?=$_SESSION['cms_user_login']?>]<a target="_top" href="exit.php">Выход</a></h2>

</body>
</html>
