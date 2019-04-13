<?php
namespace Ht\Controller;
use Think\Controller;
class SuggestController extends PublicController{
	//**********************************************
	//说明：列表信息
	//**********************************************
	public function index(){
		$suggest=M('fankui')->select();
		foreach($suggest as $k => $v){
			$suggest[$k]['username'] = M('user')->where('id="'.intval($v['uid']).'"')->getField('uname');
		}
		
		//=============
		// 将变量输出
		//=============	
		$this->assign('suggest',$suggest);
		$this->display();
	}
	
	//删除意见
	public function del(){
		$id = intval($_GET['id']);
		$res = M('fankui')->where('id="'.$id.'"')->delete();
		if($res){
			$this->success('删除成功！');
			exit();
		}else{
			$this->error('删除失败！','index');
		}
	}
}
