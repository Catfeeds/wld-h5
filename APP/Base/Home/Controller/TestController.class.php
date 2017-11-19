<?php

namespace Home\Controller;

use Think\Controller;


class TestController extends Controller {
    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //查询数据
    public function seledata()
    {
        dump(M('Users_msg')->count());
        $result = M('Users_msg_s')->order('c_id desc')->limit(10)->select();
        dump($result);
    }

    //一次插入5000条
    public function insertData()
    {
        $info = M('Users_msg')->order('c_id desc')->find();
        if ($info['c_id']) {
            $where['c_id'] = array("GT",$info['c_id']);
        }

        $data = M('Users_msg_f')->where($where)->limit(5000)->select();

        $k = 0;
        foreach ($data as $key => $value) {
            $result = M('Users_msg')->add($value);
            if ($result) {
                $k++;
            }
        }
        // dump($data);die;

        // $result = M('Users_msg')->addAll($data);
        dump($k);
    }

    public function jisuan()
    {
        $map['Id'] = 5;
        $data = FM('Test')->getPartitionTableName($map)->limit(1)->find();
        dump($data);
    }

    //改变数据库unionid
    public function PostAddmerchant()
    {
        $ucode = I('ucode');
        $type = I('type');

        $parr['ucode'] = $ucode;

        $where['c_ucode'] = $parr['ucode'];
        $upayinfo = M('Merchant')->where($where)->find();
        if (!$upayinfo) {
            $this->ajaxReturn(Message(3000,'资料不存在'));
        }

        if ($upayinfo['c_checknum'] >= 2) {
            $this->ajaxReturn(Message(3001,'资料已提交审核'));
        }

        if ($type == 1) {  //普通费率
            //提交商户普通费率资料
            $upayinfo['c_outmerchantid'] = 'mp'.time();     //生成平台商户号
            $result = IGD('Upay','Scanpay')->apiAddmerchant($upayinfo,C('UPAYRATE'),2);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }
            $this->ajaxReturn(Message(0,'提交成功'));
        } else if ($type == 2) {    //跨界
            //获取平台抽成比例      
            $result = IGD('Upay','Scanpay')->GetIndustryInfo($parr['ucode']);
            if (!$result) {
                $this->ajaxReturn(Message(3001,'行业信息不存在'));
            }

            if ($result['data']['c_isfixed1'] == 1) {
                $billRate = $result['c_scanpay_shoprake']*10;
            } else {
                $billRate = $result['c_online_shoprake']*10;
            }
            

            //提交随机商户
            $upayinfo['c_outmerchantid'] = 'mk'.time();     //生成平台商户号
            $result = IGD('Upay','Scanpay')->apiAddmerchant($upayinfo,$billRate,1);
            if ($result['code'] != 0) {
                $this->ajaxReturn($result);
            }
            $this->ajaxReturn(Message(0,'提交成功'));
        }

