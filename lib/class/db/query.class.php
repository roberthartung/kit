<?php
	namespace kit\db;
	
	use kit\loaderTrait;
	
	use kit\db\query\select;
	
	class query
	{
		use loaderTrait { __construct as loader; }
		
		const QUERY_TYPE_SELECT = 'select';
		
		private $type;
		
		private $wrapper;
		
		public function __construct($type = self::QUERY_TYPE_SELECT)
		{
			$this->loader();
			$this->type = $type;
			$class = 'kit\\db\\query\\'.$this->type;
			$this->wrapper = new $class;
		}
		
		public function __call($func, $args)
		{
			call_user_func_array(Array($this->wrapper, $func), $args);
			return $this;
		}
		
		public function __toString()
		{
			$query = '';
			
			// $this->{$this->type}();
			
			return (string) $this->wrapper;
		}
	}
?>