<?
/*~~~~~~~~~~~~~~~~~~~~~~~~~~*/
/*~~~ вставка инфоблоков ~~~*/
/*~~~~~~~~~~~~~~~~~~~~~~~~~~*/

$sql = "select * from `".$_VARS['tbl_prefix']."_iblocks` where 1";
$res = mysql_query($sql);
while($row = mysql_fetch_array($res))
{
	if($row['block_show'] == '1') 
	{		
		if(trim($row['block_tpl']) != "")
		{
			/*~~~ читаем код шаблона инфоблока ~~~*/
			$sql = "select * from `".$_VARS['tbl_template_name']."` where tpl_marker = '".$row['block_tpl']."'";
			$res4 = mysql_query($sql);
			$row4 = mysql_fetch_array($res4);
			
			$dir = chdir($_SERVER['DOC_ROOT']."/".$_VARS['tpl_dir']);
			$code = file($row4['tpl_file']);
			$tpl_code = "";
			foreach($code as $str)
			{
				$tpl_code .= $str; 
			}
			$code = $tpl_code;				
			/*~~~ /читаем код шаблона инфоблока ~~~*/
			
			$iblock_code = str_replace(":::iblock_text:::", $row['block_text'], $code);	
			$output = str_replace(":::".$row['block_marker'].":::", $iblock_code, $output);	
		}
		else
		{
			$output = str_replace(":::".$row['block_marker'].":::", InsertBlockBD($row['block_marker']), $output);	
		}
	}
	else $output = str_replace(":::".$row['block_marker'].":::", "", $output);	
}
?>