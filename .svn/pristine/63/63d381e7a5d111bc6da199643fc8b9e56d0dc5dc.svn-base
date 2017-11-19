<?php

/**
 * 商家生成收款二维码接口
 */
class QrcodeStore {
    //图片的等比缩放
    public function imgzoom($path,$maxsize){ 
      //因为PHP只能对资源进行操作，所以要对需要进行缩放的图片进行拷贝，创建为新的资源
      $src = imagecreatefrompng($path);

      //取得源图片的宽度和高度
      $size_src = getimagesize($path);
      $w = $size_src['0'];
      $h = $size_src['1'];

      //指定缩放出来的最大的宽度（也有可能是高度）
      $max = $maxsize;

      //根据最大值为700，算出另一个边的长度，得到缩放后的图片宽度和高度
      if($w > $h){
          $w = $max;
          $h = $h*($max/$size_src['0']);
      }else{
          $h = $max;
          $w = $w*($max/$size_src['1']);
      }

      //声明一个$w宽，$h高的真彩图片资源
      $image = imagecreatetruecolor($w, $h);

      //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
      imagecopyresampled($image, $src, 0, 0, 0, 0, $w, $h, $size_src['0'], $size_src['1']);

      imagepng($image,$path);
    }

    //生成二维码 新增qrcode_type(1-收款二维码,2-店铺二维码,3-收银柜台二维码)
    function CreateQrcode($parr,$shopinfo) {
        $ucode = $parr['ucode'];

        //生成二维码颜色
        $bgcolor = $parr['bgcolor'];
        if(!empty($bgcolor)){
          $bgcolor = $bgcolor;//"#f52f46"
        }else{
          $bgcolor = "";
        }       
       
        //中间图片
        $mparr['log_img'] = GetHost(4).'/'.$shopinfo['c_headimg'];

        //二维码颜色
        $mparr['bgcolor'] = $bgcolor;
        //二维码指向链接
        if($parr['qrcode_type'] == 1){
          //生成图片名称
          $mparr['img_name'] = $shopinfo['c_ucode'].'.png';
          //指向收款
          $mparr['url'] = GetHost(1).'/index.php/Order/Scanpay/index?acode='.$shopinfo['c_ucode'];
          //生成文件路径
          $mparr['oldpath'] = 'Uploads/qrcode/beginning/';
          $mparr['path'] = 'Uploads/qrcode/';

        }else if($parr['qrcode_type'] == 2){
          //生成图片名称
          $mparr['img_name'] = $shopinfo['c_ucode'].'.png';
          //指向店铺地址
          $mparr['url'] = GetHost(1).'/index.php/Store/Index/index?fromucode='.$shopinfo['c_ucode'];
          //生成文件路径
          $mparr['oldpath'] = 'Uploads/storeqrcode/beginning/';
          $mparr['path'] = 'Uploads/storeqrcode/';

          $data['share_title'] = '【'.$shopinfo['c_nickname'].'】的店铺';;
          $data['share_desc'] = '这家店铺还不错，说不定有你喜欢的东西哦！';
          $data['share_img'] = GetHost().'/'.$shopinfo['c_headimg'];
          $data['share_url'] = $mparr['url'];
        }else if($parr['qrcode_type'] == 3){
          //生成图片名称
          $mparr['img_name'] = $shopinfo['c_ucode'].$parr['deskid'].'.png';
          //指向店铺收银柜台地址
          $mparr['url'] = GetHost(1).'/index.php/Order/Scanpay/index?acode='.$shopinfo['c_ucode'].'&deskid='.$parr['deskid'];
          //生成文件路径
          $mparr['oldpath'] = 'Uploads/cashierqrcode/beginning/';
          $mparr['path'] = 'Uploads/cashierqrcode/';
        }  

        $result = $this->qrcode($mparr);

        if(!$result){
            return Message(1003,'生成二维码失败');
        }

        $path = $mparr['path'].$mparr['img_name'];

        if($parr['qrcode_type'] == 1){
          $this->imgzoom($path,700);
          $savedata['c_qrcodeimg'] =  $path;
          $result1 = M('Users')->where($where)->save($savedata);
        }else if($parr['qrcode_type'] == 2){
          $this->imgzoom($path,520);

          $key = $shopinfo['c_ucode'].'.png';
          $result = qiniu_syn_files($path,$key);

          if(!$result){
            return Message(2004,'远程上传图片失败');
          }
        }else{
          $this->imgzoom($path,520);
        }

        $data['img'] = GetHost().'/'.$mparr['path'].$mparr['img_name'].'?time='.time();
        $data['url'] = $mparr['url'];
        return MessageInfo(0,'生成二维码成功',$data);
    }

