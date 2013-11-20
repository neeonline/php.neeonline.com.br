<?
	class NeeonlineDynamicClass
	{
		protected static $_instance;
		
		protected $_dynamicData;
		protected $_classFolder;
		protected $_classType;
		
		protected function __construct()
		{
			$this->_dynamicData = array();
		}
		
		public function __set($property, $data)
		{
			$this->_dynamicData[$property] = $data;
		}
		
		public function __get($property)
		{
			$property = trim($property);
			
			if (isset($this->_dynamicData[$property])) return $this->_dynamicData[$property];
			
			if (isset($this->_classFolder) && isset($this->_classType))
			{
				$coreClasses = scandir($this->_classFolder);
				
				for ($i = 0; $i < sizeof($coreClasses); $i++)
				{
					$currentFile = $coreClasses[$i];
					
					if ($currentFile == strtolower($property) . '.neeonline.' . $this->_classType . '.php')
					{
						require_once($this->_classFolder . '/' . $currentFile);
						
						$className = 'Neeonline' . $property . $this->_classType;
						
						$newClass = new $className();
						
						$this->_dynamicData[$property] = $newClass;
						
						return $newClass;
					}
				}
			}
			
			return null;
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_instance)) self::$_instance = new self();
			
			return self::$_instance;
		}
	}
?>