<?php
namespace Api\Controller;
use Think\Controller;
class LoginController extends PublicController {

	//***************************
	//  前台登录接口
	//***************************
    public function dologin(){
		session_destroy();
		$name = trim($_POST['username']);	//接受“会员账号”
		$pwd  = md5(md5($_POST['pwd']));	//接受“会员密码”
		if (!$name || !$pwd) {
			echo json_encode(array('status'=>0,'err'=>'请输入账号或密码！'));
			exit();
		}

		$user = M('user');
		$where['name'] = $name;
		$where['pwd'] = $pwd;
		$usrNum = $user->where($where)->find();
		///echo $user->_sql();exit;
		if($usrNum){
			@session_start();
			$_COOKIE['sessionid'] = session_id();
			//$_SESSION['sessionid']=session_id();
			$_SESSION['LoginCheck']=md5($name);
			$_SESSION['LoginName']=$name;
			$_SESSION['ID']=$usrNum['id'];
			$_SESSION['photo']=$usrNum['photo'];
			
			echo json_encode(array('status'=>1,'session'=>$_SESSION));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'账号密码错误！'));
			exit();
		}		 
	}

	//***************************
	//  授权登录接口
	//***************************
	public function authlogin(){
		$openid = $_POST['openid'];
		if (!$openid) {
			echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
			exit();
		}
		$con = array();
		$con['openid']=trim($openid);
		$uid = M('user')->where($con)->getField('id');
		if ($uid) {
			$userinfo = M('user')->where('id='.intval($uid))->find();
			if (intval($userinfo['del'])==1) {
				echo json_encode(array('status'=>0,'err'=>'账号状态异常！'));
				exit();
			}
			$err = array();
			$err['ID'] = intval($uid);
			$err['NickName'] = $_POST['NickName'];
			$err['HeadUrl'] = $_POST['HeadUrl'];
			echo json_encode(array('status'=>1,'arr'=>$err));
			exit();
		}else{
			$data = array();
			$data['name'] = $_POST['NickName'];
			$data['uname'] = $_POST['NickName'];
			$data['photo'] = $_POST['HeadUrl'];
			$data['province'] = $_POST['province'];
			$data['city'] = $_POST['city'];
			$data['country'] = $_POST['country'];
			$data['sex'] = $_POST['gender'];
			$data['session_key'] = $_POST['session_key'];
			$data['openid'] = $openid;
			$data['source'] = 'wx';
			$data['qx']=6;
			$data['addtime'] = time();
			$res = M('user')->add($data);
			if ($res) {
				$err = array();
				$err['ID'] = intval($res);
				$err['NickName'] = $data['name'];
				$err['HeadUrl'] = $data['photo'];
				echo json_encode(array('status'=>1,'arr'=>$err));
				exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
		}
	}

	/**
	 * [getsessionkey code 换取 session_key]
	 * @return [openid] [用户唯一标识]
	 *         [session_key] [会话密钥]session_key 是对用户数据进行加密签名的密钥
	 */
	public function getsessionkey(){
		$wx_config = C('weixin');
    	$appid = $wx_config['appid'];
    	$secret = $wx_config['secret'];

		$code = trim($_POST['code']);
		if (!$code) {
			echo json_encode(array('status'=>0,'err'=>'非法操作！'));
			exit();
		}

		if (!$appid || !$secret) {
			echo json_encode(array('status'=>0,'err'=>'非法操作！'.__LINE__));
			exit();
		}

		$get_token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
		$ch = curl_init(); // 开启句柄
		curl_setopt($ch,CURLOPT_URL,$get_token_url); // 需要获取的 URL 地址，也可以在curl_init() 初始化会话的时候。 
		curl_setopt($ch,CURLOPT_HEADER,0);  // 设启用时会将头文件的信息作为数据流输出。 1---输出，0---不输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 ); //TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //在尝试连接时等待的秒数。设置为0，则无限等待，秒为单位 
		$res = curl_exec($ch);  // 执行给定的 cURL 会话。 
		curl_close($ch); // 关闭句柄
		echo $res;
		exit();
	}


}