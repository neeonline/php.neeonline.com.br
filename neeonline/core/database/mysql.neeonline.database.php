<?
	require_once(str_replace('/database', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineMYSQLDatabase extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_connectionHandler;
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
		
		public function isConnected()
		{
			return (isset($this->_connectionHandler) && $this->_connectionHandler);
		}
		
		public function connect($authOptions)
		{
			if ($authOptions === null || !is_array($authOptions) || !sizeof($authOptions)) return false;
			
			$this->_connectionHandler = @mysql_connect($authOptions['server'], $authOptions['username'], $authOptions['password']);
			
			if (!$this->_connectionHandler) return false;
			
			if (!mysql_select_db($authOptions['database'], $this->_connectionHandler)) return false;
			
			return $this->_connectionHandler;
		}
		
		public function getError()
		{
			return mysql_error();
		}
		
		public function close()
		{
			if (isset($this->_connectionHandler) && $this->_connectionHandler)
			{
				if (mysql_close($this->_connectionHandler))
				{
					unset($this->_connectionHandler);
					
					return true;
				}
			}
			
			return false;
		}
		
		public function execute($query)
		{
			if (!isset($this->_connectionHandler) || !$this->_connectionHandler) return null;
			
			return @mysql_query($query, $this->_connectionHandler);
		}
		
		public function lastId()
		{
			return mysql_insert_id();
		}
		
		public function numRows($queryHandler)
		{
			return mysql_num_rows($queryHandler);
		}
		
		public function affectedRows($queryHandler)
		{
			return mysql_affected_rows($queryHandler);
		}
		
		public function dataSeek($queryHandler, $pointer)
		{
			return mysql_data_seek($queryHandler, $pointer);
		}
		
		public function fetchRow($queryHandler)
		{
			return mysql_fetch_row($queryHandler);
		}
		
		public function fetchArray($queryHandler)
		{
			return mysql_fetch_array($queryHandler);
		}
		
		public function clear($queryHandler)
		{
			return mysql_free_result($queryHandler);
		}
	}
?>