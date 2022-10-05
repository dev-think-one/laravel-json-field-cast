<?php

namespace JsonFieldCast\Tests;

use Illuminate\Support\Str;
use JsonFieldCast\Json\JsonObject;
use JsonFieldCast\Tests\Fixtures\Models\User;

class ArrayMetaJsonTest extends TestCase
{
    protected User $user;
    protected User $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@test.home',
            'password' => Str::random(),
        ]);
        $this->assertTrue($this->user->exists());

        $this->user2 = User::create([
            'name'            => 'Other User',
            'email'           => 'other-test@test.home',
            'password'        => Str::random(),
            'array_json_meta' => [
                [
                    'position' => 'developer',
                    'tags'     => [
                        'php',
                        'laravel',
                    ],
                    'some_date' => '2020-03-05',
                ],
                'bla-bla',
                [
                    'position' => 'manager',
                    'tags'     => [
                        'trello',
                        'tables',
                    ],
                    'some_date' => '2022-03-05',
                ],
            ],
        ]);
        $this->assertTrue($this->user2->exists());
    }


    /** @test */
    public function user_cast_field_to_object_if_null()
    {
        $this->assertNull($this->user->getRawOriginal('array_json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\ArrayOfJsonObjectsField::class, $this->user->array_json_meta);
        /** @var \JsonFieldCast\Json\ArrayOfJsonObjectsField $meta */
        $meta = $this->user->array_json_meta;

        $this->assertCount(0, $meta);
        $this->assertNull($meta[0]);
    }

    /** @test */
    public function user_cast_field_to_object_if_not_null()
    {
        $this->assertNotNull($this->user2->getRawOriginal('array_json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\ArrayOfJsonObjectsField::class, $this->user2->array_json_meta);
        /** @var \JsonFieldCast\Json\ArrayOfJsonObjectsField $meta */
        $meta = $this->user2->array_json_meta;

        $this->assertCount(2, $meta);
        $this->assertInstanceOf(JsonObject::class, $meta[0]);
        $this->assertInstanceOf(JsonObject::class, $meta[1]);
        $this->assertEquals('tables', $meta[1]->getAttribute('tags.1'));
        $this->assertEquals('2020-03-05', $meta[0]->getDateAttribute('some_date')->format('Y-m-d'));
    }

    /** @test */
    public function iterable()
    {
        $this->assertNotNull($this->user2->getRawOriginal('array_json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\ArrayOfJsonObjectsField::class, $this->user2->array_json_meta);
        /** @var \JsonFieldCast\Json\ArrayOfJsonObjectsField $meta */
        $meta = $this->user2->array_json_meta;

        /**
         * @var JsonObject $item
         */
        foreach ($meta as $key => $item) {
            $this->assertInstanceOf(JsonObject::class, $item);
            $this->assertEquals(match ($key) {
                0 => 'developer',
                1 => 'manager',
            }, $item->getAttribute('position'));
        }
    }
}
