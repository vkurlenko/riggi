<?
/*-----------------------------------*/
/* вставка HTML-редактора			 */
/*-----------------------------------*/

// $editor_text_edit - редактируемый текст
// $editor_text_name - имя поля формы, в которую передать новый текст

include $_SERVER['DOC_ROOT']."/fckeditor/fckeditor.php";

$text = "";
if(isset($editor_text_edit))
{
	$text = mb_eregi_replace("<p>[[:space:]]*</p>","<p>&nbsp;</p>", $editor_text_edit);
}
$oFCKeditor	-> BasePath = '../../fckeditor/editor/';
$sBasePath 	= '../../fckeditor/';
$oFCKeditor = new FCKeditor($editor_text_name);
$oFCKeditor	-> BasePath	= $sBasePath ;
if(isset($editor_height))
{
	$oFCKeditor -> Height = $editor_height;
}
$oFCKeditor	-> Value		= "$text";
$oFCKeditor	-> Create();
?>