   //生成二维码
  function qrcode($mparr,$level = 'L',$size = 20){
    Vendor('phpqrcode.phpqrcode');
    $errorCorrectionLevel = $level ;//容错级别
    $matrixPointSize = intval($size);//生成图片大小

    $QR = $mparr['oldpath'].$mparr['img_name'];
    if(checkDir($QR)){
      //生成二维码图片
      $object = new \QRcode();
      $object->png($mparr['url'],$QR, $errorCorrectionLevel, $matrixPointSize, 1,false,$mparr['bgcolor']);
    }
    
    $logo = $mparr['log_img'];//准备好的logo图片
    $logo = str_replace('https', 'http', $logo);

    if ($logo !== FALSE) {
      $QR = imagecreatefromstring(file_get_contents($QR));
      $logo = imagecreatefromstring(file_get_contents($logo));
      if (imageistruecolor($logo)) imagetruecolortopalette($logo, false, 65535); // 新加这个
      $QR_width = imagesx($QR);//二维码图片宽度
      $QR_height = imagesy($QR);//二维码图片高度
      $logo_width = imagesx($logo);//logo图片宽度
      $logo_height = imagesy($logo);//logo图片高度
      $logo_qr_width = $QR_width / 5;
      $scale = $logo_width/$logo_qr_width;
      $logo_qr_height = $logo_height/$scale;
      $from_width = ($QR_width - $logo_qr_width) / 2;
      //重新组合图片并调整大小
      imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
    }
    //输出图片
    $path = $mparr['path'].$mparr['img_name'];
    if(checkDir($path)){
      imagepng($QR,$path);
    }

    if (!file_exists($path)){
      return false;
    }

    return ture;
  }

