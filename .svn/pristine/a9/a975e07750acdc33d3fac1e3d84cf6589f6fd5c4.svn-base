<?php
namespace Home\Controller;
use Think\Controller;
/**
*   数据库管理
*/
class DatabaseController extends Controller {

    // 数据库列表
    public function index()
    {
        $dbName = C('DB_NAME');
        $data = M()->query('SHOW TABLE STATUS FROM '.$dbName);
        $this->count = count($data);
        $this->assign("data",$data);
        $this->display('show');
    }

    // 备份文件列表
    public function filelist()
    {
        $list = $this->MyScandir('data/sqllog/');
        array_shift($list);
        array_shift($list);
        foreach ($list as $key => $value) {
            $data[$key]['filename'] = $value;
            $data[$key]['basetime'] = str_replace('.sql', '', $value);
            $data[$key]['basetime'] = $this->str_insert($data[$key]['basetime'], 4, '-');
            $data[$key]['basetime'] = $this->str_insert($data[$key]['basetime'], 7, '-');
            $data[$key]['basetime'] = $this->str_insert($data[$key]['basetime'], 10, ' ');
            $data[$key]['basetime'] = $this->str_insert($data[$key]['basetime'], 13, ':');
            $data[$key]['basetime'] = $this->str_insert($data[$key]['basetime'], 16, ':');
        }
        $this->data = $data;
        $this->display();
    }

    // 下载文件
    public function downfile()
    {
        ob_start();
        $filename = $_GET['surl'];
        $date = date("Ymd-H:i:m");
        header( "Content-type:  application/octet-stream ");
        header( "Accept-Ranges:  bytes ");
        header( "Content-Disposition:  attachment;  filename= {$date}.sql");
        $size = readfile($filename);
        header( "Accept-Length: " .$size);
    }


    // 删除文件
    public function del(){
        if($_GET['name']!=""){
            if(unlink($_GET['name'])){
                $this->ajaxReturn(Message('0','删除成功'));
            } else{
                $this->ajaxReturn(Message('1000','删除失败'));
            }
        }else{
            $this->ajaxReturn(Message('1000','数据为空'));
        }

    }

    //插入一段字符串
    public function str_insert($str, $i, $substr)
    {
        for($j=0; $j<$i; $j++){
            $startstr .= $str[$j];
        }
        for ($j=$i; $j<strlen($str); $j++){
            $laststr .= $str[$j];
        }
        $str = ($startstr . $substr . $laststr);
        return $str;
    }

    // 数据库备份
    public function back()
    {
        if(empty($_POST['tablearr'])) {
            $table = $this->getTable();
        } else {
            $table = explode(",",$_POST['tablearr']);
        }

        $struct = $this->bakStruct($table);
        $record = $this->bakRecord($table);
        $sqls = $struct.$record;
        $dir = "./data/sqllog/".date("YmdHis").".sql";
        checkDir($dir);
        file_put_contents($dir,$sqls);

        if(file_exists($dir)) {
            $this->success("备份成功");
        } else {
            $this->error("备份失败");
        }
    }

    // 获取数据表
    protected function getTable()
    {
        $dbName = C('DB_NAME');
        $result = M()->query('show tables from '.$dbName);
        foreach ($result as $v){
            $tbArray[]=$v['tables_in_'.C('DB_NAME')];
        }
        return $tbArray;
    }

    // 获取数据表结构
    protected function bakStruct($array)
    {
        foreach ($array as $v){
            $tbName = $v;
            $result = M()->query('show columns from '.$tbName);
            $sql .= "--\r\n";
            $sql .= "-- 数据表结构: `$tbName`\r\n";
            $sql .= "--\r\n\r\n";
            $sql .= "DROP TABLE IF EXISTS `$tbName`;\r\n";
            $sql .= "create table `$tbName` (\r\n";
            $rsCount = count($result);
            foreach ($result as $k=>$v){
                $field  =       $v['field'];
                $type   =       $v['type'];
                $default=       $v['default'];
                $extra  =       $v['extra'];
                $null   =       $v['null'];
                if (!($default=='')) {
                    $default='default '.$default;
                }
                if ($null=='NO') {
                    $null='not null';
                } else {
                    $null="null";
                }
                if($v['Key']=='PRI') {
                    $key    =       'primary key';
                }else{
                    $key    =       '';
                }
                if ($k<($rsCount-1)) {
                    $sql.="`$field` $type $null $default $key $extra ,\r\n";
                } else {
                    //最后一条不需要","号
                    $sql.="`$field` $type $null $default $key $extra \r\n";
                }
            }
            $sql.=") ENGINE=MyISAM DEFAULT CHARSET=utf8;\r\n\r\n";
        }
        return str_replace(',)',')',$sql);
    }

    // 获取数据表数据记录
    protected function bakRecord($array)
    {
        foreach ($array as $v) {
            $tbName = $v;
            $rs = M()->query('select * from '.$tbName);
            if (count($rs)<=0) {
                continue;
            }

            $sql .= "--\r\n";
            $sql .= "-- 数据表中的数据: `$tbName`\r\n";
            $sql .= "--\r\n\r\n";

            foreach ($rs as $k=>$v) {
                $sql .= "INSERT INTO `$tbName` VALUES (";
                foreach ($v as $key=>$value){
                    if ($value=='') {
                        $value='null';
                    }
                    $type = gettype($value);
                    if ($type == 'string') {
                        $value = "'".addslashes($value)."'";
                    }
                    $sql .= "$value," ;
                }
                $sql .= ");\r\n\r\n";
            }
        }
        return str_replace(',)',')',$sql);
    }

    // 数据库操作（优化，修复，查看）
    public function click()
    {
        $url = explode("&",$_GET['zhi']);
        $do = $url[0];
        $table = $url[1];
        switch ($do) {
            case optimize://优化
                $rs = M()->Query("OPTIMIZE TABLE `$table` ");
                if ($rs) {
                    $result = Message(0,"执行优化表： $table  OK！");
                } else {
                    $result = Message(0,"执行优化表： $table  失败，原因是：".M()->GetError());
                }
                break;
            case repair://修复
                $rs = M()->Query("REPAIR TABLE `$table` ");
                if($rs) {
                    $result = Message(0,"修复表： $table  OK！");
                } else {
                    $result = Message(0,"修复表： $table  失败，原因是：".M()->GetError());
                }
                break;
            default://结构
                $dsql = M()->Query("SHOW CREATE TABLE ".$table);
                foreach($dsql as $k=>$v) {
                    foreach($v as $k1=>$v1) {
                        $rs=$v1;
                    }
                }
                $result = MessageInfo(0,"查询成功",trim($rs));
        }
        $this->ajaxReturn($result);
    }

    // 读取文件夹下文件列表
    private function MyScandir($FilePath = './', $Order = 0) {
        $FilePath = opendir($FilePath);
        while (false !== ($filename = readdir($FilePath))) {
            $FileAndFolderAyy[] = $filename;
        }
        $Order == 0 ? sort($FileAndFolderAyy) : rsort($FileAndFolderAyy);
        return $FileAndFolderAyy;
    }

}
?>