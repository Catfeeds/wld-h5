window.onload = function() {

	var sh = $(".sm-header-center").height();
	var ch = sh * 26 / 100;
	$(".sm-header-left img").css("margin-top", ch + "px");
	$(".sm-header-right img").css("margin-top", ch + "px");
	$('.sm-networking').addClass('hover');

	var txth = $('.sm-search-con').height();
	$('#keyword').css("line-height", txth + 'px');
	$('#btnsearch').css("line-height", txth + 'px');

	/*人脉列表样式*/
	liststyle();
}

function liststyle() {
	// var slh = $('.sm-rm-box-l').width();
	// $('.sm-rm-box-l').height(slh);
	// $('.sm-rm-box-l').css('border-radius',slh+'px');
	var slh = $('.sm-rm-box-head').width();
	$('.sm-rm-box-head').height(slh);
	$('.sm-rm-box-head').css('border-radius', slh + 'px');

	var sr = slh * 32 / 100;
	$(".sm-rm-box-r").css("line-height", sr + "px");

}

/*商盟用户查询列表*/
var ctrls = true;
var emptyval = true;
var pageindex = 1;
var province = '';
var city = '';
var town = '';
var tab = '';
var name = '';
var trade = '';

coalitionlist(province, city, town, tab, name, trade, pageindex);
$(window).bind('scroll', function() {
	if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
		if(ctrls && emptyval) {
			coalitionlist(province, city, town, tab, name, trade, pageindex);
		}
	}
});

/*商盟用户查询列表*/
function coalitionlist(pr, c, t, tab, nkname, job, page) {
	province = pr;
	city = c;
	town = t;
	tab = tab;
	name = nkname;
	trade = job;
	if(page == 1) {
		pageindex = 1;
	}
	var urlstr = 'province=' + province + '&city=' + city + '&town=' + town + '&tab=' + tab + '&name=' + name + '&trade=' + trade + '&pageindex=' + pageindex;
	var url = WEB_HOST + "/index.php/Home/Coalition/CoalitionUserwebList?" + urlstr;
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
							var desc = '';
							if(datalist[i]['c_signature'] != null) {
								var strr = "" + datalist[i]['c_signature'] + "";
								desc = strr.replace(/(^s*)|(s*$)/g, "");
							} else {
								desc = '主人很懒，什么都没留下！';
							}
							var fromucode = "'" + datalist[i]['c_ucode'] + "'";
							_html += '<div class="sm-rm-box" onclick="loadMyspace(' + fromucode + ')">';
							_html += '<div class="sm-rm-box-l">';
							_html += '<div class="sm-rm-box-head"><img src="' + datalist[i]['c_headimg'] + '" alt=""></div>';
							if(datalist[i]['c_shop'] == 1) {
								_html += '<div class="sm-rm-box-sj"><img src="' + WEB_HOST + '/APP/Home/View/Public/img/coalition/sj.png" alt=""></div>';
							} else {
								_html += '<div class="sm-rm-box-sj"><img src="' + WEB_HOST + '/APP/Home/View/Public/img/coalition/dl.png" alt=""></div>';
							}
							_html += '</div>';
							_html += '<div class="sm-rm-box-r">';
							if(datalist[i]['c_nickname'] != null) {
								_html += '<div class="rm-name">' + datalist[i]['c_nickname'] + '</div>';
							}
							// else{
							// _html+='		<div class="rm-name">暂无</div>';
							// }
							_html += '<div class="rm-area-tag">';

							if(datalist[i]['c_trade'] == null) {
								if(datalist[i]['c_tab']) {
									_html += '<div class="rm-job">标签：' + datalist[i]['c_tab'] + '</div>';
								} else {
									_html += '<div class="rm-job">标签：暂无</div>';
								}
							} else {
								_html += '<div class="rm-job">行业：' + datalist[i]['c_trade'] + '</div>';
							}
							var citys = '';
							var towns = '';
							if(datalist[i]['c_city'] != null) {
								citys = datalist[i]['c_city'];
							} else if(datalist[i]['c_region'] != null) {
								towns = datalist[i]['c_region'];
							};
							_html += '<div class="rm-area">距离：' + datalist[i]['c_distance'] + '</div>';
							_html += '</div>';
							_html += '<div class="rm-desc">' + desc + '</div>';
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
			//$('#console').css('display', 'none');
		},
		complete: function() {
			liststyle();
			$('#console').css('display', 'none');
			ctrls = true;
		}
	});
}

/*点击按钮搜索*/
function searchfun() {
	pageindex = 1;
	var keyword = $('#keyword').val();
	// if(keyword == "")
	// {
	// 	mui.toast('请输入搜索关键字');
	// 	$('#keyword').focus();
	// 	return false;
	// }
	// else
	// {
	province = keyword;
	city = keyword;
	town = keyword;
	tab = keyword;
	name = keyword;
	trade = keyword;
	sex = keyword;
	coalitionlist(province, city, town, tab, name, trade, sex, 1);
	//}

}

/*查看个人空间*/
function loadMyspace(ucode) {
	var fucode = ucode;
	window.location.href = WEB_HOST + "/index.php/Home/Myspace/index?fromucode=" + fucode;
}