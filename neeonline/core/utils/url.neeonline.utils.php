<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineUrlUtils extends NeeonlineDynamicClass
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
		
		public function getUrlAddress()
		{
			$https	= (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
			
			$port	= (isset($_SERVER["SERVER_PORT"]) && ((!$https && $_SERVER["SERVER_PORT"] != "80") || ($https && $_SERVER["SERVER_PORT"] != "443")));
			$port	= ($port) ? ':' . $_SERVER["SERVER_PORT"] : '';
			
			$url	= ($https ? 'https://' : 'http://') . $_SERVER["SERVER_NAME"] . $port . $_SERVER["REQUEST_URI"];
			
			return $url;
		}
		
		public function filterUrlParameters($excludeParams = null, $url = null, $encode = false, $parametersInitializer = '?', $parametersSeparator = '&', $parametersUnion = '=')
		{
			if (!$url) $url = $this->getUrlAddress();
			
			if (!isset($excludeParams) || !sizeof($excludeParams)) return ($encode) ? urlencode($url) : $url;
			
			if ($parametersInitializer != '' && strpos($url, $parametersInitializer) === false)
			{
				$url = $url . $parametersInitializer;
				
				return ($encode) ? urlencode($url) : $url;
			}
			
			$splitedUrl		= explode($parametersInitializer, $url);
			$urlParameters	= explode($parametersSeparator, $splitedUrl[1]);
			$clearedUrl		= '';
			$i				= 0;
			
			for ($i = 0; $i < sizeof($urlParameters); $i++)
			{
				$splitedParameter = explode($parametersUnion, $urlParameters[$i]);
				
				if (!in_array($splitedParameter[0], $excludeParams) && isset($splitedParameter[1]))
				{
					if ($clearedUrl != '') $clearedUrl .= $parametersSeparator;
					
					$clearedUrl .= $urlParameters[$i];
				}
			}
			
			$returnValue = ($clearedUrl == '') ? $splitedUrl[0] . $parametersInitializer : $splitedUrl[0] . $parametersInitializer . $clearedUrl;
			
			return ($encode) ? urlencode($returnValue) : $returnValue;
		}
	}
?>