<?php
	namespace kit\template;
	
	use Exception;
	use kit\loaderTrait;
	use kit\template;
	
	class includeCommand
	{
		use loaderTrait {	__construct as loader;}
		
		private $attr;
		
		private $file_path;
		
		private $file_name;
		
		public function __construct(array $attr, $file_path, $file_name)
		{
			$this->attr = $attr;
			$this->file_path = $file_path;
			$this->file_name = $file_name;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			if(!isset($this->attr[0]))
			{
				throw new Exception('No path given for include command. Aborting.');
			}
			
			$path = $this->file_path;
			if(array_key_exists('root', $this->attr))
			{
				$path = 'tpl'.DIRECTORY_SEPARATOR;
			}
			
			$code .= " (new kit\\template('".$path.$this->attr[0].'.tpl'."'))->get();";
			
			/*
			$template = new template();
			echo $template->get();
			*/
			
			return $code.' ?>';
		}
	}
?>