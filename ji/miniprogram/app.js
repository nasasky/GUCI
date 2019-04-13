//app.js
App({
  onLaunch: function () {
    //调用API从本地缓存中获取数据
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)
    wx.getSystemInfo({
      success: function(res) {
        this.globalData.windowWidth = res.windowWidth;
        wx.setStorageSync('windowWidth', res.windowWidth)
        console.log(res)
    }.bind(this)
  })
  },
  onShow: function  (){
    console.log('onShow被触发，小程序显示到前台');
  },
  onHide: function (){
    console.log('onHide被触发，小程序隐藏到后台');
  },
  onError: function (error){
    console.error('onError被触发，小程序发生错误'+error);
  },
  getUserInfo:function(cb){
    var that = this
    if(this.globalData.userInfo){
      typeof cb == "function" && cb(this.globalData.userInfo)
    }else{
      //调用登录接口
      wx.login({
        success: function () {
          wx.getUserInfo({
            success: function (res) {
              that.globalData.userInfo = res.userInfo
              typeof cb == "function" && cb(that.globalData.userInfo)
            }
          })
        }
      })
    }
  },
  statistics: function(obj){
    var book = wx.getStorageSync('book') || [];
    var statistics = wx.getStorageSync('statistics') || {};
    var key = [obj.year,obj.month,obj.date].join('');
    if(statistics.hasOwnProperty(key)){
      statistics[key] = parseFloat(statistics[key]) + parseFloat(obj.money);
    }else{
      statistics[key] = obj.money;
    }
    wx.setStorageSync('statistics', statistics)
  },
  statisticsChange : function(timestamp,old_money,new_money,uniqueDateKey){
    console.log(timestamp,new_money,old_money,uniqueDateKey)
    var statistics = wx.getStorageSync('statistics') || {};
    statistics[uniqueDateKey] = parseFloat(statistics[uniqueDateKey]) + parseFloat(new_money) - parseFloat(old_money);
    wx.setStorageSync('statistics', statistics)
  },
  globalData:{
    windowWidth:0,
    globalTest:'I am Global Text',
    userInfo:null,
    typeItem:[{
      name:'一般',
      background:'red',
      iconUrl:'../../imgs/pencil.png',
      code: 1
    },{
      name:'用餐',
      background:'green',
      iconUrl:'../../imgs/baby_food.png',
      code: 2
    },{
      name:'手机',
      background:'red',
      iconUrl:'../../imgs/learning2.png',
      code: 3
    },{
      name:'营养',
      background:'green',
      iconUrl:'../../imgs/milk.png',
      code: 4
    },{
      name:'时事',
      background:'red',
      iconUrl:'../../imgs/newspaper.png',
      code: 5
    },{
      name:'交通',
      background:'green',
      iconUrl:'../../imgs/train.png',
      code: 6
    },{
      name:'衣物',
      background:'red',
      iconUrl:'../../imgs/clothes.png',
      code: 7
    },{
      name:'旅行',
      background:'green',
      iconUrl:'../../imgs/camera.png',
      code: 8
    },{
      name:'学习',
      background:'red',
      iconUrl:'../../imgs/pencilSelect.png',
      code: 9
    },{
      name:'交友',
      background:'green',
      iconUrl:'../../imgs/sum.png',
      code: 10
    },{
      name:'进步',
      background:'red',
      iconUrl:'../../imgs/sumSelect.png',
      code: 11
    }]
  }
})
