<?php


namespace JsonFieldCast\Tests\Fixtures\Models;

/**
 * @property \JsonFieldCast\Json\SimpleJsonField                      $json_meta
 * @property \JsonFieldCast\Json\SimpleJsonField                      $text_meta
 * @property \JsonFieldCast\Json\FileJsonField                        $json_file
 * @property \JsonFieldCast\Json\FileJsonField                        $text_file
 * @property \JsonFieldCast\Json\ArrayOfJsonObjectsField              $array_json_meta
 * @property \JsonFieldCast\Json\ArrayOfJsonObjectsField              $array_text_meta
 * @property \JsonFieldCast\Tests\Fixtures\Casts\Json\AbstractContent $content
 */
class User extends \Illuminate\Foundation\Auth\User
{
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'json_meta'         => \JsonFieldCast\Casts\SimpleJsonField::class,
        'text_meta'         => \JsonFieldCast\Casts\SimpleJsonField::class,
        'json_file'         => \JsonFieldCast\Casts\FileJsonField::class,
        'text_file'         => \JsonFieldCast\Casts\FileJsonField::class,
        'array_json_meta'   => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
        'array_text_meta'   => \JsonFieldCast\Casts\ArrayOfJsonObjectsField::class,
        'content'           => \JsonFieldCast\Tests\Fixtures\Casts\ContentCast::class,
    ];
}
