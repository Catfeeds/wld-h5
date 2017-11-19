/*饼状的宽高*/
var wih = $('.today-right').width();
$('.today-right').height(wih);
var hih = $('.today-right').height();
/*折线*/
var zw = $('.report_line').width(),
	zh = zw*0.40;

var val1,val2,val3,val4;/*饼状数据*/
var ctrls = true;
var emptyv = true;

var begintimes = $('#begintimes').val();
var endtimes = $('#endtimes').val();
var txt = $('#txt').val();
var arg = 1;
window.onload = function() {
	styles();
	$('.months-bg').click(function(){
		$('.months-bg').fadeOut();
		$('.months-pup').fadeOut();
	});
	if(begintimes!="" && endtimes !=""){
		outintab(4,txt,txt,1);
        mui('.mui-content').off('tap','#out_tab');
        mui('.mui-content').on('tap','#out_tab',function(){outintab(4,txt,txt,1)});
	}else{
		outintab(1,'今日收入','今日支出');
        mui('.mui-content').off('tap','#out_tab');
        mui('.mui-content').on('tap','#out_tab',function(){outintab(1,"今日收入","今日支出")});
	}
	if($.cookie('summoney')==1){
		sumshow();
	}
}

function styles() {
	var ticoh = $('.sumasset-ico').height();
	$('.sumasset-font').css('line-height', ticoh + 'px');

	var sicoh = $('.sico').height();
	$('#today-ul li').css('line-height', sicoh + 'px');

}


/*显示隐藏总资产*/
var sumclick = 1;
function sumshow() {
	if(sumclick==1){
		$.cookie('summoney', '1');
		$('.sumasset-mon').text("********");
		$('.sumasset-ico img').attr('src',WEB_HOST + '/Resource/Balance/img/imgico10.png');
		sumclick = 2;
	}else{
		$.cookie('summoney', null);
		$('.sumasset-mon').text($('#summoney_val').val());
		$('.sumasset-ico img').attr('src',WEB_HOST + '/Resource/Balance/img/imgico9.png');
		sumclick = 1;
	}
}

/*查看支出，收入*/
function outintab(n,t1,t2,endts) {
	$('.borderra').removeClass('hover');
	$('#tab_'+n).addClass('hover');
    if(n==1){
        mui('.mui-content').off('tap','#out_tab');
        mui('.mui-content').on('tap','#out_tab',function(){outintab(1,t1,t2)});
        if(arg==1){
            getdayincome(1,1,t1,t2);
        }else{
            getdayincome(2,1,t2,t1);
        }
    }else if(n==2){
        mui('.mui-content').off('tap','#out_tab');
        mui('.mui-content').on('tap','#out_tab',function(){outintab(2,t1,t2)});
        if(arg==1){
            getdayincome(1,2,t1,t2);
        }else{
            getdayincome(2,2,t2,t1);
        }
    }else if(n==3){
        mui('.mui-content').off('tap','#out_tab');
        mui('.mui-content').on('tap','#out_tab',function(){outintab(3,t1,t2)});
        if(arg==1){
            getmonthincome(1,3,$('#endday').val(),t1,t2);
            broken($('#endday').val(),1);
        }else{
            getmonthincome(2,3,$('#endday').val(),t2,t1);
            broken($('#endday').val(),2);
        }
    }else if(n==4){
        if(endts == 2){
            mui('.mui-content').off('tap','#out_tab');
            mui('.mui-content').on('tap','#out_tab',function(){outintab(4,t1,t2,endts)});
            if(arg==1){
                getmonthincome(1,4,$('#todaymon').val(),t1,t2);
            }else{
                getmonthincome(2,4,$('#todaymon').val(),t2,t1);
            }
        }
        else if(endts == 1){
            mui('.mui-content').off('tap','#out_tab');
            mui('.mui-content').on('tap','#out_tab',function(){outintab(4,t1,t2,endts)});
            if(arg==1){
                getmonthincome(1,4,begintimes,txt+'收入',txt+'支出',endtimes);
            }else{
                getmonthincome(2,4,begintimes,txt+'支出',txt+'收入',endtimes);
            }
        }else{
            if(arg==1){
                mui('.mui-content').off('tap','#out_tab');
                mui('.mui-content').on('tap','#out_tab',function(){outintab(4,t1,t2)});
                getmonthincome(1,4,$('#date1').val(),t1,t2);
            }else{
                getmonthincome(2,4,$('#date1').val(),t2,t1);
            }
        }
    }
    if(arg == 2){
        arg = 1;
    }else {
        arg = 2;
    }
}

