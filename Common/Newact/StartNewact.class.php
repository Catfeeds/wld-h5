<?php 

/**
*  首页发现相关接口
*/
class StartNewact
{
    public $glbarr = array();    //定义全局数组
    public $tnum = '0';          //定义全局数组总数

	//返回值
	//type 1为直接弹出，2为弹出底层，3为点击直接弹框，4为点击直接跳转
	//sign 标志类型（1商家，2店铺红包，3宝箱，4气球，5其他活动）
	//$clickconf [shownum,redclick,boxclick,airclick,boxrand,airrand,spandnum]
	function StartClick($parr) {

        //获取活动相关配置
        $clickconf = IGD('Redis', 'Redis')->Gethash('newact')['data'];

		//新增点击次数
        if (!empty($parr['ucode'])) {
            $result = $this->OptionRedClick($parr,$clickconf);
            $clickinfo = $result['data'];
        }
        if ($parr['longitude'] <= 0 || $parr['latitude'] <= 0) {
            $localtion = GetAreafromIp();
            $parr['longitude'] = $localtion['longitude'];
            $parr['latitude'] = $localtion['latitude'];
        }

        // 查询附近商家
        $parr['pageindex'] = $parr['pageindex'];
        $parr['pagesize'] = ($clickconf['shownum']>0)?$clickconf['shownum']:10;
        $arr = $this->PushUsers($parr);
        $this->tnum = ($clickconf['shownum']>0)?$clickconf['shownum']:10;           //改变全局总数

        if (!empty($parr['ucode'])) {
            $change1 = 0;$redsign = 0;
            if (strpos($clickconf['redclick'], '%') !== false) {
                $redsign = 1;
                $gailv = str_replace('%', '', $clickconf['redclick']);
                $jihui = $gailv*100;
                $suiji = rand(1,10000);
                if ($suiji <= $jihui) {
                    $change1 = 1;
                }
            }
            
            //出现红包
            if (($clickinfo['c_redclick'] == $clickinfo['c_clicknum'] && $change1 != 1) || $change1 == 1) {
                $parr['type'] = 1;
                if ($change1 != 1 && $redsign == 0) {
                    $result = $this->EditRandClick($parr,$clickconf,$clickinfo);  //修改获奖随机点击次数
                }
            	$prize = $this->FindRedPrize($parr,$arr);
            	if ($prize['code'] == 0) { //写入奖项出现记录
    	        	$parr['type'] = 1;
    	        	$parr['acode'] = $prize['data']['c_acode'];
                    $parr['joinaid'] = $prize['data']['c_joinaid'];
    	        	$parr['awid'] = $prize['data']['c_id'];
    	        	$result = $this->AddShowLog($parr);
    	        	if ($result['code'] == 0) {
                        $arr = $prize['data']['list'];        		
    	        	}
            	}
            }

            $change2 = 0;$boxsign = 0;
            if (strpos($clickconf['boxclick'], '%') !== false) {
                $boxsign = 1;
                $gailv = str_replace('%', '', $clickconf['boxclick']);
                $jihui = $gailv*100;
                $suiji = rand(1,10000);
                if ($suiji <= $jihui) {
                    $change2 = 1;
                }
            }

            //出现宝箱
            if (($clickinfo['c_boxclick'] == $clickinfo['c_clicknum'] && $change2 != 1) || $change2 == 1) {
                $parr['type'] = 2;
                if ($change2 != 1 && $boxsign == 0) {
                    $result = $this->EditRandClick($parr,$clickconf,$clickinfo);  //修改获奖随机点击次数
                }
            	$prize = $this->FindBoxPrize($parr,$arr);
            	if ($prize['code'] == 0) {  //写入奖项出现记录
    	        	$parr['type'] = 2;
    	        	$parr['acode'] = $prize['data']['c_acode'];
    	        	$parr['joinaid'] = $prize['data']['c_id'];
                    $parr['awid'] = '';
    	        	$result = $this->AddShowLog($parr);
    	        	if ($result['code'] == 0) { 		
                        $arr = $prize['data']['list']; 		
    	        	}
            	}
            }

            $change3 = 0;$airsign = 0;
            if (strpos($clickconf['airclick'], '%') !== false) {
                $airsign = 1;
                $gailv = str_replace('%', '', $clickconf['airclick']);
                $jihui = $gailv*100;
                $suiji = rand(1,10000);
                if ($suiji <= $jihui) {
                    $change3 = 1;
                }
            }

            //出现气球
            if (($clickinfo['c_airclick'] == $clickinfo['c_clicknum'] && $change3 != 1) || $change3 == 1) {
                $parr['type'] = 3;
                if ($change3 != 1 && $airsign == 0) {
                    $result = $this->EditRandClick($parr,$clickconf,$clickinfo);  //修改获奖随机点击次数
                }
            	$prize = $this->FindAirPrize($parr,$arr);
            	if ($prize['code'] == 0) {  //写入奖项出现记录
    	        	$parr['type'] = 3;
    	        	$parr['acode'] = $prize['data']['c_acode'];
    	        	$parr['joinaid'] = $prize['data']['c_id'];
                    $parr['awid'] = '';
    	        	$result = $this->AddShowLog($parr);
    	        	if ($result['code'] == 0) {     		
                        $arr = $prize['data']['list'];
    	        	}
            	}
            }

            //有抽奖机会出现相关活动
            $jhwhere['c_ucode'] = $parr['ucode'];
            $jhwhere['c_num'] = array('GT',0);
            $jhwhere[] = array('c_rule=2 or c_rule=4');
            $jhinfo = M('Activity_lotterynum')->where($jhwhere)->select();
            foreach ($jhinfo as $key => $value) {
                $parr['c_rule'] = $value['c_rule'];
                $result = $this->LotteryPrize($parr,$arr);
                if ($result['code'] == 0) {             
                    $arr = $result['data'];
                }
            }

            //出现其他活动
            // $prize = $this->OtherActivity($parr);

        }

        //销毁变量释放内存
        unset($this->glbarr);
        unset($this->tnum);
        
        $returndata['pageindex'] = $parr['pageindex'];
        $returndata['list'] = $arr;
        return MessageInfo(0,'查询成功',$returndata); 
    }

