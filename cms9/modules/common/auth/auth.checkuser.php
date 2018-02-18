<?
function check_access($arrUsers)
{	
	$this_access = false;
	foreach($arrUsers as $k)
	{
		if(isset($_SESSION['cms_user_group']) && $_SESSION['cms_user_group'] == $k)	
		{
			$this_access = true;
			break;
		}
		else
			$this_access = false;
	}	
	
	if($this_access == false)
	{
		?>
		<div style='margin:auto; width:300px; padding:50px; text-align:center; color:#B32B32; background:#FFEEE8; border:2px solid #B32B32; margin-top:150px'>
			Доступ запрещен. <br />Возможно у Вас нет прав для работы с этим модулем или истекло время сессии. <br /><a target="_top" href="http://<?=$_SERVER['HTTP_HOST']?>/cms9/exit.php">Войти в систему</a>
		</div>
		<?
		exit;
	}
	
	//return $
}
?>