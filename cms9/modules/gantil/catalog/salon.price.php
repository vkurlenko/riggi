<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* редактирование цен на услугу в салонах */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";

include_once $_SERVER['DOC_ROOT']."/cms9/head.php";


// запись массива цен в БД
if(isset($_POST['savePrice']))
{
	unset($_POST['savePrice']);
	
	foreach($_POST as $k => $v)
	{
		if(strpos($k, '|') !== false) $_POST[$k] = format_price($v);
		else
		{
			if($v == 'on') $_POST[$k] = 1;
			else $_POST[$k] = 0;
		}
	}
	
	$price = serialize($_POST);
	
	$sql = "UPDATE `".$_VARS['tbl_prefix']."_catalog`
			SET item_price_array = '".$price."' 
			WHERE id = ".$_GET['id'];
	$res = mysql_query($sql);
	
}

// чтение массива цен из БД
$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_catalog`
		WHERE id = ".$_GET['id'];
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

$arrPrice = unserialize($row['item_price_array']);

/*echo "<pre>";
print_r($arrPrice);
echo "</pre>";*/

?>

<script language="javascript">
	$(document).ready(function(){
		$('.clear').click(function(){
			$('input:text').attr('value', '0.00');
			return false
		})
		
		$('input:text').focus(function(){
			$(this).select();
		})
	})
</script>

<fieldset><legend>Редактирование цен на услугу <a href="/cms9/workplace.php?page=catalog&editItem&id=<?=$_GET['id']?>"><?=$row['item_name']?></a> </legend>

<form action="?id=<?=$_GET['id']?>" method="post">

<table border=0>
	<tr>
		<th></th>
		<th>Активность позиции</th>
		<?
		// специализации мастеров
		foreach($_VARS['master_spec'] as $v)
		{
		?>
		<th><?=$v[1]?></th>
		<?
		}
		?>
	</tr>
	
	
	<?
	// список салонов
	$sql = "SELECT * FROM `".$_VARS['tbl_pages_name']."`
			WHERE p_parent_id = 9
			ORDER BY p_order ASC";
	$res = mysql_query($sql);
	
	while($row = mysql_fetch_array($res))
	{
		?><tr><td><?=$row['p_title']?></td>
		<td align="center">
		<?
		$checked = '';
		if(isset($arrPrice[$row['p_url']]) && $arrPrice[$row['p_url']] == 1) $checked = ' checked ';
		?>
			<input type="checkbox" name="<?=@$row['p_url']?>" <?=$checked?> />
		</td>
		<?
		for($i = 0; $i < count($_VARS['master_spec']); $i++)
		{
			if(isset($arrPrice[$row['p_url']."|".$_VARS['master_spec'][$i][0]]))
			{
				$priceValue = $arrPrice[$row['p_url']."|".$_VARS['master_spec'][$i][0]];
			}
			else $priceValue = '0.00';
			?><td align="center"><input type="text" size="8" name="<?=$row['p_url']."|".$_VARS['master_spec'][$i][0]?>" value="<?=$priceValue?>" ></td><?
		}
		?></tr><?
	}	
	?>	
	
	<tr>
		<td><input type="submit" name="savePrice" value="Сохранить"></td>
		<td colspan="<?=count($_VARS['master_spec'])?>" align="right"><a class="clear" href="#"><strong>Обнулить все цены</strong></a></td>
	</tr>
	
</table>

</form>
</fieldset>

</body>
</html>