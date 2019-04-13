<?php
namespace Ht\Controller;
use Think\Controller;

class IndexController extends PublicController{
	//***********************************
	// iframe式显示菜单和index页
	//**********************************
	public function index(){

       $menu=M("admin_menu");
		$list=$menu->where("type!='action' AND is_show=1 AND pid=0")->select();
		foreach ($list as $k => $v) {
			$sub=$menu->where("type!=action AND is_show=1 AND pid=".$v['id'])->select();
			$list[$k]['sub']= $sub ? $sub :"";
		}
	   $this->assign("list",$list);
       //版权
       $copy=M('web')->where('id=5')->getField('concent');
       $this->assign('copy',$copy);
       $this->assign('index',$index);
	   $this->display();
	}
	/**
	 * [welcome 首页]
	 * @return [type] [description]
	 */
	public function welcome(){

		$ip=get_client_ip();
		$todaytime=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));

		$user=M('user');

		$usernum=$user->where("del=0")->count();
	
		$today_usernum=$user->where("del=0 AND addtime>=".$todaytime)->count();

		$thismonth_usernum=$user->where("del=0 AND addtime>=".$beginThismonth)->count();

		$this->assign("usernum",$usernum);

		$this->assign("today_usernum",$today_usernum);

		$this->assign("thismonth_usernum",$thismonth_usernum);
		$this->assign("ip",$ip);
		$this->display();
	}	
}