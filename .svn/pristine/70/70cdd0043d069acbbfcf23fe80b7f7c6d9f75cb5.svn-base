<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no, email=no" />         
	<title>商品管理</title>
	<meta content="微域领地,微域领地系统" name="keywords">
	<meta content="" name="description">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/resc.css?v=1.2">
	<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/shop.css?v=1.2">
	<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/jquery.js"></script>
	<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/agent.js"></script>
    <link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css?v=1.3">
</head>
<body style="background:#f0f3fa;">
	<div id="top-bg">
		<div class="top-main-w">
			<div class="top-operate">
				<ul>
					<li>欢迎您：<?php echo (session('_SHOP_NAME')); ?></li>
					<li><a href="javascript:;" style="border-right:solid 1px #999;padding-right:12px;">提取账号</a></li>
					<li><a href="">刷新</a></li>
					<li><a href="/wldApp/agent.php/Shop/Login/logout">退出</a></li>
				</ul>
			</div>
			<div class="top-logo-menu">
				<div class="top-logo-m fl"><img src="/wldApp/Agent/Shop/View/Public/images/admin-logo.png" alt=""></div>
				<div class="top-agent-menu fr">
					<ul class="agent-channel">
						<li id="li-1"><a href="/wldApp/agent.php/Shop/Information/index">公告</a></li>		
						<li id="li-2"><a href="/wldApp/agent.php/Shop/Shop/producelist">我的商品</a></li>
						<li id="li-3"><a href="/wldApp/agent.php/Shop/Member/index">会员管理</a></li>
						<li id="li-4"><a href="/wldApp/agent.php/Shop/Download/index">资料下载</a></li>
						<li id="li-5">
							<a href="/wldApp/agent.php/Shop/Personal/index">个人中心</a>
						</li>
					</ul>
				    <div id="menubox-5" class="menubox hidden-box">						
						<ul id="son-menu">
							<li><a href="/wldApp/agent.php/Shop/Personal/index">资料设置</a></li>
							<li><a href="/wldApp/agent.php/Shop/Personal/updatepwd">密码修改</a></li>
							<li><a href="/wldApp/agent.php/Shop/Personal/shopinfo">上级代理</a></li>
						</ul>
					</div>
				    <div id="menubox-2" class="menubox hidden-box hidden-loc-us">						
						<ul id="son-menu-2">
							<li><a href="/wldApp/agent.php/Shop/Shop/index">上传商品</a></li>
						</ul>
					</div>
				</div>
				
			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function(){
    // $('#li-5').hover(function(){        
    //     $('#menubox-5').slideDown(300);
    // },function(){        
    //     $('#menubox-5').hide();
    // });
    // $('.hidden-box').hover(function(){
    // 	$("#li-5 a").addClass("hover");          
    //     $(this).show();
    // },function(){
    //     $(this).slideUp(200);  
    // 	$("#li-5 a").removeClass("hover");        
    // });   


    var num;
    $('.agent-channel>li[id]').hover(function(){    	
        /*下拉框出现*/
        var Obj = $(this).attr('id');
        num = Obj.substring(3, Obj.length);        
        $('#menubox-'+num).slideDown(300);
    },function(){
        /*下拉框消失*/
        $('#menubox-'+num).hide();
    });

    $('.hidden-box').hover(function(){
    	$("#li-"+num+" a").addClass("hover"); 
        $(this).show();
    },function(){
    	$("#li-"+num+" a").removeClass("hover"); 
        $(this).slideUp(200);
    });

});	

function exitLogin()
{
	parent.window.location.href="<?php echo U('Index/index');?>";
	window.opener.location.reload(); 
}

</script>	
<div class="wrap-page" style="background:#f0f3fa;" id="goods_list">	

</div>
<div class="wrap-page bgcolor" id="store_id1">
	<a href="/wldApp/agent.php/Shop/Shop/index" class="store_tit">		
		新增商品  	
	</a>
	
	<!-- <div class="store_null">
		<img src="/wldApp/Agent/Shop/View/Public/images/store/null.png" alt="">
	</div> -->
