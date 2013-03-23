<?php
	namespace kit;
	
	trait singletonloaderTrait
	{
		use singletonTrait, loaderTrait { loaderTrait::__construct insteadof singletonTrait; loaderTrait::__construct as loader; }
		
		protected function __construct()
		{
			$this->loader();
		}
	}
?>