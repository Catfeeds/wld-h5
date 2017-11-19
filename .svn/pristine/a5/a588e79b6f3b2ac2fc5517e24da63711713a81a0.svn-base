<?php
/**
 * 	所有查询商品详情模块
 *
 */
class ProductinfoStore {
	/**
	 *  获取产品详情
	 *  @param ucode,pcode,ispreview(是否是预览   0-否，1-是),isagent(1代理商城),act_pcode(1活动产品编码)
	 *
	 */
	function GetProduceInfo($parr) {
		//查询商品基本信息
		$where['c_pcode'] = $parr['pcode'];
		$where['c_isdele'] = 1;

		if($parr['ispreview'] != 1){
			$where['c_ishow'] = 1;

			//添加商品访问记录
	        $parr2['ucode'] = $parr['ucode'];
	        $parr2['pcode'] = $parr['pcode'];
	        $get_app_type  = get_app_type();
	        if($get_app_type == 1) {
	            $parr2['source'] = 'Android';
	        } else if($get_app_type == 2) {
	            $parr2['source'] = 'IOS';
	        } else {
	        	$parr2['source'] = 'WEB';
	        }
	        $parr2['ip'] = GetIP();
	        $w1['c_ucode'] = $ucode;
	        $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
	        $userinfo['c_headimg'] = GetHost().'/'. $userinfo['c_headimg'];
	        $parr2['nickname'] = $userinfo['c_nickname'];
	        $parr2['headimg'] = $userinfo['c_headimg'];
	        $result = IGD('Resourcev2','Trade')->ProductVisit($parr2);
		}

		$data = M('Product')->where($where)->find();

		if (!$data) {
		    return Message(1001, '没有查询到相关商品信息！');
		}

		//图片
		$data['c_pimg'] = GetHost() . '/' . $data['c_pimg'];

		//描述空格处理
        $desc = $data['c_desc'];
        $qian=array(" ","　","\t","\n","\r");
        $hou=array("","","","","");
        $data['c_desc'] = str_replace($qian,$hou,$desc);

        //商品分享数据
        $data['share_title'] = $data['c_name'];
        $data['share_desc'] = $data['c_desc'];
        $data['share_img'] = $data['c_pimg'];

        //来源活动产品
        if (!empty($parr['act_pcode'])) {
			$acw['c_act_pcode'] = $parr['act_pcode'];
			$pinfo = M('Shopact_product')->where($acw)->find();
			if ($pinfo) {
				$mcode_str = str_replace('|', ',', $pinfo['c_mcode']);
				if ($mcode_str) {
					$modelwhere['c_mcode'] = array('in',$mcode_str);
				}
				$data['c_num'] = $pinfo['c_num'];
			}
		}
		
		//区分线上线下商家商品（有无型号）
		//线上商品有型号
		if($data['c_source'] == 1){
			//查询型号列表
			$modelwhere['c_pcode'] = $parr['pcode'];			
			$modellist = M('Product_model')->where($modelwhere)->select();
			foreach ($modellist as $k => $v) {
				$mprice = $v['c_price'];
				if ($k == 0) {
					$minmprice = $mprice;$maxmprice = $mprice;
				} else {
					if ($mprice > $maxmprice) {
						$maxmprice = $mprice;
					}

					if ($mprice < $minmprice) {
						$minmprice = $mprice;
					}
				}
				if ($pinfo) {
					$modellist[$k]['actprice'] = $pinfo['c_actprice'];
					if ($v['c_num'] >= $pinfo['c_num']) {
						$modellist[$k]['c_num'] = $pinfo['c_num'];
					} else {
						$modellist[$k]['c_num'] = $v['c_num'];
					}
					
				}
			}

			$data['modellist'] = $modellist;//商品型号列表

			//购物车总数
			$carparr1['ucode'] = $parr['ucode'];
			$carparr1['acode'] = $data['c_ucode'];
			if ($parr['isagent'] == 1) {
				$data['carcount'] =  IGD('Agencycar','User')->GetCount($carparr1)['data']['count'];
			} else {
				$data['carcount'] =  IGD('Shoppingcar','User')->GetCount($parr['ucode'])['data']['count'];
			}
			
			//代理商城商品
			if($parr['isagent'] == 1){
				//查询价格区间
				$disw['c_pcode'] = $parr['pcode'];
				$dis_arr = M('Agency_product_dis')->field('c_grade,c_discount')->where($disw)->select();

				$b = array();

				foreach ($dis_arr as $key => $value) {
					$b[] = $value['c_discount'];					
				}
				sort($b);

				$min_price = sprintf('%.2f',$data['c_price'] * $b[0]/10);
				$max_price = sprintf('%.2f',$data['c_price'] * $b[count($b)-1]/10);
				$data['c_pprice'] = "￥".$min_price." ~ ￥".$max_price;

				$min_mprice = sprintf('%.2f',$minmprice * $b[0]/10);
				$max_mprice = sprintf('%.2f',$maxmprice * $b[count($b)-1]/10);
				$data['c_mprice'] = "￥".$min_mprice." ~ ￥".$max_mprice;


				//查询用户代理级别
				$agparr['ucode'] = $data['c_ucode'];
				$agparr['agentucode'] = $parr['ucode'];
				$result = IGD('Agency','Store')->AgencyGrade($agparr);
				$levelarr = $result['data'];
				$data['levelname'] = '';$data['agentprice'] = '0.00';
				foreach ($levelarr as $k1 => $v1) {
					if ($k1 == 0) {  //获取代理最低消费价格
						$data['agentprice'] = $v1['c_jy_money'];
					}
					if ($v1['level'] == $v1['c_grade']) {
						$data['levelname'] = $v1['c_grade_name'];
					}
				}

				//分享链接地址
				$data['share_url'] =  GetHost(1) . '/' . "index.php/Agency/Index/pdetail?pcode=" . $data['c_pcode'] . "&pucode=" . $parr['ucode'];
			}else{
				if (empty($parr['ucode'])) {
				    $data['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $data['c_pcode'];
				} else {
				    $data['url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $data['c_pcode'] . "&pucode=" . $parr['ucode'];
				}

				//分享链接地址
				$data['share_url'] = GetHost(1) . '/' . "index.php/Shopping/Index/detail?pcode=" . $data['c_pcode'] . "&pucode=" . $parr['ucode'];
			}
		}else{//线下商品无型号
			//虚构一个型号
			$modellist[0]['c_id'] = 1;
			$modellist[0]['c_mcode'] = 'xn'.$data['c_pcode'];
			$modellist[0]['c_pcode'] = $data['c_pcode'];
			$modellist[0]['c_name'] = $data['c_name'];
			if ($pinfo) {
				$modellist[0]['actprice'] = $pinfo['c_actprice'];
				$modellist[0]['c_num'] = $pinfo['c_num'];
			}
			$modellist[0]['c_price'] = $data['c_price'];
			$modellist[0]['c_num'] = $data['c_num'];
			$data['modellist'] = $modellist;//商品型号列表

			$data['c_pprice'] = "￥".$data['c_price'];

			//产品在购物车数量
			$carparr['ucode'] = $parr['ucode'];
			$carparr['pcode'] = $data['c_pcode'];
			$data['carnum'] =  IGD('Storecar','User')->Getprocount($carparr)['data']['count'];

			//购物车总数
			$carparr1['ucode'] = $parr['ucode'];
			$carparr1['acode'] = $data['c_ucode'];
			$data['carcount'] =  IGD('Storecar','User')->GetCount($carparr1)['data']['count'];

			//分享链接地址
			$data['share_url'] = GetHost(1) . '/index.php/Shopping/Entitymap/detail?pcode=' . $data['c_pcode'].'&pucode='.$parr['ucode'];
		}

		//修改分享地址
		if (!empty($parr['act_pcode'])) {
			//查询活动类型
			$acttype = M('Activity')->where(array('c_id'=>$pinfo['c_aid']))->getField('c_activitytype');
			if ($acttype == 26) { //拼团
				$data['share_url'] = GetHost(1) . '/index.php/Shopping/Collage/pdetail?act_pcode='.$parr['act_pcode'];
			} else if ($acttype == 27) {  //砍价
				$data['share_url'] = GetHost(1) . '/index.php/Shopping/Bargain/pdetail?act_pcode='.$parr['act_pcode'];
			} else if ($acttype == 28) {  //秒杀
				$data['share_url'] = GetHost(1) . '/index.php/Shopping/Seckill/pdetail?act_pcode='.$parr['act_pcode'];
			}
		}

		//图片列表
		$imgwhere['c_pcode'] = $parr['pcode'];
		$imgs = M('Product_img')->where($imgwhere)->field('c_pimgepath,c_sign')->select();

		$count = 0;
		$count1 = 0;
		foreach ($imgs as $key => $value) {
		    if ($value['c_sign'] == 1) {
		        $bannerlist[$count]['img'] = GetHost() . '/' . $value['c_pimgepath'];
		        $count++;
		    } else {
		        $delist[$count1]['c_pimgepath'] = GetHost() . '/' . $value['c_pimgepath'];
		        $count1++;
		    }
		}

		$count = 0;
		if (count($bannerlist) == 0) {
		    foreach ($imgs as $key => $value) {
		        if ($count >= 3) {
		            break;
		        }
		        $bannerlist[$count]['img'] = GetHost() . '/' . $value['c_pimgepath'];
		        $count++;
		    }
		}

		$data['mainimgs'] = $bannerlist;//banner图
		$data['imglist'] = $delist;//商品详情图

		//统计评论条数
        $commentinfo['c_pcode'] = $parr['pcode'];
        $data['comment_num'] = M('product_score')->where($commentinfo)->count();   

        //商家昵称
        $uw['c_ucode'] = $data['c_ucode'];   
        $data['c_nickname'] = M('Users')->where($uw)->getField('c_nickname');

        return MessageInfo(0, '查询成功', $data);
    }

    //获取部份商品评论信息
    public function GetScore($parr) {
        $pcode = $parr['pcode'];

        $whereinfo['a.c_pcode'] = $pcode;

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $order = 'a.c_addtime desc';
        $list = M('product_score as a')->join($join)->where($whereinfo)->field($field)->order($order)->find();

        if (!$list) {
        	$list = array();
            return MessageInfo(0, "查询成功", $list);
        }

        //修改评论时间
        $list['c_addtime'] = date('Y-m-d', strtotime($list['c_addtime']));

        $list['c_pimg'] = GetHost() . '/' . $list['c_pimg'];

        $list['c_headimg'] = GetHost() . '/' . $list['c_headimg'];
        //查询评论图片
        $where1['c_regionid'] = $list['c_id'];
        $where1['c_sourceid'] = 3;
        $field = 'c_img,c_thumbnail_img';
        $imglist = M('Resource_img')->where($where1)->field($field)->select();
        foreach ($imglist as $key1 => $value1) {
            $imglist[$key1]['c_img'] = GetHost() . '/' . $value1['c_img'];
            $imglist[$key1]['c_thumbnail_img'] = GetHost() . '/' . $value1['c_img'];
        }
        $list["imglist"] = $imglist;

        return MessageInfo(0, '查询成功', $list);
    }

    /**
	 *  获取全部商品评论信息
	 *  @param ucode,acode,pcode
	 *
	 */
    public function GetProductAllScore($parr) {
        $pcode = $parr['pcode'];

        $whereinfo['a.c_pcode'] = $pcode;
        $whereinfo['a.c_object'] = 1;

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
            $list[$key]['c_addtime1'] = date('Y-m-d', strtotime($value['c_addtime']));

            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];

            $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];

            $list[$key]['c_like'] = IGD('Productscore','Store')->Toobig($value['c_like']);

            $list[$key]['c_nolike'] = IGD('Productscore','Store')->Toobig($value['c_nolike']);
            
            $list[$key]['imglist'] = IGD('Productscore','Store')->get_imglist($value['c_id']);//查询评论图片

            $list[$key]['comment_num'] = IGD('Productscore','Store')->Toobig(IGD('Productscore','Store')->get_comment_num($value['c_id']));//评论数量

            $list[$key]['comment_list'] = IGD('Productscore','Store')->get_commentlist($value['c_id'], 0);//评论列表

            $list[$key]['is_delete'] = IGD('Productscore','Store')->is_delete($parr['ucode'], $value['c_ucode']);//是否显示删除

            $list[$key]['is_like'] = IGD('Productscore','Store')->is_like($value['c_id'], $parr['ucode'], 0);//是否点赞

            // $list[$key]['is_nolike'] = IGD('Productscore','Store')->is_nolike($row['c_id'], $parr['ucode'], 1);//是否点不赞
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 根据产品编码
     * @param pcode
     */
    function GetModeList($pcode)
    {
    	$modelwhere['c_pcode'] = $pcode;
		$modellist = M('Product_model')->where($modelwhere)->select();
		return MessageInfo(0, '查询成功', $modellist);
    }
}