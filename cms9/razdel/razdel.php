<?
###############################################
# функции
###############################################
error_reporting(E_ALL);


/****************************/
/*  создание новой страницы */
/****************************/
function AddRazdel(
		$p_title, 
		$p_title_eng, 
		$p_url, 
		$p_redirect,
		$p_content, 
		$p_content_eng, 		
		$p_parent_id, 		
		$p_show, 
		$p_meta_title, 
		$p_meta_title_eng, 
		$p_meta_kwd, 
		$p_meta_kwd_eng, 
		$p_meta_dscr, 
		$p_meta_dscr_eng,
		$p_add_text_1,
		$p_add_text_1_eng,
		$p_tags,
		$p_tpl,
		$p_main_menu,
		$p_site_map,
		$p_video,
		$p_photo_alb,
		$p_photo_alb_2,
		$p_img,
		$p_add_text_2,
		$p_protect)
		{

	global $razdel_table;
	
	// преобразуем список тегов в строку
	$arr = explode(",", $p_tags);
	$tags = '';
	for($i = 0; $i < count($arr); $i++)
	{
		//$tags .= str_replace(" ", "&nbsp;", trim($arr[$i]));
		$tags .= trim($arr[$i]);
		if($i < (count($arr) - 1)) $tags .= ",";
	}
	$p_tags = $tags;
	// /преобразуем список тегов в строку
	
	
	$sql = "INSERT INTO `$razdel_table`(
	 p_title,
	 p_title_eng,
	 p_content,
	 p_content_eng,	 
	 p_parent_id,	 
	 p_url,	 
	 p_redirect,
	 p_show,	 
	 p_meta_title,
	 p_meta_title_eng,
	 p_meta_kwd,
	 p_meta_kwd_eng,
	 p_meta_dscr,
	 p_meta_dscr_eng,
	 p_add_text_1,
	 p_add_text_1_eng,
	 p_tags,
	 p_tpl,
	 p_main_menu,
	 p_site_map,
	 p_video,
	 p_photo_alb,
	 p_photo_alb_2,
	 p_img,
	 p_add_text_2,
	 p_protect) 
	 
	VALUES(
		'$p_title',
		'$p_title_eng',
		'$p_content',
		'".@$_POST['p_content_eng']."',	 
		$p_parent_id,	 
		'$p_url',	
		'$p_redirect', 
		'$p_show',
		'$p_meta_title',
		'$p_meta_title_eng',
		'$p_meta_kwd',
		'$p_meta_kwd_eng',
		'$p_meta_dscr',
		'$p_meta_dscr_eng',
		'$p_add_text_1',
		'$p_add_text_1_eng' ,
		'$p_tags' ,
		'$p_tpl' ,
		'$p_main_menu',
		'$p_site_map',
		'$p_video',
		'$p_photo_alb',
		'$p_photo_alb_2',
		'$p_img',
		'$p_add_text_2',
		'$p_protect')";
	
	sql($sql);
	
	$id = mysql_insert_id();
	//echo $id;
	
	if(trim($p_url) == '') $p_url = $id;
	
	$sql = "UPDATE `$razdel_table` 
			SET p_order=$id 
			WHERE id=$id";
	sql($sql);
}
/****************************/
/* /создание новой страницы */
/****************************/



