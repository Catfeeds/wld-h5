/**
 *支付安全密码
 */
/*all*/
var check_pass_word = '';
var allInputs,character, varindex = 0;

var SafePwdDialog = {
    Show:function (desc,type,redctype) {
        var _pwdhtml = "";
        _pwdhtml+='<div class="set-pwd-bg"></div>';
        _pwdhtml+='<div class="set-pwd-pup">';
        _pwdhtml+='<div class="set-pwd-con">';
        /*type=1,支付，提现；type=2红包等*/
        if(type==1){
            _pwdhtml+='<div class="set-pwd-tit divtab">';
            _pwdhtml+='<div class="set-cancel fs16 c9 fl"><img src="'+WEB_HOST+'/Resource/Balance/img/cha.png" alt=""></div>';
            _pwdhtml+='<div class="set-font-pwd fs16 c3 fl">'+desc+'</div>';
            _pwdhtml+='</div>';
        }else if(type==2){
            _pwdhtml+='<div class="set-pwd-tit divtab">';
            _pwdhtml+='<div class="set-cancel fl"><img src="'+WEB_HOST+'/Resource/Activity/img/rednewact/cha.png" alt=""></div>';
            _pwdhtml+='<div class="set-font-pwd fl"><span class="fs16 c3">支付</span><br><span class="fs14 c3" id="pup_red_type">普通红包</span></div>';
            _pwdhtml+='</div>';
            _pwdhtml+='<div class="set-pwd-money c3 bborder"><span class="fs12">￥</span><span id="pwdmoney" class="fs18">0.00</span></div>';
            _pwdhtml+='<div class="set-pwd-surplus c3 bborder"><span class="fs14 c3">余额支付</span><span id="surplusmon" class="fs12 c9">剩余：'+$('#balancemon').val()+'元</span></div>';
        }
        _pwdhtml+='<div class="pwd-input-list divtab">';
        _pwdhtml+='<form id="password">';
        _pwdhtml+='<input readonly class="pass fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_1">';
        _pwdhtml+='<input readonly class="pass fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_2">';
        _pwdhtml+='<input readonly class="pass fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_3">';
        _pwdhtml+='<input readonly class="pass fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_4">';
        _pwdhtml+='<input readonly class="pass fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_5">';
        _pwdhtml+='<input readonly class="pass pass_right fs24" style="padding: 1.5% 0;" name="pass" type="password" maxlength="1" value="" id="pass_6">';
        _pwdhtml+='</form>';
        _pwdhtml+='</div>';
        _pwdhtml+='<p style="padding: 0 0 3% 0;width: 100%;text-align: center;color: red" id="error_pwdmsg"></p>';
        _pwdhtml+='</div>';
        _pwdhtml+='</div>';
        _pwdhtml+='<div id="keyboardPwd" class="none"></div>';
        $("body").prepend(_pwdhtml);
        /*键盘*/
        var _keybordhtml = "";
        _keybordhtml += '<div class="techsup"><img src="' + WEB_HOST + '/Resource/Common/img/techsurpot.png"/></div>'
        _keybordhtml += '<div id="key_pwd" style="position:absolute;background-color:#eee;width:99.5%;bottom:0px;z-index:1000;">';
        _keybordhtml += '    <ul id="keyboard_pwd" class="fs24">';
        _keybordhtml += '            <li class="symbol"><span class="off">1</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">2</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">3</span></li>';
        _keybordhtml += '            <li class="tab"><span class="off">4</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">5</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">6</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">7</span></li>';
        _keybordhtml += '            <li class="tab"><span class="off">8</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">9</span></li>';
        _keybordhtml += '            <li class="nbsp"><span class="off">&nbsp;</span></li>';
        _keybordhtml += '            <li class="symbol"><span class="off">0</span></li>';
        _keybordhtml += '            <li class="delete"><div class="del-clear"><img src="' + WEB_HOST + '/Resource/Balance/img/dele.png"></div></li>';
        _keybordhtml += '    </ul>';
        _keybordhtml += '</div>';
        $("#keyboardPwd").html(_keybordhtml);
        if(type==1){
            $('input[name="money"]').blur();
        }else{
            if(redctype == 2) {
                $('#pup_red_type').text("拼手气红包");
            } else {
                $('#pup_red_type').text("普通红包");
            }
            $('#pwdmoney').text($('#totalmm').val());
        }
        $(".set-pwd-bg").show();
        $(".set-pwd-pup").show();
        $(".set-pwd-bg").height($(document).height());
        $('#keyboardPwd').show();
        $('.techsup').css('bottom', $('#keyboard_pwd').height()+10+"px");
        $('#keyboard_pwd .delete').height($('#keyboard_pwd .symbol').height());
        $('#keyboard_pwd .del-clear').height($('#keyboard_pwd .del-clear').width()*0.70);
        $('#keyboard_pwd .symbol').height($('#keyboard_pwd .symbol').height());
        $('#keyboard_pwd .tab').height($('#keyboard_pwd .symbol').height());
        $('#keyboard_pwd .nbsp').height($('#keyboard_pwd .symbol').height());

        /*关闭*/
        mui('.set-pwd-con').on('tap','.set-pwd-tit',function () {
            clearpwdinput();
            $('.set-pwd-bg').remove();
            $('.set-pwd-pup').remove();
            $('#keyboardPwd').remove();
        });
    }
}

