<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 通用上文件通用控制器
 */
class UploadController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    /**
	*ajax上传图片功能
	*@param $_POST['Id'] 以此作为t_imgid标示
	**/
    public function uploadify(){
        $sign = $_GET['sign'];
        $imgresult = uploadimg('tempimg',$sign);
        if ($imgresult['code'] != 0) {
            $this->ajaxReturn($imgresult);
        }
        $data['imgshow'] = GetHost().'/'.array_values($imgresult['data'])[0];
        $data['imgval'] = array_values($imgresult['data'])[0];
        $this->ajaxReturn(MessageInfo(0,'上传成功',$data));
    }

	/**
	*ajax删除功能
	*@param $_GET['name'] 匹配数据表字段
	**/
    public function delimg(){
        unlink($_GET['name']);
        qiniu_del_files($_GET['name']);
        $this->ajaxReturn(Message('0','删除成功'));
    }



}
