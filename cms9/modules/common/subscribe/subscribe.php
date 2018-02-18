<?
error_reporting(E_ALL);
include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include "subscribe.func.php";

$tableName = $_VARS['tbl_prefix']."_subscribe";


// интервал рассылки пачек писем по умолчанию (сек.)
// может быть переопределен в настройках системы
if(!isset($_VARS['env']['subscribe_period']))
{
	$_VARS['env']['subscribe_period'] = 30;
}

// кол-во писем в час по умолчанию
// может быть переопределен в настройках системы
if(!isset($_VARS['env']['subscribe_count']))
{
	$_VARS['env']['subscribe_count'] = 50;
}

$arrTableFields = array(
	"id"				=> "int auto_increment primary key",
	"subscribe_mail"	=> "text",						/* e-mail подписчика */
	"subscribe_status"	=> "enum('0','1') not null",	/* статус текущей рассылки (0 - рассылка отправлена, 1 - не отправлена) */
	"subscribe_reg_date"=> "datetime not null"			/* дата/времы регистрации подписчика */
);

$db_Table = new DB_Table();
$db_Table -> debugMode = false;
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();


/*if(!isset($_POST['mailTo'])) $mail_admin = $_VARS['env']['mail_admin'];
else $mail_admin = $_POST['mailTo'];*/
$mail_admin = $_VARS['env']['mail_admin'];

$from = $_VARS['env']['mail_from'];

/*function sendMail($mailText, $mail)
{
	global $mail_admin, $from;
	//$s = mail($mail_admin, 'Рассылка', $mailText, "From: ".$from."\nContent-Type: text/html; charset=windows-1251\r\n"."Content-Transfer-Encoding: 8bit\r\n".$Bcc);
	//$s = mail($mail_admin, 'Рассылка', $mailText, "From: ".$from."\nContent-Type: text/html; charset=windows-1251\r\n"."Content-Transfer-Encoding: 8bit\r\n");
	$s = mail($mail, 'Рассылка', $mailText, "From: ".$from."\nContent-Type: text/html; charset=windows-1251\r\n"."Content-Transfer-Encoding: 8bit\r\n");
	return $s;	
}*/

if(isset($_POST['do']))
{
	echo "Начинаем рассылку<br>";
	//echo "Прочитаем ".$_SERVER['DOC_ROOT']."/subscribe.htm<br>";
	
	if(file_exists($_SERVER['DOC_ROOT']."/subscribe.htm"))
	{
		// прочитаем файл рассылки в массив
		$file = file($_SERVER['DOC_ROOT']."/subscribe.htm");
		if(!$file) echo "Ошибка открытия файла<br>";
		else
		{
			// установим статус текущей рассылки для всех подписчиков в 0
			$sql = "update `".$tableName."` set subscribe_status = '0' where 1";
			$res = mysql_query($sql);
			
			include "subscribe.mail.php";
			
			$s = mail($mail_admin, 'Рассылка', $html, "From: ".$from."\n"."Content-Type: text/html; charset=utf-8"."\n"."Content-Transfer-Encoding: 8bit"."\r\n");
			
		}
	}
	else
	{
		?>
		<span class="msgError">Отсутствует шаблон листа рассылки (<?=$_SERVER['DOC_ROOT']."/subscribe.htm"?>)</span>
		<?
	}
		
}

// добавление новой записи
if(isset($addItem))
{
	// проверим на существование email в базе подписчиков
	$sql = "select * from `".$tableName."` where subscribe_mail = '".trim($_POST['subscribe_mail'])."'";
	$res = mysql_query($sql);
	if(mysql_num_rows($res) == 0)
	{
		// вносим новый адрес в базу
		
		$_POST['subscribe_reg_date'] = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
	
		// предварительно удалим ненужные элементы
		$arrData = delArrayElem($_POST, array("addItem", "id"));	
				
		$db_Table -> tableData = $arrData;
		$db_Table -> addItem();	
	}	
}

// удаление записи
if(isset($del_item) and isset($id))
{
	// параметры запроса на удаление
	$db_Table -> tableWhere = array("id" => $id);
	
	// удаление записи
	$db_Table -> delItem();	
}

// изменение записи
if(isset($updateItem) and isset($id))
{	
	// предварительно удалим ненужные в запросе элементы
	$arrData = delArrayElem($_POST, array("updateItem", "id"));
	
		// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}
?>

<?
include_once "head.php";
?>
<body>

<form action="" method="post">
	<input type="submit" name="do" value="Запустить рассылку" />
</form>

<fieldset><legend>Текущая рассылка</legend>
<?
$sql = "select * from `".$_VARS['tbl_prefix']."_presets` where var_name = 'subscribe_count'";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
echo "<br>Отправка последней части рассылки закончена: <strong>".date("Y"."-"."m"."-"."d"." "."H".":"."i".":"."s")."</strong><br><br>";
echo "Интервал рассылки: <strong>".$_VARS['env']['subscribe_period']." (сек.)</strong><br><br>";
echo "Количество писем за интервал: <strong>".$_VARS['env']['subscribe_count']."</strong><br><br>";

$sql = "select * from `".$tableName."` where subscribe_status = '0'";
$res = mysql_query($sql);
echo "Неотправленных писем осталось: <strong>".mysql_num_rows($res)."</strong>";
?> 
</fieldset>

<?
if(!isset($editItem) && !isset($setItem))
{
	?>
	<fieldset><legend>Список подписчиков новостной рассылки</legend>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&setItem"><img src='<?=$_ICON["add_item"]?>'>Добавить новый адрес</a>
	<?
	getItems()
	?> 
	</fieldset>
	<?
}

else
{
	$caption = "Добавить новый адрес подписки";
	$id = "";
	$subscribe_mail 	= "";
	$submit = array('addItem', 'Создать');
	
	if(isset($editItem) && isset($id))
	{		
		$id = $_GET['id'];
		$row = readItem($id);
		$caption 		= "Редактирование адреса ".$row['subscribe_mail'];
		$subscribe_mail 	= $row['subscribe_mail'];
		$submit = array('updateItem', 'Изменить');
	}
	?>
	<fieldset><legend><?=$caption;?></legend>
	
		<form method=post enctype=multipart/form-data action="?page=subscribe" name="form1" id="form1">
		<table>
			<tr>
				<td>
					Адрес подписчика</td><td>
					<input type="hidden" name="id" value="<?=$id?>">		
					<input type="text" name="subscribe_mail" size="40" value="<?=$subscribe_mail?>" />
					
				</td>
			</tr>
			
		<input type="submit" name="<?=$submit[0]?>" value='<?=$submit[1]?>' />
		</form>
	</fieldset>
	<?
}
?>

</body>
</html>
