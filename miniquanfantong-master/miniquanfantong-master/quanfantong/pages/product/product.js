// pages/product/product.js
var app=getApp();
//引入这个插件，使html内容自动转换成wxml内容
var WxParse = require('../../wxParse/wxParse.js');
var wxbarcode = require('../../utils/index.js');
Page({

   /**
    * 页面的初始数据
    */
   data: {

   },

   /**
    * 生命周期函数--监听页面加载
    */
   onLoad: function (options) {
        var that=this;
        var id=options.id;
        wx.request({
          url: app.d.ceshiUrl + '/Api/Site/detail',
          method: 'post',
          data: {
            id:id
          },
          header: {
            'content-type': 'application/x-www-form-urlencoded',
          },
          success: function (res) {
            if(res.data.status==1){
                var content=res.data.err.content;
                WxParse.wxParse('content', 'html', content, that, 5);
                if(res.data.err.myqrcode_img=="" || res.data.err.myqrcode_img=="undefined"){
                  var url = res.data.err.myqrcode_url;
                  console.log(url)
                  wxbarcode.qrcode('qrcode', url, 420, 420);
                }
                that.setData({
                  info:res.data.err
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
   preview:function (e){
      var url=e.currentTarget.dataset.url; 
      wx.previewImage({
        urls: [url],
      })
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
   onShareAppMessage: function () {

   }
})