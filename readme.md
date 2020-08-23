
# Inteve\Utils

Utility classes for web development

<a href="https://www.paypal.me/janpecha/5eur"><img src="https://buymecoffee.intm.org/img/button-paypal-white.png" alt="Buy me a coffee" height="35"></a>


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

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
