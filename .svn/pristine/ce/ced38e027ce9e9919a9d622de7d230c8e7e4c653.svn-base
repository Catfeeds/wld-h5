<?php
namespace Newact\Controller;
use Base\Controller\CheckController;
/**
 * 商圈模块
 * @author 
 */
class AdvertController extends CheckController {
    /**
    * 推广位首页头部
    * @param  openid
    */
	public function AdvertHead(){
        $parr['ucode'] = $this->ucode;

		$result = IGD('Advert','Newact')->AdvertHead($parr);
        $this->ajaxReturn($result);
	}

    /**
    * 推广物料列表
    * @param  openid,pageindex,gettype （1-包括不可投放的 2-可投放的）
    */
    public function CardList(){
        $parr['ucode'] = $this->ucode;

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $parr['gettype'] = 1;

        $result = IGD('Advert','Newact')->CardList($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 选择活动卡券
    * @param  openid,pageindex
    */
    public function SelectCoupon(){
        $parr['ucode'] = $this->ucode;

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Advert','Newact')->SelectCoupon($parr);
        $this->ajaxReturn($result);
    }

   /**
    * 创建推广物料
    * @param  openid,pid,num
    */
    public function SetupCard() {
        $parr['ucode'] = $this->ucode;

        $parr['pid'] = I('pid');
        $parr['num'] = I('num');

        $result = IGD('Advert','Newact')->SetupCard($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 广告牌详情 基本信息
    * @param cardid
    */
    public function CardInfo() {
        $parr['cardid'] = I('cardid');
        
        $result = IGD('Advert','Newact')->CardInfo($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 广告牌详情 投放记录
    * @param  cardid,pageindex
    */
    public function CardGetList(){
        $parr['cardid'] = I('cardid');
        
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Advert','Newact')->CardGetList($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 广告牌详情 使用范围
    * @param  cardid
    */
    public function CardUsePro(){
        $parr['cardid'] = I('cardid');
       
        $result = IGD('Advert','Newact')->CardUsePro($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 广告牌详情 广告撤回
     * @param cardid
     */
    public function AdvertRecall(){
        $parr['cardid'] = I('cardid');
       
        $result = IGD('Advert','Newact')->AdvertRecall($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 广告牌详情 广告牌删除
     * @param cardid
     */
    public function CardDel(){
        $parr['cardid'] = I('cardid');
       
        $result = IGD('Advert','Newact')->CardDel($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 广告位数量列表
     * @param province,city,shoptrade,condition(搜索条件)
     */
    public function AdvertSite(){
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['shoptrade'] = I('shoptrade');
        $parr['condition'] = I('condition');
       
        $result = IGD('Advert','Newact')->AdvertSite($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 指定商家广告位查询
     * @param acode
     */
    public function UserAdevert(){
        $parr['acode'] = I('acode');
       
        $result = IGD('Advert','Newact')->UserAdevert($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 投放广告
     * @param acode,cardid,type,order
     */
    public function PutinAdevert(){
        $parr['acode'] = I('acode');
        $parr['cardid'] = I('cardid');
        $parr['type'] = I('type');
        $parr['order'] = I('order');
       
        $result = IGD('Advert','Newact')->PutinAdevert($parr);
        $this->ajaxReturn($result);
    }
}