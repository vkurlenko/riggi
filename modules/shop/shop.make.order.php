<?
session_start();

/*echo "<pre>";
print_r($_POST);
echo "</pre>";
error_reporting(E_ALL);*/

include $_SERVER['DOC_ROOT']."/config.php";
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/functions.php";

$table_name = $_VARS['tbl_prefix']."_orders";
$mail_admin = $_VARS['mail_admin'];
$from = "sweetstar@sweetstar.ru";


$orderStatus = "";
$i = 0;
foreach($_VARS['order_status'] as $k => $v)
{
	$i++;
	$orderStatus .= "'".$k."'";
	if($i < count($_VARS['order_status'])) $orderStatus .= ", ";
}

$arrTblFields = array(
	"id"		=> "int auto_increment primary key",
	"orderUser"	=> "int not null default 0",
	"orderList" => "text",
	"orderDate" => "datetime not null",
	"orderStatus" => "enum(".$orderStatus.") not null"
);

$orderList = array();
$sum = 0;
$orderDate = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
$orderText = ""; // список заказанных товаров в тексте письма

/*~~~ создаем новую таблицу ~~~*/
function CreateTable()
{
	global $table_name, $arrTblFields;
	$sql = "create table `$table_name` (";
	$i = 0;
	foreach($arrTblFields as $k => $v)
	{
		$i++;
		$sql .= $k." ".$v;
		if($i == count($arrTblFields)) 
		{
			$sql .= ")";
		}
		else
		{
			$sql .= ", ";
		}
	}
			
	$res = mysql_query($sql);
	/*msgStack("<pre>".$sql."</pre>", $res);
	showMsg(true);*/
}
/*~~~ /создаем новую таблицу ~~~*/

// отправка письма админу о новом заказе
function sendMailAdmin($userName, $text)
{
	global $mail_admin, $from, $orderDate;
	
	$mailText = "Получен новый заказ от пользователя ".$userName." (".$orderDate."):\n";
	$mailText .= $text."\n";	
		
	$s = mail($mail_admin, 'Новый заказ', $mailText, "From: ".$from."\nContent-Type: text/plain; charset=utf-8");
	return $s;	
}


CreateTable();




foreach($_POST as $k => $v)
{
	if(strpos($k, $_VARS['item_prefix']) !== false)
	{
		$id = substr($k, strlen($_VARS['item_prefix']) + 1);	// выделяем id товара
		
		$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id=$id";
		$res = mysql_query($sql);
		$row = mysql_fetch_array($res);
		
		$price = $_POST['price_'.$id];							// выделяем цену товара
		$orderList[$id] = array($v, $price);					// id => array(количество, цена)
		
		$orderText .= $row['item_name']." ".$v." ед.\n";
		
		$sum += $v * $price;	// промежуточная общая сумма заказа
	}
}
$orderList['sum'] = $sum;
$orderText .= "на общую сумму ".$sum;

// узнаем id пользователя
$sql = "select id from `".$_VARS['tbl_prefix']."_users` where regLogin = '".$_SESSION['userLogin']."'";
//echo $sql;
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$userId = $row['id'];
//echo $userId;

// пишем в БД новый заказ
$sql = "insert into `$table_name` set 
	orderUser = $userId,
	orderList = '".serialize($orderList)."',
	orderDate = '$orderDate'";
	
$res = mysql_query($sql);

if($res)
{
	sendMailAdmin($_SESSION['userLogin'], $orderText);
}

/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";*/

header("Location: /your_orders/$userId/");
?>