<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class SiteController extends PublicController {
	public function detail(){
		$id=I("request.id");
		if(!$id){
			echo json_encode(array("status"=>0,"err"=>"参数不足！"));exit;
		}
		$re=M("site")->where("id=$id")->field("id,name,digest,logo,content,addtime,score,myqrcode_url,myqrcode_img")->find();
		$re['logo']=__DATAURL__.$re['logo'];
		$re['myqrcode_img']= $re['myqrcode_img'] ? __DATAURL__.$re['myqrcode_img'] : "";
		$content = str_replace(C('content.dir'), __DATAURL__ , $re['content']);
		//$content = str_replace("/Data/", "http://127.0.0.1/Data/", $re['content']);
		$re['content']= html_entity_decode($content, ENT_QUOTES ,'utf-8');
		 
		echo json_encode(array("status"=>1,"err"=>$re));
	}
}