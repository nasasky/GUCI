<?php
namespace Ht\Controller;
use Think\Controller;
class AdminuserController extends PublicController{
	//*************************
	// 管理员的管理
	//*************************
	public function adminuser(){
		$id=(int)$_GET['id'];
		$name = trim($_REQUEST['name']);

		$names=$this->htmlentities_u8($_GET['name']);
		//$qx=$_GET['qx']=='' ? '' : $_GET['qx'];
		$where= "1=1 AND del<1";
		if($name){
			$where.=" AND name like '%$name%'";
		}

		define('rows',20);//定义每页显示数量
		$count=M('adminuser')->where($where)->count();
		$rows=ceil($count/rows);

		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$userlist=M('adminuser')->where($where)->order('id desc')->limit($limit,rows)->select();
		$page_index=$this->page_index($count,$rows,$page);//分页
		foreach ($userlist as $k => $v) {
			$userlist[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
		}

		//=============
		//将变量输出
		//=============
		$this->assign('name',$name);
		$this->assign('page_index',$page_index);
		$this->assign('page',$page);
		$this->assign('userlist',$userlist);
		$this->display();	
	}

	//*************************
	// 管理员&商家会员的添加
	//*************************
	public function add(){
		//==================
		// GET到的参数集合
		//==================
		$id=(int)$_GET['id'];
		if($_POST['submit']==true){
			if (!$_POST['name']) {
				$this->error('请输入登录账号.'.__LINE__);
				exit();
			}

		    $array = array(
		        'name' => trim($_POST['name']),
				'uname' => '超级管理员',
				'qx' => 4,
				'pwd' => MD5(MD5($_POST['password'])) ,
		    );
			if(intval($_POST['admin_id'])>0){
				//更新
			    //密码为空则去掉unset，防止空置原密码
				if(!$_POST['password']) {unset($array['pwd']);}
				$sql= M('adminuser')->where("id=".intval($_POST['admin_id']))->save($array);
			}else{
				//添加
				$check = M('adminuser')->where('name="'.$array['name'].'" AND del=0 AND (qx=5 or qx=4)')->getField('id');
				if ($check) {
					$this->error('账号已存在！');
					exit();
				}
				$array['addtime'] = time();
				$sql= M('adminuser')->add($array);
				$id= $sql;
			}
			
			if($sql){  
				$this->success('保存成功！');
				exit();
			}else{
				$this->success('保存失败！');
				exit();
			}
		}
		//id>0则为编辑状态
		$adminuserinfo = $id>0 ? M('adminuser')->where("id=$id")->find():""; 
		//=============
		//将变量输出
		//=============
		$this->assign('id',$id);
		$this->assign('adminuserinfo',$adminuserinfo);
		$this->display();
	}

	public function del()
	{
		$id = intval($_REQUEST['did']);
		$info = M('adminuser')->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('参数错误.'.__LINE__);
			exit();
		}

		if (intval($info['qx'])==4) {
			$this->error('该账号不能删除.'.__LINE__);
			exit();
		}

		if ($info['del']==1) {
			$this->redirect('Adminuser/adminuser',array('page'=>intval($_REQUEST['page'])));
			exit();
		}

		$data=array();
		$data['del'] = 1;
		$up = M('adminuser')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('Adminuser/adminuser',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}
	public function admin_add(){
		$this->display();
	}
	public function admin_list(){
		$user=M("user");
		$list=$user->where("qx=4 AND del=0")->select();
		$this->assign("list",$list);
		$this->display();
	}
	/**
	 * [admin_dl 代理商管理]
	 * @return [type] [description]
	 */
	public function admin_dl(){
		$user=M("user");
		$list=$user->where("qx=4 AND del=0")->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function admin_list_disable(){
		if(IS_AJAX){
			$id=I("post.id");
			$act=I("post.act");
			if(!$id){
				$this->ajaxReturn("参数有误!");
			}
			$re=M("user")->where("id=$id")->setField("disable",$act);
			if($re){
				$returnid= $act==0? 1 :0 ;
				$this->ajaxReturn($returnid);
			}else{
				$this->ajaxReturn("修改失败!");
			}
		}
	}
	public function admin_permission(){
		$admin_menu=M("admin_menu");
		$list=$admin_menu->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function admin_role(){
		$admin_role=M("admin_role");
		$list=$admin_role->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function admin_role_del(){
		if(IS_AJAX){
			$id=I("post.id");
			if($id){
				$re=M("admin_role")->where("id=$id")->delete();
				if($re){
					$this->ajaxReturn(1);
				}else{
					$this->ajaxReturn(0);
				}
			}
		}
	}
	public function admin_role_add(){
		if(IS_POST){
			$post=I("post.");
			if(!$post['role_action']){
				$this->error("请选择权限!");
			}
			$data['role_action']=implode(",", $post['role_action']);
			$data['role_name']=$post['role_name'];
			$data['digest']=$post['digest'];
			if($post['id']){
				$re=M("admin_role")->where("id=".$post['id'])->save($data);
			}else{
				$re=M("admin_role")->add($post);
			}
			if($re>0){
				$this->success("提交成功!");
			}else{
				$this->success("提交失败!");
			}
		}else{
			$id=I("get.id");
			if($id){
				$info=M("admin_role")->where("id=".$id)->find();
				// $info['role_action']=explode(",", $info['role_action']);
				// var_dump($info);
				$this->assign("id",$id);
				$this->assign("info",$info);
			}
			$admin_menu=M("admin_menu");
			$qxlist=$admin_menu->where("is_show=1 AND pid=0")->select();
			foreach ($qxlist as $k => $v) {
				$sub=$admin_menu->where("is_show=1 AND pid=".$v['id'])->select();
				$qxlist[$k]['sub']= $sub ? $sub : "";
			}
			$this->assign("qxlist",$qxlist);
			$this->display();
		}
	}	
	
}