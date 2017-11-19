<?php
namespace Agent\Controller;
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
		$ucode = session('_AGENT_UCODE');
		$join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
		$where['a.c_acode'] = $ucode;
		$where['a.c_isagent'] = 0;
		$keys = I('keys');
		$where[] = array("b.c_name like '%$keys%' or b.c_phone='$keys'");
		$where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
		$field = 'a.c_isagent,a.c_headimg,b.*';
		$order = 'b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
		$count = M('Users as a')->join($join)->where($where)->count();
		$page = getpage($count,20);
		$limit = $page->firstRow.','.$page->listRows;
		$data = M('Users as a')->join($join)->where($where)->order($order)->limit($limit)->field($field)->select();
		$this->assign('data',$data);
        $this->statu = I('statu');

		$this->page = $page->show();
		$this->baoqian = '<div class="baoqian" style="text-align: center;width:100%;color:#999;padding:2% 0;">没有找到相关数据</div>';
		$this->display();
	}

	// 微商详情
	public function details()
	{
		$parr1['ucode'] = session('_AGENT_UCODE');
		$result = D('Common','Service')->GetupsetInfo($parr1);
		$this->shopinfo = $result['data'];
		$parr['infoid'] = I('Id');
		$this->sid = I('Id');
		$result = IGD('Myagent','Agent')->GetShopInfo($parr);
		$this->assign('data',$result['data']);
		$this->display();
	}

	// 微商审核
	public function checked()
	{
		$parr['ucode'] = session('_AGENT_UCODE');
		$result = D('Common','Service')->GetStatuMessage($parr);
		if ($result['code'] != 0) {
			$this->ajaxReturn($result);
		}

		$parr['sid'] = I('sid');
		$parr['checked'] = I('checked');
		$parr['ucode'] = session('_AGENT_UCODE');
		$result = D('Agent','Service')->AgentCheckShop($parr);
		$this->ajaxReturn($result);
	}

    //商家审核列表
    public function GetStoreShenList(){

        $ucode = session('_AGENT_UCODE');
        $type =I('type');
        $keys = I('keys');

        if(!empty($keys)){
            $where['a.c_acode'] = $ucode;
            $where['a.c_isagent'] = 0;
            $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
            $where[] = array("a.c_nickname like '%$keys%'");
            $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
            $join1 ='left join t_user_local as c on a.c_ucode=c.c_ucode';
            $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
            $order = 'b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
            $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
            foreach($data as $key =>$v){
                $order = M('Order_agent')->where(array('c_sid'=>$v['c_id'],'c_pay_status'=>1))->find();
                if($order && !empty($order)){
                    $data[$key]['pay_status'] =1;
                }else{
                    $data[$key]['pay_status'] =0;
                }
                $data[$key]['pay_money'] =0.01;
                $data[$key]['c_headimg']=$v['c_headimg']==null?null:GetHost().'/'.$v['c_headimg'];
                $data[$key]['c_name']=$v['c_nickname'];
            }
            $msg['code']=0;
            $msg['msg']='查询成功';
            $msg['data']=$data;
            $this->ajaxReturn($msg);
        }

        switch($type){
            case 1:  //已审核  包括 1 驳回  2 待区代审核 3 已通过
                $msg['code']=0;
                $msg['msg']='查询成功';
                $msg['data']=[$this->Data($ucode,2),$this->Data($ucode,3),$this->Data($ucode,1)];
                $this->ajaxReturn($msg);
                break;
            case 2:  //未审核    0 还没处理

                $where['a.c_acode'] = $ucode;
                $where['a.c_isagent'] = 0;
                $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
                $where['b.c_checked'] =0;

                $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
                $join1 = 'left join t_user_local as c on a.c_ucode=c.c_ucode';
                $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
                $order = 'b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
                $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
                foreach($data as $key =>$v){
                    $order = M('Order_agent')->where(array('c_sid'=>$v['c_id'],'c_pay_status'=>1))->find();
                    if($order && !empty($order)){
                        $data[$key]['pay_status'] =1;
                    }else{
                        $data[$key]['pay_status'] =0;
                    }
                    $data[$key]['pay_money'] =0.01;
                    $data[$key]['c_headimg']=$v['c_headimg']==null?null:GetHost().'/'.$v['c_headimg'];
                    $data[$key]['c_name']=$v['c_nickname'];
                }
                $msg['code']=0;
                $msg['msg']='查询成功';
                $msg['data']=$data;
                $this->ajaxReturn($msg);
                break;
        }
    }

    public function Data($ucode,$flag){

        $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
        $join1 = 'left join t_user_local as c on a.c_ucode=c.c_ucode';
        $field = 'a.c_isagent,a.c_headimg,a.c_nickname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
        $order = 'b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';

        if($flag==2){
            $w['b.c_checked']=array("in",array(2,4));
        }else{
            $w['b.c_checked']=$flag;
        }
        $w['a.c_acode']=$ucode;
        $w['a.c_isagent']=0;
        $w[]=array('b.c_dcode is not null');

        $list1 = M('Users as a')->join($join)->join($join1)->where($w)->order($order)->field($field)->select();
        if($flag==2){
            $aa['name']='审核中';
        }elseif($flag==3){
            $aa['name']='已通过审核';
        }elseif($flag==1){
            $aa['name']='未通过审核';
        }
        $aa['total']=count($list1);
        foreach($list1 as $one=>$a){
            $aa['list'][$one]['c_isagent']=$a['c_isagent'];
            $aa['list'][$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
            $aa['list'][$one]['c_name']=$a['c_nickname'];
            // $aa['list'][$one]['c_merchantname']=$a['c_merchantname'];
            $aa['list'][$one]['c_checked']=$a['c_checked'];
            $aa['list'][$one]['c_id']=$a['c_id'];
            $aa['list'][$one]['c_type']=$a['c_type'];
            $aa['list'][$one]['c_isfixed']=$a['c_isfixed'];
            $order = M('Order_agent')->where(array('c_sid'=>$a['c_id'],'c_pay_status'=>1))->find();
            if($order && !empty($order)){
                $aa['list'][$one]['pay_status'] =1;
            }else{
                $aa['list'][$one]['pay_status'] =0;
            }
            $aa['list'][$one]['pay_money'] =0.01;
        }
        return $aa;
    }


}