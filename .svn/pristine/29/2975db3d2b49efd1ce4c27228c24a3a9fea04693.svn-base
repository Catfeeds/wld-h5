<?php

/**
 * 商家收银管理
 *
 */
class CashierStore {

	/**
	 * 邀请收银员
	 * @param phone,name,ucode
	 */
	function InviteCashier($parr)
	{
		//查询商家信息
		$awh['c_ucode'] = $parr['ucode'];
		$acodeinfo = M('Users')->where($awh)->field('c_ucode,c_nickname')->find();
		if (!$acodeinfo) {
			return Message(3000,'信息有误');
		}

		//查询是否创建了收银台
		$dsw['c_delete'] = 2;
        $dsw['c_ucode'] = $parr['ucode'];
        $desknum = M('A_cashier_desk')->where($dsw)->count();
        if ($desknum <= 0) {
        	return Message(3000,'邀请收银员请先创建收银台');
        }

		//查询被邀请人信息
		$uwh['c_phone'] = $parr['phone'];
		$userinfo = M('Users')->where($uwh)->field('c_ucode,c_nickname')->find();
		if (!$userinfo) {
			return Message(3001,'被邀请人不存在');
		}

		//查询该用户是否已经是收银员
		$where['c_ucode'] = $userinfo['c_ucode'];
		$where['c_delete'] = 2;
		$result = M('A_cashier')->where($where)->find();
		if ($result) {
			return Message(3001,'该用户已是收银员');
		}

		$result = $this->Checkname($parr['name'],$parr['ucode']);
		if ($result['code'] != 0) {
			return $result;
		}

		$dataarr['name'] = $parr['name'];
		$dataarr['acode'] = $parr['ucode'];
		$datajson = json_encode($dataarr);
		$desc = '商家：'.$acodeinfo['c_nickname'].'，想邀请您成为他的收银员';
		//写入邀请信息
		$add['c_acode'] = $acodeinfo['c_ucode'];
		$add['c_ucode'] = $userinfo['c_ucode'];
		$add['c_type'] = 1;
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
        $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Cashier/ask?askid='.$askid;
        $msgdata['tagvalue'] = GetHost(1) . '/index.php/Home/Cashier/ask?askid='.$askid;
        $Msgcentre->CreateMessege($msgdata);

		return Message(0,'邀请成功');
	}

	//检查编号
	function Checkname($name,$acode)
	{
		$where['c_name'] = $name;
		$where['c_acode'] = $acode;
        $where['c_delete'] = 2;
        $data = M('A_cashier')->where($where)->find();
        if ($data) {
        	return Message(3000,'编号重复');
        }

        return Message(0,'编号可用');
	}

