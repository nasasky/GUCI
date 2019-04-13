import { Config } from 'config.js';

class Token {
    constructor(){
        this.verifyUrl = Config.restUrl + 'token/verify';
        this.tokenUrl = Config.restUrl + 'token/user';
    }

    verify() {
        var token = wx.getStorageSync('token');
        if(!token){
            this.getTokenFromServer();
        }
        else {
            this._verifyFromServer(token);
        }
    }

    /* 携带令牌去服务器校验令牌 */
    _verifyFromServer(token){
        var that =  this;
        wx.request({
            url: that.verifyUrl,
            method: 'POST',
            data: {
                token: token
            },
            success: function (res) {
                var valid = res.data.isValid;
                if(!valid){
                    //token令牌无效就去服务器端API重新请求有效的token令牌
                    that.getTokenFromServer();
                }
            }
        });
    }

    /* 从服务器端API获取token令牌 */
    getTokenFromServer(callBack){
        var that = this;
        wx.login({
            success: function (res) {
                wx.request({
                    url: that.tokenUrl,
                    method: 'POST',
                    data: {
                        code: res.code
                    },
                    success: function (res) {
                        //把从服务器API接口获得的token令牌存到缓存中
                        //并且把token令牌交给callBack函数处理(调用callBack函数)
                        wx.setStorageSync('token', res.data.token);
                        callBack && callBack(res.data.token);
                    }
                });
            }
        });
    }
}

export {Token};