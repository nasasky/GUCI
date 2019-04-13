//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
    indicatorDots: true,
    autoplay: true,
    interval: 3000,
    duration: 1000,
    circular: true,
    page:2
  },

  site: function (e) {
    var id=e.currentTarget.dataset.id;
    wx.navigateTo({
      url: '../product/product?id='+id,
      success: function (res) { },
      fail: function (res) { },
      complete: function (res) { },
    })
  },
  onLoad: function () {
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Index/index',
      method: 'post',
      data: {
        uid: app.globalData.userInfo.id,
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var info = res.data.info;
        console.log(info);
        if(info){
          if(info.tel == ''){
            wx.redirectTo({
              url: '../sign/sign',
            });
            return false;
          }
          if(info.level == 0){
            wx.redirectTo({
              url: '../user/user',
            });
            return false;
          }
        }else{
          wx.redirectTo({
            url: '../sign/sign',
          });
          return false;
        }
        var ggtop = res.data.ggtop;
        var sitelist= res.data.sitelist;
        //that.initProductData(data);
        that.setData({
          imgUrls: ggtop,
          sitelist: sitelist
        });
        //endInitData
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 30000
        });
      },
    })
  },
  //点击加载更多
  getMore: function (e) {
    var that = this;
    var page = that.data.page;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Index/getlist',
      method: 'post',
      data: { page: page },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var sitelist = res.data.sitelist;
        if (sitelist == '') {
          wx.showToast({
            title: '没有更多数据！',
            duration: 2000
          });
          return false;
        }
        //that.initProductData(data);
        that.setData({
          page: page + 1,
          sitelist: that.data.sitelist.concat(sitelist)
        });
        //endInitData
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
  },
})
