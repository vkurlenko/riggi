<?
// построим дерево родительских разделов
	function selectLevel($parent, $level)
	{		
		global $_MODULE_PARAM, $tableHtml, $_ICON, $jumpCount;		
		
		
		$sql = "SELECT * FROM `".$_MODULE_PARAM["tableName"]."`
				WHERE item_parent = ".$parent." 
				ORDER BY item_order ASC";
		${"res$level"} = mysql_query($sql);		
		
				
		while(${"row$level"} = mysql_fetch_array(${"res$level"}))
		{
			$space = $selected = "";		
			
			for($i = 0; $i < $level; $i++) $space .= "&nbsp;";
			
			$style = '';
			if(isset($_GET['id']) && ${"row$level"}["id"] == $_GET['id']) $style = "style='color:#090'";
			
			$tableHtml .= "
			<tr>
				<td align='center' width=100>
					<a href='?page=".$_MODULE_PARAM['name']."&id=".${"row$level"}["id"]."&move&dir=asc&jump=minus&rand=".rand()."#".${"row$level"}['id']."'>-".($jumpCount-1)."</a>
					<a href='?page=".$_MODULE_PARAM['name']."&id=".${"row$level"}["id"]."&move&dir=asc#".${"row$level"}['id']."'><img src='".$_ICON["down"]."' alt='down'></a>
					<a href='?page=".$_MODULE_PARAM['name']."&id=".${"row$level"}["id"]."&move&dir=desc#".${"row$level"}['id']."'><img src='".$_ICON["up"]."' alt='up'></a>
					<a href='?page=".$_MODULE_PARAM['name']."&id=".${"row$level"}["id"]."&move&dir=desc&jump=plus&rand=".rand()."#".${"row$level"}['id']."'>+".($jumpCount-1)."</a>
					
				</td>
				<td>";
				
				if($parent == 0) $tableHtml .= "<strong>"; 
				$tableHtml .= "<a name=".${"row$level"}['id']."></a><a href='?page=".$_MODULE_PARAM['name']."&editItem&id=".${"row$level"}['id']."' $style>".$space.$space.$space.${"row$level"}['item_name']."</a> ";
				if($parent == 0) $tableHtml .= "</strong>";
				
				$tableHtml .= "</td>				
				<td>";
				if(${"row$level"}["item_photo"] > 0) { $tableHtml .= "<img src='".$_ICON["image"]."' title='Есть картинка'>"; }
				$tableHtml .= "</td>
				<!--<td align=center><a href='/cms9/modules/gantil/catalog/salon.price.php?id=".${"row$level"}['id']."'><img src='".$_ICON["money"]."' title='Редактировать цены'></a></td>-->
				<td></td>
				<td align=center><a href='?page=".$_MODULE_PARAM['name']."&editItem&id=".${"row$level"}['id']."'><img src='".$_ICON["edit"]."' title='Редактировать'></a></td>
				<td align=center><a style='color:red' href=\"javascript:if (confirm('Удалить позицию и все дочерние позиции?')){document.location='?page=".$_MODULE_PARAM['name']."&delItem&id=".${"row$level"}['id']."'}\"><img src='".$_ICON["del"]."' title='Удалить'></a></td>
			</tr>
			";
			
			
			//$tableHtml .= "<tr><td>".$space.$space.${"row$level"}['item_name']."</td></tr>";			
			$tab = $level+1;
			selectLevel(${"row$level"}['id'], $tab);			
		}
	}
	
	
	
	


function GetItems($tableName, $orderBy = "", $orderDir = "")
{
	global $_MODULE_PARAM, $_TEXT, $_ICON,  $tableHtml;
		?>	
		<table border=0 cellpadding=5 class="list">
			<tr>		
				<th></th>				
				<th><?=$_TEXT['TEXT_HEAD']?></th>				
				<th></th>
				<th>цены</th>
				<th>edit</th>
				<th>del</th>
			</tr>
		<?
		$tableHtml = "";
		selectLevel(0, 0);	
		echo $tableHtml;		
		?>
		</table>
		<?
}

function readItem($id)
{
	global $_MODULE_PARAM;
	
	$sql = "SELECT * FROM `".$_MODULE_PARAM['tableName']."`
			WHERE id = ".$id;
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row;
}
?>