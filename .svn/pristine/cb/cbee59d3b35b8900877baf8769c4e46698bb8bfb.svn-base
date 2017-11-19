<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 商家产品模块
 */
class ProductController extends BaseController {
	// 首页 
    public function index()
    {
        $ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        if ($result['data']['c_shop'] != 1) {
            $this->error("成为商家才能进入商品管理",U('Server/index'));
        }
        
    	$this->show();
    }

    public function GetProductList()
    {
    	$parr['ucode'] = session('USER.ucode');
    	$parr['pageindex'] = I('pageindex');
    	$parr['pagesize'] = 10;
    	$result = IGD('Shop','Store')->GetProduceList($parr);
    	$this->ajaxReturn($result);
    }

    // 商品详情
    public function pdetail()
    {
        $pcode = I('pcode');

        $this->pcode = $pcode;
        $this->ucode = session('USER.ucode');
        $this->pucode = I('pucode');
        //获得产品信息
        $parr['ucode'] = $this->ucode;
        $parr['pcode'] = I('pcode');
        $parr['ispreview'] = 1;//是否是预览 (0-否，1-是)
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];

        /*商品单条评论*/
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 1;
        $parr['pcode'] = $pcode;
        if(empty($parr['pageindex'])){
            $result = IGD('Agency', 'Store')->GetAllScore($parr);
        }else{
            $result = IGD('Agency', 'Store')->GetScore($parr);
        }
        $this->proscore = $result['data']['list'][0];
        $this->display("detail");
    }

    /**
     * 2016-9-18商品管理
     * 添加商品
     */
    public function addproduct()
    {
        $this->editnum = I("editnum");
        /*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);              
        $this->category=$proresult['data'];
        if (IS_POST && session('time') == $_POST['time']) {           
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['mprice'] = $_POST['cprice'][$key];
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
            foreach (array_keys($imgresult['data']) as $key => $value) {
                if (strpos($value,"main_img")!== false) {
                    $param['imglist'][$key]['img'] = array_values($imgresult['data'])[$key];
                    $param['imglist'][$key]['sign'] = 1;
                } else {
                    $param['imglist'][$key]['img'] = array_values($imgresult['data'])[$key];
                    $param['imglist'][$key]['sign'] = 2;
                }   
            }                     
            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }

            $param['ucode'] = session('USER.ucode');            
            $param['modellist'] = $modellist;
            $desc = $_POST['desc'];
            $qian=array(" ","　","\t","\n","\r");
            $hou=array("","","","","");
            $param['desc'] = str_replace($qian,$hou,$desc);
            $param['name'] = strtrim1($_POST['name']);
            // $param['spread'] = $_POST['spread_proportion'];
            // $param['rebate'] = $_POST['rebate_proportion'];
            $param['freeprice'] = $_POST['freeprice'];            
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;    
            }     
            //dump($param);die;                   
            $proinfo = IGD('Businessv2','Store');
            $result = $proinfo->AddProudct($param);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            session('time', null);
            $this->success($result['msg'],'/Store/Gmanage/index');die;
        } 

        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
        $this->display();
    }

    // 商品编辑
    public function goods_edit()
    {
        $this->editnum = I("editnum");
        /*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);              
        $this->category=$proresult['data']; 

        $parr['ucode'] = session('USER.ucode');  
        $parr['pcode'] = I('pcode');    
        $proinfo = IGD('Businessv2','Store');
        $result = $proinfo->GetProductInfoH5($parr);
        // $this->ajaxReturn($result);
        $this->vo = $result['data'];
        foreach ($this->vo['imglist'] as $key => $value) {
            if ($value['c_sign'] == 1) {
                $mainarr[] = $this->vo['imglist'][$key];
            } else {
                $subarr[] = $this->vo['imglist'][$key];
            }
        }
        $this->mainarr = $mainarr;
        $this->subarr = $subarr;

        if (IS_POST && session('time') == $_POST['time']) {
            foreach ($_POST['mcode'] as $key => $value) {
                $modellist[$key]['mcode'] = $value;
                $modellist[$key]['mname'] = $_POST['mname'][$key];
                $modellist[$key]['mprice'] = $_POST['cprice'][$key];
                $modellist[$key]['num'] = $_POST['mnum'][$key];
                $k = $key + 1;
                $modellist[$key]['price1'] = $_POST['mprice'][$k*3-3];
                $modellist[$key]['price2'] = $_POST['mprice'][$k*3-2];
                $modellist[$key]['price3'] = $_POST['mprice'][$k*3-1];
                $modellist[$key]['maxnum1'] = $_POST['maxnum'][$k*3-3];
                $modellist[$key]['maxnum2'] = $_POST['maxnum'][$k*3-2];
            }

            //判断有图上传图片
            if (!empty($_FILES)) {
                $imgresult = uploadimg('store');
                $mainimg = array();$subimg = array();
                foreach (array_keys($imgresult['data']) as $key => $value) {
                    if (strpos($value,"main_img")!== false) {
                        $mainimg[] = array_values($imgresult['data'])[$key];
                    } else {
                        $subimg[] = array_values($imgresult['data'])[$key];
                    }   
                } 
            }

            //循环所有图片
            $i = 0;$j = 0;$h = 0;
            foreach (array_keys($_POST) as $k => $v) {                
                if (strpos($v,'main_img')  !== false) {                    
                    if (!empty($_POST[$v])) {   
                        $param['imglist'][$i]['sign'] = 1;                     
                        $param['imglist'][$i]['img'] = str_replace(GetHost().'/', '', $_POST[$v]);
                        $i++;
                    } else {
                        if ($mainimg[$j]) {
                            $param['imglist'][$i]['sign'] = 1;
                            $param['imglist'][$i]['img'] = $mainimg[$j];
                            $i++;
                        }
                        $j++;
                    }
                } else  if (strpos($v,'sub_img')  !== false) {                    
                    if (!empty($_POST[$v])) {  
                        $param['imglist'][$i]['sign'] = 2;                      
                        $param['imglist'][$i]['img'] = str_replace(GetHost().'/', '', $_POST[$v]);
                        $i++;
                    } else {
                        if ($subimg[$h]) {
                            $param['imglist'][$i]['sign'] = 2;
                            $param['imglist'][$i]['img'] = $subimg[$h];
                            $i++;
                        }
                        $h++;
                    }                   
                    
                }                
            }
          

            if (count($modellist) == 0) {
                $this->error('至少添加一个型号');
            }
            $param['pcode'] = $_POST['pcode'];            
            $param['modellist'] = $modellist;
            $param['desc'] = $_POST['desc'];         
            $param['name'] = $_POST['name'];
            // $param['spread'] = $_POST['spread_proportion'];
            // $param['rebate'] = $_POST['rebate_proportion'];
            $param['freeprice'] = $_POST['freeprice'];            
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;    
            }


            if ($param['ishow'] == 2) {
                $where['c_pcode'] = $_POST['pcode'];
                $where['c_isdel']=2;
                $where['c_state']=array("neq",2);
                $act = M('Shopact_product')->where($where)->find();
                if(!empty($act)){
                    $this->error("该商品已参与活动，不能下架");die;
                }
            }

            $proinfo = IGD('Businessv2','Store');
            $result = $proinfo->UpdateProductInfo($param);
            if ($result['code'] != 0) {
                $this->error($result['msg']);
            }
            session('time', null);
            $this->success($result['msg'],'/Store/Gmanage/index');die;
        }

        $time = time();
        session('time', $time);    // 防止重复提交
        $this->assign('time', $time);
    	$this->display('addproduct');
    }

    // 商品删除
    public function goods_delete()
    {
    	$parr['ucode'] = session('USER.ucode');  
        $parr['pcode'] = I('pcode');    
        $proinfo = IGD('Businessv2','Store');
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
  		$parr['ucode'] = session('USER.ucode');  
        $parr['pcode'] = I('pcode');    
        $proinfo = IGD('Businessv2','Store');
        $result = $proinfo->showProduct($parr);
        $this->ajaxReturn($result);
    }

    // 评论管理首页
    public function comment()
    {
    	$this->pcode = I('pcode');    	
    	if (IS_AJAX) {
    		$parr['pcode'] = I('pcode');
	    	$parr['pageindex'] = I('pageindex');
	    	$parr['pagesize'] = 10;
			$comment = IGD('Shop','Store');
			$result = $comment->GetScore($parr);			
			$this->ajaxReturn($result);
    	}
    	$this->show();
    }
}