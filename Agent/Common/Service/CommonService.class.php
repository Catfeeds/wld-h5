<?php

namespace Common\Service;

class CommonService {

    /**
     *  获取首页未读状态标志
     *  @param ucode
     */
    function GetStatuMessage($parr)
    {
        $ucode = $parr['ucode'];
        $join = 'left join t_check_infolog as b on b.c_infoid=a.c_id';
        $whereinfo[] = array("a.c_ucode='' or a.c_ucode is null or a.c_ucode='$ucode'");
        $whereinfo[] = array("b.c_infoid is null");
        $whereinfo1[] = array("a.c_ucode='$ucode'");
        $whereinfo1[] = array("b.c_infoid is null");
        $data['publicnum'] = M('Check_info as a')->join($join)->where($whereinfo)->count();
        if (!$data['publicnum']) {
            $data['publicnum'] = 0;
        }

        $data['checknum'] = M('Check_info as a')->join($join)->where($whereinfo1)->count();
        if (!$data['checknum']) {
            $data['checknum'] = 0;
        }
        return MessageInfo(0,'查询成功',$data);
    }

    /**
     *  获取首页未读状态标志
     *  @param ucode
     */
    function ReadInfostatu($parr)
    {
        //查询是否有资料
        if(empty($parr['ucode'])){
            return Message(1009,'请先登录');
        }
        $where['c_ucode'] = $parr['ucode'];
        $angentinfo = M('Check_shopinfo')->where($where)->find();
        if (empty($angentinfo['c_dcode'])) {
            return Message(1000,'资料未完善');
        }

        if ($angentinfo['c_checked'] == 3) {
            return Message(0,'资料已完善');  
        } else {
            return Message(1001,'需要审核后才能操作');
        }
    }

    /**
     *  查询平台品类
     */
    function GetCategory() {
        $data = M('Category')->select();
        if (count($data) == 0) {
            return MessageInfo(0, "查询成功", "");
        }
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 查询上级资料信息
     * @param ucode
     */
    function GetupsetInfo($parr)
    {
        $join = 'left join t_users as b on a.c_ucode=b.c_acode';
        $where['b.c_ucode'] = $parr['ucode'];
        $data = M('Check_shopinfo as a')->join($join)->where($where)->field('a.*')->find();
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 查询对应的行业列表
     * @param id(商家行业列表)
     */
    function GetIndustry($id)
    {
        if (!empty($id)) {
            $where['c_pid'] = $id;
        } else {
            $where['c_pid'] = 0;    
        }

        $where[] = array('c_id <> 21 and c_id <> 22 and c_id <> 23');
        $data = M('Shop_industry')->where($where)->select();
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 修改商家行业
     * @param ucode,tid(商家行业id)
     */
    function EditIndustry($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $save['c_shoptrade'] = $parr['tid'];
        $result = M('Users')->where($where)->save($save);
        return $result;
    }
    
    /**
     * 修改商家地理位置
     * @param ucode,lng,lat,isfixed,address1
     */
    function EditShopLocal($parr)
    {
        $where['c_ucode'] = $parr['ucode'];
        $localinfo = M('User_local')->where($where)->find();
        $data['c_ucode'] = $parr['ucode'];
        $data['c_longitude'] = $parr['lng'];
        $data['c_latitude'] = $parr['lat'];
        $data['c_isfixed'] = $parr['isfixed'];
        $data['c_address'] = $parr['address1'];
        $data['c_updatetime'] = date('Y-m-d H:i:s');
        if (!$localinfo) {
            $data['c_addtime'] = date('Y-m-d H:i:s');
            $result = M('User_local')->add($data);
        } else {
            $result = M('User_local')->where($where)->save($data);
        }

        //同步修改用户地理位置
        $add_userdata['c_isfixed1'] = $parr['isfixed'];
        $add_userdata['c_longitude1'] = $parr['lng'];
        $add_userdata['c_latitude1'] = $parr['lat'];
        $add_userdata['c_address1'] = $parr['address1'];
        $result = M('Users')->where($where)->save($add_userdata);
        return $result;
    }

    /*
      获取省市区
      @param array $parr
     */
    function GetAddress($parr) {
        $whereadd['parent_id'] = $parr['parentid'];
        $whereadd['region_type'] = $parr['regiontype'];
        $list = M('Region')->where($whereadd)->select();

        return $list;
    }
}
