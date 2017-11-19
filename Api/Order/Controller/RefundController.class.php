<?php

namespace Order\Controller;

use Base\Controller\CheckController;

/**
 *  用户订单维权操作
 *
 */
class RefundController extends CheckController {

    //售后退款退货操作
    public function Refundinfor() {

        $ucode = $this->ucode;

        $upload_path = 'weiquan';
        $result = uploadimg($upload_path);

        $imgstr = "";
        if ($result['code'] == 0) {
            $imglist = array_values($result['data']);
            foreach ($imglist as $key => $value) {
                $imgstr.=$value;
                $imgstr.="|";
            }
        }

        if (strlen($imgstr) > 0) {
            $imgstr = substr($imgstr, 0, strlen($imgstr) - 1);
        }

        $parr['ucode'] = I('ucode');
        $parr['detailid'] = I('detailid');
        $parr['type'] = I('type');
        $parr['reason'] = I('reason');
        $parr['status'] = I('status');
        $parr['remarks'] = I('remarks');
        $parr['img'] = $imgstr;

        if (substr($parr['detailid'],0,1) == 's') {
            $Refundinfor = IGD('Supplyrefund', 'Agorder');
        } else {
            $Refundinfor = IGD('Refund', 'Order');
        }

        $result1 = $Refundinfor->Refundinfor($parr);
        $this->ajaxReturn($result1);
    }

    //商家同意
    public function AgreeRefund() {

        $ucode = $this->ucode;

        $parr['rcode'] = I('rcode');
        $parr['ucode'] = $ucode;

        if (substr($parr['rcode'],0,1) == 's') {
            $Refundinfor = IGD('Supplyrefund', 'Agorder');
        } else {
            $Refundinfor = IGD('Refund', 'Order');
        }
        $result1 = $Refundinfor->AgreeRefund($parr);
        $this->ajaxReturn($result1);
    }

    //商家确认退货
    public function Refundreturn() {
        $ucode = $this->ucode;

        $parr['rcode'] = I('rcode');
        $parr['ucode'] = $ucode;

        if (substr($parr['rcode'],0,1) == 's') {
            $Refundinfor = IGD('Supplyrefund', 'Agorder');
        } else {
            $Refundinfor = IGD('Refund', 'Order');
        }
        $result1 = $Refundinfor->Refundreturn($parr);
        $this->ajaxReturn($result1);
    }

    //商家不同意退款退货
    public function disagreeAgree() {
        $ucode = $this->ucode;

        $parr['rcode'] = I('rcode');
        $parr['ucode'] = $ucode;

        $Refundinfor = IGD('Refund', 'Order');
        $result1 = $Refundinfor->disagreeAgree($parr);
        $this->ajaxReturn($result1);
    }

    //更新维权快递信息
    public function Updateexpress() {
        $ucode = $this->ucode;

        $parr['rcode'] = I('rcode');
        $parr['ucode'] = $ucode;
        $parr['transcompany'] = I('transcompany');
        $parr['transno'] = I('transno');

        if (substr($parr['rcode'],0,1) == 's') {
            $Refundinfor = IGD('Supplyrefund', 'Agorder');
        } else {
            $Refundinfor = IGD('Refund', 'Order');
        }
        $result1 = $Refundinfor->Updateexpress($parr);
        $this->ajaxReturn($result1);
    }

    //获取用户维权列表
    public function GetRefundList() {

        $ucode = $this->ucode;

        $type = I('type');

        if ($type == 2) {
            $parr['acode'] = $ucode;
        } else {
            $parr['ucode'] = $ucode;
        }

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $Refundinfor = IGD('Refund', 'Order');
        $result1 = $Refundinfor->Getrefundlist($parr);
        $this->ajaxReturn($result1);
    }

    //获取用户维权详情
    public function GetRefundInfo() {
        $ucode = $this->ucode;

        $parr['rcode'] = I('rcode');
        $parr['ucode'] = $ucode;

        if (substr($parr['rcode'],0,1) == 's') {
            $Refundinfor = IGD('Supplyrefund', 'Agorder');
        } else {
            $Refundinfor = IGD('Refund', 'Order');
        }
        $result1 = $Refundinfor->GetrefundInfo($parr);

        if ($result1['code'] == 0) {
            $result2 = $Refundinfor->Getrefundlog($parr);
            $info = $result1['data'];

            $list = $info['c_img'];

            foreach ($list as $key => $value1) {
                $img['img'] = $value1;
                $imglist[] = $img;
            }

            $info['c_img'] = $imglist;

            $info['c_log'] = $result2['data'];

            $result3 = MessageInfo(0, "查询成功", $info);
            $this->ajaxReturn($result3);
        }
        $result3 = Message(1024, "没有查询到数据");
        $this->ajaxReturn($result3);
    }

}
