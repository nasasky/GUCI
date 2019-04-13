//logs.js
var util = require('../../utils/util.js')
Page({
  data: {
    logs: []
  },
  onLoad: function () {
    this.setData({
      logs: (wx.getStorageSync('logs') || []).map(function (log) {
        return util.formatTime(new Date(log))
      })
    })
  },
  onLoad: function (options) {
    console.log('onLoad:index')
    //调用应用实例的方法获取全局数据
    console.log(options.from)
  },
})
