<? 
// выход пользователя из системы (закрываем сессию)
session_start();
//session_unset();
unset($_SESSION['cms_user_login']);
unset($_SESSION['cms_user_pwd']);
unset($_SESSION['cms_user_group']);
unset($_SESSION['cms_user_access']);

//session_destroy();
header("Location:/cms9/");
//header("WWW-Authenticate: Basic realm='Admin Page'");
//header("HTTP/1.0 401 Unauthorized");
//header("Location:/cms9/");
/*session_start();
session_unset();
unset($_SERVER['PHP_AUTH_USER']);
$_SERVER['PHP_AUTH_USER'] = "";
session_destroy();
header("Location:/cms9/");*/

?>
