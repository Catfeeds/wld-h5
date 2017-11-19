<?php
/**
 * 连锁店模块
 */
class ChainStore{
	/*     * *********************总店模块************************* */
	/** * 检查用户连锁店身份，获取连锁总店id
	 * @param ucode
	 */
	public function GetUserinfo($parr){
		$w['c_ucode'] = $parr['ucode'];
		$w['c_type'] = 1;
		$w['c_sign'] = 1;	
		$w['c_status'] = 1;

		$chain_info = M('A_federation')->where($w)->find();

		if(!$chain_info){
			return Message(1001,"没有查询到相关总店信息！"); 
		}

		return MessageInfo(0,"查询成功",$chain_info); 
	}

	public function GetUserinfoByfedera($parr){
		$w['a.c_id'] = $parr['federationid'];
		$w['a.c_type'] = 1;
		$w['a.c_status'] = 1;
		$join = 'left join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.*,b.c_nickname,b.c_headimg';
		$chain_info = M('A_federation as a')->join($join)->where($w)->field($field)->find();
		if(!$chain_info){
			return Message(1001,"没有查询到相关信息！"); 
		}

		$chain_info['c_headimg'] = GetHost().'/'.$chain_info['c_headimg'];
		return MessageInfo(0,"查询成功",$chain_info); 
	}

	/**
	 * 获取用户联盟信息及总店编码
	 * @param ucode
	 */
	function GetUnionInfo($parr)
	{
		$w['a.c_ucode'] = $parr['ucode'];
		$w['a.c_status'] = 1;

		$join = 'left join t_a_federation as b on a.c_pid=b.c_id';
		$field = 'a.*,b.c_ucode as shopcode,b.c_id as shopid';
		$chain_info = M('A_federation as a')->join($join)->where($w)->field($field)->find();

		if(!$chain_info){
			return Message(1001,"没有查询到相关信息！"); 
		}

		return MessageInfo(0,"查询成功",$chain_info); 
	}	

	/** * 今日、昨日收入统计
	 * @param pid,datetype(0-总收入，1-营收收入，2-跨界收入)
	 */
	public function GetToYes($parr){
		$pid = $parr['pid'];//总店id
		$datetype = $parr['datetype'];
		
		$param['pid'] = $pid;
		$param['datetype'] = $datetype;
		//今日收入
		$param['datetime'] = date("Y-m-d");
		$data['today'] = $this->GetDaysDate($param);

		//昨天收入
		$param['datetime'] = date("Y-m-d",strtotime("-1 day"));
		$data['yesterday'] = $this->GetDaysDate($param);

		return MessageInfo(0,"查询成功",$data);
	}

	/** * 按天统计收入数据
	 * @param pid,datetime 格式2017-03-01,datetype(0-总收入，1-营收收入，2-跨界收入)
	 */
	public function GetDaysDate($parr){
		$datetype = $parr['datetype'];
		$federationid = $parr['federationid'];

		if(empty($federationid)){
			$w['c_pid'] = $parr['pid'];
		}else{
			$w['c_federationid'] = $federationid;
		}
			
		if ($parr['datetime']) {
			$w['c_datetime'] = $parr['datetime'];
		}

		if($datetype == 1){
			//营收收入
			$w['c_type'] = 1;
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}else if($datetype == 2){
			//跨界收入
			$w['c_type'] = 2;
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}else{
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}
       
        if (!$date){
            $date = '0.00';
        }
        return $date;		
	}		

