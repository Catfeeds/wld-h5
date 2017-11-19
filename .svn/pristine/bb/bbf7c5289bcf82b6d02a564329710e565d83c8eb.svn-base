<?php

/**
 * 用户消息中心接口
 */
class MsgcentreMessage {
	//消息中心模块

	//消息中心消息数量
	public function Getmsgnum($parr){
		$ucode = $parr['ucode'];
        if (empty($ucode)) {
            $data['sys_msg'] = '0';
            $data['order_msg'] = '0';
            $data['zongnum'] = '0';
            $data['limitpay'] = 2;
            return $data;
        }
        // $sql1 = "select count(c_txcode) as num from t_users_msg where c_txcode not in ";
        // $sql1 .= "(select c_txcode from t_users_msglog where c_ucode='$ucode') ";
        // $sql1 .= "and (c_ucode='' or c_ucode is null or c_ucode='$ucode') and c_type=0 limit 1";
        // $count1 = M('')->query($sql1);
        // $data['sys_msg'] = ($count1[0]['num'] <= 0) ? 0 : $count1[0]['num'];
        $where['c_ucode'] = $ucode;
        $where['c_type'] = 0;
        $sys_msg = M('Users_msgnum')->where($where)->getField('c_num');
        $data['sys_msg'] = ($sys_msg <= 0) ? '0' : $sys_msg;

        // $sql2 = "select count(c_txcode) as num from t_users_msg where c_txcode not in ";
        // $sql2 .= "(select c_txcode from t_users_msglog where c_ucode='$ucode') ";
        // $sql2 .= "and (c_ucode='' or c_ucode is null or c_ucode='$ucode') and c_type=1 limit 1";
        // $count2 = M('')->query($sql2);
        // $data['order_msg'] = ($count2[0]['num'] <= 0) ? 0 : $count2[0]['num'];
		$where['c_type'] = 1;
        $order_msg = M('Users_msgnum')->where($where)->getField('c_num');
        $data['order_msg'] = ($order_msg <= 0) ? '0' : $order_msg;
        $data['zongnum'] = strval($data['sys_msg']+$data['order_msg']);
        $data['limitpay'] = 2;
        return $data;
	}

    //获取各类消息的总条数  add by james
    public function GetEachCount($parr){
        $ucode = $parr['ucode'];
        if (empty($ucode)) {
            $data1['c_content']='';
            $data1['c_time']='';
            $data1['count']='';
            $data1['msg_total']='';
            $data2['c_content']='';
            $data2['c_time']='';
            $data2['count']='';
            $data2['msg_total']='';
            $data3['c_content']='';
            $data3['c_time']='';
            $data3['count']='';
            $data3['msg_total']='';
            $data4['c_content']='';
            $data4['c_time']='';
            $data4['count']='';
            $data4['msg_total']='';
            return [$data1,$data2,$data4,$data3];
        }
        $con0=array(
            'c_ucode'=>$ucode,
            'c_type'=>0
        );
        $con1=array(
            'c_ucode'=>$ucode,
            'c_type'=>1
        );
        $con2=array(
            'c_ucode'=>$ucode,
            'c_type'=>2
        );
        $con3=array(
            'c_ucode'=>''
        );

        //订单
        $data1 =$this->getData($con1,$ucode,1);

        //活动
        $data2 = $this->getData($con2,$ucode,2);

        //公告
        // $gong_msg =M('Users_msg')->where($con3)->count();

        $w[] = array("c_ucode is null and c_isread=0");
        $gongInfo=M('Users_msg')->order('c_addtime desc')->where($w)->field('c_id,c_content,c_addtime')->find();
        $gong_new=M('Users_msg')->order('c_addtime desc')->where(array("c_ucode is null"))->field('c_id,c_content,c_addtime')->find();
        if(!empty($gongInfo)){
            $data4['c_content'] =$gongInfo['c_content'];
            $data4['c_time'] =$this->exchangeTime($gongInfo['c_addtime']);
        }else{
            $data4['c_content'] =$gong_new['c_content'];
            $data4['c_time'] =$gong_new['c_addtime']==null?null:$this->exchangeTime($gong_new['c_addtime']);
        }
//        $where['c_ucode'] ='';
//        $where['c_isread'] =0;
        $data4['count'] =M('Users_msg')->where($w)->count();
        // $data4['msg_total'] =$gong_msg;

        //系统
        $data3 =$this->getData($con0,$ucode,0);
        return [$data1,$data2,$data4,$data3];
    }


