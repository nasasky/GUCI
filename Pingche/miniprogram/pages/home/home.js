// pages/home/home.js

import {Home} from 'home-model.js';
var home = new Home();

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
        //加载主页数据
        this._loadData();
    },
    
    //加载主页数据的方法
    _loadData: function(){

        //获取首页banner轮播图数据并绑定到视图层wxml文件上
        var id = 1;
        home.getBannerData(id,(res)=>{             
            this.setData({
                'bannerArr':res
            });      
        });  

    //获取首页精选主题数据并绑定到视图层wxml文件上
        home.getThemeData((res)=>{            
            this.setData({
                'themeArr':res
            });
        })

    //获取首页最近新品数据并绑定到视图层wxml文件上
        home.getProductsData((res)=>{            
            this.setData({
                'productsArr':res
            });
        })
          
    },

    //跳转到单个商品详情页面点击事件处理方法    
    onProductsItemTap: function (event) {
        //获取组件标签上的小程序页面跳转参数id
        var id = home.getDataSet(event, 'id');
        wx.navigateTo({
            url:'../product/product?id=' + id
        });
    },

    //跳转到精选主题页面点击事件处理方法    
    onThemesItemTap: function (event) {
        //获取组件标签上的小程序页面跳转参数id
        var id = home.getDataSet(event, 'id');
        var name = home.getDataSet(event, 'name');        
        wx.navigateTo({
            url:'../theme/theme?id=' + id + '&name=' + name
        });
    }
    

   
})