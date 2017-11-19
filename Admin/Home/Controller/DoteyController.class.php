<?php

namespace Home\Controller;
use Think\Controller;

/**
*   活动管理2
*/
class DoteyController extends BaseController {

	//聚宝活动记录
	public function index(){
		$aid = I('aid');
		$db = M('Activity_dotey as d');
		//条件
		$activityname = trim(I('activityname'));
	    if (!empty($activityname)) {
	        $w['a.c_activityname'] = array('like', "%{$activityname}%");
	    }

	  	$nickname = trim(I('nickname'));
	    if (!empty($nickname)) {
	        $w['u.c_nickname'] = array('like', "%{$nickname}%");
	    }

	    $upstate = trim(I('upstate'));
	    if ($upstate == 1) {
	    	$upname = trim(I('upname'));
		    if (!empty($upname)) {
		        $w['pu.c_nickname'] = array('like', "%{$upname}%");
		    } else {
		    	$w[] = array("pu.c_nickname is not null");
		    }
	    } else if ($upstate == 2) {
	    	$w[] = array("pu.c_nickname is null or pu.c_nickname=''");
	    }

	    $state = trim(I('state'));
	    if ($state == 1) {
	        $w['d.c_state'] = 1;
	    } else if ($state == 2) {
	    	$w['d.c_state'] = 0;
	    }

	    $porder = trim(I('porder'));
	    if ($porder == 1) {
	        $panrn['order'] = 'd.c_portion desc';
	    } else if ($porder == 2) {
	    	$panrn['order'] = 'd.c_portion asc';
	    } else {
	    	$panrn['order'] = 'd.c_id desc';
	    }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['limit'] = 25;//分页数

	    //分页显示数据
	    $panrn['field'] = 'd.*,u.c_ucode as fromucode,u.c_nickname as fromname,pu.c_ucode as upucode,'
	    				. 'pu.c_nickname as upname,a.c_activityname';
	    $panrn['join'] = 'left join t_users as u on u.c_ucode=d.c_ucode';
	    $panrn['join1'] = 'left join t_users as pu on pu.c_ucode=d.c_pcode';
	    $panrn['join2'] = 'left join t_activity as a on a.c_id=d.c_aid';
	    $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];

		$this->count = $date['count'];//分页\
    	$this->page = $date['Page'];//分页
		$this->root_url = GetHost()."/";
		$this->post = $parent;
		$this->aid = I('aid');

		$sql = "select sum(c_portion) as portionnum,sum(c_value) as moneynum from t_activity_dotey where c_aid='$aid'";
		$doteyarr = M('')->query($sql);
		$this->portionnum = $doteyarr[0]['portionnum'];
		$this->moneynum = $doteyarr[0]['moneynum'];

		$this->display();
	}
}