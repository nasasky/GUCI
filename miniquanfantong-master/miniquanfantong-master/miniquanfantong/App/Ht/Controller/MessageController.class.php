<?php

namespace Ht\Controller;
use Think\Controller;
class MessageController extends PublicController{
	public function send(){
		if(IS_POST){
			$post=I("post.");
			$post['admin_id']=$_SESSION['admininfo']['id'];
			$post['type']=1;
			$post['send_time']=time();
			$re=M("message")->add($post);
			if($re){
				$this->success("发送成功!");
			}else{
				$this->error("发送失败!");
			}
		}else{
			$this->display();
		}
	}
	
	public function index(){
		$user=M("user");
		$list=M("message")->order('message_id desc')->select();
		foreach ($list as $k => $v) {
			$list[$k]['name']=$user->where("id=".$v['admin_id'])->getField("name");
		}
		$this->assign("list",$list);
		$this->display();
	}

	public function msg_del(){
		if(IS_AJAX){
			$id=I("post.id");
			$re=M("message")->where("message_id=$id")->delete();
			if($re){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}
	}

}