<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Home/View/Public/images/favicon.ico">
<title>串码审核</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Home/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Home/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>

</head>

<body>

	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_ADMIN_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Home/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Home/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Home/Information/index">公告</a></li>
						<li id="li-2"><a href="/wldApp/agent.php/Home/Agentntrol/index">代理管理</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Home/Shopcheck/index">商家审核</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Home/Stringcheck/index">串码审核</a></li>
						<li id="li-5"><a href="/wldApp/agent.php/Home/Download/index">资料下载</a></li>
						<li id="li-6">
							<a href="/wldApp/agent.php/Home/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-6" class="hidden-box">
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Home/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Home/Personal/updatepwd">密码修改</a></li>
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
			<p><?php echo ($info['checked']); ?>个</p>
			<p>待审核</p>
		</li>
		<li>
			<p><?php echo ($info['nochecked']); ?>个</p>
			<p>已审核</p>
		</li>
	</ul>
	<ul class="str-list" id="data_list">
		<!-- <li>
			<div class="str-item">
				<div class="fl str-headimg"><img src="/wldApp/Agent/Home/View/Public/images/mobile/agentimg.jpg" alt=""></div>
				<div class="fl str-info">
					<p>代理商名称</p>
					<p>申请串码个数：<span>10</span>个</p>
					<div class="check_radio">
						<span class="fl">
							<input type="radio" name="str_check_0" value="" id="strval_0" disabled="disabled"><label>免费</label>
						</span>
						<span class="fr">
							<input type="radio" name="str_check_0" value="" id="strval_1" checked="checked" disabled="disabled"><label>已付款</label>
						</span>
					</div>
				</div>
				<div class="str-oprate fl">
					<span class="">已审核</span>
					<span class="hover" style="display:none;">审核通过</span>
				</div>
			</div>
		</li>
		<li>
			<div class="str-item">
				<div class="fl str-headimg"><img src="/wldApp/Agent/Home/View/Public/images/mobile/agentimg.jpg" alt=""></div>
				<div class="fl str-info">
					<p>代理商名称</p>
					<p>申请串码个数：<span>20</span>个</p>
					<div class="check_radio">
						<span class="fl selected">
							<input type="radio" name="str_check_1" value="" id="strval_2"><label>免费</label>
						</span>
						<span class="fr selected">
							<input type="radio" name="str_check_1" value="" id="strval_3"><label>已付款</label>
						</span>
					</div>
				</div>
				<div class="str-oprate fl">
					<span class="" style="display:none;">已审核</span>
					<span class="hover" onclick="pupcheck()">审核通过</span>
				</div>
			</div>
		</li> -->
	</ul>
	<div class="pages">
		<div>
		 <?php echo ($page); ?>
		</div>
	</div>
	<div class="pup-bg"></div>
	<div class="sure-pup">
		<div class="sure-con">
			<div class="sure-tit">确认审核</div>
			<div class="sure-font">审核后将发送短信提醒代理</div>
			<div class="sure-btn">
				<span onclick="cancelpup()">取消</span>
				<span onclick="surecheck()">确定</span>
			</div>
		</div>
	</div>
	

</div>

<script type="text/javascript">
	window.onload=function(){

		$('#li-4 a').addClass('hover');

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
								_html+='<div class="fl str-headimg"><img src="/wldApp/Agent/Home/View/Public/images/mobile/agentimg.jpg" alt=""></div>';
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