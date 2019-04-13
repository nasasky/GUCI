<?php
namespace Ht\Controller;
use Think\Controller;
class MoreController extends PublicController{
	//*************************
	//单页设置
	//*************************
	public function pweb_gl(){
		//获取web表的数据进行输出
		$model=M('web');
		$list=$model->select();
		$this->assign('list',$list);	
		$this->display();
	}

	//*************************
	//单页设置修改
	//*************************
	public function pweb(){
		if(IS_POST){
			if(intval($_POST['id'])){
				$data = array();
				// $data['uname']=$_POST['uname'];
				$data['concent'] = $_POST['concent'];
				$data['sort'] = intval($_POST['sort']);
				$data['addtime'] = time();
				$up = M('web')->where('id='.intval($_POST['id']))->save($data);
				if ($up) {
					$this->success('保存成功！');
					exit();
				}else{
					$this->error('操作失败！');
					exit();
				}

			}else{
				$this->error('系统错误！');
				exit();
			}
		}else{
			$this->assign('datas',M('web')->where(M('web')->getPk().'='.I('get.id'))->find());
			$this->display();
		}
	}

	//*************************
	//用户反馈
	//*************************
	public function fankui(){
		//获取搜索框发送过来的数据
		//搜索，根据广告标题搜索
		$fankui=M("fankui");
		$message = intval($_REQUEST['message']);
		$condition = array();
		if ($message) {
			$condition['message'] = array('LIKE','%'.$message.'%');
			$this->assign('message',$message);
		}

		//分页
		$count   = $fankui->where($condition)->count();// 查询满足要求的总记录数
		$Page    = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

		//头部描述信息，默认值 “共 %TOTAL_ROW% 条记录”
		$Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
		//上一页描述信息
	    $Page->setConfig('prev', '上一页');
	    //下一页描述信息
	    $Page->setConfig('next', '下一页');
	    //首页描述信息
	    $Page->setConfig('first', '首页');
	    //末页描述信息
	    $Page->setConfig('last', '末页');
	    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');

		$show  = $Page->show();// 分页显示输出

		$list = $fankui->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($list as $k => $v) {
			$list[$k]['user']=M("user")->where("id=".$v['uid'])->getField("name");
			$list[$k]['addtime']=date("Y-m-d H:i:s",$v['addtime']);
		}		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display(); // 输出模板
	}

	public function fankui_del(){
		$id = intval($_REQUEST['did']);
		$check_info = M('fankui')->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error("参数有误!");
			exit();
		}

