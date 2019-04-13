// pages/ping/ping.js
var app = getApp();
Page({
  /**
   * 页面的初始数据
   */
  data: {
    tabArr: {
      curHdIndex: 0,
      curBdIndex: 0
    },
    kk: [{}, {}],
    info:'',
    tixian:0,
    keyong:0,
    price:0,

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

  yj:function(){
wx.navigateTo({
  url: '../yj/yj',
  success: function(res) {},
  fail: function(res) {},
  complete: function(res) {},
})
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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
      url: app.d.ceshiUrl + '/Api/User/getUser2',
      method: 'post',
      data: {
        uid: app.globalData.userInfo.id,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var info = res.data.info;
        var tixian = res.data.tixian;
        var keyong = res.data.keyong;
        var total = res.data.total;
        that.setData({
          info:info,
          tixian:tixian,
          keyong:keyong,
          total:total
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

  ti:function () {
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Account/getcash',
      method: 'post',
      data: {
        uid: app.globalData.userInfo.id,
        money:that.data.price,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
       var status = res.data.status;
       if(status == 1){
         wx.showToast({
           title: res.data.err,
           duration: 3000
         });
         wx.redirectTo({
           url: '../extension_centre/extension_centre',
         })
       }else{
         wx.showToast({
           title: res.data.err,
           duration: 3000
         });
       }
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 3000
        });
      },
    })
  },

  getPrice:function (event) {
    var price = event.detail.value;
    this.setData({
      price:price,
    });
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