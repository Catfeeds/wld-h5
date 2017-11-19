<?php
/**
 * 
 * 单笔实时接口
 * TRX_CODE:100014--单笔实时代付
 * TRX_CODE:100011--单笔实时代收
 * @var unknown_type
 */
define('ROOT_PATH', './');
require_once ROOT_PATH.'libs/ArrayXml.class.php';
require_once ROOT_PATH.'libs/cURL.class.php';
require_once ROOT_PATH.'libs/PhpTools.class.php';

header('Content-Type: text/html; Charset=UTF-8');
$tools=new PhpTools();
echo '<pre>';
 
// 源数组
$params = array(
    'INFO' => array(
        'TRX_CODE' => '100014',
        'VERSION' => '03',
        'DATA_TYPE' => '2',
        'LEVEL' => '6',
        'USER_NAME' => '20060400000044502',
        'USER_PASS' => '111111',
        'REQ_SN' => '200604000000445-rrrr1356732135xxxx',
    ),
    'TRANS' => array(
        'BUSINESS_CODE' => '09400',
        'MERCHANT_ID' => '200604000000445',
        'SUBMIT_TIME' => '20131218230712',
        'E_USER_CODE' => '10101328',
        'BANK_CODE' => '0105',
        'ACCOUNT_TYPE' => '00',
        'ACCOUNT_NO' => '6227001447170048826',
        'ACCOUNT_NAME' => '东东',
        'ACCOUNT_PROP' => '0',
        'AMOUNT' => '100000',
        'CURRENCY' => 'CNY',
        'ID_TYPE' => '0',
        'CUST_USERID' => '2901347',
        'SUMMARY' => '春风贷提现',
        'REMARK' => '30238924-1984857237-237843',
    ),
);
//发起请求
$result = $tools->send($params);
print_r($result);
