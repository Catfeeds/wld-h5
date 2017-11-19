(function($) {
    $.fn.showtime = function(options) { //定义插件的名称
        var dft = {
            sign: 1,
            startname: "距开始时",
            endname: "距结束时",
            ended: "活动结束",
            starttime: 0,
            endtime: 0
        };
        var ops = $.extend(dft, options);
        setInterval(function(){ 
                 ops.starttime--;
                ops.endtime--;               
                if (ops.starttime >= 0) {
                    document.all["coll_"+ops.sign].innerHTML = ops.startname;                    
                    t = parseInt(ops.starttime / 86400);
                    b = parseInt((ops.starttime % 86400) / 3600);
                    c = parseInt((ops.starttime % 3600) / 60);
                    w = ops.starttime % 60;
                    
                    document.all["t_"+ops.sign].innerHTML = t < 10 ? '0' + t : t;
                    document.all["h_"+ops.sign].innerHTML = b < 10 ? '0' + b : b;
                    document.all["f_"+ops.sign].innerHTML = c < 10 ? '0' + c : c;
                    document.all["m_"+ops.sign].innerHTML = w < 10 ? '0' + w : w;
                    
                 } else if(ops.endtime >= 0 &&　ops.starttime < 0) {
                    document.all["coll_"+ops.sign].innerHTML = ops.endname;                    
                    t = parseInt(ops.endtime / 86400);
                    b = parseInt((ops.endtime % 86400) / 3600);
                    c = parseInt((ops.endtime % 3600) / 60);
                    w = ops.endtime % 60;

                    document.all["t_"+ops.sign].innerHTML = t < 10 ? '0' + t : t;
                    document.all["h_"+ops.sign].innerHTML = b < 10 ? '0' + b : b;
                    document.all["f_"+ops.sign].innerHTML = c < 10 ? '0' + c : c;
                    document.all["m_"+ops.sign].innerHTML = w < 10 ? '0' + w : w;   
                 } else {
                    document.all["coll_"+ops.sign].innerHTML = ops.ended;
                    document.all["t_"+ops.sign].innerHTML = '00';
                    document.all["h_"+ops.sign].innerHTML = '00';
                    document.all["f_"+ops.sign].innerHTML = '00';
                    document.all["m_"+ops.sign].innerHTML = '00';     
                 }
             },1000);
        
    }
})(jQuery);