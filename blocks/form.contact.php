<?
include_once DIR_CLASS.'/class.form.php';
include_once DIR_CLASS.'/class.mail.php';


/*$lang = '';
if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
{
	$lang = '_'.$_SESSION['lang'];
}

$arrL = array(
	
	''	=> array(
		'Имя',
		'Ваш email',
		'Защитный код',
		'Отправить',
		'Подписка',
		'Дорогие друзья, чтобы отправить нам письмо воспользуйтесь формой обратной связи. Необходимо заполнить все поля.',
		'Ваше имя',
		'другой защитный код',
		'Сообщение'
		
	),
	
	'_eng' => array(
		'Name',
		'Your e-mail',
		'Protected code',
		'Send',
		'Subscribe',
		'Dear friends, чтобы отправить нам письмо воспользуйтесь формой обратной связи. Необходимо заполнить все поля.',
		'Your name',
		'other code',
		'Message'
		
	)

);*/

$arr = array(
	'userName' => array(		
		'header'	=> TEXT_LANG(17), 		
		'name' 		=> 'userName',
		'value' 	=> '',			
		'notempty' 	=> true,
		'id'		=> 'userName'
	),	
		
	'userMail' => array(
		'header'	=> TEXT_LANG(18), 		
		'name' 		=> 'userMail',
		'value' 	=> '',			
		'notempty' 	=> true,
		'valid'		=> 'EMAIL',
		'id'		=> 'userMail'
	),	
		
	'userMsg' => array(
		'header'	=> TEXT_LANG(25), 		
		'name' 		=> 'userMsg',
		'value' 	=> '',			
		'notempty' 	=> true,
		'type'		=> 'textarea',
		'id'		=> 'userMsg'
	),
	
	'captcha' => array(
		'header'	=> 'captcha',
		'name' 		=> 'captcha',
		'value' 	=> '',			
		'notempty' 	=> true,
		'id'		=> 'captcha',
		'type'		=> 'captcha',
		'src'		=> '/captcha/index.php?'.session_name().'='.session_id(),
		'width'		=> 100,
		'height'	=> 40
	),	
		
	'captchaField' => array(
		'header'	=> TEXT_LANG(19),
		'name' 		=> 'captchaField',
		'value' 	=> '',			
		'notempty' 	=> true,
		'id'		=> 'captchaField'
	),	
			
	'sendMsg'  => array(	
		'header'	=> TEXT_LANG(20),
		'type'		=> 'html',
		'name'		=> 'sendMsg',
		'id'		=> 'sendMsg',
		'value'     => 'Послать'
	),
	
	'otherCode' => array(
		'header'=> TEXT_LANG(24)
	)
);

?>
<p class="infoText"><?=TEXT_LANG(22)?></p>

<?

include 'form.ref.proc.php';


$form1 = new Form();
$form1 -> formData 		= $arr;
$form1 -> formTpl		= DIR_BLOCKS.'/form.contact.tpl.php';
$form1 -> formAction 	= '';
$form1 -> formId 		= 'formContact';
echo $form1 -> formCreate(); 

?>
<script language="javascript">
	
	
	
	
$(document).ready(function()
{
	// другой код каптча
	$('.reloadCaptcha').click(function(){
		var id = Math.floor(Math.random()*10000);
		$('#captcha').attr('src', '/captcha/index.php?id='+id);
		return false
	})
	
	
	var arrDef = [
		[$('#userName'), '<?=TEXT_LANG(23)?>'],
		[$('#userMail'), 'Email'],
		[$('#userMsg'), '<?=TEXT_LANG(25)?>'],
		[$('#captchaField'), '<?=TEXT_LANG(19)?>']
	]
	
	
	for(i = 0; i < arrDef.length; i++)
	{		
		var obj = arrDef[i][0];

		if(obj.get(0).tagName == 'TEXTAREA')
		{
			if(obj.text() == '')
				$(obj).text(arrDef[i][1])
		}
		else
		{
			if(obj.attr('value') == '')
				$(obj).attr('value', arrDef[i][1])
		}		
	}

	
	// отправка формы
	$('#sendMsg').click(function()
	{
		
		for(i = 0; i < arrDef.length; i++)
		{		
			var obj = arrDef[i][0];

			if(obj.get(0).tagName == 'TEXTAREA')
			{
				if(obj.text() == arrDef[i][1])
					$(obj).text('')
			}
			else
			{
				if(obj.attr('value') == arrDef[i][1])
					$(obj).attr('value', '')
			}		
		}
			

		$(this).parents('form').submit();
		
		
		return false;
	})

	
	
	
	var objects = $('#formContact input, #formContact textarea')
	
	function formField(object, field_type)
	{
		//var this_id = $(object).attr('id');
		
		if(field_type == 'textarea')
		{
			var textDef = $(object).text();
			//alert(textDef)
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
			var textDef = $(object).attr('value');
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
