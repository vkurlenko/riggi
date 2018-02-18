<?
$content="<h3>Статистика</h3>";

$ey=mysql_fetch_array(mysql_query("select min(`date`) as date from visitors"));
$year_min=substr($ey["date"],0,4);

if(isset($_POST["y"]))	$year=$_POST["y"];	else $year=date("Y");
if(isset($_POST["m"])) 	$month=$_POST["m"];	else $month=date("m");
if(isset($_POST["d"])) 	$day=$_POST["d"];	else $day=date("d");

$disabled_d=$disabled_m="";
if($year==0) {
	$where="";
	$shap="За все время";
	$disabled_d=$disabled_m=" disabled";
}
else if($month==0) {
	$where=" AND date2='$year-00-00' ";
	$shap="За $year год";
	$disabled_d=" disabled";
}
else if($day==0) {
	$where=" AND date1='$year-$month-00' ";
	$shap="За $month / $year";
}
else {
	$where=" AND `date`='$year-$month-$day' ";
	$shap="За $day / $month / $year";
}

if($day==0) $sel=" selected"; else $sel="";
$dd="<option value='0'$sel>Весь месяц</option>";
for($i=1;$i<32;$i++) {
$i_m=$i;
if(strlen($i_m)==1)$i_m="0$i_m";
if($day==$i_m) $sel=" selected"; else $sel="";
$dd.="<option value='$i_m'$sel>$i</option>";
}

$months_im= Array ("", "январь", "февраль" , "март" , "апрель" , "май" , "июнь" , "июль" , "август" , "сентябрь" , "октябрь" , "ноябрь" , "декабрь" ) ;
if($month==0) $sel=" selected"; else $sel="";
$mm="<option value='0'$sel>Весь год</option>";
for($i=1;$i<12;$i++) {
$i_m=$i;
if(strlen($i_m)==1)$i_m="0$i_m";
if($month==$i_m) $sel=" selected"; else $sel="";
$mm.="<option value='$i_m'$sel>{$months_im[$i]}</option>";
}


if($year==0) $sel=" selected"; else $sel="";
$yy="<option value='0'$sel>Все время</option>";
for($i=$year_min;$i<=date("Y");$i++) {
if($year==$i) $sel=" selected"; else $sel="";
$yy.="<option value='$i'$sel>$i</option>";
}

$content.=<<<YEAR
<br>
<br>
<form action="" method="post" name="form1">
<select name="d" id="d" $disabled_d>$dd</select>&nbsp;&nbsp;&nbsp;&nbsp;
<select name="m" id="m" $disabled_m onChange="if(this.value==0){document.getElementById('d').disabled='true';} else {document.getElementById('d').disabled='';}">$mm</select>&nbsp;&nbsp;&nbsp;&nbsp;
<select name="y" id="y" onChange="if(this.value==0){document.getElementById('m').disabled='true';document.getElementById('d').disabled='true';} else {document.getElementById('m').disabled='';if(document.getElementById('m').value!=0)document.getElementById('d').disabled='';}">$yy</select>&nbsp;&nbsp;&nbsp;&nbsp;
<input name="submit" type="submit" value="Отправить"><br><br>
<a href="{$_SERVER["REQUEST_URI"]}">Сбросить форму</a><br><br>

</form>
YEAR;

$Table="<table>
<tr>
<th > </th>
<th colspan=5>Люди</th>
<th colspan=2>Роботы</th>
</tr>

<tr>
<th > </th>
<th colspan=2>Суммарно</th>
<th colspan=2>Среднесуточно</th>
<th > </th>
<th colspan=2></th>
</tr>

<tr>
<th>  </th>
<th>Посетителей</th>
<th>Страниц</th>
<th>Посетителей</th>
<th>Страниц</th>
<th>Страниц на<br>поcетителя</th>

<th>Страниц</th>
<th>Страниц в день</th>
</tr>

";

#########################################################################################################################
//visitors | id ip url referer title robot date date1 date2 time 
#########################################################################################################################

$content.="<h3>$shap</h3>";


$vis=0;
$R=mysql_query("select distinct(`date`) as dat from visitors where 1 $where  ") ;
$dney=0;
while ($E=mysql_fetch_assoc($R)) {
	$E1=mysql_fetch_assoc(mysql_query("select count(distinct(ip)) as vis from visitors where 1 $where AND `date`='{$E["dat"]}' AND robot!='yes' "));
	$vis+=$E1["vis"];
	$dney++;
	}
	
$R=mysql_query("select count(*) as vis from visitors where 1 $where AND robot!='yes' ") ;
$E=mysql_fetch_assoc($R);
$pages=$E["vis"];
if($dney==0) { $vis_den=0; $pages_den=0;  }
else {  $vis_den=round(100*$vis/$dney)/100;  $pages_den=round(100*$pages/$dney)/100; }
if($vis==0) $pages_vis=0;
else $pages_vis=round(100*$pages/$vis)/100;

$R=mysql_query("select count(*) as vis_r from visitors where 1 $where AND robot='yes' ") ;
$E=mysql_fetch_assoc($R);
$pages_r=$E["vis_r"];
if($dney==0) $pages_r_a=0;
else $pages_r_a=round(100*$pages_r/$dney)/100;


$content .=$Table;
$content .="
<tr align=right style='padding-right:5px;'><th></th><th> $vis </th><th> $pages </th><th> $vis_den </th><th> $pages_den </th><th> $pages_vis </th><th> $pages_r </th><th> $pages_r_a </th></tr>";



$content .="</table>

<h3>Страницы</h3>

<table>
<tr ><th>Адрес</th><th>Заголовок</th><th>Просмотры</th></tr>
";

$Q=" select count(*) as kvo, url, title from visitors where 1 $where AND robot!='yes'  group by url order by kvo desc ";
$R=mysql_query($Q);
while ($E=mysql_fetch_assoc($R)) {
	$content .="\n<tr style='padding-right:5px;'><th align=left>{$E["url"]}</th><th align=left>{$E["title"]}</th><th align=right>{$E["kvo"]}</th></tr>\n";
}

$content .="</table>

<h3>Сайты-источники</h3>

<table>
<tr ><th>WWW</th><th>Переходы</th></tr>
";

$Q=" select count(*) as kvo, referer from visitors where 1 $where AND robot!='yes'  group by referer order by kvo desc ";
$R=mysql_query($Q);
while ($E=mysql_fetch_assoc($R)) {
//	if($E["referer"]=="http://{$_SERVER["HTTP_HOST"]}" OR $E["referer"]=="http://www.{$_SERVER["HTTP_HOST"]}" ) continue;
	if(eregi("http://{$_SERVER['HTTP_HOST']}",$E["referer"]) OR eregi("http://www.{$_SERVER['HTTP_HOST']}",$E["referer"])) continue;
	if($E["referer"]=="")$E["referer"]="Закладка";
	$content .="\n<tr style='padding-right:5px;'><th align=left>{$E["referer"]}</th><th align=right>{$E["kvo"]}</th></tr>\n";
}

$content .="</table>
<br><br>
";


#########################################################################################################################



?>
