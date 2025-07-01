<?php

	declare(strict_types=1);

	namespace Inteve\Utils;


	class XmlNamespace
	{
		/** @var string */
		private $uri;

		/** @var array<string, self> */
		private static $registry = [];


		/**
		 * @param string $uri
		 */
		public function __construct($uri)
		{
			$this->uri = $uri;
		}


		/**
		 * @return bool
		 */
		public function equals(self $other)
		{
			return $this->uri === $other->uri;
		}


		/**
		 * @return string
		 */
		public function getUri()
		{
			return $this->uri;
		}


		/**
		 * @param  string $name
		 * @return XmlName
		 */
		public function name($name)
		{
			return new XmlName($name, $this);
		}


		/**
		 * @param  string $uri
		 * @return self
		 */
		public static function get($uri)
		{
			if (!isset(self::$registry[$uri])) {
				self::$registry[$uri] = new self($uri);
			}

			return self::$registry[$uri];
		}
	}
