/*
* *
*系统自定义弹出框
* *
*/
var JqueryDialog = {

Open:function(Desc){
	//获取客户端页面宽高
	var _client_width = document.body.clientWidth;
	var _client_height = document.documentElement.scrollHeight;

	//create shadow
	if(typeof($("#jd_shadow")[0]) == "undefined"){
		//前置
		$("body").prepend("<div id='jd_shadow'>&nbsp;</div>");		
	}

	//create dialog
	if(typeof($("#jd_dialog")[0]) != "undefined"){
		$("#jd_dialog").remove();
	}
	
	var show = '';	
	show +='<div id="jd_dialog">';
	show +='<div id="jd_dialog_h">提示</div>';		
	show +='<table align="center" id="jd_dialog_c">';
	show +='<tr>';
	show +='<td align="center">'+Desc+'</td>';
	show +='</tr>';			
	show +='</table>';
	show +='<div id="jd_dialog_b">';
	show +='<div id="jd_dialog_l" onclick="JqueryDialog.Close()">否</div>';
	show +='<div id="jd_dialog_r" onclick="JqueryDialog.Ok()">是</div>';
	show +='</div>';
	show +='</div>';
	$("body").prepend(show);	
},

Show:function(desc,type) {	
	var show = '';
	show +='<table align="center" id="jd_show_time">';
	show +='<tr>';
	show +='<td align="center">'+desc+'</td>';
	show +='</tr>';			
	show +='</table>';	
	if (type=='frame') {
		// parent.document.getElementById("body").innerHTML = show;
		// $("body", document.frames("ifram_sign").document).html(show);
		$(window.parent.document).find("body").prepend(show);		
		setTimeout(function(){
			$(window.parent.document).find("#jd_show_time").remove();
		},2000);
	} else {
		$("body").prepend(show);
		setTimeout(function(){
			$("#jd_show_time").remove();
		},2000);	
	}
},


Ok:function() {
	$("#jd_shadow").remove();
	$("#jd_dialog").remove();	
	return true;
},


/// <summary>关闭模态窗口</summary>
Close:function(){
	$("#jd_shadow").remove();
	$("#jd_dialog").remove();
	return false;
},


}