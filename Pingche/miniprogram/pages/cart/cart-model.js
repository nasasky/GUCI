import { Base } from '../../utils/base.js';

class Cart extends Base {
    constructor(){
        super();
        this._storageKeyName = 'cart';
    }

    /* 
        加入到购物车
        如果之前没有这样的商品, 则直接添加一条新的记录, 数量为 counts
        如果有, 则只将相应数量 + counts
        @params:
        item - {obj} 商品对象,
        counts - {int} 商品数目,
    */
    add(item, counts){
        var cartData = this.getCartDataFromLocal();        
        var isHasInfo = this._isHasThatOne(item.id, cartData);        
        if(isHasInfo.index==-1){
            item.counts = counts;
            item.selectStatus = true; //设置选中状态
            cartData.push(item);
        }
        else {
            cartData[isHasInfo.index].counts += counts;
        }
        wx.setStorageSync(this._storageKeyName, cartData)
    }

    /* 本地缓存 保存/更新 */
    execSetStorageSync(data){
        wx.setStorageSync(this._storageKeyName, data);
    }

    /**
        *
        *
        * 功能说明: 从缓存中读取购物车数据
        * 参数说明:        
    **/
    getCartDataFromLocal(flag) {
        var res = wx.getStorageSync(this._storageKeyName);
        if(!res){
            res = [];
        }

        //在下单的时候过滤不下单的商品
        if(flag){
            var newRes = [];
            for (let i = 0; i < res.length; i++){
                if(res[i].selectStatus){
                    newRes.push(res[i]);
                }
            }
            res = newRes;
        }
        return res;
    }

    /**        
        * 功能说明:计算购物车内商品总数量
        * 参数说明: flag 为true时考虑商品的选择状态 返回的是选中的商品总个数
        * 
    **/
   getCartTotalCounts(flag){
       var data = this.getCartDataFromLocal();
       var counts = 0;

       for(let i= 0; i < data.length; i++) {
            if(flag){
                if(data[i].selectStatus){
                    counts += data[i].counts;
                }
            }  
            else {
                counts += data[i].counts;
            }          
       }
       return counts;
   }

    /**
        *
        *
        * 功能说明: 判断某个商品是否已经被添加到购物车中, 并且返回这个商品的数据以及所在数组中的序号
        * 参数说明:
        * @param  [type] $arg [description]
        * @return [type]      [description]
    **/
    _isHasThatOne(id, arr){
        var item,
        result = { index: -1};
        for (let i = 0; i < arr.length; i++) {
            item = arr[i];
            if (item.id == id) {
                result = {
                    index: i, 
                    data: item
                };
            break;
            }
        }
        return result;
    }

    /**
        *
        *
        * 功能说明:修改本地缓存中的购物车数组中的单个商品数目
        * 参数说明:
        * @param  [int] id [商品id]
        * @param  [int] counts [数目变化量]        
    **/
    _changeCounts(id, counts){
        var cartData = this.getCartDataFromLocal(),
            hasInfo = this._isHasThatOne(id, cartData);

        if(hasInfo.index != -1){
            if(hasInfo.data.counts > 1){
                cartData[hasInfo.index].counts += counts;
            }
        }

        //更新本地缓存
        wx.setStorageSync(this._storageKeyName, cartData); 

    }

    /* 
        增加购物车商品数目
    */
    addCounts(id){
        this._changeCounts(id, 1);
    }

    /* 
        减少购物车商品数目
    */
    cutCounts(id){
        this._changeCounts(id, -1);
    }

    /* 
        删除购物车商品
    */
   delete(ids){
       if(!(ids instanceof Array)){
            ids = [ids];
       }
       var cartData = this.getCartDataFromLocal();
       for (let i = 0; i < ids.length; i++) {
            var hasInfo = this._isHasThatOne(ids[i], cartData);
            if (hasInfo.index != -1) {
                cartData.splice(hasInfo.index, 1); //删除数组某一项
            }
       }

       wx.setStorageSync(this._storageKeyName, cartData);
   }



}

export { Cart };
