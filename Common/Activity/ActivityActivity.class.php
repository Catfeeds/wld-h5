<?php

/**
 * 	发现活动管理中心
 *
 */
class ActivityActivity {

    /**
     *  点击发现,出现奖项
     *  @param   ucode,longitude,latitude,pageindex
     */
    function StartClick($parr) {
        $tag = 0;      // 0不中奖，1中奖
        $type = 8;     //8表示现金，9表示实物
        // $Redis = IGD('Redis','Redis');

        $this->WriteFindslog($parr);
        // 保证activityclick存在
        // $count = $Redis->Back()->get('activityclick');
        $count = F('activityclick');
        if (!$count) {
            $count = M('Activity_findlog1')->count();
            F('activityclick',$count);
            // $result = $Redis->SetKey('activityclick',$count);
            // if($result != 0){
            //     $parr['pageindex'] = $parr['pageindex'];
            //     $parr['pagesize'] = 5;
            //     $userlist = $this->PushUsers($parr);
            //     return MessageInfo(0, '写入activityclick失败', $userlist);
            // }
        }

        //获取活动配置信息
        // $result = $Redis->Gethash('activity');
        // $aconf = $result['data'];
        $aconf = C('Activity');
        $aconf = IGD('Redis','Redis')->Gethash('activity')['data'];
        if(!$aconf){
            $parr['pageindex'] = $parr['pageindex'];
            $parr['pagesize'] = 5;
            $userlist = $this->PushUsers($parr);
            return MessageInfo(0, '获取活动配置错误', $userlist);
        }
		 $showusernum=$aconf['shownum'];

        // 查询相关推送用户
        $parr['pageindex'] = $parr['pageindex'];
        $parr['pagesize'] = $showusernum;
        $userlist = $this->PushUsers($parr, $pushactive,$refreeproduce);
		return MessageInfo(0, '活动未开始', $userlist);


      //   if (!$parr['ucode']) {
       //     return MessageInfo(0, '该用户未登陆', $userlist);
     //   }

       // if ($aconf['state'] != 1) {
      //      return MessageInfo(0, '活动未开始', $userlist);
       // }


        //查询活动
      //  $activitywhere['c_state'] = 1;            //活动开启状态
       // $activitywhere['c_activitytype'] = 1;     //发现活动类型标识
      //  $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
     //   $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));

    //    $activitydata = M('Activity')->where($activitywhere)->find();
     //   if (!$activitydata) {
    //        return MessageInfo(0, '没有相关奖品活动！', $userlist);
    //    }

      //  if ($type == 8) {
           //判断中奖
        //    $clparr['count'] = $count;
      //      $result = $this->ClickWinning($clparr,$aconf);
       //     if ($result['code'] == 0 && !empty($parr['ucode'])) {
       //         $tag = 1;
        //    }
      //  }

        //限制用户频繁中奖
        // $limmitnum = $this->LimitWinning($parr);
        // if ($limmitnum) {
        //     $tag = 0;
        // }

      //  if ($tag == 0) {
    //        return MessageInfo(0, '没有中奖数据', $userlist);
    //    }

        //查询奖品表
     //   $prizewhere['c_state'] = 1;
   //     $prizewhere['c_num'] = array('GT', 0);
    //    $prizewhere['c_aid'] = $activitydata['c_id'];
        // 出现现金与实物奖项
    //    if ($type == 8) {
            //查询金额池表
    //        $moneywhere['c_state'] = 1;
    //        $moneywhere['c_remain'] = array('GT', 0);
    //        $moneywhere['c_aid'] = $activitydata['c_id'];
    //        $moneydata = M('Activity_money')->where($moneywhere)->find();
     //       if (!$moneydata) {
    //            return MessageInfo(0, '糟糕手慢了，该红包已被人先手一步了！', $userlist);
    //        }

     //       $img = GetHost() . '/' . $moneydata['c_imgpath'];
     //       if ($moneydata['c_rule'] == 2) {
     //           $prizewhere['c_type'] = 1;
      //          $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
     //           $img = GetHost() . '/' . $prizedata['c_imgpath'];
     //       }
    //        $name = '惊喜红包';
     //   } else {
      //      $prizewhere['c_type'] = 2;
    //        $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
    //        if (!$prizedata) {
    //            return MessageInfo(0, '糟糕手慢了，该奖品已被人先手领走了！', $userlist);
    //        }
    //        $name = '神秘礼品';
   //         $img = GetHost() . '/' . $prizedata['c_imgpath'];
   //     }

   //     $parr['sign'] = 1;
    //    $result = $this->WriteFindslog($parr);
   //     if ($result['code'] != 0) {
   //         return MessageInfo(0, '记录失败', $userlist);
   //     }

   //     $pageshow = $aconf['shownum'] - 1;
    //    $parr['pageindex'] = $parr['pageindex'];
   //     $parr['pagesize'] = $showusernum - 1;
   //     $userlist = $this->PushUsers($parr, $pushactive,$refreeproduce);
    //    $userlist[$pageshow]['type'] = $type;
   //     $userlist[$pageshow]['shop'] = "0";
   //     $userlist[$pageshow]['keyword'] = $activitydata['c_id'];
  //     $userlist[$pageshow]['name'] = $name;
    //    $userlist[$pageshow]['img'] = $img;
    //    $userlist[$pageshow]['distance'] = "0";
    //    $userlist[$pageshow]['pageindex'] = $userlist[0]['pageindex'];
     //   return MessageInfo(0, '查询成功', $this->my_sort($userlist));
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

        // $Redis = IGD('Redis','Redis');
        // $result1 = $Redis->ActivityClick('activityclick');
        // if($result1['code'] != 0){
        //     return $result1;
        // }
        // $rcount = $result1['data'];
        $rcount = F('activityclick');

        $count = $rcount + 1;
        if ($count == 1) {
            $count = M('Activity_findlog1')->count();
        }

        F('activityclick',$count);
        // $result2 = $Redis->SetKey('activityclick',$count);
        // if($result2['code'] != 0){
        //     return Message($result2['code'],'保存activityclick值失败');;
        // }

        return Message(0, '记录成功');
    }

