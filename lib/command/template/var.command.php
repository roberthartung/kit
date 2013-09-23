<?php
	namespace kit\template;
	
	class varCommand
	{
		private $attr;
		
		private $data;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '<?php echo $';
			
			if(is_array($this->attr['_var']))
			{
				$code .= $this->attr['_var']['var'];
				//var_dump($this->attr['var']);
				// $code .= .''; // end of direct data from $template->data
				
				// add additional parts
				$cnt = count($this->attr['_var']) - 1;
				for($a=0;$a<$cnt;$a++)
				{
					switch($this->attr['_var'][$a]['type'])
					{
						case 'object' :
							$code .= '->'.$this->attr['_var'][$a]['var'];
						break;
						case 'array' :
							if(ctype_digit($this->attr['_var'][$a]['var']))
							{
								$code .= '['.$this->attr['_var'][$a]['var'].']';
							}
							else
							{
								$code .= '[\''.$this->attr['_var'][$a]['var'].'\']';
							}
						break;
					}
				}
			}
			else
			{
				$code .= $this->attr['_var'];
			}
			
			return $code.'; ?>';
		}
	}
?>