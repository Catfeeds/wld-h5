<?php
/**
 * 商城模块
 *
 */
class MallUser {
	/** 
	*   商城首页与内容页数据接口
	*   @param state(1为预览，2为发布)
	*/
	public function GetMallHompage($parr) {
		//获取各模块数据
		$state = $parr['state'];

		if($state == 2){
			$mw['c_state'] = 3;
		}else{
			$mw['c_state'] = 2;
		}		

	    $list = M('Mall_homepage')->where($mw)->order('c_sort asc')->select();
	    
	    if (count($list) == 0) {
	    	$list = array();
	        return MessageInfo(0,'查询成功',$list);
	    }

	    foreach ($list as $key => $value) {
	    	$list[$key]['c_themeimg'] = GetHost().'/'.$value['c_themeimg'];
	    	$list[$key]['c_subimg'] = GetHost().'/'.$value['c_subimg'];
	    	$list[$key]['c_begintime1'] = strtotime($value['c_begintime']) - time();
	        $list[$key]['c_endtime1'] = strtotime($value['c_endtime']) - time();

	        //获取模块内容
	    	$subw['c_homeid'] = $value['c_id'];
	    	$subw['c_state'] = 2;

	    	$sublist = M('Mall_homepage_img')->where($subw)->order('c_sort asc')->select();

	    	foreach ($sublist as $key1 => $value1) {
	    		$sublist[$key1]['c_img'] = GetHost().'/'.$value1['c_img'];
	    	}

	    	$list[$key]['imglist'] = $sublist;

	    	//获取活动数据
	    	if($value['c_isactivity'] != 0){
	    		$actproduct = $this->GetActProduct($value['c_isactivity']);

	    		if(empty($actproduct)){
	    			$actproduct = array();
	    		}

	    		$list[$key]['actproduct'] = $actproduct;
	    	}	        
	    }
	    
	    return MessageInfo(0,'查询成功',$list);
	}

