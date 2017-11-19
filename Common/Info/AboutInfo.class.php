<?php

/**
 * 用户反馈接口
 */
class AboutInfo {

    //反馈信息
    public function AddFeedback($parr) {

        $add['c_ucode'] = $parr['ucode'];
        $add['c_content'] = $parr['content'];
        $add['c_platform'] = $parr['platform'];
        $add['c_system'] = $parr['system'];
        $add['c_ip'] = $parr['ip'];
        $add['c_browser'] = $parr['browser'];
        $add['c_addtime'] = date('Y-m-d H:i:s', time());
        $add['c_img'] = $parr['imglist'];

        $db = M('');
        $db->startTrans();
        $result = M('Feedback')->add($add);
        if ($result <= 0) {
            $db->rollback(); //不成功，则回滚
            return Message(1025, '反馈信息失败');
        }
        $db->commit();
        return Message(0, '反馈信息成功');
    }

}
