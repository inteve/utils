# Inteve\Utils

[![Build Status](https://github.com/inteve/utils/workflows/Build/badge.svg)](https://github.com/inteve/utils/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/inteve/utils.svg)](https://packagist.org/packages/inteve/utils)
[![Latest Stable Version](https://poser.pugx.org/inteve/utils/v/stable)](https://github.com/inteve/utils/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/inteve/utils/blob/master/license.md)

Utility classes for web development

<a href="https://www.janpecha.cz/donate/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>


## Installation

[Download a latest package](https://github.com/inteve/utils/releases) or use [Composer](http://getcomposer.org/):

```
composer require inteve/utils
```

Inteve\Utils requires PHP 5.6.0 or later.


## Usage

**DateTimeFactory**

```php
$dateTimeFactory = new Inteve\Utils\DateTimeFactory;
$now = $dateTimeFactory->create();
```


**Imagick**

``` php
use Inteve\Utils\ImagickHelper;
$imagick = ImagickHelper::openImage('file.jpg');
ImagickHelper::resize($imagick, $width, $height, $flags); // same parameters as for Image::resize()
ImagickHelper::saveImage($imagick, 'thumb.jpg');

// and much more!
```


**PaginatorHelper**

```php
$paginator = new Nette\Utils\Paginator;
$steps = Inteve\Utils\PaginatorHelper::calculateSteps($paginator);
```


**XmlDocument**

```php
$xml = new Inteve\Utils\XmlDocument([
	'standalone' => 'yes',
]);
$root = $xml->create('urlset');

$item = $root->create('url');
$item->create('loc')->setText('http://example.com/');

echo $xml->toString();
```

Prints:

```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<urlset>
	<url>
		<loc>http://example.com/</loc>
	</url>
</urlset>
```

**XmlQuery**

Wrapper of SimpleXml.

```php
$query = Inteve\Utils\XmlQuery::fromString('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<urlset>
	<url>
		<loc>http://example.com/</loc>
	</url>
	<url>
		<loc>http://example.com/path</loc>
	</url>
</urlset>');

$urls = [];

foreach ($query->children('url') as $url) {
	$urls[] = $url->child('loc')->text();
}

var_dump($urls);
```

Prints:

```
http://example.com/
http://example.com/path
```


------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
