<?php

namespace Agency\Controller;

// use Think\Controller;
use Base\Controller\BaseController;

/**
 * 微商管理系统代理包管理
 */
class BagsController extends BaseController {
	
	//代理包首页
    public function index(){
    	$this->ucode = session('USER.ucode');
    	$this->show();
    }

    //获取代理包列表
    public function GetpackageList()
    {
    	$parr['ucode'] = session('USER.ucode');
    	$parr['pageindex'] = I('pageindex');
    	$parr['pagesize'] = 10;
    	$result = IGD('Agency', 'Store')->AgencyBag($parr);
        $this->ajaxReturn($result);
    }

    //添加代理包页面
    public function bagsadd()
    {
    	$this->webtit = '代理包添加';
    	$this->show();
    }

    //编辑代理包页面
    public function bagsedit()
    {
    	$this->pid = I('pid');
    	$this->webtit = '代理包编辑';

        //查询代理包详情
        $parr['ucode'] = session('USER.ucode');
        $parr['bag_code'] = $this->pid;
        $result = IGD('Agency', 'Store')->GetOneBagsInfo($parr);
        $this->data = $result['data'];
    	$this->display('bagsadd');
    }

    //操作代理包
    public function saveBagsInfo()
    {
    	$attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

    	$parr['ucode'] = session('USER.ucode');

        $parr['bag_code'] = $data['pid'];//编辑时传的参数
        $parr['bag_name'] = $data['name'];
        $parr['bag_desc'] = $data['desc'];
        $parr['status'] = $data['state'];

        //循环所有图片
        foreach (array_keys($data) as $k => $v) {
            if (strpos($v,'imglist_')  !== false) {
                if (!empty($data[$v])) {
                    $imgstr = str_replace(GetHost().'/', '', $data[$v]);
                    if (in_array($imgstr, $storeinfo['imglist'])) {
                        $imglist[] = $imgstr;
                    } else {
                        $imglist[] = copyFileToDIr($imgstr,'agencybag')['data'];
                    }
                }
            }
        }

        if (count($imglist) <= 0) {
            $this->ajaxReturn(Message(3002,'图片上传失败！'));
        }

        $parr['imglist'] = $imglist;

        $result = IGD('Agency', 'Store')->AddAgencyBag($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //代理包管理上下架
    public function BagStatus(){
        $parr['ucode'] = session('USER.ucode');
        $parr['bag_code'] = I('bag_code');
        $parr['operate'] = I('operate');//operate(1-上架，2-下架)
        $result = IGD('Agency', 'Store')->BagStatus($parr);
        $this->ajaxReturn($result);
    }

    //商品管理页面
    public function goods()
    {
        $this->pid = I('pid');
    	$this->show();
    }

    //产品列表
    public function AgencyBagProduct(){
        $parr['ucode'] = session('USER.ucode');

        $parr['bag_code'] = I('bag_code');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Agency', 'Store')->AgencyBagProduct($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //代理包 产品添加选择商品
    public function ProductList(){
        $parr['ucode'] = session('USER.ucode');

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Agency', 'Store')->ProductList($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //代理包内产品上下架
    public function BagProductStatus(){
        $parr['ucode'] = session('USER.ucode');

        $parr['bag_code'] = I('bag_code');
        $parr['pcode'] = I('pcode');
        $parr['status'] = I('status');

        $result = IGD('Agency', 'Store')->BagProductStatus($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //代理包内产品删除
    public function BagProductDel(){
        $parr['ucode'] = session('USER.ucode');

        $parr['bag_code'] = I('bag_code');
        $parr['pcode'] = I('pcode');

        $result = IGD('Agency', 'Store')->BagProductDel($parr);
        $this->ajaxReturn($result);
    }

    //添加商品页面
    public function goodsadd()
    {
        $this->pid = I('pid');
    	$this->show();
    }

    //设置折扣
    public function discount()
    {
        $this->ucode = session('USER.ucode');
        $this->pid = I('pid');
        //等级信息
        $this->pcode = I('pcode');
        $parr['ucode'] = $this->ucode;
        $result = IGD('Agency', 'Store')->AgencyGrade($parr);
        $this->data = $result['data'];

        //产品信息
        $parr['pcode'] = $this->pcode;
        $parr['bag_code'] = $this->pid;
        $result = IGD('Agency', 'Store')->GetOneProInfo($parr);
        $this->pdata = $result['data'];
        $this->show();
    }

    //保存折扣比例 代理包 产品添加编辑
    public function AddBagProduct(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $parr['ucode'] = session('USER.ucode');

        $parr['pcode'] = $data['pcode'];
        $parr['bag_code'] = $data['bag_code'];
        $parr['status'] = $data['state'];

        for ($i=0; $i < 5; $i++) {
            $mode = array();
            if (!empty($data['grade'.$i])) {
                $mode['grade_name'] = $data['grade_name'.$i];
                $mode['grade'] = $data['grade'.$i];
                $mode['discount'] = $data['discount'.$i];
                if ($mode['discount'] <= 0) {
                    $this->ajaxReturn(Message(3000,'请完善等级对应的折扣比例'));
                }
                $modelist[] = $mode;
            }
        }

        $parr['gradelist'] = $modelist;
        $result = IGD('Agency', 'Store')->AddBagProduct($parr);
        $this->ajaxReturn($result);
    }
    
    //预览详情
    public function detail()
    {
    	$this->pcode = I('pcode');
        $this->ucode = session('USER.ucode');
        //获得产品信息
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = $this->pcode;
        $parr['isagent'] = 1;
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];
        
        //分享信息
        $wxshare = new \Wxshare(C('APPID'), C('APPSECRET'));  
        $signPackage = $wxshare->GetSignPackage();  
        $signPackage['url'] = $this->data['share_url'];   
        $this->assign('signPackage',$signPackage);
        $weixinshare["c_pthumbnail"] = $this->data['share_img'];    
        $weixinshare["c_sharetitle"] = $this->data['share_title'];
        $weixinshare["c_discript"] = $this->data['share_desc'];
        $this->assign('weixinshare',$weixinshare);      
        $this->show();
    }

}