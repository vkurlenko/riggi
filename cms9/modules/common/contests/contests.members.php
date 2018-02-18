<?
include $_SERVER['DOC_ROOT']."/config.php" ;
include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php" ;
include $_SERVER['DOC_ROOT']."/db.php";
include "contests.functions.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.html.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.image.php";

include_once $_SERVER['DOC_ROOT']."/cms9/head.php";

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* если запрос на удаление работы из конкурса  */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
if(isset($_GET['delItem']) && isset($_GET['user_id']))
{
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_contests`
			WHERE id = ".$_GET['id'];
	$res_d = mysql_query($sql);
	$row_d = mysql_fetch_array($res_d);
	$arr = unserialize($row_d['contest_items']);
	
	// удалим участника конкурса из массива участников
	unset($arr[$_GET['user_id']]);
	
	// если он был ранее указан как победитель, обнуляем значение победителя
	$del_winner = '';
	if($row_d['contest_winner'] == $_GET['user_id']) $del_winner = ', contest_winner = 0, contest_winner_item = 0';
	
	// удалим участника конкурса из БД
	$sql = "UPDATE `".$_VARS['tbl_prefix']."_contests`
			SET contest_items = '".serialize($arr)."'".$del_winner."
			WHERE id = ".$_GET['id'];

	$res_m = mysql_query($sql);	
}
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/* /если запрос на удаление работы из конкурса  */
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/



if(!empty($_POST))
{
	$sql = "UPDATE `".$_VARS['tbl_prefix']."_contests`
			SET contest_winner = ".$_POST['winnerId'].",
			contest_winner_item = ".$_POST['winnerImg'.$_POST['winnerId']]."
			WHERE id = ".$_GET['id'];
	$res_m = mysql_query($sql);
	
	if($_POST['winnerId'] > 0)
	{
		$sql = "UPDATE `".$_VARS['tbl_prefix']."_contests`
				SET contest_active = '0'
				WHERE id = ".$_GET['id'];
		$res_m = mysql_query($sql);
	}
}



$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_contests`
		WHERE id = ".$_GET['id'];
$res_m = mysql_query($sql);

if(mysql_num_rows($res_m) > 0)
{
	$row_m = mysql_fetch_array($res_m);
	
	if(trim($row_m['contest_items']) != '' && is_array(unserialize($row_m['contest_items'])))
	{
		$arr = unserialize($row_m['contest_items']);
		/*echo "<pre>";
		print_r($arr);
		echo "</pre>";*/
		
		?>
		<style>
		.item{float:left; text-align:center; padding:5px}
		.itemImg{width:150px; height:150px}
		</style>
		<form action="" method="post" name="setWinner">
		<div class="item">
				<div class="itemImg">Победитель <br>не определен</div>
				<p>&nbsp;<br>&nbsp;</p>
				<p><input type="radio" name="winnerId" value="0" <? if($row_m['contest_winner'] == 0) echo 'checked'?>>
				<input type="hidden" name="winnerImg0" value="0" />
				</p>
			</div>
		<?
		
		
		//echo $row_m['contest_items'];
		foreach($arr as $k => $v)
		{
			$img = new Image();
			$img -> imgCatalogId 	= $v[0];
			$img -> imgId 			= $v[1];
			$img -> imgAlt 			= "";
			$img -> imgWidthMax 	= 150;
			$img -> imgHeightMax 	= 150;	
			$img -> imgMakeGrayScale= false;
			$img -> imgGrayScale 	= false;
			$img -> imgTransform	= "resize";
			$html = $img -> showPic();
			
			$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users`
					WHERE id = ".$k;
			$res_u = mysql_query($sql);
			$row_u = mysql_fetch_array($res_u);
			$user_name = $row_u['user_name'];
			
			$sel = '';
			if($row_m['contest_winner'] == $k) $sel = "checked";
			
			?>
			<div class="item">
				<div class="itemImg"><?=$html;?></div>
				<p><?=$user_name?><br>
					голосов - <?=$v[2]?></p>
				<p><input type="radio" name="winnerId" value="<?=$k?>" <?=$sel?> />
				<input type="hidden" name="winnerImg<?=$k?>" value="<?=$v[1]?>" />
				<a href="javascript:if (confirm('Удалить работу?')){document.location='?delItem&user_id=<?=$k;?>&id=<?=$_GET['id']?>'}"><img src='<?=$_ICON["del"]?>'></a></p>
			</div>
			<?
		}
		?>
		<div style="clear:left"></div>
		<input type="submit" value="Выбрать победителя">
		</form>
		<?
	}
}

?>