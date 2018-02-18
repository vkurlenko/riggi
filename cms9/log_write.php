<?
//error_reporting(0);
include '../config.php' ;
include '../functions.php' ;
$log_file="logs.txt";


$admin=$PHP_AUTH_USER;
$IP=$_SERVER["REMOTE_ADDR"];
$date=date("Y-m-d H:i:s");

$fout=fopen ( $log_file , "a" ) ;

fputs ( $fout , "$date $IP $admin $text\n" ) ;	

fclose($fout);

?><script type="text/javascript">window.close();</script>
<script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
