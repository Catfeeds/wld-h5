var tops = 1;
var ctrls = true;
var kongzhi = true;
getmessage();

//获取当前可是范围的高度
$(window).bind('scroll', function() {
	if(($(window).scrollTop() + $(window).height()) >= ($(document).height() - 60)) {
		if(ctrls && kongzhi) {
			getmessage();
		}
	}
});

function getmessage() {
	var _html = '';
	var url = WEB_HOST;
	$.ajax({
		type: 'get',
		dataType: 'json',
		url: url + "/index.php/Home/News/news_list?pageindex=" + tops,
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
			if(tops == 1) {
				$('.message_details').empty();
			}
			var _data = eval(obj);
			var data = _data.data;

			if(_data.code == 0) {
				var lists = data.list;

				if(tops <= data.pageCount) {
					tops = tops + 1;
					for(var i = 0; i < lists.length; i++) {

						_html += '<li>';
						_html += ' <a href=';
						_html += url + '/index.php/Home/News/details?id=' + lists[i]['c_id'];
						_html += '>';

						_html += '<p class="news_font fl fs14">';
						_html += '<span class="tit fs14">';
						_html += lists[i]['c_title'];
						_html += "</span>";

						_html += '<span class="tim fs12">';
						_html += lists[i]['c_addtime'].substr(0, 10);
						_html += '</span>';
						_html += "</p>";
						var tempurl1 = lists[i]['c_img'];
						_html += '<img src="' + tempurl1 + '" class="fr news_img"> ';
						_html += ' <div class="clear"></div>';
						_html += '  </a>';
						_html += '  </li>';

					};

				} else {
					kongzhi = false;
				}

			} else {
				kongzhi = false;
			}
			if(tops == 1) {
				html += '<div class="data-empty divtab">';
				html += '<div class="data-empty-img">';
				html += '<img src="__COMMON__/img/empty_bg_card.png" alt="" />';
				html += '</div>';
				html += '<div class="data-empty-font c3 fs13">抱歉！没有相关的资讯消息!</div>';
				html += '</div>';
			}

			$(".message_details").append(_html);
			hd = $(".news_img").width();
			$(".news_img").height(hd);

			$('#console').css('display', 'none');
		},
		complete: function() {
			ctrls = true;
			$('#console').css('display', 'block');
			$('#console').html('加载完毕');
			hd = $(".news_img").width();
			$(".news_img").height(hd);
			setTimeout(function() {
				$('#console').css('display', 'none');
			}, 1500);
		}
	});
}