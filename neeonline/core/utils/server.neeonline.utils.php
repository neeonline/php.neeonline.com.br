<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineServerUtils extends NeeonlineDynamicClass
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
		
		public function getOS()
		{
			$currentOS = '';
			
			$OSList = array(
				'Windows 3.11'			=> '(Win16)',
				'Windows 95'			=> '(Windows 95)|(Win95)|(Windows_95)',
				'Windows 98'			=> '(Windows 98)|(Win98)',
				'Windows 2000'			=> '(Windows NT 5.0)|(Windows 2000)',
				'Windows XP'			=> '(Windows NT 5.1)|(Windows XP)',
				'Windows Server 2003'	=> '(Windows NT 5.2)',
				'Windows Vista'			=> '(Windows NT 6.0)',
				'Windows 7'				=> '(Windows NT 7.0)',
				'Windows NT 4.0'		=> '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
				'Windows ME'			=> '(Windows ME)',
				'Open BSD'				=> '(OpenBSD)',
				'Sun OS'				=> '(SunOS)',
				'Linux'					=> '(Linux)|(X11)',
				'Mac OS'				=> '(Mac_PowerPC)|(Macintosh)',
				'QNX'					=> '(QNX)',
				'BeOS'					=> '(BeOS)',
				'OS/2'					=> '(OS/2)',
				'Search Bot'			=> '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)',
				'iOS'					=> '(iPhone)|(iPad)|(iPod)',
				'Android'				=> '(Android)'
			);
			
			foreach ($OSList as $key => $value)
			{
				if (@preg_match('/' . $value . '/i', $_SERVER['HTTP_USER_AGENT']))
				{
					$currentOS = $key;
					
					break;
				}
			}
			
			return $currentOS;
		}
		
		public function getBrowser()
		{
			$currentBrownser = '';
			
			$browserList = array(
				'Internet Explorer'	=> '(msie)',
				'Firefox'			=> '(firefox)',
				'Safari'			=> '(safari)',
				'Google Chrome'		=> '(chrome)',
				'Opera'				=> '(opera)',
				'Netscape'			=> '(netscape)',
				'Maxthon'			=> '(maxthon)',
				'Konqueror'			=> '(konqueror)',
				'Mobile Browser'	=> '(mobile)'
			);
			
			foreach ($browserList as $key => $value)
			{ 
				if (@preg_match('/' . $value . '/i', $_SERVER['HTTP_USER_AGENT']))
				{
					$currentBrownser = $key;
				}
			}
			
			return $currentBrownser;
		}
	}
?>