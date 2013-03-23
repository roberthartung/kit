<?php
	require_once('../../inc/core.inc.php');
	
	// register URLs here <path>, <controller>
	$kit->registerUrl('/', 'index');
	
	require_once(PATH_KIT_ROOT.'inc/run.inc.php');
?>