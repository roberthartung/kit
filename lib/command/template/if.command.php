<?php
	namespace kit\template;
	
	class ifCommand
	{
		protected $attr;
		
		public function __construct(array $attr, $file_name, $file_path)
		{
			$this->attr = $attr;
		}
		
		protected function expressionToString(array $expr)
		{
			$expr_str = '';
			
			foreach($expr AS $exp)
			{
				if(isset($exp['function_name'])) {
					$expr_str .= $exp['function_name'];
				}
				elseif(isset($exp['op']))
				{
					$expr_str .= ' '.$exp['op'].' ';
				}
				elseif(isset($exp['var']))
				{
					if(isset($exp['type']))
					{
						switch($exp['type'])
						{
							case 'object' :
								$expr_str .= '->'.$exp['var'];
							break;
							case 'array' :
								$expr_str .= '[\''.$exp['var'].'\']';
							break;
						}
					}
					else
					{
						$expr_str .= '$'.$exp['var'].'';
					}
				}
				elseif(isset($exp['number']))
				{
					$expr_str .= $exp['number'];
				}
				elseif(isset($exp['string']))
				{
					$expr_str .= '"'.$exp['string'].'"';
				}
				else
				{
					$has_sub = false;
					foreach($exp AS $_exp)
					{
						if(isset($_exp['op']) || isset($_exp['var']) || isset($_exp['number']) || isset($_exp['string']))
							continue;
						
						$has_sub = true;
					}
					
					if($has_sub)
					{
						$expr_str .= '('.$this->expressionToString($exp).')';
					}
					else
					{
						$expr_str .= $this->expressionToString($exp);
					}
				}
			}
			
			return $expr_str;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			if(array_key_exists('_end', $this->attr))
			{
				$code .= ' } '; 
			}
			elseif(isset($this->attr['_expression']))
			{
				$code .= 'if('.$this->expressionToString($this->attr['_expression']).') { ';
			}
			
			return $code.' ?>';
		}
	}
?>