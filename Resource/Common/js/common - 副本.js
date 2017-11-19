/*
 *  本地存储设置与读取
 *
 **/

// 存储json数据
function set(key, v) {
	var curTime = new Date().getTime();
	if(window.localStorage) {
		localStorage.setItem(key, JSONstringify({
			data: v,
			time: curTime
		}));
		return true;
	}
	return false;
}

// 读取本地存储json数据
function get(key) {
	var exp = 1 * 3600 * 1000;
	if(!window.localStorage) {
		return false;
	}

	var data = localStorage.getItem(key);
	var dataobj = JSON.parse(data);
	var time = new Date().getTime();
	if((time - dataobj.time) > exp) {
		return false;
	} else {
		return dataobj.data.msg;
	}
}

// 数组转换为JSON字符串
function JSONstringify(Json) {
	if($.browser.msie) {
		if($.browser.version == "7.0" || $.browser.version == "6.0") {
			var result = jQuery.parseJSON(Json);
		} else {
			var result = JSON.stringify(Json);
		}
	} else {
		var result = JSON.stringify(Json);
	}
	return result;
}

/**
 * 获得表单单元元素
 */
function getFormAttrs(forms) {
	var formObj = document.getElementById(forms);
	var sp_arr = new Object();
	if(forms == 'CONSIGNEE_ADDRESS') {
		sp_arr['provincename'] = $('#province').find("option:selected").text();
		sp_arr['cityname'] = $('#city').find("option:selected").text();
		sp_arr['districtname'] = $('#district').find("option:selected").text();
	}
	for(i = 0; i < formObj.elements.length; i++) {
		if(((formObj.elements[i].type == 'radio' || formObj.elements[i].type == 'checkbox') && formObj.elements[i].checked) || formObj.elements[i].tagName == 'SELECT' || formObj.elements[i].type == 'hidden' || formObj.elements[i].type == 'text' || formObj.elements[i].type == 'textarea' || formObj.elements[i].type == 'password' || formObj.elements[i].type == 'number' || formObj.elements[i].type == 'date' || formObj.elements[i].type == 'tel') {
			sp_arr[formObj.elements[i].name] = formObj.elements[i].value;
		}
	}
	return sp_arr;
}
/**
 * 加入收藏
 */
function AddFavorite(sURL, sTitle) {
	try {
		window.external.addFavorite(sURL, sTitle);
	} catch(e) {
		try {
			window.sidebar.addPanel(sTitle, sURL, "");
		} catch(e) {
			alert("加入收藏失败，请使用Ctrl+D进行添加");
		}
	}
}
/**
 * 设为首页
 */
