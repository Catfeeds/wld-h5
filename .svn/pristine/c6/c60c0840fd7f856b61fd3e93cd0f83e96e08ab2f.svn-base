<?php

/*
 * 用户地图坐标
 */
class CoordinateLbs {

    /**
     *  坐标转换是一类简单的HTTP接口，能够将用户输入的非高德坐标（GPS坐标、mapbar坐标、baidu坐标）转换成高德坐标。
     *  @param locations 坐标点(经度和纬度用","分割，经度在前，纬度在后。多个坐标对之间用”|”进行分隔最多支持40对坐标)
     *         coordsys 原坐标系(可选值：gps;mapbar;baidu;autonavi(不进行转换))
     *         output 返回数据格式类型(可选值：JSON,XML)
     *
     */
    public function Convert($locations,$coordsys = "baidu",$output = 'JSON') {
        $key = "469a08145d1634c2f1d6e46ea05e2314";

        $url = "http://restapi.amap.com/v3/assistant/coordinate/convert?key=" . $key . "&locations=" . $locations .  "&coordsys=" . $coordsys ."&output=" . $output;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $result = curl_exec($ch);

        if ($result == "") {
            return Message(1001, "查询失败");
        }

        $info = (Array) json_decode($result);

        $status= $info['status'];
        if($status != 1){
            $error = $this->GetErrorInfo($info['info']);
            return $error;
        }

        $datainfo['locations'] = $info['locations'];

        return MessageInfo(0, "查询成功", $datainfo);
    }

    function GetErrorInfo($infocode){
        switch ($infocode) {
            case 10001:
                $error = Message($infocode,"key不正确或过期");
                break;
            case 10002:
                $error = Message($infocode,"没有权限使用相应的服务或者请求接口的路径拼写错误");
                break;
            case 10003:
                $error = Message($infocode,"访问已超出日访问量");
                break;
            case 10004:
                $error = Message($infocode,"单位时间内访问过于频繁");
                break;
            case 10005:
                $error = Message($infocode,"IP白名单出错，发送请求的服务器IP不在IP白名单内");
                break;
            case 10006:
                $error = Message($infocode,"绑定域名无效");
                break;
            case 10007:
                $error = Message($infocode,"数字签名未通过验证");
                break;
            case 10008:
                $error = Message($infocode,"MD5安全码未通过验证");
                break;
            case 10009:
                $error = Message($infocode,"请求key与绑定平台不符");
                break;
            case 10010:
                $error = Message($infocode,"IP访问超限");
                break;
            case 10011:
                $error = Message($infocode,"服务不支持https请求");
                break;
            case 10012:
                $error = Message($infocode,"权限不足，服务请求被拒绝");
                break;
            case 10013:
                $error = Message($infocode,"Key被删除");
                break;
            case 10014:
                $error = Message($infocode,"QPS超限");
                break;
            case 10015:
                $error = Message($infocode,"受单机QPS限流限制");
                break;
            case 10016:
                $error = Message($infocode,"服务器负载过高");
                break;
            case 10017:
                $error = Message($infocode,"所请求的资源不可用");
                break;
            case 20000:
                $error = Message($infocode,"请求参数非法");
                break;
            case 20001:
                $error = Message($infocode,"缺少必填参数");
                break;
            case 20002:
                $error = Message($infocode,"请求协议非法");
                break;
            case 20003:
                $error = Message($infocode,"其他未知错误");
                break;
            case 20800:
                $error = Message($infocode,"规划点（包括起点、终点、途经点）不在中国陆地范围内");
                break;
            case 20801:
                $error = Message($infocode,"划点（起点、终点、途经点）附近搜不到路");
                break;
            case 20802:
                $error = Message($infocode,"路线计算失败，通常是由于道路连通关系导致");
                break;
            case 20803:
                $error = Message($infocode,"起点终点距离过长");
                break;

            default:
                $error = Message($infocode,"服务响应失败");
                break;
        }

        return $error;
    }
}
?>

