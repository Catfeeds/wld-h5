<?php
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
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

//上传文件
function upimg($filepath,$_file){
  $upload = new \Think\Upload();
  $upload->maxSize   =     3145728 ;
  $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
  $upload->savePath  =      $filepath.'/';
  $info   =   $upload->uploadOne($_file);
  if(!$info) {
    return Message(1007,$upload->getError());
  }else{
    $img='Uploads/'.$info['savepath'].$info['savename'];

    $result = qiniu_syn_files($img,$img);
    if(!$result){
        return Message(1007,'远程上传图片失败');  
    }
  }
  return $img;
}

//循环输出评论评论
function echocomment($parr)
{
    foreach ($parr as $key => $value) {
        echo '<tr class="text-c">';
        echo '<td><input type="checkbox" value="'.$value['c_id'].'" name="resourceid"></td>';
        echo '<td style="text-align: left;">回复：'.$value['upnickname'].'</td>';
        echo '<td><a title="会员详情" href="'.GetHost(1).'/index.php/Admin/Member/member_list?ucode='.$value['c_ucode'].'" style="text-decoration:none">'.$value['c_nickname'].'</a>';
        echo '</td>';
        echo '<td>';
        echo '<a href="'.$value['c_headimg'].'"  target="_blank">';
        echo '<img src="'.$value['c_headimg'].'"  height=80 width=80 style="border: 1px solid #ebebeb;"/>';              
        echo '</a>';
        echo '</td>';           
        echo '<td>'.$value['c_content'].'</td>';     
        echo '<td>'.$value['c_addtime'].'</td>';
        echo '<td class="f-14 td-manage">';
        echo '<a style="text-decoration:none" class="ml-5" onClick=article_edit("回复：'.$value['c_nickname'].'","Resourcev2/commentadd?rid='.$value['c_resourceid'].'&bid='.$value['c_id'].'&ptid='.$value['c_ptid'].'&upucode='.$value['c_ucode'].'") href="javascript:;" title="回复"><i class="Hui-iconfont">&#xe622;</i></a>';
        echo '<a style="text-decoration:none" class="ml-5" onClick=article_del(this,"'.$value['c_id'].'") href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>';
        echo '</td>';
        echo '</tr>';
        if (count($value['child']) > 0) {
            echocomment($value['child']);
        }
    }
}
