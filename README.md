# Laravel json field cast

![Packagist License](https://img.shields.io/packagist/l/think.studio/laravel-json-field-cast?color=%234dc71f)
[![Packagist Version](https://img.shields.io/packagist/v/think.studio/laravel-json-field-cast)](https://packagist.org/packages/think.studio/laravel-json-field-cast)
[![Total Downloads](https://img.shields.io/packagist/dt/think.studio/laravel-json-field-cast)](https://packagist.org/packages/think.studio/laravel-json-field-cast)
[![Build Status](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/badges/build.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-json-field-cast/?branch=main)

Cast json field to custom object.

## Installation

Install the package via composer:

```bash
composer require think.studio/laravel-json-field-cast
```

## Usage

### Out of the box

Cast json column to single object

```php
/**
 *  @property \JsonFieldCast\Json\SimpleJsonField $json_meta
 *  @property \JsonFieldCast\Json\SimpleJsonField $text_meta
 */
class MyModel extends Model
{
    protected $casts = [
        //...
        'json_meta'              => \JsonFieldCast\Casts\SimpleJsonField::class,
        'text_meta'              => \JsonFieldCast\Casts\SimpleJsonField::class,
    ];
}


$myModel = MyModel::find(123);
$myModel->json_meta->getAttribute('example', 'default');
$myModel->json_meta->getAttribute('my_array.3',);
$myModel->json_meta->getAttribute('my_array.test');
$myModel->json_meta->setAttribute('position', 'developer');
$myModel->json_meta->removeAttribute('position');
$myModel->json_meta->hasAttribute('position');
$myModel->json_meta->getRawData(['position', 'my_array']);

$myModel->json_meta->setDate('my-date', \Carbon\Carbon::now());
$myModel->json_meta->setDate('my-date', \Carbon\Carbon::now(), 'd/m/y');
$myModel->json_meta->setNow('my-date', 'd/m/y');
$myModel->json_meta->getDateAttribute('my-date'); // Carbon::parse()
$myModel->json_meta->getDateTimeFromFormat('my-date', 'd/m/y');
$myModel->json_meta->getDateTimeFromFormats('my-date', ['d/m/y', 'd/m/Y', 'Y-m-d']);

$myModel->json_meta->inscremt('login_count');
$myModel->json_meta->decrement('allowed_attempts');

$myModel->json_meta->toMorph('user', $user);
$user = $myModel->json_meta->fromMorph('user');
```

Cast json column to array of objects

```php
/**
 *  @property \JsonFieldCast\Json\ArrayOfJsonObjectsField $array_json_meta
 *  @property \JsonFieldCast\Json\ArrayOfJsonObjectsField $array_text_meta
 */
class MyModel extends Model
{
    protected $casts = [
        //...
        'array_json_meta'        => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
        'array_text_meta'        => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
    ];
}


$myModel = MyModel::find(123);

isset($myModel->array_json_meta[2]);

/** @var \JsonFieldCast\Json\JsonObject $item */
foreach ($myModel->array_json_meta as $item) {
    $name = $item->getAttribute('name');
    $date = $item->getDateTimeFromFormat('my-date', 'd/m/y');
}
```

### Custom castable objects

For your custom purposes you can use your own custom castable objects

```php
namespace App\Casts;

use JsonFieldCast\Casts\AbstractMeta;

class FormMeta extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \App\Casts\Json\FormMeta::class;
    }
}
```

```php
namespace App\Casts\Json;

use JsonFieldCast\Json\AbstractMeta;

class FormMeta extends AbstractMeta
{
    public function myCustomMethod(): int {
        return ((int) $this->getAttribute('foo.bar', 0)) + 25;
    }
}
```

```php
/**
 *  @property \App\Casts\Json\FormMeta $meta
 */
class Form extends Model
{
    protected $casts = [
        'meta' => \App\Casts\FormMeta::class,
    ];
}


$form = Form::find(123);
$form->meta->getAttribute('foo.bar', 0);
$form->meta->myCustomMethod();
```

### Dynamic castable objects

```php
namespace App\Casts;

use JsonFieldCast\Casts\AbstractMeta;

class FormMeta extends AbstractMeta
{
    protected function metaClass(): string
    {
        return \App\Casts\Json\AbstractFormMeta::class;
    }
}
```

```php
namespace App\Casts\Json;

use JsonFieldCast\Json\AbstractMeta;

abstract class AbstractFormMeta extends AbstractMeta
{
   public static function getCastableClassByModel(Model $model, array $data = []): ?string
    {
        return ($model->meta_type && class_exists($model->meta_type))
            ? $model->meta_type
            : null;;
    }
}
```

```php
/**
 *  @property \App\Casts\Json\AbstractFormMeta $meta
 */
class Form extends Model
{
    protected $casts = [
        'meta' => \App\Casts\FormMeta::class,
    ];
}


$formContactUs = Form::create([
    //...
    'meta_type' => ContactUsMeta::class
]);
$formJobRequest = Form::create([
    //...
    'meta_type' => JobRequestMeta::class
]);

$formContactUs->meta instanceof ContactUsMeta::class // true
$formJobRequest->meta instanceof JobRequestMeta::class // true
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
