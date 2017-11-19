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
    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/imgshow.js"></script>
    <script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/brows.js"></script>
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
<form action="" id="form1" method="post" accept-charset="utf-8" enctype="multipart/form-data">
<input type="hidden" name="pcode" value="<?php echo ($vo["c_pcode"]); ?>">
<div class="wrap-page bgeb">
    <li class="goods bgcolor">
        <div class="fl goods_name c3">商品名称</div>
        <input class="fl goods_con c9" type="text" name="name" value="<?php echo ($vo["c_name"]); ?>" placeholder="请输入商品名称">
    </li>
    <li class="goods bgcolor">
        <div class="fl goods_name c3">商品描述</div>
        <input class="fl goods_con c9" type="text" name="desc" value="<?php echo ($vo["c_desc"]); ?>" placeholder="请输入商品描述">
    </li>
    <li class="goods bgcolor">
        <div class="fl goods_name c3">商品图片</div>
        <div class="fl goods_con c9">(建议尺寸不小于720*720)</div>
        <div class="productmodular-bottom">
            <?php if (count($vo['imglist']) > 0) { foreach ($vo['imglist'] as $key => $value) { ?>
            <div class="productmodular-img" id="imgmodular<?php echo ($key); ?>">
            <div class="productmodular-position" onclick="delimg(this);">
            <img src="/wldApp/Agent/Shop/View/Public/images/delete.png" alt="">                  
            </div>
            <img src="<?php echo ($value["c_pimgepath"]); ?>" id="imgpath<?php echo ($key); ?>">
            <input style="display:none;" type="hidden" name="img<?php echo ($key); ?>" value="<?php echo ($value["c_pimgepath"]); ?>">
            </div>  
            <?php } } ?>            
            
            <div class="productmodular-img" onclick="dianji(this,'img');"  id="imgbox">
                <img src="/wldApp/Agent/Shop/View/Public/images/store/imgadd.png">                
            </div>            
        </div>
        <div class="upload-text">
            说明：第一张图为封面图
        </div>
    </li>
    <?php if (count($vo['modellist']) > 0) { foreach ($vo['modellist'] as $k => $v) { ?>
    <div class="goods_model bgcolor">
        <input type="hidden" name="mcode[]" value="<?php echo ($v["c_mcode"]); ?>">
        <li class="goods">
            <div class="fl goods_name c3">型号</div>
            <input class="fl goods_con c9" type="text" name="mname[]" value="<?php echo ($v["c_name"]); ?>" placeholder="请输入型号名称">
        </li>
        <li class="goods">
            <div class="fl goods_name c3">库存</div>
            <input class="fl goods_con c9" type="text" name="mnum[]" value="<?php echo ($v["c_num"]); ?>" placeholder="请输入型号库存">
        </li>

        <?php if (count($v['ladderprice']) > 0) { foreach ($v['ladderprice'] as $k1 => $v1) { ?>
        
        <?php if (($k1+1) == count($v['ladderprice'])) { ?>
        <li class="goods">
            <div class="model_price c9">阶梯价格<?php echo ($k1+1) ?>:</div>
            <div class="model_price c3">
                <div class="fl model_text model_span" id="minnum<?php echo $k.$k1 ?>"><?php echo ($v1["c_minnum"]); ?></div>
                <span class="fl model_text">以上</span>
                <input type="hidden" name="maxnum[]" value="100000">
                <input class="fr model_write" type="text" name="mprice[]" value="<?php echo ($v1["c_price"]); ?>" placeholder="">
                <span class="fr model_text model_right">价格￥</span>
            </div>
        </li>   
        <?php } else { ?>
        <li class="goods">
            <div class="model_price c9">阶梯价格<?php echo ($k1+1) ?>:</div>
            <div class="model_price c3">
                <div class="fl model_text model_span" id="minnum<?php echo $k.$k1 ?>"><?php echo ($v1["c_minnum"]); ?></div>
                <span class="fl model_text">件至</span>
                <input class="fl model_write" type="text" name="maxnum[]" id="<?php echo $k.($k1+1) ?>" value="<?php echo ($v1["c_maxnum"]); ?>" placeholder="">
                <span class="fl model_text">件</span>

                <input class="fr model_write" type="text" name="mprice[]" value="<?php echo ($v1["c_price"]); ?>" placeholder="">
                <span class="fr model_text model_right">价格￥</span>
            </div>
        </li>
        <?php } ?>          
        <?php } } ?>
        <script type="text/javascript">
            $('input[name=maxnum[]]').each(function(i) {
                $(this).blur(function(){                    
                    if(!checknum($(this).attr('value'))){                       
                        alert('请填写大于零的整数！');
                        return;
                    }
                    var id = $(this).attr('id');
                    var val = $(this).attr('value')-(-1);
                    $('#minnum'+id).html(val);

                });
            });

            $('input[name=mprice[]]').each(function(i) {
                $(this).blur(function(){
                    var match =  /^(0|[1-9]\d*)(\.\d{1,2})?$/;
                    if (!match.test($(this).val())) {
                        alert('请输入正确的阶梯价格！');
                        return false;   
                    }                   
                });         
            });
        </script>
        
        <li class="model_del" onclick="deletemodel(this,'1');">
            <img src="/wldApp/Agent/Shop/View/Public/images/store/delete.png" alt="">
        </li>
    </div>
    <?php } } ?>
    
    <!-- 添加型号 -->
    <div class="model_add" onclick="addmodel();"  id="model_before">
        <img src="/wldApp/Agent/Shop/View/Public/images/store/model_sub.png" alt="">
    </div>

    <li class="goods bgcolor">
        <div class="fl goods_name c3">推广返利</div>
        <input class="fl goods_con profi c9" type="text" name="spread_proportion" value="<?php echo ($vo["c_spread_proportion"]); ?>" placeholder="请输入推广返利百分比">
        <div class="fl profi_sign">%</div>
    </li>
    <li class="goods bgcolor">
        <div class="fl goods_name c3">购物返利</div>
        <input class="fl goods_con profi c9" type="text" name="rebate_proportion" value="<?php echo ($vo["c_rebate_proportion"]); ?>" placeholder="请输入购物返利百分比">
        <div class="fl profi_sign">%</div>
    </li>
    <li class="goods bgcolor">
        <div class="fl goods_name c3">分类到</div>
        <select name="categoryid" id="categoryid" class="fl goods_con c9">
            <?php if (empty($vo['c_categoryid'])) { ?>
            <option value="">请选择分类</option>
            <?php } ?>
            <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$co): $mod = ($i % 2 );++$i; if ($vo['c_categoryid'] == $co['c_id']) { ?>  
                <option selected="selected" value="<?php echo ($co["c_id"]); ?>"><?php echo ($co["c_category_name"]); ?></option>
                <?php } else { ?>   
                <option value="<?php echo ($co["c_id"]); ?>"><?php echo ($co["c_category_name"]); ?></option>
                <?php } endforeach; endif; else: echo "" ;endif; ?>
        </select>
    </li>
    <li class="goods bgcolor">
        <div class="fl goods_name c3">邮费</div>
        <input class="fl goods_con profi c9" type="text" name="freeprice" value="<?php echo ($vo["c_freeprice"]); ?>" placeholder="设置为0.00元为包邮">
        <div class="fl profi_sign">元</div>
    </li>
    <div class="goods bgcolor">
        <div class="fl shelves_box">
            <input type="checkbox" <?php if ($vo['c_ishow'] == 1) { ?>checked="checked"<?php } ?> name="ishow" value="1">           
        </div>
        <div class="fl shelves_text">上架销售</div>
    </div>
    <input type="hidden" name="time" value="<?php echo ($time); ?>">
    <div class="payment-btn">
        <div class="paym-btn" onclick="subform();">保存</div>
    </div>
