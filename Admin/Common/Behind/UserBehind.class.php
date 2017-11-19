<?php
namespace Common\Behind;
/**
 * 用户管理
 * @author qiulin
 */
class UserBehind{
	/**
    * 添加用户
	* @param phone,pwd,invitationcode
	* 
    */
    function userAdd($parr)
    {	
    	$incode = $parr['incode'];
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];
        $jmpwd = encrypt($pwd,C('ENCRYPT_KEY'));

        $tj_phone = $parr['tj_phone'];
        $isshop = $parr['c_shop'];

        $wherephone['c_phone'] = $phone;
        $userTelId = M('Users')->where($wherephone)->getField('c_id');
        if ($userTelId) {
            return Message(1000,'该手机号码已被注册！');
        }

        $ucode = $this->CreateUcode();/*创建用户编码*/

        if(!empty($tj_phone)){
            $whereinfo['c_phone'] = $tj_phone;
            $userinfo = M('Users')->where($whereinfo)->field("c_acode,c_shop,c_ucode,c_isagent")->find(); 
            if(empty($userinfo)){
                return Message(1001,'推荐人信息不存在！');
            }
            
            if($isshop == 0){//普通用户推荐只能是商家,不能是普通用户或者代理商
                if(($userinfo['c_isagent'] != 0) || ($userinfo['c_shop'] == 0)){
                    return Message(1002,'普通用户推荐人只能为商家！');
                }
                $whereadd['c_acode'] = $userinfo['c_acode'];  /*继承商家的acode*/
            }else{//商家用户推荐只能是5万代理商或者商家,不能是普通用户
                if($userinfo['c_shop'] == 1 || $userinfo['c_isagent']==2){

                    if($userinfo['c_isagent'] == 2){
                        $whereadd['c_acode'] = $userinfo['c_ucode'];  /*继承5万代理商ucode*/
                    }

                    if($userinfo['c_shop'] == 1){
                        $whereadd['c_acode'] = $userinfo['c_acode'];  /*继承商家的acode*/
                    }
                }else{
                    return Message(1003,'商家用户推荐人只能为商家或者5万代理商！！');
                }
            }
        }

