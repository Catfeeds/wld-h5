<?php
class Config{
    public $cfg = array(
        //�ӿ������ַ���̶����䣬�����޸�
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        //�����̻��ţ��̻����Ϊ�Լ���
        'mchId'=>'102553849209',
        //������Կ���̻����Ϊ�Լ���
        'key'=>'25ec3c792b754d1ae671a506fa727a5d',
        //�汾��Ĭ��Ϊ2.0
        'version'=>'2.0'
    );

    //�̻��ż���
    public $mchIdarr = array(
        '101520202796', //��ҵ���� 
        // '102513831005', //΢�������ؿƼ�С�� 
        // '102513831004',  //΢��ػ����Ƽ�С��
        // '102523831003',  //΢������ǿƼ�С��
        // '102533831007',  //΢���˫���Ƽ�С��
        // '102583831006',   //΢����������Ƽ�С��
    );

    //�̻��Ŷ�Ӧ֧����Կ
    public $mchIdgetkey = array(
        '101520202796'=>'70a26ac49e13187269811b67447e5a2c', //��ҵ���� 
        // '102513831005'=>'0e6ce4f70921f57d052800b46f50f1aa', //΢�������ؿƼ�С�� 
        // '102513831004'=>'28a45bd5a80b3d954cad96fa5d63846e',  //΢��ػ����Ƽ�С��
        // '102523831003'=>'aebf653094ab0f03edc09f0e396e296c',  //΢������ǿƼ�С��
        // '102533831007'=>'6cd2552893dd6b8b34663342945543e4',  //΢���˫���Ƽ�С��
        // '102583831006'=>'a5718ed7749a5693c9b974d934d6f56e',   //΢����������Ƽ�С��
    );

    //���췽��
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