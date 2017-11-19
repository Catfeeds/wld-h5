<?php

namespace Home\Controller;

use Base\Controller\AuthController;

/**
 * 系统中心
 */
class AboutController extends AuthController {

    //系统中心
    public function index() {
        $this->show();
    }

    // 关于小蜜
    public function Aboutmi() {
        $w['c_type_rule'] = 2;
        $data = M('News')->field('c_content')->where($w)->find();
        $this->content = $data['c_content'];
        $this->show();
    }

    //帮助中心
    public function helpmi() {
        $w['c_type_rule'] = 3;
        $keyword = trim(I('keyword'));
        if (!empty($keyword)) {
           $w['c_title'] = array('like', "%{$keyword}%");
        }
        $parent = I('param.');
        $date = M('News')->field('c_id,c_title')->where($w)->select();
        $this->data = $date;
        $this->post = $parent;
        $this->show();
    }

    //帮助中心
    public function helper() {
        $w['c_type_rule'] = 3;
        $keyword = trim(I('keyword'));
        if (!empty($keyword)) {
           $w['c_title'] = array('like', "%{$keyword}%");
        }
        $parent = I('param.');
        $date = M('News')->field('c_id,c_title')->where($w)->select();
        $this->data = $date;
        /*未读消息数量*/
        $parr['ucode'] = session('USER.ucode');
        $result =  IGD('Activityv2','Activity')->Getmsgnum($parr);
        $this->msgnum = $result['data'];

        $this->post = $parent;
        $this->apptype = I('type');
        $this->show();
    }

    //帮助详情
    public function helpdetail(){
        $id = I('tid');
        $w['c_id'] = $id;
        $this->data = M('News')->field('c_title,c_content')->where($w)->find();
        $this->show();
    }

    public function feedback() {
//        if (IS_POST && session('time') == $_POST['time']) {
//            $parr['ucode'] = session('USER.ucode');
//            $parr['content'] = $_POST['feedback'];
//            $parr['platform'] = get_device_type();
//            $parr['system'] = GetOs();
//            $parr['ip'] = GetIP();
//            $parr['browser'] = GetBrowser();
//            $imgresult = uploadimg('feedback');
//            if ($imgresult['code'] != 0) {
//                $resultdata = $imgresult['msg'];
//                echo "<script >mui.toast('$resultdata','frame')</script>";
//                return;
//            }
//            //$parr['imglist'] = array_values($imgresult['data']);
//            $parr['imglist'] = implode('|', array_values($imgresult['data']));
//            $result = IGD('About','Info')->AddFeedback($parr);
//            $resultdata = $result['msg'];
//            if ($result['code'] != 0) {
//                echo "<script >mui.toast('$resultdata','frame')</script>";
//                return;
//            }
//            session('time', null);
//            echo "<script>mui.toast('$resultdata','frame');setTimeout(function(){";
//            if($apptype==1){
//                echo "javaScript: resultData.goSetting();";
//            }else if($apptype==2){
//                echo "window.webkit.messageHandlers.AppModel.postMessage({'popSet':''});";
//            }else{
//                echo "window.parent.location.href = 'index';";
//            }
//            echo "},2000);</script>";
//
//            return;
//        }

//        $time = time();
//        session('time', $time);    // 防止重复提交
//        $this->assign('time', $time);
        /*判断访问的客户端*/
        $apptype = I('type');
        if (empty($apptype)) {
            if (is_weixin()) {
                $this->apptype = 4;
            } else {
                $this->apptype = get_device_type();
            }
        } else {
            $this->apptype = $apptype;
        }
        $this->show();
    }

    /**
     * 提交反馈
     *
     */
    public function AddFeedback(){
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));
        $parr['ucode'] = session('USER.ucode');
        $parr['content'] = $data['feedback'];
        $parr['platform'] = get_device_type();
        $parr['system'] = GetOs();
        $parr['ip'] = GetIP();
        $parr['browser'] = GetBrowser();
        if(!empty($data['imglist'])){
            $parr['imglist'] = $data['imglist'];
        }
        $result = IGD('About','Info')->AddFeedback($parr);
        $this->ajaxReturn($result);
    }

}
