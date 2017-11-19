$(function() {
	/*金额输入*/
	$('#keyboard li').click(function() {
		document.body.onselectstart = document.body.oncontextmenu = function() {
			return false;
		}
		var monstr = $('#red_money');
		var unlen = monstr.text().length;
		var resultmon = $('#totalmon').text();
		var resultmval = $('#hidredmon').val();
		if($(this).hasClass('delete')) {
			monstr.text(monstr.text().slice(0, unlen - 1));
			if((unlen - 1) == 0) {
				monstr.text("0.00");
			}
			chk_work = chk_work.slice(0, chk_work.length - 1);
			$('#totalmon').text(resultmon.slice(0, resultmon.length - 1));
			if((resultmon.length - 1) == 0) {
				$('#totalmon').text("0.00");
			}
			$('#hidredmon').val(resultmval.slice(0, resultmval.length - 1));

			if($('input[name="redbit"]').val() != "") {
				checktxt($("#hidredmon").val(), $('input[name="redbit"]').val());
			} else {
				checktxt($("#hidredmon").val(), "");
			}

		}
		if($(this).hasClass('symbol') || $(this).hasClass('tab') || $(this).hasClass('pointer')) {
			if(chk_work.length > 5) {
				return false;
			} else {
				//判断第一位不为小数
				if(chk_work.length == 0 && $(this).text() == ".") {
					return false;
				}
				var arrobj = new Array();
				arrobj = chk_work.split('');
				var bl = '';
				for(var i = 0; i <= arrobj.length; i++) {
					if(arrobj[i] == ".") {
						bl = i;
						if($(this).text() == ".") {
							return false;
						}
					}
					if(bl && i == parseInt(bl - (-3))) {
						return false;
					}
				}
				chk_work += $(this).text();
				$("#red_money").text(chk_work);
				$('#hidredmon').val(chk_work);
				$('#totalmon').text(chk_work);
				if($('input[name="redbit"]').val() != "") {
					checktxt($("#hidredmon").val(), $('input[name="redbit"]').val());
				} else {
					checktxt($("#hidredmon").val(), "");
				}
			}
		}
	});
});

var paytjsign = true;
/*支付提交*/
function PayRedMoney() {
	if(paytjsign) {
		paytjsign = false;
		var rtmon = $('#totalmm').val();
		var rtnum = $('#redbit').val();
		var redname = $('#red_remark').val();
		var joinaid = $('#joinaid').val();
		if(redname == "") {
			redname = "福旺财旺人气旺，山高水长福寿长！";
		} else {
			redname = $('#red_remark').val();
		}

		$.post(WEB_HOST + "/index.php/Activity/Rednewact/AddRedCard", {
			name: redname,
			totalnum: rtnum,
			money: rtmon,
			type: redctype,
			joinaid: joinaid,
		}, function(obj) {
			var data = eval(obj);
			if(data['code'] == 0) {
				//mui.toast(data['msg']);
				var backurl = $('#backurl').val();
				if(!backurl) {
					backurl = WEB_HOST + "/index.php/Activity/Rednewact/index";
				}
				setTimeout(function() {
					mui.openWindow({
						url: backurl,
						id: 'index'
					});
				}, 1000);
			} else {
				mui.toast(data['msg']);
				paytjsign = true;
			}
		});
	}
}

(function() {
	function tabForward(e) {
		e = e || window.event;
		var target = e.target || e.srcElement;
		if(target.value.length === target.maxLength) {
			var form = target.form;
			for(var i = 0, len = form.elements.length - 1; i < len; i++) {
				if(form.elements[i] === target) {
					if(form.elements[i++]) {
						form.elements[i++].focus();
					}
					break;
				}
			}
		}
	}
	var form = document.getElementById("password");
	form.addEventListener("keyup", tabForward, false);
})();