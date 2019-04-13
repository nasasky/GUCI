<?php
namespace Api\Controller;
use Think\Controller;
class PayController extends PublicController{
	/**
	 * 微信支付接口
	 *   {"appid":"wx0411fa6a39d61297","noncestr":"3reA4pSqGBPPryEL","package":"Sign=WXPay","partnerid":"1230636401","prepayid":"wx20170406175858b261288c050041764688","timestamp":1491472738,"sign":"4A50B67D47E062A1F3B739D76C683D37"}
	 *  appid
	 *  noncestr
	 *  package
	 *  partnerid
	 *  prepayid
	 *  timestamp
	 *  sign
	 */
    public function dowxpay(){
    	//引入文件
    	header('Access-Control-Allow-Origin: *');
		header('Content-type: text/plain');
		vendor("wxpay.wxpay");
		//支付完成后的回调处理页面
		$notify_url = C("wxpay.wx_notify_url");
		
		$uid = intval($_REQUEST['uid']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'登录状态异常.'));
			exit();
		}
		$temp_order = M('order')->where('status=50 AND uid='.$uid)->select();
		if($temp_order){
			echo json_encode(array('status'=>0,'err'=>'支付失败，你已经是会员.'));
			exit();
		}

		$order_id = intval($_REQUEST['order_id']);//订单id
		$pay_type = trim($_REQUEST['pay_type']);//支付类型
		$order = M('order')->where("id=".intval($order_id)." AND del=0")->find();
		if (!$order) {
			echo json_encode(array('status'=>0,'err'=>'订单信息错误.'));
			exit();
		}

		$order_sn = trim($order['order_sn']);//订单号

		$product=M('order_product')->where("`order_id`=".intval($order['id']))->field('name')->select();
		$body = '';
		foreach ($product as $key => $val) {
			if ($key==0) {
				$body .=$val['name'];
			}else{
				$body .=','.$val['name'];
			}
		}
		//检测是否为VIP及以上,是的话就打7.5折;
		// if($this->_getUserLevel($uid)>=10){
		// 	$order['price'] = $order['price'] *0.75;
		// }
		// 获取支付金额		
		// $total = $order['price']*100;     // 转成分
		$total = 0.01*100;
		// 商品名称
		$subject = '小程序:'.$body;
		// 订单号，示例代码使用时间值作为唯一的订单ID号
		$out_trade_no = $order_sn;

		//获取用户openid
		//$tools = new \JsApiPay();
		$openId = M("user")->where("del=0 AND id=".$uid)->getField("openid");
		if(!$openId){
			echo json_encode(array('status'=>0,'err'=>'网络错误.'));
			exit();
		}
		//配置微信支付参数
		define( "_APPID",C("weixin.appid"));
		define( "_MCHID",C("weixin.mchid"));
		define( "_KEY", C("weixin.key"));
		define( "_APPSECRET", C("weixin.secret"));

		//统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($body);                        //商品名称
		$input->SetAttach("小程序"); 
		$input->SetOut_trade_no($out_trade_no);  //订单号
		$input->SetTotal_fee($total);                       //订单总金额
		$input->SetTime_start(date("YmdHis"));           //订单生成时间
		$input->SetTime_expire(date("YmdHis", time() + 600)); //订单失效时间
		$input->SetGoods_tag('');                       //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
		$input->SetNotify_url($notify_url);             //异步通知地址
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order_data = \WxPayApi::unifiedOrder($input); 

		// foreach($order_data as $key=>$value){
	 //        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
	 //    }
		//{"appid":"wx0411fa6a39d61297","noncestr":"3reA4pSqGBPPryEL","package":"Sign=WXPay","partnerid":"1230636401","prepayid":"wx20170406175858b261288c050041764688","timestamp":1491472738,"sign":"4A50B67D47E062A1F3B739D76C683D37"}	
		//{"appid":"wx05a63e391546d945","noncestr":"Uvxaj0yZeE5uw0Wd","package":"Sign=WXPay","partnerid":"1460680002","prepayid":"wx201704221807041af49e25c90779187614","timestamp":1492855550,"sign":"08FB66188544D438DF074F815EB585DF"} 	
		$array=array(
			"appId"	   =>$order_data['appid'],
			"timeStamp"=>(string)time(),
			"nonceStr" =>$order_data['nonce_str'],
			"package"  =>"prepay_id=".$order_data['prepay_id'],
			"signType" =>"MD5",
		);
		$str = 'appId='.$array['appId'].'&nonceStr='.$array['nonceStr'].'&package='.$array['package'].'&signType=MD5&timeStamp='.$array['timeStamp'];
		//重新生成签名
		$array['paySign']=strtoupper(md5($str.'&key='.\WxPayConfig::KEY));
	
		echo json_encode(array('status'=>1,'success'=>$array));
		//exit();

    }
    /**
     * [wxnotifyurl 微信支付异步通知]
     * @return [type] [description]
     */
    public function wxnotifyurl(){
    	vendor("wxpay.wxpay");
    	$xml = file_get_contents('php://input');
    	$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    	//将支付的记录用txt的形式记录起来，文件夹自动创建，方便管理
		$dir='Data/Jsondata/paylog/'.date("Ym").'/'.date("d");
		if(!is_dir($dir)){
		  mkdir($dir,0777,1);
		}
		$file=$dir."/wxpaylog.txt";
		$content="【".date("H:i:s",time())."】=>".$arr['out_trade_no']." >>".$arr['result_code']."<<\n".json_encode($arr)."\n";
		//将记录写进记录文件，有则追加，没有则新建文件。
	    file_put_contents($file,$content,FILE_APPEND);
  		
        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
		$out_trade_no   = $arr['out_trade_no'];      //商户订单号
		$trade_no       = $arr['transaction_id'];          //支付宝交易号
		$trade_status   = $arr['result_code'];      //交易状态
		$total_fee      = $arr['total_fee'];         //交易金额
		$notify_time    = strtotime($arr['time_end']); //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
		$buyer_email    = $arr['buyer_email'];       //买家支付宝帐号；
        $parameter = array(
			"trade_no"      => $trade_no,     //支付宝交易号；
			"status"=>50,
		);
		if($arr['result_code'] == 'SUCCESS') {
			//更新订单状态
			$order=M('order');
            $re=$order->where('order_sn="'.trim($arr['out_trade_no']).'"')->save($parameter);
            if($re){
            	//更新用户的会员类型
            	$uid=$order->where('order_sn="'.trim($arr['out_trade_no']).'"')->getField("uid");
            	$type=$order->where('order_sn="'.trim($arr['out_trade_no']).'"')->getField("type");
            	$temp['level'] = $type;
            	M('user')->where('id='.intval($uid))->save($temp);
            	//将新注册的会员的费用分给其他会员
            	$price = $order->where('order_sn="'.trim($arr['out_trade_no']).'"')->getField("price");
            	$addtime = M('user')->where('id='.intval($uid))->getField('addtime');
		    	$num_vip = M('user')->where('level=10 AND disable=0 AND addtime<"'.$addtime.'"')->count();
		    	$num_glod = M('user')->where('level=20 AND disable=0 AND addtime<"'.$addtime.'"')->count();
		    	$num_king = M('user')->where('level=30 AND disable=0 AND addtime<"'.$addtime.'"')->count();
		    	if(intval($num_vip)>0){
		    		$price_vip = sprintf("%3.f",floatval($price*0.2)/intval($num_vip));
		    		$user_vip = M('user')->where('level=10 AND disable=0 AND addtime<"'.$addtime.'"')->select();
		    		foreach ($user_vip as $k => $v) {
			    		$tmp1['distribut_money'] = floatval($v['distribut_money']) + floatval($price_vip);
			    		M('user')->where('id='.intval($v['id']))->save($tmp1);
			    	}
		    	}
		    	if(intval($num_glod)>0){
		    		$price_glod = sprintf("%2.f",floatval($price*0.3)/intval($num_glod));
		    		$user_glod = M('user')->where('level=20 AND disable=0 AND addtime<"'.$addtime.'"')->select();
		    		foreach ($user_glod as $k => $v) {
			    		$tmp2['distribut_money'] = floatval($v['distribut_money']) + floatval($price_glod);
			    		M('user')->where('id='.intval($v['id']))->save($tmp2);
			    	}
		    	}
		    	if(intval($num_king)>0){
		    		$price_king = sprintf("%2.f",floatval($price*0.4)/intval($num_king));
			    	$user_king = M('user')->where('level=30 AND disable=0 AND addtime<"'.$addtime.'"')->select();	
			    	foreach ($user_king as $k => $v) {
			    		$tmp3['distribut_money'] = floatval($v['distribut_money']) + floatval($price_king);
			    		M('user')->where('id='.intval($v['id']))->save($tmp3);
			    	}
		    	}
            	// $order_pro=M("order_product")->where("order_id=".$orderid)->getField("pid");
            	// if($order_pro){
            	// 	M("product")->where("id=".$order_pro)->setInc("shiyong",1);
            	// }
            	//进行分销分润逻辑
            	$uid=$order->where('order_sn="'.trim($arr['out_trade_no']).'"')->getField("uid");
            	//1.判断是否是首单
            	if($this->_checkIsFirstOrder($uid)){
            		//是的话走首单流程
            		$this->_getMoney_firstbuy($uid,$orderid);
            	}else{
            		//否则走复购流程
            		$this->_getMoney_rebuy($uid,$orderid);
            	}

            	//返回xml格式的通知回去
            	$return = array('return_code'=>'SUCCESS','return_msg'=>'OK');
		        $re_xml = '<xml>';
		        foreach($return as $k=>$v){
		            $re_xml.='<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
		        }
		        $re_xml.='</xml>';

		        echo $re_xml;
            }else{
            	echo "fail";
            }
		}else{
			echo "fail";
		}
    }

    public function test(){
    	//进行分销分润逻辑
    	$uid=21;
    	$orderid=11;
    	//1.判断是否是首单
    	if($this->_checkIsFirstOrder($uid)){
    		//是的话走首单流程
    		//dump(1);exit;
    		$this->_getMoney_firstbuy($uid,$orderid);
    	}else{
    		// dump(2);exit;
    		//否则走复购流程
    		$this->_getMoney_rebuy($uid,$orderid);
    	}
    }
    public function test2(){
    	$re=$this->_findUserJingxiao(31);
    	dump($re);exit;
    }

    public function dowxpay2(){
    	//引入文件
    	header('Access-Control-Allow-Origin: *');
		header('Content-type: text/plain');
		vendor("wxpay.wxpay");
		//支付完成后的回调处理页面
		$notify_url = C("wxpay.wx_notify_url");
		
		$uid = intval($_REQUEST['uid']);
		if (!$uid) {
			echo json_encode(array('status'=>0,'err'=>'登录状态异常.'));
			exit();
		}

		$order_id = intval($_REQUEST['order_id']);//订单id
		$order = M('order')->where("id=".intval($order_id)." AND del=0")->find();
		if (!$order) {
			echo json_encode(array('status'=>0,'err'=>'订单信息错误.'));
			exit();
		}

		$order_sn = trim($order['order_sn']);//订单号

		$body = '全返通网会员开通！';
		// $body = '';
		// foreach ($product as $key => $val) {
		// 	if ($key==0) {
		// 		$body .=$val['name'];
		// 	}else{
		// 		$body .=','.$val['name'];
		// 	}
		// }
		//检测是否为VIP及以上,是的话就打7.5折;
		// if($this->_getUserLevel($uid)>=10){
		// 	$order['price'] = $order['price'] *0.75;
		// }
		// 获取支付金额		
		$total = $order['price']*100;     // 转成分
		// $total = 0.01*100;
		// 商品名称
		$subject = '小程序:'.$body;
		// 订单号，示例代码使用时间值作为唯一的订单ID号
		$out_trade_no = $order_sn;

		//获取用户openid
		//$tools = new \JsApiPay();
		$openId = M("user")->where("del=0 AND id=".$uid)->getField("openid");
		if(!$openId){
			echo json_encode(array('status'=>0,'err'=>'网络错误.'));
			exit();
		}
		//配置微信支付参数
		define( "_APPID",C("weixin.appid"));
		define( "_MCHID",C("weixin.mchid"));
		define( "_KEY", C("weixin.key"));
		define( "_APPSECRET", C("weixin.secret"));

		//统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($body);                        //商品名称
		$input->SetAttach("小程序"); 
		$input->SetOut_trade_no($out_trade_no);  //订单号
		$input->SetTotal_fee($total);                       //订单总金额
		$input->SetTime_start(date("YmdHis"));           //订单生成时间
		$input->SetTime_expire(date("YmdHis", time() + 600)); //订单失效时间
		$input->SetGoods_tag('');                       //设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
		$input->SetNotify_url($notify_url);             //异步通知地址
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order_data = \WxPayApi::unifiedOrder($input); 

		// foreach($order_data as $key=>$value){
	 //        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
	 //    }
		//{"appid":"wx0411fa6a39d61297","noncestr":"3reA4pSqGBPPryEL","package":"Sign=WXPay","partnerid":"1230636401","prepayid":"wx20170406175858b261288c050041764688","timestamp":1491472738,"sign":"4A50B67D47E062A1F3B739D76C683D37"}	
		//{"appid":"wx05a63e391546d945","noncestr":"Uvxaj0yZeE5uw0Wd","package":"Sign=WXPay","partnerid":"1460680002","prepayid":"wx201704221807041af49e25c90779187614","timestamp":1492855550,"sign":"08FB66188544D438DF074F815EB585DF"} 	
		$array=array(
			"appId"	   =>$order_data['appid'],
			"timeStamp"=>(string)time(),
			"nonceStr" =>$order_data['nonce_str'],
			"package"  =>"prepay_id=".$order_data['prepay_id'],
			"signType" =>"MD5",
		);
		$str = 'appId='.$array['appId'].'&nonceStr='.$array['nonceStr'].'&package='.$array['package'].'&signType=MD5&timeStamp='.$array['timeStamp'];
		//重新生成签名
		$array['paySign']=strtoupper(md5($str.'&key='.\WxPayConfig::KEY));
	
		echo json_encode(array('status'=>1,'success'=>$array));
		//exit();

    }

    public function test_price() {
    	//将新注册的会员的费用分给其他会员
    	$uid = 7;
    	$price = 1;
    	$addtime = M('user')->where('id='.intval($uid))->getField('addtime');
    	$num_vip = M('user')->where('level=10 AND disable=0 AND addtime<"'.$addtime.'"')->count();
    	$num_glod = M('user')->where('level=20 AND disable=0 AND addtime<"'.$addtime.'"')->count();
    	$num_king = M('user')->where('level=30 AND disable=0 AND addtime<"'.$addtime.'"')->count();
    	if(intval($num_vip)>0){
    		$price_vip = sprintf("%3.f",floatval($price*0.2)/intval($num_vip));
    		$user_vip = M('user')->where('level=10 AND disable=0 AND addtime<"'.$addtime.'"')->select();
    		foreach ($user_vip as $k => $v) {
	    		$tmp1['distribut_money'] = floatval($v['distribut_money']) + floatval($price_vip);
	    		M('user')->where('id='.intval($v['id']))->save($tmp1);
	    	}
    	}
    	if(intval($num_glod)>0){
    		$price_glod = sprintf("%2.f",floatval($price*0.3)/intval($num_glod));
    		$user_glod = M('user')->where('level=20 AND disable=0 AND addtime<"'.$addtime.'"')->select();
    		foreach ($user_glod as $k => $v) {
	    		$tmp2['distribut_money'] = floatval($v['distribut_money']) + floatval($price_glod);
	    		M('user')->where('id='.intval($v['id']))->save($tmp2);
	    	}
    	}
    	if(intval($num_king)>0){
    		$price_king = sprintf("%2.f",floatval($price*0.4)/intval($num_king));
	    	$user_king = M('user')->where('level=30 AND disable=0 AND addtime<"'.$addtime.'"')->select();	
	    	foreach ($user_king as $k => $v) {
	    		$tmp3['distribut_money'] = floatval($v['distribut_money']) + floatval($price_king);
	    		M('user')->where('id='.intval($v['id']))->save($tmp3);
	    	}
    	}	
    }
	
}
?>