<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 	实体店铺接口
 */
class NewController extends BaseController {


    //获取产品列表
    public function GetSelectProList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['pageindex'] =I('pageindex');
        $result =IGD('New','Store')->selectProduct($parr);
        $this->ajaxReturn($result);
    }

    //添加商品
    public function addProduct(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['free'] =I('free'); //邮费
        $parr['num'] = I('num'); //库存
        $parr['arr'] =I('arr');
        $result =IGD('New','Store')->addProduct($parr);
        $this->ajaxReturn($result);
    }

    //编辑店铺信息

    public function editStore(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['acode'] =I('acode'); //店铺code
        $parr['desc'] =I('desc');
        $parr['tel'] =I('tel');
        $result =IGD('New','Store')->EditStore($parr);
        $this->ajaxReturn($result);
    }


    //获取精品商品列表
    public function getProList(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['acode'] =I('acode');
        $result =IGD('New','Store')->getProList($parr);
        $this->ajaxReturn($result);
    }





    //查询店铺编辑信息
    public function GetStoreInfo()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['storeid'] = I('storeid');
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $result = IGD('Store','Store')->GetStoreInfo($parr);
        $data = $result['data'];

        //获取店铺头部信息
        $params['perucode'] = I('acode');
        $params['ucode'] = $ucode;
        $result = IGD('Resource','Trade')->PersonalShop($params);
        $data['c_attention'] = $result['data']['c_attention'];
        $data['c_pv'] = $result['data']['c_pv'];
        $this->ajaxReturn(MessageInfo(0,'查询成功',$data));
    }
    
    //获取所有展示图片列表
    public function get_imglist()
    {
        $storeid = I('storeid');
        $result = IGD('Store','Store')->get_imglist($storeid,4);
        $this->ajaxReturn($result);
    }

    //查询全部服务项目
    public function GetServiceList()
    {
        $parr['storeid'] = I('storeid');
        $result = IGD('Store','Store')->GetServiceList($parr);
        $this->ajaxReturn($result);
    }

    //查询实体商家产品列表
    public function GetProduceList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['gettype'] = 1;
        $result = IGD('Store','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺评论列表
    public function GetCommentList()
    {
        $pagesize = I('pagesize');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = isset($pagesize)?10:$pagesize;
        $parr['acode'] = I('acode');
        $result = IGD('Store','Store')->GetCommentList($parr);
        $this->ajaxReturn($result);
    }

    // 线上商家编辑店铺资料
    function AddStoreInfoline()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['storeid'] = I('storeid');
        $parr['ucode'] = $ucode;
        $parr['name'] = I('name');
        $parr['desc'] = I('desc');
        if (empty($parr['name']) || empty($parr['desc'])) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }              

        //上传图片
        $result = uploadimg('offstore');
        if ($_FILES) {
            if ($result['code'] == 0) {
                $imglist = array_values($result['data']);
            }
            $parr['imglist'] = $imglist;            
        } else {
            if (empty($parr['storeid'])) {
                $this->ajaxReturn(Message(2000,'请上传图片'));
            }
        }        

        $result = IGD('Store','Store')->AddStoreInfoline($parr);
        $this->ajaxReturn($result);
    }

    //添加店铺信息 第一步
    public function Addstep1()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['storeid'] = I('storeid');
        $parr['ucode'] = $ucode;
        $parr['name'] = I('name');
        $parr['desc'] = I('desc');
        $parr['provice'] = I('provice');
        $parr['city'] = I('city');
        $parr['district'] = I('district');
        $parr['address'] = I('address');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        if (empty($parr['name']) || empty($parr['desc']) || empty($parr['address']) ||
            empty($parr['longitude']) || empty($parr['latitude']) || empty($parr['provice'])
            || empty($parr['district'])) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }

        $result = IGD('Store','Store')->AddStoreInfo1($parr);
        $this->ajaxReturn($result);
    }

    //添加店铺信息 第二步
    public function Addstep2()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = $ucode;

        //上传图片
        $result = uploadimg('offstore');
        $imglist = array_values($result['data']);
        $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
        $url = GetHost() . '/';
        foreach ($imglist1 as $key => $value) {
            if (!empty($value['c_img'])) {
                $imglist[] = str_replace($url, "", $value['c_img']);
            }
        }

        if (count($imglist) == 0) {
            $this->ajaxReturn(Message(2000,'请上传图片'));
        }
        if (count($imglist) > 12) {
            $this->ajaxReturn(Message(2001,'上传图片不能超过12张'));
        }

        $parr['imglist'] = $imglist;
        $result = IGD('Store','Store')->AddStoreInfo2($parr);
        $this->ajaxReturn($result);
    }

    //添加店铺信息 第三步
    public function Addstep3(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['storeid'] = I('storeid');
        $parr['ucode'] = $ucode;

        $parr['remind'] = I('remind');
        $parr['opentime'] = I('opentime');
        $sourcearr = I('sourcearr');
        if (empty($parr['remind']) || empty($parr['opentime'])) {
            $this->ajaxReturn(Message(2000,'请完善资料信息'));
        }
        $parr['sourcearr'] = explode('|', $sourcearr);
        $result = IGD('Store','Store')->AddStoreInfo3($parr);
        $this->ajaxReturn($result);
    }

    //获取店铺模板标识
    public function GetShopTpl()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $result = IGD('Store','Store')->GetShopTpl($parr);
        $this->ajaxReturn($result);
    }

    //选择店铺模板
    public function CheckShopTpl()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');
        $parr['ucode'] = $ucode;
        $parr['tplid'] = I('tplid');
        $result = IGD('Store','Store')->CheckShopTpl($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺模板列表
    public function TemplateList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');
        $parr['ucode'] = I('ucode');
        $result = IGD('Store','Store')->TemplateList($parr);
        $this->ajaxReturn($result);
    }

    //根据店铺模板Id获取模板内容
    public function GetShopTplContent()
    {
        $parr['tempid'] = I('tempid');
        $parr['tplid'] = I('tplid');
        $parr['isprew'] = I('isprew');
        
        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');   
        $result = IGD('Store','Store')->GetShopTplContent($parr);
        $this->ajaxReturn($result);
    }

    //查询分类
    public function getcategory(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;

        $parr['acode'] = I('acode');
        $result = IGD('Store','Store')->GetCategory($parr);
        $this->ajaxReturn($result);

    }

    //编辑头部图片
    public function addheaderimg(){
        $key = I('openid');
        $parr['tplid'] = I('tplid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['types'] = I('types');

        $result = uploadimg('offstore');
        if ($_FILES) {
            if ($result['code'] == 0) {
                $imglist = array_values($result['data']);
            }
            $parr['imglist'] = $imglist;            
        } else {
            if (empty($parr['storeid'])) {
                $this->ajaxReturn(Message(2000,'请上传图片'));
            }
        }      
        $result = IGD('Store','Store')->AddHeadImg($parr);
        $this->ajaxReturn($result);
    }

    //添加banner图片
    public function addbannerimg(){
        $key = I('openid');
        $parr['tplid'] = I('tplid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['types'] = I('types');
        $parr['sign'] = I('sign');
        
        $result = uploadimg('offstore');
        if ($_FILES) {
            if ($result['code'] == 0) {
                $imglist = array_values($result['data']);
            }
            $parr['imglist'] = $imglist;            
        } else {
            if (empty($parr['storeid'])) {
                $this->ajaxReturn(Message(2000,'请上传图片'));
            }
        }      
        
        $result = IGD('Store','Store')->AddBannerImg($parr);
        $this->ajaxReturn($result);
    }
    //删除图片
    public function deleteimg(){
        $id = I('id');
        $parr['id'] = $id;
        if (isset($id)) {
             $result = IGD('Store','Store')->DelImg($parr);
        }

        $this->ajaxReturn($result);
    }

    //应用模板
    public function ApplyModel(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $tplid = I('tplid');
        $parr['ucode'] = $ucode;
        $parr['tplid'] = $tplid;
        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');  
        $result = IGD('Store','Store')->ApplyModel($parr);
        $this->ajaxReturn($result);
    }

    //获取应用模板的内容
    public function GetApplyCenter(){
        $acode = I('ucode');
        $parr['acode'] = $acode;
        $parr['app_version']=I('app_version');
        $parr['app_client'] =I('app_client');  
        $result = IGD('Store','Store')->GetApplyCenter($parr);
        $this->ajaxReturn($result);
    }

    //查询店铺发放卡劵列表
    public function ShopCouponList()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $parr['pageindex'] = '1';
        $parr['type'] = '1';
        $parr['pagesize'] = 4;
        $result = IGD('Coupon','Newact')->ShopCouponList($parr);
        $this->ajaxReturn($result);
    }

    //店铺领取卡劵
    public function ReceiveShopCoupon()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['awid'] = I('id');   
        $result = IGD('Coupon','Newact')->ReceiveShopCoupon($parr);
        $this->ajaxReturn($result);
    }

    //获取红包信息
    public function ViewShopRed(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['acode'] = I('acode');
        $result = IGD('Red','Newact')->ViewShopRed($parr);
        $this->ajaxReturn($result);
    }
    
    //领取红包
    public function ReceiveRed()
    {
        // //新增操作写入分库记录
        // $db = M('');
        // $db->startTrans('1');

        // //新增临时写入统计表
        // $countwhere['c_sign'] = 1;
        // $countwhere['c_type'] = 4;
        // $countwhere['c_ucode'] = session('USER.ucode');
        // $countwhere['c_datetime'] = date('Y-m-d');
        // $countinfo = M('Users_moneydate')->where($countwhere)->find();
        // if (!$countinfo) {
        //     $countwhere['c_updatetime'] = date('Y-m-d H:i:s');
        //     $result = M('Users_moneydate')->add($countwhere);
        // }
        // $db->commit();
        
        $parr['awid'] = I('awid');
        $parr['sid'] = I('sid');
        $key = I('openid');
        $parr['app_client'] = I('app_client');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $result = IGD('Red','Newact')->ReceiveRed($parr);
        $this->ajaxReturn($result);
    }

}
