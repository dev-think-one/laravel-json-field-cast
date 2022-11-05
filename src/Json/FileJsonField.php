<?php

namespace JsonFieldCast\Json;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileJsonField extends AbstractMeta
{
    public static string $fieldName      = 'name';
    public static string $fieldDate      = 'date';
    public static string $fieldPath      = 'path';
    public static string $fieldDisk      = 'disk';
    public static string $fieldSize      = 'size';
    public static string $fieldExtension = 'extension';

    public static string $dateFormat = 'Y-m-d H:i:s';

    /**
     * Is old file should be deleted before store new.
     *
     * @var bool
     */
    protected bool $unique;

    public function __construct(Model $model, array $data = [], bool $unique = true)
    {
        parent::__construct($model, $data);
        $this->unique = $unique;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateFileName(UploadedFile $file): string
    {
        $suffix = ".{$file->extension()}";

        return Str::limit(Str::slug(Str::beforeLast($file->getClientOriginalName(), $suffix)), 50, '') . $suffix;
    }

    /**
     * @param UploadedFile $file
     * @param string|null $disk
     * @param string $path
     *
     * @return static
     */
    public function storeUploadedFile(UploadedFile $file, ?string $disk = null, string $path = ''): static
    {
        if ($this->unique) {
            $this->delete();
        }

        $filename = $this->generateFileName($file);

        $filePath = $file->storeAs(
            $path,
            $filename,
            [ 'disk' => $disk ]
        );

        $this->setData([
            static::$fieldName      => $filename,
            static::$fieldDate      => Carbon::now()->format(static::$dateFormat),
            static::$fieldPath      => $filePath,
            static::$fieldDisk      => $disk,
            static::$fieldSize      => $file->getSize(),
            static::$fieldExtension => $file->extension(),
        ]);

        return $this;
    }

    /**
     * Unlink file and clear data.
     *
     * @return static
     */
    public function delete(): static
    {
        if ($this->exists()) {
            $path = $this->getAttribute(static::$fieldPath);
            Storage::disk($this->getAttribute(static::$fieldDisk))->delete($path);
        }

        $this->setData([]);

        return $this;
    }

    /**
     * Check is current file exists.
     *
     * @return bool
     */
    public function exists(): bool
    {
        $path = $this->getAttribute(static::$fieldPath);

        if ($path) {
            return Storage::disk($this->getAttribute(static::$fieldDisk))->exists($path);
        }

        return false;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function fileName(): ?string
    {
        $fileName = $this->getAttribute(static::$fieldName);
        if (!is_string($fileName)) {
            return null;
        }

        return $fileName ?: null;
    }

    /**
     * Get absolute path to file
     *
     * @return string|null
     */
    public function filePath(): ?string
    {
        $path = $this->getAttribute(static::$fieldPath);

        if ($path) {
            return Storage::disk($this->getAttribute(static::$fieldDisk))->path($path);
        }

        return null;
    }

    /**
     * Get file url
     *
     * @return string|null
     */
    public function fileUrl(): ?string
    {
        $path = $this->getAttribute(static::$fieldPath);

        if ($path) {
            return Storage::disk($this->getAttribute(static::$fieldDisk))->url($path);
        }

        return null;
    }

    /**
     * Get file stream
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function fileDownload(): ?StreamedResponse
    {
        $path = $this->getAttribute(static::$fieldPath);

        if ($path) {
            return Storage::disk($this->getAttribute(static::$fieldDisk))->download($path);
        }

        return null;
    }
}
