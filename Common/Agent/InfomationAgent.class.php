<?php


class InfomationAgent {

    /**
     *  写入公告信息
     *  @param ucode,ptitle,title,origin,content
     */
    function Create_information($parr)
    {
        $data['c_ucode'] = $parr['ucode'];
        $data['c_ptitle'] = $parr['ptitle'];
        $data['c_title'] = $parr['title'];
        $data['c_origin'] = $parr['origin'];
        $data['c_content'] = $parr['content'];
        $data['c_url'] = $parr['url'];
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_info')->add($data);
        if (!$result) {
            return Message(1001,'创建失败');
        }
        return Message(0,'创建成功');
    }

    /**
     * 读取未读消息
     * @param ucode,cid
     */
    function ReadMsg($parr)
    {
        $add['c_ucode'] = $parr['ucode'];
        $add['c_infoid'] = $parr['cid'];
        $result = M('Check_infolog')->where($add)->getField('c_id');
        if ($result) {
            return Message(0, '消息已读');
        }
        $add['c_addtime'] = date('Y-m-d H:i:s');
        $result = M('Check_infolog')->add($add);
        if (!$result) {
            return Message(1000, '读取失败');
        }
        return Message(0, '读取成功');
    }

    /**
     * 消息公告列表
     * @param ucode,pageindex,pagesize
     */
    function GetMsgList($parr)
    {

        $ucode = $parr['ucode'];

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_check_infolog as b on b.c_infoid=a.c_id';
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $order = 'case when ifnull(b.c_infoid,"")="" then 0 else 1 end asc,a.c_addtime desc';
        $field = 'a.*,b.c_infoid';
        $list = M('Check_info as a')->join($join)->where($whereinfo)->order($order)->limit($countPage, $pageSize)->field($field)->select();

        $count = M('Check_info as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    // 获取资料列表
    function GetMarialList()
    {
        $where['c_pid'] = 0;
        $data = M('Check_datum')->where($where)->order('c_addtime desc')->field('c_name,c_id')->select();
        foreach ($data as $key => $value) {
            $pwhere['c_pid'] = $value['c_id'];
              $data[$key]['chidren'] = M('Check_datum')->where($pwhere)->order('c_addtime desc')->select();
            if(empty($data[$key]['chidren'])){
                $data[$key]['chidren']=[];
            }
            //$list = M('Check_datum')->where($pwhere)->order('c_addtime desc')->select();
            foreach($data[$key]['chidren'] as $v =>  $val){
                $a =explode(".",$val['c_filepath']);
                $data[$key]['chidren'][$v]['type'] =$a[count($a)-1];
                $data[$key]['chidren'][$v]['c_id'] =$val['c_id'];
                $data[$key]['chidren'][$v]['c_pid'] =$val['c_pid'];
                $data[$key]['chidren'][$v]['c_name'] =$val['c_name'];
                $data[$key]['chidren'][$v]['c_filepath'] =GetHost() . '/' .$val['c_filepath'];
                $data[$key]['chidren'][$v]['c_downnum'] =$val['c_downnum'];
                $data[$key]['chidren'][$v]['c_addtime'] =substr($val['c_addtime'],0,10);
                $data[$key]['chidren'][$v]['share_title']=$val['c_name'];
                $data[$key]['chidren'][$v]['share_des']='请在PC端点击进行资料下载';
                $data[$key]['chidren'][$v]['share_img']='https://m.weilingdi.com/Resource/Common/logo.png';
                $data[$key]['chidren'][$v]['share_url']=GetHost().'/'.$val['c_filepath'];
            }
        }
        return MessageInfo(0, '查询成功', $data);
    }


    //文件分享下载
    function shareDownload($parr){
        if(empty($parr['Id'])){
            return Message(1001,'参数缺失');
        }
        $info = M('Check_datum')->where(array('c_id'=>$parr['Id']))->find();

        $share['share_title']=$info['c_name'];
        $share['share_des']='请在PC端点击进行资料下载';
        $share['share_img']='https://m.weilingdi.com/Resource/Common/logo.png';
        $share['share_url']=GetHost().'/'.$info['c_filepath'];
        return MessageInfo(0, '查询成功', $share);
    }

    // 查询下载文件
    function downfile($parr)
    {
        $where['c_id'] = $parr['Id'];
        M('Check_datum')->where($where)->setInc('c_downnum',1);
        $data = M('Check_datum')->where($where)->find();
        if (!$data) {
            return Message(404,'没有对应的文件');
        }

        $data['c_filepath'] = GetHost().'/'.$data['c_filepath'];
        return MessageInfo(0, '查询成功', $data);
    }


    /**
     * 提交信息反馈
     * @param ucode,content,img1,img2,img3     
     */
    function PutMessages($parr){
        if(empty($parr['ucode'])){
            return Message(1001,'参数缺失');
        }
        if(trim($parr['content'])==''){
            return Message(1001,'反馈内容不能为空');
        }
        $data['c_ucode'] =$parr['ucode'];
        $data['c_content'] =$parr['content'];
        $data['c_img1'] =$parr['img1'];
        $data['c_img2'] =$parr['img2'];
        $data['c_img3'] =$parr['img3'];
        $data['c_addtime'] =date('Y-m-d H:i:s');


        $where['c_ucode']=$parr['ucode'];
        $where['c_addtime'] =array('egt',date('Y-m-d').' 00:00:00');

        $find =M('Users_messages')->where($where)->find();
        if($find && !empty($find)){
            return Message(1001,'每人每天限制反馈一个建议');
        }
        $result =M('Users_messages')->add($data);
        if(!$result){
            return Message(1001,'保存数据失败');
        }

        return Message(0,'提交成功');


    }

    /**
     * 回复反馈信息
     * @param ucode，Id,content
    */

    function Reply($parr){
        if(empty($parr['ucode']) || empty($parr['Id'])){
            return Message(1001,'参数缺失');
        }
        if(trim($parr['content'])==''){
            return Message(1001,'回复内容不能为空');
        }
        $data['c_to_id'] =$parr['Id'];
        $data['c_ucode'] =$parr['ucode'];
        $data['c_to_ucode'] =M('Users_messages')->where(array('c_id'=>$parr['Id']))->getField('c_ucode');
        $data['c_content'] =$parr['content'];
        $data['c_addtime'] =date('Y-m-d H:i:s');
        $result =M('Reply_messages')->add($data);

        if(!$result){
            return Message(1001,'回复失败');
        }else{
            M('Users_messages')->where(array('c_id'=>$parr['Id']))->setInc('c_flag');
        }

        return Message(0,'回复成功');

    }

    /**
     * 反馈信息列表
     * @param ucode,pageindex,type
    */
    function GetMessagesList($parr){
        if(empty($parr['ucode']) || empty($parr['type'])){
            return Message(1001,'参数缺失');
        }
        $indexPage =$parr['pageindex'];
        $ucode =$parr['ucode'];
        $flag =$parr['flag'];
        switch($parr['type']){
            case 1:
                $data =$this->getData($ucode,$flag,$indexPage,1);
                break;
            case 2:
                $data =$this->getData($ucode,$flag,$indexPage,2);
                break;
        }

        return MessageInfo(0,'查询成功',$data);

    }

    //  获取数据


    //  获取数据

    function getData($ucode,$flag,$index,$type){
        $pageSize =10;
        if (empty($indexPage)) {
            $pageIndex = 1;
        } else {
            $pageIndex = $index;
        }
        $countPage = ($pageIndex - 1) * $pageSize;
        if($flag==1){
            $where['c_ucode']=$ucode;
        }
        if($type==1){
            $where['c_flag']=0;
        }elseif($type==2){
            $where['c_flag'] =array('gt',0);
        }
        $count =M('Users_messages')->where($where)->count();
        $info =M('Users_messages')->order('c_addtime desc')->where($where)->limit($countPage,$pageSize)->field('c_id,c_content,c_img1,c_img2,c_img3,c_addtime')->select();
        foreach($info as $key=>$v){
            $list[$key]['c_id'] =$v['c_id'];
            $list[$key]['c_content'] =$v['c_content']==null?'':$v['c_content'];
            $list[$key]['c_addtime'] =$v['c_addtime'];
            $img1 = $v['c_img1']==null?null:GetHost().'/'.$v['c_img1'];
            $img2 = $v['c_img2']==null?null:GetHost().'/'.$v['c_img2'];
            $img3 = $v['c_img3']==null?null:GetHost().'/'.$v['c_img3'];
            if($v['c_img1']==null && $v['c_img2']==null && $v['c_img3']==null){
                $list[$key]['imgList'] =[];
            }elseif($v['c_img1']!==null && $v['c_img2']==null && $v['c_img3']==null){
                $list[$key]['imgList'] =[$img1];
            }elseif($v['c_img1']!==null && $v['c_img2']!==null && $v['c_img3']==null){
                $list[$key]['imgList'] =[$img1,$img2];
            }else{
                $list[$key]['imgList'] =[$img1,$img2,$img3];
            }

            $list[$key]['reply'] =M('Reply_messages')->order('c_addtime desc')->where(array('c_to_id'=>$v['c_id']))->field('c_id,c_content,c_addtime')->select();
            foreach ($list[$key]['reply'] as $k1 => $v1) {
                $list[$key]['reply'][$k1]['c_content'] =$v1['c_content']==null?'':$v1['c_content'];
            }
        }
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);

        return $data;

    }

    /**
     * 获取反馈详情
     * @param ucode，Id
    */
    function MsgDetail($parr){
        if(empty($parr['ucode']) || empty($parr['Id'])){
            return Message(1001,'参数缺失');
        }
        $where['c_id'] =$parr['Id'];
        $data =M('Users_messages')->where($where)->field('c_id,c_content,c_img1,c_img2,c_img3,c_addtime')->find();
        $info['c_id'] =$data['c_id'];
        $info['c_content'] =$data['c_content'];
        $img1 = $data['c_img1']==null?null:GetHost().'/'.$data['c_img1'];
        $img2 = $data['c_img2']==null?null:GetHost().'/'.$data['c_img2'];
        $img3 = $data['c_img3']==null?null:GetHost().'/'.$data['c_img3'];
        if($data['c_img1']==null && $data['c_img2']==null && $data['c_img3']==null){
            $info['imgList'] =[];
        }elseif($data['c_img1']!==null &&$data['c_img2']==null && $data['c_img3']==null){
            $info['imgList'] =[$img1];
        }elseif($data['c_img1']!==null && $data['c_img2']!==null && $data['c_img3']==null){
            $info['imgList'] =[$img1,$img2];
        }else{
            $info['imgList'] =[$img1,$img2,$img3];
        }
        $info['reply'] =M('Reply_messages')->order("c_addtime desc")->where(array('c_to_id'=>$parr['Id']))->field('c_content,c_addtime')->select();
        $info['c_addtime'] =$data['c_addtime'];

        return MessageInfo(0,'查询成功',$info);
    }

}
