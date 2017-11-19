//私聊

mui('.wrap-page').on('tap', '.chatshop', function() {
	fchat($('#c_ucode').val(), $('#c_name').val());
});
/*关注*/
var atclick = 1;
mui('.wrap-page').on('tap', '.attention', function() {
	atclick = $(this).attr('title');
	attentionss(this);
});
/*代理中心*/
mui('.wrap-page').on('tap', '.dailishop', function() {
	mui.openWindow({
		url: WEB_HOST + "/index.php/Agency/Index/agentde?acode="+$('#issue_ucode').val(),
		id: "dailishop"
	});
});
// 关注操作
function attentionss(tg) {
	if(!ucode) {
		mui.confirm('关注需登录', '提示', ['取消', '确认'], function(e) {
			e.index == 0 ? "" : mui.openWindow({
				url: WEB_HOST + '/index.php/Login/Index/index?url=' + detailurl,
				id: 'login'
			});
		}, 'div');
		return;
	}

	if(atclick == 1) {
		var handle = 1;
	} else {
		var handle = 0;
	}
	$.post(WEB_HOST + '/index.php/Trade/Index/UserAttention', {
			handle: handle,
			issue_ucode: $('#issue_ucode').val()
		},
		function(obj) {
			var msg = eval(obj);
			if(msg['code'] == 0) {
				if(atclick == 1) {
					$('.attiontext').text("已关注");
					$('.attiontext').removeClass('c3');
					$('.attiontext').addClass('cb');
					$('.attentionimg').attr('src', WEB_HOST+'/Resource/Store/img/store/dpgl_icon_ygz.png');
					$(tg).attr('title', 2);
				} else {
					$('.attiontext').text("关注");
					$('.attiontext').removeClass('cb');
					$('.attiontext').addClass('c3');
					$('.attentionimg').attr('src', WEB_HOST+'/Resource/Store/img/store/dpgl_icon_jgz.png');
					$(tg).attr('title', 1);
				}
				mui.toast(msg['msg']);
			} else {
				mui.toast(msg['msg']);
			}
		}
	);
}
/*购物车*/
function operate_car(obj, pcode, type) {
	var pcode = pcode;
	$.post(WEB_HOST + "/index.php/Shopping/Entitymap/AddCar", {
		pcode: pcode,
		pucode: pucode,
		num: pnum
	}, function(msg) {
		if(msg['code'] == 0) {
			mui.toast("操作成功");
			/*1，添加；0，减少*/
			$('#ico-num').text(msg['data']['count']);
		} else {
			mui.toast(msg['msg']);
			return false;
		}
	});
}

/*删除购物车商品*/
function delecar(pcode) {
	var pcode = pcode;
	$.post(WEB_HOST + "/index.php/Shopping/Entitymap/Delecar", {
		pcode: pcode,
		acode: $('#issue_ucode').val()
	}, function(msg) {
		if(msg['code'] == 0) {
			mui.toast("操作成功");
			$('#carprice').text(msg['data']['price']);
		} else {
			mui.toast(msg['msg']);
			return false;
		}
	});
}
mui('.wrap-page').on('tap', '.look_mycart', function() {
	mycart()
});
/*查看购物车*/
function mycart() {
	if(!ucode) {
		dialogif("结算需登录！");
		return false;
	} else {
		window.location.href = WEB_HOST + "/index.php/Shopping/Entitymap/mycart?acode=" + $('#c_ucode').val();
	}
}

/*查看商品*/
function clickpro(pcode, source) {
	shopGoodsDetails(pcode, source, $('#ucode').val());
}

/*领取红包*/
mui('.details').on('tap', '.s-tem-red', function() {
	ReceiveRed();
});

var resign = true;

function ReceiveRed() {
	if(resign) {
		if(!ucode) {
			mui.confirm('领取红包需登录', '提示', ['取消', '确认'], function(e) {
				e.index == 0 ? "" : mui.openWindow({
					url: WEB_HOST + '/index.php/Login/Index/index?url=' + detailurl,
					id: 'login'
				});
			}, 'div');
			return;
		}
		resign = false;
		$.post(WEB_HOST + '/index.php/Store/Index/ReceiveRed', {
			awid: $('#cc_id').val(),
			sid: $('#sid').val()
		}, function(obj) {
			var result = eval(obj);
			resign = true;
			if(result['code'] == 0) {
				var data = result['data'];
				$('#rederror').hide();
				$('#redsuccess').show();
				$('#redmoney').text(data['value']);
				$('#redtext').text(data['name']);
				$('.s-tem-red').css('display', "none");
				$('.red-get-balace').css('display', "block");
			} else {
				$('#redsuccess').hide();
				$('#rederror').show();
				$('.s-tem-red').css('display', "none");
				$('.red-get-balace').css('display', "none");
			}

			$('.red-get-bg').fadeIn(500);
			$('.red-get-pup').fadeIn(800);
			$('.red-get-bg').height($(document).height());
//			$('.red-get-con').height($('.red-get-con').width() * 1.28);
		})

	}
}
/*查看余额*/
mui('.red-get-pup').on('tap', '.red-get-balace', function() {
	mui.openWindow({
		url: WEB_HOST + "/index.php/Balance/Index/index",
		id: "balace"
	});
});
/*关闭弹窗*/
mui('.red-get-pup').on('tap', '.red-get-btn', function() {
	$('.red-get-bg').fadeOut(200);
	$('.red-get-pup').fadeOut(500);
});

var pucode = '';
/*购买数量*/
var pnum = "";
/*加*/
function addgoods(obj, totaln, pcode) {
	if(!ucode) {
		dialogif("加入购物车需登录！");
		return false;
	} else {
		if(!isshow()) {
			mui.toast("该商品已下架！");
			return false;
		}
		var pcode = pcode;
		var pronum = $("#hid_" + obj).val();
		pnum = parseInt(pronum) + 1;
		if(pnum <= totaln) {
			$("#hid_" + obj).val(pnum);
			$('#buyinput_' + obj).val(pnum);
			$('#bysub_' + obj).css("display", "block");
			operate_car(obj, pcode, 1);
		}
	}
}
/*减*/
function subgoods(obj, totaln, pcode) {
	var pronum = $("#hid_" + obj).val();
	pnum = parseInt(pronum);
	if(pnum >= 1) {
		pnum = pnum - 1;
		if(pnum == 0) {
			$('#bysub_' + obj).css("display", "none");
			$("#buyinput_" + obj).val("");
			$("#hid_" + obj).val('0');
			delecar(pcode);
			var iconum = parseInt($('#ico-num').text());
			$('#ico-num').text(iconum - 1);
		} else {
			$("#buyinput_" + obj).val(pnum);
			$("#hid_" + obj).val(pnum);
			operate_car(obj, pcode, 0);
		}
	}
}



/*是否下架*/
function isshow() {
	var isshow = $('#c_ishow').val();
	if(isshow == 2) {
		mui.toast('该商品已下架！');
		return false;
	}
	return true;
}
mui.plusReady(function() {});


function dialogif(desc) {
	mui.confirm(desc, '提示', ['取消', '确认'], function(e) {
		e.index == 0 ? "" : mui.openWindow({
			url: WEB_HOST + '/index.php/Login/Index/index?url=' + detailurl,
			id: 'login'
		});
	}, 'div');
}