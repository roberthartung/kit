<?php
	namespace kit;
	
	class indexController extends controller
	{
		use singletonTrait;
		use loaderTrait;
		
		public function run()
		{
			$view = indexView::getInstance();
			$this->kit->setView($view);
		}
	}
?>