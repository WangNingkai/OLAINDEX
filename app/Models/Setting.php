<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use App\Models\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;
use Cache;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property mixed $value
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereMap(array $map)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
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
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    public static $setting = [
        'site_name' => 'OLAINDEX',
        'site_theme' => 'lux',
        'cache_expires' => 1800,
        'copyright' => '',
        'stats_code' => '',
        'access_token' => '',
        'show_image' => 'png',
        'show_video' => 'mp4',
        'show_dash' => 'avi',
        'show_audio' => 'mp3',
        'show_doc' => 'doc',
        'show_code' => 'php',
        'show_stream' => 'text',
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

    /**
     * 批量更新
     * @param $data
     * @return mixed
     */
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

        $newData = collect($editData)->filter(function($value) use ($saved) {
            return !in_array($value['name'], $saved, false);
        })->toArray();
        $editData = collect($editData)->reject(function($value) use ($saved) {
            return !in_array($value['name'], $saved, false);
        })->toArray();
        // 存在新数据先插入
        if ($newData) {
            self::query()->insert($newData);
        }

        $model = new self;
        $model->updateBatch($editData);

        Cache::forget('settings');

        return $data;
    }
}
