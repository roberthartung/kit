<?php
	namespace kit;
	
	use kit\controllerInterface;
	
	abstract class controller implements controllerInterface
	{
		abstract function run();
	}
?>