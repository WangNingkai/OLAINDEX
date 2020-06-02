<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models\Traits;

use DB;

trait HelperModel
{
    /**
     * 批量更新
     * @param array $multipleData
     * @return bool|int
     */
    public function updateBatch($multipleData = [])
    {
        if (empty($multipleData)) {
            return false;
        }
        // 获取表名
        $tableName = config('database.connections.mysql.prefix') . $this->getTable();
        $firstRow = current($multipleData);
        $updateColumn = array_keys($firstRow);
        // 默认以id为条件更新，如果没有ID则以第一个字段为条件
        $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
        unset($updateColumn[0]);
        $updateSql = "UPDATE " . $tableName . " SET ";
        $sets = [];
        $bindings = [];
        foreach ($updateColumn as $uColumn) {
            $setSql = "`" . $uColumn . "` = CASE ";
            foreach ($multipleData as $data) {
                $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                $bindings[] = $data[$referenceColumn];
                $bindings[] = $data[$uColumn];
            }
            $setSql .= "ELSE `" . $uColumn . "` END ";
            $sets[] = $setSql;
        }
        $updateSql .= implode(', ', $sets);
        $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
        $bindings = array_merge($bindings, $whereIn);
        $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
        $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
        // 传入预处理sql语句和对应绑定数据
        return DB::update($updateSql, $bindings);
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
     * @param $map
     * @return mixed
     */
    public function scopeWhereMap($query, array $map)
    {
        // 如果是空直接返回
        if (empty($map)) {
            return $query;
        }
        // 判断关系是 and 还是 or
        $where = 'where';
        if (isset($map['_logic'])) {
            $logic = strtolower($map['_logic']);
            $where = $logic === 'or' ? 'orWhere' : 'where';
            unset($map['_logic']);
        }
        // 判断各种方法
        foreach ($map as $k => $v) {
            if (is_array($v)) {
                $sign = strtolower($v[0]);
                switch ($sign) {
                    case 'in':
                        $query->{$where . 'In'}($k, $v[1]);
                        break;
                    case 'notin':
                        $query->{$where . 'NotIn'}($k, $v[1]);
                        break;
                    case 'between':
                        $query->{$where . 'Between'}($k, $v[1]);
                        break;
                    case 'notbetween':
                        $query->{$where . 'NotBetween'}($k, $v[1]);
                        break;
                    case 'null':
                        $query->{$where . 'Null'}($k);
                        break;
                    case 'notnull':
                        $query->{$where . 'NotNull'}($k);
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
}
