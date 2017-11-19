<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Shop/View/Public/images/favicon.ico">
<title>商家后台管理--公告中心</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>


</head>

<body>

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
						<!-- <li id="li-2"><a href="/wldApp/agent.php/Shop/Shop/producelist">我的商品</a></li> -->
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
				    <!-- <div id="menubox-2" class="menubox hidden-box hidden-loc-us">						
						<ul id="son-menu-2">
							<li><a href="/wldApp/agent.php/Shop/Shop/index">上传商品</a></li>
						</ul>
					</div> -->
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

<div class="content-s w_960" style="padding-bottom: 3%;">
  
  <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="notice-list <?php if($vo['c_infoid'] !=null) { ?>newsico<?php } ?>">
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
        <div class="notice-time fr"><?php echo ($vo["c_addtime"]); ?></div>
      </div>
      <?php if (!empty($vo['c_img'])) { ?>
      <div class="notice-desc <?php if($vo['c_infoid'] != null){?>grey<?php } ?>">
        <?php echo ($vo["c_title"]); ?>...<a href="javascript:;" onclick="loadinfo('<?php echo ($vo["c_id"]); ?>','<?php echo ($vo["c_url"]); ?>');" class="details <?php if($vo['c_infoid'] != null){?>grey<?php } ?>">查看详情</a>
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
    <img src="/wldApp/Agent/Shop/View/Public/images/agent-notice.png" alt="">
    <div class="agent-tip-font1">您的资料还未完成</div>
    <div class="agent-tip-font2">请立即填写您的资料</div>
    <div class="agent-tip-btn1" onclick="surebtn()">确定</div><div class="agent-tip-btn1 agent-tip-btn2" onclick="cancelbtn()">取消</div>
  </div>
</div>

<div class="agent-tip-pop none" id="tip-step-2">
  <div class="agent-tip-step1">
    <div class="agent-tip-st2-tit">请选择区代代理资质</div>
    <div class="agent-tip-team"><input type="radio" name="agenttype"><span class="radio-font">企业区代资料</span></div>
    <div class="agent-tip-sign"><input type="radio" name="agenttype"><span class="radio-font">个人区代资料</span></div>
    <img src="/wldApp/Agent/Shop/View/Public/images/agent-noticebg.png" alt="">   
  </div>
</div>

</div>


<!--资料未完善2016-10月版-->
<div id="page-2-bg"></div>
<div class="content-s w_960" style="padding:6% 0;overflow: hidden; " id="page-2">
  <div class="on-line-left fl">
    <div class="on-line-bee"><img src="/wldApp/Agent/Shop/View/Public/images/xiaomi_1.png" alt=""></div>
    <div class="on-line-bee-radio"><input type="radio" name="ontype" onclick="surebtn(0)">我是线上微商</div>
  </div>
  <div class="off-line-right fl">
    <div class="on-line-bee"><img src="/wldApp/Agent/Shop/View/Public/images/xiaomi_2.png" alt=""></div>
    <div class="on-line-bee-radio"><input type="radio" name="ontype" onclick="surebtn(1)">我是线下实体商家</div>    
  </div>
</div> 

<script type="text/javascript">
  window.onload=function(){
    $('#li-1 a').addClass('hover');

    $('#page-2-bg').click(function(){          
        $('#page-2-bg').hide();    
        $('#page-2').hide();
    });
  }
</script>

<script type="text/javascript">
function loadinfo (id,url) {
    $.post('/wldApp/agent.php/Shop/Information/readinfo',{Id:id}, function(obj) {
        var result = eval(obj);     
        if (result['code'] != 0) {
            alert(result['msg']);               
        } else {            
            if (url == '') {
                window.location.href = '/wldApp/agent.php/Shop/Information/detail?Id=' + id;
            } else {
                window.location.href = url;                
            }
        }        
    });    
}


getstate();
function getstate(){
  var keyUrl = '/wldApp/agent.php/Shop/Information/ReadInfostatu';
  $.ajax({url:keyUrl,dataType:"json",async:false,
      success:function(data){
        var msg = eval(data);
        if (msg['code']!=1000) {          
          // $('#tip-step-1').hide();
          // $('.agent-tip-bg').hide();            
          $('#page-2-bg').hide();    
          $('#page-2').hide();
        }else{ 
          $('#page-2').show();
          $('#page-2-bg').show();    
          $('#page-2-bg').height($(document).height());     
          
          // $('#tip-step-1').show(200);
          // $('.agent-tip-bg').show();
          // $('.agent-tip-bg').height($(document).height());    
        }
      }
  });
}  

function surebtn (val) {
  if(val==0){
    window.location.href="/wldApp/agent.php/Shop/Personal/industry?isfixed=0";
  }else if(val==1){
    window.location.href="/wldApp/agent.php/Shop/Personal/industry?isfixed=1";
  }
}
function cancelbtn () {  
  $('#tip-step-1').hide();
  $('.agent-tip-bg').hide();
}  

</script> 
</body>
</html>