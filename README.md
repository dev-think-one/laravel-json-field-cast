# Laravel json field cast

Cast json field to object.

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-json-field-cast
```

## Usage

```injectablephp
/**
 *  @property \JsonFieldCast\Json\SimpleJsonField $json_meta
 *  @property \JsonFieldCast\Json\SimpleJsonField $text_meta
 */
class MyModel extends Model
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
//...
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
