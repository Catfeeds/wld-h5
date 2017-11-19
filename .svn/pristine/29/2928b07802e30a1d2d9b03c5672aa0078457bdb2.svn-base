<?php
namespace Shop\Controller;
use Think\Controller;
/**
 *  商品管理
 */
class ShopController extends BaseController{	
	
	// 商品添加
    public function index()
    {
        /*产品分类*/
        $protype = D('Common','Service');
        $proresult = $protype->GetCategory($parr);              
        $this->category=$proresult['data'];
        if (IS_POST && session('time') == $_POST['time']) {           
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['num'] = $_POST['mnum'][$key];
                $k = $key + 1;
                $modellist[$key]['price1'] = $_POST['mprice'][$k*3-3];
                $modellist[$key]['price2'] = $_POST['mprice'][$k*3-2];
                $modellist[$key]['price3'] = $_POST['mprice'][$k*3-1];
                $modellist[$key]['maxnum1'] = $_POST['maxnum'][$k*3-3];
                $modellist[$key]['maxnum2'] = $_POST['maxnum'][$k*3-2];
            }

            $imgresult = uploadimg('store');
            if ($imgresult['code'] != 0) {
                $this->error($imgresult['msg']);
            }                 
            $param['imglist'] = array_values($imgresult['data']);           

            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }

            $param['ucode'] = session('_SHOP_UCODE');            
            $param['modellist'] = $modellist;
            $param['desc'] = $_POST['desc'];         
            $param['name'] = $_POST['name'];
            $param['spread'] = $_POST['spread_proportion'];
            $param['rebate'] = $_POST['rebate_proportion'];
            $param['freeprice'] = $_POST['freeprice'];            
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;    
            }
            $proinfo = D('Business','Service');
            $result = $proinfo->AddProudct($param);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            session('time', null);
            $this->success($result['msg'],'producelist');die;
        } 

        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
    	$this->display();
    }

    // 商品编辑
    public function goods_edit()
    {
        /*产品分类*/
        $protype = D('Common','Service');
        $proresult = $protype->GetCategory($parr);              
        $this->category=$proresult['data']; 

        $parr['ucode'] = session('_SHOP_UCODE');  
        $parr['pcode'] = I('pcode');    
        $proinfo = D('Business','Service');
        $result = $proinfo->GetProductInfo($parr);
        // $this->ajaxReturn($result);
        $this->vo = $result['data'];
        if (IS_POST && session('time') == $_POST['time']) {
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['num'] = $_POST['mnum'][$key];
                $k = $key + 1;
                $modellist[$key]['price1'] = $_POST['mprice'][$k*3-3];
                $modellist[$key]['price2'] = $_POST['mprice'][$k*3-2];
                $modellist[$key]['price3'] = $_POST['mprice'][$k*3-1];
                $modellist[$key]['maxnum1'] = $_POST['maxnum'][$k*3-3];
                $modellist[$key]['maxnum2'] = $_POST['maxnum'][$k*3-2];
            }
            foreach (array_keys($_POST) as $k => $v) {                
                if (strpos($v,'img')  !== false) {
                    if (!empty($_POST[$v])) {
                        $imglist[] = str_replace(GetHost().'/', '', $_POST[$v]);
                    }
                }                
            }
          
            if (!empty($_FILES)) {
                 $imgresult = uploadimg('store');
                if ($imgresult['code'] != 0) {
                    $this->error($imgresult['msg']);
                } 
                if (count($imglist) > 0) {
                    $param['imglist'] = array_merge($imglist,array_values($imgresult['data']));
                } else {
                    $param['imglist'] = array_values($imgresult['data']);
                }
            } else {
                $param['imglist'] = $imglist;
            }                    

            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }
            $param['pcode'] = $_POST['pcode'];            
            $param['modellist'] = $modellist;
            $param['desc'] = $_POST['desc'];         
            $param['name'] = $_POST['name'];
            $param['spread'] = $_POST['spread_proportion'];
            $param['rebate'] = $_POST['rebate_proportion'];
            $param['freeprice'] = $_POST['freeprice'];            
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;    
            }
            $proinfo = D('Business','Service');
            $result = $proinfo->UpdateProductInfo($param);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            session('time', null);
            $this->success($result['msg'],'producelist');die;
        }

        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
    	$this->display('index');
    }

    // 商品删除
    public function goods_delete()
    {
    	$parr['ucode'] = session('_SHOP_UCODE');  
        $parr['pcode'] = I('pcode');    
        $proinfo = D('Business','Service');
        $result = $proinfo->DeleteProduct($parr);
        $this->ajaxReturn($result);
    }

    // 商品上下架
    public function goods_out()
    {
    	$ishow = I('ishow');
    	if ($ishow == 1) {
    		$ishow = 1;
    	} else {
    		$ishow = 2;
    	}
        $parr['ishow'] = $ishow; 
  		$parr['ucode'] = session('_SHOP_UCODE');  
        $parr['pcode'] = I('pcode');    
        $proinfo = D('Business','Service');
        $result = $proinfo->showProduct($parr);
        $this->ajaxReturn($result);
    }


	// 产品列表
	public function producelist()
	{
		$where['c_ucode'] = session('_SHOP_UCODE');
		
		$this->display();
	}

	public function GetProductList()
    {
    	$parr['ucode'] = session('_SHOP_UCODE');
    	$parr['pageindex'] = I('pageindex');
    	$parr['pagesize'] = 10;
    	$result = D('Business','Service')->GetProductList($parr);
    	$this->ajaxReturn($result);
    }

	// 产品详情
	public function detail()
	{
		$where['c_id'] = I('Id');
		$vo = M('Check_produce')->where($where)->order('c_addtime desc')->find();
		$imgwhere['c_pid'] = $vo['c_id'];
		$vo['mainimg'] = M('Check_img')->where($imgwhere)->order('c_id desc')->select();
		$this->assign('vo',$vo);
		$this->display();
	}
  	
  	//删除产品
  	public function deleteproduct()
  	{
  		$where['c_id'] = I('Id');
  		$result = M('Check_produce')->where($where)->delete();
  		$whereimg['c_pid'] = I('Id');
  		$result = M('Check_img')->where($whereimg)->delete();
  		if (!$result)  {
  			$this->ajaxReturn(Message(1000,'删除失败'));
  		}
  		$this->ajaxReturn(Message(0,'删除成功'));
  	}
  
}