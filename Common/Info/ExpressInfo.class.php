<?php

/* 快递记录查询
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ExpressInfo {
    //枚举物流公司名称
    public function Companys(){
        $company = array('优速快递','汇通快递','申通快递','韵达快递','中通快递','圆通快递','天天快递','顺丰快递','德邦快递','快捷速递','百世快递','万象物流','安能小包','全峰快递','邦送物流','大田物流','国通快递','华宇物流','佳吉快运','佳怡物流','龙邦速递','联邦快递','共速达','联昊通','全日通','宅急送','能达速递','全一快递','速尔快递','TNT快递','中铁快运','中邮物流','新蛋物流','如风达快递','中国邮政EMS');
        return MessageInfo(0,'查询成功',$company);
    }

    //快递100 查询物流信息
    public function GetQuery($param) {
        $expressName = trim($param['expressName']);
        $expressid = trim($param['expressId']);

        $companys = $this->ExpressCompany();
        $expressNametemp = array_search($expressName, $companys);

        $url = "http://m.kuaidi100.com/query?type=" . $expressNametemp . "&postid=" . $expressid . "&id=1&valicode=&temp=" . time();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $output = curl_exec($ch);

        if ($output == "") {
            return Message(1001, "查询失败");
        }

        $info = (Array) json_decode($output);
        $message= $info['message'];
        if($message !="ok"){
            return  Message(1002, "快递公司参数异常：单号不存在或者已经过期");
        }
        $result['nu'] = $info['nu'];
        $result['companytype'] = $info['companytype'];
        $result['com'] = $info['com'];
        $result['updatetime'] = $info['updatetime'];
        $result['ischeck'] = $info['ischeck'];
        $data = (Array) $info['data'];

        for ($i = 0; $i < count($data); $i++) {
            $infodata = (Array) $data[$i];
            $datainfo[$i]['time'] = $infodata['time'];
            $datainfo[$i]['context'] = $infodata['context'];
            $datainfo[$i]['ftime'] = $infodata['ftime'];
        }

        $result['list'] = $datainfo;
        return MessageInfo(0, "查询成功", $result);
    }

    //快递公司与快递查询名称对应
    public function ExpressCompany(){
        $name_arr = array(
            'shentong' => '申通快递',
            'youshuwuliu' => '优速快递',
            'huitong' => '汇通快递',
            'yunda' => '韵达快递',
            'zhongtong' => '中通快递',
            'yuantong' => '圆通快递',
            'tiantian' => '天天快递',
            'shunfeng' => '顺丰快递',
            'ems' => '中国邮政EMS',
            'debangwuliu' => '德邦快递',
            'kuaijiesudi' => '快捷快递',
            'rufengda' => '如风达快递',
            'quanfengkuaidi' => '全峰快递',
            'zhaijisong' => '宅急送',
            'anxindakuaixi' => '安信达',
            'youzhengguonei' => '包裹平邮',
            'bangsongwuliu' => '邦送物流',
            'dhl' => 'DHL快递',
            'datianwuliu' => '大田物流',
            'guotongkuaidi' => '国通快递',
            'gongsuda' => '共速达',
            'tiandihuayu' => '华宇物流',
            'jiajiwuliu' => '佳吉快运',
            'jiayiwuliu' => '佳怡物流',
            'longbanwuliu' => '龙邦速递',
            'lianbangkuaidi' => '联邦快递',
            'lianhaowuliu' => '联昊通',
            'ganzhongnengda' => '能达速递',
            'quanyikuaidi' => '全一快递',
            'quanritongkuaidi' => '全日通',
            'suer' => '速尔快递',
            'tnt' => 'TNT快递',
            'neweggozzo' => '新蛋物流',
            'zhongtiewuliu' => '中铁快运',
            'zhongyouwuliu' => '中邮物流',
            'huitongkuaidi' => '百世快递',
            'annengwuliu' => '安能物流',
            'annengwuliu' => '安能小包',
			'wanxiangwuliu' => '万象物流',
        );

        return $name_arr;
    }

}
?>