function SetHome(obj, vrl) {
	try {
		obj.style.behavior = 'url(#default#homepage)';
		obj.setHomePage(vrl);
	} catch(e) {
		if(window.netscape) {
			try {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			} catch(e) {
				alert("此操作被浏览器拒绝！请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为'true'");
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage', vrl);
		}
	}
}

/*不能输入中文*/
function checknum(str) {
	var type = /^[0-9]*[1-9][0-9]*$/;
	var re = new RegExp(type);
	if(str.match(re) == null) {
		return false;
	}
	return true;
}

/*空值判断*/
function emptyval(str) {
	if(str == null || typeof(str) == "undefined" || str == "") {
		return true;
	}
	return false;
}

/*返回app首页*/
function jumphome(type) {
	if(type == 1) { /*安卓*/
		javaScript: resultData.goFind();
	}
	else if(type == 2) { /*ios*/
		window.webkit.messageHandlers.AppModel.postMessage({
			"home": "10000"
		});
	}
}

//动态上传多张图片
function tempUploadimg(sid, imgshow, imgval, str, obj) {
	var str = str;
	$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Common/img/load.gif");
	$.ajaxFileUpload({
		url: WEB_HOST + '/index.php/Base/Upload/uploadify', //用于文件上传的服务器端请求地址
		secureuri: false, //一般设置为false
		fileElementId: sid, //文件上传空间的id属性  <input type="file" id="file" name="file" />
		dataType: 'json', //返回值类型 一般设置为json
		success: function(result, status) //服务器成功响应处理函数
		{
			if(result.code == 0) {
				var data = result['data'];
				$('#' + imgshow).attr('src', data.imgshow);
				$('#' + imgval).val(data.imgval);
				//$('#' + sid).attr('disabled', 'disabled');
				$('#delimg_' + str).css("display", "block");
				dianji(obj);
			} else {
				mui.toast(result.msg);
				$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Trade/img/district_adv_add2x.png");
			}
		},
		error: function(data, status, e) //服务器响应失败处理函数
		{
			mui.toast(e);
			$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Trade/img/district_adv_add2x.png");
		}
	});
}

//静态上传多张图片
function tempUploadimgStatic(sid, imgshow, imgval, str, delid, sign) {
	var str = str;
	$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Common/img/load.gif");
	$.ajaxFileUpload({
		url: WEB_HOST + '/index.php/Base/Upload/uploadify?sign=' + sign, //用于文件上传的服务器端请求地址
		secureuri: false, //一般设置为false
		fileElementId: sid, //文件上传空间的id属性  <input type="file" id="file" name="file" />
		dataType: 'json', //返回值类型 一般设置为json
		success: function(result, status) //服务器成功响应处理函数
		{
			if(result.code == 0) {
				var data = result['data'];
				$('#' + imgshow).attr('src', data.imgshow);
				$('#' + imgval).val(data.imgval);
				//$('#' + sid).attr('disabled', 'disabled');
				$('#' + delid).css("display", "block");
			} else {
				mui.toast(result.msg);
				$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Home/img/getbusiness/fc-17.jpg");
			}
		},
		error: function(data, status, e) //服务器响应失败处理函数
		{
			mui.toast(e);
			$('#' + imgshow).attr('src', WEB_HOST + "/Resource/Home/img/getbusiness/fc-17.jpg");
		}
	});
}

//用户删除图片
function delUploadimg(path) {
	var get_url = WEB_HOST + '/index.php/Base/Upload/delimg';
	$.get(get_url, {
		name: path
	}, function(ret) {
		var result = eval(ret);
	});
}

/*数字字母*/
function checknumabc(str) {
	var idreg = /^[0-9a-zA-Z]*$/g;
	if(!idreg.test(str)) {
		return false;
	}
	return true;
}
/*输入数字*/
function checknumber(str) {
	var reg = /^\d+$/;
	if(!reg.test(str)) {
		return false;
	}
	return true;
}
/*邮箱验证*/
function checkemail(str) {
	var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
	if(!reg.test(str)) {
		return false;
	}
	return true;
}
/*去掉空格*/
function checktrim(str) {
	var reg = /^\s+|\s+$/g;
	return str.replace(reg, '');
}

/*检查字符串是否存在中文*/
function ischinese(str) {
	var reg = /[\u4e00-\u9fa5]/gm;
	if(!reg.test(str)) {
		return false;
	}
	return true;
}
/*检查字符串是否为合法手机号码*/
function checkphone(str) {
	var reg = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/;
	if(!reg.test(str)) {
		return false;
	}
	return true;
}


/*公用js文件*/
/*定义一个对象 来承载我们封装的事件*/
window.slide = {};/*定义了一个itcast的对象*/
//封装一个transitionEnd  过度结束事件
slide.transitionEnd = function(dom,callback){
    /* 需要绑定事件的dom  绑定之后  当触发了 事件的时候  执行 callback */
    if(dom && typeof  dom == 'object'){
        dom.addEventListener('webkitTransitionEnd',function(){
            /*if(callback){
                callback();
            }*/
            callback && callback();
        });
        dom.addEventListener('transitionEnd',function(){
            callback && callback();
        });
    }
}
/*封装tap*/
slide.tap = function(dom,callback){
    /*
     * 要求  没有触发 touchmove 事件
     *       并且响应速度要比click快
    */
    if(dom && typeof  dom == 'object'){
        var isMove = false;
        var startTime = 0;
        dom.addEventListener('touchstart',function(e){
            //console.log('touchstart');
            //console.time('tap');/*记录tap这个参数现在的时间*/
            startTime = Date.now();
        });
        dom.addEventListener('touchmove',function(e){
            //console.log('touchmove');
            isMove = true;
        });
        dom.addEventListener('touchend',function(e){
            //console.log('touchend');
            //console.timeEnd('tap')/*打印tap这个参数距离上一次记录的时候的时间*/
            /*判读  是否满足tap 的要求  一般要求tap的响应时间150*/
            if(!isMove && (Date.now()-startTime) < 150){
                /*调用 callback*/
                callback && callback(e);
            }
            /*重置 参数*/
            isMove = false;
            startTime = 0;
        });
    }
}




//局部滚动
if(!window.slide) {
	window.slide = {};
}
slide.iScroll = function(args) {
	/*调用的时候没有初始化的话就是初始化一次*/
	if(!(this instanceof arguments.callee)) return new arguments.callee(args);
	this.init(args);
};
slide.iScroll.prototype = {
	constructor: slide.iScroll,
	init: function(args) {
		/*局部变量来接受当前的this*/
		var that = this;
		/*如果传入的对象是一个Dom对象就把他看作是我们的大容器盒子*/
		if(args.swipeDom && typeof args.swipeDom == 'object') {
			that.parentDom = args.swipeDom;
		}
		/*如果不存在父容器就停止初始化*/
		if(!that.parentDom) return false;
		/*找到子容器*/
		that.childDom = that.parentDom.children && that.parentDom.children[0] ? that.parentDom.children[0] : '';
		/*如果不存在子容器就停止初始化*/
		if(!that.childDom) return false;
		/*初始化传入的参数*/
		that.settings = {};
		/*默认类型  默认的Y轴滑动 如果不是y的话就是以x轴开始滑动*/
		that.settings.swipeType = args.swipeType ? args.swipeType : 'y';
		/*默认的缓冲滑动距离*/
		that.settings.swipeDistance = args.swipeDistance >= 0 ? args.swipeDistance : 150;
		/*初始化滑动*/
		that._scroll();
	},
	/*对外开放的设置定位的方法*/
	setTranslate: function(translate) {
		this.currPostion = translate;
		this._addTransition();
		this._changeTranslate(this.currPostion);
	},
	_addTransition: function() {
		this.childDom.style.transition = "all .2s ease";
		this.childDom.style.webkitTransition = "all .2s ease"; /*兼容 老版本webkit内核浏览器*/
	},
	_removeTransition: function() {
		this.childDom.style.transition = "none";
		this.childDom.style.webkitTransition = "none"; /*兼容 老版本webkit内核浏览器*/
	},
	_changeTranslate: function(translate) {
		if(this.settings.swipeType == 'y') {
			this.childDom.style.transform = "translateY(" + translate + "px)";
			this.childDom.style.webkitTransform = "translateY(" + translate + "px)";
		} else {
			this.childDom.style.transform = "translateX(" + translate + "px)";
			this.childDom.style.webkitTransform = "translateX(" + translate + "px)";
		}
	},
	_scroll: function() {
		/*局部变量来接受当前的this*/
		var that = this;
		/*滑动的类型*/
		var type = that.settings.swipeType == 'y' ? true : false;
		/*父容器的高度或宽度*/
		var parentHeight = type ? that.parentDom.offsetHeight : that.parentDom.offsetWidth;
		/*子容器的高度或宽度*/
		var childHeight = type ? that.childDom.offsetHeight : that.childDom.offsetWidth;

		/*子容器没有父容器大的时候*/
		if(childHeight < parentHeight) {
			if(type) {
				that.childDom.style.height = parentHeight + 'px';
				childHeight = parentHeight;
			} else {
				that.childDom.style.width = parentHeight + 'px';
				childHeight = parentHeight;
			}
		}

		/*缓冲距离*/
		var distance = that.settings.swipeDistance;
		/*区间*/
		/*左侧盒子定位的区间*/
		that.maxPostion = 0;
		that.minPostion = -(childHeight - parentHeight);
		/*设置滑动的当前位置*/
		that.currPostion = 0;
		that.startPostion = 0;
		that.endPostion = 0;
		that.movePostion = 0;
		/*1.滑动*/
		that.childDom.addEventListener('touchstart', function(e) {
			/*初始的Y的坐标*/
			that.startPostion = type ? e.touches[0].clientY : e.touches[0].clientX;
		}, false);
		that.childDom.addEventListener('touchmove', function(e) {
			e.preventDefault();
			/*不停的做滑动的时候记录的endY的值*/
			that.endPostion = type ? e.touches[0].clientY : e.touches[0].clientX;
			that.movePostion = that.startPostion - that.endPostion; /*计算了移动的距离*/

			/*2.滑动区间*/
			/*就是滑动区间*/
			if((that.currPostion - that.movePostion) < (that.maxPostion + distance) &&
				(that.currPostion - that.movePostion) > (that.minPostion - distance)) {
				that._removeTransition();
				that._changeTranslate(that.currPostion - that.movePostion);
			}
		}, false);
		window.addEventListener('touchend', function(e) {
			/*在限制滑动区间之后 重新计算当前定位*/
			/*判断是否在我们的合理定位区间内*/
			/*先向下滑动 */
			if((that.currPostion - that.movePostion) > that.maxPostion) {
				that.currPostion = that.maxPostion;
				that._addTransition();
				that._changeTranslate(that.currPostion);
			}
			/*想上滑动的时候*/
			else if((that.currPostion - that.movePostion) < that.minPostion) {
				that.currPostion = that.minPostion;
				that._addTransition();
				that._changeTranslate(that.currPostion);
			}
			/*正常的情况*/
			else {
				that.currPostion = that.currPostion - that.movePostion;
			}
			that._reset();
		}, false);

	},
	_reset: function() {
		var that = this;
		that.startPostion = 0;
		that.endPostion = 0;
		that.movePostion = 0;
	}
};