/*检测用户是否设置支付安全码*/
function checkNo(detailurl,type,opr,payid,money,cbid,cbmoney,orderid) {
    $.post(WEB_HOST + '/index.php/Balance/Index/checkNo',function(data) {
        var msg = eval(data);
        if(msg['code'] == 0) {
            /*1未设置密码*/
            if(msg['flag']==1){
                mui.confirm('未设置支付密码，请前往“账户安全”设置','提示',['取消','设置'],function (e) {
                    e.index == 0 ? "" : mui.openWindow({url:WEB_HOST+"/index.php/Home/Users/safepwdtel?action=add&url="+detailurl,id:'safepwdtel'});
                },'div');
            }else{
                if(msg['data']==1){
                    mui.confirm('支付密码已连续错误5次，请30分钟后重试','提示',['取消','忘记密码'],function (e) {
                        e.index == 0 ? "" : mui.openWindow({url:WEB_HOST+"/index.php/Home/Users/safepwdtel?action=add&url="+detailurl,id:'safepwdtel'});
                    },'div');
                    return;
                }else{
                    /*弹出支付密码窗*/
                    SafePwdDialog.Show("请输入支付安全密码",type);
                    oprationpwd(detailurl,opr,payid,money,cbid,cbmoney,orderid);
                }
            }
        }else{
            mui.toast(msg['msg']);
        }
    });
}

/*键盘opration*/
function oprationpwd(detailurl,opr,payid,money,cbid,cbmoney,orderid) {
    var passwords = $('#password').get(0);
    allInputs = document.getElementById("password").getElementsByTagName('input');
    $("#password").attr("disabled", true);
    $("input.pass").attr("disabled", true);
    $('#keyboard_pwd li').click(function () {
        if ($(this).hasClass('delete')) {
            $(passwords.elements[--varindex % 6]).val('');
            if ($(passwords.elements[0]).val() == '') {
                varindex = 0;
            }
        }
        if ($(this).hasClass('symbol') || $(this).hasClass('tab')) {
            character = $(this).text();
            $(passwords.elements[varindex++ % 6]).val(character);
            if ($(passwords.elements[5]).val() != '') {
                varindex = 0;
            }
            if ($(passwords.elements[5]).val() != '') {
                var temp_rePass_word = '';
                for (var i = 0; i < allInputs.length; i++) {
                    if (allInputs[i].type == 'password' && allInputs[i].name == 'pass') {
                        temp_rePass_word += allInputs[i].value;
                    }
                }
                check_pass_word = temp_rePass_word;
                checksafepwd(detailurl,opr,check_pass_word,payid,money,cbid,cbmoney,orderid);
            }
        }
    });
}

/*验证安全密码*/
function checksafepwd(detailurl,opr,check_pass_word,payid,money,cbid,cbmoney,orderid) {
    $.post(WEB_HOST + '/index.php/Balance/Index/validateNo', {
        safepwd: check_pass_word
    }, function(data) {
        var msg = eval(data);
        if(msg['code']==0){
            if(msg['data']==5){
                mui.confirm('支付密码已连续错误5次，请30分钟后重试','提示',['取消','忘记密码'],function (e) {
                    e.index == 0 ? "" : mui.openWindow({url:WEB_HOST+"/index.php/Home/Users/safepwdtel?action=add&url="+detailurl,id:'safepwdtel'});
                },'div');
                clearpwdinput();
                $('.set-pwd-bg').remove();
                $('.set-pwd-pup').remove();
                $('#keyboardPwd').remove();
                return;
            }else if(msg['data'] && msg['data']<5 && msg['data']>0){
                $('#error_pwdmsg').text("支付密码错误，您还有"+(5-msg['data'])+"次机会");
                clearpwdinput();
                return;
            }else {
                $('#error_pwdmsg').text("");
                mui.toast("支付成功");
                //安全密码验证成功执行后续操作
                /*opr=1支付payment;opr=2提现whitdraw;por=3红包redpacket*/
                setTimeout(function () {
                    if(opr==1){
                        moneypay(payid, money, cbid, cbmoney, orderid);
                    }else if(opr==2){
                        drawpwd();
                    }else if(opr==3){
                        PayRedMoney();
                    }
                },1500);
            }
        }
    });
}


/*取消，关闭->清空密码*/
function clearpwdinput() {
    allInputs[0].value = "";
    allInputs[1].value = "";
    allInputs[2].value = "";
    allInputs[3].value = "";
    allInputs[4].value = "";
    allInputs[5].value = "";
    check_pass_word = "";
    varindex = 0;
}

