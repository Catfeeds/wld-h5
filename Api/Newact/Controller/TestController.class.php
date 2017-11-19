<?php
namespace Trade\Controller;
use Base\Controller\BaseController;
/**
 * 测试
 */
class TestController extends BaseController {
    public function Getcircleinfo() {
    	$parr['ucode'] = 'T10115';
        $parr['province'] = '湖南';
        $parr['city'] = '岳阳';

        $result = IGD('Circle','Trade')->Getcircleinfo($parr);
        $this->ajaxReturn($result);
    }

    public function Switchcircle(){
        $parr['ucode'] = 'T10115';
        $parr['provincecode'] = '1606162112460164';
        $parr['citycode'] = '1606162233430205';

		$result = IGD('Circle','Trade')->Switchcircle($parr);
        $this->ajaxReturn($result);
	}

	public function Getprovinces(){
        $result = IGD('Circle','Trade')->Getprovinces();
        $this->ajaxReturn($result);
    }

    public function Getcirclelist(){
        $parr['provincecode'] = '1606162112460164';

        $result = IGD('Circle','Trade')->Getcirclelist($parr);
        $this->ajaxReturn($result);
    }

    public function GetSourceList() {
        $parr['pageindex'] = 1;
        $parr['condition'] = '';
        $parr['pagesize'] = 10;
        $parr['provincecode'] = '1606162112460164';
        $parr['citycode'] = '1606162233430206';

        $result = IGD('Resource','Trade')->GetCircleResource($parr);
        $this->ajaxReturn($result, 'JSON');
    }

     public function GetUserSourceList() {
        $parr['pageindex'] = 1;
        $parr['ucode'] = 'T10115';
        $parr['issue_ucode'] = 'T10115'; //发表用户编码
        $parr['pagesize'] = 10;

        $result = IGD('Resource','Trade')->GetResourceList($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    public function GetSourceInfo() {
        $parr['ucode'] = $ucode;'T10115';
        $parr['sid'] = 57;

        $result = IGD('Resource','Trade')->GetResourceInfo($parr);
        $this->ajaxReturn($result, 'JSON');
    }
    //获取七牛token
    public function GetQiniuToken(){
        $result = IGD('Resource','Trade')->GetQiniuToken($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    public function Mapdata() {
        $parr['provincecode'] = '1606162112460164';
        $parr['citycode'] = '1606162233430206';

        $result = IGD('Circle','Trade')->Mapdata($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    public function Merchant() {
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
       
        $parr['juli'] = 10;
        $parr['longitude'] = '158.937788';
        $parr['latitude'] = '29.241575';
        $parr['provincecode'] = '1606162112460164';
        $parr['citycode'] = '1606162233430206';
        $parr['gettype'] = 1;//1最热，2最新，3最近
        
        $result = IGD('Circle','Trade')->Merchant($parr);
        $this->ajaxReturn($result);
    }


    public function delvideo(){
        // // 需要填写你的 Access Key 和 Secret Key
        // $accessKey = C(Access_Key);
        // $secretKey = C(Secret_Key);

        // // 构建鉴权对象
        // $bucket = C(Bucket_Name);
        // $auth = new \Com\Qiniu\src\Auth($accessKey,$secretKey);
        // // 构建鉴权对象
        // $PersistentFop = new \Com\Qiniu\src\PersistentFop($auth,$bucket);

        // $key = "20170422094937.mp4";
        // $fops = "vframe/png/offset/1/w/100";
        // $result = $PersistentFop->execute($key,$fops);
        // dump($result[0]) ;  die;
        
       
        $durl = 'http://api.qiniu.com/status/get/prefop?id=z0.58fad25545a2650c998c1724';

        $arr = curlGet($durl);
        
        $picture = 'http://'.C(Explicit_Link).'/'.$arr['items'][0]['key'];

        
        dump($picture) ;  die;



        // C(Explicit_Link);
        // dump($result);die;
        // // 要上传的空间
       
        // $videourl = "http://obmeeen00.bkt.clouddn.com/20170417214657.mp4";
        // $wipeoff = 'http://'.C(Explicit_Link).'/';
        // $key = str_replace($wipeoff, '', $videourl);

        // $result = $BucketManager->delete($bucket,$key);
        
        // if($result == null){
        //     return Message(1001,"删除失败");
        // }
    }
}