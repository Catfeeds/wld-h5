<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Agent/View/Public/images/favicon.ico">
<title>商家后台管理--串码中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Agent/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Agent/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>

</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_AGENT_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Agent/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Agent/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Agent/Information/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Agent/Personal/shopinfo">上级代理</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Agent/Shopcheck/index">商家审核</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Agent/Codecheck/index">激活串码申请</a></li>
						<li id="li-5"><a href="/wldApp/agent.php/Agent/Download/index">资料下载</a></li>
						<li id="li-6">
							<a href="/wldApp/agent.php/Agent/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-6" class="hidden-box">
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Agent/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Agent/Personal/updatepwd">密码修改</a></li>
						</ul>
					</div>
				</div>

			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function(){
    $('#li-6').hover(function(){
        $('#menubox-6').slideDown(300);
    },function(){
        $('#menubox-6').hide();
    });
    $('.hidden-box').hover(function(){
    	$("#li-6 a").addClass("hover");
        $(this).show();
    },function(){
        $(this).slideUp(200);
    	$("#li-6 a").removeClass("hover");
    });
});

function exitLogin()
{
	parent.window.location.href="<?php echo U('Index/index');?>";
	window.opener.location.reload();
}

</script>

<div class="content-s w_960">
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
		<div class="code-btn fr" onclick="codecheck()">申请审核</div>
		<div class="code-txt fl c5">激活串码申请</div>
		<div class="code-input fl "><input type="number" placeholder="请输入数量" class="fs14" id="code_num" maxlength="10"></div>
	</div>

	<div class="send-strcode" style="display:none;" onclick="sendcode()">
		<span><img src="/wldApp/Agent/Agent/View/Public/images/mobile/issue_btn.png" alt=""></span>
	</div>

	<div class="comm-nav-tab bgcolor divtab">
		<ul>
			<li class="hover c-nav-tli" id="c-nav-t0" onclick="selectstatu(0)">已激活串码商家</li>
			<li class="c-nav-tli" id="c-nav-t1" onclick="selectstatu(1)">待激活串码</li>
		</ul>
	</div>
	<div class="gbuy-data-list divtab">
		<ul id="data_list">
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3">微领地小蜜</p>
					<p class="c9">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3">微领地小蜜</p>
					<p class="c9">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3">微领地小蜜</p>
					<p class="c9">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="u-headimg fl">
					<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />
				</div>
				<div class="u-nameinfo fl">
					<p class="c3">微领地小蜜</p>
					<p class="c9">2017056201050</p>
				</div>
				<div class="u-state fl" style="text-align: right;">
					<p class="c9">2017-05-19</p>
				</div>
			</li>
			<li>
				<div class="code-str fl">98888888888</div>
				<div class="code-addtime fr c9">2017-05-19 12:00:00</div>
			</li>
		</ul>
	</div>
	<div class="pup-bg"></div>
	<div class="sure-pup">
		<div class="sure-con">
			<div class="sure-tit c3">激活串码发放成功</div>
			<div class="sure-font c5">
				<textarea cols="" rows="" id="codestr" readonly="readonly"></textarea>
			</div>
			<div class="sure-btn">
				<span onclick="cancelpup()">取消</span>
				<span onclick="copystr()">复制</span>
			</div>
		</div>
	</div>
	<div class="pages">
		<div>
		 <?php echo ($page); ?>
		</div>
	</div>

</div>


<script type="text/javascript">
	window.onload=function(){
		$('#li-4 a').addClass('hover');
	    var hzw = $('.u-headimg').width();
		$('.u-headimg').width(hzw);
		$('.u-headimg').height(hzw);
		$('.u-nameinfo').css('line-height',hzw*0.45+'px');
		$('.u-state').css('line-height',hzw+'px');
	}

	var tjsign = true;
	function codecheck() {
		if (tjsign) {
			var codenum = $('#code_num').val();
			if (!codenum) {
				alert('请输入申请串码个数');return;	
			}
			tjsign = false;
			$.post("/wldApp/agent.php/Agent/Codecheck/ApplyCode",{num:codenum},function(obj){
				var data = eval(obj);
				tjsign = true;
				if(data['code']==0){
					alert(data['msg']);
					setTimeout(function () {
						window.location.href="/wldApp/agent.php/Agent/Codecheck/index";
					}, 2000);
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

	/*列表*/
	var pageindex = 1;
	var ctrls = true;
	var emptyval = true;
	var statu = '<?php echo $statu ?>';
	if(!statu) {
		statu = 0;
	}
	/*列表*/
	var pageindex = 1;
	var ctrls = true;
	var emptyval = true;
	var statu = '<?php echo $statu ?>';
	if(!statu) {
		statu = 0;
	}
	//selectstatu(statu);

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
		//getdatalist(pageindex);
	}
	$(window).bind('scroll', function() {
		if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
			if(ctrls && emptyval) {
				//getdatalist(pageindex);
			}
		}
		if($(window).scrollTop() >= $('.comm-nav-tab').height()) {
			$('.comm-nav-tab').addClass('menu-fixed');
		}
		if($(window).scrollTop() < $('.comm-nav-tab').height()) {
			$('.comm-nav-tab').removeClass('menu-fixed');
		}
	});

	function getdatalist(page) {
		if(page == 1) {
			pageindex = 1;
		}
		var _html = "";
		var strurl = "";
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
					if(data == null || data.list == null) {
						if(pageindex == 1) { //数据为空展示
							_html+='<div class="baoqian">暂无相关记录</div>';
						}
						emptyval = false;
					} else {
						if(pageindex <= data.pageCount) {
							pageindex++;
							var datalist = data.list;
							for(var i = 0; i < datalist.length; i++) {
			/*已激活串码商家*/
			_html+='<li>';
			_html+='<div class="u-headimg fl">';
			_html+='<img src="/wldApp/Agent/Agent/View/Public/images/mobile/agentimg.jpg" alt="" />';
			_html+='</div>';
			_html+='<div class="u-nameinfo fl">';
			_html+='<p class="c3">微领地小蜜</p>';
			_html+='<p class="c9">2017056201050</p>';
			_html+='</div>';
			_html+='<div class="u-state fl" style="text-align: right;">';
			_html+='<p class="c9">2017-05-19</p>';
			_html+='</div>';
			_html+='</li>';
			/*待激活串码*/
			_html+='<li>';
			_html+='<div class="code-str fl">98888888888</div>';
			_html+='<div class="code-addtime fr c9">2017-05-19 12:00:00</div>';
			_html+='</li>';
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