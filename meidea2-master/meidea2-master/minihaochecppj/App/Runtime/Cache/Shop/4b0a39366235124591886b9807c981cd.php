<?php if (!defined('THINK_PATH')) exit(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minigueinon/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minigueinon/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minigueinon/Public/ht/js/action.js"></script>
</head>
<body>

<div class="aaa_pts_show_1">【 密码修改 】</div>

<div class="aaa_pts_show_2">
    
    <div class="aaa_pts_3">
      <form action="<?php echo U('password');?>" method="post" onsubmit="return ac_from();">
      <ul class="aaa_pts_5">
         <li><strong style="font-size:16px; color:#0c0;">用户登陆信息</strong></li>
         <li>
            <div class="d1">原始密码:</div>
            <div>
              <input type="password" class="inp_1" name="old_password" id="old_password"/>
            </div>
         </li>
         <li>
            <div class="d1">新密码:</div>
            <div>
              <input type="password" class="inp_1" name="password" id="password"/>
            </div>
            <div style="margin-left:8px;">* 为空时，将不更改密码</div>
         </li>
         <li>
            <div class="d1">确认密码:</div>
            <div>
              <input type="password" class="inp_1" name="password2" id="password2" />
            </div>
         </li>
         <li><input type="submit" name="submit" value="提交" class="aaa_pts_web_3" border="0"></li>
      </ul>
      </form>
         
    </div>
    
</div>
<script>
function ac_from(){
  var password=document.getElementById('password').value;
  var password2=document.getElementById('password2').value;
  if(password.length>0){
		if(password.length<6){
			alert('密码长度不能少于6');
			return false;
		 }else if(password!=password2){
			alert('两次输入的密码不一致！');
			return false;
		 }   
  }
  
}
</script>
</body>
</html>