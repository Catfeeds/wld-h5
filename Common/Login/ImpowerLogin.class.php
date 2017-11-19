<?php

/**
 *  第三方授权操作相关接口
 *  用户锁定相关接口
 */
class ImpowerLogin {

	/**
     * 查询用户是否授权
     * @param type(1微信,2支付宝),openid,unionid
     */
    function AuthSeacher($parr)
    {
        $openid = $parr['openid'];
        $unionid = $parr['unionid'];
        if ((empty($openid) && empty($unionid)) || empty($parr['type'])) {
            return Message(1009,'查询失败');
        }

        $where['c_type'] = $parr['type'];
        if (is_weixin()) {
            $where[] = array("c_unionid='$unionid'");
        } else if (is_aliApp()) {
            $where[] = array("c_openid='$openid'");  
        } else {
            $where[] = array("c_openid='$openid' or c_unionid='$openid'");
        }
        
        $data = M('Users_auth')->where($where)->find();
        if (!$data) {
            return Message(1009,'查询失败');
        }

        if (is_weixin()) {
            $sw['c_id'] = $data['c_id'];
            $save['c_openid'] = $openid;
            $save['c_unionid'] = $unionid;
            $result = M('Users_auth')->where($sw)->save($save);
        }
        return MessageInfo(0,'查询成功',$data);
    }

	/**
	 * 微信用户授权
	 * @param type,openid,phone,incode
	 */
	function AuthUser($parr)
	{
        $getnickname = filterEmoji($parr['nickname']);
		$parr['nickname'] = empty($getnickname)?$parr['phone']:$getnickname;
		if (empty($parr['phone'])) {
			return Message(1003,'手机号不能为空');
		}
		// if (empty($parr['openid'])) {
		// 	return Message(1004,'微信授权失败');
		// }
		$userwhere['c_phone'] = $parr['phone'];
		$userdata = M('Users')->where($userwhere)->find();
		if ($userdata) {
			$ucode = $userdata['c_ucode'];
		} else {
        	$uparr['incode'] = $parr['incode'];
        	$uparr['phone'] = $parr['phone'];
        	$pwd = 'm'.substr($parr['phone'],-6);
        	$uparr['pwd'] = encrypt($pwd,C('ENCRYPT_KEY'));
			$result = IGD('Login','Login')->register($uparr);
			if ($result['code'] != 0) {
				return Message(1006,'写入信息失败');
			}
			$ucode = $result['data']['c_ucode'];

			//新增发短信通知
			$sendparr['telephone'] = $parr['phone'];
	        $sendparr['type'] = 1000;
	        $sendparr['userid'] = C('TEl_USER');
	        $sendparr['account'] = C('TEl_ACCESS');
	        $sendparr['password'] = C('TEl_PASSWORD');
	        $sendparr['content'] = "【微领地小蜜】注册成功！您的登录账号为手机号码，密码为：" . $pwd . "，请及时下载登录修改密码，点击下载：http://dwz.cn/43sS0T";
	        $returndata = IGD('Sendmsg','Login')->SendVerify($sendparr);
		}

		if (empty($parr['type']) && empty($parr['openid'])) {
			return MessageInfo(0,'注册成功',$ucode);
		}

		// 判断微信用户是否授权小蜜帐号
		// $deletwhere['c_type'] = $parr['type'];
		// $deletwhere['c_openid'] = $parr['openid'];
		// $result = M('Users_auth')->where($deletwhere)->select();
        $result = $this->AuthSeacher($parr); 
		if ($result['code'] == 0) {
			return Message(1005,'该帐号已绑定小蜜帐号');
		}

		//判断小蜜帐号是否授权微信
		$authucode['c_ucode'] = $ucode;
		$authucode['c_type'] = $parr['type'];
		$result = M('Users_auth')->where($authucode)->select();
		if ($result) {
			return Message(1005,'小蜜帐号已授权其他帐号');
		}

		$db = M('');
		$db->startTrans();

        $getnickname = filterEmoji($parr['nickname']);

		//写入授权信息
		$authdata['c_ucode'] = $ucode;
		$authdata['c_openid'] = $parr['openid'];
        $authdata['c_unionid'] = $parr['unionid'];
		$authdata['c_name'] = $getnickname;
		$authdata['c_headimg'] = $parr['headimgurl'];
		$authdata['c_type'] = $parr['type'];
		$authdata['c_addtime'] = date('Y-m-d H:i:s');
		$result = M('Users_auth')->add($authdata);
		if (!$result) {
			$db->rollback();
			return Message(1004,'授权失败');
		}

		//修改用户资料
		if (!$userdata && !empty($getnickname)) {
			$savedata['c_nickname'] = !empty($getnickname)?$getnickname:'小蜜用户'.time();
			if ($parr['sex'] == 1) {
				$savedata['c_sex'] = '男';
			} else if ($parr['sex'] == 2) {
				$savedata['c_sex'] = '女';
			}

			//微信头像保存服务端
	        $pathdir = SYS_PATH .'Uploads'. DS .'headimg'. DS .'weixin'. DS .$ucode.'.jpg'; //头像保存路径
	        if(checkDir($pathdir)) {
	            if(!is_file($pathdir) && !empty($parr['headimgurl'])){
	                $http = new \Org\Net\Http;
	                $tx = $http->curlDownload($parr['headimgurl'],$pathdir);
	            }
	        }

	        if(is_file($pathdir)){
		        $savedata['c_headimg'] = 'Uploads/headimg/weixin/'.$ucode.'.jpg';
		        //保存远程图片
				$result = qiniu_syn_files($savedata['c_headimg'],$savedata['c_headimg']);
	            if(!$result){
	            	$db->rollback();
	                return Message(1007,'远程上传头像失败');
	            }
	        } else {
	        	$savedata['c_headimg'] = 'data/userheadimg/'.rand(11, 20).'.jpg';
	        }

			$result = M('Users')->where($userwhere)->save($savedata);
			if (!$result) {
				$db->rollback();
				return Message(1004,'保存信息失败');
			}
		}

		//查找微信用户是否有锁定关系，并锁定用户
        $parr['ucode'] = $ucode;
		$result = $this->BindWxopendid($parr);
		if ($result['code'] != 0) {
            $db->rollback();
			return $result;
		}

		$db->commit();
		return MessageInfo(0,'授权成功',$ucode);
	}

