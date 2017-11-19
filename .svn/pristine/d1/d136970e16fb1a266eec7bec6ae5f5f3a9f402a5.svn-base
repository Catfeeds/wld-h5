<?php

 /**
  * geo 保存用户位置
  * @param
  * 
  */ 
class LocalLbs {

    /**
    * 更新用户geo位置信息
    * @param ucode
    */
    function UpinfoLocal($ucode) {
        vendor('LBS.LocalServer');

        //加载Predis
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        //获取用户位置信息
        $where['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($where)->field('c_ucode,c_shop,c_longitude1,c_latitude1,c_isfixed1')->find();
        if ($userinfo['c_longitude1']>0 && $userinfo['c_latitude1']>0) {
            $latitude = $userinfo['c_latitude1'];
            $longitude = $userinfo['c_longitude1'];
            if ($userinfo['c_shop'] == 1) {
                $add_shopparams[0] = [
                    'name' => $ucode,
                    'long' => $longitude,
                    'lat' => $latitude
                ];

                if ($userinfo['c_isfixed1'] == 1) {
                    $add_fiexdparams[0] = [
                        'name' => $ucode,
                        'long' => $longitude,
                        'lat' => $latitude
                    ];
                } else {
                    $add_onlineparams[0] = [
                        'name' => $ucode,
                        'long' => $longitude,
                        'lat' => $latitude
                    ];
                }
            }

            $add_userparams[0] = [
                'name' => $ucode,
                'long' => $longitude,
                'lat' => $latitude
            ];
        }

        if (count($add_shopparams) > 0) {
            //加入商家集合
            $res = $LocalServer->add($add_shopparams,'Local_shop_assemble');
        }

        if (count($add_fiexdparams) > 0) {
            //加入实体店集合
            $res = $LocalServer->add($add_fiexdparams,'Local_fiexd_assemble');
        }

        if (count($add_onlineparams) > 0) {
            //加入微商集合
            $res = $LocalServer->add($add_onlineparams,'Local_online_assemble');
        }
        
        if (count($add_userparams) > 0) {
            //加入用户集合
            $res = $LocalServer->add($add_userparams,'Local_user_assemble');
        }
        return Message(0,'操作成功');
    }

    /**
    * 批量更新用户geo位置信息
    * @param
    */
    function UpuserLocal() {
        vendor('LBS.LocalServer');

        //加载Predis
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        $parr['pageindex'] = IGD('Common', 'Redis')->Rediesgetucode('Local_pageindex');;
        $parr['pagesize'] = 2000;
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        //获取用户位置信息
        $userlist = M('Users')->order('c_id asc')->limit($countPage, $pageSize)->field('c_ucode,c_shop,c_longitude1,c_latitude1,c_isfixed1')->select();
        if (count($userlist) > 0) {
            IGD('Common', 'Redis')->RediesStoreSram('Local_pageindex', $pageIndex+1);
        }

        $k = 0;$s = 0;$f = 0;$n = 0;
        foreach ($userlist as $key => $value) {
            if ($value['c_longitude1']>0 && $value['c_latitude1']>0 && $value['c_longitude1']>$value['c_latitude1']) {
                $latitude = $value['c_latitude1'];
                $longitude = $value['c_longitude1'];
                if ($value['c_shop'] == 1) {
                    $add_shopparams[$s] = [
                        'name' => $value['c_ucode'],
                        'long' => $longitude,
                        'lat' => $latitude
                    ];
                    $s++;

                    if ($value['c_isfixed1'] == 1) {
                        $add_fiexdparams[$f] = [
                            'name' => $value['c_ucode'],
                            'long' => $longitude,
                            'lat' => $latitude
                        ];
                        $f++;
                    } else {
                        $add_onlineparams[$n] = [
                            'name' => $value['c_ucode'],
                            'long' => $longitude,
                            'lat' => $latitude
                        ];
                        $n++;
                    }
                }

                $add_userparams[$k] = [
                    'name' => $value['c_ucode'],
                    'long' => $longitude,
                    'lat' => $latitude
                ];
                $k++;
            }
        }

        if (count($add_shopparams) > 0) {
            //加入商家集合
            $res = $LocalServer->add($add_shopparams,'Local_shop_assemble');
        }

        if (count($add_fiexdparams) > 0) {
            //加入实体店集合
            $res = $LocalServer->add($add_fiexdparams,'Local_fiexd_assemble');
        }

        if (count($add_onlineparams) > 0) {
            //加入微商集合
            $res = $LocalServer->add($add_onlineparams,'Local_online_assemble');
        }
        
        if (count($add_userparams) > 0) {
            //加入用户集合
            $res = $LocalServer->add($add_userparams,'Local_user_assemble');
        }

        return Message(0,'操作成功');
    }

    /**
    * 根据经纬度及距离由近及远查询用户编码集合
    * @param longitude,latitude,juli
    * @param usertype 0-全部用户 1-微商 2-实体店,3-所有商家 
    */
    function serach($parr,$usertype) {
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $juli = $parr['juli'];        

        //加载Predis
        vendor('LBS.LocalServer');
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        if ($usertype == 1) {  //微商
            $keyname = 'Local_online_assemble';
        } else if ($usertype == 2) { //实体店
            $keyname = 'Local_fiexd_assemble';
        } else if ($usertype == 3) { //所有商家
            $keyname = 'Local_shop_assemble';
        } else { //全部用户
            $keyname = 'Local_user_assemble';
        }

        $usermap = $LocalServer->search($longitude,$latitude,$juli,'km',$keyname);

        return $usermap;
    }

    /**
    * 根据位置集合中已有用户编码及距离由近及远查询用户编码集合
    * @param ucode,juli
    * @param usertype 0-全部用户 1-微商、会员用户 2-固定店铺商家,3-所有商家 
    */
    function searchByMembers($parr,$usertype)
    {
        $ucode = $parr['ucode'];
        $juli = $parr['juli'];
        
        //加载Predis
        vendor('LBS.LocalServer');
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        if ($usertype == 1) {  //微商
            $keyname = 'Local_online_assemble';
        } else if ($usertype == 2) { //实体店
            $keyname = 'Local_fiexd_assemble';
        } else if ($usertype == 3) { //所有商家
            $keyname = 'Local_shop_assemble';
        } else { //全部用户
            $keyname = 'Local_user_assemble';
        }

        $usermap = $LocalServer->searchByMembers($ucode,$juli,'km',$keyname);

        return $usermap;
    }

    /**
     * 列出位置集合列表所有元素
     * @param  pageindex,pagesize
     */
    function getlist($parr,$usertype)
    {
        $ucode = $parr['ucode'];
        $juli = $parr['juli'];
        
        //加载Predis
        vendor('LBS.LocalServer');
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        if ($usertype == 1) {  //微商
            $keyname = 'Local_online_assemble';
        } else if ($usertype == 2) { //实体店
            $keyname = 'Local_fiexd_assemble';
        } else if ($usertype == 3) { //所有商家
            $keyname = 'Local_shop_assemble';
        } else { //全部用户
            $keyname = 'Local_user_assemble';
        }

        $pageSize = $parr['pagesize'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;
        $countPage1 = ($pageIndex) * $pageSize;

        $usermap = $LocalServer->getlist($keyname,$countPage,$countPage1);

        return $usermap;
    }
}
?>