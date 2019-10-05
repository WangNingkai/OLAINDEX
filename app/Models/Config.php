<?php


namespace App\Models;

use App\Traits\HelperModel;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HelperModel;

    protected $table = 'config';

    protected $fillable = ['name', 'value'];

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
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * 获取配置项
     * @param $key
     * @param string $default
     * @return string
     */
    public static function get($key, $default = '')
    {
        $result = Config::query()->where('name', $key)->value('value');
        return $result ?: $default;

    }

    /**
     * 设置配置项
     * @param $key
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function set($key, $value)
    {
        return Config::query()->updateOrCreate(['name' => $key], ['value' => $value]);
    }

    /**
     * 获取全部配置项
     * @return array
     */
    public static function getAll()
    {
        $config = Config::all()->toArray();
        $configData = [];
        foreach ($config as $detail) {
            $configData[$detail['name']] = $detail['value'];
        }
        return $configData;
    }

    /**
     * 批量更新或插入
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
        $saved = Config::query()->pluck('name')->all();

        $newData = collect($editData)->filter(function ($value, $key) use ($saved) {
            return !in_array($value['name'], $saved);
        })->toArray();
        $editData = collect($editData)->reject(function ($value, $key) use ($saved) {
            return !in_array($value['name'], $saved);
        })->toArray();
        // 存在新数据先插入
        if ($newData) {
            Config::query()->insert($newData);
        }

        $model = new Config;
        $model->updateBatch($editData);
        return $data;
    }

}
