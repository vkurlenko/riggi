<?
/*****************************************/
/* проверка правильности заполнения формы*/
/*****************************************/

$error = array();	// массив ошибок
$arrSkip = array('sendMsg', 'captcha', 'otherCode');	// массив элементов, которые пропускаются при обработке формы

//mail('victor@vincinelli.com', 'test', 'msg');


if(!empty($_POST))
{
	//printArray($_POST);
	
	// есть ли в форме каптча? (по умолчанию нет)
	$is_captcha 	= false;
	$valid_captcha 	= true;
	 
	foreach($arr as $k => $v)
	{
		if(isset($v['type']) && $v['type'] == 'captcha' && isset($_POST['captchaField']))
		{
			if(!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['captchaField'])
			{
				$valid_captcha = false;		
				$error[] = TEXT_LANG(31);
			}
		}
	}
	
	if($valid_captcha == true)
	{
		
		foreach($arr as $k => $v)
		{
		
			if(in_array($k ,$arrSkip)) 
				continue;
		
			if(isset($v['notempty']) && $v['notempty'] == true && (!isset($_POST[$k]) || trim(strip_tags($_POST[$k]))) == '')
				$error[] = TEXT_LANG(32).' "'.$v['header'].'"'.BR;
				
			else
			{
				$str = trim(strip_tags($_POST[$k]));
				
				if(!isset($v['valid']))
					$v['valid'] = 'TEXT';
			
				if($str != '')
				{
					switch($v['valid'])
					{
						case 'TEXT' : 
										if(!FormElement::formValidData($v['valid'], $str))
											$error[] = TEXT_LANG(33).' "'.$arr[$k]['header'].'" '.TEXT_LANG(34).BR;									
										else
											$arr[$k]['value'] = $str;
	
										break;
						
						case 'PHONE':
										if(!FormElement::formValidData($v['valid'], $str))
											$error[] = TEXT_LANG(33).' "'.$arr[$k]['header'].'" '.TEXT_LANG(35).BR;
										else 
											$arr[$k]['value'] = $str;
	
										break;
										
						case 'EMAIL':
										if(!FormElement::formValidData($v['valid'], $str))
											$error[] = TEXT_LANG(33).' "'.$arr[$k]['header'].'" '.TEXT_LANG(36).BR;
										else 
											$arr[$k]['value'] = $str;
	
										break;
					}	
				}	
			}				
		}
		
	}
}
else
	$error[] = '';
	
$arr['captchaField']['value'] = '';
	
/******************************************/
/* /проверка правильности заполнения формы*/
/******************************************/	
	
	
	
if(!empty($error))	
{
	echo '<p class="error">';
	foreach($error as $k)
	{
		echo $k;
	}
	echo '</p>';
}
elseif(!empty($arr))
{
	
	// имя подписчика
	$userName = $arr['userName']['value'];
	$userMail = $arr['userMail']['value'];
	
	$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_users`
			WHERE user_mail = '".$userMail."'";
	$res = mysql_query($sql);
	
	if($res && mysql_num_rows($res) > 0)
	{
		echo '<p class="error">'.TEXT_LANG(37).'</p>';
	}
	else
	{
		
		
		$rand = mt_rand(0, 1000000000); // контрольная строка
			
		// внесем в БД учетную запись нового пользователя
		$now = date('Y-m-d').' '.date('H:i:s');
		$sql = "INSERT INTO `".$_VARS['tbl_prefix']."_users`
				SET user_mail 	= '".$userMail."',
				user_name	 	= '".$userName."',
				user_pwd	 	= '".$rand."',
				user_reg_date 	= '".$now."',
				user_last_visit = '".$now."'";
		$res = mysql_query($sql);
		
		
		
		/*$errorMsg[] = 'Учетная запись зарегистрирована.';				
		else */
		
		if(!$res) 
		{
			echo '<p class="error">'.TEXT_LANG(38).'</p>';	
		}
		else
		{
			// здесь отправка письма с контрольной строкой			
			$to 	= array(
					'0'=> array('name' => 'Посетителю сайта','email' => $userMail)
			); 
			/* $to 	= array(
					'0'=> array('name' => 'foruser1','email' => 'victor@vincinelli.com')
			); */
			$cc 	= array();
			$bcc 	= array();
			$read 	= array();
			$reply 	= array();
		
			$sender 	= $_VARS['env']['mail_admin'];
			$senderName = TEXT_LANG(44);
			$subject 	= TEXT_LANG(43).' '.$_SERVER['HTTP_HOST'];
			$message 	= '<html>
					<head></head>
					<body>
						'.TEXT_LANG(42).':<br>
						<a href="http://'.$_SERVER['HTTP_HOST'].'/subscribe/confirm/'.$rand.'/">http://'.$_SERVER['HTTP_HOST'].'/subscribe/confirm/'.$rand.'/</a>
					</body>
					</html>';
			
//echo "$to, $sender, $subject, $message, $cc, $bcc, $senderName, $read, true, $reply, true";
			
			//$obj = new sendMail($to, $sender, $subject, $message, $cc, $bcc, $senderName, $read, true, $reply, true);
			$obj = new sendMail();
			
			$obj-> receive 		= $to;
			$obj-> cc 			= array();
			$obj-> bcc 		= array();
			$obj-> sender 		= $sender;
			$obj-> senderName 	= $senderName;
			$obj-> subject 	= $subject;
			$obj-> message 	= $message;
			$obj-> setReply 	= true;
			$obj-> whereReply 	= '';
			$obj-> readRecipt 	= true;
			$obj-> whereRecipt = '';
			$obj-> setheader 	= "MIME-Version: 1.0 \r\n";
			
			$reg_send = $obj->sendEmail(); // результат отправки контрольного письма
			
			if($reg_send)
			{
				$result = '<p class="ok">'.TEXT_LANG(39).' '.$userMail.'. '.TEXT_LANG(40).'</p>';
				foreach($arr as $k => $v)
				{
					$arr[$k]['value'] = '';
				}
				
			}
			else
			{
				$result = '<p class="error">'.TEXT_LANG(41).'</p>';
			}
		
	}
	
	}
	
	
}
?>