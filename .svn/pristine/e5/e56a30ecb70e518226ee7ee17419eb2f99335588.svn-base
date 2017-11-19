<?php
namespace Trade\Controller;
use Base\Controller\BaseController;
/**
 * 商圈模块
 * @author 
 */
class ResourceController extends BaseController {
    /**
    * 获取某商圈全部资源信息列表
    * @param  openid,pageindex,condition,provincecode,citycode
    */
    public function GetSourceList() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['condition'] = I('condition');
        $parr['pagesize'] = 10;
        $parr['province'] = I('province');
        $parr['city'] = I('city');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['longitude'] =I('longitude');//经度
        $parr['latitude'] =I('latitude'); //纬度

        $result = IGD('Resource','Trade')->GetCircleResource($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    /**
    * 获取个人空间全部资源信息列表
    * @param  openid,pageindex,issue_ucode
    */
    public function GetUserSourceList() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['pageindex'] = I('pageindex');
        $parr['ucode'] = $ucode;
        $parr['issue_ucode'] = I('issue_ucode'); //发表用户编码
        $parr['pagesize'] = 10;

        $result = IGD('Resource','Trade')->GetResourceList($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    /**
    * 获取资源详情
    * @param  openid,resourceid
    */
    public function GetSourceInfo() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['sid'] = I('resourceid');

        $result = IGD('Resource','Trade')->GetResourceInfo($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    /**
    * 查看所有评论
    * @param  openid,resourceid
    */
    public function AllComments() {
        $id = I('resourceid');

        $result = IGD('Resource','Trade')->get_commentlist($id, 1);
        $this->ajaxReturn($result);
    }

    /**
    * 获取个人空间
    * @param  openid,perucode,source
    */
    public function SpaceHead() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['perucode'] = I('perucode'); //被访问用户编码
        $parr['source'] = I('source'); //访问来源，Android,2是IOS

        $result = IGD('Resource','Trade')->PersonalDate($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    /**
    * 获取个人店铺头部头部数据
    * @param  openid,perucode,source
    */
    public function ShopHead() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['perucode'] = I('perucode'); //被访问用户编码

        $result = IGD('Resource','Trade')->PersonalShop($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    /**
    * 获取个人店铺
    * @param  openid,perucode,source
    */
    public function ShopProducts() {
        $parr['shop_ucode'] = I('shop_ucode');
        $parr['type'] = I('gettype'); //1 按最新排序 2按销量排序 3按价格升序排序 4按价格降序排序
        $parr['pagesize'] = 10;
        $parr['pageindex'] = I('pageindex');

        $result = IGD('Resource','Trade')->GetproductList($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //新增资源信息
    public function Addimagetext() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['content'] = I('content');
        $parr['pcode'] = I('pcode');
        $parr['isaddress'] = I('isaddress');
        $parr['address'] = I('address');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');

        if($_FILES){
            $upload_path = 'source';
            $result = uploadimg($upload_path);
            
            if ($result['code'] != 0) {
                $result1 = Message(1024, "请上传图片");
                $this->ajaxReturn($result1, 'JSON');
            }

            $imglist = array_values($result['data']);
            $parr['imglist'] = $imglist;
        }

        $result1 = IGD('Resource','Trade')->AddResource($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

    //获取商盟用户商品信息
    public function UserProductList() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['ucode'] = $ucode;
        $parr['type'] = 0;
        $parr['pageindex'] = I('pageindex');
        $result = IGD('Resource','Trade')->GetProduceList($parr);
        $this->ajaxReturn($result);
    }

     //删除用户资源信息
    public function DeleteSource() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['sid'] = I('resourceid');

        $result = IGD('Resource','Trade')->DeleteResource($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //添加评论
    public function AddComment(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['resourceid'] = I('resourceid');
        $parr['content'] = I('content');
        $parr['bid'] = I('bid');
       
        $result = IGD('Resource','Trade')->CommentResource($parr);
        $this->ajaxReturn($result);
    }

    //删除评论
    public function DeleteComment(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['cid'] = I('cid');
        
        $result = IGD('Resource','Trade')->DeleteComment($parr);
        $this->ajaxReturn($result);
    }

    //添加、取消点赞
    public function HandleLike(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['resourceid'] = I('resourceid');
        $parr['handle'] = I('handle'); //0-取消点赞,1-点赞
       
        $result = IGD('Resource','Trade')->ResourceLike($parr);
        $this->ajaxReturn($result);
    }

    //用户关注
    public function Attention(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['issue_ucode'] = I('issue_ucode');//发表用户编码
        $parr['handle'] = I('handle'); //0-取消关注,1-关注

        $result = IGD('Resource','Trade')->UserAttention($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //增加推荐商品访问记录
    public function ProductVisit(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pcode'] = I('pcode');
        $parr['source'] = I('source');

        $w1['c_ucode'] = $ucode;
        $userinfo = M('Users')->where($w1)->field('c_nickname,c_headimg')->find();
        $userinfo['c_headimg'] = GetHost().'/'. $userinfo['c_headimg'];

        $parr['nickname'] = $userinfo['c_nickname'];
        $parr['headimg'] = $userinfo['c_headimg'];
        $parr['ip'] = GetIP();

        $result = IGD('Resource','Trade')->ProductVisit($parr);
        $this->ajaxReturn($result, 'JSON');
    }

     //自己访问自己个人空间
    public function MyspaceHead(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['perucode'] = $ucode;//被访问用户编码

        $result = IGD('Resource','Trade')->PersonalDate($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //我的关注 和 我的粉丝 
    public function AttentionDate(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['type'] = I('type');//1我的关注 2我的粉丝
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;

        $result = IGD('Resource','Trade')->Myattention($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //获取七牛token
    public function GetQiniuToken(){
        $result = IGD('Resource','Trade')->GetQiniuToken($parr);
        $this->ajaxReturn($result, 'JSON');
    }

    //新增视频资源
    public function Addvideo() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['content'] = I('content');
        $parr['videourl'] = I('videourl');
        $parr['isaddress'] = I('isaddress');
        $parr['address'] = I('address');
        $parr['longitude'] = I('longitude');
        $parr['latitude'] = I('latitude');
        $parr['provincecode'] = I('provincecode');
        $parr['citycode'] = I('citycode');
        $parr['pcode'] = I('pcode');

        $result1 = IGD('Resource','Trade')->Addvideo($parr);
        $this->ajaxReturn($result1, 'JSON');
    }

}