<?php
namespace Home\Controller;
use Think\Controller;

class TongjiController extends BaseController{
	//用户访问统计
	public function activity_findlog(){
		$this->display();
	}

	//ajax异步获取用户访问数据
    public function querydata(){
    	$querytime=trim(I('querytime'));
    	$querytime1=trim(I('querytime1'));
    	$timetype=trim(I('timetype')); //按时间类型查询 0-默认当天、1-昨天、2-过去7天、3-过去30天、4/5-按时间段

    	//$xunit x轴时间单位 1-小时、2-天、3-月
        //$daseflag 数据库选择 0-原表、1-新表、2-原表、新表一起
    	if(empty($querytime) && empty($querytime1)){
    		if(!empty($timetype)){
    			if($timetype == 1){
    				$xunit = 1;
    				$time = time() - ( 1  *  24  *  60  *  60 );
    				$betime = date("Y-m-d",$time);
    				$w1[] = "DATE_FORMAT(b.c_addtime,'%Y-%m-%d')='$betime'";
                    $daseflag = 1;
    			}else if($timetype == 2){
    				$xunit = 2;
    				$time = time() - ( 1  *  24  *  60  *  60 );
    				$betime = date("Y-m-d",$time);
    				$time = $time - ( 6  *  24  *  60  *  60 );
    				$betime1 = date("Y-m-d",$time);
                    $yd = '2016-11-22';
                    if(strtotime($betime1) >= strtotime($yd)){
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')<='$betime'";
                        $daseflag = 1;
                    }else if((strtotime($betime1) < strtotime($yd)) && (strtotime($betime) >= strtotime($yd))){
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<'$yd'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')>='$yd'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')<'$betime'";
                        $daseflag = 2;
                    }else{
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                        $daseflag = 0;
                    }
    			}else{
    				$xunit = 2;
    				$time = time() - ( 1  *  24  *  60  *  60 );
    				$betime = date("Y-m-d",$time);
    				$time = $time - ( 29  *  24  *  60  *  60 );
    				$betime1 = date("Y-m-d",$time);
    				$yd = '2016-11-22';
                    if(strtotime($betime1) >= strtotime($yd)){
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')<='$betime'";
                        $daseflag = 1;
                    }else if((strtotime($betime1) < strtotime($yd)) && (strtotime($betime) >= strtotime($yd))){
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<'$yd'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')>='$yd'";
                        $w1[]="DATE_FORMAT(b.c_addtime,'%Y-%m-%d')<'$betime'";
                        $daseflag = 2;
                    }else{
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                        $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                        $daseflag = 0;
                    }
    			}
    		}else{
    			$xunit = 1;
    			$timetype = 0;
    			$betime = Date('Y-m-d');//默认当天数据
    			$w1[] = "DATE_FORMAT(b.c_addtime,'%Y-%m-%d')='$betime'";
                $daseflag = 1;
    		}
    	}else{
    		if($timetype == 4){
    			$xunit = 2;
    			$w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m')='$querytime'";
                $daseflag = 0;
    		}else{
    			$xunit = 3;
    			$w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')>='$querytime'";
    			$w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')<='$querytime1'";
                $daseflag = 0;
    		}
    	}

    	$data = D('Tongji','Behind')->Accessdata($w,$w1,$daseflag,$xunit,$timetype,$querytime,$querytime1);

    	$sumCount=array_sum($data['yAxis']);

    	if($timetype == 0){
    		$subtitle = "今日访问量（次）";
    		$countitle = "今日总访问量：";
    		$xAxis = $this->getxAxis($data['xAxis'],1);
		}else if($timetype == 1){
			$subtitle = "昨日访问量（次）";
			$countitle = "昨日总访问量：";
			$xAxis = $this->getxAxis($data['xAxis'],1);
		}else if($timetype == 2){
			$subtitle = "过去7天访问量（次）";
			$countitle = "过去7天总访问量：";
			$xAxis = $this->getxAxis($data['xAxis'],2);
		}else if($timetype == 3){
			$subtitle = "过去30天访问量（次）";
			$countitle = "过去30天总访问量：";
			$xAxis = $this->getxAxis($data['xAxis'],2);
		}else if($timetype == 4){
			$subtitle = $querytime."访问量（次）";
			$countitle = $querytime."总访问量：";
			$xAxis = $this->getxAxis($data['xAxis'],2);
		}else{
			$subtitle = $querytime." ~ ".$querytime1."访问量（次）";
			$countitle = $querytime." ~ ".$querytime1."总访问量：";
			$xAxis = $this->getxAxis($data['xAxis'],3);
		}

