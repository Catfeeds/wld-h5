<?php

/**
 * 发现活动第二版
 */
class Activityv2Activity {

    //返回值
    //type 1为直接弹出，2为弹出底层，3为点击直接弹框，4为点击直接跳转, 5为积福红包雨直接弹框
    //jumptype 1为url，2为带openid的url，3为店铺，4为商品详情，5为个人空间，6为弹框活动，7为商家红包，8为常规红包
    //
    public function StartClick($parr) {

        // $aconf = C('Activity');    //活动配入数据     
        $result = $this->WriteFindslog($parr);
        $aconf = IGD('Redis', 'Redis')->Gethash('activity')['data'];

        // 查询记录条数
        // $count = F('activityclick');
        $count = IGD('Common', 'Redis')->Rediesgetucode('activityclick');
        if (!$count) {
            // $tempcount1=S('activitycount');
            // if(empty($tempcount1)){
                $count = M('Activity_findlog1')->count();
                IGD('Common', 'Redis')->RediesStoreSram('activityclick',$count,0);
            //      F('activityclick', $count);
            //      S('activitycount',$count,3600);
            // }else{
            //      $count=$tempcount1+1;
            // }
        }
        $parr['count'] = $count;

        $temparray = array();

        if (empty($parr['longitude']) || empty($parr['latitude']) || $parr['longitude'] == '0.0' || $parr['latitude'] == '0.0') {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        if (!$aconf) {
            $parr['pageindex'] = $parr['pageindex'];
            $parr['pagesize'] = 5;
            $userlist = $this->PushUsers($parr);
            return MessageInfo(0, '配入错误', $userlist);
        }

        //查询是否有相关推送活动
        if (!empty($parr['ucode'])) {
            $activityresult = $this->ReferPushActivity($aconf, $parr);

            if ($activityresult['code'] == 0) {
                $showarr = $activityresult['data'];
            }

            if ($aconf['producenum'] > 0) {
                $parr['cnum'] = $aconf['producenum'];
                $produceresult = $this->GetRefreeProduce($parr, $showarr);

                if ($produceresult['code'] == 0) {
                    $showarr = $produceresult['data'];
                }
            }
        }

        // 查询相关推送用户
        $parr['pageindex'] = $parr['pageindex'];
        $parr['pagesize'] = $aconf['shownum'];

        $userlist = $this->PushUsers($parr, $showarr,$aconf);

        return MessageInfo(0, "查询成功", $userlist);
    }

    //获取用户列表
    public function PushUsers($param, $showarr, $aconf) {

        //判断是否是活动点
        $tag = 1;
        $tag1 = 1;
        if (!empty($param['ucode'])) {
            $nowtime = time();
            $toptime = date('Y-m-d');
            $timearr = explode('|', $aconf['limittime']);
            foreach ($timearr as $key => $value) {
                $limittime = explode('-', $value);
                $begain = strtotime('-10 Minute', strtotime($toptime . ' ' . $limittime[0]));
                $stops = strtotime('+10 Minute', strtotime($toptime . ' ' . $limittime[1]));
                if ($nowtime >= $begain && $nowtime <= $stops) {
                    $tag = 2;
                    $tag1 = 2;
                    break;
                }
            }

            $timearr1 = explode('|', $aconf['collecttime']);
            foreach ($timearr1 as $key => $value) {
                $limittime1 = explode('-', $value);
                $begain1 = strtotime('-10 Minute', strtotime($toptime . ' ' . $limittime1[0]));
                $stops1 = strtotime('+10 Minute', strtotime($toptime . ' ' . $limittime1[1]));
                if ($nowtime >= $begain1 && $nowtime <= $stops1) {
                    $tag = 2;
                    $tag1 = 2;
                    break;
                }
            }

            $timearr2 = explode('|', $aconf['redtimes']);
            foreach ($timearr2 as $key => $value) {
                $limittime2 = explode('-', $value);
                $begain2 = strtotime('-10 Minute', strtotime($toptime . ' ' . $limittime2[0]));
                $stops2 = strtotime('+10 Minute', strtotime($toptime . ' ' . $limittime2[1]));
                if ($nowtime >= $begain2 && $nowtime <= $stops2) {
                    $tag = 2;
                    $tag1 = 2;
                    break;
                }
            }
        }

        $list = array();

        if ($tag == 2) {
            //从redis取出数据
            $rediskey = 'HC_' . $param['ucode'];
            $result = IGD('Common', 'Redis')->Rediesgetucode($rediskey);

            if (!$result) {
                $tag1 = 1;
            }

            if (count($result) > 0 && is_array($result)) {
                $list = $result;
            }
        }

        if ($tag1 == 1) {
            if (empty($param['pageindex'])) {
                $parr['pageindex'] = 1;
            } else {
                $parr['pageindex'] = $param['pageindex'];
            }

            $parr['longitude'] = $param['longitude'];
            $parr['latitude'] = $param['latitude'];
            $parr['pagesize'] = $param['pagesize'];

            //usertype 0-全部用户 1-微商、会员用户 2-固定店铺商家
            //取出微商用户
            $usertype = 1;
            $result = IGD('Getdata', 'Info')->CoalitionUserList($parr, $usertype);
            $list = $result['data']['list'];

            if (count($list) != $parr['pagesize']) {
                $parr['pageindex'] = 1;
                $result = IGD('Getdata', 'Info')->CoalitionUserList($parr, $usertype);
                $list = $result['data']['list'];
            }

            //取出固定店铺商家
            $usertype = 2;
            $parr1['juli'] = 4;
            $parr1['longitude'] = $param['longitude'];
            $parr1['latitude'] = $param['latitude'];
            $parr1['pagesize'] = 100;
            $parr1['pageindex'] = 1;
            $result1 = IGD('Getdata', 'Info')->CoalitionUserList($parr1, $usertype);
            if (count($result1['data']['list']) != 0) {
                $list = array_merge($list, $result1['data']['list']);
            }

            //开始写入redies
            if ($tag == 2) {
                $rediskey = 'HC_' . $param['ucode'];
                IGD('Common', 'Redis')->RediesStoreSram($rediskey, $list, 1800);
            }
        }

        foreach ($list as $key => $value) {
            $tempcount = count($showarr);
            $showarr[$tempcount]['type'] = 2;
            $showarr[$tempcount]['longitude'] = $value['c_longitude1'];
            $showarr[$tempcount]['latitude'] = $value['c_latitude1'];
            $showarr[$tempcount]['name'] = $value['c_nickname'];
            $showarr[$tempcount]['remarks'] = "";
            $showarr[$tempcount]['isshop'] = $value['c_shop'];
            if (!empty($value['c_shopnum'])) {
                $showarr[$tempcount]['shopnumimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg5.png';
            } else {
                $showarr[$tempcount]['shopnumimg'] = "";
            }
            $showarr[$tempcount]['signature'] = $value['c_signature'];

            if (empty($value['c_address1'])) {
                $showarr[$tempcount]['address'] = "该用户正在附近潜水，点TA看看";
            } else {
                $showarr[$tempcount]['address'] = $value['c_address1'];
            }

            $showarr[$tempcount]['distance'] = $value['c_distance'];
            $showarr[$tempcount]['keyvalue'] = $value['c_ucode'];
            $showarr[$tempcount]['basemap'] = $value['c_headimg'];

            if ($value['c_shop'] == 1) {
                $showarr[$tempcount]['jumptype'] = 5;
                $showarr[$tempcount]['imgtype'] = 1;
                $showarr[$tempcount]['img'] = $value['c_headimg'];
                $showarr[$tempcount]['signimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg1.png';
            } else if ($value['c_shop'] == 2) {
                $showarr[$tempcount]['jumptype'] = 5;
                $showarr[$tempcount]['imgtype'] = 2;
                $showarr[$tempcount]['img'] = GetHost() . '/Uploads/Activity/shopimg/shopimg.png';
                $showarr[$tempcount]['signimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg3.png';
            } else {
                $showarr[$tempcount]['jumptype'] = 5;
                $showarr[$tempcount]['imgtype'] = 1;
                $showarr[$tempcount]['img'] = $value['c_headimg'];
                $showarr[$tempcount]['signimg'] = GetHost() . '/Uploads/Activity/shopimg/signimg4.png';
            }
        }

        $arr['pageindex'] = $param['pageindex'];
        $arr['list'] = $showarr;
        return $arr;
    }

    //新的商品查询配置方法
    public function GetRefreeProduce($param, $showarr) {
        $parr['longitude'] = $param['longitude'];
        $parr['latitude'] = $param['latitude'];

        $parr['cnum'] = $param['cnum'];

        $result = IGD('Getdata', 'Info')->CoalitionProductList($parr);
        if ($result['code'] == 0) {
            $list = $result['data'];
        }

        foreach ($list as $key => $value) {
            $tempcount = count($showarr);
            $showarr[$tempcount]['type'] = 2;
            $showarr[$tempcount]['longitude'] = $value['c_longitude1'];
            $showarr[$tempcount]['latitude'] = $value['c_latitude1'];
            $showarr[$tempcount]['name'] = $value['c_name'];
            $showarr[$tempcount]['remarks'] = "";
            $showarr[$tempcount]['isshop'] = 0;
            $showarr[$tempcount]['shopnumimg'] = "";
            $showarr[$tempcount]['signature'] = "商品由" . $value['c_nickname'] . "提供";

            if (empty($value['c_address'])) {
                $showarr[$tempcount]['address'] = "该商品来自火星，赶快去抢购";
            } else {
                $showarr[$tempcount]['address'] = $value['c_address1'];
            }

            $showarr[$tempcount]['distance'] = $value['c_distance'];
            $showarr[$tempcount]['jumptype'] = 4;
            $showarr[$tempcount]['keyvalue'] = $value['c_pcode'];
            $showarr[$tempcount]['imgtype'] = 3;
            $showarr[$tempcount]['img'] = $value['c_pimg'];
            $showarr[$tempcount]['basemap'] = $value['c_pimg'];
            $showarr[$tempcount]['signimg'] = '';
        }

        return MessageInfo(0, "查询商品成功", $showarr);
    }

    //新的活动查询配置方法
    public function ReferPushActivity($aconf, $parr) {

        $random = $aconf['random'];
        $randnum = $aconf['randnum'];
        $randchange = $aconf['randchange'];
        $activitywhere['a.c_state'] = 1;
        //$activitywhere['a.c_show'] = 1;
        $activitywhere['a.c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['a.c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        //$activitywhere[] = array('a.c_activitytype <> 3 and a.c_activitytype <> 4 and a.c_activitytype <> 9');

        $activitydata1 = M('Activity as a')->where($activitywhere)->order('a.c_activitytype asc,a.c_id desc')->select();

        if (count($activitydata1) == 0) {
            return Message(1023, "没有活动");
        }

        $arr = array();
        foreach ($activitydata1 as $key => $value) {

            $count = count($arr);
            $tempcount = 0;
            if ($count >= 1) {
                $tempcount = $count;
            }

            switch ($value['c_activitytype']) {
                case 1:     //常规红包
                    if ($parr['raction'] == 1) {
                        $result = $this->RoutineRed($parr, $aconf, $value, $arr, $tempcount);
                        if ($result['code'] == 0) {
                            $arr = $result['data'];
                        }
                    }
                    break;
                case 2:     //大转盘
                    $result = $this->GetTurntable($parr, $aconf, $value, $arr, $tempcount);

                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }

                    break;
                case 3:     //零元抢购

                    break;
                case 4:     //砍价活动

                    break;
                case 5:     //答题活动
                    $result = $this->GetAnswer($parr, $aconf, $value, $arr, $tempcount);
                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 6:     //商家红包
                    if ($parr['raction'] == 1) {
                        $result = $this->GetRebateRed($parr, $aconf, $value, $arr, $tempcount);
                        if ($result['code'] == 0) {
                            $arr = $result['data'];
                        }
                    }
                    break;
                case 7:     //碎片活动
                    if ($parr['raction'] == 1) {
                        $result = $this->GetFragment($parr, $aconf, $value, $arr, $tempcount);
                        if ($result['code'] == 0) {
                            $arr = $result['data'];
                        }
                    }
                    break;
                case 8:     //炸金花活动

                    break;
                case 9:     //祝福活动
                    $result = $this->ActivityWish($parr, $aconf, $value, $arr, $tempcount);
                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 10:     //抽签活动
                    $result = $this->ActivityBallot($parr, $aconf, $value, $arr, $tempcount);
                    if ($result['code'] == 0) {
                        $arr = $result['data'];
                    }
                    break;
                case 16:     //找你妹活动
                    if ($parr['raction'] == 1) {
                        $result = $this->FindSomething($parr, $aconf, $value, $arr, $tempcount);
                        if ($result['code'] == 0) {
                            $arr = $result['data'];
                        }
                    }
                    break;
                case 18:     //春节积福活动
                    if ($parr['raction'] == 1) {
                        if($parr['version'] == 203){
                            //读取活动配置，是否出现红包雨
                            $nowtime = time();
                            $toptime = date('Y-m-d');
                            $timearr = explode('|', $aconf['collecttime']);
                            foreach ($timearr as $key => $value1) {
                                $limittime = explode('-', $value1);
                                $begain = strtotime($toptime . ' ' . $limittime[0]);
                                $stops = strtotime($toptime . ' ' . $limittime[1]);
                                if ($nowtime >= $begain && $nowtime <= $stops) {
                                    $result = IGD('Collect','Activity')->CollectSomething($parr, $aconf, $value, $arr, $tempcount);
                                    if ($result['code'] == 0) {
                                        $arr = $result['data'];
                                    }
                                }
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        return MessageInfo(0, "活动配置成功", $arr);
    }

    //判断是否有可以选择红包
    public function RoutineRed($parr, $aconf, $activitydata, $arr, $tempcount) {

        $boole = false;
        foreach ($arr as $key => $value) {

            if ($value['type'] == 1 && ($value['jumptype'] == 7 || $value['jumptype'] == 8)) {
                $boole = true;
                break;
            }
        }

        if ($boole) {
            return Message(1036, "没有中奖");
        }

        $result = $this->ClickWinning($parr, $aconf);

        if ($result['code'] != 0) {
            return Message(1036, "没有中奖");
        }

        //查询奖品表
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_aid'] = $activitydata['c_id'];

        //查询金额池表
        $moneywhere['c_state'] = 1;
        $moneywhere['c_remain'] = array('GT', 0);
        $moneywhere['c_aid'] = $activitydata['c_id'];
        $moneydata = M('Activity_money')->where($moneywhere)->find();

        if (!$moneydata) {
            return Message(1035, "没有红包金额");
        }

        $img = GetHost() . '/' . $moneydata['c_imgpath'];
        if ($moneydata['c_rule'] == 2) {
            $prizewhere['c_type'] = 1;
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            $img = GetHost() . '/' . $prizedata['c_imgpath'];
        }

        $parr['sign'] = 1;
        $result = $this->WriteFindslog($parr);

        //统计送出红包总数
        $redamount = IGD('Common', 'Redis')->Rediesgetucode('ACT_redamount');

        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

        $arr[$tempcount]['type'] = 1;
        $arr[$tempcount]['longitude'] = "";
        $arr[$tempcount]['latitude'] = "";
        $arr[$tempcount]['name'] = $activitydata['c_activityname'];
        $arr[$tempcount]['remarks'] = "小蜜平台已经发出" . $redamount . "个红包";
        $arr[$tempcount]['isshop'] = 0;
        $arr[$tempcount]['shopnumimg'] = "";
        $arr[$tempcount]['signature'] = "发现一个红包！";
        $arr[$tempcount]['address'] = "";
        $arr[$tempcount]['distance'] = "0公里";
        $arr[$tempcount]['jumptype'] = 8;
        $arr[$tempcount]['keyvalue'] = GetHost(2) . '/index.php/Api/Activityv2/receive';
        $arr[$tempcount]['imgtype'] = 0;
        $arr[$tempcount]['img'] = $img;
        $arr[$tempcount]['basemap'] = '';
        $arr[$tempcount]['signimg'] = '';

        return MessageInfo(0, "获取成功", $arr);
    }

    //判断是否有大转盘活动
    public function GetTurntable($parr, $aconf, $activitydata, $arr, $tempcount) {

        $clickcount = $parr['count'];
        $random = $aconf['random'];

        $tag = 0;
        if ($random != 1) { //不随机，有机会即出现转盘
            $lotterywhere['c_ucode'] = $parr['ucode'];
            $lotterywhere['c_rule'] = 2;
            $chance = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');
            if ($chance > 0) {
                $tag = 1;
            }
        } else {

            // $rouletteconf = C('Roulette');
            $rouletteconf = IGD('Redis', 'Redis')->Gethash('roulette')['data'];
            if (($clickcount % $rouletteconf['num']) == 0) {
                $tag = 1;
                $result = IGD('Question', 'Activity')->AddRouletteNum($parr, 1);
            }
        }

        if ($tag == 1) {
            $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], 30);
            $localpoint = $localpointarr['left-top'];

            $arr[$tempcount]['type'] = 4;
            $arr[$tempcount]['longitude'] = $localpoint['lng'];
            $arr[$tempcount]['latitude'] = $localpoint['lat'];
            $arr[$tempcount]['name'] = $activitydata['c_activityname'];
            $arr[$tempcount]['remarks'] = "";
            $arr[$tempcount]['isshop'] = 0;
            $arr[$tempcount]['shopnumimg'] = "";
            $arr[$tempcount]['signature'] = "";
            $arr[$tempcount]['address'] = "";
            $arr[$tempcount]['distance'] = "0公里";
            $arr[$tempcount]['jumptype'] = 2;
            $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/Activity/roulette?Id=1';
            $arr[$tempcount]['imgtype'] = 4;
            $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
            $arr[$tempcount]['basemap'] = '';
            $arr[$tempcount]['signimg'] = '';
            return MessageInfo(0, "获取成功", $arr);
        }
        return Message(1012, "没有大转盘活动");
    }

    //判断是否有答题活动
    public function GetAnswer($parr, $aconf, $activitydata, $arr, $tempcount) {
        if (rand(1, 5) != 2) {
            return Message(1036, "没有答题活动");
        }

        $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], 30);
        $localpoint = $localpointarr['right-top'];

        $arr[$tempcount]['type'] = 4;
        $arr[$tempcount]['longitude'] = $localpoint['lng'];
        $arr[$tempcount]['latitude'] = $localpoint['lat'];
        $arr[$tempcount]['name'] = $activitydata['c_activityname'];
        $arr[$tempcount]['remarks'] = "300";
        $arr[$tempcount]['isshop'] = 0;
        $arr[$tempcount]['shopnumimg'] = "";
        $arr[$tempcount]['signature'] = "";
        $arr[$tempcount]['address'] = "";
        $arr[$tempcount]['distance'] = "0公里";
        $arr[$tempcount]['jumptype'] = 2;
        $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/Seventh/index?qnum=1';
        $arr[$tempcount]['imgtype'] = 4;
        $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
        $arr[$tempcount]['basemap'] = '';
        $arr[$tempcount]['signimg'] = '';
        return MessageInfo(0, "获取成功", $arr);
    }

    //判断是否有商家红包
    public function GetRebateRed($parr, $aconf, $activitydata, $arr, $tempcount) {

        $boole = false;
        foreach ($arr as $key => $value) {
            if ($value['type'] == 1 && ($value['jumptype'] == 7 || $value['jumptype'] == 8)) {
                $boole = true;
                break;
            }
        }

        $redstag = 0;
        $nowtime = time();
        $toptime = date('Y-m-d');
        $timearr2 = explode('|', $aconf['redtimes']);
        foreach ($timearr2 as $key => $value) {
            $limittime2 = explode('-', $value);
            $begain2 = strtotime($toptime . ' ' . $limittime2[0]);
            $stops2 = strtotime($toptime . ' ' . $limittime2[1]);
            if ($nowtime >= $begain2 && $nowtime <= $stops2) {
                $redstag = 1;
                break;
            }
        }

        if ($boole || $aconf['redstatus'] != 1 || $redstag == 0) {
            return Message(1036, "没有商家红包");
        }

        $redclick = $aconf['redclick'];
        $clickcount = $parr['count'];

        if (($clickcount % $redclick) == 0) {
            //统计送出红包总数
            $redamount = IGD('Common', 'Redis')->Rediesgetucode('ACT_redamount');
            //查询有没有商家红包
            $prizewhere['c_type'] = 1;
            $prizewhere['c_aid'] = $activitydata['c_id'];
            $prizewhere['c_state'] = 1;
            $prizewhere['c_num'] = array('GT', 0);
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            if ($prizedata) {
                $parr['sign'] = 2;
                $result = $this->WriteFindslog($parr);

                $arr[$tempcount]['type'] = 1;
                $arr[$tempcount]['longitude'] = "";
                $arr[$tempcount]['latitude'] = "";
                $arr[$tempcount]['name'] = $activitydata['c_activityname'];
                $arr[$tempcount]['remarks'] = "小蜜平台已经发出" . $redamount . "个红包";
                $arr[$tempcount]['isshop'] = '发现一个商家红包！';
                $arr[$tempcount]['shopnumimg'] = "";
                $arr[$tempcount]['signature'] = "";
                $arr[$tempcount]['address'] = "";
                $arr[$tempcount]['distance'] = "0公里";
                $arr[$tempcount]['jumptype'] = 7;
                $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/Seventh/red?red=1';
                $arr[$tempcount]['imgtype'] = 0;
                $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
                $arr[$tempcount]['basemap'] = '';
                $arr[$tempcount]['signimg'] = '';
                return MessageInfo(0, "获取成功", $arr);
            }
        }

        return Message(1037, "没有商家红包");
    }

    //获取碎片活动
    public function GetFragment($parr, $aconf, $activitydata, $arr, $tempcount) {
        $chipclick = $aconf['chipclick'];
        $clickcount = $parr['count'];

        if (($clickcount % $chipclick) == 0) {
            //首先计算距离,查找碎片
            $longitude = $parr['longitude'];
            $latitude = $parr['latitude'];
            $juli = $aconf['chipjuli'];

            $where['p.c_aid'] = $activitydata['c_id'];
            $where['p.c_pid'] = array('NEQ', 0);
            $where['p.c_state'] = 1;
            $where['p.c_num'] = array('GT', 0);
            $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-p.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(p.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-p.c_longitude)/360),2)))) <= ' . $juli . '';

            $order = 'case when ifnull(p.c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((p.c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((p.c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (p.c_longitude * 3.1415) / 180 ) ) * 6380 asc';

            $field = 'p.c_id,p.c_pid,p.c_name,p.c_imgpath,p.c_marks,p.c_longitude,p.c_latitude,p.c_address,p.c_pcode';

            $chipdata = M('Activity_prize as p')->field($field)->where($where)->order($order)->select();

            if (!$chipdata) {
                return Message(1037, '附近没有碎片');
            }

            $chipcount = intval(count($chipdata));
            $chippcodearr = array();
            $shownum = rand(1, 2);

            //随机返回商品碎片数量
            foreach ($chipdata as $key => $value) {
                // 用户领取的碎片
                $chipsign = 0;
                $prizepcode['a.c_ucode'] = $parr['ucode'];
                $prizepcode['a.c_pcode'] = $value['c_pcode'];
                $pjoin = 'left join t_activity_prize as b on a.c_pid=b.c_id';
                $userchip = M('Activity_log as a')->join($pjoin)->where($prizepcode)->group('a.c_pid')->field('a.*,b.c_marks')->select();
                foreach ($userchip as $k1 => $v1) {
                    if ($value['c_id'] == $v1['c_pid'] || $value['c_marks'] == $v1['c_marks']) {
                        $chipsign = 1;
                    }
                }

                if (!in_array($value['c_pcode'], $chippcodearr) && $chipsign == 0) {
                    //查询首页显示图片
                    $chipid = $value['c_id'];
                    $w['a.c_id'] = $chipid;
                    $join = 'left join t_activity_prize as b on a.c_pid=b.c_id';
                    $field = 'b.c_pic';
                    $picinfo = M('Activity_prize as a')->field($field)->join($join)->where($w)->find();
                    if ($shownum > 0) {
                        $arr[$tempcount]['type'] = 3;
                        $arr[$tempcount]['longitude'] = $value['c_longitude'];
                        $arr[$tempcount]['latitude'] = $value['c_latitude'];
                        $arr[$tempcount]['name'] = $value['c_name'];
                        $arr[$tempcount]['remarks'] = "";
                        $arr[$tempcount]['isshop'] = 0;
                        $arr[$tempcount]['shopnumimg'] = "";
                        $arr[$tempcount]['signature'] = "";
                        $arr[$tempcount]['address'] = $value['c_address'];
                        $arr[$tempcount]['distance'] = "";
                        $arr[$tempcount]['jumptype'] = 6;
                        $arr[$tempcount]['keyvalue'] = GetHost(2) . '/index.php/Api/Activityv2/recievechip?Id=' . $value['c_id'];
                        $arr[$tempcount]['imgtype'] = 4;
                        $arr[$tempcount]['img'] = GetHost() . '/' . $picinfo['c_pic'];
                        $arr[$tempcount]['basemap'] = '';
                        $arr[$tempcount]['signimg'] = '';
                        $tempcount++;
                        $shownum--;
                    }
                    $chippcodearr[] = $value['c_pcode'];
                }
            }
            return MessageInfo(0, "获取成功", $arr);
        }

        return Message(1037, '没有商品碎片');
    }

