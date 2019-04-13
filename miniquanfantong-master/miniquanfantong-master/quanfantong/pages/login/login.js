//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    motto: 'Hello World',
    userInfo: {}
  },


  //提交
  bindFormSubmit: function (e) {
     var that = this;
     //去除两边空格
     var trim = function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
     };
     // 号码验证
     var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
     console.log(e);

     var phone = e.detail.value.cb;
     var phones = e.detail.value.ce;
     if (trim(phone).length == 0) {
        wx.showToast({
           icon: 'phone',
           title: '请输入手机号码'
        })
        return false;
     }
     if (!reg.test(trim(phone))) {
        wx.showToast({
           icon: 'phone',
           title: '请输入正确的手机号码'
        })
        return false;
     }

     //   wx.request({
     //      url: app.api.hostUrl + '/Api/User/study',
     //      method: 'post',
     //      data: {
     //         pro_id: playId,
     //         uid: app.api.userId,
     //      },
     //      header: {
     //         'Content-Type': 'application/x-www-form-urlencoded'
     //      },
     //      success: function (res) {
     //         var status = res.data.status;
     //         if (status == 1) {
     //            wx.showToast({
     //               title: '提交成功！',
     //               duration: 2000
     //            });
     //            setTimeout(function (e) {
     //               wx.navigateTo({
     //                  url: '../music/music?objectId=' + playId
     //               })
     //            });
     //         } else {
     //            wx.showToast({
     //               title: res.data.err,
     //               duration: 2000
     //            });
     //         }
     //      },
     //      fail: function (e) {
     //         wx.showToast({
     //            title: '网络异常！',
     //            duration: 2000
     //         });
     //      },
     //   })
  },
  //事件处理函数
   zhuche:function(){
       wx.navigateTo({
          url: '../sign/sign',
          success: function(res) {},
          fail: function(res) {},
          complete: function(res) {},
       })
   },
  onLoad: function () {
    console.log('onLoad')
    var that = this
    //调用应用实例的方法获取全局数据
  }
})
