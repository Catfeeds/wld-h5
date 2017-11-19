<?php
class Config{
    public $cfg = array(
        //接口请求地址，固定不变，无需修改
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        //测试商户号，商户需改为自己的
        'mchId'=>'102553849209',
        //测试密钥，商户需改为自己的
        'key'=>'25ec3c792b754d1ae671a506fa727a5d',
        //版本号默认为2.0
        'version'=>'2.0'
    );

    //商户号集合
    public $mchIdarr = array(
        '102513831005', //微领地新领地科技小店 
        '102513831004',  //微领地华航科技小店
        '102523831003',  //微领地松乔科技小店
        '102533831007',  //微领地双永科技小店
        '102583831006',   //微领地三月三科技小店
    );

    //商户号对应支付秘钥
    public $mchIdgetkey = array(
        '102513831005'=>'0e6ce4f70921f57d052800b46f50f1aa', //微领地新领地科技小店 
        '102513831004'=>'28a45bd5a80b3d954cad96fa5d63846e',  //微领地华航科技小店
        '102523831003'=>'aebf653094ab0f03edc09f0e396e296c',  //微领地松乔科技小店
        '102533831007'=>'6cd2552893dd6b8b34663342945543e4',  //微领地双永科技小店
        '102583831006'=>'a5718ed7749a5693c9b974d934d6f56e',   //微领地三月三科技小店
    );

    //构造方法
    public function __construct()
    {
        $randshu = rand(0,(count($this->mchIdarr)-1));
        $this->cfg['mchId'] = $this->mchIdarr[$randshu];
        $this->cfg['key'] = $this->mchIdgetkey[$this->mchIdarr[$randshu]];
    }
    
    public function C($cfgName,$mchId){
        if (!empty($mchId)) {
            $this->cfg['mchId'] = $mchId;
            $this->cfg['key'] = $this->mchIdgetkey[$mchId];
        }
        return $this->cfg[$cfgName];
    }
}
?>