<?
include_once DIR_FRAMEWORK.'/class.image.php';

// id альбома с фотками коллекции
$a = $_VARS['env']['photo_alb_coll'];

$sql = "SELECT * FROM `".$_VARS['tbl_prefix']."_pic_".$a."`
		WHERE 1
		ORDER BY order_by ASC";
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
		/*$img -> imgWidthMax 	= 'DEFAULT';
		$img -> imgHeightMax 	= 'DEFAULT';*/
		$img -> imgWidthMax 	= 408;
		$img -> imgHeightMax 	= 676;
		$tag = $img -> showPic();			
		
		// массив слайдов
		$arr[] = array(
			'tag' 	=> $tag,
			'name' 	=> $alt
		);
		
		$i++;
	}
}


?>
<div id="gallery_slider">
			
<?

foreach($arr as $k)
{
	?>
	 <div class="slide"> 
		<a href="#"><div><?=$k['tag']?></div></a>
		<div class="slide-info">
		  <div class="slide-text">
			<p><?=$k['name']?></p>
		  </div>
		</div>
	  </div>
	<?
}		
?>				 

<!--<div id="gallery_slider-subtitle"></div>-->			

</div>