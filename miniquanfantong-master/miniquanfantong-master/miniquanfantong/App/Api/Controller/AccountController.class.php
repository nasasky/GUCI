<?php
namespace Api\Controller;
use Think\Controller;
class AccountController extends PublicController {
	/**
	 * [getcash 提现]
	 * @return [type] [description]
	 */
    public function getcash(){
    	$post=I("post.");
        $user=M("user");
    	if(!$post['uid']){
    		echo json_encode(array("status"=>0,"err"=>"参数不足!"));
    		exit;
    	}
        $userinfo=$user->where("id=".$post['uid'])->find();
        $username=$userinfo['uname'];
        $userMoney=$userinfo['distribut_money'];
        if(!$userinfo['bankname'] || !$userinfo['bankid'] || !$userinfo['uname']){
            echo json_encode(array("status"=>0,"err"=>"请完善您的银行卡信息和实名信息!"));
            exit;
        }
        if($post['money']>$userMoney){
            echo json_encode(array("status"=>0,"err"=>"您提现的金额超出您当前的余额!"));
            exit;  
        }
          
    	$data['user_id']=$post['uid'];
    	$data['money']=$post['money'];
    	$data['count']=$userinfo['bankname']."/".$userinfo['bankid'];
        $data['username']=$username;
    	$data['addtime']=time();
    	$data['type']="bank";
    	if(!$data['money']){
    		echo json_encode(array("status"=>0,"err"=>"请输入提现金额!"));
    		exit;
    	}
    	$re=M("getcash_log")->add($data);
    	if($re){
            $user->where("id=".$post['uid'])->setDec("distribut_money",$post['money']);
            $this->_getcash_msg($re,"提出申请,金额为￥".$post['money']);
            $this->_getcash_msg($re,"等待审核");
    		echo json_encode(array("status"=>1,"err"=>"提交成功！"));
    	}else{
    		echo json_encode(array("status"=>0,"err"=>"提交失败！"));
    	}
    }
    /**
     * [getcashlog 提现记录]
     * @return [type] [description]
     */
    public function getcashlog(){
        $uid=I("post.uid");
        if(!$uid){
            echo json_encode(array("status"=>0,"err"=>"参数不足!"));
            exit;
        }
        $getcash_log=M("getcash_log");
        $list=$getcash_log->where("user_id=$uid")->order("addtime desc,id desc")->select();
        if($list){
            foreach ($list as $k => $v) {
               if($v['del']==0){
                   if($v['status']==0){
                        $status="待审核";
                    }else{
                        if($v['finish']==0){
                            $status="已审核";
                        }else{
                            $status="已完成";
                        }
                    } 
                }else{
                    $status="已注销";
                }
                $list[$k]['status']=$status;
                $list[$k]['addtime']=date("Y-m-d",$v['addtime']);
            }
            echo json_encode(array("status"=>1,"err"=>$list));
        }else{
            echo json_encode(array("status"=>0,"err"=>"请求失败！"));
        }
        
    }
    /**
     * [getcashmsg 提现进度]
     * @return [type] [description]
     */
    public function getcashmsg(){
        $uid=I("post.uid");
        if(!$uid){
            echo json_encode(array("status"=>0,"err"=>"参数不足!"));
            exit;
        }
        $logid=I("request.logid");
        $re=M("getcash_msg")->where("log_id=$logid")->order("id asc,addtime desc")->select();
        foreach ($re as $k => $v) {
            $re[$k]['addtime']=date("m-d H:i",$v['addtime']);
        }
        if($re){
            echo json_encode(array("status"=>1,"err"=>$re));
        }else{
            echo json_encode(array("status"=>0,"err"=>"请求失败!"));
        }
    }   

}