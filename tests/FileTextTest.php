<?php

namespace JsonFieldCast\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use JsonFieldCast\Json\FileJsonField;
use JsonFieldCast\Tests\Fixtures\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileTextTest extends TestCase
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

        if (!empty(FileJsonField::$fieldDisk)) {
            $this->user2 = User::create([
                'name'      => 'Other User',
                'email'     => 'other-test@test.home',
                'password'  => Str::random(),
                'text_file' => [
                    FileJsonField::$fieldName      => 'File name',
                    FileJsonField::$fieldDate      => '2022-01-02 11:22:00',
                    FileJsonField::$fieldPath      => 'file_name.pdf',
                    FileJsonField::$fieldDisk      => 'default',
                    FileJsonField::$fieldSize      => 123456,
                    FileJsonField::$fieldExtension => 'pdf',
                ],
            ]);
        }
        $this->assertTrue($this->user2->exists());
    }

    /** @test */
    public function user_cast_field_to_object_if_null()
    {
        $this->assertNull($this->user->getRawOriginal('text_file'));
        $this->assertInstanceOf(\JsonFieldCast\Json\FileJsonField::class, $this->user->text_file);
        /** @var \JsonFieldCast\Json\FileJsonField::class $file */
        $file = $this->user->text_file;

        $this->assertNull($file->getAttribute('test'));
        $this->assertEquals('default', $file->getAttribute('test', 'default'));
        $this->assertFalse($file->exists());
        $this->assertNull($file->fileName());
        $this->assertNull($file->filePath());
        $this->assertNull($file->fileUrl());
        $this->assertNull($file->fileDownload());
    }

    /** @test */
    public function cast_upload_file()
    {
        $this->user->text_file->storeUploadedFile(UploadedFile::fake()->image('My Avatar.jpg'));
        $this->user->save();
        $this->user->refresh();

        $this->assertTrue($this->user->text_file->exists());
        $this->assertEquals('my-avatar.jpg', $this->user->text_file->fileName());
        $this->assertEquals(storage_path('app/my-avatar.jpg'), $this->user->text_file->filePath());
        $this->assertEquals('/storage/my-avatar.jpg', $this->user->text_file->fileUrl());
        $this->assertInstanceOf(StreamedResponse::class, $this->user->text_file->fileDownload());
    }

    /** @test */
    public function cast_delete_file()
    {
        $this->user->text_file->storeUploadedFile(UploadedFile::fake()->image('My File.jpg'));
        $this->user->save();
        $this->user->refresh();

        $filePath = $this->user->text_file->filePath();
        $this->assertTrue(file_exists($filePath));
        $this->assertTrue($this->user->text_file->exists());
        $this->user->text_file->delete();
        $this->assertFalse(file_exists($filePath));
        $this->assertFalse($this->user->text_file->exists());
    }
}
