<?php
/**
 * 加盟店模块
 */
class LeagueStore{
	/** ***************************我的加盟店******************************* */
	/** * 检查用户联盟店身份，获取联盟店id
	 * @param ucode,federationid
	 */
	public function GetUserinfo($parr){
		if ($parr['ucode']) {
			$w['c_ucode'] = $parr['ucode'];
		} else {
			$w['c_id'] = $parr['federationid'];
		}
		
		$w['c_type'] = 2;
		$w['c_sign'] = 2;	
		$w['c_status'] = 1;

		$league_info = M('A_federation')->where($w)->find();

		if(!$league_info){
			return Message(1001,"没有查询到相关加盟店信息！"); 
		}

		return MessageInfo(0,"查询成功",$league_info); 
	}

	/** * 我的加盟店收入统计、会员统计
	 * @param ucode,federationid
	 */
	public function GetMydata($parr){		
		$ucode = $parr['ucode'];		
		$federationid = $parr['federationid'];//加盟店id

		$w[] = "c_federationid = '$federationid'";
		$w[] = "c_ucode = '$ucode'";		

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}

		$db = M();

		$sql = "select sum(case when c_type=1 then c_money else 0 end) as a,
        sum(case when c_type=2 then c_money else 0 end) as b from t_a_federation_moneylog $w1";

        $list = $db->query($sql);

        $omoney = $list[0]['a'];//全部订单收益
        $kmoney = $list[0]['b'];//全部跨界收益

        $data['omoney'] = ($omoney>0)?$omoney:'0.00';
        $data['kmoney'] = ($kmoney>0)?$omoney:'0.00';

        //本店会员
        $data['mymember'] = $this->GetMymember($ucode,$federationid);

