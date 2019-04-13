<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;
class UserController extends PublicController {
	
	Public function verify(){
	    $image = new \Org\Util\Image;
	    $image->buildImageVerify();
    }

	/**
	 * [userinfo 获取用户信息]
	 * @return [type] [description]
	 */
	public function userinfo(){
		$uid = I('request.uid');
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'请求失败.'));
			exit();
		}

		$user = M("user")->where('id='.$uid)->field('tel,shop_id,uname,email')->find();
		if($user['shop_id']>0){
			$shopname=M('shangchang')->where("id=".$user['shop_id'])->getField("name");
		}else{
			$shopname=false;
		}
		$user['shopname']=$shopname;
		echo json_encode(array('status'=>1,'userinfo'=>$user));
		exit();
	}
	public function sendcode(){
		header('Content-Type: text/plain; charset=utf-8');
		vendor("alidayu.index");
		$agentLinktel = $_REQUEST['agentLinktel'];
		// //此处需要替换成自己的AK信息
        $accessKeyId = "LTAIY6FlC03iTkpE";//参考本文档步骤2
        $accessKeySecret = "gGESoxFH7FAT517YckFW7QaAPud0EF";//参考本文档步骤2

  //       //短信API产品域名（接口地址固定，无需修改）
			$demo = new \SmsDemo(
			    $accessKeyId,
			    $accessKeySecret
			);
			$setSignName="全返通网";
			$response = $demo->sendSms(
			    "全返通网", // 短信签名
			    "SMS_90380002", // 短信模板编号
			    $agentLinktel, // 短信接收者
			    Array(  // 短信模板中字段的值
			        "codes"=>"621365",
			        "product"=>$setSignName
			    )
			);
			dump($response);

			if($response){
			$sms_log=M("sms_log");
			$array['tel']=$tel;
			$array['code']=$sms;
			$array['start_time']=time();
			$array['end_time']=$array['start_time']+600;//验证码10分钟有效;
			@$check=$sms_log->where("tel=$tel")->find();
			if($check){
				$sms_log->where("tel=$tel")->save($array);
			}else{
				$sms_log->add($array);
			}
				echo json_encode(array("status"=>1,"err"=>"请求成功!"));//成功
			}else{
				echo json_encode(array("status"=>0,"err"=>"请求失败!"));//失败
			} 
	}

	//***************************
    //  发送手机验证码
    //***************************
    public function get_code(){
        $tel = trim($_REQUEST['tel']);
        $uid = intval($_REQUEST['uid']);
        if (!$tel) {
            echo json_encode(array('status'=>0,'err'=>'参数错误！'));
            exit();
        }

        if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$/",$tel)){    
            echo json_encode(array('status'=>0,'err'=>'手机号码格式错误！err_code:'.__LINE__));
            exit(); 
        }

        $checktel = M("user")->where('tel="'.trim($tel).'" AND del=0')->getField('id');
        if ($checktel) {
            echo json_encode(array('status'=>0,'err'=>'手机号已被认证了！err_code:'.__LINE__));
            exit();
        }

        $alidayu = C('Alidayu');
        $appkey = $alidayu['appkey'];
        $secretKey = $alidayu['secretKey'];
        $setSmsFreeSignName = $alidayu['setSmsFreeSignName'];

        vendor("Sms.TopSdk");
        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        $codes = rand(100000,999999);
        
        //初始化访问的acsCleint
        $profile = \DefaultProfile::getProfile($region, $appkey, $secretKey);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new \DefaultAcsClient($profile);
        
        $request = new \Dysmsapi\Request\V20170525\SendSmsRequest;
        //必填-短信接收号码
        $request->setPhoneNumbers($tel);
        //必填-短信签名
        $request->setSignName($setSmsFreeSignName);
        //必填-短信模板Code
        $request->setTemplateCode("SMS_90380002");
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam("{\"number\":\"".$codes."\"}");
        //选填-发送短信流水号
        $request->setOutId("1234");
        
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        //dump($req);
        if($acsResponse){
            $sms_log=M("sms_log");
            $array['tel']=$tel;
            $array['code']=$codes;
            $array['start_time']=time();
            $array['end_time']=$array['start_time']+900;//验证码15分钟有效;
            @$check=$sms_log->where("tel=$tel")->find();
            if($check){
                $sms_log->where("tel=$tel")->save($array);
            }else{
                $sms_log->add($array);
            }
            echo json_encode(array('status'=>1,'codes'=>$codes));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'验证码发送失败！err_code:'.__LINE__));
            exit();
        } 
    }


	public function user_reg(){
		$uid = trim($_POST['uid']);
	  	// $pwd  = md5(md5($_POST['pwd']));
	    $tel  = trim($_POST['tel']);
	    $yzcode=$_POST['code'];
	    $sms_log=M("sms_log");
		if($yzcode){
			$mysmslog=$sms_log->where("tel=$tel")->find();
			if($yzcode!=$mysmslog['code']){
				echo json_encode(array('status'=>0,'err'=>'无效验证码'));
				exit();
			}
			if(time()>$mysmslog['end_time']){
				echo json_encode(array('status'=>0,'err'=>'无效验证码'));
				exit();
			}
		}
		$user=M('user');

		$check_mob=$user->where('tel="'.$tel.'" AND del=0')->count();
		if($check_mob>0) {
			echo json_encode(array('status'=>0,'err'=>'手机号已被绑定！'));
			exit();
		}
		$data['tel'] = $tel;
		$res = M('user')->where('id='.intval($uid))->save($data);
		$info = M('user')->where('id='.intval($uid))->find();
		if($res){
			echo json_encode(array('status'=>1,'info'=>$info));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'绑定失败！'));
			exit();
		}
	}

    /**
     * [getUserInfo 获取用户信息]
     * @return [type] [description]
     */
	public function getUserInfo(){
		$uid=I("request.uid");
		if(!$uid){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}
		$re=M("user")->where("id=$uid")->find();
		if($re){
			$address=M("china_city");
			$data['id']=$re['id'];
			$data['name']=$re['name'];
			$data['uname']=$re['uname'];
			$data['realname']=$re['realname'];
			$data['tel']=$re['tel'];
			$data['weixin']=$re['weixin'];
			$data['bankname']=$re['bankname'];
			$data['bankid']=$re['bankid'];
			$data['sex']=$re['sex'];
			$data['birthday']=date("Y-m-d",$re['birthday']);
			$data['sheng']=$re['sheng'];
			$data['city']=$re['city'];
			$data['quyu']=$re['quyu'];
			$data['shengname']=$address->where("id=".$re['sheng'])->getField("name");
			$data['cityname']=$address->where("id=".$re['city'])->getField("name");
			$data['quyuname']=$address->where("id=".$re['quyu'])->getField("name");
			echo json_encode(array("status"=>1,"err"=>$data));
		}else{
			echo json_encode(array("status"=>0,"err"=>"请求失败!"));
		}
	}
	/**
	 * [saveUserInfo 保存用户信息]
	 * @return [type] [description]
	 */
	public function saveUserInfo(){
		$post=I("post.");
		if(!$post['uid']){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}
		// $data['name']=$post['name'];
		$data['uname']=$post['uname'];
		$data['realname']=$post['realname'];
		$data['tel']=$post['tel'];
		$data['weixin']=$post['weixin'];
		$data['bankname']=$post['bankname'];
		$data['bankid']=$post['bankid'];
		$data['sex']=$post['sex'];
		$data['birthday']=strtotime($post['birthday']);
		$data['sheng']=$post['sheng'];
		$data['city']=$post['city'];
		$data['quyu']=$post['quyu'];
		$province = M('china_city')->where('id='.intval($data['sheng']))->getField('name');
        $city_name = M('china_city')->where('id='.intval($data['city']))->getField('name');
        $quyu_name = M('china_city')->where('id='.intval($data['quyu']))->getField('name');
        $data['address'] = $province.' '.$city_name.' '.$quyu_name;
		if(!$this->isMobile($data['tel'])){
			echo json_encode(array("status"=>0,"err"=>"请输入正确的手机号码!"));exit;
		}
		// $data['address']=$post['address'];
		$re=M("user")->where("id=".$post['uid'])->save($data);
		if($re){
			echo json_encode(array("status"=>1,"err"=>"提交成功!"));
		}else{
			echo json_encode(array("status"=>0,"err"=>"提交失败!"));
		}
	}
	/**
	 * [getsessionkey code 换取 session_key]
	 * @return [openid] [用户唯一标识]
	 *         [session_key] [会话密钥]session_key 是对用户数据进行加密签名的密钥
	 */
	public function getqrcode(){
		// $uid=I("post.uid");
		$uid=2;
		$access_token=$this->getaccesstoken();
		// dump($access_token);
		if($access_token){
			//post方式
	        $url="https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
	        $post=I("post.");
	        $postData["path"]="/pages/user/user?id=$uid&act=linkship";//参数 
	        $postData["width"]="430"; //二维码的宽度

	        $postData=json_encode($postData);

	        //var_dump($postData);exit;

	        $ch=curl_init();
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch,CURLOPT_HEADER,0);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	        //设置是通过post还是get方法
	        curl_setopt($ch,CURLOPT_POST,1);
	        //传递的变量
	        curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
	        $data = curl_exec($ch);
	        curl_close($ch);
	        echo $data;
		}
		
	}

	public function getaccesstoken(){
		$wx_config = C('weixin');
    	$appid = $wx_config['appid'];
    	$secret = $wx_config['secret'];
		if (!$appid || !$secret) {
			return false;
		}
		$get_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
		// dump($get_token_url);
		// $get_token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
		$ch = curl_init(); // 开启句柄
		curl_setopt($ch,CURLOPT_URL,$get_token_url); // 需要获取的 URL 地址，也可以在curl_init() 初始化会话的时候。 
		curl_setopt($ch,CURLOPT_HEADER,0);  // 设启用时会将头文件的信息作为数据流输出。 1---输出，0---不输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //在尝试连接时等待的秒数。设置为0，则无限等待，秒为单位 
		$res = curl_exec($ch);  // 执行给定的 cURL 会话。 
		curl_close($ch); // 关闭句柄
		echo $res;
		//return $res['access_token'];
	}
	/**
	 * [linkship 建立关系]
	 * @return [type] [description]
	 */
	public function linkship(){
		$post=I("post.");
		$user=M("user");
		if($post["uid"] && $post['type']=="linkship" && $post['parent']){
			if($user->where("disable=0 AND id=".$post['parent'])->getField("id")<1){
				echo json_encode(array("status"=>1,"err"=>"扫描的用户不存在或已被禁用!"));exit;
			}
			//查询是否已经绑定
			@$check=$user->where("id=".$post['uid'])->getField("first_leader");
			if($check<1){
				$re=$user->where("id=".$post['uid'])->setField("first_leader",$post['parent']);
				if($re>0){
					//第二上级是否有
					@$second=M("user")->where("id=".$post['parent']." AND level>=10")->getField("first_leader");
					if($second>0){
						$user->where("id=".$post['uid'])->setField("second_leader",$second);
					}
					echo json_encode(array("status"=>1,"err"=>"恭喜您已成为第".$post['uid']."位会员！您的推荐人ID为".$post['parent']));
				}
			}else{
				echo json_encode(array("status"=>0,"err"=>"您已经绑定了!"));
			}

		}else{
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));
		}
	}
	/**
	 * [messagelist 获取消息列表]
	 * @return [type] [description]
	 */
	public function messagelist(){
		$list['system']=M("message")->where("category=0")->order("send_time desc")->limit(100)->select();
		foreach ($list['system'] as $k => $v) {
			$list['system'][$k]['message']=mb_substr($v['message'], 0,15,"utf-8");
			$list['system'][$k]['send_time']=date("Y-m-d",$v['send_time']);
		}
		$list['huodong']=M("message")->where("category=1")->order("send_time desc")->limit(100)->select();
		foreach ($list['huodong'] as $k => $v) {
			$list['huodong'][$k]['message']=mb_substr($v['message'], 0,15,"utf-8");
			$list['huodong'][$k]['send_time']=date("Y-m-d",$v['send_time']);
		}
		echo json_encode(array("status"=>1,"err"=>$list));
	}
	/**
	 * [messagedetail 系统消息]
	 * @return [type] [description]
	 */
	public function messagedetail(){
		$id=I("post.id");
		if(!$id){
			echo json_encode(array("status"=>0,"err"=>"请求失败!"));exit;
		}
		$re=M("message")->where("message_id=$id")->getField("message");
		echo json_encode(array("status"=>1,"err"=>$re));
	}
	/**
	 * [getUserLevel 获得用户的等级信息]
	 * @return [type] [description]
	 */
	public function getUserLevel(){
		$uid=I("post.uid");
		if(!$uid){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}

		$re['lv']=$this->_getUserLevel($uid);
		$re['lv']=intval($re['lv']);
		$re['addtime']=date("Y-m-d H:i",M("user")->where("id=$uid")->getField("addtime"));
		$re['is_daili']=M("user")->where("id=$uid")->getField("is_daili");
		if($re['lv']>=10){
			$re['arr']=array();
			if($re['lv']>=10 && $re['lv']<20){
				$re['lvname']="VIP会员";
				if($re['lv']>10){
					for($i=0;$i<($re['lv']-10);$i++){
						$re['arr'][$i]=1;
					}
				}
			}elseif($re['lv']>=20 && $re['lv']<30){
				$re['lvname']="服务商";
				if($re['lv']>20){
					for($i=0;$i<($re['lv']-20);$i++){
						$re['arr'][$i]=1;
					}
				}
			}elseif($re['lv']>=30 && $re['lv']<40){
				$re['lvname']="经销商";
				if($re['lv']>30){
					for($i=0;$i<($re['lv']-30);$i++){
						$re['arr'][$i]=1;
					}
				}
			}else{
				$re['lvname']="经销商";
				$re['arr']=array();
			}
		}else{
			$re['lvname']="普通会员";
			$re['arr']=array();
		}
		echo json_encode(array("status"=>1,"err"=>$re));

	}
	public function getUserMoney(){
		$uid=I("post.uid");
		if(!$uid){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}
		//1累计佣金
		$data['allMoney']=$this->_getUserAllMoney_total($uid);
		//2用户余额
		$data['userMoney']=M("user")->where("id=$uid")->getField("distribut_money");
		//3我的团队成员人数
		$data['menber']=$this->_getMyTeamMember($uid);

		echo json_encode(array("status"=>1,"err"=>$data));
	}
	public function getUserMoneylog(){
		$uid=I("post.uid");
		if(!$uid){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}
		$list=$this->_getUserAllMoney_log($uid);
		if($list){
			echo json_encode(array("status"=>1,"err"=>$list));
		}else{
			echo json_encode(array("status"=>0,"err"=>"暂无数据!"));
		}
	}
	public function getUserTeam(){
		$uid=I("post.uid");
		if(!$uid){
			echo json_encode(array("status"=>0,"err"=>"参数不足!"));exit;
		}
		$list['zhijie']=M("user")->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->field("id,name,level,addtime,photo")->select();
		foreach ($list['zhijie'] as $k => $v) {
			$list['zhijie'][$k]['level']=intval($v['level']);
			$list['zhijie'][$k]['addtime']=date("Y-m-d",$v['addtime']);
			$list['zhijie'][$k]['lvinfo']=$this->_getLevelInfo($v['level']);
		}
		if($this->_getUserLevel($uid)>=10){
			$list['jianjie']=M("user")->where("second_leader=$uid AND del=0 AND qx=6 AND level<30")->field("id,name,level,addtime,photo")->select();
			foreach ($list['jianjie'] as $k => $v) {
				$list['jianjie'][$k]['level']=intval($v['level']);
				$list['jianjie'][$k]['addtime']=date("Y-m-d",$v['addtime']);
				$list['jianjie'][$k]['lvinfo']=$this->_getLevelInfo($v['level']);
			}
		}else{
			$list['jianjie']=array();
		}
		
		echo json_encode(array("status"=>1,"err"=>$list));
	}


	public function user_add(){
		if(IS_POST){
			$post=I("post.");
			$data['name']=$post['name'];
			$data['uname']=$post['name'];
			$data['photo']="https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83eqcxfluFyPMx5kN6yibZVEDSyNa3UG295cmOnGbApE9mg3FG1ic7b1fIrkMB1CoNrM0rZuY8cc6MFcQ/0";
			$data['12345678900'];
			$data['sex']=0;
			$data['addtime']=time();
			$data['source']="wx";
			$data['first_leader']=0;
			$data['qx']=6;
			$data['firstbuy']=0;
			$userid=M("user")->add($data);
			if($userid>0){
				$this->_linkship($userid,$post['first_leader']);
				$this->_ceshiaddorder($userid,$post['num']);
				$this->success("添加成功!");
			}
		}else{
			$this->display();
		}

	}
	public function _linkship($uid,$parentid){
		//查询是否已经绑定
		$user=M("user");
		@$check=$user->where("id=".$uid)->getField("first_leader");
		if($check<1){
			$re=$user->where("id=".$uid)->setField("first_leader",$parentid);
			if($re>0){
				//第二上级是否有
				@$second=$user->where("id=".$parentid." AND level>=10")->getField("first_leader");
				if($second>0){
					$user->where("id=".$uid)->setField("second_leader",$second);
				}
			}
		}
	}
	public function _ceshiaddorder($uid,$num){
		//UploadFiles/product/20170717/1500234134839211.jpg
		$data['order_sn']=$this->build_order_no();
		$data['shop_id']=1;
		$data['uid']=$uid;
		$data['price']=$num*59;
		$data['addtime']=time();
		$data['type']="weixin";
		$data['status']=20;
		$data['receiver']=M("user")->where("id=$uid")->getField("name");
		$data['tel']=12345678900;
		$data['address_xq']="测试订单";
		$data['code']="440183";
		$data['product_num']=$num;
		$re=M("order")->add($data);
		if($re>0){
			$this->_ceshiaddorderpro($re,$num);
			if($this->_checkIsFirstOrder($uid)){
        		//是的话走首单流程
        		$this->_getMoney_firstbuy($uid,$re);
        	}else{
        		//否则走复购流程
        		$this->_getMoney_rebuy($uid,$re);
        	}
		}
	}
	public function _ceshiaddorderpro($orderid,$num){
		$data['pid']=2;
		$data['order_id']=$orderid;
		$data['name']="博士洁出口家庭装";
		$data['photo_x']="UploadFiles/product/20170726/1501051962247716.jpg";
		$data['addtime']=time();
		$data['num']=$num;
		M("order_product")->add($data);
	}
	public function userlist(){
		$list=M("user")->where("del=0 AND qx=6")->select();
		foreach ($list as $k => $v) {
			$name=M("user")->where("id=".$v['first_leader'])->getField("name");
			$list[$k]['leader']=$name?$name:M("user")->where("id=".$v['first_leader'])->getField("uname");
			$list[$k]['lvname']=$this->_getLevelName($v['level']);
		}
		$this->assign("list",$list);
		$this->display();
	}
	public function moneylog(){
		$list=M("distribut_money_log")->select();
		foreach ($list as $k => $v) {
			$name=M("user")->where("id=".$v['user_id'])->getField("name");
			$list[$k]['username']=$name?$name:M("user")->where("id=".$v['user_id'])->getField("uname");
		}
		$this->assign("list",$list);
		$this->display();
	}

	public function getUser(){
		$uid = intval($_REQUEST['uid']);
		$info = M('user')->where('id='.$uid)->find();
		if(!$info){
			echo json_encode(array('status'=>0,'err'=>'网络错误！'));
			exit();
		}

		$tixian = M('getcash_log')->where('user_id='.$uid)->field('money,addtime')->select();
		$total = 0;
		foreach($tixian as $k => $v){
			$total += floatval($v['money']);
			$tixian[$k]['addtime'] = date('Y-m-d',$v['addtime']);
		}
		$total += $info['distribut_money'];
		$level = intval($info['level']);
		if($level == 10){
			$fenlv = 0.2;
		}else if($level == 20){
			$fenlv = 0.3;
		}else if($level == 30){
			$fenlv = 0.4;
		}else{
			$fenlv = 0;
		}
		//已获得
		$addtime = M('order')->where('uid='.$uid)->getField('addtime');
		$huode = M('order')->where('status=50 AND addtime>"'.$addtime.'" AND uid !='.$uid)->select();
		$huode2 = array();
		$temp = array();
		foreach($huode as $k => $v){
			$num = M('order')->where('status=50 AND addtime<"'.$v['addtime'].'" AND type='.intval($level))->count();
			if($v['price'] == 68){
				$temp['type'] = 'VIP会员';
			}else if($v['price'] == 168){
				$temp['type'] = '钻石会员';
			}else if($v['price'] == 268){
				$temp['type'] = '皇家会员';
			}else{
				$temp['type'] = '会员';
			}
			$temp['money'] = floatval($v['price']*0.9*$fenlv/$num);
			$temp['addtime'] = date('Y-m-d',$v['addtime']);
			array_push($huode2,$temp);
		}
		echo json_encode(array('status'=>1,'info'=>$info,'total'=>$total,'huode'=>$huode2,'tixian'=>$tixian));
		exit();
	}

	public function getUser2(){
		$uid = intval($_REQUEST['uid']);
		$info = M('user')->where('id='.$uid)->find();
		if(!$info){
			echo json_encode(array('status'=>0,'err'=>'网络错误！'));
			exit();
		}
		$tixian = M('getcash_log')->where('user_id='.$uid.' AND status != 1')->select();
		$tixian2 = 0;
		foreach($tixian as $k => $v){
			$tixian2 += floatval($v['money']);
		}
		$keyong = $info['distribut_money'];
		$total = floatval($tixian2)+floatval($keyong);
		echo json_encode(array('status'=>1,'info'=>$info,'keyong'=>$keyong,'tixian'=>$tixian2,'total'=>$total));
		exit();
	}
}