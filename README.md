# php-json-subset-encoder
JSON encoding support library for nested object and assoc array

## Requirements

php >= 5.5.x

## Installation

Omelet can be installed with Composer. 

Define the following requirement in your composer.json file:

{
    "require": {
        "ritalin/php-json-subset-encoder ": "*"
    }
}

## Quick Start

1. Create ObjectMeta instance.

```php
$meta = new ObjectMeta(NestObject::class, ['intField'], [
    'objField' => new ObjectMeta(SomeObject::class, ['b', 'c'])
]);

```

1. Build serializer.

```php
// For example, object array.

$values = [
    new NestObject('www', 10, new SomeObject('a', 'b', 'c')),
    new NestObject('xxx', 20, new SomeObject('o', 'p', 'q')),
    new NestObject('@@@', 30, new SomeObject('x', 'y', 'z'))
];

$serializer = EncoderBuilder::AsObjectArray($meta)->build($values);
```

1. Encode as JSON.

```php
$json = json_encode($serializer);
```
