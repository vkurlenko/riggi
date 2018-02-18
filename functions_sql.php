<?
include "tbl_param.php";

/*~~~ вывод сообщений отладки ~~~*/
function debugMsg($str, $res)
{
	global $debugMode;
	
	switch($debugMode)
	{
		case "all" : 
			if(!$res){
			?>
			<span class="msgError"><strong>Ошибка:</strong> <?=$str;?></span>
			<? }
			else{
			?>
			<span class="msgOk"><strong>Ok:</strong> <?=$str;?></span>
			<? }
			break;
		case "error" : 
			if(!$res){
			?>
			<span class="msgError"><strong>Ошибка:</strong> <?=$str;?></span>
			<? }			
			break;
		default : break;
	}	
}
/*~~~ /вывод сообщений отладки ~~~*/

function r($sql_query)
{
	$res = mysql_query($sql_query);
	//echo $sql_query;
	return $res;
}

function CreateTable($tbl_name, $tbl_param)
{
	$sql = "create table `$tbl_name` ($tbl_param)";
	echo $sql;
	$res = r($sql);
	if($res) $msg = "Создана таблица $tbl_name ($tbl_param)";
	else $msg = "Не создана таблица $tbl_name ($tbl_param)";
	return $res;	
}
?>