        return MessageInfo(0,"查询成功",$data); 	
	}	

	/** * 统计加盟店锁定会员
	 * @param ucode,federationid 
	 */
	public function GetMymember($ucode,$federationid){
		$w['c_pcode'] = $ucode;
		$w['c_federationid'] = $federationid;
		
		$date = M('Users_tuijian')->where($w)->count();
       
        if (!$date){
            $date = 0;
        }
        return $date;		
	}

	/**
     * 根据时间查询加盟店营业额
     * @param ucode,federationid,timetype(1-过去7天,2-过去30天),pid
     */
    public function GetdataTally($parr)
    {	
        $ucode = $parr['ucode'];
        $pid = $parr['pid'];
        $federationid = $parr['federationid'];//加盟店id		

		// $w[] = "c_ucode = '$ucode'";	
		if ($pid) {
			$w[] = "c_pid = '$pid'";
		} else {	
			$w[] = "c_federationid = '$federationid'";
		}		

		//分时间
		$timetype = $parr['timetype'];

		if($timetype == 1){
			$begintime = date("Y-m-d",strtotime("-7 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}else{
			$begintime = date("Y-m-d",strtotime("-30 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}

		$w[] = "c_datetime between '".$begintime."' and '".$endtime."'";

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}        

        $db = M();
        $sql = "select ifnull(c_money, 0) as c_money,c_datetime from t_a_federation_moneylog $w1 GROUP BY c_datetime order by c_datetime asc";

        $list = $db->query($sql);
        
        $money = 0.00;
        foreach ($list as $key => $value) {          
            $list[$key]['time'] = date("m/d", strtotime($value['c_datetime']));

            $money = bcadd($money,$list[$key]['c_money'],2);           
        }
        $data['money'] = $money;
        $data['list'] = $list;

        return MessageInfo(0, "查询成功", $data);
    }

    /**
	 * 根据月查询加盟店扫码收入明细
	 * @param federationid,time 按月时间格式2017-06-03
	 */
	 public function GetdateLog($parr)
    {	
    	if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

		$federationid = $parr['federationid'];

		$where['c_federationid'] = $federationid;

		//分时间
		if (!empty($parr['time'])) {
			$begintime = $parr['time'].' 00:00:00';
			$endtime = $parr['time'].' 23:59:59';
			
	        $where[] = array("c_addtime>='$begintime' and c_addtime<='$endtime'");        
		}
			

		$where['c_money'] = array('GT',0);
    	$where[] = array('c_source=5 or c_source=12 or c_source=15 or c_source=1 or c_source=4 or c_source=9');

    	$list = M('Users_moneylog')->where($where)->limit($countPage, $pageSize)->order('c_addtime desc')->select();

    	$count = M('Users_moneylog')->where($where)->count();
    	$pageCount = ceil($count / $pageSize);
    	if(!$list){
    		$list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
    	}
       
		foreach ($list as $key => $value) {
			$list[$key]['time'] = date("m-d H:i:s", strtotime($value['c_addtime']));
            $list[$key]['c_showimg'] = GetHost() . '/' . $value['c_showimg'];
            //查询扫码订单交易方式
            $plogwhere['c_orderid'] = $value['c_key'];
            $paylog = M('Order_paylog')->where($plogwhere)->order('c_id desc')->find();
            if ($paylog['c_payrule'] == 1) {
                $list[$key]['text'] = '支付宝支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/alpay.png';
            } else if ($paylog['c_payrule'] == 2 || $paylog['c_payrule'] == 3) {
                $list[$key]['text'] = '微信支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/wxpay.png';
            } else {
                $list[$key]['text'] = '小蜜支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/xmpay.png';
            }

            //操作员
            $cashierw['c_id'] = $value['c_cashierid'];
            $list[$key]['cashier_name'] = M('A_cashier')->where($cashierw)->getfield('c_name');
		}

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

     /**
	 * 我的加盟店关于详情
	 * @param ucode,federationid
	 */
	public function GetLeagueInfo($parr){
		$ucode = $parr['ucode'];
		$federationid = $parr['federationid'];

		$w['c_ucode'] = $ucode;
		$w['c_id'] = $federationid;

		$league_info = M('A_federation')->where($w)->find();

		//查询加盟总店信息
		$pw['a.c_id'] = $league_info['c_pid'];

		$field = "u.c_nickname,u.c_headimg,u.c_phone,l.c_address";
		$join = "left join t_users as u on a.c_ucode=u.c_ucode";
		$join1 = " left join t_user_local as l on a.c_ucode=l.c_ucode";

		$infos = M('A_federation as a')->field($field)->where($pw)->join($join)->join($join1)->find();

		$data['headimg'] = GetHost().'/'.$infos['c_headimg'];
		$data['pname'] = $infos['c_nickname'];
		$data['phone'] = $infos['c_phone'];
		$data['addtime'] = $league_info['c_addtime'];
		$data['address'] = $infos['c_address'];

		return MessageInfo(0,"查询成功",$data);
	}

	/** ***************************加盟总店******************************* */
	/** * 检查用户联盟总店身份，获取联盟总店id
	 * @param ucode
	 */
	public function GetLeaderinfo($parr){
		$w['c_ucode'] = $parr['ucode'];
		$w['c_type'] = 2;
		$w['c_sign'] = 1;	
		$w['c_status'] = 1;

		$leader_info = M('A_federation')->where($w)->find();

		if(!$leader_info){
			return Message(1001,"没有查询到相关加盟店信息！"); 
		}

		return MessageInfo(0,"查询成功",$leader_info); 
	}

	/** * 全部加盟店收入统计、会员统计
	 * @param ucode,pid(总店id)
	 */	
	public function GetAlltj($parr){		
		$ucode = $parr['ucode'];		
		$pid = $parr['pid'];//加盟店id

		$w[] = "c_pid = '$pid'";

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}

		$db = M();

		$sql = "select sum(case when c_type=1 then c_money else 0 end) as a,
        sum(case when c_type=2 then c_money else 0 end) as b from t_a_federation_moneylog $w1";

        $list = $db->query($sql);

        $omoney = $list[0]['a'];//全部订单收益
        $kmoney = $list[0]['b'];//全部跨界收益
        $allmoney = sprintf('%.2f',$omoney + $kmoney);//全部营业额

        $data['allmoney'] = $allmoney;

        //本店会员
        $tw['c_pfederationid'] = $pid;
        $data['mymember'] = M('Users_tuijian')->where($tw)->count();

        return MessageInfo(0,"查询成功",$data); 	
	}

	/** * 统计加盟店总共锁定会员
	 * @param federationid 
	 */
	public function GetAllmember($federationid){
		$where['c_pid'] = $federationid;
		$where['c_type'] = 2;
		$where['c_sign'] = 2;
		$where['c_status'] = 1;

		$member = M('A_federation')->field('c_federationid')->where($where)->find();

		$count = 0;

		foreach ($member as $key => $value) {
			$w['c_federationid'] = $value['c_id'];

			$num = M('Users_tuijian')->where($w)->count();

			$count = $count + $num;
		}
		
        return $count;		
	}

	/**
     * 根据时间查询加盟店营业额
     * @param pid,timetype(1-过去7天,2-过去30天)
     */
    public function Getalldatatj($parr)
    {	
        $pid = $parr['pid'];//加盟总店id		

		$w[] = "c_pid = '$pid' ";		

		//分时间
		$timetype = $parr['timetype'];

		if($timetype == 1){
			$begintime = date("Y-m-d",strtotime("-7 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}else{
			$begintime = date("Y-m-d",strtotime("-30 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}

		$w[] = "c_datetime between '".$begintime."' and '".$endtime."' ";

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}        

        $db = M();
        $sql = "select ifnull(c_money, 0) as c_money,c_datetime from t_a_federation_moneylog $w1 GROUP BY c_datetime order by c_datetime asc";
        $list = $db->query($sql);
        
        $money = 0.00;
        foreach ($list as $key => $value) {          
            $list[$key]['time'] = date("m/d", strtotime($value['c_datetime']));

            $money = sprintf('%.2f',$money + $value['c_money']);           
        }
        $data['money'] = $money;
        $data['list'] = $list;

        return MessageInfo(0, "查询成功", $list);
    }

    /**
     * 获取加盟店所有成员
     * @param pid
     */
    public function GetallNum($parr)
    {	
        $pid = $parr['pid'];//加盟总店id		

		$where['c_pid'] = $pid;
		$where['c_type'] = 2;
		$where['c_status'] = 1;
		$where['c_sign'] = 2;

		$list = M('A_federation')->where($where)->select();

		foreach ($list as $key => $value) {
			$list[$key]['ucode'] = $value['c_ucode'];
			$list[$key]['federationid'] = $value['c_id'];
			$list[$key]['shopcode'] = $value['c_shopcode'];
			//加盟店信息
			$uw['c_ucode'] = $value['c_ucode'];

			$userinfo = M('Users')->field('c_nickname,c_headimg,c_phone')->where($uw)->find();

			$list[$key]['nickname'] = $userinfo['c_nickname'];
			$list[$key]['headimg'] = GetHost().'/'.$userinfo['c_headimg'];
			$list[$key]['phone'] = $userinfo['c_phone'];

			//统计信息
			$param['ucode'] = $value['c_ucode'];
			$param['federationid'] = $value['c_id'];

			$tj = $this->GetMydata($param);

			$list[$key]['money'] = sprintf('%.2f',$tj['data']['omoney'] + $tj['data']['kmoney']);
			$list[$key]['member_num'] = $tj['data']['mymember'];
		}	

        return MessageInfo(0, "查询成功", $list);
    }

     /**
     * 根据手机号查询商家信息
     * @param phone
     */		
     public function Queryshopinfo($parr){
     	$w['c_phone'] = $phone;
     	$w['c_shop'] = 1;
     	
     	$info = M('Users')->field('c_ucode,c_nickname,c_headimg,c_phone,c_signature')->where($w)->find();

     	if(!$info){
     		return Message(1001,"小蜜未找到此手机号用户，请确认输入正确的手机号");
     	}

     	$info['c_headimg'] = GetHost().'/'.$info['c_headimg'];

     	return MessageInfo(0,"查询成功",$info);
     }

	/**
	 * 同意加盟
	 * @param acode,pid,name,shopcode,ucode
	 */
	public function AgreeInvita($parr)
	{
		//查询商家是否存在
        $auw['c_ucode'] = $parr['acode'];
        $auw['c_id'] = $parr['pid'];
        $auw['c_type'] = 2;
        $auw['c_sign'] = 1;
        $auw['c_status'] = 1;
        $acodeinfo = M('A_federation')->where($auw)->find();
        if (!$acodeinfo) {
            return Message(3000,'该邀请无效');
        }

        if ($acodeinfo['c_remain_num'] <= 0) {
			return Message(3001,"可添加数量为0，请联系客服");
		}
        
        // 查询用户
        $uw['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($uw)->field('c_nickname')->find();
        if (!$userinfo) {
        	return Message(1009,'登录状态失效');
        }

        $where['c_ucode'] = $parr['ucode'];
        $casherinfo = M('A_federation')->where($where)->find();
        if ($casherinfo) {
            return Message(3000,'已经是联盟成员不能再同意');
        }

        $db = M('');
        $db->startTrans();

        //写入加盟数据
        $add['c_ucode'] = $parr['ucode'];
		$add['c_pid'] = $parr['pid'];
		$add['c_name'] = $parr['name'];
		$add['c_shopcode'] = $parr['shopcode'];
		$add['c_type'] = 2;
		$add['c_status'] = 1;
		$add['c_sign'] = 2;
        $add['c_addtime'] = gdtime();
        $result = M('A_federation')->add($add);
        if (!$result) {
        	$db->rollback();
            return Message(3001,'联盟记录失败');
        }
		$unionid = $result;        

        //同步联盟临时会员
        $tjsave['c_federationid'] = $unionid;
        $tjsave['c_pfederationid'] = $parr['pid'];
        $tjwhere['c_pcode'] = $parr['ucode'];

        if (M('Scanpay_tuijian')->where($tjwhere)->getField('c_id')) {
        	$result = M('Scanpay_tuijian')->where($tjwhere)->save($tjsave);
	        if (!$result) {
	        	$db->rollback();
	            return Message(3002,'同步联盟会员失败');
	        }
        }
        
        if (M('Users_tuijian')->where($tjwhere)->getField('c_id')) {
	        //同步联盟小蜜会员
	        $result = M('Users_tuijian')->where($tjwhere)->save($tjsave);
	        if (!$result) {
	        	$db->rollback();
	            return Message(3002,'同步联盟会员失败');
	        }
	    }

	    //修改可邀请连锁店数量
        $auw['c_remain_num'] = array('GT',0);
		$result = M('A_federation')->where($auw)->setDec('c_remain_num',1);
		if(!$result){
			$db->rollback();
			return Message(3003,"修改邀请数量失败"); 
		}

        //操作成功给商家发送消息
        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['acode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = '【'.$userinfo['c_nickname'].'】已接受您的邀请，成为联盟商家。';
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Store/Leagshop/index';
        $msgdata['weburl'] = GetHost(1) . '/index.php/Store/Leagshop/index';
        $msgresult = $Msgcentre->CreateMessegeInfo($msgdata);

        $db->commit();
        return Message(0,'操作成功');
	}


	/** *********************商家管理端 加盟店模块************************* */

	//加盟店列表    ucode
	function LeagueList($parr){
		//判断用户身份信息，并获取总店信息
		$w['c_ucode'] = $parr['ucode'];
		$w['c_type'] = 2;
		$w['c_sign'] = 1;	
		$w['c_status'] = 1;

		$league_info = M('A_federation')->where($w)->find();

		if(!$league_info){
			return Message(1001,"没有查询到相关加盟店信息！"); 
		}

		//查询子加盟店列表
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}
		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$subw['c_pid'] = $league_info['c_id'];
		$subw['c_type'] = 2;
		$subw['c_sign'] = 2;

		$list = M('A_federation')->where($subw)->limit($countPage, $pageSize)->order("c_id desc")->select();
		$count = M('A_federation')->where($subw)->count();

		if(!$list){
    		$list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
    	}

		foreach ($list as $key => $value) {
			//用户昵称
			$uw['c_ucode'] = $value['c_ucode'];
			$userinfo = M('Users')->field('c_nickname,c_phone')->where($uw)->find();

			$list[$key]['c_nickname'] = $userinfo['c_nickname'];
			$list[$key]['c_phone'] = $userinfo['c_phone'];

			//位置信息
			$lw['c_ucode'] = $value['c_ucode'];
			$localtion = M('User_local')->field('c_address')->where($lw)->find();

			$list[$key]['c_address'] = $localtion['c_address'];
		}

		$pageCount = ceil($count / $pageSize);
		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	//加盟店详细信息    fid（子店id）
	function LeagueInfo($parr){

		//加盟信息
		$fw['c_id'] = $parr['fid'];
		$fw['c_type'] = 2;
		$fw['c_sign'] = 2;
		$finfo = M('A_federation')->where($fw)->find();

		//商家信息
		$w['c_ucode'] = $finfo['c_ucode'];
		$w['c_checked'] = 3;

		$info = M('Check_shopinfo')->field('c_merchantname,c_merchantshortname,c_type,c_name,c_phone,c_email,c_qq,c_legalperson,c_address,c_fee_weixin,c_legalperson')->where($w)->find();

		//用户信息
		$uw['c_ucode'] = $finfo['c_ucode'];
		$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
		$info['c_nickname'] = $userinfo['c_nickname'];
		$info['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];		


		$info['c_shopcode'] = $finfo['c_shopcode'];
		$info['c_addtime'] = date('Y-m-d',strtotime($finfo['c_addtime']));

		//行业信息
		$iw['c_ucode'] = $finfo['c_ucode'];
		$industry = M('Shop_industry')->where($iw)->getField('c_name');

		$info['industry'] = $industry;

		return MessageInfo(0,"查询成功",$info);
	}

	/**
	 * 停用与启用加盟店
	 * @param fid,status(1启用,2停用)
	 */
	function OptionLeague($parr)
	{
		$fw['c_id'] = $parr['fid'];
		$fw['c_type'] = 2;
		$fw['c_sign'] = 2;
		$save['c_status'] = $parr['status'];
		$finfo = M('A_federation')->where($fw)->save($save);
		if (!$finfo) {
			return Message(3000,'操作失败');
		}

		return Message(0,'操作成功');
	}

	//加盟店确认邀请 ucode,pucode(被邀请人用户编码),shopcode
	function Confirmsubmit($parr){
		//查询商家信息
		$awh['c_ucode'] = $parr['ucode'];
		$acodeinfo = M('Users')->where($awh)->field('c_ucode,c_nickname')->find();
		if (!$acodeinfo) {
			return Message(3000,'信息有误');
		}

		//查询加盟总店信息
		$result = $this->GetLeaderinfo($parr);
		if ($result['code'] != 0) {
			return Message(3001,'邀请失败');
		}
        $unioninfo = $result['data'];
        if ($unioninfo['c_remain_num'] <= 0) {
			return Message(3001,"可邀请数量为0，请联系客服");
		}

        $result = $this->Checkname($parr['shopcode'],$unioninfo['c_id']);
		if ($result['code'] != 0) {
			return $result;
		}

		//查询被邀请人信息以及是否存在
		$uwh['c_ucode'] = $parr['pucode'];
		$userinfo = M('Users')->where($uwh)->field('c_ucode,c_nickname')->find();
		if (!$userinfo) {
			return Message(3001,'被邀请人不存在');
		}

		$rw['c_ucode'] = $parr['pucode'];		
		$roles = M('A_federation')->where($rw)->find();
		if($roles){
			return Message(3001,'无法邀请，被邀请人已经是加盟身份');
		}

		$dataarr['acode'] = $unioninfo['c_ucode'];
		$dataarr['pid'] = $unioninfo['c_id'];
		$dataarr['name'] = $unioninfo['c_name'];
		$dataarr['shopcode'] = $parr['shopcode'];
		$datajson = json_encode($dataarr);
		$desc = '商家：'.$acodeinfo['c_nickname'].'，想邀请您成为加盟店关系';

		//写入邀请信息
		$add['c_acode'] = $acodeinfo['c_ucode'];
		$add['c_ucode'] = $userinfo['c_ucode'];
		$add['c_type'] = 2;
		$add['c_data'] = $datajson;
		$add['c_desc'] = $desc;
		$add['c_addtime'] = gdtime();
		$result = M('A_askinfo')->add($add);
		if (!$result) {
			return Message(3000,'邀请失败');
		}
		$askid = $result;

		//给用户发信息
		$Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $userinfo['c_ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = $desc;
        $msgdata['tag'] = 2;
        $msgdata['weburl'] = GetHost(1) . '/index.php/Store/Leagshop/jcheck?askid='.$askid;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Store/Leagshop/jcheck?askid='.$askid;
        IGD('Msgcentre','Message')->CreateMessege($msgdata);

		return Message(0,'邀请成功');
	}

	//检查编号
	function Checkname($shopcode,$pid)
	{
		$where['c_shopcode'] = $shopcode;
		$where['c_pid'] = $pid;
        $data = M('A_federation')->where($where)->find();
        if ($data) {
        	return Message(3000,'编号重复');
        }

        return Message(0,'编号可用');
	}

	//获取用户基本信息
	function UserInfo($phone)
	{
		$uw['c_phone'] = $phone;
		$data = M('Users')->where($uw)->field('c_ucode,c_nickname,c_phone,c_headimg,c_signature,c_shop')->find();
		if (!$data) {
			return Message(3000,'该手机号信息不存在');
		}
		if ($data['c_shop'] != 1) {
			return Message(3001,'邀请人的用户必须为商家');
		}

		$rw['c_ucode'] = $data['c_ucode'];		
		$roles = M('A_federation')->where($rw)->find();
		if($roles){
			return Message(3001,'该商家已经是加盟身份');
		}

		$data['c_headimg'] = GetHost().'/'.$data['c_headimg'];
		return MessageInfo(0,'查询成功',$data);
	}

}