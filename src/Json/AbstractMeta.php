<?php


namespace JsonFieldCast\Json;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static string|null getCastableClassByModel(Model $model, array $data = [])
 */
abstract class AbstractMeta implements \JsonSerializable
{
    use HasDataArrayWithAttributes;

    protected Model $model;

    protected array $data;

    public function __construct(Model $model, array $data = [])
    {
        $this->data  = $data;
        $this->model = $model;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getRawDataExcept(array $keys = []): array
    {
        if (!empty($keys)) {
            return array_diff_key($this->data, array_flip($keys));
        }

        return $this->data;
    }
}
