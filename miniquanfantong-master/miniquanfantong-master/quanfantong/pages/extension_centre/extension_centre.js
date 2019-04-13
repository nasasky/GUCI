var app = getApp();
Page({


  data: {
     tabArr: {
        curHdIndex: 0,
        curBdIndex: 0
     },

  },
  // tab切换
  tabFun: function (e) {
     //获取触发事件组件的dataset属性 
     var _datasetId = e.target.dataset.id;
     console.log("----" + _datasetId + "----");
     var _obj = {};
     _obj.curHdIndex = _datasetId;
     _obj.curBdIndex = _datasetId;
     this.setData({
        tabArr: _obj
     });
  }, 
  // 佣金详细
  available_commission:function(){
    wx.navigateTo({
      url: '../available_commission/available_commission',
      success: function(res) {},
      fail: function(res) {},
      complete: function(res) {},
    })
  },
// 提现
 ti: function () {
    wx.navigateTo({
      url: '../balance/balance',
      success: function (res) { },
      fail: function (res) { },
      complete: function (res) { },
    })
  },



  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function () {

  },



 
  
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/User/getUser',
      method: 'post',
      data: {
        uid: app.globalData.userInfo.id,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data.huode);
        var info = res.data.info;
        var total = res.data.total;
        that.setData({
          info: info,
          total: total,
          huode:res.data.huode,
          tixian:res.data.tixian,
        });
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 30000
        });
      },
    })
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})