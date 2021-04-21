<?php

use Inteve\Utils\InvalidArgumentException;
use Inteve\Utils\XmlDocument;
use Inteve\Utils\XmlElement;
use Inteve\Utils\XmlNamespace;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$xml = new XmlDocument;
	$root = $xml->create('urlset');
	$ns = $root->defineNamespace(NULL, 'http://www.sitemaps.org/schemas/sitemap/0.9');

	$item = $root->create($ns->name('url'));
	$item->create($ns->name('loc'))->setText('http://example.com/');

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
			'<url>',
				'<loc>http://example.com/</loc>',
			'</url>',
		'</urlset>',
	]), $xml->toString());
});


test(function () {
	$xml = new XmlDocument;
	$nsXdc = new XmlNamespace('http://www.xml.com/books');
	$nsHtml = new XmlNamespace('http://www.w3.org/HTML/1998/html4');

	$root = $xml->create($nsHtml->name('html'));
	$root->defineNamespace('xdc', $nsXdc);
	$root->defineNamespace('h', $nsHtml);

	$root->create('head')
		->create('title')
			->setText('Book Review');

	$body = $root->create('body');
	$body->attr('class', 'test');
	$body->attr($nsXdc->name('class'), 'test2');
	$body->attr($nsHtml->name('lang'), 'en');
	$bookreview = $body->create($nsXdc->name('bookreview'));
	$bookreview->create('title')->setText('XML: A Primer');
	$bookreview->create($nsHtml->name('table'));

	Assert::same(implode('', [
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
	]), $xml->toString());
});


test(function () {
	$xml = new XmlDocument;
	$nsXdc = new XmlNamespace('http://www.xml.com/books');
	$nsHtml = new XmlNamespace('http://www.w3.org/HTML/1998/html4');

	$root = $xml->create($nsHtml->name('html'));
	$root->defineNamespace('xdc', $nsXdc);
	$root->defineNamespace(NULL, $nsHtml);

	$root->create('head')
		->create('title')
			->setText('Book Review');

	$body = $root->create('body');
	$body->attr('class', 'test');
	$body->attr($nsXdc->name('class'), 'test2');
	$body->attr($nsHtml->name('lang'), 'en');
	$bookreview = $body->create($nsXdc->name('bookreview'));
	$bookreview->create('title')->setText('XML: A Primer');
	$bookreview->create($nsHtml->name('table'));

	Assert::same(implode('', [
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
	]), $xml->toString());
});
