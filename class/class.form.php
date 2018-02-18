<?
/*********************************/
/* класс создания элемента формы */
/*********************************/
class FormElement
{	
	var $header	= '';
	var $id 		= '';
	var $type 	= 'text';
	var $name 	= '';
	var $value	= '';
	var $class	= '';
	var $width	= '';
	var $height	= '';	
	var $size	= '';
	
		
	/*****************************/
	/* генерация эелемента формы */
	/*****************************/
	function createElement()
	{
		$elementHtml = '';		
		
		switch($this -> type)
		{			
			case 'textarea' 	: 
				$elementHtml = "<textarea id='".$this->id."' name='".$this->name."'  class='".$this->class."'>".$this->value."</textarea>"; 
				break;
				
			case 'html' 	: 
				$elementHtml = $this->value; 
				break;
			
			case 'submit' 	: 
				$elementHtml = "<input type='submit' name='".$this->name."' class='".$this->class."' id='".$this->id."' size='".$this->size."' value='".$this->value."'>"; 
				break;
			
			case 'captcha' 	: 
				$elementHtml = "<img id='".$this->id."' src='".$this->src."' width='".$this->width."' height='".$this->height."'>"; 
				break;
			
			case 'captcha_reload' 	: 
				$elementHtml = "<a href='#' id='".$this->id."' class='".$this->class."'>".$this->value."</a>"; 
				break;
			
			default: 
				$elementHtml = "<input type='text' name='".$this->name."' class='".$this->class."' id='".$this->id."' size='".$this->size."' value='".$this->value."'>"; 
				break;
		}
		
		return $elementHtml;
	}	
	/******************************/
	/* /генерация эелемента формы */
	/******************************/
	
	
	/*************************/
	/* валидация полей формы */
	/*************************/
	function formValidData($type, $string)
	{
		switch($type)
		{
			case 'TEXT' : 
				//if(!preg_match('(\w+)/u', $string))
				//if(!preg_match('/[^\pL]/u', $string))
				if(!mb_ereg_match('(\w+)', $string))
					return false;
				else 
					return true;
				break;
			
			case 'EMAIL' : 
				$pattern = '(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})';
				if(!mb_ereg_match($pattern, $string))
					return false;
				else 
					return true;
				break;
				
			case 'PHONE' : 
				$pattern = '(^[\+]?[0-9\s-\(\)]+$)';
				if(!mb_ereg_match($pattern, $string))
					return false;
				else 
					return true;
				break;
				
			case 'INT' : 
				$pattern = '(^\d+$)';
				if(!mb_ereg_match($pattern, $string))
					return false;
				else 
					return true;
				break;
				
			default : return true;
		}
	}
	/**************************/
	/* /валидация полей формы */
	/**************************/
}
	


/************************/
/* класс создания формы */
/************************/
class Form
{
	var $formTpl 	= '';		// путь к файлу-шаблону формы 
		
	var $formAction 	= '';		// далее атрибуты формы
	var $formName 	= '';		//
	var $formClass 	= '';		//
	var $formId 		= '';		//
	var $formMethod 	= 'post';	//
	var $formEnctype = 'multipart/form-data';	//
	
	var $formData;					// массив данных полей формы
	var $formDataOpen	= '{'; 		// символ открытия шаблона для подстановки
	var $formDataClose	= '}'; 		// символ закрытия шаблона для подстановки
	var $formPrefixT 	= 'TITLE_'; // префикс названия поля
	var $formPrefixI 	= 'HTML_';  // префикс html поля
	
	var $formError   = array();		// массив сообщений об ошибках генерации формы
	
	
	/*******************/
	/* генерация формы */
	/*******************/
	function formCreate()
	{
		$formHtml = "<form name='$this->formName' action='$this->formAction' class='$this->formClass' id='$this->formId' method='$this->formMethod' enctype='$this->formEnctype'>";
		
		$formHtml .= $this->formReadTpl();	
		
		$formHtml .= "</form>";
		
		$this->formPrintError();
		
		return $formHtml;
	}
	/********************/
	/* /генерация формы */
	/********************/
	
	
	
	
	/***************************/
	/* обработка шаблона формы */
	/***************************/
	function formReadTpl()
	{		
		$html = '';
	
		if($this->formTpl != '')
		{			
			if(file_exists($this->formTpl))
			{
				// прочитаем шаблон в массив
				$str = file($this->formTpl);
				
				// если массив не пустой
				if(is_array($this->formData) && count($this->formData) > 0)
				{
					// построчно ищем вхождения меток, выделяем имя метки
					foreach($str as $k)
					{						
						// шаблон меток						
						$regPattern = "(\{([$this->formPrefixT|$this->formPrefixI]+)([A-Za-z0-9_-]+)\})";	
							
						$arr = array();	
						
						// найдем в очередной строке вхождения меток 
						// и занесем их в массив				
						if(preg_match_all($regPattern, $k, $arr, PREG_SET_ORDER) > 0)
						{
							for($i = 0; $i < count($arr); $i++)
							{								
								if(isset($this->formData[$arr[$i][2]]))
								{
									$el = new FormElement();								
									
									foreach($this->formData[$arr[$i][2]] as $p => $v)
									{										
										$el -> $p = $v;
									}						
																		
									$regPattern = array("(\{$this->formPrefixT(".$arr[$i][2]."+)\})", "(\{$this->formPrefixI(".$arr[$i][2]."+)\})");
									
									$k = preg_replace(	$regPattern, 
														array($this->formData[$arr[$i][2]]['header'], $el -> createElement()), 
														$k, 
														1);								
								}
							}														
						}	
												
						$html .= $k;											
					}
				}
				else $this->formError[] = 'Нет массива данных формы или он пустой';								
			}
			else $this->formError[] = 'Файл шаблона формы не найден';
		}
		else $this->formError[] = 'Нет шаблона формы';
		
		return $html;
	}
	/****************************/
	/* /обработка шаблона формы */
	/****************************/
	
	
	
	
	
	
	
	
	/***********************************************/
	/* печать сообщений об ошибках генерации формы */
	/***********************************************/
	function formPrintError()
	{
		if(count($this->formError) > 0)
		{
			foreach($this->formError as $k)
			{
				echo '<p>'.$k.'</p>';
			}
		}
		//else echo 'Нет ошибок';
	}
	/************************************************/
	/* /печать сообщений об ошибках генерации формы */
	/************************************************/
	
	
	function printArray($arr)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
	}
}
?>