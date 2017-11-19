<?php
namespace Common\Behind;
/**
 * 系统统计模块
 * @author qiulin
 */
class TongjiBehind{

	//查询用户访问次数
	function  Accessdata($where,$where1,$daseflag,$xunit,$timetype,$querytime,$querytime1){
		$w = ' WHERE '.@implode(' AND ',$where);
		$w1 = ' WHERE '.@implode(' AND ',$where1);
		if($xunit == 1){
			$condition='%H';
		}else if($xunit == 2){
			$condition='%d';
		}else{
			$condition='%m';
		}

		$model1 = M('');

		if($daseflag == 0){
			$sql = "select count(*) as d,DATE_FORMAT(a.c_addtime,'$condition') as e from t_activity_findlog as a $w group by e";
			$count = $model1->query($sql);
		}else if($daseflag == 1){
			$sql = "select count(*) as d,DATE_FORMAT(b.c_addtime,'$condition') as e from t_activity_findlog1 as b $w1 group by e";
			$count = $model1->query($sql);
		}else{
			$sql = "select count(*) as d,DATE_FORMAT(a.c_addtime,'$condition') as e from t_activity_findlog as a $w group by e UNION select count(*) as d,DATE_FORMAT(b.c_addtime,'$condition') as e from t_activity_findlog1 as b $w1 group by e ";
			$count = $model1->query($sql);
		}

		if($timetype == 0){
			$xAxis = $this->hourArr();
		}else if($timetype == 1){
			$xAxis = $this->hourArr();
		}else if($timetype == 2){
			$xAxis = $this->dayArr(7);
		}else if($timetype == 3) {
			$xAxis = $this->dayArr(30);
		}else if($timetype == 4) {
			$xAxis = $this->mdayArr($querytime);
		}else{
			$xAxis = $this->monthArr($querytime,$querytime1);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}

	//查询用户收入支出
	function moneycount($where,$xunit,$timetype,$querytime,$querytime1,$type){
		$w = ' WHERE '.@implode(' AND ',$where);
		if($xunit == 1){
			$condition='%d';
		}else{
			$condition='%m';
		}

		if($type == 0){
			$w1 = $w.' AND a.c_money<0 ';
		}else{
			$w1 = $w.' AND a.c_money>0 ';
		}
		
		$sql = "select sum(a.c_money) as d,DATE_FORMAT(a.c_addtime,'$condition') as e from t_users_moneylog as a $w1 group by e";
		$model = M('');
		$count = $model->query($sql);

		if($timetype == 0){
			$xAxis[0] = date("d");
		}else if($timetype == 1){
			$xAxis[0] = date("d",strtotime("-1 day"));
		}else if($timetype == 2){
			$xAxis = $this->dayArr(7);
		}else if($timetype == 3) {
			$xAxis = $this->dayArr(30);
		}else if($timetype == 4) {
			$xAxis = $this->mdayArr($querytime);
		}else{
			$xAxis = $this->monthArr($querytime,$querytime1);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}

	//查询用户提现
	function txcount($where,$xunit,$timetype,$querytime,$querytime1,$type){
		$w = ' WHERE '.@implode(' AND ',$where);
		if($xunit == 1){
			$condition='%d';
		}else{
			$condition='%m';
		}

		if($type == 0){
			$w1 = $w;
		}else if($type == 1){
			$w1 = $w.' AND a.c_state=0 ';
		}else if($type == 2){
			$w1 = $w.' AND a.c_state=1 ';
		}else{
			$w1 = $w.' AND a.c_state=2 ';
		}
		
		$sql = "select sum(a.c_money) as d,DATE_FORMAT(a.c_addtime,'$condition') as e from t_users_drawing as a $w1 group by e";
		$model = M('');
		$count = $model->query($sql);

		if($timetype == 0){
			$xAxis[0] = date("d");
		}else if($timetype == 1){
			$xAxis[0] = date("d",strtotime("-1 day"));
		}else if($timetype == 2){
			$xAxis = $this->dayArr(7);
		}else if($timetype == 3) {
			$xAxis = $this->dayArr(30);
		}else if($timetype == 4) {
			$xAxis = $this->mdayArr($querytime);
		}else{
			$xAxis = $this->monthArr($querytime,$querytime1);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}

	//查询用户支付
	function paycount($where,$xunit,$timetype,$querytime,$querytime1,$type){
		$w = ' WHERE '.@implode(' AND ',$where);
		if($xunit == 1){
			$condition='%d';
		}else{
			$condition='%m';
		}

		if($type == 0){
			$w1 = $w;
		}else if($type == 1){
			$w1 = $w.' AND c_payrule=1 ';
		}else if($type == 2){
			$w1 = $w.' AND c_payrule=2 ';
		}else if($type == 3){
			$w1 = $w.' AND c_payrule=3 ';
		}else{
			$w1 = $w.' AND c_payrule=4 ';
		}

		// $sql = "SELECT SUM(c_money)as d,e from (
		// 		select c_money,c_payrule,c_addtime,DATE_FORMAT(c_addtime,'%d') as e from t_order_paylog 
		// 		Union All
		// 		select c_money,c_payrule,c_addtime,DATE_FORMAT(c_addtime,'%d') as e from t_supplier_order_paylog 
		// 		)t1 $w1 group by e";
	
		$sql = "SELECT SUM(c_money)as d,DATE_FORMAT(c_addtime,'%d') as e from  t_order_paylog $w1 group by e";
		
		$model = M('');
		$count = $model->query($sql);

		if($timetype == 0){
			$xAxis[0] = date("d");
		}else if($timetype == 1){
			$xAxis[0] = date("d",strtotime("-1 day"));
		}else if($timetype == 2){
			$xAxis = $this->dayArr(7);
		}else if($timetype == 3) {
			$xAxis = $this->dayArr(30);
		}else if($timetype == 4) {
			$xAxis = $this->mdayArr($querytime);
		}else{
			$xAxis = $this->monthArr($querytime,$querytime1);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}
    
    //查询用户支付
	function get_moneylog($where,$xunit,$timetype,$querytime,$querytime1,$type,$sign){
		$w = ' WHERE '.@implode(' AND ',$where);
		if($xunit == 1){
			$condition='%d';
		}else{
			$condition='%m';
		}

		$w = $w." AND c_sign='$sign' ";
		if($type == 0){
			$w1 = $w;
		}else if($type == 1){
			$w1 = $w.' AND c_type=1 ';
		}else if($type == 2){
			$w1 = $w.' AND c_type=2 ';
		}else if($type == 3){
			$w1 = $w.' AND c_type=3 ';
		}else{
			$w1 = $w.' AND c_type=4 ';
		}

		$sql = "SELECT SUM(c_money)as d,DATE_FORMAT(c_datetime,'%d') as e from  t_users_moneydate $w1 group by e";
		$model = M('');
		$count = $model->query($sql);

		if($timetype == 0){
			$xAxis[0] = date("d");
		}else if($timetype == 1){
			$xAxis[0] = date("d",strtotime("-1 day"));
		}else if($timetype == 2){
			$xAxis = $this->dayArr(7);
		}else if($timetype == 3) {
			$xAxis = $this->dayArr(30);
		}else if($timetype == 4) {
			$xAxis = $this->mdayArr($querytime);
		}else{
			$xAxis = $this->monthArr($querytime,$querytime1);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}
	//一天中的小时
	function hourArr(){
		for($i = 0;$i <= 24;$i++){
			if($i < 10 ){
				$arr[] = '0'.$i;
			}
			if($i >= 10){
				$arr[] = strval($i);
			}
		}
		return $arr;
	}

	//过去n天的日期
	function dayArr($n){
		$result = array();
		for($i = $n; $i > 0 ;$i--){
		  $result[] = strftime('%d',strtotime("-$i day"));
		}
		return $result;
	}

	//一月中的日期列表
	function mdayArr($querytime){
		$explode = explode("-",$querytime);
		$fate = cal_days_in_month(CAL_GREGORIAN,$explode[1],$explode[0]);
		for($i = 1;$i <= $fate;$i++){
			if($i < 10 ){
				$arr[] = '0'.$i;
			}
			if($i >= 10){
				$arr[] = strval($i);
			}
		}
		return $arr;
	}

	//一段时间中的月份
	function monthArr($querytime,$querytime1){
		$explode = explode("-",$querytime);
		$explode1 = explode("-",$querytime1);
		if($explode[0] == $explode1[0]){
			for($i = $explode[1];$i <= $explode1[1];$i++){
				$arr[] = intval($i);
			}
		}else{
			for($i = $explode[1];$i <= 12;$i++){
				$arr[] = intval($i);
			}
			for($i = 1;$i <= $explode1[1];$i++){
				$arr[] = intval($i);
			}
		}
		return $arr;
	}

	//组装返回图表x坐标及数据
	function packeddata($count,$xAxis){
		foreach ($xAxis as $k=>$v){
			if(!empty($count)){
				foreach ($count as $k1=>$v1){
					if($v == $v1['e']){
						$date[$k]['d']=$v1['d'];
					}
					if($date[$k]['d'] == ''){
						$date[$k]['d']='0';
					}
				}
			}else{
				$date[$k]['d']='0';
			}
			$date[$k]['e']=$v;
		}
		foreach ($date as $k2=>$v2){
			$x[]=$v2['e'];
			$y[]=$v2['d'];
		}
		$coun['xAxis']=$x;
		$coun['yAxis']=$y;		
		return $coun;
	}

	public function Userdata($where,$xunit,$timetype,$querytime,$querytime1,$type){
		$w = ' WHERE '.@implode(' AND ',$where);
		if($xunit == 1){
			$condition='%d';
		}else{
			$condition='%m';
		}
		
		$sql = "select count(*) as d,DATE_FORMAT(a.c_addtime,'$condition') as e from t_users as a $w group by e";
		$model1 = M('');
		$count = $model1->query($sql);
		
		if($timetype == 1){
			$xAxis = $this->mdayArr($querytime);
		}else if($timetype == 2){
			$xAxis = $this->monthArr($querytime,$querytime1);
		}else{
			$xAxis = $this->dayArr(30);
		}
		
		$coun=$this->packeddata($count,$xAxis);
		return $coun;
	}


}