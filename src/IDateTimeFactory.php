<?php

	declare(strict_types=1);

	namespace Inteve\Utils;


	interface IDateTimeFactory
	{
		/**
		 * @return \DateTimeInterface
		 */
		function create();
	}