	//获取用户基本信息
	function UserInfo($phone)
	{
		$uw['c_phone'] = $phone;
		$data = M('Users')->where($uw)->field('c_ucode,c_nickname,c_headimg,c_signature')->find();
		if (!$data) {
			return Message(3000,'信息不存在');
		}

		//查询该用户是否已经是收银员
		$where['c_ucode'] = $data['c_ucode'];
		$where['c_delete'] = 2;
		$result = M('A_cashier')->where($where)->find();
		if ($result) {
			return Message(3001,'该用户已是收银员');
		}

		$data['c_headimg'] = GetHost().'/'.$data['c_headimg'];
		return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 收银员列表
	 * @param ucode,pageindex,pagesize
	 */
	function GetCashierList($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['a.c_delete'] = 2;
        $where['a.c_acode'] = $parr['ucode'];
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_phone';
        $order = 'a.c_id desc';
        $list = M('A_cashier as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('A_cashier as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	/**
	 * 添加收银台
	 * @param ucode,name(收银台01)
	 */
	function AddDesk($parr)
	{

		$db = M('');
		$db -> startTrans();

		// 先查询该账号 所添加的收银台的个数
        $deskct = M('A_cashier_desk')->where(array('c_ucode'=>$parr['ucode']))->count();
        $total = $deskct+1;
        if ($total>9) {
        	$name = '收银台'.$total;
        } else {
        	$name = '收银台0'.$total;
        }

        if ($parr['sign'] != 1 && $total>6) {
        	$db->rollback();
			return Message(3000,'暂时只能添加6个收银台');
        }        

		//写入数据记录
		$add['c_ucode'] = $parr['ucode'];
		$add['c_name'] = $name;
		
		$add['c_status'] = 2;
		$add['c_delete'] = 2;
		$add['c_addtime'] = gdtime();
		$add['c_updatetime'] = gdtime();
		$result = M('A_cashier_desk')->add($add);
		if (!$result) {
			$db->rollback();
			return Message(3000,'添加失败');
		}

		$deskid = $result;
		//生成收银台二维码		
		$param['ucode'] = $parr['ucode'];//商家编码
		$param['deskid'] = $deskid;//收银台编号
		$param['xhname'] = $name;//$parr['name'];//收银台名称
		$param['qrcode_type'] = 3;//收银台

        $result = IGD('Qrcode', 'Store')->CashierQrcode($param);
        if($result['code'] != 0){
        	$db->rollback();
			return Message(3002,'生成二维码失败！');
        }

        $qcode = $result['data']['img'];

        //二维码存数据库
        $save['c_qcode'] = $qcode;

        $qw['c_id'] = $deskid;
        $result = M('A_cashier_desk')->where($qw)->save($save);
        if($result < 0){
        	$db->rollback();
			return Message(3003,'保存二维码失败！');
        }

        $db->commit();
        $data['total']=$total;
        return MessageInfo(0,'添加成功',$total);
	}

	/**
	 * 获取收银台详情
	 * @param ucode,deskid
	 */
	function GetDeskInfo($parr)
	{
		$where['c_delete'] = 2;
        $where['c_id'] = $parr['deskid'];
        $data = M('A_cashier_desk')->where($where)->find();
        if (!$data) {
            return Message(3000,'没有相关收银台');
        }

        if ($parr['info'] != 1) {
        	//重新生成收银台二维码
			$param['ucode'] = $data['c_ucode'];//商家编码
			$param['deskid'] = $data['c_id'];//收银台编号
			$param['xhname'] = $data['c_name'];//收银台名称
			$param['qrcode_type'] = 3;//收银台

	        $result = IGD('Qrcode', 'Store')->CashierQrcode($param);

	        if($result['code'] != 0){
	        	return MessageInfo(0,'查询成功',$data);
	        }

	        $qcode = $result['data']['img'];
	        
	        //修改数据库二维码路径
	        $save['c_qcode'] = $qcode;
	        $result = M('A_cashier_desk')->where($where)->save($save);
	        if($result < 0){
	        	return MessageInfo(0,'查询成功',$data);
	        }
        } else {
        	$qcode = $data['c_qcode'];
        }

        $data['c_qcode'] = $qcode;
        return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 收银台列表
	 * @param ucode
	 */
	function GetDeskList($parr)
	{
        $where['c_delete'] = 2;
        $where['c_ucode'] = $parr['ucode'];
        $order = 'c_id desc';
        $list = M('A_cashier_desk')->where($where)->order($order)->select();

        foreach ($list as $key => $value) {
        	$list[$key]['c_qcode'] = GetHost().''.$value['c_qcode'];

        	//查询正在该收银台上班的收银员
        	$cw['c_work'] = 1;
        	$cw['c_deskid'] = $value['c_id'];
        	$cashinfo = M('A_cashier')->where($cw)->field('c_id,c_ucode,c_name')->find();
        	$list[$key]['cashid'] = $cashinfo['c_id'];
        	$list[$key]['cashname'] = $cashinfo['c_name'];
        	$list[$key]['cashucode'] = $cashinfo['c_ucode'];
        }

        return MessageInfo(0, '查询成功', $list);
	}

	/**
	 * 停用与启用(删除)收银员
	 * @param cashid,ucode,status(1启用，2停用),(delete 1删除，2不删除)
	 */
	function OptionCashier($parr)
	{
		$where['c_acode'] = $parr['ucode'];
		$where['c_id'] = $parr['cashid'];
		if (!empty($parr['status'])) {
			if ($parr['status'] != 1) {
				$parr['status'] = 2;
			} else {
				$save['c_deskid'] = '';
				$save['c_work'] = 2;
			}
			$save['c_status'] = $parr['status'];
		}
		
		if (!empty($parr['delete'])) {
			if ($parr['delete'] != 1) {
				$parr['delete'] = 2;
			} else {
				$save['c_deskid'] = '';
				$save['c_work'] = 2;
			}
			$save['c_status'] = 2;
			$save['c_delete'] = $parr['delete'];
		}

		$casherinfo = M('A_cashier')->where($where)->find();

		$save['c_updatetime'] = gdtime();
		$result = M('A_cashier')->where($where)->save($save);
		if (!$result) {
			return Message(3000,'操作失败');
		}

		if (!empty($casherinfo['c_deskid']) && $casherinfo['c_work'] == 1) {
			$deskwhere['c_id'] = $casherinfo['c_deskid'];
			$deskwhere['c_status'] = 1;
			$deskwhere['c_delete'] = 2;
			$desksave['c_status'] = 2;
			$result = M('A_cashier_desk')->where($deskwhere)->save($desksave);
			if (!$result) {
				return Message(3000,'操作失败');
			}
		}

		return Message(0,'操作成功');
	}

	/**
	 * 收银台
	 * 查询7天或者30天数据用于折现图
	 * @param deskid,ucode,timetype(1-过去7天,2-过去30天)
	 */
	function LineChartdata($parr)
	{
		$timetype = $parr['timetype'];
		$ucode = $parr['ucode'];
		$deskid = $parr['deskid'];      //收银台id

		// $w1[] = "c_ucode = '$ucode'";
		$w1[] = "c_deskid = '$deskid'";

		if($timetype == 1){
			$begintime = date("Y-m-d 00:00:00",strtotime("-7 day"));
			$endtime = date("Y-m-d 23:59:59",strtotime("-1 day"));
		}else{
			$begintime = date("Y-m-d 00:00:00",strtotime("-30 day"));
			$endtime = date("Y-m-d 23:59:59",strtotime("-1 day"));
		}
		$w1[] = "c_addtime between '".$begintime."' and '".$endtime."'";
		$w = ' where '.@implode('AND ',$w1);

		$db = M('');
		$group = "gt";
		$order = 'c_addtime asc';
		$sql = "select c_addtime,sum(c_money) as c_money,FROM_UNIXTIME(unix_timestamp(c_addtime),'%Y-%m-%d') as gt from t_users_moneylog $w group by $group order by $order";
		
		$list = $db->query($sql);

        $money = 0.00;
        foreach ($list as $key => $value) {          
            $list[$key]['time'] = date("m/d", strtotime($value['gt'].' 00:00:00'));

            $money = sprintf('%.2f',$money + $value['c_money']);           
        }
        $data['money'] = $money;
        $data['list'] = $list;

        return MessageInfo(0, "查询成功", $data);
	}

	/**
	 * 查询收银台收入明细
	 * @param (cashid),deskid,ucode,time(2017-06,2017-06-12)
	 */
	function GetDeskIncome($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

		if ($parr['deskid']) {
			$where['c_deskid'] = $parr['deskid'];
		}
		
		if ($parr['cashid']) {
			$where['c_cashierid'] = $parr['cashid'];
		}

		// $where['c_ucode'] = $parr['ucode'];		

		if (!empty($parr['time'])) {
			//分时间段查询
			$arrtime = explode('-', $parr['time']);
			if (count($arrtime) == 2) {
				$begintime = $parr['time'].'-01 00:00:00';
				$endtime1 = date('Y-m-d',strtotime('+1 months',strtotime($begintime)));
				$endtime = date('Y-m-d 23:59:59',strtotime('-1 day',strtotime($endtime1)));
			} else if (count($arrtime) == 3) {
				$begintime = $parr['time'].' 00:00:00';
				$endtime = $parr['time'].' 23:59:59';
			}

	        $where[] = array("c_addtime>='$begintime' and c_addtime<='$endtime'");
		}
		
		$where['c_money'] = array('GT',0);
    	$where[] = array('c_source=9');
    	$list = M('Users_moneylog')->where($where)->limit($countPage, $pageSize)->order('c_addtime desc')->select();


    	$count = M('Users_moneylog')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }
       
       	$datesign = '';
		foreach ($list as $key => $value) {
			$list[$key]['time'] = date("m-d H:i:s", strtotime($value['c_addtime']));
			$list[$key]['remarktime'] = date("m月d日", strtotime($value['c_addtime']));
            if ($datesign != $list[$key]['remarktime'] && !empty($parr['cashid'])) {
                $datesign = $list[$key]['remarktime'];
                // $parr['datetime'] = date("Y-m-d", strtotime($value['c_addtime']));
                // $list[$key]['moneycount'] = IGD('Cashier','User')->CountDatePaylog($parr);
                // $list[$key]['commission'] = IGD('Cashier','User')->CountCommission($parr);
            }
            
            $list[$key]['c_showimg'] = GetHost() . '/' . $value['c_showimg'];
            //查询扫码订单交易方式
            $plogwhere['c_source'] = 2;
            $plogwhere['c_orderid'] = $value['c_key'];
            $paylog = M('Order_paylog')->where($plogwhere)->order('c_id desc')->find();
            if ($paylog['c_payrule'] == 1) {
                $list[$key]['text'] = '支付宝支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/alpay.png';
            } else if ($paylog['c_payrule'] == 2 || $paylog['c_payrule'] == 3) {
                $list[$key]['text'] = '微信支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/wxpay.png';
            } else {
                $list[$key]['text'] = '余额支付';
                $list[$key]['img'] = GetHost() . '/data/useimg/xmpay.png';
            }

            //显示周
            $datetime = strtotime($value['c_addtime']);
            if (date('Y-m-d',$datetime) == date('Y-m-d')) {
            	$list[$key]['showweek'] = '今天';
            } else if (date('Y-m-d',$datetime) == date('Y-m-d',strtotime('-1 day'))) {
            	$list[$key]['showweek'] = '昨天';
            } else {
            	$list[$key]['showweek'] = $this->getTimeWeek($datetime);
            }
            
            //操作员
            $cashierw['c_id'] = $value['c_cashierid'];
            $list[$key]['cashier_name'] = M('A_cashier')->where($cashierw)->getfield('c_name');
		}

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

	function getTimeWeek($time, $i = 0) {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        $oneD = 24 * 60 * 60;
        return "周" . $weekarray[date("w", $time + $oneD * $i)];
    }



}