    public function getData($con,$ucode,$type){
        //订单
        $order_msg = M('Users_msgnum')->where($con)->getField('c_num');
        $orderInfo =M('Users_msg')->order('c_addtime desc')->where(array('c_ucode'=>$ucode,'c_type'=>$type,'c_isread'=>0))->field('c_id,c_content,c_addtime')->find();
        $order_new =M('Users_msg')->order('c_addtime desc')->where(array('c_ucode'=>$ucode,'c_type'=>$type))->field('c_id,c_content,c_addtime')->find();
        if(!empty($orderInfo)){
            $data1['c_content'] =$orderInfo['c_content'];
            $data1['c_time'] =$orderInfo['c_addtime']==null?null:$this->exchangeTime($orderInfo['c_addtime']);
        }else{
            $data1['c_content'] =$order_new['c_content'];
            $data1['c_time'] =$order_new['c_addtime']==null?null:$this->exchangeTime($order_new['c_addtime']);
        }
        $data1['count'] =($order_msg <= 0) ? '0' : $order_msg;
        // $data1['msg_total'] =M('Users_msg')->where($con)->count();




        return $data1;
    }

    ////判断当前日期是否是今天 和本周 并返回日期格式

   public function exchangeTime($date,$flag=null){
        $today =date('Y-m-d');
        $newdate =substr($date,0,10);
        $year = substr($date, 2, 2);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        $ttime = substr($date, 11, 5);


        $d1 = strtotime($newdate);
        $d2 = strtotime($today);
        $Days = round(($d2-$d1)/3600/24);
       if(empty($flag) || $flag==null){
           if($Days==0){
               return $ttime;
           }elseif($Days>0 && $Days<7){
               return $this->get_week($newdate);
           }else{
               return $year .'/'.$month .'/'.$day;
           }
       }elseif($flag || $flag==1){

           if($Days==0){
               return '今日 '.$ttime;
           }elseif($Days>0 && $Days<7){
               return $this->get_week($newdate).$ttime;
           }else{
               return $year .'年'.$month .'月'.$day.'日'.$ttime;
           }
       }

    }


        public function   get_week($date){
        //强制转换日期格式
        $date_str=date('Y-m-d',strtotime($date));

        //封装成数组
        $arr=explode("-", $date_str);

        //参数赋值
        //年
        $year=$arr[0];

        //月，输出2位整型，不够2位右对齐
        $month=sprintf('%02d',$arr[1]);

        //日，输出2位整型，不够2位右对齐
        $day=sprintf('%02d',$arr[2]);

        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;

        //转换成时间戳
        $strap = mktime($hour,$minute,$second,$month,$day,$year);

        //获取数字型星期几
        $number_wk=date("w",$strap);

        //自定义星期数组
        $weekArr=array("周日","周一","周二","周三","周四","周五","周六");

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }


    //分类读取消息数量

    public function ReadEachMsg($ucode,$type){

        //将对应的数据改为 1  已读

        // $con['c_isread'] =1;
        // if($type==3){
        //     $where['c_ucode'] ='';
        // }else{
            $where['c_type'] = $type;
            $where['c_ucode'] = $ucode;
            $save['c_num'] = 0;
            $save['c_updatetime'] = date('Y-m-d H:i:s');
            M('Users_msgnum')->where($where)->save($save);
        // }
        // M('Users_msg')->where($where)->save($con);

        return Message(0, '读取成功');

    }


