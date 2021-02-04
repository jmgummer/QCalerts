<?php
/**
 * 
 */
class SendAlerts{
	
	function __construct($phpmailer,$contacts,$From,$Name){
		$this->phpmailer = $phpmailer;
		$this->contacts = $contacts;
		$this->from = $From;
		$this->name = $Name;
	}

	public function SendthisAlert($message,$attachment){
		$phpmailer = $this->phpmailer;
		$contacts = $this->contacts;
		$from = $this->from;
		$name = $this->name;

		require_once $phpmailer;

		foreach ($contacts as $email) {
			$mail = new PHPMailer(false);
    		$mail->isSMTP();
    		$mail->Host = "smtp.elasticemail.com";
    		$mail->SMTPAuth   = true; 
    		$mail->Username   = 'mail.alerts@reelanalytics.net';       // SMTP username
    		$mail->Password = "91AD1BD7CFA3F67D4AA90E8F7704203D56B3";
    		$mail->Port       = 2525;
    		$mail->From = $from;
    		$mail->FromName = $name;
    		$mail->AddAddress($email);
    		$mail->addBcc("joseph.kinyua@reelanalytics.net");
    		$today_formatted=date("d-m-Y");
    		$this_time=date("H:i:s");
    		$mail->IsHTML(true); // set email format to HTML
    		$mail->Subject = /*$subject .*/"Test : $today_formatted $this_time";
    		$mail->Body = $message;
    		$mail->AddAttachment($attachment);
    		if(!$mail->Send()) {
     			echo "Mailer Error: " . $mail->ErrorInfo ."\n";
   			}else{
   				echo "Mail set To $email";
   			}
		}
	}
}