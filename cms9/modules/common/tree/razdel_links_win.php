<html>
<head>
<title>Администрирование сайта <?=$HTTP_HOST?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="admin.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000" onLoad="window.focus();">
<?php
include $_SERVER['DOC_ROOT']."/config.php" ;
$str="";
$str.="<p style='background-color:#eeeeee;'><b>Разделы справочника</b></p><hr>";
/////////////////////////////////////////////////////////////////////////////////
function razdel_level($parent_id=0,$fon="#ffffff",$level=0)
{
global $razdel_content,$nolevel;
if(!isset($nolevel))$nolevel=100000;
$r=mysql_query("select id, title, url, address, title_s, `on` from `".$_VARS['tbl_pages_name']."` where parent_id = '$parent_id' order by order_by ");
if (mysql_num_rows($r)>0)
{
	if ($parent_id==0) {$razdel_content .=
	"<table width='97%'  border='0' align='right' cellpadding='0' cellspacing='0'>
	";
	$fon="#ffffff";
	}
	else  $razdel_content .=
	"<tr><td background='../images/vert_pol.gif'>
	<table width='97%'  border='0' align='right' cellpadding='0' cellspacing='0'>
	";
while ($ex=mysql_fetch_array($r))
	{
	$url="about.html?{$ex['id']}"; ////////
	if($ex['on']=="0" ) {$fon="#ffdddd";$nolevel=$level;}
	if($level<$nolevel) $fon="#ffffff";
	if($level==$nolevel AND $ex['on']!="0"){$fon="#ffffff";$nolevel=100000;}
	$razdel_content .=
	"
	<tr bgcolor=\"$fon\"><td><nobr><li style='margin-top:0px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;'>{$ex['title']}&nbsp;&nbsp;&nbsp;<span style=\"background-color:#cccccc;\">/{$ex['title_s']}/</span>&nbsp;&nbsp;&nbsp;<span style=\"background-color:#cccccc;\">/en/{$ex['title_s']}/</span></li></nobr></td></tr>
	";
//	$level++;
	razdel_level($ex['id'],$fon,$level+1);
	
	}
	if ($parent_id==0) $razdel_content .=
	"</table>
	";
	else $razdel_content .=
	"</table>
	</td></tr>
	";
}
else $fon="#ffffff";
}
//////////////////////////////////////////////////////////////////////////////////////
$razdel_content ="";
razdel_level(0,"#FFFFFF",0);

echo $razdel_content ;

?>
<br></body>
</html>
