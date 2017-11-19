<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 拼团活动
 */
class ShoppingController extends BaseController {

    //拼团商品详情页面
    public function pt_detail()
    {
        $group_code = I('groupcode');
        $act_code = I('act_pcode');
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        if(empty($ucode) || empty($act_code) ||empty($group_code)){
            $this->ajaxReturn(Message(1001,'参数缺失'));
        }

        //获取参团人员信息
        $parr['ucode'] = $ucode;
        $parr['groupcode'] = $group_code;
        $result1 = IGD('Groupbuy','Newact')->GroupInfo($parr);
        $groupinfo = $result1['data'];

        //获取拼团详情数据
        $parr['act_pcode'] = $act_code;
        $result2 = IGD('Groupbuy','Newact')->ProductInfo($parr);
        $pinfo = $result2['data'];

        //获得产品信息
        $parr['pcode'] = $pinfo['c_pcode'];
        $parr['actsign'] = 1;
        $result3 = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $product = $result3['data'];

        $a['pinfo'] =$pinfo;   //获取平拼团详情数据
        $a['groupinfo']= $groupinfo;//==null?"{"."}":$groupinfo;  //获取参团信息
        $a['product'] =$product; //获取产品信息

        $this->ajaxReturn(MessageInfo(0,'查询成功',$a));
    }


    //我的参团记录
    public function MyJoinGroup()
    {
        $parr['type'] = I('statu');
        $parr['pagesize'] = 10;
        $parr['pageindex'] = I('pageindex');
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        if ($parr['type'] == 0) {  // 拼团
            $result = IGD('Groupbuy','Newact')->MyJoinGroup($parr);
        } else {   // 砍价
            $result = IGD('Bargain','Newact')->MyJoinBargin($parr);
        }
        $this->ajaxReturn($result);
    }


}