  //生成店铺二维码展示图片
  function StoreQrcode($parr){
    $ucode = $parr['ucode'];

    $where['c_ucode'] = $ucode;
    $shopinfo = M('Users')->where($where)->find();

    if(!$shopinfo){
        return Message(2001,'该用户不存在');
    }

    if($shopinfo['c_shop'] == 0){
        return Message(2002,'该用户不是商家，不能生成图片！');
    }

    //检查商家二维码是否存在，不存在重新生成
    $result = $this->CreateQrcode($parr,$shopinfo);
    if($result['code'] != 0){
      return $result;
    }

    //拼凑图片
    $imgname = SYS_PATH .'Uploads'. DS .'storeqrcode'. DS .'template'. DS .'codebg.png'; //模板图片路径
    $image = new \Think\Image();
    $imagepath = SYS_PATH .'Uploads'. DS .'storeqrcode'. DS .'shopqrcode'. DS .$shopinfo['c_ucode'].'.png'; //图片复制的本地路径

    //创建目录
    if(checkDir($imagepath))     
    
    //插入文字
    $text = mb_substr($shopinfo['c_nickname'], 0, 12, 'utf8'); 
    $path = 'Uploads/storeqrcode/shopqrcode/'.$shopinfo['c_ucode'].'.png';     //最终生成图片路径
    $result = $image->open($imgname)->save($imagepath);
    $locate = array('130','100');              //文字位置
    $data = $image->open($imagepath)->text($text,'./data/simhei.ttf',18,'#000000',$locate)->save($path);

    //插入二维码
    $image_1 = imagecreatefrompng($path);//添加文字后的模板

    //二维码路径
    $ewmpath = 'Uploads/storeqrcode/' .$shopinfo['c_ucode'].'.png';
    $image_2 = imagecreatefrompng($ewmpath);

    //头像路径
    $log_img = GetHost(4).'/'.$shopinfo['c_headimg'];
    $image_3 = imagecreatefromstring(file_get_contents($log_img));
    //处理头像大小
    $size_src = getimagesize($log_img);
    $w = $size_src['0'];
    $h = $size_src['1'];   
    
    //创建一个和模板图片一样大小的真彩色画布（ps：只有这样才能保证后面copy模板图片的时候不会失真）
    $image_4 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));

    //为真彩色画布创建白色背景，再设置为透明
    $color = imagecolorallocate($image_4, 255, 255, 255);
    imagefill($image_4, 0, 0, $color);
    // imageColorTransparent($image_3, $color);

    //首先将添加文字后的模板画布采样copy到真彩色画布中，不会失真
    imagecopyresampled($image_4,$image_1,0,0,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
    //再将头像图片copy到已经模板图像的真彩色画布中，同样也不会失真
    imagecopyresampled($image_4,$image_3,65,80,0,0,60,60,$w,$h);
    // imagecopymerge($image_4,$image_3, 55,20,0,0,imagesx($image_3),imagesy($image_3),100);
    //再将二维码图片copy到已经模板图像的真彩色画布中，同样也不会失真
    imagecopymerge($image_4,$image_2, 55,160,0,0,imagesx($image_2),imagesy($image_2),100);   

    imagepng($image_4,$path);

    if(!is_file($path)){
      return Message(2003,'生成图片失败');
    }

    $date['img'] = GetHost().'/'.$path.'?time='.time();
    $date['share_desc'] = '这家店铺还不错，说不定有你喜欢的东西哦！';
    $date['share_img'] = GetHost().'/'.$shopinfo['c_headimg'];
    $date['share_url'] = GetHost(1).'/index.php/Store/Index/index?fromucode='.$shopinfo['c_ucode'];//指向店铺地址
    $date['ucode'] = $ucode;

    $key = $shopinfo['c_ucode'].'.png';
    $result = qiniu_syn_files($path,$key);

     if(!$result){
         return Message(2004,'远程上传图片失败');
    }

    return MessageInfo(0,'生成图片成功',$date);
  }



  //生成收款二维码展示图片
  function PuzzleQrcode($parr){
    $ucode = $parr['ucode'];

    $where['c_ucode'] = $ucode;
    $shopinfo = M('Users')->where($where)->find();

    if(!$shopinfo){
        return Message(2001,'该用户不存在');
    }

    if($shopinfo['c_shop'] == 0){
        return Message(2002,'该用户不是商家，不能生成图片！');
    }

    //检查商家二维码是否存在，不存在重新生成
    // if(!is_file($shopinfo['c_qrcodeimg'])){
      $result = $this->CreateQrcode($parr,$shopinfo);
      if($result['code'] != 0){
        return $result;
      }
    // }
    //拼凑图片
    $imgname = SYS_PATH .'Uploads'. DS .'qrcode'. DS .'template'. DS .'codebg.png'; //模板图片路径
    $image = new \Think\Image();
    $imagepath = SYS_PATH .'Uploads'. DS .'qrcode'. DS .'shopqrcode'. DS .$shopinfo['c_ucode'].'.png'; //图片复制的本地路径

    //判断商家收款二维码是否存在
    // if(!is_file($imagepath)){
    //创建目录
    if(checkDir($imagepath))                //创建目录
    //插入文字
    // $text = '收款人:谢秋林谢秋林谢秋林谢秋';
    $text = '收款人:'.mb_substr($shopinfo['c_nickname'], 0, 10, 'utf8'); //;40
    $difflocal = 40 - count(str_split($text));
    $path = 'Uploads/qrcode/shopqrcode/'.$shopinfo['c_ucode'].'.png';     //最终生成图片路径
    $result = $image->open($imgname)->save($imagepath);
    $x = 290 + $difflocal*6;
    $locate = array($x,'120');              //文字位置
    $data = $image->open($imagepath)->text($text,'./data/simhei.ttf',42,'#ffffff',$locate)->save($path);

    //插入二维码
    $image_1 = imagecreatefrompng($path);//添加文字后的模板

    $ewmpath = 'Uploads/qrcode/' .$shopinfo['c_ucode'].'.png';//二维码路径
    $image_2 = imagecreatefrompng($ewmpath);

    //创建一个和模板图片一样大小的真彩色画布（ps：只有这样才能保证后面copy模板图片的时候不会失真）
    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));

    //为真彩色画布创建白色背景，再设置为透明
    $color = imagecolorallocate($image_3, 255, 255, 255);
    imagefill($image_3, 0, 0, $color);
    // imageColorTransparent($image_3, $color);

    //首先将添加文字后的模板画布采样copy到真彩色画布中，不会失真
    imagecopyresampled($image_3,$image_1,0,0,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
    //再将二维码图片copy到已经模板图像的真彩色画布中，同样也不会失真
    imagecopymerge($image_3,$image_2, 270,310,0,0,imagesx($image_2),imagesy($image_2),100);

    imagepng($image_3,$path);

    if(!is_file($path)){
      return Message(2003,'生成图片失败');
    }

    $date['img'] = GetHost().'/'.$path.'?time='.time();

    $date['share_title'] = '【'.$shopinfo['c_nickname'].'】的店铺';;
   
  	$key = $shopinfo['c_ucode'].'.png';
    $result = qiniu_syn_files($path,$key);

    if(!$result){
      return Message(2004,'远程上传图片失败');
    }

    return MessageInfo(0,'生成图片成功',$date);
  }



  //生成店铺收银员柜台展示二维码图片
  function CashierQrcode($parr){
    $ucode = $parr['ucode'];

    $where['c_ucode'] = $ucode;
    $shopinfo = M('Users')->where($where)->find();

    if(!$shopinfo){
        return Message(2001,'该用户不存在');
    }

    if($shopinfo['c_shop'] == 0){
        return Message(2002,'该用户不是商家，不能生成图片！');
    }

    //检查商家二维码是否存在，不存在重新生成
    $result = $this->CreateQrcode($parr,$shopinfo);
    if($result['code'] != 0){
      return $result;
    }
    //拼凑图片
    $imgname = SYS_PATH .'Uploads'. DS .'cashierqrcode'. DS .'template'. DS .'codebg.png'; //模板图片路径
    $image = new \Think\Image();
    $imagepath = SYS_PATH .'Uploads'. DS .'cashierqrcode'. DS .'cashierqrcode'. DS .$shopinfo['c_ucode'].$parr['deskid'].'.png'; //图片复制的本地路径

    //判断商家收款二维码是否存在
    // if(!is_file($imagepath)){
    //创建目录
    if(checkDir($imagepath))                //创建目录

    $path = 'Uploads/cashierqrcode/cashierqrcode/'.$shopinfo['c_ucode'].$parr['deskid'].'.png';     //最终生成图片路径
    $result = $image->open($imgname)->save($imagepath);
    //插入文字
    $text = mb_substr($shopinfo['c_nickname'], 0, 14, 'utf8');
    $difflocal = 24 - count(str_split($text));
    $x = 230 + $difflocal*6;
    $locate = array($x,'820'); //文字位置     
    $data = $image->open($imagepath)->text($text,'./data/simhei.ttf',24,'#ffffff',$locate)->save($path);   

    //插入二维码
    $image_1 = imagecreatefrompng($path);//添加文字后的模板

    $ewmpath = 'Uploads/cashierqrcode/' .$shopinfo['c_ucode'].$parr['deskid'].'.png';//二维码路径
    $image_2 = imagecreatefrompng($ewmpath);

    //创建一个和模板图片一样大小的真彩色画布（ps：只有这样才能保证后面copy模板图片的时候不会失真）
    $image_3 = imageCreatetruecolor(imagesx($image_1),imagesy($image_1));
    //为真彩色画布创建白色背景，再设置为透明
    $color = imagecolorallocate($image_3, 255, 255, 255);
    imagefill($image_3, 0, 0, $color);
    // imageColorTransparent($image_3, $color);

    //首先将添加文字后的模板画布采样copy到真彩色画布中，不会失真
    imagecopyresampled($image_3,$image_1,0,0,0,0,imagesx($image_1),imagesy($image_1),imagesx($image_1),imagesy($image_1));
    //再将二维码图片copy到已经模板图像的真彩色画布中，同样也不会失真
    imagecopymerge($image_3,$image_2, 100,256,0,0,imagesx($image_2),imagesy($image_2),100);

    imagepng($image_3,$path);

    //再次插入文字
    $text1 = mb_substr($parr['xhname'], 0, 10, 'utf8');
    $difflocal1 = 20 - count(str_split($text1));
    $x1 = 260 + $difflocal1*6;
    $locate1 = array($x1,'860'); //文字位置    
    $data = $image->open($imagepath)->text($text1,'./data/simhei.ttf',20,'#ffffff',$locate1)->save($path);

    if(!is_file($path)){
      return Message(2003,'生成图片失败');
    }

    $date['img'] = GetHost().'/'.$path.'?time='.time();
   
    $key = $shopinfo['c_ucode'].$parr['deskid'].'.png';
    $result = qiniu_syn_files($path,$key);

    if(!$result){
      return Message(2004,'远程上传图片失败');
    }

    return MessageInfo(0,'生成图片成功',$date);
  }
}
