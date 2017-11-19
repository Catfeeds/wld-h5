<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 连锁店模块
 */
class MultshopController extends BaseController {
	
    //首页
    public function index()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['pid'] =  $this->unioninfo['c_id'];
        $parr['datetype'] = 0;
        $result = IGD('Chain','Store')->GetToYes($parr);
        $this->count = $result['data'];
        $this->display();
    }

    //数据统计
    public function datacount()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['pid'] =  $this->unioninfo['c_id'];
        $parr['datetype'] = 0;
        $result = IGD('Chain','Store')->GetToYes($parr);
        $this->count = $result['data'];
        
        //查询分店数据
        $result = IGD('Chain','Store')->SelectSubbranch($parr);
        $this->data = $result['data'];
        $this->display();
    }

    //根据时间、店铺查询连锁店营收趋势
    public function GetdataTally()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pid'] = I('pid');
        $parr['federationid'] = I('federationid');  //(查询总店则为空)
        $parr['timetype'] = I('timetype');  //(1-过去7天,2-过去30天,3-按月份)
        $parr['time'] = I('time');      //按月时间格式2017-03
        $result = IGD('Chain','Store')->GetdataTally($parr);
        $this->ajaxReturn($result);
    }

    //根据月份查询连锁店各分店营收占比
    public function Getdataproportion()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pid'] = I('pid');
        $parr['time'] = I('time');      //按月时间格式2017-03
        if (empty($parr['time'])) {
            $parr['time'] = date('Y-m');
        }
        $result = IGD('Chain','Store')->Getdataproportion($parr);
        $this->ajaxReturn($result);
    }

    //按天统计数据
    public function GetDaysDate()
    {
        $parr['pid'] = I('pid');
        $parr['federationid'] = I('federationid');  //(查询总店则为空)
        $parr['datetime'] = I('datetime');      //格式2017-03-01
        $parr['datetype'] = I('datetype');      //(0-总收入，1-营收收入，2-跨界收入)
        $result = IGD('Chain','Store')->GetDaysDate($parr);
        $this->ajaxReturn(MessageInfo(0,'查询成功',$result));
    }

    //统计总收入
    public function GetdataCount()
    {
        $parr['pid'] = I('pid');
        $parr['acttype'] = I('acttype');      //1-营收总收入，2-跨界总收入
        $result = IGD('Chain','Store')->GetdataCount($parr);
        $this->ajaxReturn($result);
    }

    //跨界收益
    public function crossincome()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        //查询分店数据
        $parr['pid'] =  $this->unioninfo['c_id'];
        // $result = IGD('Chain','Store')->SelectSubbranch($parr);
        // $this->data = $result['data'];

        $parr['acttype'] = 2;      //1-营收总收入，2-跨界总收入
        $result = IGD('Chain','Store')->GetdataCount($parr);
        $this->zmoney = $result['data']['money'];

        $parr['datetype'] = 2;
        $result = IGD('Chain','Store')->GetToYes($parr);
        $this->count = $result['data'];
        $this->display();
    }

    //查询跨界收益列表
    public function Getdatakj()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pid'] = I('pid');
        $parr['federationid'] = I('federationid'); 
        $parr['datetime'] = I('datetime'); 
        $parr['pagesize'] = 20; 
        $parr['pageindex'] = I('pageindex'); 
        $result = IGD('Chain','Store')->Getdatakj($parr);
        $this->ajaxReturn($result);
    }

    //营收明细
    public function incomedetail()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        //查询分店数据
        $parr['pid'] =  $this->unioninfo['c_id'];
        $result = IGD('Chain','Store')->SelectSubbranch($parr);
        $this->data = $result['data'];

        $parr['acttype'] = 1;      //1-营收总收入，2-跨界总收入
        $result = IGD('Chain','Store')->GetdataCount($parr);
        $this->zmoney = $result['data']['money'];
        $this->display();
    }

    //查询营收明细列表
    public function Getdatays()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pid'] = I('pid');
        $parr['federationid'] = I('federationid'); 
        $parr['datetime'] = I('datetime'); 
        $parr['pagesize'] = 20; 
        $parr['pageindex'] = I('pageindex'); 
        $result = IGD('Chain','Store')->Getdatays($parr);
        $this->ajaxReturn($result);
    }

    //店铺会员
    public function shopmember()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['pid'] =  $this->unioninfo['c_id'];
        $result = IGD('Chain','Store')->Getdatamember($parr);
        $this->data = $result['data'];
        $this->display();
    }

    //店铺会员
    public function detailmember()
    { 
        $federationid = I('federationid'); 
        $parr['federationid'] =  $federationid; 
        $result = IGD('Chain','Store')->GetUserinfoByfedera($parr);
        $this->data = $result['data'];
        
        $this->apptype = get_device_type();

        $parr['ucode'] = $this->data['c_ucode'];
        $this->count = IGD('Chain','Store')->GetmyMembleCount($parr['ucode'],$federationid);
        
        $this->wxcount = IGD('Chain','Store')->GetWxmebleCount($parr);
        $this->display();
    }

    /**
     *会员管理，获取我的会员列表
     */
    public function GetmyMembleList()
    {
        $parr['ucode'] = I('ucode');
        $parr['federationid'] = I('federationid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Chain','Store')->GetMembleList($parr);
        $this->ajaxReturn($result);
    }
    /**
     * 查询微信绑定的临时会员
     * @param ucode,pageindex,pagesize
     */
    public function GetWxmebleList()
    {
        $parr['ucode'] = I('ucode');
        $parr['federationid'] = I('federationid');
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 20;
        $result = IGD('Chain','Store')->GetWxmebleList($parr);
        $this->ajaxReturn($result);
    }

    //分店信息
    public function branchshop()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['pid'] =  $this->unioninfo['c_id'];
        $result = IGD('Chain','Store')->Getshopmember($parr);
        $this->data = $result['data'];
        $this->display();
    }


    //我的连锁店--首页
    public function mindex()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->Shopinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['federationid'] =  $this->unioninfo['c_id'];
        $parr['datetype'] = 0;
        $result = IGD('Chain','Store')->GetShopToyes($parr);
        $this->count = $result['data'];
        $this->display();
    }

    //我的连锁店--跨界收益
    public function mcrossincome()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->Shopinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['federationid'] =  $this->unioninfo['c_id'];
        $parr['datetype'] = 2;      //1-营收总收入，2-跨界总收入
        $result = IGD('Chain','Store')->GetDaysDate($parr);
        $this->zmoney = $result;

        $parr['datetime'] = date('Y-m-d',strtotime('-1 day'));
        $result = IGD('Chain','Store')->GetDaysDate($parr);
        $this->zrmoney = $result;
        $this->display();
    }

    //我的连锁店--营收明细
    public function mincomedetail()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Chain','Store')->Shopinfo($parr);
        $this->unioninfo = $result['data'];

        //查询分店数据
        $parr['federationid'] =  $this->unioninfo['c_id'];
        $parr['datetype'] = 1;      //1-营收总收入，2-跨界总收入
        $result = IGD('Chain','Store')->GetDaysDate($parr);
        $this->zmoney = $result;
        $this->display();
    }



}