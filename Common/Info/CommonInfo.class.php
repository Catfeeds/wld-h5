<?php

/**
 * 用户通用获取信息模块
 */
class CommonInfo {

    /**
     *  获取平台返利系统设置
     *
     */
    function GetSystemSet() {
        $where['state'] = 1;
        $data = M('System_settings')->where($where)->order('c_addtime desc')->find();
        if (!$data) {
            return Message(1000, '数据不存在');
        }
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     * 计算邮费
     * @param acode,area,num
     */
    function CalculationFree($acode, $area, $num) {
        $where['c_ucode'] = $acode;
        $count = M('Users_free')->where($where)->count();
        if ($count > 0) {
            $where['c_city'] = array('like', '%' . $area . '%');
            $data = M('Users_free')->where($where)->find();
            if ($data) {
                if ($num > $data['c_num']) {
                    $single = bcdiv($data['c_addfreemoney'], $data['c_addnum'], 2);
                    $othernum = $num - $data['c_num'];
                    $otherfree = bcmul($single, $othernum, 2);
                    $free = bcadd($data['c_freemoney'], $otherfree, 2);
                } else {
                    $free = $data['c_freemoney'];
                }
            } else {
                $_where['c_ucode'] = $acode;
                $_where['c_isdefault'] = 1;
                $_data = M('Users_free')->where($_where)->find();
                if ($_data) {
                    if ($num > $_data['c_num']) {
                        $single = bcdiv($_data['c_addfreemoney'], $_data['c_addnum'], 2);
                        $othernum = $num - $_data['c_num'];
                        $otherfree = bcmul($single, $othernum, 2);
                        $free = bcadd($_data['c_freemoney'], $otherfree, 2);
                    } else {
                        $free = $_data['c_freemoney'];
                    }
                } else {
                    $free = 0;
                }
            }
        } else {
            $free = 0;
        }
        return $free;
    }

    /**
     *  查询平台品类
     */
    function GetCategory() {
        $data = M('Category')->select();
        if (count($data) == 0) {
            return MessageInfo(0, "查询成功", "");
        }
        foreach ($data as $key => $value) {
            $data[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
        }
        return MessageInfo(0, '查询成功', $data);
    }

    /**
     *  查询标签列表
     *  @param pageindex,pagesize
     */
    function GetLablist($parr) {
        if (empty($parr['pageindex'])) {
            $pageIndex = 1;
        } else {
            $pageIndex = $parr['pageindex'];
        }
        $pageSize = $parr['pagesize'];
        $countPage = ($pageIndex - 1) * $pageSize;
        $list = M('Label')->limit($countPage, $pageSize)->order('c_sort desc')->select();
        if (!$list) {
            return MessageInfo(0, "查询成功", "");
        }

        $count = M('Label')->count();
        $pageCount = ceil($count / $pageSize);
        $data = Page($pageIndex, $pageCount, $count, $list);
        return MessageInfo(0, '查询成功', $data);
    }

    //获取行业信息
    function GetIndustry() {

        $list = M('Industry')->select();

        if (count($list) == 0) {
            return MessageInfo(0, "查询成功", "");
        }
        return MessageInfo(0, '查询成功', $list);
    }

    /*获取省市区*/
    function GetRegion($parentid) {
        $where['parent_id'] = $parentid;
        $field = 'region_id,parent_id,region_name,region_type';
        $order = 'region_id asc';
        $list = M('region')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    function GetRegion1($parentid) {
        $where['region_type'] = $parentid;
        $field = 'region_id,parent_id,region_name,region_type';
        $order = 'region_id asc';
        $list = M('region')->where($where)->field($field)->order($order)->select();
        return $list;
    }

    function GetAllRegion() {
        $parentid = 1;
        $province = $this->GetRegion($parentid);
        foreach ($province as $key => $value) {

            //查询市
            $city = $this->GetRegion($value['region_id']);
            foreach ($city as $key1 => $value1) {
                $region = $this->GetRegion($value1['region_id']);
                $city[$key1]['region'] = $region;
            }
            $province[$key]['city'] = $city;
        }
        return $province;
    }
    /*获取省市区end*/

    //获取banner列表
    function get_banner($parr) {
        $Banner = M('Banner');

        $source = $parr['source'];
        $tag = $parr['tag'];

        $_where['c_state'] = 0;
        $_where['c_source'] = $parr['source'];

        if (!empty($tag) && $tag==2) {
            $field = "c_id,c_title,c_weburl,c_tag,c_tagvalue,c_sort,c_img";
        }else{
            $field = "c_id,c_title,c_tag,c_weburl,c_sort,c_img";
        }

        $data = $Banner->field($field)->where($_where)->order('c_sort asc')->select();

        foreach ($data as $key => $value) {
            $data[$key]['c_img'] = GetHost() . '/' . $value['c_img'];
        }
        return MessageInfo(0,'查询成功',$data);
    }

}
