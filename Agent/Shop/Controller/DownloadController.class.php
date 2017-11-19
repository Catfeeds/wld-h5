<?php
namespace Shop\Controller;
use Think\Controller;
/**
 *  资料管理
 */
class DownloadController extends BaseController {
	public function __construct()
    {
    	parent::__construct();
		header('Content-Type:text/html; charset=utf-8');
    }

   	
	// 首页
	public function index()
	{
		$result = D('Infomation','Service')->GetMarialList();
        $this->assign('data',$result['data']);
		$this->display();
	}

	// 下载文件
    public function downfile()
    {
        $parr['Id'] = I('Id');
        $result =  D('Infomation','Service')->downfile($parr);
        $data = $result['data'];
        if ($result['code'] != 0) {
            $this->ajaxReturn(Message(404,'没有对应的文件'));
        }
        $name = $data['c_name'];
        $typearr = explode('.',$data['c_filepath']);
        $type = $typearr[count($typearr)-1];
        ob_start(); 
        $filename = $data['c_filepath'];
        header( "Content-type:  application/force-download"); 
        header( "Accept-Ranges:  bytes "); 
        header( "Content-Disposition:  attachment;  filename= {$name}.{$type}"); 
        $size = readfile($filename); 
        header( "Accept-Length: " .$size);
    }
	
}