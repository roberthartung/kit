<?php
	namespace kit;
	
	class indexView
	{
		use loaderTrait { __construct as loader; }
		use singletonTrait;
		
		protected function __construct()
		{
			$this->loader();
		}
		
		public function run()
		{
			$template = new template('tpl/index.tpl');
			
			echo $template->get();
		}
	}
?>