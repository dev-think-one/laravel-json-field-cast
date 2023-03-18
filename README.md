# Laravel json field cast

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-json-field-cast?color=%234dc71f)](https://github.com/yaroslawww/laravel-json-field-cast/blob/master/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-json-field-cast)](https://packagist.org/packages/yaroslawww/laravel-json-field-cast)
[![Total Downloads](https://img.shields.io/packagist/dt/yaroslawww/laravel-json-field-cast)](https://packagist.org/packages/yaroslawww/laravel-json-field-cast)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-json-field-cast/?branch=master)

Cast json field to object.

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-json-field-cast
```

## Usage

### Out of the box

```injectablephp
/**
 *  @property \JsonFieldCast\Json\SimpleJsonField $json_meta
 *  @property \JsonFieldCast\Json\SimpleJsonField $text_meta
 *  @property \JsonFieldCast\Json\ArrayOfJsonObjectsField $array_json_meta
 *  @property \JsonFieldCast\Json\ArrayOfJsonObjectsField $array_text_meta
 */
class MyModel extends Model
{
    protected $casts = [
        //...
        'json_meta'              => \JsonFieldCast\Casts\SimpleJsonField::class,
        'text_meta'              => \JsonFieldCast\Casts\SimpleJsonField::class,
        'array_json_meta'        => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
        'array_text_meta'        => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
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

### Custom castable objects

```injectablephp
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

```injectablephp
namespace App\Casts\Json;

use JsonFieldCast\Json\AbstractMeta;

class FormMeta extends AbstractMeta
{
    public function myCustomMethod(): int {
        return ((int) $this->getAttribute('foo.bar', 0)) + 25;
    }
}
```

```injectablephp
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

```injectablephp
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

```injectablephp
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

```injectablephp
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
