<?
	require_once(str_replace('/database', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineMSSQLDatabase extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_connectionHandler;
		
		public function __construct()
		{
			if (!extension_loaded('mssql')) trigger_error("NeeonlineMSSQLServer::__construct error: Microsft MySQL Module not loaded.", E_USER_ERROR);
			
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
			
			$this->_connectionHandler = @mssql_connect($authOptions['server'], $authOptions['username'], $authOptions['password']);
			
			if (!$this->_connectionHandler) return false;
			
			if (!mssql_select_db($authOptions['database'], $this->_connectionHandler)) return false;
			
			return $this->_connectionHandler;
		}
		
		public function getError()
		{
			return mssql_get_last_message();
		}
		
		public function close()
		{
			if (isset($this->_connectionHandler) && $this->_connectionHandler)
			{
				if (mssql_close($this->_connectionHandler))
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
			
			return @mssql_query($query, $this->_connectionHandler);
		}
		
		public function lastId()
		{
			if ($row = mssql_fetch_array(mssql_query("SELECT @@IDENTITY AS id"))) return $row['id'];
			
			return 0;
		}
		
		public function numRows($queryHandler)
		{
			return mssql_num_rows($queryHandler);
		}
		
		public function affectedRows($queryHandler)
		{
			return mssql_rows_affected($queryHandler);
		}
		
		public function dataSeek($queryHandler, $pointer)
		{
			return mssql_data_seek($queryHandler, $pointer);
		}
		
		public function fetchRow($queryHandler)
		{
			return mssql_fetch_row($queryHandler);
		}
		
		public function fetchArray($queryHandler)
		{
			return mssql_fetch_array($queryHandler);
		}
		
		public function clear($queryHandler)
		{
			return mssql_free_result($queryHandler);
		}
	}
?>