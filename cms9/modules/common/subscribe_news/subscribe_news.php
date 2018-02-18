<?
include_once $_SERVER['DOC_ROOT']."/config.php" ;

error_reporting(ERROR_LEVEL);

include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

$tableName 	= $_VARS['tbl_prefix']."_subscribe_news";
$domain 	= $_SERVER['HTTP_HOST'];
$news_url 	= "news_item"; // url страницы с новостью
$mail_admin = $_VARS['env']['mail_admin'];
$from 		= $_VARS['env']['mail_from'];

include_once "subscribe_news.func.php";



$arrTableFields = array(
	"id"				=> "int auto_increment primary key",
	"subscribe_mail"	=> "text",						/* e-mail подписчика */
	"subscribe_status"	=> "enum('0','1') not null",	/* статус текущей рассылки (0 - рассылка отправлена, 1 - не отправлена) */
	"subscribe_reg_date"=> "datetime not null"			/* дата/время регистрации подписчика */
);

$db_Table = new DB_Table();
$db_Table -> debugMode = false;
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();


// интервал рассылки пачек писем по умолчанию (сек.)
// может быть переопределен в настройках системы
$period = 30;

if(isset($_VARS['env']['subscribe_period']) && intval($_VARS['env']['subscribe_period']))
{
	$period = $_VARS['env']['subscribe_period'];
}


// кол-во писем в час по умолчанию
// может быть переопределен в настройках системы
if(!isset($_VARS['env']['subscribe_count']))
{
	$_VARS['env']['subscribe_count'] = 50;
}


if(isset($_POST['do']))
{
	echo msgOk("Начинаем рассылку");
	//echo "Прочитаем ".DIR_ROOT."/subscribe.htm<br>";	

	// установим статус текущей рассылки для всех подписчиков в 0
	$sql = "UPDATE `".$tableName."` 
			SET subscribe_status = '0' 
			WHERE 1";
	$res = mysql_query($sql);
	
	include "subscribe_news.mail.php";
	
	//$s = mail($mail_admin, 'Начата рассылка новостей', $html, "From: ".$from."\n"."Content-Type: text/html; charset=utf-8"."\n"."Content-Transfer-Encoding: 8bit"."\r\n");
		
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
	<!--<a href="http://<?=$_SERVER['HTTP_HOST']?>/templates/riggi/tpl_subscribe_mail.php" target=_blank>Посмотреть макет</a>-->
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
	
		<form method=post enctype=multipart/form-data action="?page=subscribe_news" name="form1" id="form1">
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