    function LotteryPrize($parr,$arr)
    {
        if ($parr['c_rule'] == 2) {
            $name = '发现一个活动机会';
            $signature = '恭喜您获得一次“幸运大转盘”抽奖机会，赶紧去参与吧。';
            $purl = GetHost(1).'/index.php/Activity/Index/roulette';
        } else if ($parr['c_rule'] == 4) {           
            $name = '发现一个活动机会';
            $signature = '获得该店铺发起的“购物抽奖”活动机会，赶紧去参与吧。';
            $purl = GetHost(1).'/index.php/Activity/Index/lottery';
        }

        $pt = $this->Getrand();

        $arr[$pt]['sign'] = '5';
        $arr[$pt]['type'] = '3';
        $arr[$pt]['name'] = $name;
        $arr[$pt]['signature'] = $signature;
        $arr[$pt]['leftname'] = '继续寻宝';
        $arr[$pt]['leftact'] = '3';
        $arr[$pt]['leftkey'] = '10000';
        $arr[$pt]['rightname'] = '进店参与';
        $arr[$pt]['rightact'] = '2';
        $arr[$pt]['rightkey'] = $purl;        
        $arr[$pt]['img'] = '';
        $arr[$pt]['basemap'] = '';        
        $arr[$pt]['askurl'] = '';
        return MessageInfo(0, "获取成功", $arr);
    }

