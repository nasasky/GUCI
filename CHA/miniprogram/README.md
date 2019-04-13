小程序【爱乐查】
===================================  
   
运行项目
-----------------------------------  
 #### 下载微信web开发者工具，clone项目到本地，用微信web开发者打开项目，模拟器上（编译或CTRL+S）查看项目
      git clone https://git.oschina.net/dave_hai/XiaoChengXuAiLeCha.git

gh_f6b5fea530a2_430 (1).jpg![二维码](https://git.oschina.net/uploads/images/2017/0911/221143_006dfb71_453823.png "屏幕截图.png")


 #### 最后模拟器上效果如下：
 ![首页](https://git.oschina.net/uploads/images/2017/0911/220257_e83ec141_453823.png "IMG_0210.PNG")
![银行卡归属地](https://git.oschina.net/uploads/images/2017/0911/220322_fe30f78e_453823.png "IMG_0211.PNG")
![银行汇率](https://git.oschina.net/uploads/images/2017/0911/220353_2eb79065_453823.png "IMG_0213.PNG")
  
其中一个API，代码都有：

> ### 银行卡归属地（S100）

1. ##### 请求方式：[POST](http://www.zhaotool.com/markdown.html#)
2. ##### 请求地址：[http://www.zhaotool.com/v1/api/lt/{key}/{q}](http://www.zhaotool.com/v1/api/lt/{key}) 
3. ##### 数据格式：JSON 
4. ##### 请求参数： {\#请求参数}

| 参数 | 类型 | 描述 |
| :--- | :--- | :--- |
| key | varchar | 你的密钥 |
| q | varchar | 手机号码 |

5. ##### 请求示例： http://www.zhaotool.com/v1/api/lt/e10adc3949ba59abbe56e057f20f883e/6210300009958544

* ##### 返回Body：

```
{
    "code": "0",
    "data": {
        "bankName": "北京银行",
        "cardType": "京卡借记卡-借记卡",
        "logo": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKC9k=........",
        "site": "http://www.bankofbeijing.com.cn",
        "bankMobile": "95526",
        "logoType": "BASE64",
        "bankCard": "621030000995XXXX"
    },
    "sid": "S100"
}
```

* ##### 返回说明：

最后打个广告，我弄的开发和设计师的优秀导航网站：http://www.zhaotool.com
![输入图片说明](https://git.oschina.net/uploads/images/2017/0911/220841_0264cfb9_453823.png "屏幕截图.png")


