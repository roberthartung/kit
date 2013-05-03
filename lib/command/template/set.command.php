<?php
	namespace kit\template;
	
	class setCommand
	{
		private $attr;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			$var_name = null;
			$after_op = false;
			$var_value = null;
			
			foreach($this->attr['_expression'] AS $exp)
			{
				if(isset($exp['var']))
				{
					if(isset($exp['type']))
					{
						switch($exp['type'])
						{
							case 'object' :
								$code .= '->'.$exp['var'];
							break;
						}
					}
					else
					{
						if($var_name === null)
						{
							$var_name .= $exp['var'];
						}
						$code .= '$'.$exp['var'];
					}
				}
				elseif(isset($exp['op']))
				{
					$after_op = true;
					$code .= ' '.$exp['op'].' ';
				}
				elseif(isset($exp['string']))
				{
					$code .= ' "'.$exp['string'].'" ';
					
					if($after_op)
					{
						$var_value = $exp['string'];
					}
				}
			}
			
			$code .= '; $data[\''.$var_name.'\'] = "'.$var_value.'";';
			
			/*
			foreach($this->attr AS $k => $v)
			{
				if(in_array($k, Array('_cmd')))
					continue;
				
				$code .= ' $'.$k.' = \''.$v.'\'; $this->data[\''.$k.'\'] = \''.$v.'\';';
			}
			*/
			
			return $code.' ?>';
		}
	}
?>