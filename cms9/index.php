<?
/*~~~~~~~~~~~~~~~~~~~*/
/*~~~ CMS ГЛАВНАЯ ~~~*/
/*~~~~~~~~~~~~~~~~~~~*/

session_start(); 

include_once '../config.php' ;

// проверяем на наличие данных авторизации в сессии
if(!isset($_SESSION['cms_user_login']) || !isset($_SESSION['cms_user_pwd']) || $_SESSION['cms_user_access'] != true)
{
	// отправляем на авторизацию
	include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/auth/auth.form.php";
}
else
{	
	?>
	<html>
		<head>
		<title>Администрирование сайта <?=$HTTP_HOST;?>
</title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<link href="admin.css" rel="stylesheet" />
		<script language="javascript" type="text/javascript" src="/cms9/js/jquery-1.5.min.js"></script>
		</head>
		
		<frameset rows="30,*" cols="*" frameborder="NO" border="0" framespacing="0"> 
			<frame name="main" scrolling="NO" noresize src="shapka.php" >
		   
			<frameset  cols="13%,*">
				<frame name="menu" src="menu.php" marginwidth="10" marginheight="10" scrolling="auto" frameborder="0">
				<frame name="content" src="workplace.php" marginwidth="10" marginheight="10" scrolling="auto" frameborder="0">
			</frameset>
		
		</frameset>
		
		<noframes></noframes>
	</html>
	<?
}
?>

