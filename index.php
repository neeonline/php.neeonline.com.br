<?
	require(__DIR__ . '/neeonline/core.neeonline.class.php');
	
	// echo Neeonline::getInstance()->Utils->Request->checkAll('test', 'NULL');
	// echo Neeonline::getInstance()->Utils->Url->filterUrlParameters(array('test')); // ?test&test2=2&test3=3
	// echo Neeonline::getInstance()->Utils->Security->createHash(64);
	// echo Neeonline::getInstance()->Utils->Validation->validateEmailAddress('achristian@amil.com.br');
	// echo Neeonline::getInstance()->Utils->Validation->validateUrlAddress('ftp://neeonline.co');
	// echo (Neeonline::getInstance()->Utils->Validation->validateDate('29/02/2013')) ? 'True' : 'False';
	// echo (Neeonline::getInstance()->Utils->Validation->validateTime('23:59')) ? 'True' : 'False';
	// echo Neeonline::getInstance()->Utils->Conversion->strDateTimeToTimestamp('08/08/1984 10:45');
	// echo Neeonline::getInstance()->Utils->Conversion->timestampToDatetime(Neeonline::getInstance()->Utils->ConversionUtils->strDateTimeToTimestamp('08/08/1984 10:45'));
	// echo Neeonline::getInstance()->Utils->Conversion->timestampToDatetime();
	// echo Neeonline::getInstance()->Utils->Conversion->datetimeToTimestamp('1984:08:08 10:45:00');
	// echo Neeonline::getInstance()->Utils->Conversion->strNumberToDatabase('(12) 8866-6862');
	// echo Neeonline::getInstance()->Utils->Conversion->strNumberToPHP('1288666862', '(00) 0000-0000');
	/* echo Neeonline::getInstance()->Utils->Conversion->strNumberToPHP('12988666862', '(00) 0000-0000', array(
		'129' => '(00) 00000-0000'
	));*/
	// echo Neeonline::getInstance()->Utils->Conversion->convertBytes(1099511627776);
	// echo Neeonline::getInstance()->Utils->String->clearString('Ação!!! AÇÃO 123456');
	// echo Neeonline::getInstance()->Utils->String->getFileExtension('foo/bar/file.ext');
	// echo Neeonline::getInstance()->Utils->Server->getOS();
	// echo Neeonline::getInstance()->Utils->Server->getBrowser();
	
	// Neeonline::getInstance()->I18N->load();
	// print_r(Neeonline::getInstance()->I18N->getLocations());
	// echo Neeonline::getInstance()->I18N->e('location_title');
	
	# MySQL
	/*Neeonline::getInstance()->Database->connect(array(
		'server'	=> 'localhost',
		'username'	=> 'localuser',
		'password'	=> 'localpass',
		'database'	=> 'localdatabase'
	), 30);
	Neeonline::getInstance()->Database->execute("SELECT * FROM users ORDER BY id");
	
	echo 'Page Size: ' . Neeonline::getInstance()->Database->pageSize;
	echo '<br />Current Page: ' . Neeonline::getInstance()->Database->currentPage;
	echo '<br />Total Pages: ' . Neeonline::getInstance()->Database->totalPages;
	echo '<br />Record Count: ' . Neeonline::getInstance()->Database->recordCount;
	
	echo '<br /><br /><b>Using For:</b>';
	
	for ($p = 0; $p < Neeonline::getInstance()->Database->totalPages; $p++)
	{
		echo '<br/><br/>Current Page: ' . Neeonline::getInstance()->Database->currentPage;
		
		for ($r = 0; $r < Neeonline::getInstance()->Database->pageSize; $r++)
		{
			echo '
				<ul>
					<li>Pointer Position: ' . Neeonline::getInstance()->Database->get_pointer() . '</li>
					<li>Page Position: ' . ($r + 1) . '</li>
					<li>ID: ' . Neeonline::getInstance()->Database->item('id') . '</li>
					<li>Username: ' . Neeonline::getInstance()->Database->item('username') . '</li>
				</ul>';
				
			if (!Neeonline::getInstance()->Database->move_next()) break;
		}
		
		if (!Neeonline::getInstance()->Database->next_page()) break;
	}
	
	echo '<br /><br /><b>Using While:</b>';
	
	$database = Neeonline::getInstance()->Database->newInstance();
	$database->execute("SELECT * FROM users ORDER BY id");
	
	$hasNextPage = true;
	
	while($hasNextPage === true)
	{
		echo '<br/><br/>Current Page: ' . $database->currentPage;
		
		$hasNextItem = true;
		$i = 1;
		
		while($hasNextItem === true)
		{
			echo '
				<ul>
					<li>Pointer Position: ' . $database->get_pointer() . '</li>
					<li>Page Position: ' . $i . '</li>
					<li>ID: ' . $database->item('id') . '</li>
					<li>Username: ' . $database->item('username') . '</li>
				</ul>';
			
			$i++;
			
			$hasNextItem = $database->move_next();
		}
		
		$hasNextPage = $database->next_page();
	}*/
	
	# MS SQL
	/*Neeonline::getInstance()->Database->connect(array(
		'server'	=> 'localhost',
		'username'	=> 'localuser',
		'password'	=> 'localpass',
		'database'	=> 'localdatabase'
	), 30, 'MSSQL');
	Neeonline::getInstance()->Database->execute("SELECT * FROM users ORDER BY id");
	
	echo 'Page Size: ' . Neeonline::getInstance()->Database->pageSize;
	echo '<br />Current Page: ' . Neeonline::getInstance()->Database->currentPage;
	echo '<br />Total Pages: ' . Neeonline::getInstance()->Database->totalPages;
	echo '<br />Record Count: ' . Neeonline::getInstance()->Database->recordCount;
	
	echo '<br /><br /><b>Using For:</b>';
	
	for ($p = 0; $p < Neeonline::getInstance()->Database->totalPages; $p++)
	{
		echo '<br/><br/>Current Page: ' . Neeonline::getInstance()->Database->currentPage;
		
		for ($r = 0; $r < Neeonline::getInstance()->Database->pageSize; $r++)
		{
			echo '
				<ul>
					<li>Pointer Position: ' . Neeonline::getInstance()->Database->get_pointer() . '</li>
					<li>Page Position: ' . ($r + 1) . '</li>
					<li>ID: ' . Neeonline::getInstance()->Database->item('id') . '</li>
					<li>Name: ' . Neeonline::getInstance()->Database->item('name') . '</li>
				</ul>';
				
			if (!Neeonline::getInstance()->Database->move_next()) break;
		}
		
		if (!Neeonline::getInstance()->Database->next_page()) break;
	}
	
	echo '<br /><br /><b>Using While:</b>';
	
	$database = Neeonline::getInstance()->Database->newInstance();
	$database->execute("SELECT * FROM users ORDER BY id");
	
	$hasNextPage = true;
	
	while($hasNextPage === true)
	{
		echo '<br/><br/>Current Page: ' . $database->currentPage;
		
		$hasNextItem = true;
		$i = 1;
		
		while($hasNextItem === true)
		{
			echo '
				<ul>
					<li>Pointer Position: ' . $database->get_pointer() . '</li>
					<li>Page Position: ' . $i . '</li>
					<li>ID: ' . $database->item('id') . '</li>
					<li>Name: ' . $database->item('name') . '</li>
				</ul>';
			
			$i++;
			
			$hasNextItem = $database->move_next();
		}
		
		$hasNextPage = $database->next_page();
	}*/
?>