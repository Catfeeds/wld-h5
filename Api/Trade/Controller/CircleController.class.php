<?php
namespace Trade\Controller;
use Base\Controller\BaseController;
/**
 * 商圈模块
 * @author 
 */
class CircleController extends BaseController {
    /**
    * 拉取头部商圈信息
    * @param  openid,province,city,provincecode,citycode
    */
	public function Getcircleinfo(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['longitude'] =I('longitude');//经度
        $parr['latitude'] =I('latitude'); //纬度

		$result = IGD('Circle','Trade')->Getcircleinfo($parr);
        $this->ajaxReturn($result);
	}

    /**
    * 切换省份所有省列表
    * @param  openid
    */
    public function Getprovinces(){
        $result = IGD('Circle','Trade')->Getprovinces();
        $this->ajaxReturn($result);
    }

    /**
    * 切换商圈页面所有数据
    * @param  openid,provincecode
    */
    public function Getcirclelist(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['provincecode'] = I('provincecode');

        $result = IGD('Circle','Trade')->Getcirclelist($parr);
        $this->ajaxReturn($result);
    }

   /**
    * 商圈地图商家数据
    * @param  openid,provincecode,citycode,longitude,latitude
    */
    public function Mapdata() {
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');

        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->Mapdata($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 商圈商家数据
    * @param  openid,pageindex,juli,longitude,latitude,gettype,provincecode,citycode
    */
    public function Merchant() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
       
        // $parr['juli'] = I('juli');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['gettype'] = I('gettype');//1最热，2最新，3最近
        
        $result = IGD('Circle','Trade')->Merchant($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 获取商圈用户签到信息
    * @param  openid,province,city,provincecode,citycode
    */
    public function Signinfo(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->Signinfo($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 商圈用户签到操作
    * @param  openid,provincecode,citycode
    */
    public function Usersign(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;        
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->Usersign($parr);
        $this->ajaxReturn($result);
    }

    /**
    * 获取商圈活动跑马灯数据
    * @param  provincecode,citycode
    */
    public function NewShopact(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;        
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        $result = IGD('Circle','Trade')->NewShopact($parr);
        $this->ajaxReturn($result);
    }

    //活动分享回调
    public function TurnCallback()
    {
        $parr['Id'] = I('Id');
        $result = IGD('Circle','Trade')->TurnCallback($parr);
        $this->ajaxReturn($result);
    }

}