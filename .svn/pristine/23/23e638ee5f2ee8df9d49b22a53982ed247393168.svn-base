var ucode = $('#ucode').val();
var resurl = $('#resurl').val();

var tjdisz = true;
//提交评论
function subcomment(si) {
	if(tjdisz) {
		var feel = $('#feel').val();
		var rid = $('#rid').val();
		if(feel == '') {
			mui.toast('请填写评论内容');
			return;
		}
        tjdisz = false;
		$.post(WEB_HOST + '/index.php/Trade/Index/CommentResource', {
				content: feel,
				resourceid: rid,
				bid: $('#bid').val()
			},
			function(obj) {
                tjdisz = true;
				var msg = eval(obj);
				var com = '';
				if(msg['code'] == 0) {
					var comment = msg['data'][0];
					if(!comment['upucode']) {
						if(si == 1) {
							com += '<li id="comdel-' + comment['c_id'] + '" style="border:0px;" onclick="sendcommt(\'' + rid + '\',\'回复：' + comment['c_nickname'] + '\',\'' + comment['c_ucode'] + '\',\'' + comment['c_id'] + '\')">';
							com += '<div class="comment-chk-img fl"><img src="' + comment['c_headimg'] + '" alt=""></div>';
							com += '<div class="comment-chk-info fl">';
							com += '<div class="comment-chk-nkname fs13 c3">' + comment['c_nickname'] + '</div>';
							com += '<div class="comment-chk-remark fs11 c9">' + comment['switch_addtime'] + '</div>';
							com += '</div>';
							com += '</li>';
							com += '<li>';
							com += '<div class="fl comment-chk-time fs12 c5">' + comment['c_content'] + '</div>';
							com += '<div class="fr comment-del fs12" onclick="DeleteComment(this,\'' + comment['c_id'] + '\');">删除</div>';
							com += '</li>';
						} else {
							com += '<div class="f-comm-box fs12"><span class="f-comm-send"  onclick="sendcommt(\'' + rid + '\',\'回复：' + comment['c_nickname'] + '\',\'' + comment['c_ucode'] + '\',\'' + comment['c_id'] + '\')">';
							com += '<span>' + comment['c_nickname'] + '</span>：<em>' + comment['c_content'] + '</em></span>';
							com += '<span class="f-com-del fs12" onclick="DeleteComment(this,\'' + comment['c_id'] + '\');">&nbsp;&nbsp;&nbsp;删除</span></div>';
						}
					} else {
						if(si == 1) {
							com += '<li id="comdel-' + comment['c_id'] + '" style="border:0px;" onclick="sendcommt(\'' + rid + '\',\'回复：' + comment['c_nickname'] + '\',\'' + comment['c_ucode'] + '\',\'' + comment['c_id'] + '\')">';
							com += '<div class="comment-chk-img fl"><img src="' + comment['c_headimg'] + '" alt=""></div>';
							com += '<div class="comment-chk-info fl">';
							com += '<div class="comment-chk-nkname fs13 c3">' + comment['c_nickname'] + '<span>回复</span>' + comment['upnickname'] + '</div>';
							com += '<div class="comment-chk-remark fs11 c9">' + comment['switch_addtime'] + '</div>';
							com += '</div>';
							com += '</li>';
							com += '<li>';
							com += '<div class="fl comment-chk-time fs12 c5">' + comment['c_content'] + '</div>';
							com += '<div class="fr comment-del fs12" onclick="DeleteComment(this,\'' + comment['c_id'] + '\');">删除</div>';
							com += '</li>';
						} else {
							com += '<div class="f-comm-box fs12"><span class="f-comm-reply"  onclick="sendcommt(\'' + rid + '\',\'回复：' + comment['c_nickname'] + '\',\'' + comment['c_ucode'] + '\',\'' + comment['c_id'] + '\')"><span>' + comment['c_nickname'] + '</span><em>&nbsp;回复&nbsp;</em>';
							com += '<span>' + comment['upnickname'] + '</span>：<em>' + comment['c_content'] + '</em></span>';
							com += '<span class="f-com-del fs12" onclick="DeleteComment(this,\'' + comment['c_id'] + '\');">&nbsp;&nbsp;&nbsp;删除</span></div>';
						}
					}

					$('#comment-' + rid).prepend(com);
					commstyle();
					mui.toast('评论成功');
					$(".send-comm-bg").fadeOut(300);
					$(".send-comm-pup").fadeOut(300);
				} else {
					mui.toast(msg['msg']);
				}
			});
	}
}

/*发表评论的弹框*/
function sendcommt(rid, desc, rpucode, bid) {
	if(!ucode) {
		mui.confirm('评论需登录', '提示', ['取消', '确认'], function(e) {
			e.index == 0 ? "" : mui.openWindow({
				url: WEB_HOST + '/index.php/Login/Index/index',
				id: 'login'
			});
		}, 'div');
		return;
	}
	$('.send-comm-puptit').text(desc);
	$('#rid').val(rid);
	$('#bid').val(bid);
	$('#feel').val('');
	$(".send-comm-bg").fadeIn(300);
	$(".send-comm-pup").fadeIn(300);
	$(".send-comm-bg").height($(document).height());
}

