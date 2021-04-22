<?php

	namespace Inteve\Utils;


	class XmlQuery
	{
		/** @var \SimpleXmlElement */
		private $element;


		public function __construct(\SimpleXmlElement $element)
		{
			$this->element = $element;
		}


		/**
		 * @return string
		 */
		public function getName()
		{
			return $this->element->getName();
		}


		/**
		 * @param  string|XmlName $tagName
		 * @return bool
		 */
		public function hasChild($tagName)
		{
			$children = $this->children($tagName);
			return count($children) > 0;
		}


		/**
		 * @param  string|XmlName $tagName
		 * @return self
		 */
		public function child($tagName)
		{
			$children = $this->children($tagName);

			if (count($children) > 1) {
				throw new InvalidStateException('There are multiple children with tag ' . ($tagName instanceof XmlName ? $tagName->toDebugString() : $tagName) . '.');
			}

			if (count($children) < 1) {
				throw new MissingException('Missing element with tag ' . ($tagName instanceof XmlName ? $tagName->toDebugString() : $tagName) . '.');
			}

			return reset($children);
		}


		/**
		 * @param  string|XmlName $tagName
		 * @return self[]
		 */
		public function children($tagName)
		{
			$nsUri = NULL;

			if ($tagName instanceof XmlName) {
				$nsUri = $tagName->getNamespace()->getUri();
				$tagName = $tagName->getName();
			}

			$children = $this->element->children($nsUri);
			$result = [];

			foreach ($children as $child) {
				if ($tagName === $child->getName()) {
					$result[] = new self($child);
				}
			}

			return $result;
		}


		/**
		 * @param  string|XmlName $attribute
		 * @return string
		 */
		public function attr($attribute)
		{
			$nsUri = NULL;

			if ($attribute instanceof XmlName) {
				$nsUri = $attribute->getNamespace()->getUri();
				$attribute = $attribute->getName();
			}

			$attributes = $this->element->attributes($nsUri);

			foreach ($attributes as $name => $value) {
				if ($attribute === $name) {
					return (string) $value;
				}
			}

			throw new MissingException("Missing attribute '$attribute'" . ($nsUri !== NULL ? ('(' . $nsUri . ')') : '') . '.');
		}


		/**
		 * @param  string|XmlName $attribute
		 * @return bool
		 */
		public function hasAttr($attribute)
		{
			$nsUri = NULL;

			if ($attribute instanceof XmlName) {
				$nsUri = $attribute->getNamespace()->getUri();
				$attribute = $attribute->getName();
			}

			$attributes = $this->element->attributes($nsUri);

			foreach ($attributes as $name => $value) {
				if ($attribute === $name) {
					return TRUE;
				}
			}

			return FALSE;
		}


		/**
		 * @return string
		 */
		public function text()
		{
			return (string) $this->element;
		}


		/**
		 * @param  string $s
		 * @return self
		 */
		public static function fromString($s)
		{
			$xml = simplexml_load_string($s);

			if ($xml === FALSE) {
				$details = '';

				foreach(libxml_get_errors() as $error) {
					if ($details !== '') {
						$details .= "\n";
					}

					$details .= $error->message . ' (on line ' . $error->line . ':' . $error->column . ')';
				}

				throw new InvalidArgumentException('Invalid XML' . ($details !== '' ? (': ' . $details) : '.'));
			}

			return new self($xml);
		}
	}
