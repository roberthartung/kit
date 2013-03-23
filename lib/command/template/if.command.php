<?php
	namespace kit\template;
	
	class ifCommand
	{
		private $attr;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function __toString()
		{
			$code = '';
			
			foreach($this->attr['operators'] AS $lvl => $operator)
			{
				var_dump($operator);
			}
			
			return $code;
		}
	}
?>