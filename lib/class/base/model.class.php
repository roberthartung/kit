<?php
	namespace kit\base;

	use kit\loaderTrait;

	abstract class model {
		use loaderTrait { __construct as loader; }
	}
?>