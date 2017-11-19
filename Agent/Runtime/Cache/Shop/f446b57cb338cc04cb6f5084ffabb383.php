<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="/wldApp/Agent/Shop/View/Public/images/favicon.ico">
<title>商家后台管理--我的资料</title>
<link rel="stylesheet" type="text/css" href="/wldApp/Agent/Shop/View/Public/css/agent.css">
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<<<<<<< .mine
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/common.js"></script>
<script type="text/javascript">var WEB_HOST = "<?php echo WEB_HOST ?>";</script>

=======
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/common.js?v=2017325"></script>
<script type="text/javascript">var WEB_HOST = "<?php echo WEB_HOST ?>";</script>
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/ajaxfileupload.js"></script>
>>>>>>> .r4017

<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.3"></script>

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

<div class="content-s w_960">
	<form action="/wldApp/agent.php/Shop/Personal/SaveAgentInfo" method="post" accept-charset="utf-8" enctype="multipart/form-data" id="form1">
		<div class="page-title">
			<div class="page-tit-name">我的资料</div>
		</div>
		<div class="shop-check" style="margin-top:30px;">
			<div class="check-pass fl"><input type="radio" <?php if ($vo['c_checked'] == 2 || $vo['c_checked'] == 3) { ?>disabled="disabled"<?php } ?> name="type" value="2" <?php if($vo['c_type'] == 2 || empty($vo['c_dcode'])){?> checked="checked" <?php } ?> onclick="re_type()">&nbsp;企业资料</div>
			<div class="check-pass fl"><input type="radio" <?php if ($vo['c_checked'] == 2 || $vo['c_checked'] == 3 || $isfixed == 1) { ?>disabled="disabled"<?php } ?> name="type" <?php if($vo['c_type'] == 1 && !empty($vo['c_dcode'])){?> checked="checked" <?php } ?> value="1" onclick="re_type()">&nbsp;个人资料</div>
		</div>
		<div class="resource-main">
			<div class="qy">
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">申请单位名称：</div>
						<div class="resource-r fl"><input type="text" id="companey-name" value="<?php echo ($vo["c_company"]); ?>" name="company" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span></div>
				</div>
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">申请单位地址：</div>
						<div class="resource-r fl"><input type="text" id="address-name" value="<?php echo ($vo["c_address"]); ?>" name="address" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span></div>
				</div>
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">联系人：</div>
						<div class="resource-r fl"><input type="text" id="contact-name" value="<?php echo ($vo["c_name"]); ?>" name="name" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span></div>
				</div>
			</div>
			<div class="gr">
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">姓名：</div>
						<div class="resource-r fl"><input type="text" id="user-name" value="<?php echo ($vo["c_name"]); ?>" name="name1" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span>请输入真实姓名</div>
				</div>
			</div>
			<div class="resource-li-box">
				<div class="resource-list fl">
					<div class="resource-l fl">移动电话：</div>
					<div class="resource-r fl"><input type="text" id="phone-name" value="<?php echo ($vo["c_phone"]); ?>" name="phone" class="resource-text"></div>
				</div>
				<div class="resource-tipt fl"><span>*&nbsp;</span></div>
			</div>
			<div class="resource-li-box">
				<div class="resource-list fl">
					<div class="resource-l fl">邮箱：</div>
					<div class="resource-r fl"><input type="text" id="email-name" value="<?php echo ($vo["c_email"]); ?>" name="email" class="resource-text"></div>
				</div>
				<div class="resource-tipt fl"><span>*&nbsp;</span></div>
			</div>
			<div class="resource-li-box">
				<div class="resource-list fl">
					<div class="resource-l fl">QQ：</div>
					<div class="resource-r fl"><input type="text" id="qq-name" value="<?php echo ($vo["c_qq"]); ?>" name="qq" class="resource-text"></div>
				</div>
				<div class="resource-tipt fl"><span>*&nbsp;</span></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl">固定电话：</div>
				<div class="resource-r fl"><input type="text" id="telephone-name" value="<?php echo ($vo["c_home_tel"]); ?>" name="home_tel" class="resource-text"></div>
			</div>

			<div class="qy">
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">邮政编码：</div>
						<div class="resource-r fl"><input type="text" id="post-name" value="<?php echo ($vo["c_postcode"]); ?>" name="postcode" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span></div>
				</div>
				<div class="resource-li-box">
					<div class="resource-list fl">
						<div class="resource-l fl">营业执照号：</div>
						<div class="resource-r fl"><input type="text" id="license-name" value="<?php echo ($vo["c_charter"]); ?>" name="charter" class="resource-text"></div>
					</div>
					<div class="resource-tipt fl"><span>*&nbsp;</span></div>
				</div>
			</div>
			<div class="resource-li-box">
				<div class="resource-list fl">
					<div class="resource-l fl">身份证号码：</div>
					<div class="resource-r fl"><input type="text" id="identity-name" value="<?php echo ($vo["c_idcard"]); ?>" name="idcard" class="resource-text"></div>
				</div>
				<div class="resource-tipt fl"><span>*&nbsp;</span></div>
			</div>

			<div class="page-title">
				<div class="page-tit-name">商家地址</div>
			</div>
			<div class="add-address-con">
				<div class="add-addr-box">
					<div class="add-addr-font fl">商家属性：</div>
					<div class="add-addr-txt fl">
						<?php if($isfixed==0){ ?>
						线上微商
						<input type="hidden" name="isfixed" value="0">
						<?php }elseif($isfixed==1){ ?>
						线下实体商家
						<input type="hidden" name="isfixed" value="1">
						<?php } ?>
					</div>
					<div class="add-addr-font addrfont-2 fl">所属行业：</div>
					<div class="add-addr-sel fl">
						<select id="tid" name="tid">
							<?php if($vo['tradepid']==0){ ?>
							<option value="<?php echo ($vo['c_shoptrade']); ?>"><?php echo ($vo['tradename']); ?></option>
							<?php if(is_array($industry)): $i = 0; $__LIST__ = $industry;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voi): $mod = ($i % 2 );++$i;?><option value="<?php echo ($voi['c_id']); ?>"><?php echo ($voi['c_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							<?php }else{ ?>
							<option value="<?php echo ($vo['c_shoptrade']); ?>"><?php echo ($vo['tradename']); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
					<div class="add-addr-box">
						<div class="add-area-font  fl">省：</div>
						<div class="add-addr-sel fl">
							<select name="province" id="province" onchange="loadRegion('province',2,'city','<?php echo U('Personal/getRegion');?>');">
					          <option value="" id="provincename">选择省</option>
					          <?php if(is_array($province)): foreach($province as $key=>$province): ?><option value="<?php echo ($province["region_id"]); ?>" <?php if($prove == $province['region_name']){ ?> selected="selected" <?php } ?> ><?php echo ($province["region_name"]); ?></option><?php endforeach; endif; ?>
				          	</select>
						</div>
						<div class="add-area-font fl">市：</div>
						<div class="add-addr-sel fl">
				          <select name="city" id="city" onchange="loadRegion('city',3,'district','<?php echo U('Personal/getRegion');?>');">
				          	<?php if($citye){ ?>
				          	<option value="" id="cityname"><?php echo ($citye); ?></option>
				          	<?php }else{ ?>
				          	<option value="" id="cityname">选择市</option>
				          	<?php } ?>
				          </select>
						</div>
						<div class="add-area-font fl">区：</div>
						<div class="add-addr-sel fl">
				          <select name="district" id="district">
				          	<?php if($newdistrict){ ?>
				          	<option value="" id="districtname"><?php echo ($newdistrict); ?></option>
				          	<?php }else{ ?>
				          	<option value="" id="districtname">选择区</option>
				          	<?php } ?>
				          </select>
						</div>
					</div>
					<div class="add-addr-details">
						<?php if($xsaddress){ ?>
						<textarea name="addrarea" id="addrarea" rows="8" placeholder="<?php echo ($xsaddress); ?>"><?php echo ($xsaddress); ?></textarea>
						<?php }else{ ?>
						<textarea name="addrarea" id="addrarea" rows="8" placeholder="详细地址："></textarea>
						<?php } ?>
						<input type="hidden" id="address1" name="address1" value="<?php echo ($vo['address1']); ?>">
						<div class="must-txt fl">*</div>
					</div>

			</div>

<<<<<<< .mine
			<div class="page-title">
				<div class="page-tit-name">上传身份证件</div>
			</div>
			<ul class="ul-certificate">
				<li>
					<div class="zjz-list" onclick="clickimg(1);">
						<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
						<?php if($vo['c_idcard_img'] !=null){ ?>
						<div class="pic-front"><img id="imgr1" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr1" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img" value="<?php echo ($vo['c_idcard_img']); ?>" id="saveimg_1">
						<input type="file" style="display:none;" name="idcard_img" value="" id="file1" placeholder="">
					</div>
					<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] == 1) { ?>
					<div class="box-delete <?php if($vo['c_idcard_img'] ==null){ ?> none <?php }else{ ?>block<?php } ?>" onclick="deleteimg(1)" id="delimg_1"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
					<?php } ?>
				</li>
				<li>
					<div class="zjz-list" onclick="clickimg(2);">
						<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
						<?php if($vo['c_idcard_img1'] !=null){ ?>
						<div class="pic-front"><img id="imgr2" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img1"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr2" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img1" value="<?php echo ($vo['c_idcard_img1']); ?>" id="saveimg_2">
						<input type="file" style="display:none;" name="idcard_img1" value="" id="file2" placeholder="">
					</div>
					<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] == 1) { ?>
					<div class="box-delete <?php if($vo['c_idcard_img1'] ==null){ ?> none <?php }else{ ?>block<?php } ?>" onclick="deleteimg(2)" id="delimg_2"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
					<?php } ?>
				</li>
			</ul>
