/*注册*/
var regsign = true;

function registers(url) {
	if(regsign) {
		regsign = false;
		var phone = $('#phone').val();
		var verify = $('#verify').val();
		var pwd = $('#pwd').val();
		var repwd = $('#repwd').val();
		var incode = $('#incode').val();
		var agree = $('#chk_agree');
		if(phone == '' || verify == '' || pwd == '' || repwd == '') {
			JqueryDialog.Show('请输入完整的信息');
			//$('#phone').focus();
			regsign = true;
			return false;
		}
		if(pwd != repwd) {
			JqueryDialog.Show('两次密码输入不一致');
			regsign = true;
			return false;
		}
		if(!agree.attr('checked')) {
			JqueryDialog.Show('您还未阅读《微领地服务协议》');
			regsign = true;
			return false;
		}

		$.post(WEB_HOST + '/index.php/Login/Index/register', {
				pwd: pwd,
				verify: verify,
				incode: incode,
				phone: phone,
				url: url
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					JqueryDialog.Show('注册成功');
					window.location.href = msg['data']['url'];
				} else {
					JqueryDialog.Show(msg['msg']);
					regsign = true;
					return false;
				}
			});
	}
}

/*修改密码*/
var pwdsign = true;

function updatapwd() {
	if(pwdsign) {
		pwdsign = false;
		var phone = $('#phone').val();
		var verify = $('#verify').val();
		var pwd = $('#pwd').val();
		var repwd = $('#repwd').val();
		if(phone == '' || verify == '' || pwd == '' || repwd == '') {
			JqueryDialog.Show('请输入完整的信息');
			pwdsign = true;
			return false;
		}
		if(pwd != repwd) {
			JqueryDialog.Show('两次密码输入不一致');
			pwdsign = true;
			return false;
		}
		$.post(WEB_HOST + '/index.php/Login/Index/forgetpwd', {
				pwd: pwd,
				verify: verify,
				phone: phone
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					JqueryDialog.Show('修改密码成功');
					window.location.href = WEB_HOST + '/index.php/Login/Index/index';
				} else {
					JqueryDialog.Show(msg['msg']);
					pwdsign = false;
					return false;
				}
			});
	}

}

/*验证手机号码*/
function phonetxt() {
	var phone = $('#phone').val();
	if(phone != "") {
		if(!(/^1[2|3|4|5|6|7|8|9|][0-9]\d{8}$/.test(phone))) {
			JqueryDialog.Show("手机号码格式错误！");
			return false;
		} else {
			return true;
		}
	} else {
		JqueryDialog.Show("请输入手机号码！");
		return false;
	}

}

//发送验证码时添加cookie
function addCookie(name, value, expiresHours) {
	var cookieString = name + "=" + escape(value);
	//判断是否设置过期时间,0代表关闭浏览器时失效
	if(expiresHours > 0) {
		var date = new Date();
		date.setTime(date.getTime() + expiresHours * 1000);
		cookieString = cookieString + ";expires=" + date.toUTCString();
	}
	document.cookie = cookieString;
}
//修改cookie的值
function editCookie(name, value, expiresHours) {
	var cookieString = name + "=" + escape(value);
	if(expiresHours > 0) {
		var date = new Date();
		date.setTime(date.getTime() + expiresHours * 1000); //单位是毫秒
		cookieString = cookieString + ";expires=" + date.toGMTString();
	}
	document.cookie = cookieString;
}
//根据名字获取cookie的值
function getCookieValue(name) {
	var strCookie = document.cookie;
	var arrCookie = strCookie.split(";");
	for(var i = 0; i < arrCookie.length; i++) {
		var arr = arrCookie[i].split("=");
		if(arr[0] == name) {
			return unescape(arr[1]);
			break;
		} else {
			return "";
			break;
		}
	}
}

/*获取验证码*/
function getverify(type) {
	sendCode($("#verify_btn"), type);
}

v = getCookieValue("secondsremained"); //获取cookie值
if(v > 0) {
	settime($("#verify_btn")); //开始倒计时
}

//发送验证码
var clicktag = true;

function sendCode(obj, type) {
	var phonenum = $("#phone").val();
	var result = phonetxt();
	if(result) {
		doPostBack(phonenum, type);
		addCookie("secondsremained", 120, 120); //添加cookie记录,有效时间120s
		settime(obj); //开始倒计时
	}
}
var clicktag = true;
//利用ajax提交到后台的发短信接口
function doPostBack(phonenum, type) {
	if(clicktag) {
		clicktag = false;
		var type = type;
		$.post(WEB_HOST + '/index.php/Login/Index/SendVerify', {
				type: type,
				phone: phonenum
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					JqueryDialog.Show('短信已发送！');
					$('#nextcode').addClass('next-hover');
					mui('.mui-content').on('tap', '#nextcode', function() {
						nextcode();
					});
				} else {
					JqueryDialog.Show(msg['msg']);
					$('#nextcode').removeClass('next-hover');
					mui('.mui-content').off('tap', '#nextcode');
					clearCookie('secondsremained');
					$("#verify_btn").text("获取校验码");
					clicktag = true;
					return false;
				}
			});
	}
}

//开始倒计时
var countdown;

function settime(obj) {
	countdown = getCookieValue("secondsremained");
	if(countdown == 0) {
		obj.removeAttr("disabled");
		obj.text("获取校验码");
		clearCookie('secondsremained');
		return;
	} else {
		obj.attr("disabled", true);
		obj.text("" + countdown + "后重发");
		countdown--;
		editCookie("secondsremained", countdown, countdown + 1);
	}
	setTimeout(function() {
		settime(obj)
	}, 1000) //每1000毫秒执行一次
}

/*清除cookie*/
function clearCookie(name) {
	var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cval = getCookieValue(name);
	if(cval != null)
		document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}