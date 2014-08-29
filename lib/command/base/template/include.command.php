<?php
	namespace kit\base\template;
	
	use kit\loaderTrait;
	
	abstract class includeCommand {
		use loaderTrait {	__construct as loader; }
	}
?>