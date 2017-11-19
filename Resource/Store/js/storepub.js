$(function () {
    $('.xiaomi-bar').click(function () {
        document.documentElement.scrollTop = document.body.scrollTop =0;
    });
});

/*头部样式*/
function yangshi() {
    var screenWith = $('.wrap-page').width() / 720;
    $('.header').css('width', $('.wrap-page').width());
    $('.header').height($('.header').width() * 388 / 640);
    $('.provide>li>img').css('width', 30 * screenWith);
    $('.provide>li>img').css('height', 30 * screenWith);
    $('.mui-bar').css('width', $('.mui-content').width() + 1 + 'px');
    $('.mui-tab-item>img').css('width', $('.mui-tab-item>img').width() * screenWith);
    $('.mui-tab-item>img').css('height', $('.mui-tab-item>img').width() * screenWith);

    $('.mui-content').css('margin-top', '0');
    $('.car').css('width', $('.car').width() * screenWith);
    $('.jia').css('width', $('.wrap-page').width());

    $('.bottom-icon').height($('.bottom-icon').width());
}

/*商品样式*/
function  prostyles() {
    $('.pro_image').height($('.pro_image').width());
    $('.pro_name').css('height', $('.pro_image').height()*0.7 +'px');
    $('.pro_name').css('line-height', $('.pro_name').height()* 0.5 +'px');
    // $('.subcart').height($('.subcart').width());
    // $('.addcart').height($('.subcart').width());
    // $('.inpcart input[type="text"]').height($('.subcart').height());
    // $('.inpcart input[type="text"]').css('line-height',$('.subcart').height()+"px");
}

