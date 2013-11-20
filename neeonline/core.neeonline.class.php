<?
	require_once(__DIR__ . '/core/dynamic.neeonline.class.php');
	
	class Neeonline extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_classFolder = __DIR__ . '/core';
			$this->_classType	= 'class';
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
	}
?>