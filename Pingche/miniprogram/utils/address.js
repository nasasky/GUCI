import { Base } from 'base.js';
import { Config } from 'config.js';

class Address extends Base {
    constructor() {
        super();
    }

    setAddressInfo(res){
        var province = res.provinceName || res.province,
            city = res.cityName || res.city,
            country = res.countryName || res.country,
            detail = res.detailInfo || res.detail;

        var totalDetail = city + country + detail;

        //不是直辖市就添加省名到详细完整的地址的前面
        if(!this.isCenterCity(province)){
            totalDetail = province + totalDetail;
        }

        return totalDetail;
    }

    /* 是否为直辖市 */
    isCenterCity(name){
        var centerCitys = ['北京市', '天津市', '上海市', '重庆市'],
            flag = centerCitys.indexOf(name)>=0;
            return flag;
    }

    /* 更新保存用户收货地址 */
    submitAddress(data, callback){
        data = this._setUpAddress(data);
        var param = {
            url: 'address',
            type: 'post',
            data: data, 
            sCallback: function (res) {
                callback && callback(true, res);

            },
            eCallback: function (res) {
                callback && callback(false, res);
            }
        };
        this.request(param);
    }

    /* 改变保存地址数据的字段名保证与我们自己的服务器数据库中的用户收货地址表中的字段名相同 */
    _setUpAddress(res){
        var formData = {
            name: res.userName,
            province: res.provinceName,
            city: res.cityName,
            country:res.countyName,
            mobile: res.telNumber,
            detail: res.detailInfo
        };
        return formData;
    }
}

export { Address }