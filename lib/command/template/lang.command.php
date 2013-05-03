<?php
	namespace kit\template;
	
	class langCommand
	{
		private $attr;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			if(!isset($this->attr['_identifier']))
			{
				throw new Exception('Identifier exprected');
			}
			
			$code .= ' $lang->get(\''.$this->attr['_identifier'].'\'); ';
			
			return $code.' ?>';
		}
	}
?>