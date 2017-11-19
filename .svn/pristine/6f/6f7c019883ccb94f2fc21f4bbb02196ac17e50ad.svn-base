<?php 

namespace Home\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 用户地址操作模块
 */
class AddressController extends BaseController {
	
	// 获取省市区数据列表
    public function getRegion() {
        $parr['parentid'] = I('parentid');
        $parr['regiontype'] = I('regiontype');
        $region = IGD('User', 'User');
        $result = $region->GetAddress($parr);
        $this->ajaxReturn($result);
    }

    // 新增地址
    public function insertaddress() {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $parr['id'] = $data['id'];
        $parr['ucode'] = session('USER.ucode');
        $parr['consignee'] = $data['consignee'];
        $parr['mobile'] = $data['mobile'];
        if (!checkMobile($parr['mobile'])) {
            $this->ajaxReturn(Message(1002, "手机号码格式有误！"));
        }
        $parr['province'] = $data['province'];
        $parr['city'] = $data['city'];
        $parr['district'] = $data['district'];
        $parr['address'] = $data['address'];
        $parr['provincename'] = $data['provincename'];
        $parr['cityname'] = $data['cityname'];
        $parr['districtname'] = $data['districtname'];
        $parr['isdefault'] = $data['isdefault'];
        if (empty($data['id'])) {
            $region = IGD('User', 'User');
            $regioninfo= $region->UserAddress($parr);
        }
        else {
            $region = IGD('User', 'User');
            $regioninfo= $region->EditUserAddress($parr);
        }
        $this->ajaxReturn($regioninfo);
    }

    // 设置默认地址
    public function SetAddress() {
        $parr['id'] = I('id');
        $parr['ucode'] = session('USER.ucode');
        $address = IGD('User', 'User');
        $result = $address->SetAddress($parr);
        $this->ajaxReturn($result);
    }

    // 删除地址
    public function deleteUserAddress() {
        $parr['id'] = I('id');
        $parr['ucode'] = session('USER.ucode');
        $address = IGD('User', 'User');
        $result = $address->deleteUserAddress($parr);
        $this->ajaxReturn($result);
    }

    // 获取单个地址信息
    public function FindAddress() {
        $parr['id'] = I('id');
        $address = IGD('User', 'User');
        $result = $address->FindAddress($parr);
        $this->ajaxReturn($result);
    }

}

 ?>