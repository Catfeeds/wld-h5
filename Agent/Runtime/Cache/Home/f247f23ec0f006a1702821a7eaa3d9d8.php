<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no, email=no" />
<title>激活串码审核</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/mobile/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/mobile/index.css?v=1.2">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
</head>

<body>

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

<div class="wrap-page">
	<ul class="str-t-num">
		<li>
			<p class="fs16"><?php echo ($info['checked']); ?>个</p>
			<p class="fs12">待审核</p>
		</li>
		<li>
			<p class="fs16"><?php echo ($info['nochecked']); ?>个</p>
			<p class="fs12">已审核</p>
		</li>
	</ul>
	<ul class="str-list" id="data_list">
		<!-- <li>
			<div class="str-item">
				<div class="fl str-headimg"><img src="/wldApp/Agent/Home/View/Public/images/mobile/agentimg.jpg" alt=""></div>
				<div class="fl str-info">
					<p class="fs16 c3">代理商名称</p>
					<p class="fs12 c9">申请串码个数：<span class="fs22">10</span>个</p>
					<div class="check_radio fs14">
						<span class="fl">
							<input type="radio" name="str_check_0" value="" id="strval_0" disabled="disabled"><label>免费</label>
						</span>
						<span class="fr">
							<input type="radio" name="str_check_0" value="" id="strval_1" checked="checked" disabled="disabled"><label>已付款</label>
						</span>
					</div>
				</div>
			</div>
			<div class="str-oprate">
				<span class="fs14">已审核</span>
				<span class="fs14 hover" style="display:none;">审核通过</span>
			</div>
		</li>
		<li>
			<div class="str-item">
				<div class="fl str-headimg"><img src="/wldApp/Agent/Home/View/Public/images/mobile/agentimg.jpg" alt=""></div>
				<div class="fl str-info">
					<p class="fs16 c3">代理商名称</p>
					<p class="fs12 c9">申请串码个数：<span class="fs22">20</span>个</p>
					<div class="check_radio fs14">
						<span class="fl selected">
							<input type="radio" name="str_check_1" value="" id="strval_2"><label>免费</label>
						</span>
						<span class="fr selected">
							<input type="radio" name="str_check_1" value="" id="strval_3"><label>已付款</label>
						</span>
					</div>
				</div>
			</div>
			<div class="str-oprate">
				<span class="fs14" style="display:none;">已审核</span>
				<span class="fs14 hover" onclick="pupcheck()">审核通过</span>
			</div>
		</li> -->
	</ul>
	<div class="pup-bg"></div>
	<div class="sure-pup">
		<div class="sure-con">
			<div class="sure-tit c3 fs16">确认审核</div>
			<div class="sure-font fs14 c5">审核后将发送短信提醒代理</div>
			<div class="sure-btn fs14">
				<span onclick="cancelpup()">取消</span>
				<span onclick="surecheck()">确定</span>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	window.onload=function(){

	    topstyle();/*头部样式*/

	    $('#return-top').hide();
	    $('#page-top-c-t').text("串码审核");

	    var ah = $('.str-headimg').width();
	    $('.str-headimg').width(ah);
	    $('.str-headimg').height(ah);
	    $('.str-info p').css('line-height',ah*0.3+'px');
	    $('.check_radio').height(ah*0.4);
	    $('.check_radio span').css('line-height',$('.check_radio').height()+'px');

	}
	var pageindex = 1;
	var ctrls = true;
	var emptyval = true;
	getdatalist();
	$(window).bind('scroll', function() {
		if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
			if(ctrls && emptyval) {
				getdatalist();
			}
		}	
	});

	/*数据加载*/
	function getdatalist() {
		var url = "/wldApp/agent.php/Home/Stringcheck/GetCodeCheckList?pageindex=" + pageindex;
		var _html = "";
		$.ajax({
			type: 'get',
			dataType: 'json',
			url: url,
			cache: false,
			beforeSend: function() {
				$('#console').css('display', 'block');
				$('#console').html('加载中...');
				ctrls = false;
			},
			error: function() {
				$('#console').css('display', 'block');
				$('#console').html('加载失败');
				ctrls = true;
			},
			success: function(obj) {
				if(pageindex == 1) {
					$('#data_list').empty();
				}
				var mgs = eval(obj);
				if(mgs['code'] == 0) {
					var data = mgs.data;
					if(!data ||  data.list.length <= 0) {
						if(pageindex == 1) {   //数据为空展示
							_html+='<div class="baoqian">暂无相关信息</div>';
						}
						emptyval = false;
					} else {
						if(pageindex <= data.pageCount) {
							pageindex++;
							var datalist = data.list;
							for(var i = 0; i < datalist.length; i++) {
								var dataarr = datalist[i];
								_html+='<li>';
								_html+='<div class="str-item">';
								_html+='<div class="fl str-headimg"><img src="'+dataarr['c_headimg']+'" alt=""></div>';
								_html+='<div class="fl str-info">';
								_html+='<p>'+dataarr['c_nickname']+'</p>';
								_html+='<p>申请串码个数：<span>'+dataarr['c_num']+'</span>个</p>';
								_html+='<div class="check_radio">';
								if (dataarr['c_state'] == 1) {
									/*已审核*/
									_html+='<span class="fl">';
									if (dataarr['c_isfree'] == 1) {
										_html+='<input type="radio" name="str_check_1" value="2" disabled="disabled"><label>免费</label>';
									} else {
										_html+='<input type="radio" name="str_check_1" value="2" checked="checked" disabled="disabled"><label>免费</label>';	
									}
									_html+='</span>';
									_html+='<span class="fr">';
									if (dataarr['c_isfree'] == 1) {
										_html+='<input type="radio" name="str_check_'+dataarr['c_id']+'" value="1" checked="checked" disabled="disabled"><label>已付款</label>';
									} else {
										_html+='<input type="radio" name="str_check_'+dataarr['c_id']+'" value="1" disabled="disabled"><label>已付款</label>';	
									}
									_html+='</span>';
								} else {
									/*审核通过*/
									_html+='<span class="fl selected">';
									_html+='<input type="radio" name="str_check_'+dataarr['c_id']+'" value="2"><label>免费</label>';
									_html+='</span>';
									_html+='<span class="fr selected">';
									_html+='<input type="radio" name="str_check_'+dataarr['c_id']+'" value="1"><label>已付款</label>';
									_html+='</span>';

								}
								
								_html+='</div>';
								_html+='</div>';
								_html+='<div class="str-oprate fl">';
								if (dataarr['c_state'] == 1) {
									_html+='<span class="">已审核</span>';
								} else {
									_html+='<span class="hover" onclick="pupcheck(\''+dataarr['c_id']+'\')">审核通过</span>';
								}
								_html+='</div>';
								_html+='</div>';
								_html+='</li>';
							};
						} else {
							emptyval = false;
						}
					}
				} else {
					emptyval = false;
				}
				$('#data_list').append(_html);
			},
			complete: function() {
			    var ah = $('.str-headimg').width();
			    $('.str-headimg').width(ah);
			    $('.str-headimg').height(ah);
			    $('.str-info p').css('line-height',ah*0.3+'px');
			    $('.check_radio').height(ah*0.4);
			    $('.check_radio span').css('line-height',$('.check_radio').height()+'px');
				$('#console').css('display', 'none');
				ctrls = true;
			}
		});
	}

var checksign = 0;
var isfree = '';
/*审核弹窗*/
function pupcheck(sg){
	checksign = sg;
	isfree = $('input[name="str_check_'+checksign+'"][type="radio"]:checked').val();
	if (!isfree) {
		alert('请选择状态');return;	
	}
	$('.pup-bg').fadeIn();
	$('.sure-pup').fadeIn();
	$('.pup-bg').height($(document).height());
}

/*取消*/
function cancelpup(){
	$('.pup-bg').fadeOut();
	$('.sure-pup').fadeOut();
}

/*确定审核*/
var tjsign = true;
function surecheck() {
	$('.pup-bg').fadeOut();
	$('.sure-pup').fadeOut();	
	if (tjsign) {
		tjsign = false;
		$.post("/wldApp/agent.php/Home/Stringcheck/CheckCode",{cid:checksign,isfree:isfree},function(obj){
			var data = eval(obj);
			tjsign = true;
			if(data['code']==0){
				alert(data['msg']);
				window.location.href="/wldApp/agent.php/Home/Stringcheck/index";
			}else{
				alert(data['msg']);
			}
		});
	}
}


</script>
</body>
</html>