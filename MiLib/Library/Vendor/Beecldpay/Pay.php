<?php 
require_once("sdk/src/rest/config.php");
require_once("sdk/src/rest/network.php");
require_once("sdk/src/rest/api.php");

/**
* 支付入口
*/
class Pay
{
	//商户号集合
    public $appidarr = array(
    	'b38f73b7-0832-4472-b7c4-5e31e2b3f181',
    	'd0b12d77-b6a3-449f-8a6a-545b49b4e276',
    	'1c092d1d-d98c-4d48-90cd-70b8afea746c',
    	'2a4c3f52-2a49-4a4b-9ab8-ad0a3ac96089',
    	'12c9fde7-8d65-402b-9b83-d131b045781b',
    	'662785b9-e0e1-4eb9-af32-278093d3065a',
    	'3d7616a1-2f15-4300-a419-c844f66b7a4a',
    	'cc43bf47-59e4-4b10-a5c5-536908f0b692',
    	'6a16145e-2c92-4fe7-8a95-f7302b31d351',
    	'620f3fa2-9fe1-4bd6-b00a-112d2886eb3b',
        

        // '4b03fdbb-7ff1-4cb2-8411-b245e8368ec4',
        // 'd25aa661-404c-4eb4-93cc-32c92bdcd058',
        // '08008d20-8d28-41a7-bc0f-d872081ffbe8',
        // '60d5cd9d-a692-4463-ac94-daad402a6bb9',
        // 'b3dd1076-1bc8-443c-b6e9-b15a57db576f',
        // '150db23b-0678-43ce-904e-080115132ec1',
        // '6d374ac5-60da-48e9-b5a2-766c7f46cf01',
        // '2d8d5e88-1d8b-4db0-b0c6-965a638bc3f8',
        // 'ea7649c9-57e6-41d8-9315-8a9bc99d2396',
        // '5d953386-b3d7-456c-870f-ebb2d11441aa',
    );

