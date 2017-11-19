<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 	实体店铺商品管理
 */
class EntitymapController extends BaseController {

    //查询实体商家产品管理列表
    public function GetProduceList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['gettype'] = 0;
        $result = IGD('Store','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    //查询商品信息
    public function ViewProduct()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $result = IGD('Entity','Store')->GetProductInfo($parr);

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
        if(I('type') == 1){
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

    //编辑保存商品 第一步
    public function Addstep1()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['name'] = I('pname');
        $parr['desc'] = I('pdesc');
        if (empty($parr['name']) || empty($parr['desc'])) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }
        $result = IGD('Entity', 'Store')->AddProduceInfo1($parr);
        $this->ajaxReturn($result);
    }

    //编辑保存商品 第二步与第三步
    public function Addstep2()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        //上传图片
        $result = uploadimg('entity');
        $imglist = array_values($result['data']);
        $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
        $url = GetHost() . '/';
        foreach ($imglist1 as $key => $value) {
            if (!empty($value['c_pimgepath'])) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }

        if (count($imglist) == 0) {
            $this->ajaxReturn(Message(1000, "请上传商品对应图片"));
        }

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['sign'] = I('sign');   //1主图，0副图
        $parr['imglist'] = $imglist;
        $result = IGD('Entity', 'Store')->AddProduceInfo2($parr);
        $this->ajaxReturn($result);
    }

    //编辑保存商品 第四步
    public function Addstep4()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['price'] = I('price');
        $parr['num'] = I('num');
        if ($parr['price'] <= 0 || $parr['num'] <= 0) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }
        $result = IGD('Entity', 'Store')->AddProduceInfo4($parr);
        $this->ajaxReturn($result);
    }

    //编辑保存商品 第五步
    public function Addstep5()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $ishow = I('ishow');

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['categoryid'] = I('categoryid');
        $parr['freeprice'] = I('freeprice');
        $parr['ishow'] = ($ishow == 1)?1:2;
        if (empty($parr['categoryid'])) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }
        $result = IGD('Entity', 'Store')->AddProduceInfo5($parr);
        $this->ajaxReturn($result);
    }

    //删除产品
    public function DeleteProduct() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $result = IGD('Entity', 'Store')->DeleteProduct($parr);
        $this->ajaxReturn($result);
    }

    //产品上下架
    public function showProduct() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['ishow'] = I('ishow');
        $result = IGD('Entity', 'Store')->showProduct($parr);
        $this->ajaxReturn($result);
    }
}