	// 读取未读消息
	public function ReadMsg($parr) {
		$ucode = $parr['ucode'];
		$type = $parr['type'];
        if (empty($ucode)) {
            return Message(0, '读取成功');
        }

		// $sql = "select c_txcode from t_users_msg where c_txcode not in ";
		// $sql .= "(select c_txcode from t_users_msglog where c_ucode='$ucode') ";
		// $sql .= "and (c_ucode='' or c_ucode is null or c_ucode='$ucode')";
		if (!empty($type)) {
			// $sql .= " and c_type=$type";
            $where['c_type'] = $type;
		}

        $where['c_ucode'] = $ucode;
        $save['c_num'] = 0;
		$save['c_updatetime'] = date('Y-m-d H:i:s');
        $result = M('Users_msgnum')->where($where)->save($save);
		// $data = M('')->query($sql);
		// foreach ($data as $key => $value) {
		// 	$add['c_ucode'] = $ucode;
		// 	$add['c_txcode'] = $value['c_txcode'];
		// 	$add['c_addtime'] = date('Y-m-d H:i:s');
		// 	$result = M('users_msglog')->add($add);
		// 	if (!$result) {
		// 		return Message(1000, '读取失败');
		// 	}
		// }
		return Message(0, '读取成功');
	}

    /**
     *   新增消息数量
     *   @param ucode,title,tag,tagvalue,sendnum,platform,content,type,weburl
     *   tag(1url 2url加密 3订单详情 4商品详情 5个人空间 6个人资料 7商家商品列表)
     *
     */
    function IncMsgNum($parr)
    {
        if (empty($parr['ucode'])) {
            $where['c_type'] = 0;
            $result = M('Users_msgnum')->where($where)->setInc('c_num',1);
        } else {
            //查询消息数量记录
            $where['c_ucode'] = $parr['ucode'];
            $where['c_type'] = $parr['type'];
            $msginfo = M('Users_msgnum')->where($where)->find();
            $msgdata['c_ucode'] = $parr['ucode'];
            $msgdata['c_type'] = $parr['type'];
            $msgdata['c_updatetime'] = date('Y-m-d H:i:s');
            if ($msginfo) {
                $msgdata['c_num'] = $msginfo['c_num'] + 1;
                $result = M('Users_msgnum')->where($where)->save($msgdata);
            } else {
                $msgdata['c_num'] = 1;
                $result = M('Users_msgnum')->add($msgdata);
            }
        }

        if (!$result) {
            return Message(2000,'消息数量增加失败');
        }
        return Message(0,'消息数量增加成功');
    }

