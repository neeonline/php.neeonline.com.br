<?
	require_once('dynamic.neeonline.class.php');
	require_once('mailer/sendmail.class.php');
	
	class NeeonlineMailerClass extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_sendMail;
		private $_subject;
		private $_senderEmail;
		private $_senderName;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_sendMail = new SendMail();
		}
		
		protected function _conditional($keys, $options, $content)
		{
			if (sizeof($keys) != sizeof($options)) return $content;
			
			for ($k = 0; $k < sizeof($keys); $k++)
			{
				$key = $keys[$k];
				$option = $options[$k];
				
				$initialKey = '[' . $key . ']';
				$finalKey = '[/' . $key . ']';
				
				if (strpos($content, $initialKey) === false) continue;
				
				if ($option)
				{
					$content = str_replace(array(
						$initialKey,
						$finalKey
					), array(
						'',
						''
					), $content);
				}
				else
				{
					while(strpos($content, $initialKey) !== false)
					{
						$initialPosition = strpos($content, $initialKey);
						$finalPosition = strpos($content, $finalKey, $initialPosition);
						
						$initialContent = substr($content, 0, $initialPosition);
						$finalContent = substr($content, $finalPosition + strlen($finalKey));
						
						$content = $initialContent . $finalContent;
					}
				}
			}
			
			return $content;
		}
		
		protected function _findAndReplace($replaceData, $content)
		{
			if (strpos($content, '{') === false) return $content;
			
			$initialPosition = strpos($content, '{') + 1;
			$finalPosition = strpos($content, '}', $initialPosition);
			$key = substr($content, $initialPosition, $finalPosition - $initialPosition);
			$k = '{' . $key . '}';
			
			if (isset($replaceData[$key])) $v = $replaceData[$key];
			else $v = '';
			
			$content = preg_replace('/' . $k . '/', $v, $content);
			
			return $this->_findAndReplace($replaceData, $content);
		}
		
		public function configure($subject, $senderEmail, $senderName)
		{
			$this->_subject		= $subject;
			$this->_senderEmail	= $senderEmail;
			$this->_senderName	= $senderName;
		}
		
		public function send($data, $contactsList, $conditionals = null, $templateFile = '')
		{
			if (!isset($this->_subject) || $this->_subject == '' || !isset($this->_senderEmail) || $this->_senderEmail == '' || !isset($this->_senderName) || $this->_senderName == '') trigger_error("Neeonline::Mailer::send error: invalid configuration", E_USER_ERROR);
			
			if (!is_array($data) || !sizeof($data)) trigger_error("Neeonline::Mailer::send error: invalid 'data' param", E_USER_ERROR);
			if (!is_array($contactsList) || !sizeof($contactsList)) trigger_error("Neeonline::Mailer::send error: invalid 'contactsList' param", E_USER_ERROR);
			
			if ($conditionals == null) $conditionals = array();
			if ($templateFile == '') $templateFile = __DIR__ . '/mailer/template.html';
			
			if (!file_exists($templateFile)) trigger_error("Neeonline::Mailer::send error: template file not found.", E_USER_ERROR);
			
			$template = file_get_contents($templateFile);
			
			if ($template === false || $template == '') return false;
			
			if (sizeof($conditionals))
			{
				$keys		= array();
				$options	= array();
				
				foreach ($conditionals as $key => $value)
				{
					$keys[]		= $key;
					$options[]	= $value; 
				}
				
				$template = $this->_conditional($keys, $options, $template);
			}
			
			for ($c = 0; $c < sizeof($contactsList); $c++)
			{
				$contact = $contactsList[$c];
				
				if (!isset($contact['EMAIL']) || !isset($contact['NAME'])) trigger_error("Neeonline::Mailer::send error: invalid 'contact' data. Can't find NAME or EMAIL values", E_USER_ERROR);
				
				$contactData = array();
				
				foreach ($contact as $key => $value) $contactData['CONTACT_' . $key] = $value;
				
				$messageContent = $this->_findAndReplace(array_merge($data, $contactData), $template);
				
				// Thanks for Koa Metter (koa.metter@bkwld.com) for this awesome class!
				$mail = new SendMail();
				
				$mail->addEmail($contactData['CONTACT_EMAIL'], $contactData['CONTACT_NAME']);
				
				$mail->subject($this->_subject);
				$mail->body('', $messageContent);
				$mail->from($this->_senderEmail, $this->_senderName);
				
				$mail->send();
			}
			
			return true;
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
	}
?>