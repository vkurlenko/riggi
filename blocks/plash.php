<?
include_once DIR_FRAMEWORK.'/class.image.php';

$a = $_VARS['env']['photo_alb_plash'];

// размеры плашек
$s = array(
	array(240, 140),
	array(600, 340),
	array(200, 130),
	
	array(400, 230),
	array(300, 180),
	array(240, 140)
);

$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pic_".$a."`
		WHERE 1
		ORDER BY order_by ASC
		LIMIT 0,6";
$res = mysql_query($sql);

if($res && mysql_num_rows($res) > 0)
{
	$arr = array();
	$i = 0;
	while($row = mysql_fetch_array($res))
	{	

		if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
			$alt = $row['pub'];
		else
			$alt = $row['name'];
	
		$img = new Image();
		$img -> imgCatalogId 	= $a;
		$img -> imgId 			= $row['id'];
		$img -> imgAlt 			= $alt;
		$img -> imgWidthMax 	= $s[$i][0];
		$img -> imgHeightMax 	= $s[$i][1];		
		$img -> imgTransform	= "crop";
		$tag = $img -> showPic();			
		
		$arr[] = array(
			'tag' 	=> $tag,
			'name' 	=> $alt,
			'url'	=> $row['url']
		);
		
		$i++;
	}
}
?>

<div class="mainCont1">
	<?
	for($i = 1; $i < 4; $i++)
	{
		?><div class="plash plash<?=$i?>"><a href="<?=$arr[$i-1]['url']?>"><?=$arr[$i-1]['tag']?><em class="plashText"><?=$arr[$i-1]['name']?></em></a></div>
		<?
	}
	?>
</div>

<div class="mainCont2">
<?
	for($i = 4; $i < 7; $i++)
	{
		?><div class="plash plash<?=$i;?>"><a href="<?=$arr[$i-1]['url']?>"><?=$arr[$i-1]['tag']?><em class="plashText"><?=$arr[$i-1]['name']?></em></a></div>
		<?
	}
	?>
</div>