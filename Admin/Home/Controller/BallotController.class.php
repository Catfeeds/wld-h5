<?php
namespace Home\Controller;
use Think\Controller;
/**
* 国庆抽签活动
*/
class BallotController extends BaseController {

	//根据c_activitytype 得到 活动c_id
	public function get_aid($activitytype){
		$w['c_activitytype'] = $activitytype;
		$aid = M('activity')->where($w)->getField('c_id');
		return $aid;
	}

	// 签文列表
	public function ballot_list()
	{
		$Ballot = M('Ballot');
		//条件
    	$qwname = trim(I('qwname'));
        if (!empty($qwname)) {
           $where['c_title'] = array('like', "%{$qwname}%");
        }

		$where['c_aid'] = $this->get_aid(10);
		$count = $Ballot->where($where)->count();
		$page = getpage($count,25);
		$limit = $page->firstRow.','.$page->listRows;
		$data = $Ballot->field($field)->where($where)->limit($limit)->order('c_id desc')->select();
		foreach ($data as $key => $value) {
			$data[$key]['c_activityname'] = M('Activity')->where('c_id='.$value['c_aid'])->getField('c_activityname');
		}
		$this->data = $data;
		$this->page = $page->show();
		$this->assign('count',$count);
		$this->post = I('param.');
		$this->root_url = GetHost()."/";
		$this->display();
	}

	//生成签文图片
	public function create_img($id,$desc){
		$num = $id;
		$imgname = SYS_PATH .'Uploads'. DS .'ballotbg.png'; //模板图片路径
        $image = new \Think\Image();
        $imagepath = SYS_PATH .'Uploads'. DS .'ballot'. DS . $num .'.jpg'; //图片复制的本地路径
       	$path = 'Uploads/ballot/'.$num.'.jpg';     //用户名片路径
       	unlink($path);
        //创建目录
        if(checkDir($imagepath)) {
        	$name = $desc;
        	$name = str_replace('|', '', $name);
        	$namearr = arr_split_zh($name);
        	//循环插入文字
        	for ($i = 0; $i < count($namearr); $i++) {
        		$str  = $namearr[$i];
        		if (!is_file($imagepath)) {
        			$result = $image->open($imgname)->save($imagepath);
        		} else {
        			$result = $image->open($imagepath)->save($imagepath);
        		}
		        $x = 15 + 68*(intval($i/7));
		        $y = 15 + 68*($i%7);
		        $locate = array($x,$y);              //文字位置
		        $data = $image->open($imagepath)->text($str,'./Uploads/jiantiyanti.TTF',34,'#000000',$locate)->save($path);
        	}
        }

        $result = qiniu_syn_files($path,$path);
	    if(!$result){
	    	$this->error("远程上传图片失败");
	    }

        $w['c_id'] = $id;
        $save['c_bimg'] = $path;
        $result = M('Ballot')->where($w)->save($save);
        if($result < 0){
        	return false;
        }
        return ture;
	}

	//验证数据
	function pcheckfrom($data){
		if (empty($data['name'])) {
	    	$this->error("签文名称不能为空");
		}

		if (empty($data['title'])) {
	    	$this->error("签文标题不能为空");
		}

		if (empty($data['desc'])) {
	    	$this->error("签文描述不能为空");
		}

		if (empty($data['content'])) {
		    $this->error("签文内容不能为空");
		}
	}

	//签文添加
	public function ballot_add(){
		$this->action = 'ballot_add';
		if(IS_POST){
			$db = M('Ballot');

		   	$this->pcheckfrom($_POST);

		    $data['c_aid'] = $this->get_aid(10);
		    $data['c_name'] = $_POST['name'];
		    $data['c_title'] = $_POST['title'];
		    $data['c_desc'] = $_POST['desc'];
		    $data['c_content'] = $_POST['content'];
		    $data['c_addtime'] = Date('Y-m-d H:i:s');

		    $result = $db->add($data);

		    if(!$result){
		    	$this->error('添加失败！');
		    }

		    $id = $result;

		    $result = $this->create_img($id,$_POST['desc']);
		   	if(!$result){
		    	$this->error('生成图片失败！');
		    }

			$back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Ballot/ballot_list';
			echo '<script language="javascript">alert("添加成功");</script>';
			echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
		}
		$this->display();
	}

	//签文编辑
	public function ballot_edit(){
		$this->action = 'ballot_edit';
		$Id = I('Id');
		$w['c_id'] = $Id;
		$arr = M('Ballot')->where($w)->find();
		$this->vo = $arr;
		if(IS_POST){
			$db = M('Ballot');
		    $this->pcheckfrom($_POST);

		    $data['c_name'] = $_POST['name'];
		    $data['c_title'] = $_POST['title'];
		    $data['c_desc'] = $_POST['desc'];
		    $data['c_content'] = $_POST['content'];

		    $w1['c_id'] = $_POST['Id'];
		    $result = $db->where($w1)->save($data);

		    if(!$result){
		    	$this->error('编辑失败！');
		    }

		    $id = $_POST['Id'];

		    $result = $this->create_img($id,$_POST['desc']);
		   	if(!$result){
		    	$this->error('生成图片失败！');
		    }

		    $back_url = C('TMPL_PARSE_STRING.__HHOME__').'/Ballot/ballot_list';
	        echo '<script language="javascript">alert("编辑成功");</script>';
	        echo '<script language="javascript">window.parent.location.href="'.$back_url.'";</script>';die();
		}
		$this->display('ballot_add');
	}

	//签文删除
    public function ballot_delete()
    {
        $Id = I('Id');
        $idstr = str_replace('|', ',', $Id);
        $where['c_id'] = array('in',$idstr);
        $result = M('Ballot')->where($where)->delete();
        if($result){
            $this->ajaxReturn(Message(0,'删除成功'));
        }else{
            $this->ajaxReturn(Message(1000,'删除失败'));
        }
    }

    //抽签记录
    public function ballot_log(){
    	$db = M('Ballot_log as b');
		//条件
		$bid = trim(I('bid'));
        if (!empty($bid)) {
           $w['b.c_bid'] = $bid;
        }

        $nickname = trim(I('nickname'));
        if (!empty($nickname)) {
           $w['b.c_nickname'] = array('like', "%{$nickname}%");
        }

		$panrn['where'] = $w;
		$parent = I('param.');
		$panrn['order'] = 'b.c_id desc';//排序
		$panrn['limit'] = 25;//分页数

        //分页显示数据
        $panrn['field'] = 'b.*,a.c_title';
        $panrn['join'] = 'left join t_ballot as a on a.c_id=b.c_bid';
        $list=D('Db','Behind');
		$date=$list->mate_select_pages($db,$panrn);
		$this->list = $date['list'];
		$this->count = $date['count'];//分页\
        $this->page = $date['Page'];//分页
		$this->post = $parent;
		$this->display();
    }

}
?>