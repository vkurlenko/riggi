<?
session_start();
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ ПОЛЬЗОВАТЕЛИ CMS  ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~*/
error_reporting(E_ALL);

include_once "../config.php" ;
include_once "../fckeditor/fckeditor.php";
include_once "../db.php";
//include_once "../functions.php";

check_access(array("admin"));

$table_name = $_VARS['tbl_cms_users'];
$showDebug  = true; // выводить отладочные сообщения

include_once "auth.functions.php";

// группы пользователей CMS 
$arrGroups = $_VARS['arrGroups'];

$arrTblFields = array(
	"id"		=> array("int auto_increment primary key", true, false),
	"userLogin" => array("text", "Логин", "text"),
	"userPwd" 	=> array("text", "Пароль", "text"),
	"userName"	=> array("text", "Имя", "text"),
	"userPatr"	=> array("text", "Фамилия", "text"),
	"userGroup"	=> array("text", "Группа", "select"),
	"userMail" 	=> array("text", "e-mail", "text"),
	"userPhone" => array("text", "Телефон", "text"),
	"userBlock" => array("enum('1', '0') not null", "Блокировка", "checkbox"),
	"userDate" 	=> array("datetime not null", "Дата регистрации", false),	
	"userLastVisit"	=> array("datetime not null", "Последний визит", false)	
);

CreateTable();
if(isset($set_item))
{	
	AddItem($_POST);
}

if(isset($update_item) and isset($id))
{	
	UpdateItem($_POST);
}

if(isset($del_item) and isset($id))
{
	DelItem($id);
}
?>

<?
include_once "head.php";
?>

<body>
<?
showMsg($showDebug);
if(isset($edit_item) and isset($id))
{
// форма редактирования записи
	
	$sql = "select * from `$table_name` where id='$id'";
	//echo $sql;
	$res = mysql_query($sql);	
	$row = mysql_fetch_array($res);
	
	?>
	<fieldset><legend>Редактирование</legend>
	<form method="post" enctype="multipart/form-data" action="" name="form2" id="form2">
	<table class="cmsForm">
		<tr>
			<td><?=$arrTblFields['userLogin'][1];?></td>
			<td><input type="hidden" name="id" value="<?=$row['id']?>" />
				<input type="text" name="userLogin" value="<?=$row['userLogin']?>" size="50" />
			</td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPwd'][1];?></td>
			<td><input type="text" name="userPwd" value="<?=$row['userPwd']?>" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userName'][1];?></td>
			<td><input type="text" name="userName" value="<?=$row['userName']?>" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPatr'][1];?></td>
			<td><input type="text" name="userPatr" value="<?=$row['userPatr']?>" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userGroup'][1];?></td>
			<td>
			<select name="userGroup">
				<?
				foreach($arrGroups as $k => $v)
				{
					$sel = "";
					if($row['userGroup'] == $k) $sel = " selected ";
				?>
					<option value="<?=$k?>" <?=$sel?>><?=$v[0]?></option>
				<?
				}
				?>
			</select>
			</td>
			
		</tr>
				
		<tr>
			<td><?=$arrTblFields['userMail'][1];?></td>
			<td><input type="text" name="userMail" value="<?=$row['userMail']?>" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPhone'][1];?></td>
			<td><input type="text" name="userPhone" value="<?=$row['userPhone']?>" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userBlock'][1];?></td>
			<?
			if($row['userBlock'] == 1) $check = "checked";
			else  $check = "";
			?>
			<td><input type="checkbox" name="userBlock" <?=$check;?> /></td>
		</tr>
		
	</table>
			<input type="submit" name="update_item" value="Сохранить" />	
	</form>
	<a class="serviceLink" href="?page=auth">Все пользователи</a>
	</fieldset>
	<?	
	
}
elseif(isset($add_item))
{
// форма добавления записи
?> 
<fieldset><legend>Добавить запись</legend>
<form method=post enctype=multipart/form-data action="" name="form2" id="form2">
	<table class="cmsForm">
		<tr>
			<td><?=$arrTblFields['userLogin'][1];?></td>
			<td><input type="text" name="userLogin" value="логин" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPwd'][1];?></td>
			<td><input type="text" name="userPwd" value="пароль" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userName'][1];?></td>
			<td><input type="text" name="userName" value="имя" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPatr'][1];?></td>
			<td><input type="text" name="userPatr" value="фамилия" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userGroup'][1];?></td>
			<td>
			<select name="userGroup">
				<?
				foreach($arrGroups as $k => $v)
				{
				?>
					<option value="<?=$k?>"><?=$v[0]?></option>
				<?
				}
				?>
			</select>
			</td>
			
		</tr>
		
		<tr>
			<td><?=$arrTblFields['userMail'][1];?></td>
			<td><input type="text" name="userMail" value="mail" size="50" /></td>
		</tr>
		<tr>
			<td><?=$arrTblFields['userPhone'][1];?></td>
			<td><input type="text" name="userPhone" value="телефон" size="50" /></td>
		</tr>
				
	</table>
	<input type="submit" name="set_item" value="Добавить" />
</form>
<a class="serviceLink" href="?page=auth">Все пользователи</a>
</fieldset>
<?
}
else
{

// выводим список записей
	?>
	<fieldset><legend>Администраторы сайта</legend>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить нового пользователя</a>
	<table cellpadding="5" class="list">
		<tr>			
			<?
			foreach($arrTblFields as $k => $v)
			{
				if($k == "id" || $k == "userPwd" || $k == "userDate" || $k == "userName" || $k == "userLastVisit" ) continue;
				?>
				<th><strong><?=$v[1];?></strong></th>
				<?
			}
			?>
			
			<th><strong>edit</strong></th>
			<th><strong>del</strong></th>
		</tr>
		<?
				
		$sql = "select * from `$table_name` where 1 order by id desc";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
		?>
		<tr>
			<?
			foreach($arrTblFields as $k => $v)
			{
				if($k == "id") continue;
				
				if($k == "userBlock")
				{
					/*if($row[$k] == 1) $icon =  "lock";
					else  $icon =  "user_ok";*/
					?>
					<td align="center"><? if($row[$k] == 1) {?><img src='<?=$_ICON['lock'];?>'><? }?></td>
					<?
				}
				elseif($k == "userLogin")
				{
					?>
					<td><a href="<?=$_SERVER['REQUEST_URI'];?>&edit_item&id=<?=$row['id']?>"><?=$row[$k];?></a></td>
					<?	
				}
				elseif($k == "userGroup")
				{
					?>
					<td><?=$arrGroups[$row[$k]][0];?></td>
					<?	
				}
				
				elseif($k == "userPwd" || $k == "userDate" || $k == "userName" || $k == "userLastVisit")
				{
					
				}
				else
				{
				?>
				<td><?=$row[$k];?></td>
				<?
				}
			}
			?>
			
			<td><a href="<?=$_SERVER['REQUEST_URI'];?>&edit_item&id=<?=$row['id']?>"><img src='<?=$_ICON["edit"]?>'></a></td>
			<td><a href="javascript:if (confirm('Удалить запись?')){document.location='<?=$_SERVER['REQUEST_URI'];?>&del_item&id=<?=$row['id']?>'}"><img src='<?=$_ICON["del"]?>'></a></td>
		</tr>
		<?
		}
		?>		
	</table>
	<a class="serviceLink" href="<?=$_SERVER['REQUEST_URI'];?>&add_item"><img src='<?=$_ICON["add_item"]?>'>Добавить нового пользователя</a>
	</fieldset>
	<?
}
?>

</body>
</html>