$(".send-comm-bg").click(function() {
	$(".send-comm-bg").fadeOut(300);
	$(".send-comm-pup").fadeOut(300);
});

var tjpriaise = true;
/*点赞*/
function addpraise(rid) {
	if(tjpriaise) {
		if(!ucode) {
			mui.confirm('点赞需登录', '提示', ['取消', '确认'], function(e) {
				e.index == 0 ? "" : mui.openWindow({
					url: WEB_HOST + '/index.php/Login/Index/index',
					id: 'login'
				});
			}, 'div');
			return;
		}
        tjpriaise = false;
		var handle = $('#prastatu' + rid).val();
		$.post(WEB_HOST + '/index.php/Trade/Index/ResourceLike', {
				handle: handle,
				resourceid: rid
			},
			function(obj) {
                tjpriaise = true;
				var msg = eval(obj);
				var com = '';
				if(msg['code'] == 0) {
					if(handle == 1) {
						$('#prastatu' + rid).val(0);
						$("#praiseimg-" + rid).attr('src', WEB_HOST + '/Resource/Home/img/myspace/s_34.png');
						$("#praisenum-" + rid).text(parseInt($("#praisenum-" + rid).text()) - (-1));
					} else {
						$('#prastatu' + rid).val(1);
						$("#praiseimg-" + rid).attr('src', WEB_HOST + '/Resource/Home/img/myspace/s_22.png');
						$("#praisenum-" + rid).text(parseInt($("#praisenum-" + rid).text()) - 1);
					}
					mui.toast(msg['msg']);
				} else {
					mui.toast(msg['msg']);
				}
			});

	}

}

var tjatten = true;
// 关注操作
function attentionss(rid, issue_ucode, hid, ts) {
	if(tjatten) {
		tjatten = false;
		if(!ucode) {
			mui.confirm('关注需登录', '提示', ['取消', '确认'], function(e) {
				e.index == 0 ? "" : mui.openWindow({
					url: WEB_HOST + '/index.php/Login/Index/index',
					id: 'login'
				});
			}, 'div');
			tjatten = true;
			return;
		}
		var handle = $('#attentatu' + rid).val();
		$.post(WEB_HOST + '/index.php/Trade/Index/UserAttention', {
				handle: handle,
				issue_ucode: issue_ucode
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					if(handle == 1) {
						$('#attentatu' + rid).val(0);
						if(hid == 1) {
							$('#attention-' + rid).text('取消关注');
							//$('.' + issue_ucode).remove();
						} else {
							$('#attentionimg').attr('src', GET_HOST + '/Resource/Trade/img/myspace-friend4.png');
						}
					} else {
						$('#attentatu' + rid).val(1);
						if(hid == 1) {
							$('#attention-' + rid).text('+关注');
						} else {
							$('#attentionimg').attr('src', GET_HOST + '/Resource/Trade/img/myspace-friend3.png');
						}
					}
					mui.toast(msg['msg']);
				} else {
					mui.toast(msg['msg']);
					tjatten = true;
				}
			});
	}

}

// 删除资源
function delsource(ts, rid, sg) {
	mui.confirm('确定删除该资源吗？', '提示', ['取消', '确认'], function(e) {
		e.index == 0 ? "" : $.post(WEB_HOST + '/index.php/Trade/Index/DeleteResource', {
				sid: rid
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					if(sg == 1) {
						$(ts).parent().parent().remove();
					} else {
						$(ts).parent().parent().parent().parent().remove();
					}
					mui.toast(msg['msg']);
				} else {
					mui.toast(msg['msg']);
				}
			});
	}, 'div');

}
/*删除评论*/
function DeleteComment(ts, cid) {
	mui.confirm('确定删除该评论吗？', '提示', ['取消', '确认'], function(e) {
		e.index == 0 ? "" : $.post(WEB_HOST + '/index.php/Trade/Index/DeleteComment', {
				cid: cid
			},
			function(obj) {
				var msg = eval(obj);
				if(msg['code'] == 0) {
					$(ts).parent().remove();
					mui.toast(msg['msg']);
					$('#comdel-' + cid).remove();
				} else {
					mui.toast(msg['msg']);
				}
			});
	}, 'div');
}

/*全部评论列表样式*/
function commstyle() {
	//	var sendbc = $('.sendcomm-praise').height();
	//	$('.sendcomm-praisenum').css('line-height', sendbc + 'px');

	var chkimg = $('.comment-chk-img').width(),
		lihd = chkimg * 45 / 100;
	$('.comment-chk-img').height(chkimg);
	$('.comment-chk-img').css("border-radius", chkimg + "px");
	$('.comment-chk-info').css('line-height', lihd + 'px');
}

$(function() {
	/*关闭评论弹窗*/
	$(".send-comm-bg").click(function() {
		$(".send-comm-bg").fadeOut(300);
		$(".send-comm-pup").fadeOut(300);
	});
});

/*发表评论*/
// mui('.send-comm-pup').on('tap', '.send-comm-btn', function() {
// 	subcomment();
// });