var arrOffsetTop = [];
function getdatacate() {
    var url = WEB_HOST+"/index.php/Store/Index/categorypro?acode="+$('#issue_ucode').val();
    var _html_l = "";
    var _html_r = "";
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: url,
        cache: false,
        beforeSend: function() {
            $('#console').css('display', 'block');
            $('#console').html('加载中...');
            ctrls = false;
        },
        error: function() {
            $('#console').css('display', 'block');
            $('#console').html('加载失败');
            ctrls = true;
        },
        success: function(obj) {
            $('.section_left').empty();
            $('.section_right').empty();
            var mgs = eval(obj);
            if(mgs['code'] == 0) {
                var data = mgs.data;
                if(!data || data[0]["goods"].length <= 0) {
                    $('.section_left').css('display',"none");
                    $('.section_right').css("width","100%");
                    $('.section_right').css("margin-left","0");
                    //数据为空展示
                    _html_r += '<div class="data-empty">';
                    _html_r += '<div class="data-empty-img"><img src="' + WEB_HOST + '/Resource/Balance/img/szmx_img_wujl.png" alt="" /></div>';
                    _html_r += '<div class="data-empty-font c3 fs13">您还没有相关记录</div>';
                    _html_r += '</div>';
                    emptyval = false;
                } else {
                    if(!data[0]["c_category_name"]){
                        $('.section_left').css('display',"none");
                        $('.section_right').css("width","97%");
                    }else{
                        $('.section_left').css('display',"block");
                        $('.section_right').css("width","67%");
                    }
                    for(var i = 0; i < data.length; i++) {
                        _html_l+='<a href="#section'+i+'" rel="external nofollow">'+data[i]["c_category_name"]+'</a>';
                        _html_r+='<div class="section" id="section'+i+'">';
                        _html_r+='<div id="section_name_'+i+'" class="section_name fs14 c9 divtab">'+data[i]["c_category_name"]+'</div>';
                        var dataarr = data[i]['goods'];
                        for(var j = 0;j<dataarr.length;j++){
                            _html_r+='<div class="prolist-box bborder">';
                            _html_r+='<div class="pro_image fl" onclick="clickpro(\'' + dataarr[j]['c_pcode'] + '\',' + dataarr[j]['c_source'] + ');" data-pcode="'+dataarr[j]["c_pcode"]+'" data-source="'+dataarr[j]["c_source"]+'">';
                            _html_r+='<img src="'+dataarr[j]["c_pimg"]+'" alt="">';
                            _html_r+='</div>';
                            _html_r+='<div class="pro_info fl">';
                            _html_r+='<div class="pro_name fs14 clamp2">'+dataarr[j]["c_name"]+'</div>';
                            _html_r+='<div class="pro_price">';
                            _html_r+='<div class="showaddcart fr">';
                            var carnum = dataarr[j]['cartnum'];
                            _html_r+='<div class="addcart fr" id="byadd_'+dataarr[j]["c_id"]+'" onclick="addgoods(' + dataarr[j]["c_id"] + ',' + dataarr[j]['c_num'] + ',\'' + dataarr[j]['c_pcode'] + '\')"><img src="'+WEB_HOST+'/Resource/Store/img/store/dpgl_icon_tjsp.png" alt=""></div>';
                            if(carnum==0){
                                _html_r+='<div class="inpcart fr" id="bytxt_'+dataarr[j]["c_id"]+'"><input type="text" class="fs12" value="" id="buyinput_'+dataarr[j]["c_id"]+'" disabled="disabled"></div>';
                                _html_r+='<input type="hidden" value="'+carnum+'" id="hid_'+dataarr[j]["c_id"]+'">';
                                _html_r+='<div class="subcart fr none" id="bysub_'+dataarr[j]["c_id"]+'" onclick="subgoods(' + dataarr[j]["c_id"] + ',' + dataarr[j]['c_num'] + ',\'' + dataarr[j]['c_pcode'] + '\')"><img src=" ' + WEB_HOST + '/Resource/Store/img/store/dpgl_icon_jdsp.png" alt=""></div>';

                            }else{_html_r+='<div class="inpcart fr" id="bytxt_'+dataarr[j]["c_id"]+'"><input type="text" class="fs12" value="'+carnum+'" id="buyinput_'+dataarr[j]["c_id"]+'" disabled="disabled"></div>';
                                _html_r+='<input type="hidden" value="'+carnum+'" id="hid_'+dataarr[j]["c_id"]+'">';
                                _html_r+='<div class="subcart fr" id="bysub_'+dataarr[j]["c_id"]+'" onclick="subgoods(' + dataarr[j]["c_id"] + ',' + dataarr[j]['c_num'] + ',\'' + dataarr[j]['c_pcode'] + '\')"><img src="' + WEB_HOST + '/Resource/Store/img/store/dpgl_icon_jdsp.png" alt=""></div>';
                            }

                            _html_r+='</div>';
                            _html_r+='<div class="showpirce fl fs14 cy">￥'+dataarr[j]["c_price"]+'</div>';
                            _html_r+='</div>';
                            _html_r+='</div>';
                            _html_r+='</div>';
                        }
                        _html_r+='</div>';

                    };
                }
                /*分类，左 商品，右*/
                $('.section_left').append(_html_l);
                $('.section_right').append(_html_r);
                $('.section_left').scroll();
                $('.section_right').scroll();
                // 定义一个获取所有div的距离高度
                if(emptyval){
                	for(var i = 0; i < data.length; i++) {
	                    arrOffsetTop[i] = $('#section'+i).offset().top;
	                }
                }
            } else {
                emptyval = false;
            }
        },
        complete: function() {
            prostyles();
            $('#console').css('display', 'none');
            ctrls = true;
            // 获取每个div的平均高度
            var fTotalHgt = 0;
            for(var i=0; i<$('.section').length; i++) {
                fTotalHgt += $('.section').eq(i).outerHeight();
            }
            var fAverageHgt = parseFloat(fTotalHgt / $('.section').length);
            // 滚动事件(每次滚动都做一次循环判断)
            $(window).scroll(function() {
                for(var i=0; i<$('.section').length; i++) {
                    if($(this).scrollTop() > arrOffsetTop[i] - fAverageHgt) {
                        $('.section_left a').eq(i).addClass('current').siblings().removeClass('current');
                    }
                }
            });
            /* 点击事件 */
            $('.section_left a').click(function() {
                $(this).addClass('current').siblings().removeClass('current');
                $('body, html').animate({scrollTop: arrOffsetTop[$(this).index()]}, 500);
            });
        }
    });
}

/*线上商品列表*/
function getProductlist() {
    var url = WEB_HOST + "/index.php/Store/Index/GetProduceList?acode=" + $('#issue_ucode').val() + "&pageindex=" + pageindex2 + "&isfixed=" + isfixed;
    var _html = "";
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: url,
        cache: false,
        beforeSend: function () {
            $('#console').css('display', 'block');
            $('#console').html('加载中...');
            ctrls2 = false;
        },
        error: function () {
            $('#console').css('display', 'block');
            $('#console').html('加载失败');
            ctrls2 = true;
        },
        success: function (obj) {
            if (pageindex2 == 1) {
                $('#product-list').empty();
            }
            var mgs = eval(obj);
            if (mgs['code'] == 0) {
                var data = mgs.data;
                if (data == null || data.list.length <= 0) {
                    if (pageindex == 1) {
                        _html += '<div class="data-empty divtab">';
                        _html += '<div class="data-empty-img"><img src="' + WEB_HOST + '/Resource/Balance/img/szmx_img_wujl.png" alt="" /></div>';
                        _html += '<div class="data-empty-font c3 fs14">该商家还没有添加商品</div>';
                        _html += '</div>';
                    }
                    emptyval2 = false;
                } else {
                    if (pageindex <= data.pageCount) {
                        pageindex++;
                        var datalist = data.list;
                        for (var i = 0; i < datalist.length; i++) {
                            var dataarr = datalist[i];
                            _html += '<div class="pro-item" onclick="clickpro(\'' + dataarr['c_pcode'] + '\',' + dataarr['c_source'] + ');">'
                            _html += '<div class="s-pro-img"><img src="' + dataarr['c_pimg'] + '" alt="" /></div>'
                            _html += '<p style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">' + dataarr['c_name'] + '</p>'
                            _html += '<p style="color: #FFAA3D;"><span class="fs11">￥</span>' + dataarr['c_price'] + '</p>'
                            _html += '</div>'
                        }
                        ;
                    } else {
                        emptyval2 = false;
                    }
                }
            } else {
                emptyval2 = false;
            }
            $('.commodity').append(_html);
            $('.commodity>.fl>img').height($('.commodity>.fl>img').width())
        },
        complete: function () {
            var pimgw = $('.s-pro-img').width();
            $('.s-pro-img').height(pimgw);
            /*5.27 新增 */
            $('.s-pro-lijbuy').height($('.s-pro-lijbuy').width() * 0.3 + 'px');

            $('#console').css('display', 'none');
            ctrls = true;
            if (emptyval2) {
            }
        }
    });
}

