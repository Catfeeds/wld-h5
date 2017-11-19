<?php

namespace Store\Controller;

use Base\Controller\BaseController;

/**
 * 加盟店模块
 */
class LeagshopController extends BaseController {

	//加盟店首页
    public function index()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('League','Store')->GetLeaderinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['pid'] =  $this->unioninfo['c_id'];
        $result = IGD('League','Store')->GetAlltj($parr);
        $this->count = $result['data'];

        $result = IGD('League','Store')->GetallNum($parr);
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
        $result = IGD('League','Store')->GetdataTally($parr);
        $this->ajaxReturn($result);
    }

    //收益详情
    public function income()
    {
        $this->federationid = I('federationid');

        $parr['federationid'] =  $this->federationid;        
        $result = IGD('League','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['ucode'] = $this->unioninfo['c_ucode'];
        $parr['federationid'] =  $this->federationid;
        $result = IGD('League','Store')->GetMydata($parr);
        $this->count = $result['data'];
        $this->display();
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

    //获取店铺收益详情
    public function GetdateLog()
    {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['federationid'] = I('federationid');
        $parr['time'] = I('time');      //按月时间格式2017-06-14
        $result = IGD('League','Store')->GetdateLog($parr);
        $this->ajaxReturn($result);
    }

    //邀请加盟
    public function invited()
    {
        $this->display();
    }

    //邀请加盟接口
    public function Confirmsubmit()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['pucode'] = $data['pucode'];
        $parr['shopcode'] = $data['shopcode'];
        $result = IGD('League','Store')->Confirmsubmit($parr);
        $this->ajaxReturn($result);
    }

    //获取用户信息
    public function UserInfo()
    {
        $phone = I('phone');
        $result = IGD('League','Store')->UserInfo($phone);
        $this->ajaxReturn($result);
    }

    //立即查看
    public function jcheck()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['askid'] = I('askid');

        //获取邀请详情
        $result = IGD('Ask','App')->GetAskInfo($parr);
        $this->data = $result['data'];
        $this->display();
    }

    //我的加盟店--首页
    public function myindex()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('League','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $this->federationid = $this->unioninfo['c_id'];

        $parr['federationid'] =  $this->unioninfo['c_id'];
        $result = IGD('League','Store')->GetMydata($parr);
        $this->count = $result['data'];
        $this->display();
    }

    //同意加盟
    public function AgreeInvita()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['pid'] = $data['pid'];
        $parr['name'] = $data['name'];
        $parr['shopcode'] = $data['shopcode'];
        $parr['acode'] = $data['acode'];
        $result = IGD('League','Store')->AgreeInvita($parr);
        $this->ajaxReturn($result);
    }

    //我的加盟店--关于
    public function myjoin()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('League','Store')->GetUserinfo($parr);
        $this->unioninfo = $result['data'];

        $parr['federationid'] =  $this->unioninfo['c_id'];
        $result = IGD('League','Store')->GetLeagueInfo($parr);
        $this->data = $result['data'];
        $this->display();
    }

}