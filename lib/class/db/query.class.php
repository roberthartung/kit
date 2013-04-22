<?php
	namespace kit\db;
	
	use kit\loaderTrait;
	
	use kit\db\query\select;
	
	use kit\modelInterface;
	
	class query
	{
		use loaderTrait { __construct as loader; }
		
		const QUERY_TYPE_SELECT = 'select';
		
		private $type;
		
		private $wrapper;
		
		private $model;
		
		public function __construct(modelInterface $model = null, $type = self::QUERY_TYPE_SELECT)
		{
			$this->loader();
			$this->type = $type;
			$class = 'kit\\db\\query\\'.$this->type;
			$this->wrapper = new $class;
			$this->model = $model;
		}
		
		public function getModel()
		{
			return $this->model;
		}
		
		public function __call($func, $args)
		{
			$return = call_user_func_array(Array($this->wrapper, $func), $args);;
			if(strpos($func, 'get') === 0)
			{
				return $return;
			}
			
			return $this;
		}
		
		public function execute()
		{
			return $this->db->query($this);
		}
		
		public function __toString()
		{
			return (string) $this->wrapper;
		}
	}
?>