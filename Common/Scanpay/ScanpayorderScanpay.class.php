<?php

/**
 * 用户扫码支付订单管理
 */
class ScanpayorderScanpay {

	/**
	 * 查询用户扫码支付订单列表
	 * @param ucode,pageindex,pagesize,state(0全部，1待评价，2已完成)
	 */
	function ScanpayOrderList($parr)
	{
		if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($parr['state'] == 1) {
            $where['c_evaluate'] = 0;
        } else if ($parr['state'] == 2) {
            $where['c_evaluate'] = 1;
        }

        $where['c_pay_state'] = 1;
        $where['c_ucode'] = $parr['ucode'];
        $field = 'c_id,c_ncode,c_type,c_acode,c_ucode,c_type,c_nickname,c_money,c_actual_price,c_pay_state,c_pay_rule,c_commission,c_profit,c_evaluate,c_addtime';
        $order = 'c_evaluate asc,c_id desc';
        $list = M('Scanpay')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();
        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            $uw['c_ucode'] = $value['c_acode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg,c_isfixed1')->find();
            $list[$key]['c_nickname'] = $userinfo['c_nickname'];

            $list[$key]['c_addtime'] = date('Y/m/d H:i:s',strtotime($value['c_addtime']));
            if (empty($userinfo['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            }
            if ($userinfo['c_isfixed1'] == 1) {
                $list[$key]['c_isfixed'] = 1;
            } else {
                $list[$key]['c_isfixed'] = 2;
            }
        }
        $count = M('Scanpay')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
	}

    /**
     * 查询扫码支付详情
     * @param ncode
     */
    function ScanpayOrderInfo($parr)
    {
        $field = '*';
        $where['c_ncode'] = $parr['ncode'];
        $data = M('Scanpay')->where($where)->field($field)->find();
        if (!$data) {
            return Message(3000, "数据不存在");
        }

        $uw['c_ucode'] = $data['c_acode'];
        $userinfo = M('Users')->where($uw)->field('c_nickname,c_headimg,c_isfixed1')->find();
        $data['c_nickname'] = $userinfo['c_nickname'];
        if (empty($userinfo['c_headimg'])) {
            $data['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
        } else {
            $data['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        }
        if ($data['c_pay_rule'] == 1) {
            $payrulestr = '支付宝支付';
        } else if ($data['c_pay_rule'] == 2) {
            $payrulestr = '微信支付支付';
        } else {
            $payrulestr = '微信支付';
        }
        if ($userinfo['c_isfixed1'] == 1) {
            $data['c_isfixed'] = 1;
        } else {
            $data['c_isfixed'] = 2;
        }
        $ncodewhere['c_orderid'] = $parr['ncode'];
        $ncodewhere['c_source'] = 2;
        $ncodewhere['c_payrule'] = 4;
        $banlacemoney = M('Order_paylog')->where($ncodewhere)->getField('c_money');
        $data['banlace'] = empty($banlacemoney)?'0.00':$banlacemoney;
        $data['paymoney'] = bcsub($data['c_money'], $banlacemoney, 2);
        if ($data['paymoney'] > 0 && $data['banlace'] > 0) {
            $data['paystr'] = "余额支付￥\"".$data['banlace']."\"\n".$payrulestr.'￥'.$data['paymoney'];
        } else if ($data['paymoney'] <= 0 && $data['banlace'] > 0) {
            $data['paystr'] = '余额支付￥'.$data['banlace'];
        } else if ($data['paymoney'] > 0 && $data['banlace'] <= 0) {
            $data['paystr'] = $payrulestr.'￥'.$data['paymoney'];
        }
        $data['c_addtime'] = date('Y/m/d H:i:s',strtotime($data['c_addtime']));
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 评价扫码支付订单
     * @param ncode,ucode,score,content,imglist
     */
    function EvaluateScanOrder($parr)
    {
        // 查询订单信息
        $where['c_ncode'] = $parr['ncode'];
        $detailinfo = M('Scanpay')->where($where)->find();
        if (count($detailinfo) == 0) {
            return Message(1000, '订单信息不存在');
        }

        if ($detailinfo['c_evaluate'] == 1) {
            return Message(1001, '已经评论');
        }

        if (empty($parr['content'])) {
            return Message(1002, '请输入评论内容');
        }

        $data['c_ucode'] = $detailinfo['c_ucode'];
        $data['c_orderid'] = $detailinfo['c_ncode'];
        $data['c_score'] = $parr['score'];
        $data['c_acode'] = $detailinfo['c_acode'];
        $data['c_content'] = $parr['content'];
        $data['c_source'] = 2;
        $data['c_object'] = 2;
        $data['c_addtime'] = date('Y-m-d H:i:s');

        $db = M('');
        $db->startTrans();
        $result = M('Product_score')->add($data);
        $pid = $result;
        if (!$result) {
            $db->rollback();
            return Message(1000, "评价失败，请稍后再试");
        }

        //保存图片
        $imglist = $parr['imglist'];
        $imgdata['c_regionid'] = $pid;
        $imgdata['c_sourceid'] = 3;
        $imgdata['c_addtime'] = date('Y-m-d H:i:s');
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $imgdata['c_img'] = $value;
                $imgdata['c_thumbnail_img'] = $value;
                $result = M('Resource_img')->add($imgdata);
                if (!$result) {
                    $db->rollback();
                    return Message(1000, "图片存储失败");
                }
            }
        }

        $save['c_evaluate'] = 1;
        $result = M('Scanpay')->where($where)->save($save);
        if (!$result) {
            $db->rollback();
            return Message(1000, "评价失败，请稍后再试");
        }

        $db->commit();
        return Message(0, "评价成功");
    }

    /**
     * 查询用户中心未评价订单数
     * @param ucode
     */
    public function GetScanpayNum($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_evaluate'] = 0;
        $where['c_pay_state'] = 1;
        $count1 = M('Scanpay')->where($where)->count();

        $data['waitcomment'] = $count1;
        return MessageInfo(0, "查询成功",$data);
    }
}