//卡券显示

function shopCouponList() {
    var url = WEB_HOST + "/index.php/Store/Index/GetCouponList?fromucode=" + $('#issue_ucode').val();
    var _html = "";
    $.ajax({
        type: 'get',
        dataType: 'json',
        url: url,
        cache: false,
        beforeSend: function () {
            $('#console').css('display', 'block');
            $('#console').html('加载中...');
        },
        error: function () {
            $('#console').css('display', 'block');
            $('#console').html('加载失败');
        },
        success: function (obj) {
            var mgs = eval(obj);
            if (mgs['code'] == 0) {
                var data = mgs.data;
                var datalist = data.list;
                if (!data || !datalist) {
//                  _html += '<div class="fs15 addticket c9"><img src="' + WEB_HOST + '/Resource/Store/img/store/dpsy_icon_add.png" alt="" /><div>添加卡券</div></div>'
                } else {
                    for (var i = 0; i < datalist.length; i++) {
                        _html += '<div class="ticket cf">';
                        _html += '<div class="fl divre">';
                        if (datalist[i]["receive"] == 1) {
                            _html += '<img src="' + WEB_HOST + '/Resource/Store/img/store/dpgl_img_kq03.png" alt="" />';
                        } else {
                            if (datalist[i]["c_type"] == 1) {
                                _html += '<img src="' + WEB_HOST + '/Resource/Store/img/store/dpgl_img_kq02.png" alt="" />';
                            } else if (datalist[i]["c_type"] == 2) {
                                _html += '<img src="' + WEB_HOST + '/Resource/Store/img/store/dpgl_img_kq01.png" alt="" />';
                            }
                        }
                        _html += '<div class="divab divab1"><div class="fs20" style="margin-top: 2%;">' + datalist[i]["c_money"] + '</div>';
                        if (datalist[i]["c_type"] == 1) {
                            _html += '<div class="fs11">元</div>';
                        } else if (datalist[i]["c_type"] == 2) {
                            _html += '<div class="fs11">折</div>';
                        }
                        //_html += '<div class="fs12" style="margin-top: 0.3rem;">满' + datalist[i]["c_limit_money"] + '元使用</div>';
                        _html += '<div class="fs12" style="margin-top: 0.5rem;padding:0 2%">' + datalist[i]["showstr"] + '</div>';
                        if (datalist[i]["receive"] == 1) {
                            _html += '<div class="fs12 divab buy">已领取</div>';
                        } else {
                            _html += '<div class="fs12 divab buy" onclick="receivecard(' + datalist[i]["awid"] + ')">立即领取</div>';
                        }
                        _html += '</div></div></div>';
                    }
                }
            }
            $('.ticket1').append(_html);
            $('.ticket>.fl').css('height', $('.ticket').width() * 1.7);
        },
        complete: function () {
            var pimgw = $('.s-pro-img').width();
            $('.s-pro-img').height(pimgw);
            /*5.27 新增 */
            $('.s-pro-lijbuy').height($('.s-pro-lijbuy').width() * 0.3 + 'px');

            $('#console').css('display', 'none');

        }
    });

}

/*卡券领取*/
function receivecard(awid) {
    $.post(WEB_HOST + "/index.php/Store/Index/ReceiveShopCoupon", {
        awid: awid
    }, function (obj) {
        var data = eval(obj);
        if (data['code'] == 0) {
            mui.alert(data['msg']);
            setTimeout(function () {
                mui.openWindow({
                    url: '',
                    id: ''
                });
            }, 1000);
        } else {
            mui.toast(data['msg']);
        }
    });
}

