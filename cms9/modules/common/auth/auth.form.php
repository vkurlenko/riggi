<?
//session_start();
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ АВТОРИЗАЦИЯ ПОЛЬЗОВАТЕЛЯ CMS  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
error_reporting(E_ALL);

include_once "../config.php" ;
include_once "../fckeditor/fckeditor.php";
include_once "../db.php";

$table_name = $_VARS['tbl_cms_users']; // таблица пользователей cms 
$showDebug  = true; // выводить отладочные сообщения

include "auth.functions.php";


if(!empty($_POST))
{
	$sql = "select * from `$table_name` where userLogin = '".trim($_POST['userLogin'])."' and userPwd = '".trim($_POST['userPwd'])."' and userBlock = '0' limit 0,1";
	//echo $sql;
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0)
	{
		$row = mysql_fetch_array($res);
		
		// уничтожаем переменные сессии
		unset($_SESSION['cms_user_login']);
		unset($_SESSION['cms_user_pwd']);
		unset($_SESSION['cms_user_group']);
		
		// устанавливаем новые
		$_SESSION['cms_user_login'] = trim($_POST['userLogin']);
		$_SESSION['cms_user_pwd'] 	= trim($_POST['userPwd']);		
		$_SESSION['cms_user_group'] = $row['userGroup'];
		$_SESSION['cms_user_access'] = true;
		
		header("location: /cms9/index.php");
	}

}
?>

<?
include_once "head.php"; 
?>

<body>

<div id="authForm">
	<form action="" method="post">
		<table>
			<tr>
				<td>Логин: </td>
				<td><input type="text" name="userLogin" /></td>
			</tr>
			<tr>
				<td>Пароль:</td>
				<td><input type="password" name="userPwd" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="auth_submit" value="Вход" /></td>
			</tr>			
		</table>	
	</form>
</div>

<pre>
<?
//print_r($_SESSION);
?>
</pre>



</body>
</html>
