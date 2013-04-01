<?php
	namespace kit;
	
	class indexView extends view
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