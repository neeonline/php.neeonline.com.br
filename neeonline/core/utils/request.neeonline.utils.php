<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineRequestUtils extends NeeonlineDynamicClass
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
		
		public function checkPost($parameter, $returnValue = 0)
		{
			return isset($_POST[$parameter]) ? $_POST[$parameter] : $returnValue;
		}
		
		public function checkGet($parameter, $returnValue = 0)
		{
			return isset($_GET[$parameter]) ? $_GET[$parameter] : $returnValue;
		}
		
		public function checkRequest($parameter, $returnValue = 0)
		{
			return isset($_REQUEST[$parameter]) ? $_REQUEST[$parameter] : $returnValue;
		}
		
		public function checkCookie($parameter, $returnValue = 0)
		{
			return isset($_COOKIE[$parameter]) ? $_COOKIE[$parameter] : $returnValue;
		}
		
		public function checkSession($parameter, $returnValue = 0)
		{
			return isset($_SESSION[$parameter]) ? $_SESSION[$parameter] : $returnValue;
		}
		
		public function checkAll($parameter, $returnValue = 0, $priority = null)
		{
			if ($priority === null || !sizeof($priority)) $priority = array('post', 'get', 'cookie', 'session');
			
			$value = null;
			
			for ($i = 0; $i < sizeof($priority); $i++)
			{
				if ($value != null) break;
				
				switch($priority[$i])
				{
					case 'post':
						$value = $this->checkPost($parameter, null);
						break;
					case 'get':
						$value = $this->checkGet($parameter, null);
						break;
					case 'cookie':
						$value = $this->checkCookie($parameter, null);
						break;
					case 'session':
						$value = $this->checkSession($parameter, null);
						break;
				}
			}
			
			return ($value !== null) ? $value : $returnValue;
		}
	}
?>