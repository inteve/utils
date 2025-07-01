<?php

declare(strict_types=1);

use Inteve\Utils\PaginatorHelper;
use Nette\Utils\Paginator;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	Assert::same([1], PaginatorHelper::calculateSteps(new Paginator));
});


test(function () {
	$paginator = new Paginator;
	$paginator->setPage(2);
	Assert::same([2], PaginatorHelper::calculateSteps($paginator));

	$paginator = new Paginator;
	$paginator->setPage(3);
	Assert::same([3], PaginatorHelper::calculateSteps($paginator));
});


test(function () {
	$paginator = new Paginator;
	$paginator->setPage(2);
	$paginator->setItemCount(3);
	// ItemsPerPage=1
	Assert::same([1, 2, 3], PaginatorHelper::calculateSteps($paginator));

	$paginator = new Paginator;
	$paginator->setPage(3);
	$paginator->setItemCount(3);
	// ItemsPerPage=1
	Assert::same([1, 2, 3], PaginatorHelper::calculateSteps($paginator));
});


test(function () {
	$paginator = new Paginator;
	$paginator->setPage(2);
	$paginator->setItemCount(30);
	$paginator->setItemsPerPage(2);
	Assert::same([1, 2, 3, 4, 5, 8, 12, 15], PaginatorHelper::calculateSteps($paginator));

	$paginator = new Paginator;
	$paginator->setPage(8);
	$paginator->setItemCount(30);
	$paginator->setItemsPerPage(2);
	Assert::same([1, 5, 6, 7, 8, 9, 10, 11, 12, 15], PaginatorHelper::calculateSteps($paginator));
});
