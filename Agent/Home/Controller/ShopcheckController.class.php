<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  微商审核管理
 */
class ShopcheckController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	// 微商列表
	public function index()
	{
		$ucode = session('_ADMIN_UCODE');
		$acodewhere['c_acode'] = $ucode;
		$acodearr = M('Users')->where($acodewhere)->field('c_ucode')->select();
		if ($acodearr) {
			foreach ($acodearr as $key => $value) {
				if ($key == 0) {
					$acodestr .= $value['c_ucode'];
				} else {
					$acodestr .= ','.$value['c_ucode'];
				}
			}
		} else {
			$acodestr = 'aaaaaaaaaa';
		}
		$join = 'left join t_check_shopinfo as b on b.c_ucode=a.c_ucode';
		$where['a.c_acode'] = array("in","$acodestr");
		$where['a.c_isagent'] = 0;
		$where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
		$keys = I('keys');
		$where[] = array("b.c_name like '%$keys%' or b.c_phone='$keys'");
		$field = 'a.c_isagent,a.c_headimg,b.*';
		$order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
		$count = M('Users as a')->join($join)->where($where)->count();
		$page = getpage($count,20);
		$limit = $page->firstRow.','.$page->listRows;
		$data = M('Users as a')->join($join)->where($where)->order($order)->limit($limit)->field($field)->select();
		$this->assign('data',$data);
        $this->statu = I('statu');
		//dump(M('Users')->_sql());die;
		$this->page = $page->show();
		$this->baoqian = '<div class="baoqian" style="text-align: center;width:100%;color:#999;padding:2% 0;">没有找到相关数据</div>';
		$this->display();
	}

	// 微商详情
	public function details()
	{
		$parr['infoid'] = I('Id');
		$this->sid = I('Id');
		$result = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('data',$result['data']);

		$this->display();
	}

	// 微商审核
	public function checked()
	{
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Common','Service')->GetStatuMessage($parr);
		if ($result['code'] != 0) {
			$this->ajaxReturn($result);
		}
		$parr['sid'] = I('sid');
		$parr['checked'] = I('checked');
		$parr['ucode'] = session('_ADMIN_UCODE');
		$result = D('Agent','Service')->CheckShop($parr);
		$this->ajaxReturn($result);
	}


    //商家审核列表
    public function GetStoreShenList(){

        $ucode = session('_ADMIN_UCODE');
		$type =I('type');
        $keys = I('keys');
        if(!empty($keys)){
            $acodewhere['c_acode'] = $ucode;
            $acodearr = M('Users')->where($acodewhere)->field('c_ucode')->select();
            if ($acodearr) {
                foreach ($acodearr as $key => $value) {
                    if ($key == 0) {
                        $acodestr .= $value['c_ucode'];
                    } else {
                        $acodestr .= ','.$value['c_ucode'];
                    }
                }
            } else {
                $acodestr = 'aaaaaaaaaa';
            }
            $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
            $join1 = 'left join t_user_local as c on a.c_ucode=c.c_ucode';
            $where[] = array("a.c_nickname like '%$keys%'");
            $where['a.c_acode'] = array("in","$acodestr");
            //$con =[1,2,3];
            // $where['b.c_checked'] = array("in","$con");
            $where['a.c_isagent'] = 0;
            $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
            $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
            $order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
            $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
            foreach($data as $one=>$a){
                $data[$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
                $data[$one]['c_name']=$a['c_nickname'];
            }
            $msg['code']=0;
            $msg['msg']='查询成功';
            $msg['data']=$data;
            $this->ajaxReturn($msg);
        }

        switch($type){
            case 1:
                $msg['code']=0;
                $msg['msg']='查询成功';
                $msg['data']=[$this->Data($ucode,3),$this->Data($ucode,4),$this->Data($ucode,1)];
                $this->ajaxReturn($msg);
                break;
            case 2:
                $acodewhere['c_acode'] = $ucode;
                $acodearr = M('Users')->where($acodewhere)->field('c_ucode')->select();
                if ($acodearr) {
                    foreach ($acodearr as $key => $value) {
                        if ($key == 0) {
                            $acodestr .= $value['c_ucode'];
                        } else {
                            $acodestr .= ','.$value['c_ucode'];
                        }
                    }
                } else {
                    $acodestr = 'aaaaaaaaaa';
                }
                $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
                $join1 = 'left join t_user_local as c on a.c_ucode=c.c_ucode';
                $where['a.c_acode'] = array("in","$acodestr");
                $where['a.c_isagent'] = 0;
                $where['b.c_checked'] = 2;
                $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
                $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
                $order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
                $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
                foreach($data as $one=>$a){
                    $data[$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
                    $data[$one]['c_name']=$a['c_nickname'];
                }
                $msg['code']=0;
                $msg['msg']='查询成功';
                $msg['data']=$data;
                $this->ajaxReturn($msg);
                break;
        }
    }


    public function Data($ucode,$flag){

        $acodewhere['c_acode'] = $ucode;
        $acodearr = M('Users')->where($acodewhere)->field('c_ucode')->select();
        if ($acodearr) {
            foreach ($acodearr as $key => $value) {
                if ($key == 0) {
                    $acodestr .= $value['c_ucode'];
                } else {
                    $acodestr .= ','.$value['c_ucode'];
                }
            }
        } else {
            $acodestr = 'aaaaaaaaaa';
        }
        $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
        $join1 = 'left join t_user_local as c on a.c_ucode=c.c_ucode';
        $where['a.c_acode'] = array("in","$acodestr");
        $where['a.c_isagent'] = 0;
        $where['b.c_checked'] =$flag;
        $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
        $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
        $order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
        $list1 = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
        if($flag==3) {
            $aa['name'] = '已通过审核';
        }elseif($flag==1){
            $aa['name']='未通过审核';
        }elseif($flag==0){
            $aa['name']='待审核';
        }elseif($flag==4){
            $aa['name']='审核中';
        }
        $aa['total']=count($list1);
        foreach($list1 as $one=>$a){
            $aa['list'][$one]['c_isagent']=$a['c_isagent'];
            $aa['list'][$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
            $aa['list'][$one]['c_name']=$a['c_nickname'];
            //$aa['list'][$one]['c_merchantname']=$a['c_merchantname'];
            $aa['list'][$one]['c_checked']=$a['c_checked'];
            $aa['list'][$one]['c_id']=$a['c_id'];
            $aa['list'][$one]['c_type']=$a['c_type'];
            $aa['list'][$one]['c_isfixed']=$a['c_isfixed'];
        }
        return $aa;
    }

}