</div>
</form>
<script type="text/javascript">
    window.onload = function() {
        $('.listbody-left').height($('.listbody-left').width());
        $('.productmodular-img').height($('.productmodular-img').width());
        $('.productmodular-position').height($('.productmodular-position').width()); 
        $('#li-2 a').addClass('hover');
    }
    var sign = 0;
    // 添加型号
    function addmodel() {
        sign++;
        var html = '';
        html += '<div class="goods_model bgcolor">';
        html += '<input type="hidden" name="mcode[]" value="">';
        html += '<li class="goods">';
        html += '<div class="fl goods_name c3">型号</div>';
        html += '<input class="fl goods_con c9" type="text" name="mname[]" value="" placeholder="请输入型号名称">';
        html += '</li>';
        html += '<li class="goods">';
        html += '<div class="fl goods_name c3">库存</div>';
        html += '<input class="fl goods_con c9" type="text" name="mnum[]" value="" placeholder="请输入型号库存">';
        html += '</li>';
        html += '<li class="goods">';
        html += '<div class="model_price c9">阶梯价格1:</div>';
        html += '<div class="model_price c3">';
        html += '<span class="fl model_text model_span" id="minnum'+sign+'_1">1</span>';
        html += '<span class="fl model_text">件至</span>';
        html += '<input class="fl model_write" type="text" id="'+sign+'_2" name="maxnum[]" value="" placeholder="">';
        html += '<span class="fl model_text">件</span>';

        html += '<input class="fr model_write" type="text" name="mprice[]" value="" placeholder="">';
        html += '<span class="fr model_text model_right">价格￥</span>';
        html += '</div>';
        html += '</li>';
        html += '<li class="goods">';
        html += '<div class="model_price c9">阶梯价格2:</div>';
        html += '<div class="model_price c3">';
        html += '<span class="fl model_text model_span" id="minnum'+sign+'_2">2</span>';
        html += '<span class="fl model_text">件至</span>';
        html += '<input class="fl model_write" type="text" id="'+sign+'_3" name="maxnum[]" value="" placeholder="">';
        html += '<span class="fl model_text">件</span>';

        html += '<input class="fr model_write" type="text" name="mprice[]" value="" placeholder="">';
        html += '<span class="fr model_text model_right">价格￥</span>';
        html += '</div>';
        html += '</li>';
        html += '<li class="goods">';
        html += '<div class="model_price c9">阶梯价格3:</div>';
        html += '<div class="model_price c3">';
        html += '<span class="fl model_text model_span" id="minnum'+sign+'_3">3</span>';
        html += '<span class="fl model_text">以上</span>';
        html += '<input type="hidden" name="maxnum[]" value="100000">';
        html += '<input class="fr model_write" type="text" name="mprice[]" value="" placeholder="">';
        html += '<span class="fr model_text model_right">价格￥</span>';
        html += '</div>';
        html += '</li>';
        html += '<li class="model_del" onclick="deletemodel(this,\'1\');">';
        html += '<img src="/wldApp/Agent/Shop/View/Public/images/store/delete.png" alt="">';
        html += '</li>';
        html += '</div>';
        $('#model_before').before(html);
        $('input[name=maxnum[]]').each(function(i) {
            $(this).blur(function(){                    
                if(!checknum($(this).attr('value'))){                       
                    alert('请填写大于零的整数！');
                    return;
                }
                var id = $(this).attr('id');
                var val = $(this).attr('value')-(-1);
                $('#minnum'+id).html(val);
            });
        });
        $('input[name=mprice[]]').each(function(i) {
            $(this).blur(function(){
                var match =  /^(0|[1-9]\d*)(\.\d{1,2})?$/;
                if (!match.test($(this).val())) {
                    alert('请输入正确的阶梯价格！');
                    return false;   
                }                   
            });         
        });

    }

    // 删除型号
    function deletemodel(tg,id) {
        $(tg).parent().remove();
    }

    // 提交表单
    function subform() {    
        if (!$('input[name=name]').val()) {
            alert('请填写商品名称！');
            return;
        }
        if (!$('input[name=desc]').val()) {
            alert('请填写商品描述！');
            return;
        }
        var imglg = $('.productmodular-img').size();
        if (imglg == 1) {
            alert('请上传图片！');
            return;
        }
        if ($('input[name=mname[]]').size() == 0) {
            alert('至少添加一个型号！');
            return;
        }
        if (checkmodel()) {
            var match =  /^(0|[1-9]\d*)(\.\d{1,2})?$/;
            if (!match.test($('input[name=spread_proportion]').val())) {
                alert('请输入正确的推广返利！');
                return;
            };
            if (!match.test($('input[name=rebate_proportion]').val())) {
                alert('请输入正确的购物返利！');
                return;
            };
            if (!$('#categoryid').val()) {
                alert('请选择分类！');
                return;
            };
            if (!match.test($('input[name=freeprice]').val())) {
                alert('请输入正确的邮费！');
                return;
            };
            $('#form1').submit();
        }
    }

    function checkmodel() {
        var ct = true;
        $('input[name=mname[]]').each(function(i) {
            if (!$(this).val()) {
                alert('请完善型号名称！');
                ct = false;
            }
        });
        $('input[name=mnum[]]').each(function(i) {
            if (!$(this).val()) {
                alert('请完善型号库存！');
                ct = false;
            }
        });
        $('input[name=maxnum[]]').each(function(i) {
            if (!$(this).val()) {
                alert('请完善阶梯数量！');
                ct = false;
            }
        });
        $('input[name=mprice[]]').each(function(i) {
            if (!$(this).val()) {
                alert('请完善阶梯价格！');
                ct = false; 
            }
        });
        return ct;
    }
    /*上传图片*/
    function dianji(tg,sg) {
        var html = '';
        var n = $(tg).parent().find('.productmodular-img').size();
        if (n > 9) {
            alert('最多上传9张图片');     
            return;
        }
        html += '<div style="display:none;" class="productmodular-img" id="' + sg + 'modular' + n + '">';
        html += '<div class="productmodular-position" onclick="delimg(this);">';
        html += '<img src="/wldApp/Agent/Shop/View/Public/images/delete.png" alt=""> ';                   
        html += '</div>';
        html += '<img src="" id="' + sg + 'path' + n + '">';
        html += '<input style="display:none;" type="file" name="' + sg + n +'" value="" id="' + sg + 'file1' + n + '">';
        html += '</div>';

        $('#' + sg + 'box').before(html);        
        $("#"+sg + 'file1' + n).uploadPreview({
            Img: sg + 'path' + n,
            Width: 200,
            Height: 200
        });
       
        var gg = document.getElementById(sg + 'file1' + n);
        gg.click();
        $('#'+sg + 'modular' + n).show();
        $('.productmodular-img').height($('.productmodular-img').width());
        $('.productmodular-position').height($('.productmodular-position').width()); 
    }

    // 删除图片
    function delimg(tg) {
        $(tg).parent().remove();
    }

    /*不能输入中文*/
    function checknum(str) {
        var type = /^[0-9]*[1-9][0-9]*$/;
        var re = new RegExp(type);
        if (str.match(re) == null) {
            return false;
        }
        return true;
    }

    getstate();
    function getstate(){
      var keyUrl = '/wldApp/agent.php/Shop/Information/ReadInfostatu';
      $.ajax({url:keyUrl,dataType:"json",async:false,
          success:function(data){
            var msg = eval(data);
            if (msg['code']==0) {          
                 
            }else{  
                alert('您的资料还审核通过,暂不能添加商品');
                window.location.href = '/wldApp/agent.php/Shop/Shop/producelist'; 
                return;
            }
          }
      });
    }
</script>
</body>
</html>