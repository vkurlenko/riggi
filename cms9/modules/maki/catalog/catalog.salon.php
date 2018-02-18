<?
session_start();

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";

check_access(array("admin", "manager"));

$photo_alb = 12;

$tableName = $_VARS['tbl_prefix']."_catalog_salon";

$arrTableFields = array(
	"id" 				=> "int auto_increment primary key",	
	"item_id"	 		=> "int",	// id услуги
	"item_name" 		=> "text", 	// наименование услуги
	"item_parent_id"	=> "int",	// id родительского раздела услуги
	"item_salon_active" => "text", 	// список салонов, где услуга активна
	"item_salon_price"  => "text", 	// список цен на услугу по салонам
	"item_salon_action" => "text",	// список салонов где на услугу действует акция
	
	"item_salon_action_title" 	=> "text",	// заголовок акции
	"item_salon_action_text_1" 	=> "text",	// краткое описание акции
	"item_salon_action_text_2" 	=> "text",	// полное описание акции
	"item_salon_action_img" 	=> "int",	// картинка акции
	"item_salon_action_on_main" => "enum('0','1') not null" // выводить акцию на главной
);

// создание новой таблицы БД

$db_Table = new DB_Table();
$db_Table -> debugMode = false;
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();

###################################


// создаем массив id салонов
$arrSalon = array();
$sql = "select * from `".$_VARS['tbl_pages_name']."` where p_parent_id = 13 and p_show = '1' order by p_order asc";
$res = mysql_query($sql);
while($row = mysql_fetch_array($res))
{
	$arrSalon[] = $row['id'];
}

// читаем информацию об услуге
$sql = "select * from `".$_VARS['tbl_prefix']."_catalog_items` where id=".$_GET['id'];
$res = mysql_query($sql);
$row = mysql_fetch_array($res);
$item_name = $row['item_name'];
$item_parent_id = $row['item_parent'];
$item_price_default_1 = $row['item_price'];
$item_price_default_2 = $row['item_price_2'];
$item_salon_action_on_main = "";

// читаем запись с ценами по выбранной услуге
$sql = "select * from `".$tableName."` where item_id = ".$_GET['id']." limit 0,1";
//echo $sql;
$res = mysql_query($sql);
if(mysql_num_rows($res) == 0)
{
	// если такой услуги еще нет, создаем запись 
	foreach($arrSalon as $k)
	{
		$list_price[$k] = $item_price_default_1."-".$item_price_default_2;	
		$list_action[$k] = 0;	
		$list_active[$k] = 0;	
	}
	
	$item_salon_price = serialize($list_price);
	$item_salon_active = serialize($list_active);
	$item_salon_action = serialize($list_action);
	$item_salon_action_title	= "";
	$item_salon_action_text_1	= "";
	$item_salon_action_text_2	= "";
	$item_salon_action_img		= 0;
	$item_salon_action_on_main	= 0;
	
	
	$arrData = array(
		"item_id" => $_GET['id'],
		"item_name" => addslashes($item_name),
		"item_parent_id" => $item_parent_id,
		"item_salon_active" => $item_salon_active,
		"item_salon_price" 	=> $item_salon_price,
		"item_salon_action" => $item_salon_action,
		"item_salon_action_title" 	=> $item_salon_action_title,	// заголовок акции
		"item_salon_action_text_1" 	=> $item_salon_action_text_1,	// краткое описание акции
		"item_salon_action_text_2" 	=> $item_salon_action_text_2,	// полное описание акции
		"item_salon_action_img" 	=> $item_salon_action_img,	// картинка акции	
		"item_salon_action_on_main" => $item_salon_action_on_main	// акцию на главную страницу	
	);	
		
	$db_Table -> tableData = $arrData;
	$db_Table -> addItem();			
}
else
{
	// иначе читаем запись
	$row = mysql_fetch_array($res);
	/*echo "<pre>";
	print_r($row);
	echo "</pre>";*/
	
	$list_price = unserialize($row['item_salon_price']);
	$list_action = unserialize($row['item_salon_action']);
	$list_active = unserialize($row['item_salon_active']);
	$item_salon_action_title	= $row['item_salon_action_title'];
	$item_salon_action_text_1	= $row['item_salon_action_text_1'];
	$item_salon_action_text_2	= $row['item_salon_action_text_2'];
	$item_salon_action_img		= $row['item_salon_action_img'];
	
	if($row['item_salon_action_on_main'] == 1) $item_salon_action_on_main = " checked ";
	//$item_salon_action_on_main	= $row['item_salon_action_on_main'];
	
	// если кол-во салонов изменилось и в списке цен нет id нового салона, то для этого id добавляем нулевые значения
	if(count($list_price) != count($arrSalon))
	{
		foreach($arrSalon as $k)
		{
			if(!isset($list_price[$k]))
			{
				$list_price[$k] = $item_price_default_1."-".$item_price_default_2;
			}
			
			if(!isset($list_action[$k]))
			{
				$list_action[$k] = 0;
			}
			
			if(!isset($list_active[$k]))
			{
				$list_active[$k] = 0;
			}
		}
	}	
}


