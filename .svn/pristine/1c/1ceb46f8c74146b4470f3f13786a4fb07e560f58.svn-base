<?php

namespace Test\Controller;

use Think\Controller;
use Base\Controller\ComController;

/**
 * 商圈模块
 */
class IndexController extends ComController {

    //位置
    public function localtest()
    {
        vendor('LBS.LocalServer');
        $redisconf = array(
            'scheme' => 'tcp',
            'host' => C('REDIS_HOST'),
            'port' => C('REDIS_PORT'),
            'timeout' => C('REDIS_TIMEOUT'),
            'database' => C('REDIS_DBNAME'),
            'password' => C('REDIS_AUTH'),
        );
        $LocalServer = new \LBS\LocalServer($redisconf);

        // $data = $LocalServer->getlist('LBS_set',0,2);
        // dump($data);

        // $search = $LocalServer->search('116.435182767868','39.91811857809279',1000,'km');
        // dump($search);

        // IGD('Common', 'Redis')->RediesStoreSram('alocalinfo', $search,1200);

        // $data = IGD('Common','Redis')->Rediesgetucode('alocalinfo');
// dump($data);
        // $page = $this->page_array(10,5,$data,1);
        // dump($page);

        // $search2 = $LocalServer->searchByMembers('fesco',500,'m');
        // dump($search2);die;
        
        // die;

        //获取用户位置信息
        $userlist = M('Users')->where($where)->order('c_id asc')->limit(100000)->select();
        $k = 0;
        foreach ($userlist as $key => $value) {
            $local = M('User_local')->where(array('c_ucode'=>$value['c_ucode']))->find();
            if ($local['c_longitude']>0 && $local['c_latitude']>0) {
                $add_params[$k] = [
                    'name' => $value['c_ucode'],
                    'long' => $local['c_longitude'],
                    'lat' => $local['c_latitude']
                ];
                $k++;
            }
        }
// dump($add_params);die;
        $res = $LocalServer->add($add_params);
            dump($res);
        // //添加坐标
        // $add_params = [
        //     [
        //         'name' => 'yabao_road',
        //         'long' => '116.43620200729366',
        //         'lat' => '39.916880160714435'
        //     ],
        //     [
        //         'name' => 'jianguomen',
        //         'long' => '116.4356870231628',
        //         'lat' => '39.908560377800676'
        //     ],        
        //     [
        //         'name' => 'cofco',
        //         'long' => '116.43564410781856',
        //         'lat' => '39.92024564137184'
        //     ],
        //     [
        //         'name' => 'fesco',
        //         'long' => '116.435182767868',
        //         'lat' => '39.91811857809279'
        //     ],    
        //     [
        //         'name' => 'chaoyangmen',
        //         'long' => '116.4345336732864',
        //         'lat' => '39.924466658329585'
        //     ],
        //     [
        //         'name' => 'galaxy_soho',
        //         'long' => '116.4335788068771',
        //         'lat' => '39.921372916981106'
        //     ],
        // ];
        // $res = $LocalServer->add($add_params);
        // dump($res);die;
    }

    /**   
     * 数组分页函数  核心函数  array_slice   
     * 用此函数之前要先将数据库里面的所有数据按一定的顺序查询出来存入数组中   
     * $count   每页多少条数据   
     * $page   当前第几页   
     * $array   查询出来的所有数组   
     * order 0-不变,1-反序,2-随机  
     */       
          
    function page_array($count,$page,$array,$order){      
        global $countpage;                  #定全局变量      
        $page = (empty($page))?'1':$page;     #判断当前页面是否为空 如果为空就表示为第一页面       
        $start = ($page-1)*$count;            #计算每次分页的开始位置      
        if($order == 1){      
          $array = array_reverse($array);      
        } else if ($order == 2) {
          $array = shuffle($array);
        }        
        $totals = count($array);        
        $countpage = ceil($totals/$count); #计算总页面数      
        $pagedata = array();      
        $pagedata = array_slice($array,$start,$count);      
        return $pagedata;  #返回查询数据      
    } 

    public function lbsup()
    {
        $b_time = microtime(true);
        $n = 0;
        
        $lbs = IGD('Local','Lbs'); 

        //获取用户位置信息
        $userlist = M('Users')->where($where)->order('c_id asc')->limit(100)->select();
        foreach ($userlist as $key => $value) {
            $local = M('User_local')->where(array('c_ucode'=>$value['c_ucode']))->find();
            dump($local['c_ucode'].':'.$local['c_latitude']);
            $lbs->upinfo($value['c_ucode'], $local['c_latitude'], $local['c_longitude']);
        }
        
        dump('上报成功');       
    }

    public function lbssh()
    {
        $lbs = IGD('Local','Lbs'); 
        
        $where['c_ucode'] = 'T10005';
        $local = M('User_local')->where($where)->find();
     
        $re = $lbs->serach($local['c_latitude'], $local['c_longitude'],1);
        dump($re);
    }


    public function csdb()
    {
        $money = intval(bcmul(17.90,100,2));
        dump($money);
        // $list = M('goods')->select();
        // dump($list);
    }

    //通连代付
    public function df(){
        vendor('TongLianPay.libs.ArrayXml');
        vendor('TongLianPay.libs.cURL');
        vendor('TongLianPay.libs.PhpTools');
        $tools = new \PhpTools();
        $times = time();
        $str = time();
        $subtime = preg_replace('/\-*\:*\s*/', '', date('Y-m-d H:i:s'));
       $money = 10;
        // 源数组
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '100002',
                'VERSION' => '03',
                'DATA_TYPE' => '2',
                'LEVEL' => '5',
                'USER_NAME' => '20055100001429404',
                'USER_PASS' => '111111',
                'REQ_SN' => '200551000014294-'.$str,
            ),
            'BODY' => array(
                'TRANS_SUM' => array(
                    'BUSINESS_CODE' => '09900',
                    'MERCHANT_ID' => '200551000014294',
                    'SUBMIT_TIME' => $subtime,
                    'TOTAL_ITEM' => '1',
                    'TOTAL_SUM' => $money,
                    'SETTDAY' => '',
                 ),
                'TRANS_DETAILS'=> array(
                      'TRANS_DETAIL'=> array(
                            'SN' => $str,
                            'E_USER_CODE'=> '',
                            'BANK_CODE'=> '',
                            'ACCOUNT_TYPE'=> '00',
                            'ACCOUNT_NO'=> '6236682920010889556',
                            'ACCOUNT_NAME'=> '李秋艳',  
                            'PROVINCE'=> '湖南',
                            'CITY'=> '长沙',
                            'BANK_NAME'=> '中国建设银行',
                            'ACCOUNT_PROP'=> '0',
                            'AMOUNT'=> $money,
                            'CURRENCY'=> 'CNY',
                            'PROTOCOL'=> '',
                            'PROTOCOL_USERID'=> '',
                            'ID_TYPE'=> '',
                            'ID'=> '',
                            'TEL'=> '',
                            'CUST_USERID'=> '',
                            'REMARK'=> '小蜜送钱到家',
                            'SETTACCT'=> '',
                            'SETTGROUPFLAG'=> '',
                            'SUMMARY'=> '',
                            'UNION_BANK'=> '010538987654',
                        )
                 )
            ),
        );
        
        //发起请求
        $result = $tools->send($params);
        dump($result);


    }




}