<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14
 * Time: 15:56
 */
namespace Pay\Controller;

use Base\Controller\BaseController;

class TonglianController extends BaseController
{
    function _initialize()
    {
        Vendor('TongLianPay.libs.ArrayXml');
        Vendor('TongLianPay.libs.cURL');
        Vendor('TongLianPay.libs.PhpTools');
    }

    public function test(){
        $tong =new \PhpTools();
        // 源数组
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '200004',
                'VERSION' => '03',
                'DATA_TYPE' => '2',
                'LEVEL' => '6',
                'USER_NAME' => '20060400000044502',
                'USER_PASS' => '111111',
                'REQ_SN' => '200604000000445-rrrr1356732135xxxx',
            ),
            'QTRANSREQ' => array(
                'QUERY_SN' => '22222222222222222222222',
                'MERCHANT_ID' => '200604000000445',
                'STATUS' => '2',
                'TYPE' => '1',
                'START_DAY' => '',
                'END_DAY' => ''
            ),
        );
//发起请求
        $result = $tong->send( $params);
        print_r($result);
    }

    public function test1(){
        // 源数组
        $tong =new \PhpTools();
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '100001',
                'VERSION' => '03',
                'DATA_TYPE' => '2',
                'LEVEL' => '6',
                'USER_NAME' => '20060400000044502',
                'USER_PASS' => '111111',
                'REQ_SN' => '200604000000445-ddddddxxddddsss',
            ),
            'BODY' => array(
                'TRANS_SUM' => array(
                    'BUSINESS_CODE' => '10600',
                    'MERCHANT_ID' => '200604000000445',
                    'SUBMIT_TIME' => '20131218230712',
                    'TOTAL_ITEM' => '2',
                    'TOTAL_SUM' => '2000',
                    'SETTDAY' => '',
                ),
                'TRANS_DETAILS'=> array(
                    'TRANS_DETAIL'=> array(
                        'SN' => '00001',
                        'E_USER_CODE'=> '00001',
                        'BANK_CODE'=> '0105',
                        'ACCOUNT_TYPE'=> '00',
                        'ACCOUNT_NO'=> '6225883746567298',
                        'ACCOUNT_NAME'=> '张三',
                        'PROVINCE'=> '',
                        'CITY'=> '',
                        'BANK_NAME'=> '',
                        'ACCOUNT_PROP'=> '0',
                        'AMOUNT'=> '1000',
                        'CURRENCY'=> 'CNY',
                        'PROTOCOL'=> '',
                        'PROTOCOL_USERID'=> '',
                        'ID_TYPE'=> '',
                        'ID'=> '',
                        'TEL'=> '13828383838',
                        'CUST_USERID'=> '用户自定义号',
                        'REMARK'=> '备注信息1',
                        'SETTACCT'=> '',
                        'SETTGROUPFLAG'=> '',
                        'SUMMARY'=> '',
                        'UNION_BANK'=> '010538987654',
                    ),
                    'TRANS_DETAIL2'=> array(
                        'SN' => '00002',
                        'E_USER_CODE'=> '00001',
                        'BANK_CODE'=> '0103',
                        'ACCOUNT_TYPE'=> '00',
                        'ACCOUNT_NO'=> '6225883746567228',
                        'ACCOUNT_NAME'=> '王五',
                        'PROVINCE'=> '',
                        'CITY'=> '',
                        'BANK_NAME'=> '',
                        'ACCOUNT_PROP'=> '0',
                        'AMOUNT'=> '1000',
                        'CURRENCY'=> 'CNY',
                        'PROTOCOL'=> '',
                        'PROTOCOL_USERID'=> '',
                        'ID_TYPE'=> '',
                        'ID'=> '',
                        'TEL'=> '13828383838',
                        'CUST_USERID'=> '用户自定义号',
                        'REMARK'=> '备注信息2',
                        'SETTACCT'=> '',
                        'SETTGROUPFLAG'=> '',
                        'SUMMARY'=> '',
                        'UNION_BANK'=> '010538987654',
                    )
                )
            ),
        );
//发起请求
        $result = $tong->send( $params);
        print_r($result);
    }

}