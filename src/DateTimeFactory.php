<?php

	namespace Inteve\Utils;


	class DateTimeFactory implements IDateTimeFactory
	{
		/**
		 * @return \DateTimeImmutable
		 */
		public function create()
		{
			return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
		}
	}