/*点支出的样式*/
function addstyle () {
	$('#txt_9').text('+');
	$('#txt_2').removeClass('cy');
	$('#txt_3').removeClass('cy');
	$('#txt_4').removeClass('cy');
	$('#txt_5').removeClass('cy');
	$('#txt_6').removeClass('cy');
	$('#txt_8').removeClass('cb');
	$('#txt_9').removeClass('cy');
	$('#txt_2').addClass('cb');
	$('#txt_3').addClass('cb');
	$('#txt_4').addClass('cb');
	$('#txt_5').addClass('cb');
	$('#txt_6').addClass('cb');
	$('#txt_8').addClass('cy');
	$('#txt_9').addClass('cb');
}

/*点收入的样式*/
function substyle () {
	$('#txt_9').text('-');
	$('#txt_2').removeClass('cb');
	$('#txt_3').removeClass('cb');
	$('#txt_4').removeClass('cb');
	$('#txt_5').removeClass('cb');
	$('#txt_6').removeClass('cb');
	$('#txt_8').removeClass('cy');
	$('#txt_9').removeClass('cb');
	$('#txt_2').addClass('cy');
	$('#txt_3').addClass('cy');
	$('#txt_4').addClass('cy');
	$('#txt_5').addClass('cy');
	$('#txt_6').addClass('cy');
	$('#txt_8').addClass('cb');
	$('#txt_9').addClass('cy');
}

/*弹出月份*/
function getmonths () {
	$('.months-bg').fadeIn();
	$('.months-pup').fadeIn();
	$('.months-bg').height($(document).height());
	var mTop = document.getElementsByClassName('sumin-time')[0].offsetTop;
	var mheight = $('.sumin-time').height();
    var sTop = document.body.scrollTop;
    var resultop = mTop - sTop -(-mheight);
    $('.months-pup').css('top',resultop+'px');
    var mh = $('.months-pup-con').height(),
    mhh = mh*18.5/100;
    $('.months-pup-con').css('line-height',mhh+'px');
}


/*收，支总额*/
function sumincome (begint,sign) {
	$('#addstr').val("");
	$('#substr').val("");
	var begint = begint;
	var sign = sign;
	$.ajax({
		type: "get",
		url: WEB_HOST+"/index.php/Balance/Index/timeslotexpenditure?begintime="+begint+"&sign="+sign,
		dataType: "json",
		success: function(obj) {
			var msg = eval(obj);
			if(msg['code']==0){
				var data = msg.data;
				if(sign==1){
					$('#addstr').val(data['c_money'].toFixed(2));
				}else{
					$('#substr').val(data['c_money'].toFixed(2));
				}
			}else {
				mui.toast(msg['msg']);
			}
		}
	});
}

/*点击月份效果*/
function monthlist (begint,txt) {
	$('#todaymon').val(begint);
	var begint = $('#todaymon').val();
	outintab(4,txt+"收入",txt+"支出",2);
	$('.months-bg').fadeOut();
	$('.months-pup').fadeOut();
}


