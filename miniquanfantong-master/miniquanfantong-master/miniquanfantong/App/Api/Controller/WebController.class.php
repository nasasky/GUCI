<?php
namespace Api\Controller;
use Think\Controller;
class WebController extends PublicController {
	//***************************
	//  所有单页数据接口
	//***************************
    public function web(){
    	$web_id = intval($_REQUEST['web_id']);
    	$content = M('web')->where('id='.intval($web_id))->getField('concent');
    	$content = html_entity_decode($content, ENT_QUOTES, "utf-8");
		echo urldecode(json_encode(array('status'=>1,'content'=>$content)));
    }

    public function getWeb(){
        $web=M("web");
        $list['plan']=$web->where("id=1")->getField("concent");
        $content1 = str_replace(C('content.dir'), __DATAURL__ , $list['plan']);
        $list['plan']= html_entity_decode($content1, ENT_QUOTES ,'utf-8');
        $list['aboutus']=$web->where("id=2")->getField("concent");
        $content2 = str_replace(C('content.dir'), __DATAURL__ , $list['aboutus']);
        $list['aboutus']= html_entity_decode($content2, ENT_QUOTES ,'utf-8');
        echo json_encode(array("status"=>1,"err"=>$list));
    }

    //***************************
	//  获取中心认证图片接口
	//***************************
    public function vip_char(){
    	
    	$pic = M('admin_app')->getField('photo');
    	$pic = "http://".$_SERVER['SERVER_NAME'].__DATA__.'/'.$pic;
    	echo json_encode(array('pic'=>$pic));
    	exit();
    }

    //***************************
    //  产品商家搜索接口
    //***************************
    public function searches(){
        //print_r(__PHOTOURL__);die();
        $keyword = trim($_REQUEST['keyword']);
        if (!$keyword) {
            echo json_encode(array('status'=>0,'err'=>'请输入搜索内容.'));
            exit();
        }

        $page=intval($_REQUEST['page']);
        if (!$page) {
            $page=0;
        }

        $prolist = M('product')->where('del=0 AND is_down=0 AND name LIKE "%'.$keyword.'%"')->order('addtime desc')->field('id,name,photo_x,shiyong,renqi,price,price_yh,company')->limit($page.',15')->select();
        foreach ($pro_list as $k => $v) {
            $prolist[$k]['photo_x'] = __PHOTOURL__.$v['photo_x'];
        }

        $page2=intval($_REQUEST['page2']);
        if (!$page2) {
            $page2=0;
        }

        $condition = array();
        $condition['status']=1;
        //根据店铺名称查询
        $condition['name']=array('LIKE','%'.$keyword.'%');
        //获取所有的商家数据
        $store_list = M('shangchang')->where($condition)->order('sort desc,type desc')->field('id,name,uname,logo,tel,sheng,city,quyu')->limit($page2.',6')->select();
        foreach ($store_list as $k => $v) {
            $store_list[$k]['sheng'] = M('china_city')->where('id='.intval($v['sheng']))->getField('name');
            $store_list[$k]['city'] = M('china_city')->where('id='.intval($v['city']))->getField('name');
            $store_list[$k]['quyu'] = M('china_city')->where('id='.intval($v['quyu']))->getField('name');
            $store_list[$k]['logo'] = __PHOTOURL__.$v['logo'];
            $pro_list = M('product')->where('del=0 AND is_down=0 AND shop_id='.intval($v['id']))->field('id,photo_x,price_yh')->limit(4)->select();
            foreach ($pro_list as $key => $val) {
                $pro_list[$key]['photo_x'] = __PHOTOURL__.$val['photo_x'];
            }
            $store_list[$k]['pro_list'] = $pro_list;
        }

        echo json_encode(array('status'=>1,'pro'=>$prolist,'shop'=>$store_list));
        exit();
    }