	/**
	 * 选择店铺 查询分店数据
	 * @param pid
	 */
	public function SelectSubbranch($parr){
		//查询分店数据
		$pid = $parr['pid'];
		// $sw['a.c_pid'] = $parr['pid'];
		// $sw['a.c_sign'] = 2;
		$sw[] = array("a.c_pid='$pid' or a.c_id='$pid'");
		$sw['c_status'] = 1;	
		$field = 'a.*,u.c_nickname,u.c_headimg,u.c_phone';
		$join = "inner join t_users as u on a.c_ucode=u.c_ucode";

		$subbranch = M('A_federation as a')->field($field)->join($join)->where($sw)->select();

		foreach ($subbranch as $key => $value) {
			$subbranch[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
		}

		return MessageInfo(0,"查询成功",$subbranch);
	}

	/**
     * 根据时间、店铺查询连锁店营收趋势
     * @param pid,federationid(查询总店则为空),timetype(1-过去7天,2-过去30天,3-按月份),time 按月时间格式2017-03
     */
    public function GetdataTally($parr)
    {	
		//分店铺
		$pid = $parr['pid'];
		$federationid = $parr['federationid'];

		if(empty($federationid)){
			$w[] = "c_pid = '$pid' ";
		}else{
			$w[] = "c_federationid = '$federationid' ";
		}

		//分时间
		$timetype = $parr['timetype'];

		if($timetype == 1){
			$begintime = date("Y-m-d",strtotime("-7 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
			$date = '最近七天';
		}elseif($timetype == 2){
			$begintime = date("Y-m-d",strtotime("-30 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
			$date = '最近30天';
		}else{
			if(!$parr['time']){
				return Message('1002',"参数信息有误！");
			}

			$begintime = $parr['time'].'-01';
			$endtime1 = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));

			$endtime = date('Y-m-d',strtotime('-1 day',strtotime($endtime1)));
			$date = date("Y年m月", strtotime($begintime)); 
		}

		$w[] = "c_datetime between '".$begintime."' and '".$endtime."' ";

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}        

        $db = M();
        $sql = "select ifnull(sum(c_money), 0) as c_money,c_datetime from t_a_federation_moneylog $w1 GROUP BY c_datetime order by c_datetime asc";
        $list = $db->query($sql);
        
        $money = '0.00';
        foreach ($list as $key => $value) {          
            $list[$key]['time'] = date("m/d", strtotime($value['c_datetime'])); 
            $money = bcadd($money,$list[$key]['c_money'],2);        
        }

        $data['date'] = $date;
        $data['money'] = $money;
        $data['list'] = $list;
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 根据月份查询连锁店各分店营收占比
     * @param ucode,pid,time 按月时间格式2017-03
     */
    public function Getdataproportion($parr)
    {	
        //查询所有分店信息
        $pid = $parr['pid'];
        $pw[] = array("c_pid='$pid' or c_id='$pid'");
        $pw['c_type'] = 1;
		// $pw['c_sign'] = 2;	
		$pw['c_status'] = 1;

		$subbranch = M('A_federation')->where($pw)->select();      

        if(!$subbranch){
        	$data = array();
        	return MessageInfo(0,"没有查询到分店信息！",$data);
        }

        $db = M();
        $money = '0.00';   
        
		//统计月份营收数据
		foreach ($subbranch as $key => $value) {
			$uw['c_ucode'] = $value['c_ucode'];

			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
			 
        	$subbranch[$key]['c_nickname'] = $userinfo['c_nickname'];			
 			$subbranch[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

			$w1['c_ucode'] =$value['c_ucode'];
			$w1['c_federationid'] = $value['c_id'];
			$w1['c_type'] = 1;

			$begintime = $parr['time'].'-01';
			$endtime1 = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));
			$endtime = date('Y-m-d',strtotime('-1 day',strtotime($endtime1)));
			$w1['c_datetime'] = array('between',array($begintime,$endtime));

			
			$zsummoney = M('A_federation_moneylog')->where($w1)->sum('c_money');
 			$subbranch[$key]['money'] = ($zsummoney>0)?$zsummoney:'0.00';

 			$money = bcadd($money,$subbranch[$key]['money'],2);
		}

		foreach ($subbranch as $key => $value) {
			$subbranch[$key]['proportion'] = sprintf("%.2f", ($value['money']/$money)*100).'%'; 
		}

		$data['date'] = date("Y年m月", strtotime($begintime));
		$data['money'] = $money;
        $data['list'] = $subbranch;
        return MessageInfo(0, "查询成功", $data);
    }

    /**
	 * 总收入统计
	 * @param pid,acttype(1-营收总收入，2-跨界总收入)
	 */
	public function GetdataCount($parr)
    {	
		$pid = $parr['pid'];

		$w[] = "c_pid = '$pid' ";
		if($parr['acttype'] == 1){
			$w[] = "c_type = 1 ";
		}else{
			$w[] = "c_type = 2 ";
		}

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}

		$db = M();

		$sql = "select ifnull(SUM(c_money), 0) as c_money from t_a_federation_moneylog $w1 ";
        $list = $db->query($sql);

        $data['money'] = $list[0]['c_money'];

        return MessageInfo(0,"查询成功",$data);        
    }

	/**
	 * 分页查询连锁店各分店跨界收入明细
	 * @param pageindex,pagesize,pid,federationid,datetime 按日时间格式2017-06-07
	 */
	 public function Getdatakj($parr)
    {	
    	if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $pid = $parr['pid'];

		//分时间
		if ($parr['datetime']) {
			$startime = $parr['datetime'].' 00:00:00';
	        $endtime = $parr['datetime'].' 23:59:59';
	        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");
		}

        //分店铺
        $federationid = $parr['federationid'];
        if(empty($federationid)){
        	$where['c_pfederationid'] = $pid;
        }else{
        	$where['c_federationid'] = $federationid;
        }		

		$where['c_money'] = array('GT',0);
    	$where[] = array('c_source=5 or c_source=12 or c_source=15');

    	$list = M('Users_moneylog')->field('c_id,c_ucode,c_money,c_key,c_addtime')->where($where)->order('c_addtime desc')->limit($countPage, $pageSize)->select();

    	$count = M('Users_moneylog')->where($where)->count();
    	$pageCount = ceil($count / $pageSize);

    	if(!$list){
    		$list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
    	}
       
		foreach ($list as $key => $value) {
			$uw['c_ucode'] = $value['c_ucode'];

			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
			 
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];			
 			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
 			$list[$key]['showtime'] = date('m月d日 H:i:s',strtotime($value['c_addtime']));
		}
		
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }
	
