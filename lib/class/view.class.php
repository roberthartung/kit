<?php
	namespace kit;
	
	abstract class view implements viewInterface
	{
		protected $template;
		
		public function __set($k, $v)
		{
			$this->template->$k = $v;
		}
	}
?>