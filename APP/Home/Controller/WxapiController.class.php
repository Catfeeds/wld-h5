<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 微信服务号api接口
 */
class WxapiController extends Controller {
	private $wechat;
	private $ucode;
	private $openid;
    public function __construct() {
        parent::__construct();
        header('Content-Type:text/html; charset=utf-8');
        $this->wechat = new \Com\Wechat(C('WX_TOKEN'));
    }

    // 微信接入接口
	public function index()
    {
        /* 获取请求信息 */
        $data = $this->wechat->request();
        if($data && is_array($data)){
        	// 查询用户是否授权
            $this->openid = $data['FromUserName'];
            $parr['openid'] = $this->openid;
            $parr['type'] = 1;  //1微信授权
            $result = IGD('Impower','Login')->AuthSeacher($parr);
            $this->ucode = $result['data']['c_ucode'];

            //回复用户对应事件
            $this->Receiveweixin($data);
        }
    }

    //获取微信返回的信息
    private function Receiveweixin($data)
    {
        if (isset($data['Event'])) {
            switch ($data['Event']) {
            	case 'subscribe'://关注
            		$this->Attention($data);
            		break;
            	case 'unsubscribe'://取消关注
            		$this->CancelAttention($data);
            		break;
            	case 'CLICK'://菜单点击
            		$this->OtherEvent($data);
            		break;
            	case 'SCAN2'://语音

            		break;
            	case 'MASSSENDJOBFINISH'://位置

            		break;
            	default:
            		$this->OtherEvent($data);
            		break;
            }
        } else {
            return $this->replykeywords($data);
        }
    }

    // 关键字回复规则
    private function replykeywords($data)
    {
    	$parr['key'] = $data['Content'];
    	$parr['sign'] = 1;
    	$result = D('Wxapi','Service')->GetRuleMsg($parr);
    	if ($result['code'] == 0) {
    		$this->SendFunctiom($this->openid,$result['data']);
    	} else {
    		switch ($data['Content']) {
	            case '1':     //查看发送失败消息
	                $result = $this->GetWxmsgError($this->openid);
	                break;
	            default:
                    //触发多客服模式
                    if (strstr($data['Content'], "您好") || strstr($data['Content'], "你好")
                        || strstr($data['Content'], "在吗") || strstr($data['Content'], "有人吗")
                        || strstr($data['Content'], "客服")){
                        $result = $this->send($this->openid,'正在等待客服接入，您可以先反馈问题，我们将尽快给您回复。（在线客服工作时间：工作日9:00-17:30）','text');
                        $this->wechat->response('客服', 'transfer_customer_service');
                    } else {   //未应答回复
                        $parr['sign'] = 4;
                        $result = D('Wxapi','Service')->GetRuleMsg($parr);
                        if ($result['code'] == 0) {
                            $this->SendFunctiom($this->openid,$result['data']);
                        }
                    }
	                break;
	        }
    	}
    }

    //查看发送失败的消息
    public function GetWxmsgError($openid)
    {
    	$parr['openid'] = $openid;
        $result = D('Wxapi','Service')->GetWxmsgError($parr);
        if ($result['code'] == 0) {
        	foreach ($result['data'] as $key => $value) {
        		$this->SendFunctiom($value['c_openid'],$value,1);
        	}
    	} else {
    		$this->wechat->response('您没有未读消息','text');
    	}
    }

