<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  实体商家
 */
class StoreController extends BaseController{
	//实体商家 店铺信息
	public function store_info(){
		$ucode = I('ucode');
		// $ucode = 'T10048';
		$where['c_ucode'] = $ucode;
		$storeinfo = M('Store')->where($where)->find();
		if(!empty($storeinfo)){
			//过滤省市
			$storeinfo['c_provice'] = str_replace("省",'',$storeinfo['c_provice']);
			$storeinfo['c_city'] = str_replace("市",'',$storeinfo['c_city']);

			$storeinfo['c_cityid'] = $this->GetAddrinfo($storeinfo['c_city'],1);
			$storeinfo['c_districtid'] = $this->GetAddrinfo($storeinfo['c_district'],1);

			//查询实体店铺服务
			$swhere['a.c_storeid'] = $storeinfo['c_id'];
			$field = "b.c_id,b.c_name";
			$join = "left join t_service_project as b on a.c_serviceid=b.c_id";
			$this->store_service = M('store_service as a')->field($field)->join($join)->where($swhere)->select();

			// $storeinfo['service_project'] = $store_service;

			//查询店铺图片
			$imgwhere['c_regionid'] = $storeinfo['c_id'];
			$imgwhere['c_sourceid'] = 4;
			$imglist = M('resource_img')->field('c_img')->where($imgwhere)->select();
			foreach ($imglist as $key => $value) {
            	$imglist[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
        	}

        	$storeinfo["imglist"] = $imglist;
        	$this->vo = $storeinfo;
		}
		$parr['parentid'] = 1;
    	$parr['regiontype'] = 1;
    	$this->provice = IGD('User','User')->GetAddress($parr);
		$this->services = M('service_project')->field('c_id,c_name')->select();
		$this->ucode = $ucode;
		$this->display();
	}

	//根据地区编号获取地址
    public function GetAddr(){
    	$parentid = I('id');
    	$regiontype = I('value');

    	$parr['parentid'] = $parentid;
    	$parr['regiontype'] = $regiontype;
    	$date = IGD('User','User')->GetAddress($parr);
    	$this->ajaxReturn($date);
    }

    //根据地区名称或者id获取地址
    public function GetAddrinfo($value,$flag){
    	if($flag == 1){
    		$w['region_name'] = $value;
    		$result = M('Region')->where($w)->getField('region_id');
    	}else{
    		$w['region_id'] = $value;
    		$result = M('Region')->where($w)->getField('region_name');
    	}

    	return $result;
    }

	//添加、编辑提交数据
	public function store_info_tj(){
        $db = M('');
        $db -> startTrans();

		$id = $_POST['id'];
		if(!$id){//id不存在，则为添加
			//上传图片
			$imgresult = uploadimg('offstore');
            if ($imgresult['code'] != 0) {
                $this->error($imgresult['msg']);
            }
            $param['imglist'] = array_values($imgresult['data']);
		}else{//id存在，则为编辑
			$param['storeid'] = $id;
			foreach (array_keys($_POST) as $k => $v) {
                if (strpos($v,'img')  !== false) {
                    if (!empty($_POST[$v])) {
                        $imglist[] = str_replace(GetHost().'/', '', $_POST[$v]);
                    }
                }
            }

            if (!empty($_FILES)) {
                 $imgresult = uploadimg('offstore');
                if ($imgresult['code'] != 0) {
                    $this->error($imgresult['msg']);
                }
                if (count($imglist) > 0) {
                    $param['imglist'] = array_merge($imglist,array_values($imgresult['data']));
                } else {
                    $param['imglist'] = array_values($imgresult['data']);
                }
            } else {
                $param['imglist'] = $imglist;
            }

		}

		//服务项目
        $param['sourcearr'] = $_POST['serviceid'];

        $param['ucode'] = $_POST['ucode'];
		$param['name'] = $_POST['name'];
        $param['desc'] = $_POST['desc'];
        $param['provice'] = $this->GetAddrinfo($_POST['c_provice'],2);
        $param['city'] = $this->GetAddrinfo($_POST['c_city'],2);
        $param['district'] = $this->GetAddrinfo($_POST['c_district'],2);
        $param['address'] = $_POST['address'];
        $param['longitude'] = $_POST['longitude'];
        $param['latitude'] = $_POST['latitude'];
        $param['remind'] = $_POST['remind'];
        $param['opentime'] = $_POST['opentime'];

        $result = IGD('Store','Store')->AddStoreInfo($param);

        if($result['code'] != 0){
            $db -> rollback();
        	$this->error($result['msg']);
        }

        //同步地址信息
        $users_save['c_address1'] = $_POST['address'];
        $users_save['c_longitude1'] = $_POST['longitude'];
        $users_save['c_latitude1'] = $_POST['latitude'];

        $where['c_ucode'] = $_POST['ucode'];
        $result1 = M('Users')->where($where)->save($users_save);

        if($result1 < 0){
            $db -> rollback();
            $this->error("同步用户表地址失败");
        }

        $result2 = M('User_local')->where($where)->save($users_save);

        if($result2 < 0){
            $db -> rollback();
            $this->error("同步用户地址表地址失败");
        }

        $db -> commit();
       	$this->success("保存成功");
	}

    //实体商家店铺服务列表
    public function store_service(){
        $db = M('service_project');
        //条件
        $name = trim(I('name'));
        if (!empty($name)) {
            $w['c_name'] = array('like', "%{$name}%");
        }

        $state = trim(I('state'));
        if (!empty($state)) {
            if($state == 1){
                $w['c_state'] = $state;
            }else{
                $w['c_state'] = 0;
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

    //ajax 修改服务状态
    public function service_state(){
        $Id = I('gid');
        $state = I('active');
        if(empty($Id)) die("非法操作，ID为空！");
        $where['c_id'] = $Id;
        $data['c_state'] = $state;
        $result = M('service_project')->where($where)->save($data);
        if(!$result){
            die("操作失败！");
        }
    }

    //验证表单
    public function checkfrom($data){
        if (empty($data['name'])) {
            $this->error("服务名称不能为空");
        }
        if (empty($data['sort'])) {
            $this->error("服务排序不能为空");
        }
        if ($data['state'] == '') {
            $this->error("服务开启状态未选择");
        }
    }

    //服务添加
    public function service_add(){
        $this->action = 'service_add';
        if(IS_POST){
            $db = M('service_project');
            $this->checkfrom($_POST);

            if(empty($_FILES['imgpath']['name'])){
                $this->error("服务展示图必须上传");
            }

            $fileresult = uploadimg('store');
            if ($fileresult['code'] != 0) {
              $this->error($fileresult['msg']);
            }

            $data['c_name'] = $_POST['name'];
            $data['c_imgpath'] = $fileresult['data']['imgpath'];
            $data['c_sort'] = $_POST['sort'];
            $data['c_state'] = $_POST['state'];
            $data['c_addtime'] = Date('Y-m-d H:i:s');

            $result = $db->add($data);
            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Store/store_service';
              echo '<script language="javascript">alert("添加成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('添加失败！');
            }
        }
        $this->display();
    }

    //服务编辑
    public function service_edit(){
        $Id = I('Id');
        $this->action = 'service_edit';
        $db = M('service_project');
        $where['c_id'] = $Id;
        $this->vo = $db->where($where)->find();
        if(IS_POST){
            $this->checkfrom($_POST);
            $fileresult = uploadimg('store');
            if(!empty($_FILES['imgpath']['name'])){
                if ($fileresult['code'] != 0) {
                  $this->error($fileresult['msg']);
                }
                $data['c_imgpath'] = $fileresult['data']['imgpath'];
            }

            $data['c_name'] = $_POST['name'];
            $data['c_sort'] = $_POST['sort'];
            $data['c_state'] = $_POST['state'];

            $where['c_id'] = $_POST['Id'];
            $result = M('service_project')->where($where)->save($data);

            if($result){
              $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Store/store_service';
              echo '<script language="javascript">alert("编辑成功");</script>';
              echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
            }else{
              $this->error('编辑失败！');
            }
        }
        $this->display('service_add');
    }

    //服务删除
    public function service_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('service_project')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }


    //连锁总店列表
    public function chain_list(){
        $db = M('A_federation as a');
        //条件
        $name = trim(I('name'));
        if (!empty($name)) {
            $w['c_name'] = array('like', "%{$name}%");
        }

        $state = trim(I('state'));
        if (!empty($state)) {
            if($state == 1){
                $w['c_state'] = $state;
            }else{
                $w['c_state'] = 0;
            }
        }

        $panrn['where'] = $w;
        $parent = I('param.');
        $panrn['order'] = 'c_id desc';//排序
        $panrn['limit'] = 25;//分页数

        //分页显示数据
        $list=D('Db','Behind');
        $date=$list->mate_select_pages($db,$panrn);
        $this->list = $date['list'];
        $this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
        $this->root_url = GetHost()."/";
        $this->post = $parent;
        $this->display();
    }

}