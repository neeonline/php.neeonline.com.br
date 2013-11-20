<?
	require_once('dynamic.neeonline.class.php');
	
	class NeeonlineDatabaseConnectionClass
	{
		private $_authOptions;
		
		public $serverHandler;
		
		public function __construct($authOptions, $serverType = 'MySQL')
		{
			if ($serverType === null || $serverType == '' || !is_string($serverType)) trigger_error("NeeonlineDatabaseConnectionClass::__construct error: invalid serverType param.", E_USER_ERROR);
			if ($authOptions === null || !is_array($authOptions) || !sizeof($authOptions)) trigger_error("NeeonlineDatabaseConnectionClass::__construct error: invalid authOptions param.", E_USER_ERROR);
			
			$serverType = strtolower($serverType);
			$serverFile = __DIR__ . '/database/' . strtolower($serverType) . '.neeonline.database.php';
			
			if (!file_exists($serverFile)) trigger_error("NeeonlineDatabaseConnectionClass::__construct error: server file not found.", E_USER_ERROR);
			
			require_once($serverFile);
			
			$className				= 'Neeonline' . strtoupper($serverType) . 'Database';
			$this->serverHandler	= new $className;
			$this->_authOptions		= $authOptions;
		}
		
		public function connect()
		{
			if ($this->serverHandler->isConnected()) return true;
			
			if (!$this->serverHandler->connect($this->_authOptions)) trigger_error("NeeonlineDatabaseConnectionClass::connect error: can't connect to the database server. Server error: " . $this->serverHandler->getError(), E_USER_ERROR);
			
			return true;
		}
		
		public function close()
		{
			if ($this->serverHandler->isConnected()) return $this->serverHandler->close();
			
			return false;
		}
		
		public function execute($query)
		{
			if (!$this->serverHandler->isConnected()) $this->connect();
			
			return $this->serverHandler->execute($query);
		}
	}
	
	class NeeonlineDatabaseClass extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_authOptions;
		private $_serverType;
		
		private $_databaseConnection;
		private $_queryHandler;

		private $_pointer;		
		private $_currentRow;

		public $pageSize;
		public $currentPage;
		public $totalPages;
		public $recordCount;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->_classFolder = __DIR__ . '/database';
			$this->_classType	= 'database';
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
		
		public function newInstance($authOptions = null, $serverType = null, $pageSize = null)
		{
			if (($authOptions == null && !isset($this->_authOptions)) || ($serverType == null && !isset($this->_serverType))) return null;
			
			$newSelf = new self();
			$newSelf->connect($this->_authOptions, ($pageSize !== null) ? $pageSize : $this->pageSize, $this->_serverType);
			
			return $newSelf;
		}
		
		public function connect($authOptions, $pageSize = 0, $serverType = 'MySQL')
		{
			if (isset($this->_databaseConnection)) return true;
			
			if ($serverType === null || $serverType == '' || !is_string($serverType)) trigger_error("NeeonlineDatabaseClass::__construct error: invalid serverType param.", E_USER_ERROR);
			if ($authOptions === null || !is_array($authOptions) || !sizeof($authOptions)) trigger_error("NeeonlineDatabaseClass::__construct error: invalid authOptions param.", E_USER_ERROR);
			
			$this->_authOptions	= $authOptions;
			$this->_serverType	= $serverType;
			
			$this->_databaseConnection = new NeeonlineDatabaseConnectionClass($authOptions, $serverType);
			$this->_databaseConnection->connect();
			
			$this->pageSize = $pageSize;
			
			$this->_reset();
		}
		
		public function close()
		{
			if (!isset($this->_databaseConnection)) return null;
			
			return $this->_databaseConnection->close();
		}
		
		private function _reset()
		{
			unset($this->_queryHandler);
			
			$this->_pointer		= -1;
			$this->_currentRow	= array();
			
			$this->currentPage	= ($this->pageSize) ? 1 : 0;
			$this->totalPages	= 0;
			$this->recordCount	= 0;
		}
		
		public function execute($query)
		{
			if (!isset($this->_databaseConnection)) return null;
			
			$this->_reset();
			
			if ($this->_queryHandler = $this->_databaseConnection->execute($query))
			{
				$this->recordCount = $this->_databaseConnection->serverHandler->numRows($this->_queryHandler);
				
				if (!$this->recordCount) return false;
				
				if ($this->recordCount > $this->pageSize && $this->pageSize > 0) $this->totalPages = ceil($this->recordCount / $this->pageSize);
				
				$this->_fetchRow();
				
				return true;
			}
			
			return false;
		}
		
		private function _fetchRow($row = -1)
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			$this->_currentRow = array();
			
			$this->_pointer = ($row > -1) ? $row : $this->_pointer + 1;
			
			if ($this->pageSize) $this->_pointer = min(max(0, $this->_pointer), ($this->currentPage * $this->pageSize));
			
			if (!$this->recordCount || $this->_pointer >= $this->recordCount) return false;
			
			$this->_databaseConnection->serverHandler->dataSeek($this->_queryHandler, $this->_pointer);
			
			$this->_currentRow = $this->_databaseConnection->serverHandler->fetchArray($this->_queryHandler);
			
			return true;
		}
		
		public function item($key)
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (isset($this->_currentRow[$key])) return $this->_currentRow[$key];
			
			return '';
		}
		
		public function itens()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			$returnValue = Array() ;
			
			foreach($this->_currentRow as $key => $value) if (!is_numeric($key)) $returnValue[$key] = $value;
			
			return $returnValue;
		}
		
		public function move_next()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			$nextItem = $this->_pointer + 1;
			
			if ($this->pageSize && $nextItem >= ($this->currentPage * $this->pageSize)) return false;
			
			return $this->_fetchRow($nextItem);
		}
		
		public function move_previous()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if ($this->_pointer == 0) return false;
			
			return $this->_fetchRow($this->_pointer - 1);
		}
		
		public function move_first()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			return $this->_fetchRow(0);
		}
		
		public function move_last()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			return $this->_fetchRow($this->record_count - 1);
		}
		
		public function move_to($row)
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!is_numeric($row) || $row < 0 || $row >= $this->recordCount) return false;
			
			return $this->_fetchRow($row);
		}
		
		public function next_page()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!$this->pageSize) return false;
			
			$nextPage = $this->currentPage + 1;
			
			if ($nextPage > $this->totalPages) return false;
			
			$this->currentPage = $nextPage;
			
			return $this->_fetchRow(($this->currentPage - 1) * $this->pageSize);
		}
		
		public function previous_page()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!$this->pageSize || $this->currentPage == 1) return false;
			
			$previousPage = $this->currentPage - 1;
			
			if ($previousPage <= 0) return false;
			
			$this->currentPage = $previousPage;
			
			return $this->_fetchRow(($this->currentPage - 1) * $this->pageSize);
		}
		
		public function first_page()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!$this->pageSize) return false;
			
			$this->currentPage = 1;
			
			return $this->_fetchRow(($this->currentPage - 1) * $this->pageSize);
		}
		
		public function last_page()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!$this->pageSize) return false;
			
			$this->currentPage = $this->totalPages;
			
			return $this->_fetchRow(($this->currentPage - 1) * $this->pageSize);
		}
		
		public function set_page($page)
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return null;
			
			if (!$this->pageSize || !is_numeric($page) || $page < 1 || $page > $this->totalPages) return false;
			
			$this->currentPage = $page;
			
			return $this->_fetchRow(($this->currentPage - 1) * $this->pageSize);
		}
		
		public function clear()
		{
			if (!isset($this->_databaseConnection)) return null;
			
			if (!$this->recordCount) return false;
			
			$this->_databaseConnection->serverHandler->clear($this->_queryHandler);
		}
		
		public function get_pointer()
		{
			if (!isset($this->_databaseConnection) || !$this->recordCount) return 0;
			
			return $this->_pointer + 1;
		}
		
		public function get_error()
		{
			if (!isset($this->_databaseConnection)) return null;
			
			return $this->_databaseConnection->serverHandler->getError();
		}
		
		public function get_handler()
		{
			if (!isset($this->_queryHandler)) return null;
			
			return $this->_queryHandler;
		}
	}
?>