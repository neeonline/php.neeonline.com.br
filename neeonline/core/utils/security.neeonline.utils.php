<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineSecurityUtils extends NeeonlineDynamicClass
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
		
		public function createHash($size, $charset = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ023456789!@#$%&(){}[]')
		{ 
			$createdHash = ''; 
			
			for ($i = 0; $i < $size; $i++) $createdHash .= substr($charset, rand() % 33, 1);
			
			return $createdHash;
		}
	}
?>