	/**
     * 根据商家邀请码获取商家信息
     * @param incode
     *
     */
    function GetUserByInvite($parr) {
    	if (empty($parr['incode'])) {
    		return Message(1000, '邀请码错误');
    	}
        $whereinfo['c_invitationcode'] = $parr['incode'];
        $userinfo = M('Users')->where($whereinfo)->find();
        if (count($userinfo) == 0) {
            return Message(1000, '查询失败');
        }
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
        return MessageInfo(0, '查询成功', $userinfo);
    }

    /**
     * 添加地址便捷注册
     * @param phone,pwd,type,openid,sex,nickname,openid,headimgurl
     * @param consignee,mobile,province,city,district,address,provincename,cityname,districtname
     */
    function AddressRegister($parr)
    {
        $phone = $parr['phone'];
        $pwd = $parr['pwd'];
        $getnickname = filterEmoji($parr['nickname']);
        $parr['nickname'] = empty($getnickname)?$parr['phone']:$getnickname;

        //查询用户信息
        $wherephone['c_phone'] = $phone;
        $ucode = M('Users')->where($wherephone)->getField('c_ucode');

        // 判断微信用户是否授权小蜜帐号
        // $deletwhere['c_type'] = $parr['type'];
        // $deletwhere['c_openid'] = $parr['openid'];
        // $result = M('Users_auth')->where($deletwhere)->find();
        $result = $this->AuthSeacher($parr); 
        if ($result['code'] == 0) {
            return MessageInfo(0, "添加成功",$result['data']['c_ucode']);
        }
        
        //判断小蜜帐号是否授权微信
        $authucode['c_ucode'] = $ucode;
        $authucode['c_type'] = $parr['type'];
        $result = M('Users_auth')->where($authucode)->find();
        if ($result) {
            return MessageInfo(0, "添加成功",$result['c_ucode']);
        }

        $db = M('');
        $db->startTrans(); /* 开启事务 */
        
        if ($ucode) {
            $whereaddress['c_ucode'] = $ucode;
            $save['c_is_default'] = 0;
            $result = M('Users_address')->where($whereaddress)->save($save);
            // if (!$result) {
            //     $db->rollback(); //不成功，则回滚
            //     return Message(1015, "地址改变失败");
            // }
            $sendmsg = 0;
        } else {
            $ucode = CreateUcode(); /* 创建用户编码 */
            if ($parr['sex'] == 1) {
                $useradd['c_sex'] = '男';
            } else if ($parr['sex'] == 2) {
                $useradd['c_sex'] = '女';
            }

            //微信头像保存服务端
            $pathdir = SYS_PATH .'Uploads'. DS .'headimg'. DS .'weixin'. DS .$ucode.'.jpg'; //头像保存路径
            if(checkDir($pathdir)) {
                if(!is_file($pathdir) && !empty($parr['headimgurl'])){
                    $http = new \Org\Net\Http;
                    $tx = $http->curlDownload($parr['headimgurl'],$pathdir);
                }
            }

            if(is_file($pathdir)){
                $useradd['c_headimg'] = 'Uploads/headimg/weixin/'.$ucode.'.jpg';
                //保存远程图片
                $result = qiniu_syn_files($useradd['c_headimg'],$useradd['c_headimg']);
                if(!$result){
                    $db->rollback();
                    return Message(1007,'远程上传头像失败');
                }
            } else {
                $useradd['c_headimg'] = 'data/userheadimg/'.rand(11, 20).'.jpg';
            }

            $useradd['c_ucode'] = $ucode;
            $useradd['c_phone'] = $phone;
            $useradd['c_nickname'] = !empty($getnickname)?$getnickname:'小蜜用户'.time();
            $useradd['c_signature'] = '蜜主很懒，没有什么个性签名！';
            $useradd['c_password'] = encrypt($pwd,C('ENCRYPT_KEY'));
            $useradd['c_addtime'] = date('Y-m-d H:i:s', time());
            $result = M('Users')->add($useradd);
            if (!$result) {
                $db->rollback();
                return Message(1002, '注册失败！');
            }
            $sendmsg = 1;
        }

        //写入授权信息
        $authdata['c_ucode'] = $ucode;
        $authdata['c_openid'] = $parr['openid'];
        $authdata['c_unionid'] = $parr['unionid'];
        $authdata['c_name'] = $getnickname;
        $authdata['c_headimg'] = $parr['headimgurl'];
        $authdata['c_type'] = $parr['type'];
        $authdata['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Users_auth')->add($authdata);
        if (!$result) {
            $db->rollback();
            return Message(1004,'授权失败');
        }



        //地址添加
        $whereadd['c_ucode'] = $ucode;
        $whereadd['c_consignee'] = $parr['consignee'];
        $whereadd['c_mobile'] = $parr['mobile'];
        $whereadd['c_province'] = $parr['province'];
        $whereadd['c_city'] = $parr['city'];
        $whereadd['c_district'] = $parr['district'];
        $whereadd['c_address'] = $parr['address'];
        $whereadd['c_provincename'] = $parr['provincename'];
        $whereadd['c_cityname'] = $parr['cityname'];
        $whereadd['c_districtname'] = $parr['districtname'];
        $whereadd['c_addtime'] = date('Y-m-d H:i:s', time());
        $whereadd['c_is_default'] = 1;
        $result = M('Users_address')->add($whereadd);
        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(1015, "地址添加失败");
        }

