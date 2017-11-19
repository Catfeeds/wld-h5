<?php

/**
 * 融云即时通信  好友关系
 * @author 谢秋林
 */
class FriendProcessRongcloud{
	/**
    * 添加密友 按条件查找蜜友（用户昵称、性别、行业、标签、所在地）
    * @param  pageindex，province,city,town,tab,name,trade,sex,longitude,latitude
    */
    public function Seachusers($parr){
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $juli = $parr['juli'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        if (!empty($parr['province'])) {
            $where['b.c_province'] = array('like', '%' . $parr['province'] . '%');
        }

        if (!empty($parr['city'])) {
            $where['b.c_city'] = array('like', '%' . $parr['city'] . '%');
        }

        if (!empty($parr['town'])) {
            $where['b.c_county'] = array('like', '%' . $parr['town'] . '%');
        }

        if (!empty($parr['tab'])) {
            $tabarr = explode('|', $parr['tab']);
            foreach ($tabarr as $key => $value) {
                if ($key == 0) {
                    $tabwhere .= "a.c_tab like '%" . $value . "%'";
                } else {
                    $tabwhere .= " or a.c_tab like '%" . $value . "%'";
                }
            }
            $where[] = $tabwhere;
        }

        if (!empty($parr['name'])) {
        	$name = $parr['name'];
        	$where[] = array("a.c_nickname like '%$name%' or a.c_phone='$name'");
        }

        if (!empty($parr['trade'])) {
            $tradebarr = explode('|', $parr['trade']);
            foreach ($tradebarr as $key => $value) {
                if ($key == 0) {
                    $tradwhere .= "a.c_trade like '%" . $value . "%'";
                } else {
                    $tradwhere .= " or a.c_trade like '%" . $value . "%'";
                }
            }
            $where[] = $tradwhere;
        }

        if (!empty($parr['sex'])) {
            $where['a.c_sex'] = array('like', '%' . $parr['sex'] . '%');
        }

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= ' . $parr['juli'] . '';
            }
            $order = 'case when ifnull(c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $order = 'a.c_id desc';
        }

        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_signature,a.c_province,a.c_sex,a.c_city,a.c_region,a.c_headimg,a.c_tab,a.c_trade,a.c_shop,b.c_latitude,b.c_longitude,'' as c_rongyun_token";

        $list = M('Users as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
        	$list[$key]['c_city'] = trim($value['c_city']);
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['c_friendstate'] = '0';

            if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
                $strb = "未知距离";
            } else {

                $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
                $str1 = sprintf("%.2f", $str1);
                if ($str1 < 1) {
                    $a = bcmul($str1, 1000, 2);
                    if ($a <= 10) {
                        $strb = "＜10m";
                    } else if ($a > 10 && $a <= 100) {
                        $strb = "＜100m";
                    } else {
                        $strb = sprintf("%.0f", $a) . "m";
                    }
                } else {
                    $strb = $str1 . "km";
                }
            }