	/**
	 * 根据天、店铺查询连锁店营收收入明细
	 * @param pageindex,pagesize,pid,federationid,datetime 按日时间格式2017-06-07
	 */
	public function Getdatays($parr)
    {	
    	if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

		$pid = $parr['pid'];

		//分时间
		if ($parr['datetime']) {
			$startime = $parr['datetime'].' 00:00:00';
	        $endtime = $parr['datetime'].' 23:59:59';
	        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");
		}

        //分店铺
        $federationid = $parr['federationid'];
        if(empty($federationid)){
        	$where['c_pfederationid'] = $pid;
        }else{
        	$where['c_federationid'] = $federationid;
        }		

		$where['c_money'] = array('GT',0);
    	$where[] = array('c_source=1 or c_source=4 or c_source=9');

    	$list = M('Users_moneylog')->field('c_id,c_ucode,c_money,c_addtime,c_federationid,c_pfederationid')->where($where)->order('c_id desc')->limit($countPage, $pageSize)->select();

    	$count = M('Users_moneylog')->where($where)->count();
    	$pageCount = ceil($count / $pageSize);

    	if(!$list){
    		$list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
    	}
       
		foreach ($list as $key => $value) {
			$uw['c_ucode'] = M('A_federation')->where(array('c_id'=>$value['c_federationid']))->getField('c_ucode');

			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
			 
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];			
 			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
 			$list[$key]['showtime'] = date('m月d日 H:i:s',strtotime($value['c_addtime']));
		}

