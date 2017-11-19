<?php

/**
 * 商家炸金花接口
 */
class BettingActivity {
    //4代表黑桃,3代表红桃,2代表梅花,1代表方块
    public $suits = array('4', '3', '2', '1');
    public $figures = array('2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A');
    public $cards = array();
    public function __construct()
    {
        $cards = array();
        foreach($this->suits as $suit){
            foreach($this->figures as $figure){
                $cards[] = array($suit,$figure);
            }
        }
        $this->cards = $cards;
    }



    /**
     * 添加炸金花活动
     * @param ucode,name,imgpath,value,num,type,marketprice,jionnum,postscript,pic
     */
    function AddBettingActivity($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'请先登录再操作');
        }

        //查询活动
        $activitywhere['c_show'] = 1;
        $activitywhere['c_sign'] = 2;
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 8;
        $activitydata = M('Activity')->where($activitywhere)->order('c_id desc')->find();
        if (!$activitydata) {
            return Message(2000,'炸金花活动不存在');
        }

        $datainfo['c_acode'] = $parr['ucode'];
        $datainfo['c_aid'] = $activitydata['c_id'];
        $datainfo['c_name'] = $parr['name'];
        $datainfo['c_imgpath'] = $parr['imgpath'];
        $datainfo['c_value'] = $parr['value'];
        $datainfo['c_totalnum'] = $parr['num'];
        $datainfo['c_num'] = $parr['num'];
        $datainfo['c_state'] = 1;
        $datainfo['c_type'] = $parr['type'];
        $datainfo['c_marketprice'] = $parr['marketprice'];
        $datainfo['c_bargainnum'] = 17;
        $datainfo['c_postscript'] = $parr['postscript'];
        $datainfo['c_card'] = json_encode($this->cards);
        $datainfo['c_pic'] = $parr['pic'];
        $datainfo['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Activity_prize')->add($datainfo);
        if (!$result) {
            return Message(2001,'添加活动失败');
        }
        $datainfo['c_id'] = $result;
        return MessageInfo(0,'添加成功',$datainfo);
    }

    /**
     * 领取手牌
     * @param aid,pid,openid,wxname,headerimg
     */
    function RecieveBettingCard($parr)
    {
        if (empty($parr['openid'])) {
            return Message(1004,'请从微信端进入参与活动');
        }
        //查询活动
        $activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_activitystarttime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activityendtime'] = array('EGT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 8;
        $activitydata = M('Activity')->where($activitywhere)->find();
        if (!$activitydata) {
            return Message(2000,'活动未开始或已结束');
        }

        //查询是否领取记录
        $prizelogwhere['c_pid'] = $parr['pid'];
        $prizelogwhere['c_aid'] = $parr['aid'];
        $prizelogwhere['c_openid'] = $parr['openid'];
        $result = M('Activity_bargin')->where($prizelogwhere)->find();
        if ($result) {
            $pushtdata['playcard'] = objarray_to_array(json_decode($result['c_play_card']));
            $pushtdata['remark'] = $result['c_remark'];
            $pushtdata['score'] = $result['c_barginid'];
            return MessageInfo(0,'已经领取',$pushtdata);
        }

        //查询是否还可以参与
        $prizewhere['c_id'] = $parr['pid'];
        $prizewhere['c_aid'] = $parr['aid'];
        $prizewhere['c_state'] = 1;
        $prizewhere['c_num'] = array('GT', 0);
        $prizewhere['c_bargainnum'] = array('GT', 0);
        $prizedata = M('Activity_prize')->where($prizewhere)->find();
        if (!$prizedata) {
            return Message(2004,'活动人数已满，不能再参与');
        }

        $db = M('');
        $db->startTrans();
        //获取手牌与得分
        $result = $this->ownScore($prizedata);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        $pushtarr = $result['data'];
        $addlog['c_aid'] = $parr['aid'];
        $addlog['c_pid'] = $parr['pid'];
        $addlog['c_play_card'] = json_encode($pushtarr['playcard']);
        $addlog['c_remark'] = $pushtarr['remark'];
        $addlog['c_barginid'] = $pushtarr['score'];
        $addlog['c_openid'] = $parr['openid'];
        $addlog['c_wxname'] = $parr['wxname'];
        $addlog['c_headerimg'] = $parr['headerimg'];
        $addlog['c_bargintime'] = date('Y-m-d H:i:s');
        $result = M('Activity_bargin')->add($addlog);
        if (!$result) {
            $db->rollback();
            return Message(2001,'领取失败');
        }


        $db->commit();
        return MessageInfo(0,'领取成功',$pushtarr);

    }

    /**
     * 活动领取信息并生成获奖人
     * @param aid,pid,openid
     */
    function ViewBettingInfo($parr)
    {
        //获奖奖品信息
        $prizewhere['c_id'] = $parr['pid'];
        $prizewhere['c_aid'] = $parr['aid'];
        $prizedata = M('Activity_prize')->where($prizewhere)->find();
        $prizedata['c_imgpath'] = GetHost().'/'.$prizedata['c_imgpath'];
        $prizedata['c_pic'] = GetHost().'/'.$prizedata['c_pic'];

        //活动结束生成获奖人
        $activitywhere['c_id'] = $parr['aid'];
        $activitywhere['c_activityendtime'] = array('ELT', date('Y-m-d H:i:s'));
        $activitywhere['c_activitytype'] = 8;
        $activitydata = M('Activity')->where($activitywhere)->find();
        if ($activitydata || $prizedata['c_bargainnum'] == 0) {
            //查询最高分
            $bettingwhere['c_aid'] = $parr['aid'];
            $bettingwhere['c_pid'] = $parr['pid'];
            $bettinghigh = M('Activity_bargin')->where($bettingwhere)->order('c_barginid desc')->find();
            if ($bettinghigh) {
                //查询有没有获奖人
                $bettingwhere['c_sign'] = 1;
                $result = M('Activity_bargin')->where($bettingwhere)->getField('c_id');
                //生成获奖人
                if (!$result) {
                    $bettingwhere['c_id'] = $bettinghigh['c_id'];
                    $bettingwhere['c_sign'] = 0;
                    $save['c_sign'] = 1;
                    $result = M('Activity_bargin')->where($bettingwhere)->save($save);
                }
            }
        }

        //查询是否领取记录
        $prizelogwhere['c_pid'] = $parr['pid'];
        $prizelogwhere['c_aid'] = $parr['aid'];
        $prizelogwhere['c_openid'] = $parr['openid'];
        $result = M('Activity_bargin')->where($prizelogwhere)->find();
        if ($result) {
            $pushtdata['playcard'] = objarray_to_array(json_decode($result['c_play_card']));
            $pushtdata['remark'] = $result['c_remark'];
            $pushtdata['score'] = $result['c_barginid'];
            $pushtdata['sign'] = $result['c_sign'];
            $sql = "SELECT tb.rownum FROM ( SELECT *, @rownum := @rownum + 1 AS num_tmp, @incrnum := CASE WHEN @rowtotal = obj.c_barginid THEN @incrnum "
            ."WHEN @rowtotal := obj.c_barginid THEN @rownum END AS rownum "
            ."FROM ( SELECT * FROM `t_activity_bargin`  where c_pid='".$parr['pid']."' ORDER BY c_barginid DESC ) AS obj, ( "
            ."SELECT @rownum := 0 ,@rowtotal := NULL ,@incrnum := 0 ) r ) AS tb where tb.c_openid='".$parr['openid']."'";
            $rank = M('')->query($sql);
            $pushtdata['rank'] = $rank[0]['rownum'];
        }

        $data['prizedata'] = $prizedata;
        $data['bettingdata'] = $pushtdata;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 获取领取记录
     * @param aid,pid,pageindex,pagesize
     */
    function GetBettingLog($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_aid'] = $parr['aid'];
        $where['c_pid'] = $parr['pid'];
        $list = M('Activity_bargin')->where($where)->order('c_barginid desc')->limit($countPage, $pageSize)->select();
        if (!$list) {
            return Message(0, '没有查询到数据');
        }
        foreach ($list as $key => $value) {
            $bettingarr = objarray_to_array(json_decode($value['c_play_card']));
            $figure = '';
            foreach ($bettingarr as $k => $v) {
                $figure[] = $v[1];
            }
            if($figure[1] == $figure[2]){
                $temp = $figure[0];
                $figure[0] = $figure[2];
                $figure[2] = $temp;
            }

            $list[$key]['c_play_card'] = implode('', $figure);
        }

        $count = M('Activity_bargin')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 生成三张牌并获得得分
     * 黑桃+4,红桃+3,梅花+2,方块+1
     * remark 1散牌,2对子,3顺子,4金花,5同花顺,6豹子
     */
    function OwnScore($prizedata)
    {
        $arrcards = objarray_to_array(json_decode($prizedata['c_card']));
        //生成3张牌
        shuffle($arrcards);
        $card = array(array_pop($arrcards), array_pop($arrcards), array_pop($arrcards));

        //改变参与人数与扑克牌数
        $prizewhere['c_id'] = $prizedata['c_id'];
        $saveinfo['c_bargainnum'] = $prizedata['c_bargainnum'] - 1;
        $saveinfo['c_card'] = json_encode($arrcards);
        $result = M('Activity_prize')->where($prizewhere)->save($saveinfo);
        if (!$result) {
            return Message(1000,'领取扑克牌失败');
        }

        $suit = $figure = array();
        foreach($card as $v){
            $suit[] = $v[0];
            $figure[] = array_search($v[1],$this->figures)+2;
        }

        //花色分值
        $suitccore = $suit[0] + $suit[1] + $suit[2];

        //补齐前导0
        for($i = 0; $i < 3; $i++){
            $figure[$i] = str_pad($figure[$i],2,'0',STR_PAD_LEFT);
        }
        rsort($figure);

        //对于对子做特殊处理
        if($figure[1] == $figure[2]){
            $temp = $figure[0];
            $figure[0] = $figure[2];
            $figure[2] = $temp;
        }
        $score = $figure[0].$figure[1].$figure[2];
        $remark = '散牌！';

        //豹子 60*100000
        if($figure[0] == $figure[1] && $figure[0] == $figure[2]){
            $score += 60*100000;
            $remark = '豹子！';
        }

        //金花 30*100000
        if($suit[0] == $suit[1] && $suit[0] == $suit[2]){
            $score += 30*100000;
            $remark = '金花！';
        }

        //顺子 20*100000
        if($figure[0] == $figure[1]+1 && $figure[1] == $figure[2]+1){
            $score += 20*100000;
            $remark = '顺子！';
        }

        //同花顺
        if($suit[0] == $suit[1] && $suit[0] == $suit[2] && $figure[0] == $figure[1]+1 && $figure[1] == $figure[2]+1){
            $remark = '同花顺！';
        }

        //对子 10*100000
        if($figure[0] == $figure[1] && $figure[1] != $figure[2]){
            $score += 10*100000;
            $remark = '对子！';
        }

        $data['playcard'] = $card;
        $data['remark'] = $remark;
        $data['score'] = $score + $suitccore;
        return MessageInfo(0,'领取成功',$data);
    }


}
