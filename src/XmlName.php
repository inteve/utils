<?php

	namespace Inteve\Utils;


	class XmlName
	{
		/** @var string */
		private $name;

		/** @var XmlNamespace */
		private $namespace;


		/**
		 * @param string $name
		 */
		public function __construct($name, XmlNamespace $namespace)
		{
			$this->name = $name;
			$this->namespace = $namespace;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}


		/**
		 * @return XmlNamespace
		 */
		public function getNamespace()
		{
			return $this->namespace;
		}


		/**
		 * @return string
		 */
		public function toDebugString()
		{
			return $this->name . ' (' . $this->namespace->getUri() . ')';
		}
	}
