<?php

	declare(strict_types=1);

	namespace Inteve\Utils\DI;

	use Nette;
	use Nette\Utils\Strings;


	class ServiceCollectionsInjector extends Nette\DI\CompilerExtension
	{
		/**
		 * @return void
		 */
		public function beforeCompile()
		{
			if (class_exists(Nette\DI\Definitions\Definition::class)) {
				// nette/di 3.x autowire collections automaticly
				return;
			}

			$builder = $this->getContainerBuilder();

			foreach ($builder->getDefinitions() as $definition) {
				if (!($definition instanceof \Nette\DI\ServiceDefinition)) {
					continue;
				}

				$factory = $definition->getFactory();

				if (!($factory instanceof Nette\DI\Statement && property_exists($factory, 'arguments') && method_exists($factory, 'getEntity'))) {
					continue;
				}

				$entity = $factory->getEntity();

				if (!is_string($entity)) {
					continue;
				}

				if (strpos($entity, '@') !== FALSE) { // service name
					continue;
				}

				if (strpos($entity, '::') !== FALSE) { // method factory
					continue;
				}

				$className = $entity;

				if (!class_exists($className)) {
					continue;
				}

				$rc = new \ReflectionClass($className);
				$constructor = $rc->getConstructor();

				if (!$constructor) {
					continue;
				}

				$docComment = (string) $constructor->getDocComment();

				if ($docComment === '') {
					continue;
				}

				$paramTypes = [];

				foreach(self::parseAnnotationValues('param', $docComment) as $paramDoc) {
					$parts = Strings::split($paramDoc, '/\s+/');

					if (count($parts) < 2) { // neuplna deklarace
						continue;
					}

					if (!Strings::endsWith($parts[0], '[]')) { // neni kolekci
						continue;
					}

					if (!Strings::startsWith($parts[1], '$')) { // neni nazvem promenne
						continue;
					}

					$type = Strings::substring($parts[0], 0, -2);

					if (Nette\Utils\Reflection::isBuiltinType($type)) {
						continue;
					}

					$paramName = Strings::substring($parts[1], 1);

					if (isset($paramTypes[$paramName])) {
						throw new \Inteve\Utils\Exception("Invalid PHPDoc - multiple types for parameter {$parts[1]}"); // TODO invalid state
					}

					$paramTypes[$paramName] = Nette\Utils\Reflection::expandClassName($type, $rc);
				}

				if (empty($paramTypes)) {
					continue;
				}

				foreach ($constructor->getParameters() as $param) {
					if (!$param->isArray()) {
						continue;
					}

					$paramName = $param->getName();

					if (array_key_exists($param->getPosition(), $factory->arguments)) { // uz nastaveno jako pozicni argument
						continue;
					}

					if (array_key_exists($paramName, $factory->arguments)) { // uz nastaveno jako pojmenovany argument
						continue;
					}

					if (!isset($paramTypes[$paramName])) {
						continue;
					}

					$factory->arguments[$paramName] = array_filter($builder->findByType($paramTypes[$paramName]), function ($wiredDefinition) use ($definition) {
						return $wiredDefinition !== $definition;
					});
				}
			}
		}


		/**
		 * Parse value pieces of requested annotation from given doc comment
		 *
		 * @param  string $annotation
		 * @param  string $docComment
		 * @return array<string>
		 */
		private static function parseAnnotationValues($annotation, $docComment)
		{
			$matches = [];
			preg_match_all("#@$annotation\\s+([^@\\n\\r]*)#", $docComment, $matches);
			return $matches[1];
		}
	}
