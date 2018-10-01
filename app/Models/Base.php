<?php

namespace App\Models;

use App\Helpers\Tool;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    /**
     * 禁止被批量赋值的字段
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * 添加数据
     *
     * @param  array $data 需要添加的数据
     * @return bool        是否成功
     */
    public function storeData($data)
    {
        if (empty($data)) {
            Tool::showMessage('数据为空，添加失败',false);
            return false;
        }
        //添加数据
        $result = $this->query()->create($data);
        if ($result) {
            Tool::showMessage('添加成功');
            return $result->id;
        } else {
            Tool::showMessage('添加失败',false);
            return false;
        }
    }

    /**
     * 修改数据
     *
     * @param  array $map where条件
     * @param  array $data 需要修改的数据
     * @return bool        是否成功
     */
    public function updateData($map, $data)
    {
        $model = $this
            ->query()
            ->whereMap($map)
            ->get();
        // 当数据为空的时候
        if ($model->isEmpty()) {
            Tool::showMessage('数据为空，修改失败', false);
            return false;
        }
        foreach ($model as $k => $v) {
            $result = $v->forceFill($data)->save();
        }
        if ($result) {
            Tool::showMessage('修改成功');
            return $result;
        } else {
            Tool::showMessage('修改失败',false);
            return false;
        }
    }

    /**
     * 删除数据
     *
     * @param  array $map where 条件数组形式
     * @return bool         是否成功
     */
    public function destroyData($map)
    {
        $result = $this
            ->query()
            ->whereMap($map)
            ->delete();
        if ($result) {
            Tool::showMessage('删除成功');
            return $result;
        } else {
            Tool::showMessage('删除失败',false);
            return false;
        }
    }

    /**
     * 恢复数据
     *
     * @param $map
     *
     * @return bool
     */
    public function restoreData($map)
    {
        // 恢复
        $result = $this
            ->query()
            ->whereMap($map)
            ->restore();
        if ($result) {
            Tool::showMessage('恢复成功');
            return $result;
        } else {
            Tool::showMessage('恢复失败',false);
            return false;
        }
    }

    /**
     * 彻底删除
     *
     * @param $map
     *
     * @return bool
     */
    public function forceDeleteData($map)
    {
        // 彻底删除
        $result=$this
            ->query()
            ->whereMap($map)
            ->forceDelete();
        if ($result) {
            Tool::showMessage('彻底删除成功');
            return $result;
        }else{
            Tool::showMessage('彻底删除失败',false);
            return false;
        }
    }
    /**
     * 使用作用域扩展 Builder 链式操作
     *
     * 示例:
     * $map = [
     *     'id' => ['in', [1,2,3]],
     *     'category_id' => ['<>', 9],
     *     'tag_id' => 10
     * ]
     *
     * @param $query
     * @param array $map
     * @return mixed
     */
    public function scopeWhereMap($query, array $map)
    {
        // 如果是空直接返回
        if (empty($map)) {
            return $query;
        }

        $where = 'where';
        if (isset($map['_logic'])) {
            $logic = strtolower($map['_logic']);
            $where = $logic == 'or' ? 'orWhere' : 'where';
            unset($map['_logic']);
        }
        // 判断各种方法
        foreach ($map as $k => $v) {
            if (is_array($v)) {
                $sign = strtolower($v[0]);
                switch ($sign) {
                    case 'in':
                        $query->{$where.'In'}($k, $v[1]);
                        break;
                    case 'notin':
                        $query->{$where.'NotIn'}($k, $v[1]);
                        break;
                    case 'between':
                        $query->{$where.'Between'}($k, $v[1]);
                        break;
                    case 'notbetween':
                        $query->{$where.'NotBetween'}($k, $v[1]);
                        break;
                    case 'null':
                        $query->{$where.'Null'}($k);
                        break;
                    case 'notnull':
                        $query->{$where.'NotNull'}($k);
                        break;
                    case '=':
                    case '>':
                    case '<':
                    case '<>':
                    case 'like':
                        $query->{$where}($k, $sign, $v[1]);
                        break;
                }
            } else {
                $query->$where($k, $v);
            }
        }
        return $query;
    }

    /**
     * 批量更新的方法
     * 示例参数
     * $multipleData = [
     *    [
     *        'name' => 'name 1' ,
     *        'date' => 'date 1'
     *     ],
     *     [
     *        'name' => 'name 2' ,
     *        'date' => 'date 2'
     *      ]
     *   ]
     *
     * @param array $multipleData
     * @return bool|int
     */
    function updateBatch($multipleData = []){
        if (empty($multipleData)) {
            return false;
        }
        // 获取表名
        $tableName = config('database.connections.mysql.prefix').$this->getTable();
        $updateColumn = array_keys($multipleData[0]);
        $referenceColumn = $updateColumn[0];
        unset($updateColumn[0]);
        $whereIn = "";
        // 组合sql语句
        $sql = "UPDATE ".$tableName." SET ";
        foreach ( $updateColumn as $uColumn ) {
            $sql .=  $uColumn." = CASE ";
            foreach( $multipleData as $data ) {
                $sql .= "WHEN ".$referenceColumn." = '".$data[$referenceColumn]."' THEN '".$data[$uColumn]."' ";
            }
            $sql .= "ELSE ".$uColumn." END, ";
        }
        foreach( $multipleData as $data ) {
            $whereIn .= "'".$data[$referenceColumn]."', ";
        }
        $sql = rtrim($sql, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
        // 更新
        $result = DB::update(DB::raw($sql));
        if (!$result) {
            abort('400');
        }
        return $result;
    }
}