            $list[$key]['c_distance'] = $strb;
        }

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }



    /**
     * 添加密友 按条件查找商户
     * @param  pageindex，name,longitude,latitude
     */
    public function SeachShopusers($parr){
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];
        $juli = $parr['juli'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

//        if (!empty($parr['name'])) {
//            $where['a.c_nickname'] = array('like ','%'.$parr['name'].'%');
//        }
        if (!empty($parr['name'])) {
            $search =$parr['name'];
            //$where['a.c_nickname'] = array('like ','%'.$parr['name'].'%');
            $where[] = array("a.c_nickname like '%$search%'");
        }

        if (!empty($longitude) && !empty($latitude)) {
            if ($juli > 0) {
                $where[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-b.c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(b.c_latitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-b.c_longitude)/360),2)))) <= ' . $parr['juli'] . '';
            }
            $order = 'case when ifnull(c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (c_longitude * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $order = 'a.c_id desc';
        }

        $where['a.c_shop'] =array('eq',1);

        $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
        $field = "a.c_ucode,a.c_nickname,a.c_signature,a.c_province,a.c_sex,a.c_city,a.c_region,a.c_headimg,a.c_tab,a.c_trade,a.c_shop,b.c_latitude,b.c_longitude,'' as c_rongyun_token";

        $list = M('Users as a')->join($join)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        if (!$list) {
            return MessageInfo(0, "查询成功", $list);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_city'] = trim($value['c_city']);
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
            $list[$key]['c_friendstate'] = '0';

            if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
                $strb = "未知距离";
            } else {

                $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
                $str1 = sprintf("%.2f", $str1);
                if ($str1 < 1) {
                    $a = bcmul($str1, 1000, 2);
                    if ($a <= 10) {
                        $strb = "＜10m";
                    } else if ($a > 10 && $a <= 100) {
                        $strb = "＜100m";
                    } else {
                        $strb = sprintf("%.0f", $a) . "m";
                    }
                } else {
                    $strb = $str1 . "km";
                }
            }

            $list[$key]['c_distance'] = $strb;
            $list[$key]['jumptype'] = 1;
        }

        $count = M('Users as a')->join($join)->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }



    /**
	* 发好友邀请
	* @param string $currentUserId 本人用户编号
	* @param string $id 被加好友编号
	* @param string $verifyMsg 验证消息
	*/
	function request_friend($currentUserId,$id,$verifyMsg){
		$App_Key = C(RONGCLOUD_APP_KEY);
		$App_Secret = C(RONGCLOUD_APP_SECRET);
		$RongCloud = new \Com\RongCloud\ServerAPI($App_Key,$App_Secret);

		if($currentUserId == $id){
			return Message(2001,"被添加的好友为自己");
		}

		if(empty($verifyMsg)){
			$verifyMsg = "你好！能否添加你为好友？";
		}
		//status : 1 好友, 2 请求添加, 3 请求被添加, 4 请求被拒绝, 5 我被对方删除
		$where['c_ucode'] = $id;
		$fcount = M('users')->where($where)->count();
		if($fcount == 0){
			return Message(2002,"被加好友不存在");
		}

		$db = M('friend_relation');
		$db -> startTrans();

		$my_fetch = array(
			'c_user_ucode' => $currentUserId,
			'c_friend_ucode' => $id,
		);
		$my_friend_status = $db->where($my_fetch)->field('c_status')->find();

		$other_fetch = array(
			'c_user_ucode' => $id,
			'c_friend_ucode' => $currentUserId,
		);
		$other_friend_status = $db->where($other_fetch)->field('c_status')->find();

		if ($my_friend_status && $other_friend_status) {
			if ($my_friend_status['c_status'] == 3) { //如果对方也加过我，则直接成为好友
				$status['c_status'] = 1;
				$result = $db->where($my_fetch)->save($status);
				$result1 = $db->where($other_fetch)->save($status);

				if($result && $result1){
					$db->commit();
					return Message(0,"添加成功");
				}else{
					$db->rollback();
					return Message(2003,"数据库异常");
				}
			} else if($my_friend_status['c_status'] == 2) { //重复请求
				//只向融云IM server发送消息
				$re_code = $RongCloud->messageSystemPublish('10000',array($id),'RC:ContactNtf',
				'{"sourceUserId":"'.$currentUserId.'","targetUserId":"'.$id.'","operation":"Request","message":"'.$verifyMsg.'"}','你收到一条好友邀请');

				$code = json_decode($re_code,true);
				if($code['code'] == 200){
					return Message(0,"请求发送成功");
				}else{
					return Message(2006,"请求发送失败");
				}
			}else if ($my_friend_status['c_status'] == 1) {
				return Message(2005,"你们已经是好友");
			}
		} else if ($my_friend_status){
			if ($my_friend_status['c_status'] == 1) { //已经是好友
				return Message(2005,"你们已经是好友");
			} else if($my_friend_status['c_status'] == 4) { //请求被拒绝时，重新发情请求
				$status['c_status'] = 2;
				$result = $db ->where($my_fetch)->save($status);
				$insert_date = array(
					'c_user_ucode' => $id,
					'c_friend_ucode' => $currentUserId,
					'c_status' => 3,
					'c_addtime' => date('Y-m-d H:i:s',time()),
				);
				$result1 = $db ->add($insert_date);

				if($result && $result1){
					$db->commit();
				}else{
					$db->rollback();
					return Message(2003,"数据库异常");
				}

				//向融云IM server发送消息
				$re_code = $RongCloud->messageSystemPublish('10000',array($id),'RC:ContactNtf',
				'{"sourceUserId":"'.$currentUserId.'","targetUserId":"'.$id.'","operation":"Request","message":"'.$verifyMsg.'"}','你收到一条好友邀请');

				$code = json_decode($re_code,true);
				if($code['code'] == 200){
					return Message(0,"请求发送成功");
				}else{
					return Message(2006,"请求发送失败");
				}
			} else {
				return Message(2007,"未知错误");//未知错误
			}
		} else if($other_friend_status){
			$insert_date = array(
				'c_user_ucode' => $currentUserId,
				'c_friend_ucode' => $id,
				'c_status' => 1,
				'c_addtime' => date('Y-m-d H:i:s',time()),
			);
			if ($other_friend_status['c_status'] == 1) {//之前成为过好友，则直接成为好友
				$result = $db ->add($insert_date);

				if($result){
					$db->commit();
					return Message(0,"添加成功");
				}else{
					$db->rollback();
					return Message(2003,"数据库异常");
				}
			} else if($other_friend_status['c_status'] == 4){ //之前被我拒绝过，则直接成为好友
				$result = $db ->add($insert_date);
				$status['c_status'] = 1;
				$result1 = $db->where($other_fetch)->save($status);

				if($result && $result1){
					$db->commit();
					return Message(0,"添加成功");
				}else{
					$db->rollback();
					return Message(2003,"数据库异常");
				}
			} else {
				return Message(2007,"未知错误");//未知错误
			}
		} else { //发出好友请求
			$insert_date1 = array(
				'c_user_ucode' => $currentUserId,
				'c_friend_ucode' => $id,
				'c_status' => 2,
				'c_addtime' => date('Y-m-d H:i:s',time()),
			);
			$result = $db ->add($insert_date1);
			$insert_date2 = array(
				'c_user_ucode' => $id,
				'c_friend_ucode' => $currentUserId,
				'c_status' => 3,
				'c_addtime' => date('Y-m-d H:i:s',time()),
			);
			$result1 = $db ->add($insert_date2);

			if($result && $result1){
					$db->commit();
				}else{
					$db->rollback();
					return Message(2003,"数据库异常");
				}
			//向融云IM server发送消息
			$re_code = $RongCloud->messageSystemPublish('10000',array($id),'RC:ContactNtf',
			'{"sourceUserId":"'.$currentUserId.'","targetUserId":"'.$id.'","operation":"Request","message":"'.$verifyMsg.'"}','你收到一条好友邀请');

			$code = json_decode($re_code,true);
			if($code['code'] == 200){
				return Message(0,"请求发送成功");
			}else{
				return Message(2006,"请求发送失败");
			}
		}
		return Message(2007,"未知错误");
	}

	/**
	* 处理好友邀请
	* @param string $currentUserId 本人用户编码
	* @param string $id 被加好友用户编码
	* @param int $is_access 是否接受，0-拒绝、1-接受
	*/
	function process_request_friend($currentUserId,$id,$is_access){
		$db = M('friend_relation');
		$db -> startTrans();

		$my_fetch = array(
			'c_user_ucode' => $currentUserId,
			'c_friend_ucode' => $id,
		);
		$my_friend_status = $db->where($my_fetch)->field('c_status')->find();

		$other_fetch = array(
			'c_user_ucode' => $id,
			'c_friend_ucode' => $currentUserId,
		);
		$other_friend_status = $db->where($other_fetch)->field('c_status')->find();

		if ($my_friend_status && $other_friend_status && $my_friend_status['c_status'] == 3 && $other_friend_status['c_status'] == 2){
			if($is_access){
				$update_date['c_updatetime'] = date('Y-m-d H:i:s',time());
				$update_date['c_status'] = 1;
				$result = $db->where($my_fetch)->save($update_date);
				$result1 = $db->where($other_fetch)->save($update_date);

				if($result && $result1){
					$db->commit();
				}else{
					$db->rollback();
					return Message(2001,"数据库异常");
				}

				$user_fetch1['c_ucode'] = $currentUserId;
				$user_name_1 = M('users')->where($user_fetch1)->getField('c_nickname');

				$user_fetch2['c_ucode'] = $id;
				$user_name_2 = M('users')->where($user_fetch2)->getField('c_nickname');

				//向融云IM server发送消息
				$App_Key = C(RONGCLOUD_APP_KEY);
				$App_Secret = C(RONGCLOUD_APP_SECRET);
				$RongCloud = new \Com\RongCloud\ServerAPI($App_Key,$App_Secret);

				$re_code1 = $RongCloud->messagePublish($currentUserId,array($id),'RC:InfoNtf',
					json_encode(array('message'=>'你已添加了'.$user_name_1.'，现在可以开始聊天了。')));
				$re_code2 = $RongCloud->messagePublish($id, array($currentUserId), 'RC:InfoNtf',
					json_encode(array('message'=>'你已添加了'.$user_name_2.'，现在可以开始聊天了。')));

				$code_arr1 = json_decode($re_code1,true);
				$code_arr2 = json_decode($re_code2,true);

				if($code_arr1['code'] == 200 && $code_arr2['code'] == 200){
					$data['fucode'] = $id;
					return MessageInfo(0,"添加成功",$data);
				}else{
					return Message(2002,"融云发送信息失败");
				}
			} else {
				$result = $db->where($my_fetch)->delete();
				$update_date['c_status'] = 4;
				$update_date['c_updatetime'] = date('Y-m-d H:i:s',time());
				$result1 = $db->where($other_fetch)->save($update_date);
				if($result && $result1){
					$db->commit();
					return Message(0,"已拒绝添加好友申请");
				}else{
					$db->rollback();
					return Message(2001,"数据库异常");
				}
			}
		} else {
			return Message(2002,"未知错误");
		}
	}

	/**
	* 删除好友数据
	* @param string $currentUserId 本人用户编码
	* @param string $id 被加好友用户编码
	*/
	function delete_friend($currentUserId,$id){
		$db = M('friend_relation');
		$db -> startTrans();

		$my_fetch = array(
			'c_user_ucode' => $currentUserId,
			'c_friend_ucode' => $id,
		);
		$my_friend_status = $db->where($my_fetch)->field('c_status')->find();

		$other_fetch = array(
			'c_user_ucode' => $id,
			'c_friend_ucode' => $currentUserId,
		);
		$other_friend_status = $db->where($other_fetch)->field('c_status')->find();

		if($my_friend_status && $other_friend_status){
			if($my_friend_status['c_status'] == 1){ //我们是好友
				$result = $db->where($my_fetch)->delete();
				$update_date['c_status'] = 5;
				$update_date['c_updatetime'] = date('Y-m-d H:i:s',time());
				$result1 = $db->where($other_fetch)->save($update_date);
			}else if($my_friend_status['c_status'] == 2){// 删除请求
				$result = $db->where($my_fetch)->delete();
				$result1 = $db->where($other_fetch)->delete();
			}else if($my_friend_status['c_status'] == 3){// 删除请求
				$result = $db->where($my_fetch)->delete();
				$update_date['c_status'] = 4;
				$update_date['c_updatetime'] = date('Y-m-d H:i:s',time());
				$result1 = $db->where($other_fetch)->save($update_date);
			}else{
				return Message(2002,"未知错误");
			}

			if($result && $result1){
				$db->commit();
				return Message(0,"删除成功");
			}else{
				$db->rollback();
				return Message(2001,"删除失败");
			}

		}else if($my_friend_status){
			$result = $db->where($my_fetch)->delete();
			if($result){
				$db->commit();
				return Message(0,"删除成功");
			}else{
				$db->rollback();
				return Message(2001,"删除失败");
			}
		}else{
			return Message(2002,"未知错误");
		}
	}

	/**
	* 获取好友列表
	* @param string $currentUserId 本人用户编码
	*/
	function get_friend($currentUserId){
		$sql = "SELECT u.c_ucode,u.c_nickname,u.c_headimg,u.c_signature,u.c_shop,p.c_rongyun_token,r.c_remark_name,r.c_remark_phone,r.c_remark_desc,r.c_status FROM t_friend_relation AS r ";
		$sql .= "LEFT JOIN t_users AS u ON r.c_friend_ucode=u.c_ucode ";
		$sql .= "LEFT JOIN t_user_part AS p ON r.c_friend_ucode=p.c_ucode ";
		$sql .= "WHERE r.c_user_ucode='".$currentUserId."' AND r.c_status=1 ";
		$sql .= "order by u.c_nickname asc ";

		$model = M('');
		$friends = $model->query($sql);

		foreach ($friends as $k=>$v) {
			if($v['c_remark_name']){
				$friends[$k]['c_nickname'] = $v['c_remark_name'];
			}
			// $friends[$k]['firstchar'] = $this->getfirstchar($friends[$k]['c_nickname']);
			$friends[$k]['c_headimg'] = GetHost().'/'.$friends[$k]['c_headimg'];
		}

		$sort = array(  
        	'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
        	'field'     => 'firstchar',       //排序字段  
		);  
		// $arrSort = array();  
		// foreach($friends AS $uniqid => $row){  
		//     foreach($row AS $key=>$value){  
		//         $arrSort[$key][$uniqid] = $value;  
		//     }  
		// }  
		// if($sort['direction']){  
		//     array_multisort($arrSort[$sort['field']], constant($sort['direction']), $friends);  
		// }  

		return $friends;
	}

	//php获取中文字符拼音首字母
	function getfirstchar($str){
	    if(empty($str)){return '';}
	    $fchar=ord($str{0});
	    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
	    $s1=iconv('UTF-8','gb2312',$str);
	    $s2=iconv('gb2312','UTF-8',$s1);
	    $s=$s2==$str?$s1:$str;
	    $asc=ord($s{0})*256+ord($s{1})-65536;
	    
	    if($asc>=-20319&&$asc<=-20284) return 'A';
	    if($asc>=-20283&&$asc<=-19776) return 'B';
	    if($asc>=-19775&&$asc<=-19219) return 'C';
	    if($asc>=-19218&&$asc<=-18711) return 'D';
	    if($asc>=-18710&&$asc<=-18527) return 'E';
	    if($asc>=-18526&&$asc<=-18240) return 'F';
	    if($asc>=-18239&&$asc<=-17923) return 'G';
	    if($asc>=-17922&&$asc<=-17418) return 'H';
	    if($asc>=-17417&&$asc<=-16475) return 'J';
	    if($asc>=-16474&&$asc<=-16213) return 'K';
	    if($asc>=-16212&&$asc<=-15641) return 'L';
	    if($asc>=-15640&&$asc<=-15166) return 'M';
	    if($asc>=-15165&&$asc<=-14923) return 'N';
	    if($asc>=-14922&&$asc<=-14915) return 'O';
	    if($asc>=-14914&&$asc<=-14631) return 'P';
	    if($asc>=-14630&&$asc<=-14150) return 'Q';
	    if($asc>=-14149&&$asc<=-14091) return 'R';
	    if($asc>=-14090&&$asc<=-13319) return 'S';
	    if($asc>=-13318&&$asc<=-12839) return 'T';
	    if($asc>=-12838&&$asc<=-12557) return 'W';
	    if($asc>=-12556&&$asc<=-11848) return 'X';
	    if($asc>=-11847&&$asc<=-11056) return 'Y';
	    if($asc>=-11055&&$asc<=-10247) return 'Z';
	    return null;
	}

	/**
	* 修改好友备注名称
	* @param string $currentUserId 本人用户编码
	*/
	function Editremark($parr){
		$db = M('friend_relation');

		$where['c_user_ucode'] = $parr['ucode'];
		$where['c_friend_ucode'] = $parr['fucode'];
		$where['c_status'] = 1; //只有好友才能修改备注

		$relation = $db->where($where)->find();

		if(!$relation){
			return Message(1001,"好友信息不存在！");
		}

		$update_date['c_remark_name'] = $parr['remarkname'];
		$update_date['c_remark_phone'] = $parr['remarkphone'];
		$update_date['c_remark_desc'] = $parr['remarkdesc'];
		$update_date['c_updatetime'] = date('Y-m-d H:i:s',time());

		$result = $db->where($where)->save($update_date);

		if(!$result){
			return Message(1002,"修改失败！");
		}

		return Message(0,"修改成功！");
	}

	/**
    * 聊天页面头部数据
    * @param openid,to_ucode
    */
    function chat_head($parr){
    	$ucode = $parr['ucode'];
    	$to_ucode = $parr['to_ucode'];

    	$w['c_ucode'] = $to_ucode;
    	$userinfo = M('Users')->field('c_ucode,c_nickname,c_headimg,c_shop')->where($w)->find();

    	if(!$userinfo){
    		return Message(1001,"用户信息不存在");
    	}

    	$userinfo['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

    	
    	$w2['c_user_ucode'] = $ucode;
    	$w2['c_friend_ucode'] = $to_ucode;
    	$friend_relation = M('friend_relation')->where($w2)->find();

    	//是否有备注
    	if($friend_relation['c_remark_name']){
    		$userinfo['c_nickname'] = $friend_relation['c_remark_name'];
    	}

    	$userinfo['c_remark_phone'] = $friend_relation['c_remark_phone'];
    	$userinfo['c_remark_desc'] = $friend_relation['c_remark_desc'];

    	//是否是好友
    	$isfriend = 0;
    	if($friend_relation['c_status'] == 1){
    		$isfriend = 1;
    	}
    	$userinfo['isfriend'] = $isfriend;

    	//是否关注
    	$w3['c_ucode'] = $ucode;
    	$w3['c_attention_ucode'] = $to_ucode;
    	$attention = M('users_attention')->where($w3)->find();

    	$isattention = 0;
    	if($attention){
    		$isattention = 1;
    	}
    	$userinfo['isattention'] = $isattention;

        return MessageInfo(0,"查询成功",$userinfo);
    }
}