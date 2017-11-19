$(function() {
	var myh = $(".myspace-head").width();
	$(".myspace-head").width(myh);
	$(".myspace-head").height(myh);
	$(".myspace-head").css("border-radius", myh + "px");
	var sjh = myh * 10 / 100;
	$('.myspace-sj-hy').css('top', -sjh + 'px');

	$(".myspace-s-head").click(function() {
		$('.self-space-bg').fadeIn(200);
		$('.self-space-view').fadeIn(200);
		$(".self-space-bg").height($(document).height());

		var selfh = $("#self-head").width();
		$("#self-heads").width(selfh);
		$("#self-heads").height(selfh);
	});
	$(".self-space-bg").click(function() {
		$('.self-space-bg').fadeOut(200);
		$('.self-space-view').fadeOut(200);
	});

	var imgh = $('.myspace-ss-img ul li').width();
	$('.myspace-ss-img ul li').height(imgh);
	
	space_style();
});

/*动态列表样式*/
function space_style () {
	var himg = $('.f-head-img').width();
	$('.f-head-img').height(himg);
	$('.f-head-img').css("border-radius",himg+"px");

	var hfont = $('.f-head-img').height(),
	hf = hfont*50/100;
	$('.f-name-time').css('line-height',hf+'px');

	var pimgh = $('.f-product-img').width(),
	pnameh = pimgh*50/100,
	arrowm = pimgh*25/100;
	$('.f-product-img').height(pimgh);
	$('.f-pro-names-price').css('line-height',pnameh+'px');
	$('.f-pro-arrow').css('margin-top',arrowm+'px');

	var praiseh = $('.f-add-praise').height();
	$('.f-add-praise-num').css('line-height',praiseh+'px');

	var flimg = $('.f-img-list li').width();
	$('.f-img-list li').height(flimg);
	
	$('.s-circlename').css('line-height',$('.s-circlename').height()+'px');
	
	$('.sendcomm-praise').css('line-height',$('.sendcomm-praise').height()+'px');
}
/*商盟用户查询列表*/
var ctrls = true;
var emptyval = true;
var pageindex = 1;
var ucode = $('#ucode').val();

coalitionlist();
$(window).bind('scroll', function() {
	if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
		if(ctrls && emptyval) {
			coalitionlist();
		}
	}
});