    /**
     * 查询平台活动列表
     * @param pageindex,pagesize
     */
    function ReferActivityList($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $activitywhere['c_show'] = 1;
        $activitywhere['c_sign'] = 2;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere[] = array('c_activitytype <> 2 and c_activitytype <> 5 and c_activitytype <> 6 and c_activitytype <> 7');
        $limit = $countPage . ',' . $pageSize;
        $order = 'c_addtime desc';
        $list = M('Activity')->where($activitywhere)->limit($limit)->order($order)->select();
        if (count($list) == 0) {
            return MessageInfo(0, "查询成功", $list);
        }
        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            if (strtotime($value['c_activitystarttime']) > time()) {
                $list[$key]['progress'] = 0;
            } else if (strtotime($value['c_activitystarttime']) <= time() && strtotime($value['c_activityendtime']) >= time()) {
                $list[$key]['progress'] = 1;
            } else {
                $list[$key]['progress'] = 2;
            }
        }
        $count = M('Activity')->where($activitywhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  查询单个活动
     *  @param $aid
     */
    function GetActivityOne($aid) {
        $activitywhere['c_id'] = $aid;
        $activitydata = M('Activity')->where($activitywhere)->find();
        return MessageInfo(0, '查询成功', $activitydata);
    }

    /**
     *  查询是否有相关推送活动
     *  @param $random,$randnum
     */
    function ReferPushActivity($aconf,$parr) {
        $random = $aconf['random'];
        $randnum = $aconf['randnum'];
        $randchange = $aconf['randchange'];
        $activitywhere['a.c_state'] = 1;
        $activitywhere['a.c_show'] = 1;
        $activitywhere['a.c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['a.c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere[] = array('a.c_activitytype <> 3 and a.c_activitytype <> 4 and a.c_activitytype <> 5 and a.c_activitytype <> 7 and a.c_activitytype <> 9 and a.c_activitytype <> 10 and a.c_activitytype <> 11');

        $activitydata1 = M('Activity as a')->where($activitywhere)->order('a.c_activitytype asc,a.c_id desc')->select();
        $keynum = 0;$signrotu=null;$signred=null;$signchip=null;
        if ($activitydata1) {
            // 组合数据
            foreach ($activitydata1 as $key => $value) {
                $userlist[$keynum]['type'] = 2;
                $userlist[$keynum]['shop'] = "0";
                $userlist[$keynum]['name'] = $value['c_activityname'];
                $userlist[$keynum]['img'] = GetHost() . '/' . $value['c_pimg'];
                if ($value['c_activitytype'] == 2) {
                    $signrotu = $keynum;
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Activity/roulette?Id=1';
                } else if ($value['c_activitytype'] == 4) {
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Fullmoon/index?aid='.$value['c_id'];
                } else if ($value['c_activitytype'] == 5) {
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Seventh/index?qnum=1';
                } else if ($value['c_activitytype'] == 6) {
                    $signred = $keynum;
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Seventh/red?red=1';
                } else if ($value['c_activitytype'] == 7) {
                    $signchip = $keynum;
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Seventh/chip?chip=1';
                } else if ($value['c_activitytype'] == 10) {
                    $signballot = $keynum;
                    $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Ballot/index?aid='.$value['c_id'];
                }
                $keynum ++;
            }
        }


        $activitywhere['a.c_activitytype'] = 3;
        $activitywhere['b.c_state'] = 1;
        $activitywhere['b.c_num'] = array('GT', 0);
        $join = 'inner join t_activity as a on b.c_aid=a.c_id';
        $field = 'b.*';
        $activitydata2 = M('Activity_prize as b')->join($join)->where($activitywhere)->field($field)->order('rand()')->select();
        foreach ($activitydata2 as $key => $value) {
            $userlist[$keynum]['type'] = 2;
            $userlist[$keynum]['shop'] = "0";
            $userlist[$keynum]['keyword'] = GetHost(1) . '/index.php/Home/Activity/buying?pid=' . $value['c_id'];
            $userlist[$keynum]['name'] = $value['c_name'];
            // $userlist[$keynum]['img'] = GetHost() . '/' . $value['c_imgpath'];
            $imgarr = explode("|", $value['c_imgpath']);

            foreach ($imgarr as $key1 => $value1) {
                $userlist[$keynum]['img'] = GetHost() . '/' . $value1;
            }
            $keynum++;
        }

        //点击次数
        // $Redis = IGD('Redis','Redis');
        // $clickcount = $Redis->Back()->get('activityclick');
        $clickcount = F('activityclick');

        //国庆活动
        $ballotrand = rand(1,5);
        if ($ballotrand == 1 && isset($signballot)) {
            $arraydata[] = $userlist[$signballot];
        }

        //商家红包
        $redclick = $aconf['redclick'];
        if ((($clickcount % $redclick) == 0) && isset($signred)) {
            //查询有没有商家红包
            $prizewhere['c_type'] = 1;
            $prizewhere['c_aid'] = $activitydata1[$signred]['c_id'];
            $prizewhere['c_state'] = 1;
            $prizewhere['c_num'] = array('GT', 0);
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            if ($prizedata) {
                $arraydata[] = $userlist[$signred];
                $parr['sign'] = 2;
                $result = $this->WriteFindslog($parr);
            }
        }

        // 集碎片活动
        $hourstime = date('H');
        $minutime = date('i');
        $chiptime = !empty($aconf['chiptime'])?explode('|',$aconf['chiptime']):array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');
        if($minutime <= 60) {
            if (in_array($hourstime,$chiptime) && isset($signchip)) {
                $chipclick = $aconf['chipclick'];
                if (($clickcount % $chipclick) == 0) {
                    $arraydata[] = $userlist[$signchip];
                    $parr['sign'] = 3;
                    $result = $this->WriteFindslog($parr);
                }
            }
        }

        //随机活动（大转盘）
        //获取剩余的参与次数
        $lotterywhere['c_ucode'] = $parr['ucode'];
        $lotterywhere['c_rule'] = 2;
        $chance = M('Activity_lotterynum')->where($lotterywhere)->getField('c_num');

        if ($random != 1 && $chance > 0) { //不随机，有机会即出现转盘
            if (isset($signrotu)) {
                $arraydata[] = $userlist[$signrotu];
                unset($userlist[array_search($userlist[$signrotu],$userlist)]);
            }
        }else{
            $rouletteconf = C('Roulette');
            // $rouletteconf = IGD('Redis','Redis')->Gethash('roulette')['data'];
            if (($clickcount % $rouletteconf['num']) == 0) {
                if (isset($signrotu)) {
                    $userlist[$signrotu]['keyword'] = GetHost(1) . '/index.php/Home/Activity/roulette?Id=101';
                    $arraydata[] = $userlist[$signrotu];
                    unset($userlist[array_search($userlist[$signrotu],$userlist)]);
                }
            }
        }

        //删除指定数据
        unset($userlist[array_search($userlist[$signred],$userlist)]);
        unset($userlist[array_search($userlist[$signchip],$userlist)]);
        unset($userlist[array_search($userlist[$signrotu],$userlist)]);
        unset($userlist[array_search($userlist[$signballot],$userlist)]);

        if ($randnum > count($arraydata)) {
            //补充活动不足个数
            for ($i = count($arraydata); $i < ($randnum - 1); $i++) {
                $arrcount = count($userlist) + $randchange;
                $index = rand(0, $arrcount);
                $arraydata[] = $userlist[$index];
                unset($userlist[array_search($userlist[$index],$userlist)]);
            }

        } else {
            //去除活动多余个数
            for ($j=0; $j < (count($arraydata) - $randnum); $j++) {
                $crandom = count($arraydata) - 1;
                $arrdom = rand(0,$crandom);
                array_splice($arraydata, $arrdom, 1);
            }
        }
        $result = array_filter($arraydata);
        return $result;
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

    /**
     * 限制用户频繁中奖
     * @param ucode
     */
    function LimitWinning($parr) {
        $uip = get_client_ip();
        $ucode = $parr['ucode'];
        $where['c_sign'] = 1;
        $where['c_addtime'] = array('EGT', date('Y-m-d H:i:s', strtotime('-30 seconds', time())));
        $where[] = array("c_ucode='$ucode' or c_ip='$uip'");
        $data = M('Activity_findlog1')->where($where)->order('c_addtime desc')->find();
        if (!$data) {
            return false;
        }
        return true;
    }

    /**
     *  根据配入的点击次数中奖
     *  @param count
     */
    function ClickWinning($parr,$aconf) {
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
     *  领取奖项
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
        if(empty($findlog)) {
            return Message(1032, '糟糕手慢了，该红包已被人先手一步了！');
        }

        //查询是否领取过
        $findlogwhere['c_fid'] = $findlog['c_id'];
        $result = M('Activity_log')->where($findlogwhere)->count();
        if ($result==0) {
            $tag = 1;
        }else{
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
                return Message(1032, '糟糕手慢了，该红包已被人先手一步了！33333');
            }

			$moneydata['c_rule']=1;

            if ($moneydata['c_rule'] == 2) {
                $prizewhere['c_type'] = 1;
                $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            }
        } else {
            $prizewhere['c_type'] = 2;
            $prizedata = M('Activity_prize')->where($prizewhere)->order('rand()')->find();
            if (!$prizedata) {
                $db->rollback(); //不成功，则回滚
                return Message(1032, '糟糕手慢了，该奖品已被人先手领走了！44444');
            }
        }

        // 写入活动记录表
        if ($prizedata) {
            $prizelogdata['c_ucode'] = $parr['ucode'];
            $prizelogdata['c_pcode'] = $prizedata['c_pcode'];
            $prizelogdata['c_pid'] = $prizedata['c_id'];
            $prizelogdata['c_value'] = $prizedata['c_value'];
            if ($parr['type'] == 8) {
                if (($moneydata['c_remain'] - $prizedata['c_value']) < 0) {
                    $prizelogdata['c_value'] = $moneydata['c_remain'];
                }
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
        $result = $this->WriteActlog($prizelogdata);
        if ($result['code'] != 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1033, '奖品领取记录失败！');
        }

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

            // 扣除奖池总额
            $result = M('Activity_money')->where($moneywhere)->setDec('c_remain', $prizelogdata['c_value']);
            if (!$result) {
                $db->rollback(); //不成功，则回滚
                return Message(1033, '扣除奖池总额失败！');
            }

            if ($prizedata) {
                // 扣除奖项库存
                $jxwhere['c_id'] = $prizedata['c_id'];
                $result = M('Activity_prize')->where($jxwhere)->setDec('c_num', 1);
                if (!$result) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1033, '扣除奖项库存失败！');
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
        $Msgcentre = D('Msgcentre', 'Service');
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

        if($count1>1){
            $db->rollback(); //不成功，则回滚
            return Message(1032, '您已经领取过该奖品111');
        }

        $db->commit();
        $returndata['money'] = $prizelogdata['c_value'];
        if ($parr['type'] == 1) {
            $returndata['img'] = GetHost() . '/' . $moneydata['c_imgpath'];
        } else {
            $returndata['pid'] = $prizedata['c_id'];
            $returndata['img'] = GetHost() . '/' . $prizedata['c_imgpath'];
            $returndata['url'] = GetHost(1) . '/index.php/Home/Activity/prize?pid=' . $prizedata['c_id'];
        }

        return MessageInfo(0, '领取成功', $returndata);
    }

    /**
     *  推送的相关用户
     *  @param longitude,latitude,pageindex,pagesize
     */
    function PushUsers($param, $pushactive,$producedata) {
        $wishdata = $this->ActivityWish();
        if (empty($param['pageindex'])) {
            $parr['pageindex'] = 1;
        } else {
            $parr['pageindex'] = $param['pageindex'];
        }

        if ($wishdata) {
            $parr['pagesize'] = $param['pagesize'] - 1;
        } else {
            $parr['pagesize'] = $param['pagesize'];
        }
        $parr['longitude'] = $param['longitude'];
        $parr['latitude'] = $param['latitude'];
        if (empty($param['longitude']) || empty($param['latitude']) || $param['longitude'] == '0.0' || $param['latitude'] == '0.0') {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $Coalition = D('Coalition', 'Service');
        $result = $Coalition->CoalitionUserwebList($parr);
        $list = $result['data']['list'];
        if (count($list) != $parr['pagesize']) {
            $parr['pageindex'] = 1;
            $result = $Coalition->CoalitionUserwebList($parr);
            $list = $result['data']['list'];
        }

        $tempcount = 0;

        foreach ($list as $key => $value) {
            $arrlist[$tempcount]['type'] = 5;
            $arrlist[$tempcount]['shop'] = $value['c_shop'];
            $arrlist[$tempcount]['keyword'] = $value['c_ucode'];
            $arrlist[$tempcount]['name'] = subtext($value['c_nickname'], 6);
            $arrlist[$tempcount]['img'] = $value['c_headimg'].'?imageMogr2/thumbnail/200';
            $arrlist[$tempcount]['longitude'] = $value['longitude'];
            $arrlist[$tempcount]['latitude'] = $value['latitude'];
            $arrlist[$tempcount]['distance'] = $value['c_distance'];
            $arrlist[$tempcount]['pageindex'] = $parr['pageindex'];
            $tempcount++;
        }


        foreach ($pushactive as $key => $value) {
            $arrlist[$tempcount]['type'] = $value['type'];
            $arrlist[$tempcount]['shop'] = $value['shop'];
            $arrlist[$tempcount]['keyword'] = $value['keyword'];
            $arrlist[$tempcount]['name'] = $value['name'];
            $arrlist[$tempcount]['img'] = $value['img'];
            $arrlist[$tempcount]['pageindex'] = $parr['pageindex'];
            $arrlist[$tempcount]['distance'] = "";
            $tempcount++;
        }

        foreach ($producedata as $k => $v) {
            $arrlist[$tempcount]['type'] = $v['type'];
            $arrlist[$tempcount]['shop'] = $v['shop'];
            $arrlist[$tempcount]['keyword'] = $v['keyword'];
            $arrlist[$tempcount]['name'] = $v['name'];
            $arrlist[$tempcount]['img'] = $v['img'];
            $arrlist[$tempcount]['pageindex'] = $parr['pageindex'];
            $arrlist[$tempcount]['distance'] = "";
            $tempcount++;
        }

        if ($wishdata) {
            $arrlist[$tempcount]['type'] = $wishdata['type'];
            $arrlist[$tempcount]['shop'] = $wishdata['shop'];
            $arrlist[$tempcount]['keyword'] = $wishdata['keyword'];
            $arrlist[$tempcount]['name'] = $wishdata['name'];
            $arrlist[$tempcount]['img'] = $wishdata['img'];
            $arrlist[$tempcount]['pageindex'] = $parr['pageindex'];
            $arrlist[$tempcount]['distance'] = "";
            $tempcount++;
        }

        //不够数量的用户补充商品
        if ($parr['pagesize'] > $tempcount) {
            $parr['cnum'] = $parr['pagesize'] - $tempcount;
            $refreeproduce = $this->GetRefreeProduce($parr);
            foreach ($refreeproduce as $k => $v) {
                $arrlist[$tempcount]['type'] = $v['type'];
                $arrlist[$tempcount]['shop'] = $v['shop'];
                $arrlist[$tempcount]['keyword'] = $v['keyword'];
                $arrlist[$tempcount]['name'] = $v['name'];
                $arrlist[$tempcount]['img'] = $v['img'];
                $arrlist[$tempcount]['pageindex'] = $parr['pageindex'];
                $arrlist[$tempcount]['distance'] = "";
                $tempcount++;
            }
        }

        return $this->my_sort($arrlist);
    }

    /**
     * 推荐商品
     * @param cnum
     */
    function GetRefreeProduce($parr)
    {
        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $where['c_isagent'] = 0;
        $result = M('Product')->where($where)->limit($parr['cnum'])->order('rand()')->select();
        foreach ($result as $key => $value) {
            $result[$key]['type'] = 4;
            $result[$key]['shop'] = "1";
            $result[$key]['keyword'] = $value['c_pcode'];
            $result[$key]['name'] = subtext($value['c_name'], 6);
            $result[$key]['img'] = GetHost() . '/' . $value['c_pimg'].'?imageMogr2/thumbnail/200';
        }
        return $result;
    }

    // 查询祝福活动
    function ActivityWish()
    {
        $activitywhere['c_state'] = 1;
        $activitywhere['c_show'] = 1;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 9;
        $wishdata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$wishdata || rand(1,3) != 2) {
            return false;
        }
        $result['type'] = 2;
        $result['shop'] = "0";
        $result['keyword'] = GetHost(1) . '/index.php/Home/Wish/send?aid='.$wishdata['c_id'];
        $result['name'] = subtext($wishdata['c_activityname'], 6);
        $result['img'] = GetHost() . '/' . $wishdata['c_pimg'];
        return $result;
    }

    function my_sort($arrays){
        foreach ($arrays as $key => $value) {
            $arr = array_rand($arrays,1);
            $newarr = array_splice($arrays, $arr, 1);
            $array[] = $newarr[0];
        }
        return $array;
    }
}
