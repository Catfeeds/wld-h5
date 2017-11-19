/*弹出层*/
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
function layer_show(title,url,w,h){
	if (url.indexOf("/") > 0) {
		url = HHOME+'/' + url;
	}
	if (title == null || title == '') {
		title=false;
	};
	if (url == null || url == '') {
		url="404.html";
	};
	if (w == null || w == '') {
		w=800;
	};
	if (h == null || h == '') {
		h=($(window).height() - 50);
	};
	layer.open({
		type: 2,
		area: [w+'px', h +'px'],
		fix: false, //不固定
		maxmin: true,
		shade:0.4,
		title: title,
		content: url
	});
}
/*关闭弹出框口*/
function layer_close(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
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
	var formObj = document.getElementById(forms);	
	var sp_arr = new Object();	
	if (forms == 'CONSIGNEE_ADDRESS') {
		sp_arr['provincename'] = $('#province').find("option:selected").text();
		sp_arr['cityname'] = $('#city').find("option:selected").text();
		sp_arr['districtname'] = $('#district').find("option:selected").text();
	}
	for (i = 0; i < formObj.elements.length; i++) {
		if (((formObj.elements[i].type == 'radio' || formObj.elements[i].type == 'checkbox') && formObj.elements[i].checked) || formObj.elements[i].tagName == 'SELECT' || formObj.elements[i].type == 'hidden' || formObj.elements[i].type == 'text' || formObj.elements[i].type == 'textarea' || formObj.elements[i].type == 'password' ) {
			sp_arr[formObj.elements[i].name] = formObj.elements[i].value;
		}
	}
	return sp_arr;
}
/**
 * 表单静态ajax提交数据
 * url,forms
 */ 
function ajax_post_data(url,forms,resurl) {
	var attrbul = getFormAttrs(forms);
	var str = JSON.stringify(attrbul);
	$.post(HHOME+'/' + url,{str:str}, function(obj) {
		var result = eval(obj);		
        if (result['code'] != 0) {
        	layer.msg(result['msg'],{icon:10,time:2000});        	      	
        } else {
        	layer.msg(result['msg'],{icon:1,time:2000});
        	window.parent.location.href  = HHOME+'/' + resurl;
        }        
    });
}

/**
 * 数据静态ajax提交数据
 * url,forms
 */ 
function ajax_data(url,data,resurl) {
	var e = new RegExp("&","g"); 
	var str1 = data.replace(e,"':'");
	var f = new RegExp("'","g"); 
	var str = str1.replace(f,'"');
	$.post(HHOME+'/' + url,{str:str}, function(obj) {
		var result = eval(obj);		
        if (result['code'] != 0) {
        	layer.msg(result['msg'],{icon:10,time:2000});        	      	
        } else {
        	layer.msg(result['msg'],{icon:1,time:2000});
        	window.parent.location.href  = HHOME+'/' + resurl;
        }        
    });
}