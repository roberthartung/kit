<?php
	namespace kit\db;
	
	use kit\loaderTrait;
	
	use kit\db\query\select;
	
	use kit\modelInterface;
	
	use kit\singleton;
	use kit\kit as core;
	use kit\cfg;
	use kit\template\parser;
	
	class query
	{
		const QUERY_TYPE_SELECT = 'select';
		
		private $type;
		
		private $wrapper;
		
		private $model;
		
		protected $kit;
			
		protected $template_parser;
		
		protected $cfg;
		
		protected $db;
		
		public function loader() {
			$this->kit = core::getInstance();
			$this->cfg = cfg::getInstance();
			$this->template_parser = parser::getInstance();
			$this->db = $this->kit->getDatabase();
		}
		
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
			$return = call_user_func_array(Array($this->wrapper, $func), $args);
			if(strpos($func, 'get') === 0 || $func === 'asCount')
			{
				return $return;
			}
			
			return $this;
		}
		
		public function execute()
		{
			return $this->db->query($this);
		}
		
		public function countAll() {
			return $this->db->query($this->wrapper->countAll())->getRow()->count;
		}
		
		public function __toString()
		{
			return (string) $this->wrapper;
		}
	}
?>