
<?php
/**
 * The email object for sending emails.  
 *
 * @see twaController::email();
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaSimpleEmail {

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
	$this->text = $data['text'];
	
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
	
	$this->message = $message;
	
	if(file_exists($textpath)) {
		$this->text = file_get_contents($textpath);
		if($data)
		{
			foreach($data as $key=>$value)
			{
				$this->text = str_replace('%%'.$key.'%%',$value,$this->text);
			}
			
		}	
		//echo $this->message;
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
	
	$to = array();
	if(gettype($data['to']) == "array"){
		foreach($data['to'] as $email_address){
			if($this->spamcheck($email_address)){
				$to[] = $email_address;
			}
		}
	} else {
		if($this->spamcheck($data['to'])){
			$to[] = $data['to'];
		}
	}

	$cc = array();
	if(isset($data['cc'])){
		if(gettype($data['cc']) == "array"){
			foreach($data['cc'] as $email_address){
				if($this->spamcheck($email_address)){
					$cc[] = $email_address;
				}
			}
		} else {
			if($this->spamcheck($data['cc'])){
				$cc[] = $data['cc'];
			}
		}
	}

	$bcc = array();
	if(isset($data['bcc'])){
		if(gettype($data['bcc']) == "array"){
			foreach($data['bcc'] as $email_address){
				if($this->spamcheck($email_address)){
					$bcc[] = $email_address;
				}
			}
		} else {
			if($this->spamcheck($data['bcc'])){
				$bcc[] = $data['bcc'];
			}
		}
	}
	
	if($to)
	{ 
		try {
			global $ses;
			
			$framework->load('twaDebugger')->log($emailsettings->fromemail);

			$dest = array();
			$dest['ToAddresses'] = $to;
			if($cc){
				$dest['CcAddresses'] = $cc;
			}

			if($bcc){
				$dest['BccAddresses'] = $bcc;
			}


			$result = $ses->sendEmail(array(
			    // Source is required
			    'Source' => $emailsettings->fromemail,
			    // Destination is required
			    'Destination' => $dest,
			    // Message is required
			    'Message' => array(
			        // Subject is required
			        'Subject' => array(
			            // Data is required
			            'Data' => $this->subject
			        ),
			        // Body is required
			        'Body' => array(
			            'Text' => array(
			                // Data is required
			                'Data' => $this->text
			            ),
			            'Html' => array(
			                // Data is required
			                'Data' => $this->message
			            ),
			        ),
			    ),
			    'ReplyToAddresses' => array($emailsettings->fromemail),
			    'ReturnPath' => $emailsettings->fromemail,
			));	
		
		} catch(Exception $e) {
			
			handleException($e);
			return false;
		} 
		
		return true;
		
	} else {
		
		return false;
	}
	
	
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



