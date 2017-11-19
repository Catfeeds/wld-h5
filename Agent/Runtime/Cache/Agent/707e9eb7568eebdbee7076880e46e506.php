<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link rel="shortcut icon" href="/wldApp/Agent/Agent/View/Public/images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="format-detection" content="telephone=no, email=no" />
<title>串码中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/mobile/reset.css">
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/mobile/index.css?v=1.2">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>

</head>

<body>


  <script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/js/fastclick.js"></script>
  <div class="page-top">
    <div class="page-top-l fl">
     <a href="javascript:history.go(-1)" id="return-top"> <img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-jt.png" alt=""></a>
    </div>
    <div class="page-top-c fl" id="page-top-c-t">
      公告
    </div>
    <div class="page-top-r fr">
      <a href="javascript:;" id="icolink">
        <img src="/wldApp/Agent/Agent/View/Public/images/mobile/menu-ico-l.png" alt="">
      </a>
      <a href="javascript:;" id="icohover" style="display:none;">
        <img src="/wldApp/Agent/Agent/View/Public/images/mobile/menu-ico-h.png" alt="">
      </a>
    </div>
  </div>

  <div class="m-menu-bigbg" style="background:rgba(0,0,0,.5);width: 100%;height:100%;position: absolute;z-index: 99;left:0;top:0;display:none;"></div>
  <div class="page-menu-bg" style="display:none;">
    <div class="page-menu-logo">
      <a href="/wldApp/agent.php/Agent/Information"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-logo.png" alt=""></a>
    </div>
    <div class="page-m-loginname">
      欢迎您&nbsp;&nbsp;<?php echo (session('_AGENT_NAME')); ?>
    </div>
    <ul class="page-menu-list">
      <li>
        <a href="/wldApp/agent.php/Agent/Information">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-1.png" alt=""></div>
        <div class="fl p-m-text">公告</div>
        <div class="fl p-m-num" id="notice">0</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Shopcheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-3.png" alt=""></div>
        <div class="fl p-m-text">商家审核</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Codecheck">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/code_icon.png" alt=""></div>
        <div class="fl p-m-text">串码中心</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Download">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-4.png" alt=""></div>
        <div class="fl p-m-text">资料下载</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Personal/shopinfo">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-5.png" alt=""></div>
        <div class="fl p-m-text">上级代理</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Personal">
        <div class="p-m-ico fl"><img src="/wldApp/Agent/Agent/View/Public/images/mobile/m-ico-5.png" alt=""></div>
        <div class="fl p-m-text">个人中心</div>
        </a>
      </li>
      <li>
        <a href="/wldApp/agent.php/Agent/Login/logout" class="a-loginout">
          <img src="/wldApp/Agent/Agent/View/Public/images/mobile/loginout.png" alt="">
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
  var keyUrl = '/wldApp/agent.php/Agent/Information/GetStatuMessage';
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
			<p class="fs16"><?php echo ($info['c_checknum']); ?>个</p>
			<p class="fs12">待审核</p>
		</li>
		<li>
			<p class="fs16"><?php echo ($info['c_usenum']); ?>个</p>
			<p class="fs12">可使用</p>
		</li>
		<li>
			<p class="fs16"><?php echo ($info['c_codenum']); ?>个</p>
			<p class="fs12">已拥有</p>
		</li>
	</ul>

	<div class="code-check">
		<div class="code-btn fr fs14" onclick="codecheck()">申请串码</div>
		<div class="code-txt fl c5 fs14">激活串码申请</div>
		<div class="code-input fl "><input type="number" placeholder="请输入数量" class="fs14" id="code_num" maxlength="10"></div>
	</div>

	<div class="send-strcode" style="display:none;" onclick="sendcode()">
		<span><img src="/wldApp/Agent/Agent/View/Public/images/mobile/issue_btn.png" alt=""></span>
	</div>

	<div class="comm-nav-tab bgcolor divtab fs14">
		<ul>
			<li class="hover c-nav-tli" id="c-nav-t0" onclick="selectstatu(0)">已激活串码商家</li>
			<li class="c-nav-tli" id="c-nav-t1" onclick="selectstatu(1)">待激活串码</li>
		</ul>
	</div>
	<div class="gbuy-data-list divtab">
		<ul id="data_list">
			<!-- <li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3 fs14">微领地小蜜</p>
					<p class="c9 fs12">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="fs12 c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3 fs14">微领地小蜜</p>
					<p class="c9 fs12">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="fs12 c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3 fs14">微领地小蜜</p>
					<p class="c9 fs12">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="fs12 c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3 fs14">微领地小蜜</p>
					<p class="c9 fs12">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="fs12 c9">2017-05-19</p>
				</div>
			</li>
			待激活码显示列表
			<li>
				<div class="code-str fl fs14">98888888888</div>
				<div class="code-addtime fr c9 fs12">2017-05-19 12:00:00</div>
			</li> -->
		</ul>
	</div>
	<div class="pup-bg"></div>
	<div class="sure-pup">
		<div class="sure-con">
			<div class="sure-tit c3 fs16">激活串码发放成功</div>
			<div class="sure-font fs14 c5">
				<textarea cols="" rows="" id="codestr" readonly="readonly"></textarea>
			</div>
			<div class="sure-btn fs14">
				<span onclick="cancelpup()">取消</span>
				<span onclick="copystr()">复制</span>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.onload=function(){

	    topstyle();/*头部样式*/
	    yangshi();
	}

	function yangshi() {
		$('#return-top').hide();
	    $('#page-top-c-t').text("串码中心");
	    var hzw = $('.u-headimg').width();
		$('.u-headimg').width(hzw);
		$('.u-headimg').height(hzw);
		$('.u-nameinfo').css('line-height',hzw*0.5+'px');
		$('.u-state').css('line-height',hzw+'px');
	}

	var tjsign = true;
	function codecheck() {
		if (tjsign) {
			tjsign = false;
			var codenum = $('#code_num').val();
			if (!codenum) {
				alert('请输入申请串码个数');return;	
			}
			$.post("/wldApp/agent.php/Agent/Codecheck/ApplyCode",{num:codenum},function(obj){
				var data = eval(obj);
				tjsign = true;
				if(data['code']==0){
					alert(data['msg']);
					window.location.href="/wldApp/agent.php/Agent/Codecheck/index";
				}else{
					alert(data['msg']);
				}
			});
		}
	}

	/*发放串码*/
	var ffsign = true;
	function sendcode(){
		if (confirm('确认发放，一个激活串码？')) {
			if (ffsign) {
				ffsign = false;
				$.post("/wldApp/agent.php/Agent/Codecheck/GrantCode",{num:1},function(obj){
					var data = eval(obj);
					ffsign = true;
					if(data['code']==0){
						var info = data['data'];
						$('#codestr').text(info['c_code']);
						$('.pup-bg').fadeIn();
						$('.sure-pup').fadeIn();
						$('.pup-bg').height($(document).height());
					}else{
						alert(data['msg']);
					}
				});
			}
		}
	}
	/*取消*/
	function cancelpup(){
		$('.pup-bg').fadeOut();
		$('.sure-pup').fadeOut();
		window.location.href="/wldApp/agent.php/Agent/Codecheck/index";
	}

	/*复制*/
	function copystr(){
		var Url2 = document.getElementById("codestr");
		Url2.select();
		document.execCommand("Copy");
		alert("已复制好，可贴粘。");
	}

	/*复制*/
	function copystr(){
		var Url2 = document.getElementById("codestr");
		Url2.select();
		document.execCommand("Copy");
		alert("已复制好，可贴粘。");
	}

	/*列表*/
	var pageindex = 1;
	var ctrls = true;
	var emptyval = true;
	var statu = '<?php echo $statu ?>';
	if(!statu) {
		statu = 0;
	}
	selectstatu(statu);

	function selectstatu(i) {
		statu = i;
		pageindex = 1;
		ctrls = true;
		emptyval = true;
		$('.c-nav-tli').removeClass('hover');
		$('#c-nav-t' + i).addClass('hover');
		if(statu==0){
			$('.code-check').css('display','block');
			$('.send-strcode').css('display','none');
		}else{
			$('.code-check').css('display','none');
			$('.send-strcode').css('display','block');
		}
		getdatalist();
	}
	$(window).bind('scroll', function() {
		if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
			if(ctrls && emptyval) {
				getdatalist();
			}
		}
		if($(window).scrollTop() >= $('.comm-nav-tab').height()) {
			$('.comm-nav-tab').addClass('menu-fixed');
		}
		if($(window).scrollTop() < $('.comm-nav-tab').height()) {
			$('.comm-nav-tab').removeClass('menu-fixed');
		}
	});

	function getdatalist() {
		var _html = "";
		var strurl = "/wldApp/agent.php/Agent/Codecheck/GetCodeInfoList?statu="+statu+"pageindex="+pageindex;
		$.ajax({
			type: 'get',
			dataType: 'json',
			url: strurl,
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
					if(data == null || data.list.length <= 0) {
						if(pageindex == 1) { //数据为空展示
							_html+='<div class="baoqian">暂无相关记录</div>';
						}
						emptyval = false;
					} else {
						if(pageindex <= data.pageCount) {
							pageindex++;
							var datalist = data.list;
							for(var i = 0; i < datalist.length; i++) {
								var dataarr = datalist[i];
								if (statu == 0) {
									/*已激活串码商家*/
									_html+='<li>';
									_html+='<div class="u-headimg fl">';
									_html+='<img src="'+dataarr['c_headimg']+'" alt="" />';
									_html+='</div>';
									_html+='<div class="u-nameinfo fl">';
									_html+='<p class="c3">'+dataarr['c_nickname']+'</p>';
									_html+='<p class="c9">'+dataarr['c_code']+'</p>';
									_html+='</div>';
									_html+='<div class="u-state fl" style="text-align: right;">';
									_html+='<p class="c9">'+dataarr['c_addtime'].substr(0,10)+'</p>';
									_html+='</div>';
									_html+='</li>';
								} else if (statu == 1) {
									/*待激活串码*/
									_html+='<li>';
									_html+='<div class="code-str fl">'+dataarr['c_code']+'</div>';
									_html+='<div class="code-addtime fr c9">'+dataarr['c_addtime']+'</div>';
									_html+='</li>';
								}
							}
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
				yangshi();
				$('#console').css('display', 'none');
				ctrls = true;
			}
		});
	}
</script>

</body>
</html>