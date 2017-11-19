<?php
namespace Home\Controller;
use Base\Controller\BaseController;
/**
 *  商城模块
 */
class MallController extends BaseController {
		//商城banner列表
	    public function BannerList(){
	        $parr['source'] = I('source'); //1-商城，2-小蜜商城，3新版商城
	        $parr['tag'] = I('tag'); //终端标识 1-Web 2-APP

	        $result = IGD('Common','Info')->get_banner($parr);
	        $this->ajaxReturn($result);
	    }

        //商城首页获取模块内容
        public function GetMallHompage(){
            $parr['state'] = I('state');
            $result = IGD('Mall','User')->GetMallHompage($parr);
            $this->ajaxReturn($result);
        }

    	//商城首页获取推荐商品
        public function ProductTjList(){
            $result = IGD('Mall','User')->ProductTjList($parr);
            $this->ajaxReturn($result);
        }

    //返回举报信息列表

    public function GetTipList(){
//        $data["code"] =0;
//        $data["msg"] ="返回成功";
//        $data["data"] =[
//            1=>'广告欺诈',
//            2=>'恶意刷屏',
//            3=>'图片辣眼睛',
//            4=>'言论反动',
//            5=>'色情淫秽'
//        ];
        $data =IGD('Mall','User')->GetTipsLists();
        $this->ajaxReturn($data);
    }

    //提交举报信息
     public function PutTipInfos(){
         $key = I('openid');
         $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
         $parr['tip_id'] =I('tip_id');
         $parr['content'] =I('content');
         $parr['content_id'] =I('content_id');
         $parr['ucode'] = $ucode;

         $result =IGD('Mall','User')->PutTipInfos($parr);
         $this->ajaxReturn($result);

     }

    //提交举报申诉
    public function SubmitAppeal(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
        $parr['content'] =I('content');
        $parr['Id'] =I('Id');
        $parr['ucode'] = $ucode;

        $result =IGD('Mall','User')->SubmitAppeal($parr);
        $this->ajaxReturn($result);
    }


    //获取举报动态详情
    public function GetSourceInfo() {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['sid'] = I('Id');

        $result = IGD('Resource','Trade')->GetTipInfo($parr);
        $this->ajaxReturn($result, 'JSON');
    }


    //获取关键字 列表信息
    public function getKeywords(){
        $parr['name'] =I('name');
        $result =IGD('Mall','User')->GetKeywordsList($parr);
        $this->ajaxReturn($result);
    }


    //获取搜索条件列表
    public function getConditionList(){

        $data =IGD('Mall','User')->GetSearchConditionList();
        $this->ajaxReturn($data);
    }


    //所有商品分类列表及搜索
        public function AllProductList(){
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 10;

            $parr['pname'] = I('pname');
            $parr['categoryid'] = I('categoryid');
            $parr['order_type'] =I('order_type');

            $result = IGD('Mall','User')->AllProductList($parr);
            $this->ajaxReturn($result);
        }

        //附近线下商品分类列表及搜索
        public function NearbyProductList(){
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 10;

            $parr['pname'] = I('pname');
            $parr['categoryid'] = I('categoryid');
            $parr['longitude'] = I('longitude');
            $parr['latitude'] = I('latitude');

            $result = IGD('Mall','User')->NearbyProductList($parr);
            $this->ajaxReturn($result);
        }

        // 查询商品分类
        public function GetCategory(){
            $result = IGD('Mall','User')->CategoryList();
            $this->ajaxReturn($result);
        }

        //猜你喜欢产品列表
        public function GuessProduct(){
            $parr['pageindex'] = I('pageindex');
            $parr['pagesize'] = 20;

            $key = I('openid');
            $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

            $parr['ucode'] = $ucode;

            $result = IGD('Mall','User')->GuessProduct($parr);
            $this->ajaxReturn($result);
        }

        //猜你喜欢 喜欢
        public function LikeProduct(){
            $key = I('openid');
            $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

            $parr['ucode'] = $ucode;
            $parr['pcode'] = I('pcode');
            $parr['categoryid'] = I('categoryid');

            $result = IGD('Mall','User')->LikeProduct($parr);
            $this->ajaxReturn($result);
        }

        //猜你喜欢 不喜欢
        public function NolikeProduct(){            
            $key = I('openid');
            $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

            $parr['ucode'] = $ucode;
            $parr['pcode'] = I('pcode');
            
            $result = IGD('Mall','User')->NolikeProduct($parr);
            $this->ajaxReturn($result);
        }
}