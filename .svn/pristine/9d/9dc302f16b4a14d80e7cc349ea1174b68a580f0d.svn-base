<?php

namespace Home\Controller;

use Base\Controller\BaseController;

/**
 * 通用消息模块数据获取
 */
class InfoController extends BaseController {

	//商盟banner列表
    public function BannerList(){
        $parr['source'] = I('source'); //1-商城，2-小蜜商城
        $parr['tag'] = I('tag'); //终端标识 1-Web 2-APP

        $result = IGD('Common','Info')->get_banner($parr);
        $this->ajaxReturn($result);
    }

    // 查询品类
    public function GetCategory() {
        $Coalition = IGD('Common', 'Info');
        $result = $Coalition->GetCategory();
        $this->ajaxReturn($result);
    }

    // 查询标签列表
    public function GetLablist() {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 1000;
        $Coalition = IGD('Common', 'Info');
        $result = $Coalition->GetLablist($parr);
        $this->ajaxReturn($result);
    }

    //查询行业信息
    public function GetIndustry() {
        $Coalition = IGD('Common', 'Info');
        $result = $Coalition->GetIndustry();
        $this->ajaxReturn($result);
    }

    public function GetAllRegion() {
        $type = I('type');
        $Coalition = IGD('Common', 'Info');
        $result = $Coalition->GetRegion1($type);
        $this->ajaxReturn($result);
    }

    //获取新闻更多
    public function Morenews(){
    	$parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $result = IGD('Index','App')->NewsList($parr);
        $this->ajaxReturn($result);
    }

    //获取新闻详情
    public function Newsdetails() {
        $Id = I('id');
        $result = IGD('Index','App')->Getdetails($Id);
        $this->ajaxReturn($result);
    }

    //获取物流公司名称
    public function Getcompanys(){
        $result = IGD('Express','Info')->Companys();
        $this->ajaxReturn($result);
    }

    //商品物流信息
    public function Getexpress() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['expressName'] = I('expressname');
        $parr['expressId'] = I('expressnum');
        $Express = IGD('Express', 'Info');
        $result = $Express->GetQuery($parr);
        $this->ajaxReturn($result);
    }

}