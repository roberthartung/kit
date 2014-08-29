<?php
	namespace kit\template;
	
	use Exception;
	use kit\template;
	
	//  extends \kit\base\template\includeCommand
	class includeCommand
	{
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
			
			if(!isset($this->attr[0]) && !isset($this->attr['_expression'][0]['var']))
			{
				throw new Exception('No path given for include command. Aborting.');
			}
			
			
			$additional_vars = Array();
			//  && isset($this->data[0])
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
			
			$path = addslashes($path);
			
			$code .= " \$tpl = new kit\\template(\"".$path.(isset($this->attr[0]) ? $this->attr[0] : '".$'.$this->attr['_expression'][0]['var'].'."' ).".tpl\"); echo \$tpl->get(\$data, Array(".implode(',', $additional_vars).")); unset(\$tpl); ";
			
			/*
			$template = new template();
			echo $template->get();
			*/
			
			return $code.' ?>';
		}
	}
?>