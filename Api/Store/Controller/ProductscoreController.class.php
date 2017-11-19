<?php
namespace Store\Controller;
use Base\Controller\BaseController;
/**
 * 	商家评论管理专区模块
 */
class ProductscoreController extends BaseController {
	/**
	 *  评论专区所有评论
	 *  @param openid,useraction(0-全部评论、1-店铺评论、2-商品评论)
	 *
	 */
	public function GetAllScore()
	{
	    $key = I('openid');
	    $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

	    $parr['ucode'] = $ucode;
	    $parr['pageindex'] = I('pageindex');
	    $parr['pagesize'] = 10;
	    $parr['useraction'] = I('useraction');
        $parr['acode'] = I('acode');

	    $result = IGD('Productscore','Store')->GetAllScore($parr);
	    $this->ajaxReturn($result);
	}
	
	 /**
     *  获取评价详细信息
     *  @param openid,scoreid 评价id
     */
	public function GetScoreInfo()
	{
	    $key = I('openid');
	    $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

	    $parr['ucode'] = $ucode;
	    $parr['sid'] = I('scoreid');

	    $result = IGD('Productscore','Store')->GetScoreInfo($parr);
	    $this->ajaxReturn($result);
	}

	/**
     *  点赞、点不赞
     *  @param handle 0-点赞，1-点不赞,openid,scoreid
     */
    public function ScoreLike() {
    	$key = I('openid');
    	$ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

    	$parr['ucode'] = $ucode;
    	$parr['scoreid'] = I('scoreid');
    	$parr['handle'] = I('handle');

    	$result = IGD('Productscore','Store')->ScoreLike($parr);
    	$this->ajaxReturn($result);
    }

    /**
    * 查看所有评论
    * @param  openid,scoreid
    */
    public function AllComments() {
        $id = I('scoreid');

        $result = IGD('Productscore','Store')->get_commentlist($id, 1);
        $this->ajaxReturn(MessageInfo(0,查询成功,$result));
    }

    /**
    * 添加评论
    * @param  openid,scoreid,content,bid
    */
    public function AddComment(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['scoreid'] = I('scoreid');
        $parr['content'] = I('content');
        $parr['bid'] = I('bid');
       
        $result = IGD('Productscore','Store')->CommentScore($parr);
        $this->ajaxReturn($result);
    }

    /**
	 *  删除评论及子评论
	 *  @param openid,cid
	 *
	 */
    public function DeleteComment(){
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['cid'] = I('cid');
        
        $result = IGD('Productscore','Store')->DeleteComment($parr);
        $this->ajaxReturn($result);
    }

    /**
	 *  获取全部商品评论信息
	 *  @param openid,acode,pcode
	 *
	 */
    public function GetProductAllScore()
    {
        $key = I('openid');
        $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

        $parr['ucode'] = $ucode;
        $parr['pageindex'] = I('pageindex');
        $parr['pagesize'] = 10;
        $parr['acode'] = I('acode');
        $parr['pcode'] = I('pcode');

        $result = IGD('Productinfo','Store')->GetProductAllScore($parr);
        $this->ajaxReturn($result);
    }
}