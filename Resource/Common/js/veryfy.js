//IE6 屏蔽JS错误提示
 function ResumeError() {
 	 return true;
	 }
 window.onerror = ResumeError;

//表单验证
//校验普通电话、传真号码：可以“+”开头，除数字外，可含有“-”
function isTel(s)
{
	// var pattern =/^\d{3,4}\-\d{7}$/;
	 var pattern =/^(([0\+]\d{2,3}-)?(0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;
	 if(pattern.test(s))
	 {
	  return true;
	 }
	 return false;
}
	
//校验手机号码：必须以数字开头，除数字外，可含有“-”
function isMobile(s)
	{
	 var patrn=/^(13[0-9]{9})|(15[0-9]{9})|(18[0-9]{9})$/;
	 //var patrn=/^(13[0-9]{9})|(15[89][0-9]{8})|(18[0-9]{9})$/;
	 if (!patrn.test(s)){
	   return false;
	  }
	  return true;
}

 //校验邮箱
function isEmail(s)
{
  reg=/^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/gi;
  if(!reg.test(s))
  {
      return false;
  }
  return true;
}
function ismod(v){  
	var a = /^((\(\d{3}\))|(\d{3}\-))?13\d{9}|15[89]\d{8}$/ ;  
	if( v.length!=11||!v.match(a) ){  
		return false;
	}else{  
		return true;
	}  
}

function isqq(v){
	var reg = /^[1-9]\d{4,13}$/;
	if( reg.test(v) ){  
		return true;
	}else{  
		return false;
	}  

} 

//检查是否全是中文
function ischn(str){ 
	var reg = /^[\u4E00-\u9FA5]+$/; 
	if(!reg.test(str)){ 
		return false; 
	} else{
		return true; 
	}
} 
//必须含有中文
function isstrchn(v){
	var rname=/[\u4E00-\u9FA5]/; 
	if(!rname.test(v)){  //必须含汉字
		//alert("必须含汉字!"); 
		return true; 
	 }else{
		return false;
	 }
 }

//是否全是英文字符
function isstr(v) 
{ 
	var str = /[_a-zA-Z]/; 
	if(str.test(v)) 
	{ 
		return true;
	}else{
		return false;
	}
} 
//创建一个等待窗口
function createwindow(){
	obj = $('.openwindow');
	if(typeof(obj)!='undefined') $(obj).remove();
	h = getScrollTop();
	var str = '<div class="openwindow"><img src="'+SITE_URL+'theme/images/loadings.gif"  align="absmiddle"/></div>';
	$("body").append(str);
	$('.openwindow').css('position','absolute');
	$('.openwindow').css('left',((screen.availWidth-240)/2)+'px');
	$('.openwindow').css('top',((screen.availHeight-40)/2)+'px');
	$('.openwindow').css('margin-top',(h-80)+'px');
	$('.openwindow').show("slow");
}

//移除一个窗口
function removewindow(){
	$('.openwindow').remove();
}

//创建一个带有关闭的窗口
function meswindow(mes,title,ww,hh){
	if(ww==null || ww=="" || typeof(ww)=="undefined") ww = 300;
	if(hh==null || hh=="" || typeof(hh)=="undefined") hh = 100;
	if(title==null || title=="" || typeof(title)=="undefined") title = "头部";
	obj = $('.meswindow');
	if(typeof(obj)!='undefined') $(obj).remove();
	if(mes==""||typeof(mes)=='undefined') mes = '操作成功!';
	h = getScrollTop();   
	var str = '<div class="meswindow"><p class="p_hear"><span>'+title+'</span><a onclick="closewindow(this)" href="javascript:;">关闭</a></p>'+mes+'</div>';
	$("body").append(str);
	//$('.window_box').css('height',document.body.scrollHeight);
	//$('.window_box').css('width',document.body.scrollWidth);
	
	$('.meswindow').css('position','absolute');
	$('.meswindow').css('height',hh);
	$('.meswindow').css('width',ww);
	
	$('.meswindow').css('left',((screen.availWidth-ww)/2)+'px');
	$('.meswindow').css('top',((screen.availHeight-h)/2)+'px');
	$('.meswindow').css('margin-top',(h)+'px');
	$('.meswindow').show();
	return true;
}

/********************
 * 取窗口滚动条滚动高度  
 ******************/
function getScrollTop()
{
  var scrollTop=0;
  if(document.documentElement&&document.documentElement.scrollTop)
  {
  scrollTop=document.documentElement.scrollTop;
  }
  else if(document.body)
  {
  scrollTop=document.body.scrollTop;
  }
  return scrollTop;
}




//关闭窗口
function closewindow(obj){
	$(obj).parent().parent().hide("slow");
	$(obj).parent().parent().remove();
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
	if (!idreg.test(idcard)) {
		return false;
	}
	return true;
}


