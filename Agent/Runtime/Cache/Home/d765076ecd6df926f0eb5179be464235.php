<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no, email=no" />
<title>公告中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/mobile/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/mobile/index.css?v=1.2">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
  window.onload=function(){
    var nimgh = $('.notice-img').width();
    $('.notice-img').height(nimgh);
    topstyle();/*头部样式*/

    $('#return-top').hide();

    $('input[name="keys"]').focus(function() {
      $('#search').css("border",'solid 1px #46aafa');
      $('#keys_btn').css('background-image','url(/wldApp/Agent/Home/View/Public/images/mobile/s-02.png)');
    });
    $('input[name="keys"]').blur(function() {
      $('#search').css("border",'solid 1px #ccc');
      $('#keys_btn').css('background-image','url(/wldApp/Agent/Home/View/Public/images/mobile/s-03.png)');
    });

  }

</script>

</head>

<body>

<div class="wrap-page">
    <script type="text/javascript" src="/wldApp/Agent/Home/View/Public/js/fastclick.js"></script>
  <div class="page-top">
    <div class="page-top-l fl">
     <a href="javascript:history.go(-1)" id="return-top"> <img src="/wldApp/Agent/Home/View/Public/images/mobile/m-jt.png" alt=""></a>
    </div>
    <div class="page-top-c fl" id="page-top-c-t">
      公告
    </div>
    <div class="page-top-r fr">
      <a href="javascript:;" id="icolink">
        <img src="/wldApp/Agent/Home/View/Public/images/mobile/menu-ico-l.png" alt="">
      </a>
      <a href="javascript:;" id="icohover" style="display:none;">
        <img src="/wldApp/Agent/Home/View/Public/images/mobile/menu-ico-h.png" alt="">
      </a>
    </div>
  </div>

  <div class="m-menu-bigbg" style="background:rgba(0,0,0,0.3);width: 100%;height:100%;position: absolute;z-index: 99;left:0;top:0;display:none;"></div>
  <div class="page-menu-bg" style="display:none;">
    <div class="page-menu-logo">
      <a href="/wldApp/agent.php/Home/Information"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-logo.png" alt=""></a>
    </div>
    <div class="page-m-loginname">
      欢迎您&nbsp;&nbsp;<?php echo (session('_ADMIN_NAME')); ?>
    </div>
    <ul class="page-menu-list">
      <li>
        <a href="/wldApp/agent.php/Home/Information">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-ico-1.png" alt=""></div>
        <div class="fl p-m-text">公告</div>
        <div class="fl p-m-num" id="notice">0</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Agentntrol">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-ico-2.png" alt=""></div>
        <div class="fl p-m-text">代理管理</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Shopcheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-ico-3.png" alt=""></div>
        <div class="fl p-m-text">商家审核</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Stringcheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/code_icon.png" alt=""></div>
        <div class="fl p-m-text">串码审核</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Download">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-ico-4.png" alt=""></div>
        <div class="fl p-m-text">资料下载</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Personal">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Home/View/Public/images/mobile/m-ico-5.png" alt=""></div>
        <div class="fl p-m-text">个人中心</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Home/Login/logout" class="a-loginout">
          <img src="/wldApp/Agent/Home/View/Public/images/mobile/loginout.png" alt="">
        </a>
      </li>
    </ul>

  </div>
<script type="text/javascript">

function topstyle () {
	// var pth = $('.page-top').height();
	// $('.page-top-c').css('line-height', pth + 'px');
  var topr = $('.page-top-r').width();
  $('.page-top-r').height(topr);
  var topl = $('.page-top-l').width();
  $('.page-top-l').height(topr);

}

function menustyle() {

	var pth = $('.page-top').height();
	$('.page-menu-bg').css('top', pth + 'px');

	var dh = $(document).height();
	$('.page-menu-bg').css('height', dh + 'px');

	// var icoh = $('.page-menu-list li').height();
	// $('.p-m-text').css('line-height', icoh + 'px');
	var numh = $('.p-m-num').height();
	$('.p-m-num').css('width', numh + 'px');
	$('.p-m-num').css('line-height', numh + 'px');
	$('.p-m-num').css('border-radius', numh + 'px');
}
$(function() {
  FastClick.attach(document.body);
  $('#icolink').click(function () {
    showmenu(1);
  });
  $('#icohover').click(function() {
    showmenu(2);
  });
});
function showmenu(v) {
  var v = v;
  if (v == 1) {
    $('.page-menu-bg').fadeIn(500);
    $('#icolink').hide();
    $('#icohover').show();
    $('.m-menu-bigbg').css("display","block");
    $('.m-menu-bigbg').height($(document).height());
    menustyle();
  } else {
    $('.page-menu-bg').fadeOut(300);
    $('#icolink').show();
    $('#icohover').hide();
    $('.m-menu-bigbg').css("display","none");
  }
}

