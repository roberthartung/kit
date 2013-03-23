<?php
	namespace kit;
	
	use kit\controller\controllerInterface;
	
	abstract class controller implements controllerInterface
	{
		abstract function run();
	}
?>