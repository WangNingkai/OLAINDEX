<?php

if (!function_exists('is_json')) {
    /**
     * 判断字符串是否是json
     *
     * @param $json
     * @return bool
     */
    function is_json($json)
    {
        json_decode($json);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
if (!function_exists('trans_time')) {
    /**
     * 优化时间显示
     *
     * @param mixed $sTime 源时间
     * @param int $format
     * @return false|string
     */
    function trans_time($sTime, $format = 0)
    {
        # 如果是日期格式的时间;则先转为时间戳
        if (!is_integer($sTime)) {
            $sTime = strtotime($sTime);
        }
        //sTime=源时间，cTime=当前时间，dTime=时间差
        $cTime = time();
        $dTime = $cTime - $sTime;
        // 计算两个时间之间的日期差
        $date1 = date_create(date("Y-m-d", $cTime));
        $date2 = date_create(date("Y-m-d", $sTime));
        $diff = date_diff($date1, $date2);
        $dDay = $diff->days;
        if ($dTime == 0) {
            return "1秒前";
        } elseif ($dTime < 60 && $dTime > 0) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600 && $dTime > 0) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay == 1) {
            return date("昨天 H:i", $sTime);
        } elseif ($dDay == 2) {
            return date("前天 H:i", $sTime);
        } elseif ($format == 1) {
            return date("m-d H:i", $sTime);
        } else {
            if (date('Y', $cTime) != date('Y', $sTime)) {
                return date("Y-n-j", $sTime);
            } else {
                return date("n-j", $sTime);
            }
        }
    }
}

if (!function_exists('micro_time')) {
    /**
     * 微秒时间戳
     * @return float
     */
    function micro_time()
    {
        list($micro_sec, $sec) = explode(' ', microtime());
        $micro_time = (float)sprintf('%.0f', (floatval($micro_sec) + floatval($sec)) * 1000);
        return $micro_time;
    }
}

