// pages/inf/inf.js
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    'headLineList': '',
    'page':0,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/News/lists',
      method: 'post',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data);
        //--init data 
        var headLineList = res.data.newslist;
        var page = res.data.page + 1;
          that.setData({
            headLineList: headLineList,
            page:page,
          });
        
      },
      error: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000,
        });
      },

    });
  },
  // 资讯
  jumpDetails: function (e) {
    console.log(e);
    var newId = e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '../news/news?newId='+newId,
      success: function (res) {
        // success
      },
      fail: function () {
        // fail
      },
      complete: function () {
        // complete
      }
    })
  },
  //加载更多
  loadMore:function () {
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/News/lists',
      method: 'post',
      data:{
        page:that.data.page,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        console.log(res.data);
        //--init data 
        var headLineList = res.data.newslist;
        if (headLineList == '') {
          wx.showToast({
            title: '没有更多数据！',
            duration: 2000
          });
          return false;
        }
        that.setData({
          headLineList: that.data.headLineList.concat(headLineList),
          page:that.data.page + 1,
        });

      },
      error: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000,
        });
      },

    });
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
  // onShareAppMessage: function () {
  //   return {
  //     title: '微信小程序联盟',
  //     desc: '最具人气的小程序开发联盟!',
  //     path: '/page/user?id=123'
  //   }
  // },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  }
})