/*每日统计*/
function getdayincome (type,tabs,t1,t2) {
	$('#txt_2').text("");
	$('#txt_8').text("");
	var sign = type;
	var daytimes = "";
	if(tabs == 1){
		daytimes = $('#todayn').val();
	}else if(tabs == 2){
		daytimes = $('#yestoday').val();
	}
	$('#txt_10').hide();
	$('#lineimg_1').hide();
	$('#txt_1').text(t1);
	$('#txt_7').text(t2);
	$.ajax({
		type: "get",
		url: WEB_HOST+"/index.php/Balance/Index/dayincome?time="+daytimes+"&sign="+sign,
		dataType: "json",
		//在请求之前调用的函数
		beforeSend: function() {
			ctrls = false;
		},
		//成功返回之后调用的函数
		success: function(obj) {
			var msg = eval(obj);
			if(msg['code']==0){
				var json = msg.data;
				if(json == null || json.list == null){
					emptyv = false;
				}else{
					var data = json.list;
					for (var i = 0; i < data.length; i++) {
						if(data[i]['c_type']==2){/*扫码支付*/
							val2 = data[i]['c_money'];
							$('#txt_3').text(data[i]['c_money'].toFixed(2));
						}else if(data[i]['c_type']==3){/*线上订单*/
							val3 = data[i]['c_money'];
							$('#txt_4').text(data[i]['c_money'].toFixed(2));
						}else if(data[i]['c_type']==4){/*收入红包，支出提现*/
							if(sign==1){
								val4 = data[i]['c_money'];
								$('#txt_5').text(data[i]['c_money'].toFixed(2));
								$('#txt_13').html("红&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;包");
								$('#imgsrc_1').attr('src',WEB_HOST+'/Resource/Balance/img/imgico1.png');
							}else{
								val4 = data[i]['c_money'];
								$('#txt_5').text(data[i]['c_money'].toFixed(2));
								$('#txt_13').html("提&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;现");
								$('#imgsrc_1').attr('src',WEB_HOST+'/Resource/Balance/img/imgico7.png');
							}
						}else if(data[i]['c_type']==1){/*其他*/
							val1 = data[i]['c_money'];
							$('#txt_6').text(data[i]['c_money'].toFixed(2));
						}
					};
					if(sign == 1){
						$('#txt_2').text(json['dateincome']);
						$('#txt_8').text("-"+json['dateout']);
					}else{
						$('#txt_2').text(json['dateout']);
						$('#txt_8').text("+"+json['dateincome']);
					}
					drawchart(val1,val2,val3,val4,wih,hih);

				}
			}else {
				mui.toast(msg['msg']);
				emptyv = false;
			}
		},
		complete: function(XMLHttpRequest, textStatus) {
			if(sign==1){
				addstyle();
			}else {
				substyle();
			}
		},
		error: function() {
			ctrls = true;
		}
	});
}

/*时间段统计*/
function getmonthincome (type,tabs,begint,t1,t2,endt) {
	$('#txt_2').text("");
	$('#txt_8').text("");
	var sign = type;
	var begint = begint;
	if(tabs==3){
		$('#txt_10').hide();
		$('#lineimg_1').show();
	}else if(tabs==4){
		$('#txt_10').show();
		$('#lineimg_1').hide();
	}
	$('#txt_1').text(t1);
	$('#txt_7').text(t2);
	var weburl = "";
	if(endt){
		weburl += WEB_HOST+"/index.php/Balance/Index/getdetailtime?begintime="+begint+"&sign="+sign+"&endtime="+endt;
	}else{
		weburl += WEB_HOST+"/index.php/Balance/Index/timeslotincome?begintime="+begint+"&sign="+sign;
	}
	$.ajax({
		type: "get",
		url: weburl,
		dataType: "json",
		//在请求之前调用的函数
		beforeSend: function() {
			ctrls = false;
		},
		//成功返回之后调用的函数
		success: function(obj) {
			var msg = eval(obj);
			if(msg['code']==0){
				var json = msg.data;
				if(json==null || json.list ==null){
					emptyv = false;
				}else{
					var data = json.list;
					for (var i = 0; i < data.length; i++) {
						if(data[i]['c_type']==2){/*扫码支付*/
							val2 = data[i]['c_money'];
							$('#txt_3').text(data[i]['c_money'].toFixed(2));
						}else if(data[i]['c_type']==3){/*线上订单*/
							val3 = data[i]['c_money'];
							$('#txt_4').text(data[i]['c_money'].toFixed(2));
						}else if(data[i]['c_type']==4){/*收入红包，支出提现*/
							if(sign==1){
								val4 = data[i]['c_money'];
								$('#txt_5').text(data[i]['c_money'].toFixed(2));
								$('#txt_13').html("红&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;包");
								$('#imgsrc_1').attr('src',WEB_HOST+'/Resource/Balance/img/imgico1.png');
							}else{
								val4 = data[i]['c_money'];
								$('#txt_5').text(data[i]['c_money'].toFixed(2));
								$('#txt_13').html("提&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;现");
								$('#imgsrc_1').attr('src',WEB_HOST+'/Resource/Balance/img/imgico7.png');
							}
						}else if(data[i]['c_type']==1){/*其他*/
							val1 = data[i]['c_money'];
							$('#txt_6').text(data[i]['c_money'].toFixed(2));
						}
					};
					if(sign == 1){
						$('#txt_2').text(json['dateincome']);
						$('#txt_8').text("-"+json['dateout']);
					}else{
						$('#txt_2').text(json['dateout']);
						$('#txt_8').text("+"+json['dateincome']);
					}

					drawchart(val1,val2,val3,val4,wih,hih);
				}
			}else {
				mui.toast(msg['msg']);
				emptyv = false;
			}
		},
		complete: function(XMLHttpRequest, textStatus) {
			if(sign==1){
				addstyle();
			}else {
				substyle();
			}
		},
		error: function() {
			ctrls = true;
		}
	});
}

