var check_pass_word = '';
var passwords = $('#password').get(0);
var pwd2 = $('#pwd2').get(0);
$(function() {
	var _pwdhtml = "";
    _pwdhtml +='<div class="techsup"><img src="'+WEB_HOST+'/Resource/Common/img/techsurpot.png"/></div>'
    _pwdhtml += '<div id="key_pwd" style="position:absolute;background-color:#eee;width:99.5%;bottom:0px;z-index:100000;">';    
    _pwdhtml += '    <ul id="keyboard_pwd" style="margin:2px -2px 1px 2px" class="fs20">';
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
	$('.techsup').css('bottom', $('#keyboard_pwd').height()+10+"px");
	$('#keyboard_pwd .delete').height($('#keyboard_pwd .symbol').height());
	$('#keyboard_pwd .del-clear').height($('#keyboard_pwd .del-clear').width()*0.70);	
	var character, index = 0,
		index_2 = 0;
	$("#password").attr("disabled", true);
	$("#pwe2").attr("disabled", true);
	$("input.pass").attr("disabled", true);
	var allInputs = document.getElementById("password").getElementsByTagName('input');
	var allInputs2 = document.getElementById("pwd2").getElementsByTagName('input');
	$('#keyboard_pwd li').click(function() {
		if($(this).hasClass('delete')) {
			if($('#pwd2').css('display') == "none") {
				$(passwords.elements[--index % 6]).val('');
				if($(passwords.elements[0]).val() == '') {
					index = 0;
				}
			} else {
				$(pwd2.elements[--index_2 % 6]).val('');
				if($(pwd2.elements[0]).val() == '') {
					index_2 = 0;
				}
			}
			return false;
		}
		if($(this).hasClass('symbol') || $(this).hasClass('tab')) {

			if($('#pwd2').css('display') == "none") {
				character = $(this).text();
				$(passwords.elements[index++ % 6]).val(character);
				if($(passwords.elements[5]).val() != '') {
					index = 0;
				}
				if($(passwords.elements[5]).val() != '') {
					var temp_rePass_word = '';
					for(var i = 0; i < allInputs.length; i++) {
						if(allInputs[i].type == 'password' && allInputs[i].name == 'pass') {
							temp_rePass_word += allInputs[i].value;
						}
					}
					check_pass_word = temp_rePass_word;
					$('#check_pass_word').val(temp_rePass_word);
					$('#pagetit').text("确认安全密码");
					$('#password').css('display', "none");
					$('#pwd2').css('display', "block");
				}

			} else {
				character = $(this).text();
				$(pwd2.elements[index_2++ % 6]).val(character);
				if($(pwd2.elements[5]).val() != '') {
					index_2 = 0;
				}
				if($(pwd2.elements[5]).val() != '') {
					var temp_rePass_word = '';
					for(var i = 0; i < allInputs2.length; i++) {
						if(allInputs2[i].type == 'password' && allInputs2[i].name == 'pass2') {
							temp_rePass_word += allInputs2[i].value;
						}
					}
					check_pass_word = temp_rePass_word;
					$('#check_pwd_2').val(temp_rePass_word);
					if($('#check_pass_word').val() != $('#check_pwd_2').val()) {
						mui.toast("两次密码输入不一致");
						setTimeout("location.reload()", 800);
					} else {
						var pwdflag = true;
						if(pwdflag){
							pwdflag = false;
							$.ajax({
								url: WEB_HOST + '/index.php/Home/Users/Setpwd',
								type: 'post',
								data: {
									safepwd: $('#check_pass_word').val(),
									affirm_safepwd: $('#check_pwd_2').val()
								},
								dataType: 'json',
								success: function(data) {
									var msg = eval(data);
									if(msg['code'] == 0) {
										mui.toast('设置成功！');
										setTimeout(function() {
											window.location.href = $('#url').val();
										}, 1000);
									} else {
										mui.toast(msg['msg']);
										pwdflag = true;
										setTimeout("location.reload()", 1000);
									}
								}
							});
						}
					}
				}
			}
		}
	});
});