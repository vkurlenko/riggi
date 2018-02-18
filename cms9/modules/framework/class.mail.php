<?
class Mail
{
	var $mailAddressTo;
	var $mailAddressFrom;
	var $mailSubject;
	var $mailText;
	var $mailResult;
	var $mailContentType = "text/plain";
	
	function mailSend()
	{		
		$s = mail($this -> mailAddressTo, 
					$this -> mailSubject, 
					$this -> mailText, 
					"From: ".$this -> mailAddressFrom."\nContent-Type: ".$this -> mailContentType."; charset=utf-8\r\n"."Content-Transfer-Encoding: 8bit\r\n"
					);
		$this -> mailResult = $s;
		return $this -> mailResult;
	}
}

?>