        // 保存第三方token
        $tokenresult = IGD('UserProcess', 'Rongcloud')->token($ucode);
        if ($tokenresult['code'] != 0) {
            $db->rollback();
            return Message(1005, '保存第三方token失败！');
        }

        //查找微信用户是否有锁定关系，并锁定用户
        $parr['ucode'] = $ucode;
        $result = $this->BindWxopendid($parr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        if ($sendmsg == 1) {
            $sendparr['telephone'] = $phone;
            $sendparr['type'] = 1000;
            $sendparr['userid'] = C('TEl_USER');
            $sendparr['account'] = C('TEl_ACCESS');
            $sendparr['password'] = C('TEl_PASSWORD');
            $sendparr['content'] = "【微领地小蜜】注册成功！您的登录账号为手机号码，密码为：" . $pwd . "，请及时下载登录修改密码，点击下载：http://dwz.cn/43sS0T";
            $returndata = IGD('Sendmsg','Login')->SendVerify($sendparr);
        }
        $db->commit();
        
        return MessageInfo(0, "添加成功",$ucode);
    }

    /**
     * 修改用户授权信息
     * @param type,openid,nickname,headimgurl,unionid
     */
    function PerfectAuthInfo($parr)
    {
        $openid = $parr['openid'];
        $where['c_type'] = $parr['type'];
        $where[] = array("c_openid='$openid' or c_unionid='$openid'");
        $save['c_name'] = filterEmoji($parr['nickname']);
        $save['c_headimg'] = $parr['headimgurl'];
        $save['c_unionid'] = $parr['unionid'];
        $result = M('Users_auth')->where($where)->save($save);
        if (!$result) {
            return Message(1009,'修改失败');
        }
        return Message(0,'修改成功');
    }

