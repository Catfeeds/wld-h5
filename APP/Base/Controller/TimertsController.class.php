<?php
namespace Base\Controller;

use Think\Controller;

/**
 * 定时器访问控制器
 */
class TimertsController extends Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
    }

    //总执行入口
    public function index()
    {
    	$this->CheckAdvert();
    	$this->CheckGroupbuyOrder();
    	$this->CheckGroupbuy();
    	$this->RecallShopRed();
        $this->RecallGoods();
        $this->CheckActivity();
        $this->TimersCheckSign();
        $this->ChangceRed();
    }

    //矫正扫码系统账目
    public function CheckIncomeDiff()
    {
        $date = I('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $star = $date.' 00:00:00';
        $end = $date.' 23:59:59';
        if (!strtotime($date)) {
            $this->ajaxReturn(3000,'时间格式错误');
        }

        $db = M('');

        $sql1 = "select * from (select *,count(c_ucode) as ct FROM t_users_moneydate WHERE c_type=2 and c_sign=1 and " 
              . "c_datetime>='$star' and c_datetime<='$end' GROUP BY c_ucode) as t where t.ct>1";

        $data1 = $db->query($sql1);
        if (empty($data1)) {
            $this->ajaxReturn(Message(3001,'没有错误记录'));
        }
        
        foreach ($data1 as $key => $value) {            

            $ucode = $value['c_ucode'];

            //获得正确金额
            $sql2 = "select sum(c_money) as tmoney from t_users_moneylog where c_ucode='$ucode' and c_source=9 and "
                  . "(c_addtime BETWEEN  '$star' and '$end') and c_money>0";

            $data2 = $db->query($sql2);
            $tmoney = $data2[0]['tmoney'];

            //查询个人重复记录数据
            $sql3 = "select * from t_users_moneydate where c_ucode='$ucode' and c_sign=1 and "
                  . "c_datetime>='$star' and c_datetime<='$end' and c_type=2";
            $data3 = $db->query($sql3);
            foreach ($data3 as $k => $v) {
                $ow['c_id'] = $v['c_id'];
                if ($v['c_id'] == $value['c_id']) {
                    //修改金额
                    $osave['c_money'] = $tmoney;
                    $result = M('Users_moneydate')->where($ow)->save($osave);
                } else {
                    //删除记录
                    $result = M('Users_moneydate')->where($ow)->delete();
                }
            }
        }

        $this->ajaxReturn(Message(0,'同步成功'));
    }

    //矫正红包系统账目
    public function CheckRedDiff()
    {
        $date = I('date');
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        
        $star = $date.' 00:00:00';
        $end = $date.' 23:59:59';
        if (!strtotime($date)) {
            $this->ajaxReturn(3000,'时间格式错误');
        }

        $db = M('');

        $sql1 = "select * from (select *,count(c_ucode) as ct FROM t_users_moneydate WHERE c_type=4 and c_sign=1 and " 
              . "c_datetime>='$star' and c_datetime<='$end' GROUP BY c_ucode) as t where t.ct>1";

        $data1 = $db->query($sql1);
        if (empty($data1)) {
            $this->ajaxReturn(Message(3001,'没有错误记录'));
        }
        
        foreach ($data1 as $key => $value) {            

            $ucode = $value['c_ucode'];

            //获得正确金额
            $sql2 = "select sum(c_money) as tmoney from t_users_moneylog where c_ucode='$ucode' and c_source=18 and "
                  . "(c_addtime BETWEEN  '$star' and '$end') and c_money>0";

            $data2 = $db->query($sql2);
            $tmoney = $data2[0]['tmoney'];

            //查询个人重复记录数据
            $sql3 = "select * from t_users_moneydate where c_ucode='$ucode' and c_sign=1 and "
                  . "c_datetime>='$star' and c_datetime<='$end' and c_type=4";
            $data3 = $db->query($sql3);
            foreach ($data3 as $k => $v) {
                $ow['c_id'] = $v['c_id'];
                if ($v['c_id'] == $value['c_id']) {
                    //修改金额
                    $osave['c_money'] = $tmoney;
                    $result = M('Users_moneydate')->where($ow)->save($osave);
                } else {
                    //删除记录
                    $result = M('Users_moneydate')->where($ow)->delete();
                }
            }
        }

        $this->ajaxReturn(Message(0,'同步成功'));
    }

    //检测广告位过期
    public function CheckAdvert()
    {
    	$result = IGD('Advert','Newact')->CheckAdvert();
    	dump($result);
    }

    //检测拼团订单失败退款
    public function CheckGroupbuyOrder()
    {
    	$result = IGD('Groupbuy','Newact')->CheckGroupbuyOrder();
    	dump($result);
    }

    //检测所有活动产品到期返回库存
    public function CheckGroupbuy()
    {
    	$result = IGD('Groupbuy','Newact')->CheckGroupbuy();
    	dump($result);
    }

    //返回店铺红包24小时红包
    public function RecallShopRed()
    {
    	$result = IGD('Red','Newact')->RecallShopRed();
    	dump($result);
    }

    //定时器检测领取时间过期放弃领取
    public function RecallGoods()
    {
        $result = IGD('Exchange','Newact')->RecallGoods();
        dump($result);
    }

    //定时器检测活动改变热门状态
    public function CheckActivity()
    {
        $result = IGD('Airbox','Newact')->CheckActivity();
        dump($result);
    }

    //定时器检测超过凌晨12点，结束上班
    public function TimersCheckSign()
    {
        $result = IGD('Cashier','User')->TimersCheckSign();
        dump($result);
    }

    public function ChangceRed()
    {
        $result = IGD('Red','Newact')->ChangceRed();
        dump($result);
    }

    //代付金额
    public function dfoption()
    {
        //按月结算
        $result = IGD('Splitting','Order')->SettlementByMonth();
        dump($result);
    }

    //代付金额
    public function dfoption1()
    {
        //按天结算
        $result = IGD('Splitting','Order')->SettlementByDate();
        dump($result);
    }

    //代付金额
    public function dfoption2()
    {
        //按分结算
        $result = IGD('Splitting','Order')->SettlementBySecond();
        dump($result);
    }

    //代付金额
    public function dkoption()
    {
        //按月结算
        $result = IGD('Splitting','Order')->KouSettlementByMonth();
        dump($result);
    }

    //代付金额
    public function dkoption1()
    {
        //按天结算
        $result = IGD('Splitting','Order')->KouSettlementByDate();
        dump($result);
    }

    //代付金额
    public function dkoption2()
    {
        //按分结算
        $result = IGD('Splitting','Order')->KouSettlementBySecond();
        dump($result);
    }

    //定时器还原结算款到可提现金额
    public function BackStmoney() {
        $result = IGD('Money','User')->BackStmoney();
        dump($result);
    }
}
