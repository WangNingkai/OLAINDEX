<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Helpers;

use Hashids\Hashids;

/**
 * ID加密辅助类
 *
 * Class HashidsHelper
 * @package common\tools
 */
class HashidsHelper
{
    /**
     * 长度
     *
     * @var int
     */
    public static $length = 8;

    /**
     * @var \Hashids\Hashids
     */
    protected static $hashids;

    /**
     * 加密
     *
     * @param mixed ...$numbers
     * @return string
     */
    public static function encode(...$numbers)
    {
        return self::getHashids()->encode(...$numbers);
    }

    /**
     * 解密
     *
     * @param string $hash
     * @return array|mixed
     * @throws \Exception
     */
    public static function decode(string $hash)
    {
        $data = self::getHashids()->decode($hash);
        if (empty($data) || !is_array($data)) {
            return null;
        }

        return count($data) === 1 ? $data[0] : $data;
    }

    /**
     * @return Hashids
     */
    private static function getHashids()
    {
        if (!self::$hashids instanceof Hashids) {
            self::$hashids = new Hashids(config('app.key'), self::$length); // all lowercase
        }

        return self::$hashids;
    }
}
