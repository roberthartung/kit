<?php
	require_once('/path/to/kit/inc/core.inc.php');
	
	// register URLs here <path>, <controller>
	$kit->registerUrl('/', 'index');
	
	require_once(PATH_KIT_ROOT.'inc/setup.inc.php');
	
	$kit->run();
?>