	//消息列表 $type 0为系统消息 1为订单消息,platform 1安卓，2IOS
	public function Getsysmsg($parr){

		$ucode = $parr['ucode'];
		$type = $parr['type'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if($type == 0 && isset($type)){
        	$whereinfo['c_type'] = 0;
        } else if($type == 1){
        	$whereinfo['c_type'] = 1;
        }

        if ($parr['platform'] == 1) {
            $whereinfo[] = array("c_platform='1' or c_platform='3'");
        } else  if ($parr['platform'] == 2) {
            $whereinfo[] = array("c_platform='1' or c_platform='2'");
        } else {
            $whereinfo[] = array("c_platform='1'");
        }

        $whereinfo[] = array("c_ucode='' or c_ucode is null or c_ucode='$ucode'");
		$order = 'c_id desc';
		$list = M('users_msg')->where($whereinfo)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('users_msg')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);

        if(!$list){
            $list = array();
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}


    //消息设置已读未读
    public function ChangeMessStatus($parr){
        $ucode =$parr['ucode'];
        $type =$parr['type'];

//        if($type!==0 && $type!==1 && $type!==2 && $type!==3){
//            return Message(1001,"缺少参数");
//        }

        switch($type){
            case 0://订单消息
                $result =$this->ReadEachMsg($ucode,1);
                break;
            case 1://活动消息
                $result =$this->ReadEachMsg($ucode,2);
                break;
            case 2://公告消息
                $result =$this->ReadEachMsg($ucode,3);
                break;
            case 3://系统消息
                $result =$this->ReadEachMsg($ucode,0);
                break;
        }

        return $result;

    }


    // 订单消息列表  platform 1安卓，2IOS  add by james   0 订单 1 活动  2 系统
    public function GetOrderMessages($parr){
        $data = $this->GetMsg($parr,1);
        return MessageInfo(0, '查询成功', $data);
    }
     // 系统消息列表  platform 1安卓，2IOS    add  by james
    public function GetSysMessages($parr){
        $data = $this->GetMsg($parr,0);
        return MessageInfo(0, '查询成功', $data);
    }

    // 系统消息列表  platform 1安卓，2IOS    add  by james
    public function GetActiveMessages($parr){
        $data = $this->GetMsg($parr,2);
        return MessageInfo(0, '查询成功', $data);
    }

    // 公告消息列表  platform 1安卓，2IOS    add  by james
    public function GetGongMessages($parr){
        $data = $this->GetMsg($parr,3);
        return MessageInfo(0, '查询成功', $data);
    }


    public function GetMsg($parr,$type){

        $ucode = $parr['ucode'];
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        if ($parr['platform'] == 1) {
            $whereinfo[] = array("c_platform='1' or c_platform='3'");
        } else  if ($parr['platform'] == 2) {
            $whereinfo[] = array("c_platform='1' or c_platform='2'");
        } else {
            $whereinfo[] = array("c_platform='1'");
        }

        if($type ==1){
            $this->ReadEachMsg($ucode,1);
            $whereinfo[] = array('c_ucode'=>$ucode,'c_type'=>1);
        }elseif($type==0){
            $this->ReadEachMsg($ucode,0);
            $whereinfo[] = array('c_ucode'=>$ucode,'c_type'=>0);
        }elseif($type==2){
            $this->ReadEachMsg($ucode,2);
            $whereinfo[] = array('c_ucode'=>$ucode,'c_type'=>2);
        }elseif($type==3){
            $con['c_isread'] =1;
            $where[] = array("c_ucode is null");
            M('Users_msg')->where($where)->save($con);
            $whereinfo[] = array("c_ucode is null");
        }
//        $whereinfo[] = array("c_ucode='' or c_ucode is null or c_ucode='$ucode'");
        $order = 'c_id desc';
        $list = M('users_msg')->where($whereinfo)->order($order)->limit($countPage, $pageSize)->select();
        $count = M('users_msg')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);

        foreach($list as $key=>$value){
            $list[$key]['c_addtime'] =$value['c_addtime']==null?null:$this->exchangeTime($value['c_addtime'],1);
        }

        if(!$list){
            $list = array();
        }
        $data = Page($pageIndex, $pageCount, $count, $list);

        return $data;
    }

	//微聊 好友列表分页(用于网页)
	public function Getfriends($parr){

		$ucode = $parr['ucode'];

		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

		$sql = "SELECT u.c_ucode,u.c_nickname,u.c_headimg,u.c_signature,u.c_shop FROM t_friend_relation AS r ";
		$sql .= "LEFT JOIN t_users AS u ON r.c_friend_ucode=u.c_ucode ";
		$sql .= "WHERE r.c_user_ucode='$ucode' AND r.c_status=1 ";
		$sql .= "order by u.c_nickname desc ";
		$sql .= "limit $countPage,$pageSize ";

		$model = M('');
		$list = $model->query($sql);

		$count = M('users_msg')->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);

