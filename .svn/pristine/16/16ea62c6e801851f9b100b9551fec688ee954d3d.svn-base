<?php
namespace Common\Behind;

class DbBehind{
	//多条查询分页  
	/**  $db 表名 
	 *  $panrn  查询条件where  、查询字段 field、 排序  order 、分页limit
	**/
    function mate_select_pages($db,$panrn) {
    	$where=$panrn['where'];
    	$field=$panrn['field'];
    	$limit=$panrn['limit'];
    	$order=$panrn['order'];
    	$join=$panrn['join'];
    	$join1=$panrn['join1'];
    	$join2=$panrn['join2'];
        $join3=$panrn['join3'];
    	$count= $db->where($where)->join($join)->join($join1)->join($join2)->join($join3)->count();
    	$Page = getpage($count,$limit);
    	$data['list']=$db->where($where)->join($join)->join($join1)->join($join2)->join($join3)->limit($Page->firstRow.','.$Page->listRows)->field($field)->order($order)->select();
    	// dump($data['list']);die;
        $data['Page']= $Page->show();// 分页显示输出
    	$data['post']=I('param.');//筛选返条件返回值
    	$data['count']=$count;
        $resultjson = $data;
        return $resultjson;
    }
    
    //查询单条无分页
     function mate_find($db,$panrn){
        $where=$panrn['where'];
        $field=$panrn['field'];
        $join=$panrn['join'];
        $join1=$panrn['join1'];
    	$join2=$panrn['join2'];
        $join3=$panrn['join3'];
        $lsit=$db->where($where)->join($join)->join($join1)->join($join2)->join($join3)->field($field)->find();
        $resultjson = $lsit;
        return $resultjson;
    }

    //单条查询
    function videofind($db,$panrn){
    	$where=$panrn['where'];
    	$field=$panrn['field'];
    	$info=$db->where($where)->field($field)->find();
    	$resultjson = $info;
    	return $resultjson;
    }

    //查询多条无分页
     function mate_select($db,$panrn){
        $where=$panrn['where'];
        $field=$panrn['field'];
        $join=$panrn['join'];
        $join1=$panrn['join1'];
        $join2=$panrn['join2'];
        $join3=$panrn['join3'];
        $lsit=$db->where($where)->join($join)->join($join1)->join($join2)->join($join3)->field($field)->select();
        $resultjson = $lsit;
        return $resultjson;
    }
    
    //删除
    function viddelete($db,$where){
    	$b=$db->where($where)->delete();
    	$resultjson = $b;
    	return $resultjson;
    }
    
    //添加
    function condadd($db){
    	$b=$db->add($_POST);
    	$resultjson = $b;
    	return $resultjson;
    }

    //插入数据
    function insert($db,$data){
        $b=$db->add($data);
        $resultjson = $b;
        return $resultjson;
    }
     
    //修改
    function update($db,$data,$where){
    	$b=$db->where($where)->save($data);
    	$resultjson = $b;
    	return $resultjson;
    }
  
    //添加图片
    function img($c_img,$catalog){
    	$upload = new \Think\Upload();
    	$upload->maxSize   =     3145728 ;
    	$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
    	$upload->savePath  =      $catalog;
    	$info   =   $upload->uploadOne($c_img);
    	if(!$info) {
    		$img=$this->error($upload->getError());
    	}else{
    		$img='Uploads/'.$info['savepath'].$info['savename'];
    	}
    	return $img;
    }
}