var app=getApp();
Page({
  data:{

  },
  onLoad: function (e) {
    var that=this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Web/getkfinfo',
      method: "post",
      data: {

      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
          console.log(res.data)
          if(res.data.status==1){
            that.setData({
              info: res.data.err
            })
          }else{
            wx.showToast({
              title: res.data.err,
            })
          }
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 30000
        });
      },
    })

  },
  calling: function () {
    wx.makePhoneCall({
      phoneNumber: this.data.info.tel, //此号码并非真实电话号码，仅用于测试
      success: function () {
        console.log("拨打电话成功！")
      },
      fail: function () {
        console.log("拨打电话失败！")
      }
    })
  },

    // 弹窗
   setModalStatus: function (e) {
    console.log("设置显示状态，1显示0不显示", e.currentTarget.dataset.status);
    var animation = wx.createAnimation({
      duration: 200,
      timingFunction: "linear",
      delay: 0
    })
    this.animation = animation
    animation.translateY(300).step()
    this.setData({
      animationData: animation.export()
    })
    if (e.currentTarget.dataset.status == 1) {
      this.setData(
        {
          showModalStatus: true
        }
      );
    }
    setTimeout(function () {
      animation.translateY(0).step()
      this.setData({
        animationData: animation
      })
      if (e.currentTarget.dataset.status == 0) {
        this.setData(
          {
            showModalStatus: false
          }
        );
      }
    }.bind(this), 200)
  },
  bindFormSubmit:function (e){
     var fdata = e.detail.value;
     if(!fdata.message || !fdata.tel){
       wx.showToast({
         title: '请填补充完内容再提交!谢谢!',
       })
       return false;
     }
     wx.request({
       url: app.d.ceshiUrl + '/Api/Web/upfankui',
       method: "post",
       data: {
          uid:app.globalData.userInfo.id,
          message: fdata.message,
          tel:fdata.tel
       },
       header: {
         'Content-Type': 'application/x-www-form-urlencoded'
       },
       success: function (res) {
          if(res.data.status==1){
            wx.showToast({
              title: res.data.err,
            })
            setTimeout(function(){
              wx.navigateBack();
            },2000); 
          }else{
            wx.showToast({
              title: res.data.err,
            })
          }
       },
       fail: function (e) {
         wx.showToast({
           title: '网络异常！',
           duration: 30000
         });
       },
     })

  }
})