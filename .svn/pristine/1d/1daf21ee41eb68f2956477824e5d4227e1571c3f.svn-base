/*
*  本地存储设置与读取
*
**/

// 存储json数据
function set(key,v) {
	var curTime = new Date().getTime();
	if (window.localStorage) {
		localStorage.setItem(key,JSONstringify({data:v,time:curTime}));
		return true;
	}
	return false;
}

// 读取本地存储json数据
function get(key) {
	var exp = 1*3600*1000;
	if (!window.localStorage) {
		return false;
	}

	var data = localStorage.getItem(key);
	var dataobj = JSON.parse(data);
	var time = new Date().getTime();
	if ((time - dataobj.time) > exp) {
		return false;
	} else {
		return dataobj.data.msg;
	}
}


// 数组转换为JSON字符串
function JSONstringify(Json){
	if($.browser.msie){
	   if($.browser.version=="7.0"||$.browser.version=="6.0"){
		  var  result=jQuery.parseJSON(Json);
	   }else{
		  var result=JSON.stringify(Json);
	   }
	}else{
		var result=JSON.stringify(Json);
	}
	return result;
}

/**
 * 获得表单单元元素
 */
function getFormAttrs(forms) {
	var formObj=document.getElementById(forms);
	var sp_arr = new Object();
	if (forms == 'CONSIGNEE_ADDRESS') {
		sp_arr['provincename'] = $('#province').find("option:selected").text();
		sp_arr['cityname'] = $('#city').find("option:selected").text();
		sp_arr['districtname'] = $('#district').find("option:selected").text();
	}
	for (i = 0; i < formObj.elements.length; i++) {
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
	} catch (e) {
		try {
			window.sidebar.addPanel(sTitle, sURL, "");
		} catch (e) {
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
	} catch (e) {
		if (window.netscape) {
			try {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			} catch (e) {
				alert("此操作被浏览器拒绝！请在浏览器地址栏输入“about:config”并回车然后将[signed.applets.codebase_principal_support]设置为'true'");
			}
			var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
			prefs.setCharPref('browser.startup.homepage', vrl);
		}
	}
}


/*不能输入中文*/
function checknum(str){
	var type = /^[0-9]*[1-9][0-9]*$/;
    var re = new RegExp(type);
    if (str.match(re) == null) {
        return false;
    }
    return true;
}

/*空值判断*/
function emptyval (str) {
    if (str == null || typeof(str) == "undefined" || str =="") {
        return true;
    }
    return false;
}

/*返回app首页*/
function jumphome(type) {
	if (type == 1) {/*安卓*/
		javaScript:resultData.goFind();
	} else if (type == 2) { /*ios*/
		window.webkit.messageHandlers.AppModel.postMessage({"home": "10000"});
	}
}

//用户通用上传图片
function tempUploadimg(sid, imgshow, imgval) {
	//document.getElementById(sid).onchange = function(event) {
		$('#' + imgshow).attr('src', WEB_HOST+"/Uploads/load.gif");
		$.ajaxFileUpload({
			url: WEB_HOST + '/agent.php/Home/Upload/uploadify?sign=1', //用于文件上传的服务器端请求地址
			secureuri: false, //一般设置为false
			fileElementId: sid, //文件上传空间的id属性  <input type="file" id="file" name="file" />
			dataType: 'json', //返回值类型 一般设置为json
			success: function(result, status) //服务器成功响应处理函数
			{
				if (result.code == 0) {
					var data = result['data'];
					$('#' + imgshow).attr('src', data.imgshow);
					$('#' + imgval).val(data.imgval);
					$('#delimg_' + sid).css("display", "block");
					//JqueryDialog.Show(result.msg);
				} else {
					alert(result.msg);
					$('#' + imgshow).attr('src', WEB_HOST+"/Agent/Shop/View/Public/images/add.jpg");
				}
			},
			error: function(data, status, e) //服务器响应失败处理函数
			{
				alert(e);
				$('#' + imgshow).attr('src', WEB_HOST+"/Agent/Shop/View/Public/images/add.jpg");
			}
		});
	//}
}

//用户删除图片
function delUploadimg(path) {
	var get_url = WEB_HOST + '/agent.php/Home/Upload/delimg';
	$.get(get_url, {
		name:path
	}, function(ret) {
		var result = eval(ret);
		/*JqueryDialog.Show(result.msg);*/
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
    if (!reg.test(str)) {
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
    if (!reg.test(str)) {
        return false;
    }
    return true;
}
/*检查字符串是否为合法手机号码*/
function checkphone(str) {
    var reg = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/;
    if (!reg.test(str)) {
        return false;
    }
    return true;
}
