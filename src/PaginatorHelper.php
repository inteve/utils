<?php

	declare(strict_types=1);

	namespace Inteve\Utils;


	class PaginatorHelper
	{
		public function __construct()
		{
			throw new StaticClassException('This is static class.');
		}


		/**
		 * @return int[]
		 */
		public static function calculateSteps(\Nette\Utils\Paginator $paginator)
		{
			$page = $paginator->page;

			if ($paginator->pageCount < 2) {
				return [$page];
			}

			$arr = range(max($paginator->firstPage, $page - 3), min((int) $paginator->lastPage, $page + 3));
			$count = 4;
			$quotient = ($paginator->pageCount - 1) / $count;
			for ($i = 0; $i <= $count; $i++) {
				$arr[] = (int) round($quotient * $i) + $paginator->firstPage;
			}
			sort($arr);
			return array_values(array_unique($arr));
		}
	}
