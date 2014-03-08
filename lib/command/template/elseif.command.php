<?php
	namespace kit\template;
	
	class elseifCommand extends ifCommand
	{
		public function __construct(array $attr, $file_name, $file_path)
		{
			parent::__construct($attr, $file_name, $file_path);
		}
		
		public function run()
		{
			$code = '<?php ';
			
			if(isset($this->attr['_expression']))
			{
				$code .= '} elseif ('.$this->expressionToString($this->attr['_expression']).') { ';
			}
			
			return $code.' ?>';
		}
	}
?>