        $this->ajaxReturn(Message(1000,'没有提交'));
    }

    public function GetQiniuToken()
    {
       $longitude = '113.259931';
       $latitude = '41.685418';
       $getlocal = file_get_contents("http://api.map.baidu.com/cloudrgc/v1?location=".$latitude.",".$longitude."&geotable_id=135675&coord_type=bd09ll&ak=lIqrLulxigbplnce2Ol5IG46ePXX2KLS");
       $result = objarray_to_array(json_decode($getlocal));
       $this->ajaxReturn($result);
    }

    public function getiparea()
    {
        $Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
        $area = $Ip->getlocation(get_client_ip()); // 获取某个IP地址所在的位置
        dump($area);
    }

    function GetIpLookup($ip = ''){  
        if(empty($ip)){  
            $ip = get_client_ip();  
        }  
        $res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);  
        if(empty($res)){ return false; }  
        $jsonMatches = array();  
        preg_match('#\{.+?\}#', $res, $jsonMatches);  
        if(!isset($jsonMatches[0])){ return false; }  
        $json = json_decode($jsonMatches[0], true);  
        if(isset($json['ret']) && $json['ret'] == 1){  
            $json['ip'] = $ip;  
            unset($json['ret']);  
        }else{  
            return false;  
        }  
        dump($json);  
    } 

    //测试提交费率
    public function csapiPayconf()
    {
        $merchantId = '199530441887';
        $billRate = '100';
        $payTypeIdArr = array('542','543','10000181');       //微信app支付类型id
        $result = IGD('Upay','Scanpay')->csapiPayconf($merchantId,$billRate,$payTypeIdArr);
        dump($result);
    }

    //测试支付回调
    public function testhd()
    {
        $param['orderid'] = I('oid');
        $param['payrule'] = I('pr');
        $param['actualprice'] = I('fe');
        $param['thirdpartynum'] = I('toid');
        $param['upay'] = I('up');
        if (substr($param['orderid'],0,1) == 'l') {  //代理商城订单
            $result = IGD('Agorder', 'Order')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 'n') {   //扫码订单
            $result = IGD('Scanpay', 'Scanpay')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 't') {   //普通线下订单
            $result = IGD('Storeorder', 'Order')->PayOrder($param);
        } else if (substr($param['orderid'],0,1) == 'g') {  //拼团订单
            $result = IGD('Groupbuy', 'Newact')->PayOrder($param);
        }  else if (substr($param['orderid'],0,1) == 'm') {  //秒杀订单
            $result = IGD('Seckill', 'Newact')->PayOrder($param);
        }  else {   //普通线上订单
            $result = IGD('Order', 'Order')->PayOrder($param);
        }
        $this->ajaxReturn($result);
    }

    public function tyyyyy()
    {
        $mchidarr = explode(',', C('LINEMICH'));
        $mch_id = $mchidarr[rand(0,(count($mchidarr)-1))];
        dump($mch_id);
    }

    public function editaddress()
    {
        // $where['c_ucode'] = 'T10001';
        $where[] = array("c_address is not null and c_address<>''");
        $where[] = array("c_province is null");
        $list = M('User_local')->where($where)->limit(1000)->select();
        dump(count($list));

        $op = 0;
        foreach ($list as $key => $value) {
            
            /*拆分地址address1：分割省，市，区*/
            $prove = explode("省", $value['c_address']);
            $citye = explode("市", $prove[1]);
            $district = str_replace($prove[0].'省'.$citye[0].'市', '', $value['c_address']);
            $whereadd['b.region_name'] = array('like','%'.$citye[0].'%');
            $whereadd['b.region_type'] = 2;
            $whereadd['a.region_type'] = 3;
            $join = 'left join t_region as b on a.parent_id=b.region_id';
            $list = M('Region as a')->join($join)->where($whereadd)->field('a.*')->select();
            foreach ($list as $k => $v) {
                if (strpos($district,$v['region_name']) !== false) {
                    $newdistrict = $v['region_name'];
                }
            }
            $xsaddress = str_replace($newdistrict, '', $district);


            $opw['c_id'] = $value['c_id'];
            $save['c_province'] = $prove[0];
            $save['c_city'] = $citye[0];
            $save['c_county'] = $newdistrict;
            $result = M('User_local')->where($opw)->save($save);
            if ($result) {
                $op++;
            }
        }

        dump($op);
    }

    public function editcode()
    {
        // $where['region_type'] = 3;
        // $list = M('Region')->where($where)->select();
        // $op = 0;
        // foreach ($list as $key => $value) {
        //     $name = $value['region_name'];
        //     $bw['c_name'] = array("like", "$name%");
        //     $data = M('Region_b')->where($bw)->find();

        //     $opw['region_id'] = $value['region_id'];
        //     $save['c_upaycode'] = $data['c_code'];
        //     // dump($name);
        //     // dump($save['c_upaycode']);
        //     $result = M('Region')->where($opw)->save($save);
        //     if ($result) {
        //         $op++;
        //     }
        // }

        dump($op);
    }

    public function surlask()
    {
        dump(decrypt('lWxmk5s=',C('ENCRYPT_KEY')));

//         $url = '    
// app_id=2015052600090779&biz_content=%7B%22timeout_express%22%3A%2230m%22%2C%22seller_id%22%3A%22%22%2C%22product_code%22%3A%22QUICK_MSECURITY_PAY%22%2C%22total_amount%22%3A%220.02%22%2C%22subject%22%3A%221%22%2C%22body%22%3A%22%E6%88%91%E6%98%AF%E6%B5%8B%E8%AF%95%E6%95%B0%E6%8D%AE%22%2C%22out_trade_no%22%3A%22314VYGIAGG7ZOYY%22%7D&charset=utf-8&method=alipay.trade.app.pay&sign_type=RSA2&timestamp=2016-08-15%2012%3A12%3A15&version=1.0&sign=MsbylYkCzlfYLy9PeRwUUIg9nZPeN9SfXPNavUCroGKR5Kqvx0nEnd3eRmKxJuthNUx4ERCXe552EV9PfwexqW%2B1wbKOdYtDIb4%2B7PL3Pc94RZL0zKaWcaY3tSL89%2FuAVUsQuFqEJdhIukuKygrXucvejOUgTCfoUdwTi7z%2BZzQ%3D';
//         dump(urldecode($url));
    }

    public function starts()
    {
        $parr['ucode'] = 'wld1ad6a2c986a3223a';
        $parr['shop'] = 1;
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['pageindex'] = 1;
        $parr['version'] = I('version');
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        $Activity = IGD('Start', 'Newact');
        $result = $Activity->StartClick($parr);

        $this->ajaxReturn($result);
    }

    public function setsys()
    {
        // $a = array(
        //     'shownum' => '7',
        //     'redclick' => '1-10|15-30|50-80',
        //     'boxclick' => '1-10|15-30|50-80',
        //     'airclick' => '1-10|15-30|50-80',
        //     'boxrand' => '1-40|41-60|61-80',
        //     'airrand' => '1-40|41-60|61-80',
        //     'spandnum' => '3',
        // );

        $a = array(
            'shownum' => '7',
            'redclick' => '80%',
            'boxclick' => '50%',
            'airclick' => '50%',
            'boxrand' => '1-95|96-98|99-100',
            'airrand' => '1-95|96-98|99-100',
            'spandnum' => '3',
        );

        $result = IGD('Redis','Redis')->Sethash('newact',$a);
        $this->ajaxReturn($result);
    }

    public function tsaddress()
    {
        $parentid = 1;
        $province = $this->GetRegion($parentid);
        foreach ($province as $key => $value) {
            $area0[$value['region_id']] = $value['region_name'];
            //查询市
            $city = $this->GetRegion($value['region_id']);
            foreach ($city as $key1 => $value1) {
                $area1[$value['region_id']][$key1] = array($value1['region_name'],$value1['region_id']);
                $region = $this->GetRegion($value1['region_id']);
                foreach ($region as $key2 => $value2) {
                    $area2[$value1['region_id']][$key2] = array($value2['region_name'],$value2['region_id']);
                }
            }
        }

        $cityarr['area0'] = $area0;
        $cityarr['area1'] = $area1;
        $cityarr['area2'] = $area2;
        $strjson = json_encode($cityarr);
        file_put_contents('address1.json', $strjson);
        dump($strjson);
    }

    /*获取省市区*/
    public function GetRegion($parentid) {
        $where['parent_id'] = $parentid;
        $field = 'region_id,parent_id,region_name,region_type';
        $order = 'region_id asc';
        $list = M('region')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    public function address2()
    {
        $data[0]['provinceName'] = '不限';
        $data[0]['provinceld'] = 0;
        $data[0]['cities'][0]['cityName'] = '不限';
        $data[0]['cities'][0]['cityId'] = 0;
        $data[0]['cities'][0]['counties'][0]['countyName'] = '不限';
        $data[0]['cities'][0]['counties'][0]['countyId'] = 0;

        $parentid = 1;
        $province = $this->GetRegion($parentid);
        foreach ($province as $key => $value) {
            $data[$key+1]['provinceName'] = $value['region_name'];
            $data[$key+1]['provinceld'] = $value['region_id'];
            //查询市
            $city = $this->GetRegion($value['region_id']);
            foreach ($city as $key1 => $value1) {
                $data[$key+1]['cities'][$key1]['cityName'] = $value1['region_name'];
                $data[$key+1]['cities'][$key1]['cityId'] = $value1['region_id'];
                $region = $this->GetRegion($value1['region_id']);
                foreach ($region as $key2 => $value2) {
                    $area2[$value1['region_id']][$value2['region_id']] = $value2['region_name'];
                    $data[$key+1]['cities'][$key1]['counties'][$key2]['countyName'] = $value2['region_name'];
                    $data[$key+1]['cities'][$key1]['counties'][$key2]['countyId'] = $value2['region_id'];
                }
            }
        }

        $strjson = json_encode($data);
        file_put_contents('address.json', $strjson);
        dump($strjson);
    }
        
}