		$returndata['sumCount']=$countitle.$sumCount."  (次)";
		$returndata['subtitle']=$subtitle;
    	$returndata['yAxis']=$data['yAxis'];
    	$returndata['xAxis']=$xAxis;
    	$this->ajaxReturn($returndata,'JSON');
    }

    //x轴添加单位
    function getxAxis($Arr,$type){
    	if($type == 1){
    		$xunit = '时';
    	}else if($type == 2){
    		$xunit = '日';
    	}else{
    		$xunit = '月';
    	}

    	foreach ($Arr as $row) {
    		$result[] = $row.$xunit;
    	}
    	return $result;
    }

    //用户收支统计
    public function users_moneylog(){
        $sql = "select sum(case when c_money<0 then c_money else 0 end) as a,
        sum(case when c_money>0 then c_money else 0 end) as b,
        sum(case when c_bkmoney>0 then c_bkmoney else 0 end) as bk from t_users_moneylog ";
        $sql1 = "select sum(c_money) as c from t_users ";
        $model = M();
        $data = $model->query($sql);
        $data1 = $model->query($sql1);

        $outcome = $data[0]['a'];
        $income = $data[0]['b'];
        $remain = $data1[0]['c'];
        $bkmoney = $data[0]['bk'];

        $result = sprintf("%.2f", ($remain-$outcome)-($income-$bkmoney));
        if($result != 0){
            $this->warning = '警告：总收入减总支出不等于总余额。相差：'.$result.'(元)！';
        }

        $this->outcome = $outcome;
        $this->income = $income;
        $this->remain = $remain;
        $this->display();
    }

    //ajax异步获取用户收支数据
    public function query_moneylog(){
        $querytime=trim(I('querytime'));
        $querytime1=trim(I('querytime1'));
        $timetype=trim(I('timetype')); //按时间类型查询 0-默认当天、1-昨天、2-过去7天、3-过去30天、4/5-按时间段

        //$xunit x轴时间单位 1-小时、2-天、3-月
        if(empty($querytime) && empty($querytime1)){
            if(!empty($timetype)){
                if($timetype == 1){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m-%d')='$betime'";
                }else if($timetype == 2){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 6  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                }else{
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 29  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                }
            }else{
                $xunit = 1;
                $timetype = 0;
                $betime = Date('Y-m-d');//默认当天数据
                $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m-%d')='$betime'";
            }
        }else{
            if($timetype == 4){
                $xunit = 1;
                $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m')='$querytime'";
            }else{
                $xunit = 2;
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')>='$querytime'";
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')<='$querytime1'";
            }
        }

        $outcome = D('Tongji','Behind')->moneycount($w,$xunit,$timetype,$querytime,$querytime1,0);
        foreach($outcome['yAxis'] as $k=>$v){
            $outcome['yAxis'][$k] = sprintf('%.2f',abs($outcome['yAxis'][$k]));
        }
        $income = D('Tongji','Behind')->moneycount($w,$xunit,$timetype,$querytime,$querytime1,1);

        $q_outcome = array_sum($outcome['yAxis']);
        $q_income = array_sum($income['yAxis']);

        if($timetype == 0){
            $subtitle = $betime."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = "今日用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],2);
        }else if($timetype == 1){
            $subtitle = $betime."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = "昨日用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],2);
        }else if($timetype == 2){
            $subtitle = $betime1." ~ ".$betime."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = "过去7天用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],2);
        }else if($timetype == 3){
            $subtitle = $betime1." ~ ".$betime."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = "过去30天用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],2);
        }else if($timetype == 4){
            $subtitle = $querytime."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = $querytime."用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],2);
        }else{
            $subtitle = $querytime." ~ ".$querytime1."  总收入：".$q_income."（元） 总支出：".$q_outcome."（元）";
            $title = $querytime." ~ ".$querytime1."用户收入支出条形图统计";
            $xAxis = $this->getxAxis($outcome['xAxis'],3);
        }

        $returndata['maintitle'] = $title;
        $returndata['subtitle'] = $subtitle;
        $returndata['yAxis'] = $outcome['yAxis'];
        $returndata['yAxis1'] = $income['yAxis'];
        $returndata['xAxis'] = $xAxis;
        $this->ajaxReturn($returndata,'JSON');
    }

    //用户提现统计
    public function users_txlog(){
        $sql = "select sum(case when c_state=0 then c_money else 0 end) as a,
            sum(case when c_state=1 then c_money else 0 end) as b,
            sum(case when c_state=2 then c_money else 0 end) as c
            from t_users_drawing ";
        $model = M();
        $data = $model->query($sql);
        $all = sprintf('%.2f',array_sum($data[0]));
        $this->all = $all;
        $this->sqz = $data[0]['a'];
        $this->ytk = $data[0]['b'];
        $this->bty = $data[0]['c'];
        $this->display();
    }

    //ajax 异步获取用户提款数据
    public function query_txlog(){
       $querytime=trim(I('querytime'));
        $querytime1=trim(I('querytime1'));
        $timetype=trim(I('timetype')); //按时间类型查询 0-默认当天、1-昨天、2-过去7天、3-过去30天、4/5-按时间段

        //$xunit x轴时间单位 1-小时、2-天、3-月
        if(empty($querytime) && empty($querytime1)){
            if(!empty($timetype)){
                if($timetype == 1){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m-%d')='$betime'";
                }else if($timetype == 2){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 6  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                }else{
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 29  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
                }
            }else{
                $xunit = 1;
                $timetype = 0;
                $betime = Date('Y-m-d');//默认当天数据
                $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m-%d')='$betime'";
            }
        }else{
            if($timetype == 4){
                $xunit = 1;
                $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m')='$querytime'";
            }else{
                $xunit = 2;
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')>='$querytime'";
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')<='$querytime1'";
            }
        }

        $all = D('Tongji','Behind')->txcount($w,$xunit,$timetype,$querytime,$querytime1,0);
        $sqz = D('Tongji','Behind')->txcount($w,$xunit,$timetype,$querytime,$querytime1,1);
        $ytk = D('Tongji','Behind')->txcount($w,$xunit,$timetype,$querytime,$querytime1,2);
        $bty = D('Tongji','Behind')->txcount($w,$xunit,$timetype,$querytime,$querytime1,3);

        $q_all = array_sum($all['yAxis']);
        $q_sqz = array_sum($sqz['yAxis']);
        $q_ytk = array_sum($ytk['yAxis']);
        $q_bty = array_sum($bty['yAxis']);

        if($timetype == 0){
            $subtitle = $betime."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $title = "今日用户提款条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 1){
            $subtitle = $betime."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $title = "昨日用户提款条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 2){
            $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 3){
            $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $title = "过去30天用户提款条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 4){
            $subtitle = $querytime."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $title = $querytime."用户提款条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else{
            $subtitle = $querytime." ~ ".$querytime1."  总计：".$q_all."（元） 申请中：".$q_sqz."（元） 已提款：".$q_ytk."（元） 不同意：".$q_bty."（元）";
            $title = $querytime." ~ ".$querytime1."用户提款条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],3);
        }

        $returndata['maintitle'] = $title;
        $returndata['subtitle'] = $subtitle;
        $returndata['yAxis'] = $all['yAxis'];
        $returndata['yAxis1'] = $sqz['yAxis'];
        $returndata['yAxis2'] = $ytk['yAxis'];
        $returndata['yAxis3'] = $bty['yAxis'];
        $returndata['xAxis'] = $xAxis;
        $this->ajaxReturn($returndata,'JSON');
    }

    //活动分享
    public function share_log(){
        $db = M('Share_log as l');
        //条件
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['l.c_nickname'] = $c_username;
        }

        $activityname = trim(I('activityname'));
        if (!empty($activityname)) {
            $w['a.c_activityname'] = $activityname;
        }

        $c_state = trim(I('c_state'));
        if (!empty($c_state)) {
            if($c_state == 10){
               $c_state = 0;
            }
            $w['l.c_state'] = $c_state;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'l.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'l.*,a.c_activityname';
        $panrn['join'] = 'left join t_activity as a on a.c_id=l.c_aid';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];

        foreach($data_list as $key=>$value){
            $w1['c_vcode'] = $value['c_vcode'];
            $data_list[$key]['numbs'] = M('Share_viewlog')->where($w1)->count();
        }

        $countarr = $this->Get_count($w);
        $this->m_count = $countarr[0];
        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->display();
    }

    //统计送出佣金
    public function Get_count($w){
        if(!empty($w)){
            $wkey = array_keys($w);
            $wvalues = array_values($w);
            $where = '';
            foreach ($wkey as $key => $value) {
               if (strpos($value, 'l.c_nickname') !== false) {
                    $where .= " and l.c_nickname='".$wvalues[$key]."'";
                }elseif(strpos($value, 'a.c_activityname') !== false){
                    $where .= " and a.c_activityname='".$wvalues[$key]."'";
                }elseif(strpos($value, 'l.c_state') !== false){
                    $where .= " and l.c_state='".$wvalues[$key]."'";
                }
            }
        }

        $sql = "select sum(case when l.c_money>0 then l.c_money else 0 end) as money
        from t_share_log as l
        left join t_activity as a on a.c_id=l.c_aid where 1=1 ".$where;

        $model = M();
        $data = $model->query($sql);
        return $data;
    }

    //分享查看记录
    public function share_viewlog(){
         $db = M('Share_viewlog as l');
        //条件
        $vcode = I('vcode');
        $w['l.c_vcode'] = $vcode;

        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $w['l.c_nickname'] = $c_username;
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'l.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $data_list = $date['list'];

        $this->list = $data_list;
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->post = $parent;
        $this->display();
    }

    //支付（支付方式）数据统计
    public function users_paylog(){
        $sql = "select t2.*,a.c_payname from t_pay_type as a LEFT JOIN (
                select c_payrule,SUM(c_money) as smoney from (
                select c_payrule,c_money from  t_order_paylog
                Union All
                select c_payrule,c_money from t_supplier_order_paylog) t1  GROUP BY c_payrule) t2
                on a.c_payrule=t2.c_payrule";
        $model = M();
        $data = $model->query($sql);

        foreach ($data as $key => $value) {
            $all = sprintf('%.2f',$value['smoney']+$all);
            if($value['c_payrule'] == 1){
                $zfb = sprintf('%.2f',$value['smoney']);
            }else if($value['c_payrule'] == 2){
                $sjwx = sprintf('%.2f',$value['smoney']);
            }else if($value['c_payrule'] == 3){
                $wx = sprintf('%.2f',$value['smoney']);
            }else if($value['c_payrule'] == 4){
                $ye = sprintf('%.2f',$value['smoney']);
            }
        }

        $this->all = isset($all) ? $all : 0.00;
        $this->zfb = isset($zfb) ? $zfb : 0.00;
        $this->sjwx = isset($sjwx) ? $sjwx : 0.00;
        $this->wx = isset($wx) ? $wx : 0.00;
        $this->ye = isset($ye) ? $ye : 0.00;
        $this->display();
    }

    //ajax 异步获取用户提款数据
    public function query_paylog(){
       $querytime=trim(I('querytime'));
        $querytime1=trim(I('querytime1'));
        $timetype=trim(I('timetype')); //按时间类型查询 0-默认当天、1-昨天、2-过去7天、3-过去30天、4/5-按时间段

        //$xunit x轴时间单位 1-小时、2-天、3-月
        if(empty($querytime) && empty($querytime1)){
            if(!empty($timetype)){
                if($timetype == 1){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $w[] = "DATE_FORMAT(c_addtime,'%Y-%m-%d')='$betime'";
                }else if($timetype == 2){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 6  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(c_addtime,'%Y-%m-%d')<='$betime'";
                }else{
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 29  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="DATE_FORMAT(c_addtime,'%Y-%m-%d')>='$betime1'";
                    $w[]="DATE_FORMAT(c_addtime,'%Y-%m-%d')<='$betime'";
                }
            }else{
                $xunit = 1;
                $timetype = 0;
                $betime = Date('Y-m-d');//默认当天数据
                $w[] = "DATE_FORMAT(c_addtime,'%Y-%m-%d')='$betime'";
            }
        }else{
            if($timetype == 4){
                $xunit = 1;
                $w[] = "DATE_FORMAT(c_addtime,'%Y-%m')='$querytime'";
            }else{
                $xunit = 2;
                $w[]="DATE_FORMAT(c_addtime,'%Y-%m')>='$querytime'";
                $w[]="DATE_FORMAT(c_addtime,'%Y-%m')<='$querytime1'";
            }
        }

        $all = D('Tongji','Behind')->paycount($w,$xunit,$timetype,$querytime,$querytime1,0);
        $zfb = D('Tongji','Behind')->paycount($w,$xunit,$timetype,$querytime,$querytime1,1);
        $sjwx = D('Tongji','Behind')->paycount($w,$xunit,$timetype,$querytime,$querytime1,2);
        $wx = D('Tongji','Behind')->paycount($w,$xunit,$timetype,$querytime,$querytime1,3);
        $ye = D('Tongji','Behind')->paycount($w,$xunit,$timetype,$querytime,$querytime1,4);

        $q_all = sprintf('%.2f',array_sum($all['yAxis']));
        $q_zfb = sprintf('%.2f',array_sum($zfb['yAxis']));
        $q_sjwx = sprintf('%.2f',array_sum($sjwx['yAxis']));
        $q_wx = sprintf('%.2f',array_sum($wx['yAxis']));
        $q_ye = sprintf('%.2f',array_sum($ye['yAxis']));

        if($timetype == 0){
            $subtitle = $betime."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $title = "今日支付方式报表条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 1){
            $subtitle = $betime."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $title = "昨日支付方式报表条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 2){
            $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 3){
            $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $title = "过去30天支付方式报表条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else if($timetype == 4){
            $subtitle = $querytime."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $title = $querytime."支付方式报表条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],2);
        }else{
            $subtitle = $querytime." ~ ".$querytime1."  总计：".$q_all."（元） 支付宝支付：".$q_zfb."（元） 手机微信支付：".$q_sjwx."（元） 微信支付：".$q_wx."（元）余额支付：".$q_ye."（元）";
            $title = $querytime." ~ ".$querytime1."支付方式报表条形统计图";
            $xAxis = $this->getxAxis($all['xAxis'],3);
        }

        $returndata['maintitle'] = $title;
        $returndata['subtitle'] = $subtitle;
        $returndata['yAxis'] = $all['yAxis'];
        $returndata['yAxis1'] = $zfb['yAxis'];
        $returndata['yAxis2'] = $sjwx['yAxis'];
        $returndata['yAxis3'] = $wx['yAxis'];
        $returndata['yAxis4'] = $ye['yAxis'];
        $returndata['xAxis'] = $xAxis;
        $this->ajaxReturn($returndata,'JSON');
    }

    //用户量统计
    public function user_numb(){
        $this->count = M('Users')->count();
        $this->display();
    }
    //ajax异步获取用户量数据
    public function user_querydata(){
        $querytime=trim(I('querytime'));
        $querytime1=trim(I('querytime1'));
        $timetype=trim(I('timetype')); //按时间类型查询 1当月/2-按时间段

        //$xunit x轴时间单位 1-天、2-月
        if(!empty($querytime) && !empty($querytime1)){
            if($timetype == 1){
                $xunit = 1;
                $w[] = "DATE_FORMAT(a.c_addtime,'%Y-%m')='$querytime'";
            }else{
                $xunit = 2;
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')>='$querytime'";
                $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m')<='$querytime1'";
            }
        }else{
            $xunit = 1;
            $time = time() - ( 1  *  24  *  60  *  60 );
            $betime = date("Y-m-d",$time);
            $time = $time - ( 29  *  24  *  60  *  60 );
            $betime1 = date("Y-m-d",$time);
            $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')>='$betime1'";
            $w[]="DATE_FORMAT(a.c_addtime,'%Y-%m-%d')<='$betime'";
        }

        $data = D('Tongji','Behind')->Userdata($w,$xunit,$timetype,$querytime,$querytime1);

        $sumCount=array_sum($data['yAxis']);
        $allCount = M('Users')->count();

        if($timetype == 1){
            $subtitle = $querytime."每日用户增加量（人）";
            $countitle = "用户总量：".$allCount."(人);   ".$querytime;
            $xAxis = $this->getxAxis($data['xAxis'],2);
        }else if($timetype == 2){
            $subtitle = $querytime." ~ ".$querytime1."每日用户增加量（人）";
            $countitle = "用户总量：".$allCount."(人);   ".$querytime." ~ ".$querytime1;
            $xAxis = $this->getxAxis($data['xAxis'],3);
        }else{
            $subtitle = "过去30天每日用户增加量（人）";
            $countitle = "用户总量：".$allCount."(人);   "."过去30天";
            $xAxis = $this->getxAxis($data['xAxis'],2);
        }

        $returndata['sumCount']=$countitle."用户增加量：".$sumCount."  (人)";
        $returndata['subtitle']=$subtitle;
        $returndata['yAxis']=$data['yAxis'];
        $returndata['xAxis']=$xAxis;
        $this->ajaxReturn($returndata,'JSON');
    }

    //用户点击统计
    public function startclick(){
        $db = M('A_startclick as a');

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "d.c_addtime between '".$begintime."' and '".$endtime."'";
        }


        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'a.c_id desc';//排序
        $panrn['limit'] = 25;//分页数

         //分页显示数据
        $panrn['field'] = 'a.*,u.c_nickname';
        $panrn['join'] = 'left join t_users as u on u.c_ucode=a.c_ucode';
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //金额收明细数据统计
    public function moneylog(){
        
        // $sql = "select t2.*,a.c_payname from t_pay_type as a LEFT JOIN (
        //         select c_payrule,SUM(c_money) as smoney from (
        //         select c_payrule,c_money from  t_order_paylog
        //         Union All
        //         select c_payrule,c_money from t_supplier_order_paylog) t1  GROUP BY c_payrule) t2
        //         on a.c_payrule=t2.c_payrule";
        //  $model = M();
        //  $data = $model->query($sql);
        // foreach ($data as $key => $value) {
        //     $all = sprintf('%.2f',$value['smoney']+$all);
        //     if($value['c_type'] == 1){
        //         $qt = sprintf('%.2f',$value['smoney']);
        //     }else if($value['c_type'] == 2){
        //         $smzf = sprintf('%.2f',$value['smoney']);
        //     }else if($value['c_type'] == 3){
        //         $xsdd = sprintf('%.2f',$value['smoney']);
        //     }else if($value['c_type'] == 4){
        //         $hb = sprintf('%.2f',$value['smoney']);
        //     }
        // }
        // $this->all = isset($all) ? $all : 0.00;
        // $this->qt = isset($qt) ? $qt : 0.00;
        // $this->smzf = isset($smzf) ? $smzf : 0.00;
        // $this->xsdd = isset($xsdd) ? $xsdd : 0.00;
        // $this->hb = isset($hb) ? $hb : 0.00;
        $this->display();
    }

    //ajax 异步获取平台金额记录数据
    public function get_moneylog(){
        $querytime=trim(I('querytime'));
        $querytime1=trim(I('querytime1'));
        $sign = I('sign');
        $timetype=trim(I('timetype')); //按时间类型查询 0-默认当天、1-昨天、2-过去7天、3-过去30天、4/5-按时间段

        //$xunit x轴时间单位 1-小时、2-天、3-月
        if(empty($querytime) && empty($querytime1)){
            if(!empty($timetype)){
                if($timetype == 1){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $w[] = "c_datetime='$betime'";
                }else if($timetype == 2){
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 6  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="c_datetime>='$betime1'";
                    $w[]="c_datetime<='$betime'";
                }else{
                    $xunit = 1;
                    $time = time() - ( 1  *  24  *  60  *  60 );
                    $betime = date("Y-m-d",$time);
                    $time = $time - ( 29  *  24  *  60  *  60 );
                    $betime1 = date("Y-m-d",$time);
                    $w[]="c_datetime>='$betime1'";
                    $w[]="c_datetime<='$betime'";
                }
            }else{
                $xunit = 1;
                $timetype = 0;
                $betime = Date('Y-m-d');//默认当天数据
                $w[] = "c_datetime='$betime'";
            }
        }else{
            if($timetype == 4){
                $xunit = 1;
                $w[] = "DATE_FORMAT(c_datetime,'%Y-%m')='$querytime'";
            }else{
                $xunit = 2;
                $w[]="DATE_FORMAT(c_datetime,'%Y-%m')>='$querytime'";
                $w[]="DATE_FORMAT(c_datetime,'%Y-%m')<='$querytime1'";
            }
        }

        $all = D('Tongji','Behind')->get_moneylog($w,$xunit,$timetype,$querytime,$querytime1,0,$sign);
        $qt = D('Tongji','Behind')->get_moneylog($w,$xunit,$timetype,$querytime,$querytime1,1,$sign);
        $smzf = D('Tongji','Behind')->get_moneylog($w,$xunit,$timetype,$querytime,$querytime1,2,$sign);
        $xsdd = D('Tongji','Behind')->get_moneylog($w,$xunit,$timetype,$querytime,$querytime1,3,$sign);
        $hb = D('Tongji','Behind')->get_moneylog($w,$xunit,$timetype,$querytime,$querytime1,4,$sign);

        $q_all = sprintf('%.2f',array_sum($all['yAxis']));
        $q_qt = sprintf('%.2f',array_sum($qt['yAxis']));
        $q_smzf = sprintf('%.2f',array_sum($smzf['yAxis']));
        $q_xsdd = sprintf('%.2f',array_sum($xsdd['yAxis']));
        $q_hb = sprintf('%.2f',array_sum($hb['yAxis']));
        if ($sign == 1) 
        {
            if($timetype == 0){
                $subtitle = $betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = "今日金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 1){
                $subtitle = $betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = "昨日金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 2){
                $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = "过去7天金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 3){
                $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = "过去30天金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 4){
                $subtitle = $querytime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = $querytime."金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else{
                $subtitle = $querytime." ~ ".$querytime1."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）红包(提现)：".$q_hb."（元）";
                $title = $querytime." ~ ".$querytime1."金额明细收入报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],3);
            }
        }elseif ($sign == 2) {
            if($timetype == 0){
                $subtitle = $betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = "今日金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 1){
                $subtitle = $betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = "昨日金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 2){
                $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = "过去7天金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 3){
                $subtitle = $betime1." ~ ".$betime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = "过去30天金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else if($timetype == 4){
                $subtitle = $querytime."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = $querytime."金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],2);
            }else{
                $subtitle = $querytime." ~ ".$querytime1."  总计：".$q_all."（元） 其他：".$q_qt."（元） 扫码支付：".$q_smzf."（元） 线上订单：".$q_xsdd."（元）提现：".$q_hb."（元）";
                $title = $querytime." ~ ".$querytime1."金额明细支出报表条形统计图";
                $xAxis = $this->getxAxis($all['xAxis'],3);
            }
        }
        

        $returndata['maintitle'] = $title;
        $returndata['subtitle'] = $subtitle;
        $returndata['yAxis'] = $all['yAxis'];
        $returndata['yAxis1'] = $qt['yAxis'];
        $returndata['yAxis2'] = $smzf['yAxis'];
        $returndata['yAxis3'] = $xsdd['yAxis'];
        $returndata['yAxis4'] = $hb['yAxis'];
        $returndata['xAxis'] = $xAxis;
        $this->ajaxReturn($returndata,'JSON');
    }
}