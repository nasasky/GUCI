
import { Base } from '../../utils/base.js';
class Home extends Base{
    constructor(){
        super();
    }
    
    //首页banner的数据
    getBannerData(id,callback){

        var params = {
            url: 'banner/'+id,
            sCallback:function(res) {
                callback && callback(res.items);
            }
        }
        this.request(params);        
    }

    /* 首页精选主题的数据 */
    getThemeData(callback){

        var params = {
            url: 'theme?ids=1,2,3',
            sCallback:function(data) {
                callback && callback(data);
            }
        }
        this.request(params);        
    }

    /* 首页最近新品的数据 */
    getProductsData(callback){

        var params = {
            url: 'product/recent',
            sCallback:function(data) {
                callback && callback(data);
            }
        }
        this.request(params);        
    }
}

export {Home};