    //调用发送
    public function SendFunctiom($openid,$msg,$rule="0")
    {
    	$parrlog['mid'] = $msg['c_id'];
    	if ($rule == 0) {
    		//添加消息记录
	    	$parr['openid'] = $openid;
	    	$parr['msgcode'] = $msg['c_msgcode'];
	    	$parr['msg'] = $msg['c_text'];
	    	$parr['msgtype'] = $msg['c_type'];
	    	$result = D('Wxapi','Service')->AddSendMsglog($parr);
	    	if ($result['code'] == 0) {
	    		$rule = 1;
	    		$parrlog['mid'] = $result['data']['c_id'];
	    	}
    	}

    	if ($rule == 1) {
			switch ($msg['c_type']) {
				case 'text':
					$result = $this->send($openid,$msg['c_text'],'text');
					break;
				case 'news':
	    			$newsmsg['title'] = $msg['c_text'];
	                $newsmsg['description'] = $msg['c_desc'];
	                $newsmsg['url'] = $msg['c_url'];
	                $newsmsg['picurl'] = $msg['c_picurl'];
					$result = $this->send($openid,json_encode($newsmsg),'news');
					break;
				default:
					$result = $this->send($openid,$msg['c_text'],$msg['c_type']);
					break;
			}

			//发送消息成功与失败记录
			if ($result['errcode'] == 0) {
				$parrlog['state'] = 1;
				$parrlog['errcode'] = 0;
	           	$parrlog['errmsg'] = '发送成功';
	           	$result = D('Wxapi','Service')->Receivelog($parrlog);
	        } else {
	        	$parrlog['state'] = 2;
	           	$parrlog['errcode'] = $result['errcode'];
	           	$parrlog['errmsg'] = $result['errmsg'];
	           	$result = D('Wxapi','Service')->Receivelog($parrlog);
	        }
    	}
    }

    // 其他消息事件
    public function OtherEvent($data)
    {
        switch ($data['EventKey']) {
            case '客服':
                $result = $this->send($this->openid,'等待接入客服，请反馈您的问题，等待客服回复','text');
                $this->wechat->response('客服', 'transfer_customer_service');
                break;
            case '1':
                $this->historymsg($data);
                break;
            default:
                $this->kefu($data);
                break;
        }
    }

    public function kefu($data)
    {
    	$this->wechat->response('客服信息！','text');
    }

    //用户关注
    public function Attention($data)
    {
    	$parr['sign'] = 3;
    	if (!empty($this->ucode)) {
    		$parr['sign'] = 2;
    	}
    	$result = D('Wxapi','Service')->GetRuleMsg($parr);
    	if ($result['code'] == 0) {
    		$this->SendFunctiom($this->openid,$result['data']);
    	}
    }

    //取消关注
    public function CancelAttention($data)
    {
    	$this->wechat->response('已取消关注！','text');
    }

    /*
     * 给用户主动发送消息调用方法
     * news类消息 msg为数组 title、description、url
    */
    public function send($openid = "",$msg = "",$type = "")
    {
        if (empty($openid)) {
            $result['errcode'] = 1004;
            $result['errmsg'] = 'openid为空';
            return $result;
        }
        if ($type=='text') {
            $data = '{"touser":"' . $openid . '","msgtype":"text","text":{"content":"' . $msg . '"}}';
        } else if ($type=='news') {
            $msg = str_replace('&quot;', '"', $msg);
            $datajson = objarray_to_array(json_decode($msg));
            if ($datajson['title']) {
                $title = $datajson['title'];
                $description = $datajson['description'];
                $url = $datajson['url'];
                $PIC_URL = $datajson['picurl'];
                $data = '{"touser": "' . $openid . '","msgtype": "news","news": {"articles": [{"title": "' . $title . '", "description": "' . $description . '","url":"' . $url . '","picurl":"' . $PIC_URL. '"}]}}';
            } else {
                $data = '{"touser": "' . $openid . '","msgtype": "news","news": {"articles": [';
                foreach ($datajson as $key => $value) {
                    $title = $value['title'];
                    $description = $value['description'];
                    $url = $value['url'];
                    $PIC_URL = $value['picurl'];
                    if ($key == (count($datajson)-1)) {
                        $data .= '{"title": "' . $title . '", "description": "' . $description . '","url":"' . $url . '","picurl":"' . $PIC_URL. '"}';
                    } else {
                        $data .= '{"title": "' . $title . '", "description": "' . $description . '","url":"' . $url . '","picurl":"' . $PIC_URL. '"},';
                    }

                }
                $data .= ']}}';
            }

        } else if ($type=='image') {
            $data = '{"touser":"' . $openid . '","msgtype":"image","image":{"media_id":"' . $msg . '"}}';
        }
        $access_token = get_token();
        $result = curlPost('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $access_token, $data, 0);
        return $result;
    }

}
