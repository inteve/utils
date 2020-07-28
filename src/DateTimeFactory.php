<?php

	namespace Inteve\Utils;


	class DateTimeFactory implements IDateTimeFactory
	{
		/**
		 * @return \DateTimeImmutable
		 */
		public function create()
		{
			return new \DateTimeImmutable(NULL, new \DateTimeZone('UTC'));
		}
	}
