<?php

namespace Store\Controller;

use Base\Controller\AuthController;

/**
 * 	实体店铺商品管理
 */
class EntitymapController extends AuthController {

	/**
     * 首页
     */
    public function index()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->show();
    }

    /**
     * 添加实体商家商品信息
     */
    public function addproduct()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->editnum = I("editnum");
        /*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);
        $this->category=$proresult['data'];
        if (IS_POST && session('time') == $_POST['time']) {
            $param['ucode'] = session('USER.ucode');
            $param['num'] = $_POST['mnum'];
            $param['price'] = $_POST['cprice'];
            $desc = $_POST['desc'];
            $qian=array(" ","　","\t","\n","\r");
            $hou=array("","","","","");
            $param['desc'] = str_replace($qian,$hou,$desc);
            $param['name'] = strtrim1($_POST['name']);
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
                    $param['imglist'][$key]['sign'] = 0;
                }
            }
            $param['freeprice'] = $_POST['freeprice'];
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;
            }
            $proinfo = IGD('Entity','Store');
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


   /**
    * / 商品编辑
    *
    */
    public function goods_edit()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $this->editnum = I("editnum");
        /*产品分类*/
        $protype = IGD('Common','Info');
        $proresult = $protype->GetCategory($parr);
        $this->category=$proresult['data'];

        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $proinfo = IGD('Entity','Store');
        $result = $proinfo->GetProductInfo($parr);

        $this->vo = $result['data'];
        //$this->ajaxReturn($result['data']);
        $this->mainarr = $result['data']['mainimgs'];
        $this->subarr = $result['data']['imglist'];

        if (IS_POST && session('time') == $_POST['time']) {
            $param['ucode'] = session('USER.ucode');
            $param['pcode'] = $_POST['pcode'];
            $param['name'] = $_POST['name'];
            $desc = $_POST['desc'];
            $qian=array(" ","　","\t","\n","\r");
            $hou=array("","","","","");
            $param['desc'] = str_replace($qian,$hou,$desc);
            $param['num'] = $_POST['mnum'];
            $param['price'] = $_POST['cprice'];
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
                        $param['imglist'][$i]['sign'] = 0;
                        $param['imglist'][$i]['img'] = str_replace(GetHost().'/', '', $_POST[$v]);
                        $i++;
                    } else {
                        if ($subimg[$h]) {
                            $param['imglist'][$i]['sign'] = 0;
                            $param['imglist'][$i]['img'] = $subimg[$h];
                            $i++;
                        }
                        $h++;
                    }

                }
            }
            $param['freeprice'] = $_POST['freeprice'];
            $param['categoryid'] = $_POST['categoryid'];
            if (empty($_POST['ishow'])) {
                $param['ishow'] = 2;
            } else {
                $param['ishow'] = 1;
            }

            if ($param['ishow'] ==2) {
                $where['c_pcode'] = $_POST['pcode'];
                $where['c_isdel']=2;
                $where['c_state']=array("neq",2);
                $act = M('Shopact_product')->where($where)->find();
                if(!empty($act)){
                    $this->error("该商品已参与活动，不能下架");die;
                }
            }

            $proinfo = IGD('Entity','Store');
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
        $this->display('addproduct');
    }

    /**
     * 商品删除
     */
    public function goods_delete()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $proinfo = IGD('Entity','Store');
        $result = $proinfo->DeleteProduct($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 商品上下架
     *
     */
    public function goods_out()
    {
        if(!session('USER.ucode')){
            $this->userlogin();die;
        }
        $ishow = I('ishow');
        if ($ishow == 1) {
            $ishow = 1;
        } else {
            $ishow = 2;
        }
        $parr['ishow'] = $ishow;
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $proinfo = IGD('Entity','Store');
        $result = $proinfo->showProduct($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 商品预览详情
     *
     */
    public function pdetail()
    {
        /*商品详情*/
        $parr['ucode'] = session('USER.ucode');
        $parr['pcode'] = I('pcode');
        $parr['ispreview'] = 1;//是否是预览 (0-否，1-是)
        $result = IGD('Productinfo','Store')->GetProduceInfo($parr);
        $this->data = $result['data'];

        /*商品评论*/
        $proparr['pcode'] = I('pcode');
        $proparr['pagesize'] = 1;
        $comment = IGD('Shop','Store');
        $comdata = $comment->GetScore($proparr);

        $this->commentcount = $comdata['data']['dataCount'];
        $this->proscore = $comdata['data']['list'][0];

        /*app端等于1*/
        $this->apptype = I('type');

        /*pucode推广人*/
        $this->pucode = I('pucode');

        $this->ucode = session('USER.ucode');

        $this->display("detail");
    }

    /**
     * 评论管理
     *
     */
    public function comment()
    {
        $this->pcode = I('pcode');
        $this->show();
    }

    /**
     * 获取商品列表
     */
    public function GetProduceList()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['gettype'] = I('gettype');
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Store','Store')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 获取商品评论列表数据
     */
    public function GetCommentList()
    {
        $parr['pcode'] = I('pcode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $comment = IGD('Shop','Store');
        $result = $comment->GetScore($parr);
        $this->ajaxReturn($result);
    }

}