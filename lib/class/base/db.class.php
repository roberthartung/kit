<?php
	namespace kit\base;

	use kit\loaderTrait;

	abstract class kit {
		use loaderTrait { __construct as loader; }
	}
?>