=======
			<div class="page-title">
				<div class="page-tit-name">上传身份证件</div>
			</div>
			<ul class="ul-certificate">
				<li>
					<div class="zjz-list">
						<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
						<?php if($vo['c_idcard_img'] !=null){ ?>
						<div class="pic-front"><img id="imgr1" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr1" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img" value="<?php echo ($vo['c_idcard_img']); ?>" id="saveimg_1">
						<input type="file" name="idcard_img" value="" id="file1" placeholder=""  onclick="clickimg(1);">
					</div>
					<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?>  onclick="deleteimg(1)" id="delimg_1"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
				</li>
				<li>
					<div class="zjz-list">
						<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
						<?php if($vo['c_idcard_img1'] !=null){ ?>
						<div class="pic-front"><img id="imgr2" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_idcard_img1"]); ?>" alt=""></div>
						<?php }else{ ?>
						<div class="pic-front"><img id="imgr2" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
						<?php } ?>
						<input type="hidden" name="idcard_img1" value="<?php echo ($vo['c_idcard_img1']); ?>" id="saveimg_2">
						<input type="file" name="idcard_img1" value="" id="file2" placeholder=""  onclick="clickimg(2);">
					</div>
					<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?>  onclick="deleteimg(2)" id="delimg_2"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
				</li>
			</ul>
>>>>>>> .r4017

			<div class="qy">
				<div class="page-title">
					<div class="page-tit-name">上传营业执照</div>
				</div>
				<ul class="ul-certificate">
					<li>
<<<<<<< .mine
						<div class="zjz-list" onclick="clickimg(3);">
							<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
=======
						<div class="zjz-list">
							<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
>>>>>>> .r4017
							<?php if($vo['c_charter_img'] !=null){ ?>
							<div class="pic-front"><img id="imgr3" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_charter_img"]); ?>" alt=""></div>
							<?php }else{ ?>
							<div class="pic-front"><img id="imgr3" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
							<?php } ?>
<<<<<<< .mine
							<input type="hidden" name="charter_img" value="<?php echo ($vo['c_charter_img']); ?>" id="saveimg_3">
							<input type="file" style="display:none;" name="charter_img" value="" id="file3" placeholder="">
=======
							<input type="hidden" name="charter_img" value="<?php echo ($vo['c_charter_img']); ?>" id="saveimg_3">
							<input type="file" onclick="clickimg(3);" name="charter_img" value="" id="file3" placeholder="">
>>>>>>> .r4017
						</div>
<<<<<<< .mine
						<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] == 1) { ?>
						<div class="box-delete <?php if($vo['c_charter_img'] ==null){ ?> none <?php }else{ ?>block<?php } ?>" onclick="deleteimg(3)" id="delimg_3"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
						<?php } ?>