</div>
<script type="text/javascript">
	window.onload = function() {
		$('.listbody-left').height($('.listbody-left').width());
		$('#li-2 a').addClass('hover');
		// $('#store_id1').hide();
		// $('#store_id2').show();
	}

	var ctrls = true;
		var emptyval = true;
		var pageindex = 1;
		getscore(pageindex);
		$(window).bind('scroll', function() {
			if (($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
				if (ctrls && emptyval) {
					getscore(pageindex);	
				}		
			}		
		});		
        function getscore (page) {
        	if (page==1) {
				pageindex = 1;
			}			
			var _html="";
			var strurl = "/wldApp/agent.php/Shop/Shop/GetProductList?pageindex="+pageindex;
			$.ajax({
				type:'get', dataType:'json', url:strurl, cache:false, beforeSend:function() {
					$('#console').css('display', 'block');
					$('#console').html('加载中...');
					ctrls = false;
				}, error:function() {
					$('#console').css('display', 'block');
					$('#console').html('加载失败');
					ctrls = true;
				},success:function(obj) {
					if(pageindex == 1) {
						$('#goods_list').empty();
					}
					var mgs = eval(obj);
					if(mgs['code']==0) {
						var data = mgs.data;
						if(data == null || data.list == null){							
							if (pageindex==1) {
								_html += '<div class="wrap-page bgcolor">';								
								_html += '<div class="store_null">';
								_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/null.png" alt="">';
								_html += '</div>';
								_html += '</div>';
							}							
							emptyval = false;
						} else {
						if (pageindex <= data.pageCount) {
							pageindex++;
							var datalist = data.list;
							for (var i = 0;i < datalist.length;i++) {
								_html += '<div class="goods_list" id="goods_list">';
								_html += '<a class="orderlist-body bgcolor" href="/wldApp/agent.php/Shop/Shop/goods_edit?pcode='+datalist[i]['c_pcode']+'">';
								_html += '<div class="fl listbody-left">';
								_html += '<img src="'+datalist[i]['c_pimg']+'" alt="">';
								_html += '</div>';
								_html += '<div class="fl listbody-center">';
								if (datalist[i]['c_isagent'] == 0) {
									_html += '<h3><span>(自营)</span>'+datalist[i]['c_name']+'</h3>';
								} else {
									_html += '<h3><span>(代理)</span>'+datalist[i]['c_name']+'</h3>';
								}
								_html += '<div class="store_mid">';
								_html += '<div class="fl price_left">￥'+datalist[i]['c_price']+'</div>';
								_html += '<div class="fr price_img">';
								_html += '<img src="/wldApp/Agent/Shop/View/Public/images/jiantou.png" alt="">';
								_html += '</div>';
								_html += '</div>';
								_html += '<div class="listbody-bot">';
								_html += '<div class="fl order-price">销量：'+datalist[i]['c_salesnum']+'</div>';
								_html += '<div class="fl order-num">库存：'+datalist[i]['c_num']+'</div>';
								_html += '</div>';
								_html += '</div>';
								_html += '</a>';
								_html += '<div class="store_menu bgcolor">';
								// _html += '<a href="/wldApp/agent.php/Shop/Shop/comment?pcode='+datalist[i]['c_pcode']+'" class="fl menu_list c9" style="margin-left:5%;">';
								// _html += '<div class="menu_img">';
								// _html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s1.png" alt="">';
								// _html += '</div>评论管理</a>';
								_html += '<a href="/wldApp/agent.php/Shop/Shop/goods_edit?pcode='+datalist[i]['c_pcode']+'" class="fl menu_list c9" style="margin-left:25%;">';
								_html += '<div class="menu_img">';
								_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s2.png" alt="">';
								_html += '</div>编辑</a>';
								_html += '<a href="javascript:;" class="fl menu_list c9" onclick="removeGoods(this,\''+datalist[i]['c_pcode']+'\');">';
								_html += '<div class="menu_img">';
								_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s5.png" alt="">';
								_html += '</div>删除</a>';
								if (datalist[i]['c_ishow'] == 2) {
									_html += '<a href="javascript:;" class="fl menu_list c9" onclick="updownGoods(1,\''+datalist[i]['c_pcode']+'\');">';
									_html += '<div class="menu_img">';
									_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s6.png" alt="">';
									_html += '</div>上架</a>';
								} else {
									_html += '<a href="javascript:;" class="fl menu_list c9" onclick="updownGoods(2,\''+datalist[i]['c_pcode']+'\');">';
									_html += '<div class="menu_img">';
									_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s7.png" alt="">';
									_html += '</div>下架</a>';
								}	
								

								// _html += '<a href="/wldApp/agent.php/Shop/Shop/pdetail?pcode='+datalist[i]['c_pcode']+'" class="fl menu_list c9">';
								// _html += '<div class="menu_img">';
								// _html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s3.png" alt="">';
								// _html += '</div>浏览</a>';
								// _html += '<a href="'+WEB_HOST+'/index.php/Home/Shop/details?pcode='+datalist[i]['c_pcode']+'&pucode='+datalist[i]['c_ucode']+'" class="fl menu_list c9">';
								// _html += '<div class="menu_img">';
								// _html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/s4.png" alt="">';
								// _html += '</div>分享</a>';
								_html += '</div>';
								_html += '</div>';	
								_html += '</div>';	
							}
						}
						else{
							emptyval=false;
						}
					}
					}
					else{
						emptyval=false;
					}
					$('#goods_list').append(_html);	
				}, complete:function() {									
		            $('.listbody-left').height($('.listbody-left').width());
					ctrls = true;
				}
			});
        }

	// 上下架
	function updownGoods(ishow,pcode) {
		$.ajax({
			type: "POST",
			url:  "/wldApp/agent.php/Shop/Shop/goods_out",
			data: "pcode=" + pcode +"&ishow=" + ishow,
			dataType: "json",
			success: function(json) {
				var obj = eval(json);
				if (obj.code == 0) {
					alert(obj.msg);
					window.location.href = '';						
				} else {
					alert(obj.msg);
				}
			}
		});		
	}

	// 删除商品
	function removeGoods(tg,pcode) {
		$.ajax({
			type: "POST",
			url:  "/wldApp/agent.php/Shop/Shop/goods_delete",
			data: "pcode=" + pcode,
			dataType: "json",
			success: function(json) {
				var _html = '';
				var obj = eval(json);
				if (obj.code == 0) {
					alert(obj.msg);	
					$(tg).parent().parent().remove();
					if ($('.listbody-center').size() == 0) {
						_html += '<div class="wrap-page bgcolor">';
						_html += '<div class="store_null">';
						_html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/null.png" alt="">';
						_html += '</div>';
						_html += '</div>';
						$('#goods_list').append(_html);
					};						
				} else {
					alert(obj.msg);
				}
			}
		});		
	}

	function sharepro(id) {
		var clipboard = new Clipboard('#' + id);
		clipboard.on('success', function(e) {
			console.log(e);
			alert('分享链接已复制到剪贴板！');
		});
		clipboard.on('error', function(e) {
			console.log(e);
		});
	}

</script>
</body>
</html>