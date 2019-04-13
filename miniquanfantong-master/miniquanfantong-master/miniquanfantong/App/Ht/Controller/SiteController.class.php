<?php
namespace Ht\Controller;
use Think\Controller;
class SiteController extends PublicController{

	
	public function add(){	
		  $id=I("get.id");
		  if(IS_POST){
		  	$post=I("post.");
		  	// dump($post);exit;
		  	//上传产品小图
			if (!empty($_FILES["logo"]["tmp_name"])) {
					//文件上传
					$info = $this->upload_images($_FILES["logo"],array('jpg','png','jpeg'),"Site/".date(Ymd));
				    if(!is_array($info)) {// 上传错误提示错误信息
				        $this->error($info);
				        exit();
				    }else{// 上传成功 获取上传文件信息
					    $array['logo'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
					    $xt = M('site')->where('id='.intval($id))->field('logo')->find();
					    if ($id && $xt['logo']) {
					    	$img_url = "Data/".$xt['logo'];
							if(file_exists($img_url)) {
								@unlink($img_url);
							}
					    }
				    }
			}
			if (!empty($_FILES["myqrcode_img"]["tmp_name"])) {
					//文件上传
					$info2 = $this->upload_images($_FILES["myqrcode_img"],array('jpg','png','jpeg'),"Site/".date(Ymd));
				    if(!is_array($info2)) {// 上传错误提示错误信息
				        $this->error($info2);
				        exit();
				    }else{// 上传成功 获取上传文件信息
					    $array['myqrcode_img'] = 'UploadFiles/'.$info['savepath'].$info['savename'];
					    $xt2 = M('site')->where('id='.intval($id))->field('myqrcode_img')->find();
					    if ($id && $xt2['myqrcode_img']) {
					    	$img_url = "Data/".$xt2['myqrcode_img'];
							if(file_exists($img_url)) {
								@unlink($img_url);
							}
					    }
				    }
			}

			$array['name']=$post['name'];
			$array['digest']=$post['digest'];
			$array['score']=$post['score'];
			$array['myqrcode_url']=$post['myqrcode_url'];
			$array['is_show']=$post['is_show']?$post['is_show']:0;
			$array['content']=$post['content'];
			//dump($array);exit;
			if($id>0){
				$re=M("site")->where("id=$id")->save($array);
			}else{
				$array['addtime']=time();
				$re=M("site")->add($array);
			}
			if($re){
				$this->success("添加成功!");
			}else{
				$this->error("添加失败!");
			}

		  }else{
		  	$this->assign("id",$id);
		  	$info = $id>0 ? M("site")->where("id=$id")->find() : "";
		  	$this->assign('info',$info);
			$this->display();
		  }
	}
	public function index(){
		$list=M("site")->where("del=0")->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function show(){
		if(IS_AJAX){
			$id=I("post.id");
			$act=I("post.act");
			if(!$id){
				$this->ajaxReturn("参数有误!");
			}
			$re=M("site")->where("id=$id")->setField("is_show",$act);
			if($re){
				$returnid= $act==0? 1 :0 ;
				$this->ajaxReturn($returnid);
			}else{
				$this->ajaxReturn("修改失败!");
			}
		}
	}
	public function tj(){
		if(IS_AJAX){
			$id=I("post.id");
			$act=I("post.act");
			if(!$id){
				$this->ajaxReturn("参数有误!");
			}
			$re=M("site")->where("id=$id")->setField("is_tj",$act);
			if($re){
				$returnid= $act==0? 1 :0 ;
				$this->ajaxReturn($returnid);
			}else{
				$this->ajaxReturn("修改失败!");
			}
		}
	}
	public function del(){
		if(IS_AJAX){
			$id=I("post.id");
			$re=M("site")->where("id=$id")->delete();
			if($re){
				$this->ajaxReturn(1);
			}else{
				$this->ajaxReturn(0);
			}
		}
	}
	

}