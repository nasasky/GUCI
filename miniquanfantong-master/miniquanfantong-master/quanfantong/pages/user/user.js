// pages/user/user.js
var app = getApp()
Page({

   /**
    * 页面的初始数据
    */
   data: {
      items: [
         {
            value: 'Android',
             png: '../../images/v1.png',
            vip: 'VIP会员', 
            yuan: '68元',
            checked:'true'
         },
         {
            value: 'IOS',   png: '../../images/v2.png',
            vip: '钻石会员', yuan: '168元', 

         },
         {
            value: 'Web',   png: '../../images/v3.png',
            vip: '皇家会员', yuan: '268元',
            bot: 'border-bottom:1rpx solid #ccc; '
         },

      ],
      price:0,
      name:'',
   },
   radioChange: function (e) {
      console.log(e);
      var name = e.currentTarget.dataset.name;
      var price = e.currentTarget.dataset.price;
      this.setData({
        price: price,
        name: name
      })


   },
   /**
    * 生命周期函数--监听页面加载
    */
   onLoad: function (options) {
     var that = this;
     app.getUserInfo(function (userInfo) {
       //更新数据
       that.setData({
         userInfo: userInfo,
         loadingHidden: true
       })
     });
     console.log(this.data.userInfo);
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

   },


   zhifu: function () {
     
     //创建订单
     var that = this;
     console.log(this.data);
     wx.request({
       url: app.d.ceshiUrl + '/Api/Payment/payment2',
       method: 'post',
       data: {
         uid: app.globalData.userInfo.id,
         price: that.data.price,//总价
         name: that.data.name,
       },
       header: {
         'Content-Type': 'application/x-www-form-urlencoded'
       },
       success: function (res) {
         //--init data        
         var data = res.data;
         console.log(data);

         if (data.status == 1) {
           //创建订单成功     
             that.wxpay(data.arr);
         } else {
           wx.showToast({
             title: '支付失败！',
           })
         }
       },
     });

   },
   wxpay: function (order) {
     console.log(order.order_id);
     wx.showToast({
       title: "支付成功!",
       duration: 2000,
     })
     setTimeout(function () {
       wx.navigateTo({
         url: '../index/index',
       })
     }, 2500);
     return false;
     wx.request({
       url: app.d.ceshiUrl + '/Api/Pay/dowxpay2',
       data: {
         order_id: order.order_id,
         uid: app.globalData.userInfo.id,
       },
       method: 'POST',
       header: {
         'Content-Type': 'application/x-www-form-urlencoded'
       }, // 设置请求的 header
       success: function (res) {
         if (res.data.status == 1) {
           var order = res.data.success;
           console.log(order);
           wx.requestPayment({
             timeStamp: order.timeStamp,
             nonceStr: order.nonceStr,
             package: order.package,
             signType: 'MD5',
             paySign: order.paySign,
             success: function (res) {
               wx.showToast({
                 title: "支付成功!",
                 duration: 2000,
               })
               setTimeout(function () {
                 wx.navigateTo({
                   url: '../index/index',
                 })
               }, 2500);
             },
             fail: function () {
               wx.showToast({
                 title: "支付失败!",
               })
             }
           })
         }
       },
       fail: function () {
         // fail
       },
       complete: function () {
         // complete
       }
     })
   },
})