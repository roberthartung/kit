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
			
			$additional_vars = Array();
			if(isset($this->attr['_expression']))
			{
				foreach($this->attr['_expression'] AS $exp)
				{
					if(isset($exp['var']))
					{
						$additional_vars[] = "'".$exp['var']."' => $".$exp['var'];
					}
				}
			}
			
			$path = $this->file_path;
			if(array_key_exists('root', $this->attr))
			{
				$path = 'tpl'.DIRECTORY_SEPARATOR;
			}
			
			$code .= " echo (new kit\\template('".$path.$this->attr[0].'.tpl'."'))->get(\$data, Array(".implode(',', $additional_vars)."));";
			
			/*
			$template = new template();
			echo $template->get();
			*/
			
			return $code.' ?>';
		}
	}
?>