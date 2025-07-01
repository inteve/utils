<?php

	declare(strict_types=1);

	namespace Inteve\Utils;


	class Helpers
	{
		/**
		 * @param  string $value
		 * @return string
		 */
		public static function escapeXml($value)
		{
			// XML 1.0: \x09 \x0A \x0D and C1 allowed directly, C0 forbidden
			// XML 1.1: \x00 forbidden directly and as a character reference,
			//   \x09 \x0A \x0D \x85 allowed directly, C0, C1 and \x7F allowed as character references
			return htmlspecialchars((string) preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '', $value), ENT_QUOTES, 'UTF-8');
		}
	}
