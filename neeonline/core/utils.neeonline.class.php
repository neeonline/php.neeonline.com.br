<?
	require_once('dynamic.neeonline.class.php');
	
	class NeeonlineUtilsClass extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_classFolder = __DIR__ . '/utils';
			$this->_classType	= 'utils';
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
	}
?>