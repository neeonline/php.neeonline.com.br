<?
	require_once('dynamic.neeonline.class.php');
	
	class NeeonlineI18NClass extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_defaultLocation;
		private $_language;
		private $_languagePath;
		
		public $location; 
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function load($defaultLocation = 'pt_BR', $ignoreCache = false, $languagePath = '')
		{
			if ($languagePath == '') $languagePath = __DIR__ . '/i18n/';
			
			$this->_defaultLocation = $defaultLocation;
			
			$location = $defaultLocation;
			
			if (!$ignoreCache)
			{
				if ($newLanguage = isset($_GET['i18n']) ? $_GET['i18n'] : false) $location = $newLanguage;
				else if ($newLanguage = isset($_POST['i18n']) ? $_POST['i18n'] : false) $location = $newLanguage;
				else if ($savedLanguage = isset($_COOKIE['i18n']) ? $_COOKIE['i18n'] : false) $location = $savedLanguage;
			}
			
			$languageFile = $languagePath . $location . '.neeonline.language.php';
			
			if (!file_exists($languageFile))
			{
				$languageFile = $languagePath . $this->_defaultLocation . '.neeonline.language.php';
				
				if (!file_exists($languageFile)) trigger_error("Neeonline::I18N::__construct error: language file not found.", E_USER_ERROR);
				
				$location = $this->_defaultLocation;
			}
			
			include($languageFile);
			
			$languageData			= 'I18N_' . $location;
			
			$this->location			= $location;
			$this->_languagePath	= $languagePath;
			$this->_language		= $$languageData;
			
			if (!$ignoreCache) setcookie('i18n', $this->location, time() + 60 * 60 * 24 * 30);
		}
		
		public function exportLocationData($location = '')
		{
			if (!isset($this->_languagePath) || !isset($this->_language)) return false;
			
			if ($location == '') $location = $this->location;
			
			$languageFile = $this->_languagePath . $location . '.neeonline.language.php';
			
			if (!file_exists($languageFile)) return false;
			
			include($languageFile);
			
			$languageData = 'I18N_' . $location;
			$language = $$languageData;
			
			return $language;
		}
		
		public function getFirstLanguage($languagePath = '')
		{
			if ($languagePath == '') $languagePath = __DIR__ . '/i18n/';
			
			$languagesList = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$firstLanguage = $languagesList[0];
			
			if (strpos($firstLanguage, '-') !== false)
			{
				$arrLanguage = explode('-', $firstLanguage);
				
				$firstLanguage = $arrLanguage[0] . '_' . strtoupper($arrLanguage[1]);
			}
			
			$languageFile = $languagePath . $firstLanguage . '.neeonline.language.php';
			
			if (file_exists($languageFile)) return $firstLanguage;
			
			return '';
		}
		
		public function e($key)
		{
			if (isset($this->_language) && isset($this->_language[$key])) return $this->_language[$key];
			else return $key;
		}
		
		public function getLocations()
		{
			$returnValue = array();
			
			if ($locations = @scandir($this->_languagePath))
			{
				for ($i = 0; $i < sizeof($locations); $i++)
				{
					$currentFile = $locations[$i];
					
					if (strpos($currentFile, '.neeonline.language.php') && file_exists($this->_languagePath . $currentFile))
					{
						$fp			= fopen($this->_languagePath . $currentFile, 'r');
						$fileData	= fread( $fp, 8192 );
						fclose($fp);
						
						$fileData = str_replace("\r", "\n", $fileData);
						
						$locationHeader = array(
							'location'	=> 'Translation Location',
							'title'		=> 'Translation Title',
							'author'	=> 'Translation Author',
							'version'	=> 'Translation Version'
						);
						
						foreach ($locationHeader as $key => $value)
						{
							// Thanks for Wordpress.org developers for this awesome regex magic!
							
							if (preg_match( '/^[ \t\/*#@]*' . preg_quote($value, '/') . ':(.*)$/mi', $fileData, $match) && $match[1]) $locationHeader[$key] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
							else $locationHeader[$key] = '';
						}
						
						$returnValue[] = $locationHeader;
					}
				}
			}
			
			return $returnValue;
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
	}
?>