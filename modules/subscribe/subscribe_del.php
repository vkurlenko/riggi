<?
/*~~~~~~~~~~~~~~~~~~~~~~~~*/
/*    */
/*~~~~~~~~~~~~~~~~~~~~~~~~*/

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php";
include $_SERVER['DOC_ROOT']."./db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

$tableName = $_VARS['tbl_prefix']."_subscribe";

$arrTableFields = array(
	"id"				=> "int auto_increment primary key",
	"subscribe_mail"	=> "text",						/* e-mail  */
	"subscribe_status"	=> "enum('0','1') not null",	/*    (0 -  , 1 -  ) */
	"subscribe_reg_date"=> "datetime not null"			/* /   */
);

$db_Table = new DB_Table();
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;

if(isset($_GET['delItem']))
{
	//    
	$db_Table -> tableWhere = array("subscribe_mail" => $_GET['delItem']);
	
	//  
	$db_Table -> delItem();	
}

header ("location:http://".$_SERVER['HTTP_HOST']."/50/");
?>