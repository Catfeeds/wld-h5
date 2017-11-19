<?php

/**
 * 	中秋祝福活动
 *
 */
class SignupActivity {

    /**
     * 获取签到信息
     * @param aid,sendname,receivername,content,imgurl
     */
    function GetSignInfo($parr){
        $ucode =$parr['ucode'];
        if(empty($ucode)){
            return Message(1001,'参数缺失');
        }
        $where['c_createtime'] =array('egt',date('Y-m-d')." 00:00:00");
        $where['c_ucode'] =$ucode;
        $record =M('Signup_record')->where($where)->find();
        $info =M('Users_signup')->where(array('c_ucode'=>$ucode))->find();
        $data['count'] =$info['c_count']==null?0:$info['c_count'];
        if(empty($record)){ //今日还未签到
            $data['sign']=0;
        }else{
            $data['sign']=1;
        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 获取用户签到和中奖记录
     * @param ucode
     */
    function GetSignRecord($parr){
        $ucode =$parr['ucode'];
        if(empty($ucode)){
            return Message(1001,'参数缺失');
        }

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $list =M('Signup_record')->order('c_createtime desc')->where(array('c_ucode'=>$ucode))->field('c_id,c_createtime,c_flag,c_money')->limit($countPage, $pageSize)->select();
        $count =M('Signup_record')->order('c_id desc')->where(array('c_ucode'=>$ucode))->count();
        $pageCount = ceil($count / $pageSize);

        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 签到
     * @param  ucode
     */

    function SignUp($parr){
        $ucode =$parr['ucode'];
        if(empty($ucode)){
            return Message(1001,'参数缺失');
        }
        $today =date('Y-m-d')." 00:00:00";
        $where['c_ucode']=$ucode;
        //$where['c_flag']=0;
        $where['c_createtime']=array('egt',$today);
        $info =M('Users_signup')->where(array('c_ucode'=>$ucode))->find();
        $record =M('Signup_record')->where($where)->find();
        $addSign =array(
            'c_ucode'=>$ucode,
            'c_count'=>1,
            'c_addtime'=>date('Y-m-d H:i:s')
        );

        //获取活动数据
        $result = IGD('Index','Newact')->GetPlatActInfo(30,'');
        if ($result['code'] != 0) {
            return Message(3000,'活动还没有开始');
        }
        $actdata = $result['data'];
        $yu['ucode']=$ucode;

        $db =M('');
        if(empty($info) && empty($record)){  //没有历史数据  执行签到
            try{
                $db->startTrans();
                if(M('Users_signup')->add($addSign)){
                    $red =$this->WriteRedRecord($ucode,$actdata);
                    $redbag = array(
                        'c_ucode' => $ucode,
                        'c_createtime' => date('Y-m-d H:i:s'),
                        'c_flag' => 2,
                        'c_money' =>$red
                    );
                    M('Signup_record')->add($redbag);
                    $data['type'] =2;
                    $data['money'] =$red;
                    $data['count'] =$info['c_count']+1;
                    $db->commit();
                }else{
                    $db->rollback();
                }
            }catch(Exception $e){
                $db->rollback();
                return Message(1002,'签到失败');
            }
        }elseif(!empty($info) && empty($record)){   //有历史签到数据  但今天没签到
            $yestoday =date("Y-m-d",strtotime("-1 day"));
            $con['c_ucode'] =$ucode;
            $con['c_createtime'] =array('egt',$yestoday." 00:00:00");
            $yesinfo =M('Signup_record')->where($con)->find();
            try{
                 $db->startTrans();
                if($info['c_count']<6){   //正常日期  送红包  七天则只送抽奖不送红包
                    $red =$this->WriteRedRecord($ucode,$actdata);
                    $redbag = array(
                        'c_ucode' => $ucode,
                        'c_createtime' => date('Y-m-d H:i:s'),
                        'c_flag' => 2,
                        'c_money' => $red
                    );
                    $data['type'] =2;
                    $data['money'] =$red;
                    $data['count'] =$info['c_count']+1;
                }
                if(!empty($yesinfo)){
                    M('Users_signup')->where(array('c_ucode'=>$ucode))->setInc('c_count');
                    if($info['c_count']==6){   //连续签到了6 天 今天再签到满足7天条件将获取抽奖机会，然后签到天数清零
                        $arr['ucode']=$ucode;
                        $arr['rule']=2;
                        $return =IGD('Advert','Newact')->AddActNum($arr,1); //增加一次大转盘抽奖机会
                        if($return['code'] ==0){
                            M('Users_signup')->where(array('c_ucode'=>$ucode))->save(array('c_count'=>0,'c_updatetime'=>date('Y-m-d H:i:s')));
                            //M('Signup_record')->add($redRecord); //写一条抽奖记录
                            $redbag = array(
                                'c_ucode' => $ucode,
                                'c_createtime' => date('Y-m-d H:i:s'),
                                'c_flag' =>1,
                                'c_money' =>1
                            );
                            $data['type'] =1;
                            $data['money'] =1;
                            $data['count'] =7;

                        }else{
                             $db->rollback();
                        }
                    }
                }else{
                    M('Users_signup')->where(array('c_ucode'=>$ucode))->save(array('c_count'=>1,'c_updatetime'=>date('Y-m-d H:i:s')));
                }

                M('Signup_record')->add($redbag);

                 $db->commit();

            }catch(Exception $e){
                // $db->rollback();
                return Message(1002,'签到失败');
            }
        }elseif(!empty($record)){     //今天已签过到
            return Message(1004,'今日不能重复签到！');
        }

        return MessageInfo(0,'签到成功',$data);

    }


    //写红包数据

    function WriteRedRecord($ucode,$actdata){
        $yu['ucode']=$ucode;
        $rr =$this->ReceiveMoney($yu,$actdata);
        if($rr['code']==0) {
            return $rr['data']['c_value'];
        }else{
            return false;
        }

    }

    /**
     * 签到之后随机获取红包
     * @param ucode
     */
    function ReceiveMoney($parr,$actdata)
    {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];

        $prizewhere['c_joinaid'] = $actdata['c_id'];
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizedata = M('A_redprize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(0, '红包已被抢光，进店逛逛吧~');
        }

        //计算红包金额
        if ($prizedata['c_type'] == 2) {
            $money_arr = IGD('Red','Newact')->randBonus($prizedata['c_remain_money'],$prizedata['c_num'],1);
            $money = $money_arr[0];
        } else if ($prizedata['c_type'] == 1) {
            $money = sprintf('%.2f',$prizedata['c_money']/$prizedata['c_totalnum']);
        } else {
            //查询商家红包
            $money = rand($prizedata['c_value']*100,$prizedata['c_maxvalue']*100)/100;
        }

        //写入领取记录
        $parr['joinaid'] = $prizedata['c_joinaid'];
        $parr['pid'] = $prizedata['c_pid'];
        $parr['awid'] = $prizedata['c_id'];
        $parr['acode'] = $acode;
        $parr['name'] = $prizedata['c_name'];
        $parr['img'] = $prizedata['c_img'];
        $parr['value'] = $money;
        $parr['marks'] = $prizedata['c_remark'];
        $parr['type'] = 2;
        $parr['state'] = 1;
        $result = $this->WriteRedReciveLog($parr);
        if ($result['code'] != 0) {
            return $result;
        }

        //扣除奖项数量
        $prizewhere['c_id'] = $prizedata['c_id'];
        $prisave['c_remain_money'] = $prizedata['c_remain_money'] - $money;
        $prisave['c_num'] = $prizedata['c_num'] - 1;
        $result = M('A_redprize')->where($prizewhere)->save($prisave);
        if (!$result) {
            return Message(3002, '扣除奖项剩余数量失败');
        }

        if ($prizedata['c_pid']) {
            //修改红包信息
            $result = IGD('Red','Newact')->DecCouponCard($prizedata['c_pid'],1,1);
            if($result['code'] != 0){
                return Message(1001,"扣除红包数量失败");
            }

            //扣除红包管理总金额
            $redwhere['c_id'] = $prizedata['c_pid'];
            $result = M('A_actred')->where($redwhere)->setDec('c_remain_money',$money);
            if(!$result){
                return Message(1001,"扣除红包总金额失败");
            }
        }

        $showsign = '签到';


        //操作用户余额
        $param['type'] = 1;
        $param['ucode'] = $ucode;
        $param['money'] = $money;
        $param['source'] = 18;
        $param['key'] = $prizedata['c_id'];
        $param['desc'] = '在店铺中领取商家发出的红包';
        $param['state'] = 1;
        $param['isagent'] = 0;
        $param['joinaid']= $prizedata['c_joinaid'];
        $param['showimg'] = 'Uploads/settlementshow/hong.png';
        $param['showtext'] = '领取'.$showsign.'红包';
        $result = IGD('Money','User')->OptionMoney($param);
        if($result['code'] != 0){
            return Message(3002,'修改用户余额失败');
        }

        //写入消息中心
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $ucode;
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '您发现的'.$showsign.'红包金额为￥' . $money . '，领取成功已转入余额';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Balance/Index/budget';
        $msgresult = $Msgcentre->CreateMessege($msgdata);

        $prizedata['c_value'] = $money;
        return MessageInfo(0,'领取成功',$prizedata);
    }


    /**
     * 新增红包领取记录
     * @param ucode,pid,orderid,joinaid,awid,acode,name,img,value,marks,type,state
     */
    function WriteRedReciveLog($parr)
    {
        $log['c_ucode'] = $parr['ucode'];
        $log['c_pid'] = $parr['pid'];
        $log['c_orderid'] = $parr['orderid'];
        $log['c_joinaid'] = $parr['joinaid'];
        $log['c_awid'] = $parr['awid'];
        $log['c_acode'] = $parr['acode'];
        $log['c_name'] = $parr['name'];
        $log['c_img'] = $parr['img'];
        $log['c_value'] = $parr['value'];
        $log['c_marks'] = $parr['marks'];
        $log['c_type'] = $parr['type'];
        $log['c_state'] = $parr['state'];
        $log['c_addtime'] = gdtime();
        $result = M('A_actredlog')->add($log);
        if (!$result) {
            return Message(3003, '领取记录添加失败！');
        }
        return Message(0, '领取记录成功！');
    }


}

?>