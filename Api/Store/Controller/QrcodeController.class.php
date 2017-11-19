<?php

namespace Store\Controller;

use Base\Controller\CheckController;

/**
 * 	商家二维码
 */
class QrcodeController extends CheckController {
	//生成收款二维码
	public function createqrcode(){
                $key = I('openid');
                $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
                $parr['ucode'] = $ucode;

                $parr['qrcode_type'] = 1;
                
                $Qrcode = IGD('Qrcode', 'Store');
                $result = $Qrcode->CreateQrcode($parr);

                $this->ajaxReturn($result);
	}

	//生成收款图片
	public function puzzleqrcode(){
                $key = I('openid');
                $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
                $parr['ucode'] = $ucode;

                $parr['qrcode_type'] = 1;

                $Qrcode = IGD('Qrcode', 'Store');
                $result = $Qrcode->PuzzleQrcode($parr);

                $this->ajaxReturn($result);
	}

        //红包领取店铺二维码
        public function shopred_qrcode(){
                $key = I('openid');
                $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);

                $parr['ucode'] = $ucode;
                $parr['qrcode_type'] = 2;              
                $parr['bgcolor'] = "#f52f46";                

                $Qrcode = IGD('Qrcode', 'Store');
                $result = $Qrcode->CreateQrcode($parr);

                $this->ajaxReturn($result);
        }

        //生成商家店铺二维码
        public function shopqrcode(){
                $key = I('openid');
                $ucode = IGD('Common', 'Redis')->Rediesgetucode($key);
                $parr['ucode'] = $ucode;

                $parr['qrcode_type'] = 2;

                //$style_arr = array("#000000","#ffaa3c","#5ab9f9","#ff95ca","#5cadad","#f52f46");

                $style_arr = array("#000000","#cc0000","#5ab9f9","#6246fa","#5cadad","#f52f46");

                $random_num = I('random_num');

                $parr['bgcolor'] = $style_arr[$random_num];

                $bgcolor = I('bgcolor');
                if(!empty($bgcolor)){
                        $parr['bgcolor'] = $bgcolor;
                }
                
                $Qrcode = IGD('Qrcode', 'Store');
                $result = $Qrcode->StoreQrcode($parr);

                $this->ajaxReturn($result);
        }
}