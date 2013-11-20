<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineStringUtils extends NeeonlineDynamicClass
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
		
		public function clearString($strContent, $encode = "UTF-8")
		{
			
			$map = array(
				'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
				'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
				'C' => '/&Ccedil;/',
				'c' => '/&ccedil;/',
				'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
				'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
				'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
				'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
				'N' => '/&Ntilde;/',
				'n' => '/&ntilde;/',
				'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
				'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
				'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
				'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
				'Y' => '/&Yacute;/',
				'y' => '/&yacute;|&yuml;/',
				'_'	=> '/ /',
				'' => '/&ordf;/', //ª
				'' => '/&ordm;/', //º
				'' => '/[^a-zA-Z0-9_]/'
			);
			
			return strtolower(trim(preg_replace($map, array_keys($map), htmlentities(str_replace('&', '', $strContent), ENT_NOQUOTES, $encode))));
		}
		
		public function getFileExtension($fileName, $returnDot = false)
		{
			$extension = strtolower(str_replace('.', '', strrchr($fileName, '.')));
			
			return ($returnDot) ? '.' . $extension : $extension;
		}
	}
?>