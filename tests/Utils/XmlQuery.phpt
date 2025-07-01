<?php

declare(strict_types=1);

use Inteve\Utils\InvalidStateException;
use Inteve\Utils\MissingException;
use Inteve\Utils\XmlQuery;
use Inteve\Utils\XmlNamespace;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$query = XmlQuery::fromString(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
			'<url>',
				'<loc>http://example.com/</loc>',
			'</url>',
			'<url>',
				'<loc>http://example2.com/</loc>',
			'</url>',
		'</urlset>',
	]));

	Assert::exception(function () use ($query) {
		$query->child('url');
	}, InvalidStateException::class, 'There are multiple children with tag url.');

	$actual = [];

	foreach ($query->children('url') as $url) {
		$actual[] = $url->child('loc')->text();
	}

	Assert::same([
		'http://example.com/',
		'http://example2.com/',
	], $actual);
});


test(function () {
	$query = XmlQuery::fromString(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<h:html xmlns:xdc="http://www.xml.com/books" xmlns:h="http://www.w3.org/HTML/1998/html4">',
			'<h:head><h:title>Book Review</h:title></h:head>',
			'<h:body class="test" xdc:class="test2" lang="en">',
				'<xdc:bookreview>',
					'<xdc:title>XML: A Primer</xdc:title>',
					'<h:table />',
				'</xdc:bookreview>',
			'</h:body>',
		'</h:html>',
	]));
	$nsXdc = new XmlNamespace('http://www.xml.com/books');
	$nsHtml = new XmlNamespace('http://www.w3.org/HTML/1998/html4');

	Assert::same('html', $query->getName());

	Assert::false($query->hasChild('head'));
	Assert::true($query->hasChild($nsHtml->name('head')));

	Assert::exception(function () use ($query) {
		$query->child('head');
	}, MissingException::class, 'Missing element with tag head.');

	Assert::exception(function () use ($query) {
		$query->attr('unexists');
	}, MissingException::class, 'Missing attribute \'unexists\'.');

	$body = $query->child($nsHtml->name('body'));
	Assert::false($body->hasAttr('unexists'));
	Assert::true($body->hasAttr('class'));
	Assert::true($body->hasAttr('lang'));
	Assert::true($body->hasAttr($nsXdc->name('class')));

	Assert::same('test', $body->attr('class'));
	Assert::same('en', $body->attr('lang'));
	Assert::same('test2', $body->attr($nsXdc->name('class')));

	Assert::same('XML: A Primer', $query->child($nsHtml->name('body'))
		->child($nsXdc->name('bookreview'))
		->child($nsXdc->name('title'))
		->text()
	);
});

test(function () {
	$query = XmlQuery::fromString(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<html xmlns:xdc="http://www.xml.com/books" xmlns="http://www.w3.org/HTML/1998/html4">',
			'<head><title>Book Review</title></head>',
			'<body class="test" xdc:class="test2" lang="en">',
				'<xdc:bookreview>',
					'<xdc:title>XML: A Primer</xdc:title>',
					'<table />',
				'</xdc:bookreview>',
			'</body>',
		'</html>',
	]));
	$nsXdc = new XmlNamespace('http://www.xml.com/books');
	$nsHtml = new XmlNamespace('http://www.w3.org/HTML/1998/html4');

	Assert::same('html', $query->getName());

	Assert::true($query->hasChild('head'));
	Assert::true($query->hasChild($nsHtml->name('head')));

	Assert::exception(function () use ($query) {
		$query->child('unexists');
	}, MissingException::class, 'Missing element with tag unexists.');

	$body = $query->child($nsHtml->name('body'));
	Assert::false($body->hasAttr('unexists'));
	Assert::true($body->hasAttr('class'));
	Assert::true($body->hasAttr('lang'));
	Assert::true($body->hasAttr($nsXdc->name('class')));

	Assert::same('test', $body->attr('class'));
	Assert::same('en', $body->attr('lang'));
	Assert::same('test2', $body->attr($nsXdc->name('class')));

	Assert::same('XML: A Primer', $query->child($nsHtml->name('body'))
		->child($nsXdc->name('bookreview'))
		->child($nsXdc->name('title'))
		->text()
	);
});
