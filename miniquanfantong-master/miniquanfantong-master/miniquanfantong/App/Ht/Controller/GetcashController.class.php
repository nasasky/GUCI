<?php
namespace Ht\Controller;
use Think\Controller;
class GetcashController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->Getcash = M('getcashLog');
	}


	public function nofinish(){
		//搜索，根据广告标题搜索
		$list=$this->Getcash->where("del=0 AND status=0")->select();
		foreach ($list as $k => $v) {
			$username=M("user")->where("del=0 AND id=".$v['user_id'])->getField("name");
			$list[$k]['name']=$username?$username:M("user")->where("del=0 AND id=".$v['user_id'])->getField("uname");
		}
		$this->assign('list',$list);
		$this->display(); // 输出模板

	}
	public function finish(){
		//搜索，根据广告标题搜索
		$list=$this->Getcash->where("del=0 AND status=1")->select();
		foreach ($list as $k => $v) {
			$username=M("user")->where("del=0 AND id=".$v['user_id'])->getField("name");
			$list[$k]['name']=$username?$username:M("user")->where("del=0 AND id=".$v['user_id'])->getField("uname");
		}
		$this->assign('list',$list);
		$this->display(); // 输出模板

	}
	public function getcash_shenhe(){
		$id=I("post.id");
		if($id){
			$data['status']=1;
			$data['confirm']=time();
			$re=$this->Getcash->where("id=$id")->save($data);
			if($re){
				$this->_getcash_msg($id,"审核通过,请注意查收!");
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(0);
		}
	}
	public function getcash_finish(){
		$id=I("post.id");
		if($id){
			$data['finish']=1;
			$data['finishtime']=time();
			$re=$this->Getcash->where("id=$id")->save($data);
			if($re){
				$this->_getcash_msg($id,"已确认收款!");
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(0);
		}
	}
	public function getcash_cancel(){
		$id=I("post.id");
		if($id){
			$data['finish']=0;
			$data['finishtime']="";
			$data['status']=0;
			$data['confirm']="";
			$re=$this->Getcash->where("id=$id")->save($data);
			if($re){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(0);
		}
	}
	public function getcash_return(){
		$id=I("post.id");
		if($id){
			$log=$this->Getcash->where("id=$id")->find();
			$distribut_money=$log['money'];
			$userid=$log['user_id'];
			$re=$this->Getcash->where("id=$id")->setField("del",1);
			if($re){
				M("user")->where("id=$userid")->setInc("distribut_money",$distribut_money);
				$this->_getcash_msg($id,"本次提现已被注销!请重新发起!");		
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}else{
			$this->ajaxReturn(0);
		}
	}


}