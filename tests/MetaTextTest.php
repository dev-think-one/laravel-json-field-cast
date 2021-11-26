<?php

namespace JsonFieldCast\Tests;

use Illuminate\Support\Str;
use JsonFieldCast\Tests\Fixtures\Models\User;

class MetaTextTest extends TestCase
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
            'name'      => 'Other User',
            'email'     => 'other-test@test.home',
            'password'  => Str::random(),
            'text_meta' => [
                'position' => 'developer',
                'tags'     => [
                    'php',
                    'laravel',
                ],
            ],
        ]);
        $this->assertTrue($this->user2->exists());
    }

    /** @test */
    public function user_cast_field_to_object_if_null()
    {
        $this->assertNull($this->user->getRawOriginal('text_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $this->user->text_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $this->user->text_meta;

        $this->assertNull($meta->getAttribute('test'));
        $this->assertEquals('default', $meta->getAttribute('test', 'default'));
    }

    /** @test */
    public function user_cast_field_to_object_if_not_null()
    {
        $user = User::create([
            'name'      => 'Test User2',
            'email'     => 'test@test2.home',
            'password'  => Str::random(),
            'text_meta' => [
                'some_key' => 'my_value',
            ],
        ]);

        $this->assertNotNull($user->getRawOriginal('text_meta'));
        $this->assertStringContainsString('some_key', $user->getRawOriginal('text_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $user->text_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $user->text_meta;

        $this->assertNull($meta->getAttribute('test'));
        $this->assertEquals('default', $meta->getAttribute('test', 'default'));
        $this->assertEquals('my_value', $meta->getAttribute('some_key'));
    }


    /** @test */
    public function not_created_user_cast_field_to_object_if_not_null()
    {
        $user = new User([
            'name'      => 'Test User2',
            'email'     => 'test@test2.home',
            'password'  => Str::random(),
            'text_meta' => [
                'some_key' => 'my_value',
            ],
        ]);

        $this->assertNull($user->getRawOriginal('text_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $user->text_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $user->text_meta;

        $this->assertNull($meta->getAttribute('test'));
        $this->assertEquals('default', $meta->getAttribute('test', 'default'));
        $this->assertEquals('my_value', $meta->getAttribute('some_key'));
    }

    /** @test */
    public function method_has_attribute()
    {
        $this->assertTrue($this->user2->text_meta->hasAttribute('position'));
        $this->assertTrue($this->user2->text_meta->hasAttribute('tags'));
        $this->assertTrue($this->user2->text_meta->hasAttribute('tags.1'));
        $this->assertFalse($this->user2->text_meta->hasAttribute('tags.3'));
        $this->assertFalse($this->user2->text_meta->hasAttribute('some'));
        $this->assertFalse($this->user2->text_meta->hasAttribute('position.2'));
    }

    /** @test */
    public function method_get_raw_data_except()
    {
        $data = $this->user2->text_meta->getRawDataExcept(['position']);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
    }

    /** @test */
    public function method_get_raw_data_except_with_empty_array()
    {
        $data = $this->user2->text_meta->getRawDataExcept();
        $this->assertArrayHasKey('position', $data);
        $this->assertEquals('developer', $data['position']);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
    }

    /** @test */
    public function method_set_data()
    {
        $this->user2->text_meta->setData([
            'example' => 'my data',
        ]);
        $this->assertArrayNotHasKey('position', $this->user2->text_meta->toArray());
        $this->assertArrayNotHasKey('tags', $this->user2->text_meta->jsonSerialize());
        $this->assertEquals('my data', $this->user2->text_meta->getAttribute('example'));
    }

    /** @test */
    public function method_get_raw_data()
    {
        $data = $this->user2->text_meta->getRawData(['tags']);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
        $this->assertArrayNotHasKey('position', $data);
        $this->assertCount(1, $data);
    }

    /** @test */
    public function method_get_raw_data_with_empty_array()
    {
        $data = $this->user2->text_meta->getRawData();
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
        $this->assertArrayHasKey('position', $data);
        $this->assertEquals('developer', $data['position']);
        $this->assertCount(2, $data);
    }

    /** @test */
    public function method_get_raw_data_with_string()
    {
        $data = $this->user2->text_meta->getRawData('tags.0');
        $this->assertEmpty($data);
        $this->assertIsArray($data);

        $data = $this->user2->text_meta->getRawData('tags');
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('php', $data['tags'][0]);
        $this->assertEquals('laravel', $data['tags'][1]);

        $data = $this->user2->text_meta->getRawData('position');
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals('developer', $data['position']);
    }

    /** @test */
    public function method_set_attribute()
    {
        $this->assertNull($this->user2->text_meta->getAttribute('example'));

        $this->user2->text_meta->setAttribute('example', 'My Example');
        $this->user2->save();

        $user = User::find($this->user2->getKey());
        $this->assertEquals('My Example', $user->text_meta->getAttribute('example'));
    }

    /** @test */
    public function method_remove_attribute()
    {
        $this->assertEquals('laravel', $this->user2->text_meta->getAttribute('tags.1'));

        $this->user2->text_meta->removeAttribute('tags');
        $this->assertNull($this->user2->text_meta->getAttribute('tags'));

        $this->user2->save();

        $user = User::find($this->user2->getKey());
        $this->assertNull($user->text_meta->getAttribute('tags'));
    }

    /** @test */
    public function is_empty()
    {
        /** @var User $user */
        $user = User::create([
            'name'      => __FUNCTION__,
            'email'     => __FUNCTION__ . '@test.home',
            'password'  => Str::random(),
            'text_meta' => [
                'position' => 'developer',
                'tags'     => [
                    'php',
                    'laravel',
                ],
            ],
        ]);

        $this->assertFalse($user->text_meta->isEmpty());

        $user->text_meta->setData([]);
        $this->assertTrue($user->text_meta->isEmpty());
    }
}