/*****************************************/
/* сохранение отредактированной страницы */
/*****************************************/
function UpdRazdel(
		$id, 
		$p_title, 
		$p_title_eng, 
		$p_url,
		$p_redirect,
		$p_content, 
		$p_content_eng, 		
		$p_show, 
		$p_meta_title, 
		$p_meta_title_eng, 
		$p_meta_kwd, 
		$p_meta_kwd_eng, 
		$p_meta_dscr, 
		$p_meta_dscr_eng,
		$p_add_text_1,
		$p_add_text_1_eng,
		$p_tags,
		$p_tags_new,
		$p_tpl,
		$p_main_menu,
		$p_site_map,
		$p_video,
		$p_photo_alb,
		$p_photo_alb_2,
		$p_img,
		$p_add_text_2,
		$p_protect)
	{
	
	global $razdel_table, $_VARS;
	
	if(trim($p_tags_new) == "")
	{
		$arr = explode(",", $p_tags);
		$tags = '';
		for($i = 0; $i < count($arr); $i++)
		{
			$tags .= trim($arr[$i]);
			if($i < (count($arr) - 1)) $tags .= ",";
		}		
	}
	else 
		$tags = trim($p_tags_new);
		
	$p_tags = $tags;
	

	$sql = "UPDATE `$razdel_table` 
			SET 
				p_title			= '$p_title',
				p_title_eng		= '$p_title_eng',
				p_url 			= '$p_url',
				p_redirect 		= '$p_redirect',
				p_content		= '$p_content',
				p_content_eng	= '$p_content_eng',		
				p_show			= '$p_show',
				p_meta_title	= '$p_meta_title',
				p_meta_title_eng	= '$p_meta_title_eng',
				p_meta_kwd		= '$p_meta_kwd',
				p_meta_kwd_eng	= '$p_meta_kwd_eng',
				p_meta_dscr		= '$p_meta_dscr',
				p_meta_dscr_eng	= '$p_meta_dscr_eng',
				p_add_text_1	= '$p_add_text_1',
				p_add_text_1_eng	= '$p_add_text_1_eng',
				p_tags			= '$p_tags',
				p_tpl			= '$p_tpl',
				p_main_menu		= '$p_main_menu',
				p_site_map		= '$p_site_map',
				p_video			= '$p_video',
				p_photo_alb 	= '$p_photo_alb',
				p_photo_alb_2 	= '$p_photo_alb_2',
				p_img 			= '$p_img',
				p_add_text_2	= '$p_add_text_2',
				p_protect 		= '$p_protect'
		
			WHERE id=$id";
	
	sql($sql);
}
/******************************************/
/* /сохранение отредактированной страницы */
/******************************************/



/*********************************/
/* изменение сортировки страницы */
/*********************************/
function MoveRazdelUp($id)
{
	global $razdel_table;

	$res = sql("SELECT p_order, p_parent_id from $razdel_table where id=$id");
	$order = mysql_result($res, 0, 'p_order');
	$p_parent_id = mysql_result($res, 0, 'p_parent_id');

	$res = sql("select p_order from $razdel_table where p_parent_id=$p_parent_id and p_order<$order order by p_order desc limit 1");
	if ($res and mysql_num_rows($res)) 
	{
		$order2 =  mysql_result($res, 0);
		sql("update $razdel_table set p_order=$order where p_order=$order2");
		sql("update $razdel_table set p_order=$order2 where id=$id");
	}
}


function MoveRazdelDown($id)
{
	global $razdel_table;

	$res = sql("SELECT p_order,p_parent_id from $razdel_table where id=$id");
	$order = mysql_result($res, 0, 'p_order');
	$p_parent_id = mysql_result($res, 0, 'p_parent_id');

	$res = sql("select p_order from $razdel_table where p_parent_id=$p_parent_id and p_order>$order order by p_order asc limit 1");
	if ($res and mysql_num_rows($res)) 
	{
		$order2 =  mysql_result($res, 0);
		sql("update $razdel_table set p_order=$order where p_order=$order2");
		sql("update $razdel_table set p_order=$order2 where id=$id");
	}
}

/**********************************/
/* /изменение сортировки страницы */
/**********************************/


#--------вывод групп
function GetRazdel($action, $n = 0){
	global $razdel_table;

	if($action == "podr")		$query = "select * from `$razdel_table` where id=$n";
	if($action == "all")		$query = "select * from `$razdel_table` order by p_order asc";
	if($action == "parent")		$query = "select * from `$razdel_table` where p_parent_id=$n order by p_order asc";
	//echo $query;
	return ( SqlParseRes($query) );
}

#--------вывод групп
function GetRazdelMaillist($action, $n = 0)
{
	global $razdel_table;

	if($action == "podr")		
		$query = "select * from `$razdel_table` where id=$n";
		
	if($action == "all")		
		$query = "select * from `$razdel_table` where contact_email<>'' order by p_order asc";
		
	if($action == "parent")		
		$query = "select * from `$razdel_table` where p_parent_id=$n order by p_order asc";
	
	return ( SqlParseRes($query) );
}



