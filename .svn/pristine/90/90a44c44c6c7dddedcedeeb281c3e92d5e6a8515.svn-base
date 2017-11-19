<?php

namespace Order\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 商家订单管理
 */
class StoreorderController extends BaseController {
	
  	// 首页 
    public function index()
    {        
    	$this->show();
    }

    // 订单列表
    public function orderlist()
    {
        //$this->apptype = I('type');
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }
        $this->statu = I('statu');
        $ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->show();
    }

    /*订单首页*/
    public function orderindex()
    {
        //$this->apptype = I('type');
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }
        $parr['acode'] = session('USER.ucode');
        $proinfo = IGD('Order','Order');
        $result = $proinfo->orderCountInfo($parr);
        $this->data = $result['data'];

        $ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->info=$result['data'];
        $this->show();
    }

    /*修改运费*/
    public function freight()
    {
        if (IS_AJAX) {
            $parr['ucode'] = session('USER.ucode');
            $parr['orderid'] = I('orderid');
            $parr['free'] = I('free');
            $orderinfo = IGD('Order','Order');
            $result = $orderinfo->SaveFreeMoney($parr);            
            $this->ajaxReturn($result);
        }
        $this->free= I('free')<=0?'0.00':I('free');
        $this->orderid = I('orderid');
        //$this->apptype = I('type');
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }
        $this->show();
    }

    // 获取订单列表
    public function GetOrderList()
    {   
        $parr['acode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['pageindex'] = I('pageindex');
        $parr['flag'] = I('flag');
        $parr['keys'] = I('keys');
        $parr['pagesize'] = 10;
        $result = IGD('Order','Order')->GetOrderList($parr);
        $this->ajaxReturn($result);
    }

	
    // 获取维权列表
    public function Getrefundlist()
    {
        $parr['acode'] = session('USER.ucode');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Refund','Order')->Getrefundlist($parr);
        $this->ajaxReturn($result);
    }

    // 订单详情
    public function detail() 
    {
        $parr['orderid'] = I('orderid');
        $parr['acode'] = session('USER.ucode');
        $result = IGD('Order','Order')->GetOrderInfo($parr);
        $this->assign('data',$result['data']);
        //$apptype = I('type');
        //if (empty($apptype)) {
            if (is_weixin()) {
                $this->apptype = 4;
            } else {
                $this->apptype = get_device_type();
            }
        //} else {
           // $this->apptype = $apptype;
        //}
        $this->show();
    }

    //订单发货
    public function deliver() {
        if (IS_AJAX) {
            $parr['orderid'] = I('orderid');
            $parr['expressname'] = I('expressname');
            $parr['expressnum'] = I('expressnum');
            $result = IGD('Order','Order')->Senddelivery($parr);
            $this->ajaxReturn($result); 
        } 
        $parr['orderid'] = I('orderid');
        $parr['acode'] = session('USER.ucode');
        $result = IGD('Order','Order')->GetOrderInfo($parr);
        $this->assign('data',$result['data']); 
        /*快递公司查询*/	
        $expinfo = IGD('Express','Info')->Companys();
        $this->expinfo = $expinfo['data'];

        $this->show();
    }

	/*到店自提确认发货，确认收货*/
	public function delivery(){
        $parr['orderid'] = I('orderid');
        $result = IGD('Order','Order')->delivery($parr);
        $this->ajaxReturn($result);
	}

    // 售后详情
    public function warranty_info() 
    {
        $parr['acode'] = session('USER.ucode');
        $parr['rcode'] = I('rcode');
        $result = IGD('Refund','Order')->GetrefundInfo($parr);
        $data = $result['data'];  
        $this->subtime = strtotime('+1 weeks',strtotime($data['c_addtime'])) - time();
        $this->assign('data',$data);
        //$this->apptype = I('type');
        if (is_weixin()) {
            $this->apptype = 4;
        } else {
            $this->apptype = get_device_type();
        }
        $this->show();
    }

    //协商详情
    public function confer_info() 
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['rcode'] = I('rcode');
        $result = IGD('Refund','Order')->Getrefundlog($parr);
        $this->assign('data',$result['data']);
        $this->show();
    }

    // 同意退款退货操作
    public function AgreeRefund() 
    {
        $parr['addressid'] = I('addressid');
        $parr['rcode'] = I('rcode');
        $result = IGD('Refund','Order')->AgreeRefund($parr);
        $this->ajaxReturn($result);
    }

    // 不同意退款退货操作
    public function disagreeAgree() 
    {
        $parr['rcode'] = I('rcode');
        $result = IGD('Refund','Order')->disagreeAgree($parr);
        $this->ajaxReturn($result);
    }

    // 买家确认收货
    public function Refundreturn()
    {
        $parr['rcode'] = I('rcode');
        $result = IGD('Refund','Order')->Refundreturn($parr);
        $this->ajaxReturn($result);
    }

    //查询物流
    public function logitics() { 
        $orderid = I('orderid'); 
        $orderparr['orderid'] = $orderid;
        $orderinfo = IGD('Storeorder', 'Order')->GetPayorderinfo($orderparr);
        $orderdata = $orderinfo['data'];
        $parr['expressName'] = trim($orderdata['c_expressname']);
        $parr['expressId'] = trim($orderdata['c_expressnum']);
        $result = IGD('Express', 'Info')->GetQuery($parr);        
        if ($result['code'] == 0) {
            $logisticsDetails = $result['data'];

            $tag = $logisticsDetails['ischeck'];
            if ($tag == 1) {
                $state = "已收件";
            } else {
                $state = "未收件";
            }

            $listDetail = $logisticsDetails['list'];

        }

        $this->assign('tag', $tag);
        $this->assign('state', $state);
        $this->assign('list', $listDetail);
        $this->assign('orderid', $orderid);
        $this->assign('transcompany', $parr['expressName']);
        $this->assign('transid', $parr['expressId']);
        $this->display();
    }    
    
	/**
	* 到店自提-hhs-2017-08-24
	* 活动(拼团)订单列表
	*/ 
	public function actgoodslist(){		
		$this->statu = I('statu');
		$this->flag = I('flag');
	  	$this->display();
	}
	
	/**
	 * 搜索提货码
	 */	    
	public function ordersearch(){		
		$this->statu = I('statu');
		$this->flag = I('flag');
	  	$this->display();
	}
}