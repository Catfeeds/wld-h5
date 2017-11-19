<?php

/**
 * 相关风控管理
 */
class FundsInfo {

    //查询风控规则
    public function GetFundsRule($parr) {
    	$where['c_state'] = 1;
    	$where['c_ucode'] = array(array('exp','is null'),array('eq',''),'or');
		$data1 = M('Trade_setting')->where($where)->order('c_id desc')->find();

		$where['c_ucode'] = $parr['ucode'];
		$data2 = M('Trade_setting')->where($where)->order('c_id desc')->find();
        
        if (!empty($data2)) {
        	return MessageInfo(0, '查询成功',$data2);
        }
        return MessageInfo(0, '查询成功',$data1);
    }

     /**
     * 查询用户风控信息
     * @param ucode,sign
     */
    function GetUseFunds($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        if ($parr['sign'] == 2 || $parr['sign'] == 3 || $parr['sign'] == 4) {
        	$where['c_sign'] = array(array('eq',1),array('eq',$parr['sign']),'or');
        } else {
        	$where['c_sign'] = $parr['sign'];
        }
        
        $where['c_starttime'] = array('ELT', gdtime());
        $where['c_endtime'] = array('EGT', gdtime());
        $result = M('Trade_limit')->where($where)->find();
        if ($result) {
            return Message(4000,$result['c_remarks']);
        }
        return Message(0,'没有风控记录');
    }

}
