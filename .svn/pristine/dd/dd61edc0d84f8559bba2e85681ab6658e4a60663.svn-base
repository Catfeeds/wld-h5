/*
 *  用户提交资料信息
 *
 ***/
// 新增编辑地址
function insertAddress(forms) {
	var attrbul = getFormAttrs(forms);
	if(attrbul['province'] == "" || attrbul['city'] == "" || attrbul['district'] == "" || attrbul['mobile'] == "" || attrbul['consignee'] == "" || attrbul['address'] == "") {
		mui.toast('请输入完整信息');
		return false;
	}
	var id = $('input[name="id"]').val();
	$.ajax({
		type: "POST",
		url: WEB_HOST + '/index.php/Home/Address/insertaddress',
		data: "attrbul=" + JSON.stringify(attrbul),
		dataType: "json",
		success: function(json) {
			var msg = eval(json);
			if(msg.code == 0) {
				if(id) {
					loadaddress(msg.data, id);
					$('#insert_sub_address').text('编辑');
				} else {
					loadaddress(msg.data);
					$('#insert_sub_address').text('添加');
				}
			} else {
				mui.toast(msg.msg);
			}
		}
	});
}

// 编辑地址查询
function xinzengdz(id) {
	$.ajax({
		type: "POST",
		url: WEB_HOST + '/index.php/Home/Address/FindAddress',
		data: "id=" + id,
		dataType: "json",
		success: function(data) {
			if(data != null) {
				$('#consignee').val(data.c_consignee);
				$('#mobile').val(data.c_mobile);
				$('#provincename').val(data.c_province);
				$('#cityname').val(data.c_city);
				$('#districtname').val(data.c_district);
				$('#address').val(data.c_address);

				//$('#province').find("option:selected").text(data.c_provincename);
				$('#provincename').text(data.c_provincename);
				$('#cityname').text(data.c_cityname);
				$('#districtname').text(data.c_districtname);
				// $("#district option:selected").val(data.c_districtname);
				// $("#city option:selected").val(data.c_cityname);

				$('#isdefault').val(data.c_is_default);

				$('.insert_bg').fadeIn(200);
				$('.insert_address').show();
				$('input[name="id"]').val(data.c_id);
				$('#insert_sub_address').text('编辑');
			} else {
				mui.toast(msg.msg);
			}
			// var msg = eval(json);			
			// if (msg.code == 0) {
			// 	var data = msg.data;			
			// } else {
			// 	mui.toast(msg.msg);					
			// }
		}
	});
}

// 设置默认地址
function SetAddress(id) {
	$.ajax({
		type: "POST",
		url: WEB_HOST + '/index.php/Home/Address/SetAddress',
		data: "id=" + id,
		dataType: "json",
		success: function(json) {
			var msg = eval(json);
			if(msg.code == 0) {
				mui.toast(msg.msg);
				$('#isdefault').val(1);
				$('#page2').hide();
				$('#page1').show();
				yangshi();
			} else {
				mui.toast(msg.msg);
			}
		}
	});
}

// 删除地址
function deleteAddress(id) {
	if(confirm('确认删除吗？删除不可恢复！')) {
		$.ajax({
			type: "POST",
			url: WEB_HOST + '/index.php/Home/Address/deleteUserAddress',
			data: "id=" + id,
			dataType: "json",
			success: function(json) {
				var msg = eval(json);
				if(msg.code == 0) {
					$('#address_' + id).remove();
					$('#kongbai_' + id).remove();
					$('#id').val('');
				} else {
					mui.toast(msg.msg);
				}
			}
		});
	}
}

//修改用户信息
function update_user_info(forms) {
	var formbul = getFormAttrs(forms);
	if(formbul['phone'] == "" || formbul['pass'] == "") {
		mui.toast('请输入完整信息');
		return false;
	}
	$.ajax({
		type: "POST",
		url: WEB_HOST + '/index.php/Home/User/UpdateAccount',
		data: "formbul=" + JSON.stringify(formbul),
		dataType: "json",
		success: function(json) {
			var obj = eval(json);
			if(obj.code == 0) {
				mui.toast(obj.msg);
			} else {
				mui.toast(obj.msg);
			}
		}
	});
	return false;
}

/*
 * 用户操作订单块
 *
 */