	/** 
	*   获取活动产品数据
	*   @param type(1-砍价活动，2-秒杀活动，3-抢购活动，4-拼团活动)
	*/
	public function GetActProduct($type){
		$actproduct = array();
		switch ($type) {
			case 1:
				//查询平台活动aid
				$result = IGD('Index','Newact')->GetPlathavingAct(27,2);

				if($result['code'] != 0){
					break; 
				}

				$aid = $result['data']['c_id'];

				$pw['c_aid'] = $aid;
				$pw['c_state'] = 1;

				$actproduct = M('Shopact_product')->where($pw)->limit(9)->order('rand()')->select();

				foreach ($actproduct as $key => $value) {
					$actproduct[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
					$actproduct[$key]['c_tag'] = 1;
					$actproduct[$key]['c_tagvalue'] = '';
					$actproduct[$key]['c_weburl'] = GetHost().'/index.php/Shopping/Bargain/pdetail?act_pcode='.$value['c_act_pcode'];
				}

				break;
			case 2:
				//查询平台活动aid
				$result = IGD('Index','Newact')->GetPlathavingAct(28,2);

				if($result['code'] != 0){
					break; 
				}

				$aid = $result['data']['c_id'];

				$pw['c_aid'] = $aid;
				$pw['c_state'] = 1;

				$actproduct = M('Shopact_product')->where($pw)->limit(9)->order('rand()')->select();

				foreach ($actproduct as $key => $value) {
					$actproduct[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
					$actproduct[$key]['c_tag'] = 1;
					$actproduct[$key]['c_tagvalue'] = '';
					$actproduct[$key]['c_weburl'] = GetHost().'/index.php/Shopping/Seckill/pdetail?act_pcode='.$value['c_act_pcode'];
				}

				break;
			case 3:
				// //制造虚假数据
				// $actproduct = array();
				// for ($i=0; $i < 9; $i++) { 
				// 	$actproduct[$i]['c_pcode'] = 'p1234560'.$i;
				// 	$actproduct[$i]['c_ucode'] = 'u1234560'.$i;
				// 	$actproduct[$i]['c_name'] = '商品0'.$i;
				// 	$i1 = $i + 1;
				// 	$actproduct[$i]['c_imgpath'] = GetHost().'/Uploads/propic/Bitmap'.$i1.'.png';
				// 	$actproduct[$i]['c_actprice'] = '123'.$i;
				// 	$actproduct[$i]['c_value'] = '223'.$i;
				// 	$actproduct[$i]['c_tag'] = 1;
				// 	$actproduct[$i]['c_tagvalue'] = '';
				// 	$actproduct[$i]['c_weburl'] = 'www.baidu.com';
				// }
				break;
			case 4:
				//查询平台活动aid
				$result = IGD('Index','Newact')->GetPlathavingAct(26,2);

				if($result['code'] != 0){
					break; 
				}

				$aid = $result['data']['c_id'];

				$pw['c_aid'] = $aid;
				$pw['c_state'] = 1;

				$actproduct = M('Shopact_product')->where($pw)->limit(9)->order('rand()')->select();

				foreach ($actproduct as $key => $value) {
					$actproduct[$key]['c_imgpath'] = GetHost().'/'.$value['c_imgpath'];
					$actproduct[$key]['c_tag'] = 1;
					$actproduct[$key]['c_tagvalue'] = '';
					$actproduct[$key]['c_weburl'] = GetHost().'/index.php/Shopping/Collage/pdetail?act_pcode='.$value['c_act_pcode'];
				}

				break;
			default:
				$actproduct = array();
				break;
		}

		return $actproduct;
	}

	/**
	 * 商城首页获取推荐商品(已修改)
	 * @param 
	 */
	Public function ProductTjList($parr){
		$pw['t.c_state'] = 1;
		$pw['p.c_ishow'] = 1;
		$pw['p.c_isdele'] = 1;
		$pw['p.c_isagent'] = 0;

		$field = "t.c_sort,p.c_pcode,p.c_ucode,p.c_name,p.c_source,p.c_pimg,p.c_price";
		$join = "inner join t_product as p on p.c_pcode=t.c_pcode";
		$order = "t.c_sort asc";

		$list = M('Product_tj as t')->field($field)->join($join)->where($pw)->limit(9)->order($order)->select();

		if(!$list){
			$list = array();
			return MessageInfo(0,"查询成功",$list);
		}

		foreach ($list as $key => $value) {
			$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
		}

		return MessageInfo(0,"查询成功",$list);
	}

    /**
     * 获取 举报规则列表
     * @param
    */

    public function GetTipsLists(){
        $where['status']=1;
        $data =M('Tipsinfo')->where($where)->field("c_tid,c_content,c_flag")->select();
        return MessageInfo(0,"查询成功",$data);
    }


    /**
     * 提交 举报
     * @param tip_id,content_id
    */

    public function PutTipInfos($parr){

        if(empty($parr['ucode']) ||empty($parr['tip_id']) || empty($parr['content_id'])){
           return Message(1001,"缺少参数");
        }
        $add['c_ucode']=$parr['ucode'];
        $add['c_tip_id']=$parr['tip_id'];
        $add['c_content_id']=$parr['content_id'];
        $add['c_content'] =$parr['content'];
        $add['c_ctime'] =date('Y-m-d H:i:s');


        $info =M('Resource')->where(array('c_id'=>$parr['content_id']))->find();
        $userInfo =M('Users')->where(array('c_ucode'=>$info['c_ucode']))->field('c_nickname')->find();
        $circle =M('Circle')->where(array('c_provincecode'=>$info['c_provincecode'],'c_citycode'=>$info['c_citycode']))->field('c_name')->find();

        $con['c_ucode']=$parr['ucode'];
        $con['c_content_id']=$parr['content_id'];

        $find =M("Usertip_record")->where($con)->find();
        if(!empty($find)){
            return Message(1004,"同一用户对同一动态不能多次举报");
        }

        //获取 举报 内容的
        $arr['c_tid'] =$parr['tip_id'];
        $coninfo =M('tipsinfo')->where($arr)->getField('c_content');


        $where['c_content_id']=$parr['content_id'];
        $count =M('Usertip_record')->where($where)->count();

        if($count>3){  //大于3  则表示有4条以上举报记录 加上当前这条 共5条举报 就影藏该动态
          //把当前状态改为 已删除
            $Msg1 = IGD('Msgcentre', 'Message');
            $msgdata['ucode'] = $info['c_ucode'];
            $msgdata['type'] = 0;
            $msgdata['platform'] = 1;
            $msgdata['sendnum'] = 1;
            $msgdata['title'] = '系统消息';
            $msgdata['tag'] = 39;
            $msgdata['content'] = '【警告】由于您在'.$info['c_addtime'].'在'.$circle['c_name'].'发布的商圈动态受到多名用户举报，经系统诊断，认为情况较为恶劣，系统已自动删除该条动态！';
            $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['content_id'];
            $msgdata['tagvalue'] = $parr['content_id'];

            $Msg1->CreateMessege($msgdata);   //写入消息

            $Msg2 = IGD('Msgcentre', 'Message');
            $msg['ucode'] = $parr['ucode'];
            $msg['type'] = 0;
            $msg['platform'] = 1;
            $msg['sendnum'] = 1;
            $msg['title'] = '系统消息';
            $msg['tag'] = 10000;
            $msg['content'] = '【通知】您在'.date('Y-m-d H:i:s').'在'.$circle['c_name'].'举报的'.$userInfo['c_nickname'].'发布的动态，有'.$coninfo.'行为，我们已经郑重警告该用户，系统已删除该条动态，感谢您的积极反应，我们已进行严肃处理！';
            $msg['weburl'] = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['content_id'];
            $msg['tagvalue'] = -1;
            $Msg2->CreateMessege($msg);   //写入消息
            $data=1;
          M('Resource')->where(array('c_id'=>$parr['content_id']))->save(array('c_status'=>0));
        }else{
            //写入消息中心
            $Msg3 = IGD('Msgcentre', 'Message');
            $msg3['ucode'] = $parr['ucode'];
            $msg3['type'] = 0;
            $msg3['platform'] = 1;
            $msg3['sendnum'] = 1;
            $msg3['title'] = '系统消息';
            $msg3['tag'] = 10000;
            $msg3['content'] = '【通知】您在'.date('Y-m-d H:i:s').'在'.$circle['c_name'].'举报的'.$userInfo['c_nickname'].'发布的动态，有'.$coninfo.'行为，我们已反应处理，感谢您的积极反应，我们已进行严肃处理！';
            $msg3['weburl'] = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['content_id'];
            $msg3['tagvalue'] = -1;

            $Msg3->CreateMessege($msg3);   //写入消息

            $data=0;
        }

        $result =M('Usertip_record')->add($add);
        if(!$result){
            return Message(1002,"提交失败");
        }

        return MessageInfo(0,"操作成功",$data);

    }


    /**
     * 提交申诉
     * @param ucode Id content
    */
    public function SubmitAppeal($parr){

        if(empty($parr['ucode']) ||empty($parr['Id'])){
            return Message(1001,"缺少参数");
        }
        if(trim($parr['content'])==''){
            return Message(1001,'申诉理由不能为空');
        }
        $find =M('Usertip_appeal')->where(array('c_ucode'=>$parr['ucode'],'c_tip_id'=>$parr['Id']))->find();
        if(!empty($find)){
            return Message(1001,'同一动态不能重复申诉');
        }
        $add['c_ucode']=$parr['ucode'];
        $add['c_tip_id']=$parr['Id'];;
        $add['c_content'] =$parr['content'];
        $add['c_addtime'] =date('Y-m-d H:i:s');

        $result =M('Usertip_appeal')->add($add);
        if(!$result){
            return Message(1001,'保存失败');
        }
        return Message(0,'提交成功');

    }
    /**
     *  获取关键字 列表
     * @param
     */
    public function GetKeywordsList(){
//        if(!empty($parr['name'])){
//            $where[] = array("key_name like '%" . $parr['name'] . "%'");
//        }
        $where['status']=1;
        $list =M('Keywords')->where($where)->field('c_key_name')->select();

        return MessageInfo(0, '查询成功', $list);
    }

    /**
     * 获取搜索条件列表
     *
    */

    public function GetSearchConditionList(){

        $where['status']=1;
        $data =M('Search_category')->where($where)->field("c_tid,c_name")->select();
        return MessageInfo(0,"查询成功",$data);
    }


	/**
	 *  所有商品分类列表及搜索
	 *  @param pageindex,pagesize,pname,categoryid,source
	 *  
	 */
	public function AllProductList($parr) {
	    if (empty($parr['pageindex'])) {
	        $pageIndex = 1;
	    } else {
	        $pageIndex = $parr['pageindex'];
	    }

	    if ($parr['categoryid'] == 50) {
	        $parr['categoryid'] = 0;
	    }

	    $pageSize = $parr['pagesize'];
	    $countPage = ($pageIndex - 1) * $pageSize;

	    $where['c_ishow'] = 1;
	    $where['c_isdele'] = 1;
	    $where['c_isagent'] = 0;
	    if (!empty($parr['source'])) {
	    	$where['c_source'] = 1;
	    }

	    if (!empty($parr['pname'])) {
	        $where[] = array("c_name like '%" . $parr['pname'] . "%'");
	    }

	    if (!empty($parr['categoryid'])) {
	        $where[] = array("c_categoryid=" . $parr['categoryid']);
	    }

        if(!empty($parr['order_type'])){
            if($parr['order_type']=='4'){  //销量
                $order ='c_salesnum desc';
            }elseif($parr['order_type']=='0'){  //综合
                $order ='c_salesnum desc,c_price desc';
            }elseif($parr['order_type']=='1'){  //信用
                $order ='c_salesnum desc';
            }elseif($parr['order_type']=='2'){  //价格 高-->低
                $order='c_price desc';
            }elseif($parr['order_type']=='3'){  //价格  低-->高
                $order='c_price asc';
            }
        }else{
            $order = 'c_id desc';
        }

	    $list = M('Product')->where($where)->order($order)->limit($countPage, $pageSize)->select();

	    $count = M('Product')->where($where)->count();
	    $pageCount = ceil($count / $pageSize);

	    if (count($list) == 0) {
	        $list = array();
	        $data = Page($pageIndex, $pageCount, $count, $list);
	        return MessageInfo(0, '查询成功', $data);
	    }

	    foreach ($list as $key => $value) {
	        $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
	        $list[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?type=1&pcode=" . $value['c_pcode'];
            $con['c_ucode']=$value['c_ucode'];
            $province =M('User_local')->where($con)->find();
            $list[$key]['c_province'] =$province['c_province']?$province['c_province']:'未知';
	    }

	    $data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	/**
	 *  附近线下商品分类列表及搜索
	 *  @param pageindex,pagesize,ucode,pname,categoryid,可传可不传(longitude,latitude)
	 *  
	 */
	function NearbyProductList($parr) {
	    $longitude = $parr['longitude'];
	    $latitude = $parr['latitude'];

	    if (empty($parr['pageindex'])) {
	        $pageIndex = 1;
	    } else {
	        $pageIndex = $parr['pageindex'];
	    }

	    if ($parr['categoryid'] == 50) {
	        $parr['categoryid'] = 0;
	    }

	    $pageSize = $parr['pagesize'];
	    $countPage = ($pageIndex - 1) * $pageSize;

	    $where['a.c_ishow'] = 1;
	    $where['a.c_isdele'] = 1;
	    $where['a.c_source'] = 2;
	    $where['a.c_isagent'] = 0;

	    if (!empty($parr['pname'])) {
	        $where['a.c_name'] = array('like', '%' . $parr['pname'] . '%');
	    }

	    if (!empty($parr['categoryid'])) {
	        $where['a.c_categoryid'] = $parr['categoryid'];
	    }

        if (!empty($longitude) && !empty($latitude)) {
            $order = 'case when ifnull(b.c_latitude,"")="" then 0 else 1 end desc,ACOS(SIN((' . $latitude . ' * 3.1415) / 180 ) *SIN((b.c_latitude * 3.1415) / 180 ) +COS((' . $latitude . ' * 3.1415) / 180 ) * COS((b.c_latitude * 3.1415) / 180 ) *COS((' . $longitude . ' * 3.1415) / 180 - (b.c_longitude * 3.1415) / 180 ) ) * 6380 asc';
        } else {
            $localtion = GetAreafromIp();
            $longitude = $localtion['longitude'];
            $latitude = $localtion['latitude'];
            $order = 'a.c_id desc';
        }
	    
	    $join = 'left join t_user_local as b on a.c_ucode=b.c_ucode';
	    $join1 = 'left join t_users as u on a.c_ucode=u.c_ucode';
	    $field = 'a.*,b.c_longitude,b.c_latitude,u.c_nickname';

	    $list = M('Product as a')->join($join)->join($join1)->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

	    $count = M('Product as a')->join($join)->where($where)->count();
	    $pageCount = ceil($count / $pageSize);

	    if (count($list) == 0) {
	        $list = array();
	        $data = Page($pageIndex, $pageCount, $count, $list);
	        return MessageInfo(0, '查询成功', $data);
	    }

	    foreach ($list as $key => $value) {
	        $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
	        if (empty($gettype)) {
	            $list[$key]['c_distance'] = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
	        }
	        $list[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?type=1&pcode=" . $value['c_pcode'];

	        if (empty($value['c_longitude']) || empty($value['c_latitude'])) {
	            $strb = "未知距离";
	        } else {

	            $str1 = GetDistance($longitude, $latitude, $value['c_longitude'], $value['c_latitude']);
	            $str1 = sprintf("%.2f", $str1);
	            if ($str1 < 1) {
	                $a = bcmul($str1, 1000, 2);
	                    $strb = "＜10m";
                        if ($a <= 10) {
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

	    $data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	//商品分类
	public function CategoryList($parr) {
	    $where['c_isshow'] = 1;
	    $data = M('Category')->where($where)->select();
	    foreach ($data as $key => $value) {
	        $data[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
	    }
	    if (count($data) == 0) {
	        $data = array();
	    }
	    return MessageInfo(0, '查询成功', $data);
	}

	/**
	 *  猜你喜欢产品列表
	 *  @param pageindex,pagesize,ucode
	 *  
	 */
	public function GuessProduct($parr){
		if (empty($parr['pageindex'])) {
		    $pageIndex = 1;
		} else {
		    $pageIndex = $parr['pageindex'];
		}
	
		$pageSize = $parr['pagesize'];
		$countPage = ($pageIndex - 1) * $pageSize;		
		$ucode = $parr['ucode'];

		$fed = 'a.*';
		$where['a.c_ishow'] = 1;
		$where['a.c_isdele'] = 1;
		$where['a.c_isagent'] = 0;
		$where[] = array('b.c_ucode is null');
		$join = "left join t_guess_nolike as b on b.c_pcode=a.c_pcode and b.c_ucode='$ucode'";	

		$count = M('Product as a')->join($join)->where($where)->count();
		//循坏显示
		$dif = $pageIndex - ceil($count / $pageSize);
		if($dif > 0){
			$pageIndex = 1;
			$countPage = ($pageIndex - 1) * $pageSize;
		}

		$list = M('Product as a')->join($join)->where($where)->field($fed)->order('rand()')->limit($countPage, $pageSize)->select();
		foreach ($list as $key => $value) {
			$uw['c_ucode'] = $value['c_ucode'];
			$userinfo = M('Users')->field('c_headimg,c_nickname')->where($uw)->find();

			$list[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
			$list[$key]['c_nickname'] = $userinfo['c_nickname'];
			$list[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
		}

		//随机查询喜欢的产品类型产品列表
		$rand = rand(1,4);
		if($rand == 2 || count($list) <= 0){
			$lw['c_ucode'] = $ucode;
			$like_category = M('Guess_like')->where($lw)->order('rand()')->find();
			if($like_category){
				$pw['c_categoryid'] = $like_category['c_categoryid'];
				$pw['c_ishow'] = 1;
				$pw['c_isdele'] = 1;
				$pw['c_isagent'] = 0;
				$like_arr = M('Product')->where($pw)->limit(5)->order('rand()')->select();
				foreach ($like_arr as $key => $value) {
					$uw['c_ucode'] = $value['c_ucode'];
					$userinfo = M('Users')->field('c_headimg,c_nickname')->where($uw)->find();

					$like_arr[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
					$like_arr[$key]['c_nickname'] = $userinfo['c_nickname'];
					$like_arr[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

					$tempcount = count($like_arr);
					$list[$tempcount] = $like_arr[$key];
				}
			}
		}

		$pageCount = ceil($count / $pageSize);
		$data = Page($pageIndex, $pageCount, $count, $list);
	    return MessageInfo(0, '查询成功', $data);
	}

	/**
	 *  猜你喜欢 喜欢操作
	 *  @param ucode,pcode,categoryid
	 *  
	 */
	public function LikeProduct($parr){
		$ucode = $parr['ucode'];
		$pcode = $parr['pcode'];
		$categoryid = $parr['categoryid'];

		if(empty($ucode) || empty($pcode) || empty($categoryid)){
			return Message(1001,"缺少参数");
		}

		$w['c_ucode'] = $ucode;
		$w['c_pcode'] = $pcode;

		$result = M('Guess_like')->where($w)->find();

		if(!$result){
			$add['c_ucode'] = $ucode;
			$add['c_pcode'] = $pcode;
			$add['c_categoryid'] = $categoryid;
			$add['c_addtime'] = gdtime();

			$result = M('Guess_like')->add($add);

			if(!$result){
				return Message(1002,"添加失败");
			}
		}

		return Message(0,"操作成功");
	}

	/**
	 *  猜你喜欢 不喜欢操作
	 *  @param ucode,pcode,
	 *  
	 */
	public function NolikeProduct($parr){
		$ucode = $parr['ucode'];
		$pcode = $parr['pcode'];

		if(empty($ucode) || empty($pcode)){
			return Message(1001,"缺少参数");
		}

		$w['c_ucode'] = $ucode;
		$w['c_pcode'] = $pcode;

		$result = M('Guess_nolike')->where($w)->find();

		if(!$result){
			$add['c_ucode'] = $ucode;
			$add['c_pcode'] = $pcode;
			$add['c_addtime'] = gdtime();

			$result = M('Guess_nolike')->add($add);

			if(!$result){
				return Message(1002,"添加失败");
			}
		}

		return Message(0,"操作成功");
	}

}