        $whereadd['c_ucode'] = $ucode;
		$whereadd['c_phone'] = $phone;
        $whereadd['c_nickname'] = $parr['nickname'];
        $whereadd['c_headimg'] = 'Uploads/user.png';   /*默认头像*/
		$whereadd['c_password'] = $jmpwd;
        $whereadd['c_signature'] = "蜜主很懒，没有什么个性签名！";
		$whereadd['c_level'] = $parr['c_level'];
		$whereadd['c_sex'] = $parr['sex'];
		$whereadd['c_signature'] = $parr['c_signature'];
		$whereadd['c_province'] = $parr['c_province'];
		$whereadd['c_city'] = $parr['c_city'];
		$whereadd['c_region'] = $parr['c_region']; 
		$whereadd['c_shop'] = $isshop;
		$whereadd['c_invitationcode'] = $parr['c_invitationcode'];
		$whereadd['c_num'] = $parr['c_num'];  
        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());

        $User = M('Users');
        $User->startTrans();/*开启事务*/
		$result = $User->add($whereadd);
        if (!$result) {
            $User->rollback();
            return Message(1004,'添加会员失败！');  
        }  

        //建立推荐关系
        if($userinfo['c_isagent'] == 0){
            $w['c_ucode'] = $ucode;
            $count = M('users_tuijian')->where($w)->count();
            if($count > 0){
                $result = M('users_tuijian')->where($w)->delete();
                if (!$result) {
                    $db->rollback();
                    return Message(1009, '清除原有推荐关系失败！');
                }
            } 
            if(!empty($tj_phone)){
                $wheretj['c_pcode'] = $userinfo['c_ucode'];
                $wheretj['c_ucode'] = $ucode;
                $wheretj['c_addtime'] = date('Y-m-d H:i:s', time());
                $result = M('users_tuijian')->add($wheretj);
                if (!$result) {
                    $User->rollback();
                    return Message(1005,'推荐关系绑定失败！');  
                } 
            }
        }

        // 保存第三方token        
        $tokenresult = D('UserProcess','Service')->token($ucode);
        if ($tokenresult['code'] != 0) {
            $User->rollback();
            return Message(1006,'保存第三方token失败！');
        }

        //商家建立基本资料
        if($isshop != 0){
            $result = $this->shopinfo($ucode,1);/*普通用户修改为商家，保存商家信息（其他信息为空）*/
            if(!$result){
                return Message(1007,'添加商家基本信息失败！');  
            }
        }

        $User->commit();         
        return Message(0,'添加会员成功');            
    }

    /**
     *  生成用户编码
     *  @param 
     */
    function CreateUcode($prefix ="wld") {
        //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 4);
        $uuid .= substr($str, 8, 2);
        $uuid .= substr($str, 12, 3);
        $uuid .= substr($str, 16, 2);
        $uuid .= substr($str, 20, 5);
        return $prefix . $uuid;
    }

    /**
     *  修改用户推荐关系
     *  @param 
     */
    function GetAgentid($parr){
    	$Users = M('Users'); 
    	$TJ = M('users_tuijian');
    	$Users->startTrans();

        $tj_phone = $parr['tj_phone'];
        $isshop = $parr['c_shop'];
        $ucode = $parr['ucode'];

        $where['c_ucode'] = $ucode;
        $pcode = $TJ->where($where)->getField('c_pcode');

        $uinfo = $Users->where($where)->field('c_acode,c_shop,c_isagent')->find();
        $shop = $uinfo['c_shop'];
        $isagent = $uinfo['c_isagent'];

        //判断推荐人是否是自己
        if($ucode == $pcode){
            return Message(1001,'推荐人不能为用户本身!');
        }

        //判断推荐用户是否存在，并查出推荐人信息
        if(!empty($tj_phone)){
            $wherephone['c_phone'] = $tj_phone;
            $pinfo = $Users->where($wherephone)->field('c_ucode,c_acode,c_shop,c_isagent')->find();
            if(empty($pinfo)){
                return Message(1002,'该手机号码无人注册！');
            }
        }

        if($isagent != 0){
            return Message(1005,'代理商暂时不能修改推荐关系！');
        }

        if($shop == 0 && $isshop == 1){//普通会员修改成商家
            //商家建立基本资料
            $result = $this->shopinfo($ucode,1);/*普通用户修改为商家，保存商家信息（其他信息为空）*/
            if(!$result){
                $Users->rollback();
                return Message(1007,'添加商家基本信息失败！');  
            }

            //推荐关系无修改
            if($pcode == $pinfo['c_ucode']){
                return Message(0,'无任何修改！');
            }

           if(!empty($pcode)){
                return Message(1008,'先前存在关系,暂时不能修改！');
           }else{
                if(!empty($tj_phone)){
                    if($pinfo['c_shop'] == 1 || $pinfo['c_isagent'] == 2){
                        if($pinfo['c_isagent'] == 2){
                            $sdata['c_acode'] = $pinfo['c_ucode'];  /*继承5万代理商ucode*/
                        }

                        if($pinfo['c_shop'] == 1){
                            $sdata['c_acode'] = $pinfo['c_acode'];  /*继承商家的acode*/
                        }
                    }else{
                        return Message(1003,'商家用户推荐人只能为商家或者5万代理商！！');
                    }

                    $result = $Users->where($where)->save($sdata);
                    if($pinfo['c_isagent'] == 0){
                        $result1 = $this->changr($ucode,$pinfo['c_ucode'],0);
                        if(!$result1){
                            return Message(1004,"推荐关系绑定失败");
                        }
                    }

                    if($result){
                        $Users->commit();
                        return Message(0,'推荐关系绑定成功!');
                    }else{
                        $Users->rollback();
                        return Message(1004,"推荐关系绑定失败");
                    }
                }
           } 
        }

        if($shop == 0 && $isshop == 0){//普通会员
            //推荐关系无修改
            if($pcode == $pinfo['c_ucode']){
                return Message(0,'无任何修改！');
            }

            if(!empty($pcode)){
                if(!empty($tj_phone)){
                    if(($pinfo['c_isagent'] != 0) || ($pinfo['c_shop'] == 0)){
                        return Message(1004,'普通用户推荐人只能为商家！');
                    }

                    if($uinfo['c_acode'] == $pinfo['c_acode']){
                        $result = ture;
                    }else{
                        $sdata['c_acode'] = $pinfo['c_acode'];  /*继承商家的acode*/
                        $result = $Users->where($where)->save($sdata);
                    }

                    $result1 = $this->changr($ucode,$pinfo['c_ucode'],1);
                    if($result && $result1){
                        $Users->commit();
                        return Message(0,'推荐关系绑定成功!');
                    }else{
                        $Users->rollback();
                        return Message(1004,"推荐关系绑定失败!");
                    }
                }else{
                    $sdata['c_acode'] = '';  

                    $result = $Users->where($where)->save($sdata);
                    $result1 = $this->changr($ucode,$pinfo['c_ucode'],2);
                    if($result && $result1){
                        $Users->commit();
                        return Message(0,'推荐关系绑定成功!');
                    }else{
                        $Users->rollback();
                        return Message(1004,"推荐关系绑定失败!");
                    }
                }
            }else{
                if(!empty($tj_phone)){
                   if(($pinfo['c_isagent'] != 0) || ($pinfo['c_shop'] == 0)){
                        return Message(1004,'普通用户推荐人只能为商家！');
                    }
                   $sdata['c_acode'] = $pinfo['c_acode'];  /*继承商家的acode*/

                   $result = $Users->where($where)->save($sdata);
                   $result1 = $this->changr($ucode,$pinfo['c_ucode'],0);

                   if($result && $result1){
                       $Users->commit();
                       return Message(0,'推荐关系绑定成功!');
                   }else{
                       $Users->rollback();
                       return Message(1004,"推荐关系绑定失败！");
                   }
                }
            }
        }

        if($shop == 1 && $isshop == 1){//商家
            return Message(1005,'商家暂时不能修改推荐关系！');
        }
        
        return Message(1006,'逻辑错误');
    }

    function changr($ucode,$pcode,$flag){
        if($flag == 0){
            $adata['c_pcode'] = $pcode;
            $adata['c_ucode'] = $ucode;
            $adata['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('users_tuijian')->add($adata);
            if (!$result) {
                return false;  
            } 
        }else if($flag == 1){
            $where['c_ucode'] = $ucode;
            $sdata['c_pcode'] = $pcode;
            $sdata['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('users_tuijian')->where($where)->save($sdata);
            if (!$result) {
                return false;  
            } 
        }else{
            $where['c_ucode'] = $ucode;
            $result = M('users_tuijian')->where($where)->delete();
            if (!$result) {
                return false;  
            } 
        }
        return true;
    }

    function shopinfo($ucode,$istore){
        $w['c_ucode'] = $ucode;
        $userinfo = M('users')->where($w)->field('c_phone,c_nickname')->find();

        $sdata['c_dcode'] = 'XWS'.mb_substr(time(), 5, 9, 'utf8').CreateOrder('CD');

        $sdata['c_ucode'] = $ucode;
        $sdata['c_name'] = $userinfo['c_nickname'];
        $sdata['c_istore'] = $istore;
        $sdata['c_phone'] = $userinfo['c_phone'];
        $sdata['c_checked'] = 3;
        $sdata['c_addtime'] = date('Y-m-d H:i:s', time());

        $result = M('check_shopinfo')->add($sdata);
        if (!$result) {
            return false;  
        } 

        return true;
    }

    /**
     *  用户编辑
     *  @param 
     */
    function userEdit($parr){
    	$Users = M('users');
    	$ucode = $parr['ucode'];
    	
        $whereadd['c_nickname'] = $parr['nickname'];
		$whereadd['c_level'] = $parr['c_level'];
		$whereadd['c_sex'] = $parr['sex'];
		$whereadd['c_signature'] = $parr['c_signature'];
		$whereadd['c_province'] = $parr['c_province'];
		$whereadd['c_city'] = $parr['c_city'];
		$whereadd['c_region'] = $parr['c_region']; 
		$whereadd['c_shop'] = $parr['c_shop'];
		$whereadd['c_invitationcode'] = $parr['c_invitationcode'];
		$whereadd['c_num'] = $parr['c_num'];

		$where['c_ucode'] = $ucode;
		$result = $Users->where($where)->save($whereadd);

		if($result || $result == 0){
            //刷新融云用户信息
            D('UserProcess','Service')->userRefresh($ucode);
            if(!empty($parr['nickname'])){
                //如果用户是实体商家，修改店铺名称
                $w['c_ucode'] = $ucode;
                $isfixed = M('User_local')->where($w)->getField('c_isfixed');
                if($isfixed == 1){
                    $savestore['c_name'] = $nickname;
                    M('Store')->where($w)->save($savestore);
                }
            }
			return Message(0,"用户编辑成功");
		}else{
			return Message(1001,"用户编辑失败");
		}  
    }

    /**
     *  用户收货地址
     *  @param 
     */
    function addressAdd($parr,$handle){
    	$ucode = $parr['ucode'];

    	$data['c_ucode'] = $ucode;
    	$data['c_consignee'] = $parr['consignee'];
    	$data['c_mobile'] = $parr['mobile'];
    	$data['c_province'] = $parr['c_province'];
    	$data['c_provincename'] = $this->getRegion_name($parr['c_province']);
    	$data['c_city'] = $parr['c_city'];
    	$data['c_cityname'] = $this->getRegion_name($parr['c_city']);
    	$data['c_district'] = $parr['c_district'];
    	$data['c_districtname'] = $this->getRegion_name($parr['c_district']);
    	$data['c_address'] = $parr['address'];
    	//用户是否存在收货地址
    	$where['c_ucode'] = $ucode;
    	$count = M('users_address')->where($where)->count();

    	$is_default = $parr['c_is_default'];
    	if($handle == 1){
    		if($count == 0){
    			$data['c_is_default'] = 1;
    		}else{
    			if($is_default == 1){
    				$set_result = M('users_address')->where($where)->setField('c_is_default',0);
    			}
    			$data['c_is_default'] = $is_default;
    		}
    		$data['c_addtime'] = date('Y-m-d H:i:s',time());

    		$result = M('users_address')->add($data);

    		if($result){
    			return Message(0,"添加成功");
    		}else{
    			return Message(1001,"添加失败");
    		}
    	}else{
    		if($is_default == 1){
    			$set_result = M('users_address')->where($where)->setField('c_is_default',0);
    		}
    		$data['c_is_default'] = $is_default;
    		
    		$where1['c_id'] = intval($parr['addressid']);
    		$result = M('users_address')->where($where1)->save($data);

    		if($result || $result == 0){
    			return Message(0,"保存成功");
    		}else{
    			return Message(1001,"保存失败");
    		}
    	}
    }

    //获取地址表地名
    function getRegion_name($region_id){
    	$where['region_id'] = $region_id;
    	$region_name = M('region')->where($where)->getField('region_name');
    	return $region_name;
    }

    //会员级别添加
    function gradeAdd($parr){
        $Id = intval($parr['Id']);
        $data['c_level_name'] = $parr['c_level_name'];
        $data['c_rule'] = intval($parr['c_rule']);
        $data['c_desc'] = $parr['c_desc'];

        if(empty($Id)){
            $where['c_rule'] = intval($parr['c_rule']);
            $count = M('user_level')->where($where)->count();
            if($count > 0){
                return Message(1001,"等级规则已经存在，请勿重复");
            }
            
            $data['c_addtime'] = date('Y-m-d H:i:s',time());
            $result = M('user_level')->add($data);

            if($result){
                return Message(0,"添加成功");
            }else{
                return Message(1002,"添加失败");
            }
        }else{
            $where['c_id'] = $Id;
            $result = M('user_level')->where($where)->save($data);

            if($result || $result == 0){
                return Message(0,"保存成功");
            }else{
                return Message(1002,"保存失败");
            }
        }
    }

    //代理商添加注册用户
    public function register($parr){
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];
        $nickname = $parr['nickname'];
        $isagent = $parr['isagent'];
        $tj_phone = $parr['tj_phone'];
        $invite_code = $parr['invite_code'];

        $wherephone['c_phone'] = $phone;

        $userTelId = M('Users')->where($wherephone)->getField('c_id');
        if ($userTelId) {
            return Message(1001, '该手机号码已经已被注册！');
        }

        if($isagent == 2){
            if (!empty($tj_phone)) {
                $wherinfo['c_phone'] = $tj_phone;
                $userinfo = M('Users')->where($wherinfo)->field("c_ucode,c_isagent")->find(); 
                if (empty($userinfo)) {
                    return Message(1002, '推荐代理商不存在!');
                }
                if($userinfo['c_isagent'] != 1){
                    return Message(1003, '推荐代理商必须为50万代理商!');
                }
                $pcode = $userinfo['c_ucode'];
                $whereadd['c_acode'] =  $pcode;/* 继承推荐人商家ucode*/
            }

            if(!empty($invite_code)){
                $result = $this->binding($invite_code,2);/* 市代绑定邀请卡*/
                if($result['code'] == 0){
                    $pcode = $result['data']['c_ucode'];
                    $whereadd['c_acode'] =  $pcode;/* 继承推荐人商家ucode*/
                }else{
                    return Message(1004,$result['msg']);
                }
            }
        }else{
            if(!empty($invite_code)){
                $result = $this->binding($invite_code,1);/* 区代绑定邀请卡*/
                if($result['code'] != 0){
                    return Message(1004,$result['msg']);
                }
            }
        }

        $ucode = $this->CreateUcode(); /* 创建用户编码 */

        $whereadd['c_ucode'] = $ucode;  
        $whereadd['c_level'] = 1;
        $whereadd['c_phone'] = $phone;
        $whereadd['c_isagent'] = $isagent;  
        $whereadd['c_nickname'] = $nickname; /* 默认昵称 */
        $whereadd['c_password'] = $pwd;
        $whereadd['c_signature'] = "蜜主很懒，没有什么个性签名！";
        $whereadd['c_headimg'] = 'Uploads/user.png';
        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());

        $User = M('Users');
        $result = $User->add($whereadd);
        if (!$result) {
            return Message(1005, '注册失败！');
        }

        //绑定邀请卡
        if(!empty($invite_code)){
            $w['c_code'] = $invite_code;
            $s['c_ucode'] = $ucode;
            $s['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('invite_code')->where($w)->save($s);
            if(!$result){
                return Message(1007, '绑定邀请卡失败！');
            }
        }

        // 保存第三方token        
        $tokenresult = D('UserProcess', 'Service')->token($ucode);
        if ($tokenresult['code'] != 0) {
            return Message(1006, '保存第三方token失败！');
        }

        $data['ucode'] = $ucode;
        $data['pcode'] = $pcode;
        return MessageInfo(0, '注册成功',$data);
    }

    function binding($invite_code,$type){
        $w['c_code'] = $invite_code;
        $codeinfo = M('invite_code')->where($w)->find();

        if(empty($codeinfo)){
            return Message(1001, '邀请卡不存在！');
        }

        if(!empty($codeinfo['c_ucode'])){
            return Message(1002, '邀请卡已经被绑定！');
        }

        if($type == 1){
            if($codeinfo['c_rule'] != 1){
                return Message(1003, '区级代理只能绑定钻卡！');
            }
            return Message(0,'返回推荐代理商编码');
        }else{
            if($codeinfo['c_rule'] != 2){
                return Message(1003, '市级代理只能绑定金卡！');
            }

            $w1['c_code'] = $codeinfo['c_fcode'];
            $fcodeinfo = M('invite_code')->where($w1)->find();
            if(empty($fcodeinfo['c_ucode'])){
                return Message(1004, '该金卡所属钻卡未被绑定！');
            }

            return MessageInfo(0,'返回推荐代理商编码',$fcodeinfo);
        }
        
    }

    //升级代理商
    function UpGrade($data){
        $ucode = $data['ucode'];
        $tj_phone = $data['tj_phone'];
        $isagent = $data['isagent'];
        $invite_code = $data['invite_code'];

        $db = M('');
        $db ->startTrans();

        $pcode = '';
        if($isagent == 2){
            if (!empty($tj_phone)) {
                $wherinfo['c_phone'] = $tj_phone;
                $userinfo = M('Users')->where($wherinfo)->field("c_ucode,c_isagent")->find(); 
                if (empty($userinfo)) {
                    return Message(1002, '推荐代理商不存在!');
                }
                if($userinfo['c_isagent'] != 1){
                    return Message(1003, '推荐代理商必须为50万代理商!');
                }
                $pcode = $userinfo['c_ucode'];/*代理商编码 继承推荐人ucode*/
            }

            if(!empty($invite_code)){
                $result = $this->binding($invite_code,2);/* 市代绑定邀请卡*/
                if($result['code'] == 0){
                    $pcode = $result['data']['c_ucode'];/*代理商编码 继承推荐人ucode*/
                }else{
                    return Message(1004,$result['msg']);
                }
            }
        }else{
            if(!empty($invite_code)){
                $result = $this->binding($invite_code,1);/* 区代绑定邀请卡*/
                if($result['code'] != 0){
                    return Message(1004,$result['msg']);
                }
            }
        }

        $w['c_ucode'] = $ucode;

        //修改会员信息
        if($isagent == 2){
            $sdata['c_acode'] = $pcode;
        }else{
            $sdata['c_acode'] = ''; 
        }
        $sdata['c_isagent'] = $isagent;
        $result = M('users')->where($w)->save($sdata);
        if (!$result) {
            $db->rollback();
            return Message(1005, '会员信息修改失败!');
        }

        //取消推荐关系
        $count = M('users_tuijian')->where($w)->count();
        if($count > 0){
            $result = M('users_tuijian')->where($w)->delete();
            if (!$result) {
                $db->rollback();
                return Message(1009, '清除原有推荐关系失败！');
            }
        } 
        
        //绑定邀请卡
        if(!empty($invite_code)){
            $w1['c_code'] = $invite_code;
            $s['c_ucode'] = $ucode;
            $s['c_updatetime'] = date('Y-m-d H:i:s', time());
            $result = M('invite_code')->where($w1)->save($s);
            if(!$result){
                $db->rollback();
                return Message(1007, '绑定邀请卡失败！');
            }
        }

        //填写代理商基本信息
        $result = $this->shopinfo($ucode,2);
        if(!$result){
            $db->rollback();
            return Message(1008, '代理商基本信息写入失败！');
        }
        $db->commit();
        return Message(0,'升级成功');
    }

    /**
     * 个人开户资料批量导出
     * @param  string $value [description]
     * @return [type]        [description]
     */
    function PersonExport()
    {
        $db = M('users_bank as b');
        //条件
        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
            $w['u.c_nickname'] = array('like', "%{$nickname}%");
        }

        $c_phoner = trim(I('phone'));
        if (!empty($c_phoner)) {
            $w['u.c_phone'] = $c_phoner;
        }

        $c_uname = trim(I('c_uname'));
        if (!empty($c_uname)) {
            $w['b.c_uname'] = $c_uname;
        }

        $c_carid = trim(I('c_carid'));
        if (!empty($c_carid)) {
            $w['b.c_carid'] = $c_carid;
        }

        $c_shop = trim(I('c_shop'));
        if ($c_shop == 1) {
            $w['a.c_shop'] = 1;
        } else if ($c_shop == 2) {
            $w['a.c_shop'] = 0;
        } else {
            die("传入用户属性");
        }        

        //数据数量
        $least=trim(I('least'));
        if(!empty($least)){
            $rise=$least;//起
        }
        $maximum=trim(I('maximum'));
        if(!empty($maximum)){
            $to=$maximum;//终至
        }
        $total=25;//总行
        $s=ceil($rise * $total) - $total;//当前页，第几条开始
        $scope=intval($to - $rise) + 1;
        $conud=ceil($scope * $total); //多少条


        $order = 'b.c_id asc';//排序
        $field = 'b.*,u.c_nickname';
        $join = 'left join t_users as u on u.c_ucode=b.c_ucode';

        $data = $db->join($join)->field($field)->where($w)->order($order)->limit($s,$conud)->select();

        //导入PHPExcel类库
        import("Common.Org.PHPExcel");
        import("Common.Org.PHPExcel.Writer.Excel5");
        import("Common.Org.PHPExcel.IOFactory.php");

              
        $date[0][0]=array("姓名","邮箱地址","手机号码","证件类型","证件号码","证件有效期",
        "用户名","提现","转出","订单支付","代收付款","代付付款","授信额度",
        "银行帐号","户名","银行账户类型","行别","开户银行名称","开户银行所在地","所属机构号");

        $k1=0;
        foreach($data as $k=>$v){
            $k1++;
            $v['c_idcardtype'] = ($v['c_idcardtype'] == 2)?'01':'00';
            $v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'21':'11';
            $v['c_bankcity'] = $this->GetLocalName($v['c_bankcity'],'2');
            $v['c_bankid'] = $this->GetBankName($v['c_bankid']);

            $date[$k1][0] = array(
                $v['c_principal'],
                $v['c_email'],
                '\''.$v['c_principalmobile'],
                '\''.$v['c_idcardtype'],
                '\''.$v['c_idcard'],
                ' ',
                'wld-',
                'Y',
                'Y',
                'Y',
                'Y',
                'Y',
                '\''.'0',
                '\''.$v['c_accountcode'],
                $v['c_accountname'],
                $v['c_accounttype'],
                $v['c_bankid'],
                $v['c_bankname'],
                $v['c_bankcity'],
                ' ',
            );

            $filename="个人用户开户资料";   
            $this->getExcel1($filename,$date);
        }

        // $date[0][0]=array("商户名称","商户简称","经营币种","经营类型","行业类别","省份","城市","区县","地址","邮箱","企业法人","客服电话","负责人",
        //  "负责人手机号","负责人身份证","银行卡号","开户行","开户人","帐号类型","开户支行","支行省份","支行市区",
        //  "证件类型","证件号","执卡人手机号","资料提交时间");
        // $k1=0;
        // foreach($data as $k=>$v){
        //  $k1++;
        //  $v['c_mchdealtype'] = ($v['c_mchdealtype'] == 1)?'实体':'虚拟';
        //  $v['c_accounttype'] = ($v['c_mchdealtype'] == 1)?'企业':'个人';
        //  $v['c_idcardtype'] = ($v['c_idcardtype'] == 1)?'身份证':'护照帐户类';
        //  $v['c_province'] = $this->GetLocalName($v['c_province'],'1');
        //  $v['c_city'] = $this->GetLocalName($v['c_city'],'2');
        //  $v['c_county'] = $this->GetLocalName($v['c_county'],'3');
        //  $v['c_bankprovince'] = $this->GetLocalName($v['c_bankprovince'],'1');
        //  $v['c_bankcity'] = $this->GetLocalName($v['c_bankcity'],'2');
        //  $v['c_bankid'] = $this->GetBankName($v['c_bankid']);
        //  $v['c_industrid'] = $this->GetIndustryName($v['c_industrid']);

        //  $date[$k1][0] = array(
        //      $v['c_merchantname'],
        //      $v['c_merchantshortname'],
        //      $v['c_feetype'],
        //      $v['c_mchdealtype'],
        //      $v['c_industrid'],
        //      $v['c_province'],
        //      $v['c_city'],
        //      $v['c_county'],
        //      $v['c_address'],
        //      $v['c_email'],
        //      $v['c_legalperson'],
        //      '\''.$v['c_customerphone'],
        //      $v['c_principal'],
        //      $v['c_principalmobile'],
        //      '\''.$v['c_idcode'],
        //      '\''.$v['c_accountcode'],
        //      $v['c_bankid'],
        //      $v['c_accountname'],
        //      $v['c_accounttype'],
        //      $v['c_bankname'],
        //      $v['c_bankprovince'],
        //      $v['c_bankcity'],
        //      $v['c_idcardtype'],
        //      '\''.$v['c_idcard'],
        //      $v['c_banktel'],
        //      '\''.$v['c_addtime'],
        //  );
        // }

        // //导入PHPExcel类库
        // import("Common.Org.PHPExcel");
        // import("Common.Org.PHPExcel.Writer.Excel5");
        // import("Common.Org.PHPExcel.IOFactory.php");
        
        // $filename="商户提交资料";      
        
        // $this->getExcel($filename,$date);
    }

    //调用phpExcel
    private function getExcel1($fileName,$data){
        //对数据进行检验
        if(empty($data)||!is_array($data)){
            die("data must be a array");
        }
        $date=date("Y_m_d",time());
        $fileName.="_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        $objProps=$objPHPExcel->getProperties();
        $column=1;
        $objActSheet=$objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth('22');//设置列宽

    
        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
        foreach ($data as $ke=>$row){
            foreach($row as $key=>$rows){
                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:T{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:T{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFont()->setBold(true);//粗体
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
                }else{
                    if(!empty($rows)){
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:T{$column}");//设置边框
                    }
                }
                if($rows['0']=='姓名'){
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:T{$column}")->getAlignment()->setWrapText(true);//换行
                //行写入
                $span = ord("A");
    
                foreach($rows as $keyName=>$value){
                    // 列写入
                    $j=chr($span);
                    $value = !empty($value)?$value:' ';
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
                $column++;
                
            }
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}