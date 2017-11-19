var ctrls = true;
var check_pass_word = '';
var passwords = $('#password').get(0);
var allInputs = document.getElementById("password").getElementsByTagName('input');
var character, index = 0;
$(function() {
    var _pwdhtml = "";
    _pwdhtml +='<div class="techsup"><img src="'+WEB_HOST+'/Resource/Common/img/techsurpot.png"/></div>'
    _pwdhtml += '<div id="key_pwd" style="position:absolute;background-color:#eee;width:99.5%;bottom:0px;z-index:1000;">';
    _pwdhtml += '    <ul id="keyboard_pwd" style="margin:2px -2px 1px 2px" class="fs24">';
    _pwdhtml += '            <li class="symbol"><span class="off">1</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">2</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">3</span></li>';
    _pwdhtml += '            <li class="tab"><span class="off">4</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">5</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">6</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">7</span></li>';
    _pwdhtml += '            <li class="tab"><span class="off">8</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">9</span></li>';
    _pwdhtml += '            <li class="nbsp"><span class="off">&nbsp;</span></li>';
    _pwdhtml += '            <li class="symbol"><span class="off">0</span></li>';
    _pwdhtml += '            <li class="delete"><div class="del-clear"><img src="' + WEB_HOST + '/Resource/Balance/img/dele.png"></div></li>';
    _pwdhtml += '    </ul>';
    _pwdhtml += '</div>';
    $("#keyboardDIV").html(_pwdhtml);
    $("#password").attr("disabled", true);
    $("input.pass").attr("disabled", true);
    $('#keyboard_pwd li').click(function(){
    //mui('#key_pwd').on('tap','#keyboard_pwd li',function() {
        if ($(this).hasClass('delete')) {
            $(passwords.elements[--index % 6]).val('');
            if ($(passwords.elements[0]).val() == '') {
                index = 0;
            }
        }
        if ($(this).hasClass('symbol') || $(this).hasClass('tab')) {
                character = $(this).text();
                $(passwords.elements[index++ % 6]).val(character);
                if ($(passwords.elements[5]).val() != '') {
                    index = 0;
                }
                if ($(passwords.elements[5]).val() != '') {
                    var temp_rePass_word = '';
                    for (var i = 0; i < allInputs.length; i++) {
                        if (allInputs[i].type == 'password' && allInputs[i].name == 'pass') {
                            temp_rePass_word += allInputs[i].value;
                        }
                    }
                    check_pass_word = temp_rePass_word;
                    $.ajax({
                        url: WEB_HOST + '/index.php/Balance/Index/checksecurity',
                        type: 'post',
                        data: {
                            securitycode: check_pass_word
                        },
                        dataType: 'json',
                        success: function(data) {
                            var msg = eval(data);
                            if (msg['code'] == 0) {
                                drawpwd();
                            } else {
                                mui.toast(msg['msg']);
                                allInputs[0].value = "";
                                allInputs[1].value = "";
                                allInputs[2].value = "";
                                allInputs[3].value = "";
                                allInputs[4].value = "";
                                allInputs[5].value = "";
                                check_pass_word = "";
                                index = 0;
                            }
                        }
                    });
                    // $(".set-pwd-bg").hide();
                    // $(".set-pwd-pup").hide();
                    // $("#keyboardDIV").hide();
                }
        }
    });
});

mui('.set-pwd-con').on('tap','.set-pwd-tit',function() {
    allInputs[0].value = "";
    allInputs[1].value = "";
    allInputs[2].value = "";
    allInputs[3].value = "";
    allInputs[4].value = "";
    allInputs[5].value = "";
    check_pass_word = "";
    index = 0;
    $('.set-pwd-bg').hide();
    $('.set-pwd-pup').hide();
    $('#keyboardDIV').hide();
});
var dradsign = true;
/*提交申请*/
function drawpwd() {
	if(dradsign){
		dradsign = false;
	    var attrbul = getFormAttrs('form1');
	    $.ajax({
	        type: "POST",
	        url: WEB_HOST + '/index.php/Balance/Index/withdrawing',
	        data: "attrbul=" + JSON.stringify(attrbul),
	        dataType: "json",
	        success: function(json) {
	            var msg = eval(json);
	            if (msg.code == 0) {
	                ctrls = false;
	                mui.toast(msg.msg);
	                setTimeout(function() {
	                    window.location.href = WEB_HOST + '/index.php/Balance/Index/successes?sign=' + $('#sign').val() + '&mymoney=' + $('input[name=mymoney]').val();
	                }, 1000);
	            } else {
	                mui.toast(msg.msg);
	                allInputs[0].value = "";
	                allInputs[1].value = "";
	                allInputs[2].value = "";
	                allInputs[3].value = "";
	                allInputs[4].value = "";
	                allInputs[5].value = "";
	                check_pass_word = "";
	                index = 0;
	                dradsign = true;
	            }
	        }
	    });
	}
}