=======
						<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?>  onclick="deleteimg(3)" id="delimg_3"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
>>>>>>> .r4017
					</li>
				</ul>
				<div class="page-title">
					<div class="page-tit-name">上传企业标志</div>
				</div>
				<ul class="ul-certificate">
					<li>
<<<<<<< .mine
						<div class="zjz-list" onclick="clickimg(4);">
							<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
=======
						<div class="zjz-list">
							<img src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt="">
>>>>>>> .r4017
							<?php if($vo['c_company_sign'] !=null){ ?>
							<div class="pic-front"><img id="imgr4" src="<?php echo GetHost(2); ?>/<?php echo ($vo["c_company_sign"]); ?>" alt=""></div>
							<?php }else{ ?>
							<div class="pic-front"><img id="imgr4" src="/wldApp/Agent/Shop/View/Public/images/add.jpg" alt=""></div>
							<?php } ?>
<<<<<<< .mine
							<input type="hidden" name="company_sign" value="<?php echo ($vo['c_company_sign']); ?>" id="saveimg_4">
							<input type="file" style="display:none;" name="company_sign" value="" id="file4" placeholder="">
=======
							<input type="hidden" name="company_sign" value="<?php echo ($vo['c_company_sign']); ?>" id="saveimg_4">
							<input type="file" onclick="clickimg(4);" name="company_sign" value="" id="file4" placeholder="">
