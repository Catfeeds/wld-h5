<?php

namespace Store\Controller;

use Base\Controller\BaseController;
/**
 * 商家店铺首页
 */
class IndexController extends BaseController {

    //店铺首页
    public function index()
    {
        dump(111);
    }

    //商家会员
    public function myupuser(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;

        $result = IGD('User','User')->Getmysup($parr);
        $this ->ajaxReturn($result);
    }

    //查询微商产品列表
    public function GetProduceList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['type'] = 1;
        $result = IGD('Shop','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    //获取产品信息
    public function GetProductInfo() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['pcode'] = I('pcode');
        $parr['type'] = I('type');
        $parr['ucode'] = $ucode;
        $parr['show'] = 1;
        //查询产品信息
        $Shop = IGD('Shop', 'Store');
        $result = $Shop->GetProduceInfo($parr);

        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        //查询用户信息
        $data = $result['data'];

        $ucode1 = $data['c_ucode'];
        $parr1['iucode'] = $ucode;
        $parr1['ucode'] = $ucode1;
        $User = IGD('UserProcess', 'Rongcloud');
        $result = $User->GetUserInfo($parr1);

        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        $nickname = $result['data']['c_nickname'];
        $data['c_nickname'] = $nickname;

        //添加商品访问记录
        $parr2['ucode'] = $ucode;
        $parr2['pcode'] = I('pcode');
        if(I('type') == 2){
            $parr2['source'] = 'Android';
        }else{
            $parr2['source'] = 'IOS';
        }
        $parr2['ip'] = GetIP();
        $w1['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
        $userinfo['c_headimg'] = GetHost().'/'. $userinfo['c_headimg'];

        $parr2['nickname'] = $userinfo['c_nickname'];
        $parr2['headimg'] = $userinfo['c_headimg'];

        $result = IGD('Resourcev2','Trade')->ProductVisit($parr2);

        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }

        $retrunresult = MessageInfo(0, "查询成功", $data);

        $this->ajaxReturn($retrunresult);
    }

    //获取商家广告位数据
    public function GetShopAdvert(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $parr['type'] = I('type');
        $result = IGD('Advert','Newact')->GetShopAdvert($parr);
        $this->ajaxReturn($result);
    }
}