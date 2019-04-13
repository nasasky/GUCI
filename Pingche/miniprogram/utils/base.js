
import { Config } from './config.js';
import { Token } from './token.js';

class Base{
    constructor(){
        this.baseRequestUrl = Config.restUrl;
    }

    /* 封装的接口请求方法 */
    //当noRefetch为true时,不做未授权重试机制
    request(params, noRefetch){
        var that = this;
        var url = this.baseRequestUrl + params.url;    

        if(!params.type){
            params.type = 'GET';
        }
        wx.request({
            url: url,
            data: params.data,
            method: params.type,
            header: {
                'content-type':'application/json',
                'token':wx.getStorageSync('token')
            },
            success:function(res){ 
                //把HTTP状态码转换为字符串
                var code = res.statusCode.toString();
                var startChar = code.charAt(0);
                
                //向服务器端API接口请求成功获得响应(HTTP状态码为2开头如200)
                if(startChar == '2'){
                    params.sCallback && params.sCallback(res.data);
                }
                else {
                    //向服务器端API接口请求失败获得响应(HTTP状态码为4开头如400)                       
                    
                    if (code == '401') {
                        //token.getTokenFromServer
                        //base.request
                        /* 
                          无限未授权重试问题可以理解为一种特殊情况,
                          就是要求小程序客户端携带有效的token令牌去访问服务器端API接口,
                          但是小程序客户端每次都故意不携带有效的token令牌去访问服务器端API接口,
                          这样服务器端API接口就必须每次忍受小程序客户端的故意不携带token令牌的访问,
                          这就造成了死循环,导致产生小程序代码死循环,从而造成小程序崩溃.
                          所以为了避免这种问题的出现,我们给客户端的请求一个noRefresh标记,当noRefresh为true时,
                          就不让小程序客户端来重新请求访问服务器端了.

                          给个生活场景案例来理解 比如: 向取款机取钱要输入密码,如果不做限定,一个人一直输错密码,
                          那就要一直要等着这个人在输密码,那么这台取款机就一直提示"密码错误,请重新输入密码",从而无法为其他人提供服务了.
                          这就是死循环.为了解决这个死循环问题,银行就设置只要输错密码三次,就不能再输入密码了.不让这个人再重新输入密码了.这样就解决了死循环问题.

                          密码限制三次输错就不让输入密码就是一个noRefresh标记.


                          
                        */
                        //noRefetch为true时不执行that._refetch(params),不重发请求了
                        if(!noRefetch){
                            that._refetch(params);
                        }                        
                    }

                    //不再重发了才执行eCallback()回调函数
                    if(noRefetch){
                        params.eCallback && params.eCallback(res.data);
                    }
                    
                }
                
            },
            fail:function(err){
                //服务器端API接口调用失败(如断网的情况根本没有去到服务器API接口内部)
                console.log(err);
            }
        });
    }

    _refetch(params){
        var token = new Token();
        token.getTokenFromServer((token)=>{
            this.request(params, true);
        });
    }

    /* 获得元素(组件标签)上的绑定的值(小程序页面跳转参数传递) */
    getDataSet(event, key) {
        return event.currentTarget.dataset[key];
    }

}

export {Base}