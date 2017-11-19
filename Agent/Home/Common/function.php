<?php

function getpage($count, $pagesize = 10) {
    $Page = new Think\Page($count, $pagesize);
    $Page->setConfig('header', '<span class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</span>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数
    return $Page;
}



?>