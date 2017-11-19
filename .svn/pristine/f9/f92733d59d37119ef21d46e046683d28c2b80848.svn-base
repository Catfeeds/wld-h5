<?php

/**
 * 	实体店铺管理
 *
 */
class StoreStore {

	/**
	 * 查询实体店铺信息
	 * @param ucode,storeid,acode
	 */
	function GetStoreInfo($parr)
	{
		if (!empty($parr['storeid'])) {
			$where['c_id'] = $parr['storeid'];
		} else {
            if (!empty($parr['acode'])) {
                $where['c_ucode'] = $parr['acode'];
                if ($parr['ucode'] != $parr['acode'] && !empty($parr['ucode'])) {
                    //增加个人空间浏览量
                    $wv['ucode'] = $parr['ucode'];
                    $wv['vucode'] = $parr['acode'];
                    $get_app_type  = get_app_type();
                    if($get_app_type == 1) {
                        $wv['source'] = 'Android';
                    } else if($get_app_type == 2) {
                        $wv['source'] = 'IOS';
                    } else {
                        $wv['source'] = 'WEB';
                    }

                    $w1['c_ucode'] = $parr['ucode'];
                    $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
                    $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
                    $wv['nickname'] = $userinfo['c_nickname'];
                    $wv['headimg'] = $userinfo['c_headimg'];
                    $wv['browser'] = GetBrowser();
                    $wv['ip'] = GetIP();
                    $wv['type'] = 2;
                    $result = IGD('Resource','Trade')->SpacePv($wv);
                }
            } else {
                $where['c_ucode'] = $parr['ucode'];
            }
        }

		$data = M('Store')->where($where)->order('c_id desc')->find();

        $userwhere['c_ucode'] = empty($data['c_ucode'])?$where['c_ucode']:$data['c_ucode'];
        $suserinfo = M('Users')->where($userwhere)->field('c_nickname,c_isfixed1')->find();
        $data['c_nickname'] = $suserinfo['c_nickname'];
        $data['c_name'] = empty($data['c_name'])?$data['c_nickname']:$data['c_name'];
        $data['c_ucode'] = $where['c_ucode'];
		// if (!$data) {
		// 	return MessageInfo(0,'该商家还未填写完整商铺信息',$data);
		// }
        //高德坐标为空，则转换存储
        if((empty($data['c_gd_longitude']) || empty($data['c_gd_latitude'])) && $suserinfo['c_isfixed1'] == 1 && !empty($data)){
            $locations = $data['c_longitude'].','.$data['c_latitude'];

            $result = IGD('Coordinate','Lbs')->Convert($locations);

            $location_info = explode(',',$result['data']['locations']);

            $data['c_gd_longitude'] = $location_info[0];
            $data['c_gd_latitude'] = $location_info[1];

            $sdata['c_gd_longitude'] = $location_info[0];
            $sdata['c_gd_latitude'] = $location_info[1];

            $result = M('Store')->where($where)->save($sdata);
        }

		//店铺图片
		$get_imglist = $this->get_imglist($data['c_id'],4);
		$data['imglist'] = $get_imglist['data'];

		//服务方式
		$join = 'left join t_service_project as b on a.c_serviceid=b.c_id';
		$servicewhere['a.c_storeid'] = $data['c_id'];
        $servicewhere[] = array("b.c_name is not null and b.c_name <> ''");
        $servicewhere[] = array("b.c_imgpath is not null and b.c_imgpath <> ''");
        $data['service'] = M('Store_service as a')->join($join)->where($servicewhere)
        	->field('a.*,b.c_name,b.c_imgpath')->group('b.c_id')->select();

        foreach ($data['service'] as $key => $value) {
            $data['service'][$key]['c_imgpath'] = empty($value['c_imgpath'])?$value['c_imgpath']:GetHost().'/'.$value['c_imgpath'];
        }

        // 分享数据 
        $shareimg = $data['imglist'][0]['c_thumbnail_img'];      
        $data['sharetit'] = '【'.$data['c_nickname'].'】的店铺';
        $data['sharedesc'] = '这家店铺还不错，说不定有你喜欢的东西哦！';
        $data['shareimg'] = empty($shareimg)?GetHost() . '/Uploads/logo.png':$shareimg;
        $data['shareurl'] = GetHost(1) . '/index.php/Store/Index/index?fromucode=' . $data['c_ucode'];
        $carparr['ucode'] = $parr['ucode'];
        $carparr['acode'] = $data['c_ucode'];
        $carinfos = IGD('Storecar','User')->GetCount($carparr)['data'];
        $data['carcount'] =  $carinfos['count'];       
        $data['price'] =  $carinfos['price'];       
        return MessageInfo(0,'查询成功',$data);
	}

	/**
     * 查询所有服务方式
     *  @param storeid
     */
	function GetServiceList($parr)
	{
        //查询用户所选服务项目
        $servicewhere['c_storeid'] = $parr['storeid'];
        $servicedata = M('Store_service')->where($servicewhere)->select();
		$where['c_state'] = 1;
		$data = M('Service_project')->where($where)->order('c_sort desc')->select();
        foreach ($data as $key => $value) {
            $data[$key]['checked'] = 0;
            foreach ($servicedata as $k1 => $v1) {
                if ($v1['c_serviceid'] == $value['c_id']) {
                    $data[$key]['checked'] = 1;
                }
            }
            $data[$key]['c_imgpath'] = empty($value['c_imgpath'])?$value['c_imgpath']:GetHost().'/'.$value['c_imgpath'];
        }
		return MessageInfo(0,'查询成功',$data);
	}