#--------удаление раздела
function DelRazdel($id)
{
	global $razdel_table;

	$razdels = GetRazdel('parent', $id);
	if ($razdels)
	{
		foreach ($razdels as $key => $razdel)
		{
			DelRazdel($razdel['id']);
		}
	}

	sql ("delete from `$razdel_table` where id=$id");
}


#--------вывод групп
function GetRazdelInfo($n = 0)
{
	global $razdel_table;

	$res = sql("select text from `$razdel_table` where id=$n");
	if($res and mysql_numrows($res)) 
		$cont = mysql_result($res, 0, 'p_content');
		
	return (($cont));
}

#--------вывод групп
function EchoRazdelInfo($n = 0)
{
	global $razdel_table;

	$res = sql("select p_content from `$razdel_table` where id=$n");
	if($res and mysql_numrows($res)) 
		$cont = mysql_result($res, 0, 'p_content');
		
	echo(($cont));
}


#--------вывод групп
function GetPath($razdel_id, $p = '')
{
	$level = 0;
	
	if($razdel_id)
	{
		$par = $razdel_id;
		
		while($par) 
		{
			$p_razdel = GetRazdel('podr', $par);
		
			$path[$level]['p_title'] = $p_razdel[0]['p_title'];
			$path_ids[$level] = $p_razdel[0]['id'];
			
			if($p_razdel[0]['url'] AND $p_razdel[0]['url'] != ".") 
				$path[$level]['url'] = $p_razdel[0]['url'];
			else 
			{
				if($p) 
					$path[$level]['url'] = $p;
				else 
					$path[$level]['url'] = "?{$p_razdel[0]['id']}";
			}
			
			$par = $p_razdel[0]['p_parent_id'];
			if (!$par) $main_parent = $p_razdel[0]['id'];
			$level++;
		}
	}
	if(isset($path))		
		$res['path'] = array_reverse($path);
		
	if(isset($path_ids))	
		$res['path_ids'] = array_reverse($path_ids);
		
	if(isset($main_parent))	
		$res['main_parent'] = $main_parent;
		
	if(!isset($res)) 
		$res="";
		
	return ($res);
}


#--------вывод групп
function GetPodrazdel($main_parent, $p = '')
{
	$razdel = GetRazdel('podr', $main_parent);
	$parent_url = $razdel[0]['url'];
	
	if($podrazdel = GetRazdel('parent', $main_parent))
	{
		for ($i = 0; $i < count($podrazdel); $i++) 
		{
			if($podrazdel[$i]['url']) 
				$podrazdel[$i]['url'] = $podrazdel[$i]['url'];
			else
			{
				if($p) 
					$podrazdel[$i]['url'] = $p . "?{$podrazdel[$i]['id']}";
				else
				{
					if (!$podrazdel[$i]['url']) 
						$podrazdel[$i]['url'] = "$parent_url?{$podrazdel[$i]['id']}";
				}
			}
		}
	}
	return ($podrazdel);
}


function GetRazdelMap($ids)
{
	if(is_Array($ids) and count($ids)) 
	{
		$res = '';
		for($i = 0; $i < count($ids); $i++) 
		{
			$id = $ids[$i];

			$razdel = GetRazdel('podr', $id);
			if ($razdel)
			{
				$res .= "<li><a href='{$razdel[0]['url']}'>{$razdel[0]['p_title']}</a>";
				$parent_url = $razdel[0]['url'];

				$razdels = GetRazdel('parent', $id);
				if ($razdels)
				{
					$res .= "<ul>";
					foreach ($razdels as $key => $razdel)
					{
						if (!$razdel['url']) 
							$razdel['url'] = $parent_url . '?' . $razdel['id'];
							
						$res .= "<li><a href='{$razdel['url']}'>{$razdel['p_title']}</a></li>";
					}
					$res .=  "</ul>";
				}
				$res .=  "</li>";
			}
		}
		return $res;
	}
	else return 0;
}

function save_subscribe_file($tpl)
{
	if($tpl == "tpl_subscribe")
	{		
		$f = fopen($_SERVER['DOC_ROOT']."/subscribe.htm", "w+");
		
		$output = file("http://".$_SERVER['HTTP_HOST']."/subscribe_page/");
		foreach($output as $k)
		{
			$str = fputs($f, $k);
		}		
	}
}
?>