//发送验证码时添加cookie
function addCookie(name,value,expiresHours){ 
    var expiresDate= new Date();
	expiresDate.setTime(expiresDate.getTime() + (expiresHours * 1000));
	//alert(expiresDate.getTime() + (expiresHours * 1000));
	$.cookie(name, value, {expires : expiresDate});
} 
//修改cookie的值
function editCookie(name,value,expiresHours){ 
    var expiresDate= new Date();
	expiresDate.setTime(expiresDate.getTime() + (expiresHours * 1000));	
	$.cookie(name, value, {expires : expiresDate});	
} 
//根据名字获取cookie的值
function getCookieValue(name){ 
    return $.cookie(name);
}

var clicksign = true;
var ctrl_str = true;
//发送验证码
function sendCode(obj,type,name,obj2){
	if(clicksign){
		clicksign = false;
	    var phonenum = obj2.val();
	    var result = isPhoneNum(obj2);
	    if(result){
		    $.ajax({
		        async : false,
		        cache : false,
		        type : 'POST',
		        url : WEB_HOST+'/index.php/Login/Index/sendVerify',// 请求的action路径
		        data:{"phone":phonenum,'type':type},
		        error : function() {// 请求失败处理函数
		        },
		        success : function(data){
			        var da = eval(data);
				    if(da.code==0){
				        mui.toast('短信已发送');	
				        ctrl_str = true;
			        	addCookie(name,120,120);//添加cookie记录,有效时间120s
			        	settimep(obj,name);//开始倒计时
				    }else{//返回验证码
				        mui.toast(da.msg);
				        ctrl_str = false;
				        clicksign = true;
				    }	      	
		        }
		    });
	    }		
	}
	return ctrl_str;
}

//开始倒计时
var countdown;
var myVar; 
function settimep(obj,name) { 
	countdown = parseInt($.cookie(name));
    if (countdown == 0 || isNaN(countdown)) {   
    	clearTimeout(myVar);   
        obj.html('获取验证码'); 
        obj.removeAttr("disabled"); 
        $.cookie(name,null);
        return false;
    } else { 
        obj.attr("disabled", true); 
        obj.html(countdown+"秒后重发送"); 
        countdown--; 
        editCookie(name,countdown,countdown+1);
    } 
	myVar = setTimeout(function() {settimep(obj,name)},1000); 
}

//校验手机号是否合法
function isPhoneNum(obj){
    var phonenum = obj.val();
    var myreg = /^1[3|4|5|7|8][0-9]\d{8}$/; 
    if(!myreg.test(phonenum)){ 
        mui.toast('请输入有效的手机号码');
        return false; 
    }else{
        return true;
    }
}
