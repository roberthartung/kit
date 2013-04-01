<?php
	namespace kit\db\result;
	
	class row
	{
		private $columns;
		
		private $changed = false;
		
		public function __construct(array $columns)
		{
			$this->columns = $columns;
			/*
			foreach($this->model->getColumns() AS $col)
			{
				$name = $col['name'];
				var_dump($this->$name);
			}
			*/
			
			/*
			object(kit\db\result\row)#12 (5) {
  ["video_id"]=>
  string(1) "1"
  ["file_name"]=>
  string(7) "tmp.mp4"
  ["file_size"]=>
  string(6) "123456"
  ["video_length"]=>
  string(2) "10"
  ["status"]=>
  string(7) "enabled"
}
			*/
		}
		
		public function __call($func, $args)
		{
			if(strpos($func, 'set') === 0)
			{
				$col_name = substr($func,4);
				if(array_key_exists($col_name, $this->columns))
				{
					$this->$col_name = $args[0];
					return true;
				}
			}
			elseif(strpos($func, 'get') === 0)
			{
				$col_name = substr($func,4);
				if(array_key_exists($col_name, $this->columns))
				{
					$this->changed = true;
					return $this->$col_name;
				}
			}
			
			return null;
		}
	}
?>