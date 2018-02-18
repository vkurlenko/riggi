<?
session_start();
error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT']."/config.php";
require_once "razdel.php";
require_once $_SERVER['DOC_ROOT']."/db.php";
include $_SERVER['DOC_ROOT']."/functions_sql.php";
include $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/framework/class.db.php";
include_once $_SERVER['DOC_ROOT']."/".$_VARS['cms_dir']."/".$_VARS['cms_modules']."/common/auth/auth.checkuser.php";

check_access(array("admin", "editor"));

$razdel_table = $tableName = $_VARS['tbl_pages_name'];

$arrTableFields = array(
	"id"				=> "int auto_increment primary key",
	"p_title"			=> "text",
	"p_title_eng"		=> "text",	
	"p_url"				=> "text",
	"p_redirect"		=> "text",
	"p_content"			=> "text",
	"p_content_eng"		=> "text",
	"p_add_text_1"		=> "text",
	"p_add_text_1_eng"	=> "text",
	"p_add_text_2"		=> "text",
	"p_add_text_2_eng"	=> "text",
	"p_parent_id"		=> "int",
	"p_nosearch"		=> "enum('0','1') not null",	
	"p_order"			=> "int",
	"p_tags"			=> "text",
	"p_show"			=> "enum('1','0') not null",
	"p_meta_title"		=> "text",
	"p_meta_title_eng"	=> "text",	
	"p_meta_kwd"		=> "text",
	"p_meta_kwd_eng"		=> "text",
	"p_meta_dscr"		=> "text",
	"p_meta_dscr_eng"	=> "text",
	"p_tpl"				=> "text",
	"p_main_menu"		=> "enum('0','1') not null",	
	"p_site_map"		=> "enum('1','0') not null",
	"p_video"			=> "text",
	"p_photo_alb"		=> "int",
	"p_photo_alb_2"		=> "int",
	"p_img"				=> "int",
	"p_protect"			=> "enum('0','1') not null"	
);

$db_Table = new DB_Table();
$db_Table -> tableName = $tableName;
$db_Table -> tableFields = $arrTableFields;
$db_Table -> create();

if(!isset($parent)) 
	$parent = 0;

if(isset($name) AND isset($whattodo) AND ($whattodo=="spisok_add")) 
{
	$p_content = add_magic_quotes_gpc($name);
	AddSpisok($name, $parent, $adres);
}

if (isset($addPage))
{
	$p_content 		= add_magic_quotes_gpc($p_content);
	/*$p_add_text_1 	= strip_tags($p_add_text_1, '<ul><li><a><br>');	
	$p_add_text_1 	= str_replace('&nbsp;', '', $p_add_text_1);*/
	$p_url			= trim($p_url);
	
	if($p_url=="" OR  mysql_num_rows(mysql_query("select `id` from `".$_VARS['tbl_pages_name']."` where p_url='$p_url' limit 0,1"))>0) 
	{	
	}
	
	AddRazdel(
	$p_title, 
	@$p_title_eng, 
	$p_url, 
	$p_redirect,
	$p_content, 
	@$p_content_eng,	
	$parent, 	
	$p_show, 
	$p_meta_title, 
	@$p_meta_title_eng, 
	$p_meta_kwd, 
	@$p_meta_kwd_eng, 
	$p_meta_dscr, 
	@$p_meta_dscr_eng, 
	@$p_add_text_1,
	@$p_add_text_1_eng,
	$p_tags,
	$p_tpl,
	$p_main_menu,
	$p_site_map,	
	@$p_video,
	@$p_photo_alb,
	$p_photo_alb_2,
	$p_img,
	@$p_add_text_2,
	$p_protect);	
}

if (isset($name) AND isset($whattodo) AND ($whattodo=="spisok_upd")) 
{
	$name = add_magic_quotes_gpc($name);
	UpdSpisok($id, $name, $adres);
}