    /**
     * 查找微信用户是否有锁定关系，并锁定用户
     * @param ucode,openid
     */
    function BindWxopendid($parr)
    {
        $ucode = $parr['ucode'];
        $openid = $parr['openid'];
        $unionid = $parr['unionid'];
        $ucodetj = $this->FindUcodeLock($ucode);
        if (is_weixin()) {
            $openidtj = $this->FindOpenidLock($unionid);
        } else {
            $openidtj = $this->FindOpenidLock($openid);
        }
        
        if (!$ucodetj && $openidtj) {
            $parr['acode'] = $openidtj['c_pcode'];
            $result = $this->AddUcodeLock($parr);
            return $result;
        }
        return Message(0,'没有锁定关系');
    }

    //查询微信,支付宝是否被锁定
    function FindOpenidLock($openid) {
        $where[] = array("c_openid='$openid' or c_unionid='$openid'");
        $result = M('Scanpay_tuijian')->where($where)->find();
        return $result;
    }

    //锁定微信openid
    function AddOpenidLock($parr) {
        //查询联盟信息
        $cwh['c_ucode'] = $parr['acode'];
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pid'])) {
            $pfederationid = $unioninfo['c_id'];
        } else {
            $pfederationid = $unioninfo['c_pid'];
        }
        $federationid = $unioninfo['c_id'];
        $add['c_pfederationid'] = $pfederationid;
        $add['c_federationid'] = $federationid;

        $add['c_openid'] = $parr['openid'];
        $add['c_pcode'] = $parr['acode'];
        $add['c_unionid'] = $parr['unionid'];
        $add['c_type'] = $parr['type'];
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Scanpay_tuijian')->add($add);
        if (!$result) {
            return Message(3000, '锁定用户关系失败');
        }
        return Message(0, '锁定用户关系成功');
    }

    //查询用户是否被锁定
    function FindUcodeLock($ucode) {
        $result = M('Users_tuijian')->where(array('c_ucode' => $ucode))->find();
        return $result;
    }

    //锁定用户
    function AddUcodeLock($parr) {
        $ucode = $parr['ucode'];
        $acode = $parr['acode'];
        $openid = $parr['openid'];

        if ($ucode == $acode) {
            //清空微信推荐关系
            if (!empty($openid)) {
                $openidwhere[] = array("c_openid='$openid' or c_unionid='$openid'");
                $result = M('Scanpay_tuijian')->where($openidwhere)->delete();
            }
            return Message(0, '自己不能绑定自己');
        }
        $userinfowhere['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($userinfowhere)->field('c_isagent,c_acode')->find();
        if ($userinfo['c_isagent'] > 0) {
            return Message(0, '锁定用户关系成功');
        }

        //查询联盟信息
        $cwh['c_ucode'] = $parr['acode'];
        $unioninfo = M('A_federation')->where($cwh)->find();
        if (empty($unioninfo['c_pid'])) {
            $pfederationid = $unioninfo['c_id'];
        } else {
            $pfederationid = $unioninfo['c_pid'];
        }
        $federationid = $unioninfo['c_id'];
        $add['c_pfederationid'] = $pfederationid;
        $add['c_federationid'] = $federationid;

        $add['c_ucode'] = $ucode;
        $add['c_pcode'] = $acode;
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $add['c_source'] = 3;
        $result = M('Users_tuijian')->add($add);
        if (!$result) {
            return Message(3000, '锁定用户关系失败');
        }

        $whereacodeinfo['c_ucode'] = $acode;
        $acodeinfo = M('Users')->where($whereacodeinfo)->getField('c_acode');
        if (!$acodeinfo) {
            return Message(1016, "未有所属代理商");
        }

        if (!$userinfo['c_acode']) {
            $datauserinfo['c_acode'] = $acodeinfo;
            $result = M('Users')->where($userinfowhere)->save($datauserinfo);
            if (!$result) {
                return Message(1016, "纳入代理商失败");
            }
        }

        //改变锁定状态
        if (is_weixin()) {
            $openidwhere['c_unionid'] = $openid;
        } else {
            $openidwhere['c_openid'] = $openid;
        }
        $openidsave['c_lock'] = 1;
        $result = M('Scanpay_tuijian')->where($openidwhere)->save($openidsave);

        return Message(0, '锁定用户关系成功');
    }
}
