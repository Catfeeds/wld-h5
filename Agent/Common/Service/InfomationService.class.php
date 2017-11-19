<?php

namespace Common\Service;

class InfomationService {

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
		}
        return MessageInfo(0, '查询成功', $data);
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
}
