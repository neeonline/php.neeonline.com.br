<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineXMLSerializerUtils extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_xmlVersion;
		private $_xmlEncoding;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_xmlVersion		= '1.0';
			$this->_xmlEnconding	= 'UTF-8';
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
		
		public function xmlFromObject($object, $nodeBlock = 'nodes', $node = 'node')
		{
			return $this->xmlFromArray(get_object_vars($object), $nodeBlock, $node);
		}
		
		public function xmlFromArray($array, $nodeBlock = 'nodes', $node = 'node')
		{
			$returnValue	= '<?xml version="' . $this->_xmlVersion . '" encoding="' . $this->_xmlEnconding . '" ?>';
			
			$returnValue	.= '<' . $nodeBlock . '>';
			$returnValue	.= $this->_generateNodes($array, $node);
			$returnValue	.= '</' . $nodeBlock . '>';
			
			return $returnValue;
		}
		
		private function _generateNodes($content, $node)
		{
			$returnValue = '';
			
			if (is_array($content) || is_object($content))
			{
				foreach ($content as $key => $value)
				{
					if (is_numeric($key)) $key = $node;
					
					$returnValue .= '<' . $key . '>' . $this->_generateNodes($value, $node) . '</' . $key . '>';
				}
			}
			else $returnValue = sprintf("<![CDATA[%s]]>", $content);
			
			return $returnValue;
		 }
	}
?>