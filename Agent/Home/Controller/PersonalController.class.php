<?php
namespace Home\Controller;
use Think\Controller;
class PersonalController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	//登录页面
	public function index()
	{
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Agent','Service')->GetShopInfo($parr);

        /*判断：如果是获取的用户的名称则清空，输入真实姓名*/
        $needle = "小蜜";
        $namestr = explode($needle,$result['data']['c_name']);
        if(count($namestr)>1){
            $result['data']['c_name'] = "";
        }else{
            $result['data']['c_name'] = $result['data']['c_name'];
        }

		$this->assign('vo',$result['data']);
		$this->display();
	}

    // 添加与修改个人资料
    public function SaveAgentInfo()
    {
        $parr['ucode'] = session('_ADMIN_UCODE');
        $result = D('Agent','Service')->GetShopInfo($parr);
        $vodata = $result['data'];

    	$parr['ucode'] = session('_ADMIN_UCODE');
    	$parr['type'] = I('type');
    	$parr['istore'] = 2;
    	$parr['phone'] = I('phone');
    	$parr['email'] = I('email');
    	$parr['qq'] = I('qq');
    	$parr['home_tel'] = I('home_tel');
        $parr['idcard'] = I('idcard');
        $parr['idcard_img'] = I('idcard_img');
        if($parr['idcard_img'] != $vodata['c_idcard_img']){
            $parr['idcard_img'] = copyFileToDIr(I('idcard_img'),'agent',1)['data'];
        }
        $parr['idcard_img1'] = I('idcard_img1');
        if($parr['idcard_img1'] != $vodata['c_idcard_img1']){
            $parr['idcard_img1'] = copyFileToDIr(I('idcard_img1'),'agent',1)['data'];
        }
        if (empty($parr['idcard'])|| empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            $this->error('请完善身份证资料信息');
        }
    	if (empty($parr['type']) ||empty($parr['phone']) ||empty($parr['email']) ||empty($parr['qq'])) {
    		$this->error('请完善资料信息');
    	}
    	if ($parr['type'] == 2) {
    		$parr['name'] = I('name');
    		$parr['postcode'] = I('postcode');
	    	$parr['company'] = I('company');
	    	$parr['address'] = I('address');
	    	$parr['charter'] = I('charter');
            $parr['charter_img'] = I('charter_img');
            if($parr['charter_img'] != $vodata['c_charter_img']){
                $parr['charter_img'] = copyFileToDIr(I('charter_img'),'agent',1)['data'];
            }
            $parr['company_sign'] = I('company_sign');
            if($parr['company_sign'] != $vodata['c_company_sign']){
                $parr['company_sign'] = copyFileToDIr(I('company_sign'),'agent',1)['data'];
            }
	    	if (empty($parr['postcode'])|| empty($parr['name'])  || empty($parr['charter']) || empty($parr['company']) || empty($parr['address'])) {
    			$this->error('请完善资料信息');
    		}
            if(empty($parr['charter_img']) || empty($parr['company_sign'])){
                $this->error('请上传相关证件图片');
            }
    	} else {
    		$parr['name'] = I('name1');
    		if (empty($parr['name'])) {
    			$this->error('请完善资料信息');
    		}
    	}

    	$result = D('Agent','Service')->SaveAgentInfo($parr);
    	if ($result['code'] != 0) {
    		$this->error($result['msg']);
    	}
    	$this->success('提交成功');
    }

    // 保存收款相关信息
    function SaveBankInfo()
    {
    	$parr['ucode'] = session('_ADMIN_UCODE');
    	$parr['fee_bank'] = I('fee_bank');
    	$parr['fee_branch'] = I('fee_branch');
    	$parr['fee_cardnum'] = I('fee_cardnum');
    	$parr['fee_name'] = I('fee_name');
    	$parr['fee_alipay'] = I('fee_alipay');
    	$parr['fee_weixin'] = I('fee_weixin');
    	if (empty($parr['fee_bank'])|| empty($parr['fee_branch'])  || empty($parr['fee_cardnum']) || empty($parr['fee_name']) || empty($parr['fee_alipay']) || empty($parr['fee_weixin'])) {
			$this->error('请完善收款资料信息');
		}

    	$result = D('Agent','Service')->SaveBankInfo($parr);
    	if ($result['code'] != 0) {
    		$this->error('提交失败');
    	}
    	$this->success('提交成功');
    }

	// 修改密码
    public function updatepwd()
    {
    	if (IS_POST) {
    		$db = M('Users');
			if(!empty($_POST)){
				$pbusinesscode['c_ucode'] = session('_ADMIN_UCODE');
				$loginpwd = $db->where($pbusinesscode)->getField('c_password');
				$pwd = encrypt($_POST['pwd_old'],C('ENCRYPT_KEY'));
				if($pwd != $loginpwd){
					 $this->error('旧密码不正确');die;
				}elseif(empty($_POST['pwd_new'])){
					$this->error('新密码不能为空');die;
				}elseif(md5($_POST['pwd_new']) != md5($_POST['loginpwd'])){
					$this->error('新密码与确认新密码不相同');die;
				}else{
					$data['c_password'] = encrypt($_POST['loginpwd'],C('ENCRYPT_KEY'));
					$result = $db->where($pbusinesscode)->save($data);
					if(!$result){
						$this->error('修改失败');
					}else{
						$this->success('修改成功！。。。请重新登录');
						session('_ADMIN_UCODE',null);
						session('_ADMIN_NAME',null);
						echo '<script language="javascript">top.location="'.U('Login/index').'";</script>';
					}

				}
		 	}
    	}
    	$this->display();
    }

}