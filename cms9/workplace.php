<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include '../config.php' ;
include '../functions.php' ;
include $_SERVER['DOC_ROOT'].'/'.$_VARS['cms_dir'].'/'.$_VARS['cms_modules'].'/modules.php';
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/auth/auth.checkuser.php";

/*function check_access($arrUsers)
{	
	$this_access = false;
	foreach($arrUsers as $k)
	{
		if($_SESSION['cms_user_group'] == $k)	
		{
			$this_access = true;
			break;
		}
	}	
	
	if($this_access == false)
	{
		echo "Доступ запрещен";
		exit;
	}
}*/

if(isset($page)) 
{
	include $_MODULES[$page][1];
}
else include $_MODULES['pages'][1];

?>