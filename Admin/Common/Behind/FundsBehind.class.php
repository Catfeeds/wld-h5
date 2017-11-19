<?php
// 资金风控
namespace Common\Behind;

class FundsBehind {
 
    //每日利润导出Excel表格
    function profitIndexnt(){
        $w = array();
       

        $begintime = I('EntTime1');
        $endtime = I('EntTime2');   
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "c_datetime between '".$begintime."' and '".$endtime."'";
        }

        //数据数量
        $least=trim(I('least'));
        if(!empty($least)){
            $rise=$least;//起
        }
        $maximum=trim(I('maximum'));
        if(!empty($maximum)){
            $to=$maximum;//终至
        }
        $total=25;//总行
        $s=ceil($rise * $total) - $total;//当前页，第几条开始
        $scope=intval($to - $rise) + 1;
        $conud=ceil($scope * $total); //多少条

        $parent = I('param.');
        if(!empty($w)){
            $w1 = ' WHERE '.@implode('AND ',$w);
        }
        
        $data = M('System_profit')->where($w1)->select();


        $date[0][0]=array("平台总抽成","平台总利润","线上总抽成","线下总抽成","线上扫码抽成","线下扫码抽成",
                "线上订单抽成","线下订单抽成","线上商家提成","线下商家提成","线上代理提成","线下代理提成","线上经理提成","线下经理提成","线上红包抽成","线下红包抽成",'线上平台抽成',"线下平台抽成","日期");
        $k1=1;
        foreach($data as $k=>$v){
            $k1++;
            $date[$k1][0] = array(
                $v['c_total_rake'],
                $v['c_total_profit'],
                $v['c_online_rake'],
                $v['c_offline_rake'],
                $v['c_onsp_rake'],
                $v['c_offsp_rake'],
                $v['c_onod_rake'],
                $v['c_offod_rake'],
                $v['c_onshop_rake'],
                $v['c_offshop_rake'],
                $v['c_onagent_rake'],
                $v['c_offagent_rake'],
                $v['c_onarea_rake'],
                $v['c_offarea_rake'],
                $v['c_onred_rake'],
                $v['c_offred_rake'],
                $v['c_onsys_rake'],
                $v['c_offsys_rake'],
                '\''.$v['c_datetime'],

            );
        }
        //导入PHPExcel类库
        import("Common.Org.PHPExcel");
        import("Common.Org.PHPExcel.Writer.Excel5");
        import("Common.Org.PHPExcel.IOFactory.php");
        $filename="每日利润记录";
        $this->getExcel($filename,$date);
    }
    
    //调用phpExcel
    private function getExcel($fileName,$data){
        //对数据进行检验
        if(empty($data)||!is_array($data)){
            die("data must be a array");
        }
        $date=date("Y_m_d",time());
        $fileName.="_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        $objProps=$objPHPExcel->getProperties();
        $column=1;
        $objActSheet=$objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('k')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth('32');//设置列宽
       

    
        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
        foreach ($data as $ke=>$row){
            foreach($row as $key=>$rows){
                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:V{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:V{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setBold(true);//粗体
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
                }else{
                    if(!empty($rows)){
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:V{$column}");//设置边框
                    }
                }
                if($rows['0']=='平台总抽成'){
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setWrapText(true);//换行
                //行写入
                $span = ord("A");
    
                foreach($rows as $keyName=>$value){
                    // 列写入
                    $j=chr($span);
                    $value = !empty($value)?$value:' ';
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
                $column++;
                
            }
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }



     //每日账目导出Excel表格
    function accountsIndex(){

        $db = M('Users_moneydate as a ');
         
        //会员昵称       
        $c_username = trim(I('c_nickname'));
        if (!empty($c_username)) {
            $wus['c_nickname'] = $c_username;
        }

        $c_phone = trim(I('c_phone'));
        if (!empty($c_phone)) {
            $wus['c_phone'] = $c_phone;
        }

        $c_ucode = trim(I('ucode'));
        if (!empty($c_ucode)) {
            $w['a.c_ucode'] = $c_ucode;
            $this->ucode = $c_ucode;
        }
        if (count($wus) > 0) {
            $usinfo = M('Users')->where($wus)->field('c_ucode')->find();
            if ($usinfo) {
                $w['a.c_ucode'] = $usinfo['c_ucode'];
            }
        }
        
        //用户编码
        $ucode = trim(I('ucode'));
        if (!empty($ucode)) {
            $w['a.c_ucode'] = $ucode;
        }

        //金额类型
        $c_type = trim(I('c_type'));
        if (!empty($c_type)) {
            $w['a.c_type'] = $c_type;
        }

        //收支明细
        $c_sign = trim(I('c_sign'));
        if (!empty($c_sign)) {
            $w['a.c_sign'] = $c_sign;
        } else {
            $w['a.c_sign'] = 1;
        }

        //日期
        $begintime = I('EntTime1');
        $endtime = I('EntTime2');
        if(isset($begintime)&&!empty($begintime) && isset($endtime)&&!empty($endtime)){
            $w[] = "a.c_datetime between '".$begintime."' and '".$endtime."'";
        }

        //排序方式
        $order = trim(I('order'));
        if ($order == 2) {  //时间排序
            $order1 = 'a.c_id desc';
        } else {  //默认金额排序
            if ($c_sign == 2) {
                $order1 = 'a.c_datetime desc,a.c_money asc';
            } else {
                $order1 = 'a.c_datetime desc,a.c_money desc';
            }
        }

        //数据数量
        $least=trim(I('least'));
        if(!empty($least)){
            $rise=$least;//起
        }
        $maximum=trim(I('maximum'));
        if(!empty($maximum)){
            $to=$maximum;//终至
        }
        $total=25;//总行
        $s=ceil($rise * $total) - $total;//当前页，第几条开始
        $scope=intval($to - $rise) + 1;
        $conud=ceil($scope * $total); //多少条

        $parent = I('param.');
        if(!empty($w)){
            $w1 = ' WHERE '.@implode('AND ',$w);
        }
        

        $order =$order1;//排序
        $field = 'a.*';        
        $data = $db->join($join)->field($field)->where($w)->order($order)->limit($s,$conud)->select();       



        $date[0][0]=array("用户昵称","用户手机号","金额类型","每日金额","收支状态","日期",
                "更新时间");
        $k1=1;
        foreach($data as $k=>$v){
            $k1++;
            $uw['c_ucode'] = $v['c_ucode'];
            $userinfo = M('Users')->where($uw)->field('c_nickname,c_phone')->find();
            $v['c_nickname'] = $userinfo['c_nickname'];
            $v['c_phone'] = $userinfo['c_phone'];

            
            $date[$k1][0] = array(
                $v['c_nickname'],
                '\''.$v['c_phone'],
                $v['c_type'],
                $v['c_money'],
                $v['c_sign'],
                '\''.$v['c_datetime'],
                '\''.$v['c_updatetime'],


            );


        }
        //导入PHPExcel类库
        import("Common.Org.PHPExcel");
        import("Common.Org.PHPExcel.Writer.Excel5");
        import("Common.Org.PHPExcel.IOFactory.php");
        $filename="每日账目记录";
        $this->getExcel1($filename,$date);
    }
    
    //调用phpExcel
    private function getExcel1($fileName,$data){
        //对数据进行检验
        if(empty($data)||!is_array($data)){
            die("data must be a array");
        }
        $date=date("Y_m_d",time());
        $fileName.="_{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel=new \PHPExcel();
        $objProps=$objPHPExcel->getProperties();
        $column=1;
        $objActSheet=$objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setName('微软雅黑');//设置字体
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);//设置默认高度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth('22');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth('32');//设置列宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('32');//设置列宽
        

    
        //设置边框
        $sharedStyle1=new \PHPExcel_Style();
        $sharedStyle1->applyFromArray(array('borders'=>array('allborders'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN))));
        foreach ($data as $ke=>$row){
            foreach($row as $key=>$rows){
                if(count($row)==1&&empty($row[0][1])&&empty($rows[1])&&!empty($rows)){
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A{$column}:V{$column}");//设置边框
                    array_unshift($rows,$rows['0']);
                    $objPHPExcel->getActiveSheet()->mergeCells("A{$column}:V{$column}");//合并单元格
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setSize(12);//字体
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFont()->setBold(true);//粗体
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FFB8CCE4');
                }else{
                    if(!empty($rows)){
                        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1,"A{$column}:V{$column}");//设置边框
                    }
                }
                if($rows['0']=='用户昵称'){
                    //背景色填充
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
                    $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getFill()->getStartColor()->setARGB('FF4F81BD');
                }
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                $objPHPExcel->getActiveSheet()->getStyle("A{$column}:V{$column}")->getAlignment()->setWrapText(true);//换行
                //行写入
                $span = ord("A");
    
                foreach($rows as $keyName=>$value){
                    // 列写入
                    $j=chr($span);
                    $value = !empty($value)?$value:' ';
                    $objActSheet->setCellValue($j.$column, $value);
                    $span++;
                }
                $column++;
                
            }
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }

 

}
