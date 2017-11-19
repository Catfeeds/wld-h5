<?php

/**
 * 店铺搜索与品类查询接口
 */
class SeekService {

    /**
     *  店铺搜索
     *  @param pageindex,pagesize,nickname
     *
     */
    function ShopSeek($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $condition = $parr['nickname'];
        if (!empty($condition)) {
            $where[] = array("c_nickname like '%" . $condition . "%' or c_shopnum='" . $condition . "'");
            // $where['c_nickname'] = array('like', '%' . $parr['nickname'] . '%');
        }

        $where['c_shop'] = 1;
        $order = 'c_shopnum desc,c_id desc';

        $field = "c_ucode,c_nickname,c_signature,c_headimg,c_trade,c_shop";

        $list = M('Users')->where($where)->field($field)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('Users')->where($where)->count();
        $pageCount = ceil($count / $pageSize);
        if (!$list) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, "查询成功", $data);
        }

        foreach ($list as $key => $value) {

            $jumptype = 0;
            if ($value['c_shop'] == 1) {
                //判断用户跳转
                $whereinfo['c_ucode'] = $value['c_ucode'];
                $c_isfixed = M('User_local')->where($whereinfo)->getField('c_isfixed');
                if ($c_isfixed == 1) {
                    $jumptype = 2;
                } else {
                    $jumptype = 1;
                }
            }
            $list[$key]['jumptype'] = $jumptype;
            if (empty($value['c_headimg'])) {
                $list[$key]['c_headimg'] = GetHost() . '/' . 'Uploads/logo.jpg';
            } else {
                $list[$key]['c_headimg'] = GetHost() . '/' . $value['c_headimg'];
            }
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  商品搜索
     *  @param pageindex,pagesize,pname,categoryid
     *
     */
    public function ProductList($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }

        if ($parr['categoryid'] == 50) {
            $parr['categoryid'] = 0;
        }

        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;

        $where['c_ishow'] = 1;
        $where['c_isdele'] = 1;
        $where['c_source'] = 1;

        if (!empty($parr['pname'])) {
            $where[] = array("c_name like '%" . $parr['pname'] . "%'");
        }

        if (!empty($parr['categoryid'])) {
            $where[] = array("c_categoryid=" . $parr['categoryid']);
        }

        $order = 'c_id desc';

        $list = M('Product')->where($where)->order($order)->limit($countPage, $pageSize)->select();

        $count = M('Product')->where($where)->count();
        $pageCount = ceil($count / $pageSize);

        if (count($list) == 0) {
            $list = array();
            $data = Page($pageIndex, $pageCount, $count, $list);
            return MessageInfo(0, '查询成功', $data);
        }

        foreach ($list as $key => $value) {
            $list[$key]['c_pimg'] = GetHost() . '/' . $value['c_pimg'];
            $list[$key]['url'] = GetHost(1) . '/' . "index.php/Home/Shop/details?type=1&pcode=" . $value['c_pcode'];
        }

        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //商品分类
    public function CategoryList($parr) {
        $where['c_isshow'] = 1;
        $data = M('Category')->where($where)->select();
        foreach ($data as $key => $value) {
            $data[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
        }
        if (count($data) == 0) {
            $data = array();
        }
        return MessageInfo(0, '查询成功', $data);
    }

}
