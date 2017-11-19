<?php
namespace Agent\Controller;

use Base\Controller\BaseController;
/**
 *  公告控制器
 */
class AreagentController extends BaseController{

    // 获取 3 条 区代公告信息
    public function GetThreeNotice()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $join = "left join t_check_infolog as b on a.c_id=b.c_infoid and b.c_ucode='$ucode'";
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $whereinfo[] = array("a.c_type=1 or a.c_type=2 or a.c_type=4");
        $order = 'case when ifnull(b.c_infoid,"")="" then 0 else 1 end asc,a.c_addtime desc';
        $field = 'a.*,b.c_infoid';
        $count = M('Check_info as a')->join($join)->where($whereinfo)->count();
        //$page = getpage($count,20);
        //$limit = $page->firstRow.','.$page->listRows;
        $data = M('Check_info as a')->join($join)->where($whereinfo)->order($order)->limit(3)->field($field)->select();
        foreach($data as $key=>$value){
            $data[$key]['c_addtime'] =substr($value['c_addtime'],0,10);
            if($value['c_content']!==null){
                $data[$key]['c_flag'] =1;
                $data[$key]['to_url'] =GetHost(1).'/index.php/Home/Info/affiche?Id='.$value['c_id'];
            }else{
                if(strstr($value['c_url'],'/Home/Agentntrol/')){
                    $data[$key]['c_flag'] =3;
                }elseif(strstr($value['c_url'],'/Home/Shopcheck/')){
                    $to_id =explode('=',$value['c_url'])[1];
                    $checked =M('Check_shopinfo')->where(array('c_id'=>$to_id))->getField('c_checked');
                    $data[$key]['c_flag'] =2;
                    $data[$key]['c_to_id'] =$to_id;
                    $data[$key]['c_checked'] =$checked;
                }
            }
        }

