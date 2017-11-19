<?php 
require_once("sdk/src/rest/config.php");
require_once("sdk/src/rest/network.php");
require_once("sdk/src/rest/api.php");

/**
* 长沙微领地网络支付入口
*/
class Pay3
{
	//商户号集合
    public $appidarr = array(
    	'2b335616-0085-4815-b6aa-eeaa3db3c50c',
    	'23a46ca4-6371-4e50-827b-9e16ce14dd53',
    	'b5fa6804-0067-44d3-8003-cd69c919f86f',
    	'607eceb9-592d-4056-8717-cbfcc644102e',
    	'caed6364-02be-4e0a-82e2-dc79aeae52c8',
    	'9d852479-d1cf-4c35-ba71-9c3bbe8af920',
    	'817f32c1-864d-4bdc-8f77-dd6058b8ad8f',
    	'8c1bc34d-4ccf-4cb9-a908-7fc1219e6f10',
    	'0bb86bef-8155-44ea-b42e-02850f0c5559',
    	'fc225b3c-25b2-4787-b017-d7a6f2be26ed',
    );

    //商户号对应参数
    public $appid_getkey = array(
        '2b335616-0085-4815-b6aa-eeaa3db3c50c'=>array('da9929c5-1cf2-4f68-9846-a63fb9acaf9b','780b23d9-2846-4b72-be0f-35fe02d5b3f6','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '23a46ca4-6371-4e50-827b-9e16ce14dd53'=>array('eb2bcc0b-d39d-4c2b-90d1-3cbaf418e043','dda66f03-27eb-43d1-b5f7-fb70a91fdcb8','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        'b5fa6804-0067-44d3-8003-cd69c919f86f'=>array('c69ff50f-d0da-4024-a588-8e72fa452ce3','34395209-de41-4c31-a31c-bc09bc289278','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '607eceb9-592d-4056-8717-cbfcc644102e'=>array('23696dc5-91a8-448e-9ad3-6c5be916a81a','eac6f5c4-6b78-4491-9119-e09538568c4b','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        'caed6364-02be-4e0a-82e2-dc79aeae52c8'=>array('8b3ec60d-c9dc-449f-ac58-7270d9003a41','f957d6c6-3473-497f-af33-b6ef20b110fc','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '9d852479-d1cf-4c35-ba71-9c3bbe8af920'=>array('d1d85d13-5fad-44f9-a926-3d3529bdb145','69b2f6e0-6ec8-48ef-b8e9-c566e58e4b4c','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '817f32c1-864d-4bdc-8f77-dd6058b8ad8f'=>array('722d31b1-d1e6-41fa-8251-5e0d0ffebf90','f10dae13-9b28-4c29-8aeb-09a207c1a9f5','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '8c1bc34d-4ccf-4cb9-a908-7fc1219e6f10'=>array('be5a8c50-7025-41af-ac87-1d6d0315ca8a','a54f437c-947d-4609-8731-f6f9c642ddad','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        '0bb86bef-8155-44ea-b42e-02850f0c5559'=>array('582334a0-3bb7-423f-b62a-e1116a637aa7','a2934cd3-cce9-4423-8c38-3984449b6983','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
        'fc225b3c-25b2-4787-b017-d7a6f2be26ed'=>array('7d24f1cf-c514-4c67-94a2-ec91c55e7ff6','920e1870-e3b7-4d10-b387-e95cd8789e90','4bfdd244-574d-4bf3-b034-0c751ed34fee'),
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
		$data["optional"] = array("app_id"=>$this->app_id,'wxptsign'=>$parr['wxptsign']);

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







