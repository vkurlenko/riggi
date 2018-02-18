<div class="sbscr">
<?
	//printArray($_GET);
	if(	isset($_GET['param2']) && $_GET['param2'] == 'confirm' &&
		isset($_GET['param3']))
	{
		$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users`
				WHERE user_pwd = '".$_GET['param3']."'";
		$res = mysql_query($sql);
		
		if($res && mysql_num_rows($res) > 0)
		{
			
			$row = mysql_fetch_array($res);
			$sql = "UPDATE `".$_VARS['tbl_prefix']."_users`
					SET user_block = '0',
					user_pwd = '',
					user_register = '1'					
					WHERE user_pwd = '".$_GET['param3']."'";
			$res = mysql_query($sql);
			
			
			if($res)
			{
				$sql = "INSERT INTO `".$_VARS['tbl_prefix']."_subscribe_news`
						SET subscribe_mail 	= '".$row['user_mail']."',
						subscribe_status 	= '0',				
						subscribe_reg_date  = NOW()";
				$res2 = mysql_query($sql);
				
				if($res2)
				{
					$result = '<p class="ok">Подписка успешно завершена.</p>';
					include 'blocks/form.subscribe.php';
				}
				else
				{
					
					$result = '<p class="error">Ошибка добавления адреса в лист рассылки.</p>';
					include 'blocks/form.subscribe.php';
				}
			}
			else
			{
				//echo '<p class="error">Контрольная строка не найдена, либо процедура подписки уже успешно завершена.</p>';
				$result = '<p class="error">Контрольная строка не найдена, либо процедура подписки уже успешно завершена.</p>';
				include 'blocks/form.subscribe.php';
			}
		}
		else
		{
			$result = '<p class="error">Контрольная строка не найдена, либо процедура подписки уже успешно завершена.</p>';
			include 'blocks/form.subscribe.php';
		}
			
	}
	else
		include 'blocks/form.subscribe.php';
?>
<div style="clear:both"></div>
	
</div>