        $msg['code']=0;
        $msg['msg']='查询成功';
        $msg['data']=$data;
        $this->ajaxReturn($msg);
    }


    // 获取 所有区代公告信息
    public function GetAllNotice()
    {

        $key = I('openid');
        $indexPage =I('pageindex');
        $pageSize =10;
        if (empty($indexPage)) {
            $pageIndex = 1;
        } else {
            $pageIndex = $indexPage;
        }
        $countPage = ($pageIndex - 1) * $pageSize;
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $join = "left join t_check_infolog as b on a.c_id=b.c_infoid and b.c_ucode='$ucode'";
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $whereinfo[] = array("a.c_type=1 or a.c_type=2 or a.c_type=4");
        $order = 'case when ifnull(b.c_infoid,"")="" then 0 else 1 end asc,a.c_addtime desc';
        $field = 'a.*,b.c_infoid';
        $count = M('Check_info as a')->join($join)->where($whereinfo)->count();
        //$page = getpage($count,20);
        //$limit = $page->firstRow.','.$page->listRows;
        $data = M('Check_info as a')->join($join)->where($whereinfo)->order($order)->limit($countPage,$pageSize)->field($field)->select();
        foreach($data as $key=>$value){
            $data[$key]['c_addtime'] =substr($value['c_addtime'],0,10);
            if($value['c_content']!==null){
                $data[$key]['c_flag'] =1;
                $data[$key]['to_url'] =GetHost(1).'/index.php/Home/Info/affiche?Id='.$value['c_id'];
            }else{
                if(strstr($value['c_url'],'/Home/Agentntrol/')){
                    $data[$key]['c_flag'] =2;
                }elseif(strstr($value['c_url'],'/Home/Shopcheck/')){
                    $to_id =explode('=',$value['c_url'])[1];
                    $checked =M('Check_shopinfo')->where(array('c_id'=>$to_id))->getField('c_checked');
                    $data[$key]['c_flag'] =3;
                    $data[$key]['c_to_id'] =$to_id;
                    $data[$key]['c_checked'] =$checked;
                }
            }
        }
        $pageCount = ceil($count / $pageSize);

        $list = Page($pageIndex, $pageCount, $count, $data);
        $msg['code']=0;
        $msg['msg']='查询成功';
        $msg['data']=$list;
        $this->ajaxReturn($msg);
    }

    //读取公告未读消息

    public function GetNotRead(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode']=$ucode;
        $parr['cid'] =I('cid');
        $result =IGD('Infomation','Agent')->ReadMsg($parr);
        $this->ajaxReturn($result);
    }

    // 公告详情页
    public function GetDetail()
    {
        $where['c_id'] = I('Id');
        $vo = M('Check_info')->where($where)->find();
        $vo['c_addtime'] =substr($vo['c_addtime'],0,10);
        $msg['code']=0;
        $msg['msg']='查询成功';
        $msg['data']=$vo;
        $this->ajaxReturn($msg);
    }

    // 代理商管理
    public function AgentManager(){

        $key = I('openid');
        $indexPage =I('pageindex');
        $pageSize =10;
        if (empty($indexPage)) {
            $pageIndex = 1;
        } else {
            $pageIndex = $indexPage;
        }
        $countPage = ($pageIndex - 1) * $pageSize;
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $join = 'left join t_check_shopinfo as b on a.c_ucode=b.c_ucode';
        $where['a.c_acode'] = $ucode;
        $where['a.c_isagent'] = 2;
        $where[] = array("b.c_dcode !='' or b.c_dcode is not null ");
        $field = 'a.c_isagent,a.c_headimg,b.c_id,b.c_type,b.c_name,b.c_phone,b.c_checked';
        $order = 'b.c_checked=0,b.c_updatetime desc,b.c_addtime desc';
        $count = M('Users as a')->join($join)->where($where)->count();
        //$page = getpage($count,10);
        //$limit = $page->firstRow.','.$page->listRows;
        $data = M('Users as a')->join($join)->where($where)->order($order)->limit($countPage,$pageSize)->field($field)->select();
        foreach($data as $key=>$value){
            $data[$key]['c_headimg'] =$value['c_headimg']==null?null:GetHost().'/'.$value['c_headimg'];
        }
        $pageCount = ceil($count / $pageSize);

        $list = Page($pageIndex, $pageCount, $count, $data);
        $msg['code']=0;
        $msg['msg']='查询成功';
        $msg['data']=$list;
        $this->ajaxReturn($msg);

    }

    //代理商详情
    public function AgentDetail(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] =$ucode;
        $parr['infoid'] = I('Id');
        $result = IGD('Myagent','Agent')->GetAgentShopInfo($parr);
        $this->ajaxReturn($result);
    }


    //商家审核列表
    public function GetStoreShenList(){

        $key = I('openid');
        $type =I('type');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        //$ucode ="xms9ccbd1470ce51798";
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
            $field = 'a.c_isagent,a.c_headimg,b.c_merchantname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
            $order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
            $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
            foreach($data as $one=>$a){
                $data[$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
                $data[$one]['c_name']=$a['c_merchantname'];
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
                $field = 'a.c_isagent,a.c_headimg,b.c_merchantname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
                $order = 'case when ifnull(b.c_checked,"")="2" then 0 else 1 end asc,b.c_checked asc,b.c_updatetime desc,b.c_addtime desc';
                $data = M('Users as a')->join($join)->join($join1)->where($where)->order($order)->field($field)->select();
                foreach($data as $one=>$a){
                    $data[$one]['c_headimg']=$a['c_headimg']==null?null:GetHost().'/'.$a['c_headimg'];
                    $data[$one]['c_name']=$a['c_merchantname'];
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
        $field = 'a.c_isagent,a.c_headimg,b.c_merchantname,b.c_name,b.c_checked,b.c_id,b.c_type,c.c_isfixed';
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
            $aa['list'][$one]['c_name']=$a['c_merchantname'];
            //$aa['list'][$one]['c_merchantname']=$a['c_merchantname'];
            $aa['list'][$one]['c_checked']=$a['c_checked'];
            $aa['list'][$one]['c_id']=$a['c_id'];
            $aa['list'][$one]['c_type']=$a['c_type'];
            $aa['list'][$one]['c_isfixed']=$a['c_isfixed'];
        }
        return $aa;
    }

    //商家详情
    public function StoreDetail(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr1['ucode'] = $ucode;
        $parr['infoid'] = I('Id');
        $result = IGD('Myagent','Agent')->GetShopInfo($parr);

        $this->ajaxReturn($result);

    }

    //商家审核
    public function StoreShenhe(){

        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;

//        //查询是否有资料
//        $where['c_ucode'] = $ucode;
//        $angent = M('Check_shopinfo')->where($where)->find();
//
//        $msg['code']=1001;
//        if(empty($angent['c_dcode']) || $angent['c_checked'] !==3){
//            $msg['msg']='请先完善您的资料再来审核';
//            $this->ajaxReturn($msg);
//        }


        $result = IGD('Common','Agent')->GetStatuMessage($parr);
        if ($result['code'] != 0) {
            $this->ajaxReturn($result);
        }
        $parr['sid'] = I('sid');
        $parr['checked'] = I('checked');
        $parr['ucode'] = $ucode;
        $result = IGD('Myagent','Agent')->CheckShop($parr);
        $this->ajaxReturn($result);
    }

    //我的资料 第一步

    public function first(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['type'] =I('type');
        if(empty($parr['ucode']) || empty($parr['type'])){
            return Message(1001,'请完善参数');
        }
        $result =IGD('Myagent','Agent')->Fisrt($parr);

        $this->ajaxReturn($result);

    }
    //我的资料 第二步

    public function second(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['company'] =I('company');
        $parr['address'] =I('address');
        $parr['name'] =I('name');
        $parr['phone']=I('phone');
        $parr['email']=I('email');
        $parr['qq']=I('qq');
        $parr['idcard']=I('idcard');
        $parr['home_tel']=I('home_tel');
        $parr['postcode']=I('postcode');
        $parr['charter']=I('charter');
        if(empty($parr['ucode']) || empty( $parr['address'])|| empty( $parr['name'])|| empty( $parr['phone'])|| empty( $parr['email'])|| empty( $parr['qq'])|| empty( $parr['idcard'])|| empty( $parr['home_tel'])){
            return Message(1001,'请完善参数');
        }

        $result =IGD('Myagent','Agent')->Second($parr);

        $this->ajaxReturn($result);

    }
    //我的资料 第三步
    public function third(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $parr['fee_bank'] =I('fee_bank');
        $parr['fee_branch']=I('fee_branch');
        $parr['fee_cardnum']=I('fee_cardnum');
        $parr['fee_name']=I('fee_name');
        $parr['fee_alipay']=I('fee_alipay');
        $parr['fee_weixin']=I('fee_weixin');

        if(empty($parr['ucode']) || empty( $parr['fee_bank'])|| empty( $parr['fee_branch'])|| empty( $parr['fee_cardnum'])|| empty( $parr['fee_name'])|| empty( $parr['fee_alipay'])|| empty( $parr['fee_weixin'])){
            return Message(1001,'请完善参数');
        }

        $result =IGD('Myagent','Agent')->Third($parr);

        $this->ajaxReturn($result);
    }
    //我的资料 第四步
    public function fourth(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['ucode'] = $ucode;
        $result = IGD('Setinfo','Agent')->GetShopInfo($parr);
        $vodata = $result['data'];

        $result = uploadimg('agent');
        $imglist = array();
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
        }

        if($result['code'] == 1006){
            $imglist1 = objarray_to_array(json_decode(urldecode($_POST['imglist'])));
            $url = GetHost() . '/';
            foreach ($imglist1 as $key => $value) {
                $imglist[] = str_replace($url, "", $value['c_pimgepath']);
            }
        }
        $parr['idcard_img'] = $imglist[0];
        $parr['idcard_img1'] = $imglist[1];

        if (empty($parr['idcard_img']) || empty($parr['idcard_img1'])) {
            $this->ajaxReturn(Message(3001,'请上传身份证图'));
        }

        if ($vodata['c_type'] == 2) {
            $parr['charter_img'] = $imglist[2];
            $parr['company_sign'] = $imglist[3];
            if (empty($parr['charter_img']) || empty($parr['company_sign'])) {
                $this->ajaxReturn(Message(3002,'线下商家请上传营业执照与企业标识图'));
            }
        }

        $result =IGD('Myagent','Agent')->Fourth($parr);

        $this->ajaxReturn($result);

    }

    //资料列表
    public function dataList(){
        $result = IGD('Infomation','Agent')->GetMarialList();
        $this->ajaxReturn($result);
    }

    //资料下载

    public function download(){
        $parr['Id'] = I('Id');
        $result =  IGD('Infomation','Agent')->downfile($parr);
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



    // 改变消息状态
    public function readinfo()
    {
        $parr['ucode'] = session('_AGENT_UCODE');
        $parr['cid'] = I('Id');
        $result = D('Infomation','Service')->ReadMsg($parr);
        $this->ajaxReturn($result);
    }


    /*完善资料提醒*/
    public function ReadInfostatu()
    {
        $parr['ucode'] = session('_AGENT_UCODE');
        $result = D('Common','Service')->ReadInfostatu($parr);
        $this->ajaxReturn($result);
    }

    /*获取首页未读状态标志*/
    public function GetStatuMessage()
    {
        $parr['ucode'] = session('_AGENT_UCODE');
        $result = D('Common','Service')->GetStatuMessage($parr);
        $this->ajaxReturn($result);
        // $publicnum = $result['data']['publicnum'];
        // $checknum = $result['data']['checknum'];
    }
}