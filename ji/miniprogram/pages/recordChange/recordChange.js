//recordChange.js
//获取应用实例
var app = getApp()
Page({
  data: {
    zhichu: false,
    noteIconSrc: '../../imgs/note.png',
    typeItem:app.globalData.typeItem,
    calculateKey:[{
      text: 1,
      number: 1,
      code: 1
    },{
      text: 2,
      number: 2,
      code: 2
    },{
      text: 3,
      number: 3,
      code: 3
    },{
      text: 4,
      number: 4,
      code: 4
    },{
      text: 5,
      number: 5,
      code: 5
    },{
      text: 6,
      number: 6,
      code: 6
    },{
      text: 7,
      number: 7,
      code: 7
    },{
      text: 8,
      number: 8,
      code: 8
    },{
      text: 9,
      number: 9,
      code: 9
    },{
      text: '清零',
      number: '清零',
      code: 'zero'
    },{
      text: 0,
      number: 0,
      code: 0
    },{
      text: '.',
      number: '.',
      code: 'dot'
    }],
    inOutSwitch: true,
    typeIconSrcSelected: '',
    typeNameSelected: '',
    typeCodeSelected:'',
    typeBgColorSelected:'',
    inputMoney: '0'
  },
  changeType: function(e){
    console.log(e.currentTarget.dataset.typeBgColor);
    this.setData({
      typeNameSelected : e.currentTarget.dataset.typeName,
      typeCodeSelected : e.currentTarget.dataset.typeCode,
      typeIconSrcSelected : e.currentTarget.dataset.typeIconSrc,
    })
  },
  inputMoney: function(e){
    if(e.currentTarget.dataset.inputNumber === 'zero'){
      this.setData({
        inputMoney : '0'
      })
    }else if(e.currentTarget.dataset.inputNumber === 'dot'){
      if(this.data.inputMoney.indexOf('.')<0){
        this.setData({
          inputMoney : this.data.inputMoney.toString() + '.'
        })
      }
    }else{
      var money = this.data.inputMoney;
      if(money === '0'){
        money = [e.currentTarget.dataset.inputNumber].join('');
      }else{
        money = [money,e.currentTarget.dataset.inputNumber].join('');
      }
      this.setData({
        inputMoney : money
      })
    }
  },
  inOutChange: function(e){
    this.setData({
      inOutSwitch:e.currentTarget.dataset.switch
    })
  },
  saveCharge: function(e){
    if(this.data.inputMoney !== '0'){
      var book = wx.getStorageSync('book') || [];
      var d = new Date();
      if(this.data.inputMoney.indexOf('.') == this.data.inputMoney.length-1){
        this.setData({
          inputMoney: this.data.inputMoney.slice(0,this.data.inputMoney.length-1)
        })
      }
      for(var i =0; i< book.length; i++){
        if(this.data.timestamp == book[i].timestamp){
          book[i].money = this.data.inputMoney;
          book[i].typeCode = this.data.typeCodeSelected;
        }
      }
      wx.setStorageSync('book', book);
      app.statisticsChange(this.data.timestamp,this.data.oldMoney,this.data.inputMoney,this.data.uniqueDateKey)
      // var obj = {
      //   book:this.data.inOutSwitch,
      //   // typeName:this.data.typeNameSelected,
      //   typeCode:this.data.typeCodeSelected,
      //   // typeBgColor: this.data.typeBgColorSelected,
      //   money:this.data.inputMoney,
      //   timestamp:d.getTime(),
      //   year:d.getFullYear(),
      //   month:d.getMonth(),
      //   date:d.getDate(),
      //   uniqueDateKey: parseInt([d.getFullYear(),d.getMonth(),d.getDate()].join(''))
      // };
      // book.unshift(obj);
      // wx.setStorageSync('book', book);
      wx.navigateBack({
        delta: 1
      })
      // app.statistics(obj)
    }else{
      // wx.navigateBack({
      //   delta: 1
      // })
    }
  },
  onLoad: function (options) {
    //url里面的参数可以直接通过options获取
    //options.title
    console.log('onLoad:recordChange',options)
    var types = app.globalData.typeItem;
    var typeNameSelected, typeCodeSelected, typeIconSrcSelected, inputMoney;
    types.map(function(item){
      if(item.code == options.typeCode){
        typeNameSelected = item.name;
        typeCodeSelected = item.code;
        typeIconSrcSelected = item.iconUrl
      }
    })
    this.setData({
      typeNameSelected : typeNameSelected,
      typeCodeSelected : typeCodeSelected,
      typeIconSrcSelected : typeIconSrcSelected,
      inputMoney: options.money,
      timestamp:options.timestamp,
      uniqueDateKey: options.uniqueDateKey,
      oldMoney:options.money
    })
  },
  onReady: function () {
    console.log('onReady:recordChange')

  },
  onShow: function () {
    console.log('onShow:recordChange')

  },
  onHide: function () {
    console.log('onHide:recordChange')
  },
  onUnload: function () {
    console.log('onUnload:recordChange')
  },
  onPullDownRefresh: function () {
    console.log('onPullDownRefresh:recordChange')
  },
  onReachBottom: function () {
    console.log('onReachBottom:recordChange')
  },
  onShareAppMessage: function () {
    console.log('onShareAppMessage:recordChange')
  },
})
