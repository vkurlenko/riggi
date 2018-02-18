<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ класс отладочных сообщений ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*$msg_type = "all";

class Msg_Debug
{
	var $msg_type_show = "all";

	function msg_print($msg_type, $msg_text)
	{
		switch($this -> msg_type_show)
		{
			case "all" : 
		};
	}
}*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /класс отладочных сообщений ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ класс таблицы MySQL ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
class DB_Table
{
	var $tableName;			// имя таблицы	(string)
	var $tableFields;		// поля таблицы (array)
	var $tableData; 			// данные (array)
	var $tableWhere; 		// данные условия where (array)
	var $tableOrderField; 	// поле, служащее для сортировки данных таблицы
	var $tableFieldJump; 	// 
	var $tableJumpCount; 	// быстрый сдвиг на Х позиций
	var $debugMode = true; 	// флаг вывода отладочных сообщений
	var $createTestRecord = false;
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~*/	
	/*~~~ создание таблицы ~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~*/		
	function create()
	{
		$strFields = "";
		$i = 0;
		
		foreach($this -> tableFields as $k => $v)
		{
			$i++;
			$strFields .= $k." ".$v;
			if($i < count($this -> tableFields)) $strFields .= ", ";
		}
		$sql = "CREATE TABLE `".$this -> tableName."` (".$strFields.")";
		$res = mysql_query($sql);	
		
		//$this -> debugMessage($sql, $res);
		
		// создаем тестовую запись с id=1 
		if($this -> createTestRecord) 
		{
			$sql = "SELECT * FROM `".$this -> tableName."` 
					WHERE id = 1";
			$res = mysql_query($sql);
			if(mysql_num_rows($res) == 0)
			{
				$this -> tableData = array();
				$this -> addItem();			
			}
		}
	}
	
	/*~~~ /создание таблицы ~~~*/
	
	
	
	
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/*~~~ добавление записи в таблицу ~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	
	function addItem()
	{
		$strFields = $strData = "";
		$i = 0;
		$numFields = count($this -> tableData);
		
		foreach($this -> tableData as $k => $v)
		{				
			$strFields .= $k;
		
			if($this->tableFields[$k] == "text" || strpos($this->tableFields[$k], "date") !== false || strpos($this->tableFields[$k], "time") !== false || strpos($this->tableFields[$k], "enum") !== false)
			{
				$v = "'".trim($v)."'";
			}				
			$strData .= $v;
			
			$i++;
			
			if($i < $numFields) 
			{
				$strFields .= ", ";
				$strData .= ", ";
			}					
		}			
		
		$sql = "insert into `".$this -> tableName."` (".$strFields.") values(".$strData.")";
		$res = mysql_query($sql);
		$this -> debugMessage($sql, $res);		
	}	
	
	/*~~~ /добавление записи в таблицу ~~~*/
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/		
	/*~~~ удаление записи из таблицы ~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/			
	
	function delItem()
	{

		$i = 0;
		$str = "";
		foreach($this->tableWhere as $k => $v)
		{
			if($this->tableFields[$k] == "text" || strpos($this->tableFields[$k], "enum") !== false)
			{
				$v = "'".trim($v)."'";
			}				
			$str .= $k." = ".$v;
			
			$i++;
			//if($i < count($this->tableWhere)) $str .= " and ";			
			if($i < count($this->tableWhere)) $str .= " OR ";			
		}
		
		$sql = "DELETE FROM `".$this->tableName."` 
				WHERE ".$str;		
		$res = mysql_query($sql);
		$this -> debugMessage($sql, $res);		
				
	}
	
	/*~~~ /удаление записи из таблицы ~~~*/	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~*/			
	/*~~~ изменение записи ~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~*/				
	
