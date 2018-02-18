<?
class DB
{
	function db_set($db_cfg)
	{
		if(isset($db_cfg[HOST]))
		{
			define('DB_HOST',	$db_cfg[HOST][0]);
			define('DB_NAME',	$db_cfg[HOST][1]);
			define('DB_USER',	$db_cfg[HOST][2]);
			define('DB_PASS',	$db_cfg[HOST][3]);
		}
		else 
		{
			echo 'Ошибка конфигурации БД ('.HOST.')'.BR;
			exit;
		}
	}

	function db_connect()
	{
		$mysql_mylink = mysql_connect(DB_HOST, DB_USER, DB_PASS);
		
		//echo 'mysql_mylink = '.$mysql_mylink;
		return mysql_select_db(DB_NAME);		
	}
	
	function db_set_names()
	{
		return mysql_query("SET NAMES 'utf8'");
	}
}
?>