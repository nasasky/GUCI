<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends PublicController {
  	 /**
      * [index 首页]
      * @return [type] [description]
      */
    public function index(){
      $uid = intval($_REQUEST['uid']);
      $info = M('user')->where('id='.$uid)->find();

    	$ggtop=M('guanggao')->where("position=1")->order('sort desc,id asc')->field('id,name,photo')->limit(10)->select();
  		foreach ($ggtop as $k => $v) {
  			$ggtop[$k]['photo']=__DATAURL__.$v['photo'];
  		}

      $sitelist = M('site')->where('del=0 AND is_show=1')->order("score desc,is_tj desc")->field('id,name,digest,logo,score')->limit(50)->select();
      foreach ($sitelist as $k => $v) {
        $sitelist[$k]['logo']=__DATAURL__.$v['logo'];
      }

      echo json_encode(array("status"=>1,'info'=>$info,"ggtop"=>$ggtop,"sitelist"=>$sitelist));
    }
    /**
     * [getlist 加载更多]
     * @return [type] [description]
     */
    public function getlist(){
        $page = intval($_REQUEST['page']);
        $limit = intval($page*50)-50;

        $sitelist = M('site')->where('del=0 AND is_show=1')->order("score desc,is_tj desc")->field('id,name,digest,logo,score')->limit($limit,50)->select();
        foreach ($sitelist as $k => $v) {
          $sitelist[$k]['logo']=__DATAURL__.$v['logo'];
        }
        echo json_encode(array('status'=>1,'sitelist'=>$sitelist));
    }


}