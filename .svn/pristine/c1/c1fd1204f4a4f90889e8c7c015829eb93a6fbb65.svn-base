<?php 
use com\cskj\pay\demo\common\ConfigUtil;
include '../common/ConfigUtil.php';

error_reporting(0);

date_default_timezone_set("PRC");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<link rel="stylesheet" type="text/css"
	href="../../../../../css/main.css">
    <title>demo create order</title>

    </head>
    <body>
    <form action="../action/ClientOrder1.php" method="post" target="_blank">
    <div class="content">
    <div class="content_0">
    <label>交易类型-tradeType:</label>
    <input type="txt" name="returnCode" value="0"> <br/>

    <label>版本-version:</label>
    <input type="txt" name="resultCode" value="0"> <br/>

    <label>商户号-mchId:</label>
    <input type="txt" name="sign" value="550BE3CEE2AD6F3D921E839D33B1B588"> <br/>

    <label>商品描述-body:</label>
    <input type="txt" name="status" value="04"> <br/>

    <label>商户订单号-outTradeNo:</label>
    <input type="txt" name="channel" value="wxPub"> <br/>

    <label>交易金额-amount:</label>
    <input type="txt" name="body" value="购买商品"> <br/>

    <label>附加数据-description:</label>
    <input type="txt" name="outTradeNo" value="201609291428437"> <br/>

    <label>货币类型-currency:</label>
    <input type="txt" name="amount" value="0.01"> <br/>

    <label>订单支付时间-timePaid:</label>
    <input type="txt" name="currency" value="CNY"> <br/>

    <label>订单失效时间-timeExpire:</label>
    <input type="txt" name="transTime" value="20160929142845"> <br/>

    <label>商品的标题-subject:</label>
    <input type="txt" name="payChannelType" value="weixin"> <br/>

    <input type="submit" value="下单" id="showlayerButton" class="btn1">
    </div>
    </div>
    </form>

    </body>
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

    </html>