    //***************************
    //  获取app主题颜色接口
    //***************************
    public function getcolor(){
        $colors = M('admin_app')->where('pid=29')->getField('theme_color');
        if ($colors) {
            echo json_encode(array('status'=>1,'colors'=>$colors));
            exit();
        }else{
            echo json_encode(array('status'=>0));
            exit();
        }
    }

    
    public function getqrcode(){
        $uid=I("request.uid");
        if(!$uid){
            echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
        }
        
        $qrcode=M("user")->where("id=$uid")->getField("qrcode");
        if($qrcode){
            echo json_encode(array("status"=>1,"err"=>__DATAURL__.$qrcode));
        }else{
            echo json_encode(array("status"=>0,"err"=>"您还没有您的小程序码!是否要生成个人专属小程序码?"));
        }                   
    }
    /**
     * [makeqrcode 生成二维码]
     * @return [type] [description]
     */
    public function makeqrcode(){
        $uid=I("request.uid");
        if(!$uid){
            echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
        }
        $access_token=$this->_getAccessToken();
        //2
        $path="pages/user/user?linkshipID=".$uid;
        $width=430;
        $post_data='{"path":"'.$path.'","width":'.$width.'}'; 
        //$post_data='{"scene":"'.$uid.'","width":'.$width.'}'; 
        $url="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
        //$url="http://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
        $result=$this->api_notice_increment($url,$post_data);
        //3
        $newFilePath='UploadFiles/user_img/qrcode_'.$uid.'_'.date("Ymd",time()).'.jpg';
        if(empty($result)){
            $result=file_get_contents("php://input");
        }
        $newFile = fopen("Data/".$newFilePath,"w");//打开文件准备写入
        fwrite($newFile,$result);//写入二进制流到文件
        fclose($newFile);//关闭文件
        M("user")->where("id=$uid")->setField("qrcode",$newFilePath);
        echo json_encode(array("status"=>1,"err"=>__DATAURL__.$newFilePath));
    }
    /**
     * [sendTemplate 发送模版消息接口]
     * @param  [type] $orderid [description]
     * @return [type]          [description]
     */
    public function sendTemplate(){
        //72or6cc6K3-4859zJz2dIcM1TIjiTkVkvlslCOEyG3M
        $uid=I("request.uid");
        //dump(I("request."));exit;
        // $order=M("order")->where("id=$orderid")->find();
        $openid=M("user")->where("id=".$uid)->getField("openid");
        $touser = $openid;
        $template_id = "72or6cc6K3-4859zJz2dIcM1TIjiTkVkvlslCOEyG3M";
        $page = "pages/user/user";
        $form_id = I('request.form_id');
        $access_token=$this->_getAccessToken();
        $value=array(
            "keyword1"=>array(
                "value"=>"201707291705",
                "color"=>"#173177"
            ),
            "keyword2"=>array(
                "value"=>M("user")->where("id=$uid")->getField("name"),
                "color"=>"#173177"
            ),
            "keyword3"=>array(
                "value"=>"2017年8月1日",
                "color"=>"#173177"
            ),
            "keyword4"=>array(
                "value"=>"18814373687",
                "color"=>"#173177"
            ),
            "keyword5"=>array(
                "value"=>"体检",
                "color"=>"#173177"
            )
        );
        $dd = array();
        $dd['touser']=$touser;//openid
        $dd['template_id']=$template_id;//模版id
        $dd['page']=$page;  //点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,该字段不填则模板无跳转。
        $dd['form_id']=$form_id;//formid or prepayid       
        $dd['data']=$value;                         //模板内容，不填则下发空模板
        $dd['color']='';                            //模板内容字体的颜色，不填默认黑色
        $dd['emphasis_keyword']="keyword1.DATA";    //模版关键字放大
        $posturl="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
        $re=$this->https_curl_json($posturl,$dd,'json');
        echo json_encode($re);
    }
    /**
     * [getkfinfo 联系我们]
     * @return [type] [description]
     */
    public function getkfinfo(){
        $re=M("program")->where("id=1")->field("kfbanner,tel")->find();
        $re['kfbanner']=__DATAURL__.$re['kfbanner'];
        if($re){
            echo json_encode(array("status"=>1,"err"=>$re));
        }else{
            echo json_encode(array("status"=>0,"err"=>"请求失败!"));
        }
    }
    /**
     * [upfankui 提交反馈]
     * @return [type] [description]
     */
    public function upfankui(){
        $post=I("post.");
        if(!$post['uid']){
            echo json_encode(array("status"=>0,"err"=>"参数不足!"));
        }
        $data['uid']=$post['uid'];
        $data['message']=$post['message'];
        $data['addtime']=time();
        $data['tel']=$post['tel'];
        $re=M("fankui")->add($data);
        if($re){
            echo json_encode(array("status"=>1,"err"=>"感谢您的反馈,祝您生活愉快!"));
        }else{
            echo json_encode(array("status"=>0,"err"=>"请求失败!"));
        }
    }
}