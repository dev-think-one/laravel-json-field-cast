<?php

namespace JsonFieldCast\Tests;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonFieldCast\Tests\Fixtures\Models\User;

class MetaJsonTest extends TestCase
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
            'json_meta' => [
                'position' => 'developer',
                'tags'     => [
                    'php',
                    'laravel',
                ],
                'some_date'      => '2020-03-05',
                'formatted_date' => '04/02/2019',
                'foo_number'     => '3.2',
            ],
        ]);
        $this->assertTrue($this->user2->exists());
    }


    /** @test */
    public function user_cast_field_to_object_if_null()
    {
        $this->assertNull($this->user->getRawOriginal('json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $this->user->json_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $this->user->json_meta;

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
            'json_meta' => [
                'some_key' => 'my_value',
            ],
        ]);

        $this->assertNotNull($user->getRawOriginal('json_meta'));
        $this->assertStringContainsString('some_key', $user->getRawOriginal('json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $user->json_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $user->json_meta;

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
            'json_meta' => [
                'some_key' => 'my_value',
            ],
        ]);

        $this->assertNull($user->getRawOriginal('json_meta'));
        $this->assertInstanceOf(\JsonFieldCast\Json\SimpleJsonField::class, $user->json_meta);
        /** @var \JsonFieldCast\Json\SimpleJsonField::class $meta */
        $meta = $user->json_meta;

        $this->assertNull($meta->getAttribute('test'));
        $this->assertEquals('default', $meta->getAttribute('test', 'default'));
        $this->assertEquals('my_value', $meta->getAttribute('some_key'));
    }

    /** @test */
    public function method_has_attribute()
    {
        $this->assertTrue($this->user2->json_meta->hasAttribute('position'));
        $this->assertTrue($this->user2->json_meta->hasAttribute('tags'));
        $this->assertTrue($this->user2->json_meta->hasAttribute('tags.1'));
        $this->assertFalse($this->user2->json_meta->hasAttribute('tags.3'));
        $this->assertFalse($this->user2->json_meta->hasAttribute('some'));
        $this->assertFalse($this->user2->json_meta->hasAttribute('position.2'));
    }

    /** @test */
    public function method_get_date_attribute()
    {
        $this->assertNull($this->user2->json_meta->getDateAttribute('position'));
        $this->assertNull($this->user2->json_meta->getDateAttribute('not_exists'));
        $date = $this->user2->json_meta->getDateAttribute('some_date');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2020-03-05', $date->format('Y-m-d'));
    }

    /** @test */
    public function method_get_date_from_format_throw_exception_on_wrong_format()
    {
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);
        $this->assertNull($this->user2->json_meta->getDateTimeFromFormat('position'));
    }

    /** @test */
    public function method_get_date_from_format_attribute()
    {
        $this->assertNull($this->user2->json_meta->getDateTimeFromFormat('not_exists'));
        $date = $this->user2->json_meta->getDateTimeFromFormat('formatted_date', 'd/m/Y');
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2019-02-04', $date->format('Y-m-d'));
    }

    /** @test */
    public function method_get_date_from_formats_throw_exception_on_wrong_format()
    {
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);
        $this->assertNull($this->user2->json_meta->getDateTimeFromFormats('position', ['Y-m-d', 'd/m/y', 'd/m/Y']));
    }

    /** @test */
    public function method_get_date_from_formats_throw_exception_on_wrong_format2()
    {
        $this->expectException(\Carbon\Exceptions\InvalidFormatException::class);
        $this->assertNull($this->user2->json_meta->getDateTimeFromFormats('formatted_date', ['Y-m-d', 'd/m/y', 'D/m/Y']));
    }

    /** @test */
    public function method_get_date_from_formats_attribute()
    {
        $this->assertNull($this->user2->json_meta->getDateTimeFromFormats('not_exists'));
        $date = $this->user2->json_meta->getDateTimeFromFormats('formatted_date', ['Y-m-d', 'd/m/y', 'd/m/Y']);
        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertEquals('2019-02-04', $date->format('Y-m-d'));
    }

    /** @test */
    public function method_increment()
    {
        $this->assertEquals(1, $this->user2->json_meta->increment('not_exists')->getAttribute('not_exists'));
        $this->assertEquals(4.23, $this->user2->json_meta->increment('not_exists_bar', 4.23)->getAttribute('not_exists_bar'));
        $this->assertEquals(4.2, $this->user2->json_meta->increment('foo_number')->getAttribute('foo_number'));
        $this->assertEquals(8.43, $this->user2->json_meta->increment('foo_number', 4.23)->getAttribute('foo_number'));
    }

    /** @test */
    public function method_increment_return_error_if_not_numeric()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->user2->json_meta->increment('position');
    }

    /** @test */
    public function method_decrement()
    {
        $this->assertEquals(-1, $this->user2->json_meta->decrement('not_exists')->getAttribute('not_exists'));
        $this->assertEquals(-4.23, $this->user2->json_meta->decrement('not_exists_bar', 4.23)->getAttribute('not_exists_bar'));
        $this->assertEquals(2.2, $this->user2->json_meta->decrement('foo_number')->getAttribute('foo_number'));
        $this->assertEquals(-2.03, round($this->user2->json_meta->decrement('foo_number', 4.23)->getAttribute('foo_number'), 2));
    }

    /** @test */
    public function method_decrement_return_error_if_not_numeric()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->user2->json_meta->decrement('position');
    }

    /** @test */
    public function method_get_raw_data_except()
    {
        $data = $this->user2->json_meta->getRawDataExcept([ 'position' ]);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
    }

    /** @test */
    public function method_get_raw_data_except_with_empty_array()
    {
        $data = $this->user2->json_meta->getRawDataExcept();
        $this->assertArrayHasKey('position', $data);
        $this->assertEquals('developer', $data['position']);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
    }

    /** @test */
    public function method_set_data()
    {
        $this->user2->json_meta->setData([
            'example' => 'my data',
        ]);
        $this->assertArrayNotHasKey('position', $this->user2->json_meta->toArray());
        $this->assertArrayNotHasKey('tags', $this->user2->json_meta->jsonSerialize());
        $this->assertEquals('my data', $this->user2->json_meta->getAttribute('example'));
    }

    /** @test */
    public function method_get_raw_data()
    {
        $data = $this->user2->json_meta->getRawData([ 'tags' ]);
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
        $this->assertArrayNotHasKey('position', $data);
        $this->assertCount(1, $data);
    }

    /** @test */
    public function method_get_raw_data_with_empty_array()
    {
        $data = $this->user2->json_meta->getRawData();
        $this->assertArrayHasKey('tags', $data);
        $this->assertCount(2, $data['tags']);
        $this->assertEquals('laravel', $data['tags'][1]);
        $this->assertArrayHasKey('position', $data);
        $this->assertEquals('developer', $data['position']);
        $this->assertCount(5, $data);
    }

    /** @test */
    public function method_set_attribute()
    {
        $this->assertNull($this->user2->json_meta->getAttribute('example'));

        $this->user2->json_meta->setAttribute('example', 'My Example');
        $this->user2->save();

        $user = User::find($this->user2->getKey());
        $this->assertEquals('My Example', $user->json_meta->getAttribute('example'));
    }

    /** @test */
    public function method_remove_attribute()
    {
        $this->assertEquals('laravel', $this->user2->json_meta->getAttribute('tags.1'));

        $this->user2->json_meta->removeAttribute('tags');
        $this->assertNull($this->user2->json_meta->getAttribute('tags'));

        $this->user2->save();

        $user = User::find($this->user2->getKey());
        $this->assertNull($user->json_meta->getAttribute('tags'));
    }

    /** @test */
    public function is_empty()
    {
        /** @var User $user */
        $user = User::create([
            'name'      => __FUNCTION__,
            'email'     => __FUNCTION__ . '@test.home',
            'password'  => Str::random(),
            'json_meta' => [
                'position' => 'developer',
                'tags'     => [
                    'php',
                    'laravel',
                ],
            ],
        ]);

        $this->assertFalse($user->json_meta->isEmpty());

        $user->json_meta->setData([]);
        $this->assertTrue($user->json_meta->isEmpty());
    }

    /** @test */
    public function to_morph()
    {
        /** @var Model $fakeUser */
        $fakeUser = User::create([
            'name'     => __FUNCTION__,
            'email'    => __FUNCTION__ . '@test.home',
            'password' => Str::random(),
        ]);

        $this->user2->json_meta->toMorph('user', $fakeUser);
        $this->user2->save();
        Arr::get($this->user2->json_meta->getRawData(), 'user.id', $fakeUser->getKey());
        Arr::get($this->user2->json_meta->getRawData(), 'user.class', $fakeUser->getMorphClass());

        $this->user2->json_meta->toMorph('user1', $fakeUser, 'idFoo', 'classBar');
        $this->user2->save();
        Arr::get($this->user2->json_meta->getRawData(), 'user1.idFoo', $fakeUser->getKey());
        Arr::get($this->user2->json_meta->getRawData(), 'user1.classBar', $fakeUser->getMorphClass());

        $this->user2->json_meta->toMorph('', $fakeUser);
        $this->user2->save();
        Arr::get($this->user2->json_meta->getRawData(), 'id', $fakeUser->getKey());
        Arr::get($this->user2->json_meta->getRawData(), 'class', $fakeUser->getMorphClass());
    }

    /** @test */
    public function from_morph()
    {
        /** @var Model $fakeUser */
        $fakeUser = User::create([
            'name'      => __FUNCTION__,
            'email'     => __FUNCTION__ . '@test.home',
            'password'  => Str::random(),
            'json_meta' => [
                'position' => 'developer',

                'some_date'      => '2020-03-05',
                'formatted_date' => '04/02/2019',
                'foo_number'     => '3.2',
            ],
        ]);

        $this->user2->json_meta->setData([
            'user' => [
                'id'    => $fakeUser->getKey(),
                'class' => $fakeUser->getMorphClass(),
            ],
            'user1' => [
                'idFoo'    => $fakeUser->getKey(),
                'classBar' => $fakeUser->getMorphClass(),
            ],
            'id'    => $fakeUser->getKey(),
            'class' => $fakeUser->getMorphClass(),
        ]);
        $this->user2->save();

        $user = $this->user2->json_meta->fromMorph('user');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($fakeUser->getKey(), $user->getKey());

        $user = $this->user2->json_meta->fromMorph('user1');
        $this->assertNull($user);

        $user = $this->user2->json_meta->fromMorph('user1', null, 'idFoo', 'classBar');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($fakeUser->getKey(), $user->getKey());

        $user = $this->user2->json_meta->fromMorph('');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($fakeUser->getKey(), $user->getKey());
    }

    /** @test */
    public function from_morph_returns_defaultValue()
    {
        /** @var Model $defaultUser */
        $defaultUser = User::create([
            'name'      => __FUNCTION__,
            'email'     => __FUNCTION__ . '@test.home',
            'password'  => Str::random(),
        ]);
        $this->user2->json_meta->setData([
            'user_wrong_class' => [
                'id'    => '2',
                'class' => 'fake',
            ],
            'user_wrong_id' => [
                'id'    => '9999',
                'class' => $defaultUser->getMorphClass(),
            ],
        ]);
        $this->user2->save();

        $user = $this->user2->json_meta->fromMorph('fake_user', $defaultUser);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($defaultUser->getKey(), $user->getKey());

        $user = $this->user2->json_meta->fromMorph('user_wrong_class', $defaultUser);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($defaultUser->getKey(), $user->getKey());

        $user = $this->user2->json_meta->fromMorph('user_wrong_id', $defaultUser);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($defaultUser->getKey(), $user->getKey());
    }
}
