<?php
namespace Home\Controller;
use Think\Controller;
/**
 *  通用方法封装
 */
class CommonController extends Controller {

	/**
	*ajax上传图片功能
	*@param $_POST['Id'] 以此作为t_imgid标示
	**/
    public function uploadify(){
        $m = date('Ym');
        $d = date('d');
        $Id = $_POST['Id'];
        $verifyToken = md5($_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $getBack = uploadimg("Pattern");
            if($getBack['code'] != 0){
                $this->error('上传错误,请重新尝试...！');
            }else{// 上传成功 获取上传文件信息
                echo implode('|',$getBack['data']);
            }
        }
    }

	/**
	*ajax删除功能
	*@param $_GET['name'] 匹配数据表字段
	**/
    public function del(){
        if($_GET['name']!=""){
            // if(unlink($_GET['name'])){
                $this->ajaxReturn(Message('0','删除成功'));
            // } else{
            //     $this->ajaxReturn(Message('1000','删除失败'));
            // }
        }else{
            $this->ajaxReturn(Message('1000','数据为空'));
        }

    }

    // 删除所有所传图片
    public function del_img($imgarr){
        foreach ($imgarr as $key => $value) {
            unlink($value);
        }
    }

}