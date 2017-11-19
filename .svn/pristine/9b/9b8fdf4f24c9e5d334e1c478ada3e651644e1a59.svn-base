<?php

use Com\RedisModel;

/**
 * redis缓存通用操作
 */
class CommonRedis {

    /**
     *  Redies根据key取用户编码
     *  @param string $key
     */
    function Rediesgetucode($key) {
        $Redis = new RedisModel();
        $result = $Redis->get($key);
        $arr = objarray_to_array(json_decode($result));
        if (count($arr) > 0 && is_array($arr)) {
            $result = $arr;
        }
        $Redis->closeping();
        return $result;
    }

    /**
     *  Redies存储用户缓存
     */
    function RediesStoreSram($key,$value,$expire) {
        $Redis = new RedisModel();
        if(is_null($value)) { // 删除数据
            $result = $Redis->delete($key);
        } else { // 存储数据
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if ($expire == 0 && isset($expire)) {
                $result = $Redis->set($key,$value,0,0,0,0);
            } else {
                if (is_numeric($expire)) {
                    $result = $Redis->set($key,$value,0,0,$expire);
                } else {
                    $result = $Redis->set($key,$value,0,0,7200);
                }
            }
        }

        $Redis->closeping();
        return $result;
    }


}
