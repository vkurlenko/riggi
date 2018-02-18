<?
error_reporting(E_ALL);

/*~~~ создаем новую таблицу ~~~*/
function CreateTable()
{
	global $table_name, $arrTblFields, $showDebug;
	$sql = "create table `$table_name` (";
	$i = 0;
	foreach($arrTblFields as $k => $v)
	{
		$i++;
		$sql .= $k." ".$v[0];
		if($i == count($arrTblFields)) 
		{
			$sql .= ")";
		}
		else
		{
			$sql .= ", ";
		}
	}
			
	//echo $sql;
	$res = mysql_query($sql);
	//msgStack("<pre>".$sql."</pre>", $res);
	
}
/*~~~ /создаем новую таблицу ~~~*/


/*~~~ добавляем запись в таблицу (передается весь массив _POST) ~~~*/
function AddItem($post)
{
	global $table_name, $arrTblFields;
	
	$sql = "select * from `$table_name` where userLogin='".$post['userLogin']."'";
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0)
	{
		msgStack("Такой пользователь '".$post['userLogin']."' уже существует", false);
	}
	else
	{
		$fields = "";
		$values = "";
		
		$i = 0;
		unset($post['set_item']); // удаляем ненужные элементы
		foreach($post as $k => $v)
		{
			$i++;
			$fields .= $k;
			$values .= "'".$v."'";
			if($i == count($post))
			{
				$fields .= "";
				$values .= "";
			}
			else
			{
				$fields .= ", ";
				$values .= ", ";
			}	
		}
		
		$sql = "insert into `$table_name` ($fields) values($values)";
		
		$res = mysql_query($sql);
		
		msgStack($sql, $res);		
		
		
		$userDate = $userLastVisit = date("Y")."-".date("m")."-".date("d")." ".date("H").":".date("i").":".date("s");
		$sql = "update `$table_name` set userDate = '$userDate', userLastVisit = '$userLastVisit' where id=".mysql_insert_id();
		$res = mysql_query($sql);
		
		msgStack($sql, $res);
		
	}
	
	unset($_GET['add_item']);
	return $res;
	
}
/*~~~ /добавляем запись в таблицу (передается весь массив _POST) ~~~*/

/*~~~ редактирование записи ~~~*/
function UpdateItem($post)
{
	global $table_name, $arrTblFields;
	
	unset($post['update_item']); // удаляем ненужные элементы
	
	if(!isset($post['userBlock'])) $post['userBlock'] = 0;
	else $post['userBlock'] = 1;

	$i = 0;
	$sql = "update `$table_name` set ";
	foreach($post as $k => $v) 
	{
		$i++;
		if($k == "id") continue;
		$sql .= $k." = '".$v."'";
		if($i == count($post))
		{
			$sql .= "";
		}
		else
		{
			$sql .= ", ";
		}	
	}
	$sql .= " where id=".$post['id'];
	$res = mysql_query($sql);
	
	/*if($post['userBlock'] == 0)
	{
		$text = "Ваша учетная запись на сайте http://sweetstar.ru активирована\n";
		$text .= "Ваш логин:".$post['regLogin']."\n";
		$text .= "Ваш пароль: ".$post['regPwd']."\n";
		
	}
	else
	{
		$text = "Ваша учетная запись на сайте http://sweetstar.ru заблокирована\n";
	}
	$s = sendSmtp($post['regMail'], 'Информация о регистрации на сайте sweetstar.ru',  $text);*/
	
	//msgStack($sql, $res);
	return $res;
}
/*~~~ /редактирование записи ~~~*/


function DelItem($id)
{
	global $table_name;
	$sql = "delete from `$table_name` where id=$id";
	$res = mysql_query($sql);
	return $res;
}
?>