if(isset($updateItem) and isset($id))
{	
	$list_price 	= array();
	$list_action 	= array();
	$list_active 	= array();
	
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	
	foreach($arrSalon as $k)
	{
		/* формируем строку диапазона цен для каждого салона */
		if(isset($_POST['item_salon_price_1'][$k]))
		{
			if(trim($_POST['item_salon_price_1'][$k]) == '') $_POST['item_salon_price_1'][$k] = 0;			
		}
		else $_POST['item_salon_price_1'][$k] = 0;
		
		if(isset($_POST['item_salon_price_2'][$k]))
		{
			if(trim($_POST['item_salon_price_2'][$k]) == '') $_POST['item_salon_price_2'][$k] = 0;			
		}
		else $_POST['item_salon_price_2'][$k] = 0;
		
		$arr = array($_POST['item_salon_price_1'][$k], $_POST['item_salon_price_2'][$k]);
		$list_price[$k] = implode("-", $arr);
		/* /формируем строку диапазона цен для каждого салона */
		
		if(isset($_POST['item_salon_action'][$k]))
		{
			$list_action[$k] = 1;
		}
		else $list_action[$k] = 0;
		
		if(isset($_POST['item_salon_active'][$k]))
		{
			$list_active[$k] = 1;
		}
		else $list_active[$k] = 0;
		
		
	}
	
	$item_salon_price = serialize($list_price);
	$item_salon_action = serialize($list_action);
	$item_salon_active = serialize($list_active);
	$item_salon_action_title = $_POST['item_salon_action_title'];
	$item_salon_action_text_1 = $_POST['item_salon_action_text_1'];
	$item_salon_action_text_2 = $_POST['item_salon_action_text_2'];
	$item_salon_action_img = $_POST['item_salon_action_img'];
	
	if(isset($_POST['item_salon_action_on_main']))
	{
		$item_salon_action_on_main = 1;
	}
	else $item_salon_action_on_main = 0;
	
	
	$arrData = array(
		"item_salon_price" => $item_salon_price,
		"item_salon_action" => $item_salon_action,
		"item_salon_active" => $item_salon_active,
		"item_salon_action_title" => $_POST['item_salon_action_title'],
		"item_salon_action_text_1" => $_POST['item_salon_action_text_1'],
		"item_salon_action_text_2" => $_POST['item_salon_action_text_2'],
		"item_salon_action_img" => $_POST['item_salon_action_img'],
		"item_salon_action_on_main" => $item_salon_action_on_main				
	);
	
	if($item_salon_action_on_main == 1)
	{
		$item_salon_action_on_main = " checked ";
		// по какому условию будем делать запрос	
		$db_Table -> tableWhere = array("item_salon_action_on_main" => 1);
		$db_Table -> tableData = array("item_salon_action_on_main" => 0);
		$db_Table -> updateItem();		
	}
	else $item_salon_action_on_main = "";
		
	// по какому условию будем делать запрос	
	$db_Table -> tableWhere = array("item_id" => $id);
	
	// запрос к БД
	$db_Table -> tableData = $arrData;
	$db_Table -> updateItem();	
}

include_once "head.php";
?>

<body>
	