		$up = M('fankui')->where('id='.intval($id))->delete();
		if ($up) {
			$this->success('操作成功!');
			exit();
		}else{
			$this->error("操作失败!");
		    exit;
		}
	}

	//*************************
	//城市管理
	//*************************
	public function city(){
		$id=(int)$_GET['id'];
		//一级列表
		$city=M('ChinaCity')->where("tid=".$id)->select();
		foreach ($city as $k => $v) {
			$city[$k]['priv']=$v['tid']<1 ? '省级' : M('ChinaCity')->where('id='.$v['tid'])->getField('name');
		}
		//dump($city);exit;
		//省市区面包屑，此调用函数在楼下
		$nav=$id>0 ? $this->city_jibie($id) : NULL;
		//dump($_GET);
		//如果有GET到type=del就执行删除
		if($_GET['type']=='del'){
			$this->delete('ChinaCity',$id);
		}
		
		//=============
		//将变量输出
		//=============
		$this->assign('id',$id);
		$this->assign('city',$city);
		$this->assign('nav',$nav);
		$this->display();
	}

	//*************************
	//城市管理  面包屑功能
	//*************************
	public function city_jibie($id){
	   $re=M('ChinaCity')->field('name,tid,id')->where('id='.$id)->find();
	   //dump($re);
	   $text = '<a href="?id='.$re['id'].'">'.$re['name'].'</a>';
	   if($re['tid']>0){
		   $text = $this->city_jibie($re['tid']) .' -> '. $text;   
	   }
	   return $text;
	}


	//*************************
	//城市管理  添加下级县市
	//*************************
	public function city_add(){
		//这是点击添加下级是获取
	    $tid=(int)$_GET['tid'];
	    //这是点击修改时获取
		$id=(int)$_GET['id'];
		$priv=M('ChinaCity')->where('id='.$tid)->find();
		$city=M('ChinaCity')->where('id='.$id)->find();
		//dump($priv);
		//修改时获取post过来的东西，然后进行判断插入或者更新
		if($_POST['submit']){
			 //dump($_POST);exit;
			  $array = array(
			             'tid' => $tid ,
						 'name' => $this->htmlentities_u8($_POST['name']) ,
			               );
			  //此处为添加下级
			  if($id<1)
			  {
				 $id =M('ChinaCity')->add($array);
				 echo '<script>alert("操作成功！");location="?tid='.$tid.'&id='.$id.'";</script>';
			  }else{
			  	 //此处为修改
				 $sql = M('ChinaCity')->where('id='.$id)->save($array);  
			  }
			  //修改后的后续行为
			  if($sql){			  
				  echo '<script>alert("操作成功！");location="?tid='.$tid.'&id='.$id.'";</script>';
			   }else{
				  echo '<script>alert("操作失败！");history.go(-1);</script>';
			   }
			  
		}
		//此处为添加新的下级的后续操作
		if($id>0){
		  $tid = M('ChinaCity')->where('id='.$id)->getField('tid');
		}
		//=============
		//将变量输出
		//=============
		$this->assign('id',$id);
		$this->assign('priv',$priv);
		$this->assign('city',$city);
		$this->display();
	}

	//*************************
	// 新闻栏目设置
	//*************************
	public function new_option(){
		$id=(int)$_GET['id'];
		$option=$_GET['option'];
		if($_POST['submit']==true){
		   
		   try{
			   
			   $array['sort']=(int)$_POST['sort'];
			   $array['name']=$this->htmlentities_u8($_POST['name']);
			   $array['photo']=$this->htmlentities_u8($_POST['photo']);
			   $array= array(
						'pid' => (int)$_POST['pid'],
						'sort' => (int)$_POST['sort'],
						'key' => $this->htmlentities_u8($_POST['key']),
						'val' => $this->htmlentities_u8($_POST['val']),
						  );
				foreach ($array as $k => $v) {
					if(!$v){
						unset($array[$k]);
					}
				}	  
				if(strlen($array['val'])!=6){
					throw new \Exception('颜色不正确！');
				}
			   
				$sql=M('config')->where("id=$id")->save($array);
				
				if(!$sql) throw new \Exception('操作失败！');
				
				echo '<script>
						  alert("操作成功！");
						  location.href="new_option?id='.$id.'"
						  window.opener.location.reload();
					   </script>';
			
		   }catch(\Exception $e){
			   echo '<script>
				      alert("'.$e->getMessage().'");
					  history.go(-1);
				    </script>';
		   }
		   return ;
		}

		if($option=="more"){
			$more="more";//设置标示
		}else{$more="";}
		$config= $id>0 ? M('config')->where("id=$id")->find() : ""; 
		//将栏目分类遍历出来
		$newscat= M('category')->where('tid=23')->select();
		foreach ($newscat as $k => $v) {
		  $config['pid']==$v['id'] ? $select="selected=selected" : $select="";
		  $newscat[$k]['id'] = "value='".$v['id']."' $select";  
		}
		//dump($newscat);exit;
		//=============
		//将变量输出
		//=============
		$this->assign('id',$id);
		$this->assign('config',$config);
		$this->assign('more',$more);
		$this->assign('newscat',$newscat);
		$this->display();
	}

	//*************************
	// 小程序配置 设置页面
	//*************************
	public function setup(){
		if(IS_POST){
			//构建数组
			M('program')->create();
			//上传产品分类缩略图
			if (!empty($_FILES["file2"]["tmp_name"])) {
				//文件上传
				$info2 = $this->upload_images($_FILES["file2"],array('jpg','png','jpeg'),"logo");
			    if(!is_array($info2)) {// 上传错误提示错误信息
			        $this->error($info2);
			    }else{// 上传成功 获取上传文件信息
				    M('program')->logo = 'UploadFiles/'.$info2['savepath'].$info2['savename'];
			    }
			}
			if (!empty($_FILES["file3"]["tmp_name"])) {
				//文件上传
				$info3 = $this->upload_images($_FILES["file3"],array('jpg','png','jpeg'),"logo");
			    if(!is_array($info3)) {// 上传错误提示错误信息
			        $this->error($info3);
			    }else{// 上传成功 获取上传文件信息
				    M('program')->banner = 'UploadFiles/'.$info3['savepath'].$info3['savename'];
			    }
			}
			M('program')->uptime=time();

			$check = M('program')->where('id=1')->getField('id');
			if (intval($check)) {
				$up = M('program')->where('id=1')->save();
			}else{
				M('program')->id=1;
				$up = M('program')->add();
			}

			if ($up) {
				$this->success('保存成功！');
				exit();
			}else {
				$this->error('操作失败！');
				exit();
			}
			
		}else{
			$this->assign('info',M('program')->where('id=1')->find());
			$this->display();
		}

	}

}