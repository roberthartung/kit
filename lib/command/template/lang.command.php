<?php
	namespace kit\template;
	
	use Exception;
	
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
			
			if(isset($this->attr['_identifier']))
			{
				$code .= ' echo $lang->get(\''.$this->attr['_identifier'].'\'); ';
			}
			if(isset($this->attr['_identifiers']))
			{
				$code .= ' echo $lang->get(\''.implode("','", $this->attr['_identifiers']).'\'); ';
			}
			else
			{
				throw new Exception('Identifier exprected');
			}
			
			
			
			return $code.' ?>';
		}
	}
?>