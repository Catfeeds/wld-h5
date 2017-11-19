<?php

namespace Balance\Controller;

//use Think\Controller;
use Base\Controller\BaseController;

/**
 * 结算中心模块
 */
class IndexController extends BaseController {
	
  	// 首页
    public function index()
    {
        $ucode = session('USER.ucode');
        $resultuser = IGD('Login','Login')->GetUserByCode($ucode);
        if ($resultuser['data']['c_shop'] == 1) {
            $this->redirect('Balance/Index/sjindex');die;
        }

        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Settlement','User')->Summary($parr);
        $this->data = $result['data'];

        $param['ucode'] = session('USER.ucode');
        $bindinfo = IGD('Settlement','User')->Getbank($parr);
        $this->bindinfo = $bindinfo['data'];

        /*判断是否开户*/
        $parr2['ucode'] = session('USER.ucode');
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr2);
        $this->ysstate = $result['code'];
        
    	$this->show();
    }

    //记账本
    public function tallybook()
    {
        for ($i=0; $i < 5; $i++) {
            $datearr[$i]['name'] = date('Y年m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date'] = date('Y-m',strtotime('-'.($i+1).' months',time()));
        }

        $this->monthn = date('Y-m');/*当前月*/
        $this->datearr = $datearr;
        $this->show();
    }

    //查询扫码支付月统计账单
    public function GetdataTally()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['time'] = I('time');
        $parr['type'] = I('type');
        $result = IGD('Settlement','User')->GetdataTally($parr);
        $this->ajaxReturn($result);
    }

    //记账详情
    public function tallydetail()
    {
        $this->money = I('money');
        $this->time = I('time');
        $this->num = I('num');
        if ($this->time == date('Y-m-d')) {
            $this->showtime = '今日';
        } else if ($this->time == date('Y-m-d',strtotime('-1 days'))) {
            $this->showtime = '昨日';
        } else {
            $this->showtime = date('m月d号',strtotime($this->time));
        }
        
        //获取当日记账本详情
        $parr['ucode'] = session('USER.ucode');
        $parr['datetime'] = $this->time;
        $result = IGD('Settlement','User')->CountdateMoney($parr);
        $this->bookinfo = $result['data'];

        $this->show();
    }
    
    /*账户类型账单详情*/
    public function ledgerdetail(){
	   	$this->time = I('time');
        $this->type = I('type');
	   	$this->money = I('money');
	   	$this->show();
    }
    
    /*账户类型每日账单列表*/
   //public function 

    public function GetdateLog()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pagesize'] = 20;
        $parr['pageindex'] = I('pageindex');
        $parr['datetime'] = I('time');
        $parr['type'] = I('type');
        $result = IGD('Settlement','User')->GetdateLog($parr);
        $this->ajaxReturn($result);
    }

    // 交易进行中
    public function tradeing()
    {
        $this->show();
    }

    public function GetTradeList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pagesize'] = 20;
        $parr['pageindex'] = I('pageindex');
        $statu = I('statu');
        if ($statu == 0) {
            $parr['type'] = 1;
            $result = IGD('Settlement','User')->Goodspayment($parr);
        } else if ($statu == 1) {
            $result = IGD('Settlement','User')->Buyrebate($parr);
        } else if ($statu == 2) {
            $result = IGD('Settlement','User')->Spreadrebate($parr);
        }

        $this->ajaxReturn($result);
    }

    // 提现记录
    public function drawinglog()
    {
        $ucode = session('USER.ucode');
        $this->money = IGD('Settlement','User')->Getdrawing($ucode);
        $this->show();
    }

    //查看提现记录
    public function GetdrawingList()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['pagesize'] = 20;
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Settlement','User')->GetdrawingList($parr);
        $this->ajaxReturn($result);
    }

    // 收支记录
    public function budget()
    {
        $this->statu = I('statu');
        $ucode = session('USER.ucode');
        $result = IGD('Login','Login')->GetUserByCode($ucode);
        $this->isshop = $result['data']['c_shop'];

        $this->dtime = I('dtime');
        $this->dtype = I('dtype');
        $this->typename = I('typename');

        $this->show();
    }

    //查询收支记录
    public function GetMoneyLog() {
        $parr['ucode'] = session('USER.ucode');
        $parr['type'] = I('type');
        $parr['pagesize'] = 20;
        $parr['c_time'] = I('dtime');
        $parr['source'] = I('dtype');
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Settlement','User')->GetMoneyLog($parr);
        $this->ajaxReturn($result);
    }

    //绑定银行卡
    public function bindcard()
    {
        if (IS_AJAX) {
            $attrbul = I('attrbul');
            $attrbul = str_replace('&quot;', '"', $attrbul);
            $data = objarray_to_array(json_decode($attrbul));

            $parr['ucode'] = session('USER.ucode');
            $parr['carid'] = $data['carid'];
            $parr['bankname'] = $data['bankname'];
            $parr['uname'] = $data['uname'];
            $parr['banksn'] = preg_replace("/\s/","",$data['account']);
            $result = IGD('Settlement','User')->bindingbank($parr);
            $this->ajaxReturn($result);
        }
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];
        $this->show();
    }

    //申请提现
    public function drawmoney()
    {
        if (IS_AJAX) {
            $attrbul = I('attrbul');
            $attrbul = str_replace('&quot;', '"', $attrbul);
            $data = objarray_to_array(json_decode($attrbul));

            $parr['ucode'] = session('USER.ucode');
            $parr['money'] = $data['mymoney'];
            $parr['pwd'] = $data['pwd'];
            $result = IGD('Settlement','User')->drawing($parr);
            $this->ajaxReturn($result);
        }
        $ucode = session('USER.ucode');
        $parr['ucode'] = $ucode;
        $data = IGD('Settlement','User')->Getbank($parr);
        if (!$data['data']) {
            $this->success('请先绑定银行卡再操作','index');die;
        }
        $data['data']['c_banksn'] = mb_substr($data['data']['c_banksn'], 0, 3, 'utf-8') . "*****" . mb_substr($data['data']['c_banksn'], -4, 4, 'utf-8');
        $this->info = $data['data'];
        $this->balance = IGD('Balance', 'User')->GetBalance($ucode);
        $this->show();
    }

    /**
     * 实名认证页面
     *
     */
    public function identify()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];

        $this->sign = I('sign');
        $this->show();
    }

    /**
     * 收款方式页面
     *
     */
    public function cashway()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];
        $this->show();
    }

    /**
     * 绑定支付宝页面
     */
    public function bindingap()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];
        $this->show();
    }

    /**
     * 绑定银行卡页面
     */
    public function bindingup()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];

        $parrg['parentid'] = 1;
        $parrg['regiontype'] = 1;
        $region = IGD('User', 'User');
        $province_list = $region->GetAddress($parrg);
        $this->province = $province_list;

        $this->show();
    }

    public function getRegion() {
        $parr['parentid'] = I('parentid');
        $parr['regiontype'] = I('regiontype');
        $region = IGD('Getbusiness', 'User');
        $result = $region->GetAddress($parr);
        $this->ajaxReturn($result);
    }


    /**
     * 绑定微信页面
     */
    public function bindingwx()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        // if ($data['data']['iswx_auth']) {
        //     $this->success('请先在VIP小蜜微信公众平台上完成小蜜授权操作','bindwxintro');die;
        // }
        $this->info = $data['data'];
        $this->show();
    }

    /**
     * 绑定微信流程介绍页面
     */
    public function bindwxintro()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];
        $this->show();
    }

    /**
     * 提现申请提交完成页面
     */
    public function successes()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];

        $this->sign = I('sign');

        $this->mymoney = I('mymoney');


        /*判断是否开户*/
        $parr2['ucode'] = session('USER.ucode');
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr2);
        $this->ysstate = $result['code'];

        $this->show();
    }

    /**
     * 提现操作页面
     */
    public function withdraw()
    {
        $parr['ucode'] = session('USER.ucode');
        $data = IGD('Settlement','User')->Getbank($parr);
        $this->info = $data['data'];


        $this->yesinfo = IGD('Balance', 'User')->GetSyncYesMoney($parr)['data'];

        /*余额获取*/
        $ucode = session('USER.ucode');
        $this->balance = IGD('Balance', 'User')->GetBalance($ucode);

        /*已提现总额*/
        $parr['sign'] = I('sign');
        $this->mytxmoney = IGD('Settlement','User')->drawnowzong($parr);
        /*提现类型*/
        $this->sign = I('sign');

        /*获取提现安全密码*/
        $ucode = session('USER.ucode');
        $userinfo = IGD('Login','Login')->GetUserByCode($ucode);
        $userdata = $userinfo['data'];
        $this->safepwd = $userdata['c_safepwd'];

        /*判断是否开户*/
        $parr2['ucode'] = session('USER.ucode');
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr2);
        $this->ysstate = $result['code'];

        $this->show();
    }

    // 筛选
    public function screen()
    {
        $this->show();
    }

    /**
     *  绑定身份证
     *  @param ucode,carid,uname
     */
    public function bindidcard()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['carid'] = I('carid');
        $parr['uname'] = I('uname');
        $result = IGD('Settlement','User')->bindidcard($parr);
        $this->ajaxReturn($result);
    }


    /**
     * 绑定支付宝
     * @param  ucode,alipayname,alipaycard
     */
    public function bindzfbbank()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['alipayname'] = I('alipayname');
        $parr['alipaycard'] = I('alipaycard');
        $result = IGD('Settlement','User')->bindzfbbank($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 绑定微信
     * @param  ucode,wxname,wxcard
     */
    public function bindwxbank()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['wxname'] = I('wxname');
        $parr['wxcard'] = I('wxcard');
        $result = IGD('Settlement','User')->bindwxbank($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 绑定银行卡
     * @param  ucode,bankname,banksn
     */
    public function bindingbank()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['bankname'] = $data['bankname'];
        $parr['sub_bankname'] = $data['sub_bankname'];
        $parr['province'] = $data['provincename'];
        $parr['city'] = $data['cityname'];
        $parr['banksn'] = preg_replace("/\s/","",$data['banksn']);
        $result = IGD('Settlement','User')->bindingbank($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 提现申请
     * @param ucode,money,pwd,sign(1银行卡,2微信,3支付宝)
     */
    //ucode,money,pwd(银行卡id)
    public function withdrawing()
    {
        $attrbul = I('attrbul');
        $attrbul = str_replace('&quot;', '"', $attrbul);
        $data = objarray_to_array(json_decode($attrbul));

        $parr['ucode'] = session('USER.ucode');
        $parr['money'] = $data['mymoney'];
        $parr['sign'] = $data['sign'];
        $result = IGD('Settlement','User')->drawing($parr);
        $this->ajaxReturn($result);
    }

    /*2017-3-14-新增验证提现安全密码*/
    /*
     * 验证安全密码
     * ucode 用户编码
     * securitycode 用户输入的安全码
     **/
    public function checksecurity()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['securitycode'] = I('securitycode');
        $result = IGD('Security','User')->checksecurity($parr);
        $this->ajaxReturn($result);
    }

    /**
     * 商家结算中心首页
     */
    public function sjindex()
    {
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Settlement','User')->Summary($parr);
        $this->data = $result['data'];

        $param['ucode'] = session('USER.ucode');
        $bindinfo = IGD('Settlement','User')->Getbank($parr);
        $this->bindinfo = $bindinfo['data'];

        for ($i=0; $i < 5; $i++) {
            $datearr[$i]['name'] = date('Y年m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['moname'] = date('m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date'] = date('Y-m',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date1'] = date('Y-m-d',strtotime('-'.($i+1).' months',time()));
        }
        for ($i=0; $i < 7; $i++) {
            $dayarr[$i]['name'] = date('d号',strtotime('-'.($i+1).' day',time()));
            $dayarr[$i]['val'] = date('Y-m-d',strtotime('-'.($i+1).' day',time()));
        }
        $this->todayn = date('Y-m-d');/*每日*/
        $this->yestoday = $dayarr[0]['val'];/*昨日*/
        $this->endday = $dayarr[6]['val'];/*本周*/
        $this->todaymon = $datearr[0]['date1'];/*本月*/
        $this->date1 = $datearr[0]['date1'];

        $this->dayarr = $dayarr;
        $this->datearr = $datearr;

        $iftimes = date('Y-m-d',I('date'));
        $datein = I('date');
        if($datein &&  $iftimes != '1970-01-01') {
            $this->endtimes = date('Y-m-'.$this->days_in_month(date('Y-m',$datein)),$datein);
            $this->begintimes = date('Y-m-01',$datein);
            $this->txt = date('Y年m月',$datein);
        }

        /*判断是否开户*/
        $parr2['ucode'] = session('USER.ucode');
        $result = IGD('Ysepay','Scanpay')->GetYsedata($parr2);
        $this->ysstate = $result['code'];
        $this->show();
    }

    /*详情页传的时间*/
    public function getdetailtime()
    {
        $parr['ucode'] = session('USER.ucode');
        $parr['begintime'] = I('begintime');
        $parr['endtime'] = I('endtime');
        $parr['sign'] = I('sign');
        $result = IGD('Settlement','User')->timeslotincome($parr);
        $this->ajaxReturn($result);
    }

    //获取某年某月天数
    function days_in_month($date) {
        $datearr = explode('-', $date);
        $year = $datearr[0];
        $month = $datearr[1];
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }

    //每日分类型统计(分类型统计，主要用于饼状体)
    /*
     * ucode 用户编码
     * time 时间 格式2017-03-06
     * sign 1代表收入，2代表支出
     *      */
    public function dayincome() {
        $parr['ucode'] = session('USER.ucode');
        $parr['time'] = I('time');
        $parr['sign'] = I('sign');
        $result = IGD('Settlement','User')->dayincome($parr);
        $this->ajaxReturn($result);
    }

    //时间段收支出统计（总统计，不分类型）
    /* ucode 用户编码
     * begintime 开始时间 时间 格式2017-03-06
     * endtime  支出时间
     *  sign 1代表收入，2代表支出
     *  */
    public function timeslotexpenditure() {
        $parr['ucode'] = session('USER.ucode');
        $parr['begintime'] = I('begintime');
        $parr['endtime'] = date("Y-m-d");
        $parr['sign'] = I('sign');
        $result = IGD('Settlement','User')->timeslotexpenditure($parr);
        $this->ajaxReturn($result);
    }

    //时间段查询（分日期总统计。主要用户每日的折线图）
    /* ucode 用户编码
     * begintime 开始时间 时间 格式2017-03-06
     * endtime  支出时间
     * sign 1代表收入，2代表支出
     */
    public function broken() {
        $parr['ucode'] = session('USER.ucode');
        $parr['begintime'] = I('begintime');;
        $parr['endtime'] = date("Y-m-d");
        $parr['sign'] = I('sign');
        $result = IGD('Settlement','User')->broken($parr);
        $this->ajaxReturn($result);
    }

    //时间段收支统计(分类型统计，主要用于饼状体)
    /* begintime 开始时间 时间 格式2017-03-06
     * endtime  结束时间
     * sign 1代表收入，2代表支出
     */
    public function timeslotincome() {
        $begintime = I('begintime');
        if (count(explode('-', $begintime)) == 2) {
            $parr['begintime'] = $begintime.'-01';
            $parr['endtime'] = $begintime.'-'.$this->days_in_month($begintime);
        } else {
            $parr['begintime'] = I('begintime');
            $endtime = date("Y-m-d");
            if ($parr['begintime'] == $endtime) {
                $parr['endtime'] = $endtime;
            } else {
                $parr['endtime'] = date("Y-m-d",strtotime('-1 days',time()));
            }
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['sign'] = I('sign');
        $result = IGD('Settlement','User')->timeslotincome($parr);
        $this->ajaxReturn($result);
    }

    /*收支明细详情*/
    public function detail()
    {
        $this->apptype = I('apptype');
        if (!$this->apptype) {
            if (is_weixin()) {
                $this->apptype = 4;
            } else {
                $this->apptype = get_device_type();
            }
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('id');
        $result = IGD('Settlement','User')->template($parr);
        $budget = $result['data'];
        $source = $budget['c_source'];
        $parr['source'] = $source;
        $parr['key'] = $budget['c_key'];
        if ($source == 1) {  //普通商城订单(c_orderid)
            $this->showtype = 1;
            $result = IGD('Settlement','User')->ordertemplate($parr);
            if ($result['code'] != 0) {
                $result = IGD('Settlement','User')->details($parr);
            }
        } else if ($source == 5 || $source == 13 || $source == 14) { //普通商城优惠类(c_detailid)
            $this->showtype = 2;
            $result = IGD('Settlement','User')->details($parr);
        } else if ($source == 4) {  //小蜜商城订单(c_orderid)
            $this->showtype = 3;
            $result = IGD('Settlement','User')->supplierordert($parr);
        } else if ($source == 15) { //小蜜商城优惠类(c_detailid)
            $this->showtype = 4;
            $result = IGD('Settlement','User')->supplierdetails($parr);
        } else if ($source == 9 || $source == 12) {   //扫码订单
            $this->showtype = 5;
            $result = IGD('Settlement','User')->scanpaytemplate($parr);
        } else if ($source == 6) {    //提现
            $this->showtype = 6;
            $result = IGD('Settlement','User')->tixiantemplate($parr);
        } else if ($source == 16) {  //普通商城退款
            $this->showtype = 7;
            $result = IGD('Settlement','User')->orderrefundinfor($parr);
        } else if ($source == 17) {  //小蜜商城退款
            $this->showtype = 8;
            $result = IGD('Settlement','User')->supplierorderrefundinfor($parr);
        } else if ($source == 2) {   //后台
            $this->showtype = 9;
        } else {   //活动
            $this->showtype = 10;
            $parr['joinaid'] = $budget['c_joinaid'];
            $result = IGD('Settlement','User')->findActivty($parr);
        }
        $this->source = $source;
        $this->budget = $budget;
        $this->data = $result['data'];
        // var_dump($this->showtype);
        // var_dump($source);var_dump($this->data);
        $this->show();
    }
    
    /*解绑银行卡*/
   public function RelieveBank(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Settlement','User')->RelieveBank($parr);
        $this->ajaxReturn($result);
   }

    // 月账单
    public function monthbill()
    {
        // var_dump(2222);die;
        for ($i=0; $i < 5; $i++) {
            $datearr[$i]['name'] = date('Y年m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['moname'] = date('m月',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date'] = date('Y-m',strtotime('-'.($i+1).' months',time()));
            $datearr[$i]['date1'] = date('Y-m-d',strtotime('-'.($i+1).' months',time()));
        }
        for ($i=0; $i < 7; $i++) {
            $dayarr[$i]['name'] = date('d号',strtotime('-'.($i+1).' day',time()));
            $dayarr[$i]['val'] = date('Y-m-d',strtotime('-'.($i+1).' day',time()));
        }
        $this->todayn = date('Y-m-d');/*每日*/
        $this->yestoday = $dayarr[0]['val'];/*昨日*/
        $this->endday = $dayarr[6]['val'];/*本周*/
        $this->todaymon = $datearr[0]['date'];/*本月*/
        $this->tmonname = $datearr[0]['moname'];/*本月name*/
        $this->date1 = $datearr[0]['date1'];

        $this->dayarr = $dayarr;
        $this->datearr = $datearr;
        /*收支明细页面传值查看月账单*/
        $iftimes = date('Y-m-d',I('date'));
        $datein = I('date');
        if($datein &&  $iftimes != '1970-01-01') {
            $this->endtimes = date('Y-m-'.$this->days_in_month(date('Y-m',$datein)),$datein);
            $this->begintimes = date('Y-m-01',$datein);
            $this->txt = date('Y-m',$datein);
            $this->txtmonth = date('m月',$datein);
        }
        $this->show();
    }


     /*收支明细详情*/
    public function incomepay()
    {
        $this->apptype = I('apptype');
        if (!$this->apptype) {
            if (is_weixin()) {
                $this->apptype = 4;
            } else {
                $this->apptype = get_device_type();
            }
        }
        $parr['ucode'] = session('USER.ucode');
        $parr['id'] = I('id');
        $result = IGD('Settlement','User')->template($parr);
        $budget = $result['data'];
        $source = $budget['c_source'];
        $parr['source'] = $source;
        $parr['key'] = $budget['c_key'];
        if ($source == 1) {  //普通商城订单(c_orderid)
            $this->showtype = 1;
            $result = IGD('Settlement','User')->ordertemplate($parr);
            if ($result['code'] != 0) {
                $result = IGD('Settlement','User')->details($parr);
            }
        } else if ($source == 5 || $source == 13 || $source == 14) { //普通商城优惠类(c_detailid)
            $this->showtype = 2;
            $result = IGD('Settlement','User')->details($parr);
        } else if ($source == 4) {  //小蜜商城订单(c_orderid)
            $this->showtype = 3;
            $result = IGD('Settlement','User')->supplierordert($parr);
        } else if ($source == 15) { //小蜜商城优惠类(c_detailid)
            $this->showtype = 4;
            $result = IGD('Settlement','User')->supplierdetails($parr);
        } else if ($source == 9 || $source == 12) {   //扫码订单 
            $this->showtype = 5;
            $result = IGD('Settlement','User')->scanpaytemplate($parr);
        } else if ($source == 6) {    //提现
            $this->showtype = 6;
            $result = IGD('Settlement','User')->tixiantemplate($parr);
        } else if ($source == 16) {  //普通商城退款
            $this->showtype = 7;
            $result = IGD('Settlement','User')->orderrefundinfor($parr);
        } else if ($source == 17) {  //小蜜商城退款
            $this->showtype = 8;
            $result = IGD('Settlement','User')->supplierorderrefundinfor($parr);
        } else if ($source == 2) {   //后台
            $this->showtype = 9;
        } else {   //活动
            $this->showtype = 10;
            $parr['joinaid'] = $budget['c_joinaid'];
            $result = IGD('Settlement','User')->findActivty($parr);
        }
        $s_time = date('Y-m-d H:i:s');
        $result['data']['s_time'] = $s_time;
        $result['data']['ucode'] =  $parr['ucode'];
        $this->source = $source;
        $this->budget = $budget;
        $this->data = $result['data'];
        $this->show();
    }

    /*筛选页面*/
    public function filter(){
        $parr['ucode'] = session('USER.ucode');
        $result = IGD('Settlement','User')->GetMoneyLog($parr);

        $this->dtime = I('dtime');
        $this->dtype = I('dtype');
        $this->typename = I('typename');

        $this->show();
    }

    //20171114检查是否有安全码
    public function checkNo(){
        $ucode = session('USER.ucode');
        $parr['ucode'] =$ucode;
        $result =IGD('Security','User')->checkNum($parr);
        $this->ajaxReturn($result);
    }

    //20171114验证安全码
    public function validateNo(){
        $ucode = session('USER.ucode');
        $parr['ucode'] =$ucode;
        $parr['safepwd'] =I('safepwd');
        $result =IGD('Security','User')->validateNum($parr);
        $this->ajaxReturn($result);
    }

}