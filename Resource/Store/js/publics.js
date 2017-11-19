

/*查看全部商家信息*/
mui('.mui-content').on('tap', '.store-more', function() {
	$('#slideup').slideUp();
	$('#slidedown').slideDown();
	var sicoh = $('.service-ico').height();
	$('.service-text').css('line-height', sicoh + 'px');
});
mui('.mui-content').on('tap', '.store-info-slidup', function() {
	$('#slideup').slideDown();
	$('#slidedown').slideUp();
});