/*折线图获取数据*/
function broken (begint,sign) {
	var begint = begint;
	var sign = sign;
	$.ajax({
		type: "get",
		url: WEB_HOST+"/index.php/Balance/Index/broken?begintime="+begint+"&sign="+sign,
		dataType: "json",
		success: function(obj) {
			var msg = eval(obj);
			if(msg['code']==0){
				var data = msg.data;
				var daysarr = [''+data[0]['day']+'',''+data[1]['day']+'',''+data[2]['day']+'',''+data[3]['day']+'',''+data[4]['day']+'',''+data[5]['day']+'',''+data[6]['day']+''];
				var datasarr = [''+data[0]['c_money']+'',''+data[1]['c_money']+'',''+data[2]['c_money']+'',''+data[3]['c_money']+'',''+data[4]['c_money']+'',''+data[5]['c_money']+'',''+data[6]['c_money']+''];
				drawline(zw,zh,daysarr,datasarr);
			}else {
				mui.toast(msg['msg']);
			}
		}
	});
}

//画饼状图
function drawchart(a,b,c,d,w,h) {
	var a = a;
	var b = b;
	var c = c;
	var d = d;
	var data = [{
		name: '',
		value: a,
		color: '#2466b0'
	}, {
		name: '',
		value: b,
		color: '#46a9fa'
	}, {
		name: '',
		value: c,
		color: '#ffad3f'
	}, {
		name: '',
		value: d,
		color: '#f05a4b'
	}];

	var chart = new iChart.Donut2D({
		render: 'canvasDiv',
		align:'center',
		center: {
			text: '',
			shadow: false,
			shadow_offsetx: 0,
			shadow_offsety: 2,
			shadow_blur: 2,
			shadow_color: '#b7b7b7',
			color: '#6f6f6f'
		},
		data: data,
		offsetx: 0,
		offsety: 0,
		shadow: false,
		background_color: '#ffffff',
		separate_angle: 0, //分离角度
		tip: {
			enable: true,
			showType: 'fixed'
		},
		sub_option: {
			label: false,
			color_factor: 0.3
		},
		showpercent: true,
		decimalsnum: 2,
		width: w,
		height: h,
		radius: w,
		border:false
	});

	chart.draw();
}

//画折线图
function drawline(zw,zh,months,data) {
	var data = [{
		name:'',
		value:data,
		color: '#46a9fa',
		line_width: 1
	}];

	var labels = months;

	var line = new iChart.LineBasic2D({
		render: 'lineimg_1',
		data: data,
		align: 'center',
		width: zw,
		height: zh,
		sub_option: {
			smooth: false, //平滑曲线
			point_size: 7,
			hollow_inside:false,//设置一个点的亮色在外环的效果
		},
		tip: {
			enable: true,
			shadow: true
		},
		legend: {
			enable: false
		},
		crosshair: {
			enable: true,
		},
		coordinate: {
			width: '100%',
			valid_width: '100%',
			height: 260,
			axis: {
				color: '#ffffff',
				width: [0, 0, 2, 2]
			},
			grids: {
				vertical: {
					way: 'share_alike',
					value: 4
				}
			},
			gridlinesVisible:false,
			scale: [{
				position: 'left',
				start_scale: 0,
				end_scale: 0,
				scale_space: 5,
				scale_size: 0,
				scale_color: '#ffffff',
				label: {
					color:'#ffffff',
				}
			}, {
				position: 'bottom',
				scale_size: 0,
				labels: labels,
				scale_color: '#ffffff',
				label: {
					color:'#555555',
				}
			}]
		},
		padding:35,
		border:false
	});
	//开始画图
	line.draw();

}