>>>>>>> .r4017
						</div>
<<<<<<< .mine
						<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] == 1) { ?>
						<div class="box-delete <?php if($vo['c_company_sign'] ==null){ ?> none <?php }else{ ?>block<?php } ?>" onclick="deleteimg(4)" id="delimg_4"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
						<?php } ?>
					</li>
				</ul>
			</div>
			<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] != 1) { ?>
			<div class="resource-sub"><img src="/wldApp/Agent/Shop/View/Public/images/submit-g.png" alt=""></div>
			<?php } else { ?>
=======
						<div class="box-delete none" <?php if(!empty($vo['c_dcode']) && $vo['c_checked'] == 1){ ?> style="display:block" <?php } ?>  onclick="deleteimg(4)" id="delimg_4"><img src="/wldApp/Agent/Shop/View/Public/images/fc-16.png" alt=""></div>
					</li>
				</ul>
			</div>
			<?php if (!empty($vo['c_dcode']) && $vo['c_checked'] != 1) { ?>
			<div class="resource-sub"><img src="/wldApp/Agent/Shop/View/Public/images/submit-g.png" alt=""></div>
			<?php } else { ?>
>>>>>>> .r4017
			<div class="resource-sub" onclick="validateForm();"><img src="/wldApp/Agent/Shop/View/Public/images/submit-b.png" alt=""></div>
			<?php } ?>
		</div>
		<!--经纬度-->
		<input type="hidden" name="lng" id="lng" value="<?php echo ($vo['c_longitude']); ?>">
		<input type="hidden" name="lat" id="lat" value="<?php echo ($vo['c_latitude']); ?>">
	</form>
</div>
<div class="mar30"></div>
<div id="container"></div>
<script type="text/javascript">
	//点击图片
<<<<<<< .mine
	// function clickimg (id) {
	// 	$('#file' + id).uploadPreview({
	//            Img: 'imr' + id,
	//            Width: 188,
	//            Height: 188
	//        });
	// 	var gg = document.getElementById('file' + id);
	//       	gg.click();
	//       	$('#saveimg_'+id).val("");
	// }
	//点击上传图片
	function clickimg (id) {
		var gg = document.getElementById('file' + id);
       	gg.click();
       	tempUploadimg('file'+id,'imgr'+id,'saveimg_'+id);
       	$('#delimg_' + id).css("display","block");
=======
	// function clickimg (id) {
	// 	$('#file' + id).uploadPreview({
	//            Img: 'imr' + id,
	//            Width: 188,
	//            Height: 188
	//        });
	// 	var gg = document.getElementById('file' + id);
	//       	gg.click();
	//       	$('#saveimg_'+id).val("");
	// }
	//点击上传图片
	// function clickimg(id) {
	// 	var gg = document.getElementById('file' + id);
	// 	gg.click();
	// 	tempUploadimg('file' + id, 'imgr' + id, 'saveimg_' + id);
	// 	$('#delimg_' + id).css("display", "block");
	// }
	function clickimg(id) {
		document.getElementById('file' + id).onchange = function() {
			tempUploadimg('file' + id, 'imgr' + id, 'saveimg_' + id);
		}
		// var filestr = document.getElementById('file' + id);
		// filestr.click();
		$('#delimg_' + id).css("display", "block");
		//alert("外面的点击");
>>>>>>> .r4017
	}
	/*清空图片*/
	function deleteimg (id) {
		delUploadimg($('#saveimg_'+id).val());
		$('#saveimg_'+id).val("");
		$('#file'+id).val("");
		$('#imgr'+id).attr("src","/wldApp/Agent/Shop/View/Public/images/add.jpg");
		$('#delimg_'+id).css("display","none");
	}
	// 验证上传图片个数
	function checkimg(type, total) {
		var imgr = "/wldApp/Agent/Shop/View/Public/images/add.jpg";
		for (var i = 1; i <= total; i++) {
			var saveimg = $('#saveimg_' + i).val();
			beforei = $('#imgr' + i).attr("src");
			if (beforei == imgr || saveimg == "") {
				alert('请完善相关证件的图片！');
				return false;
				break;
			}
		};
		return true;
	}
	var map = new BMap.Map("container");
	var localSearch = new BMap.LocalSearch(map);

	// 表单提交验证
	var pcdaddress = "";
	function validateForm() {
		var selectedvalue = $("input:radio[name='type']:checked").val();
		if(selectedvalue==2){
			if ($('input[name="company"]').val() == '') {
				alert('请输入申请单位名称');
				$('input[name="company"]').focus();
				return false;
			}
			if ($('input[name="address"]').val() == '') {
				alert('请输入申请单位地址');
				$('input[name="address"]').focus();
				return false;
			}
			if ($('input[name="name"]').val() == '') {
				alert('请输入联系人');
				$('input[name="name"]').focus();
				return false;
			}
			if ($('input[name="companey-name"]').val() == '') {
				alert('请输入申请单位地址');
				$('input[name="companey-name"]').focus();
				return false;
			}
		} else {
			if ($('input[name="name1"]').val() == '') {
				alert('请输入真实姓名！');
				$('input[name="name1"]').focus();
				return false;
			}
		}

		var match = /^1[3|4|5|7|8][0-9]\d{8}$/;
		if (!match.exec($('input[name="phone"]').val())) {
			alert('手机号码格式错误');
			$('input[name="phone"]').focus();
            return false;
        }
		if ($('input[name="email"]').val() == '') {
			alert('请输入邮箱号');
			$('input[name="email"]').focus();
			return false;
		}else{
	        var emailreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	        if(!emailreg.test($('input[name="email"]').val())){
	        	alert('请输入正确的电子邮箱');
	        	$('input[name="email"]').focus();
				return false;
	        }
		}
		if ($('input[name="qq"]').val() == '') {
			alert('请输入QQ号');
			$('input[name="qq"]').focus();
			return false;
		}
		var province = $("#province option:selected").text();
		var city = $("#city option:selected").text();
		var district = $("#district option:selected").text();
		var addrarea = document.getElementById('addrarea').value;
		var voaddr = '<?php echo $vo["address1"] ?>';
		//if(voaddr){
			if(province=="选择省" || city=="请选择" || district=="请选择" || addrarea==""){
				alert("请完善地址信息！");
				$('#addrarea').focus();
				return false;
			}else if(province=="选择省" || city=="选择市" || district=="选择区" || addrarea==""){
				alert("请完善地址信息！");
				$('#addrarea').focus();
				return false;
			}
		//}else{
			$('#address1').val(province+"省"+city+"市"+district+addrarea);
			pcdaddress = province+city+district;/*获取经纬度地址*/
		//}

		// if ($('input[name="home_tel"]').val() == '') {
		// 	alert('请输入固定电话');
		// 	$('input[name="home_tel"]').focus();
		// 	return false;
		// }else{
		// 	var phonereg =  /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/;
		// 	if (!phonereg.test($('input[name="home_tel"]').val())) {
		// 		alert('请输入正确的固定电话');
		// 		$('input[name="home_tel"]').focus();
		// 		return false;
		// 	}
		// }
		if(selectedvalue==2) {
			if ($('input[name="postcode"]').val() == '') {
				alert('请输入邮政编码');
				$('input[name="postcode"]').focus();
				return false;
			}else{
				var postreg = /^\d{6}$/;
				if(!postreg.test($('input[name="postcode"]').val())){
					alert('请输入正确的邮政编码');
					$('input[name="postcode"]').focus();
					return false;
				}
			}
			if ($('input[name="charter"]').val() == '') {
				alert('请输入企业营业执照号');
				$('input[name="charter"]').focus();
				return false;
			}
			if (!checkimg(2, 4)) {
				return false;
			}
		}

		if ($('input[name="idcard"]').val() == '') {
			alert('请输入身份证号');
			$('input[name="idcard"]').focus();
			return false;
		}else{
			if (!checkIdcard($('input[name="idcard"]').val())) {
				alert('请输入正确的身份证号码！');
				$('input[name="idcard"]').focus();
				return false;
			}
		}
		if (!checkimg(1, 2)) {
			return false;
		}
		searchByStationName();

	}

	/*根据地址获取经纬度*/
	function searchByStationName() {
		var keyword = pcdaddress;
		localSearch.search(keyword);
	    localSearch.setSearchCompleteCallback(function (searchResult) {
	        var poi = searchResult.getPoi(0);
	        if (typeof(poi) == "undefined"){
	        	alert('地址错误');
	        	return;
	        }else{
		        $('#lng').val(poi.point.lng);
		        $('#lat').val(poi.point.lat);
	        }
	        $('#form1').submit();
	    });
	}

	/*身份证验证*/
	function checkIdcard (idcard) {
		var idreg = /^[0-9a-zA-Z]*$/g;
		// var idreg = /^(\d{18,18}|\d{15,15}|\d{17,17}x)$/;
		// switch (idcard.length) {
		// 	case 10: //台湾
		// 		if (idcard.indexOf("(") > 0) {
		// 			if (isNaN(idcard.substr(0,1))) {  //香港
		// 				idreg = /^[A-Z][0-9]{6}\([0-9A]\)$/;
		// 			} else {	//澳门
		// 				idreg = /^[157][0-9]{6}\([0-9]\)$/;
		// 			}
		// 		} else {   //台湾
		// 			idreg = /^[A-Z][0-9]{9}$/;
		// 		}
		// 		break;
		// 	default:
		// 		idreg = /^(\d{18,18}|\d{15,15}|\d{17,17}x)$/;
		// 		break;
		// }
		if (!idreg.test($('input[name="idcard"]').val())) {
			return false;
		}
		return true;
	}
</script>
<script type="text/javascript">
	var citye ='<?php echo $citye ?>';
	var newdistrict = '<?php echo $newdistrict ?>';
	window.onload=function(){
		$('#li-5 a').addClass('hover');
		re_type();

		loadRegion('province',2,'city',"<?php echo U('Personal/getRegion');?>");
	}
	/*选择资料类型*/
	function re_type () {
		// var selectedvalue = "<?php echo $vo['c_type'] ?>";
		// if (!selectedvalue) {
		// 	selectedvalue = $("input:radio[name='type']:checked").val();
		// }
		var selectedvalue = $("input:radio[name='type']:checked").val();
		if(selectedvalue==2){
			$(".qy").show();
			$(".gr").hide();
		}else{
			$(".qy").hide();
			$(".gr").show();
		}
	}
	/*获取省市区信息*/
	function loadRegion(sel, type_id, selName, url) {
		jQuery("#" + selName + " option").each(function() {
			jQuery(this).remove();
		});
		//jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#"+selName));
		if (jQuery("#" + sel).val() == 0) {
			return;
		}
		jQuery.getJSON(url, {
				parentid: jQuery("#" + sel).val(),
				regiontype: type_id
			},
			function(data) {
				//$("#district").html('<option value="" selected="selected">请选择</option>');
				if (data) {
					jQuery.each(data, function(idx, item) {
						if (item.region_name == citye && selName == "city") {
							jQuery("<option value=" + item.region_id + " selected=\"selected\">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						} else if (item.region_name == newdistrict && selName == "district") {
							jQuery("<option value=" + item.region_id + " selected=\"selected\">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						} else {
							jQuery("<option value=" + item.region_id + ">" + item.region_name + "</option>").appendTo(jQuery("#" + selName));
						}

					});
					if (selName == "city") {
						loadRegion('city', 3, 'district', "<?php echo U('Personal/getRegion');?>");
					}
				} else {

					jQuery("<option value='' selected='selected'>请选择</option>").appendTo(jQuery("#" + selName));

				}
			}
		);
	}


</script>
<div class="content-s w_960" style="display:none;">
	<form action="/wldApp/agent.php/Shop/Personal/SaveBankInfo" method="POST" id="form2">
		<div class="page-title">
			<div class="page-tit-name">收款账号</div>
		</div>
		<div class="resource-main">
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>银行开户行：</div>
				<div class="resource-r fl">
					<select name="fee_bank" id="bank-name" class="bank-name">
						<?php if ($vo['c_fee_bank']): ?>
						<option value="<?php echo ($vo["c_fee_bank"]); ?>"><?php echo ($vo["c_fee_bank"]); ?></option>
						<?php endif ?>
						<option value="">请选择银行</option>
						<option value="中国银行">中国银行</option>
				  		<option value="中国建设银行">中国建设银行</option>
				  		<option value="中国农业银行">中国农业银行</option>
				  		<option value="中国工商银行">中国工商银行</option>
				  		<option value="中国邮政银行">中国邮政银行</option>
				  		<option value="中国交通银行">中国交通银行</option>
				  		<option value="中国招商银行">中国招商银行</option>
					</select>
				</div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>支行名称：</div>
				<div class="resource-r fl"><input type="text" id="branch-name" name="fee_branch" class="resource-text" value="<?php echo ($vo["c_fee_branch"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>银行卡号：</div>
				<div class="resource-r fl"><input type="text" id="card-name" name="fee_cardnum" class="resource-text" value="<?php echo ($vo["c_fee_cardnum"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>持卡人姓名：</div>
				<div class="resource-r fl"><input type="text" id="have-name" name="fee_name" class="resource-text" value="<?php echo ($vo["c_fee_name"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>支付宝账号：</div>
				<div class="resource-r fl"><input type="text" id="alipay-name" name="fee_alipay" class="resource-text" value="<?php echo ($vo["c_fee_alipay"]); ?>"></div>
			</div>
			<div class="resource-list">
				<div class="resource-l fl"><span>*&nbsp;</span>微信账号：</div>
				<div class="resource-r fl"><input type="text" id="weixin-name" name="fee_weixin" class="resource-text" value="<?php echo ($vo["c_fee_weixin"]); ?>"></div>
			</div>
			<div class="resource-sub" onclick="subform();"><img src="/wldApp/Agent/Shop/View/Public/images/save.png" alt="" class="fl save"></div>
		</div>
	</form>
</div>
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/imgshow.js"></script>
<script type="text/javascript" src="/wldApp/Agent/Shop/View/Public/js/brows.js"></script>
<script type="text/javascript">
	function subform() {
		if ($('#bank-name').val() == '') {
			alert('请填写银行开户行');
			return false;
		}
		if ($('#branch-name').val() == '') {
			alert('请填写支行名称');
			return false;
		}
		if ($('#card-name').val() == '') {
			alert('请填写银行卡号');
			return false;
		}
		if ($('#have-name').val() == '') {
			alert('请填写持卡人姓名');
			return false;
		}
		if ($('#alipay-name').val() == '') {
			alert('请填写支付宝帐号');
			return false;
		}
		if ($('#weixin-name').val() == '') {
			alert('请填写微信帐号');
			return false;
		}

		$('#form2').submit();
	}
</script>
</body>
</html>