<?php 

/**
*   平台利润记录
*/
class SystemOrder
{
	/**
	 * 记录平台利润
	 * @param orderid,acode,type(1线上,2线下)
	 */
	function WriteProfit($parr)
	{
		$type = $parr['type'];

		if ($type == 1) { //订单
			$result = $this->GetOrderProfit($parr);
		} else if ($type == 2) { //扫码
			$result = $this->GetScanpayProfit($parr);
		} else {
			return Message(0,'不需要记录');
		}

		if ($result['code'] != 0) {
			return Message(0,'不需要记录');
		}
		$profitlist = $result['data'];

		//查询当天利润是否存在
		$profitadd['c_datetime'] = date('Y-m-d');
		$profitadd['c_updatetime'] = gdtime();
		$where['c_datetime'] = date('Y-m-d');
		$profitinfo = M('System_profit')->where($where)->find();
		if ($profitinfo) {
			$result = M('System_profit')->where($where)->save($profitadd);
		} else {
			$result = M('System_profit')->add($profitadd);
		}

		if (!$result) {
			return Message(3001,'平台利润记录失败');
		}

		//循环利润累加
		$profitname = array_keys($profitlist);
		$profitvalue = array_values($profitlist);
		foreach ($profitvalue as $key => $value) {
			if ($value > 0) {
				$result = M('System_profit')->where($where)->setInc($profitname[$key],$value);
				if (!$result) {
					return Message(3002,'利润累计失败');
				}
			}
		}

		return Message(0,'记录成功');
	}

	/**
	 * 查询线上订单利润
	 * @param orderid,acode
	 */
	function GetOrderProfit($parr)
	{
		//查询订单详情信息
		$where['c_orderid'] = $parr['orderid'];
		$infolist = M('Order_details')->where($where)->select();
		$commission = '0.00';
		foreach ($infolist as $key => $value) {
			$commission = bcadd($commission,$value['c_commission'],2);
		}

		if ($commission <= 0) {
			return Message(3000,'没有抽成');
		}
		
		//查询系统配入
        $settinginfo = IGD('Common', 'Info')->GetSystemSet();
        $setting = $settinginfo['data'];
        if ($settinginfo['code'] != 0) {
            return Message(1017, "系统配置不存在");
        }
        $shopscale = $setting['c_shop_refreescale'];    //商家提成
        $cityscale = $setting['c_city_scale'];			//代理提成
        $areascale = $setting['c_area_scale'];			//区域经理提成
        $red_scale = $setting['c_red_scale'];   		//红包提成            

        $shopmoney = bcmul($commission, bcdiv($shopscale, 100, 4), 2); //商家提成金额
        $citymoney = bcmul($commission, bcdiv($cityscale, 100, 4), 2); //代理提成金额
        $areamoney = bcmul($commission, bcdiv($areascale, 100, 4), 2); //区域经理提成金额
        $redprice = bcmul($commission, bcdiv($red_scale, 100, 4), 2);  //红包金额

        $total_rake = $commission;
        $total_profit = $commission - $shopmoney - $citymoney - $areamoney - $redprice;

        //查询商家属性并赋值输出
        $fiexd = M('Users')->where(array('c_ucode'=>$parr['acode']))->getField('c_isfixed1');
        if ($fiexd == 1) {  //线下
        	$data['c_total_rake'] = $total_rake;
        	$data['c_total_profit'] = $total_profit;
        	$data['c_online_rake'] = $total_rake;
        	$data['c_onod_rake'] = $total_rake;
        	$data['c_onshop_rake'] = $shopmoney;
        	$data['c_onagent_rake'] = $citymoney;
        	$data['c_onarea_rake'] = $areamoney;
        	$data['c_onred_rake'] = $redprice;
        	$data['c_onsys_rake'] = $total_profit;
        } else {
        	$data['c_total_rake'] = $total_rake;
        	$data['c_total_profit'] = $total_profit;
        	$data['c_offline_rake'] = $total_rake;
        	$data['c_offod_rake'] = $total_rake;
        	$data['c_offshop_rake'] = $shopmoney;
        	$data['c_offagent_rake'] = $citymoney;
        	$data['c_offarea_rake'] = $areamoney;
        	$data['c_offred_rake'] = $redprice;
        	$data['c_offsys_rake'] = $total_profit;
        }

        return MessageInfo(0,'查询成功',$data);
	}

	/**
	 * 查询线上订单利润
	 * @param orderid,acode
	 */
	function GetScanpayProfit($parr)
	{
		//查询订单详情信息
		$where['c_ncode'] = $parr['orderid'];
		$infolist = M('Scanpay')->where($where)->find();
		$commission = $infolist['c_commission'];

		if ($commission <= 0) {
			return Message(3000,'没有抽成');
		}

		//查询商家属性并赋值输出
        $acodeinfo = M('Users')->where(array('c_ucode'=>$parr['acode']))->field('c_isfixed1,c_shoptrade')->find();

        //查询系统配入
        $setting = M('Shop_industry')->where(array('c_id'=>$acodeinfo['c_shoptrade']))->find();
        if (!$setting) {
            return Message(1017, "系统配置不存在");
        }

        if ($acodeinfo['c_isfixed1'] == 1) {  //线下
        	$shopscale = $setting['c_scanpay_tjprofit'];    //商家提成
	        $cityscale = $setting['c_scanpay_cityprofit'];			//代理提成
	        $areascale = $setting['c_scanpay_areaprofit'];			//区域经理提成
	        $red_scale = $setting['c_scanpay_redscale'];   		//红包提成     
        } else {
        	$shopscale = $setting['c_online_tjprofit'];    //商家提成
	        $cityscale = $setting['c_online_cityprofit'];			//代理提成
	        $areascale = $setting['c_online_areaprofit'];			//区域经理提成
	        $red_scale = $setting['c_online_redscale'];   		//红包提成     
        }

        $shopmoney = bcmul($commission, bcdiv($shopscale, 100, 4), 2); //商家提成金额
        $citymoney = bcmul($commission, bcdiv($cityscale, 100, 4), 2); //代理提成金额
        $areamoney = bcmul($commission, bcdiv($areascale, 100, 4), 2); //区域经理提成金额
        $redprice = bcmul($commission, bcdiv($red_scale, 100, 4), 2);  //红包金额

        $total_rake = $commission;
        $total_profit = $commission - $shopmoney - $citymoney - $areamoney - $redprice;

        if ($acodeinfo['c_isfixed1'] == 1) {  //线下
        	$data['c_total_rake'] = $total_rake;
        	$data['c_total_profit'] = $total_profit;
        	$data['c_online_rake'] = $total_rake;
        	$data['c_onsp_rake'] = $total_rake;
        	$data['c_onshop_rake'] = $shopmoney;
        	$data['c_onagent_rake'] = $citymoney;
        	$data['c_onarea_rake'] = $areamoney;
        	$data['c_onred_rake'] = $redprice;
        	$data['c_onsys_rake'] = $total_profit;
        } else {
        	$data['c_total_rake'] = $total_rake;
        	$data['c_total_profit'] = $total_profit;
        	$data['c_offline_rake'] = $total_rake;
        	$data['c_offsp_rake'] = $total_rake;
        	$data['c_offshop_rake'] = $shopmoney;
        	$data['c_offagent_rake'] = $citymoney;
        	$data['c_offarea_rake'] = $areamoney;
        	$data['c_offred_rake'] = $redprice;
        	$data['c_offsys_rake'] = $total_profit;
        }

        return MessageInfo(0,'查询成功',$data);
	}

}



?>