    /**
     * 获取随机个数红包奖项
     * @param ucode,longitude,latitude
     */
    function FindRedPrize($parr,$arr)
    {
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

    	//获取平台红包活动详情
        $result = IGD('Index','Newact')->GetPlatActInfo(20,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        //查询当天发现的红包列表
        $stw['c_ucode'] = $parr['ucode'];
        $stw['c_type'] = 1;
        $stw[] = array('c_acode is not null');
        $stw['c_status'] = array('neq',0);        
        $stw['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));        
        $acodearr = M('A_start_log')->where($stw)->field('c_acode')->select(); 
        $acodestr = arr_to_str($acodearr);

        if (!empty($acodestr)) {
            // $prizewhere['c_acode'] = array('not in',$acodestr);
            $prizewhere['c_acode'] = array(array('not in',$acodestr),array('EXP','IS NULL'),'OR');
        }

        //查询红包
        $prizewhere['c_num'] = array('GT',0);
        $prizewhere['c_joinaid'] = $activitydata['c_id'];
        $prizewhere['c_status'] = 1;
        $prizewhere['c_delete'] = 2;
        $prizedata = M('A_redprize')->where($prizewhere)->order('rand()')->find();
        if (!$prizedata) {
            return Message(3000, '没有相关数据');
        }

        if (!empty($prizedata['c_acode'])) {
            $uw['c_ucode'] = $prizedata['c_acode'];
            $userinfo = M('Users')->where($uw)->find();
            $pt = $this->Getrand();
            $arr[$pt]['keyvalue'] = $userinfo['c_ucode'];
            $arr[$pt]['nickname'] = $userinfo['c_nickname'];
            $arr[$pt]['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            $arr[$pt]['longitude'] = $userinfo['c_longitude1'];
            $arr[$pt]['latitude'] = $userinfo['c_latitude1'];            
            $arr[$pt]['address'] = $userinfo['c_address1'];
            $arr[$pt]['distance'] = $this->plandistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
        } else {
            $pt = $this->Getrand();
            $prizedata['c_acode'] = $arr[$pt]['keyvalue'];
        }

        $arr[$pt]['sign'] = '2';
        $arr[$pt]['type'] = '3';
        $arr[$pt]['name'] = '发现一个店铺红包';
        $arr[$pt]['signature'] = "小蜜在该商家店铺发现了红包，机会不容错过，赶快去领取~";
        $arr[$pt]['leftname'] = '继续发现';
        $arr[$pt]['leftact'] = '3';
        $arr[$pt]['leftkey'] = '10000';
        $arr[$pt]['rightname'] = '到店领取';
        $arr[$pt]['rightact'] = '3';
        $arr[$pt]['rightkey'] = '13';        
        $arr[$pt]['img'] = '';
        $arr[$pt]['basemap'] = '';        
        $arr[$pt]['askurl'] = '';
        $prizedata['list'] = $arr;
        return MessageInfo(0, "获取成功", $prizedata);
    }

    /**
     * 获取随机宝箱奖项
     * @param ucode,longitude,latitude
     */
    function FindBoxPrize($parr,$arr)
    {
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

    	//获取平台宝箱活动详情
        $result = IGD('Index','Newact')->GetPlathavingAct(22,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        //查询当天发现的宝箱列表
        $stw['c_ucode'] = $parr['ucode'];
        $stw['c_type'] = 2;
        $stw[] = array('c_acode is not null');
        $stw['c_status'] = array('neq',0);        
        $stw['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));        
        $acodearr = M('A_start_log')->where($stw)->field('c_acode')->select(); 
        $acodestr = arr_to_str($acodearr);

        //查询宝箱活动
        if (!empty($acodestr)) {
            // $actwhere['c_acode'] = array('not in',$acodestr);
            $prizewhere['c_acode'] = array(array('not in',$acodestr),array('EXP','IS NULL'),'OR');
        }
        
        $actwhere['c_aid'] = $activitydata['c_id'];
        $actwhere['c_state'] = 1;
        $actwhere['c_ishot'] = 1;
        //查询五十米范围内的宝箱
        $juli = 0.05;
        $actwhere[] = '(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*(' . $latitude . '-c_latitude)/360),2)+COS(PI()*33.07078170776367/180)* COS(c_longitude * PI()/180)*POW(SIN(PI()*(' . $longitude . '-c_longitude)/360),2)))) <= ' .  $juli . '';
        $actdata = M('Actjoin_moneylog')->where($actwhere)->order('rand()')->find();
        if (!$actdata) {
            return Message(3000, '没有相关数据');
        }

        if (!empty($actdata['c_acode'])) {
            $uw['c_ucode'] = $actdata['c_acode'];
            $userinfo = M('Users')->where($uw)->find();
            $pt = $this->Getrand();
            $arr[$pt]['keyvalue'] = $userinfo['c_ucode'];
            $arr[$pt]['nickname'] = $userinfo['c_nickname'];
            $arr[$pt]['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            $arr[$pt]['longitude'] = $userinfo['c_longitude1'];
            $arr[$pt]['latitude'] = $userinfo['c_latitude1'];            
            $arr[$pt]['address'] = $userinfo['c_address1'];
            $arr[$pt]['distance'] = $this->plandistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
        } else {
            $pt = $this->Getrand();
            $actdata['c_acode'] = $arr[$pt]['keyvalue'];
        }

        $arr[$pt]['sign'] = '3';
        $arr[$pt]['type'] = '3';
        $arr[$pt]['name'] = '发现一个宝箱';
        $arr[$pt]['signature'] = "小蜜发现该商家放出的宝箱，机会不容错过，赶快去领取~";
        $arr[$pt]['leftname'] = '继续寻宝';
        $arr[$pt]['leftact'] = '3';
        $arr[$pt]['leftkey'] = '10000';
        $arr[$pt]['rightname'] = '到店领取';
        $arr[$pt]['rightact'] = '3';
        $arr[$pt]['rightkey'] = '13';        
        $arr[$pt]['img'] = '';
        $arr[$pt]['basemap'] = '';
        $arr[$pt]['askurl'] = GetHost(2) . '/api.php/Home/Start/ReceiveBox?joinaid='.$actdata['c_id'];
        $actdata['list'] = $arr;
        return MessageInfo(0, "获取成功", $actdata);
    }

    // 获得该店铺发起的“购物抽奖”活动机会，赶紧去参与吧~

    /**
     * 获取随机气球奖项
     * @param ucode,longitude,latitude
     */
    function FindAirPrize($parr,$arr)
    {
        $longitude = $parr['longitude'];
        $latitude = $parr['latitude'];

    	//获取平台气球活动详情
        $result = IGD('Index','Newact')->GetPlathavingAct(23,1);
        if ($result['code'] != 0) {
            return $result;
        }
        $activitydata = $result['data'];

        //查询当天发现的气球列表
        $stw['c_ucode'] = $parr['ucode'];
        $stw['c_type'] = 3;
        $stw[] = array('c_acode is not null');
        $stw['c_status'] = array('neq',0);
        $stw['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')));
        $acodearr = M('A_start_log')->where($stw)->field('c_acode')->select(); 
        $acodestr = arr_to_str($acodearr);

        //查询气球活动
        if (!empty($acodestr)) {
            // $actwhere['c_acode'] = array('not in',$acodestr);
            $prizewhere['c_acode'] = array(array('not in',$acodestr),array('EXP','IS NULL'),'OR');
        }

        $actwhere['c_aid'] = $activitydata['c_id'];
        $actwhere['c_state'] = 1;
        $actwhere['c_ishot'] = 1;
        $actdata = M('Actjoin_moneylog')->where($actwhere)->order('rand()')->find();
        if (!$actdata) {
            return Message(3000, '没有相关数据');
        }

        if (!empty($actdata['c_acode'])) {
            $uw['c_ucode'] = $actdata['c_acode'];
            $userinfo = M('Users')->where($uw)->find();
            $pt = $this->Getrand();
            $arr[$pt]['keyvalue'] = $userinfo['c_ucode'];
            $arr[$pt]['nickname'] = $userinfo['c_nickname'];
            $arr[$pt]['headimg'] = GetHost() . '/' . $userinfo['c_headimg'];
            $arr[$pt]['longitude'] = $userinfo['c_longitude1'];
            $arr[$pt]['latitude'] = $userinfo['c_latitude1'];            
            $arr[$pt]['address'] = $userinfo['c_address1'];
            $arr[$pt]['distance'] = $this->plandistance($longitude, $latitude, $userinfo['c_longitude1'], $userinfo['c_latitude1']);
        } else {
            $pt = $this->Getrand();
            $actdata['c_acode'] = $arr[$pt]['keyvalue'];
        }

        $arr[$pt]['sign'] = '4';
        $arr[$pt]['type'] = '3';
        $arr[$pt]['name'] = '发现一个热气球';
        $arr[$pt]['signature'] = "小蜜发现该商家放出的热气球，机会不容错过，赶快去领取~";
        $arr[$pt]['leftname'] = '继续寻宝';
        $arr[$pt]['leftact'] = '3';
        $arr[$pt]['leftkey'] = '10000';
        $arr[$pt]['rightname'] = '到店领取';
        $arr[$pt]['rightact'] = '3';
        $arr[$pt]['rightkey'] = '13';        
        $arr[$pt]['img'] = '';
        $arr[$pt]['basemap'] = '';
        $arr[$pt]['askurl'] = GetHost(2) . '/api.php/Home/Start/ReceiveAir?joinaid='.$actdata['c_id'];
        $actdata['list'] = $arr;
        return MessageInfo(0, "获取成功", $actdata);
    }

    /**
     * 按距离获取商家列表
     * @param ucode,pageindex,pagesize,longitude,latitude
     */
    function PushUsers($param) {
        $list = array();
        if (empty($param['pageindex'])) {
            $parr['pageindex'] = 1;
        } else {
            $parr['pageindex'] = $param['pageindex'];
        }

        $parr['longitude'] = $param['longitude'];
        $parr['latitude'] = $param['latitude'];
        $parr['pagesize'] = $param['pagesize'];

        //usertype 0-全部用户 1-微商、会员用户 2-固定店铺商家,3-所有商家
        //取出微商用户
        $usertype = 3;
        $result = IGD('Getdata', 'Info')->CoalitionUserList1($parr, $usertype);
        $list = $result['data']['list'];

        if (count($list) != $parr['pagesize']) {
            $parr['pageindex'] = 1;
            $result = IGD('Getdata', 'Info')->CoalitionUserList1($parr, $usertype);
            $list = $result['data']['list'];
        }

        $arr = array();$pt = 0;
        foreach ($list as $key => $value) {
            $arr[$pt]['sign'] = '1';
            $arr[$pt]['type'] = '3';
            $arr[$pt]['longitude'] = $value['c_longitude1'];
            $arr[$pt]['latitude'] = $value['c_latitude1'];
            $arr[$pt]['name'] = '发现一个商家';
            $arr[$pt]['nickname'] = $value['c_nickname'];
            $arr[$pt]['headimg'] = $value['c_headimg'];
            $arr[$pt]['signature'] = $value['c_signature'];
            $arr[$pt]['distance'] = $value['c_distance'];
            if (empty($value['c_address1'])) {
                $arr[$pt]['address'] = "该用户正在附近潜水，点TA看看";
            } else {
                $arr[$pt]['address'] = $value['c_address1'];
            }
            $arr[$pt]['leftname'] = '继续寻宝';
            $arr[$pt]['leftact'] = '3';
            $arr[$pt]['leftkey'] = '10000';
            $arr[$pt]['rightname'] = '进店逛逛';
            $arr[$pt]['rightact'] = '3';
            $arr[$pt]['rightkey'] = '13';
            $arr[$pt]['keyvalue'] = $value['c_ucode'];
            $arr[$pt]['img'] = $value['c_headimg'];
            $arr[$pt]['basemap'] = '';
            $arr[$pt]['askurl'] = '';
            $pt++;
        }

        return $arr;
    }

	/**
	 * 添加奖项出现记录
	 * @param ucode,type,joinaid,awid,pcode
	 */
    function AddShowLog($parr)
    {
    	$log['c_ucode'] = $parr['ucode'];
    	$log['c_acode'] = $parr['acode'];
        $log['c_type'] = $parr['type'];
        $log['c_joinaid'] = $parr['joinaid'];
        $log['c_awid'] = $parr['awid'];
        $log['c_pcode'] = $parr['pcode'];
        $log['c_status'] = 0;
        $log['c_addtime'] = gdtime();
        $result = M('A_start_log')->add($log);
        if (!$result) {
            return Message(3003, '记录失败！');
        }
        return Message(0, '记录成功！');
    }

    /**
     * 获取出现记录
     * @param ucode,type,status
     */
    function GetShowLog($ucode,$acode,$type,$status,$istime)
    {
    	$where['c_ucode'] = $ucode;
    	$where['c_type'] = $type;
        if (!empty($acode)) {
            $where['c_acode'] = $acode;
        }
        if (isset($status)) {
            $where['c_status'] = $status;
        } else {
            $where[] = array('c_status=0 or c_status=1'); 
        }

        if ($istime == 1) {
            $where['c_addtime'] = array('between',array(date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')));
        }
    	$data = M('A_start_log')->where($where)->order('c_id desc')->find();
    	if (!$data) {
    		return Message(3000,'记录不存在');
    	}
    	return MessageInfo(0, '查询成功！',$data);
    }

    /**
     * 修改出现记录状态
     * @param ucode,id,status
     */
    function UpdateShowLog($ucode,$id,$status)
    {
    	$where['c_id'] = $id;
    	$where['c_status'] = 0;

    	if ($status == 1) {
    		$save['c_receivetime'] = gdtime();
    	}
    	$save['c_status'] = $status;
    	$save['c_updatetime'] = gdtime();
    	$result = M('A_start_log')->where($where)->save($save);
    	if (!$result) {
            return Message(3000,'修改失败');
        }

        return Message(0,'修改成功');
    }

    /**
     * 新增与添加用户首页点击次数
     * @param ucode,clickconf
     */
    function OptionRedClick($parr,$clickconf)
    {
        $where['c_ucode'] = $parr['ucode'];
        $where['c_datetime'] = date('Y-m-d');        
        $data['c_updatetime'] = gdtime();
        $clickdata = M('A_startclick')->where($where)->find();
        if (empty($clickdata['c_redclick']) && strpos($clickconf['redclick'], '%') === false) {
            $data['c_redclick'] = $this->GetAreaArraynum($clickconf['redclick']);
        }
        if (empty($clickdata['c_boxclick']) && strpos($clickconf['boxclick'], '%') === false) {
            $data['c_boxclick'] = $this->GetAreaArraynum($clickconf['boxclick']);
        }
        if (empty($clickdata['c_airclick']) && strpos($clickconf['airclick'], '%') === false) {
            $data['c_airclick'] = $this->GetAreaArraynum($clickconf['airclick']);
        }

        if (!$clickdata) {
        	$data['c_ucode'] = $parr['ucode'];
        	$data['c_datetime'] = date('Y-m-d');
            $data['c_clicknum'] = 1;            
            $data['c_addtime'] = gdtime();
            $result = M('A_startclick')->add($data);
            $returndata = $data;
        } else {
            $data['c_clicknum'] = $clickdata['c_clicknum'] + 1;
            $result = M('A_startclick')->where($where)->save($data);
            $clickdata['c_clicknum'] = $clickdata['c_clicknum'] + 1;
            $returndata = $clickdata;
        }

        if (!$result) {
            return Message(3000,'保存失败');
        }

        return MessageInfo(0,'保存成功',$returndata);
    }

    /**
     * 修改用户获奖随机点击次数
     * @param clickconf,clickinfo,ucode,type(1红包,2宝箱,3气球)
     */
    function EditRandClick($parr,$clickconf,$clickinfo)
    {
    	$where['c_ucode'] = $parr['ucode'];
        $where['c_datetime'] = date('Y-m-d'); 
    	if ($parr['type'] == 1) {
    		$data['c_redclick'] = $this->GetAreaArraynum($clickconf['redclick'],$clickinfo['c_redclick']);
    	} else if ($parr['type'] == 2) {
    		$data['c_boxclick'] = $this->GetAreaArraynum($clickconf['boxclick'],$clickinfo['c_boxclick']);
    	} else if ($parr['type'] == 3) {
    		$data['c_airclick'] = $this->GetAreaArraynum($clickconf['airclick'],$clickinfo['c_airclick']);
    	}
    
 	    $data['c_updatetime'] = gdtime();
        $result = M('A_startclick')->where($where)->save($data);
        if (!$result) {
            return Message(3000,'保存失败');
        }

        return Message(0,'保存成功');
    }

    //取区间数组随机值
    function GetAreaArraynum($array,$val,$n='0')
    {
        $arr = explode('|', $array);
        $nk = 0;
        foreach ($arr as $key => $value) {
            $arrval = explode('-', $value);
            if ($arrval[0] <= $val && $val <= $arrval[1]) {
                $nk = $key + 1;
            }
        }

        if ($nk >= count($arr)) {
            return $val;
        }
        $arrval = explode('-', $arr[$nk]);
        return rand($arrval[0],$arrval[1]);
    }

    //计算距离
    function plandistance($longitude, $latitude, $longitude1, $latitude1)
    {
        $str1 = GetDistance($longitude, $latitude, $longitude1, $latitude1);
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
        return '距您'.$strb;
    }

    //产生全局不重复随机数
    function Getrand()
    {
        $newarr = array();
        for ($i=0; $i < $this->tnum; $i++) { 
            if (!in_array($i,$this->glbarr)) {
                $newarr[] = $i;
            }
        }

        $randnum = count($newarr)-1;
        $num = 0;
        if ($randnum > 0) {
            $num = $newarr[rand(0,$randnum)];
        }
       
        $this->glbarr[count($this->glbarr)] = $num;     //改变随机数组
        return $num;
    }
}
	
?>