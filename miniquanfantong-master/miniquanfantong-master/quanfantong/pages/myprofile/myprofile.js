var app=getApp();
Page({
  data: {
    shengArr: [],//省级数组
    shengId: [],//省级id数组
    shiArr: [],//城市数组
    shiId: [],//城市id数组
    quArr: [],//区数组
    shengIndex: 0,
    shiIndex: 0,
    quIndex: 0,
    mid: 0,
    sheng: 0,
    city: 0,
    area: 0,
    code: 0,
    cartId: 0,
    info:[],
    shengname:''
   },
  bindPickerChange: function (e) {
    console.log('picker发送选择改变，携带值为', e.detail.value)
    this.setData({
      index: e.detail.value
    })
  },
  bindDateChange: function(e) {
    this.setData({
      date: e.detail.value
    })
  },
  // 表单提交
  reg: function (e) {
    console.log(e.detail.value);
    var that=this;
    var fdata=e.detail.value;
    wx.request({
      url: app.d.ceshiUrl + '/Api/User/saveUserInfo',
      data: {
        uid: app.globalData.userInfo.id,
        uname: fdata.uname,
        tel: fdata.tel,
        weixin: fdata.weixin,
        bankname: fdata.bankname,
        bankid: fdata.bankid,
        sex: that.data.sex,
        birthday: that.data.date ? that.data.date : that.data.info.birthday,
        sheng: that.data.sheng ? that.data.sheng : that.data.info.sheng,
        city: that.data.city ? that.data.city : that.data.info.city,
        quyu: that.data.area ? that.data.area : that.data.info.quyu,
      },
      header: {// 设置请求的 header
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: function (res) {
        if(res.data.status==1){
          wx.showToast({
            title: res.data.err,
          })
          setTimeout(function(){
            wx.navigateBack({ })
          },2000)
        }else{
          wx.showToast({
            title: res.data.err,
          })
        }
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 30000
        });
      },
    })
   
  },
  onLoad: function (options) {
    // 生命周期函数--监听页面加载
    var that = this;
    //获取用户信息
    wx.request({
      url: app.d.ceshiUrl + '/Api/User/getUserInfo',
      data: {
        uid: app.globalData.userInfo.id
      },
      header: {// 设置请求的 header
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: function (res) {
        console.log(res.data);
        var rdata=res.data.err;
        var _obj={};
        _obj.curBdIndex = rdata.sex;
        _obj.curHdIndex = rdata.sex;
        if(res.data.status==1){
          that.setData({
            info:rdata,
            tabArr: _obj,
            sex:rdata.sex
          })
        }
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 30000
        });
      },
    })
    //获取省级城市
    wx.request({
      url: app.d.ceshiUrl + '/Api/Address/get_province',
      data: {},
      method: 'POST',
      success: function (res) {
        var status = res.data.status;
        var province = res.data.list;
        var sArr = [];
        var sId = [];
        console.log(that.data.info.shengname);
        if (that.data.info.shengname){
          sArr.push(that.data.info.shengname);
        }else{
          sArr.push('请选择');
        }
        sId.push('0');
        for (var i = 0; i < province.length; i++) {
          sArr.push(province[i].name);
          sId.push(province[i].id);
        }
        that.setData({
          shengArr: sArr,
          shengId: sId
        })
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      },
    })

  },

  bindPickerChangeshengArr: function (e) {
    this.setData({
      shengIndex: e.detail.value,
      shiArr: [],
      shiId: [],
      quArr: [],
      quiId: []
    });
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Address/get_city',
      data: { sheng: e.detail.value },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {// 设置请求的 header
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        // success
        var status = res.data.status;
        var city = res.data.city_list;

        var hArr = [];
        var hId = [];
        hArr.push('请选择');
        hId.push('0');
        for (var i = 0; i < city.length; i++) {
          hArr.push(city[i].name);
          hId.push(city[i].id);
        }
        that.setData({
          sheng: res.data.sheng,
          shiArr: hArr,
          shiId: hId
        })
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      },

    })
  },
  bindPickerChangeshiArr: function (e) {
    this.setData({
      shiIndex: e.detail.value,
      quArr: [],
      quiId: []
    })
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Address/get_area',
      data: {
        city: e.detail.value,
        sheng: this.data.sheng
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {// 设置请求的 header
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var status = res.data.status;
        var area = res.data.area_list;

        var qArr = [];
        var qId = [];
        qArr.push('请选择');
        qId.push('0');
        for (var i = 0; i < area.length; i++) {
          qArr.push(area[i].name)
          qId.push(area[i].id)
        }
        that.setData({
          city: res.data.city,
          quArr: qArr,
          quiId: qId
        })
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
  },
  bindPickerChangequArr: function (e) {
    console.log(this.data.city)
    this.setData({
      quIndex: e.detail.value
    });
    var that = this;
    wx.request({
      url: app.d.ceshiUrl + '/Api/Address/get_code',
      data: {
        quyu: e.detail.value,
        city: this.data.city
      },
      method: 'POST', // OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
      header: {// 设置请求的 header
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        that.setData({
          area: res.data.area,
          code: res.data.code
        })
      },
      fail: function () {
        // fail
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      }
    })
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
      tabArr: _obj,
      sex: _datasetId
    });
  },

});