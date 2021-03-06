<?php

// 微资源 2.0版本

class Resourcev2Trade {

    /**
     *  获取资源列表
     *  @param ucode 用户编码,issue_ucode 发表者用户编码
     */
    function GetResourceList($parr) {
        $db = M('Resource as r');

        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $w['r.c_status'] = 1;
        $sign = 0;
        if (!empty($parr['issue_ucode'])) {
            $w['r.c_ucode'] = $parr['issue_ucode'];
            $sign = 1;
        }
        if (!empty($parr['condition'])) {
            $condition = $parr['condition'];
            $w[] = array("r.c_content like '%" . $condition . "%' or u.c_nickname like '%" . $condition . "%' or u.c_shopnum='" . $condition . "'");
        }

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop";
        $join = "inner join t_users as u on r.c_ucode=u.c_ucode";
        $rt = $db->field($field)->join($join)->where($w)->order('r.c_istop desc,r.c_id desc')->limit($countPage, $pageSize)->select();

        $count = M('Resource as r')->join($join)->where($w)->count();
        $pageCount = ceil($count / $pageSize);

        if (count($rt) == 0) {
            $rt = array();
            $data = Page($pageIndex, $pageCount, $count, $rt);
            return MessageInfo(0, '查询成功', $data);
        }

        $resourcelist = array();

        if (!empty($rt)) {
            foreach ($rt as $row) {
                if (empty($row['c_id']))
                    continue;

                $jumptype = 0;
                if ($row['c_shop'] == 1) {
                    //判断用户跳转
                    $whereinfo['c_ucode'] = $row['c_ucode'];
                    $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                    if ($c_isfixed == 1) {
                        $jumptype = 2;
                    } else {
                        $jumptype = 1;
                    }
                }

                $resourcelist[] = array(
                    'c_id' => $row['c_id'],
                    'c_ucode' => $row['c_ucode'],
                    'c_nickname' => $row['c_nickname'],
                    'c_headimg' => GetHost() . '/' . $row['c_headimg'],
                    'c_content' => subtext($row['c_content'], 86),
                    'c_comment' => $row['c_comment'],
                    'c_click' => $row['c_click'],
                    'c_like' => $this->Toobig($row['c_like']),
                    'c_address' => $this->IsNull($row['c_address']),
                    'c_istop' => $row['c_istop'],
                    'jumptype' => $jumptype,
                    'c_addtime' => $row['c_addtime'],
                    'switch_addtime' => $this->GetShowTime($row['c_addtime'], $sign),
                    'switch_addtime1' => $this->GetShowTime($row['c_addtime'], 0),
                    //资源图片列表
                    'imglist' => $this->get_imglist($row['c_id']),
                    //推荐商品列表
                    'tj_product' => $this->get_product($row['c_id']),
                    //评论列表
                    'comment_list' => $this->get_commentlist($row['c_id'], 0),
                    //用户是否关注资源发布者
                    'is_attention' => $this->is_attention($parr['ucode'], $row['c_ucode']),
                    //是否显示删除
                    'is_delete' => $this->is_delete($parr['ucode'], $row['c_ucode']),
                    //是否点赞
                    'is_like' => $this->is_like($row['c_id'], $parr['ucode']),
                );
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $resourcelist);
        return MessageInfo(0, "查询成功", $data);
    }

    //数量太大，返回999
    public function Toobig($num) {
        if (intval($num) > 999) {
            $num = 999;
        }

        return $num;
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

    /**
     *  获取资源详细信息
     *  @param sid 资源id
     */
    function GetResourceInfo($parr) {
        $db = M('Resource as r');

        $w['r.c_status'] = 1;
        $w['r.c_id'] = $parr['sid'];

        $field = "r.*,u.c_nickname,u.c_headimg,u.c_shop";
        $join = "left join t_users as u on r.c_ucode=u.c_ucode";
        $rt = $db->field($field)->join($join)->where($w)->find();

        if (count($rt) == 0) {
            return MessageInfo(0, '查询成功', $data);
        }

        $jumptype = 0;
        if ($rt['c_shop'] == 1) {
            //判断用户状态
            $whereinfo['c_ucode'] = $rt['c_ucode'];
            $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
            if ($c_isfixed == 1) {
                $jumptype = 2;
            } else {
                $jumptype = 1;
            }
        }

        $rt['jumptype'] = $jumptype;
        $rt['c_like'] = $this->Toobig($rt['c_like']);
        $rt['switch_addtime'] = $this->GetShowTime($rt['c_addtime'], 0);
        $rt['c_headimg'] = GetHost() . '/' . $rt['c_headimg'];
        $rt['imglist'] = $this->get_imglist($rt['c_id']);
        $rt['tj_product'] = $this->get_product($rt['c_id']);
        $rt['comment_list'] = $this->get_commentlist($rt['c_id'], 1);
        //用户是否关注资源发布者
        $rt['is_attention'] = $this->is_attention($parr['ucode'], $rt['c_ucode']);
        //是否显示删除
        $rt['is_delete'] = $this->is_delete($parr['ucode'], $rt['c_ucode']);
        //是否点赞
        $rt['is_like'] = $this->is_like($rt['c_id'], $parr['ucode']);

        //增加资源浏览量
        $arr['sid'] = $parr['sid'];
        $result = $this->ResourceClick($arr);
        if ($result['code'] != 0) {
            return $result;
        }

        return MessageInfo(0, "查询成功", $rt);
    }

    //获取资源图片列表
    public function get_imglist($id) {
        $w['c_regionid'] = $id;
        $w['c_sourceid'] = 2;
        $data = M('Resource_img')->field('c_img,c_thumbnail_img')->where($w)->select();

        foreach ($data as $k => $v) {
            $data[$k]['c_img'] = GetHost() . '/' . $v['c_img'];
            $data[$k]['c_thumbnail_img'] = GetHost() . '/' . $v['c_img'];
        }

        if (count($data) == 0) {
            $data = array();
        }

        return $data;
    }

    //获取资源推荐商品信息
    public function get_product($id) {
        $db = M('Resource_product as r');
        $w['r.c_resourceid'] = $id;
        $field = 'r.c_pcode,p.c_name,p.c_price,p.c_pimg,p.c_source';
        $join = 'left join t_product as p on r.c_pcode=p.c_pcode';

        $data = $db->field($field)->join($join)->where($w)->select();

        foreach ($data as $key => $value) {
            $data[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $data[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?pcode=" . $value['c_pcode'];
        }

        if (count($data) == 0) {
            $data = array();
        }

        return $data;
    }

    //获取资源评论列表
    public function get_commentlist($id, $flag) {
        $db = M('resource_comment as r');

        $join = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on r.c_upucode=pu.c_ucode';
        $field = 'r.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';

        $commentwhere['r.c_resourceid'] = $id;
        $commentwhere['r.c_state'] = 1;

        $order = 'r.c_id desc';
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
            $arr[$k]['c_resourceid'] = $v['c_resourceid'];

            $arr[$k]['c_ucode'] = $v['c_ucode'];
            $arr[$k]['c_nickname'] = $v['c_nickname'];
            $arr[$k]['c_headimg'] = GetHost() . '/' . $v['c_headimg'];

            if (!empty($v['c_upucode'])) {
                $arr[$k]['upucode'] = $this->IsNull($v['c_upucode']);
                $arr[$k]['upheadimg'] = $this->IsNull($v['upheadimg'], 1);
                $arr[$k]['upnickname'] = $this->IsNull($v['upnickname']);
            }

            $arr[$k]['c_content'] = $v['c_content'];
            $arr[$k]['c_addtime'] = $v['c_addtime'];
            $arr[$k]['switch_addtime'] = $this->GetShowTime($v['c_addtime'], 0);
        }
        return $arr;
    }

    //用户是否关注资源发布者
    public function is_attention($c_ucode, $c_attention_ucode) {
        if (empty($c_ucode)) {
            return 0;
        }

        $w['c_ucode'] = $c_ucode;
        $w['c_attention_ucode'] = $c_attention_ucode;

        $count = M('Users_attention')->where($w)->count();

        if ($count == 0) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        return $flag;
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
    public function is_like($resourceid, $ucode) {
        if (empty($ucode)) {
            return 0;
        }

        $w['c_ucode'] = $ucode;
        $w['c_resourceid'] = $resourceid;

        $count = M('Resource_like')->where($w)->count();

        if ($count == 0) {
            $flag = 0;
        } else {
            $flag = 1;
        }
        return $flag;
    }

    //获取前台显示时间
    public function GetShowTime($time, $sign) {
        if ($sign == 0) {
            $time = time() - strtotime($time);
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
            if (empty($elapse)) {
                $backtime = '刚刚';
            } else {
                $backtime = $elapse . '前';
            }
        } else if ($sign == 1) {
            $backtime = $time;
            $month = substr($time, 5, 2);
            $day = substr($time, 8, 2);
            $ttime = substr($time, 11, 5);
            $backtime = $month . '/' . $day . '|' . $ttime;
        } else {
            $year = substr($time, 2, 2);
            $month = substr($time, 5, 2);
            $day = substr($time, 8, 2);
            $ttime = substr($time, 11, 5);
            $backtime = $year . '/' . $month . '/' . $day . ' ' . $ttime;
        }

        return $backtime;
    }

    /**
     *  添加资源
     *  @param ucode,content,pcode,isaddress,address,longitude,latitude
     */
    public function AddResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }
        $add['c_ucode'] = $parr['ucode'];
        $add['c_content'] = $parr['content'];
        $add['c_status'] = 1;
        $add['c_longitude'] = $parr['longitude'];
        $add['c_latitude'] = $parr['latitude'];
        $add['c_isaddress'] = $parr['isaddress'];
        if ($parr['isaddress'] == 1) {
            if (empty($parr['address'])) {
                return Message(1024, '位置信息为空');
            }
            $add['c_address'] = $parr['address'];
        }
        $add['c_addtime'] = date('Y-m-d H:i:s', time());
        $add['c_updatetime'] = date('Y-m-d H:i:s', time());

        $db = M('');
        $db->startTrans();
        $result = M('Resource')->add($add);
        $rid = $result;
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '添加资源失败');
        }

        if (!empty($parr['pcode'])) {
            //添加推荐商品
            $pdata['c_resourceid'] = $rid;
            $pdata['c_pcode'] = $parr['pcode'];
            $result = M('resource_product')->add($pdata);

            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '推荐商品添加失败');
            }
        }


        //添加图片
        $imglist = $parr['imglist'];
        // $image = new \Think\Image();
        foreach ($imglist as $key => $value) {
            $imgdata['c_sourceid'] = 2;
            $imgdata['c_regionid'] = $rid;
            // $imgoption = SYS_PATH . str_replace('/', DS, $value);
            // $thumbimg = 'Uploads/source/thumb/' . date('Y-m-d') . '/tb_' . $key . time() . '.jpg';
            // $thumbimgpath = SYS_PATH . str_replace('/', DS, $thumbimg);
            // checkDir($thumbimgpath);
            // $image->open($imgoption)->thumb(150, 150)->save('./' . $thumbimg);
            $imgdata['c_img'] = $value;
            $imgdata['c_thumbnail_img'] = $value;
            $result = M('Resource_img')->add($imgdata);
            if ($result <= 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '资源图片添加失败');
            }
        }

        //添加5公里商圈记录
        //发送类型，1跳转到url, 2跳转到url（需要传入openid）,3订单详情，4商品详情，5个人空间，6个人资料，7商家商品列表 ,8红包，9资源列表 ，10资源详情，11粉丝列表）
        $blogdata['ucode'] = $parr['ucode'];
        $blogdata['behavior'] = 1;
        $blogdata['regionid'] = $rid;
        $blogdata['tag'] = 10;
        $blogdata['tagvalue'] = $rid;
        $blogdata['longitude'] = $parr['longitude'];
        $blogdata['latitude'] = $parr['latitude'];

        if ($parr['isaddress'] == 1) {
            if (empty($parr['address'])) {
                $blogdata['address'] = '';
            }
            $blogdata['address'] = $parr['address'];
        }
        $blogdata['addtime'] = date('Y-m-d H:i:s', time());

        $result = IGD('Servecentre', 'Serve')->Addlogs($blogdata);

        //给关注用户发送动态消息
        $userw['c_ucode'] = $parr['ucode'];
        $nickname = M('Users')->where($userw)->getField('c_nickname');

        $w['c_attention_ucode'] = $parr['ucode'];
        $ucode_list = M('Users_attention')->field('c_ucode')->where($w)->select(); //发送对象ucode

        if (!empty($ucode_list)) {
            foreach ($ucode_list as $key => $value) {
                $Msgcentre = IGD('Msgcentre', 'Message');
                $msgdata['ucode'] = $value['c_ucode'];
                $msgdata['type'] = 0;
                $msgdata['platform'] = 1;
                $msgdata['sendnum'] = 1;
                $msgdata['title'] = '系统消息';
                $msgdata['content'] = "您所关注的用户'" . $nickname . "'又发表资源动态了，前去围观！";
                $msgdata['tag'] = 10;
                $msgdata['weburl'] = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $rid;
                $msgdata['tagvalue'] = $rid;
                $Msgcentre->CreateMessegeInfo($msgdata);
            }
        }

        $db->commit();
        return Message(0, '添加成功');
    }

    /**
     *  删除资源信息
     *  @param sid 资源id，ucode
     */
    public function DeleteResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $sid = $parr['sid'];

        $w['c_id'] = $sid;
        $rucode = M('Resource')->where($w)->getField('c_ucode');
        if ($parr['ucode'] != $rucode) {
            return Message(1010, "您没有权限删除");
        }

        $db = M('');
        $db->startTrans();

        $result = M('Resource')->where($w)->delete();

        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除资源数据失败');
        }

        //删除图片
        $imgw['c_regionid'] = $sid;
        $imgw['c_sourceid'] = 2;

        $imglist = M('Resource_img')->field('c_thumbnail_img')->where($imgw)->select();
        if (!empty($imglist)) {
            foreach ($imglist as $key => $value) {
                unlink($value);
            }

            $result = M('Resource_img')->where($imgw)->delete();
            if ($result < 0) {
                $db->rollback(); //不成功，则回滚
                return Message(1025, '删除图片失败');
            }
        }

        $pw['c_resourceid'] = $sid;
        //删除推荐商品表
        $result = M('Resource_product')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除推荐商品失败');
        }

        //删除资源评论表
        $result = M('Resource_comment')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除评论列表失败');
        }

        //删除资源点赞表
        $result = M('Resource_like')->where($pw)->delete();
        if ($result < 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '删除点赞记录表失败');
        }


        $db->commit();
        return Message(0, '删除成功');
    }

    /**
     *  点赞、取消点赞 
     *  @param sid,handle 1-点赞，0-取消点赞,ucode点赞用户编号，resourceid
     */
    public function ResourceLike($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录！");
        }

        $db = M('');
        $db->startTrans();

        $flage = $parr['handle'];
        $where['c_id'] = $parr['resourceid'];
        //查询记录条件
        $data['c_ucode'] = $parr['ucode'];
        $data['c_resourceid'] = $parr['resourceid'];

        $result = M('Resource_like')->where($data)->getField('c_id');

        if ($result) {
            $result = M('Resource_like')->where($data)->delete();
            if (!$result) {
                $db->rollback();
                return Message(1011, "删除点赞记录失败！");
            }

            $result = M('Resource')->where($where)->setDec('c_like', '1');
            if (!$result) {
                $db->rollback();
                return Message(1012, "减少点赞量统计失败！");
            }

            $succeed_msg = "取消点赞成功";
        } else {
            $data['c_addtime'] = date('Y-m-d H:i:s');

            $result = M('Resource_like')->add($data);
            if (!$result) {
                $db->rollback();
                return Message(1011, "添加点赞记录失败！");
            }

            $result = M('Resource')->where($where)->setInc('c_like', '1');
            if (!$result) {
                $db->rollback();
                return Message(1012, "增加点赞量统计失败！");
            }

            $succeed_msg = "点赞成功";

            //给资源发布者发送信息
            $userinfo = M('Resource')->field('c_ucode')->where($where)->find();
            if ($userinfo) {
                $pdata['rid'] = $parr['resourceid'];
                $pdata['pucode'] = $userinfo['c_ucode']; //发表者
                $pdata['ucode'] = $parr['ucode']; //点赞
                $this->PostMsg($pdata, 1);
            }
        }

        $db->commit();
        return Message(0, $succeed_msg);
    }

    /**
     *  关注、取消关注 
     *  @param handle 1-关注，0-取消关注,ucode用户编号,issue_ucode被关注用户编码
     */
    public function UserAttention($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录！");
        }

        if ($parr['ucode'] == $parr['issue_ucode']) {
            return Message(1008, "自己不能关注自己！");
        }

        $db = M('');
        $db->startTrans();

        $flage = $parr['handle'];
        //关注记录查询条件
        $w['c_ucode'] = $parr['ucode'];
        $w['c_attention_ucode'] = $parr['issue_ucode'];
        if ($flage == 1) {
            //添加关注记录
            $count = M('users_attention')->where($w)->count();

            if ($count > 0) {
                return Message(1010, "您已经关注该用户，无法重新关注！");
            }

            $save_date['c_ucode'] = $parr['ucode'];
            $save_date['c_attention_ucode'] = $parr['issue_ucode'];
            $save_date['c_addtime'] = date('Y-m-d H:i:s');

            $result = M('users_attention')->add($save_date);

            if (!$result) {
                $db->rollback();
                return Message(1011, "添加关注记录失败！");
            }

            //添加被关注统计
            $where['c_ucode'] = $parr['issue_ucode'];
            $count1 = M('users_date')->where($where)->count();
            if ($count1 == 0) {
                $save_date1['c_ucode'] = $parr['issue_ucode'];
                $save_date1['c_attention'] = 1;
                $result = M('users_date')->add($save_date1);
                if (!$result) {
                    $db->rollback();
                    return Message(1012, "添加关注统计记录失败！");
                }
            } else {
                $result = M('users_date')->where($where)->setInc('c_attention', '1');
                if (!$result) {
                    $db->rollback();
                    return Message(1013, "增加关注量统计失败！");
                }
            }
            $succeed_msg = "关注成功";

            //给被关注者发送信息
            if ($parr['issue_ucode']) {
                $pdata['pucode'] = $parr['issue_ucode'];
                $pdata['ucode'] = $parr['ucode'];
                $this->PostMsg($pdata, 4);
            }
        } else {
            //删除关注记录
            $result = M('users_attention')->where($w)->delete();

            if (!$result) {
                $db->rollback();
                return Message(1014, "删除关注记录失败！");
            }

            //减少关注统计量
            $where['c_ucode'] = $parr['issue_ucode'];
            $result = M('users_date')->where($where)->setDec('c_attention', '1');
            if (!$result) {
                $db->rollback();
                return Message(1015, "减少关注量统计失败！");
            }
            $succeed_msg = "取消关注成功";
        }

        $db->commit();
        return Message(0, $succeed_msg);
    }

    /**
     *  评论信息
     *  @param content,resourceid,ucode,bid
     */
    public function CommentResource($parr) {
        if (empty($parr['ucode'])) {
            return Message(1009, "请先登录");
        }

        $w['c_resourceid'] = $parr['resourceid'];
        $count = M('Resource')->where($w)->count();
        if ($count == 0) {
            return Message(1000, '评论失败,资源不存在');
        }

        $data['c_content'] = $parr['content'];
        $data['c_resourceid'] = $parr['resourceid'];
        $data['c_ucode'] = $parr['ucode'];
        $upucode = '';
        if ($parr['bid'] != '' && $parr['bid'] != null && $parr['bid'] != 0) {
            $data['c_bid'] = $parr['bid'];

            $w1['c_id'] = $parr['bid'];
            $comment_uinfo = M('Resource_comment')->where($w1)->field('c_ptid,c_ucode')->find();
            // var_dump($comment_uinfo);die;
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
        $result = M('Resource_comment')->add($data);
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
            $result = M('Resource_comment')->where($where)->save($save);
            if ($result <= 0) {
                $db->rollback();
                return Message(1003, '评论失败');
            }
        }

        //添加资源的评论数
        $arr['sid'] = $parr['resourceid'];
        $arr['handle'] = 1;
        $arr['num'] = 1;
        $result = $this->ResourceComment($arr);
        if ($result['code'] != 0) {
            $bd->rollback();
            return $result;
        }

        $db->commit();

        //给资源发布者发送信息
        $w['c_id'] = $parr['resourceid'];
        $userinfo = M('Resource')->field('c_ucode')->where($w)->find();
        if ($userinfo) {
            $pdata['rid'] = $parr['resourceid'];
            $pdata['pucode'] = $userinfo['c_ucode'];
            $pdata['ucode'] = $parr['ucode'];
            $this->PostMsg($pdata, 2);
        }

        //给评论者发送消息
        if ($upucode != '') {
            $pdata1['rid'] = $parr['resourceid'];
            $pdata1['pucode'] = $upucode;
            $pdata1['ucode'] = $parr['ucode'];
            $this->PostMsg($pdata1, 3);
        }

        //返回评论数据
        $join = 'left join t_users as u on r.c_ucode=u.c_ucode';
        $join1 = 'left join t_users as pu on r.c_upucode=pu.c_ucode';
        $field = 'r.*,u.c_ucode,u.c_headimg,u.c_nickname,pu.c_ucode as upucode,pu.c_headimg as upheadimg,pu.c_nickname as upnickname';
        $commentwhere['r.c_id'] = $id;
        $comment = M('resource_comment as r')->join($join)->join($join1)->where($commentwhere)->field($field)->select();

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
        $comment_info = M('Resource_comment')->field('c_resourceid,c_ucode')->where($where)->find();

        // $where1['c_id'] = $comment_info['c_resourceid'];
        // $r_ucode = M('Resource')->where($where1)->getField('c_ucode');
        // if($parr['ucode'] != $r_ucode){
        if ($parr['ucode'] != $comment_info['c_ucode']) {
            return Message(1010, "您没有权限删除");
        }
        // }

        $db = M('');
        $db->startTrans();

        $cid = M('Resource_comment')->where($where)->getField('c_ptid');
        if ($cid == $parr['cid']) {
            $where2['c_ptid'] = $cid;
            $count = M('Resource_comment')->where($where2)->count();
            $result = M('Resource_comment')->where($where2)->delete();
        } else {
            $count = M('Resource_comment')->where($where)->count();
            $result = M('Resource_comment')->where($where)->delete();
        }

        if (!$result) {
            $db->rollback();
            return Message(1003, '删除失败');
        }

        //删除资源评论数
        $arr['sid'] = $comment_info['c_resourceid'];
        $arr['handle'] = 0;
        $arr['num'] = $count;

        $result = $this->ResourceComment($arr);
        if ($result['code'] != 0) {
            $db->rollback();
            return $result;
        }

        $db->commit();
        return Message(0, '删除成功');
    }

    //给关注的用户发送动态消息,flage 1-资源点赞、2-资源评论
    public function PostMsg($parr, $flage) {
        $w['c_ucode'] = $parr['ucode'];
        $nickname = M('Users')->where($w)->getField('c_nickname');

        switch ($flage) {
            case 1:
                $content = $nickname . "赞了我！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            case 2:
                $content = $nickname . "评论了我的动态！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            case 3:
                $content = $nickname . "回复了我！";
                $weburl = GetHost(1) . '/index.php/Home/Index/comment?rid=' . $parr['rid'];
                $tag = 10;
                $tagvalue = $parr['rid'];
                break;
            default:
                $content = $nickname . "关注了我！";
                $weburl = GetHost(1) . '/index.php/Home/Myspace/myfans';
                $tag = 11;
                $tagvalue = 2;
                break;
        }

        $Msgcentre = IGD('Msgcentre', 'Message');
        $msgdata['ucode'] = $parr['pucode'];
        $msgdata['type'] = 0;
        $msgdata['sendnum'] = 1;
        $msgdata['title'] = '系统消息';
        $msgdata['content'] = $content;
        $msgdata['tag'] = $tag;
        $msgdata['weburl'] = $weburl;
        $msgdata['tagvalue'] = $tagvalue;
        $Msgcentre->CreateMessegeInfo($msgdata);
    }

    /**
     *  个人空间头部数据
     *  @param ucode,perucode
     */
    public function PersonalDate($parr) {
        $db = M('Users as u');
        $w['u.c_ucode'] = $parr['perucode']; //被访问者

        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_signature,u.c_isagent,u.c_shop,u.c_shopnum,d.c_pv,d.c_attention,u.c_isfixed1 as c_isfixed';
        $data = $db->join($join)->where($w)->field($field)->find();

        if (empty($data)) {
            return Message(1001, '你访问的用户信息不存在！');
        }

        $data['c_headimg'] = GetHost() . '/' . $data['c_headimg'];
        $data['is_attention'] = $this->is_attention($parr['ucode'], $parr['perucode']);
        $data['share_title'] = "邀你参观【" . $data['c_nickname'] . "】的小蜜空间";
        $data['share_des'] = '小伙伴们快到我的小蜜空间中看看吧';
        if($data['c_shop']==1){
       		$data['share_url'] = GetHost(1) . "/index.php/Store/Index/index?fromucode=" . $parr['perucode'];
        }else{        	
        	$data['share_url'] = GetHost(1) . "/index.php/Home/Myspace/index?fromucode=" . $parr['perucode'];
        }
        if (!empty($data['c_shopnum'])) {
            $shopnum = $data['c_shopnum'];
            $sw['c_shopnum'] = $shopnum;
            $img = M('Shop_num')->where($sw)->getField('c_img');
            $data['shopnum_img'] = GetHost() . '/' . $img;
        } else {
            $data['shopnum_img'] = '';
        }

        if (empty($data['c_pv'])) {
            $data['c_pv'] = 0;
        }

        if (empty($data['c_attention'])) {
            $data['c_attention'] = 0;
        }

        if ($data['c_shop'] == 1) {

            $shopwehere['c_ucode'] = $data['c_ucode'];
            $shopuserinfo = M('User_local')->where($shopwehere)->find();

            if ($shopuserinfo['c_isfixed'] == 1) {
                $data['c_source'] = "2";
            } else {
                $data['c_source'] = "1";
            }
            $data['shop_url'] = '';
        } else {
            $data['c_source'] = "0";
        }

        if (!empty($parr['ucode']) && ($parr['perucode'] == $parr['ucode'])) {
            $where['c_ucode'] = $parr['ucode'];
            $data['my_attention'] = M('users_attention')->where($where)->count(); //我的关注
        } else {
            //增加个人空间浏览量
            $wv['ucode'] = $parr['ucode'];
            $wv['vucode'] = $parr['perucode'];
            $wv['source'] = $parr['source'];

            $w1['c_ucode'] = $parr['ucode'];
            $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
            $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

            $wv['nickname'] = $userinfo['c_nickname'];
            $wv['headimg'] = $userinfo['c_headimg'];
            // $wv['browser'] = GetBrowser();
            $wv['ip'] = GetIP();
            $wv['type'] = 1;

            $result = $this->SpacePv($wv);
            if ($result['code'] != 0) {
                return $result;
            }
        }
        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  个人店铺头部数据
     *  @param ucode,perucode
     */
    public function PersonalShop($parr) {
        $db = M('Users as u');
        $w['u.c_ucode'] = $parr['perucode'];

        $join = 'left join t_users_date as d on d.c_ucode=u.c_ucode';
        $field = 'u.c_ucode,u.c_headimg,u.c_nickname,u.c_shopnum,d.c_pv';
        $data = $db->join($join)->join($join1)->where($w)->field($field)->find();

        if (empty($data)) {
            return Message(1001, "查询失败");
        }

        $data['c_headimg'] = GetHost() . '/' . $data['c_headimg'];

        if (!empty($data['c_shopnum'])) {
            $shopnum = $data['c_shopnum'];
            $sw['c_shopnum'] = $shopnum;
            $img = M('Shop_num')->where($sw)->getField('c_img');
            $data['shopnum_img'] = GetHost() . '/' . $img;
        } else {
            $data['shopnum_img'] = '';
        }

        if (empty($data['c_pv'])) {
            $data['c_pv'] = 0;
        }

        //增加个人空间浏览量
        $wv['ucode'] = $parr['ucode'];
        $wv['vucode'] = $parr['perucode'];
        $wv['source'] = $parr['source'];

        $w1['c_ucode'] = $parr['ucode'];
        $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
        $userinfo['c_headimg'] = GetHost() . '/' . $userinfo['c_headimg'];

        $wv['nickname'] = $userinfo['c_nickname'];
        $wv['headimg'] = $userinfo['c_headimg'];
        // $wv['browser'] = GetBrowser();
        $wv['ip'] = GetIP();
        $wv['type'] = 2;

        $result = $this->SpacePv($wv);

        return MessageInfo(0, "查询成功", $data);
    }

    /**
     *  增加资源浏览次数 
     *  @param sid
     */
    public function ResourceClick($parr) {
        $where['c_id'] = $parr['sid'];
        $result = M('Resource')->where($where)->setInc('c_click', '1');
        if (!$result) {
            return Message(1000, "增加查看记录失败");
        }
        return Message(0, "查看成功");
    }

    /**
     *  增加资源浏览次数 
     *  @param sid
     */
    public function ResourceComment($parr) {
        $handle = $parr['handle'];
        $num = $parr['num'];
        $id = $parr['sid'];

        $where['c_id'] = $id;
        if ($handle == 1) {
            $result = M('Resource')->where($where)->setInc('c_comment', $num);
        } else {
            $result = M('Resource')->where($where)->setDec('c_comment', $num);
        }
        if (!$result) {
            return Message(1000, "修改评论数量失败");
        }
        return Message(0, "修改成功");
    }

    /**
     *  添加个人空间浏览记录
     *  @param c_ucode，c_vucode
     */
    public function SpacePv($parr) {
        $db = M('');
        $db->startTrans();

        $add_date['c_ucode'] = $parr['ucode'];
        $add_date['c_vucode'] = $parr['vucode'];

        if (!empty($parr['nickname'])) {
            $add_date['c_username'] = $parr['nickname'];
        } else {
            $add_date['c_username'] = '未知用户';
        }

        if (!empty($parr['headimg'])) {
            $add_date['c_headimg'] = $parr['headimg'];
        } else {
            $add_date['c_headimg'] = GetHost() . '/Uploads/user.png';
        }

        $add_date['c_source'] = $parr['source'];
        $add_date['c_ip'] = $parr['ip'];
        $add_date['c_type'] = $parr['type'];
        $add_date['c_browser'] = $parr['browser'];
        $add_date['c_addtime'] = date('Y-m-d H:i:s');

        $result = M('Users_spacelog')->add($add_date);

        if (!$result) {
            $db->rollback();
            return Message(1000, "增加访问记录失败");
        }

        $w['c_ucode'] = $parr['vucode'];
        $count = M('users_date')->where($w)->count();

        if ($count == 0) {
            $adata['c_ucode'] = $parr['vucode'];
            $adata['c_pv'] = 1;
            $result1 = M('users_date')->add($adata);
        } else {
            $result1 = M('users_date')->where($w)->setInc('c_pv', '1');
        }

        if (!$result) {
            $db->rollback();
            return Message(1001, "增加个人空间访问量失败");
        }

        $db->commit();
        return Message(0, "查看成功");
    }

    /**
     *  添加推荐商品记录
     *  @param ucode，pcode
     */
    public function ProductVisit($parr) {
        $add_date['c_ucode'] = $parr['ucode'];
        $add_date['c_pcode'] = $parr['pcode'];

        if (!empty($parr['nickname'])) {
            $add_date['c_username'] = $parr['nickname'];
        } else {
            $add_date['c_username'] = '未知用户';
        }

        if (!empty($parr['headimg'])) {
            $add_date['c_headimg'] = $parr['headimg'];
        } else {
            $add_date['c_headimg'] = GetHost() . '/Uploads/user.png';
        }

        $add_date['c_source'] = $parr['source'];
        $add_date['c_ip'] = $parr['ip'];
        $add_date['c_browser'] = $parr['browser'];
        $add_date['c_addtime'] = date('Y-m-d H:i:s');

        $result = M('product_visit')->add($add_date);

        if (!$result) {
            return Message(1000, "增加访问记录失败");
        }

        return Message(0, "添加成功");
    }

    /**
     *  获取个人商铺商品数据
     *  @param shop_ucode，type，pageindex，pagesize
     */
    public function GetproductList($parr) {

        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $type = $parr['type'];

        $whereinfo = "c_ishow =1 and c_isdele=1 and c_ucode='" . $parr['shop_ucode'] . "'";
        $field = 'c_pcode,c_name,c_pimg,c_price';
        $orderid = '';
        if (!empty($type) && $type == 1) {
            $orderid .= 'c_addtime desc,';
        } else if (!empty($type) && $type == 2) {
            $orderid .= 'c_salesnum desc,';
        } else if (!empty($type) && $type == 3) {
            $orderid .= 'c_price asc,';
        } else if (!empty($type) && $type == 4) {
            $orderid .= 'c_price desc,';
        }

        $orderid .= 'c_id desc';
        $list = M('product')->where($whereinfo)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

    /**
     *  获取商铺全部商品数据
     *  @param type，pageindex，pagesize
     */
    public function GetAllproductList($parr) {

        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $type = $parr['type'];

        $whereinfo = "c_ishow =1 and c_isdele=1 ";
        $orderid = '';
        if (!empty($type) && $type == 1) {
            $orderid .= ' ';
        } else if (!empty($type) && $type == 2) {
            $orderid .= 'c_salesnum desc,';
        } else if (!empty($type) && $type == 3) {
            $orderid .= 'c_price asc,';
        } else if (!empty($type) && $type == 4) {
            $orderid .= 'c_price desc,';
        }

        $orderid .= 'c_id desc';
        $list = M('product')->where($whereinfo)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('product')->where($whereinfo)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        foreach ($list as $k => $v) {
            $list[$k]['c_pimg'] = GetHost() . '/' . $v['c_pimg'];
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

    /**
     *  获取我的关注详细数据
     *  @param pageindex，pagesize，type
     */
    public function Myattention($parr) {
        if (empty($parr['pageindex'])) {
            $pageindex = 1;
        } else {
            $pageindex = $parr['pageindex'];
        }
        $pagesize = $parr['pagesize'];
        $countPage = ($pageindex - 1) * $pagesize;

        $ucode = $parr['ucode'];

        if ($parr['type'] == 1) {
            $w['t.c_ucode'] = $ucode;
            $join = 'left join t_users as u on t.c_attention_ucode=u.c_ucode';
        } else {
            $w['t.c_attention_ucode'] = $ucode;
            $join = 'left join t_users as u on t.c_ucode=u.c_ucode';
        }

        $field = 'u.c_shop as jumptype,u.c_ucode,u.c_nickname,u.c_headimg,t.c_addtime,t.c_id';
        $orderid = 'u.c_id desc';

        $list = M('users_attention as t')->where($w)->field($field)->join($join)->order($orderid)->limit($countPage, $pagesize)->select();

        $dataCount = M('users_attention as t')->where($w)->join($join)->count();
        $pageCount = ceil($dataCount / $pagesize);

        if (count($list) <= 0) {
            $list = array();
            $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
            return MessageInfo(0, '查询成功', $listinfo);
        }

        if ($parr['type'] == 1) {
            foreach ($list as $k => $v) {
                $list[$k]['c_headimg'] = GetHost() . '/' . $list[$k]['c_headimg'];
                $list[$k]['c_addtime'] = $this->GetShowTime($list[$k]['c_addtime'], 0);
                $list[$k]['is_attention'] = 1;
            }
        } else {
            foreach ($list as $k => $v) {
                $list[$k]['c_headimg'] = GetHost() . '/' . $list[$k]['c_headimg'];
                $list[$k]['c_addtime'] = $this->GetShowTime($list[$k]['c_addtime'], 0);
                $list[$k]['is_attention'] = $this->is_attention($ucode, $list[$k]['c_ucode']);
            }
        }

        $listinfo = Page($pageindex, $pageCount, $dataCount, $list);
        return MessageInfo(0, '查询成功', $listinfo);
    }

}
