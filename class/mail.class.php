<?php
/**
* Class ClsMail
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class ClsMail
{
	private $from;
	private $to;
	private $cc;
	private $bcc;
	private $replyto;
	private $subject;
	private $headers;
	private $attachment;
	private $extracmd;
	private $type;

	/**
	* constructor
	*/
	public function __construct()
	{
		$this->to = Array();
		$this->from = "";
		$this->cc = Array();
		$this->bcc = Array();
		$this->attachment = Array();
		$this->replyto = "";
		$this->extracmd = null;
		$this->type = "html";
	}

	/**
	* Inserts the sender of the email
	* @param string $name Name
	* @param string $mail Mail
	*/
	public function from($name, $mail)
	{
		$this->from = "$name <$mail>";
	}

	/**
	* Includes recipients of email
	* @param string $name Name
	* @param string $mail Mail
	* @param string $reset reinitializing array to
	*/
	public function to($name, $mail, $reset = false)
	{
	    if ($reset) $this->to = Array();
	    if (!empty($name)) $this->to[] = "$name <$mail>";
	    else $this->to[] = "<$mail>";		 
	}

	/**
	* Includes recipients cc of email
	* @param string $name Name
	* @param string $mail Mail
	*/
	public function cc($name, $mail, $reset = false)
	{
	    if ($reset) $this->cc = Array();
	    if (!empty($name)) $this->cc[] = "$name <$mail>";
	    else $this->cc[] = "<$mail>";	
	}

	/**
	* Includes recipients bcc of email
	* @param string $name Name
	* @param string $mail Mail
	*/
	public function bcc($name, $mail, $reset = false)
	{
	    if ($reset) $this->bcc = Array();
	    if (!empty($name)) $this->bcc[] = "$name <$mail>";
	    else $this->bcc[] = "<$mail>";	
	}

	/**
	* Insert the address of the reply email
	* @param string $name Name
	* @param string $mail Mail
	*/
	public function replyto($name, $mail)
	{	    
	    if (!empty($name)) $this->replyto = "$name <$mail>";
	    else $this->replyto = "<$mail>";	
	}

	/**
	* Insert the subject
	* @param string $in
	*/
	public function subject($in)
	{
		$this->subject = $in;
	}

	/**
	* Insert the extra command
	* @param string $in
	*/
	public function extracmd($in)
	{
		$this->extracmd = $in;
	}

	/**
	* Insert the message
	* @param string $in
	*/
	public function message($in)
	{
		$this->message = $in;
	}

	/**
	* Message type
	* text / html
	* @param string $in
	*/
	public function type($in)
	{
		$this->type = $in;
	}

	/**
	* Message attachment
	* text / html
	* @param string $in
	*/
	public function attachment($attachment,$name = "")
	{
		if (empty($name)) $name = basename($attachment);
		$this->attachment[$name] = $attachment;
	}
 

	/**
	* Send all mail
	*/
	public function send()
	{
		$boundary1 ="XXMAILXX".md5(time())."XXMAILXX";
		$boundary2 ="YYMAILYY".md5(time())."YYMAILYY";  

		$this->headers  = 'To: '.implode(", ", $this->to).PHP_EOL;
		$this->headers .= 'From: '.$this->from.PHP_EOL;
		if (!empty($this->cc))  $this->headers .= 'Cc: '.implode(", ", $this->cc).PHP_EOL;
		if (!empty($this->bcc)) $this->headers .= 'Bcc: '.implode(", ", $this->bcc).PHP_EOL;
		if (!empty($this->replyto)) $this->headers .= 'Return-path: '.$this->replyto.PHP_EOL;
               
		$this->headers .= "MIME-Version: 1.0".PHP_EOL;

		// Con allegato/i 
		if (count($this->attachment)>0)
		{
			 $this->headers .= "Content-Type: multipart/mixed;".PHP_EOL;
			 $this->headers .= " boundary=\"{$boundary1}\"".PHP_EOL.PHP_EOL;
			 $this->headers .= "--$boundary1".PHP_EOL;
		}
		$this->headers .= "Content-Type: multipart/alternative;\n";
		$this->headers .= " boundary=\"{$boundary2}\"".PHP_EOL.PHP_EOL;

		// Questa è la parte "testuale" del messaggio
		$message  = "--{$boundary2}".PHP_EOL;
		$message .= "Content-type:text/plain; charset=\"iso-8859-1\"".PHP_EOL;
		$message .= "Content-Transfer-Encoding: 7bit".PHP_EOL.PHP_EOL;
		$message .= strip_tags(str_replace("<br />", "\n", str_replace("<br>", "\n", $this->message))).PHP_EOL;

		//mail html
		if ($this->type=="html")
		{
			 $message .= "--{$boundary2}".PHP_EOL;
			 $message .= "Content-type:text/html; charset=\"iso-8859-1\"".PHP_EOL;
			 $message .= "Content-Transfer-Encoding: 7bit".PHP_EOL.PHP_EOL;
			 $message .= $this->message.PHP_EOL.PHP_EOL;
		}
		$message .= "--{$boundary2}--".PHP_EOL;

		if (count($this->attachment)>0)
		{
		  foreach ($this->attachment as $name => $attach)
		  {
				$file_size = filesize($attach);
				$handle = fopen($attach, "r");
				$content = fread($handle, $file_size);
				fclose($handle);
				$content = chunk_split(base64_encode($content));
				$filename = $name;
				$message .= "--{$boundary1}".PHP_EOL;
				$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"".PHP_EOL;
				$message .= "Content-Transfer-Encoding: base64\r\n";
				$message .= "Content-Disposition: attachment; filename=\"".$filename."\"".PHP_EOL.PHP_EOL;
				$message .= $content.PHP_EOL.PHP_EOL;
			}
		}
		$message .= "--{$boundary1}--".PHP_EOL;
		if (mail("", $this->subject, $message, $this->headers)) return true;
		return false;
	}
}
?>
