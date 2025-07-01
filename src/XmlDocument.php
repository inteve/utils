<?php

	declare(strict_types=1);

	namespace Inteve\Utils;


	class XmlDocument
	{
		/** @var array<string, string> */
		private $declarations = [
			'version' => '1.0',
			'encoding' => 'UTF-8',
		];

		/** @var XmlElement[] */
		private $elements = [];


		/**
		 * @param array<string, string> $declarations
		 */
		public function __construct(array $declarations = [])
		{
			foreach ($declarations as $declaration => $value) {
				$this->setDeclaration($declaration, $value);
			}
		}


		/**
		 * @param  string $declaration
		 * @param  string $value
		 * @return self
		 */
		public function setDeclaration($declaration, $value)
		{
			$this->declarations[$declaration] = $value;
			return $this;
		}


		/**
		 * @param  string $tagName
		 * @param  array<string, string> $attributes
		 * @return XmlElement
		 */
		public function create($tagName, array $attributes = [])
		{
			return $this->elements[] = new XmlElement($tagName, $attributes);
		}


		/**
		 * @return self
		 */
		public function addXml(XmlElement $element)
		{
			$this->elements[] = $element;
			return $this;
		}


		/**
		 * @return string
		 */
		public function toString()
		{
			$s = '<?xml';

			foreach ($this->declarations as $declaration => $value) {
				$s .= ' ' . $declaration . '="' . Helpers::escapeXml($value) . '"';
			}

			$s .= '?>' . "\n";

			foreach ($this->elements as $element) {
				$s .= $element->toString();
			}

			return $s;
		}


		/**
		 * Clones all children too.
		 */
		public function __clone()
		{
			foreach ($this->elements as $key => $value) {
				if (is_object($value)) {
					$this->elements[$key] = clone $value;
				}
			}
		}
	}
