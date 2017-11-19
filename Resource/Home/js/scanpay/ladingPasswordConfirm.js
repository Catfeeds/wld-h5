var character = true;
var chk_work = "";
$(function() {
     FastClick.attach(document.body);
    var _html = "";
    _html +='<div id="key" style="position:absolute;background-color:#eee;width:99.5%;bottom:0px;">';
    /*_html +='    <div class="hide-keyp"><div class="hide-keyp-ar"><img src="'+WEB_HOST+'/APP/Home/View/Public/img/scanpay/fw-17.png"></div></div>';*/
    _html +='    <ul id="keyboard" style="margin:2px -2px 1px 2px" class="fs24">';
    _html +='        <div class="li-left">';
    _html +='            <li class="symbol"><span class="off">1</span></li>';
    _html +='            <li class="symbol"><span class="off">2</span></li>';
    _html +='            <li class="symbol"><span class="off">3</span></li>';
    _html +='            <li class="tab"><span class="off">4</span></li>';
    _html +='            <li class="symbol"><span class="off">5</span></li>';
    _html +='            <li class="symbol"><span class="off">6</span></li>';
    _html +='            <li class="symbol"><span class="off">7</span></li>';
    _html +='            <li class="tab"><span class="off">8</span></li>';
    _html +='            <li class="symbol"><span class="off">9</span></li>';
    _html +='            <li class="nbsp"><span class="off">&nbsp;</span></li>';
    _html +='            <li class="symbol"><span class="off">0</span></li>';
    _html +='            <li class="pointer"><span class="off">.</span></li>';
    _html +='        </div>';
    _html +='        <div class="li-right">';
    _html +='            <li class="delete"><div class="del-clear"><img src="'+WEB_HOST+'/APP/Home/View/Public/img/scanpay/dele.png"></div></li>';
    _html +='            <li class="symbol_pay"><div class="paybtn" id="paybtn" onmouseup="CreateScanpayOrder()">支付</div></li>';
    _html +='        </div>';
    _html +='    </ul>';
    _html +='</div>';

    $("#keyboardDIV").html(_html);
    styles();
    if($('#pay_money').text()==""){
        $('.symbol_pay').css('background',"#fff");
        $('.symbol_pay #paybtn').css('color',"#4d4d57");
        $('.scanmoney').css('color',"#ccc");
        document.getElementById('paybtn').onmouseup = null;
    }
    //$('#pay_money').click(function(){
        // $("#keyboardDIV").html(_html);
        // styles();
        // if($('#pay_money').text()==""){
        //     $('.symbol_pay').css('background',"#ccc");
        //     $('.symbol_pay #paybtn').css('color',"#555");
        //     $('.scanmoney').css('color',"#999");
        //     document.getElementById('paybtn').onmouseup = null;
        // }

        // $(".hide-keyp").click(function(){
        //     $("#keyboardDIV").html("");
        // });
        // $('#keyboard li').click(function() {
        //     var monstr = document.getElementById('pay_money');
        //     var unlen = monstr.innerHTML.length;
        //     if ($(this).hasClass('delete')) {
        //         monstr.innerHTML = monstr.innerHTML.slice(0,unlen-1);
        //         return false;
        //     }
        //     if ($(this).hasClass('symbol') || $(this).hasClass('tab') || $(this).hasClass('pointer')) {
        //         $("#pay_money").append($(this).text());
        //         if($("#pay_money").text().length>10){
        //             //JqueryDialog.Show('长度超过');
        //             $("#pay_money").text("");
        //             return false;
        //         }else{
        //             if($("#pay_money").text().indexOf('.')==0){
        //                 $("#pay_money").text("0.");
        //             }
        //             if(FindCount($("#pay_money").text(),'.')>2){
        //                 $("#pay_money").text("");
        //                 return false;
        //             }
        //             var moneys = parseFloat($("#pay_money").text());
        //             if(!isNaN(moneys)){
        //                 var match = /^\d+(\.\d{0,2})?$/;
        //                 if (!match.test(moneys)) {
        //                     JqueryDialog.Show('请输入正确的支付金额！');
        //                     $("#pay_money").text("");
        //                     return false;
        //                 }
        //             }
        //         }
        //         //placeCaretAtEnd($('#pay_money')[0]);
        //     }
        // });
    //});

    // $(".hide-keyp").click(function(){
    //     $("#keyboardDIV").html("");
    // });
    $('#keyboard li').click(function() {
        document.body.onselectstart=document.body.oncontextmenu=function(){ return false;}
        var monstr = document.getElementById('pay_money');
        var unlen = monstr.innerHTML.length;
        if ($(this).hasClass('delete')) {
            monstr.innerHTML = monstr.innerHTML.slice(0,unlen-1);
            character = true;
            if($('#pay_money').text()==""){
                $('.symbol_pay').css('background',"#fff");
                $('.symbol_pay #paybtn').css('color',"#4d4d57");
                $('.scanmoney').css('color',"#ccc");
                document.getElementById('paybtn').onmouseup = null;
            }
        }
        if ($(this).hasClass('symbol') || $(this).hasClass('tab') || $(this).hasClass('pointer')) {
            if(character){
                $("#pay_money").append($(this).text());
                $('.symbol_pay').css('background',"#46a9fa");
                $('.symbol_pay #paybtn').css('color',"#fff");
                $('.scanmoney').css('color',"#46a9fa");
                document.getElementById('paybtn').onmouseup = function(){
                    CreateScanpayOrder();
                }
                if($("#pay_money").text().length>10){
                    character = false;
                }else{
                    if($("#pay_money").text().indexOf('.')==0){
                        $("#pay_money").text("0.");
                    }
                    if(FindCount($("#pay_money").text(),'.')>1){
                        character = false;
                    }
                    var moneys = $("#pay_money").text();
                    if(!isNaN(moneys)){
                        var match = /^[0-9]+([.]{1}[0-9]{0,1})?$/;
                        if (!match.test(moneys)) {
                            character = false;
                        }
                    }
                }
            }
        }
    });
});


function styles () {
    var rh = $('.li-left').height();
    var deh = $('.delete').height();
    $('.li-right').height(rh);
    $('.paybtn').height(rh-deh);
    //$('.paybtn').css('line-height',(rh-deh-10)+"px");
}

/*A字符串在B字符串出现次数*/
function FindCount(targetStr, FindStr) {
    var start = 0;
    var aa = 0;
    var ss =targetStr.indexOf(FindStr, start);
    while (ss > -1) {
        start = ss + FindStr.length;
        aa++;
        ss = targetStr.indexOf(FindStr, start);
    }
    return aa;
}

/*实现光标显示位置*/
function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}