var app = getApp();
var util = require('../../utils/util.js');
var page = 1;
Page({
  data: {
    seecomment: false,
    reply: ''
  },
  add: function () {
    wx.navigateTo({
      url: '/pages/dynamic/add'
    })
  },
  previeimg: function (e) {
    var that = this;
    console.log(e);
    wx.previewImage({
      current: e.currentTarget.id,
      urls: that.data.list[e.currentTarget.dataset.name].img
    })
  },
  getList: function () {
    var that = this;
    util.req('dynamic/getList', { page: page, sk: app.globalData.sk }, function (data) {
      var list = data.list;
      if (page == 1) {
        var arr = new Array();
      } else {
        var arr = that.data.list;
      }

      list.forEach(function (item) {
        var li = {
          avatarUrl: item.avatarUrl,
          content: item.content,
          id: item.id,
          img: JSON.parse(item.img),
          nickName: item.nickName,
          time: util.getDateBiff(item.time * 1000),
          zan: item.zan,
          comments: item.comment
        }
        arr.push(li);
      })
      that.setData({ list: arr });
    })
  },
  del: function (e) {
    var that = this;
    wx.showModal({
      title: '删除提示',
      content: '确定删除?',
      success: function (res) {
        if (res.confirm) {
          var list = that.data.list;
          var id = list[e.target.dataset.id].id;
          util.req('dynamic/del',{id:id,sk:app.globalData.sk},function(data){
            if(data.status == '1'){
              list.splice(e.target.dataset.id,1);
              console.log(list);
              that.setData({list:list});
              wx.showToast({
                title: '删除成功',
              })
            }else{
              util.isError(data.msg,that);
            }
          })
        }
      }
    })
  },
  onLoad: function (options) {
    this.getList();
  },
  onReachBottom: function () {
    if (!this.data.nomore) {
      page++;
      this.getList();
    }
  },
  seecomment: function (e) {
    console.log(e);
    var reply = (!e.target.dataset.name) ? '' : '回复' + e.target.dataset.name;
    this.setData({
      'reply': reply,
      'seecomment': true,
      'nowid': e.currentTarget.id
    });
  },
  comment: function (e) {
    var that = this;
    var content = e.detail.value;
    if (content == '') {
      return false;
    }
    util.req('comment/add', {
      'iid': that.data.list[that.data.nowid].id,
      'reply': (that.data.reply).replace('回复', ''),
      'type': 'dynamic',
      'content': e.detail.value,
      'sk': app.globalData.sk
    }, function (data) {
      if (data.status == 1) {
        var list = that.data.list;
        list[that.data.nowid].comments.unshift({ id: data.id, iid: that.data.list[that.data.nowid].id, content: e.detail.value, nickName: app.globalData.userInfo.nickName, reply: (that.data.reply).replace('回复', '') })
      }
      that.setData({ list: list });
    })
  },
  hidecomment: function () {
    this.setData({ 'seecomment': false });
  },
  onPullDownRefresh: function () {
    page = 1;
    this.getList();
    wx.stopPullDownRefresh();
  }
})