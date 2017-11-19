<?php
namespace Balance\Controller;
use Base\Controller\BaseController;
/**
 * 结算中心
 * @author 
 */
class BalanceController extends BaseController {
    //查询收支记录
    public function GetMoneyLog() {
        $key = I('openid');
        $parr['ucode'] = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['type'] = I('type');
        $parr['source'] = I('source');
        $parr['c_time'] = I('c_time');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Settlement','User')->GetMoneyLog($parr);
        $this->ajaxReturn($result);
    } 
    
    // 查询类型列表
    public function MoneyType(){
        $result= array(
            'code'=>'0','msg'=>'查询成功',
            'list'=>array(
                array('c_key'=>'0',
                       'c_desc' =>'扫码'
                    ),
                array('c_key'=>'1',
                       'c_desc' =>'线上订单'
                    ),
                array('c_key'=>'2',
                       'c_desc' =>'红包'
                    ),
                array('c_key'=>'3',
                       'c_desc' =>'提现'
                    ),
                array('c_key'=>'4',
                       'c_desc' =>'跨界'
                    ),
                array('c_key'=>'5',
                       'c_desc' =>'其他'
                    )
            )); 
        $this->ajaxReturn($result);
    }

}