$('.m-menu-bigbg').click(function () {
    $('.page-menu-bg').fadeOut(300);
    $('#icolink').show();
    $('#icohover').hide();
    $('.m-menu-bigbg').css("display","none");
});


getnum();

/*显示数字*/
function getnum(){
  var keyUrl = '/wldApp/agent.php/Home/Information/GetStatuMessage';
  $.ajax({url:keyUrl,dataType:"json",async:false,
      success:function(data){
        var msg = eval(data);
        if (msg['code']==0) {
          $('#notice').text(msg['data']['publicnum']);
          $('#checka').text(msg['data']['checknum']);
        }else{
          alert(msg['msg']);
        }
      }
  });
}
</script>

  <form action="/wldApp/agent.php/Home/Shopcheck/index" id="form1" method="get">
    <div id="search" class="bgcolor">
      <input type="text" name="keys" placeholder="输入搜索关键字" class="fs14 c9">
      <input class="button" type="submit" value="" id="keys_btn">
    </div>
  </form>
  <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="notice-list bgcolor">
    <?php if (!empty($vo['c_img'])) { ?>
      <div class="notice-img fl">
        <img src="/wldApp/<?php echo ($vo["c_img"]); ?>" alt="">
      </div>
    <?php } ?>
    <div class="notice-info fl" <?php if (empty($vo['c_img'])) { ?>style="width:91.5%;" <?php } ?> >
      <div class="notice-tit-tim">
        <div class="notice-tit fl">
          <a href="javascript:;" class="notice-tit-a <?php if($vo['c_infoid'] != null){?>grey<?php } ?>" onclick="loadinfo('<?php echo ($vo["c_id"]); ?>','<?php echo ($vo["c_url"]); ?>');"><?php echo ($vo["c_ptitle"]); ?></a>
        </div>
        <div class="notice-time fr"><?php echo (mb_substr($vo["c_addtime"],0,10,'utf-8')); ?></div>
      </div>
      <?php if (!empty($vo['c_img'])) { ?>
      <div class="notice-desc <?php if($vo['c_infoid'] != null){ ?>grey<?php } ?>">
        <?php echo ($vo["c_title"]); ?>...<a href="javascript:;" onclick="loadinfo('<?php echo ($vo["c_id"]); ?>','<?php echo ($vo["c_url"]); ?>');" class="details <?php if($vo['c_infoid'] != null){ ?>grey<?php } ?>">查看详情</a>
      </div>
      <?php } ?>
    </div>
  </div><?php endforeach; endif; else: echo "" ;endif; ?>
  <div class="pages">
    <div>
     <?php echo ($page); ?>
    </div>
  </div>


<!--提示资料未完成-->
<div class="agent-tip-bg none"></div>
<div class="agent-tip-pop none" id="tip-step-1">
  <div class="agent-tip-step1">
    <img src="/wldApp/Agent/Home/View/Public/images/agent-notice.png" alt="">
    <div class="agent-tip-font1">您的资料还未完成</div>
    <div class="agent-tip-font2">请立即填写您的资料</div>
    <div class="agent-tip-btn1" onclick="surebtn()">确定</div><div class="agent-tip-btn1 agent-tip-btn2" onclick="cancelbtn()">取消</div>
  </div>
</div>

<div class="agent-tip-pop none" id="tip-step-2">
  <div class="agent-tip-step1">
    <div class="agent-tip-st2-tit">请选择区代资质</div>
    <div class="agent-tip-team"><input type="radio" name="agenttype"><span class="radio-font">企业区代资料</span></div>
    <div class="agent-tip-sign"><input type="radio" name="agenttype"><span class="radio-font">个人区代资料</span></div>
    <img src="/wldApp/Agent/Home/View/Public/images/agent-noticebg.png" alt="">
  </div>
</div>



</div>

</body>

<script type="text/javascript">
function loadinfo (id,url) {
    $.post('/wldApp/agent.php/Home/Information/readinfo',{Id:id}, function(obj) {
        var result = eval(obj);
        if (result['code'] != 0) {
           alert(result['msg']);
        } else {
            if (url == '') {
                window.location.href = '/wldApp/agent.php/Home/Information/detail?Id=' + id;
            } else {
                window.location.href = url;
            }
        }
    });
}

getstate();
function getstate(){
  var keyUrl = '/wldApp/agent.php/Home/Information/ReadInfostatu';
  $.ajax({url:keyUrl,dataType:"json",async:false,
      success:function(data){
        var msg = eval(data);
        if (msg['code']!=1000) {
          $('#tip-step-1').hide();
          $('.agent-tip-bg').hide();
        }else{
          $('#tip-step-1').show(200);
          $('.agent-tip-bg').show();
          $('.agent-tip-bg').height($(document).height());
        }
      }
  });
}

function surebtn () {
  window.location.href="/wldApp/agent.php/Home/Personal";
}
function cancelbtn () {
  $('#tip-step-1').hide();
  $('.agent-tip-bg').hide();
}
</script>
</html>