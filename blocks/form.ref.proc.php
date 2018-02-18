<?
/*****************************************/
/* проверка правильности заполнения формы*/
/*****************************************/

$error = array();	// массив ошибок
$arrSkip = array('sendMsg', 'captcha', 'otherCode');	// массив элементов, которые пропускаются при обработке формы




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
				$error[] = 'Ошибка проверки защитного кода';
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
				$error[] = 'Не заполнено поле "'.$v['header'].'"'.BR;
				
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
											$error[] = 'Поле "'.$arr[$k]['header'].'" должно содержать только текст'.BR;									
										else
											$arr[$k]['value'] = $str;
	
										break;
						
						case 'PHONE':
										if(!FormElement::formValidData($v['valid'], $str))
											$error[] = 'Поле "'.$arr[$k]['header'].'" должно иметь формат +7 123 4567890'.BR;
										else 
											$arr[$k]['value'] = $str;
	
										break;
										
						case 'EMAIL':
										if(!FormElement::formValidData($v['valid'], $str))
											$error[] = 'Поле "'.$arr[$k]['header'].'" должно иметь формат name@server.domain'.BR;
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
	if($shop_email != '')
		$address = $shop_email;
	else
		$address = $_VARS['env']['mail_admin'];

	$userName = $arr['userName']['value'];
	$to 	= array(
			'0'=> array('name' => 'Администратору магазина','email' => $address)
	);
	$cc 	= array();
	$bcc 	= array();
	$read 	= array();
	$reply 	= array();

	$sender 	= $arr['userName']['value'];
	$senderName = $arr['userName']['value'];
	$subject 	= 'Поступило сообщение от  посетителя сайта';
	$message 	= '<html>
			<head>
			 </head>
			<body>
				От посетителя сайта '.$userName.' поступило сообщение.<br /><br />
				Email : '.$arr['userMail']['value'].'<br>
				Сообщение : 
				<p>'.$arr['userMsg']['value'].'</p>
			</body>
			</html>';
	//$obj = new sendMail($to, $sender, $subject, $message, $cc, $bcc,$senderName, $read, true, $reply, true);
	$obj = new sendMail();
			
			$obj-> receive 		= $to;
			$obj-> cc 			= array();
			$obj-> bcc 			= array();
			$obj-> sender 		= $sender;
			$obj-> senderName 	= $senderName;
			$obj-> subject 		= $subject;
			$obj-> message 		= $message;
			$obj-> setReply 	= true;
			$obj-> whereReply 	= '';
			$obj-> readRecipt 	= true;
			$obj-> whereRecipt = '';
			$obj-> setheader 	= "MIME-Version: 1.0 \r\n";
	$call_send = $obj->sendEmail(); // результат отправки письма
	
	if($call_send)
	{
		foreach($arr as $k => $v)
		{
			$arr[$k]['value'] = '';
		}
		echo '<p class="ok">Ваше сообщение отправлено администратору сайта</p>';		
	}
}
?>