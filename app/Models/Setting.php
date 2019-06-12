<?php

namespace App\Models;

use App\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;
use Cache;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property mixed $value
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereMap($map)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use  HelperModel;

    /**
     * @var array $fillable
     */
    protected $fillable = ['name', 'value'];
    /**
     * @var array $casts
     */
    protected $casts = [
        'id' => 'int',
    ];

    /**
     * 如果 value 是 json 则转成数组
     *
     * @param $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return is_json($value) ? json_decode($value, true) : $value;
    }

    /**
     * 如果 value 是数组 则转成 json
     *
     * @param $value
     */
    public function setValueAttribute($value): void
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    public static function batchUpdate($data)
    {
        $editData = [];
        foreach ($data as $k => $v) {
            $editData[] = [
                'name' => $k,
                'value' => is_array($v) ? json_encode($v) : $v
            ];
        }
        // 查询数据库中是否有配置
        $saved = self::query()->pluck('name')->all();

        $newData = collect($editData)->filter(static function ($value) use ($saved) {
            return !in_array($value['name'], $saved, false);
        })->toArray();
        $editData = collect($editData)->reject(static function ($value) use ($saved) {
            return !in_array($value['name'], $saved, false);
        })->toArray();
        // 存在新数据先插入
        if ($newData) {
            self::query()->insert($newData);
        }

        $model = new self;
        $model->updateBatch($editData);

        Cache::forget('setting');

        return $data;
    }

}
