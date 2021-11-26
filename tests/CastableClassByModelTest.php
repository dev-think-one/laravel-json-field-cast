<?php

namespace JsonFieldCast\Tests;

use Illuminate\Support\Str;
use JsonFieldCast\Tests\Fixtures\Casts\Json\EditorContent;
use JsonFieldCast\Tests\Fixtures\Casts\Json\FormContent;
use JsonFieldCast\Tests\Fixtures\Models\User;

class CastableClassByModelTest extends TestCase
{

    /** @test */
    public function get_different_classes_by_model()
    {
        $user = User::create([
            'name'         => 'Test User',
            'email'        => 'test@test.home',
            'password'     => Str::random(),
            'content_type' => FormContent::class,
        ]);
        $this->assertTrue($user->exists());

        $this->assertInstanceOf(FormContent::class, $user->content);

        $user2 = User::create([
            'name'         => 'Test User',
            'email'        => 'test2@test.home',
            'password'     => Str::random(),
            'content_type' => EditorContent::class,
        ]);
        $this->assertTrue($user2->exists());

        $this->assertInstanceOf(EditorContent::class, $user2->content);
    }
}
