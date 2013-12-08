<?php
	namespace kit;
	
	abstract class view implements viewInterface
	{
		protected $template;
		
		public function __set($k, $v)
		{
			$this->template->$k = $v;
		}
		
		public function get()
		{
			return $this->template->get();
		}
		
		public function run()
		{
			echo $this->get();
		}
	}
?>