<form action="?page=catalog_salon&id=<?=$_GET['id']?>" method="post"  >
<fieldset>
	<legend>Стоимость услуги в салонах</legend>
	<p>
		<a style="text-decoration:underline" href="?page=catalog_items&edit_menu&type=item_menu&id=<?=$_GET['id']?>"><strong><?=$item_name?></strong></a>
	</p>
	
	
		<input type="hidden" name="item_id" value="<?=$_GET['id']?>">
	<table>
		<tr>
			<th>активность позиции</th>
			<th>салон</th>
			<th>цена в салоне</th>
			<th>акция в салоне</th>
			
		</tr>
		
		<?
		$sql = "select * from `".$_VARS['tbl_pages_name']."` where p_parent_id = 13 and p_show = '1' order by p_order asc";
		$res = mysql_query($sql);
		while($row = mysql_fetch_array($res))
		{
			?>
			<tr>
				<td align="center">
					<?
					$checked = "";
					if($list_active[$row['id']] == 1) $checked = " checked ";
					?>
					<input type="checkbox" name="item_salon_active[<?=$row['id']?>]"  <?=$checked?>>
				</td>
				<td><?=$row['p_title']?></td>
				<td>
					<?
					$arrThisPrice = explode("-", $list_price[$row['id']], 2);
					?>
					от <input type="text" name="item_salon_price_1[<?=$row['id']?>]" value="<?=$arrThisPrice[0]?>" >
					до <input type="text" name="item_salon_price_2[<?=$row['id']?>]" value="<?=$arrThisPrice[1]?>" >
				</td>
				<td align="center">
					<?
					$checked = "";
					if($list_action[$row['id']] == 1) $checked = " checked ";
					?>
					<input type="checkbox" name="item_salon_action[<?=$row['id']?>]" <?=$checked?>>
				</td>
				
			</tr>			
			<?
		}
		?>
			
	</table>
</fieldset>
<p>&nbsp;</p>
<p>&nbsp;</p>
<fieldset>
	<legend>Описание акции, проводимой по данной услуге</legend>
	
	<table>
		<tr>
			<td>Заголовок акции</td>
			<td><input type="text" name="item_salon_action_title" style="width:500px" value="<?=$item_salon_action_title?>" ></td> 	
				
		</tr>
		<tr>
			<td>Картинка акции</td>
			<td>
				<select name="item_salon_action_img">
					<option value='0' selected>Без картинки
					<?
					$r = mysql_query("select * from `photo".$_VARS['env']['alb_action']."` order by `order_by` asc ");
					while($row = mysql_fetch_array($r))
					{
						$selected = "";
						if($row['id'] == $item_salon_action_img) $selected = " selected ";
					
						?>
						<option value='<?=$row['id']?>' <?=$selected?>><?=$row['name']?>
						<?
					}
					?>
				</select>
				<span style="font-size:10px;">(название картинки из фотобанка "<a href="workplace.php?page=photo&zhanr=<?=$_VARS['env']['alb_action'];?>" target="_self">Картинки для акций</a>")</span>
				
				<?
				$pic_width 	= 50;	// заданная ширина итогового изображения
				$pic_height = 50;	// заданная высота итогового изображения
				
				$img_alb_id	= $_VARS['env']['alb_action'];	// id альбома в базе
				$img_id	= $item_salon_action_img;				// id изображения в базе	
				$pic_align 	= "left";	// способ выравнивания тега <IMG>
				if($item_salon_action_img > 0)
				{
					include $_SERVER['DOC_ROOT']."/modules/img/image.inc.php";	
				}
				?>
				
			</td> 	
			
			<tr>
				<td>На главную слева</td>
				<td><input type="checkbox" name="item_salon_action_on_main" <?=$item_salon_action_on_main;?> /></td>
			</tr>
			
		</tr>
	</table>
		<p><strong>Краткое описание акции</strong></p>
			<?
			$editor_text_edit = $item_salon_action_text_1;
			$editor_text_name = 'item_salon_action_text_1';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
			?>			
			<p><strong>Полное описание акции</strong></p>
			<?
			$editor_text_edit = $item_salon_action_text_2;
			$editor_text_name = 'item_salon_action_text_2';
			$editor_height = 200;
			include $_VARS['cms_modules']."/common/editor/fck_editor.php";		
			?>			
			
	
	<input type="submit" name="updateItem" value="Сохранить">
	

</fieldset>
</form>

<?
include "catalog.salon.info.php";
?>
</body>
</html>
