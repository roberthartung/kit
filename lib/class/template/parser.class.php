<?php
	namespace kit\template;
	
	use Exception;
	
	/**
	 * Internally used class for pasing bracket expressions
	 */
	
	class bracket_stack
	{
		private $parent;
		
		private $children;
		
		public function __construct($parent)
		{
			$this->parent = $parent;
		}
		
		public function add($child)
		{
			$this->children[] = $child;
		}
		
		public function getParent()
		{
			return $this->parent;
		}
		
		public function toArray()
		{
			$data = Array();
			foreach($this->children AS $child)
			{
				if($child instanceof self)
				{
					$data[] = $child->toArray();
				}
				else
				{
					$data[] = $child;
				}
			}
			
			return $data;
		}
	}
	
	class parser extends \kit\base\template\parser
	{
		const STATE_PARSE_ERROR = 0;
		
		const STATE_COMMAND_BEGIN = 1;
		
		const STATE_COMMAND = 2;
				
		const STATE_BEGIN = 3;
		
		const STATE_PARAMETER_STRING = 4;
		
		const STATE_EXPRESSION = 5;
		
		const STATE_EXPRESSION_VAR = 6;
		
		const STATE_EXPRESSION_AFTER_VAR = 7;
		
		const STATE_EXPRESSION_NUMBER = 8;
		
		const STATE_EXPRESSION_STRING = 9;
		
		const STATE_VALUE = 10;
		
		const STATE_VALUE_STRING = 11;
		
		const STATE_COMMAND_VAR = 12;
		
		const STATE_LANGUAGE = 13;
		
		const STATE_LANGUAGE_IDENTIFIER = 14;
		
		const STATE_LANGUAGE_AFTER_IDENTIFIER = 15;
		
		const STATE_VALUE_VAR = 16;
		
		//const STATE_EXPRESSION_AFTER_VAR = 7;
		
		//const STATE_EXPRESSION_AFTER_OPERATOR = 8;
		
		/*
		const STATE_BEGIN = 1;
		
		const STATE_VAR = 2;
		
		const STATE_VALUE = 3;
		
		const STATE_AFTER_VAR = 4;
		
		const STATE_AFTER_OPERATOR = 5;
		
		const STATE_VAR_AFTER_DOT_ANNOTATION = 6;
		
		const STATE_VAR_AFTER_OBJ_ANNOTATION = 7;
		*/
		
		
		
		private $state;
		
		private $len;
		
		private $str;
		
		private $brackets = 0;
		
		private $i;
		
		private $operators = Array('===', 'and', '!==', '--', '++', '==', '!=', '>=', '<=', 'or', '&&', '||', '<', '>', '&', '^', '~', '|', '+', '-');
		
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
		
		/**
		 * Find operator
		 * 
		 * @return boolean Operator found or false
		 */
		
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
		
		/**
		 * Throw error
		 */
		
		private function error()
		{
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			$this->trace = $trace[0];
			$this->_state = $this->state;
			$this->state = self::STATE_PARSE_ERROR;
		}
		
		/**
		 * Parses a template string
		 * 
		 * @param $template		Template Contents
		 * @param $file_path	File Path
		 * @param $file_name	File Name
		 * 
		 * return Parsed Template
		 */
		
		public function parse($template, $file_path, $file_name)
		{
			$parts = preg_split('/({[^\s].*?[^\\\]})/is', $template, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			
			//ob_start();
			$parsed = '';
			
			foreach($parts AS $part)
			{
				$l = strlen($part);
				
				if($part[0] === '{' && $part[$l-1] === '}')
				{
					// preg_match('/([a-z_-]+)(:[a-z0-9])?/i', substr($x,1,-1), $m);
					
					$part = substr($part,1,-1);
					
					$attr = $this->_parse($part);
					
					$_cmd = 'kit\\template\\'.$attr['_cmd'].'Command';
					$cmd = new $_cmd($attr, $file_path, $file_name);
					$parsed .= $cmd->run();
				}
				else
				{
					$parsed .= $part;
				}
			}
			
			
			return $parsed;
		}
		
		/**
		 * Command
		 */
		
		private $_cmd;
		
		private $_attr_name;
		
		private $_attr_value;
		
		private $_var;
		
		private $_operands;
		
		private $_operand;
		
		private $_expression;
		
		private $_expressions;
		
		private $_parameter;
		
		private $_escaped;
		
		private $_brackets;
		
		private $_end;
		
		private $_var_type;
		
		private $_op;
		
		private $_number;
		
		private $_string;
		
		private $attributes;
		
		private $_bracket_stack_top;
		
		private $_bracket_stack;
		
		private $_lang_identifier;
		
		private $debug = false;
		
		
		/*
		public function onClosingBracket()
		{
			//var_dump($this->_operands, $this->_operand);
			
			if($this->_brackets < 0)
			{
				$this->error();
				return false;
			}
			
			$this->_brackets--;
			
			if($this->_brackets === 0)
			{
				$this->_expression[] = $this->_operands;
				$this->state = self::STATE_BEGIN;
				$this->_operands = Array();
				return false;
			}
			
			$this->state = self::STATE_EXPRESSION;
			
			return true;
		}
		*/
		
		/**
		 * Switches the state
		 */
		
		private function switchState($state)
		{
			$this->state = $state;
			$this->i--;
		}
		
		/**
		 * Checks if the operator needs to be appended
		 */
		
		private function checkAppendOperator()
		{
			if($this->_bracket_stack_top === null)
			{
				if($this->_op !== null)
				{
					$this->_expression[] = Array('op' => $this->_op);
					$this->_op = null;
				}
				
				return;
			}
			
			if($this->_op !== null)
			{
				// $this->_expressions[$this->_brackets][] = Array('op' => $this->_op);
				$this->_expressions[] = Array('op' => $this->_op);
				$this->_op = null;
			}
		}
		
		/**
		 * Check if the operand needs to be appended
		 */
		
		private function checkAppendOperand($state = null)
		{
			if($state !== null)
			{
				$this->state = $state;
			}
			
			if($this->_bracket_stack === null)
			{
				$var = &$this->_expression;
			}
			else
			{
				$var = &$this->_expressions;
			}
			
			if($this->_var !== null)
			{
				if($this->_var_type !== null)
				{
					$var[] = Array('var' => $this->_var, 'type' => $this->_var_type);
					$this->_var_type = null;
				}
				else
				{
					$var[] = Array('var' => $this->_var);
				}
				$this->_var = null;
			}
			elseif($this->_number !== null)
			{
				$var[] = Array('number' => $this->_number);
				$this->_number = null;
			}
			elseif($this->_parameter !== null)
			{
				$var[] = Array('parameter' => $this->_parameter);
				$this->_parameter = null;
			}
			elseif($this->_string !== null)
			{
				$var[] = Array('string' => $this->_string);
				$this->_string = null;
			}
			
			
			
			/*
			if($this->_brackets === 0)
			{
				if($this->_var !== null)
				{
					$this->_expression[] = Array('var' => $this->_var);
					$this->_var = null;
				}
				elseif($this->_number !== null)
				{
					$this->_expression[] = Array('number' => $this->_number);
					$this->_number = null;
				}
				elseif($this->_parameter !== null)
				{
					$this->_expression[] = Array('parameter' => $this->_parameter);
					$this->_parameter = null;
				}
				elseif($this->_string !== null)
				{
					$this->_expression[] = Array('string' => $this->_string);
					$this->_string = null;
				}
				
				return;
			}
			
			
			if($this->_var !== null)
			{
				$this->_operands[$this->_brackets][] = Array('var' => $this->_var);
				$this->_var = null;
			}
			elseif($this->_number !== null)
			{
				$this->_operands[$this->_brackets][] = Array('number' => $this->_number);
				$this->_number = null;
			}
			elseif($this->_string !== null)
			{
				$this->_operands[$this->_brackets][] = Array('string' => $this->_string);
				$this->_string = null;
			}
			else
			{
				return;
			}
			
			if($state !== null)
			{
				$this->state = $state;
			}
			*/
		}
		
		/**
		 * Appends an attribute to the expression
		 */
		
		private function checkAppendAttribute()
		{
			if($this->_attr_name !== null)
			{
				if(count($this->_expression)) {
					$this->attributes[$this->_attr_name] = $this->_expression;
					$this->_expression = Array();
				} else {
					$this->attributes[$this->_attr_name] = $this->_attr_value;
				}
				
				$this->_attr_name = null;
				$this->_attr_value = null;
			}
		}
		
		/**
		 * Called at an opening bracket
		 */
		
		private function openingBracket()
		{
			if($this->_bracket_stack === null)
			{
				// current stack was done and we had a top stack before, then we're adding 
				$this->_bracket_stack_top = new bracket_stack(null);
				$this->_bracket_stack = $this->_bracket_stack_top;
				if($this->_attr_name != null) {
					// Append Function Name
					$this->_expression[] = Array('function_name' => $this->_attr_name);
					$this->_attr_name = null;
				}
			}
			else
			{
				if(count($this->_expressions))
				{
					$this->_bracket_stack->add($this->_expressions);
					$this->_expressions = Array();
				}
				
				$bracket_stack = new bracket_stack($this->_bracket_stack);
				$this->_bracket_stack->add($bracket_stack);
				$this->_bracket_stack = $bracket_stack;
			}
			
			//echo "\t", spl_object_hash($this->_bracket_stack);
		}
		
		/**
		 * Called at an closing bracket
		 */
		
		private function closingBracket()
		{
			//echo "\t", spl_object_hash($this->_bracket_stack);
			
			//echo 'closing: ';
			//var_dump($this->_bracket_stack); //  $this->_var, $this->_op
			
			if(count($this->_expressions))
			{
				$this->_bracket_stack->add($this->_expressions);
				$this->_expressions = Array();
			}
			
			if($this->_bracket_stack === null)
			{
				$this->error();
				return false;
			}
			
			$this->_bracket_stack = $this->_bracket_stack->getParent();
			
			if($this->_bracket_stack === null)
			{
				$this->_expression[] = $this->_bracket_stack_top->toArray();
				$this->_bracket_stack_top = null;
			}
			
			return true;
		}
		
		/**
		 * Appends the var
		 */
		
		private function checkAppendVar()
		{
			if(isset($this->attributes['_var']['var']))
			{
				$this->attributes['_var'][] = Array('type' => $this->_var_type, 'var' => $this->_var);
			}
			else
			{
				$this->attributes['_var']['var'] = $this->_var;
			}
			$this->_var = null;
		}
		
		/**
		 * Actual parsing of the string
		 * 
		 * @param $str			Template String
		 * 
		 * @return Parsed String
		 */
		
		public function _parse($str)
		{
			$this->str = $str;
			$this->len = strlen($str);
			$this->state = self::STATE_COMMAND_BEGIN;
			
			$this->attributes = Array();
			
			$this->_cmd = null;
			$this->_escaped = false;
			$this->_attr_name = null;
			$this->_attr_value = null;
			$this->_operand = null;
			$this->_operands = Array();
			$this->_expression = Array();
			$this->_expressions = Array();
			$this->_var = null;
			$this->_parameter = null;
			$this->_brackets = 0;
			$this->_end = false;
			$this->_op = null;
			$this->_number = null;
			$this->_var_type = null;
			$this->_string = null;
			$this->_bracket_stack = null;
			$this->_bracket_stack_top = null;
			$this->_lang_identifier = null;
			
			// Go to through all characters
			for($this->i = 0; $this->i < $this->len; $this->i++)
			{
				// get character from 
				$c = $this->str[$this->i];
				
				if($this->debug)
				{
					echo "\n", '[state: ', $this->state, '] ', $c, "\t";
				}
				
				switch($this->state)
				{
					// we ALWAYS want a command first
					case self::STATE_COMMAND_BEGIN :
						if(ctype_alpha($c) || $c === '_')
						{
							$this->state = self::STATE_COMMAND;
							$this->_cmd .= $c;
							continue;
						}
						
						if($c === '/')
						{
							$this->_end = true;
							continue;
						}
						
						if($c === '%')
						{
							$this->state = self::STATE_LANGUAGE;
							$this->_cmd = 'lang';
							continue;
						}
						
						if($c === '$')
						{
							$this->state = self::STATE_COMMAND_VAR;
							$this->_cmd = 'var';
							continue;
						}
						
						$this->error();
					break;
					case self::STATE_LANGUAGE :
						if(ctype_space($c))
						{
							$this->error();
							continue;
						}
						
						if($c === '.')
						{
							$this->attributes['_identifiers'][] = $this->_lang_identifier;
							$this->_lang_identifier = null;
							continue;
						}
						
						$this->_lang_identifier .= $c;
						
						/*
						if($c === '"')
						{
							$this->state = self::STATE_LANGUAGE_IDENTIFIER;	
						}
						*/
					break;
					/*
					case self::STATE_LANGUAGE_IDENTIFIER :
						if(ctype_space($c))
						{
							continue;
						}
						
						if($c === '"')
						{
							$this->attributes['_identifier'] = $this->_lang_identifier;
							$this->_lang_identifier = null;
							$this->state = self::STATE_LANGUAGE_AFTER_IDENTIFIER;
							continue;
						}
						
						$this->_lang_identifier .= $c;
					break;
					*/
					case self::STATE_COMMAND_VAR :
						if(ctype_space($c))
						{
							continue;
						}
						
						if($this->_var === null)
						{
							if(ctype_alpha($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						else
						{
							if($this->find('->'))
							{
								$this->i++;
								$this->_var_type = 'object';
								$this->checkAppendVar();
								continue;
							}
							elseif($c === '.')
							{
								$this->_var_type = 'array';
								$this->checkAppendVar();
								continue;
							}
							elseif(ctype_alnum($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						
						$this->error();
					break;
					
					/**
					 * STATE_COMMAND
					 */
					
					// we've successfully read the first character from the 
					case self::STATE_COMMAND :
						if(ctype_space($c)) // command done
						{
							$this->state = self::STATE_BEGIN;
							continue;
						}
						
						if(ctype_alnum($c) || $c === '_')
						{
							$this->_cmd .= $c;
							continue;
						}
						
						$this->error();
					break;
					
					/**
					 * ##### STATE_BEGIN
					 */
					
					// beginning of a new attribute / expression etc
					case self::STATE_BEGIN :
						if($c === '"')
						{
							$this->state = self::STATE_PARAMETER_STRING;
							continue;
						}
						
						if($c === '(')
						{
							$this->openingBracket();	
							$this->state = self::STATE_EXPRESSION;
							//$this->_brackets++;
							continue;
						}
						
						if($c === '$')
						{
							$this->switchState(self::STATE_EXPRESSION);
							continue;
						}
						
						if(ctype_space($c))
						{
							continue;
						}
						
						if($op = $this->is_operator())
						{
							// we got a expression for the resulting array
							if(count($this->_expression))
							{
								$this->_op = $op;
								$this->checkAppendOperator();
								$this->state = self::STATE_EXPRESSION;
								//$this->_expression[] = Array('op' => $op);
								continue;
							}
							
							//$this->_bracket_stack->add($this->_op);
						}
						
						if($this->_attr_name === null)
						{
							if(ctype_alpha($c) || $c === '_')
							{
								$this->_attr_name .= $c;
								continue;
							}
						}
						else
						{
							if(ctype_alnum($c) || $c === '_' || $c === '-')
							{
								$this->_attr_name .= $c;
								continue;
							}
						}
						
						if($c === '=')
						{
							$this->state = self::STATE_VALUE;
							continue;
						}
						
						$this->error();
					break;
					
					/**
					 * ##### STATE_VALUE
					 */
					
					case self::STATE_VALUE :
						if(ctype_space($c))
						{
							if($this->_attr_value !== null)
							{
								$this->checkAppendAttribute();
								$this->state = self::STATE_BEGIN;
								continue;
							}
						}
						
						if($c === '$') {
							$this->state = self::STATE_VALUE_VAR;
							continue;
						}
						
						if($c === '"')
						{
							$this->state = self::STATE_VALUE_STRING;
							continue;
						}
						
						if(ctype_alnum($c) || $c === '_' || $c === '_')
						{
							$this->_attr_value .= $c;
							continue;
						}
						
						if($c === '.') {
							// Array syntax: xyz=foo.bar
							// @TODO
							continue;
						}
						
						/*
						if($c === '$') {
							$this->switchState(self::STATE_EXPRESSION);
							continue;
						}
						*/
						
						$this->error();
					break;
					
					/**
					 * ##### STATE_VALUE_STRING
					 */
					
					case self::STATE_VALUE_STRING :
						if($c === '\\')
						{
							if(!$this->_escaped)
							{
								$this->_escaped = true;
							}
							else
							{
								$this->_string .= $c;
								$this->_escaped = false;
							}
							
							continue;
						}
						
						if($c === '"')
						{
							if(!$this->_escaped)
							{
								$this->_attr_value = $this->_string;
								$this->checkAppendAttribute();
								$this->_string = null;
								$this->state = self::STATE_BEGIN;
							}
							else
							{
								$this->_string .= $c;
								$this->_escaped = false;
							}
							
							continue;
						}
						
						if($this->_escaped)
						{
							$this->_string .= '\\';
							$this->_escaped = false;
						}
						
						$this->_string .= $c;
						
						//$this->error();
					break;
					
					case self::STATE_VALUE_VAR :
						if(ctype_space($c))
						{
							$this->checkAppendOperand();
							$this->checkAppendAttribute();
							$this->state = self::STATE_BEGIN;
							continue;
						}
						// probably end of var - an operator might follow
						elseif($this->find('->'))
						{
							$this->i++;
							$this->checkAppendOperand();
							$this->_var_type = 'object';
							continue;
						}
						elseif($c === '.')
						{
							$this->checkAppendOperand();
							$this->_var_type = 'array';
							continue;
						}
						
						if($this->_var === null)
						{
							if(ctype_alpha($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						else
						{						
							if(ctype_alnum($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						
						$this->error();
					break;
					
					/**
					 * ##### STATE_PARAMETER_STRING
					 */
					
					// we're in a parameter
					case self::STATE_PARAMETER_STRING :
						if($c === '\\' && !$this->_escaped)
						{
							$this->_escaped = true;
							
							continue;
						}
						
						if($c === '"' && !$this->_escaped)
						{
							$this->state = self::STATE_BEGIN;
							
							if($this->_op !== null)
							{
								//$this->checkAppendOperator();
								$this->_op = null;
								$this->checkAppendOperand();
							}
							else
							{
								$this->attributes[] = $this->_parameter;
								$this->_parameter = null;
							}
							
							continue;
						}
						
						$this->_parameter .= $c;
					break;
					
					/**
					 * STATE_EXPRESSION
					 */
					
					case self::STATE_EXPRESSION :
						// $this->_operands[] = Array('var' => $this->_var);
						
						if(ctype_space($c))
						{
							continue;
						}
						
						if($c === '$')
						{
							$this->state = self::STATE_EXPRESSION_VAR;
							continue;
						}
						
						if($c === '(')
						{
							$this->openingBracket();
							continue;
						}
						
						if($c === ')')
						{
							//$this->onClosingBracket();
							$this->closingBracket();
							
							if($this->_bracket_stack === null)
							{
								$this->state = self::STATE_BEGIN;
							}
							
							continue;
						}
						
						if($op = $this->is_operator())
						{
							$this->_op = $op;
							$this->checkAppendOperator();
							//$this->_bracket_stack->add($op);
							continue;
						}
						
						if($c === '"')
						{
							$this->_string = '';
							$this->state = self::STATE_EXPRESSION_STRING;
							continue;
						}
						
						if(ctype_digit($c))
						{
							// $this->_number .= $c;
							
							//$this->checkAppendOperator();
							//$this->_operands[] = Array('op' => $this->_op);
							//$this->_op = null;
							$this->switchState(self::STATE_EXPRESSION_NUMBER);
							continue;
						}
						
						$this->error();
					break;
					case self::STATE_EXPRESSION_VAR :
						if($this->_var != null)
						{
							if(ctype_space($c))
							{
								$this->checkAppendOperand();
								$this->state = self::STATE_EXPRESSION;
								/*
								$this->checkAppendOperand(self::STATE_EXPRESSION);
								if($this->_brackets === 0)
								{
									$this->state = self::STATE_BEGIN;
								}
								*/
								
								continue;
							}
							elseif($c === ')')
							{
								//$this->closingBracket();
								$this->checkAppendOperand();
								$this->switchState(self::STATE_EXPRESSION);
								continue;
							}
							// probably end of var - an operator might follow
							elseif($this->find('->'))
							{
								
								$this->i++;
								$this->checkAppendOperand();
								$this->_var_type = 'object';
								continue;
							}
							elseif($c === '.')
							{
								$this->checkAppendOperand();
								$this->_var_type = 'array';
								continue;
							}
							// exclude 'or' & 'and' operator
							elseif(($op = $this->is_operator()))
							{
								if(ctype_alpha($op))
								{
									$this->i--;
								}
								else
								{
								//var_dump($this->_var, $op);
									$this->_op = $op;
									$this->checkAppendOperand();
									$this->checkAppendOperator();
									$this->state = self::STATE_EXPRESSION;
									continue;
								}
							}
							// needed for {set $foo=$bar}
							elseif($c === '=')
							{
								$this->_op = '=';
								$this->checkAppendOperand();
								$this->checkAppendOperator();
								$this->state = self::STATE_EXPRESSION;
								continue;
							}
						}
						
						if($this->_var === null)
						{
							if(ctype_alpha($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						else
						{						
							if(ctype_alnum($c) || $c === '_')
							{
								$this->_var .= $c;
								continue;
							}
						}
						
						$this->error();
					break;
					
					/**
					 * ##### STATE_EXPRESSION_NUMBER
					 */
					
					case self::STATE_EXPRESSION_NUMBER :
						if(ctype_space($c))
						{
							if($this->_number != null)
							{
								$this->state = self::STATE_EXPRESSION;
								continue;
							}
						}
						
						if($c === ')')
						{
							$this->checkAppendOperand();
							//$this->closingBracket();
							//$this->_operands[] = Array('number' => $this->_number);
							$this->switchState(self::STATE_EXPRESSION);
							continue;
						}
						
						// . needed for floating point
						if(ctype_digit($c) || $c === '.')
						{
							$this->_number .= $c;
							continue;
						}
					
						$this->error();
					break;
					case self::STATE_EXPRESSION_STRING :
						if($c === '\\' && !$this->_escaped)
						{
							$this->_escaped = true;
							continue;
						}
						
						if($c === '"' && !$this->_escaped)
						{
							$this->checkAppendOperand();
							$this->state = self::STATE_EXPRESSION;
							continue;
						}
						
						$this->_string .= $c;
					break;
					case self::STATE_EXPRESSION_AFTER_VAR : 
						if($this->find('->'))
						{
							$this->i++;
							$this->_var_type = 'object';
							//$this->checkAppendVar();
							$this->state = self::STATE_EXPRESSION_VAR;
							continue;
						}
						else if($c === '.')
						{
							$this->_var_type = 'array';
							//$this->checkAppendVar();
							$this->state = self::STATE_EXPRESSION_VAR;
							continue;
						}
					break;
					/*
					case self::STATE_EXPRESSION_AFTER_VAR :
						if($this->_op = $this->is_operator())
						{
							$this->state = self::STATE_EXPRESSION_AFTER_OPERATOR;
							continue;
						}
						
						$this->error();
					break;
					
					case self::STATE_EXPRESSION_AFTER_OPERATOR :
						if(ctype_space($c))
						{
							// Skip spaces before
							if($this->_operand === null)
							{
								continue;
							}
							
							// @TODO save var/operand + operator + var/operand to the list
							$this->state = self::STATE_EXPRESSION;
							
							continue;
						}
						
						// 2nd Operand is a var
						if($c === '$')
						{
							die('@TODO');
							continue;
						}
						elseif($c === '"')
						{
							die('@TODO');
							continue;
						}
						elseif(ctype_digit($c))
						{
							$this->_operand .= $c;
							continue;
						}
						elseif($c === ')')
						{
							$this->onClosingBracket();
							continue;
						}
						
						$this->error();
					break;
					*/
					default :
						$this->error();
						break 2;
					break;
				}
			}
			
			// Final State
			switch($this->state)
			{
				case self::STATE_BEGIN :
					if($this->_parameter !== null)
					{
						if($this->_op !== null)
						{
							
						}
					}
					
					if($this->_attr_name !== null)
					{
						$this->attributes[$this->_attr_name] = $this->_attr_value;
					}
				
					// var_dump($this->_attr_name);
				break;
				case self::STATE_COMMAND_VAR :
					if(isset($this->attributes['_var']['var']))
					{
						$this->attributes['_var'][] = Array('type' => $this->_var_type, 'var' => $this->_var);
					}
					else
					{
						$this->attributes['_var'] = $this->_var;
					}
				break;
				case self::STATE_VALUE :
					$this->checkAppendAttribute();
				break;
				case self::STATE_LANGUAGE :
					if($this->_lang_identifier !== null)
					{
						if(isset($this->attributes['_identifiers']))
						{
							$this->attributes['_identifiers'][] = $this->_lang_identifier;	
						}
						else
						{
							$this->attributes['_identifier'] = $this->_lang_identifier;	
						}
						$this->_lang_identifier = null;
					}
				break;
				case self::STATE_COMMAND :
					
				break;
				case self::STATE_EXPRESSION_VAR :
					$this->checkAppendOperand();
				break;
				case self::STATE_EXPRESSION_NUMBER :
					$this->checkAppendOperand();
				break;
				case self::STATE_EXPRESSION :
					if($this->_bracket_stack === null)
					{
						break;
					}
				
				default :
					throw new Exception('Parse ERROR at offset '.$this->i.' at "'.substr($this->str, $this->i).'" in state '.$this->state.' in line '.$this->trace['line']);
				break;
			}
			
			$this->attributes['_cmd'] = $this->_cmd;
			if($this->_end)
				$this->attributes['_end'] = true;
			
			if(count($this->_expression))
			{
				$this->attributes['_expression'] = $this->_expression;
			}
			
			if($this->debug)
			{
				var_dump($this->attributes, $this->_expression, $this->_bracket_stack_top);
			}
			
			return $this->attributes;
		}
	}
?>