//取消订单
function canlceorder(orderid) {
	mui.confirm('确定取消该订单？', '提示', ['取消', '确认'], function(e) {
		e.index == 0 ? "" :
			$.ajax({
				type: "POST",
				url: WEB_HOST + '/index.php/Order/Index/CancelOrder',
				data: "orderid=" + orderid,
				dataType: "json",
				success: function(json) {
					var obj = eval(json);
					if(obj.code == 0) {
						mui.toast(obj.msg);
						window.location.href = '';
					} else {
						mui.toast(obj.msg);
					}
				}
			});
	}, 'div')
}
var remind = true;
//提醒发货
function RemindDeliver(orderid) {
	if(remind){
		remind = false;
		$.ajax({
			type: "POST",
			url: WEB_HOST + '/index.php/Order/Index/RemindDeliver',
			data: "orderid=" + orderid,
			dataType: "json",
			success: function(json) {
				var obj = eval(json);
				if(obj.code == 0) {
					mui.toast(obj.msg);
				} else {
					mui.toast(obj.msg);
					remind = true;
				}
			}
		});
	}
}
var confir = true;
// 确认收货
function confirmorder(orderid) {
	if(confir){
		confir = false;
		$.ajax({
			type: "POST",
			url: WEB_HOST + '/index.php/Order/Index/Confirmorder',
			data: "orderid=" + orderid,
			dataType: "json",
			success: function(json) {
				var obj = eval(json);
				if(obj.code == 0) {
					mui.toast(obj.msg);
					setTimeout(function() {
						window.location.href = '';
					}, 2000)
				} else {
					mui.toast(obj.msg);
					confir = true;
				}
			}
		});
		
	}
}
var refundtag = true;
// 同意退款退货操作
function AgreeRefund(rcode, addressid) {
	if(refundtag){
		refundtag = false;
		$.ajax({
			type: "POST",
			url: WEB_HOST + '/index.php/Order/Storeorder/AgreeRefund',
			data: "rcode=" + rcode + "&addressid=" + addressid,
			dataType: "json",
			success: function(json) {
				var obj = eval(json);
				if(obj.code == 0) {
					mui.toast(obj.msg);
					mui('.mui-content').on('tap', '#sel_address');
					setTimeout(function() {
						window.location.href = '';
					}, 2000)
				} else {
					mui.toast(obj.msg);
					refundtag = false;
				}
			}
		});
		
	}
}

// 不同意退款退货操作
function disagreeAgree(rcode) {
	$.ajax({
		type: "POST",
		url: WEB_HOST + '/index.php/Order/Storeorder/disagreeAgree',
		data: "rcode=" + rcode,
		dataType: "json",
		success: function(json) {
			var obj = eval(json);
			if(obj.code == 0) {
				mui.toast(obj.msg);
				setTimeout(function() {
					window.location.href = '';
				}, 2000)
			} else {
				mui.toast(obj.msg);
			}
		}
	});
}
var clicktag = true;
// 买家确认收货
function Refundreturn(rcode) {
	if(clicktag){
		clicktag = false;
		$.ajax({
			type: "POST",
			url: WEB_HOST + '/index.php/Order/Storeorder/Refundreturn',
			data: "rcode=" + rcode,
			dataType: "json",
			success: function(json) {
				var obj = eval(json);
				if(obj.code == 0) {
					mui.toast(obj.msg);
					setTimeout(function() {
						window.location.href = '';
					}, 2000)
				} else {
					mui.toast(obj.msg);
					clicktag = true;
				}
			}
		});
		
	}
}

//删除订单

function deleteorder(orderid,type){
	mui.confirm('确定删除订单？', '提示', ['取消', '确认'], function(e) {
		e.index == 0 ? "" :
			$.ajax({
				type: "POST",
				url: WEB_HOST + '/index.php/Order/Index/DelOrder',
				data: "orderid=" + orderid,
				dataType: "json",
				success: function(json) {
					var obj = eval(json);
					if(obj.code == 0) {
						mui.toast(obj.msg);
						window.location.href = WEB_HOST + '/index.php/Order/Index/orderlist?statu='+type;
					} else {
						mui.toast(obj.msg);
					}
				}
				
			});
	}, 'div')
}

/*
 * 用户购物车操作
 *
 */

// 储存购物数据
function StorageMycart(i, data) {
	var datajson = JSONstringify(data);
	$.cookie("product_" + i, datajson, {
		path: '/',
		expires: 30
	});
}

// 清空购物车数据
function ClearMycart(i) {
	if(i) {
		$.cookie("product_" + i, null, {
			path: '/',
			expires: 30
		});
	} else {
		var arr = document.cookie.split('; ');
		for(var i = 0; i < arr.length; i++) {
			/* 将cookie名称和值拆分进行判断 */
			var arr2 = arr[i].split('=');
			if(arr2[0].indexOf("product_") == 0) {
				$.cookie(arr2[0], null, {
					path: '/',
					expires: 30
				});
			}
		}
	}
}

// 读取购物车数据
function ReadMycart() {
	var j = 0;
	var data = new Array();
	var arr = document.cookie.split('; ');
	for(var i = 0; i < arr.length; i++) {
		/* 将cookie名称和值拆分进行判断 */
		var arr2 = arr[i].split('=');
		if(arr2[0].indexOf("product_") == 0) {
			data[j] = jQuery.parseJSON($.cookie(arr2[0]));
			j++;
		}
	}
	return JSONstringify(data);
}