	function updateItem()
	{
		$strData = "";
		$i = 0;
		$numFields = count($this -> tableData);
		
		// формирование строки обновления данных
		
		foreach($this -> tableData as $k => $v)
		{
			if($this->tableFields[$k] == "text" || strpos($this->tableFields[$k], "date") !== false || strpos($this->tableFields[$k], "time") !== false || strpos($this->tableFields[$k], "enum") !== false)
			{
				$v = "'".trim($v)."'";
			}				
			$strData .= $k." = ".$v;
			
			$i++;
			
			if($i < $numFields) 
			{				
				$strData .= ", ";
			}					
		}	
		
		// формирование строки условия
		
		$i = 0;
		$where = "";
		foreach($this->tableWhere as $k => $v)
		{
			if($this->tableFields[$k] == "text" || strpos($this->tableFields[$k], "enum") !== false)
			{
				$v = "'".trim($v)."'";
			}				
			$where .= $k." = ".$v;
			
			$i++;
			if($i < count($this->tableWhere)) $where .= " and ";			
		}		
		
		$sql = "update `".$this->tableName."` set ".$strData." where ".$where;
		$res = mysql_query($sql);		
		
		$this -> debugMessage($sql, $res);
	}	
	
	
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* изменение порядка следования записей */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	function reOrderItem()
	{
		if($this -> tableWhere['dir'] == "asc") $arrow = ">";
		elseif($this -> tableWhere['dir'] == "desc") $arrow = "<";
		
		$sql = "SELECT * FROM `".$this->tableName."` 
				WHERE id=".$this -> tableWhere['id'];
		$res = mysql_query($sql);
		
		$this -> debugMessage($sql, $res);
		
		$row = mysql_fetch_array($res);
		
		$sql = "SELECT * FROM `".$this->tableName."` 
				WHERE (".$this->tableOrderField." ".$arrow." ".$row[$this->tableOrderField].") 
				ORDER BY ".$this->tableOrderField." ".$this -> tableWhere['dir']." 
				LIMIT 1";
		$res = mysql_query($sql);
		
		
		$this -> debugMessage($sql, $res);
		
		$row_2 = mysql_fetch_array($res);
		
		$sql = "UPDATE `".$this -> tableName."` 
				SET ".$this -> tableOrderField."=".$row_2[$this->tableOrderField]." 
				WHERE id=".$this -> tableWhere['id'];
		$res = mysql_query($sql);
		
		$this -> debugMessage($sql, $res);
				
		$sql = "UPDATE `".$this->tableName."` 
				SET ".$this->tableOrderField."=".$row[$this->tableOrderField]." 
				WHERE id=".$row_2['id'];
		$res = mysql_query($sql);
		
		$this -> debugMessage($sql, $res);
	}
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* изменение порядка следования записей (+-10) */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	function reOrderItemJump ()
	{
		// определим знак
		if($this -> tableWhere['dir'] == "asc") $arrow = ">";
		elseif($this -> tableWhere['dir'] == "desc") $arrow = "<";
		
		// причитаем выбранную запись
		$sql = "SELECT * FROM `".$this->tableName."` 
				WHERE id=".$this -> tableWhere['id'];
		$res = mysql_query($sql);
		
		$this -> debugMessage($sql, $res);
		
		$row = mysql_fetch_array($res);
		
		// шаг прыжка
		$limit = $this->tableJumpCount;
		
		if($this -> tableWhere['dir'] == 'desc') $limit = $limit - 1;
		
		// найдем запись - цель
		$sql = "SELECT * FROM `".$this->tableName."` 
				WHERE (".$this->tableOrderField." ".$arrow." ".$row[$this->tableOrderField]."
				AND item_parent = ".$row['item_parent'].") 
				ORDER BY ".$this->tableOrderField." ".$this -> tableWhere['dir']." 
				LIMIT ".$limit;
		$res = mysql_query($sql);
				
		$this -> debugMessage($sql, $res);
		
		$i = 0;
		
		if(mysql_num_rows($res) > 0)
		{			
			while($row_2 = mysql_fetch_array($res))
			{
				// нас интересует только последняя запись в выборке
				$order = $row_2[$this->tableOrderField];
				$orderId = $row_2['id'];
			}
			
			// все остальные записи, где поле order > order цели увеличим на 1		
			$sql = "UPDATE `".$this -> tableName."` 
					SET ".$this -> tableOrderField."=".$this -> tableOrderField." + 1 
					WHERE ".$this -> tableOrderField." > ".$order." 
					OR ".$this -> tableOrderField." = ".$order;
			$res_p = mysql_query($sql);
			
			
			$this -> debugMessage($sql, $res);
			
			// значение order выбранной (перемещаемой) записи установим в значение записи-цели
			$sql = "UPDATE `".$this -> tableName."` 
					SET ".$this -> tableOrderField."=".$order." 
					WHERE id=".$this -> tableWhere['id'];
			$res = mysql_query($sql);
			
			$this -> debugMessage($sql, $res);		
		}
	}
	
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/* вывод отладочных сообщений */
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/	
	function debugMessage($msgString, $result)
	{
		if($this -> debugMode == true)
		{
			if($result)
			{
				echo "<span class='msgOk'>";
			}
			else echo "<span class='msgError'>";
			
			echo "<strong>SQL</strong> = ".$msgString."<br><br><strong>RES</strong> = ".$result;	
			echo "</span>";
		}
	}

}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ /класс таблицы MySQL ~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~~*/




function delArrayElem($arr, $arrElem)
{
	foreach($arr as $k => $v)
	{
		foreach($arrElem as $s)
		{
			if($k == $s) unset($arr[$k]);
		}
	}
	
	return $arr;
}


?>