<?php

declare(strict_types=1);

use Inteve\Utils\InvalidArgumentException;
use Inteve\Utils\XmlDocument;
use Inteve\Utils\XmlElement;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';


test(function () {
	$xml = new XmlDocument([
		'standalone' => 'yes',
	]);
	$root = $xml->create('urlset');

	$item = $root->create('url');
	$item->create('loc')->setText('http://example.com/');

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
		"\n",
		'<urlset>',
			'<url>',
				'<loc>http://example.com/</loc>',
			'</url>',
		'</urlset>',
	]), $xml->toString());
});


test(function () {
	$xml = new XmlDocument([
		'standalone' => 'yes',
	]);
	$root = $xml->create('urlset');

	$item = $root->create('url', [
		'priority' => '1.0',
	]);
	$item->create('ns:loc')
		->attr('ns:color', 'red')
		->attr('color', 'white')
		->setText('http://example.com/');

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
		"\n",
		'<urlset>',
			'<url priority="1.0">',
				'<ns:loc ns:color="red" color="white">http://example.com/</ns:loc>',
			'</url>',
		'</urlset>',
	]), $xml->toString());
});


test(function () {
	$xml = new XmlDocument;
	$root = new XmlElement('urlset');

	$url = new XmlElement('url');

	$loc = new XmlElement('loc');
	$loc->setText('http://example.com/');

	$xml->addXml($root);
	$root->addXml($url);
	$url->addXml($loc);

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<urlset>',
			'<url>',
				'<loc>http://example.com/</loc>',
			'</url>',
		'</urlset>',
	]), $xml->toString());
});


test(function () {
	$url = new XmlElement('url');
	Assert::same('<url />', $url->toString());
});


test(function () {
	$url = new XmlElement('url');
	$loc = new XmlElement('loc');
	$loc->setText('http://example.com/');
	$url->setXml($loc);
	Assert::same('<url><loc>http://example.com/</loc></url>', $url->toString());
});


test(function () {
	$url = new XmlElement('url');
	$loc = new XmlElement('loc');
	$loc->setText('http://example.com/');
	$url->addXml($loc);
	$url->addXml($loc);
	Assert::same('<url><loc>http://example.com/</loc><loc>http://example.com/</loc></url>', $url->toString());
});


test(function () {
	Assert::exception(function () {
		$url = new XmlElement('url');
		$url->setText('text');
		$loc = new XmlElement('loc');
		$url->addXml($loc);
	}, InvalidArgumentException::class, 'Cannot add XML element to text content.');
});


test(function () {
	$url = new XmlElement('url');
	$loc = new XmlElement('loc');
	$loc->setText('http://example.com/');
	$url->addXml($loc);

	$clonedUrl = clone $url;
	$url->attr('priority', 1.1);
	$loc->setText('http://example2.com/');

	Assert::same('<url priority="1.1"><loc>http://example2.com/</loc></url>', $url->toString());
	Assert::same('<url><loc>http://example.com/</loc></url>', $clonedUrl->toString());
});


test(function () {
	$xml = new XmlDocument;
	$url = $xml->create('url');

	$clonedXml = clone $xml;
	$url->attr('priority', '1.0');

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<url priority="1.0" />',
	]), $xml->toString());

	Assert::same(implode('', [
		'<?xml version="1.0" encoding="UTF-8"?>',
		"\n",
		'<url />',
	]), $clonedXml->toString());
});
