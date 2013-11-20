<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineConversionUtils extends NeeonlineDynamicClass
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
		
		public function strDateTimeToTimestamp($strDatetime, $datetimeFormat = '', $d = 'DD', $m = 'MM', $y = 'YYYY', $h = 'HH', $i = 'II', $s = 'SS')
		{
			if ($datetimeFormat != '' && (strpos($datetimeFormat, 'DD') === false || strpos($datetimeFormat, 'MM') === false || strpos($datetimeFormat, 'YYYY') === false || strpos($datetimeFormat, 'HH') === false || strpos($datetimeFormat, 'II') === false)) return false;
			
			if ($d == '') $d = 'DD';
			if ($m == '') $m = 'MM';
			if ($y == '') $y = 'YYYY';
			if ($h == '') $h = 'HH';
			if ($i == '') $i = 'II';
			if ($s == '') $s = 'SS';
			
			if ($datetimeFormat == '') $datetimeFormat = $d . $m  . $y . $h . $i . $s;
			
			$clearDatetime = preg_replace('/[^a-zA-Z0-9_]/', '', $strDatetime);
			$datetimeFormat = preg_replace('/[^a-zA-Z0-9_]/', '', $datetimeFormat);
			
			$day	= (int) substr($clearDatetime, strpos($datetimeFormat, $d), strlen($d));
			$month	= (int) substr($clearDatetime, strpos($datetimeFormat, $m), strlen($m));
			$year	= (int) substr($clearDatetime, strpos($datetimeFormat, $y), strlen($y));
			$hour	= (int) substr($clearDatetime, strpos($datetimeFormat, $h), strlen($h));
			$minute	= (int) substr($clearDatetime, strpos($datetimeFormat, $i), strlen($i));
			$second	= 0;
			
			if (strpos($datetimeFormat, $s) !== false) $second	= (int) substr($clearDatetime, strpos($datetimeFormat, $s), strlen($s));
			
			return mktime($hour, $minute, $second, $month, $day, $year);
		}
		
		public function timestampToDatetime($timestamp = 0, $endDay = false)
		{
			if (!$timestamp)
			{
				$timestamp = mktime(date('H'), date('i'), date('s'), date('n'), date('j'), date('Y'));
				
				if ($endDay)
				{
					$date = date('Y-m-d', $timestamp);
					$date .= ' 23:59:59';
					
					$timestamp = strtotime($date);
				}
			}
			
			return date('Y-m-d H:i:s', $timestamp);
		}
		
		public function datetimeToTimestamp($datetime)
		{
			return strtotime($datetime);
		}
		
		public function htmlToDatabase($strHTML)
		{
			return htmlentities($strHTML, ENT_QUOTES | ENT_IGNORE, 'UTF-8');
		}
		
		public function htmlToPHP($strHTML)
		{
			return html_entity_decode($content, ENT_COMPAT, 'UTF-8');
		}
		
		public function strNumberToDatabase($strNumber)
		{
			return preg_replace('/[^0-9]/', '', $strNumber);
		}
		
		public function strNumberToPHP($strNumber, $numberMask = '', $specialMask = array())
		{
			$strNumber = $this->strNumberToDatabase($strNumber);
			$maskedNumber = '';
			
			if (sizeof($specialMask)) foreach ($specialMask as $key => $value) if (!strncmp($strNumber, $key, strlen($key)) && strlen($strNumber) === strlen($this->strNumberToDatabase($value))) $numberMask = $value;
			
			if (strlen($numberMask))
			{
				$n = -1;
				
				for ($i = 0; $i < strlen($numberMask); $i++)
				{
					$c = $numberMask[$i];
					
					$maskedNumber .= (is_numeric($c) === true) ? $strNumber[++$n] : $c;
				}
			}
			else $maskedNumber = (int) $strNumber;
			
			return $maskedNumber;
		}
		
		public function convertBytes($bytes, $precision = 2)
		{
			$units	= array('B', 'KB', 'MB', 'GB', 'TB'); 
		
			$bytes	= max($bytes, 0); 
			$pow	= floor(($bytes ? log($bytes) : 0) / log(1024)); 
			$pow	= min($pow, count($units) - 1); 
			
			$bytes	/= pow(1024, $pow);
			
			return round($bytes, $precision) . ' ' . $units[$pow]; 
		}
	}
?>