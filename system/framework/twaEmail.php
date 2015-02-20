<?php
/**
 * The email object for sending emails.  
 *
 * @see twaController::email();
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaEmail {

/**
 * The subject of the email
 *
 * @var string
 */
public  $subject;
/**
 * The message of the email
 *
 * @var string
 */
public  $message;
/**
 * The text content of the email
 *
 * @var string
 */
public  $text;
/**
 * The boundary between html and text content of the email
 *
 * @var string
 */
public  $boundary;
/**
 * The email headers
 *
 * @var string
 */
public  $headers;
/**
 * Create Email from provided subject and message
 *
 * @param Array $data contains subject and message to email.
 * @access public
 */
public function CreateEmail($data) 
{
	
	$this->subject = stripslashes($data['subject']);
	$this->message = $data['message'];
	
}
/**
 * Create Email from a template provided
 *
 * @param Array $data contains template and subject to email.
 * @access public
 */
public function CreateEmailFromTemplate($data)
{
	
	global $framework;
	$this->subject = stripslashes($data['subject']);
	
	$templatepath = $framework->load('twaLanguage')->path.DS.'email_templates'.DS.$data['template'].".html";
	$textpath = $framework->load('twaLanguage')->path.DS.'email_templates'.DS.$data['template'].".txt";
	
	
	
	if(!file_exists($templatepath))
	{
		die('{"returnCode":1,"error":"Template '.$templatepath.' Not Found"}');
	}
	
	$message = "";
	
	$message = file_get_contents($templatepath);

	if($data)
	{
		foreach($data as $key=>$value)
		{
			$message = str_replace('%%'.$key.'%%',$value,$message);
		}
		
	}
	$this->boundary = uniqid('np');
	$this->message = "";
	
	if(file_exists($textpath)) {
		$this->text = file_get_contents($textpath);
		if($data)
		{
			foreach($data as $key=>$value)
			{
				$this->text = str_replace('%%'.$key.'%%',$value,$this->text);
			}
			
		}
		
		//echo $text;
		
	
		$this->message .= "\r\n\r\n--" . $this->boundary . "\r\n";
		$this->message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
		$this->message .= $this->text;
		$this->message .= "\r\n\r\n--" . $this->boundary . "\r\n";
		$this->message .= "Content-type: text/html;charset=utf-8\r\n\r\n";	
		$this->message .= $message;
		$this->message .= "\r\n\r\n--" . $this->boundary . "--";
		//echo $this->message;
	} else {
		
		$this->message = $message;
	}
	//echo $this->message;
	
}
/**
 * Send email to specified recipients
 *
 * @param Array $data contains recipients information.
 * @access public
 */
public function SendEmail($data)
{
	
	global $framework;
	
	$database = $framework->getDB();
	$emailsettings = $framework->globalsettings()->emailsettings;
	
	if($data['to'] == 'admin')
	{
		$data['to'] = $emailsettings->fromemail;	
	}
	
	if(isset($data['fromemail']))
	{
		$emailsettings->fromemail = $data['fromemail'];
		if(isset($data['fromname']))
		{
			$emailsettings->fromname = $data['fromname'];	
		}
		else
		{
			$emailsettings->fromname = "";		
		}
	}
	
	$this->headers = "MIME-Version: 1.0"."\n";
	
	if(isset($this->text) && $this->text != '') {
		$this->headers .= "Content-Type: multipart/alternative;boundary=".$this->boundary."\r\n";
	} else {
		$this->headers .= "Content-type: text/html; charset=iso-8859-1"."\n";
	}
	
	$this->headers .= "From: ".$emailsettings->fromname."<".$emailsettings->fromemail.">\n";
	$this->headers .= "Reply-To: ".$emailsettings->fromname."<".$emailsettings->fromemail.">\n";
	$this->headers .= "Return-Path: ".$emailsettings->fromname."<".$emailsettings->fromemail.">\n";
	
	ini_set("sendmail_from",$emailsettings->fromemail);
	
	$spamfilter = $this->spamcheck($data['to']);
	if($spamfilter)
	{ 
		if(!mail($data['to'],$this->subject,$this->message,$this->headers)) {
			
			return false;
		}	
		
	} else {
		
		return false;
	}
	ini_restore('sendmail_from'); 
	
	
	return true;
	
}
/**
 * Check for spam
 *
 * @param string $field contains recipient's email address.
 * @return boolean TRUE is successful.
 * @access public
 */
public function spamcheck($field)
{
	$field=filter_var($field, FILTER_SANITIZE_EMAIL);

	if(filter_var($field, FILTER_VALIDATE_EMAIL))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}


}