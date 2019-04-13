<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>后台管理</title>
<link href="/minihaochecppj/Public/ht/css/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/minihaochecppj/Public/ht/js/jquery.js"></script>
<script type="text/javascript" src="/minihaochecppj/Public/ht/js/action.js"></script>
</head>
<body>

<div class="aaa_pts_show_1">【 店铺管理 】</div>

<div class="aaa_pts_show_2">
    <div>
       <div class="aaa_pts_4"><a href="<?php echo U('index');?>">全部店铺</a></div>
    </div>
    <div class="aaa_pts_3">
      <div class="pro_4 bord_1">
         <div class="pro_5">商家名称：<input type="text" class="inp_1 inp_6" id="name" value="<?php echo ($name); ?>"></div>
         <div class="pro_5">
               推荐店铺：
               <select class="inp_1 inp_6" id="tuijian">
			      <option value="">全部店铺</option>
                  <option value="1" <?php echo ($tuijian=='1' ? 'selected=selected' : NULL); ?>>推荐店铺</option>
                  <option value="0" <?php echo ($tuijian=='0' ? 'selected=selected' : NULL); ?>>非推荐店铺</option>
	           </select>
         </div>
         
         <div class="pro_5">
               所在城市：
               <select class="inp_1 inp_3 inp_6" id="sheng" style="width:80px;" onchange="china_city_ajax(this.value,'city')">
			            <option value="">省份</option>
				          <?php echo ($output_sheng); ?>
               </select>
               <select class="inp_1 inp_6" id="city" style="width:80px;" onchange="china_city_ajax(this.value,'quyu')">
			            <option value="">城市</option>
                  <?php echo ($output_city); ?>
               </select>
               <select class="inp_1 inp_6"  id="quyu"  style="width:80px;">
			            <option value="">区</option>
                  <?php echo ($output_quyu); ?> 
               </select>
         </div>
         
         <div class="pro_6"><input type="button" class="aaa_pts_web_3" value="搜 索" style="margin:0;" onclick="product_option(0);"></div>
      </div>
      
      <table class="pro_3">
         <tr class="tr_1">
           <td style="width:80px;">ID</td>
           <td style="width:100px;">图片</td>
           <td>商家名称</td>
           <td style="width:100px;">负责人</td>
           <td style="width:120px;">所在地</td>
           <!-- <td style="width:100px;">推荐</td> -->
           <td style="width:260px;">操作</td>
         </tr>
         <tbody id="news_option">
         <!-- 遍历 -->
          <?php if(is_array($shangchang)): $i = 0; $__LIST__ = $shangchang;if( count($__LIST__)==0 ) : echo "暂时没有店铺，赶紧进行招商吧" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr data-id="<?php echo ($v["id"]); ?>" data-name="<?php echo ($v["name"]); ?>">
             <td><?php echo ($v["id"]); ?></td>
             <td><img src="/minihaochecppj/Data/<?php echo ($v["logo"]); ?>" class="img_1"/></td>
             <td><?php echo ($v["name"]); ?></td>
             <td><?php echo ($v["uname"]); ?></td>
             <td><a href="javascript:void(0);" title="<?php echo ($v["address_xq"]); ?>"><?php echo ($v["zn-sheng"]); ?></a></td>
             <!-- <td><?php if($v["type"] == 1): ?><label style="color:green;">推荐</label><?php endif; ?></td> -->
             <td class="obj_1">
             <!--  <a href="<?php echo U('set_tj');?>?shop_id=<?php echo ($v["id"]); ?>&page=<?php echo ($page); ?>&name=<?php echo ($name); ?>&shop_id=<?php echo ($shop_id); ?>&tuijian=<?php echo ($tuijian); ?>">推荐</a> | -->
             <a href="<?php echo U('set_user');?>?shop_id=<?php echo ($v["id"]); ?>">账号设置</a> | 
              <!-- <a href="<?php echo U('Order/order_count');?>?shop_id=<?php echo ($v["id"]); ?>">销售统计</a> |
              <a href="<?php echo U('Post/post');?>?shop_id=<?php echo ($v["id"]); ?>" target="_blank">快递设置</a> | -->
              <a href="<?php echo U('Shangchang/add');?>?id=<?php echo ($v["id"]); ?>&page=<?php echo ($page); ?>&name=<?php echo ($name); ?>&type=<?php echo ($type); ?>&sheng=<?php echo ($v["sheng"]); ?>&city=<?php echo ($v["city"]); ?>&quyu=<?php echo ($v["quyu"]); ?>&tiaojian=<?php echo ($v["tuijian"]); ?>">修改</a> |
              <a onclick="del_id_urls(<?php echo ($v["id"]); ?>)"><?php if($v["status"] == 0): ?>恢复<?php else: ?>禁用<?php endif; ?></a>
             </td>
            </tr><?php endforeach; endif; else: echo "暂时没有店铺，赶紧进行招商吧" ;endif; ?>
         <!-- 遍历 -->
         </tbody>
         <tr>
            <td colspan="10" class="td_2">
               <?php echo ($page_index); ?>     
             </td>-
         </tr>
      </table>      
    </div>
    
</div>
<script>
//*******************************************
//搜索极品数据组装，解决下一页跟不上节奏
var type='<?php echo ($type); ?>';
//搜索的
function product_option(page){
  var obj={
     "name":$("#name").val(),
     "level":$("#level").val(),
     "type":'<?php echo ($type); ?>',
     "sheng":$("#sheng").val(),
     "city":$("#city").val(),
     "quyu":$("#quyu").val(),
     "tuijian":$("#tuijian").val(),
    }
  var url='?page='+page;
  $.each(obj,function(a,b){
    url+='&'+a+'='+b;
   });
  location=url; 
}
//*********************************************

//分页的
// function product_option(page){
//    window.location.href='?page='+page+'&message='+$("#message").val()
// }

//更改按钮
if(type=='xz'){
	$('.obj_1').html('<input type="button" value="选 择" class="aaa_pts_web_3" style="margin:3px 0;" onclick="window_opener(this)">');
}

//选择返回
function window_opener(e){
  var obj=$(e);
  window.opener.document.getElementById('shop_id').value=obj.parent().parent().attr('data-id');
  window.opener.document.getElementById('partner').value=obj.parent().parent().attr('data-name');
  
  window.close();
}

//区域选择
function china_city_ajax(id,obj_id){
   $('#district').html('<option value="">区</option>');
   $.ajax({
		 url:'<?php echo U("Public/china_city");?>',
		 type:'GET',
		 timeout:30000,
		 data:{'id':id},
		 dataType:"json",
		 error: function(){
			$('#loding').hide();
			alert('请求失败，请检查网络');
		 },
		 success:function(data){
			var text=obj_id=='city' ? '<option value="">城市</option>' : '<option value="">区</option>';
			$.each(data,function (a,b){
				text+='<option value="'+b.id+'">'+b.name+'</option>';
			});
			$('#'+obj_id).html(text);
		 }
	 });
}

function del_id_urls (id) {
  if (confirm('您确定要执行此操作吗？')) {
    location.href="<?php echo U('del');?>?did="+id;
  };
}
</script>
</body>
</html>