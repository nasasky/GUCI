<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class PublicController extends Controller {
    public static $num;
  	public static $str;
    //构造函数
    public function _initialize(){
	    //php 判断http还是https
    	$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
    	//所有图片路径
	    define(__DATAURL__, $http_type.$_SERVER['SERVER_NAME'].__DATA__.'/');
	    define(__PUBLICURL__, $http_type.$_SERVER['SERVER_NAME'].__PUBLIC__.'/');
	    define(__HTTP__, $http_type);
    }
   
    //查找二级分类下的所有子分类id，用逗号拼接
    public function catid_tree($id=2){
		$Category = M('category');
		$list=$Category->where("tid=".$id)->order('sort desc,id asc')->select();
		//dump($list);exit;
		$cidstr='';
		foreach($list as $v){
			$json[]=$v['id'];
			$num=$Category->where("tid=".$v['id'])->field('id')->count();
			if($num>0){
				$json[]=$this->catid_tree($v['id']);
			}
		}
		$cidstr.=implode(',',$json);
		return $cidstr;		
	}
	//一次性查出产品分类的所有分类
	public function cat_tree($id=2){
		$Category = M('category');
		$list=$Category->where("tid=".$id)->field('id,tid,name')->order('sort desc,id asc')->select();
		//echo '<pre>';print_r($list);exit;
		foreach($list as $v){
			$num = $Category->where("tid=".$v['id'])->count();
			$subclass=array();
			if($num>0)
			{
				$subclass=$this->cat_tree($v['id']);
			}
			$json[]=array(
				'id' => $v['id'] ,
				'name' => $v['name'] ,
				'num' => $num ,
				'subclass' => $subclass,
			);
		}
		return $json;		
	}
	//导航部分  查找父级分类
    function getAllFcateIds($categoryID)
    {
        //初始化ID数组
        $array[] = $categoryID;
         
        do
        {
            $ids = '';
            $where['id'] = array('in',$categoryID);
            $cate = M('category')->where($where)->field('id,tid,name')->select();
           // echo M('aaa_cpy_category')->_sql();
            foreach ($cate as $v)
            {
                $array[] = $v['tid'];
                $ids .= ',' . $v['tid'];
            }
            $ids = substr($ids, 1, strlen($ids));
            $categoryID = $ids;
        }
        while (!empty($cate));
       // $cates=array();
        foreach ($array as $key=>$va){
           $cates[] = M('category')->where('id='.$va)->field('id,tid,name')->find();
          // echo M('aaa_cpy_category')->_sql();
		  //echo $cates[$key]['name'];
		   $cates[$key]['name']=str_replace('（系统分类，不要删除）','',$cates[$key]['name']);
        }
        array_pop($cates);
        $ca=array_reverse($cates);
		//echo "<pre>";
	   // print_r($ca);
        return $ca; //返回数组
    }
    
	public function ispc($val){
		//$val = 1850;//这个为admin_app的id
		$app = M('admin_app');
		$val=$app->getField('id');
		//$url = $app->db(2,DB)->where('id='.$val)->field('ispcshop,end_time,name,pcnav_color,ahover_color')->find();
		$url = $app->where('id='.$val)->field('ispcshop,end_time,name,pcnav_color,ahover_color')->find();
		//print_r($url);exit;
		//return $url;
		
		if($url['end_time'] > time()){
			return $url;
		}else{
			return 0;
		}
    }
    /**针对涂屠生成唯一订单号
	*@return int 返回16位的唯一订单号
	*/
	public function build_order_no(){
		return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	}

	/**
     * [checkshopmoney 检查店铺的保证金]
     * @return [type] [description]
     */
    public function checkshopmoney($shopid){
        $user=M("user");
        @$re=$user->where("shop_id=".$shopid." AND del=0")->find();
        if($re){
        	if($re['shop_money']>0){
				return $re['shop_money'];
        	}else{
				return false;
        	}           
        }else{
            return false;
        }
    }
   /**
    * 验证手机号是否正确
    * @author honfei
    * @param number $mobile
    */
    function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }
    /*
	*
	* 图片上传的公共方法
	*  $file 文件数据流 $exts 文件类型 $path 子目录名称
	*/
	public function upload_images($file,$exts,$path){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =  2097152 ;// 设置附件上传大小2M
		$upload->exts      =  $exts;// 设置附件上传类型
		$upload->rootPath  =  './Data/UploadFiles/'; // 设置附件上传根目录
		$upload->savePath  =  ''; // 设置附件上传（子）目录
		$upload->saveName = time().mt_rand(100000,999999); //文件名称创建时间戳+随机数
		$upload->autoSub  = true; //自动使用子目录保存上传文件 默认为true
		$upload->subName  = $path; //子目录创建方式，采用数组或者字符串方式定义
		// 上传文件 
		$info = $upload->uploadOne($file);
		if(!$info) {// 上传错误提示错误信息
		    return $upload->getError();
		}else{// 上传成功 获取上传文件信息
			//return 'UploadFiles/'.$file['savepath'].$file['savename'];
			return $info;
		}
	}
	/**
	 * [_checkIsFirstOrder 查询是否是首单]
	 * @param  [type] $uid [description]
	 * @return [bool]      [bool]
	 */
	public function _checkIsFirstOrder($uid){
		$re=M("user")->where("id=$uid")->getField("firstbuy");
		if($re==0){
			//符合首购条件，返回ture
			return true;
		}else{
			//不符合首购条件，返回false
			return false;
		}
	}
	/**
	 * [_checkIsTenNum 查询是否订单大于10盒]
	 * @param  [type] $orderid [description]
	 * @return [type]          [description]
	 */
	public function _checkIsTenNum($orderid){
		$re=M("order")->where("id=$orderid")->getField("product_num");
		if(intval($re)>=10){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * [_getUserLevel 查询会员当前等级]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function _getUserLevel($uid){
		$re=M("user")->where("id=$uid")->getField("level");
		return $re;
	}
	/**
	 * [_findUserFuwus 查找当前会员的服务商出来]
	 * @param  [type]  $uid [description]
	 * @param  integer $lv  [description]
	 * @return [type]       [description]
	 */
	public function _findUserFuwus($uid){
		$parentid=M("user")->where("id=".$uid)->getField("first_leader");
		if($parentid>0){
			$parentlv=M("user")->where("id=".$parentid)->getField("level");
			if($parentlv>=20){
				return $parentid;	
			}else{
				return $this->_findUserFuwus($parentid);
			}	
		}else{
			return 0;
		}		
	}
	/**
	 * [_findUserJingxiao 查找当前会员的团队经销商出来]
	 * @param  [type] $uid [description]
	 * @return [type]      [id]
	 */
	public function _findUserJingxiao($uid){
		$parentid=M("user")->where("id=".$uid)->getField("first_leader");
		if($parentid>0){
			$parentlv=M("user")->where("id=".$parentid)->getField("level");
			if($parentlv>=30){
				return $parentid;	
			}else{
				return $this->_findUserJingxiao($parentid);
			}
			
		}else{
			return 0;
		}		
	}

	/**
	 * [_getMoney_firstbuy 首单奖励接口]
	 * @param  [type] $uid     [description]
	 * @param  [type] $orderid [description]
	 * @return [type]          [description]
	 */
	public function _getMoney_firstbuy($uid,$orderid){
		$user=M("user");
		$order=M("order");

		//1判断是否满10盒，并标记已经消费首单资格了
		if($this->_checkIsTenNum($orderid)){
			$data['level']=10;
			$data['firstbuy']=1;
			$user->where("id=$uid")->save($data);
			$this->_check_level_up($uid);
		}else{
			$user->where("id=$uid")->setField("firstbuy",1);
		}
		//获得订单总价
		$orderprice=$order->where("id=$orderid")->getField("price");
		//2看有没上级，有上级则上级获得奖励
		@$firleaderid=$user->where("id=$uid")->getField("first_leader");
		if($firleaderid>0){
			
			if($this->_getUserLevel($firleaderid)>=10){
				//如果是VIP以上的就享受7.5折后的50%分成
				$money1=$orderprice*0.5;
			}else{
				//如果是普通会员就享受7.5折后的10%分成
				$money1=$orderprice*0.1;

			}
			$user->where("id=$firleaderid")->setInc("distribut_money",floatval($money1));
			$this->_make_dmoney_log($firleaderid,$money1,"【首】【ID:".$uid."】直接会员首单消费总额为".$orderprice."元,共获得￥".$money1."的佣金奖励,您当前等级为".$this->_getUserLevelName($firleaderid)."");
		}
		//3看有没间接的上级,只有服务商等级以上才能享受间接会员级别的奖励加成加成是5%;
		@$secleaderid=$user->where("id=$uid")->getField("second_leader");
		if($secleaderid>0){
			if($this->_getUserLevel($secleaderid)>=20){
				$money2=$orderprice*0.75*0.05;
				$user->where("id=$secleaderid")->setInc("distribut_money",floatval($money2));
				$this->_make_dmoney_log($secleaderid,$money2,"【首】【ID:".$uid."】间接会员首单消费总额为".$orderprice."元,共获得￥".$money2."的佣金奖励,您当前等级为".$this->_getUserLevelName($secleaderid)."");
			}
		}
		//4查看我的上面的第一位统领我的经销商,该经销商获得我的订单3%的分成;
		$jingxiao=$this->_findUserJingxiao($uid);
		if($jingxiao>0){
			$money3=$orderprice*0.75*0.03;
			$user->where("id=$jingxiao")->setInc("distribut_money",floatval($money3));
			$this->_make_dmoney_log($jingxiao,$money3,"【首】【ID:".$uid."】团队会员首单总额为".$orderprice."元,共获得￥".$money3."的佣金奖励,您当前等级为".$this->_getUserLevelName($jingxiao)."");
		}
	}
	/**
	 * [_getMoney_rebuy 复购奖励接口]
	 * @param  [type] $uid     [description]
	 * @param  [type] $orderid [description]
	 * @return [type]          [description]
	 */  
	public function _getMoney_rebuy($uid,$orderid){
		$user=M("user");
		$order=M("order");
		//1检测还未成为VIP的用户
		$userLV=$this->_getUserLevel($uid);
		if($userLV<10){
			if($this->_checkIsTenNum($orderid)){
				$user->where("id=$uid")->setField("level",10);
				$this->_check_level_up($uid);
			}
		}
		//获得订单总价
		$orderprice=$order->where("id=$orderid")->getField("price");
		//2看有没上级，有上级则上级获得奖励 不管是普通会员还是VIP及以上 统一获得7.5折后的10%
		@$firleaderid=$user->where("id=$uid")->getField("first_leader");
		if($firleaderid>0){
			$money1=$orderprice*0.75*0.1;
			$user->where("id=$firleaderid")->setInc("distribut_money",floatval($money1));
			$this->_make_dmoney_log($firleaderid,$money1,"【ID:".$uid."】直接会员(".$this->_getUserLevelName($uid).")消费".$orderprice."元,您共获得￥".$money1."的佣金奖励,您当前等级为".$this->_getUserLevelName($firleaderid)."");
		}
		//3看有没间接的上级,只有服务商等级以上才能享受间接会员级别的奖励加成加成是5%;
		@$secleaderid=$user->where("id=$uid")->getField("second_leader");
		if($secleaderid>0){
			if($this->_getUserLevel($secleaderid)>=20){
				$money2=$orderprice*0.75*0.05;
				$user->where("id=$secleaderid")->setInc("distribut_money",floatval($money2));
				$this->_make_dmoney_log($secleaderid,$money2,"【ID:".$uid."】间接会员(".$this->_getUserLevelName($uid).")消费".$orderprice."元,您共获得￥".$money2."的佣金奖励,您当前等级为".$this->_getUserLevelName($secleaderid)."");
			}
		}
		//4查看我的上面的第一位统领我的经销商,该经销商获得我的订单3%的分成;
		$jingxiao=$this->_findUserJingxiao($uid);
		if($jingxiao>0){
			$money3=$orderprice*0.75*0.03;
			$user->where("id=$jingxiao")->setInc("distribut_money",floatval($money3));
			$this->_make_dmoney_log($jingxiao,$money3,"【ID:".$uid."】团队会员(".$this->_getUserLevelName($uid).")消费".$orderprice."元,您共获得￥".$money3."的佣金奖励,您当前等级为".$this->_getUserLevelName($jingxiao)."");
		}

	}

	/**
	 * [_make_dmoney_log 佣金获得记录日志]
	 * @param  [type] $uid    [description]
	 * @param  [type] $money  [description]
	 * @param  [type] $digest [description]
	 * @return [bool]         [description]
	 */
	public function _make_dmoney_log($uid,$money,$digest){
		$distribut_money_log=M("distribut_money_log");
		$data['user_id']=$uid;
		$data['distribut_money']=$money;
		$data['digest']=$digest;
		$data['addtime']=time();
		$re=$distribut_money_log->add($data);
		if($re){return true;}else{return false;}
	}
	/**
	 * [_check_level_up 检测现在的]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function _check_level_up($uid){
		@$parentid=M("user")->where("id=$uid")->getField("first_leader");
		if($parentid>0){
			$parentlv=$this->_getUserLevel($parentid);
			if($parentlv>=10 && $parentlv<20){
				$this->_make_vip_levelup($parentid,$parentlv);
			}else{
				//经销商、服务商和普通用户就不做处理了
			}
		}
	}
	/**
	 * [_make_vip_levelup VIP升级]
	 * @param  [type] $uid   [description]
	 * @param  [type] $level [description]
	 * @return [type]        [description]
	 */
	public function _make_vip_levelup($uid,$level){
		M("user")->where("id=$uid")->setInc("level",1);
		if($level+1>19){
			@$parentid=$this->_findUserFuwus($uid);
			if($parentid>0){
				$parentlv=$this->_getUserLevel($parentid);
				if($parentlv>=20 && $parentlv<30){
					$this->_make_fuwus_levelup($parentid,$parentlv);
				}
			}
		}
	}
	/**
	 * [_make_fuwus_levelup 服务商升级]
	 * @param  [type] $uid   [description]
	 * @param  [type] $level [description]
	 * @return [type]        [description]
	 */
	public function _make_fuwus_levelup($uid,$level){
		M("user")->where("id=$uid")->setInc("level",1);
		if($level+1>29){
			//这里跟上面的不一样，这里会搜索我团队的经销商出来。
			@$parentid=$this->_findUserJingxiao($uid);
			if($parentid>0){
				$parentlv=$this->_getUserLevel($parentid);
				if($parentlv>=30){
					$this->_make_jingxiao_levelup($parentid);
				}
			}
		}
	}
	/**
	 * [_make_jingxiao_levelup 经销商升级]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function _make_jingxiao_levelup($uid){
		if($uid>0){
			M("user")->where("id=$uid")->setInc("level",1);
		}
	}
	public function _getUserAllMoney_total($uid){
		$log=M("distribut_money_log")->where("user_id=$uid AND del=0")->field("distribut_money")->select();
		$money=0;
		if($log){
			foreach ($log as $k => $v) {
				$money=$money+$v['distribut_money'];
			}
			return $money;
		}else{
			return 0;
		}
		
	}
	public function _getUserAllMoney_log($uid){
		$log=M("distribut_money_log")->where("user_id=$uid AND del=0")->order("addtime desc")->limit(200)->select();
		if($log){
			foreach ($log as $k => $v) {
				$log[$k]['addtime']=date("Y-m-d H:i",$v['addtime']);
			}
			return $log;
		}else{
			return false;
		}

	}

	public function _getMyTeamMember($uid){
		$userLv=$this->_getUserLevel($uid);
		if($userLv<20){
			$vipMember=M("user")->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->count();
			return $vipMember;
		}elseif($userLv>=20 && $userLv<30){
			$fuwusMember1=M("user")->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->count();
			$fuwusMember2=M("user")->where("second_leader=$uid AND del=0 AND qx=6 AND level<30")->count();
			return $fuwusMember1+$fuwusMember2;
		}else{
			self::$num=0;
			$jingxiaoMember=$this->_getJingxiaoMember($uid,self::$num);
			return $jingxiaoMember;
		}
	}

	public function _getJingxiaoMember($uid,$num){
		// $list=M("user")->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->field("first_leader")->select();
		// if($list){
		// 	foreach ($list as $k => $v) {
		// 		$list[$k]["sub"]=$this->_getJingxiaoMember($v['first_leader']);
		// 	}
		// }
		// return $list;
		$count=M('user')->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->count('id');
      	self::$num=$num+$count;
      	$sub=M('user')->field('id,first_leader')->where("first_leader=$uid AND del=0 AND qx=6 AND level<30")->select();
      	foreach ($sub as $k => $v) {
        	if($v['id']){
         		 $this->_getJingxiaoMember($v['id'],self::$num);
        	}
      	}
     	return self::$num;
	}
	/**
	 * [_getLevelInfo 获取等级等信息接口]
	 * @param  [type] $level [description]
	 * @return [type]        [description]
	 */
	public function _getLevelInfo($level){
		if($level>=10){
			$re['arr']=array();
			if($level>=10 && $level<20){
				$re['lvname']="VIP会员";
				if($level>10){
					for($i=0;$i<($level-10);$i++){
						$re['arr'][$i]=1;
					}
				}
			}elseif($level>=20 && $level<30){
				$re['lvname']="服务商";
				if($level>20){
					for($i=0;$i<($level-20);$i++){
						$re['arr'][$i]=1;
					}
				}
			}elseif($level>=30 && $level<40){
				$re['lvname']="经销商";
				if($level>20){
					for($i=0;$i<($level-30);$i++){
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
		return $re;
	}
	/**
	 * [_getUserLevelName 获取用户等级名称]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function _getUserLevelName($uid){
		$lv=$this->_getUserLevel($uid);
		if($lv<10){
			return "普通会员";
		}elseif($lv>=10 && $lv<20){
			return "VIP会员";
		}elseif($lv>=20 && $lv<30){
			return "服务商";
		}else{
			return "经销商";
		}
	}
	/**
	 * [_getLevelName 获取等级名称]
	 * @param  [type] $lv [description]
	 * @return [type]     [description]
	 */
	public function _getLevelName($lv){
		if($lv<10){
			return "普通会员";
		}elseif($lv>=10 && $lv<20){
			return "VIP会员";
		}elseif($lv>=20 && $lv<30){
			return "服务商";
		}else{
			return "经销商";
		}
	}
	public function _getAccessToken(){
		static $access_token;
		$appid=C("weixin.appid");
	    $secret=C("weixin.secret");
	    $access_token = S($token.'weixin_access_token');
	    if($access_token) { //已缓存，直接使用
	        return $access_token;
	    } else { //获取access_token
	        $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
	        // 使用CURL
	        $ch1 = curl_init ();
	        $timeout = 5;
	        curl_setopt ( $ch1, CURLOPT_URL, $url_get );
	        curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
	        curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
	        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
	        $accesstxt = curl_exec ( $ch1 );
	        curl_close ( $ch1 );
	        $access = json_decode ( $accesstxt, true );  //将access_token转换为数组
	        // 缓存数据7000秒
	        S($token.'weixin_access_token',$access['access_token'],7000);
	        return $access['access_token'];
	    }
	}
	/**
	 * [send_post 组装请求数据]
	 * @param  [type] $url       [description]
	 * @param  [type] $post_data [description]
	 * @param  string $method    [description]
	 * @return [type]            [description]
	 */
	public function send_post($url, $post_data,$method='POST') {
        $postdata = http_build_query($post_data);
        $options = array(
          'http' => array(
            'method' => $method, //or GET
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
          )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
    /**
     * [api_notice_increment 发送curl请求]
     * @param  [type] $url  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function api_notice_increment($url, $data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        //     var_dump($tmpInfo);
        //    exit;
        if (curl_errno($ch)) {
          return false;
        }else{
          // var_dump($tmpInfo);
          return $tmpInfo;
        }
    }
    
    /* 发送json格式的数据，到api接口 -xzz0704  */
    public function https_curl_json($url,$data,$type){
        if($type=='json'){//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
            $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
            $data=json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        return $output;
    }
    /**
     * [_getcash_msg 生成提现日志]
     * @param  [type] $logid [description]
     * @param  [type] $msg   [description]
     * @return [type]        [description]
     */
    public function _getcash_msg($logid,$msg){
    	$data['log_id']=$logid;
    	$data['message']=$msg;
    	$data['addtime']=time();
    	$re=M("getcash_msg")->add($data);
    	if($re){
    		return true;
    	}else{
    		return false;
    	}
    }
}