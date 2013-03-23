<?php
	namespace kit\template;
	
	use Exception;
	use kit\loaderTrait;
	use kit\template;
	
	class includeCommand
	{
		use loaderTrait {	__construct as loader;}
		
		private $attr;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '';
			
			if(!isset($this->attr[0]))
			{
				throw new Exception;
			}
			
			$template = new template('tpl/'.$this->attr[0].'.tpl');
			echo $template->get();
			
			return $code;
		}
	}
?>