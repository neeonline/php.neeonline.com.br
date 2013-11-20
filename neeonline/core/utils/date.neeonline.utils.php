<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineDateUtils extends NeeonlineDynamicClass
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
		
		public function getPreviousWeek($amount = 1, $live = false)
		{
			if ($live)
			{
				$end	= strtotime('now');
				$start	= strtotime('-' . (($amount * 7) - 1) . ' day', $end);
			}
			else
			{
				$start = strtotime('last week');
				
				if ($amount > 1) $start = strtotime('-' . ($amount - 1) . ' week', $start);
				
				$end = strtotime('+' . $amount . ' week', $start);
			}
			
			return (object) array(
				'start'	=> $start,
				'end'	=> $end
			);
		}
		
		public function getPreviousMonth($amount = 1, $live = false)
		{
			if ($live)
			{
				$end	= strtotime('now');
				$start	= strtotime('-' . $amount . ' month', $end);
			}
			else
			{
				$currentMonth	= date('n');
				$currentYear	= date('Y');
				
				$previousMonth	= $currentMonth - $amount;
				$previousYear	= $currentYear;
				
				if ($previousMonth <= 0)
				{
					$previousMonth	= 12 + $previousMonth;
					$previousYear	= $currentYear - 1;
				}
				
				$start = mktime(0, 0, 0, $previousMonth, 1, $previousYear);
				$end = strtotime('+' . $amount . ' month', $start);
			}
			
			return (object) array(
				'start'	=> $start,
				'end'	=> $end
			);
		}
		
		public function getPreviousYear($amount = 1, $live = false)
		{
			if ($live)
			{
				$end	= strtotime('now');
				$start	= strtotime('-' . $amount . ' year', $end);
			}
			else
			{
				$currentYear	= date('Y');
				$previousYear	= $currentYear - 1;
				
				$start	= mktime(0, 0, 0, 1, 1, $previousYear);
				$end	= strtotime('+' . $amount . ' year', $start);
			}
			
			return (object) array(
				'start'	=> $start,
				'end'	=> $end
			);
		}
		
		public function getWeekDays($weekNumber, $year, $startDate, $endDate, $dateStamp = 'd/m/Y', $useTimestamps = true)
		{
			if ($useTimestamps)
			{
				$startDate	= strtotime($startDate);
				$endDate	= strtotime($endDate);
			}
			
			// Get first and last day of the week
			$monday = strtotime($year . 'W' . str_pad($weekNumber, 2, 0, STR_PAD_LEFT));
			$sunday = strtotime($year . 'W' . str_pad($weekNumber, 2, 0, STR_PAD_LEFT) . '7');
			
			// Check if first day is later than start date
			if ($monday < $startDate) $monday = $startDate;
			
			// Check if last day is after than end date
			if ($sunday > $endDate) $sunday = $endDate;
			
			// Return date range string
			$monday = date($dateStamp, $monday);
			$sunday = date($dateStamp, $sunday);
			
			return ($monday == $sunday) ? $monday : $monday . ' - ' . $sunday;
		}
		
		public function createDayRangeObject($startDate, $amount, $dateStamp = 'd/m/Y', $useTimestamps = true)
		{
			if ($useTimestamps) $startDate = strtotime($startDate);
			
			$dateRange = array(
				date($dateStamp, $startDate) => 0
			);
			
			for ($i = 1; $i <= $amount; $i++)
			{
				$day = date($dateStamp, strtotime("+$i day", $startDate));
				
				$dateRange[$day] = 0;
			}
			
			return (object) $dateRange;
		}
		
		public function createWeekRangeObject($amount, $weekNumber, $year, $startDate, $endDate, $dateStamp = 'd/m/Y', $useTimestamps = true)
		{
			if ($useTimestamps)
			{
				$startDate	= strtotime($startDate);
				$endDate	= strtotime($endDate);
			}
			
			$dateRange = array();
			
			$currentWeek = $weekNumber - 1;
			$currentYear = $year;
			
			for ($i = 0; $i < $amount; $i++)
			{
				$currentWeek++;
				
				if ($currentWeek > 52)
				{
					$currentWeek = 1;
					$currentYear++;
				}
				
				$week = $this->getWeekDays($currentWeek, $currentYear, $startDate, $endDate, $dateStamp, false);
				
				$dateRange[$week] = 0;
			}
			
			return $dateRange;
		}
		
		public function createMonthRangeObject($amount, $startMonth, $startYear)
		{
			$dateRange = array();
			
			$currentMonth	= $startMonth;
			$currentYear	= $startYear;
			
			for ($i = 0; $i < $amount; $i++)
			{
				if (sizeof($currentMonth) == 1) $currentMonth = '0' . $currentMonth;
				
				$dateRange[$currentMonth . '/' . $currentYear] = 0;
				
				$currentMonth++;
				
				if ($currentMonth > 12)
				{
					$currentMonth = 1;
					$currentYear++;
				}
			}
			
			return $dateRange;
		}
		
		function createYearRangeObject($amount, $startYear)
		{
			$dateRange = array();
			
			for ($i = 0; $i < $amount; $i++)
			{
				$dateRange[$startYear + $i] = 0;
			}
			
			return $dateRange;
		}
		
		/**
		* Interval options:
		*	yyyy - Number of full years
		*	q - Number of full quarters
		*	m - Number of full months
		*	mm - Number of months
		*	y - Difference between day numbers (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		*	d - Number of full days
		*	w - Number of full weekdays
		*	ww - Number of full weeks
		*	h - Number of full hours
		*	n - Number of full minutes
		*	s - Number of full seconds (default)
		*/ 
		public function calculateDateDiff($interval, $startDate, $endDate, $useTimestamps = true)
		{
			if ($useTimestamps)
			{
				$startDate	= strtotime($startDate);
				$endDate	= strtotime($endDate);
			}
			
			$difference = $endDate - $startDate; // Difference in seconds
			
			switch($interval)
			{
				case 'yyyy': // Number of full years
					$years_difference = floor($difference / 31536000);
					
					if (mktime(date("H", $startDate), date("i", $startDate), date("s", $startDate), date("n", $startDate), date("j", $startDate), date("Y", $startDate) + $years_difference) > $endDate) $years_difference--;
						
					if (mktime(date("H", $endDate), date("i", $endDate), date("s", $endDate), date("n", $endDate), date("j", $endDate), date("Y", $endDate) - ($years_difference + 1)) > $startDate) $years_difference++;
						
					$datediff = $years_difference;
					break;
				case "q": // Number of full quarters
					$quarters_difference = floor($difference / 8035200);
					
					while (mktime(date("H", $startDate), date("i", $startDate), date("s", $startDate), date("n", $startDate) + ($quarters_difference * 3), date("j", $startDate), date("Y", $startDate)) < $endDate) $quarters_difference++;
					
					$datediff = $quarters_difference - 1;
					break;
				case "m": // Number of full months
					$months_difference = floor($difference / 2678400);
					
					while (mktime(date("H", $startDate), date("i", $startDate), date("s", $startDate), date("n", $startDate) + ($months_difference), date("j", $startDate), date("Y", $startDate)) < $endDate) $months_difference++;
					
					$datediff = $months_difference; // -1
					break;
				case "mm":
					$datediff = ceil($difference / 2678400);
					break;
				case 'y': // Difference between day numbers
					$datediff = date("z", $endDate) - date("z", $endDate);
					break;
				case "d": // Number of full days
					$datediff = floor($difference / 86400);
					break;
				case "w": // Number of full weekdays
					$days_difference = floor($difference / 86400);
					$weeks_difference = floor($days_difference / 7); // Complete weeks
					$first_day = date("w", $endDate);
					$days_remainder = floor($days_difference % 7);
					$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
					
					if ($odd_days > 7) $days_remainder--; // Sunday
					
					if ($odd_days > 6) $days_remainder--; // Saturday
					
					$datediff = ($weeks_difference * 5) + $days_remainder;
					break;
				case "ww": // Number of full weeks
					$datediff = ceil($difference / 604800);
					break;
				case "h": // Number of full hours
					$datediff = floor($difference / 3600);
					break;
				case "n": // Number of full minutes
					$datediff = floor($difference / 60);
					break;
				default: // Number of full seconds (default)
					$datediff = $difference;
					break;
			}
			
			return $datediff;
		}
	}
?>