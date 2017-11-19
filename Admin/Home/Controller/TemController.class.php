<?php
namespace Home\Controller;
use Think\Controller;
//财务对账临时报表
class TemController extends BaseController{
	//财务每天上午10点截单，排查提现用户中是否存在错误进账
	function errordata(){
		//当前时间
		$nowtime = date('Y-m-d H:i:s');

		//截单时间
		$sectime = date('Y-m-d')." 10:00:00";

		if($nowtime > $sectime){
			$begintime = date("Y-m-d  H:i:s",strtotime("-1 days",strtotime($sectime)));
			$endtime = $sectime;
		}else{
			$endtime = date('Y-m-d', strtotime('-1 days'))." 10:00:00";
			$begintime = date("Y-m-d  H:i:s",strtotime("-1 days",strtotime($endtime)));
		}

		$w = "c_state=0 ";
		$w = $w." and c_addtime between '".$begintime."' and '".$endtime."' ";		

	    $w1 = "(t.c_source=9 or t.c_source=4 or t.c_source=1) and t.c_money>0 and t.c_addtime between '".$begintime."' and '".$endtime."' ";	

	    $key = trim(I('key'));
        if (!empty($key)) {
        	$w1 = $w1." and t.c_key= '".$key."' ";
        }

        $db = M('');

        $sql = "select  t.c_ucode,t.c_key,t.c_money,t.c_addtime, count(0) as ct from t_users_moneylog t where t.c_ucode in ( 
		SELECT c_ucode from t_users_drawing WHERE $w
		)
		AND  $w1 GROUP BY t.c_key HAVING ct>1;";

		$data = $db->query($sql);

		foreach ($data as $key => $value) {
			$where['c_ucode'] = $value['c_ucode'];

			//用户信息
			$userinfo = M('Users')->field('c_nickname,c_phone,c_headimg')->where($where)->find();

			$data[$key]['c_nickname'] = $userinfo['c_nickname'];
			$data[$key]['c_phone'] = $userinfo['c_phone'];
			$data[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];

			//提现信息
			$dw[] = array("c_ucode='".$value['c_ucode']."' and c_addtime between '".$begintime."' and '".$endtime."'");
			$drawinglog = M('Users_drawing')->field('c_sign')->where($dw)->find();
			$data[$key]['c_sign'] = $drawinglog['c_sign'];
		}

		$this->count = count($data);

		$parent = I('param.');
		$this->list = $data;
		$this->post = $parent;

		$this->display();
	}

	//技术查账每日异常订单数据
	function errororders(){
		//查询条件
		$w = "(c_source=9 or c_source=4 or c_source=1 or c_source=2) and c_money>0 and c_key is not null ";

		$sectime = I('sectime');
	    if(isset($sectime)&&!empty($sectime)){
	        $w = $w." and c_addtime between '".$sectime." 00:00:00' and '".$sectime." 23:59:59' ";
	    }else{
	    	$star = date("Y-m-d",strtotime("-7 days"));
	    	$end = date("Y-m-d");
	    	$w = $w." and c_addtime between '".$star." 00:00:00' and '".$end." 23:59:59' ";
	    }		
	    
        $db = M('');

        $sql = "select  c_ucode,c_source,c_key,c_money,c_addtime, count(0) as ct from t_users_moneylog  where  $w GROUP BY c_key HAVING ct>1;";

		$data = $db->query($sql);

		foreach ($data as $key => $value) {
			$where['c_ucode'] = $value['c_ucode'];

			//用户信息
			$userinfo = M('Users')->field('c_nickname,c_phone,c_headimg')->where($where)->find();

			$data[$key]['c_nickname'] = $userinfo['c_nickname'];
			$data[$key]['c_phone'] = $userinfo['c_phone'];
			$data[$key]['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
		}
		
		$this->count = count($data);

		$parent = I('param.');
		$this->list = $data;
		$this->post = $parent;

		$this->display();
	}		
}