    //商户号对应参数
    public $appid_getkey = array(
        'b38f73b7-0832-4472-b7c4-5e31e2b3f181'=>array('09cf0acb-e950-4518-9e41-cb74821512f6','eac384c7-d361-4d1b-b1dc-4a6a6dd80430','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        'd0b12d77-b6a3-449f-8a6a-545b49b4e276'=>array('97f3f84f-c7dd-4916-a364-56f23d2fe396','2f9d5ba8-2b4d-4141-b15d-bf01d895ebc7','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '1c092d1d-d98c-4d48-90cd-70b8afea746c'=>array('2f97aeaf-1e5f-4ac2-8ea9-7ff581a73da0','0cf80d28-fb82-4a2d-a0af-71f68e7db440','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '2a4c3f52-2a49-4a4b-9ab8-ad0a3ac96089'=>array('b6420ad7-d8f5-482b-9800-e66211afe50e','63f025f0-2fbf-4120-9b5a-4d096055caa2','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '12c9fde7-8d65-402b-9b83-d131b045781b'=>array('2c511e10-a60e-4ab5-84c5-4f8c960aba4f','4eee829c-1821-4fe9-b5b6-62e506aa4a33','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '662785b9-e0e1-4eb9-af32-278093d3065a'=>array('c3da0e3c-706e-4737-80e3-a17c7068f13b','92b62fb2-88a5-4879-8420-627a7fde793c','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '3d7616a1-2f15-4300-a419-c844f66b7a4a'=>array('4fbc5f3a-186a-4441-92f9-e47759818f56','26e34a1d-1136-4b4a-b197-fe6a53467581','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        'cc43bf47-59e4-4b10-a5c5-536908f0b692'=>array('0e495915-02e3-4752-b534-ef710b6c755d','a25f3aa2-afa8-48e3-b789-fc35b0932d15','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '6a16145e-2c92-4fe7-8a95-f7302b31d351'=>array('9384b686-032a-4898-8922-4b762849e014','d5d1259b-39e2-4736-bbb0-a94a5999c751','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '620f3fa2-9fe1-4bd6-b00a-112d2886eb3b'=>array('faa35b5b-482b-4309-bfad-b47e1a2ecee1','9a13dfbd-21fb-4a0a-bb33-bab9dea24433','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        


        // '4b03fdbb-7ff1-4cb2-8411-b245e8368ec4'=>array('9f22ae2d-4b18-4eb6-b3d0-d754e2e76ce9','a729167d-4c21-46ad-9825-d8298361a02c','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // 'd25aa661-404c-4eb4-93cc-32c92bdcd058'=>array('8f58c9c7-940a-40f0-a221-47deaa176434','2e3c2995-256e-4a4b-ab45-c066259161bb','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '08008d20-8d28-41a7-bc0f-d872081ffbe8'=>array('b6d664d9-b75a-43d3-9281-2106dc28506d','492744cf-52c8-4030-9ba8-681d3b9cefde','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '60d5cd9d-a692-4463-ac94-daad402a6bb9'=>array('600c707d-c3ab-40b1-9e8c-94aba756bf22','2e2c4a1d-ae4e-43b8-8461-37b53d252ab4','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // 'b3dd1076-1bc8-443c-b6e9-b15a57db576f'=>array('1b88268b-0c2b-471e-824a-8bc49e7f7f18','deed1808-e0c7-48bb-a353-5a81778129ba','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '150db23b-0678-43ce-904e-080115132ec1'=>array('27cce8bf-0fff-4d5f-8740-b71adabae191','5197a830-970d-4aa8-b1ad-8c8deffa848c','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '6d374ac5-60da-48e9-b5a2-766c7f46cf01'=>array('092fb5bd-c016-4e02-b044-14a3fc5cf484','f4ec9f0c-7ad1-443d-9b11-5c3fce054a17','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '2d8d5e88-1d8b-4db0-b0c6-965a638bc3f8'=>array('9065b6df-bcd2-48a4-9ac2-738541a26ddd','62192e93-4294-43f2-a8f1-951eb3a884eb','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // 'ea7649c9-57e6-41d8-9315-8a9bc99d2396'=>array('049f0632-63f3-42cb-b747-38f8c11162b3','f337abef-58aa-474b-ad35-d01cdac76c14','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        // '5d953386-b3d7-456c-870f-ebb2d11441aa'=>array('69407733-39b1-4524-b802-4b6337d638f2','cfa89110-1ba5-401b-bced-09935bd2cb41','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
    );

	public $app_id = '';
	//支付或者查询时使用
	public $app_secret = '';
	//退款或者打款时使用
	public $master_secret = '';
	//test_secret for sandbox
	public $test_secret = '';

	function __construct($app_id)
	{
		if (!empty($app_id)) {
            $this->app_id = $app_id;
            $this->app_secret = $this->appid_getkey[$app_id][0];
            $this->master_secret = $this->appid_getkey[$app_id][1];
            $this->test_secret = $this->appid_getkey[$app_id][2];
        } else {
        	$randshu = rand(0,(count($this->appidarr)-1));
	        $this->app_id = $this->appidarr[$randshu];
	        $this->app_secret = $this->appid_getkey[$this->app_id][0];
            $this->master_secret = $this->appid_getkey[$this->app_id][1];
            $this->test_secret = $this->appid_getkey[$this->app_id][2];
        }

		$this->api = new \beecloud\rest\api();
		$this->international = new \beecloud\rest\international();
		$this->subscription = new \beecloud\rest\Subscriptions();
		$this->auth = new \beecloud\rest\Auths();

		$this->api->registerApp($this->app_id, $this->app_secret, $this->master_secret, $this->test_secret);
	    $this->api->setSandbox(false);
	}

	/**
	 * 生成支付参数
	 * @param  total_fee,bill_no,title,(return_url),notify_url,openid,type
	 * @return [type]       [description]
	 */
	function createorderinfo($parr)
	{
		//total_fee(int 类型) 单位分
		$data["total_fee"] = $parr['total_fee'];
		$data["bill_no"] = $parr['bill_no'];
		//title UTF8编码格式，32个字节内，最长支持16个汉字
		$data["title"] = $parr['title'];
		//渠道类型:ALI_WEB 或 ALI_QRCODE 或 UN_WEB或JD_WAP或JD_WEB, BC_GATEWAY为京东、BC_WX_WAP、BC_ALI_WEB渠道时为必填, BC_ALI_WAP不支持此参数
		$data["return_url"] = $parr['return_url'];
		$data["notify_url"] = $parr['notify_url'];

		//选填 optional, 附加数据, eg: {"key1”:“value1”,“key2”:“value2”}
		//用户自定义的参数，将会在webhook通知中原样返回，该字段主要用于商户携带订单的自定义数据
		$data["optional"] = array("app_id"=>$this->app_id);

		$data["openid"] = $parr['openid'];
		$data["limit_credit"] = true;
		$type = $parr['type'];
		switch($type){
		    case 'ALI_WEB' :
		        $title = "支付宝即时到账";
		        $data["channel"] = "ALI_WEB";
		        break;
		    case 'ALI_WAP' :
		        $title = "支付宝移动网页";
		        $data["channel"] = "ALI_WAP";
		        //非必填参数,boolean型,是否使用APP支付,true使用,否则不使用
		        //$data["use_app"] = false;
		        break;
		    case 'WX_JSAPI':
		        $data["channel"] = "BC_WX_JSAPI";
		        $title = "微信公众号";
		        break;
		    default :
		        exit("No this type.");
		        break;
		}

		$result = $this->api->bill($data);
		return $result;
	}

	/**
	 * 查询订单信息
	 * @param  bill_no
	 * @return [type]       [description]
	 */
	function query_order($parr)
	{
		$data["bill_no"] = $parr['bill_no'];
		$result = $this->api->bills($data);
		return $result;
	}

	/**
	 * 支付异步回调签名校验
	 * @param string $value [description]
	 */
	public function CheckSign($msg)
	{
		$sign = md5($this->app_id . $msg->transaction_id . $msg->transaction_type . $msg->channel_type . $msg->transaction_fee.$this->master_secret);
		if ($sign != $msg->signature) {
		    // 签名不正确
		    return false;
		}
		return true;
	}

}


?>