if(isset($upd_id))
{
	$p_content = add_magic_quotes_gpc($p_content);
	/*$p_add_text_1 = strip_tags($p_add_text_1, '<ul><li><a>');
	$p_add_text_1 = str_replace('&nbsp;', '', $p_add_text_1);*/

	UpdRazdel(
		$upd_id, 	
		$p_title, 
		@$p_title_eng, 
		$p_url,
		$p_redirect,
		$p_content, 
		@$p_content_eng, 	
		$p_show, 
		$p_meta_title, 
		@$p_meta_title_eng, 
		$p_meta_kwd, 
		@$p_meta_kwd_eng, 
		$p_meta_dscr, 
		@$p_meta_dscr_eng,
		@$p_add_text_1,
		@$p_add_text_1_eng,
		$p_tags,
		@$p_tags_new,
		$p_tpl,
		$p_main_menu,
		$p_site_map,	
		@$p_video,
		@$p_photo_alb,
		$p_photo_alb_2,
		$p_img,
		@$p_add_text_2,
		@$p_protect);
		
		save_subscribe_file($p_tpl);
}

if (isset($spisok_del_id)){
	DelSpisok($spisok_del_id);
}

if (isset($del_id)){
	DelRazdel($del_id);
}

if (isset($move_down)){
	MoveRazdelDown($move_down);
	header ("Location: /".$_VARS['cms_dir']."/razdel/index.php?parent=$parent&rand=" . time());
	exit;
}

if (isset($move_up)){
	MoveRazdelUp($move_up);
	header ("Location: /".$_VARS['cms_dir']."/razdel/index.php?parent=$parent&rand=" . time());
	exit;
}


if (isset($move_spisok_down)){
	MoveSpisokDown($move_spisok_down);
	header ("Location: /".$_VARS['cms_dir']."/razdel/index.php?parent=$parent&rand=" . time());
	exit;
}

if (isset($move_spisok_up)){
	MoveSpisokUp($move_spisok_up);
	header ("Location: /".$_VARS['cms_dir']."/razdel/index.php?parent=$parent&rand=" . time());
	exit;
}

?> 

<html><head>
<title>Редакторский интерфейс сайта <?=$HTTP_SERVER_VARS["SERVER_NAME"] ?></title>
<link rel="stylesheet" href="/cms9/admin.css" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="pragma" content="no-cache">
<script language="javascript" type="text/javascript" src="../js/jquery-1.5.min.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('table.list tr').mouseover(function(){
		$(this).addClass('highlight')
	}).mouseout(function(){
		$(this).removeClass('highlight')
	})
})
</script>
<script language="JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
</head>

<body>
<!--<pre>
<?
print_r($_GET);
?>


</pre>-->
<h3>
<?
if ($parent){
	$par = $parent;
	$nav = '';
	$i = 1;
	while ($par) {
		$p_razdel = GetRazdel('podr', $par);

		if ($i) $nav = " / {$p_razdel[0]['p_title']}" . $nav;
		else $nav = " / <a href=index.php?parent={$p_razdel[0]['id']}>{$p_razdel[0]['p_title']}</a>" . $nav;

		$par = $p_razdel[0]['p_parent_id'];
		$i = 0;
	}
	echo "<a href=index.php>разделы</a>";
	echo "$nav";
}
else {
	/*echo "разделы";*/
}
?>
</h3>


<?

$razdels = GetRazdel('parent', $parent);

?>
<fieldset><legend>Структура сайта</legend>
	<a class="serviceLink" href="/cms9/razdel/razdel_add.php?parent=<?=$parent;?>"><img src='<?=$_ICON["add_item"]?>'>Добавить раздел</a>
	<table cellpadding=5 class="list">
	<tr>
		<th class="smalltext">id</th>
		<th class="smalltext">вниз</th >
		<th class="smalltext">вверх</th >	
		<th ><img src='<?=$_ICON["main_menu"];?>' title="В главном меню"></th >	
		<th class="smalltext" align="center">заголовок</th >
		<th class="smalltext" align="center">адрес</th >
		<th><img src='<?=$_ICON["lock"]?>' title='Закрытый раздел'></th >
		<th class="smalltext" align="center">шаблон</th >
		<th class="smalltext">изменить</th >
		<th class="smalltext">подразделы</th >
		<th class="smalltext">удалить</th >
	</tr>
	<?
