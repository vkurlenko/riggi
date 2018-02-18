<?
//error_reporting(0);
$log_file="logs.txt";

$content="";

if(isset($delete) and $delete=="delete") {
$fout=fopen ( $log_file , "w" ) ;
fwrite($fout,"");
$content .= "<h2>Лог очищен</h2>";

}



if(file_exists($log_file) AND filesize($log_file )>0) {
$fout=fopen ( $log_file , "r" ) ;
$log_text= fread ( $fout , filesize($log_file ) ) ;	
fclose($fout);
$content .= nl2br($log_text);
}


$content ="<div style='padding-top:10px;padding-left:25px;'><h2>Логи</h2>$content</div>";

?>