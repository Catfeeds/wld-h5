<?php
/**
 * 	商家评论管理专区模块
 *
 */
class ProductscoreStore {
    /**
	 *  获取商家有关评论列表
	 *  @param ucode,acode,useraction(0-全部评论、1-店铺评论、2-商品评论)
	 *
	 */
    public function GetAllScore($parr) {
        if($parr['acode']){
            $whereinfo['a.c_acode'] = $parr['acode'];
        }else{
            $whereinfo['a.c_acode'] = $parr['ucode'];
        }

    	if($parr['useraction'] == 1){
    		$whereinfo['a.c_object'] = 2;
    	}else if($parr['useraction'] == 2){
    		$whereinfo['a.c_object'] = 1;
    	}

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $field = 'a.*,b.c_nickname,b.c_headimg';
        $order = 'a.c_addtime desc';
        $list = M('product_score as a')->join($join)->where($whereinfo)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('product_score as a')->join($join)->where($whereinfo)->count();
        $pageCount = ceil($count / $pageSize);

        if (!$list) {
        	$list = array();
        	$data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $row) {
            $list[$key]['c_addtime1'] = date('Y-m-d H:i', strtotime($row['c_addtime']));//修改评论时间

            $list[$key]['c_pimg'] = GetHost() . '/' . $row['c_pimg'];

            $list[$key]['c_headimg'] = GetHost() . '/' . $row['c_headimg'];

            $list[$key]['c_like'] = $this->Toobig($row['c_like']);

            $list[$key]['c_nolike'] = $this->Toobig($row['c_nolike']);
            
            $list[$key]['imglist'] = $this->get_imglist($row['c_id']);//查询评论图片

            $list[$key]['comment_num'] = $this->Toobig($this->get_comment_num($row['c_id']));//评论数量

            $list[$key]['comment_list'] = $this->get_commentlist($row['c_id'], 0);//评论列表

            $list[$key]['is_delete'] = $this->is_delete($parr['ucode'], $row['c_ucode']);//是否显示删除

            $list[$key]['is_like'] = $this->is_like($row['c_id'], $parr['ucode'], 0);//是否点赞

            //商品跳转标识
            $list[$key]['source'] = '';
            if($row['c_pcode']){
                $w['c_pcode'] = $row['c_pcode'];
                $pinfo = M('Product')->where($w)->field('c_isagent,c_source')->find();

                if($pinfo['c_isagent'] == 2){
                    $list[$key]['source'] = 3;
                }else{
                    if($pinfo['c_source'] == 1){
                        $list[$key]['source'] = 1;
                    }else if($pinfo['c_source'] == 2){
                        $list[$key]['source'] = 2;
                    }
                }
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

     /**
     *  获取评价详细信息
     *  @param ucode,sid 评价id
     */
    function GetScoreInfo($parr) {
        $db = M('product_score as a');

        $w['a.c_id'] = $parr['sid'];

        $field = "a.*,u.c_nickname,u.c_headimg";
        $join = "left join t_users as u on a.c_ucode=u.c_ucode";
        $rt = $db->field($field)->join($join)->where($w)->find();

        if (count($rt) == 0) {
        	$rt = array();
            return MessageInfo(0, '查询成功', $rt);
        }

        $rt['c_addtime1'] = date('Y-m-d H:i', strtotime($rt['c_addtime']));//修改评论时间

		$rt['c_pimg'] = GetHost() . '/' . $rt['c_pimg'];

		$rt['c_headimg'] = GetHost() . '/' . $rt['c_headimg'];

		$rt['c_like'] = $this->Toobig($rt['c_like']);

		$rt['c_nolike'] = $this->Toobig($rt['c_nolike']);

		$rt['imglist'] = $this->get_imglist($rt['c_id']);//查询评论图片

		$rt['comment_num'] = $this->Toobig($this->get_comment_num($rt['c_id']));//评论数量

		$rt['comment_list'] = $this->get_commentlist($rt['c_id'], 1);//评论列表

		$rt['is_delete'] = $this->is_delete($parr['ucode'], $rt['c_ucode']);//是否显示删除

		$rt['is_like'] = $this->is_like($rt['c_id'], $parr['ucode'], 0);//是否点赞

        $rt['source'] = 0;
		if($rt['c_pcode']){
            $w1['c_pcode'] = $rt['c_pcode'];
            $pinfo = M('Product')->where($w1)->field('c_isagent,c_source')->find();

            if($pinfo['c_isagent'] == 2){
                $rt['source'] = 3;
            }else{
                if($pinfo['c_source'] == 1){
                    $rt['source'] = 1;
                }else if($pinfo['c_source'] == 2){
                    $rt['source'] = 2;
                }
            }
        }
        return MessageInfo(0, "查询成功", $rt);
    }

    //数量太大，返回k.w
    public function Toobig($num) {
        if (intval($num) > 1000) {
        	$b = sprintf('%.2f',$num/1000);
        	$num = $b.'k';
        }else if(intval($num) > 10000){
        	$b = sprintf('%.2f',$num/10000);
        	$num = $b.'w';
        }

        return $num;
    }

    //获取评价图片列表
    public function get_imglist($id) {
    	$w['c_regionid'] = $id;
    	$w['c_sourceid'] = 3;
    	$data = M('Resource_img')->field('c_img,c_thumbnail_img')->where($w)->select();

    	if (count($data) == 0) {
            $data = array();
        }

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            $data[$k]['c_thumbnail_img'] = GetHost() . '/' . $v['c_img'];
        }

        return $data;
    }

    //获取评论数量
    public function get_comment_num($id) {
    	$w['c_scoreid'] = $id;
    	$w['c_state'] = 1;
    	$w['c_bid'] = 0;

    	$data = M('Product_score_comment')->where($w)->count();
        
        return $data;
    }

    //获取资源评论列表
    public function get_commentlist($id, $flag) {
        $db = M('Product_score_comment as s');

        $join = 'left join t_users as u on s.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on s.c_upucode=pu.c_ucode';
        $field = 's.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';

        $commentwhere['s.c_scoreid'] = $id;
        $commentwhere['s.c_state'] = 1;

        $order = 's.c_id desc';
        if ($flag == 0) {
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->limit(3)->field($field)->select();
        } else {
            $comment = $db->join($join)->join($join1)->where($commentwhere)->order($order)->field($field)->select();
        }

        if ($comment) {
            $comment = $this->packedData($comment);
        }
        if (count($comment) == 0) {
            $comment = array();
        }
        return $comment;
    }

    //组成评论显示数组
    public function packedData($data) {
        $arr = array();
        foreach ($data as $k => $v) {
            $arr[$k]['c_id'] = $v['c_id'];
            $arr[$k]['c_scoreid'] = $v['c_scoreid'];

            $arr[$k]['c_ucode'] = $v['c_ucode'];
            $arr[$k]['c_nickname'] = $v['c_nickname'];
            $arr[$k]['c_headimg'] = GetHost() . '/' . $v['c_headimg'];

            if (!empty($v['upucode'])) {
                $arr[$k]['upucode'] = $this->IsNull($v['upucode']);
                $arr[$k]['upheadimg'] = $this->IsNull($v['upheadimg'], 1);
                $arr[$k]['upnickname'] = $this->IsNull($v['upnickname']);
            }

            $arr[$k]['c_content'] = $v['c_content'];
            $arr[$k]['c_addtime'] = $v['c_addtime'];
            // $arr[$k]['switch_addtime'] = $this->GetShowTime($v['c_addtime'], 0);
        }
        return $arr;
    }

     //判断返回字符串，为空返回‘’
    public function IsNull($str, $sign) {
        if ($sign == 1) {
            if (empty($str)) {
                $str = '';
            } else {
                $str = GetHost() . '/' . $str;
            }
        } else {
            if (empty($str)) {
                $str = '';
            }
        }
        return $str;
    }

    //用户是否显示删除按钮
    public function is_delete($ucode, $pucode) {
        if ($ucode == $pucode) {
            $flag = 1;
        } else {
            $flag = 0;
        }
        return $flag;
    }

    //用户是否点赞
    public function is_like($scoreid, $ucode, $type) {
        if (empty($ucode)) {
            return 0;
        }

        $w['c_ucode'] = $ucode;
        $w['c_scoreid'] = $scoreid;

        if($type == 0){
        	$w['c_type'] = $type;
        }

        $count = M('Product_score_like')->where($w)->count();

        if ($count == 0) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        return $flag;
    }

    /**
     *  点赞、点不赞
     *  @param handle 0-点赞，1-点不赞,ucode点赞用户编号，scoreid
     */
    public function ScoreLike($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录！");
        }

        $w['c_scoreid'] = $parr['scoreid'];
        $w['c_ucode'] = $parr['ucode'];

        $result = M('Product_score_like')->where($w)->find();

        if($result){
        	return Message(1001,"重复操作");
        }

        $add['c_ucode'] = $parr['ucode'];
        $add['c_scoreid'] = $parr['scoreid'];
        $add['c_type'] = $parr['handle'];
        $add['c_addtime'] = gdtime();

        $db = M('');
        $db->startTrans();

        $result = M('Product_score_like')->add($add);

        if(!$result){
        	$db -> rollback();
        	return Message(1002,"操作失败");
        }

        $where['c_id'] = $parr['scoreid'];

        if($parr['handle'] == 0){
        	$result = M('Product_score')->where($where)->setInc('c_like', '1');
        }else{
        	$result = M('Product_score')->where($where)->setInc('c_nolike', '1');
        }
        
        if (!$result) {
            $db->rollback();
            return Message(1012, "增加统计量失败！");
        }

        $db -> commit();
        return Message(0,"操作成功");
    }

    /**
     *  评论信息
     *  @param content,scoreid,ucode,bid
     */
    public function CommentScore($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $w['c_id'] = $parr['scoreid'];
        $count = M('Product_score')->where($w)->count();
        if ($count == 0) {
            return Message(1000, '评论失败,主评论不存在');
        }

        $data['c_content'] = $parr['content'];
        $data['c_scoreid'] = $parr['scoreid'];
        $data['c_ucode'] = $parr['ucode'];
        $upucode = '';
        if ($parr['bid'] != '' && $parr['bid'] != null && $parr['bid'] != 0) {
            $data['c_bid'] = $parr['bid'];

            $w1['c_id'] = $parr['bid'];
            $comment_uinfo = M('Product_score_comment')->where($w1)->field('c_ptid,c_ucode')->find();
            
            $ptid = $comment_uinfo['c_ptid'];
            $upucode = $comment_uinfo['c_ucode'];

            $data['c_upucode'] = $upucode;

            if (!empty($ptid)) {
                $data['c_ptid'] = $ptid;
            } else {
                return Message(1001, '评论失败,该评论已经被删除');
            }
        } else {
            $data['c_bid'] = 0;
        }
        $data['c_addtime'] = date('Y-m-d H:i:s');

        $db = M('');
        $db->startTrans();
        $result = M('Product_score_comment')->add($data);

        if (!$result) {
            $db->rollback();
            return Message(1002, '评论失败');
        }

        if ($parr['bid'] == '') {
            $ptid = $result;
        }

        $id = $result;

        if (empty($data['c_ptid'])) {
            $save['c_ptid'] = $ptid;
            $where['c_id'] = $id;
            $result = M('Product_score_comment')->where($where)->save($save);
            if ($result <= 0) {
                $db->rollback();
                return Message(1003, '评论失败');
            }
        }

        $db->commit();

        //返回评论数据
        $join = 'left join t_users as u on a.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on a.c_upucode=pu.c_ucode';
        $field = 'a.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';
        $commentwhere['a.c_id'] = $id;
        $comment = M('Product_score_comment as a')->join($join)->join($join1)->where($commentwhere)->field($field)->select();

        if ($comment) {
            $comment = $this->packedData($comment);
        }

        return MessageInfo(0, '评论成功', $comment);
    }

    /**
     *  删除评论及子评论
     *  @param ucode,cid
     */
    public function DeleteComment($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $where['c_id'] = $parr['cid'];
        $comment_info = M('Product_score_comment')->field('c_scoreid,c_ucode')->where($where)->find();

        if ($parr['ucode'] != $comment_info['c_ucode']) {
            return Message(1010, "您没有权限删除");
        }

        $cid = M('Product_score_comment')->where($where)->getField('c_ptid');
        if ($cid == $parr['cid']) {
            $where2['c_ptid'] = $cid;
            $result = M('Product_score_comment')->where($where2)->delete();
        } else {
            $result = M('Product_score_comment')->where($where)->delete();
        }

        if (!$result) {
            return Message(1003, '删除失败');
        }

        return Message(0, '删除成功');
    }
}