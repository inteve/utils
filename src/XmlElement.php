<?php

	namespace Inteve\Utils;


	class XmlElement
	{
		/** @var string */
		private $tagName;

		/** @var XmlNamespace|NULL */
		private $namespace;

		/** @var array<string, string> */
		private $attributes = [];

		/** @var array<string, array<string, array{0:XmlNamespace, 1:string}>> */
		private $foreignAttributes = [];

		/** @var string|XmlElement[]|NULL */
		private $content;

		/** @var XmlContext|NULL */
		private $context;


		/**
		 * @param string|XmlName $tagName
		 * @param array<string, string> $attributes
		 */
		public function __construct($tagName, array $attributes = [])
		{
			if ($tagName instanceof XmlName) {
				$this->tagName = $tagName->getName();
				$this->namespace = $tagName->getNamespace();

			} else {
				$this->tagName = (string) $tagName;
			}

			foreach ($attributes as $attribute => $value) {
				$this->attr($attribute, $value);
			}
		}


		/**
		 * @param  string|NULL $prefix
		 * @param  string|XmlNamespace $uri
		 * @return XmlNamespace
		 */
		public function defineNamespace($prefix, $uri)
		{
			if ($this->context === NULL) {
				$this->context = new XmlContext;
			}

			return $this->context->defineNamespace($prefix, $uri);
		}


		/**
		 * @param  string|XmlName $attribute
		 * @param  string $value
		 * @return self
		 */
		public function attr($attribute, $value)
		{
			if ($attribute instanceof XmlName) {
				$namespace = $attribute->getNamespace();
				$attribute = $attribute->getName();
				$this->foreignAttributes[$namespace->getUri()][$attribute] = [
					$namespace,
					(string) $value,
				];

			} else {
				$this->attributes[$attribute] = (string) $value;
			}

			return $this;
		}


		/**
		 * @param  string $text
		 * @return self
		 */
		public function setText($text)
		{
			$this->content = (string) $text;
			return $this;
		}


		/**
		 * @return self
		 */
		public function setXml(self $element)
		{
			$this->content = [$element];
			return $this;
		}


		/**
		 * @return self
		 */
		public function addXml(self $element)
		{
			if ($this->content === NULL) {
				$this->content = [$element];

			} elseif (is_array($this->content)) {
				$this->content[] = $element;

			} else {
				throw new InvalidArgumentException('Cannot add XML element to text content.');
			}

			return $this;
		}


		/**
		 * @param  string|XmlName $tagName
		 * @param  array<string, string> $attributes
		 * @return self
		 */
		public function create($tagName, array $attributes = [])
		{
			if (!($tagName instanceof XmlName) && $this->namespace !== NULL) { // auto-namespace
				$tagName = $this->namespace->name($tagName);
			}

			$element = new XmlElement($tagName, $attributes);
			$this->addXml($element);
			return $element;
		}


		/**
		 * @return string
		 */
		public function toString(XmlContext $context = NULL)
		{
			$tagName = $this->tagName;
			$tagNamespaces = '';

			if ($this->context !== NULL) {
				$context = $context !== NULL ? XmlContext::merge($context, $this->context) : $this->context;
				$tagNamespaces = ' ' . $this->context->getDeclaration();

			}

			if ($context !== NULL) {
				$tagName = $context->getName($tagName, $this->namespace !== NULL ? $this->namespace : $context->getImplicitNamespace());
			}

			$s = '<' . $tagName . $tagNamespaces;

			foreach ($this->attributes as $attr => $value) {
				$s .= ' ' . $attr . '="' . Helpers::escapeXml($value) . '"';
			}

			if (count($this->foreignAttributes) > 0) {
				if ($context === NULL) {
					throw new InvalidStateException('Element has namespaced attributes but namespaces are not defined.');
				}

				foreach ($this->foreignAttributes as $nsUri => $nsAttributes) {
					foreach ($nsAttributes as $attr => $nsAttributeData) {
						$namespace = $nsAttributeData[0];
						$value = $nsAttributeData[1];

						if (!($this->namespace !== NULL && $namespace->equals($this->namespace))) { // need prefix?
							$attr = $context->getName($attr, $namespace);
						}

						$s .= ' ' . $attr . '="' . Helpers::escapeXml($value) . '"';
					}
				}
			}

			if ($this->content === NULL) { // no content
				$s .= ' />';

			} else {
				$s .= '>';

				if (is_string($this->content)) {
					$s .= Helpers::escapeXml($this->content);

				} elseif (is_array($this->content)) {
					foreach ($this->content as $element) {
						$s .= $element->toString($context);
					}

				} else {
					throw new InvalidStateException('Invalid content.');
				}

				$s .= '</' . $tagName . '>';
			}

			return $s;
		}


		/**
		 * Clones all children too.
		 */
		public function __clone()
		{
			if ($this->context !== NULL) {
				$this->context = clone $this->context;
			}

			if (is_array($this->content)) {
				foreach ($this->content as $key => $value) {
					if (is_object($value)) {
						$this->content[$key] = clone $value;
					}
				}
			}
		}
	}
