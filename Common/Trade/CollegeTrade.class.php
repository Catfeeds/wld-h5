<?php

/**
 *  微商学院管理中心
 *
 */
class CollegeTrade {
    /**
     *  获取商学院信息列表
     *  @param  pageindex,pagesize
     */
    public function GetCollegeList($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $where['c_isshow'] = 1;

        if (!empty($parr['keyword'])) {
            $where['c_title'] = array('like','%'.$parr['keyword'].'%');
        }
        $field = 'c_id,c_title,c_type,c_desc,c_img,c_url,c_addtime,c_click';
        $list = M('Business_college')->where($where)->order('c_addtime desc')->limit($countPage, $pageSize)->field($field)->select();
        foreach ($list as $key => $value) {
            // $imgarr = explode('|',$value['c_img']);
            // for ($i=0; $i < count($imgarr); $i++) {
            //     $imgarr[$i] = GetHost().'/'.$imgarr[$i];
            // }
            $sourcewhere['c_sourceid'] = 1;
            $sourcewhere['c_regionid'] = $value['c_id'];
            $list[$key]['c_img'] = M('Resource_img')->where($sourcewhere)->select();
            foreach ($list[$key]['c_img'] as $k => $v) {
                $list[$key]['c_img'][$k]['c_img'] = GetHost().'/'.$v['c_img'];
                $list[$key]['c_img'][$k]['c_thumbnail_img'] = GetHost().'/'.$v['c_img'];
            }
            $time = time() - strtotime($value['c_addtime']);
            $list[$key]['show_time'] = $this->GetShowTime($time);
            $list[$key]['comcount'] = M('Business_appraise')->where('c_collegeid="'.$value['c_id'].'"')->count();
        }

        $count = M('Business_college')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, "查询成功",$data);
    }

    /**
     *  获取商学院信息详情
     *  @param cid,ucode
     */
    public function GetCollegeDetail($parr)
    {
        // if (empty($parr['ucode'])) {
        //     return Message(1009, "请先登录");
        // }

        $where['c_id'] = $parr['cid'];
        $where['c_isshow'] = 1;
        $data = M('Business_college')->where($where)->find();
        if (!$data) {
            return Message(1000, "查询失败");
        }
        // $imgarr = explode('|',$data['c_img']);
        // for ($i=0; $i < count($imgarr); $i++) {
        //     $imgarr[$i] = GetHost().'/'.$imgarr[$i];
        // }
        $sourcewhere['c_sourceid'] = 1;
        $sourcewhere['c_regionid'] = $data['c_id'];
        $data['c_img'] = M('Resource_img')->where($sourcewhere)->select();
        foreach ($data['c_img'] as $k => $v) {
            $data['c_img'][$k]['c_img'] = GetHost().'/'.$v['c_img'];
            $data['c_img'][$k]['c_thumbnail_img'] = GetHost().'/'.$v['c_img'];
        }
        $this->CollegeClick($parr);
        return MessageInfo(0, "查询成功",$data);
    }

     /**
     *  获取商学院信息评论
     *  @param cid,pageindex,pagesize,lastptid
     */
    public function GetCommentlist($parr)
    {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $join = 'left join t_users as c on b.c_ucode=c.c_ucode';
        $field = 'b.c_id,b.c_bid,b.c_ptid,b.c_content,b.c_addtime,c.c_ucode,c.c_headimg,c.c_nickname';
        $commentwhere['b.c_collegeid'] = $parr['cid'];
        $commentwhere['b.c_state'] = 1;
        $order = 'b.c_ptid asc,b.c_addtime asc';
        $comment = M('Business_appraise as b')->join($join)->where($commentwhere)->order($order)->limit($countPage, $pageSize)->field($field)->select();
        foreach ($comment as $key => $value) {
            $comment[$key]['c_headimg'] = GetHost().'/'.$value['c_headimg'];
        }
        if ($comment) {
            $comment = $this->toLayer($comment,'child',0,$parr['lastptid']);
        }

        $count = M('Business_appraise as b')->join($join)->where($commentwhere)->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $comment);

        return MessageInfo(0, "查询成功",$data);
    }

    //组成多维数组
    public function toLayer($cate, $name = 'child', $pid = 0,$lastptid){
        $arr = array();
        foreach ($cate as $v) {
            if ($v['c_bid'] == $pid) {
                $v[$name] = self::toLayer($cate, $name, $v['c_id']);
                $arr[] = $v;
            }

            if ($v['c_ptid'] == $lastptid && $v['c_bid'] != $pid) {
                $v[$name] = array();
                $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
                $rewhere['a.c_id'] = $v['c_bid'];
                $v['c_replayname'] = M('Business_appraise as a')->join($join)->where($rewhere)->getField('b.c_nickname');
                $arr[] = $v;
            }
        }
        return $arr;
    }

    /**
     *  增加浏览次数
     *  @param cid,ucode
     */
    public function CollegeClick($parr)
    {
        $where['c_id'] = $parr['cid'];
        $result = M('Business_college')->where($where)->setInc('c_click','1');
        if (!$result) {
            return Message(1000, "查看失败");
        }
        return Message(0, "查看成功");
    }

    /**
     *  评论信息
     *  @param content,collegeid,ucode,bid,ptid
     */
    public function CommentCollege($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }
        $data['c_content'] = $parr['content'];
        $data['c_collegeid'] = $parr['collegeid'];
        $data['c_ucode'] = $parr['ucode'];
        if ($parr['bid'] != '') {
            $data['c_bid'] = $parr['bid'];
        } else {
            $data['c_bid'] = 0;
        }

        if (!empty($parr['ptid'])) {
            $data['c_ptid'] = $parr['ptid'];
        }
        $data['c_addtime'] = date('Y-m-d H:i:s');
        $db = M('');
        $db->startTrans();
        $result = M('Business_appraise')->add($data);
        if (!$result) {
            $db->rollback();
            return Message(1003,'评论失败');
        }

        $id = $result;
        if (empty($parr['ptid'])) {
            $save['c_ptid'] = $result;
            $where['c_id'] = $result;
            $result = M('Business_appraise')->where($where)->save($save);
            if (!$result) {
                $db->rollback();
                return Message(1003,'评论失败');
            }
            $data['c_ptid'] = $save['c_ptid'];
        }

        $db->commit();

        $userwhere['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($userwhere)->field('c_headimg,c_nickname')->find();
        $data['c_headimg'] = GetHost().'/'.$userinfo['c_headimg'];
        $data['c_nickname'] = $userinfo['c_nickname'];
        $join = 'left join t_users as b on a.c_ucode=b.c_ucode';
        $rewhere['a.c_id'] = $parr['bid'];
        $data['c_replayname'] = M('Business_appraise as a')->join($join)->where($rewhere)->getField('b.c_nickname');

        $data['c_id'] = $id;
        return MessageInfo(0,'评论成功',$data);
    }

    /**
     *  删除评论及子评论
     *  @param ucode,cid
     */
    public function DeleteComment($parr)
    {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $where['c_id'] = $parr['cid'];
        $cid = M('Business_appraise')->where($where)->getField('c_ptid');
        if ($cid == $parr['cid']) {
            $where1['c_ptid'] = $cid;
            $result = M('Business_appraise')->where($where1)->delete();
        } else {
            $result = M('Business_appraise')->where($where)->delete();
        }

        if (!$result) {
            return Message(1003,'删除失败');
        }

        return Message(0,'删除成功');
    }

    //获取前台显示时间
    public function GetShowTime($time) {
        $year = floor($time / 60 / 60 / 24 / 365);
        $time -= $year * 60 * 60 * 24 * 365;
        $month = floor($time / 60 / 60 / 24 / 30);
        $time -= $month * 60 * 60 * 24 * 30;
        $week = floor($time / 60 / 60 / 24 / 7);
        $time -= $week * 60 * 60 * 24 * 7;
        $day = floor($time / 60 / 60 / 24);
        $time -= $day * 60 * 60 * 24;
        $hour = floor($time / 60 / 60);
        $time -= $hour * 60 * 60;
        $minute = floor($time / 60);
        $time -= $minute * 60;
        $second = $time;
        $elapse = '';

        $unitArr = array('年' => 'year', '个月' => 'month', '周' => 'week', '天' => 'day',
            '小时' => 'hour', '分钟' => 'minute', '秒' => 'second'
        );

        foreach ($unitArr as $cn => $u) {
            if ($$u > 0) {
                $elapse = $$u . $cn;
                break;
            }
        }
        return $elapse.'前';
    }

}
