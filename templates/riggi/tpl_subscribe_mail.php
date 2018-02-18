
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body style="background:#333; color:#FFFFFF; font-family:tahoma;">
	
	<table style="padding:0; margin:0; border:0; width:100%; background:#333;" cellspacing=10>
		<!-- header -->
		<tr>
			<?
			include $_SERVER['DOCUMENT_ROOT'].'/blocks/menu.main.subscr.php';
			?>
		</tr>
		<!-- /header -->
		
		<!-- content -->
		<tr>
			<td colspan=<?=count($arrMenuMain)?>>{SUBSCRIBE}</td>
		</tr>
		<!-- /content -->
		
		<!-- footer -->
		<tr>
			<td colspan=<?=count($arrMenuMain)?>>
			<?
			include $_SERVER['DOCUMENT_ROOT'].'/blocks/menu.foot.subscr.php';
			?>
			</td>
		</tr>
		<!-- /footer -->
		
		
	
	</table>
	
			
</body>
</html>
