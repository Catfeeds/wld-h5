<?php
/**
 * 	商家成为加盟店总店、连锁店总店身份
 *
 */
class IdentityStore {
	/**
	 * 身份添加
	 * @param ucode,itype(1连锁总店，2加盟总店)
	 */
	function AddIdentity($parr){
		//确人用户信息
		$w['c_ucode'] = $parr['ucode'];
		
		$userinfo = M('Users')->where($w)->find();

		if(!$userinfo){
			return Message(3001,"该用户信息不存在！");
		}
		
		if($userinfo['c_shop'] == 0){
			return Message(3002,"该用户不为商家不能操作！");
		}

		//确人商家资料
		$checkinfo = M('Check_shopinfo')->field('c_id,c_role')->where($w)->find();
		
		if(!$checkinfo || ($checkinfo['c_role'] != 0 && !empty($checkinfo))){
			return Message(3003,"该用户具备特色身份！");
		}

		//确定特色身份是否存在
		$roles = M('A_federation')->where($w)->find();
		if($roles){
			return Message(3004,'该商家已经存在特殊身份');
		}

		//开始添加信息
		$db = M('');
		$db -> startTrans();

		//修改商家资料
		$itype = $parr['itype'];

		$cdata['c_role'] = $itype;
		$result = M('Check_shopinfo')->where($w)->save($cdata);

		if($result < 0){
			$db->rollback();
			return Message(3005,'操作失败');
		}

		//添加加盟店、连锁店信息
		$add['c_ucode'] = $parr['ucode'];
		$add['c_name'] = $userinfo['c_nickname'];
		$add['c_type'] = $itype;
		$add['c_status'] = 1;
		$add['c_sign'] = 1;
		$add['c_addtime'] = gdtime();

		$result = M('A_federation')->add($add);

		if($result < 0){
			$db->rollback();
			return Message(3006,'操作失败');
		}

		$pfederationid = $result;

		//同步联盟临时会员
        $tjsave['c_pfederationid'] = $pfederationid;
        $tjsave['c_federationid'] = $pfederationid;
        $tjwhere['c_pcode'] = $parr['ucode'];

        if (M('Scanpay_tuijian')->where($tjwhere)->getField('c_id')) {
        	$result = M('Scanpay_tuijian')->where($tjwhere)->save($tjsave);
	        if (!$result) {
	        	$db->rollback();
	            return Message(3007,'同步联盟会员失败');
	        }
        }
        
        if (M('Users_tuijian')->where($tjwhere)->getField('c_id')) {
	        //同步联盟小蜜会员
	        $result = M('Users_tuijian')->where($tjwhere)->save($tjsave);
	        if (!$result) {
	        	$db->rollback();
	            return Message(3008,'同步联盟会员失败');
	        }
	    }

        //操作成功给商家发送消息
        if($itype == 1){
        	$content = "尊敬的用户，小蜜已经为您开通连锁店总店身份！";
        	$furl = GetHost(1) . '/index.php/Store/Multshop/index';
        }else{
        	$content = "尊敬的用户，小蜜已经为您开通加盟店总店身份！";
        	$furl = GetHost(1) . '/index.php/Store/Leagshop/index';
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['ucode'];
        $msgdata['type'] = 0;
        $msgdata['platform'] = 1;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['tag'] = 2;
        $msgdata['content'] = $content;
        $msgdata['tagvalue'] = $furl;
        $msgdata['weburl'] = $furl;
        $msgresult = $Msgcentre->CreateMessegeInfo($msgdata);

        $db->commit();
        return Message(0,'操作成功');
	}

}