<?php
	namespace kit;
	
	function urlify($str)
	{
		// , 
		return preg_replace(Array('/\s/i', '/-{2,}/i', '/[^a-z0-9-]/i'), Array('-', '-', ''), $str);
	}
?>