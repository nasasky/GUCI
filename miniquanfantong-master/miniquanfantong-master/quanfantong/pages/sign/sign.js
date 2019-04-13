// pages/sign/sign.js
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    VerifyCode: '点击获取',
    lol: true,
    checkboxItems: [
      { name: 'USA', value: '我已了解同意' },
    ],
    content: '本网站所提供的信息，只供参考之用。本网站及其雇员一概毋须以任何方式就任何信息传递或传送的失误、不准确或错误对用户或任何其他人士负任何直接或间接的责任。在法律允许的范围内，本网站在此声明,不承担用户或任何人士就使用或未能使用本网站所提供的信息或任何链接或项目所引致的任何直接、间接、附带、从属、特殊、惩罚性或惩戒性的损害赔偿（包括但不限于收益、预期利润的损失或失去的业务、未实现预期的节省）。本网站所提供的信息，若在任何司法管辖地区供任何人士使用或分发给任何人士时会违反该司法管辖地区的法律或条例的规定或会导致本网站或其第三方代理人受限于该司法管辖地区内的任何监管规定时，则该等信息不宜在该司法管辖地区供该等任何人士使用或分发给该等任何人士。用户须自行保证不会受限于任何限制或禁止用户使用或分发本网站所提供信息的当地的规定。本网站图片，文字之类版权申明，因为网站可以由注册用户自行上传图片或文字，本网站无法鉴别所上传图片或文字的知识版权，如果侵犯，请及时通知我们，本网站将在第一时间及时删除'

  },
  //提交
  bindFormSubmit: function (e) {
    var that = this;
    //去除两边空格
    var trim = function (str) {
      return str.replace(/(^\s*)|(\s*$)/g, "");
    };
    // 号码验证
    var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    console.log(e);
    var fdata = e.detail.value;
    var phone = fdata.phone;
    if (trim(phone).length == 0) {
      wx.showToast({
        icon: 'phone',
        title: '请输入手机号码'
      })
      return false;
    }
    if (!reg.test(trim(phone))) {
      wx.showToast({
        icon: 'phone',
        title: '请输入正确的手机号码'
      })
      return false;
    }

    wx.request({
      url: app.d.ceshiUrl + '/Api/User/user_reg',
      method: 'post',
      data: {
        uid: app.globalData.userInfo.id,
        tel:fdata.phone,
        yzcode:fdata.yzcode
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      success: function (res) {
        var status = res.data.status;
        var info = res.data.info;
        if (status == 1) {
          wx.showToast({
            title: '提交成功！',
            duration: 2000
          });
          if(info){
            if(info.level == 0){
              setTimeout(function (e) {
                wx.navigateTo({
                  url: '../user/user'
                })
              },2300);
              return false;
            }else{
              setTimeout(function (e) {
                wx.navigateTo({
                  url: '../index/index'
                })
              }, 2300);
              return false;
            }
          }
          
        } else {
          wx.showToast({
            title: res.data.err,
            duration: 2000
          });
        }
      },
      fail: function (e) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000
        });
      },
    })
  },

  // 声明
  powerDrawer: function (e) {
    var currentStatu = e.currentTarget.dataset.statu;
    this.util(currentStatu)
  },
  util: function (currentStatu) {
    /* 动画部分 */
    // 第1步：创建动画实例 
    var animation = wx.createAnimation({
      duration: 200, //动画时长 
      timingFunction: "linear", //线性 
      delay: 0 //0则不延迟 
    });

    // 第2步：这个动画实例赋给当前的动画实例 
    this.animation = animation;

    // 第3步：执行第一组动画 
    animation.opacity(0).rotateX(-100).step();

    // 第4步：导出动画对象赋给数据对象储存 
    this.setData({
      animationData: animation.export()
    })

    // 第5步：设置定时器到指定时候后，执行第二组动画 
    setTimeout(function () {
      // 执行第二组动画 
      animation.opacity(1).rotateX(0).step();
      // 给数据对象储存的第一组动画，更替为执行完第二组动画的动画对象 
      this.setData({
        animationData: animation
      })
      //关闭 
      if (currentStatu == "close") {
        this.setData(
          {
            showModalStatus: false
          }
        );
      }
    }.bind(this), 200)

    // 显示 
    if (currentStatu == "open") {
      this.setData(
        {
          showModalStatus: true
        }
      );
    }
  },
  checkboxChange: function (e) {
    var checked = e.detail.value
    var changed = {}
    console.log(this.data.checkboxItems[0].name)
    if (checked.indexOf(this.data.checkboxItems[0].name) !== -1) {
      changed['checkboxItems[0].checked'] = true
    } else {
      changed['checkboxItems[0].checked'] = false
    }
    this.setData(changed)
    console.log(changed)
  },



  //手机输入框遗失光标则获取value然后把数据放入this.data.linkTel中去
  blurTel: function (e) {
    var linkTel = e.detail.value.replace(/\s/g, "");
    this.setData({
      linkTel: linkTel
    })
  },
  //点击发送验证码，获取手机号码，往后台发送数据
  setVerify: function (e) {
    var linkTel = this.data.linkTel;
    if(!linkTel || linkTel=="undefined"){
      wx.showToast({
        title: '请输入手机号码!',
      })
      return false;
    }

    var _Url = app.d.ceshiUrl + '/Api/User/get_code';

    var total_micro_second = 60 * 1000;    //表示60秒倒计时，想要变长就把60修改更大
    //验证码倒计时
    count_down(this, total_micro_second);
    this.setData({
      lol: false
    })
    wx.request({
      url: _Url,
      method: 'POST',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        tel: linkTel
      },
      success: function (res) {
        if (res.data.status == 1) {
          wx.showModal({
            title: '提示',
            content: '发送验证码成功！',
            showCancel: false
          })
        }else {
          wx.showToast({
            title: res.data.err,
            duration: 2000,
          })
        }
      },
      fail: function (res) {
        wx.showToast({
          title: '网络异常！',
          duration: 2000,
        })
      },

    });
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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



//下面的代码在page({})外面
/* 毫秒级倒计时 */
function count_down(that, total_micro_second) {
  if (total_micro_second <= 0) {
    that.setData({
      VerifyCode: "重新发送",
      lol: true
    });
    // timeout则跳出递归
    return;
  }

  // 渲染倒计时时钟
  that.setData({
    VerifyCode: date_format(total_micro_second) + " 秒"
  });

  setTimeout(function () {
    // 放在最后--
    total_micro_second -= 10;
    count_down(that, total_micro_second);
  }, 10)



}

// 时间格式化输出，如03:25:19 86。每10ms都会调用一次
function date_format(micro_second) {
  // 秒数
  var second = Math.floor(micro_second / 1000);
  // 小时位
  var hr = Math.floor(second / 3600);
  // 分钟位
  var min = fill_zero_prefix(Math.floor((second - hr * 3600) / 60));
  // 秒位
  var sec = fill_zero_prefix((second - hr * 3600 - min * 60));// equal to => var sec = second % 60;
  // 毫秒位，保留2位
  var micro_sec = fill_zero_prefix(Math.floor((micro_second % 1000) / 10));

  return sec;
}

// 位数不足补零
function fill_zero_prefix(num) {
  return num < 10 ? "0" + num : num
}