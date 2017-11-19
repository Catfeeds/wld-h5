<?php

use Com\RedisModel;

/**
 * Redis缓存操作
 */
class RedisRedis {
	//实例名
    public $Redis = null;

  	 //初始化
    public function __construct()
    {
        if (!isset($this->Redis)) {
            $this->Redis = new RedisModel();
        }
        $result = $this->ping();
        if(!$result){
        	return Message(2009,'Redis链接失败');
        }
    }

     /**
     * 查看redis连接是否断开
     * @return $return bool true:连接未断开 false:连接已断开
     */
    public function ping()
    {
        $return = $this->Redis->ping();
        return $return;
    }

    /**
     * 返回$this->Redis对象
     */
    public function Back(){
    	 return $this->Redis;
    }

    /**
     * 写入key-value
     * @param $key string 要存储的key名
     * @param $value mixed 要存储的值
     * @param $time float 过期时间(S)
     */
    public function SetKey($key,$value,$time)
    {
    	if(empty($key)){
    		return Message(2001,'key值不能为空');
    	}

    	if(empty($time)){
    		$time = 0;
    	}

    	$result = $this->Redis->set($key,$value,0,0,$time,0);
    	if(!$result){
    		return Message(2002,'key值添加失败');
    	}

        return Message(0,'key值添加成功');
    }

     /**
     * 获取某个key值
     * @param $key string/array 要获取的key或者key数组
     */
    public function GetKey($key)
    {
        if(empty($key)){
    		return Message(2001,'key值不能为空');
    	}

    	$isexist = $this->Redis->exists($key);

    	if(!$isexist){
    		return Message(2002,'所取key值不存在');
    	}

    	$data = $this->Redis->get($key);

        if($data == false){
        	return Message(2003,'获取key值失败');
        }

        return MessageInfo(0,'获取key值成功',$data);
    }

     /**
     * 删除某个key值
     * @param $key string
     */
    public function DeleteKey($key)
    {
        if(empty($key)){
    		return Message(2001,'key值不能为空');
    	}

    	$isexist = $this->Redis->exists($key);

    	if(!$isexist){
    		return Message(2002,'所删除的key值不存在');
    	}

  		$key_arr = array($key);
        $result = $this->Redis->delete($key_arr);

  		if($result != 1){
  			return Message(1003,'删除失败');
  		}
        return Message(0,'删除成功');
    }

     /**
     * 将key->value写入hash表中
     * @param $hash string 哈希表名
     * @param $data array 要写入的数据 array('key'=>'value')
     * @param $time 有效时间（s）
     */
    public function Sethash($hash,$data,$time)
    {
    	if(empty($hash)){
    		return Message(2001,'hash表名不能为空');
    	}

    	if(empty($time)){
    		$time = 0;
    	}

    	$result = $this->Redis->hashSet($hash,$data,$time);

    	if(!$result){
    		return Message(2002,'保存失败');
    	}

        return Message(0,'保存成功');
    }

    /**
     * 获取hash表的数据
     * @param $hash string 哈希表名
     * @param $key mixed 表中要存储的key名 默认为null 返回所有key>value
     */
    public function Gethash($hash)
    {
    	if(empty($hash)){
    		return Message(2001,'hash表名不能为空');
    	}

    	$date = $this->Redis->hashGet($hash,null,2);

       	if(empty($date)){
       		return Message(2002,'查询失败');
       	}

        return MessageInfo(0,'查询成功',$date);
    }

    //添加activityclick值
    public function ActivityClick($key){
    	$isexist = $this->Redis->exists($key);
    	if(!$isexist){
    		$result = $this->Redis->set($key,0,0,0,0,0);
    		if(!$result){
    			return Message(2001,'初始化activityclick失败');
    		}
    	}

    	$date = $this->Redis->get($key);

        if($date == false){
        	return Message(2002,'获取activityclick值失败');
        }

        return MessageInfo(0,'查询成功',$date);
    }

}