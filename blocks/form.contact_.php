<?
/******************************************/
/* проверка правильности заполнения формы */
/******************************************/

$error = array();				// массив ошибок
$arrSkip = array('captcha');	// массив элементов, которые пропускаются при обработке формы

if(!empty($_POST))
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
				}	
			}	
		}				
	}
}
else
	$error[] = '';
	
/******************************************/
/* /проверка правильности заполнения формы*/
/******************************************/	
?>
<script language="javascript">
	var arrDef = [
		'Ваше имя',
		'Email',
		'Сообщение',
		'Защитный код'
	];
	
$(document).ready(function()
{
	$('#userName').attr('value', arrDef[0]);
	$('#userMail').attr('value', arrDef[1]);
	$('#userMsg').text(arrDef[2]);
	$('#captchaField').attr('value', arrDef[3]);
	
	var objects = $('#formContact input, #formContact textarea')
	
	function formField(object, field)
	{
		var textDef = $(object).attr('value');
		
		if(field == 'textarea')
		{
			$(object).focus(function()
			{
				if($(this).text() == textDef)
					$(this).text('');
				else
					$(this).select();
			}).blur(function()
			{	  	
				if($(this).text() == '') 
					$(this).text(textDef);
			})	
		}
		else
		{
			$(object).focus(function()
			{
				if($(this).attr('value') == textDef)
					$(this).attr('value', '');
				else
					$(this).select();
			}).blur(function()
			{	  	
				if($(this).attr('value') == '') 
					$(this).attr('value', textDef);
			})	
		}
		
		
	}
	
	$('#formContact input').each(function()
	{
		formField($(this), 'input')
	})
	
	formField($('#formContact textarea'), 'textarea')
	
	
})	
</script>
<p class="infoText">Дорогие друзья, чтобы отправить нам письмо, воспользуйтесь формой обратной связи. Необходимо заполнить все поля.</p>
		
<form id="formContact">
	<input type="text" id="userName" name="userName" value="" />
	<input type="text" id="userMail" name="userMail" value="" />
	<textarea id="userMsg" name="userMsg"></textarea>
	<div>
		<div class="infoCaptcha">
			<img id="captcha" src="/captcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" width="100" height="40"><br />
			<a class="reloadCaptcha" href="#">другой защитный код</a>
		</div>
		<div class="infoCaptchaCheck">
			<input type="text" id="captchaField" name="captcha" value="" />
			<a id="sendMsg" href="#">Послать</a>
		</div>
	
	</div>
								
</form>