	//获取图片列表
    function get_imglist($id,$sourceid) {
        $w['c_regionid'] = $id;
        $w['c_sourceid'] = $sourceid;
        $data = M('Resource_img')->field('c_img,c_thumbnail_img')->where($w)->select();

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            $data[$k]['c_thumbnail_img'] = GetHost() . '/' . $v['c_img'];
        }

        return MessageInfo(0,'查询成功',$data);
    }

    /**
     *  获取实体店铺用户所有产品列表
     *  @param  pageindex,pagesize,ucode,gettype,acode
     *
     */
    function GetProduceList($parr)
    {
        $pageSize = $parr['pagesize'];
        $type = $parr['gettype'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $countPage = ($pageIndex - 1) * $pageSize;

        if ($type == 1) {
        	$ucode = $parr['acode'];
            $where['c_ishow'] = 1;
        } else {
        	$ucode = $parr['ucode'];
        	if (empty($ucode)) {
        		return Message(1009,'请先登录，再操作');
        	}
        }
        
        $where['c_isagent'] = 0;
        $where['c_isdele'] = 1;
        $where['c_ucode'] = $ucode;
        $where['c_source'] = 2;

        $list = M('Product')->where($where)->limit($countPage, $pageSize)->order('c_id desc')->select();

        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {
            if (!empty($value['c_pimg'])) {
                $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            }

            $carparr['ucode'] = $parr['ucode'];
            $carparr['pcode'] = $value['c_pcode'];
            $list[$key]['carnum'] =  IGD('Storecar','User')->Getprocount($carparr)['data']['count'];
            $list[$key]['sharetit'] = $value['c_name'];
            $list[$key]['sharedesc'] = $value['c_desc'];
            $list[$key]['shareimg'] = $list[$key]['c_pimg'];
            $list[$key]['shareurl'] =  GetHost(1) . '/index.php/Shopping/Entitymap/detail?pcode=' . $value['c_pcode']."&pucode=".$parr['ucode'];

        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  获取实体店铺所有评论信息列表
     *  @param  pageindex,pagesize,acode
     *
     */
    function GetCommentList($parr)
    {
        $whereinfo['a.c_acode'] = $parr['acode'];
        $whereinfo['a.c_source'] = 2;

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $order = 'a.c_addtime desc';
        $list = M('product_score as a')->join($join)->where($whereinfo)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {

            //修改评论时间
            $list[$key]['c_addtime'] = date('Y-m-d', strtotime($value['c_addtime']));

            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            //查询评论图片
            $where1['c_regionid'] = $value['c_id'];
            $where1['c_sourceid'] = 3;
            $field = 'c_img,c_thumbnail_img';
            $imglist = M('Resource_img')->where($where1)->field($field)->select();

            foreach ($imglist as $key1 => $value1) {
                $imglist[$key1]['c_img'] = GetHost() . '/' . $value1['c_img'];
                $imglist[$key1]['c_thumbnail_img'] = GetHost() . '/' . $value1['c_img'];
            }
            $list[$key]["imglist"] = $imglist;
        }
        
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 网页端添加店铺资料
     * @param ucode,name,desc,address,longitude,latitude,(storeid)
     *        imglist,remind,opentime,sourcearr
     */
    function AddStoreInfo($parr)
    {
    	$db = M('');
        $db->startTrans();

    	if (empty($parr['ucode'])) {
			return Message(1009,'请登录后，再操作');
		}

		if (!empty($parr['storeid'])) {
			$storewhere['c_ucode'] = $parr['ucode'];
			$storewhere['c_id'] = $parr['storeid'];
			$storeinfo = M('Store')->where($storewhere)->find();
		}

        if ($storeinfo['c_name'] != $parr['name']) {
            $whereacodeinfo['c_nickname'] = array('EQ', $parr['name']);
            $whereacodeinfo['c_ucode'] = array('NEQ', $parr['ucode']);
            $count = M('Users')->where($whereacodeinfo)->count();
            if ($count > 0) {
                return Message(1021, "该名称已被占用");
            }
            $wherestoreinfo['c_name'] = array('EQ', $parr['name']);
            $count1 = M('Store')->where($wherestoreinfo)->count();
            if ($count1 > 0) {
                return Message(1021, "该名称已被占用");
            }
        }

        //转换高德地图坐标，并储存
        $locations = $parr['longitude'].','.$parr['latitude'];

        $result = IGD('Coordinate','Lbs')->Convert($locations);

        $location_info = explode(',',$result['data']['locations']);

        $storedata['c_gd_longitude'] = $location_info[0];
        $storedata['c_gd_latitude'] = $location_info[1];

		$storedata['c_ucode'] = $parr['ucode'];
		$storedata['c_name'] = $parr['name'];
		$storedata['c_desc'] = $parr['desc'];
        $storedata['c_provice'] = $parr['provice'];
        $storedata['c_city'] = $parr['city'];
        $storedata['c_district'] = $parr['district'];
		$storedata['c_address'] = $parr['address'];
		$storedata['c_longitude'] = $parr['longitude'];
		$storedata['c_latitude'] = $parr['latitude'];
		$storedata['c_updatetime'] = date('Y-m-d H:i:s');
		$storedata['c_remind'] = $parr['remind'];
		$storedata['c_opentime'] = $parr['opentime'];
		if (!$storeinfo) {
			$storedata['c_addtime'] = date('Y-m-d H:i:s');
			$result = M('Store')->add($storedata);
			$storeid = $result;
		} else {
			$result = M('Store')->where($storewhere)->save($storedata);
			$storeid = $parr['storeid'];
		}

		if (!$result) {
			$db->rollback(); //不成功，则回滚
			return Message(2000,'添加失败');
		}

        //店铺名同步用户昵称
        $userwhere['c_ucode'] = $parr['ucode'];
        $usersave['c_nickname'] = $parr['name'];
        $result = M('Users')->where($userwhere)->save($usersave);
        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(2002,'昵称修改失败');
        // }

        //同步用户地理位置
        $localwhere['c_ucode'] = $parr['ucode'];
        $usercal = M('User_local')->where($localwhere)->find();
        $add_data['c_ucode'] = $parr['ucode'];
        $add_data['c_isfixed'] = 1;
        $add_data['c_longitude'] = $parr['longitude'];
        $add_data['c_latitude'] = $parr['latitude'];
        $add_data['c_address'] = $parr['provice'].$parr['city'].$parr['district'].$parr['address'];
        $add_data['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$usercal) {
            $add_data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($add_data);
        } else {
            $result = M('User_local')->where($localwhere)->save($add_data);
        }

        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(2001, "保存地址失败");
        // }

        //同步修改用户地理位置
        $add_userdata['c_longitude'] = $parr['longitude'];
        $add_userdata['c_latitude'] = $parr['latitude'];
        $add_userdata['c_address'] = $parr['provice'].$parr['city'].$parr['district'].$parr['address'];
        $result = M('Users')->where($localwhere)->save($add_userdata);

		//删除图片
        $imgw['c_regionid'] = $storeid;
        $imgw['c_sourceid'] = 4;
        $imgadata = M('Resource_img')->field('c_img')->where($imgw)->select();
        if (!empty($imgadata)) {
            foreach ($imgadata as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

		//添加图片
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $imgdata['c_sourceid'] = 4;
                $imgdata['c_regionid'] = $storeid;
                $imgdata['c_img'] = $value;
                $imgdata['c_thumbnail_img'] = $value;
                $result = M('Resource_img')->add($imgdata);
                if ($result <= 0) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1025, '店铺图片添加失败');
                }
            }
        }

    	//删除服务项目
        $servicewhere['c_ucode'] = $parr['ucode'];
		$servicewhere['c_storeid'] = $storeid;
        $servicedata = M('Store_service')->where($servicewhere)->select();
        if (!empty($servicedata)) {
            $result = M('Store_service')->where($servicewhere)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(2003, '删除服务项目失败');
            }
        }

		//保存服务项目
		$sourcearr = $parr['sourcearr'];
		if (is_array($sourcearr)) {
			foreach ($sourcearr as $key => $value) {
                if (!empty($value)) {
                    $servicedata['c_serviceid'] = $value;
                    $servicedata['c_ucode'] = $parr['ucode'];
                    $servicedata['c_storeid'] = $storeid;
                    $servicedata['c_addtime'] = date('Y-m-d H:i:s');
                    $result = M('Store_service')->add($servicedata);
                    if (!$result) {
                        $db->rollback(); //不成功，则回滚
                        return Message(2004,'操作失败');
                    }
                }
			}
		}

		$db->commit();
   	 	return Message(0, '操作成功');
    }

    /**
     * 线上商家编辑店铺资料
     * @param ucode,name,desc
     */
    function AddStoreInfoline($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'请登录后，再操作');
        }

        if (!empty($parr['storeid'])) {
            $storewhere['c_id'] = $parr['storeid'];
        }
        $storewhere['c_ucode'] = $parr['ucode'];
        $storeinfo = M('Store')->where($storewhere)->find();

        if ($storeinfo['c_name'] != $parr['name']) {
            $whereacodeinfo['c_nickname'] = array('EQ', $parr['name']);
            $whereacodeinfo['c_ucode'] = array('NEQ', $parr['ucode']);
            $count = M('Users')->where($whereacodeinfo)->count();
            if ($count > 0) {
                return Message(1021, "该名称已被占用");
            }
            $wherestoreinfo['c_name'] = array('EQ', $parr['name']);
            $count1 = M('Store')->where($wherestoreinfo)->count();
            if ($count1 > 0) {
                return Message(1021, "该名称已被占用");
            }
        }

        $db = M('');
        $db->startTrans();
        $storedata['c_ucode'] = $parr['ucode'];
        $storedata['c_name'] = $parr['name'];
        $storedata['c_desc'] = $parr['desc'];
        $storedata['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$storeinfo) {
            $storedata['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('Store')->add($storedata);
            $storeid = $result;
        } else {
            $result = M('Store')->where($storewhere)->save($storedata);
            $storeid = $parr['storeid'];
        }

        if (!$result) {
            $db->rollback(); //不成功，则回滚
            return Message(2000,'添加失败');
        }

        //店铺名同步用户昵称
        $userwhere['c_ucode'] = $parr['ucode'];
        $usersave['c_nickname'] = $parr['name'];
        $result = M('Users')->where($userwhere)->save($usersave);
        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(2002,'昵称修改失败');
        // }

        //删除图片
        $imgw['c_regionid'] = $storeid;
        $imgw['c_sourceid'] = 4;
        $imgadata = M('Resource_img')->field('c_img')->where($imgw)->select();
        if (!empty($imgadata)) {
            foreach ($imgadata as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

        //添加图片
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $imgdata['c_sourceid'] = 4;
                $imgdata['c_regionid'] = $storeid;
                $imgdata['c_img'] = $value;
                $imgdata['c_thumbnail_img'] = $value;
                $result = M('Resource_img')->add($imgdata);
                if ($result <= 0) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1025, '店铺图片添加失败');
                }
            }
        }

        $db->commit();
        $data['storeid'] = $storeid;
        return MessageInfo(0,'操作成功',$data);
    }

	/**
	 * 店铺资料上传第一步(基本资料)
	 * @param ucode,name,desc,address,longitude,latitude,(storeid)
	 */
	function AddStoreInfo1($parr)
	{
		if (empty($parr['ucode'])) {
			return Message(1009,'请登录后，再操作');
		}

		if (!empty($parr['storeid'])) {
            $storewhere['c_id'] = $parr['storeid'];
        }
		$storewhere['c_ucode'] = $parr['ucode'];
		$storeinfo = M('Store')->where($storewhere)->find();

        if ($storeinfo['c_name'] != $parr['name']) {
            $whereacodeinfo['c_nickname'] = array('EQ', $parr['name']);
            $whereacodeinfo['c_ucode'] = array('NEQ', $parr['ucode']);
            $count = M('Users')->where($whereacodeinfo)->count();
            if ($count > 0) {
                return Message(1021, "该名称已被占用");
            }
            $wherestoreinfo['c_name'] = array('EQ', $parr['name']);
            $count1 = M('Store')->where($wherestoreinfo)->count();
            if ($count1 > 0) {
                return Message(1021, "该名称已被占用");
            }
        }

        $db = M('');
        $db->startTrans();

        //转换高德地图坐标，并储存
        $locations = $parr['longitude'].','.$parr['latitude'];

        $result = IGD('Coordinate','Lbs')->Convert($locations);

        $location_info = explode(',',$result['data']['locations']);

        $storedata['c_gd_longitude'] = $location_info[0];
        $storedata['c_gd_latitude'] = $location_info[1];

        // $parr['city'] = ($parr['city'] == '')?$parr['provice']:$parr['city'];
		$storedata['c_ucode'] = $parr['ucode'];
		$storedata['c_name'] = $parr['name'];
		$storedata['c_desc'] = $parr['desc'];
        $storedata['c_provice'] = $parr['provice'];
        $storedata['c_city'] = $parr['city'];
        $storedata['c_district'] = $parr['district'];
		$storedata['c_address'] = $parr['address'];
		$storedata['c_longitude'] = $parr['longitude'];
		$storedata['c_latitude'] = $parr['latitude'];
		$storedata['c_updatetime'] = date('Y-m-d H:i:s');
		if (!$storeinfo) {
			$storedata['c_addtime'] = date('Y-m-d H:i:s');
			$result = M('Store')->add($storedata);
			$storeid = $result;
		} else {
			$result = M('Store')->where($storewhere)->save($storedata);
			$storeid = $parr['storeid'];
		}

		if (!$result) {
            $db->rollback(); //不成功，则回滚
			return Message(2000,'添加失败');
		}

        //店铺名同步用户昵称
        $userwhere['c_ucode'] = $parr['ucode'];
        $usersave['c_nickname'] = $parr['name'];
        $result = M('Users')->where($userwhere)->save($usersave);
        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(2002,'昵称修改失败');
        // }

        //同步用户地理位置
        $localwhere['c_ucode'] = $parr['ucode'];
        $usercal = M('User_local')->where($localwhere)->find();
        $add_data['c_ucode'] = $parr['ucode'];
        $add_data['c_isfixed'] = 1;
        $add_data['c_longitude'] = $parr['longitude'];
        $add_data['c_latitude'] = $parr['latitude'];
        $add_data['c_address'] = $parr['provice'].$parr['city'].$parr['district'].$parr['address'];
        $add_data['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$usercal) {
            $add_data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($add_data);
        } else {
            $result = M('User_local')->where($localwhere)->save($add_data);
        }

        // if (!$result) {
        //     $db->rollback(); //不成功，则回滚
        //     return Message(2001, "保存地址失败");
        // }

        //同步修改用户地理位置
        $add_userdata['c_longitude1'] = $parr['longitude'];
        $add_userdata['c_latitude1'] = $parr['latitude'];
        $add_userdata['c_address1'] = $parr['provice'].$parr['city'].$parr['district'].$parr['address'];
        $result = M('Users')->where($localwhere)->save($add_userdata);

        $db->commit();
		$data['storeid'] = $storeid;
		return MessageInfo(0,'操作成功',$data);
	}

	/**
	 * 店铺资料上传第二步(上传店铺图片)
	 * @param ucode,storeid,imglist
	 */
	function AddStoreInfo2($parr)
	{
		$storewhere['c_ucode'] = $parr['ucode'];
		$storewhere['c_id'] = $parr['storeid'];
		$storeinfo = M('Store')->where($storewhere)->find();
		if (!$storeinfo) {
			return Message(2001,'店铺信息不存在');
		}

		$db = M('');
        $db->startTrans();
        //删除图片
        $imgw['c_regionid'] = $parr['storeid'];
        $imgw['c_sourceid'] = 4;
        $imgadata = M('Resource_img')->field('c_img')->where($imgw)->select();
        if (!empty($imgadata)) {
            foreach ($imgadata as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

		//添加图片
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $imgdata['c_sourceid'] = 4;
                $imgdata['c_regionid'] = $parr['storeid'];
                $imgdata['c_img'] = $value;
                $imgdata['c_thumbnail_img'] = $value;
                $result = M('Resource_img')->add($imgdata);
                if ($result <= 0) {
                    $db->rollback(); //不成功，则回滚
                    return Message(1025, '店铺图片添加失败');
                }
            }
        }

        $db->commit();
   	 	return Message(0, '操作成功');
	}

	/**
	 * 店铺资料上传第二步(提交服务项目等)
	 * @param ucode,storeid,remind,opentime,sourcearr
	 */
	function AddStoreInfo3($parr)
	{
		$storewhere['c_ucode'] = $parr['ucode'];
		$storewhere['c_id'] = $parr['storeid'];
		$storeinfo = M('Store')->where($storewhere)->find();
		if (!$storeinfo) {
			return Message(2001,'店铺信息不存在');
		}

		$db = M('');
        $db->startTrans();

		$storedata['c_remind'] = $parr['remind'];
		$storedata['c_opentime'] = $parr['opentime'];
		$storedata['c_updatetime'] = date('Y-m-d H:i:s');
		$result = M('Store')->where($storewhere)->save($storedata);
		if (!$result) {
			$db->rollback(); //不成功，则回滚
			return Message(2002,'操作失败');
		}

		//删除服务项目
        $servicewhere['c_ucode'] = $parr['ucode'];
		$servicewhere['c_storeid'] = $parr['storeid'];
        $servicedata = M('Store_service')->where($servicewhere)->select();
        if (!empty($servicedata)) {
            $result = M('Store_service')->where($servicewhere)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(2003, '删除服务项目失败');
            }
        }

		//保存服务项目
		$sourcearr = $parr['sourcearr'];
		if (is_array($sourcearr)) {
			foreach ($sourcearr as $key => $value) {
                if (!empty($value)) {
                    $projectwhere['c_id'] = $value;
                    $projectnum = M('Service_project')->where($projectwhere)->count();
                    if ($projectnum > 0) {
                        $servicedata['c_serviceid'] = $value;
                        $servicedata['c_ucode'] = $parr['ucode'];
                        $servicedata['c_storeid'] = $parr['storeid'];
                        $servicedata['c_addtime'] = date('Y-m-d H:i:s');
                        $result = M('Store_service')->add($servicedata);
                        if (!$result) {
                            $db->rollback(); //不成功，则回滚
                            return Message(2004,'操作失败');
                        }
                    }
                }
			}
		}

		$db->commit();
   	 	return Message(0, '操作成功');
	}

    /**
     * 商家靓号列表查询接口
     * @param pageindex,pagesize,shopnum,places
     */
    function ShopNumList($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where[] = array("c_ucode is null or c_ucode=''");
        if (!empty($parr['shopnum'])) {
            $where['c_shopnum'] = $parr['shopnum'];
        }
        if (!empty($parr['places'])) {
           $where[] = array("LENGTH(c_shopnum)=".$parr['places']);
        }
        $list = M('Shop_num')->where($where)->order('c_id desc')->limit($countPage, $pageSize)->field('*')->select();
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        $count = M('Shop_num')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 查询店铺模板列表
     * @param ucode
     */
    public function TemplateList($parr){
        $ucode = $parr['ucode'];

        $tw[] = array("c_ucode='$ucode' or c_ucode is null or c_ucode=''");
        $list1 = M('Shop_template')->where($tw)->order('c_sign asc,c_id desc')->select();

        $num = 0;
        foreach ($list1 as $key => $value) {
            if ($value['c_sign'] != $list1[$key-1]['c_sign']) {
                $list[$num]['c_id'] = $value['c_id'];
                $list[$num]['c_ucode'] = $value['c_ucode'];
                $list[$num]['c_name'] = $value['c_name'];
                $list[$num]['c_cover_img'] = GetHost().'/'.$value['c_cover_img'];
                $list[$num]['c_type'] = $value['c_type'];
                $list[$num]['c_tplid'] = $value['c_tplid'];
                $list[$num]['c_sign'] = $value['c_sign'];
                $list[$num]['c_edite'] = $value['c_edite'];
                $list[$num]['c_addtime'] = $value['c_addtime'];
                $num++;
            }
        }

        //查询商家选择的店铺模板
        $param['ucode'] = $parr['ucode'];
        $result = $this->GetShopTpl($param);

        if($result['code'] == 0){
            $data['tplid'] = $result['data']['tplid'];
            $data['compareid'] = $result['data']['compareid'];
        }else{
            $data['tplid'] = 1;
            $data['compareid'] = 1;
        }

        $data['list'] = $list;

        $where['c_ucode'] = $ucode;

        $fiexd = M('User_local')->where($where)->field('c_isfixed')->find();

        // if (!isset($data['c_isfixed'])) {
        //     return Message(3001,'不是商家');
        // }
        $data['c_isfixed'] = $fiexd['c_isfixed'];

        return MessageInfo(0,"查询成功",$data);
    }

    /**
     * 查询商家选择的店铺模板
     * @param ucode,acode
     * 
     */
    function GetShopTpl($parr)
    {   
        //查询选择模板
        if (!empty($parr['acode'])) {
            $where['c_ucode'] = $parr['acode'];
        } else {
            $where['c_ucode'] = $parr['ucode'];
        }
        $tplid = M('A_storetpl')->where($where)->getField('c_tplid');
        if (!$tplid) {
            $tplid = '1';
        }

        //查询模板信息
        $tpw['c_id'] = $tplid;
        $tempinfo = M('Shop_template')->where($tpw)->find();

        //查询模板内容是否应用
        // $w['c_tempid'] = $tplid;
        // $w['c_isdel'] = 1;
        // $w['c_sign'] = 2;
        // $info = M('Shop_template_content')->where($w)->find();
        // if (!empty($info)) {
        //     $data['compareid'] = $tempinfo['c_sign']; 
        // }

        $data['compareid'] = $tempinfo['c_sign'];
        if (empty($data['compareid'])) {
            $data['compareid'] = 1;
        } 
        $data['tplid'] = $tplid;
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     * 选择店铺模板
     * @param ucode,tplid
     * 
     */

    function CheckShopTpl($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009,'请登录再操作');
        }

        if (!$parr['tplid']) {
            return Message(3001,'请选择对应模板');
        }

        $db = M('');
        $db->startTrans();

        //查询模板信息
        $w['c_id'] = $parr['tplid'];
        $result = M('Shop_template')->where($w)->find();
        $edite = $result['c_edite'];
        $where['c_ucode'] = $parr['ucode'];
        if ($edite == 1 || empty($parr["app_version"])) {//不可编辑
            //不可编辑直接选择模板
            $add['c_ucode'] = $parr['ucode'];
            $add['c_tplid'] = $parr['tplid'];
            $add['c_updatetime'] = gdtime();
            $data = M('A_storetpl')->where($where)->find();
            if ($data) {
                $res = M('A_storetpl')->where($where)->save($add);
                $tempid = $add['c_tplid'];
            } else {
                $res = M('A_storetpl')->add($add);
                $tempid = $add['c_tplid'];
            }

            if (!$res) {
                $db->rollback();
                return Message(3000,'选择失败');
            }            
        }else if($edite == 2){//可编辑

            //可编辑  新增模板记录
            $t['c_ucode']       = $parr['ucode'];
            $t['c_name']        = $result['c_name'];
            $t['c_cover_img']   = $result['c_cover_img'];
            $t['c_type'] = $result['c_type'];
            $t['c_addtime'] = gdtime();
            $t['c_tplid'] = $result['c_id'];
            $t['c_sign'] = $result['c_sign'];
            $t['c_edite'] = $edite;

            //查询是否已选择过模板
            $w1['c_ucode'] = $parr['ucode'];
            $w1['c_sign'] = $result['c_sign'];
            $info = M('Shop_template')->where($w1)->find();            
            if (empty($info)) {
                $addinfo = M('Shop_template')->add($t);
                $tempid = $addinfo;
                if (!$addinfo) {
                    $db->rollback();
                    return Message(3000,'选择失败');
                }
            }else{
                $tempid = $info['c_id'];
            }

            //选择模板
            $temp['c_tplid'] = $tempid;
            $temp['c_ucode'] = $parr['ucode'];
            $temp['c_updatetime'] = gdtime();
            $datainfo = M('A_storetpl')->where($where)->find();
            if ($datainfo) {
                $saveinfo = M('A_storetpl')->where($where)->save($temp);
            } else {
                $saveinfo = M('A_storetpl')->add($temp);
            }
            if (!$saveinfo) {
                $db->rollback();
                return Message(3000,'选择失败');
            }
        }


        $db->commit();
        return MessageInfo(0,'选择成功',$tempid); 
    }

    /**
     * 根据店铺模板Id获取模板内容
     * @param tplid
     * 
     */
    function GetShopTplContent($parr){
        //接收参数
        $tplid = $parr['tplid'];
        $isprew = $parr['isprew'];

        //查询已应用模板内容 
        $w['c_tempid'] = $tplid;
        if (empty($parr["app_version"])) {
            $w['c_types'] = array('exp','is null');
        } else {
           $w['c_types'] = array('exp','is not null');
        }

        if ($isprew != 1) {
            $w['c_sign'] = 2;
        }
        $w['c_isdel'] = array('neq',2);
        $data = M('Shop_template_content')->where($w)->order('c_sort asc')->select();
        if(!$data){
            return MessageInfo(0,"查询成功",$data);
        }

        foreach ($data as $key => $value) {
            $data[$key]['c_img'] = GetHost().'/'.$value['c_img'];
        }

        return MessageInfo(0,"查询成功",$data);
    }

    /**
     * 编辑店铺头部
     * @param ucode types
     * 
     */
    function AddHeadImg($parr){
        $ucode = $parr['ucode'];
        if (empty($ucode)) {
             return MessageInfo(1000,'请先登录，再操作');
        }

        //接收参数判断    
        $types = $parr['types'];
        $tplid = $parr['tplid'];
        if (empty($tplid) || $types != 1) {
             return MessageInfo(1000,'参数错误');
        }


        $where['c_id'] = $tplid;
        $where['c_ucode'] = $ucode;
        $tempinfo = M('Shop_template')->where($where)->find();
        if (!$tempinfo) {
            return MessageInfo(1000,'请选择模板');
        }
            
        $db = M('');
        $db->startTrans();    

        //循环写入内容信息
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $data['c_tempid'] = $tplid;
                $data['c_function'] = '头部图片';
                $data['c_img'] = $value;
                $data['c_addtime'] = gdtime();
                $data['c_types'] = $types;
                $data['c_isdel']  = 1;
                $data['c_tplid'] = $tempinfo['c_tplid'];

                $w['c_tempid'] = $tplid;
                $w['c_types'] = $types;
                $w['c_tplid'] = $tempinfo['c_tplid'];
                $w['c_isdel'] = '1';
                $info = M('Shop_template_content')->where($w)->find();
                if ($info) {
                    $res = M('Shop_template_content')->where($w)->save($data);
                    $c_id = $info['c_id'];
                }else{
                    $res = M('Shop_template_content')->add($data);
                    $c_id = $res;
                }
                
                if (!$res) {
                    $db->rollback();
                    return Message(1025, '图片添加失败');
                }

                //查询返回结果
                $w1['c_id'] = $c_id;
                $imginfo = M('Shop_template_content')->where($w1)->find();
                $imginfo['headimg'] = GetHost().'/'.$imginfo['c_img'];
            } else {
                $db->rollback();
                return Message(1025, '请上传图片');
            }
        }
          
        $db->commit(); 
        return MessageInfo(0,'添加成功',$imginfo);
    }
    /**
     * 添加banner图片
     * @param ucode types sign
     * 
     */

    function AddBannerImg($parr){
        $ucode = $parr['ucode'];
        if (empty($ucode)) {
            return MessageInfo(1000,'请先登录，再操作');
        }

        //接收参数
        $types = $parr['types'];
        $sign = $parr['sign'];
        $tplid = $parr['tplid'];
        if (empty($tplid) || empty($types) || empty($sign)) {
            return MessageInfo(1000,'参数错误');
        }
        
        //查询模板
        $where['c_id'] = $tplid;
        $where['c_ucode'] = $ucode;
        $tempinfo = M('Shop_template')->where($where)->find();
        if (!$tempinfo) {
            return MessageInfo(1000,'模板不存在');
        }

        $db = M('');
        $db->startTrans();

        //循环存储内容
        $imglist = $parr['imglist'];
        foreach ($imglist as $key => $value) {
            if (!empty($value)) {
                $data['c_tempid'] = $tplid;
                $data['c_function'] = 'banner图片';
                $data['c_img'] = $value;
                $data['c_addtime'] = gdtime();
                $data['c_types'] = $types;
                $data['c_sort']  = $sign;
                $data['c_isdel']  = 1;
                $data['c_tplid'] = $tempinfo['c_tplid'];

                $w['c_tempid'] = $tplid;
                $w['c_types'] = $types;
                $w['c_sort'] = $sign;
                $w['c_tplid'] = $tempinfo['c_tplid'];
                $w['c_isdel'] = '1';
                $info = M('Shop_template_content')->where($w)->find();
                if ($info) {
                    $res = M('Shop_template_content')->where($w)->save($data);
                    $c_id = $info['c_id'];
                }else{
                    $res = M('Shop_template_content')->add($data);
                    $c_id = $res;
                }
                
                if (!$res) {
                    $db->rollback();
                    return Message(1025, '图片添加失败');
                }

                //返回结果
                $w1['c_id'] = $c_id;
                $imginfo = M('Shop_template_content')->where($w1)->find();
                $imginfo['banner'] = GetHost().'/'.$imginfo['c_img'];
            } else {
                $db->rollback();
                return Message(1025, '请上传图片');
            }
            
        }

        $db->commit();
        return MessageInfo(0,'添加成功',$imginfo);
    }
    /**
     * 删除图片
     * @param id
     * 
     */
    function DelImg($parr){
        $id = $parr['id'];
        if (empty($id)) {
           return Message(1000, $id);
        }
        $where['c_id'] = $id;
        $data['c_isdel'] = 2;
        $result = M('Shop_template_content')->where($where)->save($data);
        if (!$result) {
            return Message(1001, '删除失败');
        }
        return Message(0, '删除成功');
    }

    /**
     * 获取分类和分类详情
     * @param ucode
     * 
     */
    function GetCategory($parr){
        $acode = $parr['acode'];
        $ucode = $parr['ucode'];
        if (empty($acode)) {
            return Message(1000, '缺少参数');
        }

        $where['c_ucode'] = $acode;
        $where['c_isdel'] = 0;

        $result = M('Product_category')->where($where)->field('c_id,c_category_name')->select();
        if (!$result) {
            $w1['c_isagent'] = 0;
            $w1['c_isdele'] = 1;
            $w1['c_ishow'] = 1;
            $w1['c_ucode'] = $acode;
            $w1['c_product_category'] = '0';

            $result[0]['goods'] = M('Product')->where($w1)->order('c_id desc')->select();
            if (!$result) {
                return Message(1001, '查询失败');
            }
            $result[0]['c_category_name'] = '';
            $result[0]['c_id'] = '';
            $cart['ucode'] = $ucode;
            $cart['acode'] = $acode;
            $mycart = IGD('Storecar', 'User')->GetCar($cart);
            $datainfo = $mycart['data'][0]['list'];

            foreach ($result[0]['goods'] as $k => $value) {
                $result[0]['goods'][$k]['cartnum'] = 0;
                $result[0]['goods'][$k]['c_pimg'] = GetHost().'/'.$value['c_pimg'];
                foreach ($datainfo as $s => $val) {
                    if ($val['c_pcode'] == $result[0]['goods'][$k]['c_pcode']) {
                        $result[0]['goods'][$k]['cartnum'] = $val['c_num'];
                    }   
                }
            }
            return MessageInfo(0, '查询成功',$result);
        }

        //获取购物车
        $cart['ucode'] = $ucode;
        $cart['acode'] = $acode;
        $mycart = IGD('Storecar', 'User')->GetCar($cart);
        $datainfo = $mycart['data'][0]['list'];
        $n = 0;
        foreach ($result as $key => $value) {
            $w1['c_isagent'] = 0;
            $w1['c_isdele'] = 1;
            $w1['c_ishow'] = 1;
            $w1['c_ucode'] = $acode;
            $w1['c_product_category'] = $value['c_id'];

            $result[$key]['goods']  = M('Product')->where($w1)->order('c_id desc')->select();
            if (empty($result[$key]['goods'])) {
                unset($result[$key]);
                continue;
            }
            foreach ($result[$key]['goods'] as $k => $v) {
                $result[$key]['goods'][$k]['cartnum'] = 0;
                $result[$key]['goods'][$k]['c_pimg'] = GetHost().'/'.$v['c_pimg'];
                foreach ($datainfo as $s => $val) {
                    if ($val['c_pcode'] == $result[$key]['goods'][$k]['c_pcode']) {
                        $result[$key]['goods'][$k]['cartnum'] = $val['c_num'];
                    }   
                }
            }
            $n++;  
        }

        $w2['c_isagent'] = 0;
        $w2['c_isdele'] = 1;
        $w2['c_ishow'] = 1;
        $w2['c_ucode'] = $acode;
        $w2['c_product_category'] = '0';
        $result[$n]['c_id'] = '';
        $result[$n]['c_category_name'] = '';
        $result[$n]['goods']  = M('Product')->where($w2)->order('c_addtime desc')->select();

        if (!empty($result[$n]['goods'])) {
            $result[$n]['c_category_name'] = '未分类';
            foreach ($result[$n]['goods'] as $k => $v) {
                $result[$n]['goods'][$k]['cartnum'] = 0;
                $result[$n]['goods'][$k]['c_pimg'] = GetHost().'/'.$v['c_pimg'];
                foreach ($datainfo as $s => $val) {
                    if ($val['c_pcode'] == $result[$n]['goods'][$k]['c_pcode']) {
                        $result[$n]['goods'][$k]['cartnum'] = $val['c_num'];
                    }   
                }
            }
        }else{
            $result[$n]['goods'] = array();
        }
        
        return MessageInfo(0,'查询成功',array_values($result));
            
    }
    /**
     * 应用模板
     * @param ucode
     * 
     */
    function ApplyModel($parr){

        $ucode = $parr['ucode'];
        $tplid = $parr['tplid'];
        if (empty($tplid)) {
            return Message(3000,'参数缺失');
        }

        //查询用户自定义模板
        $w['c_ucode'] = $ucode;
        $w['c_id'] = $tplid;
        $info = M('Shop_template')->where($w)->find();
        if (!$info) {
            return  Message(3001,'请先选中模板');
        }

        //事务开始
        $db = M('');
        $db->startTrans();
        
        //应用之前重置所有自定义模板不应用状态
        $changewh['c_tempid'] = $tplid;
        $changewh['c_isdel'] = 1;
        if (!empty($parr['app_version'])) {
            $changewh['c_sign'] = 2;
        }
        $changesv['c_sign'] = 1;
        $result = M('Shop_template_content')->where($changewh)->getField('c_id');
        if ($result) {
            $result = M('Shop_template_content')->where($changewh)->save($changesv);
            if (!$result) {
                $db->rollback();  //事务回滚
                return  Message(3002,'重置模板失败');
            }
        }
        
        //应用已选择模板
        $where['c_tempid'] = $tplid;
        $where['c_tplid'] = $info['c_tplid'];
        $where['c_isdel'] = 1;
        $data['c_sign'] = 2;          //应用标识2应用 1未应用
        $result = M('Shop_template_content')->where($where)->getField('c_id');
        if ($result) {
            $result = M('Shop_template_content')->where($where)->save($data);
            if (!$result) {
                $db->rollback();  //事务回滚
                return  Message(3003,'应用模板失败');
            }
        }

        $db->commit();   //提交事务
        return Message(0,'应用成功');
    }

    //获取应用模板的内容
    function GetApplyCenter($parr){
        $ucode = $parr['acode'];
        if (empty($ucode)) {
            return Message(3002,'缺少参数');    
        }
        $w['c_ucode'] = $ucode;
        $info = M('Shop_template')->where($w)->find();
        if (empty($info)) {
            $info['c_id'] = 1;
            $info['c_tplid'] = 1;
        }
        $where['c_tempid'] = $info['c_id'];
        $where['c_tplid'] = $info['c_tplid'];
        $where['c_sign'] = '2';
        $where['c_isdel'] = '1';
        $result = M('Shop_template_content')->where($where)->select();
        if (empty($result)) {
            return  Message(3001,'没有模板内容');
        }
        return MessageInfo(0,'查询成功',$result);

    }

    //查询分类商品
    function GetCategoryInfo($parr){
        $ucode = $parr['ucode'];//用户ucode
        $acode = $parr['acode'];//商家ucode
        $id = $parr['id'];

        if ($id == 0) {
            $w1['c_isagent'] = 0;
            $w1['c_isdele'] = 1;
            $w1['c_ishow'] = 1;
            $w1['c_source'] = 2;
            $w1['c_ucode'] = $acode;
            $w1['c_product_category'] = '0';
            $result = M('Product')->where($w1)->order('c_addtime desc')->select();
            if (empty($result)) {
                return MessageInfo(3000,'没有数据');
            }

             //获取购物车
            $cart['ucode'] = $ucode;
            $cart['acode'] = $acode;
            $mycart = IGD('Storecar', 'User')->GetCar($cart);
            $datainfo = $mycart['data'][0]['list'];
            foreach ($result as $k => $v) {
                $result[$k]['cartnum'] = 0;
                $result[$k]['c_pimg'] = GetHost().'/'.$v['c_pimg'];
                foreach ($datainfo as $s => $val) {
                    if ($val['c_pcode'] == $result[$k]['c_pcode']) {
                        $result[$k]['cartnum'] = $val['c_num'];
                    }   
                }
            }
            return MessageInfo(0, '查询成功', $result);

        }
       
        $w1['c_isagent'] = 0;
        $w1['c_isdele'] = 1;
        $w1['c_ishow'] = 1;
        $w1['c_source'] = 2;
        $w1['c_ucode'] = $acode;
        $w1['c_product_category'] = $id;

        $result = M('Product')->where($w1)->order('c_addtime desc')->select();
        if (!$result) {
             return MessageInfo(3000,'查询失败');
        }
        if (!empty($ucode)) {
             //获取购物车
            $cart['ucode'] = $ucode;
            $cart['acode'] = $acode;
            $mycart = IGD('Storecar', 'User')->GetCar($cart);
            $datainfo = $mycart['data'][0]['list'];
            foreach ($result as $k => $v) {
                $result[$k]['cartnum'] = 0;
                $result[$k]['c_pimg'] = GetHost().'/'.$v['c_pimg'];
                foreach ($datainfo as $s => $val) {
                    if ($val['c_pcode'] == $result[$k]['c_pcode']) {
                        $result[$k]['cartnum'] = $val['c_num'];
                    }   
                }
            }
            return MessageInfo(0, '查询成功', $result);

        }
        foreach ($result as $key => $value) {
            $result[$key]['cartnum'] = 0;
            $result[$key]['c_pimg'] = GetHost().'/'.$value['c_pimg'];

        }
        return MessageInfo(0, '查询成功', $result);

    }
    
   
}