/*商盟用户查询列表*/
function coalitionlist() {
	var urlstr = GET_HOST + "/index.php/Home/Myspace/GetResourceList?pageindex=" + pageindex + "&issue_ucode=" + $('#issue_ucode').val();
	var _html = "";
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: urlstr,
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
				$('#rm-list').empty();
			}
			var mgs = eval(obj);
			if(mgs['code'] == 0) {
				var data = mgs.data;
				if(data == null || data.list == null) {
					if(pageindex == 1) {
						_html += '<div class="baoqian">没有找到相关信息</div>';
					}
					emptyval = false;
				} else {
					if(pageindex <= data.pageCount) {
						pageindex++;
						var datalist = data.list;
						for(var i = 0; i < datalist.length; i++) {
							_html += '<div class="source-list">';
							if(datalist[i]['is_like'] == 0) {
								var oplike = 1;
							} else {
								var oplike = 0;
							}
							if(datalist[i]['is_attention'] == 0) {
								var opatten = 1;
							} else {
								var opatten = 0;
							}
							_html += '<input type="hidden" id="prastatu' + datalist[i]['c_id'] + '" value="' + oplike + '">';
							_html += '<input type="hidden" id="attentatu' + datalist[i]['c_id'] + '" value="' + opatten + '">';
							_html += '<input type="hidden" id="rid_' + pageindex + '_' + i + '" value="' + datalist[i]['c_id'] + '"/>';
							_html += '<div class="fl source-left">';
							var switch_addtime = datalist[i]['switch_addtime'].split("|");
							_html += '<div class="c3 timsmonth">' + switch_addtime[0] + '</div><div class="c9 timshours">' + switch_addtime[1] + '</div>';
							if(datalist[i]['is_delete'] == 1) {
								_html += '<div class="soucedel" onclick="delsource(this,\'' + datalist[i]['c_id'] + '\',1)">删除</div>';
							}
							_html += '</div>';
							_html += '<div class="fl source-right">';
							_html += '<div class="source-top">';
							if(pageindex == 2 && i == 0) {
								_html += '<img src="' + GET_HOST + '/Resource/Store/img/index/stbg.png" alt="">';
							} else {
								_html += '<img src="' + GET_HOST + '/Resource/Store/img/index/stbg1.png" alt="">';
							}
							_html += '</div>';
							_html += '<div class="source-cotent">';
							_html += '<div class="source-body">';

							if (datalist[i]['c_content']) {
								var contentstr = datalist[i]['c_content'].substring(0, 90);
							} else {
								var contentstr = '';
							}
							
							_html += '<div class="f-description new-desc" onclick="location.href=\'' + WEB_HOST + '/index.php/Trade/Index/redetail?rid=' + datalist[i]['c_id'] + '&circlename='+datalist[i]['circle_name']+'\'">' + contentstr + '</div>';

							var imglist = datalist[i]['imglist'];							
							if(imglist.length > 0) {								
								_html += '<ul class="f-img-list divtab">';
								for(var j = 0; j < imglist.length; j++) {
									if(j == 1 || j == 4 || j == 7) {
										_html += '<li class="swipebox aimgclass"><img src="' + imglist[j]['c_thumbnail_img'] + '" alt=""></li>';
									} else {
										_html += '<li class="swipebox"><img src="' + imglist[j]['c_thumbnail_img'] + '" alt=""></li>';
									}
								};
								_html += '</ul>';								
							}

							var produce = datalist[i]['tj_product'];
							if(produce.length > 0) {
								for(var k = 0; k < produce.length; k++) {
									_html += '<a class="f-product-info" href="' + produce[k]['url'] + '">';
									_html += '<div class="f-product-img fl"><img src="' + produce[k]['c_pimg'] + '" alt=""></div>';
									_html += '<div class="f-pro-names-price fl">';
									_html += '<div class="f-product-name">' + produce[k]['c_name'] + '</div>';
									_html += '<div class="f-product-price">￥' + produce[k]['c_price'] + '</div>';
									_html += '</div>';
									_html += '<div class="f-pro-arrow fr">';
									_html += '<img src="' + GET_HOST + '/Resource/Home/img/myspace/s_15.png" alt="">';
									_html += '</div>';
									_html += '</a>';
								};
							}

							_html += '<div class="f-comment-all">';
							// _html += '<span class="f-comment-tit">全部评论('+datalist[i]['c_comment']+')</span>';
							_html += '<div class="f-comm-list" id="comment-' + datalist[i]['c_id'] + '">';
							var comment = datalist[i]['comment_list'];
							if(comment.length > 0) {
								for(var c = 0; c < comment.length; c++) {
									if(!comment[c]['upucode']) {
										_html += '<div class="f-comm-box"><span class="f-comm-send"  onclick="sendcommt(\'' + datalist[i]['c_id'] + '\',\'回复：' + comment[c]['c_nickname'] + '\',\'' + comment[c]['c_ucode'] + '\',\'' + comment[c]['c_id'] + '\')"><span>' + comment[c]['c_nickname'] + '</span>：<em>' + comment[c]['c_content'] + '</em></span>';
										if(ucode == comment[c]['c_ucode']) {
											_html += '<span class="f-com-del" onclick="DeleteComment(this,\'' + comment[c]['c_id'] + '\');">&nbsp;&nbsp;&nbsp;删除</span>';
										}
										_html += '</div>';
									} else {
										_html += '<div class="f-comm-box"><span class="f-comm-reply"  onclick="sendcommt(\'' + datalist[i]['c_id'] + '\',\'回复：' + comment[c]['c_nickname'] + '\',\'' + comment[c]['c_ucode'] + '\',\'' + comment[c]['c_id'] + '\')"><span>' + comment[c]['c_nickname'] + '</span><em>&nbsp;回复&nbsp;</em><span>' + comment[c]['upnickname'] + '</span>：<em>' + comment[c]['c_content'] + '</em></span>';
										if(ucode == comment[c]['c_ucode']) {
											_html += '<span class="f-com-del" onclick="DeleteComment(this,\'' + comment[c]['c_id'] + '\');">&nbsp;&nbsp;&nbsp;删除</span>';
										}
										_html += '</div>';
									}
								};
							}
							_html += '</div>';

							_html += '<a href="' + WEB_HOST + '/index.php/Trade/Index/redetail?rid=' + datalist[i]['c_id'] + '&circlename='+datalist[i]['circle_name']+'" class="f-check-all-comm">查看详情</a>';

							_html += '<div class="f-comm-ico-list">';

							_html += '<div class="send-comm-tipimg send-comm-tipimg1 fl" onclick="sendcommt(\'' + datalist[i]['c_id'] + '\',\'评论\')"><img src="' + GET_HOST + '/Resource/Home/img/myspace/s-04_03.png" alt=""></div>';
							_html += '<div class="sendcomm-praisenum sendcomm-praisenum1 fr" id="praisenum-' + datalist[i]['c_id'] + '">' + datalist[i]['c_like'] + '</div>';

							_html += '<div class="sendcomm-praise sendcomm-praise1 fr">';
							if(datalist[i]['is_like'] == 1) {
								_html += '<img src="' + GET_HOST + '/Resource/Home/img/myspace/s_34.png" alt="" id="praiseimg-' + datalist[i]['c_id'] + '" onclick="addpraise(\'' + datalist[i]['c_id'] + '\',0)">';
							} else {
								_html += '<img src="' + GET_HOST + '/Resource/Home/img/myspace/s_22.png" alt="" id="praiseimg-' + datalist[i]['c_id'] + '" onclick="addpraise(\'' + datalist[i]['c_id'] + '\',1)">';
							}
							_html += '</div>';
							_html += '</div>';
							_html += '</div>';
							_html += '</div>';
							_html += '</div>';
							_html += '</div>';
							_html += '</div>';

						};
					} else {
						emptyval = false;
					}
				}
			} else {
				emptyval = false;
			}
			$('#rm-list').append(_html);
		},
		complete: function() {
			space_style();
			/*查看资源图片*/
			$(".f-img-list").each(function(index) {
				$(this).viewer({
					title: false
				});
			});		
			$('#console').css('display', 'none');
			ctrls = true;
		}
	});
}