        if(!$list){
            $list = array();
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	* 获取好友列表 不分页(用于APP)
	* @param string $currentUserId 本人用户编码
	*/
	function get_friend($currentUserId){
		$sql = "SELECT u.c_ucode,u.c_nickname,u.c_headimg,u.c_signature,u.c_shop FROM t_friend_relation AS r ";
		$sql .= "LEFT JOIN t_users AS u ON r.c_friend_ucode=u.c_ucode ";
		$sql .= "WHERE r.c_user_ucode='$currentUserId' AND r.c_status=1 ";
		$sql .= "order by u.c_nickname desc ";

		$model = M('');
		$data = $model->query($sql);

        if(!$data){
            $data = array();
        }

		return MessageInfo(0, '查询成功', $data);
	}

	/**
     *   创建消息并发送
     *   @param ucode,title,tag,tagvalue,sendnum,platform,content,type,weburl
     *   tag(1url 2url加密 3订单详情 4商品详情 5个人空间 6个人资料 7商家商品列表)
     *
     */
    public function CreateMessege($parr)
    {
        $data['c_txcode'] = CreateUcode('msg');
        $data['c_ucode'] = $parr['ucode'];
        $data['c_title'] = $parr['title'];
        $data['c_tag'] = $parr['tag'];
        $data['c_tagvalue'] = $parr['tagvalue'];
        $data['c_sendnum'] = $parr['sendnum'];
        $data['c_platform'] = $parr['platform'];
        $data['c_content'] = $parr['content'];
        $data['c_weburl'] = $parr['weburl'];
        $data['c_type'] = $parr['type'];
        $data['c_state'] = 0;
        $data['c_addtime'] = date('Y-m-d H:i:s');
        //是否是语音消息
        $data['c_issound'] = $parr['issound'];

        $result = M('Users_msg')->add($data);
        if (!$result) {
            return Message(1002,'消息创建失败');
        }

        $data['c_tagvalue'] = $result;
        $result = $this->IncMsgNum($parr);
        if (!$result) {
            return $result;
        }
        
        if (!empty($parr['ucode'])) {
            $result = IGD('JPush','Jpush')->push_notification($data);
        }
        return Message(0,'消息创建成功');
    }

    /**
     *   创建消息内容不发送
     *   @param ucode,title,tag,tagvalue,sendnum,platform,content,type,weburl
     *   tag(1url 2url加密 3订单详情 4商品详情 5个人空间 6个人资料 7商家商品列表)
     *
     */
    public function CreateMessegeInfo($parr)
    {
        $data['c_txcode'] = CreateUcode('msg');
        $data['c_ucode'] = $parr['ucode'];
        $data['c_title'] = $parr['title'];
        $data['c_tag'] = $parr['tag'];
        $data['c_tagvalue'] = $parr['tagvalue'];
        $data['c_sendnum'] = $parr['sendnum'];
        $data['c_platform'] = $parr['platform'];
        $data['c_content'] = $parr['content'];
        $data['c_weburl'] = $parr['weburl'];
        $data['c_type'] = $parr['type'];
        $data['c_state'] = 0;
        if(!empty($parr['timer'])){
        	$data['c_timer'] = $parr['timer'];
        	$data['c_istimer'] = $parr['istimer'];
        }
        //是否是语音消息
        $data['c_issound'] = $parr['issound'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Users_msg')->add($data);
        if (!$result) {
            return Message(1002,'消息创建失败');
        }

        $result = $this->IncMsgNum($parr);
        if (!$result) {
            return $result;
        }

        return Message(0,'消息创建成功');
    }

    //获取用户是否开启到帐推送提醒功能
    function GetPushstate($parr){
        $w['c_ucode'] = $parr['ucode'];

        $ispush = M('User_part')->where($w)->getField('c_ispush');
       
        $data['ispush'] = $ispush;
        return MessageInfo(0,"查询成功",$data);
    }
    //操作用户是否开启到帐推送提醒功能
    function OperateState($parr){
        $user_action = I('user_action');

        $w['c_ucode'] = $parr['ucode'];
        $save_date['c_ispush'] = $user_action;

        $result = M('User_part')->where($w)->save($save_date);

        if($result < 0){
            return Message(1001,"操作失败！");
        }

        return Message(0,"操作成功！");
    }
}