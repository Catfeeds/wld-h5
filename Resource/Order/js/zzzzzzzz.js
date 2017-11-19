var reg = new RegExp('^(([0-9]+\\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\\.[0-9]+)|([0-9]*[1-9][0-9]*))$');
window.onload = function() {
	pstyle();
}

function pstyle() {
	var rh = $(".payment-icon").width();
	$(".payment-text").css("line-height", rh + "px");

	var proimgh = $('.buy-pro-img').width();
	$('.buy-pro-img').height(proimgh);
	var lh = proimgh * 33 / 100;
	$(".buy-pro-r-info").css("line-height", lh + "px");

	var pth = $('.pay-types').height();
	$('#balance').css('line-height', pth + 'px');

	var mh = $('.pay-type-money').height();
	$('.pay-types-font').css('line-height', mh + 'px');
}
/*余额处理*/
$(function() {
	$('#balance').change(function() {
		var actval = parseFloat($('#actval_mon').val());
		var aftercoum = parseFloat($('#rusultmon').val());
		if($(this).val() != "") {
			if(!(reg.test($(this).val()))) {
				mui.toast("请输入正确的金额格式！");
				return false;
			} else {
				if($('#kq_statu').val()==0){/*不使用优惠券*/					
					if(parseFloat($(this).val()) >= actval) {
						$(this).val(actval.toFixed(2));
						$('#needmoney').text("0.00");
						$('#sure_paym').text("￥"+actval.toFixed(2));
					} else {
						$('#needmoney').text((actval - parseFloat($(this).val())).toFixed(2));
						$(this).val(parseFloat($(this).val()).toFixed(2));
						$('#sure_paym').text("￥"+(actval - parseFloat($(this).val())).toFixed(2));
					}
				}else{/*使用优惠券*/	
					if(aftercoum >= 0) {
						if(parseFloat($(this).val()) >= aftercoum) {
							$('#needmoney').text("0.00");
							$(this).val((aftercoum).toFixed(2));
							$('#sure_paym').text("￥"+(aftercoum).toFixed(2));	
						} else {
							$('#needmoney').text((aftercoum - parseFloat($(this).val())).toFixed(2));
							$(this).val(parseFloat($(this).val()).toFixed(2));
							$('#sure_paym').text("￥"+(aftercoum - parseFloat($(this).val())).toFixed(2));						
						}
					}else {
					}					
				}
			}
		}
	});
});

/*保留两位小数*/
function toDecimal2(x) {
	var f = parseFloat(x);
	if(isNaN(f)) {
		return false;
	}
	var f = Math.round(x * 100) / 100;
	var s = f.toString();
	var rs = s.indexOf('.');
	if(rs < 0) {
		rs = s.length;
		s += '.';
	}
	while(s.length <= rs + 2) {
		s += '0';
	}
	return s;
}