<?php
	namespace kit\template;
	
	use kit\singletonTrait;
	use Exception;
	
	class parser
	{
		use singletonTrait;
		
		const STATE_PARSE_ERROR = 0;
		
		const STATE_BEGIN = 1;
		
		const STATE_VAR = 2;
		
		const STATE_VALUE = 3;
		
		const STATE_AFTER_VAR = 4;
		
		const STATE_AFTER_OPERATOR = 5;
		
		private $state = self::STATE_BEGIN;
		
		private $len;
		
		private $str;
		
		private $brackets = 0;
		
		private $i;
		
		private $operators = Array('===', 'and', '--', '++', '==', '>=', '<=', 'or', '&&', '||', '<', '>', '&', '^', '~', '|', '+', '-');
		
		private $_error_line;
		
		// private $operators_concat = Array('===', 'and', '==', '>=', '<=', 'or', '&&', '||', '<', '>', '&', '^', '~', '|');
		
		// private $operators_comp = Array('===', '==', '>=', '<=', '<', '>', '&', '^', '~', '|');
		
		/*
		public function __construct($str)
		{
			
		}
		*/
		
		private function find($str)
		{
			$l = strlen($str);
			for($i = 0; $i < $l; $i++)
			{
				if(!isset($this->str[$this->i + $i]) || $str[$i] !== $this->str[$this->i + $i])
					return false;
			}
			
			return $str;
		}
		
		private function is_operator()
		{
			foreach($this->operators AS $op)
			{
				if($this->find($op))
				{
					$this->i += strlen($op) - 1;
					return $op;
				}
			}
			return false;
		}
		
		private function error()
		{
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			$this->trace = $trace[0];
			$this->state = self::STATE_PARSE_ERROR;
		}
		
		public function parse($template)
		{
			$parts = preg_split('/({[^\s].*?[^\\\]})/is', $template, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			
			ob_start();
			
			foreach($parts AS $part)
			{
				$l = strlen($part);
				
				if($part[0] === '{' && $part[$l-1] === '}')
				{
					// preg_match('/([a-z_-]+)(:[a-z0-9])?/i', substr($x,1,-1), $m);
					
					$part = substr($part,1,-1);
					
					$attr = $this->_parse($part);
					
					$_cmd = 'kit\\template\\'.$attr['_cmd'].'Command';
					$cmd = new $_cmd($attr);
					echo $cmd->run();
				}
				else
				{
					echo $part;
				}
			}
			
			return ob_get_clean();
		}
		
		public function _parse($str)
		{
			$this->str = $str;
			$this->len = strlen($str);
			
			// 'operators' => Array()
			$attr = Array();
			
			$_attr_name = null;
			$_attr_value = null;
			$_var = null;
			$_escaped = false;
			$_operators = Array();
			$_end = false;
			$_space = false;
			$_cmd = null;
			$_operand = null;
			
			// Loop through all characters
			for($this->i = 0; $this->i < $this->len; $this->i++)
			//while($this->i++ < $this->len)
			{
				// get character from 
				$c = $this->str[$this->i];
				
				// echo '[', $this->state, '] ', $c, "\n";
				
				switch($this->state)
				{
					case self::STATE_BEGIN :
						if(ctype_space($c))
						{
							if($_attr_name !== null || $_attr_value !== null)
							{
								if($_attr_value === null && $_cmd === null)
								{
									$_cmd = $_attr_name;
								}
								else
								{
									$attr[$_attr_name] = $_attr_value;	
								}
								$_attr_name = null;
								$_attr_value = null;
							}
							
							continue;
						}
						
						if($op = $this->is_operator())
						{
							// @TODO Extract operators from lower levels in here.
							
							// $_operators[>= $this->brackets] => save somewhere else
							
							/*
							$i = $this->brackets+1;
							do
							{
								if(!isset($_operators[$i]))
								{
									break;	
								}
								$attr['operators'][] = $_operators[$i];
								$i++;
							}
							while(true);
							*/
							
							$attr['operators'][] = $_operators;
							$attr['operators'][] = $op;
							
							if($_attr_name === null)
							{
								$this->state = self::STATE_AFTER_OPERATOR;
							}
							
							
							// $_operators[$this->brackets][] = Array('op' => $op);
							$_operators = Array();
							continue;
						}
						
						//echo '[', $this->state, '] ', $c, "\n";
						
						if(!ctype_alnum($c) && $c !== '-' && $c !== '_') 
						{
							if($c === '\\')
							{
								if($_escaped)
								{
									
								}
								else
								{
									$_escaped = true;
								}
							}
							/*
							elseif(ctype_space($c) && ($_attr_name !== null || $_attr_value !== null))
							{
								// new attribute
								
								$attr[$_attr_name] = $_attr_value;
								$_attr_name = null;
								$_attr_value = null;
							}
							*/
							elseif($c === '$')
							{
								$this->state = self::STATE_VAR;
							}
							elseif($c === '.')
							{
								
							}
							elseif($op = $this->is_operator())
							{
								
							}
							elseif($c === '=')
							{
								$this->state = self::STATE_VALUE;
							}
							elseif($c === '(')
							{
								$this->brackets++;
								continue;
							}
							elseif($c === ')')
							{
								if($_var !== null)
								{
									$_operators[$this->brackets][] = Array('var' => $_var);
									$_var = null;
								}
								
								$this->brackets--;
								continue;
							}
							elseif($c === ':')
							{
								
							}
							elseif($c === '/')
							{
								$_end = true;
							}
							elseif($c === '"' || $c === "'")
							{
								$this->state = self::STATE_VALUE;
							}
							else
							{
								$this->error();
								break 2;
							}
							
							continue;
						}
						
						$_attr_name .= $c;
					break;
					
					/**
					 * STATE_VAR
					 */
					
					case self::STATE_VAR :
						/* $_attr_name === null && */
						if(ctype_space($c) && $_var === null) // ignores whitespaces but only in front of the var
						{
							$_space = true;
							continue;
						}
						
						if(!ctype_alnum($c) && $c !== '_')
						{
							if(ctype_space($c))
							{
								$_space = true;
								$_operators[$this->brackets][] = Array('var' => $_var);
								$_var = null;
								$this->state = self::STATE_AFTER_VAR;
							}
							elseif($op = $this->is_operator())
							{
								if(!$_space && ($op == '--' || $op == '++'))
								{
									$_operators[$this->brackets][] = Array('var' => $_var, 'op' => $op);
									
									$this->state = self::STATE_BEGIN;
								}
								else
								{
									
									$_operators[$this->brackets][] = Array('var' => $_var);
									$_operators[$this->brackets][] = Array('op' => $op);
									$this->state = self::STATE_AFTER_OPERATOR;
								}
								
								$_var = null;
							}
							else
							{
								$this->i--;
								$this->state = self::STATE_BEGIN;
								// $this->error();
							}
							
							continue;
						}
						elseif($op = $this->is_operator())
						{
							$_operators[$this->brackets][] = Array('var' => $_var);
							$_operators[$this->brackets][] = Array('op' => $op);
							$_var = null;
							$this->state = self::STATE_AFTER_OPERATOR;
							continue;
						}
						
						$_var .= $c;
					break;
					
					/**
					 * STATE_AFTER_VAR
					 */
					
					case self::STATE_AFTER_VAR :
						// skip unneeded spaces
						if(ctype_space($c))
						{
							continue;
						}
						
						if($op = $this->is_operator())
						{
							$this->state = self::STATE_AFTER_OPERATOR;
							$_operators[$this->brackets][] = Array('op' => $op);
							continue;
						}
						
						$this->state = self::STATE_PARSE_ERROR;
					break;
					
					/**
					 * STATE_AFTER_OPERATOR
					 */
					
					case self::STATE_AFTER_OPERATOR :
						if(ctype_space($c))
						{
							continue;
						}
						
						if($c === '$')
						{
							$this->state = self::STATE_VAR;
							
							continue;
						}
						elseif($c === '(')
						{
							$this->i--;
							$this->state = self::STATE_BEGIN;
							
							continue;
						}
						
						/*
						elseif()
						{
							$this->state = self::STATE_BEGIN;
						}
						else
						{
							$this->error();
						}
						*/
						
						$_operand .= $c;
					break;
					
					/**
					 * STATE_VALUE
					 */
					
					case self::STATE_VALUE :
						if($c === '"' || $c === "'")
						{
							continue;
						}
						
						if(ctype_space($c))
						{
							$this->state = self::STATE_BEGIN;
							$attr[$_attr_name] = $_attr_value;
							$_attr_name = null;
							$_attr_value = null;
							continue;
						}
						
						if($c === '$')
						{
							$this->state = self::STATE_VAR;
							continue;
						}
						
						$_attr_value .= $c;
					break;
					default :
						break 2;
					break;
				}
				
				$_space = false;
			}
			
			/**
			 * The last state has to be checked here again
			 */
		 	
			switch($this->state)
			{
				case self::STATE_BEGIN :
				case self::STATE_VALUE :
					if($_attr_name !== null || $_attr_value !== null)
					{
						if($_attr_value === null && $_cmd === null)
						{
							$_cmd = $_attr_name;
						}
						elseif($_attr_name === null)
						{
							$attr[] = $_attr_value;
						}
						else
						{
							$attr[$_attr_name] = $_attr_value;
						}						
					}
				break;
				case self::STATE_VAR :
					if($_attr_name !== null)
					{
						$attr[$_attr_name] = Array('var' => $_var);
					}
					elseif(!count($_operators))
					{
						$attr['var'] = $_var;
					}
					else
					{	
						$_operators[$this->brackets][] = Array('var' => $_var);
					}
				break;
				case self::STATE_AFTER_OPERATOR :
					if(ctype_digit($_operand))
					{
						$_operators[$this->brackets][] = Array('number' => $_operand);
					}
					else
					{
						$_operators[$this->brackets][] = Array('value' => $_operand);
					}
					
				break;
				default :
					throw new Exception('Parse ERROR at offset '.$this->i.' at "'.substr($this->str, $this->i).'" in state '.$this->state.' in line '.$this->trace['line']);
				break;
			}
			
			if(count($_operators))
			{
				$attr['operators'][] = $_operators;
			}
			
			if($_end)
			{
				$attr['_end'] = $_end;
			}
			
			if($_cmd !== null)
			{
				$attr['_cmd'] = $_cmd;
			}
			
			//echo '<div style="margin-left: 15px;"><b>$_operators:</b> ', print_r($_operators, true), '</div>';
			
			return $attr;
		}
	}
?>