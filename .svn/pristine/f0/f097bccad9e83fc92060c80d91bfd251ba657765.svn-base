$(function() {
    //setInterval('AutoScroll("#listname")', 2000); /*中奖名单滚动*/
    get_list(); /*获取中奖名单信息*/
    function get_list() {
        $.getJSON(WEB_HOST + '/index.php/Activity/Index/handdrawList', function(json) {
            var data = eval(json);
            if (data['code'] == 0) {
                var _List = data['data'];
                var listDate = "<ul>";
                $.each(_List, function(i) {
                    listDate = listDate + '<li class="c5"><div class="xm">' + _List[i]['name'] + '</div><div class="jp">' +
                        _List[i]['praisecontent'] + '</div><div class="sj">' + _List[i]['time'] + '</div></li>';
                });
                listDate = listDate + '</ul>';

                $('#listname').html(listDate);
            }
        });
    }
    setInterval(get_list(), 100);

    $('.winning-bg').click(function(){
        $(".winning-bg").fadeOut(300);
        $(".winning-pup").fadeOut(300);
    });
    $('.winning-close').click(function(){
        $(".winning-bg").fadeOut(300);
        $(".winning-pup").fadeOut(300);
    });
    $('.winning-sure').click(function(){
        $(".winning-bg").fadeOut(300);
        $(".winning-pup").fadeOut(300);
    });

    $('.pup-num-sure').click(function(){
        $(".winning-bg-num").fadeOut(300);
        $(".winning-pup-num").fadeOut(300);
    });
    $('.winning-bg-num').click(function(){
        $(".winning-bg-num").fadeOut(300);
        $(".winning-pup-num").fadeOut(300);
    });

});

var lih = $("#listname ul li").height();
function AutoScroll(obj) {
    $(obj).find("ul:first").animate({
        marginTop: "-" + lih + "px"
    }, 500, function() {
        $(this).css({
            marginTop: "0px"
        }).find("li:first").appendTo(this);
    });
}


var _start = false;
window.onload = function() { 

    yangshi();
    /*转盘效果*/ 

    $("#startbtn").click(function() {
        if (!_start) {
            $.ajax({
                type: 'get',
                url: WEB_HOST + '/index.php/Activity/Index/run',
                dataType: 'json',
                cache: false,
                before: function() {
                    _start = true;
                },
                error: function() {
                    alert('出错了！');
                    _start = false;
                },
                success: function(json) {
                    var json = eval(json);
                    if (json["code"] == 1009) {
                        if (confirm(json.msg)) {
                            window.location.href = $('#loginurl').val();
                        }
                        _start = false;
                        return false;
                    } else if (json["code"] != 1009 && json["code"] != 0) {
                        if(json["code"] == 3001){
                            roulenumber();
                            _start = false;
                            return false;
                        }
                        mui.toast(json["msg"]);
                        _start = false;
                        return false;
                    }
                    var info = json["data"];
                    var p = info["name"]; //奖项  
                    var n = info["num"]; //剩余抽奖次数 

                    var Id = info["Id"];
                    var a = parseInt(info["angle"]); //角度
                    if (p != "" && a != 0) {
                        $('#rotate').rotate({
                            angle:0,
                            animateTo:3600+a,
                            duration:8000,
                            callback:function (){
                            $('#num').html(n);                   
                            yangshi();
                            $('#curlurl').val(WEB_HOST+"/index.php/Home/Wish/roushare?pid="+Id);
                            $('#curltit').val('我在小蜜成功领取一份惊喜');
                            rouleshare(info["c_name"],info["c_value"],info["c_type"],info["c_img"]);
                            _start = false;                                
                            $("#startbtn").css("cursor", "pointer");
                            }
                        });             
                        
                    } else {
                        mui.toast('活动已结束！');
                    }
                }
            });

        }
        _start = true;
    });
}
function yangshi() {
    var rourwg = $('.roule_num').width();
    var rouwg = $('.roule_num').outerWidth();
    $('.roule_num').css('height', rouwg + 'px').css('line-height', rourwg + 'px').css('border-radius', rouwg + 'px');
    $('.prize_img').height($('.prize_img').width());
    $('.prize_num').each(function() {
        var prirwg = $(this).width();
        var priwg = $(this).outerWidth();
        $(this).css('height', priwg + 'px').css('line-height', prirwg + 'px').css('border-radius', priwg + 'px');
    });
    
    // var prirwg = $('.prize_num').width();
    // var priwg = $('.prize_num').outerWidth();
    // $('.prize_num').css('height', priwg + 'px').css('line-height', prirwg + 'px').css('border-radius', priwg + 'px');
}


/*获奖弹框*/
function rouleshare(name,val,tp,imgpath){
    if(tp==2){
        $('.winning-proimg').hide();
        $('.winning-moneyimg').show();
        $("#cptype").html('现金红包');
    }else if(tp==4){
        $('.winning-moneyimg').hide();
        $('.winning-proimg').show();
        $('#prize-img').attr("src",imgpath);
        $("#cptype").html(name);
    }
    $('#cpval').html(val);
    $(".winning-bg").fadeIn(300);
    $(".winning-pup").fadeIn(300);
    $(".winning-bg").height($(document).height());
    var wwh = $('.winning-proimg img').height();
    $('.winning-proimg').height(wwh);
    
}
  

/*抽奖次数用完提醒*/
function roulenumber () {
    $(".winning-bg-num").fadeIn(300);
    $(".winning-pup-num").fadeIn(300);
    $(".winning-bg-num").height($(document).height());    
}




