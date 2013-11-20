<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineValidationUtils extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
		
		public function validateEmailAddress($emailAddress, $checkDomain = true)
		{
			$isValid = true;
			$atIndex = strrpos($emailAddress, "@");
			
			if ($atIndex === false) $isValid = false;
			else
			{
				$local	= substr($emailAddress, 0, $atIndex);
				$domain	= substr($emailAddress, $atIndex + 1);
				
				$localLenght	= strlen($local);
				$domainLenght	= strlen($domain);
				
				if ($localLenght < 1 || $localLenght > 64 || $domainLenght < 1 || $domainLenght > 255) $isValid = false;
				else if ($local[0] == '.' || $local[$localLenght - 1] == '.' || preg_match('/\\.\\./', $local)) $isValid = false;
				else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) || preg_match('/\\.\\./', $domain)) $isValid = false;
				else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace('\\\\', '', $local))) $isValid = false;
				
				if ($checkDomain && $isValid && !(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'))) $isValid = false;
			}
			
   			return ($isValid) ? $emailAddress : false;
		}
		
		public function validateUrlAddress($urlAddress)
		{
			if (strpos($urlAddress, 'http://') === false && strpos($urlAddress, 'ftp://') === false) $urlAddress = 'http://' . $urlAddress;
			
			$isValid = preg_match('/^(http|https|ftp):\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $urlAddress);
			
			return ($isValid) ? $urlAddress : false;
		}
		
		public function validateDate($strDate, $dateFormat = '', $d = 'DD', $m = 'MM', $y = 'YYYY')
		{
			if ($dateFormat != '' && (strpos($dateFormat, 'DD') === false || strpos($dateFormat, 'MM') === false || strpos($dateFormat, 'YYYY') === false)) return false;
			
			if ($d == '') $d = 'DD';
			if ($m == '') $m = 'MM';
			if ($y == '') $y = 'YYYY';
			
			if ($dateFormat == '') $dateFormat = $d . $m  . $y;
			
			$clearDate	= preg_replace('/[^a-zA-Z0-9_]/', '', $strDate);
			$dateFormat	= preg_replace('/[^a-zA-Z0-9_]/', '', $dateFormat);
			
			$day	= (int) substr($clearDate, strpos($dateFormat, $d), strlen($d));
			$month	= (int) substr($clearDate, strpos($dateFormat, $m), strlen($m));
			$year	= (int) substr($clearDate, strpos($dateFormat, $y), strlen($y));
			
			if (!$day || !$month || !$year || $day > 31 || $month > 12) return false;
			
			return checkdate($month, $day, $year);
		}
		
		public function validateTime($strTime, $separator = ':')
		{
			if ($separator == '') return false;
			
			@list($hour, $minutes, $seconds) = array_map('intval', explode($separator, $strTime));
			
			if (!isset($seconds)) $seconds = 0;
			
			return ($hour < 24 && $minutes < 60 && $seconds < 60) ? true : false;
		}
	}
?>