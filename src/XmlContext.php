<?php

	namespace Inteve\Utils;


	class XmlContext
	{
		/** @var array<string, XmlNamespace> */
		private $namespaces = [];


		/**
		 * @param  string|NULL $prefix
		 * @param  string|XmlNamespace $uri
		 * @return XmlNamespace
		 */
		public function defineNamespace($prefix, $uri)
		{
			$prefix = (string) $prefix;
			$namespace = ($uri instanceof XmlNamespace) ? $uri : new XmlNamespace($uri);

			foreach ($this->namespaces as $nsPrefix => $nsNamespace) {
				if ($nsNamespace->equals($namespace)) {
					throw new InvalidStateException("Namespace URI already exists with prefix '$nsPrefix'.");
				}
			}

			return $this->namespaces[$prefix] = $namespace;
		}


		/**
		 * @return array<string, XmlNamespace>
		 */
		public function getNamespaces()
		{
			return $this->namespaces;
		}


		/**
		 * @return XmlNamespace
		 */
		public function getImplicitNamespace()
		{
			return isset($this->namespaces['']) ? $this->namespaces[''] : NULL;
		}


		/**
		 * @param  string $name
		 * @return string
		 */
		public function getName($name, XmlNamespace $namespace = NULL)
		{
			if ($namespace === NULL) {
				if (isset($this->namespaces[''])) {
					throw new InvalidArgumentException('Name without namespace is not allowed in context with implicit namespace.');
				}

				return $name;
			}

			$prefix = NULL;

			foreach ($this->namespaces as $nsPrefix => $nsNamespace) {
				if ($nsNamespace->equals($namespace)) {
					$prefix = $nsPrefix;
				}
			}

			if ($prefix === NULL) {
				throw new InvalidStateException("Namespace '{$namespace->getUri()}' has defined no prefix in this context.");
			}

			if ($prefix === '') {
				return $name;
			}

			return $prefix . ':' . $name;
		}


		/**
		 * @return string
		 */
		public function getDeclaration()
		{
			$s = '';
			$isFirst = TRUE;

			foreach ($this->namespaces as $prefix => $namespace) {
				if (!$isFirst) {
					$s .= ' ';
				}

				$s .= 'xmlns' . ($prefix !== '' ? (':' . $prefix) : '') . '="' . Helpers::escapeXml($namespace->getUri()) . '"';
				$isFirst = FALSE;
			}

			return $s;
		}


		/**
		 * @return self
		 */
		public static function merge(self $parent, self $child)
		{
			$namespaces = clone $parent;

			foreach ($child->getNamespaces() as $prefix => $uri) {
				$namespaces->defineNamespace($prefix, $uri);
			}

			return $namespaces;
		}
	}
