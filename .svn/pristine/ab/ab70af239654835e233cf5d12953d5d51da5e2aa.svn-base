<?php 
use com\cskj\pay\demo\common\ConfigUtil;
include '../common/ConfigUtil.php';
error_reporting(0);
date_default_timezone_set("PRC");
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8" />
<title>模拟商户--订单退款页面</title>
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
</head>
</head>
<body>

	<form method="post" action="../action/RefundOrder.php" id="paySignForm">
		<div class="content">
			<div class="content_1">
				<ul class="form-wrap" id="J-form-wrap">

					<label>交易类型-tradeType:</label>
					<input type="text" name="tradeType" value="cs.refund.apply" maxlength="32" /> <br/>
			
					<label>接口版本-version:</label>
					<input type="text" name="version" value="1.0" maxlength="8" /><br/>

					<label>商户号-mchId:</label>
					<input type="text" name="mchId"  value="<?php echo ConfigUtil::get_val_by_key('merchantNum');?>" maxlength="32" /><br/>

					<label>渠道类型-channel:</label>
					<select id="channel" onchange="changeChannel(this)" name="channel">	
					    <option value="0">---请选择---</option>
						<option value="wxPub">微信公众账号支付</option>
						<option value="wxPubQR">微信公众账号扫码支付</option>
						<option value="wxApp">微信app支付</option>
						<option value="wxMicro">微信付款码支付</option>
						<option value="jdPay">京东支付</option>
						<option value="jdPayGate">京东网关</option>
						<option value="jdMicro">京东付款码支付</option>
						<option value="jdQR">京东扫码支付</option>
					</select><br/>

					<label>商户订单号-outTradeNo:</label>
					<input type="text" name="outTradeNo" value="" placeholder="请输入商户订单号" maxlength="32" /><br/>

					<label>商户退款单号-outRefundNo:</label>
					<input type="text" name="outRefundNo" value=""  placeholder="请输入商户退款单号" maxlength="32" /><br/>

					<label>退款金额-amount:</label>
					<input type="text" name="amount" value=""  placeholder="请输入退款金额" /><br/>

					<label>退款详情-description:</label>
					<input type="text" name="description" value=""  placeholder="请输入退款详情" /><br/>
					
					<div id="wxPub" style="display:none">
						<label>指定支付方式-limitPay:</label>
						<input type="text" name="limitPay" value=""> <br/>
						
						<label>openId:</label>
						<input type="text" name="openId" value=""> <br/>
						
						<label>结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
						
						<label>商品标记-goodsTag:</label>
						<input type="text" name="goodsTag" value=""> <br/>
					</div>
						
					<div id="wxPubQR" style="display:none">
						<label>指定支付方式-limitPay:</label>
						<input type="text" name="limitPay" value=""> <br/>
						
						<label>商品id-productId:</label>
						<input type="text" name="productId" value=""> <br/>
						
						<label>结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
						
						<label>商品标记-goodsTag:</label>
						<input type="text" name="goodsTag" value=""> <br/>
					</div>
					
					<div id="wxApp" style="display:none">
						<label>指定支付方式-limitPay:</label>
						<input type="text" name="limitPay" value=""> <br/>
						
						<label>结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
						
						<label>商品标记-goodsTag:</label>
						<input type="text" name="goodsTag" value=""> <br/>
					</div>
					
					<div id="wxMicro" style="display:none">
						<label>授权码-authCode:</label>
						<input type="text" name="limitPay" value=""> <br/>
						
						<label>结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
						
						<label>商品标记-goodsTag:</label>
						<input type="text" name="goodsTag" value=""> <br/>
					</div>
					
					<div id="jdPay" style="display:none">
						<label>支付成功跳转路径url-callbackUrl:</label>
						<input type="text" name="callbackUrl" value=""> <br/>
						
						<label>支付完成后结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
					</div>
					
					<div id="jdPayGate" style="display:none">
						<label>支付成功跳转路径url-callbackUrl:</label>
						<input type="text" name="callbackUrl" value=""> <br/>
						
						<label>支付完成后结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
					</div>
					
					<div id="jdMicro" style="display:none">
						<label>支付完成后结果通知url-notifyUrl:</label>
						<input type="text" name="notifyUrl" value=""> <br/>
					</div>
					
					<div id="jdQR" style="display:none">
					</div>
					
					<input type="submit" value="退款申请" class="btn1">
				</ul>
			</div>
		</div>
	</form>
</body>
</html>
<script type="text/javascript">
 	function changeChannel(chan){
	    for(var i=1;i<chan.length;i++)
	    {
	    	var div = document.getElementById(chan.options[i].value);
	    	div.style.display="none";
	    } 
 		if(chan.value != 0){
 			document.getElementById(chan.value).style.display="";
 		}
	}
 </script>