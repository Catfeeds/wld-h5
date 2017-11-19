<?php

/**
 * 	中秋祝福活动
 *
 */
class WishActivity {

	/**
	 * 添加祝福
	 * @param aid,sendname,receivername,content,imgurl
	 */
	function AddWish($parr)
	{
		$wishdata['c_aid'] = $parr['aid'];
		$wishdata['c_sendname'] = $parr['sendname'];
		$wishdata['c_receivername'] = $parr['receivername'];
		$wishdata['c_content'] = $parr['content'];
		$wishdata['c_imgurl'] = $parr['imgurl'];
		$wishdata['c_addtime'] = date('Y-m-d H:i:s');
		$result = M('Activity_wish')->add($wishdata);
		if (!$result) {
			return Message(4000,'添加失败');
		}
		$wishdata['c_id'] = $result;
		return MessageInfo(0,'添加成功',$wishdata);
	}

	/**
	 * 获取祝福信息
	 * @param id,aid
	 */
	function GetWish($parr)
	{
		$where['c_id'] = $parr['id'];
		// $where['c_aid'] = $parr['aid'];
		$data = M('Activity_wish')->where($where)->find();
		if (!$data) {
			return Message(4001,'查询失败');
		}
		$data['c_imgurl'] = GetHost().'/'.$data['c_imgurl'];
		return MessageInfo(0,'查询成功',$data);
	}

}

?>