		$data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }
	
	/**
	 * 连锁店各分店会员列表
	 * @param ucode
	 */
	public function Getdatamember($parr){
        //查询所有分店信息
        $pw['c_pid'] = $parr['pid'];
        $pw['c_type'] = 1;
		$pw['c_sign'] = 2;	
		$pw['c_status'] = 1;

		$subbranch = M('A_federation')->where($pw)->select();      

        if(!$subbranch){
        	$data = array();
        	return MessageInfo(0,"没有查询到分店信息！",$data);
        }

        $db = M();
        $num = 0;   
        
		foreach ($subbranch as $key => $value) {
			$w['c_pcode'] = $value['c_ucode'];
			$w['c_federationid'] = $value['c_id'];

			//会员数
			$count1 = M('Users_tuijian')->where($w)->count();

			if(!$count1){			 
        		$count1 = 0;
			}

			//临时会员数
			$count2 = M('Scanpay_tuijian')->where($w)->count();

			if(!$count2){			 
        		$count2 = 0;
			}

			$member = $count1 + $count2;
			$subbranch[$key]['member'] = $member;

			//查询用户信息
			$uw['c_ucode'] = $value['c_ucode'];
			$userinfo = M('Users')->where($uw)->field('c_ucode,c_nickname,c_headimg')->find();
			$subbranch[$key]['c_nickname'] = $userinfo['c_nickname'];
			$subbranch[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

			$num = $num + $member;
		}		

		$data['num'] = $num;
        $data['list'] = $subbranch;
        return MessageInfo(0, "查询成功", $data);
	}
	
	/**
	 * 分店会员详情信息
	 * @param ucode(分店店家ucode),federationid,pageindex,pagesize
	 */
	public function GetMembleList($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}
		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$join = 'inner join t_users as b on a.c_ucode=b.c_ucode';
		$field = 'a.c_ucode,b.c_phone,b.c_headimg,a.c_addtime,b.c_shop,b.c_nickname,a.c_source';

		$where['a.c_pcode'] = $parr['ucode'];
		$where['a.c_federationid'] = $parr['federationid'];

		$order = 'a.c_addtime desc';
		$list = M('Users_tuijian as a')->join($join)->where($where)->field($field)->limit($countPage, $pageSize)->order($order)->select();

		foreach ($list as $key => $value) {

		    if ($value['c_source'] == 1) {
		        $list[$key]['c_sourcestr'] = "邀请码";
		    } elseif ($value['c_source'] == 2) {
		        $list[$key]['c_sourcestr'] = "商品购买";
		    } elseif ($value['c_source'] == 3) {
		        $list[$key]['c_sourcestr'] = "扫码支付";
		    } else {
		        $list[$key]['c_sourcestr'] = "邀请码";
		    }

		    if (empty($value['c_headimg'])) {
		        $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
		    } else {
		        $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
		    }
		    $list[$key]['alerttime'] = date('Y/m/d H:i:s',strtotime($value['c_addtime']));

		    //判断用户是否绑定微信
		    $userwhere1['c_type'] = 1;
		    $userwhere1['c_ucode'] = $value['c_ucode'];
		    $countweixin = M('Users_auth')->where($userwhere1)->count();
		    $list[$key]['iswx_auth'] = 0;
		    if ($countweixin > 0) {
		        $list[$key]['iswx_auth'] = 1;
		    }

		    //判断用户是否绑定支付宝
		    $userwhere1['c_type'] = 2;
		    $countalipay = M('Users_auth')->where($userwhere1)->count();
		    $list[$key]['isal_auth'] = 0;
		    if ($countalipay > 0) {
		        $list[$key]['isal_auth'] = 1;
		    }

		    //判断用户是否实名认证
		    $userwhere['c_ucode'] = $value['c_ucode'];
		    $countcard = M('Users_bank')->where($userwhere)->count();
		    $list[$key]['iscard_band'] = 0;
		    if ($countcard > 0) {
		        $list[$key]['iscard_band'] = 1;
		    }

		}
		$count = $this->GetmyMembleCount($parr['ucode'],$parr['federationid']);
		$pageCount = ceil($count / $pageSize);
		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0, "查询成功", $data);
	}

	 /**
     *  查询分店会员总数
     *  @param ucode
     */
    public function GetmyMembleCount($ucode,$federationid) {
        $join = 'inner join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'b.c_phone,b.c_headimg,b.c_addtime,b.c_shop,b.c_nickname';
        $where['a.c_pcode'] = $ucode;
        $where['a.c_federationid'] = $federationid;
        $count = M('Users_tuijian as a')->join($join)->where($where)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * 查询分店的临时会员
     * @param ucode(分店店家ucode),federationid,pageindex,pagesize
     */
    function GetWxmebleList($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $field = '*';
        $where['c_lock'] = 2;
        $where['c_pcode'] = $parr['ucode'];
        $where['c_federationid'] = $parr['federationid'];
        
        $order = 'c_id desc';
        $list = M('Scanpay_tuijian')->where($where)->field($field)->limit($countPage, $pageSize)->order($order)->select();

        foreach ($list as $key => $value) {
            if (!$value['c_name']) {
                if ($value['c_type'] == 1) {
                    $list[$key]['c_name'] = '微信用户'.$value['c_id'];
                } else if ($value['c_type'] == 2) {
                    $list[$key]['c_name'] = '支付宝用户'.$value['c_id'];
                }
            }

        }
        $count = $this->GetWxmebleCount($parr);
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     * 查询微信临时会员总数
     * @param ucode,federationid
     */
    function GetWxmebleCount($parr)
    {
        $where['c_lock'] = 2;
        $where['c_pcode'] = $parr['ucode'];
        $where['c_federationid'] = $parr['federationid'];
       
        $count = M('Scanpay_tuijian')->where($where)->count();
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
	 * 连锁店各分店详细信息
	 * @param ucode
	 */
	public function Getshopmember($parr){
        //查询所有分店信息
        $pw['c_pid'] = $parr['pid'];
        $pw['c_type'] = 1;
		$pw['c_sign'] = 2;	
		$pw['c_status'] = 1;

		$subbranch = M('A_federation')->where($pw)->select();      

        if(!$subbranch){
        	$data = array();
        	return MessageInfo(0,"没有查询到分店信息！",$data);
        }

		foreach ($subbranch as $key => $value) {
			$w['c_ucode'] = $value['c_ucode'];	

			//查询用户信息
			$userinfo = M('Users')->where($w)->field('c_ucode,c_nickname,c_headimg,c_phone')->find();
			$subbranch[$key]['c_phone'] = $userinfo['c_phone'];
			$subbranch[$key]['c_nickname'] = $userinfo['c_nickname'];
			$subbranch[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];		

			//地址
			$localtion = M('User_local')->where($w)->getField('c_address');			
			$subbranch[$key]['localtion'] = $localtion;

			//分店累计营业额
			$federationid = $value['c_id'];

			$mw['c_federationid'] = $federationid;
			$mw['c_ucode'] = $value['c_ucode'];

			$subbranch[$key]['money'] = M('A_federation_moneylog')->where($mw)->sum('c_money');
		}		

        return MessageInfo(0, "查询成功", $subbranch);
	}

	/*     * *********************分店模块************************* */
	/** * 查询分店信息
	 * @param ucode
	 */
	public function Shopinfo($parr){
		$w['c_ucode'] = $parr['ucode'];
		$w['c_type'] = 1;
		$w['c_sign'] = 2;	
		$w['c_status'] = 1;

		$shop_info = M('A_federation')->where($w)->find();

		if(!$shop_info){
			return Message(1001,"没有查询到相关分店信息！");
		}

		$uw['c_ucode'] = $parr['ucode'];

		$userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg')->find();

		if(!$userinfo){
			return Message('1002',"没有查询到相关用户信息！");
		}

		$shop_info['c_nickname'] = $userinfo['c_nickname']; 
		$shop_info['c_headimg'] = GetHost().'/'.$userinfo['c_headimg']; 

		return MessageInfo(0,"查询成功",$shop_info);
	}


	/** * 按天统计分店收入数据
	 * @param ucode,federationid,datetime 格式2017-03-01,datetype(0-总收入，1-营收收入，2-跨界收入)
	 */
	public function GetShopDaysdate($parr){
		$datetype = $parr['datetype'];

		$w['c_ucode'] = $parr['ucode'];
		$w['c_federationid'] = $parr['federationid'];
		$w['c_datetime'] = $parr['datetime'];

		if($datetype == 1){
			//营收收入
			$w['c_type'] = 1;
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}elseif($datetype == 2){
			//跨界收入
			$w['c_type'] = 2;
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}else{
			$date = M('A_federation_moneylog')->where($w)->sum('c_money');
		}
       
        if (!$date){
            $date = 0;
        }
        return $date;		
	}	

	/** * 分店今日、昨日收入统计
	 * @param ucode,federationid,datetype(0-总收入，1-营收收入，2-跨界收入)
	 */
	public function GetShopToyes($parr){
		$federationid = $parr['federationid'];//分店id
		$datetype = $parr['datetype'];
		
		$param['federationid'] = $federationid;
		$param['ucode'] = $parr['ucode'];
		$param['datetype'] = $datetype;
		//今日收入
		$param['datetime'] = date("Y-m-d");
		$data['today'] = $this->GetShopDaysdate($param);

		//昨天收入
		$param['datetime'] = date("Y-m-d",strtotime("-1 day"));
		$data['yesterday'] = $this->GetShopDaysdate($param);

		return MessageInfo(0,"查询成功",$data);
	}

	/**
     * 根据时间查询连锁店单分店营收趋势
     * @param ucode,federationid,timetype(1-过去7天,2-过去30天,3-按月份),time 按月时间格式2017-03
     */
    public function GetShopdata($parr)
    {	
		//分店铺
		$w[] = "c_ucode = '".$parr['ucode']."'";

		$federationid = $parr['federationid'];//分店id
		$w[] = "c_federationid = '$federationid'";		

		//分时间
		$timetype = $parr['timetype'];

		if($timetype == 1){
			$begintime = date("Y-m-d",strtotime("-7 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}elseif($timetype == 2){
			$begintime = date("Y-m-d",strtotime("-30 day"));
			$endtime = date("Y-m-d",strtotime("-1 day"));
		}else{
			if(!$parr['time']){
				return Message('1002',"参数信息有误！");
			}

			$begintime = $parr['time'].'-01';
			$endtime1 = date('Y-m-d',strtotime('+'.($i+1).' months',strtotime($begintime)));

			$endtime = date('Y-m-d',strtotime('-1 day',strtotime($endtime1)));
		}

		$w[] = "c_datetime between '".$begintime."' and '".$endtime."'";

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}        

        $db = M();
        $sql = "select ifnull(sum(c_money), 0) as c_money,c_datetime from t_a_federation_moneylog $w1 GROUP BY c_datetime order by c_datetime desc";
        $list = $db->query($sql);
        
        foreach ($list as $key => $value) {          
            $list[$key]['time'] = date("m月d日", strtotime($value['c_datetime']));           
        }

        $data['list'] = $list;
        return MessageInfo(0, "查询成功", $data);
    }

     /**
	 * 分店总收入统计
	 * @param ucode,federationid,acttype(1-营收总收入，2-跨界总收入)
	 */
	public function GetshopdataCount($parr)
    {	
        //查询分店信息
		$federationid = $parr['federationid'];

		$w[] = "c_federationid = '$federationid'";
		$w[] = "c_ucode = ".$parr['ucode'];

		if($parr['acttype == 1']){
			$w[] = "c_type = 1";
		}else{
			$w[] = "c_type = 2";
		}

		if(!empty($w)){
			$w1 = ' WHERE '.@implode('AND ',$w);
		}

		$db = M();

		$sql = "select ifnull(SUM(c_money), 0) as c_money from t_a_federation_moneylog $w1 ";
        $list = $db->query($sql);

        $data['money'] = $list[0]['c_money'];

        return MessageInfo(0,"查询成功",$data);        
    }

	/**
	 * 分页查询连锁店单分店跨界收入明细
	 * @param ucode,federationid,pageindex,pagesize
	 */
	 public function Getshopdatakj($parr)
    {	
    	if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        
		$federationid = $parr['federationid'];//分店id

		$where[] = "c_ucode = ".$parr['ucode'];
		$where[] = "c_federationid = '$federationid'";
		$where[] = "c_money > 0";
    	$where[] = array('c_source=5 or c_source=11 or c_source=15');

    	$list = M('Users_moneylog')->field('c_ucode,c_money,c_key,c_addtime')->where($where)->order('c_addtime desc')->limit($countPage, $pageSize)->select();

    	$count = M('Users_moneylog')->where($where)->count();
    	$pageCount = ceil($count / $pageSize);

    	if(!$list){
    		$list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
    	}
       
		foreach ($list as $key => $value) {
			$uw['c_ucode'] = $value['c_ucode'];

			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
			 
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];			
 			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
		}
		
        $data = Page($pageIndex, $pageCount, $count, $lit);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
	 * 根据天、店铺查询连锁店单分店营收收入明细
	 * @param ucode,federationid,datetime 按日时间格式2017-06-07
	 */
	 public function Getshopdatays($parr)
    {	

		//分时间
		$startime = $parr['datetime'].' 00:00:00';
        $endtime = $parr['datetime'].' 23:59:59';
        $where[] = array("c_addtime>='$startime' and c_addtime<'$endtime'");

        //分店铺
        $federationid = $parr['federationid'];
    	$where[] = "c_federationid = '$federationid'";
  
    	$where[] = "c_ucode = ".$parr['ucode'];        	

		$where[] = "c_money > 0";
    	$where[] = array('c_source=1 or c_source=4 or c_source=9');

    	$list = M('Users_moneylog')->field('c_ucode,c_money,c_addtime')->where($where)->order('c_addtime desc')->select();

    	if(!$list){
    		$list = array();
            return MessageInfo(0, '查询成功', $data);
    	}
       
       	$money = 0.00; 
		foreach ($list as $key => $value) {
			$uw['c_ucode'] = $value['c_ucode'];

			$userinfo = M('Users')->field('c_nickname,c_headimg')->where($uw)->find();
			 
        	$list[$key]['c_nickname'] = $userinfo['c_nickname'];			
 			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

 			$money = sprintf('%2.f',$money + $list[$key]['money']);
		}

		$data['money'] = $money;
		$data['list'] = $list;
		
        return MessageInfo(0, '查询成功', $data);
    }

    //连锁店列表    ucode
	function ChainList($parr){
		//判断用户身份信息，并获取总店信息
		$w['c_ucode'] = $parr['ucode'];
		$w['c_type'] = 1;
		$w['c_sign'] = 1;	
		$w['c_status'] = 1;

		$league_info = M('A_federation')->where($w)->find();

		if(!$league_info){
			return Message(1001,"没有查询到相关连锁店信息！"); 
		}

		//查询子连锁店列表
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}
		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;

		$subw['c_pid'] = $league_info['c_id'];
		$subw['c_type'] = 1;
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
			$userinfo = M('Users')->field('c_nickname,c_phone,c_headimg')->where($uw)->find();

			$list[$key]['c_nickname'] = $userinfo['c_nickname'];
			$list[$key]['c_phone'] = $userinfo['c_phone'];
			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

			//获取商家资料审核状态
			$cw['c_ucode'] = $value['c_ucode'];
			$checkinfo = M('Check_shopinfo')->field('c_id,c_checked,c_dcode')->where($cw)->find();

			$list[$key]['c_checked'] = $checkinfo['c_checked'];
			$list[$key]['c_dcode'] = $checkinfo['c_dcode'];
		}

		$pageCount = ceil($count / $pageSize);
		$data = Page($pageIndex, $pageCount, $count, $list);
		return MessageInfo(0,"查询成功",$data);
	}

	//验证用户是否可成为连锁店
	function UserInfo($phone)
	{
		$uw['c_phone'] = $phone;
		$data = M('Users')->where($uw)->field('c_ucode,c_shop,c_phone')->find();
		if (!$data) {
			return Message(1000,'该手机号信息不存在');
		}
		if ($data['c_shop'] == 1) {
			return Message(3000,'添加对象不能是商家');
		}

		$rw['c_ucode'] = $data['c_ucode'];		
		$roles = M('A_federation')->where($rw)->find();
		if($roles){
			return Message(3001,'该用户已经是连锁身份');
		}

		$data['c_headimg'] = GetHost().'/'.$data['c_headimg'];
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 连锁总店添加连锁店
	 * @param phone,ucode,pwd,shopcode
	 */
	function Confirmsubmit($parr)
	{
		$ucode = $parr['ucode'];
		$phone = $parr['phone'];
		$pwd = $parr['pwd'];
		$shopcode = $parr['shopcode'];

		//查询商家连锁总店身份
		$w['c_ucode'] = $ucode;
		$w['c_type'] = 1;
		$w['c_sign'] = 1;	
		$w['c_status'] = 1;
		$league_info = M('A_federation')->where($w)->find();
		if(!$league_info){
			return Message(3000,"暂未有权限添加连锁分店"); 
		}

		if ($league_info['c_remain_num'] <= 0) {
			return Message(3001,"可添加数量为0，请联系客服");
		}

		//查询商家信息
		$sw['c_ucode'] = $ucode;
		$userinfo = M('Users')->where($sw)->field('c_ucode,c_acode,c_isfixed1')->find();		

		$uw['c_phone'] = $phone;
		$data = M('Users')->where($uw)->field('c_id,c_ucode,c_shop,c_phone')->find();
		if ($data) {
			if ($data['c_shop'] == 1) {
				return Message(3001,'添加对象不能是商家');
			}

			$rw['c_ucode'] = $data['c_ucode'];		
			$roles = M('A_federation')->where($rw)->find();
			if($roles){
				return Message(3002,'该用户已经是连锁身份');
			}

			$db = M('');
        	$db->startTrans(); /* 开启事务 */

			//改变所属代理
			$newwheresave['c_acode'] = $userinfo['c_acode'];
			$newwheresave['c_isfixed1'] = $userinfo['c_isfixed1'];
			$newwherinfo['c_id'] = $data['c_id'];			
	        $result = M('Users')->where($newwherinfo)->save($newwheresave);
	        // if (!$result) {
	        //     $db->rollback();
	        //     return Message(3003, '归属代理失败！');
	        // }

			$yhucode = $data['c_ucode'];
			$nickname = $data['c_nickname'];
		} else {
			if (empty($pwd)) {
				return Message(3005,'请设置初始密码');
			}

			$db = M('');
        	$db->startTrans(); /* 开启事务 */

			//注册用户基本信息
			$yhucode = CreateUcode('xms'); /* 创建用户编码 */
			$whereadd['c_ucode'] = $yhucode;
			$whereadd['c_acode'] = $userinfo['c_acode'];
            $whereadd['c_signature'] = '蜜主很懒，没有什么个性签名！';
            $whereadd['c_level'] = 1; /* 普通会员 */
            $whereadd['c_headimg'] = 'data/userheadimg/' . rand(1, 10) . '.jpg';
            $whereadd['c_phone'] = $phone;
            $whereadd['c_password'] = encrypt($pwd,C('ENCRYPT_KEY'));
            $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Users')->add($whereadd);
            if (!$result) {
	            $db->rollback();
	            return Message(3002, '注册失败！');
	        }

	        //改变昵称
	        $newwheresave['c_nickname'] = '小蜜用户m' . $result;
	        $newwheresave['c_isfixed1'] = $userinfo['c_isfixed1'];
	        $newwherinfo['c_id'] = $result;
	        $result = M('Users')->where($newwherinfo)->save($newwheresave);
	        if (!$result) {
	            $db->rollback();
	            return Message(3003, '注册失败！');
	        }

	        $nickname = $newwheresave['c_nickname'];
		}

		//同步位置表
		$localw['c_ucode'] = $yhucode;
		$result = M('User_local')->where($localw)->find();
		$localinfo['c_isfixed'] = $userinfo['c_isfixed1'];
		$localinfo['c_updatetime'] = gdtime();
		if (!$result) {
			$localinfo['c_addtime'] = gdtime();
			$result = M('User_local')->add($localinfo); 
		} else {
			$result = M('User_local')->where($localw)->save($localinfo); 
		}
		if (!$result) {
            $db->rollback();
            return Message(3003, '同步信息失败');
        }

        //先查看商家信息表数据
        $shopxinx = M('Check_shopinfo')->where($localw)->find();
        if (!$shopxinx) { 
	        //新增商户资料信息表
	        $shopinfo['c_addtime'] = date('Y-m-d H:i:s');
	        $shopinfo['c_istore'] = 1;
	        $shopinfo['c_ucode'] = $yhucode;
	        $shopinfo['c_name'] = $nickname;
	        $shopinfo['c_phone'] = $phone;
	        $shopinfo['c_checked'] = 0;
	        $result = M('Check_shopinfo')->add($shopinfo);
	        if (!$result) {
	            $db->rollback();
	            return Message(1002, '注册失败！');
	        }
	    }

        //写入联盟数据
        $add['c_ucode'] = $yhucode;
		$add['c_pid'] = $league_info['c_id'];
		$add['c_name'] = $league_info['c_name'];
		$add['c_shopcode'] = $shopcode;
		$add['c_type'] = 1;
		$add['c_status'] = 2;
		$add['c_sign'] = 2;
        $add['c_addtime'] = gdtime();
        $result = M('A_federation')->add($add);
        if (!$result) {
        	$db->rollback();
            return Message(3001,'联盟记录失败');
        }

        //修改可邀请连锁店数量
        $w['c_remain_num'] = array('GT',0);
		$result = M('A_federation')->where($w)->setDec('c_remain_num',1);
		if(!$result){
			$db->rollback();
			return Message(3003,"修改邀请数量失败"); 
		}

        $db->commit();
        return Message(0, '添加成功');
	}

}