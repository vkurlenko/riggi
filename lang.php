<?

$arrLang = array(

	'' => array(
		0 => 'Посмотреть магазины на Google.карте',
		1 => 'Всего магазинов:',
		2 => 'Увеличить карту города',
		3 => 'Перейти в магазин',
		
		4 => 'Подробнее',
		5 => 'Архив новостей',
		6 => 'Новостная лента',
		
		7 => 'Карта сайта',
		
		8 => 'Поиск',
		9 => 'страниц',
		10 => 'Поиск завершен, ни одного совпадения не найдено',
		11 => '',
		12 => 'Найдено по словосочетанию',
		13 => 'а',
		14 => 'ы',
		
		15 => 'Riggi офис и шоурум',
		
		16 => 'поиск',
		
		17 => 'Имя',
		18 => 'Ваш email',
		19 => 'Защитный код',
		20 => 'Отправить',
		21 => 'Подписка',
		22 => 'Дорогие друзья, чтобы отправить нам письмо воспользуйтесь формой обратной связи. Необходимо заполнить все поля.',
		23 => 'Ваше имя',
		24 => 'другой защитный код',
		25 => 'Сообщение',
		26 => 'Дорогие друзья, чтобы оформить подписку на новости воспользуйтесь формой. Необходимо заполнить все поля.',
		
		27 => 'Карта сайта',
		28 => 'Подписка',
		29 => 'Контакты',
		
		//30 => 'Информация для потенциальных партнеров',
		
		31 => 'Ошибка проверки защитного кода',
		32 => 'Не заполнено поле',
		33 => 'Поле',
		34 => 'должно содержать только текст',
		35 => 'должно иметь формат +7 123 4567890',
		36 => 'должно иметь формат name@server.domain',
		37 => 'Указанный адрес уже используется.',
		38 => 'Ошибка создания учетной записи.',
		39 => 'Письмо с контрольной строкой отправлено на указанный email',
		40 => 'Для завершения процедуры подписки перейдите по ссылке, указанной в письме.',
		41 => 'Ошибка отправки письма с контрольной строкой.',
		42 => 'Для подтверждения подписки перейдите по ссылке',
		43 => 'Подписка на сайте',
		44 => 'Администратор'
		
	),
	
	'_eng' => array(
		0 => 'Show shops on Google.map',
		1 => 'Vsego magazinov',
		2 => 'Zoom map',
		3 => 'Go to shop',
		
		4 => 'More',
		5 => 'Archive',
		6 => 'News tape',
		
		7 => 'Site map',
		
		8 => 'Search',
		9 => 'pages',
		10 => 'eng Поиск завершен, ни одного совпадения не найдено',
		11 => '',
		12 => 'eng Найдено по словосочетанию',
		13 => ' ', 
		14 => ' ',
		
		15 => 'Riggi office and showroom',
		
		16 => 'search',
		
		17 => 'Name',
		18 => 'Your e-mail',
		19 => 'Protected code',
		20 => 'Send',
		21 => 'Subscribe',
		22 => 'Dear friends, чтобы отправить нам письмо воспользуйтесь формой обратной связи. Необходимо заполнить все поля.',
		23 => 'Your name',
		24 => 'other code',
		25 => 'Message',
		26 => 'Dear friends, чтобы оформить подписку на новости воспользуйтесь формой. Необходимо заполнить все поля.',
		
		27 => 'Sitemap',
		28 => 'Subscribe',
		29 => 'Contacts',
		
		//30 => 'Information for partners',
		
		31 => 'Protected code check error',
		32 => 'Empty field',
		33 => 'Field',
		34 => 'must have text only',
		35 => 'must have format +7 123 4567890',
		36 => 'must have format name@server.domain',
		37 => 'This email already used.',				
		38 => 'Create account error.',		
		39 => 'Письмо с контрольной строкой отправлено на указанный email',
		40 => '',
		41 => '',
		42 => '',
		43 => '',
		44 => 'Administrator'		
	)
);

function TEXT_LANG($index)
{
	global $arrLang, $lang;
	
	if(!isset($arrLang[$lang][$index]) || $arrLang[$lang][$index] == '')
	{
		if(isset($arrLang[''][$index]))
			return $arrLang[''][$index];
		else
			return '';
	}
	else
		return $arrLang[$lang][$index];	
	
}

?>