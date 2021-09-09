<?php

namespace JsonFieldCast\Tests;

use Illuminate\Support\Str;
use JsonFieldCast\Tests\Fixtures\Models\User;

class GeneralJsonTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@test.home',
            'password' => Str::random(),
        ]);

        $this->assertTrue($this->user->exists());
    }

    /** @test */
    public function other_filed_not_casted()
    {
        $this->assertNull($this->user->getRawOriginal('notfield'));
        $this->assertNull($this->user->notfield);
    }
}
