<?php 
use com\cskj\pay\demo\common\ConfigUtil;
include '../common/ConfigUtil.php';
error_reporting(0);
?>
<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
<title>交易查询</title>
</head>
<body>
	<div class="content">
		<div class="content_0">
			<div class="content_1">
				<form method="post" action="../action/QueryOrder.php"
					id="queryTradeForm">

					<ul class="form-wrap" id="J-form-wrap">
								
						<label>交易类型-tradeType:</label>
				<input type="text"  name="tradeType" value="cs.trade.single.query" maxlength="32" /> <br/>
				
				<label>接口版本-version:</label>
				<input type="text"  name="version" value="1.0" maxlength="8" /><br/>
				
				<label>商户号-mchId:</label>
				<input type="text"  name="mchId" value="<?php echo ConfigUtil::get_val_by_key('merchantNum');?>" maxlength="32" /><br/>
				
				<label>商户订单号-outTradeNo:</label>
				<input type="text"  name="outTradeNo" value="" placeholder="请输入商户订单号" maxlength="32" /><br/>
				
				<label>商户原交易订单号-oriTradeNo:</label>
				<input type="text"  name="oriTradeNo" value="" placeholder="请输入商户原交易订单号" maxlength="32" /><br/>

				<label>查询类型:</label>
				<select id="queryType" name="queryType">	
				    <option value="0">-请选择-</option>
					<option value="1">订单查询</option>
					<option value="3">退款查询</option>
				</select><br/>

				<input type="submit" value="查询" class="btn1"><br/>
					</ul>
				</form>
			</div>
		</div>
	</div>
</body>
</html>