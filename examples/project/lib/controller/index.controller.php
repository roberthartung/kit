<?php
	namespace kit;
	
	class indexController extends controller
	{
		use singletonLoaderTrait;
		
		public function run()
		{
			$view = indexView::getInstance();
			$this->kit->setView($view);
		}
	}
?>