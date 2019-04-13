//index.js
//获取应用实例
var app = getApp()
Page({
  data: {
      isToSumPage: false,
      animationData:{},
      userAvatarAnimation:{},
      hasUserAvatarTap : false,
      bookLength:0,
      records:[],
      userAvatarTouchParams:{},
      statistics:{}
    },
  //事件处理函数
  userAvatarTap: function(e) {
    if(this.data.longTap){
      setTimeout(()=>{
        wx.navigateTo({
          url: '../logs/logs'
        })
      },500)
      var animation = wx.createAnimation({
        duration: 1000,
        timingFunction: 'ease'
      })
      this.animation = animation
      var left = app.globalData.windowWidth / 2;
      animation.scale(1).left(left).top(55).translate(-35, 35).step()
      this.setData({
        animationData:animation.export(),
        longTap: false
      })
    }else{
      if(!this.data.hasUserAvatarTap){
        var animation = wx.createAnimation({
          duration: 1000,
          timingFunction: 'ease'
        })
        this.animation = animation
        animation.rotate(180).translate(260, -32).step()
        this.setData({
          animationData:animation.export(),
          hasUserAvatarTap: true
        })
        setTimeout(()=>{
          wx.navigateTo({
            url: '../mark/mark'
          })
        },250)
      }else{
        var animation = wx.createAnimation({
          duration: 1000,
          timingFunction: 'ease'
        })
        this.animation = animation
        animation.step()
        this.setData({
          animationData:animation.export(),
          hasUserAvatarTap: false
        })
      }
    }
  },
  recordChange:function(e){
    console.log(e)
    wx.navigateTo({
      url: '../recordChange/recordChange?timestamp=' + e.currentTarget.dataset.timestamp + '&typeCode='  + e.currentTarget.dataset.typeCode + '&money=' + e.currentTarget.dataset.money + '&uniqueDateKey=' + e.currentTarget.dataset.uniquedatekey
    })
  },
  userAvatarTouch: function(e){
    var startX = e.touches[0].clientX;
    this.setData({
      userAvatarTouchParams: Object.assign({},this.data.userAvatarTouchParams,{startX:startX})
    });
    console.log(this.data.userAvatarTouchParams);
  },
  userAvatarTouchMove: function(e){
    var currentX = e.touches[0].clientX;
    var animation = wx.createAnimation({
      duration: 20,
      timingFunction: 'linear',
    })
    var userAvatarAnimation = wx.createAnimation({
      duration: 20,
      timingFunction: 'linear',
    })
    this.animation = animation;
    this.userAvatarAnimation = userAvatarAnimation;
    var diff = currentX - this.data.userAvatarTouchParams.startX;
    if(!this.data.isToSumPage){
      if(diff > 0.3 * app.globalData.windowWidth){
          this.setData({ isToSumPage: true })
          wx.navigateTo({
            url: '../logs/logs'
          })
      }else{
        var left = -35 + diff;
        animation.translate(left,35).step();
        userAvatarAnimation.rotate(diff).step();
        this.setData({
          animationData:animation.export(),
          userAvatarAnimation: userAvatarAnimation.export()
        })
      }
    }
  },
  userAvatarTouchend: function(e){
    console.log('后台执行end')
    var animation = wx.createAnimation({
      duration: 500,
      timingFunction: 'linear',
    })
    var userAvatarAnimation = wx.createAnimation({
      duration: 500,
      timingFunction: 'linear',
    })
    this.animation = animation;
    this.userAvatarAnimation = userAvatarAnimation;
    animation.translate(-35,35).step();
    userAvatarAnimation.rotate(0).step();
      //console.log('头像动画');
      this.setData({
        animationData:animation.export(),
        userAvatarAnimation: userAvatarAnimation.export(),
        hasUserAvatarTap: false,
        isToSumPage: false
      });
  },
  onLoad: function (options) {
    //url里面的参数可以直接通过options获取
    //options.title
    //console.log('onLoad:index')
    var that = this
    //调用应用实例的方法获取全局数据
    app.getUserInfo(function(userInfo){
      //更新数据
      that.setData({
        userInfo:userInfo
      })
    })
  },
  onReady: function () {
    //console.log('onReady:index');
  },
  onShow: function () {
    //console.log('onShow:index');
    this.show10records();
    console.log(this.data.records,'sssss')
  },
  show10records:function(){
    var book = wx.getStorageSync('book') || [];
    var statistics = wx.getStorageSync('statistics') || {};

    if(book.length > 0){
      book = this.formatRecords(book);
      //console.log(book);
      this.setData({
        bookLength: book.length,
        records:book,
        statistics: statistics
      })
    }
  },
  onHide: function () {
    console.log('onHide:index');
    setTimeout(function(){
      this.userAvatarTouchend();
    }.bind(this),1000)
  },
  onUnload: function () {
    //console.log('onUnload:index')
  },
  onPullDownRefresh: function () {
    //console.log('onPullDownRefresh:index')
  },
  onReachBottom: function () {
    //console.log('onReachBottom:index')
  },
  onShareAppMessage: function () {
    //console.log('onShareAppMessage:index')
  },
  formatRecords:function(book){
    //console.log(book);
    var a = [];
    var types = app.globalData.typeItem;
    book.map((item)=>{
      for(var i=0; i<types.length;i++){
        var flag = false;
        //console.log(types[i].code,item.typeCode);
        if(types[i].code == item.typeCode){
          //console.log('相同');
          a.push({
            money: item.money,
            book: item.book,
            year: item.year,
            month: item.month,
            date: item.date,
            background: types[i].background,
            iconUrl: types[i].iconUrl,
            name: types[i].name,
            timestamp: item.timestamp,
            typeCode: item.typeCode,
            uniqueDateKey: item.uniqueDateKey
          })
          flag = true;
        }
        if(flag){
          break;
        }
      }
    })
    return a;
  }
})
