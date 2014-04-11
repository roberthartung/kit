<?php
	namespace kit\template;
	
	class loopCommand
	{
		private $attr;
		
		private $data;
		
		public function __construct(array $attr)
		{
			$this->attr = $attr;
		}
		
		public function run()
		{
			$code = '<?php ';
			
			//var_dump($this->attr);
			
			if(array_key_exists('_end', $this->attr))
			{
				$code .= ' } ';
			}
			else
			{
				if(array_key_exists('from', $this->attr))
				{
					//$code .= ' var_dump($this->data[\''.$this->attr['from'].'\']); ';
					
					// Var?
					if(is_array($this->attr['from'])) {
						$from = '';
						foreach($this->attr['from'] AS $exp)
						{
							if(isset($exp['var']))
							{
								if(isset($exp['type']))
								{
									switch($exp['type'])
									{
										case 'object' :
											$from .= '->'.$exp['var'];
										break;
										case 'array' :
											$from .= '[\''.$exp['var'].'\']';
										break;
									}
								}
								else
								{
									$from .= '$'.$exp['var'].'';
								}
							}
						}
					} else {
						$from = '$'.$this->attr['from'];
					}
					
					$as = array_key_exists('as', $this->attr) ? $this->attr['as'] : 'row';
				  
					$code .= ' foreach('.$from.' AS $__key => $'.$as.') {';
				}
			}
			
			return $code.' ?>';
		}
	}
?>