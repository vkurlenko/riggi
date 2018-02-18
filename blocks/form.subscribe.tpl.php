
	{HTML_userName}
	{HTML_userMail}
	<div>
		<div class="infoCaptcha">
			<!--<img id="captcha" src="/captcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" width="100" height="40">-->
			{HTML_captcha}<br />
			<a class="reloadCaptcha" href="#">{TITLE_otherCode}</a>
		</div>
		<div class="infoCaptchaCheck">
			<!--<input type="text" id="captchaField" name="captcha" value="" />-->
			{HTML_captchaField}
			<a id="sendMsg" href="#">{TITLE_sendMsg}</a>
		</div>
	</div>