    // 查询祝福活动
    function ActivityWish($parr, $aconf, $activitydata, $arr, $tempcount) {

        if (rand(1, 5) != 2) {
            return Message(1036, "没有祝福活动");
        }

        $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], 30);
        $localpoint = $localpointarr['left-bottom'];

        $arr[$tempcount]['type'] = 4;
        $arr[$tempcount]['longitude'] = $localpoint['lng'];
        $arr[$tempcount]['latitude'] = $localpoint['lat'];
        $arr[$tempcount]['name'] = $activitydata['c_activityname'];
        $arr[$tempcount]['remarks'] = "300";
        $arr[$tempcount]['isshop'] = 0;
        $arr[$tempcount]['shopnumimg'] = "";
        $arr[$tempcount]['signature'] = "";
        $arr[$tempcount]['address'] = "";
        $arr[$tempcount]['distance'] = "0公里";
        $arr[$tempcount]['jumptype'] = 2;
        $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/Wish/send?aid=' . $activitydata['c_id'];
        $arr[$tempcount]['imgtype'] = 4;
        $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
        $arr[$tempcount]['basemap'] = '';
        $arr[$tempcount]['signimg'] = '';

        return MessageInfo(0, "获取成功", $arr);
    }

    // 查询抽签活动
    function ActivityBallot($parr, $aconf, $activitydata, $arr, $tempcount) {

        if (rand(1, 5) != 2) {
            return Message(1036, "没有抽签活动");
        }

        $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], 30);
        $localpoint = $localpointarr['right-bottom'];

        $arr[$tempcount]['type'] = 4;
        $arr[$tempcount]['longitude'] = $localpoint['lng'];
        $arr[$tempcount]['latitude'] = $localpoint['lat'];
        $arr[$tempcount]['name'] = $activitydata['c_activityname'];
        $arr[$tempcount]['remarks'] = "300";
        $arr[$tempcount]['isshop'] = 0;
        $arr[$tempcount]['shopnumimg'] = "";
        $arr[$tempcount]['signature'] = "";
        $arr[$tempcount]['address'] = "";
        $arr[$tempcount]['distance'] = "0公里";
        $arr[$tempcount]['jumptype'] = 2;
        $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/Ballot/index?aid=' . $activitydata['c_id'];
        $arr[$tempcount]['imgtype'] = 4;
        $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
        $arr[$tempcount]['basemap'] = '';
        $arr[$tempcount]['signimg'] = '';

        return MessageInfo(0, "获取成功", $arr);
    }

    //爱我就快一点（找你妹活动）
    function FindSomething($parr, $aconf, $activitydata, $arr, $tempcount) {
        if (rand(1, 5) != 2) {
            return Message(1036, "没有抽签活动");
        }

        $localpointarr = returnSquarePoint($parr['longitude'], $parr['latitude'], 30);
        $localpoint = $localpointarr['right-bottom'];

        $arr[$tempcount]['type'] = 4;
        $arr[$tempcount]['longitude'] = $localpoint['lng'];
        $arr[$tempcount]['latitude'] = $localpoint['lat'];
        $arr[$tempcount]['name'] = $activitydata['c_activityname'];
        $arr[$tempcount]['remarks'] = "300";
        $arr[$tempcount]['isshop'] = 0;
        $arr[$tempcount]['shopnumimg'] = "";
        $arr[$tempcount]['signature'] = "";
        $arr[$tempcount]['address'] = "";
        $arr[$tempcount]['distance'] = "0公里";
        $arr[$tempcount]['jumptype'] = 2;
        $arr[$tempcount]['keyvalue'] = GetHost(1) . '/index.php/Home/H5Game/index';
        $arr[$tempcount]['imgtype'] = 4;
        $arr[$tempcount]['img'] = GetHost() . '/' . $activitydata['c_pimg'];
        $arr[$tempcount]['basemap'] = '';
        $arr[$tempcount]['signimg'] = '';

        return MessageInfo(0, "获取成功", $arr);
    }

    /**
     *  根据配入的点击次数中奖
     *  @param count
     */
    function ClickWinning($parr, $aconf) {
        $count = $parr['count'];
        $clicknum = $aconf['maxclick'];

        $nowtime = time();
        $toptime = date('Y-m-d');
        $timearr = explode('|', $aconf['limittime']);
        foreach ($timearr as $key => $value) {
            $limittime = explode('-', $value);
            $begain = strtotime($toptime . ' ' . $limittime[0]);
            $stops = strtotime($toptime . ' ' . $limittime[1]);
            if ($nowtime >= $begain && $nowtime <= $stops) {
                $clicknum = $aconf['minclick'];
            }
        }
        if (($count % $clicknum) == 0) {
            return Message(0, '中奖');
        }
        return Message(1035, '没中奖');
    }

    /**
     * 写入中奖记录
     * @param string $value [description]
     */
    function WriteActlog($parr) {
        $prizelogdata['c_ucode'] = $parr['c_ucode'];
        $prizelogdata['c_pcode'] = $parr['c_pcode'];
        $prizelogdata['c_orderid'] = $parr['c_orderid'];
        $prizelogdata['c_aid'] = $parr['c_aid'];
        $prizelogdata['c_pid'] = $parr['c_pid'];
        $prizelogdata['c_fid'] = $parr['c_fid'];
        $prizelogdata['c_value'] = $parr['c_value'];
        $prizelogdata['c_type'] = $parr['c_type'];
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_log')->add($prizelogdata);
        if (!$result) {
            return Message(1003, '记录失败');
        }
        return Message(0, '记录成功');
    }

    //数组排序
    function my_sort($arrays) {
        foreach ($arrays as $key => $value) {
            $arr = array_rand($arrays, 1);
            $newarr = array_splice($arrays, $arr, 1);
            $array[] = $newarr[0];
        }
        return $array;
    }

    /**
     *  常规红包的领取
     *  @param ucode,type[8现金，9实物奖]
     */
    function ReceivePrize($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, '您尚未登录，不能领取！');
        }

        //查询活动
        $activitywhere['c_state'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 1;
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(1031, '没有相关奖品活动！');
        }

        //判断中奖
        $tag = 0;     // 0不中奖，1中奖
        $where['c_sign'] = 1;
        $where['c_ucode'] = $parr['ucode'];
        $pjtime = date('Y-m-d H:i:s', strtotime('-1 Minute', time()));  //中奖一分钟未领失效
        $where['c_addtime'] = array('EGT', $pjtime);
        $findlog = M('Activity_findlog1')->where($where)->order('c_id desc')->find();
        if (empty($findlog)) {
            return Message(1032, '糟糕手慢了，该红包已被人先手一步了！');
        }

        //查询是否领取过
        $findlogwhere['c_fid'] = $findlog['c_id'];
        $result = M('Activity_log')->where($findlogwhere)->count();
        if ($result == 0) {
            $tag = 1;
        } else {
            return Message(1032, '糟糕手慢了，该红包已被人先手一步了！');
        }
        $db = M('');
        $db->startTrans();

        if ($tag == 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1032, '没有对应的奖项');
        }

        //查询奖品表
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_aid'] = $activitydata['c_id'];

        // 区分现金与实物奖项
        if ($parr['type'] == 8) {
            //查询金额池表
            $moneywhere['c_state'] = 1;
            $moneywhere['c_ucode'] = $parr['ucode'];
            $moneywhere['c_remain'] = array('GT', 0);
            $moneywhere['c_aid'] = $activitydata['c_id'];
            $moneydata = M('Activity_money')->where($moneywhere)->find();
            if (!$moneydata) {
                $db->rollback(); //不成功，则回滚
                return Message(1032, '糟糕手慢了，该红包已被人先手一步了！');
            }

            if ($moneydata['c_rule'] == 2) {
                $prizewhere['c_type'] = 1;
                $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            }
        } else {
            $prizewhere['c_type'] = 2;
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            if (!$prizedata) {
                $db->rollback(); //不成功，则回滚
                return Message(1032, '糟糕手慢了，该奖品已被人先手领走了！');
            }
        }

        // 写入活动记录表
        if ($prizedata) {
            $prizelogdata['c_ucode'] = $parr['ucode'];
            $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
            $prizelogdata['c_pid'] = $prizedata['c_id'];
            $prizelogdata['c_value'] = $prizedata['c_value'];
            if ($parr['type'] == 8) {
                $prizelogdata['c_value'] = rand($prizedata['c_bargainprice'] * 100, $prizedata['c_value'] * 100) / 100;
            }
            $prizelogdata['c_type'] = $prizedata['c_type'];
        } else {
            $moneyprize = rand($moneydata['c_min_money'] * 100, $moneydata['c_max_money'] * 100);
            $prizelogdata['c_value'] = bcdiv($moneyprize, 100, 2);
            if (($moneydata['c_remain'] - $prizelogdata['c_value']) < 0) {
                $prizelogdata['c_value'] = $moneydata['c_remain'];
            }
            $prizelogdata['c_type'] = 1;
        }

        $prizelogdata['c_fid'] = $findlog['c_id'];
        $prizelogdata['c_aid'] = $activitydata['c_id'];
        $prizelogdata['c_ucode'] = $parr['ucode'];
        $prizelogdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_log')->add($prizelogdata);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '奖品领取记录失败！');
        }
        $regionid = $result;

        if ($parr['type'] == 8) {
            // 写入用户余额
            $rebatemoney = IGD('Money', 'User');
            $moneyparr['ucode'] = $parr['ucode'];
            $moneyparr['money'] = $prizelogdata['c_value'];
            $moneyparr['source'] = 3;
            $moneyparr['key'] = $activitydata['c_activityname'];
            $moneyparr['desc'] = "您在点击发现活动中，发现红包并领取金额";
            $moneyparr['state'] = 1;  //完成状态
            $moneyparr['type'] = 1;
            $moneyparr['isagent'] = 0;
            $moneyparr['joinaid'] = $activitydata['c_id'];
            $moneyparr['showimg'] = 'Uploads/settlementshow/hong.png';
            $moneyparr['showtext'] = '红包';
            $result = $rebatemoney->OptionMoney($moneyparr);
            if ($result['code'] !== 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1033, '修改用户余额失败！');
            }

            if ($prizedata) {
                // 扣除奖项库存
                $jxwhere['c_id'] = $prizedata['c_id'];
                $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', 1);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1033, '扣除奖项库存失败！');
                }
            } else {
                // 扣除奖池总额
                $result = M('Activity_money')->where($moneywhere)->setDec('c_remain', $prizelogdata['c_value']);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1033, '扣除奖池总额失败！');
                }
            }
        } else {
            // 扣除奖项库存
            $jxwhere['c_id'] = $prizedata['c_id'];
            $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', 1);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1033, '扣除奖项库存失败！');
            }
        }

        // 写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        if ($parr['type'] == 8) {
            $msgdata['content'] = '您发现的红包金额为￥' . $prizelogdata['c_value'] . '，领取成功已转入余额';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Balance/budget';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Balance/budget';
        } else {
            $msgdata['content'] = '您发现了一个奖品：' . $prizedata['c_name'] . '，点击马上领取';
            $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Activity/prize?pid=' . $prizedata['c_id'];
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Activity/prize?pid=' . $prizedata['c_id'];
        }

        $msgresult = $Msgcentre->CreateMessege($msgdata);
        if ($msgresult['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '创建消息失败！');
        }

        //查询是否领取过
        $findlogwhere1['c_fid'] = $findlog['c_id'];
        $count1 = M('Activity_log')->lock(true)->where($findlogwhere1)->count();

        if ($count1 > 1) {
            $db->rollback(); //不成功，则回滚
            return Message(1032, '您已经领取过该奖品111');
        }

        $redamount = IGD('Common', 'Redis')->Rediesgetucode('ACT_redamount');
        IGD('Common', 'Redis')->RediesStoreSram('ACT_redamount', $redamount + 1,0);
        $db->commit();
        $returndata['money'] = $prizelogdata['c_value'];
        $returndata['hint'] = '已放入您的口袋,快去体验吧!';
        $returndata['acthint'] = "亲可以在\"服务中心-结算中心\"\n找到现金红包哦!";
        if ($parr['type'] == 1) {
            $returndata['img'] = GetHost() . '/' . $moneydata['c_imgpath'];
        } else {
            $returndata['pid'] = $prizedata['c_id'];
            $returndata['img'] = GetHost() . '/' . $prizedata['c_imgpath'];
            $returndata['url'] = GetHost(1) . '/index.php/Home/Activity/prize?pid=' . $prizedata['c_id'];
        }

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $parr['ucode'];
        $blogdata['behavior'] = 3;
        $blogdata['regionid'] = $regionid;
        $blogdata['tag'] = 10000;
        $blogdata['tagvalue'] = '1';

        //查询自己位置信息
        $result1 = IGD('Servecentre', 'Serve')->GetLocation($parr['ucode']);
        $localtion = $result1['data'];

        $longitude = $localtion['longitude'];
        $latitude = $localtion['latitude'];
        $address = $localtion['address'];

        $blogdata['longitude'] = $longitude;
        $blogdata['latitude'] = $latitude;
        $blogdata['address'] = $address;
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = IGD('Servecentre', 'Serve')->Addlogs($blogdata);

        return MessageInfo(0, '领取成功', $returndata);
    }

    /**
     *  写入发现记录
     *  @param ucode
     */
    function WriteFindslog($parr) {
        $uip = get_client_ip();
        $data['c_ucode'] = $parr['ucode'];
        $data['c_sign'] = $parr['sign'];
        $data['c_ip'] = $uip;
        $localtion = GetAreafromIp();
        $data['c_address'] = $localtion['localtion'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_findlog1')->add($data);
        if (!$result) {
            return Message(1036, '记录失败');
        }

        // $count = F('activityclick') + 1;
        $count = IGD('Common', 'Redis')->Rediesgetucode('activityclick') + 1;
        if ($count == 1) {
            // $tempcount1=S('activitycount');
            // if(empty($tempcount1)){
                $count = M('Activity_findlog1')->count();
                IGD('Common', 'Redis')->RediesStoreSram('activityclick',$count,0);
            // }else{
            //      $count=$tempcount1+1;
            // } 
        }

        // F('activityclick', $count);
        // S('activitycount',$count,3600);
        IGD('Common', 'Redis')->RediesStoreSram('activityclick', $count, 0);
        return Message(0, '记录成功');
    }

    /**
     *  未读消息
     */
    function Getmsgnum($parr) {
        $ucode = $parr['ucode'];
        if (!empty($ucode)) {
            // $sql = "select count(c_txcode) as num from t_users_msg where c_txcode not in ";
            // $sql .= "(select c_txcode from t_users_msglog where c_ucode='$ucode') ";
            // $sql .= "and (c_ucode='' or c_ucode is null or c_ucode='$ucode') limit 1";
            // $data = M('')->query($sql);
            $where['c_ucode'] = $ucode;
            $data = M('Users_msgnum')->where($where)->field('SUM(c_num) as num')->select();
            $msgnum = ($data[0]['num'] <= 0) ? 0 : $data[0]['num'];
        } else {
            $msgnum = 0;
        }
        return MessageInfo(0, '查询成功', $msgnum);
    }

    /**
     *  正在进行的活动数量
     */
    function Getactivitynum() {
        $activitywhere['c_state'] = 1;
        $activitywhere['c_show'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitynum = M('Activity')->where($activitywhere)->count();

        return MessageInfo(0, '查询成功', $activitynum);
    }

    /**
     *  未兑换碎片数量
     */
    function Getchipnum($parr) {
        $ucode = $parr['ucode'];
        //获取活动id
        $w['c_activitytype'] = 7;
        $aid = M('activity')->where($w)->getField('c_id');

        $chipwhere['c_orderid'] = array('eq', NULL);
        $chipwhere['c_aid'] = $aid;
        $chipnum = M('Activity_log')->where($chipwhere)->count();

        return MessageInfo(0, '查询成功', $chipnum);
    }

}
