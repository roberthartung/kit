<?php
	namespace kit\template;
	
	class elseCommand
	{
		private $attr;
		
		public function __construct(array $attr, $file_name, $file_path)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			$code .= ' } else { '; 
			
			return $code.' ?>';
		}
	}
?>