if ($razdels){
	
	foreach ($razdels as $key => $razdel)
	{
		$bgc = "";
		if($razdel['p_show']==0) $bgc = " bgcolor=\"#ffdddd\"";
		$func = ""; 
		if(strlen($razdel['p_tpl']) > 0) $func = "<span style='color:green;'>".$razdel['p_tpl']."</span>"; 
		?>
		<tr <?=$bgc;?> valign="middle">
			<td align="center"><?=$razdel['id']?></td>
			<td align="center"><a href='/cms9/razdel/index.php?parent=<?=$parent?>&move_down=<?=$razdel['id']?>' title='вниз'><img src='<?=$_ICON["down"]?>'></a></td>
			<td align="center"><a href='/cms9/razdel/index.php?parent=<?=$parent?>&move_up=<?=$razdel['id']?>' title='вверх'><img src='<?=$_ICON["up"]?>'></a></td>
			<td>
			<?
			if($razdel['p_main_menu'] == 1) { ?><img title="В главном меню" src='<?=$_ICON["main_menu"];?>'><? }
			?>
			</td>
			<td><strong><a href="/cms9/razdel/razdel_edit.php?id=<?=$razdel['id']?>&parent=<?=$parent?>" title='Изменить раздел'><?=$razdel['p_title']?></a></strong></td>
			<td class="smalltext"><a href='/<?=$razdel['p_url']?>/' target=_blank>http://<?=$_SERVER['HTTP_HOST']?>/<?=$razdel['p_url']?>/</a></td>
			<td><? if($razdel['p_protect'] == '1'){ ?><img src='<?=$_ICON["lock"]?>'  title='Закрытый раздел'><? } ?></td>
			<td class="smalltext">
				<?
				$sql = "select * from `".$_VARS['tbl_template_name']."` where tpl_marker = '".$razdel['p_tpl']."'";
				$res = mysql_query($sql);
				$row = mysql_fetch_array($res);
				?>
				<a href="/cms9/workplace.php?page=templates&edit_tpl&id=<?=$row['id']?>"><?=$func?></a>
			</td>
			<td align="center"><a href="/cms9/razdel/razdel_edit.php?id=<?=$razdel['id']?>&parent=<?=$parent?>" title='Изменить раздел'><img src='<?=$_ICON["edit"]?>'></a></td>
			<td align="center"><?
				$sql = "select * from `".$_VARS['tbl_pages_name']."` where p_parent_id = ".$razdel['id'];
				$res = mysql_query($sql);
				if(mysql_num_rows($res) > 0)
				{
					$icon = "<img src='".$_ICON["next"]."'>";				
				}
				else
				{
					$icon = "<img src='".$_ICON["next_empty"]."'>";	
				}
				?><a href="/cms9/razdel/index.php?parent=<?=$razdel['id']?>" title='Перейти к дочерним разделам'><?=$icon;?></a>
			</td>			
			
			<td align="center"><a title='Удалить' href="javascript: if (confirm('Удалить раздел и его подразделы?')) {document.location='?del_id=<?=$razdel['id']?>&parent=<?=$parent?>'}"><img src='<?=$_ICON["del"]?>'></a></td>

		</tr>
	<?		
	}
	?>
	</table>
	<a class="serviceLink" href="/cms9/razdel/razdel_add.php?parent=<?=$parent;?>"><img src='<?=$_ICON["add_item"]?>'>Добавить раздел</a>
</fieldset>
	<?
}
?>


</body>
</html>