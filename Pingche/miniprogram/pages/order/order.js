// pages/order/order.js

import { Cart } from '../cart/cart-model.js';
//import { Order } from '../order/order-model.js';
import { Address } from '../../utils/address.js';

var cart = new Cart();
//var order = new Order();
var address = new Address();

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
        //订单的商品信息是购物车中提交的选中商品的信息
        var productsArr;
        this.data.account = options.account;
        

        //订单的商品信息是购物车中提交的选中商品的信息
        productsArr = cart.getCartDataFromLocal(true);

        this.setData({
            productsArr: productsArr,
            account: options.account,
            orderStatus: 0
        });
    },

    editAddress: function(){
        var that = this;
        wx.chooseAddress({
            success:function(res){

                //console.log(res);
                /* 2019年1月11日微信小程序API的wx.chooseAddress方法
                   在模拟器上返回的用户收货地址数据格式中的telNumber键名的值是带有-符号的电话号码
                   而我们的服务器端API要求的是手机号码,是没有带-的
                   要做处理转化手机号码
                   课程源代码在视频中授课的那一天从chooseAddress获得的是手机号码没带-符号
                   而我自己写的代码现在从chooseAddress获得的是电话号码带有-符号
                   另外县名的键名用的是英文的countyName
                   所以开发者一定要查看明确所调用接口返回的数据格式,否则很容易被坑死

                   以下是2019年1月11日在微信开发者工具中模拟器上微信小程序API的wx.chooseAddress方法返回的模拟收货地址json格式数据
                   {cityName:"广州市",countyName:"海珠区",detailInfo:"新港中路397号",errMsg:"chooseAddress:ok",nationalCode:"510000",postalCode:"510000",provinceName:"广东省",telNumber:"020-81167888",userName:"张三"}
                 */

                 //转换电话号码为手机号码 测试用, 正式上线项目中要确定telNumber的格式是不是手机号码格式
                 //视频课程源代码没有这个转换步骤,这是我自己加的
                 //所有用户收货地址手机号码统一改成字符串18798118175
                 res.telNumber = "18798118175";
                 //console.log(res);

                var addressInfo = {
                    name: res.userName,
                    mobile: res.telNumber,
                    totalDetail: address.setAddressInfo(res)
                };

                that._bindAddressInfo(addressInfo);

                //保存用户收货地址
                address.submitAddress(res,(flag) => {
                    if(!flag) {
                        that.showTips('操作提示', '地址信息更新失败! ');
                    }
                })
            }
        });
    },

    /* 
        提示窗口
        参数: title 标题
              content 内容
              flag 是否跳转到 "我的页面"
    */
    showTips: function (title, content, flag) {
        wx.showModal({
            title: title,
            content: content,
            showCancel: false, 
            success: function (res) {
                if (flag) {
                    wx.switchTab({
                        url: '/pages/my/my'
                    });
                }
            }
            
        });
    },

    /* 绑定地址信息 */
    _bindAddressInfo: function (addressInfo) {
        this.setData({
            addressInfo: addressInfo
        });
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